<?php
/**
 * Search Results Template
 * 
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( have_posts() ) {
	?>
	<header class="page-header">
		<h1 class="page-title">
			<?php printf( esc_html__( 'Results for: %s', 'fasdent' ), '<span>' . get_search_query() . '</span>' ); ?>
		</h1>
	</header>
	<?php
	
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', 'search' );
	}
	
	the_posts_navigation();
} else {
	get_template_part( 'template-parts/content', 'none' );
}

get_footer();
