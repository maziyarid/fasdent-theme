<?php
/**
 * فیلدهای ACF (PHP-registered) + فال‌بک متاباکس داخلی — Fasdent
 *
 * فیلدهای خدمت: قیمت پایه، مدت درمان، مراحل انجام (Repeater)، مزایا (Repeater)،
 * سوالات متداول (Repeater)، گالری قبل/بعد، خدمات مرتبط (Relationship)، آیکون FA.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ثبت گروه فیلدهای ACF (در صورت فعال بودن افزونه ACF).
 */
function fasdent_register_acf_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	/* ── فیلدهای خدمت ─────────────────────────────── */
	acf_add_local_field_group( array(
		'key'      => 'group_fasdent_service',
		'title'    => 'جزئیات خدمت دندانپزشکی',
		'fields'   => array(
			array(
				'key'          => 'field_fasdent_price',
				'name'         => 'service_price',
				'label'        => 'قیمت پایه (تومان)',
				'type'         => 'text',
				'instructions' => 'مثال: از ۴٬۵۰۰٬۰۰۰ تومان',
			),
			array(
				'key'   => 'field_fasdent_duration',
				'name'  => 'service_duration',
				'label' => 'مدت زمان درمان',
				'type'  => 'text',
				'instructions' => 'مثال: ۱ تا ۲ جلسه، هر جلسه ۴۵ دقیقه',
			),
			array(
				'key'        => 'field_fasdent_steps',
				'name'       => 'service_steps',
				'label'      => 'مراحل انجام درمان',
				'type'       => 'repeater',
				'layout'     => 'block',
				'button_label' => 'افزودن مرحله',
				'sub_fields' => array(
					array( 'key' => 'field_fasdent_step_title', 'name' => 'title', 'label' => 'عنوان مرحله', 'type' => 'text' ),
					array( 'key' => 'field_fasdent_step_desc', 'name' => 'description', 'label' => 'توضیح مرحله', 'type' => 'textarea', 'rows' => 3 ),
				),
			),
			array(
				'key'        => 'field_fasdent_benefits',
				'name'       => 'service_benefits',
				'label'      => 'مزایا و کاربردها',
				'type'       => 'repeater',
				'layout'     => 'table',
				'button_label' => 'افزودن مزیت',
				'sub_fields' => array(
					array( 'key' => 'field_fasdent_benefit_icon', 'name' => 'icon', 'label' => 'آیکون FA', 'type' => 'text', 'placeholder' => 'fa-solid fa-shield-heart' ),
					array( 'key' => 'field_fasdent_benefit_text', 'name' => 'text', 'label' => 'متن مزیت', 'type' => 'text' ),
				),
			),
			array(
				'key'        => 'field_fasdent_faqs',
				'name'       => 'service_faqs',
				'label'      => 'سوالات متداول',
				'type'       => 'repeater',
				'layout'     => 'block',
				'button_label' => 'افزودن سوال',
				'sub_fields' => array(
					array( 'key' => 'field_fasdent_faq_q', 'name' => 'question', 'label' => 'سوال', 'type' => 'text' ),
					array( 'key' => 'field_fasdent_faq_a', 'name' => 'answer', 'label' => 'پاسخ (جمله اول = پاسخ مستقیم برای Featured Snippet)', 'type' => 'textarea', 'rows' => 4 ),
				),
			),
			array(
				'key'           => 'field_fasdent_gallery',
				'name'          => 'service_gallery',
				'label'         => 'گالری قبل / بعد',
				'type'          => 'gallery',
				'instructions'  => 'تصاویر WebP با Alt Text توصیفی سئوشده آپلود کنید.',
				'return_format' => 'array',
			),
			array(
				'key'           => 'field_fasdent_related',
				'name'          => 'related_services',
				'label'         => 'خدمات مرتبط (Cross-Cluster)',
				'type'          => 'relationship',
				'post_type'     => array( 'service' ),
				'max'           => 3,
				'return_format' => 'id',
				'instructions'  => 'حداکثر ۳ خدمت طبق نقشه لینک‌سازی داخلی.',
			),
			array(
				'key'         => 'field_fasdent_icon',
				'name'        => 'service_icon',
				'label'       => 'آیکون Font Awesome',
				'type'        => 'text',
				'placeholder' => 'fa-solid fa-tooth',
			),
			array(
				'key'   => 'field_fasdent_aftercare',
				'name'  => 'service_aftercare',
				'label' => 'مراقبت‌های بعد از درمان',
				'type'  => 'wysiwyg',
				'media_upload' => false,
			),
			array(
				'key'          => 'field_fasdent_key_takeaways',
				'name'         => 'key_takeaways',
				'label'        => 'نکات کلیدی',
				'type'         => 'repeater',
				'layout'       => 'table',
				'max'          => 5,
				'button_label' => 'افزودن نکته',
				'sub_fields'   => array(
					array( 'key' => 'field_fasdent_kt_icon', 'name' => 'icon', 'label' => 'آیکون FA', 'type' => 'text', 'placeholder' => 'fa-solid fa-check-circle', 'default_value' => 'fa-solid fa-check-circle' ),
					array( 'key' => 'field_fasdent_kt_text', 'name' => 'text', 'label' => 'متن نکته', 'type' => 'text' ),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'service',
				),
			),
		),
	) );

	/* ── فیلد امتیاز نظر بیمار ────────────────────── */
	acf_add_local_field_group( array(
		'key'      => 'group_fasdent_testimonial',
		'title'    => 'جزئیات نظر بیمار',
		'fields'   => array(
			array(
				'key'           => 'field_fasdent_rating',
				'name'          => 'rating',
				'label'         => 'امتیاز (۱ تا ۵)',
				'type'          => 'number',
				'min'           => 1,
				'max'           => 5,
				'default_value' => 5,
			),
			array(
				'key'   => 'field_fasdent_t_service',
				'name'  => 'related_service',
				'label' => 'خدمت مرتبط',
				'type'  => 'relationship',
				'post_type' => array( 'service' ),
				'max'   => 1,
				'return_format' => 'id',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'testimonial',
				),
			),
		),
	) );

	/* ── فیلدهای پزشک ─────────────────────────────── */
	acf_add_local_field_group( array(
		'key'      => 'group_fasdent_doctor',
		'title'    => 'جزئیات پزشک',
		'fields'   => array(
			array( 'key' => 'field_fasdent_doc_title', 'name' => 'doctor_title', 'label' => 'عنوان تخصصی', 'type' => 'text', 'placeholder' => 'جراح و دندانپزشک' ),
			array( 'key' => 'field_fasdent_doc_edu', 'name' => 'doctor_education', 'label' => 'تحصیلات', 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_fasdent_doc_license', 'name' => 'doctor_license', 'label' => 'شماره نظام پزشکی', 'type' => 'text' ),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'doctor',
				),
			),
		),
	) );
}
add_action( 'acf/init', 'fasdent_register_acf_fields' );

