<?php
/**
 * فراخوانی CSS / JS / فونت‌های لوکال — Fasdent
 * @package Fasdent
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fasdent_enqueue_scripts(): void {
	wp_enqueue_style( 'fasdent-irancell', FASDENT_URI . '/assets/fonts/Irancell/irancell.css', array(), FASDENT_VERSION );
	wp_enqueue_style( 'fasdent-fontawesome', FASDENT_URI . '/assets/fonts/FontAwesome/css/all.css', array(), FASDENT_VERSION );

	$main_css = file_exists( FASDENT_DIR . '/assets/css/main.min.css' ) ? 'main.min.css' : 'main.css';
	wp_enqueue_style( 'fasdent-main', FASDENT_URI . '/assets/css/' . $main_css, array( 'fasdent-irancell', 'fasdent-fontawesome' ), FASDENT_VERSION );

	wp_enqueue_style( 'fasdent-ui-system', FASDENT_URI . '/assets/css/ui-system.css', array( 'fasdent-main' ), FASDENT_VERSION );
	wp_enqueue_style( 'fasdent-chat', FASDENT_URI . '/assets/css/fasdent-chat.css', array( 'fasdent-ui-system' ), FASDENT_VERSION );
	wp_enqueue_style( 'fasdent-style', get_stylesheet_uri(), array( 'fasdent-main' ), FASDENT_VERSION );
	wp_enqueue_style( 'fasdent-print', FASDENT_URI . '/assets/css/print.css', array( 'fasdent-main' ), FASDENT_VERSION, 'print' );

	$main_js = file_exists( FASDENT_DIR . '/assets/js/main.min.js' ) ? 'main.min.js' : 'main.js';
	wp_enqueue_script( 'fasdent-main', FASDENT_URI . '/assets/js/' . $main_js, array(), FASDENT_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'fasdent-nav', FASDENT_URI . '/assets/js/fasdent-nav.js', array(), FASDENT_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'fasdent-chat', FASDENT_URI . '/assets/js/fasdent-chat.js', array(), FASDENT_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );

	wp_localize_script( 'fasdent-main', 'fasdentData', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'fasdent_form_nonce' ),
		'phone'   => function_exists( 'fasdent_phone' ) ? fasdent_phone() : '09201441469',
	) );

	if ( is_singular( 'post' ) ) {
		$sp_css = file_exists( FASDENT_DIR . '/assets/css/single-post.min.css' ) ? 'single-post.min.css' : 'single-post.css';
		wp_enqueue_style( 'fasdent-single-post', FASDENT_URI . '/assets/css/' . $sp_css, array( 'fasdent-main' ), FASDENT_VERSION );
		$sp_js = file_exists( FASDENT_DIR . '/assets/js/single-post.min.js' ) ? 'single-post.min.js' : 'single-post.js';
		wp_enqueue_script( 'fasdent-single-post', FASDENT_URI . '/assets/js/' . $sp_js, array( 'fasdent-main' ), FASDENT_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'fasdent_enqueue_scripts' );

function fasdent_enqueue_page_template_assets(): void {
	if ( ! is_page_template( 'page-templates/fasdent-page.php' ) ) return;
	$page_css = file_exists( FASDENT_DIR . '/assets/css/page.min.css' ) ? 'page.min.css' : 'page.css';
	wp_enqueue_style( 'fasdent-page', FASDENT_URI . '/assets/css/' . $page_css, array( 'fasdent-main' ), FASDENT_VERSION );
	$page_js = file_exists( FASDENT_DIR . '/assets/js/page.min.js' ) ? 'page.min.js' : 'page.js';
	wp_enqueue_script( 'fasdent-page', FASDENT_URI . '/assets/js/' . $page_js, array( 'fasdent-main' ), FASDENT_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
}
add_action( 'wp_enqueue_scripts', 'fasdent_enqueue_page_template_assets' );

function fasdent_preload_fonts(): void {
	$fonts = array( '/assets/fonts/Irancell/Irancell_Regular.woff2', '/assets/fonts/Irancell/Irancell_Bold.woff2', '/assets/fonts/FontAwesome/webfonts/fa-solid-900.woff2' );
	foreach ( $fonts as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>\n', esc_url( FASDENT_URI . $font ) );
	}
}
add_action( 'wp_head', 'fasdent_preload_fonts', 1 );
