<?php
/** Targeted WordPress PHPUnit regression tests for Plugin 1.3.4. */

class Alipasandi_Registry_And_Validators_Test extends WP_UnitTestCase {
	public function tear_down(): void {
		remove_all_filters( 'alipasandi_service_registry' );
		remove_all_filters( 'alipasandi_booking_horizon_days' );
		alipasandi_service_registry_state( true );
		parent::tear_down();
	}

	public function test_01_all_appointment_choices_derive_from_registry_only() {
		$registry = alipasandi_service_registry();
		$expected = array();
		foreach ( $registry as $item ) {
			if ( ! empty( $item['bookable'] ) ) { $expected[] = $item['label']; }
		}
		$this->assertSame( array_values( array_unique( $expected ) ), alipasandi_allowed_services() );
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
				'key' => 'malformed', 'label' => 'Malformed', 'bookable' => true,
				'page_slug' => '', 'content_managed' => false, 'icon' => 'tooth',
				'unknown_field' => 'must-not-pass',
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
				'key' => 'bad-icon', 'label' => 'Bad Icon', 'bookable' => true,
				'page_slug' => '', 'content_managed' => false, 'icon' => 'not-a-real-icon',
			);
			return $registry;
		} );
		$state = alipasandi_service_registry_state( true );
		$this->assertArrayNotHasKey( 'bad-icon', $state['items'] );
		$this->assertContains( 'invalid_icon:bad-icon', $state['issues'] );
	}

	public function test_05_booking_horizon_has_one_filterable_ssot() {
		$this->assertSame( 365, alipasandi_booking_horizon_days() );
		add_filter( 'alipasandi_booking_horizon_days', function () { return 90; } );
		$this->assertSame( 90, alipasandi_booking_horizon_days() );
	}

	public function test_06_relative_date_boundaries_use_wordpress_timezone() {
		$today = current_datetime()->setTime( 0, 0 );
		$this->assertWPError( alipasandi_validate_appointment_date( $today->modify( '-1 day' )->format( 'Y-m-d' ) ) );
		$this->assertInstanceOf( DateTimeImmutable::class, alipasandi_validate_appointment_date( $today->format( 'Y-m-d' ) ) );
		$this->assertInstanceOf( DateTimeImmutable::class, alipasandi_validate_appointment_date( $today->modify( '+365 days' )->format( 'Y-m-d' ) ) );
		$this->assertWPError( alipasandi_validate_appointment_date( $today->modify( '+366 days' )->format( 'Y-m-d' ) ) );
		$this->assertWPError( alipasandi_validate_appointment_date( '2026-02-29' ) );
		$this->assertWPError( alipasandi_validate_appointment_date( 'not-a-date' ) );
	}

	public function test_07_opening_hours_split_closed_and_invalid_contract() {
		$parsed = alipasandi_parse_opening_hours( "MO=09:00-13:00,14:00-18:00\nFR=CLOSED" );
		$this->assertEmpty( $parsed['errors'] );
		$this->assertCount( 2, $parsed['specs'] );
		$this->assertSame( array(), $parsed['schedule']['FR'] );
		$bad = alipasandi_parse_opening_hours( 'MO=18:00-09:00' );
		$this->assertNotEmpty( $bad['errors'] );
	}

	public function test_08_e164_country_and_geo_boundaries() {
		$this->assertTrue( alipasandi_valid_e164( '+989123456789' ) );
		$this->assertFalse( alipasandi_valid_e164( '989123456789' ) );
		$this->assertFalse( alipasandi_valid_e164( '+0989123456789' ) );
		$this->assertFalse( alipasandi_valid_e164( '+98ABC' ) );
		$this->assertFalse( alipasandi_valid_e164( '+98ABC9123456789' ) );
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

	public function test_09_booking_parser_supports_global_and_day_specific_grammar() {
		$global = alipasandi_parse_booking_times( "09:00\n10:30" );
		$this->assertEmpty( $global['errors'] );
		$this->assertSame( array( '09:00', '10:30' ), $global['by_day']['*'] );
		$daily = alipasandi_parse_booking_times( "MO=09:00,10:30\nFR=CLOSED" );
		$this->assertEmpty( $daily['errors'] );
		$this->assertSame( array( '09:00', '10:30' ), $daily['by_day']['MO'] );
		$this->assertSame( array(), $daily['by_day']['FR'] );
		$override = alipasandi_parse_booking_times( "09:00\nFR=CLOSED" );
		$this->assertSame( array(), alipasandi_slots_for_day( $override, 'FR' ) );
		$this->assertSame( array( '09:00' ), alipasandi_slots_for_day( $override, 'MO' ) );
	}

	public function test_10_opening_close_boundary_is_end_exclusive() {
		$hours = alipasandi_parse_opening_hours( 'MO=09:00-17:00' );
		$this->assertTrue( alipasandi_slot_within_schedule( 'MO', '09:00', $hours['schedule'] ) );
		$this->assertTrue( alipasandi_slot_within_schedule( 'MO', '16:59', $hours['schedule'] ) );
		$this->assertFalse( alipasandi_slot_within_schedule( 'MO', '17:00', $hours['schedule'] ) );
	}

	public function test_11_schedule_overlap_and_duplicate_day_fail_closed() {
		$overlap = alipasandi_parse_opening_hours( 'MO=09:00-13:00,12:30-15:00' );
		$this->assertNotEmpty( $overlap['errors'] );
		$duplicate = alipasandi_parse_opening_hours( "MO=09:00-12:00\nMO=14:00-17:00" );
		$this->assertNotEmpty( $duplicate['errors'] );
	}

	public function test_12_persian_digits_are_normalized_before_validation() {
		$this->assertSame( 9 * 60 + 30, alipasandi_time_minutes( '۰۹:۳۰' ) );
		$this->assertTrue( alipasandi_valid_e164( '‎+۹۸۹۱۲۳۴۵۶۷۸۹' ) );
		$this->assertTrue( alipasandi_valid_patient_phone( '۰۹۱۲ ۳۴۵ ۶۷۸۹' ) );
		$this->assertFalse( alipasandi_valid_patient_phone( 'abc09123456789' ) );
	}

	public function test_13_booking_policy_sanitizers_are_strict_and_bounded() {
		$this->assertSame( '365', alipasandi_sanitize_booking_horizon_option( '۳۶۵' ) );
		$this->assertSame( '', alipasandi_sanitize_booking_horizon_option( '0' ) );
		$this->assertSame( '', alipasandi_sanitize_booking_horizon_option( '-5' ) );
		$this->assertSame( '', alipasandi_sanitize_booking_horizon_option( '3651' ) );
		$this->assertSame( '0', alipasandi_sanitize_booking_lead_option( '0' ) );
		$this->assertSame( '43200', alipasandi_sanitize_booking_lead_option( '43200' ) );
		$this->assertSame( '', alipasandi_sanitize_booking_lead_option( '43201' ) );
	}
}
