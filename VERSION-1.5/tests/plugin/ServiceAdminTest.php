<?php
/** Unit tests for the admin-facing half of Plugin/includes/service-content.php. */

final class ServiceAdminTest extends Alipasandi_TestCase {
	private const NOTICE_KEY = 'alipasandi_service_notice_7';

	/** Register a service page with the stable key stamped on it. */
	private function add_service_page( int $id = 10, string $key = 'implant' ): int {
		WP_Test_State::add_post( $id, 'page', array( 'post_name' => $key ) );
		update_post_meta( $id, ALIPASANDI_SERVICE_KEY_META, $key );
		return $id;
	}

	/** A complete meta-box payload. */
	private function payload( array $overrides = array() ): array {
		return array_merge(
			array(
				'title'          => 'ایمپلنت تک‌دندان',
				'intro'          => 'معرفی درمان',
				'what_text_raw'  => "پاراگراف اول\n\nپاراگراف دوم\n\n   ",
				'diagram_raw'    => 'پایه، اباتمنت, روکش',
				'benefits'       => array(
					array( 'title' => 'دقت', 'text' => '<strong>متن</strong>', 'icon' => 'shield' ),
					array( 'title' => '', 'text' => '' ),
				),
				'steps'          => array( array( 'number' => '01', 'title' => 'معاینه', 'text' => 'متن' ) ),
				'faqs'           => array( array( 'question' => 'چقدر طول می‌کشد؟', 'answer' => 'بستگی دارد.' ) ),
			),
			$overrides
		);
	}

	/** Submit a meta-box payload for a post. */
	private function submit( int $post_id, array $payload = null, bool $complete = true, string $nonce = null ): void {
		$_POST = array(
			'alipasandi_service_meta_nonce' => null === $nonce ? wp_create_nonce( 'alipasandi_save_service_meta' ) : $nonce,
			'alipasandi_svc'                => null === $payload ? $this->payload() : $payload,
		);
		if ( $complete ) {
			$_POST['alipasandi_svc_payload_complete'] = '1';
		}
		alipasandi_save_service_meta( $post_id );
	}

	public function test_save_stores_sanitized_structured_content(): void {
		$id = $this->add_service_page();

		$this->submit( $id );
		$meta = get_post_meta( $id, ALIPASANDI_SERVICE_META_KEY, true );

		$this->assertSame( 'ایمپلنت تک‌دندان', $meta['title'] );
		$this->assertSame( array( 'پاراگراف اول', 'پاراگراف دوم' ), $meta['what_text'], 'Blank blocks are dropped.' );
		$this->assertSame( array( 'پایه', 'اباتمنت', 'روکش' ), $meta['diagram'], 'Both Persian and ASCII commas split the list.' );
		$this->assertSame( array( array( 'دقت', '<strong>متن</strong>', 'shield' ) ), $meta['benefits'], 'Empty benefit rows are dropped.' );
		$this->assertSame( array( array( '01', 'معاینه', 'متن' ) ), $meta['steps'] );
		$this->assertSame( array( array( 'چقدر طول می‌کشد؟', 'بستگی دارد.' ) ), $meta['faqs'] );
		$this->assertSame( ALIPASANDI_SERVICE_META_SCHEMA, $meta['schema_version'] );
	}

	public function test_save_requires_a_valid_nonce(): void {
		$id = $this->add_service_page();

		$this->submit( $id, null, true, 'forged' );

		$this->assertFalse( alipasandi_service_meta_exists( $id ) );
	}

	public function test_save_ignores_pages_that_are_not_service_pages(): void {
		WP_Test_State::add_post( 11, 'page', array( 'post_name' => 'about-us' ) );

		$this->submit( 11 );

		$this->assertFalse( alipasandi_service_meta_exists( 11 ) );
	}

	public function test_save_skips_revisions_and_autosaves(): void {
		$id = $this->add_service_page();
		WP_Test_State::add_post( 12, 'revision', array( 'post_parent' => $id ) );
		WP_Test_State::add_post( 13, 'page', array( 'post_parent' => $id, 'is_autosave' => true ) );

		$this->submit( 12 );
		$this->submit( 13 );

		$this->assertFalse( alipasandi_service_meta_exists( 12 ) );
		$this->assertFalse( alipasandi_service_meta_exists( 13 ) );
	}

