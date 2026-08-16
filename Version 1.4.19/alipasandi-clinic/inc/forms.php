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
	$raw = alipasandi_clinic_option( 'clinic_booking_times' );
	if ( ! is_string( $raw ) || '' === trim( $raw ) ) {
		$raw = "۱۰:۰۰\n۱۰:۳۰\n۱۱:۰۰\n۱۱:۳۰\n۱۲:۰۰\n۱۲:۳۰\n۱۴:۰۰\n۱۴:۳۰\n۱۵:۰۰\n۱۵:۳۰\n۱۶:۰۰\n۱۶:۳۰\n۱۷:۰۰\n۱۷:۳۰\n۱۸:۰۰\n۱۸:۳۰";
	}
	$lines = preg_split( '/\r\n|\r|\n/', $raw );
	$times = array();
	foreach ( $lines as $line ) {
		$line = trim( alipasandi_normalize_digits( $line ) );
		// Keep Persian digits for display consistency if original used them — normalize both ways for match.
		$line = trim( $line );
		if ( '' !== $line ) {
			$times[] = $line;
		}
	}
	// Prefer original customizer lines (may be Persian digits) for display:
	$display = array();
	foreach ( preg_split( '/\r\n|\r|\n/', alipasandi_clinic_option( 'clinic_booking_times' ) ?: $raw ) as $line ) {
		$line = trim( $line );
		if ( '' !== $line ) {
			$display[] = $line;
		}
	}
	return $display ? array_values( array_unique( $display ) ) : array( '۱۰:۰۰', '۱۴:۰۰' );
}

/** Notification inbox for forms (Customizer → clinic_notify_email). */
function alipasandi_notify_email() {
	$email = alipasandi_clinic_option( 'clinic_notify_email' );
	if ( ! is_email( $email ) ) {
		$email = 'drkeyvanalipasandi@gmail.com';
	}
	return $email;
}

/** Build branded HTML email (RTL, palette). Fonts fall back in clients. */
function alipasandi_mail_html( $title, $rows ) {
	$cream    = '#F2E9DA';
	$caramel  = '#C58D52';
	$espresso = '#2C1D16';
	$body_rows = '';
	foreach ( $rows as $label => $value ) {
		$body_rows .= '<tr><td style="padding:10px 12px;border-bottom:1px solid #e8dcc8;color:#684936;font-size:13px;width:34%;">' . esc_html( $label ) . '</td><td style="padding:10px 12px;border-bottom:1px solid #e8dcc8;color:' . $espresso . ';font-size:14px;font-weight:600;">' . esc_html( $value ) . '</td></tr>';
	}

	// System font stack only — no external CDN dependency for reliability.
	$font_stack = 'Tahoma, Arial, sans-serif';
	$head       = '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
		. '<style>body,table,td,div,p,a,span{font-family:' . $font_stack . ' !important;}</style>';

	return '<!DOCTYPE html><html lang="fa" dir="rtl"><head>' . $head . '</head>'
		. '<body style="margin:0;padding:24px;background:' . $cream . ';font-family:' . $font_stack . ';direction:rtl;text-align:right;">'
		. '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e6d7c0;">'
		. '<tr><td style="background:' . $espresso . ';padding:20px 24px;">'
		. '<div style="color:' . $caramel . ';font-size:12px;letter-spacing:.08em;">DR KEYVAN ALIPASANDI</div>'
		. '<div style="color:' . $cream . ';font-size:18px;font-weight:700;margin-top:6px;">' . esc_html( $title ) . '</div>'
		. '</td></tr>'
		. '<tr><td style="padding:8px 0 0;"><table width="100%" cellspacing="0" cellpadding="0">' . $body_rows . '</table></td></tr>'
		. '<tr><td style="padding:16px 24px 22px;color:#684936;font-size:12px;line-height:1.7;">این پیام از فرم وب‌سایت ارسال شده است. نوبت پس از تماس کلینیک قطعی می‌شود.</td></tr>'
		. '</table></body></html>';
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
	$location     = alipasandi_clinic_option( 'clinic_city' ) ?: 'نوشهر';
	$html         = alipasandi_mail_html(
		'پیام جدید از وب‌سایت',
		array(
			'نام'       => $name,
			'تلفن'      => $phone,
			'موضوع'     => $subject ? $subject : '—',
			'محل مطب'   => $location,
			'پیام'      => $message,
		)
	);
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$sent    = wp_mail( alipasandi_notify_email(), $mail_subject, $html, $headers );

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
	$allowed_times_norm = array_map( 'alipasandi_normalize_digits', alipasandi_allowed_times() );
	$time_ok = in_array( $fields['time'], alipasandi_allowed_times(), true )
		|| in_array( alipasandi_normalize_digits( $fields['time'] ), $allowed_times_norm, true );
	$valid_form = ! in_array( '', array( $fields['service'], $fields['date'], $fields['time'], $fields['name'], $fields['phone'] ), true )
		&& in_array( $fields['service'], alipasandi_allowed_services(), true )
		&& $time_ok
		&& alipasandi_text_length( $fields['name'] ) >= 2
		&& preg_match( '/^[0-9+()\-\s]{7,20}$/', $fields['phone'] )
		&& $valid_date;

	if ( ! $valid_form ) {
		alipasandi_form_redirect( 'appointments', 'invalid' );
	}

	$location     = alipasandi_clinic_option( 'clinic_city' ) ?: 'نوشهر';
	$address      = alipasandi_clinic_option( 'clinic_address' );
	$mail_subject = sprintf( 'درخواست نوبت جدید — %s — %s', $location, $fields['name'] );
	$html         = alipasandi_mail_html(
		'درخواست نوبت جدید',
		array(
			'محل مراجعه' => $location,
			'آدرس'       => $address,
			'خدمت'       => $fields['service'],
			'تاریخ'      => $fields['date'],
			'ساعت'       => $fields['time'],
			'نام'        => $fields['name'],
			'تلفن'       => $fields['phone'],
			'توضیحات'    => $fields['notes'] ? $fields['notes'] : '—',
		)
	);
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$sent    = wp_mail( alipasandi_notify_email(), $mail_subject, $html, $headers );

	alipasandi_form_redirect( 'appointments', $sent ? 'success' : 'mail_error' );
}
add_action( 'admin_post_nopriv_alipasandi_appointment', 'alipasandi_handle_appointment' );
add_action( 'admin_post_alipasandi_appointment', 'alipasandi_handle_appointment' );
