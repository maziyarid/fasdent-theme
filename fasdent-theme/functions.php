<?php
/**
 * Fasdent Theme — بوت‌استرپ قالب
 * کلینیک دندانپزشکی فس‌دنت — دکتر کیوان علی‌پسندی
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // دسترسی مستقیم ممنوع.
}

define( 'FASDENT_VERSION', '1.0.0' );
define( 'FASDENT_DIR', get_template_directory() );
define( 'FASDENT_URI', get_template_directory_uri() );

/* ── ماژول‌های قالب ─────────────────────────────── */
require FASDENT_DIR . '/inc/setup.php';        // تنظیمات پایه، منوها، theme supports
require FASDENT_DIR . '/inc/enqueue.php';      // CSS/JS/فونت لوکال
require FASDENT_DIR . '/inc/post-types.php';   // CPT: service, doctor, testimonial, faq
require FASDENT_DIR . '/inc/taxonomies.php';   // Taxonomy: service_category
require FASDENT_DIR . '/inc/acf-fields.php';   // فیلدهای ACF + فال‌بک متاباکس
require FASDENT_DIR . '/inc/customizer.php';   // تنظیمات کلینیک (تلفن، آدرس، ساعات…)
require FASDENT_DIR . '/inc/seo.php';          // Meta, Canonical, OG, Twitter
require FASDENT_DIR . '/inc/schema.php';       // JSON-LD Schemas
require FASDENT_DIR . '/inc/breadcrumb.php';   // Breadcrumb + BreadcrumbList Schema
require FASDENT_DIR . '/inc/security.php';     // هاردنینگ امنیتی
require FASDENT_DIR . '/inc/performance.php';  // Defer JS, Lazy Load, Preload
require FASDENT_DIR . '/inc/forms.php';        // فرم تماس و رزرو نوبت (Nonce + Sanitize)
require FASDENT_DIR . '/inc/elementor.php';    // سازگاری المنتور / Theme Builder

/* ── توابع کمکی سراسری ─────────────────────────── */

/**
 * دریافت شماره تلفن کلینیک (فرمت نمایش).
 */
function fasdent_phone(): string {
	return (string) get_theme_mod( 'fasdent_phone', '09201441469' );
}

/**
 * دریافت شماره تلفن برای لینک tel: (فرمت بین‌المللی).
 */
function fasdent_phone_link(): string {
	return (string) get_theme_mod( 'fasdent_phone_intl', '+989201441469' );
}

/**
 * دکمه Click-to-Call استاندارد.
 *
 * @param string $label برچسب دکمه.
 * @param string $class کلاس‌های اضافه.
 */
function fasdent_call_button( string $label = '', string $class = '' ): void {
	$label = $label ?: sprintf(
		/* translators: %s: شماره تلفن */
		__( 'تماس فوری: %s', 'fasdent' ),
		fasdent_phone()
	);
	printf(
		'<a href="tel:%1$s" class="btn btn-call %2$s"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i><span>%3$s</span></a>',
		esc_attr( fasdent_phone_link() ),
		esc_attr( $class ),
		esc_html( $label )
	);
}

/**
 * دکمه رزرو نوبت استاندارد (CTA).
 *
 * @param string $label برچسب دکمه.
 * @param string $class کلاس‌های اضافه.
 */
function fasdent_booking_button( string $label = '', string $class = '' ): void {
	$label = $label ?: __( 'رزرو نوبت آنلاین', 'fasdent' );
	printf(
		'<a href="%1$s" class="btn btn-primary %2$s"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i><span>%3$s</span></a>',
		esc_url( home_url( '/appointment/' ) ),
		esc_attr( $class ),
		esc_html( $label )
	);
}

/**
 * دریافت مقدار فیلد سفارشی (ACF یا متای فال‌بک).
 *
 * @param string   $key     کلید فیلد.
 * @param int|null $post_id شناسه پست.
 * @return mixed
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
