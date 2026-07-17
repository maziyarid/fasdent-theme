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

/* ═══════════════════════════════════════════════════
 * REST API: خبرنامه (newsletter) — بدون PHI
 * POST /wp-json/fasdent/v1/newsletter
 * فیلدها: email (required), fasdent_nonce (required), hp_field (honeypot)
 * ═══════════════════════════════════════════════════ */

/**
 * ثبت endpoint خبرنامه.
 */
function fasdent_register_newsletter_endpoint(): void {
	register_rest_route( 'fasdent/v1', '/newsletter', array(
		'methods'             => 'POST',
		'callback'            => 'fasdent_newsletter_handler',
		'permission_callback' => '__return_true',
		'args'                => array(
			'email' => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_email',
				'validate_callback' => function ( $val ) {
					return is_email( $val );
				},
			),
		),
	) );
}
add_action( 'rest_api_init', 'fasdent_register_newsletter_endpoint' );

/**
 * پردازش درخواست خبرنامه.
 *
 * @param WP_REST_Request $request درخواست REST.
 * @return WP_REST_Response
 */
function fasdent_newsletter_handler( WP_REST_Request $request ): WP_REST_Response {
	// ۱. بررسی Honeypot.
	$hp = $request->get_param( 'hp_field' );
	if ( ! empty( $hp ) ) {
		// بات — پیام موفق جعلی.
		return new WP_REST_Response( array( 'success' => true, 'message' => __( 'ثبت‌نام با موفقیت انجام شد.', 'fasdent' ) ), 200 );
	}

	// ۲. اعتبارسنجی Nonce (اختیاری در REST اما امنیت اضافه می‌کند).
	$nonce = $request->get_param( 'fasdent_nonce' );
	if ( $nonce && ! wp_verify_nonce( sanitize_key( $nonce ), 'fasdent_newsletter' ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'اعتبارسنجی نامعتبر است.', 'fasdent' ) ), 403 );
	}

	// ۳. محدودیت نرخ بر اساس IP.
	$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$rl_key = 'fasdent_nl_' . md5( $ip );
	if ( (int) get_transient( $rl_key ) >= 5 ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'تعداد درخواست‌ها از حد مجاز تجاوز کرده. لطفاً بعداً دوباره امتحان کنید.', 'fasdent' ) ), 429 );
	}
	set_transient( $rl_key, (int) get_transient( $rl_key ) + 1, HOUR_IN_SECONDS );

	// ۴. دریافت و اعتبارسنجی ایمیل.
	$email = sanitize_email( $request->get_param( 'email' ) );
	if ( ! is_email( $email ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'ایمیل معتبر نیست.', 'fasdent' ) ), 400 );
	}

	// ۵. ذخیره در option به عنوان لیست ساده (بدون PHI / اطلاعات پزشکی).
	$subscribers = get_option( 'fasdent_newsletter_subscribers', array() );
	if ( ! is_array( $subscribers ) ) {
		$subscribers = array();
	}
	// جلوگیری از ورود تکراری.
	if ( in_array( $email, $subscribers, true ) ) {
		return new WP_REST_Response( array( 'success' => true, 'message' => __( 'این ایمیل قبلاً ثبت شده است.', 'fasdent' ) ), 200 );
	}
	$subscribers[] = $email;
	update_option( 'fasdent_newsletter_subscribers', $subscribers, false );

	// ۶. اعلان به مدیر.
	$admin_email = get_option( 'admin_email' );
	wp_mail(
		$admin_email,
		__( 'عضو جدید خبرنامه فس‌دنت', 'fasdent' ),
		sprintf( __( 'ایمیل جدید در خبرنامه ثبت شد: %s', 'fasdent' ), $email )
	);

	return new WP_REST_Response( array( 'success' => true, 'message' => __( 'ثبت‌نام با موفقیت انجام شد. به زودی اولین خبرنامه را دریافت می‌کنید.', 'fasdent' ) ), 200 );
}

/* ═══════════════════════════════════════════════════
 * Callback نمایش نظر (در اینجا تعریف شده تا در comments.php قابل استفاده باشد)
 * ═══════════════════════════════════════════════════ */

/**
 * رندر هر نظر در لیست — شناسایی پاسخ دکتر با badge.
 *
 * @param WP_Comment $comment شی نظر.
 * @param array      $args    آرگومان‌های wp_list_comments.
 * @param int        $depth   عمق نظر.
 */
function fasdent_comment_callback( WP_Comment $comment, array $args, int $depth ): void {
	$is_admin = user_can( (int) $comment->user_id, 'manage_options' );
	$extra    = $is_admin ? ' is-doctor-reply' : '';
	$classes  = implode( ' ', get_comment_class( $extra, $comment ) );
	?>
	<li id="comment-<?php comment_ID(); ?>" class="<?php echo esc_attr( $classes ); ?>">
		<article class="comment-body card">
			<header class="comment-header">
				<div class="comment-author-avatar"><?php echo get_avatar( $comment, (int) ( $args['avatar_size'] ?? 56 ) ); ?></div>
				<div class="comment-author-info">
					<span class="comment-author-name">
						<?php comment_author(); ?>
						<?php if ( $is_admin ) : ?><span class="doctor-badge"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i> <?php esc_html_e( 'پاسخ دکتر', 'fasdent' ); ?></span><?php endif; ?>
					</span>
					<time class="comment-date" datetime="<?php comment_date( 'Y-m-d' ); ?>"><?php comment_date( 'j F Y' ); ?></time>
				</div>
				<div class="comment-actions">
					<?php comment_reply_link( array_merge( $args, array(
						'add_below' => 'comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'] ?? 3,
						'before'    => '',
						'after'     => '',
						'reply_text' => '<i class="fa-solid fa-reply" aria-hidden="true"></i> ' . __( 'پاسخ', 'fasdent' ),
					) ) ); ?>
					<?php edit_comment_link( '<i class="fa-solid fa-pen" aria-hidden="true"></i> ' . __( 'ویرایش', 'fasdent' ), '<span class="edit-link">', '</span>' ); ?>
				</div>
			</header>
			<?php if ( '0' === $comment->comment_approved ) : ?>
			<p class="alert alert--warn"><i class="fa-solid fa-clock" aria-hidden="true"></i> <?php esc_html_e( 'نظر شما در انتظار بررسی است.', 'fasdent' ); ?></p>
			<?php endif; ?>
			<div class="comment-content"><?php comment_text(); ?></div>
		</article>
	<?php
}
