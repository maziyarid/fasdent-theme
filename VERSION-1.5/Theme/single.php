<?php
/** Single post template. */
get_header();
?>
<main id="main-content" class="content-area">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php $categories = wp_get_post_categories( get_the_ID(), array( 'fields' => 'names' ) ); ?>
			<?php
			$title_html = the_title( '', '', false );
			$meta_html  = '<div class="entry-meta"><span>نویسنده: ' . get_the_author() . '</span><span>انتشار: ' . esc_html( get_the_date() ) . '</span>';
			if ( get_the_modified_time( 'U' ) > get_the_time( 'U' ) ) {
				$meta_html .= '<span>آخرین بازبینی: ' . esc_html( get_the_modified_date() ) . '</span>';
			}
			$meta_html .= '</div>';
			alipasandi_inner_hero( 'header', esc_html( $categories ? implode( '، ', $categories ) : 'مقاله آموزشی' ), $title_html, '', $meta_html );
			?>
		<article <?php post_class( 'section section-bone' ); ?>><div class="site-container">
			<?php if ( has_post_thumbnail() ) : ?><div class="feature-image" style="max-width:980px;margin:0 auto 36px"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
			<div class="entry-content"><?php the_content(); ?><?php wp_link_pages(); ?></div>
			<div class="medical-note narrow-container"><?php alipasandi_the_icon( 'info', 22 ); ?><div><strong>یادآوری پزشکی</strong><p>این مطلب عمومی است و جایگزین معاینه، تشخیص یا طرح درمان اختصاصی نیست.</p></div></div>
			<?php if ( comments_open() || get_comments_number() ) : ?><div class="narrow-container comments-wrap"><?php comments_template(); ?></div><?php endif; ?>
		</div></article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
