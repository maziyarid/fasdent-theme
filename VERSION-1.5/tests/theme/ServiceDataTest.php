<?php
/** Unit tests for Theme/inc/service-data.php (read-only legacy service content). */

final class ServiceDataTest extends Alipasandi_TestCase {
	public function test_unknown_keys_return_an_empty_array(): void {
		$this->assertSame( array(), alipasandi_get_service_legacy( 'whitening' ) );
		$this->assertSame( array(), alipasandi_get_service_legacy( '' ) );
	}

	public function test_every_registered_service_key_has_legacy_content(): void {
		foreach ( alipasandi_service_keys() as $key ) {
			$service = alipasandi_get_service_legacy( $key );

			$this->assertNotSame( array(), $service, 'Missing legacy fallback for: ' . $key );
			foreach ( array( 'title', 'intro', 'image', 'image_alt', 'what_text', 'benefits', 'steps', 'faqs' ) as $field ) {
				$this->assertArrayHasKey( $field, $service, $key . ' is missing ' . $field );
				$this->assertNotEmpty( $service[ $field ], $key . ' has an empty ' . $field );
			}
		}
	}

	public function test_legacy_hero_images_exist_in_the_theme(): void {
		foreach ( alipasandi_service_keys() as $key ) {
			$image = alipasandi_get_service_legacy( $key )['image'];

			$this->assertFileExists( get_template_directory() . '/assets/images/' . $image );
		}
	}

	public function test_legacy_rows_match_the_shapes_the_sanitizer_expects(): void {
		foreach ( alipasandi_service_keys() as $key ) {
			$service = alipasandi_get_service_legacy( $key );

			foreach ( $service['benefits'] as $benefit ) {
				$this->assertCount( 3, $benefit, $key . ' benefits are title/text/icon rows.' );
				$this->assertNotSame( '', alipasandi_icon( $benefit[2] ), 'Unknown benefit icon in ' . $key . ': ' . $benefit[2] );
			}
			foreach ( $service['steps'] as $step ) {
				$this->assertCount( 3, $step, $key . ' steps are number/title/text rows.' );
			}
			foreach ( $service['faqs'] as $faq ) {
				$this->assertCount( 2, $faq, $key . ' FAQs are question/answer rows.' );
			}
		}
	}

	public function test_legacy_content_survives_sanitization_without_loss(): void {
		foreach ( alipasandi_service_keys() as $key ) {
			$service = alipasandi_get_service_legacy( $key );
			$clean   = alipasandi_sanitize_service_meta( $service );

			$this->assertSame( $service['title'], $clean['title'], $key . ' title changed during sanitization.' );
			$this->assertSame( $service['intro'], $clean['intro'], $key . ' intro changed during sanitization.' );
			$this->assertCount( count( $service['benefits'] ), $clean['benefits'], $key . ' lost benefit rows.' );
			$this->assertCount( count( $service['steps'] ), $clean['steps'], $key . ' lost step rows.' );
			$this->assertCount( count( $service['faqs'] ), $clean['faqs'], $key . ' lost FAQ rows.' );
		}
	}
}
