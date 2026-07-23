<?php
/**
 * Extra enqueues — v2.4.0
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_enqueue_v24_assets(): void {
	$ver = defined( 'FASDENT_VERSION' ) ? FASDENT_VERSION : '2.4.0';

	$fixes = get_template_directory() . '/assets/css/fixes-v232.css';
	if ( file_exists( $fixes ) ) {
		wp_enqueue_style( 'fasdent-fixes', get_template_directory_uri() . '/assets/css/fixes-v232.css', array( 'fasdent-main' ), $ver );
	}

	$ba_css = get_template_directory() . '/assets/css/before-after.css';
	if ( file_exists( $ba_css ) ) {
		$deps = array( 'fasdent-main' );
		if ( wp_style_is( 'fasdent-fixes', 'enqueued' ) || wp_style_is( 'fasdent-fixes', 'registered' ) ) {
			$deps[] = 'fasdent-fixes';
		}
		wp_enqueue_style( 'fasdent-before-after', get_template_directory_uri() . '/assets/css/before-after.css', $deps, $ver );
	}

	$ba_js = get_template_directory() . '/assets/js/before-after.js';
	if ( file_exists( $ba_js ) ) {
		wp_enqueue_script(
			'fasdent-before-after',
			get_template_directory_uri() . '/assets/js/before-after.js',
			array(),
			$ver,
			array( 'in_footer' => true, 'strategy' => 'defer' )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fasdent_enqueue_v24_assets', 30 );
