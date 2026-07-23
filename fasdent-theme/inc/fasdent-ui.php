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
	wp_enqueue_script(
		'fasdent-ui',
		get_template_directory_uri() . '/assets/js/fasdent-ui.js',
		array( 'fasdent-main' ),
		defined( 'FASDENT_VERSION' ) ? FASDENT_VERSION : wp_get_theme()->get( 'Version' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'fasdent_ui_assets' );
