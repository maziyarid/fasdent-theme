<?php
/**
 * Fasdent React Theme Functions
 *
 * @package Fasdent
 * @since 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'FASDENT_VERSION' ) ) {
	define( 'FASDENT_VERSION', '3.1.0' );
}
if ( ! defined( 'FASDENT_DIR' ) ) {
	define( 'FASDENT_DIR', get_template_directory() );
}
if ( ! defined( 'FASDENT_URI' ) ) {
	define( 'FASDENT_URI', get_template_directory_uri() );
}

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
	define( 'FASDENT_DOCTOR_INSTAGRAM', 'Dr.keyvan_alipasandi' );
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
	define(
		'FASDENT_GOOGLE_MAP',
		'https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3200.9933219676263!2d51.50583407631748!3d36.65061127611691!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMzbCsDM5JzAyLjIiTiA1McKwMzAnMzAuMyJF!5e0!3m2!1sen!2sus!4v1785923326577!5m2!1sen!2sus'
	);
}

function fasdent_require( string $relative ): void {
	$path = FASDENT_DIR . '/' . ltrim( $relative, '/' );
	if ( is_readable( $path ) ) {
		require_once $path;
	}
}

fasdent_require( 'inc/setup.php' );
fasdent_require( 'inc/post-types.php' );
fasdent_require( 'inc/taxonomies.php' );

function fasdent_use_react(): bool {
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

function fasdent_react_app(): void {
	if ( ! fasdent_use_react() ) {
		return;
	}
	echo '<div id="root"></div>';
}

function fasdent_enqueue_react_css(): void {
	if ( ! fasdent_use_react() ) {
		return;
	}

	$fa_css = FASDENT_DIR . '/assets/fonts/FontAwesome/css/all.css';
	$fa_uri = FASDENT_URI . '/assets/fonts/FontAwesome/css/all.css';
	if ( ! file_exists( $fa_css ) ) {
		$fa_css = FASDENT_DIR . '/assets/FontAwesome/css/all.css';
		$fa_uri = FASDENT_URI . '/assets/FontAwesome/css/all.css';
	}
	if ( file_exists( $fa_css ) ) {
		wp_enqueue_style( 'fasdent-fontawesome', $fa_uri, array(), FASDENT_VERSION );
	}

	$ir_css = FASDENT_DIR . '/assets/fonts/Irancell/irancell.css';
	if ( ! file_exists( $ir_css ) ) {
		$ir_css = FASDENT_DIR . '/assets/Irancell/irancell.css';
		if ( file_exists( $ir_css ) ) {
			wp_enqueue_style( 'fasdent-irancell', FASDENT_URI . '/assets/Irancell/irancell.css', array(), FASDENT_VERSION );
		}
	} else {
		wp_enqueue_style( 'fasdent-irancell', FASDENT_URI . '/assets/fonts/Irancell/irancell.css', array(), FASDENT_VERSION );
	}

	$app_css = FASDENT_DIR . '/dist/assets/app.css';
	if ( file_exists( $app_css ) ) {
		wp_enqueue_style(
			'fasdent-react-style',
			FASDENT_URI . '/dist/assets/app.css',
			array( 'fasdent-fontawesome', 'fasdent-irancell' ),
			FASDENT_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fasdent_enqueue_react_css', 20 );

function fasdent_output_react_app(): void {
	if ( ! fasdent_use_react() ) {
		return;
	}

	$theme_uri  = FASDENT_URI;
	$react_data = array(
		'site'           => array(
			'name'        => get_bloginfo( 'name' ),
			'url'         => home_url( '/' ),
			'description' => get_bloginfo( 'description' ),
			'language'    => get_bloginfo( 'language' ),
			'direction'   => is_rtl() ? 'rtl' : 'ltr',
		),
		'api'            => array(
			'root'      => esc_url_raw( rest_url() ),
			'namespace' => 'wp/v2',
			'nonce'     => wp_create_nonce( 'wp_rest' ),
		),
		'theme'          => array(
			'uri'     => $theme_uri,
			'version' => FASDENT_VERSION,
			'assets'  => $theme_uri . '/assets',
			'images'  => $theme_uri . '/assets/images',
		),
		'clinic'         => array(
			'name'             => 'کلینیک تخصصی دندانپزشکی فسدنت',
			'doctor'           => FASDENT_DOCTOR_NAME,
			'doctor_title'     => FASDENT_DOCTOR_TITLE,
			'license'          => FASDENT_DOCTOR_LICENSE,
			'experience'       => FASDENT_DOCTOR_EXPERIENCE,
			'phone'            => FASDENT_PHONE,
			'phone_intl'       => FASDENT_PHONE_INTL,
			'phone_link'       => 'tel:' . FASDENT_PHONE_INTL,
			'email'            => FASDENT_EMAIL,
			'address'          => FASDENT_ADDRESS,
			'working_hours'    => FASDENT_WORKING_HOURS,
			'instagram_doctor' => FASDENT_DOCTOR_INSTAGRAM,
			'instagram_clinic' => FASDENT_CLINIC_INSTAGRAM,
			'whatsapp'         => 'https://wa.me/' . ltrim( FASDENT_PHONE_INTL, '+' ),
			'google_map'       => FASDENT_GOOGLE_MAP,
			'doctor_image'     => $theme_uri . '/assets/images/Dr%20Keyvan%20Alipasandi.jpg',
			'logo'             => $theme_uri . '/assets/images/Logo.webp',
			'favicon'          => $theme_uri . '/assets/images/Favicon.webp',
		),
		'services'       => array( 'ایمپلنت', 'ترمیم', 'جراحی دندان عقل', 'عصب کشی' ),
		'implant_brands' => array( 'Bego', 'Megagen', 'Straumann', 'Sic', '3zahn' ),
		'booking_url'    => home_url( '/appointment/' ),
		'blog_url'       => home_url( '/blog/' ),
	);

	if ( is_singular() ) {
		$obj = get_queried_object();
		if ( $obj && isset( $obj->post_name ) ) {
			$react_data['current'] = array(
				'type'  => get_post_type(),
				'slug'  => $obj->post_name,
				'id'    => (int) $obj->ID,
				'title' => get_the_title( $obj ),
			);
		}
	}

	echo '<script>window.FASDENT_REACT = ' . wp_json_encode( $react_data ) . ';</script>' . "\n";

	$js = FASDENT_DIR . '/dist/assets/app.js';
	if ( file_exists( $js ) ) {
		echo '<script type="module" crossorigin src="' . esc_url( FASDENT_URI . '/dist/assets/app.js' ) . '"></script>' . "\n";
	}
}
add_action( 'wp_footer', 'fasdent_output_react_app', 5 );

function fasdent_body_classes( array $classes ): array {
	$classes[] = 'fasdent-rtl';
	return $classes;
}
add_filter( 'body_class', 'fasdent_body_classes' );

function fasdent_site_icon(): void {
	$path = FASDENT_DIR . '/assets/images/Favicon.webp';
	if ( file_exists( $path ) ) {
		echo '<link rel="icon" type="image/webp" href="' . esc_url( FASDENT_URI . '/assets/images/Favicon.webp' ) . '" />' . "\n";
	}
}
add_action( 'wp_head', 'fasdent_site_icon', 1 );

function fasdent_after_switch_theme(): void {
	if ( function_exists( 'fasdent_register_post_types' ) ) {
		fasdent_register_post_types();
	}
	if ( function_exists( 'fasdent_register_taxonomies' ) ) {
		fasdent_register_taxonomies();
	}
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'fasdent_after_switch_theme' );

function fasdent_disable_admin_bar(): void {
	if ( fasdent_use_react() && ! current_user_can( 'manage_options' ) ) {
		show_admin_bar( false );
	}
}
add_action( 'wp', 'fasdent_disable_admin_bar' );
