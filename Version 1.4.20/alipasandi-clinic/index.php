<?php
/** Blog and fallback index. */
get_header();
?>
<main id="main-content" class="content-area">
	<header class="inner-hero">
		<div class="narrow-container section-heading" style="padding-block:64px;position:relative">
			<span class="eyebrow">دانش و مراقبت</span>
			<h1><?php echo is_archive() ? wp_kses_post( get_the_archive_title() ) : esc_html__( 'مقالات آموزشی', 'alipasandi-clinic' ); ?></h1>
			<p>مطالب عمومی برای آگاهی بهتر از سلامت دهان؛ این مطالب جایگزین معاینه و تشخیص فردی نیستند.</p>
		</div>
	</header>
	<section class="section section-bone">
		<div class="site-container">
			<?php if ( have_posts() ) : ?>
				<div class="post-grid">
					<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/post', 'card' ); endwhile; ?>
				</div>
				<div class="pagination"><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => 'قبلی', 'next_text' => 'بعدی' ) ); ?></div>
			<?php else : ?>
				<div class="entry-card">هنوز مطلبی منتشر نشده است.</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer(); ?>

