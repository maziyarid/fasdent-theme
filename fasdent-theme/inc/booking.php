<?php
/**
 * Booking System — Fasdent
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_booking_create_tables(): void {
	global $wpdb;
	if ( (int) get_option( 'fasdent_booking_db_version', 0 ) >= 1 ) { return; }
	$charset = $wpdb->get_charset_collate();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fasdent_bookings (
		id            bigint(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
		name          varchar(100) NOT NULL DEFAULT '',
		phone         varchar(20)  NOT NULL DEFAULT '',
		email         varchar(100)          DEFAULT NULL,
		age           tinyint(3)   UNSIGNED DEFAULT NULL,
		gender        varchar(10)           DEFAULT NULL,
		symptoms      text         NOT NULL,
		medical_hist  text                  DEFAULT NULL,
		medications   text                  DEFAULT NULL,
		allergies     text                  DEFAULT NULL,
		service_id    bigint(20)   UNSIGNED DEFAULT NULL,
		doctor_id     bigint(20)   UNSIGNED DEFAULT NULL,
		preferred_date date                 DEFAULT NULL,
		time_range    varchar(20)           DEFAULT NULL,
		is_emergency  tinyint(1)   NOT NULL DEFAULT 0,
		privacy_ok    tinyint(1)   NOT NULL DEFAULT 0,
		status        varchar(20)  NOT NULL DEFAULT 'pending',
		admin_notes   text                  DEFAULT NULL,
		ga_session    varchar(100)          DEFAULT NULL,
		ip_address    varchar(45)           DEFAULT NULL,
		created_at    datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at    datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY status (status),
		KEY preferred_date (preferred_date),
		KEY phone (phone)
	) $charset;" );
	update_option( 'fasdent_booking_db_version', 1 );
}
add_action( 'after_switch_theme', 'fasdent_booking_create_tables' );
add_action( 'init', function () {
	static $done = false;
	if ( ! $done && ! get_option( 'fasdent_booking_db_version' ) ) {
		fasdent_booking_create_tables(); $done = true;
	}
} );

function fasdent_save_booking( array $data ) {
	global $wpdb;
	// Build row and format strings together to avoid NULL-with-%d issues.
	$row     = [];
	$formats = [];

	$str_fields = [ 'name', 'phone', 'symptoms', 'status' ];
	$null_str   = [ 'email', 'gender', 'medical_hist', 'medications', 'allergies', 'preferred_date', 'time_range', 'ga_session', 'ip_address' ];
	$null_int   = [ 'age', 'service_id', 'doctor_id' ];
	$int_fields = [ 'is_emergency', 'privacy_ok' ];

	foreach ( $str_fields as $f ) {
		$row[ $f ]     = $data[ $f ] ?? '';
		$formats[]     = '%s';
	}
	foreach ( $null_str as $f ) {
		$row[ $f ]  = isset( $data[ $f ] ) && '' !== $data[ $f ] ? (string) $data[ $f ] : null;
		$formats[]  = '%s';
	}
	foreach ( $null_int as $f ) {
		$v = isset( $data[ $f ] ) && '' !== $data[ $f ] ? (int) $data[ $f ] : null;
		$row[ $f ]  = $v;
		$formats[]  = null === $v ? '%s' : '%d'; // NULL stored as string NULL.
	}
	foreach ( $int_fields as $f ) {
		$row[ $f ]  = (int) ( $data[ $f ] ?? 0 );
		$formats[]  = '%d';
	}
	$row['status'] = 'pending'; // override.

	$result = $wpdb->insert( $wpdb->prefix . 'fasdent_bookings', $row, $formats );
	return $result ? (int) $wpdb->insert_id : false;
}

function fasdent_ajax_submit_booking(): void {
	if ( ! isset( $_POST['fasdent_form_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['fasdent_form_nonce'] ) ), 'fasdent_form_nonce' ) ) {
		wp_send_json_error( [ 'message' => 'اعتبارسنجی نامعتبر.' ] );
	}
	if ( ! empty( $_POST['_hp_website'] ) ) { wp_send_json_success( [ 'message' => 'ثبت شد.' ] ); }
	$ip  = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$key = 'fasdent_booking_' . md5( $ip );
	if ( (int) get_transient( $key ) >= 3 ) {
		wp_send_json_error( [ 'message' => 'تعداد درخواست‌ها بیش از حد مجاز است.' ] );
	}
	set_transient( $key, (int) get_transient( $key ) + 1, HOUR_IN_SECONDS );

	$name    = sanitize_text_field( wp_unslash( $_POST['name']    ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone']   ?? '' ) );
	$email   = sanitize_email(       wp_unslash( $_POST['email']   ?? '' ) );
	$age     = (int) ( $_POST['age'] ?? 0 );
	$gender  = sanitize_key(         wp_unslash( $_POST['gender']  ?? '' ) );
	$symptoms= sanitize_textarea_field( wp_unslash( $_POST['symptoms']     ?? '' ) );
	$mhist   = sanitize_textarea_field( wp_unslash( $_POST['medical_hist'] ?? '' ) );
	$meds    = sanitize_textarea_field( wp_unslash( $_POST['medications']  ?? '' ) );
	$allergy = sanitize_textarea_field( wp_unslash( $_POST['allergies']    ?? '' ) );
	$svc_id  = (int) ( $_POST['service_id']  ?? 0 );
	$doc_id  = (int) ( $_POST['doctor_id']   ?? 0 );
	$pref_dt = sanitize_text_field( wp_unslash( $_POST['preferred_date'] ?? '' ) );
	$trange  = sanitize_key(        wp_unslash( $_POST['time_range']     ?? '' ) );
	$emerg   = ! empty( $_POST['is_emergency'] ) ? 1 : 0;
	$privacy = ! empty( $_POST['privacy_ok']  ) ? 1 : 0;
	$ga_sess = sanitize_text_field( wp_unslash( $_POST['ga_session'] ?? '' ) );

	if ( '' === $name || '' === $phone ) {
		wp_send_json_error( [ 'message' => 'نام و شماره تماس الزامی است.' ] );
	}
	if ( ! preg_match( '/^(\+98|0)?9\d{9}$/', preg_replace( '/\s+/', '', $phone ) ) ) {
		wp_send_json_error( [ 'message' => 'شماره موبایل معتبر نیست.' ] );
	}
	if ( '' === $symptoms ) {
		wp_send_json_error( [ 'message' => 'شرح مشکل دندانی الزامی است.' ] );
	}
	if ( ! $privacy ) {
		wp_send_json_error( [ 'message' => 'تایید حریم خصوصی الزامی است.' ] );
	}

	$booking_id = fasdent_save_booking( [
		'name'           => $name,
		'phone'          => $phone,
		'email'          => $email  ?: null,
		'age'            => $age    ?: null,
		'gender'         => $gender ?: null,
		'symptoms'       => $symptoms,
		'medical_hist'   => $mhist    ?: null,
		'medications'    => $meds     ?: null,
		'allergies'      => $allergy  ?: null,
		'service_id'     => $svc_id   ?: null,
		'doctor_id'      => $doc_id   ?: null,
		'preferred_date' => $pref_dt  ?: null,
		'time_range'     => $trange   ?: null,
		'is_emergency'   => $emerg,
		'privacy_ok'     => $privacy,
		'ga_session'     => $ga_sess  ?: null,
		'ip_address'     => $ip,
	] );

	$svc_name = $svc_id ? get_the_title( $svc_id ) : 'مشخص نشده';
	$subject  = "نوبت جدید — {$name} — #{$booking_id}";
	$body     = "ID: #{$booking_id}\nنام: {$name}\nتلفن: {$phone}\nخدمت: {$svc_name}\nتاریخ: {$pref_dt}\nبازه: {$trange}\nاورژانسی: " . ( $emerg ? 'بله' : 'خیر' ) . "\n\nشرح:\n{$symptoms}\n\nسابقه:\n{$mhist}";
	wp_mail( get_theme_mod( 'fasdent_email', 'info@fasdent.ir' ), $subject, $body );

	wp_send_json_success( [
		'message'    => 'نوبت شما ثبت شد. پشتیبانی با شماره ' . fasdent_phone() . ' با شما تماس خواهد گرفت.',
		'booking_id' => $booking_id,
	] );
}
add_action( 'wp_ajax_fasdent_submit_booking',        'fasdent_ajax_submit_booking' );
add_action( 'wp_ajax_nopriv_fasdent_submit_booking', 'fasdent_ajax_submit_booking' );
