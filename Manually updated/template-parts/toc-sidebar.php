<?php
/**
 * Template part: Table of contents sidebar.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'FASDENT_TOC_SIDEBAR' ) ) {
	define( 'FASDENT_TOC_SIDEBAR', true );
}

remove_filter( 'the_content', 'fasdent_toc_inject_inline', 15 );

global $post;

if ( ! $post instanceof WP_Post || ! function_exists( 'fasdent_toc_extract' ) || ! function_exists( 'fasdent_toc_render' ) ) {
	return;
}

$content = get_the_content( null, false, $post );
$items   = fasdent_toc_extract( $content );

if ( ! is_array( $items ) || count( $items ) < 3 ) {
	return;
}
?>
<aside class="single-post__toc" aria-label="<?php esc_attr_e( 'فهرست مطالب', 'fasdent' ); ?>">
	<?php echo wp_kses_post( fasdent_toc_render( $items ) ); ?>
</aside>
