<?php
/**
 * Performance helpers — Fasdent
 * Lazy load, WebP on upload, resource hints, DNS prefetch, LCP fetchpriority.
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Generator removal is also handled in security.php; keep for safety.
add_action( 'init', static function (): void { remove_action( 'wp_head', 'wp_generator' ); } );

/** DNS prefetch for analytics services when configured. */
function fasdent_resource_hints( array $hints, string $relation_type ): array {
	if ( 'dns-prefetch' !== $relation_type ) { return $hints; }
	if ( get_theme_mod( 'fasdent_ga4_id', '' ) ) {
		$hints[] = 'https://www.googletagmanager.com';
		$hints[] = 'https://www.google-analytics.com';
	}
	if ( get_theme_mod( 'fasdent_clarity_id', '' ) ) { $hints[] = 'https://www.clarity.ms'; }
	return $hints;
}
add_filter( 'wp_resource_hints', 'fasdent_resource_hints', 10, 2 );

/**
 * Single-pass content filter: lazy-load all images, then promote the first
 * one to eager + fetchpriority=high for LCP. Both operations run together so
 * the first image is never touched twice.
 */
function fasdent_process_content_images( string $content ): string {
	if ( ! is_singular() && ! is_home() ) { return $content; }
	$first = true;
	return preg_replace_callback( '/<img([^>]*)>/i', function ( array $m ) use ( &$first ): string {
		$attrs = $m[1];
		if ( $first ) {
			$first = false;
			// First image: eager + high priority for LCP.
			$attrs = preg_replace( '/\bloading=["\'][^"\']*["\']/i', '', $attrs );
			if ( ! str_contains( $attrs, 'fetchpriority' ) ) { $attrs .= ' fetchpriority="high"'; }
			$attrs .= ' loading="eager" decoding="sync"';
		} else {
			// Subsequent images: lazy load, skip if already set.
			if ( ! preg_match( '/\bloading=/i', $attrs ) ) { $attrs .= ' loading="lazy"'; }
			if ( ! preg_match( '/\bdecoding=/i', $attrs ) ) { $attrs .= ' decoding="async"'; }
		}
		return "<img{$attrs}>";
	}, $content ) ?? $content;
}
add_filter( 'the_content', 'fasdent_process_content_images', 20 );

/** Convert JPEG/PNG to WebP on upload (requires GD with WebP support). */
function fasdent_generate_webp_on_upload( array $metadata, int $attachment_id ): array {
	if ( ! function_exists( 'imagewebp' ) ) { return $metadata; }
	$file = get_attached_file( $attachment_id );
	if ( ! $file ) { return $metadata; }
	$mime = mime_content_type( $file );
	if ( ! in_array( $mime, [ 'image/jpeg', 'image/png' ], true ) ) { return $metadata; }
	$webp = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $file );
	if ( ! $webp || file_exists( $webp ) ) { return $metadata; }
	$img = 'image/jpeg' === $mime ? imagecreatefromjpeg( $file ) : imagecreatefrompng( $file );
	if ( ! $img ) { return $metadata; }
	if ( 'image/png' === $mime ) {
		$bg = imagecreatetruecolor( (int) imagesx( $img ), (int) imagesy( $img ) );
		imagefill( $bg, 0, 0, imagecolorallocate( $bg, 255, 255, 255 ) );
		imagecopy( $bg, $img, 0, 0, 0, 0, (int) imagesx( $img ), (int) imagesy( $img ) );
		imagedestroy( $img ); $img = $bg;
	}
	imagewebp( $img, $webp, 82 );
	imagedestroy( $img );
	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'fasdent_generate_webp_on_upload', 10, 2 );

/** Strip emoji scripts/styles — reduces requests. */
remove_action( 'wp_head',           'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles',   'print_emoji_styles' );
remove_action( 'admin_print_scripts','print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
