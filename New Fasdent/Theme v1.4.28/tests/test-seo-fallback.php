<?php
/**
 * Permanent regression contract for emergency SEO/indexability failure mode.
 * Run inside the WordPress PHPUnit suite; never loaded by the public Theme.
 */

class Alipasandi_SEO_Fallback_Regression_Test extends WP_UnitTestCase {
	private $page_id;
	private $post_id;
	private $appointment_id;

	public function set_up(): void {
		parent::set_up();
		$this->page_id = self::factory()->post->create( array( 'post_type'=>'page', 'post_status'=>'publish', 'post_title'=>'SEO Fallback Page' ) );
		$this->post_id = self::factory()->post->create( array( 'post_type'=>'post', 'post_status'=>'publish', 'post_title'=>'SEO Fallback Post' ) );
		$this->appointment_id = self::factory()->post->create( array( 'post_type'=>'page', 'post_status'=>'publish', 'post_title'=>'درخواست نوبت', 'post_name'=>'appointments' ) );
		add_filter( 'alipasandi_seo_plugin_active', '__return_false', 999 );
	}

	public function tear_down(): void {
		remove_filter( 'alipasandi_seo_plugin_active', '__return_false', 999 );
		remove_filter( 'alipasandi_seo_plugin_active', '__return_true', 1000 );
		parent::tear_down();
	}

	private function fallback_output() {
		ob_start();
		alipasandi_output_social_meta();
		return ob_get_clean();
	}

	private function robots() {
		return apply_filters( 'wp_robots', array() );
	}

	public function test_01_published_page_inactive_has_safe_canonical() {
		$this->go_to( get_permalink( $this->page_id ) );
		$out = $this->fallback_output();
		$this->assertStringContainsString( 'property="og:url"', $out );
		$this->assertStringContainsString( esc_url( get_permalink( $this->page_id ) ), $out );
	}

	public function test_02_published_post_inactive_has_safe_canonical() {
		$this->go_to( get_permalink( $this->post_id ) );
		$out = $this->fallback_output();
		$this->assertStringContainsString( 'property="og:type" content="article"', $out );
		$this->assertStringContainsString( 'property="og:url"', $out );
	}

	public function test_03_home_emits_no_singular_meta_fallback() {
		$this->go_to( home_url( '/' ) );
		$this->assertSame( '', $this->fallback_output() );
	}

	public function test_04_appointment_inactive_is_noindex_follow() {
		$this->go_to( get_permalink( $this->appointment_id ) );
		$robots = $this->robots();
		$this->assertTrue( isset( $robots['noindex'] ) && $robots['noindex'] );
		$this->assertTrue( isset( $robots['follow'] ) && $robots['follow'] );
	}

	public function test_05_search_inactive_is_noindex_follow() {
		$this->go_to( home_url( '/?s=implant' ) );
		$this->assertSame( '', $this->fallback_output() );
		$robots = $this->robots();
		$this->assertTrue( isset( $robots['noindex'] ) && $robots['noindex'] );
		$this->assertTrue( isset( $robots['follow'] ) && $robots['follow'] );
	}

	public function test_06_404_inactive_is_noindex_nofollow_and_real_404() {
		$this->go_to( home_url( '/__alipasandi_missing__/' ) );
		$this->assertTrue( is_404() );
		$this->assertSame( '', $this->fallback_output() );
		$robots = $this->robots();
		$this->assertTrue( isset( $robots['noindex'] ) && $robots['noindex'] );
		$this->assertTrue( isset( $robots['nofollow'] ) && $robots['nofollow'] );
	}

	public function test_07_archive_inactive_is_noindex_follow() {
		$cat_id = self::factory()->category->create( array( 'name'=>'Fallback Archive' ) );
		$this->go_to( get_category_link( $cat_id ) );
		$this->assertSame( '', $this->fallback_output() );
		$robots = $this->robots();
		$this->assertTrue( isset( $robots['noindex'] ) && $robots['noindex'] );
		$this->assertTrue( isset( $robots['follow'] ) && $robots['follow'] );
	}

	public function test_08_blog_archive_current_matrix_is_noindex_follow() {
		update_option( 'show_on_front', 'page' );
		$front = self::factory()->post->create( array( 'post_type'=>'page', 'post_status'=>'publish', 'post_title'=>'Home' ) );
		$posts = self::factory()->post->create( array( 'post_type'=>'page', 'post_status'=>'publish', 'post_title'=>'Blog' ) );
		update_option( 'page_on_front', $front );
		update_option( 'page_for_posts', $posts );
		$this->go_to( get_permalink( $posts ) );
		$this->assertTrue( is_home() );
		$robots = $this->robots();
		$this->assertTrue( isset( $robots['noindex'] ) && $robots['noindex'] );
		$this->assertTrue( isset( $robots['follow'] ) && $robots['follow'] );
	}

	public function test_09_feed_contract_value_is_exact() {
		$this->assertSame( 'noindex, follow', alipasandi_feed_robots_header_value() );
	}

	public function test_10_core_sitemap_is_disabled_during_seo_owner_failure() {
		$this->assertFalse( apply_filters( 'wp_sitemaps_enabled', true ) );
	}

	public function test_11_active_owner_suppresses_emergency_robots_and_preserves_sitemap() {
		$this->go_to( get_permalink( $this->appointment_id ) );
		add_filter( 'alipasandi_seo_plugin_active', '__return_true', 1000 );
		$robots = $this->robots();
		$this->assertArrayNotHasKey( 'noindex', $robots, 'Theme must not duplicate Rank Math robots policy while owner is active.' );
		$this->assertTrue( apply_filters( 'wp_sitemaps_enabled', true ) );
		$this->assertSame( '', $this->fallback_output() );
	}

	public function test_12_active_inactive_reactivated_failure_mode_is_deterministic() {
		$this->go_to( get_permalink( $this->appointment_id ) );
		$this->assertTrue( isset( $this->robots()['noindex'] ) );
		add_filter( 'alipasandi_seo_plugin_active', '__return_true', 1000 );
		$this->assertArrayNotHasKey( 'noindex', $this->robots() );
		remove_filter( 'alipasandi_seo_plugin_active', '__return_true', 1000 );
		$this->assertTrue( isset( $this->robots()['noindex'] ) );
		$this->assertFalse( apply_filters( 'wp_sitemaps_enabled', true ) );
	}
}
