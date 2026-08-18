<?php
/** Generic page template. */
get_header();
?>
<main id="main-content" class="content-area">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php $title_html = the_title( '', '', false ); alipasandi_inner_hero( 'header', 'کلینیک دندانپزشکی', $title_html ); ?>
		<section class="section section-bone"><article <?php post_class( 'narrow-container entry-card' ); ?>><div class="entry-content"><?php the_content(); ?><?php wp_link_pages(); ?></div></article></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
