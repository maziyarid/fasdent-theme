<?php
/**
 * Template Name: گالری
 *
 * @package Fasdent
 */

get_header(); ?>
<section class="section">
	<div class="container">
		<h1><?php the_title(); ?></h1>
		<div class="grid-3">
			<?php
			$gallery_posts = get_posts( array( 'post_type' => 'service', 'numberposts' => 6, 'post_status' => 'publish' ) );
			foreach ( $gallery_posts as $post ) {
				setup_postdata( $post );
				echo '<div class="card">';
				if ( has_post_thumbnail( $post ) ) {
					echo get_the_post_thumbnail( $post, 'fasdent-gallery' );
				}
				echo '<h3>' . esc_html( $post->post_title ) . '</h3></div>';
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php get_footer(); ?>