/* ═══════════════════════════════════════════════════
 * فال‌بک متاباکس داخلی (وقتی ACF نصب نیست)
 * داده‌ها در همان کلیدهای متا ذخیره می‌شوند تا با ACF سازگار بمانند.
 * ═══════════════════════════════════════════════════ */

/**
 * ثبت متاباکس فال‌بک.
 */
function fasdent_fallback_metaboxes(): void {
	if ( function_exists( 'acf_add_local_field_group' ) ) {
		return; // ACF فعال است.
	}
	add_meta_box( 'fasdent_service_meta', __( 'جزئیات خدمت', 'fasdent' ), 'fasdent_service_metabox_html', 'service', 'normal', 'high' );
	add_meta_box( 'fasdent_testimonial_meta', __( 'امتیاز بیمار', 'fasdent' ), 'fasdent_testimonial_metabox_html', 'testimonial', 'side' );
}
add_action( 'add_meta_boxes', 'fasdent_fallback_metaboxes' );

/**
 * HTML متاباکس خدمت.
 *
 * @param WP_Post $post پست.
 */
function fasdent_service_metabox_html( WP_Post $post ): void {
	wp_nonce_field( 'fasdent_service_meta', 'fasdent_service_meta_nonce' );
	$price    = get_post_meta( $post->ID, 'service_price', true );
	$duration = get_post_meta( $post->ID, 'service_duration', true );
	$icon     = get_post_meta( $post->ID, 'service_icon', true );
	$faqs     = get_post_meta( $post->ID, 'service_faqs_json', true );
	$steps    = get_post_meta( $post->ID, 'service_steps_json', true );
	$benefits = get_post_meta( $post->ID, 'service_benefits_json', true );
	$related  = get_post_meta( $post->ID, 'related_services_slugs', true );
	?>
	<p><label><?php esc_html_e( 'قیمت پایه (تومان):', 'fasdent' ); ?><br>
		<input type="text" name="service_price" class="widefat" value="<?php echo esc_attr( $price ); ?>"></label></p>
	<p><label><?php esc_html_e( 'مدت زمان درمان:', 'fasdent' ); ?><br>
		<input type="text" name="service_duration" class="widefat" value="<?php echo esc_attr( $duration ); ?>"></label></p>
	<p><label><?php esc_html_e( 'آیکون Font Awesome:', 'fasdent' ); ?><br>
		<input type="text" name="service_icon" class="widefat" dir="ltr" value="<?php echo esc_attr( $icon ); ?>" placeholder="fa-solid fa-tooth"></label></p>
	<p><label><?php esc_html_e( 'سوالات متداول (JSON: [{"question":"..","answer":".."}]):', 'fasdent' ); ?><br>
		<textarea name="service_faqs_json" class="widefat" rows="5" dir="ltr"><?php echo esc_textarea( $faqs ); ?></textarea></label></p>
	<p><label><?php esc_html_e( 'مراحل انجام (JSON: [{"title":"..","description":".."}]):', 'fasdent' ); ?><br>
		<textarea name="service_steps_json" class="widefat" rows="5" dir="ltr"><?php echo esc_textarea( $steps ); ?></textarea></label></p>
	<p><label><?php esc_html_e( 'مزایا (JSON: [{"icon":"..","text":".."}]):', 'fasdent' ); ?><br>
		<textarea name="service_benefits_json" class="widefat" rows="5" dir="ltr"><?php echo esc_textarea( $benefits ); ?></textarea></label></p>
	<p><label><?php esc_html_e( 'اسلاگ خدمات مرتبط (با کاما، حداکثر ۳):', 'fasdent' ); ?><br>
		<input type="text" name="related_services_slugs" class="widefat" dir="ltr" value="<?php echo esc_attr( $related ); ?>" placeholder="dental-bridge,implant-prosthesis"></label></p>
	<?php
}

