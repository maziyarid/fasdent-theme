<?php
/** Generic page template. */
get_header();
?>
<main id="main-content" class="content-area">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="inner-hero"><div class="narrow-container section-heading" style="padding-block:64px;position:relative"><span class="eyebrow">کلینیک دندانپزشکی</span><h1><?php the_title(); ?></h1></div></header>
		<section class="section section-bone"><article <?php post_class( 'narrow-container entry-card' ); ?>><div class="entry-content"><?php the_content(); ?><?php wp_link_pages(); ?></div></article></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
