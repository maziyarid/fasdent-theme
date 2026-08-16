<?php
/** PHPUnit unit contract; the release runbook also executes the actual HTTP probe. */

class Alipasandi_NAP_Consistency_Test extends WP_UnitTestCase {
	private $options = array(
		'clinic_business_name' => 'Test Clinic',
		'clinic_street'         => '1 Test Street',
		'clinic_city'           => 'Test City',
		'clinic_region'         => 'Test Region',
		'clinic_country'        => 'IR',
		'clinic_address'        => '1 Test Street، Test City، Test Region، IR',
		'clinic_phone'          => '+98 11 0000 0000',
		'clinic_phone_e164'     => '+981100000000',
		'local_schema_owner'    => 'rank_math',
	);

	public function set_up(): void {
		parent::set_up();
		foreach ( $this->options as $key => $value ) {
			update_option( alipasandi_clinic_option_name( $key ), $value, false );
		}
	}

	public function tear_down(): void {
		foreach ( array_keys( $this->options ) as $key ) {
			delete_option( alipasandi_clinic_option_name( $key ) );
		}
		delete_option( 'alipasandi_rank_math_observations_v1' );
		parent::tear_down();
	}

	public function test_01_canonical_nap_is_complete() {
		$this->assertTrue( Alipasandi_NAP_Helper::is_complete() );
		$this->assertSame( '+981100000000', alipasandi_phone_href() );
		$this->assertSame( $this->options['clinic_address'], alipasandi_display_address() );
	}

	public function test_02_surface_marker_is_bound_to_canonical_values() {
		ob_start();
		alipasandi_nap_surface_marker( 'home' );
		$output = ob_get_clean();
		$this->assertSame( alipasandi_nap_surface_hash(), alipasandi_extract_nap_marker( $output, 'home' ) );
	}

	public function test_03_mail_uses_the_same_canonical_values() {
		$mail = alipasandi_mail_nap_block();
		$this->assertStringContainsString( $this->options['clinic_business_name'], $mail );
		$this->assertStringContainsString( $this->options['clinic_address'], $mail );
		$this->assertStringContainsString( $this->options['clinic_phone_e164'], $mail );
	}

	public function test_04_partial_nap_fails_closed() {
		delete_option( alipasandi_clinic_option_name( 'clinic_street' ) );
		$this->assertFalse( Alipasandi_NAP_Helper::is_complete() );
		$this->assertSame( array(), Alipasandi_NAP_Helper::structured_address() );
	}

	public function test_05_non_http_consistency_contract_passes() {
		$report = alipasandi_nap_consistency_report( false );
		$this->assertTrue( $report['pass'] );
		$this->assertSame( $report['expected_sha256'], $report['surfaces']['email'] );
	}

	public function test_06_rank_math_local_entity_is_normalized_or_removed_without_list_drift() {
		$input = array(
			array( '@type'=>'Dentist', 'name'=>'Stale', 'address'=>array( '@type'=>'PostalAddress', 'streetAddress'=>'Stale' ) ),
			array( '@type'=>'WebSite', 'name'=>'Site' ),
		);
		$normalized = alipasandi_rank_math_central_nap( $input );
		$this->assertSame( 'Test Clinic', $normalized[0]['name'] );
		$this->assertSame( '1 Test Street', $normalized[0]['address']['streetAddress'] );
		update_option( alipasandi_clinic_option_name( 'local_schema_owner' ), 'disabled' );
		$disabled = alipasandi_rank_math_central_nap( $input );
		$this->assertTrue( array_is_list( $disabled ) );
		$this->assertCount( 1, $disabled );
		$this->assertSame( 'WebSite', $disabled[0]['@type'] );
	}
}
