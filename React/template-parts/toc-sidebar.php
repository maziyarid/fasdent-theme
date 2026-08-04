<?php
/**
 * Template part: فهرست مطالب (Sidebar)
 * استفاده: get_template_part('template-parts/toc-sidebar')
 * @package Fasdent
 */

// تعریف قبل از remove_filter تا toc.php بتواند وضعیت را بررسی کند.
if ( ! defined( 'FASDENT_TOC_SIDEBAR' ) ) {
	define( 'FASDENT_TOC_SIDEBAR', true );
}
// حذف فیلتر inline در صورت استفاده از sidebar.
remove_filter( 'the_content', 'fasdent_toc_inject_inline', 15 );

global $post;
$content = get_the_content( null, false, $post );
$items   = function_exists( 'fasdent_toc_extract' ) ? fasdent_toc_extract( $content ) : array();
if ( count( $items ) < 3 ) { return; }
echo fasdent_toc_render( $items );