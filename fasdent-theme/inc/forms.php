<?php
/**
 * فرم تماس و رزرو نوبت — Fasdent
 * - Nonce + Sanitize
 * - Honeypot بررسی
 * - محدودیت نرخ (Rate Limiting): ۳ ارسال در ساعت از هر IP
 * - ذخیره در پست‌تایپ fasdent_submission (BUG-008 FIX)
 * - ارسال ایمیل اطلاع‌رسانی
 * - حذف فیلد ایمیل از نظرات
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ═══════════════════════════════════════════════════
 * ثبت CPT برای ذخیره فرم‌های ارسال‌شده
 * ═══════════════════════════════════════════════════ */
function fasdent_register_submission_cpt(): void {
	register_post_type( 'fasdent_submission', array(
		'labels'             => array(
			'name'          => __( 'فرم‌های دریافتی', 'fasdent' ),
			'singular_name' => __( 'فرم دریافتی', 'fasdent' ),
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'capability_type'    => 'post',
		'supports'           => array( 'title', 'editor', 'custom-fields' ),
		'menu_icon'          => 'dashicons-email-alt',
		'menu_position'      => 25,
		'show_in_rest'       => false,
	) );
}
add_action( 'init', 'fasdent_register_submission_cpt' );

/* ═══════════════════════════════════════════════════
 * بررسی محدودیت نرخ
 * ═══════════════════════════════════════════════════ */
function fasdent_rate_limit_check( string $ip ): bool {
	$transient_key = 'fasdent_form_' . md5( $ip );
	$count         = (int) get_transient( $transient_key );
	if ( $count >= 3 ) {
		return false; // بیش از حد مجاز.
	}
	set_transient( $transient_key, $count + 1, HOUR_IN_SECONDS );
	return true;
}

/* ═══════════════════════════════════════════════════
 * پردازش فرم تماس/رزرو
 * ═══════════════════════════════════════════════════ */
function fasdent_handle_form_submission(): void {
	// ۱. اعتبارسنجی Nonce.
	if ( ! isset( $_POST['fasdent_form_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['fasdent_form_nonce'] ) ), 'fasdent_form_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'اعتبارسنجی نامعتبر است.' ) );
	}

	// ۲. بررسی Honeypot — اگر پر شده = بات.
	if ( ! empty( $_POST['_hp_website'] ) ) {
		// بدون خطا — بات را گمراه می‌کنیم.
		wp_send_json_success( array( 'message' => 'درخواست شما با موفقیت ثبت شد.' ) );
	}

	// ۳. محدودیت نرخ بر اساس IP.
	$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	if ( ! fasdent_rate_limit_check( $ip ) ) {
		wp_send_json_error( array( 'message' => 'تعداد درخواست‌ها از حد مجاز تجاوز کرده. لطفاً بعداً دوباره امتحان کنید.' ) );
	}

	// ۴. دریافت و پاکسازی داده‌ها.
	$name    = isset( $_POST['name'] )    ? sanitize_text_field( wp_unslash( $_POST['name'] ) )    : '';
	$phone   = isset( $_POST['phone'] )   ? sanitize_text_field( wp_unslash( $_POST['phone'] ) )   : '';
	$email   = isset( $_POST['email'] )   ? sanitize_email( wp_unslash( $_POST['email'] ) )        : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$type    = isset( $_POST['form_type'] ) ? sanitize_text_field( wp_unslash( $_POST['form_type'] ) ) : 'contact';

	// ۵. اعتبارسنجی فیلدهای الزامی.
	if ( '' === $name || '' === $phone ) {
		wp_send_json_error( array( 'message' => 'نام و شماره تماس الزامی است.' ) );
	}
	// بررسی فرمت موبایل ایران.
	if ( ! preg_match( '/^(\+98|0)?9\d{9}$/', preg_replace( '/\s+/', '', $phone ) ) ) {
		wp_send_json_error( array( 'message' => 'شماره تماس معتبر نیست. مثال: ۰۹۱۲۳۴۵۶۷۸۹' ) );
	}

	// ۶. ذخیره در CPT (BUG-008 FIX).
	$label      = 'appointment' === $type ? 'رزرو نوبت' : 'تماس';
	$post_title = $label . ' — ' . $name . ' — ' . wp_date( 'Y-m-d H:i' );
	$post_id    = wp_insert_post( array(
		'post_type'   => 'fasdent_submission',
		'post_title'  => $post_title,
		'post_status' => 'publish',
		'post_content' => wp_kses_post( "نام: {$name}\nتلفن: {$phone}\nایمیل: {$email}\nپیام: {$message}" ),
	) );
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_submission_name',  $name );
		update_post_meta( $post_id, '_submission_phone', $phone );
		update_post_meta( $post_id, '_submission_email', $email );
		update_post_meta( $post_id, '_submission_type',  $type );
		update_post_meta( $post_id, '_submission_ip',    $ip );
	}

	// ۷. ارسال ایمیل اطلاع‌رسانی.
	$to      = get_theme_mod( 'fasdent_email', 'info@fasdent.ir' );
	$subject = 'درخواست ' . $label . ' از سایت فس‌دنت — ' . $name;
	$body    = "نام: {$name}\nتلفن: {$phone}\nایمیل: {$email}\nنوع: {$label}\nپیام:\n{$message}\n\nIP: {$ip}\nزمان: " . wp_date( 'Y-m-d H:i:s' );
	wp_mail( $to, $subject, $body );

	wp_send_json_success( array( 'message' => 'درخواست شما با موفقیت ثبت شد. در اسرع وقت با شما تماس می‌گیریم.' ) );
}
add_action( 'wp_ajax_fasdent_handle_form',        'fasdent_handle_form_submission' );
add_action( 'wp_ajax_nopriv_fasdent_handle_form', 'fasdent_handle_form_submission' );

