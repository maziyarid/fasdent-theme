<?php
/**
 * تنظیمات پایه قالب — Fasdent
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * راه‌اندازی قالب.
 */
function fasdent_setup(): void {
	// ترجمه‌پذیری.
	load_theme_textdomain( 'fasdent', FASDENT_DIR . '/languages' );

	// پشتیبانی‌های هسته.
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 220,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	// اندازه‌های تصویر بهینه.
	add_image_size( 'fasdent-card', 480, 320, true );      // کارت خدمات.
	add_image_size( 'fasdent-hero', 1440, 640, true );     // هیرو.
	add_image_size( 'fasdent-gallery', 640, 480, true );   // گالری قبل/بعد.

	// منوها.
	register_nav_menus( array(
		'main-menu'   => __( 'منوی اصلی', 'fasdent' ),
		'footer-menu' => __( 'منوی فوتر', 'fasdent' ),
		'legal-menu'  => __( 'منوی قوانین', 'fasdent' ),
	) );
}
add_action( 'after_setup_theme', 'fasdent_setup' );

/**
 * ابزارک‌های فوتر.
 */
function fasdent_widgets_init(): void {
	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar( array(
			'name'          => sprintf( __( 'ستون فوتر %d', 'fasdent' ), $i ),
			'id'            => 'footer-' . $i,
			'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="footer-widget-title">',
			'after_title'   => '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'fasdent_widgets_init' );

/**
 * تنظیم ساختار پیوند یکتا هنگام فعال‌سازی قالب:
 * /%category%/%postname%/ برای پست‌های بلاگ.
 */
function fasdent_activation_permalinks(): void {
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%category%/%postname%/' );
	update_option( 'rewrite_rules', '' );
	$wp_rewrite->flush_rules( true );
}
add_action( 'after_switch_theme', 'fasdent_activation_permalinks' );

/**
 * کلاس‌های body برای RTL و صفحات اورژانس.
 *
 * @param array $classes کلاس‌های فعلی.
 * @return array
 */
function fasdent_body_classes( array $classes ): array {
	$classes[] = 'fasdent-rtl';
	if ( fasdent_is_emergency_context() ) {
		$classes[] = 'is-emergency';
	}
	return $classes;
}
add_filter( 'body_class', 'fasdent_body_classes' );

/**
 * آیا صفحه فعلی مرتبط با اورژانس دندانپزشکی است؟ (قالب C)
 */
function fasdent_is_emergency_context(): bool {
	if ( is_tax( 'service_category', 'dental-emergency' ) ) {
		return true;
	}
	if ( is_singular( 'service' ) ) {
		$terms = get_the_terms( get_the_ID(), 'service_category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( 'dental-emergency' === $term->slug ) {
					return true;
				}
				$parent = $term->parent ? get_term( $term->parent, 'service_category' ) : null;
				if ( $parent && ! is_wp_error( $parent ) && 'dental-emergency' === $parent->slug ) {
					return true;
				}
			}
		}
	}
	return false;
}
