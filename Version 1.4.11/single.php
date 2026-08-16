<?php
/** Single post template. */
get_header();
?>
<main id="main-content" class="content-area">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php $categories = wp_get_post_categories( get_the_ID(), array( 'fields' => 'names' ) ); ?>
			<header class="inner-hero"><div class="narrow-container section-heading" style="padding-block:64px;position:relative"><span class="eyebrow"><?php echo esc_html( $categories ? implode( '، ', $categories ) : 'مقاله آموزشی' ); ?></span><h1><?php the_title(); ?></h1><div class="entry-meta"><span>نویسنده: <?php the_author(); ?></span><span>انتشار: <?php echo esc_html( get_the_date() ); ?></span><?php if ( get_the_modified_time( 'U' ) > get_the_time( 'U' ) ) : ?><span>آخرین بازبینی: <?php echo esc_html( get_the_modified_date() ); ?></span><?php endif; ?></div></div></header>
		<article <?php post_class( 'section section-bone' ); ?>><div class="site-container">
			<?php if ( has_post_thumbnail() ) : ?><div class="feature-image" style="max-width:980px;margin:0 auto 36px"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
			<div class="entry-content"><?php the_content(); ?><?php wp_link_pages(); ?></div>
			<div class="medical-note narrow-container"><?php echo alipasandi_icon( 'info', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong>یادآوری پزشکی</strong><p>این مطلب عمومی است و جایگزین معاینه، تشخیص یا طرح درمان اختصاصی نیست.</p></div></div>
			<?php if ( comments_open() || get_comments_number() ) : ?><div class="narrow-container comments-wrap"><?php comments_template(); ?></div><?php endif; ?>
		</div></article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
