<?php
/**
 * صفحه عمومی — Fasdent
 * @package Fasdent
 */
get_header();
?>
<section class="section">
	<div class="container" style="max-width:800px;">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article class="page-content">
			<header class="page-header" style="margin-bottom:2rem;">
				<h1><?php the_title(); ?></h1>
				<?php if ( has_post_thumbnail() ) : ?>
				<div style="margin-top:1.25rem;border-radius:var(--radius);overflow:hidden;"><?php the_post_thumbnail( 'fasdent-hero', array( 'loading' => 'eager', 'style' => 'width:100%;height:auto;' ) ); ?></div>
				<?php endif; ?>
			</header>
			<div class="prose card" style="padding:2rem;">
				<?php the_content(); ?>
			</div>
		</article>
		<?php endwhile; endif; ?>
	</div>
</section>
<?php get_footer(); ?>