/**
 * HTML متاباکس نظر بیمار.
 *
 * @param WP_Post $post پست.
 */
function fasdent_testimonial_metabox_html( WP_Post $post ): void {
	wp_nonce_field( 'fasdent_service_meta', 'fasdent_service_meta_nonce' );
	$rating = get_post_meta( $post->ID, 'rating', true ) ?: 5;
	?>
	<p><label><?php esc_html_e( 'امتیاز (۱ تا ۵):', 'fasdent' ); ?>
		<input type="number" name="rating" min="1" max="5" value="<?php echo esc_attr( $rating ); ?>"></label></p>
	<?php
}

/**
 * ذخیره امن متاباکس‌ها.
 *
 * @param int $post_id شناسه پست.
 */
function fasdent_save_metaboxes( int $post_id ): void {
	if ( ! isset( $_POST['fasdent_service_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fasdent_service_meta_nonce'] ), 'fasdent_service_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = array( 'service_price', 'service_duration', 'service_icon', 'related_services_slugs', 'rating' );
	foreach ( $text_fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
	$json_fields = array( 'service_faqs_json', 'service_steps_json', 'service_benefits_json' );
	foreach ( $json_fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			$raw = sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) );
			// اعتبارسنجی JSON.
			if ( '' === $raw || null !== json_decode( $raw, true ) ) {
				update_post_meta( $post_id, $field, $raw );
			}
		}
	}
}
add_action( 'save_post', 'fasdent_save_metaboxes' );

