<?php
/**
 * Taxonomies — Fasdent
 * service_category (دسته خدمات) — هرارشیک با rewrite: /services/{slug}/
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ثبت طبقه‌بندی دسته خدمات.
 */
function fasdent_register_taxonomies(): void {
	register_taxonomy( 'service_category', array( 'service' ), array(
		'labels'            => array(
			'name'          => __( 'دسته‌های خدمات', 'fasdent' ),
			'singular_name' => __( 'دسته خدمات', 'fasdent' ),
			'add_new_item'  => __( 'افزودن دسته جدید', 'fasdent' ),
			'edit_item'     => __( 'ویرایش دسته', 'fasdent' ),
			'search_items'  => __( 'جستجوی دسته‌ها', 'fasdent' ),
		),
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array(
			'slug'         => 'services',
			'with_front'   => false,
			'hierarchical' => true,
		),
	) );
}
add_action( 'init', 'fasdent_register_taxonomies', 9 ); // قبل از CPT.

/**
 * رندر فیلد آیکون Font Awesome برای دسته خدمات.
 *
 * @param string $icon      مقدار فعلی.
 * @param bool   $table_row آیا خروجی به‌صورت ردیف جدول ادمین باشد.
 */
function fasdent_render_term_icon_field( string $icon = '', bool $table_row = true ): void {
	wp_nonce_field( 'fasdent_term_meta', 'fasdent_term_meta_nonce' );

	if ( $table_row ) {
		?>
		<tr class="form-field">
			<th scope="row"><label for="fasdent_icon"><?php esc_html_e( 'آیکون Font Awesome', 'fasdent' ); ?></label></th>
			<td>
				<input type="text" name="fasdent_icon" id="fasdent_icon" dir="ltr" value="<?php echo esc_attr( $icon ); ?>" placeholder="fa-solid fa-tooth">
				<p class="description"><?php esc_html_e( 'مثال: fa-solid fa-tooth — لیست آیکون‌ها در fontawesome.com', 'fasdent' ); ?></p>
			</td>
		</tr>
		<?php
		return;
	}
	?>
	<div class="form-field term-fasdent-icon-wrap">
		<label for="fasdent_icon"><?php esc_html_e( 'آیکون Font Awesome', 'fasdent' ); ?></label>
		<input type="text" name="fasdent_icon" id="fasdent_icon" dir="ltr" value="<?php echo esc_attr( $icon ); ?>" placeholder="fa-solid fa-tooth">
		<p><?php esc_html_e( 'مثال: fa-solid fa-tooth — لیست آیکون‌ها در fontawesome.com', 'fasdent' ); ?></p>
	</div>
	<?php
}

/**
 * فیلد آیکون برای فرم ایجاد دسته جدید.
 */
function fasdent_term_fields_add_form(): void {
	fasdent_render_term_icon_field( '', false );
}
add_action( 'service_category_add_form_fields', 'fasdent_term_fields_add_form' );

/**
 * فیلد آیکون برای فرم ویرایش دسته.
 *
 * @param WP_Term $term شیء ترم.
 */
function fasdent_term_fields_edit_form( WP_Term $term ): void {
	$icon = (string) get_term_meta( $term->term_id, 'fasdent_icon', true );
	fasdent_render_term_icon_field( $icon, true );
}
add_action( 'service_category_edit_form_fields', 'fasdent_term_fields_edit_form' );

/**
 * ذخیره متای ترم (با اعتبارسنجی Nonce و Sanitize).
 *
 * @param int $term_id شناسه ترم.
 */
function fasdent_save_term_fields( int $term_id ): void {
	if ( ! isset( $_POST['fasdent_term_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['fasdent_term_meta_nonce'] ) ), 'fasdent_term_meta' ) ) {
		return;
	}
	if ( isset( $_POST['fasdent_icon'] ) ) {
		$icon = sanitize_text_field( wp_unslash( $_POST['fasdent_icon'] ) );
		if ( '' === $icon ) {
			delete_term_meta( $term_id, 'fasdent_icon' );
		} else {
			update_term_meta( $term_id, 'fasdent_icon', $icon );
		}
	}
}
add_action( 'edited_service_category', 'fasdent_save_term_fields' );
add_action( 'created_service_category', 'fasdent_save_term_fields' );

/**
 * دریافت کلاس آیکون FA برای یک ترم دسته خدمات (با پیش‌فرض معنادار).
 *
 * @param WP_Term|int $term ترم یا شناسه.
 * @return string
 */
function fasdent_category_icon( $term ): string {
	$term_id   = $term instanceof WP_Term ? $term->term_id : (int) $term;
	$term_obj  = $term instanceof WP_Term ? $term : get_term( $term_id, 'service_category' );
	$slug      = $term_obj instanceof WP_Term ? $term_obj->slug : '';

	$meta = get_term_meta( $term_id, 'fasdent_icon', true );
	if ( $meta ) {
		return $meta;
	}

	// آیکون‌های پیش‌فرض معنادار برای هر دسته.
	$defaults = array(
		'general-dentistry'   => 'fa-solid fa-tooth',
		'cosmetic-dentistry'  => 'fa-solid fa-face-smile',
		'dental-implant'      => 'fa-solid fa-screwdriver-wrench',
		'orthodontics'        => 'fa-solid fa-teeth',
		'oral-surgery'        => 'fa-solid fa-user-doctor',
		'endodontics'         => 'fa-solid fa-syringe',
		'periodontics'        => 'fa-solid fa-teeth-open',
		'pediatric-dentistry' => 'fa-solid fa-child',
		'prosthodontics'      => 'fa-solid fa-crown',
		'dental-emergency'    => 'fa-solid fa-truck-medical',
	);
	return $defaults[ $slug ] ?? 'fa-solid fa-tooth';
}
