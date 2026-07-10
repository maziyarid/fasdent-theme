<?php
/**
 * Template part: before/after gallery entry
 *
 * @package Fasdent
 */
?>
<div class="card">
	<?php if ( has_post_thumbnail() ) : ?>
		<?php the_post_thumbnail( 'fasdent-gallery' ); ?>
	<?php endif; ?>
	<h3><?php the_title(); ?></h3>
</div>
