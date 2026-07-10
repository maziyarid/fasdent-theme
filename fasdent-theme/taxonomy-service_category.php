<?php get_header(); ?>
<section class="section">
	<div class="container">
		<?php $term = get_queried_object(); ?>
		<h1><?php echo esc_html( $term->name ); ?></h1>
		<p><?php echo esc_html( $term->description ?: 'خدمات تخصصی در این دسته آماده ارائه است.' ); ?></p>
		<div class="grid-3">
			<?php
			$query = new WP_Query( array(
				'post_type'      => 'service',
				'posts_per_page' => 12,
				'tax_query'      => array( array( 'taxonomy' => 'service_category', 'field' => 'term_id', 'terms' => $term->term_id ) ),
			) );
			if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
				<article class="service-card">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; wp_reset_postdata(); endif; ?>
		</div>
	</div>
</section>
<?php get_footer(); ?>