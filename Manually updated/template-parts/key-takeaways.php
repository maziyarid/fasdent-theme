<?php
/**
 * Template part: Key takeaways.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$takeaways = function_exists( 'fasdent_field' ) ? fasdent_field( 'key_takeaways' ) : array();
if ( ! is_array( $takeaways ) || empty( $takeaways ) ) {
	return;
}

$heading_id = wp_unique_id( 'key-takeaways-' );
?>
<section class="key-takeaways card" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
	<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="key-takeaways__title">
		<i class="fa-duotone fa-solid fa-clipboard-check" aria-hidden="true"></i>
		<?php esc_html_e( 'نکات کلیدی این مطلب', 'fasdent' ); ?>
	</h2>
	<ul class="key-takeaways__list">
		<?php foreach ( $takeaways as $item ) :
			$icon = isset( $item['icon'] ) ? (string) $item['icon'] : 'fa-duotone fa-solid fa-circle-check';
			$text = isset( $item['text'] ) ? trim( (string) $item['text'] ) : '';
			if ( '' === $text ) { continue; }
		?>
			<li><i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i><span><?php echo esc_html( $text ); ?></span></li>
		<?php endforeach; ?>
	</ul>
</section>
