<?php get_header(); ?>
<section class="section">
	<div class="container">
		<h1><?php the_title(); ?></h1>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<div class="card">
				<?php the_content(); ?>
			</div>
		<?php endwhile; endif; ?>
	</div>
</section>
<?php get_footer(); ?>