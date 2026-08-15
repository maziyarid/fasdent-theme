<?php
/** Targeted WordPress PHPUnit regressions for Plugin 1.3.3. */

class Alipasandi_Registry_And_Validators_Test extends WP_UnitTestCase {
	private $previous_timezone;

	public function set_up(): void {
		parent::set_up();
		$this->previous_timezone = get_option( 'timezone_string', '' );
		update_option( 'timezone_string', 'Asia/Tehran' );
	}

	public function tear_down(): void {
		remove_all_filters( 'alipasandi_service_registry' );
		remove_all_filters( 'alipasandi_booking_horizon_days' );
		remove_all_filters( 'alipasandi_booking_min_lead_minutes' );
		remove_all_filters( 'alipasandi_booking_now' );
		alipasandi_service_registry_state( true );
		update_option( 'timezone_string', $this->previous_timezone );
		parent::tear_down();
	}

	private function set_booking_now( $datetime ) {
		remove_all_filters( 'alipasandi_booking_now' );
		add_filter( 'alipasandi_booking_now', function () use ( $datetime ) {
			return new DateTimeImmutable( $datetime, wp_timezone() );
		} );
	}

	public function test_01_appointment_ui_and_server_use_registry_stable_keys_only() {
		$registry = alipasandi_service_registry();
		$expected = array();
		foreach ( $registry as $key => $item ) {
			if ( ! empty( $item['bookable'] ) ) { $expected[ $key ] = $item['label']; }
		}
		$this->assertSame( $expected, alipasandi_bookable_services() );
		$this->assertSame( array_keys( $expected ), alipasandi_allowed_services() );
		$this->assertSame( $expected['implant'], alipasandi_service_label( 'implant' ) );
		$this->assertSame( '', alipasandi_service_label( 'ایمپلنت دندان' ), 'Display labels must never be accepted as identity.' );
		$this->assertFalse( function_exists( 'alipasandi_appointment_service_extras' ), 'A secondary appointment list must not exist.' );
	}

	public function test_02_registry_has_exact_six_field_schema() {
		$fields = alipasandi_service_registry_fields();
		sort( $fields );
		foreach ( alipasandi_service_registry() as $key => $item ) {
			$item_fields = array_keys( $item );
			sort( $item_fields );
			$this->assertSame( $fields, $item_fields, 'Registry entry schema drift: ' . $key );
		}
	}

	public function test_03_unknown_registry_field_fails_closed_and_is_reported() {
		add_filter( 'alipasandi_service_registry', function ( $registry ) {
			$registry['malformed'] = array(
				'key'=>'malformed', 'label'=>'Malformed', 'bookable'=>true,
				'page_slug'=>'', 'content_managed'=>false, 'icon'=>'tooth',
				'unknown_field'=>'must-not-pass',
			);
			return $registry;
		} );
		$state = alipasandi_service_registry_state( true );
		$this->assertArrayNotHasKey( 'malformed', $state['items'] );
		$this->assertContains( 'unknown_field:malformed:unknown_field', $state['issues'] );
	}

	public function test_04_invalid_registry_icon_fails_closed() {
		add_filter( 'alipasandi_service_registry', function ( $registry ) {
			$registry['bad-icon'] = array(
				'key'=>'bad-icon', 'label'=>'Bad Icon', 'bookable'=>true,
				'page_slug'=>'', 'content_managed'=>false, 'icon'=>'not-a-real-icon',
			);
			return $registry;
		} );
		$state = alipasandi_service_registry_state( true );
		$this->assertArrayNotHasKey( 'bad-icon', $state['items'] );
		$this->assertContains( 'invalid_icon:bad-icon', $state['issues'] );
	}

	public function test_05_duplicate_display_label_is_non_silent_warning_not_identity_collision() {
		add_filter( 'alipasandi_service_registry', function ( $registry ) {
			$registry['duplicate-label-demo'] = array(
				'key'=>'duplicate-label-demo', 'label'=>$registry['implant']['label'], 'bookable'=>true,
				'page_slug'=>'', 'content_managed'=>false, 'icon'=>'tooth',
			);
			return $registry;
		} );
		$state = alipasandi_service_registry_state( true );
		$this->assertArrayHasKey( 'duplicate-label-demo', $state['items'] );
		$this->assertNotEmpty( array_filter( $state['warnings'], function ( $warning ) { return 0 === strpos( $warning, 'duplicate_label:' ); } ) );
	}

	public function test_06_booking_horizon_and_lead_time_have_one_filterable_ssot_each() {
		$this->assertSame( 365, alipasandi_booking_horizon_days() );
		$this->assertSame( 0, alipasandi_booking_min_lead_minutes() );
		add_filter( 'alipasandi_booking_horizon_days', function () { return 90; } );
		add_filter( 'alipasandi_booking_min_lead_minutes', function () { return 60; } );
		$this->assertSame( 90, alipasandi_booking_horizon_days() );
		$this->assertSame( 60, alipasandi_booking_min_lead_minutes() );
	}

	public function test_07_relative_date_boundaries_use_wordpress_timezone() {
		$this->set_booking_now( '2026-08-14 17:00:30' );
		$this->assertWPError( alipasandi_validate_appointment_date( '2026-08-13' ) );
		$this->assertInstanceOf( DateTimeImmutable::class, alipasandi_validate_appointment_date( '2026-08-14' ) );
		$this->assertInstanceOf( DateTimeImmutable::class, alipasandi_validate_appointment_date( '2027-08-14' ) );
		$this->assertWPError( alipasandi_validate_appointment_date( '2027-08-15' ) );
		$this->assertWPError( alipasandi_validate_appointment_date( '2026-02-29' ) );
		$this->assertWPError( alipasandi_validate_appointment_date( 'not-a-date' ) );
	}

