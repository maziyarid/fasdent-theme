<?php
/** Post card used by archives. */
?>
<article <?php post_class( 'post-card' ); ?>>
	<a href="<?php the_permalink(); ?>">
		<div class="post-card-image">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<span style="display:grid;height:100%;place-items:center;color:var(--color-caramel-accent)"><?php echo alipasandi_icon( 'tooth', 56 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<?php endif; ?>
		</div>
		<div class="post-card-copy">
			<h2><?php the_title(); ?></h2>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
			<div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div>
		</div>
	</a>
</article>

