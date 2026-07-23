<?php
/**
 * Knowledge Base archive.
 *
 * @package Fasdent
 */

get_header();
?>
<section class="section kb-archive">
	<div class="container">
		<header class="kb-archive__header">
			<h1 class="section-title"><?php post_type_archive_title(); ?></h1>
			<p class="section-desc"><?php esc_html_e( 'همه مقالات آموزشی کلینیک فس‌دنت', 'fasdent' ); ?></p>
		</header>

		<?php
		$topics = get_terms( array( 'taxonomy' => 'kb_topic', 'hide_empty' => true ) );
		if ( $topics && ! is_wp_error( $topics ) ) :
			?>
			<nav class="kb-filter" aria-label="<?php esc_attr_e( 'فیلتر موضوع', 'fasdent' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'kb_article' ) ); ?>" class="kb-filter__btn <?php echo ! is_tax() ? 'is-active' : ''; ?>"><?php esc_html_e( 'همه', 'fasdent' ); ?></a>
				<?php foreach ( $topics as $t ) : ?>
					<a href="<?php echo esc_url( get_term_link( $t ) ); ?>" class="kb-filter__btn <?php echo is_tax( 'kb_topic', $t->term_id ) ? 'is-active' : ''; ?>"><?php echo esc_html( $t->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="kb-articles-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/kb-card' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
		<?php else : ?>
			<p class="kb-empty"><?php esc_html_e( 'مقاله‌ای در این بخش نیست.', 'fasdent' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
