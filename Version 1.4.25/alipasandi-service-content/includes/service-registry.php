<?php
/** Central service registry and appointment-option contract. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Normalize one registry record while accepting the legacy string-list filter shape. */
function alipasandi_normalize_service_registry( $registry ) {
	$defaults = array(
		'implant' => array( 'key'=>'implant', 'label'=>'ایمپلنت دندان', 'bookable'=>true, 'page_slug'=>'services/implant', 'content_managed'=>true, 'icon'=>'implant' ),
		'crown'   => array( 'key'=>'crown', 'label'=>'روکش زیرکونیا', 'bookable'=>true, 'page_slug'=>'services/crown', 'content_managed'=>true, 'icon'=>'crown' ),
		'surgery' => array( 'key'=>'surgery', 'label'=>'جراحی و لثه', 'bookable'=>true, 'page_slug'=>'services/surgery', 'content_managed'=>true, 'icon'=>'surgery' ),
		'general' => array( 'key'=>'general', 'label'=>'درمان عمومی', 'bookable'=>true, 'page_slug'=>'services/general', 'content_managed'=>true, 'icon'=>'general' ),
	);

	if ( ! is_array( $registry ) ) {
		return $defaults;
	}

	$out = array();
	foreach ( $registry as $index => $value ) {
		if ( is_string( $value ) ) {
			$key = sanitize_key( $value );
			$item = isset( $defaults[ $key ] ) ? $defaults[ $key ] : array( 'key'=>$key, 'label'=>$key, 'bookable'=>false, 'page_slug'=>'services/' . $key, 'content_managed'=>true, 'icon'=>'tooth' );
		} elseif ( is_array( $value ) ) {
			$key = sanitize_key( isset( $value['key'] ) ? $value['key'] : ( is_string( $index ) ? $index : '' ) );
			if ( '' === $key ) { continue; }
			$base = isset( $defaults[ $key ] ) ? $defaults[ $key ] : array( 'key'=>$key, 'label'=>$key, 'bookable'=>false, 'page_slug'=>'', 'content_managed'=>false, 'icon'=>'tooth' );
			$item = array_merge( $base, $value );
			$item['key'] = $key;
		} else {
			continue;
		}

		$item['label'] = sanitize_text_field( isset( $item['label'] ) ? $item['label'] : $item['key'] );
		$item['bookable'] = ! empty( $item['bookable'] );
		$item['content_managed'] = ! empty( $item['content_managed'] );
		$item['page_slug'] = isset( $item['page_slug'] ) ? trim( sanitize_text_field( $item['page_slug'] ), '/' ) : '';
		$item['icon'] = isset( $item['icon'] ) ? sanitize_key( $item['icon'] ) : 'tooth';
		$out[ $item['key'] ] = $item;
	}
	return $out;
}

/**
 * SSOT for service metadata. The existing alipasandi_service_registry filter is
 * preserved; legacy callbacks returning a string list remain supported.
 */
function alipasandi_service_registry() {
	$base = alipasandi_normalize_service_registry( array( 'implant', 'crown', 'surgery', 'general' ) );
	return alipasandi_normalize_service_registry( apply_filters( 'alipasandi_service_registry', $base ) );
}

function alipasandi_service_registry_item( $key ) {
	$registry = alipasandi_service_registry();
	$key = sanitize_key( $key );
	return isset( $registry[ $key ] ) ? $registry[ $key ] : null;
}

/** Only page-backed content-managed services participate in content migration/health. */
function alipasandi_service_keys() {
	$keys = array();
	foreach ( alipasandi_service_registry() as $key => $item ) {
		if ( ! empty( $item['content_managed'] ) && ! empty( $item['page_slug'] ) ) {
			$keys[] = $key;
		}
	}
	return array_values( array_unique( $keys ) );
}

/** Explicit non-page appointment choices, governed separately from page services. */
function alipasandi_appointment_service_extras() {
	$extras = array(
		'consultation' => 'مشاوره اولیه',
		'root-canal'   => 'عصب‌کشی',
		'cleaning'     => 'جرم‌گیری',
		'other'        => 'سایر',
	);
	$extras = apply_filters( 'alipasandi_appointment_service_extras', $extras );
	if ( ! is_array( $extras ) ) { return array(); }
	$out = array();
	foreach ( $extras as $key => $label ) {
		$key = sanitize_key( $key );
		$label = sanitize_text_field( $label );
		if ( '' !== $key && '' !== $label ) { $out[ $key ] = $label; }
	}
	return $out;
}

/** Labels accepted by the appointment form/server validator. */
function alipasandi_allowed_services() {
	$labels = array();
	foreach ( alipasandi_service_registry() as $item ) {
		if ( ! empty( $item['bookable'] ) && '' !== $item['label'] ) {
			$labels[] = $item['label'];
		}
	}
	$labels = array_merge( $labels, array_values( alipasandi_appointment_service_extras() ) );
	return array_values( array_unique( array_filter( $labels ) ) );
}
