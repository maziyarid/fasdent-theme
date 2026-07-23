<?php
/**
 * Extra enqueues — v2.5.0
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_enqueue_v25_assets(): void {
	$ver = defined( 'FASDENT_VERSION' ) ? FASDENT_VERSION : '2.5.0';
	$uri = get_template_directory_uri();
	$dir = get_template_directory();

	if ( file_exists( $dir . '/assets/css/fixes-v232.css' ) ) {
		wp_enqueue_style( 'fasdent-fixes', $uri . '/assets/css/fixes-v232.css', array( 'fasdent-main' ), $ver );
	}
	if ( file_exists( $dir . '/assets/css/before-after.css' ) ) {
		wp_enqueue_style( 'fasdent-before-after', $uri . '/assets/css/before-after.css', array( 'fasdent-main' ), $ver );
	}
	if ( file_exists( $dir . '/assets/js/before-after.js' ) ) {
		wp_enqueue_script( 'fasdent-before-after', $uri . '/assets/js/before-after.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
	if ( file_exists( $dir . '/assets/css/knowledge-base.css' ) ) {
		wp_enqueue_style( 'fasdent-kb', $uri . '/assets/css/knowledge-base.css', array( 'fasdent-main' ), $ver );
	}
}
add_action( 'wp_enqueue_scripts', 'fasdent_enqueue_v25_assets', 30 );
