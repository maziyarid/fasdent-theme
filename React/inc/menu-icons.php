<?php
/**
 * Font Awesome 7+ menu icons and editable menu-item icon fields.
 * Requires a licensed Font Awesome Pro Kit or self-hosted Pro assets.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fasdent_sanitize_icon_classes( $value ) {
	$tokens = preg_split( '/\s+/', (string) $value );
	$tokens = array_filter( array_map( 'sanitize_html_class', $tokens ) );
	return implode( ' ', array_unique( $tokens ) );
}

function fasdent_default_menu_icon( $title, $url = '' ) {
	$raw_haystack = wp_strip_all_tags( (string) $title ) . ' ' . (string) $url;
	$haystack     = function_exists( 'mb_strtolower' ) ? mb_strtolower( $raw_haystack ) : strtolower( $raw_haystack );
	$map = array(
		'خانه|home'                         => 'fa-duotone fa-solid fa-house',
		'درباره|about'                      => 'fa-duotone fa-solid fa-circle-info',
		'خدمات|service|درمان'               => 'fa-duotone fa-solid fa-tooth',
		'پزشک|دکتر|doctor|team'             => 'fa-duotone fa-solid fa-user-doctor',
		'وبلاگ|مقاله|blog|article'           => 'fa-duotone fa-solid fa-newspaper',
		'نمونه|گالری|gallery|before|after'   => 'fa-duotone fa-solid fa-images',
		'تماس|contact'                      => 'fa-duotone fa-solid fa-phone',
		'نوبت|رزرو|booking|appointment'      => 'fa-duotone fa-solid fa-calendar-check',
		'سوال|پرسش|faq'                     => 'fa-duotone fa-solid fa-circle-question',
		'قیمت|هزینه|price'                  => 'fa-duotone fa-solid fa-tags',
		'موقعیت|آدرس|location'              => 'fa-duotone fa-solid fa-location-dot',
	);

	foreach ( $map as $pattern => $icon ) {
		if ( preg_match( '/(' . $pattern . ')/u', $haystack ) ) {
			return $icon;
		}
	}

	return 'fa-duotone fa-solid fa-circle-dot';
}

function fasdent_add_menu_item_icon( $title, $item, $args, $depth ) {
	if ( is_admin() || false !== strpos( $title, 'menu-item-icon' ) ) {
		return $title;
	}

	$icon = get_post_meta( $item->ID, '_fasdent_menu_icon', true );
	$icon = $icon ? fasdent_sanitize_icon_classes( $icon ) : fasdent_default_menu_icon( $title, $item->url );

	return sprintf(
		'<i class="%1$s menu-item-icon" aria-hidden="true"></i><span class="menu-item-label">%2$s</span>',
		esc_attr( $icon ),
		$title
	);
}
add_filter( 'nav_menu_item_title', 'fasdent_add_menu_item_icon', 10, 4 );

function fasdent_menu_icon_field( $item_id, $menu_item ) {
	$value = get_post_meta( $item_id, '_fasdent_menu_icon', true );
	?>
	<p class="description description-wide fasdent-menu-icon-field">
		<label for="edit-menu-item-fasdent-icon-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'کلاس آیکن Font Awesome', 'fasdent' ); ?><br>
			<input type="text" class="widefat code" id="edit-menu-item-fasdent-icon-<?php echo esc_attr( $item_id ); ?>" name="menu-item-fasdent-icon[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="fa-duotone fa-solid fa-tooth">
			<span class="description"><?php esc_html_e( 'خالی بگذارید تا آیکن مناسب به‌صورت خودکار انتخاب شود.', 'fasdent' ); ?></span>
		</label>
	</p>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'fasdent_menu_icon_field', 10, 2 );

function fasdent_save_menu_icon_field( $menu_id, $menu_item_db_id ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$value = isset( $_POST['menu-item-fasdent-icon'][ $menu_item_db_id ] )
		? fasdent_sanitize_icon_classes( wp_unslash( $_POST['menu-item-fasdent-icon'][ $menu_item_db_id ] ) )
		: '';

	if ( $value ) {
		update_post_meta( $menu_item_db_id, '_fasdent_menu_icon', $value );
	} else {
		delete_post_meta( $menu_item_db_id, '_fasdent_menu_icon' );
	}
}
add_action( 'wp_update_nav_menu_item', 'fasdent_save_menu_icon_field', 10, 2 );
