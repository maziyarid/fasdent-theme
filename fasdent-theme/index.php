<?php
/**
 * قالب آرشیو پیش‌فرض — Fasdent
 * @package Fasdent
 */
get_header();
?>
<section class="section">
	<div class="container">
		<header class="archive-header" style="margin-bottom:2rem;">
			<h1 class="section-title"><?php
				if ( is_home() ) {
					esc_html_e( 'آخرین مطالب', 'fasdent' );
				} elseif ( is_category() ) {
					echo esc_html( single_cat_title( '', false ) );
				} elseif ( is_archive() ) {
					the_archive_title();
				} else {
					esc_html_e( 'مطالب', 'fasdent' );
				}
			?></h1>
		</header>
		<?php if ( have_posts() ) : ?>
		<div class="grid-3">
			<?php while ( have_posts() ) : the_post(); ?>
			<article class="card post-card">
				<?php if ( has_post_thumbnail() ) : ?>
				<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'fasdent-card', array( 'loading' => 'lazy' ) ); ?></a>
				<?php endif; ?>
				<div class="post-card__body">
					<h2 style="font-size:1rem;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p style="font-size:.82rem;color:var(--color-muted);"><?php the_excerpt(); ?></p>
					<a href="<?php the_permalink(); ?>" class="btn btn-primary" style="margin-top:.5rem;font-size:.82rem;"><?php esc_html_e( 'ادامه مطلب', 'fasdent' ); ?></a>
				</div>
			</article>
			<?php endwhile; ?>
		</div>
		<div class="pagination"><?php the_posts_pagination( array(
			'prev_text' => '<i class="fa-solid fa-angle-right" aria-hidden="true"></i> قبلی',
			'next_text' => 'بعدی <i class="fa-solid fa-angle-left" aria-hidden="true"></i>',
		) ); ?></div>
		<?php else : ?>
		<p class="no-results"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> <?php esc_html_e( 'محتوایی یافت نشد.', 'fasdent' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>