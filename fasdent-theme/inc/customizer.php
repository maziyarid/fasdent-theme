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
}
add_action( 'customize_register', 'fasdent_customize_register' );