	public function test_08_valid_leap_day_passes_when_inside_horizon() {
		$this->set_booking_now( '2028-01-01 09:00:00' );
		$this->assertInstanceOf( DateTimeImmutable::class, alipasandi_validate_appointment_date( '2028-02-29' ) );
	}

	public function test_09_same_day_past_or_equal_minute_fails_but_future_minute_passes() {
		$this->set_booking_now( '2026-08-14 17:00:30' );
		$date = alipasandi_validate_appointment_date( '2026-08-14' );
		$this->assertWPError( alipasandi_validate_appointment_datetime( $date, '10:00' ) );
		$this->assertWPError( alipasandi_validate_appointment_datetime( $date, '17:00' ) );
		$this->assertInstanceOf( DateTimeImmutable::class, alipasandi_validate_appointment_datetime( $date, '17:01' ) );
	}

	public function test_10_positive_lead_time_filter_is_enforced_by_same_backend_validator() {
		$this->set_booking_now( '2026-08-14 17:00:30' );
		add_filter( 'alipasandi_booking_min_lead_minutes', function () { return 60; } );
		$date = alipasandi_validate_appointment_date( '2026-08-14' );
		$this->assertWPError( alipasandi_validate_appointment_datetime( $date, '18:00' ) );
		$this->assertInstanceOf( DateTimeImmutable::class, alipasandi_validate_appointment_datetime( $date, '18:01' ) );
	}

	public function test_11_opening_hours_split_closed_adjacent_and_whitespace_contract() {
		$parsed = alipasandi_parse_opening_hours( "  MO = 09:00-13:00, 13:00-18:00  \n\nFR=CLOSED" );
		$this->assertEmpty( $parsed['errors'] );
		$this->assertCount( 2, $parsed['specs'] );
		$this->assertSame( array(), $parsed['schedule']['FR'] );
		$this->assertSame( "MO=09:00-13:00,13:00-18:00\nFR=CLOSED", $parsed['canonical'] );
	}

	public function test_12_opening_hours_duplicate_day_overlap_reversed_and_closed_conflict_fail() {
		$this->assertNotEmpty( alipasandi_parse_opening_hours( "MO=09:00-12:00\nMO=14:00-18:00" )['errors'] );
		$this->assertNotEmpty( alipasandi_parse_opening_hours( 'MO=09:00-13:00,12:00-18:00' )['errors'] );
		$this->assertNotEmpty( alipasandi_parse_opening_hours( 'MO=18:00-09:00' )['errors'] );
		$this->assertNotEmpty( alipasandi_parse_opening_hours( 'FR=CLOSED,09:00-10:00' )['errors'] );
	}

	public function test_13_opening_hours_persian_digits_normalize_and_missing_weekday_is_not_invented() {
		$parsed = alipasandi_parse_opening_hours( "MO=۰۹:۰۰-۱۳:۰۰\nFR=CLOSED" );
		$this->assertEmpty( $parsed['errors'] );
		$this->assertSame( "MO=09:00-13:00\nFR=CLOSED", $parsed['canonical'] );
		$this->assertArrayNotHasKey( 'TU', $parsed['schedule'] );
	}

	public function test_14_e164_country_and_geo_boundaries() {
		$this->assertTrue( alipasandi_valid_e164( '+989123456789' ) );
		$this->assertFalse( alipasandi_valid_e164( '989123456789' ) );
		$this->assertFalse( alipasandi_valid_e164( '+0989123456789' ) );
		$this->assertFalse( alipasandi_valid_e164( '+98ABC' ) );
		$this->assertFalse( alipasandi_valid_e164( '+98123' ) );
		$this->assertTrue( alipasandi_valid_country_code( 'IR' ) );
		$this->assertTrue( alipasandi_valid_country_code( 'ir' ) );
		$this->assertFalse( alipasandi_valid_country_code( 'IRN' ) );
		$this->assertTrue( alipasandi_valid_geo( '-90', 'lat' ) );
		$this->assertTrue( alipasandi_valid_geo( '90', 'lat' ) );
		$this->assertFalse( alipasandi_valid_geo( '-90.0001', 'lat' ) );
		$this->assertFalse( alipasandi_valid_geo( '90.0001', 'lat' ) );
		$this->assertTrue( alipasandi_valid_geo( '-180', 'lng' ) );
		$this->assertTrue( alipasandi_valid_geo( '180', 'lng' ) );
		$this->assertFalse( alipasandi_valid_geo( '-180.0001', 'lng' ) );
		$this->assertFalse( alipasandi_valid_geo( '180.0001', 'lng' ) );
	}

	public function test_15_duplicate_stable_service_identity_fails_closed() {
		$one = self::factory()->post->create( array( 'post_type'=>'page', 'post_status'=>'publish', 'post_title'=>'Implant A' ) );
		$two = self::factory()->post->create( array( 'post_type'=>'page', 'post_status'=>'publish', 'post_title'=>'Implant B' ) );
		update_post_meta( $one, ALIPASANDI_SERVICE_KEY_META, 'implant' );
		update_post_meta( $two, ALIPASANDI_SERVICE_KEY_META, 'implant' );
		$this->assertNull( alipasandi_get_service_page( 'implant' ) );
	}
	public function test_16_patient_phone_is_flexible_but_requires_real_digits() {
		$this->assertTrue( alipasandi_valid_patient_phone( '0912 345 6789' ) );
		$this->assertTrue( alipasandi_valid_patient_phone( '+98 (912) 345-6789' ) );
		$this->assertFalse( alipasandi_valid_patient_phone( '-------' ) );
		$this->assertFalse( alipasandi_valid_patient_phone( '09ABC123456' ) );
		$this->assertFalse( alipasandi_valid_patient_phone( '12345' ) );
	}
}
