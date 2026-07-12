<?php
/**
 * Security hardening — Fasdent
 * Headers, version hiding, XML-RPC off, login errors, Permissions-Policy.
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

function fasdent_remove_wp_version_strings( string $src ): string {
	if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'style_loader_src',  'fasdent_remove_wp_version_strings', 20 );
add_filter( 'script_loader_src', 'fasdent_remove_wp_version_strings', 20 );

remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

add_filter( 'xmlrpc_enabled', '__return_false' );

add_filter( 'login_errors', static fn() => __( 'اطلاعات ورود نادرست است.', 'fasdent' ) );

function fasdent_security_headers( array $headers ): array {
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['X-Frame-Options']        = 'SAMEORIGIN';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	$headers['Permissions-Policy']     = 'camera=(), microphone=(), geolocation=(self), payment=()';
	$headers['X-XSS-Protection']       = '1; mode=block';
	return $headers;
}
add_filter( 'wp_headers', 'fasdent_security_headers' );

function fasdent_sanitize_textarea( string $value ): string {
	return sanitize_textarea_field( wp_unslash( $value ) );
}