<?php
/**
 * Theme bootstrap.
 *
 * @package Alipasandi_Clinic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALIPASANDI_THEME_VERSION', '1.4.28' );
define( 'ALIPASANDI_SERVICE_PLUGIN_MIN_VERSION', '1.3.3' );

/** Return the companion plugin state without invoking plugin business logic. */
function alipasandi_service_plugin_state() {
	if ( ! defined( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION' ) ) {
		return 'missing';
	}
	return version_compare( ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION, ALIPASANDI_SERVICE_PLUGIN_MIN_VERSION, '>=' ) ? 'ready' : 'outdated';
}

function alipasandi_service_plugin_ready() {
	return 'ready' === alipasandi_service_plugin_state();
}

require_once get_template_directory() . '/inc/icons.php';
/*
 * Legacy service data remains an emergency READ-ONLY render source for the
 * 1.4.x migration window. It is never an admin/edit/write owner.
 */
require_once get_template_directory() . '/inc/service-data.php';
if ( ! alipasandi_service_plugin_ready() ) {
	require_once get_template_directory() . '/inc/service-meta.php';
	require_once get_template_directory() . '/inc/forms.php';
}
require_once get_template_directory() . '/inc/images.php';
require_once get_template_directory() . '/inc/seo.php';

/**
 * If an unsupported old companion plugin is present, force emergency
 * compatibility to read-only by unhooking known mutation/form endpoints.
 */
function alipasandi_enforce_read_only_compatibility() {
	if ( 'outdated' !== alipasandi_service_plugin_state() ) {
		return;
	}
	$hooks = array(
		array( 'init', 'alipasandi_register_service_meta', 10 ),
		array( 'add_meta_boxes', 'alipasandi_register_service_meta_box', 10 ),
		array( 'save_post_page', 'alipasandi_save_service_meta', 10 ),
		array( '_wp_put_post_revision', 'alipasandi_service_meta_revision_fields', 10 ),
		array( 'wp_restore_post_revision', 'alipasandi_service_meta_restore_revision', 10 ),
		array( 'admin_menu', 'alipasandi_service_tools_menu', 10 ),
		array( 'admin_post_alipasandi_service_migrate', 'alipasandi_service_admin_migrate', 10 ),
		array( 'admin_post_alipasandi_service_export', 'alipasandi_service_admin_export', 10 ),
		array( 'admin_init', 'alipasandi_register_site_settings', 10 ),
		array( 'admin_init', 'alipasandi_site_settings_migrate_legacy_theme_mods', 2 ),
		array( 'admin_menu', 'alipasandi_operational_settings_menu', 10 ),
		array( 'admin_post_nopriv_alipasandi_contact', 'alipasandi_handle_contact', 10 ),
		array( 'admin_post_alipasandi_contact', 'alipasandi_handle_contact', 10 ),
		array( 'admin_post_nopriv_alipasandi_appointment', 'alipasandi_handle_appointment', 10 ),
		array( 'admin_post_alipasandi_appointment', 'alipasandi_handle_appointment', 10 ),
	);
	foreach ( $hooks as $hook ) {
		remove_action( $hook[0], $hook[1], $hook[2] );
	}
	// Old-plugin operational filters are also disabled in compatibility mode.
	remove_filter( 'rank_math/json_ld', 'alipasandi_rank_math_central_nap', 90 );
	remove_filter( 'wp_mail_from', 'alipasandi_domain_mail_from', 10 );
	remove_filter( 'wp_mail_from_name', 'alipasandi_domain_mail_from_name', 10 );
}
alipasandi_enforce_read_only_compatibility();

/** Admin + Site Health signal: the production business-logic owner is required. */
function alipasandi_companion_plugin_admin_notice() {
	if ( alipasandi_service_plugin_ready() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	$state = alipasandi_service_plugin_state();
	$message = 'missing' === $state
		? 'افزونه Alipasandi Service Content نصب/فعال نیست. فرم‌ها غیرفعال و Theme فقط در حالت Read-only compatibility است.'
		: 'نسخه افزونه Alipasandi Service Content قدیمی است. حداقل نسخه 1.3.3 لازم است؛ فرم‌ها و مسیرهای Write تا ارتقا غیرفعال هستند.';
	echo '<div class="notice notice-error"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
}
add_action( 'admin_notices', 'alipasandi_companion_plugin_admin_notice' );

function alipasandi_companion_plugin_site_health( $tests ) {
	if ( alipasandi_service_plugin_ready() ) {
		return $tests;
	}
	$tests['direct']['alipasandi_companion_plugin_required'] = array(
		'label' => 'Alipasandi companion plugin required',
		'test'  => function () {
			return array(
				'label'       => 'افزونه Business Logic سایت آماده Production نیست',
				'status'      => 'critical',
				'badge'       => array( 'label' => 'Alipasandi', 'color' => 'blue' ),
				'description' => '<p>' . esc_html( 'Theme در این وضعیت فقط داده موجود را Render می‌کند؛ فرم‌ها و تمام مسیرهای Write عمداً غیرفعال‌اند.' ) . '</p>',
				'test'        => 'alipasandi_companion_plugin_required',
			);
		},
	);
	return $tests;
}
add_filter( 'site_status_tests', 'alipasandi_companion_plugin_site_health' );

/** Set up native WordPress features. */
function alipasandi_theme_setup() {
	load_theme_textdomain( 'alipasandi-clinic', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
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
	// Playfair Display is self-hosted (WOFF2 weight 600/700) via theme.css @font-face — no Google Fonts request.
	wp_enqueue_style(
		'alipasandi-theme',
		get_template_directory_uri() . '/assets/css/theme.css',
		array(),
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

/*
 * Emergency NAP compatibility is intentionally READ-ONLY. The companion
 * plugin owns settings registration, validation, migration and admin UI.
 */
if ( ! function_exists( 'alipasandi_normalize_digits' ) ) {
	function alipasandi_normalize_digits( $value ) {
		return strtr( (string) $value, array( '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9' ) );
	}
}

if ( ! function_exists( 'alipasandi_clinic_option' ) ) {
	function alipasandi_clinic_option( $key ) {
		$key = sanitize_key( $key );
		if ( 'clinic_website' === $key ) {
			return home_url( '/' );
		}
		$value = get_option( 'alipasandi_' . $key, null );
		if ( null !== $value && '' !== $value ) {
			return $value;
		}
		$minimal = array(
			'clinic_business_name' => 'کلینیک دندانپزشکی دکتر کیوان علی‌پسندی',
			'clinic_city'          => 'نوشهر',
			'clinic_region'        => 'مازندران',
			'clinic_country'       => 'IR',
		);
		return isset( $minimal[ $key ] ) ? $minimal[ $key ] : '';
	}
}

if ( ! function_exists( 'alipasandi_phone_href' ) ) {
	function alipasandi_phone_href() {
		$phone = alipasandi_clinic_option( 'clinic_phone_e164' );
		if ( '' === trim( (string) $phone ) ) {
			$phone = alipasandi_clinic_option( 'clinic_phone' );
		}
		return preg_replace( '/[^0-9+]/', '', alipasandi_normalize_digits( $phone ) );
	}
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

/**
 * Design-locked primary service keys. Registry extensibility does NOT add
 * records to Home/Nav/Services Hub automatically; a fifth primary service
 * requires a versioned Theme/UI + SEO/Navigation release.
 */
function alipasandi_primary_service_keys() {
	return array( 'implant', 'crown', 'surgery', 'general' );
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
if ( ! function_exists( 'alipasandi_show_treatment_count' ) ) {
	function alipasandi_show_treatment_count() {
		return (bool) get_option( 'alipasandi_show_treatment_count', false );
	}
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
 * Page/content provisioning is intentionally not owned by the presentation
 * theme. Existing pages are managed explicitly in WordPress; the companion
 * plugin owns service data and operational business logic.
 */

