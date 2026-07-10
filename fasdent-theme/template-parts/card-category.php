<?php
/**
 * Template part: category card
 *
 * @package Fasdent
 */
?>
<article class="category-card">
	<h3><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></h3>
	<p><?php echo esc_html( $term->description ?: 'خدمات تخصصی در این دسته' ); ?></p>
</article>
