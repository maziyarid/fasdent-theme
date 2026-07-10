<?php
/**
 * Template part: service card
 *
 * @package Fasdent
 */
?>
<article class="service-card">
	<h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
	<?php the_excerpt(); ?>
</article>
