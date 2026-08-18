<?php
/** Unit tests for Plugin/includes/forms.php (request validation, quotas, choice lists, mail body). */

final class FormsTest extends Alipasandi_TestCase {
	public function test_request_without_nonce_is_rejected(): void {
		$this->assertSame( 'invalid_security', alipasandi_validate_form_request( 'alipasandi_contact', 'alipasandi_contact_nonce' ) );
	}

	public function test_request_with_wrong_nonce_is_rejected(): void {
		$_POST['alipasandi_contact_nonce'] = wp_create_nonce( 'some_other_action' );
		$this->assertSame( 'invalid_security', alipasandi_validate_form_request( 'alipasandi_contact', 'alipasandi_contact_nonce' ) );
	}

	public function test_valid_nonce_passes(): void {
		$_POST['alipasandi_contact_nonce'] = wp_create_nonce( 'alipasandi_contact' );
		$this->assertSame( 'valid', alipasandi_validate_form_request( 'alipasandi_contact', 'alipasandi_contact_nonce' ) );
	}

	public function test_filled_honeypot_is_rejected_even_with_a_valid_nonce(): void {
		$_POST['alipasandi_contact_nonce'] = wp_create_nonce( 'alipasandi_contact' );
		$_POST['website_url']              = 'https://spam.example';
		$this->assertSame( 'invalid_security', alipasandi_validate_form_request( 'alipasandi_contact', 'alipasandi_contact_nonce' ) );
	}

	public function test_rate_limit_allows_five_submissions_then_blocks(): void {
		for ( $i = 1; $i <= 5; $i++ ) {
			$this->assertSame( 'valid', alipasandi_form_rate_limit_status( 'alipasandi_contact' ), 'Submission ' . $i . ' must be allowed.' );
		}
		$this->assertSame( 'rate_limited', alipasandi_form_rate_limit_status( 'alipasandi_contact' ) );
	}

	public function test_rate_limit_is_scoped_per_action_and_per_client(): void {
		for ( $i = 1; $i <= 5; $i++ ) {
			alipasandi_form_rate_limit_status( 'alipasandi_contact' );
		}
		$this->assertSame( 'valid', alipasandi_form_rate_limit_status( 'alipasandi_appointment' ), 'Quota must not leak between forms.' );

		$_SERVER['REMOTE_ADDR'] = '198.51.100.7';
		$this->assertSame( 'valid', alipasandi_form_rate_limit_status( 'alipasandi_contact' ), 'Quota must not leak between clients.' );
	}

	public function test_rate_limit_client_ip_is_filterable(): void {
		add_filter(
			'alipasandi_form_client_ip',
			static function () {
				return '10.0.0.1';
			}
		);
		for ( $i = 1; $i <= 5; $i++ ) {
			alipasandi_form_rate_limit_status( 'alipasandi_contact' );
		}
		$_SERVER['REMOTE_ADDR'] = '198.51.100.7';
		$this->assertSame( 'rate_limited', alipasandi_form_rate_limit_status( 'alipasandi_contact' ), 'A filtered proxy IP must own the quota.' );
	}

	public function test_text_length_counts_multibyte_characters(): void {
		$this->assertSame( 3, alipasandi_text_length( 'علی' ) );
		$this->assertSame( 4, alipasandi_text_length( 'سلام' ) );
		$this->assertSame( 0, alipasandi_text_length( '' ) );
	}

	public function test_allowed_services_are_unique_non_empty_choices(): void {
		$services = alipasandi_allowed_services();
		$this->assertNotEmpty( $services );
		$this->assertSame( array_values( array_unique( $services ) ), $services );
		$this->assertNotContains( '', $services );
		$this->assertContains( 'ایمپلنت دندان', $services );
	}

