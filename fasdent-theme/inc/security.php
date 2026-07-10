<?php
/**
 * هاردنینگ امنیتی — Fasdent
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* عدم افشای نسخه وردپرس. */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * حذف نسخه از URL فایل‌های استاتیک هسته (جلوگیری از افشای نسخه).
 *
 * @param string $src آدرس فایل.
 * @return string
 */
function fasdent_remove_wp_version_strings( string $src ): string {
	if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'style_loader_src', 'fasdent_remove_wp_version_strings', 20 );
add_filter( 'script_loader_src', 'fasdent_remove_wp_version_strings', 20 );

/* حذف تگ‌های غیرضروری head. */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

/* غیرفعال‌سازی XML-RPC (کاهش سطح حمله). */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * حذف پیام خطای دقیق در ورود (جلوگیری از User Enumeration).
 */
add_filter( 'login_errors', static fn() => __( 'اطلاعات ورود نادرست است.', 'fasdent' ) );

/**
 * هدرهای امنیتی پایه.
 *
 * @param array $headers هدرها.
 * @return array
 */
function fasdent_security_headers( array $headers ): array {
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['X-Frame-Options']        = 'SAMEORIGIN';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	return $headers;
}
add_filter( 'wp_headers', 'fasdent_security_headers' );

/**
 * پاکسازی متن چندخطی فرم‌ها (کمکی).
 *
 * @param string $value مقدار خام.
 * @return string
 */
function fasdent_sanitize_textarea( string $value ): string {
	return sanitize_textarea_field( wp_unslash( $value ) );
}
