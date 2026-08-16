<?php
/**
 * Contact and appointment request processing.
 *
 * @package Alipasandi_Service_Content
 * @since 1.3.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Validate mail identity without pretending that SMTP delivery is proven. */
function alipasandi_mail_configuration() {
	$notify = sanitize_email( alipasandi_clinic_option( 'clinic_notify_email' ) );
	$from = sanitize_email( alipasandi_clinic_option( 'clinic_mail_from' ) );
	$from_name = trim( (string) alipasandi_clinic_option( 'clinic_mail_from_name' ) );
	$errors = array();
	if ( ! is_email( $notify ) ) { $errors[] = 'notify_email_invalid'; }
	if ( ! is_email( $from ) ) { $errors[] = 'mail_from_invalid'; }
	if ( '' === $from_name ) { $errors[] = 'mail_from_name_missing'; }
	$home_host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
	$home_host = preg_replace( '/^www\./', '', $home_host );
	$from_domain = false !== strpos( $from, '@' ) ? strtolower( substr( strrchr( $from, '@' ), 1 ) ) : '';
	$domain_ok = '' !== $home_host && ( $from_domain === $home_host || str_ends_with( $from_domain, '.' . $home_host ) );
	if ( ! $domain_ok ) { $errors[] = 'mail_from_domain_mismatch'; }
	return array( 'pass'=>empty( $errors ), 'errors'=>$errors, 'notify_email'=>$notify, 'mail_from'=>$from, 'mail_from_name'=>$from_name );
}

/** One shared form-readiness decision with a machine-valid direct-phone route. */
function alipasandi_form_readiness( $channel ) {
	$channel = sanitize_key( $channel );
	$mail = alipasandi_mail_configuration();
	$phone = alipasandi_phone_href();
	$phone_valid = alipasandi_valid_e164( $phone );
	$errors = $mail['errors'];
	if ( ! $phone_valid ) { $errors[] = 'direct_phone_invalid'; }
	if ( 'appointment' === $channel ) {
		$policy = alipasandi_booking_policy_configuration();
		$interop = alipasandi_validate_booking_hours_interoperability();
		$future = alipasandi_available_future_slots();
		if ( empty( $policy['pass'] ) ) { $errors = array_merge( $errors, $policy['errors'] ); }
		if ( empty( $interop['pass'] ) ) { $errors[] = $interop['code']; }
		if ( empty( $future['pass'] ) ) { $errors[] = $future['code']; }
	}
	$ready = empty( $errors );
	$mode = $ready ? 'full' : ( $phone_valid ? 'phone_only' : ( $mail['pass'] ? 'mail_only' : 'unavailable' ) );
	return array(
		'ready'          => $ready,
		'mode'           => $mode,
		'errors'         => array_values( array_unique( $errors ) ),
		'mail_configured'=> $mail['pass'],
		'phone_valid'    => $phone_valid,
		'phone_e164'     => $phone_valid ? $phone : '',
	);
}

/** Public Theme contract. */
function alipasandi_form_channel_ready( $channel ) {
	$readiness = alipasandi_form_readiness( $channel );
	return ! empty( $readiness['ready'] );
}

/** Validate a patient's callback number without treating it as the clinic route. */
function alipasandi_valid_patient_phone( $value ) {
	$value = alipasandi_normalize_digits( $value );
	$value = preg_replace( '/[\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $value );
	$digits = preg_replace( '/[\s()+.-]+/u', '', (string) $value );
	return 1 === preg_match( '/^[0-9]{7,15}$/', (string) $digits );
}

/** Privacy-minimized rate-limit key; proxy headers are intentionally ignored. */
function alipasandi_form_client_key( $action ) {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	if ( function_exists( 'wp_privacy_anonymize_ip' ) ) { $ip = wp_privacy_anonymize_ip( $ip ); }
	return 'alipasandi_form_rate_' . hash_hmac( 'sha256', sanitize_key( $action ) . '|' . $ip, wp_salt( 'nonce' ) );
}

/** Bounded transient limiter. Edge/WAF remains the scale owner. */
function alipasandi_form_rate_limited( $action ) {
	$key = alipasandi_form_client_key( $action );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) { return true; }
	set_transient( $key, $count + 1, 15 * MINUTE_IN_SECONDS );
	return false;
}

/** Safe form result redirect. */
function alipasandi_form_redirect( $page, $status ) {
	$url = function_exists( 'alipasandi_page_url' ) ? alipasandi_page_url( $page ) : home_url( '/' . trim( $page, '/' ) . '/' );
	wp_safe_redirect( add_query_arg( 'form_status', sanitize_key( $status ), $url ) . '#clinic-form' );
	exit;
}

