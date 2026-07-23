<?php
/**
 * Extra enqueues for v2.3.3 fixes.
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_enqueue_v233_fixes(): void {
	$ver = defined( 'FASDENT_VERSION' ) ? FASDENT_VERSION : '2.3.3';
	$css = get_template_directory() . '/assets/css/fixes-v232.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'fasdent-fixes',
			get_template_directory_uri() . '/assets/css/fixes-v232.css',
			array( 'fasdent-main' ),
			$ver
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fasdent_enqueue_v233_fixes', 30 );
