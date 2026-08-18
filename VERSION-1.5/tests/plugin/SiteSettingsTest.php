<?php
/** Unit tests for Plugin/includes/site-settings.php (NAP, recipients, schema integration). */

final class SiteSettingsTest extends Alipasandi_TestCase {
	public function test_nap_field_schema_is_four_tuple_with_callable_sanitizer(): void {
		$fields = alipasandi_nap_fields();
		$this->assertNotEmpty( $fields );

		foreach ( $fields as $key => $field ) {
			$this->assertSame( $key, sanitize_key( $key ), 'Option keys must be storage safe: ' . $key );
			$this->assertCount( 4, $field, 'Field definition must be label/type/sanitizer/default: ' . $key );
			$this->assertContains( $field[1], array( 'text', 'url', 'email', 'textarea' ), 'Unsupported input type: ' . $key );
			$this->assertIsCallable( $field[2], 'Sanitizer must be callable: ' . $key );
			$this->assertIsString( $field[3], 'Default must be a string: ' . $key );
		}
	}

	public function test_email_defaults_are_valid_addresses(): void {
		foreach ( alipasandi_nap_fields() as $key => $field ) {
			if ( 'email' !== $field[1] || '' === $field[3] ) {
				continue;
			}
			$this->assertTrue( (bool) is_email( $field[3] ), 'Email default must be valid: ' . $key );
		}
	}

	public function test_clinic_option_falls_back_to_default_when_unset_or_empty(): void {
		$this->assertSame( 'نوشهر', alipasandi_clinic_option( 'clinic_city' ) );

		$this->set_clinic_option( 'clinic_city', '' );
		$this->assertSame( 'نوشهر', alipasandi_clinic_option( 'clinic_city' ), 'Empty stored values must not win over the default.' );

		$this->set_clinic_option( 'clinic_city', 'چالوس' );
		$this->assertSame( 'چالوس', alipasandi_clinic_option( 'clinic_city' ) );
	}

	public function test_clinic_option_returns_empty_string_for_unknown_key(): void {
		$this->assertSame( '', alipasandi_clinic_option( 'not_a_registered_field' ) );
	}

	public function test_form_recipients_default_to_both_configured_inboxes(): void {
		$this->assertSame(
			array( 'clinic@fasdent.ir', 'Dr.keyvan.alipasandii@gmail.com' ),
			alipasandi_form_recipients()
		);
	}

	public function test_form_recipients_drop_invalid_and_duplicate_addresses(): void {
		$this->set_clinic_option( 'clinic_notify_email', 'ops@fasdent.ir' );
		$this->set_clinic_option( 'clinic_notification_cc', 'not-an-email' );
		$this->assertSame( array( 'ops@fasdent.ir' ), alipasandi_form_recipients() );

		$this->set_clinic_option( 'clinic_notification_cc', 'ops@fasdent.ir' );
		$this->assertSame( array( 'ops@fasdent.ir' ), alipasandi_form_recipients(), 'The same inbox must not be notified twice.' );
	}

	public function test_normalize_digits_converts_persian_and_arabic_numerals(): void {
		$this->assertSame( '0123456789', alipasandi_normalize_digits( '۰۱۲۳۴۵۶۷۸۹' ) );
		$this->assertSame( '0123456789', alipasandi_normalize_digits( '٠١٢٣٤٥٦٧٨٩' ) );
		$this->assertSame( '0920 144 1469', alipasandi_normalize_digits( '۰۹۲۰ ۱۴۴ ۱۴۶۹' ) );
		$this->assertSame( 'تلفن: 09201441469', alipasandi_normalize_digits( 'تلفن: ۰۹۲۰۱۴۴۱۴۶۹' ) );
	}

	public function test_phone_href_keeps_only_dialable_characters(): void {
		$this->assertSame( '+989201441469', alipasandi_phone_href() );

		$this->set_clinic_option( 'clinic_phone_e164', '+۹۸ (۱۱) ۵۲۳-۴۵۶۷ داخلی' );
		$this->assertSame( '+98115234567', alipasandi_phone_href() );
	}

	public function test_show_treatment_count_is_opt_in(): void {
		$this->assertFalse( alipasandi_show_treatment_count() );

		WP_Test_State::$options['alipasandi_show_treatment_count'] = '1';
		$this->assertTrue( alipasandi_show_treatment_count() );
	}

