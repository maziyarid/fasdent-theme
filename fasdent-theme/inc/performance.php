<?php
/**
 * Performance helpers — Fasdent
 * Lazy load, WebP on upload, resource hints, DNS prefetch, LCP fetchpriority.
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Lazy load + decoding=async — skips images already marked loading=eager. */
function fasdent_lazy_load_images( string $content ): string {
	if ( ! is_singular() && ! is_home() ) { return $content; }
	return preg_replace( '/<img(?![^>]*loading=)([^>]*)>/i', '<img$1 loading="lazy" decoding="async">', $content ) ?? $content;
}
add_filter( 'the_content', 'fasdent_lazy_load_images', 20 );

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

/** fetchpriority=high on first content image for LCP improvement. */
function fasdent_prioritize_first_image( string $content ): string {
	if ( ! is_singular() ) { return $content; }
	$done = false;
	return preg_replace_callback( '/<img([^>]*)>/i', function ( array $m ) use ( &$done ): string {
		if ( $done ) { return $m[0]; }
		$done  = true;
		$attrs = preg_replace( '/\bloading=["\']lazy["\']/i', 'loading="eager"', $m[1] );
		if ( ! str_contains( $attrs, 'fetchpriority' ) )    { $attrs .= ' fetchpriority="high"'; }
		if ( ! preg_match( '/\bloading=/i',   $attrs ) )    { $attrs .= ' loading="eager"'; }
		if ( ! preg_match( '/\bdecoding=/i',  $attrs ) )    { $attrs .= ' decoding="sync"'; }
		return "<img{$attrs}>";
	}, $content ) ?? $content;
}
add_filter( 'the_content', 'fasdent_prioritize_first_image', 25 );

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