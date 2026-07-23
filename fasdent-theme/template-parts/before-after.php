<?php
/**
 * Before/After card for grids.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = function_exists( 'fasdent_ba_get_images' ) ? fasdent_ba_get_images() : array( 'before' => 0, 'after' => 0, 'label' => '' );
$thumb_id = $data['after'] ?: ( $data['before'] ?: get_post_thumbnail_id() );
?>
<article <?php post_class( 'ba-card card' ); ?>>
	<a class="ba-card__link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده نمونه %s', 'fasdent' ), get_the_title() ) ); ?>">
		<div class="ba-card__media">
			<?php if ( $thumb_id ) : ?>
				<?php echo wp_get_attachment_image( $thumb_id, 'fasdent-gallery', false, array( 'loading' => 'lazy', 'class' => 'ba-card__img' ) ); ?>
			<?php else : ?>
				<span class="ba-card__placeholder" aria-hidden="true"><i class="fa-solid fa-images"></i></span>
			<?php endif; ?>
			<span class="ba-card__overlay" aria-hidden="true">
				<span class="ba-card__tag"><?php esc_html_e( 'قبل / بعد', 'fasdent' ); ?></span>
			</span>
		</div>
		<div class="ba-card__body">
			<?php if ( ! empty( $data['label'] ) ) : ?>
				<p class="ba-card__eyebrow"><?php echo esc_html( $data['label'] ); ?></p>
			<?php endif; ?>
			<h3 class="ba-card__title"><?php the_title(); ?></h3>
		</div>
	</a>
</article>
