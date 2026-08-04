<?php
/**
 * Before/After comparison slider.
 *
 * Expects $args['post_id'] or uses current post.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$data    = function_exists( 'fasdent_ba_get_images' ) ? fasdent_ba_get_images( $post_id ) : array( 'before' => 0, 'after' => 0, 'label' => '' );

if ( ! $data['before'] || ! $data['after'] ) {
	return;
}

$before_src = wp_get_attachment_image_url( $data['before'], 'fasdent-gallery' ) ?: wp_get_attachment_image_url( $data['before'], 'large' );
$after_src  = wp_get_attachment_image_url( $data['after'], 'fasdent-gallery' ) ?: wp_get_attachment_image_url( $data['after'], 'large' );
$before_alt = get_post_meta( $data['before'], '_wp_attachment_image_alt', true ) ?: __( 'قبل از درمان', 'fasdent' );
$after_alt  = get_post_meta( $data['after'], '_wp_attachment_image_alt', true ) ?: __( 'بعد از درمان', 'fasdent' );
$label      = $data['label'] ?: get_the_title( $post_id );
$uid        = 'ba-' . $post_id;
?>
<figure class="ba-slider" id="<?php echo esc_attr( $uid ); ?>" data-ba-slider role="img" aria-label="<?php echo esc_attr( sprintf( __( 'مقایسه قبل و بعد: %s', 'fasdent' ), $label ) ); ?>">
	<div class="ba-slider__viewport">
		<img class="ba-slider__img ba-slider__img--before" src="<?php echo esc_url( $before_src ); ?>" alt="<?php echo esc_attr( $before_alt ); ?>" loading="lazy" decoding="async" draggable="false">
		<div class="ba-slider__after-wrap" style="clip-path: inset(0 50% 0 0);">
			<img class="ba-slider__img ba-slider__img--after" src="<?php echo esc_url( $after_src ); ?>" alt="<?php echo esc_attr( $after_alt ); ?>" loading="lazy" decoding="async" draggable="false">
		</div>
		<div class="ba-slider__handle" style="left:50%;" role="slider" tabindex="0" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50" aria-label="<?php esc_attr_e( 'مقایسه قبل و بعد', 'fasdent' ); ?>">
			<span class="ba-slider__handle-line" aria-hidden="true"></span>
			<span class="ba-slider__handle-btn" aria-hidden="true"><i class="fa-solid fa-arrows-left-right"></i></span>
		</div>
		<span class="ba-slider__badge ba-slider__badge--before"><?php esc_html_e( 'قبل', 'fasdent' ); ?></span>
		<span class="ba-slider__badge ba-slider__badge--after"><?php esc_html_e( 'بعد', 'fasdent' ); ?></span>
	</div>
	<?php if ( $label ) : ?>
		<figcaption class="ba-slider__caption"><?php echo esc_html( $label ); ?></figcaption>
	<?php endif; ?>
</figure>
