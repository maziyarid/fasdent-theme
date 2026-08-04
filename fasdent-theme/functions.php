<?php
/**
 * Fasdent Theme bootstrap - React Version
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

// Load all theme functionality
require_once FASDENT_DIR . '/inc/setup.php';
require_once FASDENT_DIR . '/inc/enqueue.php';
require_once FASDENT_DIR . '/inc/post-types.php';
require_once FASDENT_DIR . '/inc/taxonomies.php';
require_once FASDENT_DIR . '/inc/customizer.php';
require_once FASDENT_DIR . '/inc/seo.php';
require_once FASDENT_DIR . '/inc/schema.php';
require_once FASDENT_DIR . '/inc/breadcrumb.php';
require_once FASDENT_DIR . '/inc/security.php';
require_once FASDENT_DIR . '/inc/performance.php';
require_once FASDENT_DIR . '/inc/forms.php';
require_once FASDENT_DIR . '/inc/toc.php';
require_once FASDENT_DIR . '/inc/post-meta.php';
require_once FASDENT_DIR . '/inc/related-posts.php';
require_once FASDENT_DIR . '/inc/cookies.php';
require_once FASDENT_DIR . '/inc/dashboard.php';
require_once FASDENT_DIR . '/inc/booking.php';
require_once FASDENT_DIR . '/inc/booking-rest.php';
require_once FASDENT_DIR . '/inc/polls.php';
require_once FASDENT_DIR . '/inc/ajax-search.php';
require_once FASDENT_DIR . '/inc/admin-bookings.php';
require_once FASDENT_DIR . '/inc/floating-chat.php';
require_once FASDENT_DIR . '/inc/chat-channels-admin.php';
require_once FASDENT_DIR . '/inc/before-after.php';
require_once FASDENT_DIR . '/inc/knowledge-base.php';
require_once FASDENT_DIR . '/inc/landing-blocks.php';
require_once FASDENT_DIR . '/inc/theme-featured-images.php';

// Load optional overrides
if ( file_exists( FASDENT_DIR . '/inc/customizer-overrides.php' ) ) {
	require_once FASDENT_DIR . '/inc/customizer-overrides.php';
}
if ( file_exists( FASDENT_DIR . '/inc/enqueue-patch.php' ) ) {
	require_once FASDENT_DIR . '/inc/enqueue-patch.php';
}
if ( file_exists( FASDENT_DIR . '/inc/fasdent-ui.php' ) ) {
	require_once FASDENT_DIR . '/inc/fasdent-ui.php';
}

// Load demo data importer in admin
if ( is_admin() && file_exists( FASDENT_DIR . '/data/demo/import.php' ) ) {
	require_once FASDENT_DIR . '/data/demo/import.php';
}

/**
 * Phone number helper functions
 */
function fasdent_phone(): string {
	return (string) get_theme_mod( 'fasdent_phone', '09201441469' );
}

function fasdent_phone_link(): string {
	return (string) get_theme_mod( 'fasdent_phone_intl', '+989201441469' );
}

/**
 * Call button helper
 */
function fasdent_call_button( string $label = '', string $class = '' ): void {
	$label = $label ?: sprintf( __( 'تماس فوری: %s', 'fasdent' ), fasdent_phone() );
	printf(
		'<a href="tel:%1$s" class="btn btn-call %2$s"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i><span>%3$s</span></a>',
		esc_attr( fasdent_phone_link() ),
		esc_attr( $class ),
		esc_html( $label )
	);
}

/**
 * Booking button helper
 */
function fasdent_booking_button( string $label = '', string $class = '' ): void {
	$label = $label ?: __( 'رزرو نوبت آنلاین', 'fasdent' );
	$url   = get_theme_mod( 'fasdent_booking_url', '' ) ?: home_url( '/appointment/' );
	printf(
		'<a href="%1$s" class="btn btn-primary %2$s"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i><span>%3$s</span></a>',
		esc_url( $url ),
		esc_attr( $class ),
		esc_html( $label )
	);
}

/**
 * Get ACF field with fallback to post meta
 */
function fasdent_field( string $key, ?int $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, $post_id );
		if ( null !== $value && '' !== $value ) {
			return $value;
		}
	}
	return get_post_meta( $post_id, $key, true );
}

/**
 * React App Integration
 * Load the built React application
 */
