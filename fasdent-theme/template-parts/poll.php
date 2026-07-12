<?php
/**
 * Poll Widget Template Part
 * Usage: get_template_part( 'template-parts/poll' );
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$post_id = get_the_ID();
if ( ! function_exists( 'fasdent_get_poll' ) ) { return; }
$poll = fasdent_get_poll( $post_id );
if ( ! $poll ) { return; }
global $wpdb;
$ip_hash = md5( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ) );
$voted   = (bool) $wpdb->get_var( $wpdb->prepare(
  "SELECT id FROM {$wpdb->prefix}fasdent_poll_votes WHERE poll_id=%d AND ip_hash=%s",
  $poll->id, $ip_hash
) );
echo fasdent_render_poll( $poll, $voted );