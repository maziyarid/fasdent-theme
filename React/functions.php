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

// Real clinic data - constants for easy access
if ( ! defined( 'FASDENT_DOCTOR_NAME' ) ) {
    define( 'FASDENT_DOCTOR_NAME', 'دکتر کیوان علی پسندی' );
}
if ( ! defined( 'FASDENT_DOCTOR_TITLE' ) ) {
    define( 'FASDENT_DOCTOR_TITLE', 'دکتری حرفه ای ( ایمپلنتولوژیست )' );
}
if ( ! defined( 'FASDENT_DOCTOR_LICENSE' ) ) {
    define( 'FASDENT_DOCTOR_LICENSE', '۱۹۱۷۴۰ شماره نظام' );
}
if ( ! defined( 'FASDENT_DOCTOR_EXPERIENCE' ) ) {
    define( 'FASDENT_DOCTOR_EXPERIENCE', 'بیش از ۱۰ سال سابقه' );
}
if ( ! defined( 'FASDENT_DOCTOR_INSTAGRAM' ) ) {
    define( 'FASDENT_DOCTOR_INSTAGRAM', 'Dr.keyvan.alipasandi' );
}
if ( ! defined( 'FASDENT_CLINIC_INSTAGRAM' ) ) {
    define( 'FASDENT_CLINIC_INSTAGRAM', 'Fasdent.clinic' );
}
if ( ! defined( 'FASDENT_PHONE' ) ) {
    define( 'FASDENT_PHONE', '09201441469' );
}
if ( ! defined( 'FASDENT_PHONE_INTL' ) ) {
    define( 'FASDENT_PHONE_INTL', '+989201441469' );
}
if ( ! defined( 'FASDENT_EMAIL' ) ) {
    define( 'FASDENT_EMAIL', 'Dr.keyvan.alipasandii@gmail.com' );
}
if ( ! defined( 'FASDENT_ADDRESS' ) ) {
    define( 'FASDENT_ADDRESS', 'نوشهر، میدان آزادی، ستارخان شمالی، ساختمان امیراد ۱، طبقه پنجم' );
}
if ( ! defined( 'FASDENT_WORKING_HOURS' ) ) {
    define( 'FASDENT_WORKING_HOURS', 'ساعت کاری از ساعت ۱۱ صبح الي ۱۹ شب' );
}
if ( ! defined( 'FASDENT_GOOGLE_MAP' ) ) {
    define( 'FASDENT_GOOGLE_MAP', 'https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3200.9933219676263!2d51.50583407631748!3d36.65061127611691!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMzbCsDM5JzAyLjIiTiA1McKwMzAnMzAuMyJF!5e0!3m2!1sen!2sus!4v1785923326577!5m2!1sen!2sus' );
}

// Load theme setup.
require_once FASDENT_DIR . '/inc/setup.php';

// Load post types and taxonomies for backend functionality
require_once FASDENT_DIR . '/inc/post-types.php';
require_once FASDENT_DIR . '/inc/taxonomies.php';

// Load customizer and enqueue
require_once FASDENT_DIR . '/inc/customizer.php';
require_once FASDENT_DIR . '/inc/enqueue.php';

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
        'clinic' => array(
            'name' => 'کلینیک تخصصی دندانپزشکی فسدنت',
            'doctor' => FASDENT_DOCTOR_NAME,
            'doctor_title' => FASDENT_DOCTOR_TITLE,
            'license' => FASDENT_DOCTOR_LICENSE,
            'experience' => FASDENT_DOCTOR_EXPERIENCE,
            'phone' => FASDENT_PHONE,
            'phone_intl' => FASDENT_PHONE_INTL,
            'phone_link' => 'tel:' . FASDENT_PHONE_INTL,
            'email' => FASDENT_EMAIL,
            'address' => FASDENT_ADDRESS,
            'working_hours' => FASDENT_WORKING_HOURS,
            'instagram_doctor' => FASDENT_DOCTOR_INSTAGRAM,
            'instagram_clinic' => FASDENT_CLINIC_INSTAGRAM,
            'whatsapp' => 'https://wa.me/' . FASDENT_PHONE_INTL,
            'google_map' => FASDENT_GOOGLE_MAP,
        ),
        'services' => array(
            'ایمپلنت',
            'ترمیم',
            'جراحی دندان عقل',
            'عصب کشی',
        ),
        'implant_brands' => array(
            'Bego',
            'Megagen',
            'Straumann',
            'Sic',
            '3zahn',
        ),
        'booking_url' => home_url( '/appointment/' ),
    );

    echo '<script type="module">window.FASDENT_REACT = ' . wp_json_encode( $react_data ) . ';</script>' . "\n";

    // Output React app script
    if ( file_exists( $dist_dir . '/assets/app.js' ) ) {
        echo '<script type="module" crossorigin src="' . esc_url( $dist_uri . '/assets/app.js' ) . '"></script>' . "\n";
    }
}
add_action( 'wp_footer', 'fasdent_output_react_app', 1 );

/**
 * Enqueue React CSS and local fonts.
 *
 * @since 3.0.0
 */
function fasdent_enqueue_react_css(): void {
    if ( ! fasdent_use_react() ) {
        return;
    }

    $dist_dir = FASDENT_DIR . '/dist';
    $dist_uri = FASDENT_URI . '/dist';

    // Enqueue local fonts CSS
    wp_enqueue_style(
        'fasdent-irancell',
        FASDENT_URI . '/assets/fonts/Irancell/irancell.css',
        array(),
        FASDENT_VERSION
    );

    wp_enqueue_style(
        'fasdent-fontawesome-all',
        FASDENT_URI . '/assets/fonts/FontAwesome/css/all.css',
        array(),
        FASDENT_VERSION
    );

    wp_enqueue_style(
        'fasdent-fontawesome-solid',
        FASDENT_URI . '/assets/fonts/FontAwesome/css/solid.css',
        array(),
        FASDENT_VERSION
    );

    // Enqueue React CSS if it exists.
    if ( file_exists( $dist_dir . '/assets/app.css' ) ) {
        wp_enqueue_style(
            'fasdent-react-style',
            $dist_uri . '/assets/app.css',
            array('fasdent-irancell', 'fasdent-fontawesome-all', 'fasdent-fontawesome-solid'),
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

/**
 * Add favicon to site icon
 */
function fasdent_site_icon() {
    if ( file_exists( FASDENT_DIR . '/assets/images/Favicon.webp' ) ) {
        echo '<link rel="icon" type="image/webp" href="' . esc_url( FASDENT_URI . '/assets/images/Favicon.webp' ) . '" />';
    }
}
add_action( 'wp_head', 'fasdent_site_icon', 1 );
