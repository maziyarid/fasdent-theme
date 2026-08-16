<?php
/** Secure contact and appointment-request handlers owned exclusively by the Site Plugin. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function alipasandi_form_page_url( $slug ) {
	$page = get_page_by_path( trim( $slug, '/' ) );
	return $page instanceof WP_Post ? get_permalink( $page ) : home_url( '/' . trim( $slug, '/' ) . '/' );
}

function alipasandi_form_redirect( $fallback_slug, $status ) {
	$referer = wp_get_referer();
	$target  = $referer ? $referer : alipasandi_form_page_url( $fallback_slug );
	wp_safe_redirect( add_query_arg( 'form_status', sanitize_key( $status ), $target ) . '#clinic-form' );
	exit;
}

function alipasandi_validate_form_request( $action, $nonce_name ) {
	if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), $action ) ) {
		return 'invalid_security';
	}
	if ( ! empty( $_POST['website_url'] ) ) { return 'invalid_security'; }
	return 'valid';
}

function alipasandi_text_length( $value ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( (string) $value ) : strlen( (string) $value );
}

/** Canonical configured appointment times; empty means appointment form is not operationally ready. */
function alipasandi_allowed_times() {
	$valid = alipasandi_validate_booking_slots( alipasandi_clinic_option( 'clinic_booking_times' ) );
	if ( is_wp_error( $valid ) || '' === trim( $valid ) ) { return array(); }
	return preg_split( '/\r\n|\r|\n/', $valid );
}

/** Explicit recipient only; there is no hard-coded Gmail fallback. */
function alipasandi_notify_email() {
	$email = sanitize_email( alipasandi_clinic_option( 'clinic_notify_email' ) );
	return is_email( $email ) ? $email : '';
}

function alipasandi_mail_configuration_ready() {
	$notify = alipasandi_notify_email();
	$from = sanitize_email( alipasandi_clinic_option( 'clinic_mail_from' ) );
	return is_email( $notify ) && is_email( $from ) && alipasandi_email_matches_home_domain( $from );
}

function alipasandi_form_channel_ready( $channel ) {
	if ( ! alipasandi_mail_configuration_ready() ) { return false; }
	if ( 'appointment' === $channel ) {
		return ! empty( alipasandi_allowed_services() ) && ! empty( alipasandi_allowed_times() );
	}
	return 'contact' === $channel;
}

/**
 * Privacy-minimized rate limit. Raw IP is never stored. The transient key is a
 * salted one-way identifier. Counter update is intentionally non-atomic; abuse
 * at scale belongs at the edge/WAF and this limitation is documented.
 */
function alipasandi_form_rate_limit_status( $action ) {
	$remote = isset( $_SERVER['REMOTE_ADDR'] ) ? trim( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) ) : '';
	$ip = (string) apply_filters( 'alipasandi_form_client_ip', $remote, $action );
	if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
		error_log( '[Alipasandi] Form rate-limit unavailable: no validated client IP for action=' . sanitize_key( $action ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return 'unavailable'; // Fail closed rather than sharing a global "unknown" quota.
	}
	$key = 'alipasandi_form_' . hash_hmac( 'sha256', sanitize_key( $action ) . '|' . $ip, wp_salt( 'auth' ) );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) { return 'rate_limited'; }
	set_transient( $key, $count + 1, 15 * MINUTE_IN_SECONDS );
	return 'valid';
}

/** Build branded HTML email. No user input is used in headers. */
function alipasandi_mail_html( $title, $rows ) {
	$cream = '#F2E9DA'; $caramel = '#C58D52'; $espresso = '#2C1D16';
	$body_rows = '';
	foreach ( $rows as $label => $value ) {
		$body_rows .= '<tr><td style="padding:10px 12px;border-bottom:1px solid #e8dcc8;color:#684936;font-size:13px;width:34%;">' . esc_html( $label ) . '</td><td style="padding:10px 12px;border-bottom:1px solid #e8dcc8;color:' . $espresso . ';font-size:14px;font-weight:600;">' . esc_html( $value ) . '</td></tr>';
	}
	$font_stack = 'Tahoma, Arial, sans-serif';
	$head = '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style>body,table,td,div,p,a,span{font-family:' . $font_stack . ' !important;}</style>';
	return '<!DOCTYPE html><html lang="fa" dir="rtl"><head>' . $head . '</head><body style="margin:0;padding:24px;background:' . $cream . ';font-family:' . $font_stack . ';direction:rtl;text-align:right;"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e6d7c0;"><tr><td style="background:' . $espresso . ';padding:20px 24px;"><div style="color:' . $caramel . ';font-size:12px;letter-spacing:.08em;">DR KEYVAN ALIPASANDI</div><div style="color:' . $cream . ';font-size:18px;font-weight:700;margin-top:6px;">' . esc_html( $title ) . '</div></td></tr><tr><td style="padding:8px 0 0;"><table width="100%" cellspacing="0" cellpadding="0">' . $body_rows . '</table></td></tr><tr><td style="padding:16px 24px 22px;color:#684936;font-size:12px;line-height:1.7;">این پیام از فرم وب‌سایت ارسال شده است. درخواست نوبت فقط پس از تماس کلینیک قطعی می‌شود.</td></tr></table></body></html>';
}

