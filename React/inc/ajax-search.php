<?php
/**
 * Live AJAX Search — Fasdent
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_ajax_live_search(): void {
	check_ajax_referer( 'fasdent_form_nonce', 'nonce' );
	$term = sanitize_text_field( wp_unslash( $_POST['term'] ?? '' ) );
	if ( mb_strlen( $term ) < 2 ) {
		wp_send_json_success( [ 'results' => [] ] );
	}
	$type_labels = [ 'post' => 'مقاله', 'service' => 'خدمت', 'doctor' => 'پزشک', 'faq' => 'سوال' ];
	$posts = get_posts( [
		'post_type'      => [ 'post', 'service', 'doctor', 'faq' ],
		'posts_per_page' => 8,
		'post_status'    => 'publish',
		's'              => $term,
	] );
	$results = [];
	foreach ( $posts as $p ) {
		$results[] = [
			'title'     => get_the_title( $p ),
			'url'       => get_permalink( $p ),
			'type'      => $type_labels[ $p->post_type ] ?? $p->post_type,
			'excerpt'   => wp_trim_words( wp_strip_all_tags( $p->post_content ), 12 ),
			'thumbnail' => has_post_thumbnail( $p ) ? get_the_post_thumbnail_url( $p, 'thumbnail' ) : '',
		];
	}
	wp_send_json_success( [ 'results' => $results ] );
}
add_action( 'wp_ajax_fasdent_live_search',        'fasdent_ajax_live_search' );
add_action( 'wp_ajax_nopriv_fasdent_live_search', 'fasdent_ajax_live_search' );

add_action( 'rest_api_init', function () {
	register_rest_route( 'fasdent/v1', '/search', [
		'methods'             => 'GET',
		'callback'            => function ( WP_REST_Request $req ) {
			$term = sanitize_text_field( $req->get_param( 'term' ) ?? '' );
			if ( mb_strlen( $term ) < 2 ) return rest_ensure_response( [] );
			$posts = get_posts( [
				'post_type'      => [ 'post', 'service', 'faq' ],
				'posts_per_page' => 6,
				'post_status'    => 'publish',
				's'              => $term,
			] );
			return rest_ensure_response( array_map( fn( $p ) => [
				'title' => get_the_title( $p ),
				'url'   => get_permalink( $p ),
				'type'  => $p->post_type,
			], $posts ) );
		},
		'permission_callback' => '__return_true',
	] );
} );