	public function test_save_leaves_existing_meta_untouched_when_the_meta_box_was_not_submitted(): void {
		$id = $this->add_service_page();
		update_post_meta( $id, ALIPASANDI_SERVICE_META_KEY, array( 'title' => 'قبلی' ) );

		$_POST = array( 'alipasandi_service_meta_nonce' => wp_create_nonce( 'alipasandi_save_service_meta' ) );
		alipasandi_save_service_meta( $id );

		$this->assertSame( array( 'title' => 'قبلی' ), get_post_meta( $id, ALIPASANDI_SERVICE_META_KEY, true ) );
		$this->assertFalse( get_transient( self::NOTICE_KEY ), 'An absent payload is not an error.' );
	}

	public function test_truncated_post_requests_are_refused_and_reported(): void {
		$id = $this->add_service_page();
		update_post_meta( $id, ALIPASANDI_SERVICE_META_KEY, array( 'title' => 'قبلی' ) );

		$this->submit( $id, null, false );

		$this->assertSame( array( 'title' => 'قبلی' ), get_post_meta( $id, ALIPASANDI_SERVICE_META_KEY, true ) );
		$this->assertSame( 'partial_post', get_transient( self::NOTICE_KEY ) );
	}

	public function test_duplicate_service_keys_block_saving(): void {
		$id = $this->add_service_page();
		$this->add_service_page( 20 );

		$this->submit( $id );

		$this->assertFalse( alipasandi_service_meta_exists( $id ) );
		$this->assertSame( 'duplicate_key', get_transient( self::NOTICE_KEY ) );
	}

	public function test_title_and_intro_are_required(): void {
		$id = $this->add_service_page();

		$this->submit( $id, $this->payload( array( 'intro' => '' ) ) );
		$this->assertFalse( alipasandi_service_meta_exists( $id ) );
		$this->assertSame( 'required', get_transient( self::NOTICE_KEY ) );

		delete_transient( self::NOTICE_KEY );
		$this->submit( $id, $this->payload( array( 'title' => '' ) ) );
		$this->assertFalse( alipasandi_service_meta_exists( $id ) );
		$this->assertSame( 'required', get_transient( self::NOTICE_KEY ) );
	}

	public function test_content_images_inherit_alt_text_from_the_media_library(): void {
		$id = $this->add_service_page();
		WP_Test_State::add_post( 30, 'attachment', array( 'is_image' => true ) );
		update_post_meta( 30, '_wp_attachment_image_alt', '  عکس درمان  ' );

		$this->submit( $id, $this->payload( array( 'image_id' => 30 ) ) );

		$this->assertSame( 'عکس درمان', get_post_meta( $id, ALIPASANDI_SERVICE_META_KEY, true )['image_alt'] );
	}

	public function test_content_images_without_any_alt_text_are_refused(): void {
		$id = $this->add_service_page();
		WP_Test_State::add_post( 30, 'attachment', array( 'is_image' => true ) );

		$this->submit( $id, $this->payload( array( 'image_id' => 30 ) ) );

		$this->assertFalse( alipasandi_service_meta_exists( $id ) );
		$this->assertSame( 'image_alt', get_transient( self::NOTICE_KEY ) );
	}

	public function test_decorative_images_do_not_need_alt_text(): void {
		$id = $this->add_service_page();
		WP_Test_State::add_post( 30, 'attachment', array( 'is_image' => true ) );

		$this->submit( $id, $this->payload( array( 'image_id' => 30, 'image_decorative' => '1' ) ) );

		$meta = get_post_meta( $id, ALIPASANDI_SERVICE_META_KEY, true );
		$this->assertSame( 30, $meta['image_id'] );
		$this->assertSame( '', $meta['image_alt'] );
	}

	public function test_admin_notice_is_rendered_once_and_then_cleared(): void {
		set_transient( self::NOTICE_KEY, 'required', 60 );

		ob_start();
		alipasandi_service_admin_notice();
		$notice = ob_get_clean();

		$this->assertStringContainsString( 'notice notice-error', $notice );
		$this->assertFalse( get_transient( self::NOTICE_KEY ) );

		ob_start();
		alipasandi_service_admin_notice();
		$this->assertSame( '', ob_get_clean(), 'The notice is not repeated.' );
	}

