<?php
/** Secure native contact and appointment form handlers. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Redirect back to a form with a compact status code. */
function alipasandi_form_redirect( $fallback_slug, $status ) {
	$referer = wp_get_referer();
	$target  = $referer ? $referer : alipasandi_page_url( $fallback_slug );
	wp_safe_redirect( add_query_arg( 'form_status', sanitize_key( $status ), $target ) . '#clinic-form' );
	exit;
}

/** Reject bots and requests without a valid nonce. */
function alipasandi_validate_form_request( $action, $nonce_name ) {
	if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), $action ) ) {
		return false;
	}
	return empty( $_POST['website_url'] );
}

/** Normalize Persian and Arabic numerals before validating phone input. */
function alipasandi_normalize_digits( $value ) {
	return strtr(
		$value,
		array(
			'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
			'۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
			'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
			'٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
		)
	);
}

/** Count characters safely on hosts where mbstring is not enabled. */
function alipasandi_text_length( $value ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
}

/** Appointment choices shared by the form and its server-side validator. */
function alipasandi_allowed_services() {
	return array( 'ایمپلنت دندان', 'روکش زیرکونیا', 'جراحی و لثه', 'درمان عمومی', 'مشاوره اولیه', 'عصب‌کشی', 'جرم‌گیری', 'سایر' );
}

/** Clinic time choices shared by the form and its server-side validator. */
function alipasandi_allowed_times() {
	return array( '۱۰:۰۰', '۱۰:۳۰', '۱۱:۰۰', '۱۱:۳۰', '۱۲:۰۰', '۱۲:۳۰', '۱۴:۰۰', '۱۴:۳۰', '۱۵:۰۰', '۱۵:۳۰', '۱۶:۰۰', '۱۶:۳۰', '۱۷:۰۰', '۱۷:۳۰', '۱۸:۰۰', '۱۸:۳۰' );
}

/** Handle the contact form and email the WordPress administrator. */
function alipasandi_handle_contact() {
	if ( ! alipasandi_validate_form_request( 'alipasandi_contact', 'alipasandi_contact_nonce' ) ) {
		alipasandi_form_redirect( 'contact', 'error' );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? alipasandi_normalize_digits( sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( alipasandi_text_length( $name ) < 2 || '' === $phone || ! preg_match( '/^[0-9+()\-\s]{7,20}$/', $phone ) ) {
		alipasandi_form_redirect( 'contact', 'invalid' );
	}

	$mail_subject = sprintf( 'پیام جدید سایت: %s', $subject ? $subject : 'تماس با کلینیک' );
	$mail_body    = "نام: {$name}\nتلفن: {$phone}\nموضوع: {$subject}\n\nپیام:\n{$message}";
	$sent         = wp_mail( get_option( 'admin_email' ), $mail_subject, $mail_body );

	alipasandi_form_redirect( 'contact', $sent ? 'success' : 'mail_error' );
}
add_action( 'admin_post_nopriv_alipasandi_contact', 'alipasandi_handle_contact' );
add_action( 'admin_post_alipasandi_contact', 'alipasandi_handle_contact' );

/** Handle appointment requests and email the WordPress administrator. */
function alipasandi_handle_appointment() {
	if ( ! alipasandi_validate_form_request( 'alipasandi_appointment', 'alipasandi_appointment_nonce' ) ) {
		alipasandi_form_redirect( 'appointments', 'error' );
	}

	$fields = array();
	foreach ( array( 'service', 'date', 'time', 'name', 'phone' ) as $field ) {
		$fields[ $field ] = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
	}
	$fields['phone'] = alipasandi_normalize_digits( $fields['phone'] );
	$fields['notes'] = isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '';

	$valid_date = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $fields['date'] ) && $fields['date'] >= current_time( 'Y-m-d' );
	$valid_form = ! in_array( '', array( $fields['service'], $fields['date'], $fields['time'], $fields['name'], $fields['phone'] ), true )
		&& in_array( $fields['service'], alipasandi_allowed_services(), true )
		&& in_array( $fields['time'], alipasandi_allowed_times(), true )
		&& alipasandi_text_length( $fields['name'] ) >= 2
		&& preg_match( '/^[0-9+()\-\s]{7,20}$/', $fields['phone'] )
		&& $valid_date;

	if ( ! $valid_form ) {
		alipasandi_form_redirect( 'appointments', 'invalid' );
	}

	$mail_subject = sprintf( 'درخواست نوبت جدید: %s', $fields['name'] );
	$mail_body    = "خدمت: {$fields['service']}\nتاریخ درخواستی: {$fields['date']}\nساعت: {$fields['time']}\nنام: {$fields['name']}\nتلفن: {$fields['phone']}\n\nتوضیحات:\n{$fields['notes']}";
	$sent         = wp_mail( get_option( 'admin_email' ), $mail_subject, $mail_body );

	alipasandi_form_redirect( 'appointments', $sent ? 'success' : 'mail_error' );
}
add_action( 'admin_post_nopriv_alipasandi_appointment', 'alipasandi_handle_appointment' );
add_action( 'admin_post_alipasandi_appointment', 'alipasandi_handle_appointment' );
