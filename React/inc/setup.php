<?php
/**
 * Theme setup - React Version
 * 
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fasdent_setup(): void {
	load_theme_textdomain( 'fasdent', FASDENT_DIR . '/languages' );

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

	add_image_size( 'fasdent-card', 480, 320, true );
	add_image_size( 'fasdent-hero', 1440, 640, true );
	add_image_size( 'fasdent-gallery', 640, 480, true );

	register_nav_menus( array(
		'main-menu'   => __( 'منوی اصلی', 'fasdent' ),
		'footer-menu' => __( 'منوی فوتر', 'fasdent' ),
		'legal-menu'  => __( 'منوی قانونی', 'fasdent' ),
	) );
}
add_action( 'after_setup_theme', 'fasdent_setup' );

function fasdent_widgets_init(): void {
	register_sidebar( array(
		'name'          => __( 'ستون فوتر 1', 'fasdent' ),
		'id'            => 'footer-1',
		'description'   => __( 'ویجت‌های ستون فوتر 1', 'fasdent' ),
		'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="footer-widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'fasdent_widgets_init' );
