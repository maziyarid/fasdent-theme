<?php
/**
 * Emergency service-content compatibility for Theme 1.4.25.
 *
 * READ ONLY: no meta registration, meta box, save, migration, revision or export
 * hooks live in the Theme. Alipasandi Service Content is the sole owner of those
 * operations. This file exists only to render already-stored data when the
 * companion plugin is missing/outdated.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ALIPASANDI_SERVICE_META_KEY' ) ) {
	define( 'ALIPASANDI_SERVICE_META_KEY', '_alipasandi_service' );
}
if ( ! defined( 'ALIPASANDI_SERVICE_KEY_META' ) ) {
	define( 'ALIPASANDI_SERVICE_KEY_META', '_alipasandi_service_key' );
}

if ( ! function_exists( 'alipasandi_service_keys' ) ) {
	function alipasandi_compat_service_keys() {
		return array( 'implant', 'crown', 'surgery', 'general' );
	}
}

if ( ! function_exists( 'alipasandi_get_service_page' ) ) {
	function alipasandi_get_service_page( $key ) {
		$key = sanitize_key( $key );
		if ( ! in_array( $key, alipasandi_compat_service_keys(), true ) ) {
			return null;
		}
		$found = get_posts(
			array(
				'post_type'              => 'page',
				'post_status'            => array( 'publish', 'draft', 'private', 'pending', 'future' ),
				'meta_key'               => ALIPASANDI_SERVICE_KEY_META,
				'meta_value'             => $key,
				'posts_per_page'         => 2,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		if ( count( $found ) > 1 ) {
			return null; // Fail closed on duplicate identity.
		}
		if ( 1 === count( $found ) ) {
			return $found[0];
		}
		$page = get_page_by_path( 'services/' . $key );
		if ( ! $page ) {
			$page = get_page_by_path( $key );
		}
		return $page instanceof WP_Post ? $page : null;
	}
}

if ( ! function_exists( 'alipasandi_service_meta_exists' ) ) {
	function alipasandi_service_meta_exists( $post_id ) {
		return metadata_exists( 'post', (int) $post_id, ALIPASANDI_SERVICE_META_KEY );
	}
}

if ( ! function_exists( 'alipasandi_get_service' ) ) {
	function alipasandi_get_service( $key ) {
		$page = alipasandi_get_service_page( $key );
		if ( $page && alipasandi_service_meta_exists( $page->ID ) ) {
			$meta = get_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, true );
			return is_array( $meta ) ? $meta : array();
		}

		// Emergency rendering only. Missing meta after a completed migration is a
		// critical operational condition and should not remain silent.
		$status = get_option( 'alipasandi_service_meta_status_v1', array() );
		if ( is_array( $status ) && ! empty( $status['completed'] ) ) {
			error_log( '[Alipasandi] CRITICAL: legacy service fallback used after completed migration for key=' . sanitize_key( $key ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
		return function_exists( 'alipasandi_get_service_legacy' ) ? alipasandi_get_service_legacy( $key ) : array();
	}
}
