<?php
/**
 * Fasdent Theme — بوت‌استرپ قالب
 * کلینیک دندانپزشکی فس‌دنت — دکتر کیوان علی‌پسندی
 *
 * @package Fasdent
 * @version 2.2.1  (UI/UX polish + fully working floating chat)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FASDENT_VERSION', '2.2.1' );
define( 'FASDENT_DIR', get_template_directory() );
define( 'FASDENT_URI', get_template_directory_uri() );

require FASDENT_DIR . '/inc/setup.php';
require FASDENT_DIR . '/inc/enqueue.php';
require FASDENT_DIR . '/inc/post-types.php';
require FASDENT_DIR . '/inc/taxonomies.php';
require FASDENT_DIR . '/inc/acf-fields.php';
require FASDENT_DIR . '/inc/customizer.php';
require FASDENT_DIR . '/inc/seo.php';
require FASDENT_DIR . '/inc/schema.php';
require FASDENT_DIR . '/inc/breadcrumb.php';
require FASDENT_DIR . '/inc/security.php';
require FASDENT_DIR . '/inc/performance.php';
require FASDENT_DIR . '/inc/forms.php';
require FASDENT_DIR . '/inc/elementor.php';
require FASDENT_DIR . '/inc/toc.php';
require FASDENT_DIR . '/inc/post-meta.php';
require FASDENT_DIR . '/inc/related-posts.php';
require FASDENT_DIR . '/inc/cookies.php';
require FASDENT_DIR . '/inc/dashboard.php';
require FASDENT_DIR . '/inc/booking.php';
require FASDENT_DIR . '/inc/polls.php';
require FASDENT_DIR . '/inc/ajax-search.php';
require FASDENT_DIR . '/inc/admin-bookings.php';
require FASDENT_DIR . '/inc/floating-chat.php';

if ( is_admin() && file_exists( FASDENT_DIR . '/data/demo/import.php' ) ) {
	require FASDENT_DIR . '/data/demo/import.php';
}

function fasdent_phone(): string {
	return (string) get_theme_mod( 'fasdent_phone', '09201441469' );
}

function fasdent_phone_link(): string {
	return (string) get_theme_mod( 'fasdent_phone_intl', '+989201441469' );
}

function fasdent_call_button( string $label = '', string $class = '' ): void {
	$label = $label ?: sprintf( __( 'تماس فوری: %s', 'fasdent' ), fasdent_phone() );
	printf(
		'<a href="tel:%1$s" class="btn btn-call %2$s"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i><span>%3$s</span></a>',
		esc_attr( fasdent_phone_link() ),
		esc_attr( $class ),
		esc_html( $label )
	);
}

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