function fasdent_react_app() {
	// Check if we're in development mode
	$is_dev = defined( 'FASDENT_DEV_MODE' ) && FASDENT_DEV_MODE;
	
	if ( $is_dev ) {
		// Development mode: load from Vite dev server
		$vite_url = 'http://localhost:5173';
		echo '<div id="root"></div>';
		echo '<script type="module" src="' . esc_url( $vite_url ) . '/src/main.tsx"></script>';
	} else {
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
}

/**
 * Check if we should use React for this request
 */
function fasdent_use_react() {
	// Don't use React in admin
	if ( is_admin() ) {
		return false;
	}
	
	// Don't use React for REST API requests
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}
	
	// Don't use React for feed requests
	if ( is_feed() ) {
		return false;
	}
	
	// Don't use React for trackback/pingback
	if ( is_trackback() || ( function_exists( 'is_pingback' ) && is_pingback() ) ) {
		return false;
	}
	
	// Use React for all front-end requests
	return true;
}

/**
 * Main template loader for React
 */
function fasdent_react_template( $template ) {
	if ( fasdent_use_react() ) {
		// Look for a React-specific template
		$react_template = locate_template( 'template-react.php' );
		if ( $react_template ) {
			return $react_template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'fasdent_react_template' );

/**
 * Add React data to wp_localize_script
 */
function fasdent_react_data() {
	if ( ! fasdent_use_react() ) {
		return;
	}
	
	$react_data = array(
		'site' => array(
			'name' => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'url' => home_url(),
			'language' => get_bloginfo( 'language' ),
			'direction' => is_rtl() ? 'rtl' : 'ltr',
		),
		'phone' => fasdent_phone(),
		'phone_link' => fasdent_phone_link(),
		'booking_url' => get_theme_mod( 'fasdent_booking_url', home_url( '/appointment/' ) ),
		'menus' => array(
			'main' => fasdent_get_menu_array( 'main-menu' ),
			'footer' => fasdent_get_menu_array( 'footer-menu' ),
			'legal' => fasdent_get_menu_array( 'legal-menu' ),
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
 * Get menu as array for React
 */
function fasdent_get_menu_array( $menu_location ) {
	$locations = get_nav_menu_locations();
	if ( ! isset( $locations[ $menu_location ] ) ) {
		return array();
	}
	
	$menu = wp_get_nav_menu_items( $locations[ $menu_location ] );
	if ( ! $menu ) {
		return array();
	}
	
	$menu_array = array();
	foreach ( $menu as $item ) {
		$menu_array[] = array(
			'id' => $item->ID,
			'title' => $item->title,
			'url' => $item->url,
			'classes' => $item->classes,
			'parent' => $item->menu_item_parent,
			'children' => array(),
		);
	}
	
	// Build hierarchy
	$menu_array = fasdent_build_menu_hierarchy( $menu_array );
	
	return $menu_array;
}

/**
 * Build menu hierarchy
 */
function fasdent_build_menu_hierarchy( $items, $parent_id = 0 ) {
	$children = array();
	foreach ( $items as $item ) {
		if ( $item['parent'] == $parent_id ) {
			$item['children'] = fasdent_build_menu_hierarchy( $items, $item['id'] );
			$children[] = $item;
		}
	}
	return $children;
}

/**
 * Add React-specific body classes
 */
function fasdent_react_body_classes( $classes ) {
	if ( fasdent_use_react() ) {
		$classes[] = 'fasdent-react';
		$classes[] = 'fasdent-rtl';
	}
	return $classes;
}
add_filter( 'body_class', 'fasdent_react_body_classes' );

/**
 * Disable WordPress admin bar in React mode for cleaner UI
 */
function fasdent_disable_admin_bar() {
	if ( fasdent_use_react() && ! current_user_can( 'administrator' ) ) {
		add_filter( 'show_admin_bar', '__return_false' );
	}
}
add_action( 'wp', 'fasdent_disable_admin_bar' );

/**
 * Add preload for React assets
 */
function fasdent_react_preload() {
	if ( ! fasdent_use_react() ) {
		return;
	}
	
	$dist_path = FASDENT_URI . '/dist';
	
	if ( file_exists( FASDENT_DIR . '/dist/assets/app.css' ) ) {
		echo '<link rel="preload" href="' . esc_url( $dist_path . '/assets/app.css' ) . '" as="style">';
	}
	
	if ( file_exists( FASDENT_DIR . '/dist/assets/app.js' ) ) {
		echo '<link rel="preload" href="' . esc_url( $dist_path . '/assets/app.js' ) . '" as="script">';
	}
}
add_action( 'wp_head', 'fasdent_react_preload', 1 );
