<?php
/**
 * Fasdent Theme Functions for React
 *
 * @package Fasdent
 * @since 3.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define theme version if not already defined.
if ( ! defined( 'FASDENT_VERSION' ) ) {
    define( 'FASDENT_VERSION', '3.0.0' );
}

// Define theme directory & URI constants.
if ( ! defined( 'FASDENT_DIR' ) ) {
    define( 'FASDENT_DIR', get_template_directory() );
}
if ( ! defined( 'FASDENT_URI' ) ) {
    define( 'FASDENT_URI', get_template_directory_uri() );
}

// Load theme setup.
require_once FASDENT_DIR . '/inc/setup.php';

/**
 * Determine if React should be used for this request.
 *
 * @since 2.0.0
 * @return bool
 */
function fasdent_use_react(): bool {
    // Never in admin, REST, AJAX, cron, or WP-CLI.
    if ( is_admin() ) {
        return false;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return false;
    }

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return false;
    }

    if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
        return false;
    }

    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        return false;
    }

    return true;
}

/**
 * Output React app script and data in footer.
 *
 * @since 3.0.0
 */
function fasdent_output_react_app(): void {
    if ( ! fasdent_use_react() ) {
        return;
    }

    $dist_dir = FASDENT_DIR . '/dist';
    $dist_uri = FASDENT_URI . '/dist';

    // Output React data as inline script
    $react_data = array(
        'site' => array(
            'name' => get_bloginfo( 'name' ),
            'url'  => get_bloginfo( 'url' ),
            'description' => get_bloginfo( 'description' ),
            'language' => get_bloginfo( 'language' ),
            'direction' => is_rtl() ? 'rtl' : 'ltr',
        ),
        'api'  => array(
            'root'      => esc_url_raw( rest_url() ),
            'namespace' => 'wp/v2',
        ),
        'phone' => get_theme_mod( 'fasdent_phone', '09201441469' ),
        'phone_link' => get_theme_mod( 'fasdent_phone_intl', '+989201441469' ),
        'booking_url' => get_theme_mod( 'fasdent_booking_url', home_url( '/appointment/' ) ),
    );

    echo '<script type="module">window.FASDENT_REACT = ' . wp_json_encode( $react_data ) . ';</script>' . "\n";

    // Output React app script
    if ( file_exists( $dist_dir . '/assets/app.js' ) ) {
        echo '<script type="module" crossorigin src="' . esc_url( $dist_uri . '/assets/app.js' ) . '"></script>' . "\n";
    }
}
add_action( 'wp_footer', 'fasdent_output_react_app', 1 );

/**
 * Enqueue React CSS.
 *
 * @since 3.0.0
 */
function fasdent_enqueue_react_css(): void {
    if ( ! fasdent_use_react() ) {
        return;
    }

    $dist_dir = FASDENT_DIR . '/dist';
    $dist_uri = FASDENT_URI . '/dist';

    // Enqueue CSS if it exists.
    if ( file_exists( $dist_dir . '/assets/app.css' ) ) {
        wp_enqueue_style(
            'fasdent-react-style',
            $dist_uri . '/assets/app.css',
            array(),
            FASDENT_VERSION
        );
    }
}
add_action( 'wp_enqueue_scripts', 'fasdent_enqueue_react_css', 20 );

/**
 * Output the React app root element.
 *
 * Use this in templates (e.g., front-page.php) where the React app should mount.
 *
 * @since 2.0.0
 */
function fasdent_react_app(): void {
    if ( ! fasdent_use_react() ) {
        return;
    }

    // This ID must match what your React app uses to mount.
    echo '<div id="root"></div>';
}

/**
 * Add RTL body class.
 *
 * @since 2.0.0
 * @param array $classes Existing body classes.
 * @return array
 */
function fasdent_body_classes( array $classes ): array {
    $classes[] = 'fasdent-rtl';
    return $classes;
}
add_filter( 'body_class', 'fasdent_body_classes' );
