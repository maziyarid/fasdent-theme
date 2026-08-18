<?php
/** Unit tests for Plugin/includes/service-content.php (registry, sanitization, resolution, status). */

final class ServiceContentTest extends Alipasandi_TestCase {
	public function test_service_keys_are_the_four_canonical_records(): void {
		$this->assertSame( array( 'implant', 'crown', 'surgery', 'general' ), alipasandi_service_keys() );
	}

	public function test_service_keys_filter_is_sanitized_and_deduplicated(): void {
		add_filter(
			'alipasandi_service_registry',
			static function () {
				return array( 'Implant', 'implant', 'White Ning', '', 'general' );
			}
		);

		$this->assertSame( array( 'implant', 'whitening', 'general' ), alipasandi_service_keys() );
	}

	public function test_service_keys_fail_closed_on_a_broken_filter(): void {
		add_filter(
			'alipasandi_service_registry',
			static function () {
				return 'not-an-array';
			}
		);

		$this->assertSame( array(), alipasandi_service_keys() );
	}

	public function test_sanitize_html_keeps_allowed_markup_and_strips_scripts(): void {
		$clean = alipasandi_service_sanitize_html( '<strong>الف</strong><em>ب</em><br><span>ج</span><script>alert(1)</script>' );

		$this->assertStringContainsString( '<strong>الف</strong>', $clean );
		$this->assertStringContainsString( '<em>ب</em>', $clean );
		$this->assertStringContainsString( '<span>ج</span>', $clean );
		$this->assertStringNotContainsString( '<script', $clean );
	}

	public function test_sanitize_html_forces_safe_rel_on_new_window_links(): void {
		$clean = alipasandi_service_sanitize_html( '<a href="https://fasdent.ir" target="_blank" rel="opener">پیوند</a>' );

		$this->assertStringContainsString( 'rel="noopener noreferrer"', $clean );
		$this->assertStringNotContainsString( 'rel="opener"', $clean );
	}

	public function test_sanitize_html_leaves_same_window_links_alone(): void {
		$clean = alipasandi_service_sanitize_html( '<a href="https://fasdent.ir/services/implant/">پیوند</a>' );

		$this->assertStringContainsString( 'href="https://fasdent.ir/services/implant/"', $clean );
		$this->assertStringNotContainsString( 'noopener', $clean );
	}

	public function test_sanitize_html_removes_event_handlers_and_unsafe_protocols(): void {
		$clean = alipasandi_service_sanitize_html( '<a href="javascript:alert(1)" onclick="alert(2)">x</a>' );

		$this->assertStringNotContainsString( 'onclick', $clean );
		$this->assertStringNotContainsString( 'javascript:', $clean );
	}

	public function test_sanitize_service_meta_rejects_non_arrays(): void {
		$this->assertSame( array(), alipasandi_sanitize_service_meta( 'string' ) );
		$this->assertSame( array(), alipasandi_sanitize_service_meta( null ) );
	}

	public function test_sanitize_service_meta_stamps_schema_and_normalizes_plain_fields(): void {
		$meta = alipasandi_sanitize_service_meta(
			array(
				'title'    => "  <b>ایمپلنت</b>\n دندان  ",
				'eyebrow'  => 'خدمات',
				'unknown'  => 'must be dropped',
			)
		);

		$this->assertSame( ALIPASANDI_SERVICE_META_SCHEMA, $meta['schema_version'] );
		$this->assertSame( 'ایمپلنت دندان', $meta['title'] );
		$this->assertSame( 'خدمات', $meta['eyebrow'] );
		$this->assertArrayNotHasKey( 'unknown', $meta, 'Unknown fields must never be stored.' );
		$this->assertSame( '', $meta['cta_title'], 'Missing fields must be present and empty.' );
	}

	public function test_sanitize_service_meta_allows_limited_html_in_long_copy(): void {
		$meta = alipasandi_sanitize_service_meta(
			array(
				'intro'     => '<strong>مقدمه</strong><script>alert(1)</script>',
				'cta_text'  => '<a href="https://fasdent.ir" target="_blank">تماس</a>',
			)
		);

		$this->assertSame( '<strong>مقدمه</strong>alert(1)', $meta['intro'] );
		$this->assertStringContainsString( 'rel="noopener noreferrer"', $meta['cta_text'] );
	}

