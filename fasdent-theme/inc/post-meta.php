<?php
/**
 * Post Meta Enhancement — Fasdent
 * Reading time, view count, reactions
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** زمان مطالعه بر حسب دقیقه (سرعت فارسی: ۲۰۰ کلمه/دقیقه). */
function fasdent_reading_time( ?int $post_id = null ): int {
	$post_id  = $post_id ?: get_the_ID();
	$stored   = (int) get_post_meta( $post_id, '_reading_time', true );
	if ( $stored ) { return $stored; }
	$content  = get_post_field( 'post_content', $post_id );
	$words    = str_word_count( wp_strip_all_tags( $content ), 0 );
	$time     = max( 1, (int) ceil( $words / 200 ) );
	update_post_meta( $post_id, '_reading_time', $time );
	return $time;
}
add_action( 'save_post', function( $id ) {
	delete_post_meta( $id, '_reading_time' );
} );

/** ردیابی بازدید با کَش‌پذیری (از طریق transient روزانه). */
function fasdent_track_view( int $post_id ): void {
	if ( is_admin() || is_user_logged_in() ) { return; }
	$key   = 'fasdent_views_' . wp_date( 'Y-m-d' ) . '_' . $post_id;
	$count = (int) get_transient( $key );
	if ( ! $count ) {
		// ذخیره در postmeta.
		$total = (int) get_post_meta( $post_id, '_view_count', true );
		update_post_meta( $post_id, '_view_count', $total + 1 );
	}
	set_transient( $key, $count + 1, DAY_IN_SECONDS );
}
add_action( 'wp', function() {
	if ( is_singular( array( 'post', 'service' ) ) ) {
		fasdent_track_view( get_the_ID() );
	}
} );

/** دریافت تعداد بازدید. */
function fasdent_get_view_count( ?int $post_id = null ): int {
	return (int) get_post_meta( $post_id ?: get_the_ID(), '_view_count', true );
}

/** AJAX handler واکنش به مطلب. */
function fasdent_handle_post_reaction(): void {
	check_ajax_referer( 'fasdent_form_nonce', 'nonce' );
	$post_id  = (int) sanitize_text_field( wp_unslash( $_POST['post_id'] ?? '0' ) );
	$reaction = sanitize_key( wp_unslash( $_POST['reaction'] ?? '' ) );
	$allowed  = array( 'helpful', 'thanks', 'accurate' );
	if ( ! $post_id || ! in_array( $reaction, $allowed, true ) ) {
		wp_send_json_error();
	}
	$ip_hash = md5( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ) );
	$key     = 'fasdent_reaction_' . $post_id . '_' . $reaction . '_' . $ip_hash;
	if ( get_transient( $key ) ) {
		wp_send_json_error( array( 'message' => 'قبلاً واکنش ثبت کرده‌اید.' ) );
	}
	set_transient( $key, 1, 30 * DAY_IN_SECONDS );
	$count_key = '_reaction_' . $reaction;
	$count     = (int) get_post_meta( $post_id, $count_key, true ) + 1;
	update_post_meta( $post_id, $count_key, $count );
	wp_send_json_success( array( 'count' => $count ) );
}
add_action( 'wp_ajax_fasdent_post_reaction',        'fasdent_handle_post_reaction' );
add_action( 'wp_ajax_nopriv_fasdent_post_reaction', 'fasdent_handle_post_reaction' );