<?php
/**
 * Knowledge Base topic taxonomy archive.
 *
 * @package Fasdent
 */

get_header();
$term = get_queried_object();
$icon = ( $term instanceof WP_Term && function_exists( 'fasdent_kb_topic_icon' ) ) ? fasdent_kb_topic_icon( $term ) : 'fa-solid fa-book-medical';
?>
<section class="section kb-archive">
	<div class="container">
		<header class="kb-archive__header">
			<p class="kb-archive__icon" aria-hidden="true"><i class="<?php echo esc_attr( $icon ); ?>"></i></p>
			<h1 class="section-title"><?php single_term_title(); ?></h1>
			<?php if ( $term instanceof WP_Term && $term->description ) : ?>
				<p class="section-desc"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>
		</header>

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
			<p class="kb-empty"><?php esc_html_e( 'مقاله‌ای در این موضوع نیست.', 'fasdent' ); ?></p>
		<?php endif; ?>

		<p style="text-align:center;margin-top:2rem;">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'kb_article' ) ); ?>" class="btn btn-secondary"><?php esc_html_e( 'همه مقالات', 'fasdent' ); ?></a>
		</p>
	</div>
</section>
<?php
get_footer();
