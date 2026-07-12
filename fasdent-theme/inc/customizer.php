<?php
/**
 * Theme Customizer — تنظیمات کلینیک فس‌دنت
 * تمام اطلاعات ثابت سایت قابل ویرایش بدون کدنویسی.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ثبت تنظیمات Customizer.
 *
 * @param WP_Customize_Manager $wp_customize مدیر Customizer.
 */
function fasdent_customize_register( WP_Customize_Manager $wp_customize ): void {

	$wp_customize->add_panel( 'fasdent_panel', array(
		'title'    => __( 'تنظیمات کلینیک فس‌دنت', 'fasdent' ),
		'priority' => 10,
	) );

	/* ── بخش اطلاعات تماس ─────────────────────────── */
	$wp_customize->add_section( 'fasdent_contact', array(
		'title' => __( 'اطلاعات تماس', 'fasdent' ),
		'panel' => 'fasdent_panel',
	) );

	$contact_fields = array(
		'fasdent_clinic_name' => array( 'کلینیک دندانپزشکی فس‌دنت', __( 'نام کلینیک', 'fasdent' ) ),
		'fasdent_doctor_name' => array( 'دکتر کیوان علی‌پسندی', __( 'نام پزشک مسئول', 'fasdent' ) ),
		'fasdent_phone'       => array( '09201441469', __( 'شماره تماس (نمایشی)', 'fasdent' ) ),
		'fasdent_phone_intl'  => array( '+989201441469', __( 'شماره تماس (فرمت tel: بین‌المللی)', 'fasdent' ) ),
		'fasdent_address'     => array( 'تهران', __( 'آدرس کلینیک', 'fasdent' ) ),
		'fasdent_email'       => array( 'info@fasdent.ir', __( 'ایمیل', 'fasdent' ) ),
		'fasdent_hours'       => array( 'شنبه تا پنجشنبه ۹ تا ۲۱', __( 'ساعات کاری (نمایشی)', 'fasdent' ) ),
		'fasdent_geo_lat'     => array( '35.7219', __( 'عرض جغرافیایی (Geo Lat)', 'fasdent' ) ),
		'fasdent_geo_lng'     => array( '51.3347', __( 'طول جغرافیایی (Geo Lng)', 'fasdent' ) ),
		'fasdent_map_embed'   => array( '', __( 'کد Embed نقشه گوگل (فقط src آی‌فریم)', 'fasdent' ) ),
	);

	foreach ( $contact_fields as $id => $data ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $data[0],
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $data[1],
			'section' => 'fasdent_contact',
			'type'    => 'text',
		) );
	}

	/* ── شبکه‌های اجتماعی ─────────────────────────── */
	$wp_customize->add_section( 'fasdent_social', array(
		'title' => __( 'شبکه‌های اجتماعی', 'fasdent' ),
		'panel' => 'fasdent_panel',
	) );

	$socials = array(
		'fasdent_instagram' => __( 'اینستاگرام', 'fasdent' ),
		'fasdent_telegram'  => __( 'تلگرام', 'fasdent' ),
		'fasdent_whatsapp'  => __( 'واتس‌اپ', 'fasdent' ),
	);
	foreach ( $socials as $id => $label ) {
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'fasdent_social',
			'type'    => 'url',
		) );
	}

	/* ── آمار صفحه اصلی ───────────────────────────── */
	$wp_customize->add_section( 'fasdent_stats', array(
		'title' => __( 'آمار کلینیک (صفحه اصلی)', 'fasdent' ),
		'panel' => 'fasdent_panel',
	) );

	$stats = array(
		'fasdent_stat_patients' => array( '12000', __( 'تعداد بیماران درمان‌شده', 'fasdent' ) ),
		'fasdent_stat_years'    => array( '15', __( 'سال تجربه', 'fasdent' ) ),
		'fasdent_stat_implants' => array( '3500', __( 'ایمپلنت موفق', 'fasdent' ) ),
		'fasdent_stat_rating'   => array( '4.9', __( 'میانگین رضایت (از ۵)', 'fasdent' ) ),
	);
	foreach ( $stats as $id => $data ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $data[0],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $data[1],
			'section' => 'fasdent_stats',
			'type'    => 'text',
		) );
	}

	/* ── نوار اورژانس ─────────────────────────────── */
	$wp_customize->add_section( 'fasdent_emergency', array(
		'title' => __( 'نوار اورژانس', 'fasdent' ),
		'panel' => 'fasdent_panel',
	) );
	$wp_customize->add_setting( 'fasdent_emergency_text', array(
		'default'           => 'اورژانس دندانپزشکی ۲۴ ساعته — همین حالا تماس بگیرید:',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'fasdent_emergency_text', array(
		'label'   => __( 'متن نوار اورژانس', 'fasdent' ),
		'section' => 'fasdent_emergency',
		'type'    => 'text',
	) );

	/* ── Analytics & Integrations ─────────────────────── */
	$wp_customize->add_section( 'fasdent_analytics', array(
		'title' => __( 'آنالیتیکس و یکپارچگی', 'fasdent' ),
		'panel' => 'fasdent_panel',
	) );

	$analytics_fields = array(
		'fasdent_ga4_id'           => array( '', __( 'Google Analytics 4 ID (G-XXXXXXXXXX)', 'fasdent' ) ),
		'fasdent_clarity_id'       => array( '', __( 'Microsoft Clarity ID', 'fasdent' ) ),
		'fasdent_turnstile_key'    => array( '', __( 'Cloudflare Turnstile Site Key', 'fasdent' ) ),
		'fasdent_turnstile_secret' => array( '', __( 'Cloudflare Turnstile Secret Key', 'fasdent' ) ),
		'fasdent_cookie_text'      => array( 'این سایت از کوکی برای بهبود تجربه استفاده می‌کند.', __( 'متن بنر کوکی', 'fasdent' ) ),
		'fasdent_indexnow_key'     => array( '', __( 'IndexNow API Key (برای ارسال سریع به Bing/Google)', 'fasdent' ) ),
	);
	foreach ( $analytics_fields as $id => $data ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $data[0],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $data[1],
			'section' => 'fasdent_analytics',
			'type'    => 'text',
		) );
	}
}
add_action( 'customize_register', 'fasdent_customize_register' );

