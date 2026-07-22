<?php
/**
 * Template part: Service card.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icon  = function_exists( 'fasdent_field' ) ? fasdent_field( 'service_icon' ) : '';
$price = function_exists( 'fasdent_field' ) ? fasdent_field( 'service_price' ) : '';
$dur   = function_exists( 'fasdent_field' ) ? fasdent_field( 'service_duration' ) : '';
$icon  = $icon ?: 'fa-duotone fa-solid fa-tooth';
?>
<article <?php post_class( 'service-card card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="service-card__media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده خدمت %s', 'fasdent' ), get_the_title() ) ); ?>">
			<?php the_post_thumbnail( 'fasdent-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
			<span class="service-card__media-icon" aria-hidden="true"><i class="fa-duotone fa-solid fa-arrow-up-left-from-circle"></i></span>
		</a>
	<?php endif; ?>
	<div class="service-card__body">
		<span class="service-card__icon" aria-hidden="true"><i class="<?php echo esc_attr( $icon ); ?>"></i></span>
		<h3 class="service-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="service-card__excerpt"><?php the_excerpt(); ?></div>
		<?php if ( $price || $dur ) : ?>
			<div class="service-card__meta">
				<?php if ( $price ) : ?><span class="service-price"><i class="fa-duotone fa-solid fa-tag" aria-hidden="true"></i> <?php echo esc_html( $price ); ?></span><?php endif; ?>
				<?php if ( $dur ) : ?><span class="service-duration"><i class="fa-duotone fa-solid fa-clock" aria-hidden="true"></i> <?php echo esc_html( $dur ); ?></span><?php endif; ?>
			</div>
		<?php endif; ?>
		<a href="<?php the_permalink(); ?>" class="btn btn-primary service-card__button"><?php esc_html_e( 'اطلاعات بیشتر', 'fasdent' ); ?> <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
	</div>
</article>