function alipasandi_handle_contact() {
	if ( ! alipasandi_form_channel_ready( 'contact' ) ) { alipasandi_form_redirect( 'contact', 'configuration_error' ); }
	if ( 'valid' !== alipasandi_validate_form_request( 'alipasandi_contact', 'alipasandi_contact_nonce' ) ) { alipasandi_form_redirect( 'contact', 'error' ); }

	$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? alipasandi_normalize_digits( sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	if ( alipasandi_text_length( $name ) < 2 || alipasandi_text_length( $name ) > 120 || ! preg_match( '/^[0-9+()\-\s]{7,20}$/', $phone ) || alipasandi_text_length( $subject ) > 160 || '' === trim( $message ) || alipasandi_text_length( $message ) > 3000 ) {
		alipasandi_form_redirect( 'contact', 'invalid' );
	}
	$rate = alipasandi_form_rate_limit_status( 'alipasandi_contact' );
	if ( 'valid' !== $rate ) { alipasandi_form_redirect( 'contact', $rate ); }

	$mail_subject = sprintf( 'پیام جدید سایت: %s', $subject ? $subject : 'تماس با کلینیک' );
	$html = alipasandi_mail_html( 'پیام جدید از وب‌سایت', array( 'نام'=>$name, 'تلفن'=>$phone, 'موضوع'=>$subject ?: '—', 'محل مطب'=>alipasandi_clinic_option( 'clinic_city' ), 'پیام'=>$message ) );
	$sent = wp_mail( alipasandi_notify_email(), $mail_subject, $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
	alipasandi_form_redirect( 'contact', $sent ? 'success' : 'mail_error' );
}
add_action( 'admin_post_nopriv_alipasandi_contact', 'alipasandi_handle_contact' );
add_action( 'admin_post_alipasandi_contact', 'alipasandi_handle_contact' );

function alipasandi_handle_appointment() {
	if ( ! alipasandi_form_channel_ready( 'appointment' ) ) { alipasandi_form_redirect( 'appointments', 'configuration_error' ); }
	if ( 'valid' !== alipasandi_validate_form_request( 'alipasandi_appointment', 'alipasandi_appointment_nonce' ) ) { alipasandi_form_redirect( 'appointments', 'error' ); }

	$fields = array();
	foreach ( array( 'service', 'date', 'time', 'name', 'phone' ) as $field ) {
		$fields[ $field ] = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
	}
	$fields['phone'] = alipasandi_normalize_digits( $fields['phone'] );
	$fields['time'] = alipasandi_normalize_digits( $fields['time'] );
	$fields['notes'] = isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '';
	$date = alipasandi_validate_appointment_date( $fields['date'] );
	$time_ok = in_array( $fields['time'], alipasandi_allowed_times(), true );
	$valid = ! in_array( '', array( $fields['service'], $fields['date'], $fields['time'], $fields['name'], $fields['phone'] ), true )
		&& in_array( $fields['service'], alipasandi_allowed_services(), true )
		&& $time_ok
		&& ! is_wp_error( $date )
		&& ( ! is_wp_error( $date ) && alipasandi_appointment_within_opening_hours( $date, $fields['time'] ) )
		&& alipasandi_text_length( $fields['name'] ) >= 2 && alipasandi_text_length( $fields['name'] ) <= 120
		&& preg_match( '/^[0-9+()\-\s]{7,20}$/', $fields['phone'] )
		&& alipasandi_text_length( $fields['notes'] ) <= 2000;
	if ( ! $valid ) { alipasandi_form_redirect( 'appointments', 'invalid' ); }

	$rate = alipasandi_form_rate_limit_status( 'alipasandi_appointment' );
	if ( 'valid' !== $rate ) { alipasandi_form_redirect( 'appointments', $rate ); }
	$html = alipasandi_mail_html( 'درخواست نوبت جدید', array( 'خدمت'=>$fields['service'], 'تاریخ پیشنهادی'=>$fields['date'], 'ساعت پیشنهادی'=>$fields['time'], 'نام'=>$fields['name'], 'تلفن'=>$fields['phone'], 'محل مطب'=>alipasandi_clinic_option( 'clinic_city' ), 'توضیحات'=>$fields['notes'] ?: '—' ) );
	$sent = wp_mail( alipasandi_notify_email(), 'درخواست نوبت جدید - ' . $fields['service'], $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
	alipasandi_form_redirect( 'appointments', $sent ? 'success' : 'mail_error' );
}
add_action( 'admin_post_nopriv_alipasandi_appointment', 'alipasandi_handle_appointment' );
add_action( 'admin_post_alipasandi_appointment', 'alipasandi_handle_appointment' );
