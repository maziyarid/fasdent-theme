<?php
/**
 * Related Posts — Fasdent
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * دریافت مطالب مرتبط.
 * اولویت با متای related_posts (تنظیم‌شده توسط ایمپورتر دمو یا ویرایشگر).
 * در نبود متا، از دسته‌بندی مشترک استفاده می‌شود.
 *
 * @param int|null $post_id شناسه پست.
 * @param int      $count   تعداد.
 * @return WP_Post[]
 */
function fasdent_get_related_posts( ?int $post_id = null, int $count = 3 ): array {
	$post_id = $post_id ?: get_the_ID();
	if ( ! $post_id ) {
		return array();
	}

	// Prefer explicit related_posts meta (demo importer or manual).
	$related_ids = get_post_meta( $post_id, 'related_posts', true );
	if ( is_array( $related_ids ) && ! empty( $related_ids ) ) {
		$related_ids = array_map( 'intval', $related_ids );
		$related_ids = array_filter( $related_ids );
		$related_ids = array_diff( $related_ids, array( $post_id ) );
		if ( $related_ids ) {
			$posts = get_posts( array(
				'post_type'      => 'post',
				'post__in'       => array_slice( $related_ids, 0, $count ),
				'posts_per_page' => $count,
				'orderby'        => 'post__in',
				'post_status'    => 'publish',
			) );
			if ( $posts ) {
				return $posts;
			}
		}
	}

	// Fallback: same category, random.
	$cats = get_the_category( $post_id );
	if ( ! $cats ) {
		return array();
	}
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
 * درج مطالب مرتبط inline (بعد از پاراگراف دوم).
 */
function fasdent_inject_inline_related( string $content ): string {
	if ( ! is_singular( 'post' ) ) {
		return $content;
	}
	$posts = fasdent_get_related_posts( null, 1 );
	if ( ! $posts ) {
		return $content;
	}
	$related = $posts[0];
	$card    = '<div class="inline-related-post">'
		. '<strong>بیشتر بخوانید:</strong> '
		. '<a href="' . esc_url( get_permalink( $related ) ) . '">' . esc_html( $related->post_title ) . '</a>'
		. '</div>';
	$pos = 0;
	for ( $i = 0; $i < 2; $i++ ) {
		$p = strpos( $content, '</p>', $pos );
		if ( $p === false ) {
			break;
		}
		$pos = $p + 4;
	}
	if ( $pos > 0 ) {
		$content = substr_replace( $content, $card, $pos, 0 );
	}
	return $content;
}
add_filter( 'the_content', 'fasdent_inject_inline_related', 20 );
