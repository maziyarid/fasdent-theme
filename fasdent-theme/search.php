<?php get_header(); ?>
<section class="section">
	<div class="container">
		<h1>نتایج جستجو</h1>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article class="card" style="margin-bottom:1rem;">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; else : ?>
			<p>موردی برای این عبارت یافت نشد.</p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>