	public function test_sanitize_service_meta_enforces_list_limits_and_drops_blank_rows(): void {
		$paragraphs = array_fill( 0, ALIPASANDI_SERVICE_MAX_PARAGRAPHS + 5, 'پاراگراف' );
		$paragraphs[] = '   ';

		$meta = alipasandi_sanitize_service_meta(
			array(
				'what_text' => $paragraphs,
				'diagram'   => array( 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت' ),
				'steps'     => array_merge(
					array( array( '۱', 'مرحله', '<em>متن</em>' ), array( '۲', '', '  ' ), 'not-an-array' ),
					array_fill( 0, ALIPASANDI_SERVICE_MAX_STEPS + 3, array( '۳', 'مرحله' ) )
				),
				'faqs'      => array_merge(
					array( array( 'پرسش', '<strong>پاسخ</strong>' ), array( '', '' ) ),
					array_fill( 0, ALIPASANDI_SERVICE_MAX_FAQS + 2, array( 'پرسش', 'پاسخ' ) )
				),
			)
		);

		$this->assertCount( ALIPASANDI_SERVICE_MAX_PARAGRAPHS, $meta['what_text'] );
		$this->assertSame( array( 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش' ), $meta['diagram'], 'The diagram list is capped at six labels.' );
		$this->assertLessThanOrEqual( ALIPASANDI_SERVICE_MAX_STEPS, count( $meta['steps'] ) );
		$this->assertLessThanOrEqual( ALIPASANDI_SERVICE_MAX_FAQS, count( $meta['faqs'] ) );
		$this->assertSame( array( '۱', 'مرحله', '<em>متن</em>' ), $meta['steps'][0] );
		$this->assertSame( array( 'پرسش', '<strong>پاسخ</strong>' ), $meta['faqs'][0] );
	}

	public function test_sanitize_service_meta_drops_blank_diagram_labels(): void {
		$meta = alipasandi_sanitize_service_meta( array( 'diagram' => array( 'یک', '  ', 'دو' ) ) );

		$this->assertSame( array( 'یک', 'دو' ), $meta['diagram'] );
	}

	public function test_sanitize_service_meta_falls_back_to_a_known_benefit_icon(): void {
		$meta = alipasandi_sanitize_service_meta(
			array(
				'benefits' => array(
					array( 'دقت', 'متن', 'shield' ),
					array( 'ماندگاری', 'متن', 'not-a-real-icon' ),
					array( '', '   ' ),
					'not-an-array',
				),
			)
		);

		$this->assertCount( 2, $meta['benefits'] );
		$this->assertSame( 'shield', $meta['benefits'][0][2] );
		$this->assertSame( 'tooth', $meta['benefits'][1][2], 'Unknown icons must fall back to the default.' );
	}

	public function test_sanitize_service_meta_only_keeps_real_image_attachments(): void {
		WP_Test_State::add_post( 51, 'attachment', array( 'is_image' => true ) );
		WP_Test_State::add_post( 52, 'attachment', array( 'is_image' => false ) );

		$this->assertSame( 51, alipasandi_sanitize_service_meta( array( 'image_id' => 51 ) )['image_id'] );
		$this->assertSame( 0, alipasandi_sanitize_service_meta( array( 'image_id' => 52 ) )['image_id'], 'Non-image attachments must be rejected.' );
		$this->assertSame( 0, alipasandi_sanitize_service_meta( array( 'image_id' => 999 ) )['image_id'], 'Missing attachments must be rejected.' );
		$this->assertSame( 1, alipasandi_sanitize_service_meta( array( 'image_decorative' => 'yes' ) )['image_decorative'] );
		$this->assertSame( 0, alipasandi_sanitize_service_meta( array() )['image_decorative'] );
	}

	public function test_service_page_resolves_by_stable_key_meta(): void {
		WP_Test_State::add_post( 10, 'page', array( 'post_name' => 'implant-landing' ) );
		update_post_meta( 10, ALIPASANDI_SERVICE_KEY_META, 'implant' );

		$page = alipasandi_get_service_page( 'implant' );
		$this->assertNotNull( $page );
		$this->assertSame( 10, (int) $page->ID );
	}

	public function test_service_page_resolves_by_slug_when_no_key_meta_exists(): void {
		WP_Test_State::add_post( 11, 'page', array( 'post_name' => 'crown' ) );

		$page = alipasandi_get_service_page( 'crown' );
		$this->assertNotNull( $page );
		$this->assertSame( 11, (int) $page->ID );
	}

	public function test_service_page_fails_closed_on_duplicate_stable_keys(): void {
		WP_Test_State::add_post( 12, 'page', array( 'post_name' => 'implant-a' ) );
		WP_Test_State::add_post( 13, 'page', array( 'post_name' => 'implant-b' ) );
		update_post_meta( 12, ALIPASANDI_SERVICE_KEY_META, 'implant' );
		update_post_meta( 13, ALIPASANDI_SERVICE_KEY_META, 'implant' );

		$this->assertNull( alipasandi_get_service_page( 'implant' ) );
	}

	public function test_service_page_ignores_unregistered_keys(): void {
		WP_Test_State::add_post( 14, 'page', array( 'post_name' => 'whitening' ) );
		$this->assertNull( alipasandi_get_service_page( 'whitening' ) );
	}

	public function test_service_key_for_post_prefers_meta_over_slug(): void {
		WP_Test_State::add_post( 20, 'page', array( 'post_name' => 'crown' ) );
		update_post_meta( 20, ALIPASANDI_SERVICE_KEY_META, 'implant' );

		$this->assertSame( 'implant', alipasandi_service_key_for_post( 20 ) );
	}

	public function test_service_key_for_post_uses_slug_and_rejects_other_post_types(): void {
		WP_Test_State::add_post( 21, 'page', array( 'post_name' => 'surgery' ) );
		WP_Test_State::add_post( 22, 'post', array( 'post_name' => 'surgery' ) );
		WP_Test_State::add_post( 23, 'page', array( 'post_name' => 'about-us' ) );

		$this->assertSame( 'surgery', alipasandi_service_key_for_post( 21 ) );
		$this->assertSame( '', alipasandi_service_key_for_post( 22 ) );
		$this->assertSame( '', alipasandi_service_key_for_post( 23 ) );
		$this->assertSame( '', alipasandi_service_key_for_post( 999 ) );
	}

	public function test_get_service_returns_meta_when_it_exists(): void {
		WP_Test_State::add_post( 30, 'page', array( 'post_name' => 'implant' ) );
		update_post_meta( 30, ALIPASANDI_SERVICE_META_KEY, array( 'title' => 'ایمپلنت' ) );

		$this->assertSame( array( 'title' => 'ایمپلنت' ), alipasandi_get_service( 'implant' ) );
	}

	public function test_get_service_falls_back_to_legacy_theme_content(): void {
		$service = alipasandi_get_service( 'implant' );

		$this->assertNotSame( array(), $service, 'With no page and no meta the read-only legacy content is used.' );
		$this->assertSame( alipasandi_get_service_legacy( 'implant' ), $service );
	}

	public function test_get_service_returns_empty_array_for_an_unknown_key(): void {
		$this->assertSame( array(), alipasandi_get_service( 'whitening' ) );
	}

	public function test_get_service_treats_existing_empty_meta_as_authoritative(): void {
		WP_Test_State::add_post( 31, 'page', array( 'post_name' => 'crown' ) );
		update_post_meta( 31, ALIPASANDI_SERVICE_META_KEY, '' );

		$this->assertSame( array(), alipasandi_get_service( 'crown' ), 'Existing meta must never fall back to legacy content.' );
	}

	public function test_meta_exists_distinguishes_absent_from_empty(): void {
		WP_Test_State::add_post( 32, 'page' );
		$this->assertFalse( alipasandi_service_meta_exists( 32 ) );

		update_post_meta( 32, ALIPASANDI_SERVICE_META_KEY, array() );
		$this->assertTrue( alipasandi_service_meta_exists( 32 ) );
	}

	public function test_migration_status_is_normalized_when_the_option_is_corrupt(): void {
		WP_Test_State::$options[ ALIPASANDI_SERVICE_META_STATUS ] = 'corrupt';
		$status = alipasandi_get_service_migration_status();

		$this->assertFalse( $status['completed'] );
		$this->assertSame( array(), $status['pages'] );

		WP_Test_State::$options[ ALIPASANDI_SERVICE_META_STATUS ] = array( 'completed' => true );
		$this->assertSame( array(), alipasandi_get_service_migration_status()['pages'] );
	}

	public function test_migration_log_is_sanitized_and_bounded(): void {
		for ( $i = 0; $i < 105; $i++ ) {
			alipasandi_service_log_migration( '30', 'Implant', 'Migrated', 'reason <b>' . $i . '</b>' );
		}

		$log = get_option( ALIPASANDI_SERVICE_MIGRATION_LOG );
		$this->assertCount( 100, $log, 'The log keeps only the newest 100 entries.' );
		$this->assertSame( 30, $log[0]['page_id'] );
		$this->assertSame( 'implant', $log[0]['key'] );
		$this->assertSame( 'migrated', $log[0]['status'] );
		$this->assertStringNotContainsString( '<b>', $log[0]['reason'] );
		$this->assertSame( '2026-01-15 09:00:00', $log[0]['timestamp'] );
	}

	/** Register a page for every service key so migration can complete. */
	private function add_all_service_pages(): void {
		$id = 40;
		foreach ( alipasandi_service_keys() as $key ) {
			WP_Test_State::add_post( $id++, 'page', array( 'post_name' => $key ) );
		}
	}

	public function test_migration_copies_sanitized_legacy_content_and_stamps_stable_keys(): void {
		$this->add_all_service_pages();

		alipasandi_migrate_service_content_to_meta();

		$status = alipasandi_get_service_migration_status();
		$this->assertTrue( $status['completed'] );
		$this->assertSame( array( 'implant', 'crown', 'surgery', 'general' ), array_keys( $status['pages'] ) );
		$this->assertSame( array( 'migrated', 'migrated', 'migrated', 'migrated' ), array_values( $status['pages'] ) );

		$page = alipasandi_get_service_page( 'implant' );
		$this->assertSame( 'implant', get_post_meta( $page->ID, ALIPASANDI_SERVICE_KEY_META, true ) );

		$meta = get_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, true );
		$this->assertSame( ALIPASANDI_SERVICE_META_SCHEMA, $meta['schema_version'] );
		$this->assertSame( alipasandi_sanitize_service_meta( alipasandi_get_service_legacy( 'implant' ) ), $meta );
	}

	public function test_migration_is_idempotent_and_never_overwrites_edited_meta(): void {
		$this->add_all_service_pages();
		$page = alipasandi_get_service_page( 'crown' );
		update_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, array( 'title' => 'ویرایش ادمین' ) );

		alipasandi_migrate_service_content_to_meta();

		$this->assertSame( 'existing', alipasandi_get_service_migration_status()['pages']['crown'] );
		$this->assertSame( array( 'title' => 'ویرایش ادمین' ), get_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, true ) );

		$before = WP_Test_State::$post_meta;
		alipasandi_migrate_service_content_to_meta();
		$this->assertSame( $before, WP_Test_State::$post_meta, 'A completed migration must not touch post meta again.' );
	}

