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

// BUG-002 FIX: تابع fasdent_preload_assets() حذف شد — تکراری با fasdent_preload_fonts() در inc/enqueue.php.
// Preload فونت‌های حیاتی را fasdent_preload_fonts() در enqueue.php با priority 1 مدیریت می‌کند.
