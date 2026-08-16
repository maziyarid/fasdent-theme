<?php
/** Cause-specific, content-preserving schema repair diagnosis tests. */

class Alipasandi_Schema_Repair_Diagnosis_Test extends WP_UnitTestCase {
	private $page_id;
	private $valid_meta;

	public function set_up(): void {
		parent::set_up();
		$this->page_id = self::factory()->post->create( array( 'post_type'=>'page', 'post_status'=>'publish', 'post_title'=>'Test Service' ) );
		$this->valid_meta = alipasandi_sanitize_service_meta(
			array(
				'title' => 'Test service',
				'intro' => 'Approved introduction.',
			)
		);
	}

	public function test_01_missing_marker_only_is_repairable() {
		$meta = $this->valid_meta;
		unset( $meta['schema_version'] );
		update_post_meta( $this->page_id, ALIPASANDI_SERVICE_META_KEY, $meta );
		$diagnosis = alipasandi_service_schema_diagnosis( $this->page_id );
		$this->assertSame( array( 'missing_schema_marker' ), $diagnosis['categories'] );
		$this->assertTrue( $diagnosis['repairable'] );
	}

	public function test_02_unknown_schema_version_is_never_silently_rewritten() {
		$meta = $this->valid_meta;
		$meta['schema_version'] = ALIPASANDI_SERVICE_META_SCHEMA + 1;
		update_post_meta( $this->page_id, ALIPASANDI_SERVICE_META_KEY, $meta );
		$diagnosis = alipasandi_service_schema_diagnosis( $this->page_id );
		$this->assertContains( 'unknown_schema_version', $diagnosis['categories'] );
		$this->assertFalse( $diagnosis['repairable'] );
	}

	public function test_03_required_content_or_sanitizer_drift_blocks_repair() {
		$meta = $this->valid_meta;
		unset( $meta['schema_version'] );
		$meta['intro'] = '';
		$meta['unexpected_field'] = 'must not be normalized automatically';
		update_post_meta( $this->page_id, ALIPASANDI_SERVICE_META_KEY, $meta );
		$diagnosis = alipasandi_service_schema_diagnosis( $this->page_id );
		$this->assertContains( 'required_field_content_missing', $diagnosis['categories'] );
		$this->assertContains( 'sanitizer_drift', $diagnosis['categories'] );
		$this->assertFalse( $diagnosis['repairable'] );
	}

	public function test_04_duplicate_meta_rows_are_malformed_and_blocked() {
		add_post_meta( $this->page_id, ALIPASANDI_SERVICE_META_KEY, $this->valid_meta, false );
		add_post_meta( $this->page_id, ALIPASANDI_SERVICE_META_KEY, $this->valid_meta, false );
		$diagnosis = alipasandi_service_schema_diagnosis( $this->page_id );
		$this->assertSame( array( 'malformed_meta' ), $diagnosis['categories'] );
		$this->assertSame( 'duplicate_meta_rows', $diagnosis['reason'] );
		$this->assertFalse( $diagnosis['repairable'] );
	}

	public function test_05_schema_marker_is_excluded_from_content_hash() {
		$before = $this->valid_meta;
		unset( $before['schema_version'] );
		$after = $before;
		$after['schema_version'] = ALIPASANDI_SERVICE_META_SCHEMA;
		$this->assertSame( alipasandi_service_content_sha256( $before ), alipasandi_service_content_sha256( $after ) );
	}
}
