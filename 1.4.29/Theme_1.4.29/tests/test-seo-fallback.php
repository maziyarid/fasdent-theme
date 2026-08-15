<?php
/**
 * Permanent regression contract for the emergency SEO fallback.
 * Run inside the WordPress PHPUnit test suite; never loaded by the theme.
 */

class Alipasandi_SEO_Fallback_Regression_Test extends WP_UnitTestCase {
	private $page_id;
	private $post_id;

	public function set_up(): void {
		parent::set_up();
		$this->page_id = self::factory()->post->create( array( 'post_type'=>'page', 'post_status'=>'publish', 'post_title'=>'SEO Fallback Page' ) );
		$this->post_id = self::factory()->post->create( array( 'post_type'=>'post', 'post_status'=>'publish', 'post_title'=>'SEO Fallback Post' ) );
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

	public function test_03_home_context_emits_no_singular_fallback() {
		$this->go_to( home_url( '/' ) );
		$this->assertSame( '', $this->fallback_output() );
	}

	public function test_04_search_context_emits_no_singular_fallback() {
		$this->go_to( home_url( '/?s=implant' ) );
		$this->assertSame( '', $this->fallback_output() );
	}

	public function test_05_404_context_emits_no_singular_fallback() {
		$this->go_to( home_url( '/__alipasandi_missing__/' ) );
		$this->assertSame( '', $this->fallback_output() );
	}

	public function test_06_archive_context_emits_no_singular_fallback() {
		$cat_id = self::factory()->category->create( array( 'name'=>'Fallback Archive' ) );
		$this->go_to( get_category_link( $cat_id ) );
		$this->assertSame( '', $this->fallback_output() );
	}

	public function test_07_feed_context_emits_no_singular_fallback() {
		$this->go_to( get_feed_link() );
		$this->assertSame( '', $this->fallback_output() );
	}

	public function test_08_active_inactive_reactivated_contract() {
		$this->go_to( get_permalink( $this->page_id ) );
		$this->assertNotSame( '', $this->fallback_output(), 'Inactive SEO owner should permit fallback.' );
		add_filter( 'alipasandi_seo_plugin_active', '__return_true', 1000 );
		$this->assertSame( '', $this->fallback_output(), 'Active SEO owner must suppress theme fallback.' );
		remove_filter( 'alipasandi_seo_plugin_active', '__return_true', 1000 );
		$this->assertNotSame( '', $this->fallback_output(), 'After deactivation/reactivation simulation, fallback must remain callable without fatal.' );
	}
}
