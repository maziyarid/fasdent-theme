<?php
/**
 * Performance helpers — Fasdent
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * افزودن loading="lazy" و decoding="async" به تصاویر داخل محتوا.
 *
 * @param string $content محتوای پست.
 * @return string
 */
function fasdent_lazy_load_images( string $content ): string {
	if ( ! is_singular() && ! is_home() ) {
		return $content;
	}
	return preg_replace( '/<img(.*?)src=(["\'])(.*?)\2(.*?)>/i', '<img$1src=$2$3$2$4 loading="lazy" decoding="async">', $content ) ?? $content;
}
add_filter( 'the_content', 'fasdent_lazy_load_images', 20 );

/**
 * حذف نسخه وردپرس از هدر.
 */
function fasdent_remove_wp_version(): void {
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'fasdent_remove_wp_version' );

/**
 * افزودن دستورات preload برای فونت و CSS حیاتی.
 * این نسخه فقط از دارایی‌های لوکال استفاده می‌کند و از CDN خارج نمی‌شود.
 */
function fasdent_preload_assets(): void {
	$asset = FASDENT_URI . '/assets/fonts/vazirmatn/Vazirmatn-Regular.woff2';
	if ( file_exists( FASDENT_DIR . '/assets/fonts/vazirmatn/Vazirmatn-Regular.woff2' ) ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( $asset ) );
	}
}
add_action( 'wp_head', 'fasdent_preload_assets', 1 );
