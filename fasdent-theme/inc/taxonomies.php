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
 * فیلدهای اضافه ترم: آیکون Font Awesome + متن Hero.
 */
function fasdent_term_fields_form( $term = null ): void {
	$icon = $term instanceof WP_Term ? get_term_meta( $term->term_id, 'fasdent_icon', true ) : '';
	wp_nonce_field( 'fasdent_term_meta', 'fasdent_term_meta_nonce' );
	?>
	<tr class="form-field">
		<th scope="row"><label for="fasdent_icon"><?php esc_html_e( 'آیکون Font Awesome', 'fasdent' ); ?></label></th>
		<td>
			<input type="text" name="fasdent_icon" id="fasdent_icon" dir="ltr" value="<?php echo esc_attr( $icon ); ?>" placeholder="fa-solid fa-tooth">
			<p class="description"><?php esc_html_e( 'مثال: fa-solid fa-tooth — لیست آیکون‌ها در fontawesome.com', 'fasdent' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'service_category_edit_form_fields', 'fasdent_term_fields_form' );

/**
 * ذخیره متای ترم (با اعتبارسنجی Nonce و Sanitize).
 *
 * @param int $term_id شناسه ترم.
 */
function fasdent_save_term_fields( int $term_id ): void {
	if ( ! isset( $_POST['fasdent_term_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fasdent_term_meta_nonce'] ), 'fasdent_term_meta' ) ) {
		return;
	}
	if ( isset( $_POST['fasdent_icon'] ) ) {
		update_term_meta( $term_id, 'fasdent_icon', sanitize_text_field( wp_unslash( $_POST['fasdent_icon'] ) ) );
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
	$term_id = $term instanceof WP_Term ? $term->term_id : (int) $term;
	$slug    = $term instanceof WP_Term ? $term->slug : ( get_term( $term_id, 'service_category' )->slug ?? '' );

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
