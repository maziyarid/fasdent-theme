<?php
/**
 * Template part: Before and after gallery card.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'before-after-card card' ); ?>>
	<a class="before-after-card__media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده نمونه درمان %s', 'fasdent' ), get_the_title() ) ); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'fasdent-gallery', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
		<?php else : ?>
			<span class="before-after-card__placeholder" aria-hidden="true"><i class="fa-duotone fa-solid fa-images"></i></span>
		<?php endif; ?>
		<span class="before-after-card__overlay" aria-hidden="true"><i class="fa-duotone fa-solid fa-magnifying-glass-plus"></i></span>
	</a>
	<div class="before-after-card__body">
		<p class="before-after-card__eyebrow"><i class="fa-duotone fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i> <?php esc_html_e( 'قبل و بعد درمان', 'fasdent' ); ?></p>
		<h3 class="before-after-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	</div>
</article>
