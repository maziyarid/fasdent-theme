<?php
/**
 * Fasdent UI 2.0 integration.
 * Add: require get_template_directory() . '/inc/fasdent-ui.php';
 * to functions.php.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/menu-icons.php';
require_once get_template_directory() . '/inc/floating-chat.php';

function fasdent_ui_assets() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_script( 'fasdent-ui', get_template_directory_uri() . '/assets/js/fasdent-ui.js', array(), $version ?: '2.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'fasdent_ui_assets' );
