<?php get_header(); ?>
<section class="section">
	<div class="container">
		<h1>خدمات دندانپزشکی فس‌دنت</h1>
		<p>خدمات تخصصی، زیبایی، ایمپلنت، ارتودنسی، جراحی و اورژانس در یک مجموعه حرفه‌ای.</p>
		<div class="grid-3">
			<?php
			$terms = get_terms( array( 'taxonomy' => 'service_category', 'hide_empty' => false, 'parent' => 0 ) );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$icon = fasdent_category_icon( $term );
					echo '<article class="category-card"><h2><i class="' . esc_attr( $icon ) . '"></i> ' . esc_html( $term->name ) . '</h2><p>' . esc_html( $term->description ?: 'خدمات تخصصی در این دسته' ) . '</p><a href="' . esc_url( get_term_link( $term ) ) . '">مشاهده دسته</a></article>';
				}
			}
			?>
		</div>
	</div>
</section>
<?php get_footer(); ?>