	public function test_unknown_notice_codes_render_nothing(): void {
		set_transient( self::NOTICE_KEY, 'made_up_code', 60 );

		ob_start();
		alipasandi_service_admin_notice();

		$this->assertSame( '', ob_get_clean() );
	}

	public function test_health_report_flags_every_broken_service_record(): void {
		$this->add_service_page( 40, 'implant' );
		update_post_meta( 40, ALIPASANDI_SERVICE_META_KEY, array( 'title' => 'ایمپلنت' ) );
		WP_Test_State::add_post( 41, 'page', array( 'post_name' => 'crown' ) );

		$report = alipasandi_service_health_report();

		$this->assertFalse( $report['pass'] );
		$this->assertFalse( $report['migration_completed'] );
		$this->assertSame( ALIPASANDI_SERVICE_META_SCHEMA, $report['schema_version'] );
		$this->assertSame( ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION, $report['plugin_version'] );
		$this->assertSame( array( 'schema_invalid', 'required_fields_missing' ), $report['services']['implant']['issues'] );
		$this->assertSame( array( 'meta_missing' ), $report['services']['crown']['issues'] );
		$this->assertSame( array( 'page_missing' ), $report['services']['surgery']['issues'] );
		$this->assertSame( 40, $report['services']['implant']['page_id'] );
	}

	public function test_health_report_flags_missing_image_files(): void {
		$id = $this->add_service_page( 50, 'implant' );
		WP_Test_State::add_post( 51, 'attachment', array( 'is_image' => true ) );
		update_post_meta(
			$id,
			ALIPASANDI_SERVICE_META_KEY,
			array( 'schema_version' => ALIPASANDI_SERVICE_META_SCHEMA, 'title' => 'ایمپلنت', 'intro' => 'متن', 'image_id' => 51 )
		);

		$this->assertSame( array( 'image_invalid' ), alipasandi_service_health_report()['services']['implant']['issues'] );
	}

	public function test_health_report_passes_for_a_fully_migrated_site(): void {
		$id = 60;
		foreach ( alipasandi_service_keys() as $key ) {
			WP_Test_State::add_post( $id++, 'page', array( 'post_name' => $key ) );
		}
		alipasandi_migrate_service_content_to_meta();

		$report = alipasandi_service_health_report();

		$this->assertTrue( $report['migration_completed'] );
		$this->assertTrue( $report['pass'], 'A migrated site must report healthy: ' . wp_json_encode( $report['services'] ) );
	}

	public function test_export_payload_is_checksummed_and_covers_every_service(): void {
		$this->add_service_page( 70, 'implant' );
		update_post_meta( 70, ALIPASANDI_SERVICE_META_KEY, array( 'title' => 'ایمپلنت' ) );

		$payload = alipasandi_service_export_payload();

		$this->assertSame( ALIPASANDI_SERVICE_META_SCHEMA, $payload['schema_version'] );
		$this->assertSame( alipasandi_service_keys(), array_keys( $payload['services'] ) );
		$this->assertSame( 70, $payload['services']['implant']['page_id'] );
		$this->assertSame( 'implant', $payload['services']['implant']['slug'] );
		$this->assertSame( array( 'title' => 'ایمپلنت' ), $payload['services']['implant']['meta'] );
		$this->assertNull( $payload['services']['crown']['meta'], 'Services without a page export a null payload.' );
		$this->assertSame(
			hash( 'sha256', wp_json_encode( $payload['services'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ),
			$payload['content_sha256']
		);
	}

	public function test_site_health_tests_are_registered_and_report_status(): void {
		$tests = alipasandi_plugin_site_health_tests( array( 'direct' => array() ) );

		$this->assertArrayHasKey( 'alipasandi_service_content_health', $tests['direct'] );
		$this->assertArrayHasKey( 'alipasandi_rank_math_owner', $tests['direct'] );

		$content = call_user_func( $tests['direct']['alipasandi_service_content_health']['test'] );
		$this->assertSame( 'critical', $content['status'], 'An unmigrated site is critical.' );
		$this->assertSame( 'alipasandi_service_content_health', $content['test'] );

		$seo = call_user_func( $tests['direct']['alipasandi_rank_math_owner']['test'] );
		$this->assertSame( 'critical', $seo['status'], 'Rank Math owns production SEO and is absent here.' );
	}
}