	public function test_migration_stays_pending_for_missing_pages_without_blocking_the_others(): void {
		WP_Test_State::add_post( 50, 'page', array( 'post_name' => 'implant' ) );

		alipasandi_migrate_service_content_to_meta();

		$status = alipasandi_get_service_migration_status();
		$this->assertFalse( $status['completed'], 'Missing pages keep the migration retryable.' );
		$this->assertSame( 'migrated', $status['pages']['implant'] );
		$this->assertSame( 'failed', $status['pages']['crown'] );

		$reasons = array_column( get_option( ALIPASANDI_SERVICE_MIGRATION_LOG ), 'reason' );
		$this->assertContains( 'page_not_found', $reasons );
	}

	public function test_migration_fails_closed_on_duplicate_stable_keys(): void {
		WP_Test_State::add_post( 60, 'page', array( 'post_name' => 'implant' ) );
		WP_Test_State::add_post( 61, 'page', array( 'post_name' => 'implant-copy' ) );
		update_post_meta( 60, ALIPASANDI_SERVICE_KEY_META, 'implant' );
		update_post_meta( 61, ALIPASANDI_SERVICE_KEY_META, 'implant' );

		alipasandi_migrate_service_content_to_meta();

		$this->assertSame( 'failed', alipasandi_get_service_migration_status()['pages']['implant'] );
		$this->assertFalse( alipasandi_service_meta_exists( 60 ) );
		$this->assertFalse( alipasandi_service_meta_exists( 61 ) );
	}

