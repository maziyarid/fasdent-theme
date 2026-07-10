<?php
/**
 * Template Name: سوالات متداول
 *
 * @package Fasdent
 */

get_header(); ?>
<section class="section">
	<div class="container">
		<h1><?php the_title(); ?></h1>
		<div class="card">
			<?php
			$faqs = get_posts( array( 'post_type' => 'faq', 'numberposts' => 50, 'post_status' => 'publish' ) );
			foreach ( $faqs as $faq ) {
				echo '<div class="faq-item"><button type="button">' . esc_html( $faq->post_title ) . '</button><div class="faq-answer">' . wp_kses_post( $faq->post_content ) . '</div></div>';
			}
			?>
		</div>
	</div>
</section>
<?php get_footer(); ?>