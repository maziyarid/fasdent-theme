<?php
/**
 * Poll System — Fasdent
 * Tables: wp_fasdent_polls, wp_fasdent_poll_votes
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_polls_create_tables(): void {
	global $wpdb;
	if ( (int) get_option( 'fasdent_polls_db_version', 0 ) >= 1 ) { return; }
	$c = $wpdb->get_charset_collate();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fasdent_polls (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		post_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
		question text NOT NULL,
		options longtext NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'active',
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id), KEY post_id (post_id)
	) $c;" );
	dbDelta( "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fasdent_poll_votes (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		poll_id bigint(20) UNSIGNED NOT NULL,
		option_id varchar(50) NOT NULL,
		ip_hash varchar(64) NOT NULL,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY unique_vote (poll_id, ip_hash),
		KEY poll_id (poll_id)
	) $c;" );
	update_option( 'fasdent_polls_db_version', 1 );
}
add_action( 'after_switch_theme', 'fasdent_polls_create_tables' );
add_action( 'init', function () {
	static $done = false;
	if ( ! $done && ! get_option( 'fasdent_polls_db_version' ) ) {
		fasdent_polls_create_tables(); $done = true;
	}
} );

function fasdent_get_poll( int $post_id ): ?object {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}fasdent_polls WHERE post_id=%d AND status='active' LIMIT 1",
		$post_id
	) );
}

function fasdent_get_poll_results( int $poll_id, array $options ): array {
	global $wpdb;
	$res  = array_fill_keys( array_keys( $options ), 0 );
	$rows = $wpdb->get_results( $wpdb->prepare(
		"SELECT option_id, COUNT(*) AS cnt FROM {$wpdb->prefix}fasdent_poll_votes WHERE poll_id=%d GROUP BY option_id",
		$poll_id
	) );
	foreach ( $rows as $row ) {
		if ( isset( $res[ $row->option_id ] ) ) $res[ $row->option_id ] = (int) $row->cnt;
	}
	return $res;
}

function fasdent_render_poll( object $poll, bool $voted = false ): string {
	$options = json_decode( $poll->options, true );
	if ( ! is_array( $options ) || ! $options ) return '';
	$total = 0; $results = [];
	if ( $voted ) { $results = fasdent_get_poll_results( (int) $poll->id, $options ); $total = array_sum( $results ); }
	$html  = '<div class="poll-widget' . ( $voted ? ' voted' : '' ) . '" data-poll-id="' . esc_attr( $poll->id ) . '">';
	$html .= '<p class="poll-question"><i class="fa-regular fa-chart-bar" aria-hidden="true"></i> ' . esc_html( $poll->question ) . '</p>';
	$html .= '<div class="poll-options">';
	foreach ( $options as $key => $label ) {
		$pct = ( $voted && $total > 0 ) ? round( ( ( $results[ $key ] ?? 0 ) / $total ) * 100 ) : 0;
		if ( $voted ) {
			$html .= '<div class="poll-result-row"><span class="poll-label">' . esc_html( $label ) . '</span>';
			$html .= '<div class="poll-bar" role="progressbar" aria-valuenow="' . $pct . '" aria-valuemin="0" aria-valuemax="100" style="--pct:' . $pct . '%"><span>' . $pct . '%</span></div></div>';
		} else {
			$html .= '<button type="button" class="poll-option-btn btn btn-secondary" data-option="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</button>';
		}
	}
	$html .= '</div>';
	if ( $voted ) $html .= '<p class="poll-total"><small>' . sprintf( '%d نفر شرکت کردند', $total ) . '</small></p>';
	$html .= '</div>';
	return $html;
}

function fasdent_ajax_poll_vote(): void {
	check_ajax_referer( 'fasdent_form_nonce', 'nonce' );
	$poll_id   = (int) sanitize_text_field( wp_unslash( $_POST['poll_id']   ?? '0' ) );
	$option_id = sanitize_key( wp_unslash( $_POST['option_id'] ?? '' ) );
	if ( ! $poll_id || ! $option_id ) wp_send_json_error();
	global $wpdb;
	$poll = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fasdent_polls WHERE id=%d AND status='active'", $poll_id ) );
	if ( ! $poll ) wp_send_json_error( [ 'message' => 'نظرسنجی یافت نشد.' ] );
	$options = json_decode( $poll->options, true );
	if ( ! isset( $options[ $option_id ] ) ) wp_send_json_error( [ 'message' => 'گزینه نامعتبر.' ] );
	$ip_hash  = md5( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ) );
	$inserted = $wpdb->insert(
		$wpdb->prefix . 'fasdent_poll_votes',
		[ 'poll_id' => $poll_id, 'option_id' => $option_id, 'ip_hash' => $ip_hash ],
		[ '%d', '%s', '%s' ]
	);
	if ( ! $inserted ) wp_send_json_error( [ 'message' => 'قبلاً رای داده‌اید.' ] );
	wp_send_json_success( [ 'html' => fasdent_render_poll( $poll, true ) ] );
}
add_action( 'wp_ajax_fasdent_poll_vote',        'fasdent_ajax_poll_vote' );
add_action( 'wp_ajax_nopriv_fasdent_poll_vote', 'fasdent_ajax_poll_vote' );

add_shortcode( 'fasdent_poll', function ( array $atts ): string {
	$atts    = shortcode_atts( [ 'post_id' => get_the_ID() ], $atts );
	$poll    = fasdent_get_poll( (int) $atts['post_id'] );
	if ( ! $poll ) return '';
	global $wpdb;
	$ip_hash = md5( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ) );
	$voted   = (bool) $wpdb->get_var( $wpdb->prepare(
		"SELECT id FROM {$wpdb->prefix}fasdent_poll_votes WHERE poll_id=%d AND ip_hash=%s",
		$poll->id, $ip_hash
	) );
	return fasdent_render_poll( $poll, $voted );
} );