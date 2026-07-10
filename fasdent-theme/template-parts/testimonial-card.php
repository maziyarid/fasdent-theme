<?php
/**
 * Template part: testimonial card
 *
 * @package Fasdent
 */
?>
<article class="testimonial-card">
	<p>“<?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?>”</p>
	<strong><?php the_title(); ?></strong>
</article>