/** Render the actual clinic NAP block included in outbound messages. */
function alipasandi_mail_nap_block() {
	$lines = array();
	$name = trim( (string) alipasandi_clinic_option( 'clinic_business_name' ) );
	$address = Alipasandi_NAP_Helper::display_address();
	$phone_display = trim( (string) alipasandi_clinic_option( 'clinic_phone' ) );
	$phone_e164 = alipasandi_phone_href();
	if ( '' !== $name ) { $lines[] = $name; }
	if ( '' !== $address ) { $lines[] = $address; }
	if ( '' !== $phone_display && '' !== $phone_e164 ) { $lines[] = $phone_display . ' (' . $phone_e164 . ')'; }
	return implode( "\n", $lines );
}

/** Send a plain-text operational message through WordPress mail. */
function alipasandi_send_form_mail( $subject, $lines ) {
	$config = alipasandi_mail_configuration();
	if ( empty( $config['pass'] ) ) { return false; }
	$lines[] = '';
	$lines[] = '---';
	$lines[] = alipasandi_mail_nap_block();
	$lines[] = 'Submitted at: ' . current_time( 'mysql' ) . ' (' . wp_timezone_string() . ')';
	return wp_mail(
		$config['notify_email'],
		wp_strip_all_tags( $subject ),
		implode( "\n", array_map( 'wp_strip_all_tags', $lines ) ),
		array( 'Content-Type: text/plain; charset=UTF-8' )
	);
}

/** Process contact requests. */
function alipasandi_handle_contact() {
	if ( 'POST' !== strtoupper( isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '' ) ) { alipasandi_form_redirect( 'contact', 'invalid' ); }
	$nonce = isset( $_POST['alipasandi_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['alipasandi_contact_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'alipasandi_contact' ) ) { alipasandi_form_redirect( 'contact', 'invalid' ); }
	$honeypot = isset( $_POST['website_url'] ) ? trim( (string) wp_unslash( $_POST['website_url'] ) ) : '';
	if ( '' !== $honeypot ) { alipasandi_form_redirect( 'contact', 'success' ); }
	if ( alipasandi_form_rate_limited( 'contact' ) ) { alipasandi_form_redirect( 'contact', 'rate_limited' ); }
	if ( ! alipasandi_form_channel_ready( 'contact' ) ) { alipasandi_form_redirect( 'contact', 'unavailable' ); }
	$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	if ( '' === $name || '' === $message || ! alipasandi_valid_patient_phone( $phone ) ) { alipasandi_form_redirect( 'contact', 'invalid' ); }
	$sent = alipasandi_send_form_mail( 'پیام جدید وب‌سایت' . ( '' !== $subject ? ' — ' . $subject : '' ), array( 'نام: ' . $name, 'شماره تماس: ' . $phone, 'موضوع: ' . $subject, 'پیام:', $message ) );
	alipasandi_form_redirect( 'contact', $sent ? 'success' : 'mail_error' );
}
add_action( 'admin_post_nopriv_alipasandi_contact', 'alipasandi_handle_contact' );
add_action( 'admin_post_alipasandi_contact', 'alipasandi_handle_contact' );

/** Process appointment requests. */
function alipasandi_handle_appointment() {
	if ( 'POST' !== strtoupper( isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '' ) ) { alipasandi_form_redirect( 'appointments', 'invalid' ); }
	$nonce = isset( $_POST['alipasandi_appointment_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['alipasandi_appointment_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'alipasandi_appointment' ) ) { alipasandi_form_redirect( 'appointments', 'invalid' ); }
	$honeypot = isset( $_POST['website_url'] ) ? trim( (string) wp_unslash( $_POST['website_url'] ) ) : '';
	if ( '' !== $honeypot ) { alipasandi_form_redirect( 'appointments', 'success' ); }
	if ( alipasandi_form_rate_limited( 'appointment' ) ) { alipasandi_form_redirect( 'appointments', 'rate_limited' ); }
	if ( ! alipasandi_form_channel_ready( 'appointment' ) ) { alipasandi_form_redirect( 'appointments', 'unavailable' ); }
	$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$service = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
	$time = isset( $_POST['time'] ) ? sanitize_text_field( wp_unslash( $_POST['time'] ) ) : '';
	$notes = isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '';
	if ( '' === $name || ! alipasandi_valid_patient_phone( $phone ) || ! in_array( $service, alipasandi_allowed_services(), true ) ) { alipasandi_form_redirect( 'appointments', 'invalid' ); }
	$slot = alipasandi_validate_appointment_datetime( $date, $time );
	if ( is_wp_error( $slot ) ) { alipasandi_form_redirect( 'appointments', 'invalid' ); }
	$sent = alipasandi_send_form_mail( 'درخواست نوبت جدید — ' . $service, array( 'نام: ' . $name, 'شماره تماس: ' . $phone, 'خدمت: ' . $service, 'تاریخ پیشنهادی: ' . $date, 'زمان پیشنهادی: ' . $time, 'توضیحات:', $notes ) );
	alipasandi_form_redirect( 'appointments', $sent ? 'success' : 'mail_error' );
}
add_action( 'admin_post_nopriv_alipasandi_appointment', 'alipasandi_handle_appointment' );
add_action( 'admin_post_alipasandi_appointment', 'alipasandi_handle_appointment' );
