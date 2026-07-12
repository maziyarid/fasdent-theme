<?php
/**
 * فراخوانی CSS / JS / فونت‌های لوکال — Fasdent
 * همه فایل‌ها بدون CDN و از مسیر assets قالب بارگذاری می‌شوند.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * فراخوانی استایل‌ها و اسکریپت‌های قالب.
 */
function fasdent_enqueue_scripts(): void {

	// ۱) فونت فارسی ایرانسل — کاملاً لوکال با font-display: swap.
	wp_enqueue_style(
		'fasdent-irancell',
		FASDENT_URI . '/assets/fonts/Irancell/irancell.css',
		array(),
		FASDENT_VERSION
	);

	// ۲) Font Awesome 7 Pro — کاملاً لوکال (بدون CDN).
	wp_enqueue_style(
		'fasdent-fontawesome',
		FASDENT_URI . '/assets/fonts/FontAwesome/css/all.css',
		array(),
		FASDENT_VERSION
	);

	// ۳) استایل اصلی قالب (RTL — Mobile First).
	$main_css = file_exists( FASDENT_DIR . '/assets/css/main.min.css' ) ? 'main.min.css' : 'main.css';
	wp_enqueue_style(
		'fasdent-main',
		FASDENT_URI . '/assets/css/' . $main_css,
		array( 'fasdent-irancell', 'fasdent-fontawesome' ),
		FASDENT_VERSION
	);

	// ۴) style.css (هدر قالب — برای شناسایی وردپرس).
	wp_enqueue_style( 'fasdent-style', get_stylesheet_uri(), array( 'fasdent-main' ), FASDENT_VERSION );

	// ۵) Print CSS.
	wp_enqueue_style(
		'fasdent-print',
		FASDENT_URI . '/assets/css/print.css',
		array( 'fasdent-main' ),
		FASDENT_VERSION,
		'print'
	);

	// ۵) اسکریپت اصلی (defer — غیرحیاتی).
	$main_js = file_exists( FASDENT_DIR . '/assets/js/main.min.js' ) ? 'main.min.js' : 'main.js';
	wp_enqueue_script(
		'fasdent-main',
		FASDENT_URI . '/assets/js/' . $main_js,
		array(),
		FASDENT_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// داده‌های لازم برای JS (ای‌جکس فرم‌ها + Nonce).
	wp_localize_script( 'fasdent-main', 'fasdentData', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'fasdent_form_nonce' ),
		'phone'   => fasdent_phone(),
	) );

	// نظرات تو در تو.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'fasdent_enqueue_scripts' );

/**
 * Preload فونت‌های حیاتی برای بهبود LCP/CLS.
 */
function fasdent_preload_fonts(): void {
	$fonts = array(
		'/assets/fonts/Irancell/Irancell_Regular.woff2',
		'/assets/fonts/Irancell/Irancell_Bold.woff2',
		'/assets/fonts/FontAwesome/webfonts/fa-solid-900.woff2',
	);
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( FASDENT_URI . $font )
		);
	}
}
add_action( 'wp_head', 'fasdent_preload_fonts', 1 );
