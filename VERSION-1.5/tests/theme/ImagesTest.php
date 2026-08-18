<?php
/** Unit tests for Theme/inc/images.php (bundled responsive image markup). */

final class ImagesTest extends Alipasandi_TestCase {
	/** Render the helper and return its markup. */
	private function render( string $filename, string $alt = '', array $args = array() ): string {
		ob_start();
		alipasandi_theme_image( $filename, $alt, $args );
		return (string) ob_get_clean();
	}

	public function test_missing_files_render_nothing(): void {
		$this->assertSame( '', $this->render( 'does-not-exist.jpg', 'alt' ) );
	}

	public function test_path_traversal_is_reduced_to_a_basename(): void {
		$this->assertSame( '', $this->render( '../../wp-config.php' ) );
	}

	public function test_bundled_image_renders_intrinsic_dimensions_and_webp_sources(): void {
		$html = $this->render( 'implant-hero.jpg', 'ایمپلنت' );
		$size = getimagesize( get_template_directory() . '/assets/images/implant-hero.jpg' );

		$this->assertStringContainsString( '<picture>', $html );
		$this->assertStringContainsString( 'src="' . get_template_directory_uri() . '/assets/images/implant-hero.jpg"', $html );
		$this->assertStringContainsString( 'alt="ایمپلنت"', $html );
		$this->assertStringContainsString( 'width="' . $size[0] . '" height="' . $size[1] . '"', $html );
		$this->assertStringContainsString( 'decoding="async"', $html );
		$this->assertStringContainsString( 'loading="lazy"', $html );
		$this->assertStringContainsString( 'type="image/webp"', $html );
		$this->assertMatchesRegularExpression( '/srcset="[^"]*implant-hero-\d+\.webp \d+w/', $html );
	}

	public function test_decorative_images_drop_the_alternative_text(): void {
		$html = $this->render( 'implant-hero.jpg', 'ایمپلنت', array( 'decorative' => true ) );

		$this->assertStringContainsString( 'alt=""', $html );
	}

	public function test_priority_arguments_are_passed_through(): void {
		$html = $this->render(
			'implant-hero.jpg',
			'ایمپلنت',
			array(
				'class'         => 'hero-image',
				'sizes'         => '(max-width: 768px) 100vw, 50vw',
				'loading'       => '',
				'fetchpriority' => 'high',
			)
		);

		$this->assertStringContainsString( 'class="hero-image"', $html );
		$this->assertStringContainsString( 'fetchpriority="high"', $html );
		$this->assertStringContainsString( 'sizes="(max-width: 768px) 100vw, 50vw"', $html );
		$this->assertStringNotContainsString( 'loading=', $html, 'An empty loading value opts out of the attribute.' );
	}
}
