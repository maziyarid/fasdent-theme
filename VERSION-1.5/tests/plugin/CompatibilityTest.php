<?php
/** Unit tests for Plugin/includes/compatibility.php (theme support contract). */

final class CompatibilityTest extends Alipasandi_TestCase {
	public function test_supported_theme_version_is_reported_compatible(): void {
		$this->assertSame( 'compatible', alipasandi_plugin_theme_compatibility() );
	}

	public function test_minimum_supported_theme_version_is_compatible(): void {
		wp_get_theme()->version = ALIPASANDI_SERVICE_SUPPORTED_THEME_MIN;
		$this->assertSame( 'compatible', alipasandi_plugin_theme_compatibility() );
	}

	public function test_older_theme_is_reported_outdated(): void {
		wp_get_theme()->version = '1.4.23';
		$this->assertSame( 'theme_outdated', alipasandi_plugin_theme_compatibility() );
	}

	public function test_foreign_theme_is_reported_without_a_version_check(): void {
		wp_get_theme()->stylesheet = 'twentytwentyfive';
		wp_get_theme()->version    = '1.0.0';

		$this->assertSame( 'other_theme', alipasandi_plugin_theme_compatibility() );
	}

	public function test_outdated_theme_notice_is_rendered_for_capable_users(): void {
		wp_get_theme()->version = '1.4.23';

		ob_start();
		alipasandi_plugin_compatibility_notice();
		$notice = ob_get_clean();

		$this->assertStringContainsString( 'notice notice-error', $notice );
		$this->assertStringContainsString( '1.4.24', $notice );
	}

	public function test_no_notice_is_rendered_for_a_supported_theme(): void {
		ob_start();
		alipasandi_plugin_compatibility_notice();
		$this->assertSame( '', ob_get_clean() );
	}
}
