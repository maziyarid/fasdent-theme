<?php
/**
 * فرم تماس و رزرو نوبت — Fasdent
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * پردازش فرم تماس/رزرو.
 */
function fasdent_handle_form_submission(): void {
	if ( ! isset( $_POST['fasdent_form_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['fasdent_form_nonce'] ) ), 'fasdent_form_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'اعتبارسنجی نامعتبر است.' ) );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$type    = isset( $_POST['form_type'] ) ? sanitize_text_field( wp_unslash( $_POST['form_type'] ) ) : 'contact';

	if ( '' === $name || '' === $phone ) {
		wp_send_json_error( array( 'message' => 'نام و شماره تماس الزامی است.' ) );
	}

	$subject = 'درخواست ' . ( 'appointment' === $type ? 'رزرو نوبت' : 'تماس' ) . ' از سایت فس‌دنت';
	$body    = "نام: {$name}\nتلفن: {$phone}\nایمیل: {$email}\nپیام: {$message}";
	$to      = get_theme_mod( 'fasdent_email', 'info@fasdent.ir' );

	wp_mail( $to, $subject, $body );
	wp_send_json_success( array( 'message' => 'درخواست شما با موفقیت ثبت شد. در اسرع وقت با شما تماس می‌گیریم.' ) );
}
add_action( 'wp_ajax_fasdent_handle_form', 'fasdent_handle_form_submission' );
add_action( 'wp_ajax_nopriv_fasdent_handle_form', 'fasdent_handle_form_submission' );
