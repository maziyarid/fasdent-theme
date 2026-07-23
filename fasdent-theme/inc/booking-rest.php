<?php
/**
 * Bookings REST API — external integrations (Excel, Sheets, CRM).
 *
 * GET  /wp-json/fasdent/v1/bookings
 * GET  /wp-json/fasdent/v1/bookings/{id}
 * POST /wp-json/fasdent/v1/bookings/{id}/status
 *
 * Auth: manage_options or X-Fasdent-Key header
 *
 * @package Fasdent
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fasdent_booking_rest_permission( WP_REST_Request $request ): bool {
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}
	$key = (string) get_option( 'fasdent_booking_api_key', '' );
	if ( $key && hash_equals( $key, (string) $request->get_header( 'x_fasdent_key' ) ) ) {
		return true;
	}
	return false;
}

function fasdent_booking_row_to_array( object $row ): array {
	return array(
		'id'             => (int) $row->id,
		'name'           => $row->name,
		'phone'          => $row->phone,
		'email'          => $row->email,
		'age'            => $row->age !== null ? (int) $row->age : null,
		'gender'         => $row->gender,
		'symptoms'       => $row->symptoms,
		'medical_hist'   => $row->medical_hist,
		'medications'    => $row->medications,
		'allergies'      => $row->allergies,
		'service_id'     => $row->service_id ? (int) $row->service_id : null,
		'service_name'   => $row->service_id ? get_the_title( (int) $row->service_id ) : null,
		'doctor_id'      => $row->doctor_id ? (int) $row->doctor_id : null,
		'preferred_date' => $row->preferred_date,
		'time_range'     => $row->time_range,
		'is_emergency'   => (bool) $row->is_emergency,
		'status'         => $row->status,
		'admin_notes'    => $row->admin_notes,
		'created_at'     => $row->created_at,
		'updated_at'     => $row->updated_at,
	);
}

function fasdent_register_booking_rest(): void {
	register_rest_route(
		'fasdent/v1',
		'/bookings',
		array(
			'methods'             => 'GET',
			'permission_callback' => 'fasdent_booking_rest_permission',
			'callback'            => static function ( WP_REST_Request $req ) {
				global $wpdb;
				$table  = $wpdb->prefix . 'fasdent_bookings';
				$status = sanitize_key( (string) $req->get_param( 'status' ) );
				$limit  = min( 200, max( 1, (int) $req->get_param( 'per_page' ) ?: 50 ) );
				$page   = max( 1, (int) $req->get_param( 'page' ) ?: 1 );
				$offset = ( $page - 1 ) * $limit;
				$where  = '1=1';
				$args   = array();
				if ( $status ) {
					$where .= ' AND status = %s';
					$args[] = $status;
				}
				$since = sanitize_text_field( (string) $req->get_param( 'since' ) );
				if ( $since ) {
					$where .= ' AND created_at >= %s';
					$args[] = $since;
				}
				$sql = "SELECT * FROM {$table} WHERE {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d";
				$args[] = $limit;
				$args[] = $offset;
				$rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ) );
				$data = array_map( 'fasdent_booking_row_to_array', $rows ?: array() );
				return rest_ensure_response( array( 'data' => $data, 'count' => count( $data ) ) );
			},
		)
	);

	register_rest_route(
		'fasdent/v1',
		'/bookings/(?P<id>\\d+)',
		array(
			'methods'             => 'GET',
			'permission_callback' => 'fasdent_booking_rest_permission',
			'callback'            => static function ( WP_REST_Request $req ) {
				global $wpdb;
				$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'fasdent_bookings WHERE id = %d', (int) $req['id'] ) );
				if ( ! $row ) {
					return new WP_Error( 'not_found', 'Booking not found', array( 'status' => 404 ) );
				}
				return rest_ensure_response( fasdent_booking_row_to_array( $row ) );
			},
		)
	);

	register_rest_route(
		'fasdent/v1',
		'/bookings/(?P<id>\\d+)/status',
		array(
			'methods'             => 'POST',
			'permission_callback' => 'fasdent_booking_rest_permission',
			'callback'            => static function ( WP_REST_Request $req ) {
				global $wpdb;
				$id      = (int) $req['id'];
				$status  = sanitize_key( (string) $req->get_param( 'status' ) );
				$allowed = array( 'pending', 'confirmed', 'completed', 'cancelled' );
				if ( ! in_array( $status, $allowed, true ) ) {
					return new WP_Error( 'invalid_status', 'Invalid status', array( 'status' => 400 ) );
				}
				$notes = sanitize_textarea_field( (string) $req->get_param( 'admin_notes' ) );
				$data  = array( 'status' => $status );
				$fmt   = array( '%s' );
				if ( $notes ) {
					$data['admin_notes'] = $notes;
					$fmt[]               = '%s';
				}
				$wpdb->update( $wpdb->prefix . 'fasdent_bookings', $data, array( 'id' => $id ), $fmt, array( '%d' ) );
				$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'fasdent_bookings WHERE id = %d', $id ) );
				return rest_ensure_response( $row ? fasdent_booking_row_to_array( $row ) : array( 'ok' => true ) );
			},
		)
	);
}
add_action( 'rest_api_init', 'fasdent_register_booking_rest' );
