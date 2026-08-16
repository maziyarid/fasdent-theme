<?php
/**
 * Theme bootstrap.
 *
 * @package Alipasandi_Clinic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALIPASANDI_THEME_VERSION', '1.4.6' );

require_once get_template_directory() . '/inc/icons.php';
require_once get_template_directory() . '/inc/service-data.php';
require_once get_template_directory() . '/inc/forms.php';
require_once get_template_directory() . '/inc/images.php';
require_once get_template_directory() . '/inc/seo.php';

/** Set up native WordPress features. */
function alipasandi_theme_setup() {
	load_theme_textdomain( 'alipasandi-clinic', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/css/theme.css', 'assets/css/rtl.css' ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'alipasandi_theme_setup' );

/** Set the default media content width for embeds and generated markup. */
function alipasandi_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'alipasandi_content_width', 820 );
}
add_action( 'after_setup_theme', 'alipasandi_content_width', 0 );

/** Load the self-contained theme assets. */
function alipasandi_enqueue_assets() {
	// Premium serif for logo wordmark only (pairs with Vazirmatn). Alternatives documented in release notes.
	wp_enqueue_style(
		'alipasandi-logo-font',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'alipasandi-theme',
		get_template_directory_uri() . '/assets/css/theme.css',
		array( 'alipasandi-logo-font' ),
		ALIPASANDI_THEME_VERSION
	);
	wp_enqueue_style(
		'alipasandi-rtl',
		get_template_directory_uri() . '/assets/css/rtl.css',
		array( 'alipasandi-theme' ),
		ALIPASANDI_THEME_VERSION
	);
	wp_enqueue_script(
		'alipasandi-theme',
		get_template_directory_uri() . '/assets/js/theme.js',
		array(),
		ALIPASANDI_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'alipasandi_enqueue_assets' );

/** Keep the approved tooth mark as the public favicon on every front-end page. */
function alipasandi_output_favicon() {
	$favicon_url = get_template_directory_uri() . '/assets/images/favicon.svg';
	echo '<link rel="icon" href="' . esc_url( $favicon_url ) . '" type="image/svg+xml">' . "\n";
	echo '<link rel="alternate icon" href="' . esc_url( get_template_directory_uri() . '/assets/images/favicon-32.png' ) . '" type="image/png" sizes="32x32">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( get_template_directory_uri() . '/assets/images/apple-touch-icon.png' ) . '" sizes="180x180">' . "\n";
}
remove_action( 'wp_head', 'wp_site_icon', 99 );
add_action( 'wp_head', 'alipasandi_output_favicon', 2 );

/** Theme Customizer: keep real-world details editable without touching code. */
function alipasandi_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'alipasandi_clinic_details',
		array(
			'title'    => __( 'اطلاعات کلینیک', 'alipasandi-clinic' ),
			'priority' => 30,
		)
	);

	$settings = array(
		'clinic_phone'       => array( 'تلفن', '0920 144 1469', 'sanitize_text_field' ),
		'clinic_address'     => array( 'آدرس تهران', 'تهران، جردن، قبادیان غربی، پلاک ۱۱، واحد ۱۰', 'sanitize_text_field' ),
		'clinic_address_2'   => array( 'آدرس نوشهر', 'نوشهر، ستارخان، امیراد ۱، طبقه ۵', 'sanitize_text_field' ),
		'clinic_maps_2'      => array( 'لینک نقشه نوشهر', 'https://maps.app.goo.gl/ycPem67fpYbvgdXZ7?g_st=atm', 'esc_url_raw' ),
		'clinic_website'     => array( 'وب‌سایت', 'https://drkeyvanalipasandi.com', 'esc_url_raw' ),
		'clinic_instagram'   => array( 'اینستاگرام', 'https://instagram.com/drkeyvanalipasandi', 'esc_url_raw' ),
		'clinic_whatsapp'    => array( 'واتساپ', 'https://wa.me/989201441469', 'esc_url_raw' ),
		'clinic_telegram'    => array( 'تلگرام', 'https://t.me/drkeyvanalipasandi', 'esc_url_raw' ),
	);

	foreach ( $settings as $key => $setting ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $setting[1],
				'sanitize_callback' => $setting[2],
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $setting[0],
				'section' => 'alipasandi_clinic_details',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_setting(
		'show_treatment_count',
		array(
			'default'           => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'show_treatment_count',
		array(
			'label'       => __( 'نمایش آمار بیش از ۱۰٬۰۰۰ کیس درمانی', 'alipasandi-clinic' ),
			'description' => __( 'فقط پس از تأیید نهایی مستندات این گزینه را فعال کنید.', 'alipasandi-clinic' ),
			'section'     => 'alipasandi_clinic_details',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'alipasandi_customize_register' );

/** Retrieve a clinic setting with its approved default. */
function alipasandi_clinic_option( $key ) {
	$defaults = array(
		'clinic_phone'     => '0920 144 1469',
		'clinic_address'   => 'تهران، جردن، قبادیان غربی، پلاک ۱۱، واحد ۱۰',
		'clinic_address_2' => 'نوشهر، ستارخان، امیراد ۱، طبقه ۵',
		'clinic_maps_2'    => 'https://maps.app.goo.gl/ycPem67fpYbvgdXZ7?g_st=atm',
		'clinic_website'   => 'https://drkeyvanalipasandi.com',
		'clinic_instagram' => 'https://instagram.com/drkeyvanalipasandi',
		'clinic_whatsapp'  => 'https://wa.me/989201441469',
		'clinic_telegram'  => 'https://t.me/drkeyvanalipasandi',
	);

	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/** Convert a display phone number to a safe tel: value. */
function alipasandi_phone_href() {
	$phone = alipasandi_normalize_digits( alipasandi_clinic_option( 'clinic_phone' ) );
	return preg_replace( '/[^0-9+]/', '', $phone );
}

/** Resolve a known page URL and provide a stable fallback before setup. */
function alipasandi_page_url( $slug ) {
	if ( 'home' === $slug ) {
		return home_url( '/' );
	}

	$page = get_page_by_path( $slug );
	if ( ! $page && 0 === strpos( $slug, 'services/' ) ) {
		$page = get_page_by_path( wp_basename( $slug ) );
	}
	return $page ? get_permalink( $page ) : home_url( '/' . trim( $slug, '/' ) . '/' );
}

/** Render the approved wordmark consistently in the header and footer. */
function alipasandi_brand_logo( $extra_class = '' ) {
	$classes = trim( 'site-brand ' . sanitize_html_class( $extra_class ) );
	?>
	<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Dr Keyvan Alipasandi Dental Clinic — صفحه اصلی', 'alipasandi-clinic' ); ?>">
		<span class="brand-mark"><?php echo alipasandi_icon( 'tooth', 34 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<span class="brand-copy"><strong>Dr Keyvan Alipasandi</strong><small>DENTAL CLINIC</small></span>
	</a>
	<?php
}

/** The four approved independent service landing pages. */
function alipasandi_service_links() {
	return array(
		array( 'label' => 'ایمپلنت دندان', 'slug' => 'implant', 'url' => alipasandi_page_url( 'services/implant' ) ),
		array( 'label' => 'روکش دندان', 'slug' => 'crown', 'url' => alipasandi_page_url( 'services/crown' ) ),
		array( 'label' => 'جراحی و لثه', 'slug' => 'surgery', 'url' => alipasandi_page_url( 'services/surgery' ) ),
		array( 'label' => 'درمان عمومی', 'slug' => 'general', 'url' => alipasandi_page_url( 'services/general' ) ),
	);
}

/** The four approved primary links are shared by desktop and mobile navigation. */
function alipasandi_primary_links() {
	return array(
		array( 'label' => 'صفحه اصلی', 'url' => home_url( '/' ), 'active' => is_front_page(), 'current' => is_front_page() ),
		array( 'label' => 'درباره ما', 'url' => alipasandi_page_url( 'about' ), 'active' => is_page( 'about' ), 'current' => is_page( 'about' ) ),
		array( 'label' => 'خدمات', 'url' => alipasandi_page_url( 'services' ), 'active' => is_page( array( 'services', 'implant', 'crown', 'surgery', 'general' ) ), 'current' => is_page( 'services' ), 'children' => alipasandi_service_links() ),
		array( 'label' => 'تماس با ما', 'url' => alipasandi_page_url( 'contact' ), 'active' => is_page( 'contact' ), 'current' => is_page( 'contact' ) ),
	);
}

/** Render the same approved links in every primary-navigation surface. */
function alipasandi_render_primary_links( $mobile = false ) {
	foreach ( alipasandi_primary_links() as $link ) {
		$has_children = ! empty( $link['children'] );
		$classes      = array();
		if ( $link['active'] ) {
			$classes[] = 'current-menu-item';
		}
		if ( $has_children ) {
			$classes[] = 'menu-item-has-children';
		}
		$class = $classes ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' : '';
		$current = ! empty( $link['current'] ) ? ' aria-current="page"' : '';

		if ( $mobile ) {
			$mobile_classes = trim( 'mobile-nav-item ' . implode( ' ', $classes ) );
			echo '<div class="' . esc_attr( $mobile_classes ) . '">';
			if ( $has_children ) {
				echo '<div class="mobile-nav-parent"><a href="' . esc_url( $link['url'] ) . '"' . $current . '>' . esc_html( $link['label'] ) . '</a>';
				echo '<button class="mobile-submenu-toggle" type="button" aria-expanded="false" aria-controls="mobile-services-submenu" data-mobile-submenu-toggle><span class="screen-reader-text">' . esc_html__( 'نمایش زیرمنوی خدمات', 'alipasandi-clinic' ) . '</span>' . alipasandi_icon( 'chevron', 19 ) . '</button></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<div class="mobile-submenu" id="mobile-services-submenu" hidden>';
				foreach ( $link['children'] as $child ) {
					$child_current = is_page( $child['slug'] );
					echo '<a href="' . esc_url( $child['url'] ) . '"' . ( $child_current ? ' class="current-menu-item" aria-current="page"' : '' ) . '>' . esc_html( $child['label'] ) . '</a>';
				}
				echo '</div>';
			} else {
				echo '<a href="' . esc_url( $link['url'] ) . '"' . $current . '>' . esc_html( $link['label'] ) . '</a>';
			}
			echo '</div>';
		} else {
			echo '<li' . $class . '><a href="' . esc_url( $link['url'] ) . '"' . $current . ( $has_children ? ' aria-haspopup="true"' : '' ) . '>' . esc_html( $link['label'] ) . '</a>';
			if ( $has_children ) {
				echo '<ul class="sub-menu" aria-label="' . esc_attr__( 'خدمات', 'alipasandi-clinic' ) . '">';
				foreach ( $link['children'] as $child ) {
					$child_current = is_page( $child['slug'] );
					echo '<li' . ( $child_current ? ' class="current-menu-item"' : '' ) . '><a href="' . esc_url( $child['url'] ) . '"' . ( $child_current ? ' aria-current="page"' : '' ) . '>' . esc_html( $child['label'] ) . '</a></li>';
				}
				echo '</ul>';
			}
			echo '</li>';
		}
	}
}

/** Resolve the existing posts page without changing a live URL. */
function alipasandi_articles_url() {
	$posts_page = (int) get_option( 'page_for_posts' );
	if ( $posts_page ) {
		return get_permalink( $posts_page );
	}
	foreach ( array( 'articles', 'blog' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			return get_permalink( $page );
		}
	}
	return home_url( '/?post_type=post' );
}

/** Keep the treatment count hidden until the clinic explicitly approves it. */
function alipasandi_show_treatment_count() {
	return (bool) get_theme_mod( 'show_treatment_count', false );
}

/** Add useful body classes for styling. */
function alipasandi_body_classes( $classes ) {
	$classes[] = 'alipasandi-rtl';
	if ( is_front_page() ) {
		$classes[] = 'is-clinic-home';
	}
	return $classes;
}
add_filter( 'body_class', 'alipasandi_body_classes' );

/**
 * Provision only missing starter pages on first activation.
 * Existing pages and an existing front-page choice are never overwritten.
 */
function alipasandi_provision_pages() {
	$pages = array(
		'home'         => 'صفحه اصلی',
		'about'        => 'درباره ما',
		'services'     => 'خدمات',
		'contact'      => 'تماس با ما',
		'appointments' => 'رزرو نوبت',
		'faq'          => 'سوالات متداول',
	);
	$created = array();

	foreach ( $pages as $slug => $title ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$created[ $slug ] = $page->ID;
			continue;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			)
		);
		if ( ! is_wp_error( $page_id ) ) {
			$created[ $slug ] = $page_id;
		}
	}

	$service_pages = array(
		'implant' => 'ایمپلنت دندان',
		'crown'   => 'روکش دندان',
		'surgery' => 'جراحی و لثه',
		'general' => 'درمان عمومی',
	);
	$parent_id = isset( $created['services'] ) ? $created['services'] : 0;

	foreach ( $service_pages as $slug => $title ) {
		$page = get_page_by_path( 'services/' . $slug );
		if ( ! $page ) {
			$page = get_page_by_path( $slug );
		}
		if ( ! $page ) {
			wp_insert_post(
				array(
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_parent'  => $parent_id,
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => '',
				)
			);
		}
	}

	if ( empty( get_option( 'page_on_front' ) ) && isset( $created['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $created['home'] );
	}

	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'alipasandi_provision_pages' );