	public function test_domain_mail_from_prefers_configured_address(): void {
		$this->assertSame( 'noreply@fasdent.ir', alipasandi_domain_mail_from( 'wordpress@localhost' ) );

		$this->set_clinic_option( 'clinic_mail_from', 'broken-address' );
		$this->assertSame( 'wordpress@localhost', alipasandi_domain_mail_from( 'wordpress@localhost' ), 'An invalid address must not replace the WordPress default.' );
	}

	public function test_legacy_theme_mods_migrate_once_into_options(): void {
		WP_Test_State::$theme_mods['clinic_city']          = ' چالوس ';
		WP_Test_State::$theme_mods['clinic_notify_email']  = 'legacy@fasdent.ir';
		WP_Test_State::$theme_mods['show_treatment_count'] = true;

		alipasandi_site_settings_migrate_legacy_theme_mods();

		$this->assertSame( 'چالوس', get_option( 'alipasandi_clinic_city' ), 'Migrated values must pass through the field sanitizer.' );
		$this->assertSame( 'legacy@fasdent.ir', get_option( 'alipasandi_clinic_notify_email' ) );
		$this->assertTrue( get_option( 'alipasandi_show_treatment_count' ) );
		$this->assertTrue( get_option( 'alipasandi_nap_migrated' ) );

		WP_Test_State::$theme_mods['clinic_city'] = 'تهران';
		alipasandi_site_settings_migrate_legacy_theme_mods();
		$this->assertSame( 'چالوس', get_option( 'alipasandi_clinic_city' ), 'Migration must not run twice.' );
	}

	public function test_legacy_migration_skips_blank_theme_mods(): void {
		WP_Test_State::$theme_mods['clinic_city'] = '';
		alipasandi_site_settings_migrate_legacy_theme_mods();
		$this->assertNull( get_option( 'alipasandi_clinic_city', null ) );
	}

	public function test_rank_math_json_ld_is_untouched_without_rank_math(): void {
		$data = array( array( '@type' => 'Dentist', 'name' => 'stale' ) );
		$this->assertSame( $data, alipasandi_rank_math_central_nap( $data ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_rank_math_json_ld_overwrites_nap_for_local_business_entities(): void {
		define( 'RANK_MATH_VERSION', '1.0.0' );

		$this->set_clinic_option( 'clinic_postal_code', '46514' );
		$this->set_clinic_option( 'clinic_geo_lat', '36.6489' );
		$this->set_clinic_option( 'clinic_geo_lng', '51.4964' );

		$data = alipasandi_rank_math_central_nap(
			array(
				array( '@type' => array( 'Dentist', 'LocalBusiness' ), 'name' => 'stale', 'geo' => 'stale' ),
				array( '@type' => 'WebPage', 'name' => 'keep' ),
			)
		);

		$this->assertSame( 'کلینیک دندانپزشکی دکتر کیوان علی‌پسندی', $data[0]['name'] );
		$this->assertSame( 'https://fasdent.ir/', $data[0]['url'] );
		$this->assertSame( '+989201441469', $data[0]['telephone'] );
		$this->assertSame( 'IR', $data[0]['address']['addressCountry'] );
		$this->assertSame( '46514', $data[0]['address']['postalCode'] );
		$this->assertSame( array( '@type' => 'GeoCoordinates', 'latitude' => 36.6489, 'longitude' => 51.4964 ), $data[0]['geo'] );
		$this->assertSame( 'keep', $data[1]['name'], 'Non local-business entities must be left alone.' );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_rank_math_json_ld_drops_geo_when_coordinates_are_incomplete(): void {
		define( 'RANK_MATH_VERSION', '1.0.0' );

		$this->set_clinic_option( 'clinic_geo_lat', '36.6489' );
		$this->set_clinic_option( 'clinic_geo_lng', 'not-a-number' );

		$data = alipasandi_rank_math_central_nap( array( array( '@type' => 'Dentist', 'geo' => 'stale' ) ) );

		$this->assertArrayNotHasKey( 'geo', $data[0] );
		$this->assertArrayNotHasKey( 'postalCode', $data[0]['address'], 'An unset postal code must not be published.' );
	}
}
