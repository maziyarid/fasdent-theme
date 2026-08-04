<?php
/**
 * Fasdent Theme - React Version
 * 
 * @package Fasdent
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Theme constants
define( 'FASDENT_VERSION', '3.0.0' );
define( 'FASDENT_DIR', get_template_directory() );
define( 'FASDENT_URI', get_template_directory_uri() );

// Load theme setup
require_once FASDENT_DIR . '/inc/setup.php';

/**
 * React App Integration
 * Load the built React application
 */
function fasdent_react_app() {
	// Production mode: load built assets
	$dist_path = FASDENT_URI . '/dist';
	
	// Load CSS
	if ( file_exists( FASDENT_DIR . '/dist/assets/app.css' ) ) {
		wp_enqueue_style( 'fasdent-react', $dist_path . '/assets/app.css', array(), FASDENT_VERSION );
	}
	
	// Load JS
	if ( file_exists( FASDENT_DIR . '/dist/assets/app.js' ) ) {
		wp_enqueue_script( 'fasdent-react', $dist_path . '/assets/app.js', array(), FASDENT_VERSION, true );
	}
	
	// Output root div
	echo '<div id="root"></div>';
}

/**
 * Add React data to wp_localize_script
 */
function fasdent_react_data() {
	$react_data = array(
		'site' => array(
			'name' => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'url' => home_url(),
			'language' => get_bloginfo( 'language' ),
			'direction' => is_rtl() ? 'rtl' : 'ltr',
		),
		'rest_url' => rest_url(),
		'nonce' => wp_create_nonce( 'wp_rest' ),
		'current_user' => is_user_logged_in() ? array(
			'id' => get_current_user_id(),
			'name' => wp_get_current_user()->display_name,
			'email' => wp_get_current_user()->user_email,
		) : null,
	);
	
	wp_localize_script( 'fasdent-react', 'FASDENT_REACT', $react_data );
}
add_action( 'wp_enqueue_scripts', 'fasdent_react_data', 20 );

/**
 * Add preload for React assets
 */
function fasdent_react_preload() {
	$dist_path = FASDENT_URI . '/dist';
	
	if ( file_exists( FASDENT_DIR . '/dist/assets/app.css' ) ) {
		echo '<link rel="preload" href="' . esc_url( $dist_path . '/assets/app.css' ) . '" as="style">';
	}
	
	if ( file_exists( FASDENT_DIR . '/dist/assets/app.js' ) ) {
		echo '<link rel="preload" href="' . esc_url( $dist_path . '/assets/app.js' ) . '" as="script">';
	}
}
add_action( 'wp_head', 'fasdent_react_preload', 1 );

/**
 * Add RTL body class
 */
function fasdent_body_classes( $classes ) {
	$classes[] = 'fasdent-rtl';
	return $classes;
}
add_filter( 'body_class', 'fasdent_body_classes' );
