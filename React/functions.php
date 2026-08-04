<?php
/**
 * Fasdent Theme Functions for React
 *
 * @package Fasdent
 * @since 2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define theme constants
if ( ! defined( 'FASDENT_DIR' ) ) {
    define( 'FASDENT_DIR', get_template_directory() );
}
if ( ! defined( 'FASDENT_URI' ) ) {
    define( 'FASDENT_URI', get_template_directory_uri() );
}

// Load theme setup
require_once FASDENT_DIR . '/inc/setup.php';

/**
 * Check if we should use React for this request
 *
 * @since 2.0.0
 * @return bool
 */
function fasdent_use_react() {
    // Don't use React in admin
    if ( is_admin() ) {
        return false;
    }
    
    // Don't use React in REST API requests
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return false;
    }
    
    // Don't use React in AJAX requests
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return false;
    }
    
    // Don't use React in cron
    if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
        return false;
    }
    
    // Don't use React in WP-CLI
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        return false;
    }
    
    return true;
}

/**
 * React App Integration
 * Load the built React application
 *
 * @since 2.0.0
 */
function fasdent_react_app() {
    if ( ! fasdent_use_react() ) {
        return;
    }
    
    $dist_path = FASDENT_URI . '/dist';
    
    // Load CSS
    if ( file_exists( FASDENT_DIR . '/dist/assets/app.css' ) ) {
        wp_enqueue_style( 'fasdent-react', $dist_path . '/assets/app.css', array(), FASDENT_VERSION );
    }
    
    // Load JS as ES module
    if ( file_exists( FASDENT_DIR . '/dist/assets/app.js' ) ) {
        // Directly output the script tag with type="module"
        echo '<script type="module" crossorigin src="' . esc_url( $dist_path . '/assets/app.js' ) . '"></script>';
    }
    
    // Output root div
    echo '<div id="root"></div>';
}

/**
 * Add React data to wp_localize_script
 *
 * @since 2.0.0
 */
function fasdent_react_data() {
    if ( ! fasdent_use_react() ) {
        return;
    }
    
    $dist_path = FASDENT_URI . '/dist';
    
    // Enqueue CSS if it exists
    if ( file_exists( FASDENT_DIR . '/dist/assets/app.css' ) ) {
        wp_enqueue_style( 'fasdent-react', $dist_path . '/assets/app.css', array(), FASDENT_VERSION );
    }
    
    // Enqueue JS if it exists
    if ( file_exists( FASDENT_DIR . '/dist/assets/app.js' ) ) {
        wp_enqueue_script( 'fasdent-react', $dist_path . '/assets/app.js', array(), FASDENT_VERSION, true );
    }
    
    // Add React data to wp_localize_script
    $react_data = array(
        'site' => array(
            'name' => get_bloginfo( 'name' ),
            'url' => get_bloginfo( 'url' ),
        ),
        'api' => array(
            'root' => esc_url_raw( rest_url() ),
            'namespace' => 'wp/v2',
        ),
    );
    
    wp_localize_script( 'fasdent-react', 'FASDENT_REACT', $react_data );
}
add_action( 'wp_enqueue_scripts', 'fasdent_react_data', 20 );

/**
 * Add preload for React assets
 *
 * @since 2.0.0
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

/**
 * Add RTL body class
 *
 * @since 2.0.0
 * @param array $classes Existing body classes.
 * @return array
 */
function fasdent_body_classes( $classes ) {
    $classes[] = 'fasdent-rtl';
    return $classes;
}
add_filter( 'body_class', 'fasdent_body_classes' );