/* ═══════════════════════════════════════════════════
 * حذف فیلد ایمیل از نظرات وردپرس
 * ═══════════════════════════════════════════════════ */

/**
 * حذف فیلد ایمیل از فرم نظرات.
 *
 * @param array $fields فیلدهای پیش‌فرض.
 * @return array
 */
function fasdent_remove_comment_email_field( array $fields ): array {
	unset( $fields['email'] );
	unset( $fields['url'] );
	return $fields;
}
add_filter( 'comment_form_default_fields', 'fasdent_remove_comment_email_field' );

/**
 * تنظیم ایمیل پیش‌فرض برای نظراتی که بدون ایمیل ارسال شده‌اند.
 *
 * @param array $commentdata داده نظر.
 * @return array
 */
function fasdent_comment_optional_email( array $commentdata ): array {
	if ( empty( $commentdata['comment_author_email'] ) ) {
		$commentdata['comment_author_email'] = 'anonymous@fasdent.ir';
	}
	return $commentdata;
}
add_filter( 'preprocess_comment', 'fasdent_comment_optional_email' );

/**
 * بررسی Honeypot در نظرات.
 *
 * @param array $commentdata داده نظر.
 * @return array
 */
function fasdent_comment_honeypot_check( array $commentdata ): array {
	if ( ! empty( $_POST['comment_hp_email'] ) ) {
		wp_die( esc_html__( 'ارسال نظر مجاز نیست.', 'fasdent' ), 403 );
	}
	return $commentdata;
}
add_filter( 'preprocess_comment', 'fasdent_comment_honeypot_check', 5 );

/**
 * محدودیت نرخ نظرات (۳ نظر در ساعت از یک IP).
 *
 * @param array $commentdata داده نظر.
 * @return array
 */
function fasdent_comment_rate_limit( array $commentdata ): array {
	$ip  = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$key = 'fasdent_comment_' . md5( $ip );
	if ( (int) get_transient( $key ) >= 3 ) {
		wp_die( esc_html__( 'تعداد ارسال نظر از حد مجاز تجاوز کرده. لطفاً بعداً امتحان کنید.', 'fasdent' ), 429 );
	}
	set_transient( $key, (int) get_transient( $key ) + 1, HOUR_IN_SECONDS );
	return $commentdata;
}
add_filter( 'preprocess_comment', 'fasdent_comment_rate_limit', 10 );

/**
 * بستن خودکار نظرات برای پست‌های قدیمی‌تر از ۳۰ روز.
 *
 * @param bool    $open     آیا نظرات باز است.
 * @param int     $post_id  شناسه پست.
 * @return bool
 */
function fasdent_auto_close_old_comments( bool $open, int $post_id ): bool {
	if ( ! $open ) {
		return $open;
	}
	$post = get_post( $post_id );
	if ( ! $post ) {
		return $open;
	}
	$age = time() - strtotime( $post->post_date );
	return $age < ( 30 * DAY_IN_SECONDS ) ? $open : false;
}
add_filter( 'comments_open', 'fasdent_auto_close_old_comments', 10, 2 );