	public function test_allowed_times_default_list_is_normalized_and_sorted(): void {
		$times = alipasandi_allowed_times();

		$this->assertNotEmpty( $times );
		$this->assertSame( '10:00', $times[0] );
		$this->assertSame( array_values( array_unique( $times ) ), $times, 'Duplicate slots must be collapsed.' );

		$sorted = $times;
		sort( $sorted, SORT_STRING );
		$this->assertSame( $sorted, $times );

		foreach ( $times as $time ) {
			$this->assertMatchesRegularExpression( '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $time );
		}
	}

	public function test_allowed_times_reads_configured_slots_and_drops_invalid_lines(): void {
		$this->set_clinic_option( 'clinic_booking_times', "۰۹:۳۰\r\n25:00\n09:30\n  18:00  \nظهر\n7:5\n23:59" );

		$this->assertSame( array( '09:30', '18:00', '23:59' ), alipasandi_allowed_times() );
	}

	public function test_allowed_times_falls_back_when_configuration_is_blank(): void {
		$this->set_clinic_option( 'clinic_booking_times', "   \n  " );
		$blank = alipasandi_allowed_times();

		WP_Test_State::$options = array();
		$this->assertSame( alipasandi_allowed_times(), $blank, 'Whitespace-only configuration must behave like no configuration.' );
		$this->assertContains( '10:00', $blank );
	}

	public function test_allowed_times_is_capped_at_48_slots(): void {
		$lines = array();
		for ( $hour = 0; $hour < 24; $hour++ ) {
			foreach ( array( '00', '15', '30', '45' ) as $minute ) {
				$lines[] = sprintf( '%02d:%s', $hour, $minute );
			}
		}
		$this->set_clinic_option( 'clinic_booking_times', implode( "\n", $lines ) );

		$this->assertCount( 48, alipasandi_allowed_times() );
	}

	public function test_notify_email_uses_the_first_recipient(): void {
		$this->set_clinic_option( 'clinic_notify_email', 'ops@fasdent.ir' );
		$this->assertSame( 'ops@fasdent.ir', alipasandi_notify_email() );
	}

	public function test_notify_email_falls_back_when_no_recipient_is_valid(): void {
		$this->set_clinic_option( 'clinic_notify_email', 'broken' );
		$this->set_clinic_option( 'clinic_notification_cc', 'also-broken' );
		$this->assertSame( 'clinic@fasdent.ir', alipasandi_notify_email() );
	}

	public function test_mail_html_is_rtl_and_escapes_submitted_values(): void {
		$html = alipasandi_mail_html(
			'درخواست نوبت جدید',
			array(
				'نام'  => 'Ali <script>alert(1)</script>',
				'تلفن' => '0920 144 1469',
			)
		);

		$this->assertStringContainsString( 'dir="rtl"', $html );
		$this->assertStringContainsString( 'درخواست نوبت جدید', $html );
		$this->assertStringNotContainsString( '<script>', $html );
		$this->assertStringContainsString( '&lt;script&gt;', $html );
		$this->assertStringContainsString( '0920 144 1469', $html );
		$this->assertStringContainsString( 'نام', $html );
	}

	public function test_mail_html_escapes_row_labels(): void {
		$html = alipasandi_mail_html( 'عنوان', array( '<b>label</b>' => 'value' ) );
		$this->assertStringContainsString( '&lt;b&gt;label&lt;/b&gt;', $html );
	}

	/** Run a handler and return the form_status it redirected with. */
	private function run_handler( callable $handler ): string {
		try {
			$handler();
		} catch ( WP_Test_Redirect $redirect ) {
			parse_str( (string) wp_parse_url( $redirect->location, PHP_URL_QUERY ), $query );
			$this->assertStringEndsWith( '#clinic-form', $redirect->location, 'Users are always returned to the form anchor.' );
			return isset( $query['form_status'] ) ? $query['form_status'] : '';
		}

		$this->fail( 'The handler did not redirect.' );
	}

	/** A complete, valid contact submission. */
	private function valid_contact_post(): array {
		return array(
			'alipasandi_contact_nonce' => wp_create_nonce( 'alipasandi_contact' ),
			'name'                     => 'علی رضایی',
			'phone'                    => '۰۹۱۲۳۴۵۶۷۸۹',
			'subject'                  => 'مشاوره',
			'message'                  => 'سلام، درخواست مشاوره دارم.',
		);
	}

	/** A complete, valid appointment submission. */
	private function valid_appointment_post(): array {
		return array(
			'alipasandi_appointment_nonce' => wp_create_nonce( 'alipasandi_appointment' ),
			'service'                      => 'ایمپلنت دندان',
			'date'                         => '2026-02-01',
			'time'                         => '۱۰:۳۰',
			'name'                         => 'علی رضایی',
			'phone'                        => '۰۹۱۲۳۴۵۶۷۸۹',
			'notes'                        => 'ترجیح می‌دهم صبح باشد.',
		);
	}

	public function test_contact_handler_sends_a_notification_and_reports_success(): void {
		$this->set_clinic_option( 'clinic_notify_email', 'ops@fasdent.ir' );
		$this->set_clinic_option( 'clinic_city', 'نوشهر' );
		$_POST = $this->valid_contact_post();

		$this->assertSame( 'success', $this->run_handler( 'alipasandi_handle_contact' ) );

		$this->assertCount( 1, WP_Test_State::$mail );
		$mail = WP_Test_State::$mail[0];
		$this->assertSame( alipasandi_form_recipients(), $mail['to'] );
		$this->assertContains( 'ops@fasdent.ir', $mail['to'] );
		$this->assertSame( 'پیام جدید سایت: مشاوره', $mail['subject'] );
		$this->assertSame( array( 'Content-Type: text/html; charset=UTF-8' ), $mail['headers'] );
		$this->assertStringContainsString( '09123456789', $mail['message'], 'Persian digits are normalized before notifying the clinic.' );
		$this->assertStringContainsString( 'نوشهر', $mail['message'] );
	}

	public function test_contact_handler_reports_a_mail_error_without_losing_the_status(): void {
		WP_Test_State::$mail_succeeds = false;
		$_POST                        = $this->valid_contact_post();

		$this->assertSame( 'mail_error', $this->run_handler( 'alipasandi_handle_contact' ) );
	}

	public function test_contact_handler_rejects_forged_requests_before_sending_mail(): void {
		$_POST = array_merge( $this->valid_contact_post(), array( 'alipasandi_contact_nonce' => 'forged' ) );

		$this->assertSame( 'error', $this->run_handler( 'alipasandi_handle_contact' ) );
		$this->assertSame( array(), WP_Test_State::$mail );
	}

	/**
	 * @dataProvider invalid_contact_field_provider
	 */
	public function test_contact_handler_rejects_invalid_fields( array $overrides ): void {
		$_POST = array_merge( $this->valid_contact_post(), $overrides );

		$this->assertSame( 'invalid', $this->run_handler( 'alipasandi_handle_contact' ) );
		$this->assertSame( array(), WP_Test_State::$mail );
	}

	/** @return array<string,array{0:array<string,string>}> */
	public function invalid_contact_field_provider(): array {
		return array(
			'name too short'   => array( array( 'name' => 'ا' ) ),
			'missing phone'    => array( array( 'phone' => '' ) ),
			'lettered phone'   => array( array( 'phone' => 'call-me' ) ),
			'phone too short'  => array( array( 'phone' => '12345' ) ),
			'message too long' => array( array( 'message' => str_repeat( 'ا', 3001 ) ) ),
		);
	}

	public function test_contact_handler_rate_limits_repeat_submissions(): void {
		for ( $i = 0; $i < 5; $i++ ) {
			$_POST = $this->valid_contact_post();
			$this->assertSame( 'success', $this->run_handler( 'alipasandi_handle_contact' ) );
		}

		$_POST = $this->valid_contact_post();
		$this->assertSame( 'rate_limited', $this->run_handler( 'alipasandi_handle_contact' ) );
		$this->assertCount( 5, WP_Test_State::$mail, 'A throttled submission is never mailed.' );
	}

	public function test_appointment_handler_accepts_persian_time_slots(): void {
		$this->set_clinic_option( 'clinic_notify_email', 'ops@fasdent.ir' );
		$_POST = $this->valid_appointment_post();

		$this->assertSame( 'success', $this->run_handler( 'alipasandi_handle_appointment' ) );

		$mail = WP_Test_State::$mail[0];
		$this->assertStringContainsString( 'درخواست نوبت جدید', $mail['subject'] );
		$this->assertStringContainsString( 'علی رضایی', $mail['subject'] );
		$this->assertStringContainsString( 'ایمپلنت دندان', $mail['message'] );
		$this->assertStringContainsString( '2026-02-01', $mail['message'] );
	}

	public function test_appointment_handler_shows_a_dash_when_no_notes_are_supplied(): void {
		$_POST = array_merge( $this->valid_appointment_post(), array( 'notes' => '' ) );

		$this->assertSame( 'success', $this->run_handler( 'alipasandi_handle_appointment' ) );
		$this->assertStringContainsString( '—', WP_Test_State::$mail[0]['message'] );
	}

	/**
	 * @dataProvider invalid_appointment_field_provider
	 */
	public function test_appointment_handler_rejects_invalid_fields( array $overrides ): void {
		$_POST = array_merge( $this->valid_appointment_post(), $overrides );

		$this->assertSame( 'invalid', $this->run_handler( 'alipasandi_handle_appointment' ) );
		$this->assertSame( array(), WP_Test_State::$mail );
	}

	/** @return array<string,array{0:array<string,string>}> */
	public function invalid_appointment_field_provider(): array {
		return array(
			'unlisted service' => array( array( 'service' => 'کاشت مو' ) ),
			'unlisted time'    => array( array( 'time' => '03:15' ) ),
			'past date'        => array( array( 'date' => '2020-01-01' ) ),
			'malformed date'   => array( array( 'date' => '01/02/2026' ) ),
			'missing date'     => array( array( 'date' => '' ) ),
			'name too short'   => array( array( 'name' => 'ع' ) ),
			'bad phone'        => array( array( 'phone' => 'phone' ) ),
			'notes too long'   => array( array( 'notes' => str_repeat( 'ا', 2001 ) ) ),
		);
	}

	public function test_appointment_handler_honours_configured_time_slots(): void {
		$this->set_clinic_option( 'clinic_booking_times', "09:00\n09:30" );
		$_POST = $this->valid_appointment_post();

		$this->assertSame( 'invalid', $this->run_handler( 'alipasandi_handle_appointment' ), '10:30 is no longer offered.' );

		$_POST = array_merge( $this->valid_appointment_post(), array( 'time' => '09:00' ) );
		$this->assertSame( 'success', $this->run_handler( 'alipasandi_handle_appointment' ) );
	}

	public function test_handlers_return_the_visitor_to_the_referring_form(): void {
		$_SERVER['HTTP_REFERER'] = 'https://fasdent.ir/appointments/?utm_source=ig';
		$_POST                   = $this->valid_appointment_post();

		try {
			alipasandi_handle_appointment();
			$this->fail( 'The handler did not redirect.' );
		} catch ( WP_Test_Redirect $redirect ) {
			unset( $_SERVER['HTTP_REFERER'] );
			$this->assertStringStartsWith( 'https://fasdent.ir/appointments/?utm_source=ig&', $redirect->location );
			$this->assertStringContainsString( 'form_status=success', $redirect->location );
		}
	}

	public function test_handlers_fall_back_to_the_form_page_without_a_referer(): void {
		$_POST = $this->valid_contact_post();

		try {
			alipasandi_handle_contact();
			$this->fail( 'The handler did not redirect.' );
		} catch ( WP_Test_Redirect $redirect ) {
			$this->assertStringStartsWith( 'https://fasdent.ir/contact/?form_status=success', $redirect->location );
		}
	}
}
