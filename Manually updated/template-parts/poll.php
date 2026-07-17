<?php
/**
 * Template part: Poll widget.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fasdent_get_poll' ) || ! function_exists( 'fasdent_render_poll' ) ) {
	return;
}

$poll = fasdent_get_poll( get_the_ID() );
if ( ! $poll || empty( $poll->id ) ) {
	return;
}

global $wpdb;

$remote_ip     = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
$anonymized_ip = function_exists( 'wp_privacy_anonymize_ip' ) ? wp_privacy_anonymize_ip( $remote_ip ) : $remote_ip;
$ip_hash       = hash_hmac( 'sha256', $anonymized_ip, wp_salt( 'nonce' ) );
$table_name    = $wpdb->prefix . 'fasdent_poll_votes';
$voted         = (bool) $wpdb->get_var(
	$wpdb->prepare(
		"SELECT 1 FROM {$table_name} WHERE poll_id = %d AND ip_hash = %s LIMIT 1", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		absint( $poll->id ),
		$ip_hash
	)
);
?>
<section class="poll-widget card" aria-label="<?php esc_attr_e( 'نظرسنجی', 'fasdent' ); ?>">
	<?php echo fasdent_render_poll( $poll, $voted ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</section>
