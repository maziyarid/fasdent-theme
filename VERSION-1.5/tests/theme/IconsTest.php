<?php
/** Unit tests for Theme/inc/icons.php (inline SVG icon contract). */

final class IconsTest extends Alipasandi_TestCase {
	/** Icon keys the theme templates and the plugin benefit picker rely on. */
	private const REQUIRED_ICONS = array(
		'tooth',
		'implant',
		'crown',
		'surgery',
		'general',
		'calendar',
		'phone',
		'location',
		'check',
		'arrow',
		'menu',
		'close',
		'chevron',
		'info',
		'shield',
		'clipboard',
		'clock',
		'chat',
		'instagram',
		'whatsapp',
		'telegram',
	);

	public function test_unknown_icon_returns_empty_string(): void {
		$this->assertSame( '', alipasandi_icon( 'not-an-icon' ) );
		$this->assertSame( '', alipasandi_icon( '' ) );
	}

	public function test_every_required_icon_renders_a_decorative_svg(): void {
		foreach ( self::REQUIRED_ICONS as $name ) {
			$svg = alipasandi_icon( $name );

			$this->assertStringStartsWith( '<svg class="icon icon-' . $name . '"', $svg, 'Missing icon: ' . $name );
			$this->assertStringEndsWith( '</svg>', $svg );
			$this->assertStringContainsString( 'viewBox="0 0 24 24"', $svg );
			$this->assertStringContainsString( 'aria-hidden="true"', $svg, 'Icons are decorative: ' . $name );
			$this->assertStringContainsString( 'focusable="false"', $svg, 'Icons must stay out of tab order: ' . $name );
			$this->assertStringContainsString( 'currentColor', $svg, 'Icons must inherit colour: ' . $name );
		}
	}

	public function test_default_size_is_24_square(): void {
		$this->assertStringContainsString( 'width="24" height="24"', alipasandi_icon( 'tooth' ) );
	}

	public function test_size_is_clamped_between_12_and_96(): void {
		$this->assertStringContainsString( 'width="12" height="12"', alipasandi_icon( 'tooth', 4 ) );
		$this->assertStringContainsString( 'width="96" height="96"', alipasandi_icon( 'tooth', 500 ) );
		$this->assertStringContainsString( 'width="40" height="40"', alipasandi_icon( 'tooth', -40 ), 'Negative sizes are absolute values, not clamped to the minimum.' );
		$this->assertStringContainsString( 'width="32" height="32"', alipasandi_icon( 'tooth', '32' ) );
	}

	public function test_benefit_icons_accepted_by_the_plugin_all_exist_in_the_theme(): void {
		foreach ( array( 'tooth', 'crown', 'general', 'implant', 'shield', 'clock', 'surgery', 'info' ) as $name ) {
			$this->assertNotSame( '', alipasandi_icon( $name ), 'Plugin-allowed benefit icon missing from the theme: ' . $name );
		}
	}
}
