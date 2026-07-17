<?php
/**
 * Template part: Testimonial card.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rating     = function_exists( 'fasdent_field' ) ? (float) fasdent_field( 'rating' ) : 5.0;
$service_id = function_exists( 'fasdent_field' ) ? absint( fasdent_field( 'related_service' ) ) : 0;
$quote      = wp_trim_words( wp_strip_all_tags( get_the_content() ), 48 );
?>
<article <?php post_class( 'testimonial-card card' ); ?>>
	<span class="testimonial-card__quote-icon" aria-hidden="true"><i class="fa-duotone fa-solid fa-quote-right"></i></span>
	<?php get_template_part( 'template-parts/rating-display', null, array( 'rating' => $rating ) ); ?>
	<blockquote class="testimonial-text"><p><?php echo esc_html( $quote ); ?></p></blockquote>
	<footer class="testimonial-footer">
		<span class="testimonial-avatar" aria-hidden="true"><i class="fa-duotone fa-solid fa-user"></i></span>
		<div>
			<strong class="testimonial-author"><?php the_title(); ?></strong>
			<?php if ( $service_id ) : ?>
				<span class="testimonial-service"><a href="<?php echo esc_url( get_permalink( $service_id ) ); ?>"><?php echo esc_html( get_the_title( $service_id ) ); ?></a></span>
			<?php endif; ?>
		</div>
	</footer>
</article>
