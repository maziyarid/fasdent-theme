<?php
/**
 * Related Posts — Fasdent
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * دریافت مطالب مرتبط بر اساس دسته‌بندی.
 */
function fasdent_get_related_posts( ?int $post_id = null, int $count = 3 ): array {
	$post_id = $post_id ?: get_the_ID();
	$cats    = get_the_category( $post_id );
	if ( ! $cats ) { return array(); }
	return get_posts( array(
		'post_type'      => 'post',
		'posts_per_page' => $count,
		'post__not_in'   => array( $post_id ),
		'category__in'   => wp_list_pluck( $cats, 'term_id' ),
		'orderby'        => 'rand',
		'post_status'    => 'publish',
	) );
}

/**
 * درج مطالب مرتبط inline (بعد از پاراگراف دوم و چهارم).
 */
function fasdent_inject_inline_related( string $content ): string {
	if ( ! is_singular( 'post' ) ) { return $content; }
	$posts = fasdent_get_related_posts( null, 1 );
	if ( ! $posts ) { return $content; }
	$related = $posts[0];
	$card    = '<div class="inline-related-post">'
		. '<strong>بیشتر بخوانید:</strong> '
		. '<a href="' . esc_url( get_permalink( $related ) ) . '">' . esc_html( $related->post_title ) . '</a>'
		. '</div>';
	// درج بعد از دومین </p>.
	$pos = 0;
	for ( $i = 0; $i < 2; $i++ ) {
		$p = strpos( $content, '</p>', $pos );
		if ( $p === false ) { break; }
		$pos = $p + 4;
	}
	if ( $pos > 0 ) {
		$content = substr_replace( $content, $card, $pos, 0 );
	}
	return $content;
}
add_filter( 'the_content', 'fasdent_inject_inline_related', 20 );