	public function test_dry_run_reports_the_plan_without_writing_anything(): void {
		WP_Test_State::add_post( 70, 'page', array( 'post_name' => 'implant' ) );
		WP_Test_State::add_post( 71, 'page', array( 'post_name' => 'crown' ) );
		update_post_meta( 71, ALIPASANDI_SERVICE_META_KEY, array( 'title' => 'روکش' ) );

		$plan = alipasandi_service_migration_dry_run();

		$this->assertFalse( $plan['write'] );
		$this->assertFalse( $plan['pass'], 'Missing service pages must block the plan.' );
		$this->assertSame( ALIPASANDI_SERVICE_META_SCHEMA, $plan['schema_version'] );
		$this->assertSame( 'migrate', $plan['services']['implant']['action'] );
		$this->assertSame( 70, $plan['services']['implant']['page_id'] );
		$this->assertSame( 'skip_existing', $plan['services']['crown']['action'] );
		$this->assertSame( 'blocked_missing_page', $plan['services']['surgery']['action'] );
		$this->assertFalse( alipasandi_service_meta_exists( 70 ), 'A dry run never writes meta.' );
		$this->assertFalse( get_option( ALIPASANDI_SERVICE_META_STATUS ), 'A dry run never writes status.' );
	}

	public function test_migration_is_skipped_once_completed(): void {
		WP_Test_State::$options[ ALIPASANDI_SERVICE_META_STATUS ] = array( 'completed' => true, 'pages' => array() );

		alipasandi_migrate_service_content_to_meta();

		$this->assertSame( array( 'completed' => true, 'pages' => array() ), get_option( ALIPASANDI_SERVICE_META_STATUS ) );
		$this->assertFalse( get_option( ALIPASANDI_SERVICE_MIGRATION_LOG ), 'A completed migration must not log anything.' );
	}
}
