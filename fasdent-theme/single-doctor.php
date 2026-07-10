<?php get_header(); ?>
<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article class="card">
				<h1><?php the_title(); ?></h1>
				<?php if ( has_post_thumbnail() ) : ?>
					<div style="max-width:320px;margin-bottom:1rem;">
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
				<?php endif; ?>
				<?php the_content(); ?>
			</article>
		<?php endwhile; endif; ?>
	</div>
</section>
<?php get_footer(); ?>