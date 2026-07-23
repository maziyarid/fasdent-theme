<?php
/**
 * Resolve featured images from theme assets/images/{slug}.webp
 * and/or post meta `fasdent_theme_image`.
 *
 * @package Fasdent
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fasdent_theme_image_url( string $relative ): string {
	$relative = ltrim( str_replace( '..', '', $relative ), '/' );
	if ( ! $relative ) {
		return '';
	}
	$path = get_template_directory() . '/' . $relative;
	if ( file_exists( $path ) ) {
		return get_template_directory_uri() . '/' . $relative;
	}
	return '';
}

function fasdent_post_image_slug( ?int $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();
	$post    = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	if ( 'page' === $post->post_type && (int) get_option( 'page_on_front' ) === $post_id ) {
		return 'home';
	}
	return $post->post_name ?: '';
}

function fasdent_resolve_theme_featured_url( ?int $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();
	$meta    = (string) get_post_meta( $post_id, 'fasdent_theme_image', true );
	if ( $meta ) {
		$url = fasdent_theme_image_url( $meta );
		if ( $url ) {
			return $url;
		}
	}
	$slug = fasdent_post_image_slug( $post_id );
	if ( ! $slug ) {
		return '';
	}
	foreach ( array( 'webp', 'jpg', 'jpeg', 'png' ) as $ext ) {
		$url = fasdent_theme_image_url( 'assets/images/' . $slug . '.' . $ext );
		if ( $url ) {
			return $url;
		}
	}
	return '';
}

function fasdent_filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	if ( $html ) {
		return $html;
	}
	$url = fasdent_resolve_theme_featured_url( (int) $post_id );
	if ( ! $url ) {
		return $html;
	}
	$alt   = the_title_attribute( array( 'echo' => false, 'post' => $post_id ) );
	$class = is_array( $attr ) && isset( $attr['class'] ) ? $attr['class'] : 'attachment-post-thumbnail size-post-thumbnail wp-post-image';
	return sprintf(
		'<img src="%1$s" alt="%2$s" class="%3$s" loading="lazy" decoding="async" />',
		esc_url( $url ),
		esc_attr( $alt ),
		esc_attr( $class )
	);
}
add_filter( 'post_thumbnail_html', 'fasdent_filter_post_thumbnail_html', 10, 5 );

function fasdent_has_theme_thumbnail( ?int $post_id = null ): bool {
	if ( has_post_thumbnail( $post_id ) ) {
		return true;
	}
	return (bool) fasdent_resolve_theme_featured_url( $post_id );
}

function fasdent_filter_has_post_thumbnail( $has, $post, $thumbnail_id ) {
	if ( $has ) {
		return $has;
	}
	$post_id = $post instanceof WP_Post ? $post->ID : (int) $post;
	return (bool) fasdent_resolve_theme_featured_url( $post_id );
}
add_filter( 'has_post_thumbnail', 'fasdent_filter_has_post_thumbnail', 10, 3 );