/* ═══════════════════════════════════════════════════
 * توابع خواندن یکپارچه (ACF یا JSON فال‌بک)
 * ═══════════════════════════════════════════════════ */

/**
 * دریافت آرایه Repeater (ACF یا JSON فال‌بک).
 *
 * @param string   $acf_key  کلید فیلد ACF.
 * @param string   $json_key کلید متای JSON.
 * @param int|null $post_id  شناسه پست.
 * @return array
 */
function fasdent_repeater( string $acf_key, string $json_key, ?int $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$rows = get_field( $acf_key, $post_id );
		if ( is_array( $rows ) && $rows ) {
			return $rows;
		}
	}
	$json = get_post_meta( $post_id, $json_key, true );
	if ( $json ) {
		$decoded = json_decode( $json, true );
		if ( is_array( $decoded ) ) {
			return $decoded;
		}
	}
	return array();
}

/**
 * دریافت سوالات متداول خدمت.
 */
function fasdent_get_service_faqs( ?int $post_id = null ): array {
	return fasdent_repeater( 'service_faqs', 'service_faqs_json', $post_id );
}

/**
 * دریافت مراحل انجام خدمت.
 */
function fasdent_get_service_steps( ?int $post_id = null ): array {
	return fasdent_repeater( 'service_steps', 'service_steps_json', $post_id );
}

/**
 * دریافت مزایای خدمت.
 */
function fasdent_get_service_benefits( ?int $post_id = null ): array {
	return fasdent_repeater( 'service_benefits', 'service_benefits_json', $post_id );
}

/**
 * دریافت پست‌های خدمات مرتبط (ACF Relationship یا اسلاگ فال‌بک).
 *
 * @param int|null $post_id شناسه پست.
 * @return WP_Post[]
 */
function fasdent_get_related_services( ?int $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	// ۱) ACF Relationship.
	if ( function_exists( 'get_field' ) ) {
		$ids = get_field( 'related_services', $post_id );
		if ( is_array( $ids ) && $ids ) {
			$ids = array_map( static fn( $i ) => $i instanceof WP_Post ? $i->ID : (int) $i, $ids );
			return get_posts( array( 'post_type' => 'service', 'post__in' => $ids, 'orderby' => 'post__in', 'numberposts' => 3 ) );
		}
	}

	// ۲) فال‌بک اسلاگ‌ها (هاردکد Cross-Cluster از WXR).
	$slugs_raw = get_post_meta( $post_id, 'related_services_slugs', true );
	if ( $slugs_raw ) {
		$slugs = array_filter( array_map( 'trim', explode( ',', $slugs_raw ) ) );
		if ( $slugs ) {
			$posts = get_posts( array( 'post_type' => 'service', 'post_name__in' => $slugs, 'numberposts' => 3, 'orderby' => 'post_name__in' ) );
			if ( $posts ) {
				return $posts;
			}
		}
	}

	// ۳) فال‌بک نهایی: ۳ خدمت هم‌دسته.
	$terms = get_the_terms( $post_id, 'service_category' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		return get_posts( array(
			'post_type'    => 'service',
			'numberposts'  => 3,
			'post__not_in' => array( $post_id ),
			'tax_query'    => array(
				array(
					'taxonomy' => 'service_category',
					'field'    => 'term_id',
					'terms'    => wp_list_pluck( $terms, 'term_id' ),
				),
			),
		) );
	}
	return array();
}