/**
 * تزریق Google Analytics 4 در head.
 * Consent Mode v2: gtag('config') is deferred — GA4 script loads but data is
 * not sent until the user grants consent via the cookie banner (main.js).
 */
function fasdent_inject_analytics(): void {
	$ga4_id = get_theme_mod( 'fasdent_ga4_id', '' );
	if ( ! $ga4_id ) {
		return;
	}
	$ga4_id = esc_js( $ga4_id );
	// Script tag loads async; gtag('config') is intentionally placed here so
	// GA4 queues the config but honours the default 'denied' consent set by
	// fasdent_consent_mode_default() (cookies.php, priority 0).
	echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga4_id}\"></script>\n";
	echo "<script>\n";
	echo "window.dataLayer=window.dataLayer||[];\n";
	echo "function gtag(){dataLayer.push(arguments);}\n";
	echo "gtag('js',new Date());\n";
	// Use 'update' with consent_required so hits are queued not sent until consent.
	echo "gtag('config','{$ga4_id}',{wait_for_update:500,consent_required:true});\n";
	echo "</script>\n";
}
add_action( 'wp_head', 'fasdent_inject_analytics', 10 );

/**
 * تزریق Microsoft Clarity در head.
 */
function fasdent_inject_clarity(): void {
	$cid = get_theme_mod( 'fasdent_clarity_id', '' );
	if ( ! $cid ) {
		return;
	}
	$cid = esc_js( $cid );
	echo "<script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y)})(window,document,'clarity','script','{$cid}');</script>\n";
}
add_action( 'wp_head', 'fasdent_inject_clarity', 11 );
