<?php
/** Central service registry and appointment-option contract. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Exact public schema for one service-registry record. */
function alipasandi_service_registry_fields() {
	return array( 'key', 'label', 'bookable', 'page_slug', 'content_managed', 'icon' );
}

/** Theme-supported icon names; extensions may add an icon explicitly. */
function alipasandi_service_registry_icon_allowlist() {
	$icons = array( 'implant', 'crown', 'surgery', 'general', 'tooth' );
	$icons = apply_filters( 'alipasandi_service_registry_icon_allowlist', $icons );
	if ( ! is_array( $icons ) ) { return array( 'tooth' ); }
	$out = array();
	foreach ( $icons as $icon ) {
		$icon = sanitize_key( $icon );
		if ( '' !== $icon ) { $out[] = $icon; }
	}
	return array_values( array_unique( $out ) );
}

/**
 * Registry SSOT. Non-page appointment choices live here too, so the rendered
 * appointment options and server-side allowlist cannot drift into two lists.
 */
function alipasandi_service_registry_defaults() {
	return array(
		'implant'      => array( 'key'=>'implant', 'label'=>'ایمپلنت دندان', 'bookable'=>true, 'page_slug'=>'services/implant', 'content_managed'=>true, 'icon'=>'implant' ),
		'crown'        => array( 'key'=>'crown', 'label'=>'روکش زیرکونیا', 'bookable'=>true, 'page_slug'=>'services/crown', 'content_managed'=>true, 'icon'=>'crown' ),
		'surgery'      => array( 'key'=>'surgery', 'label'=>'جراحی و لثه', 'bookable'=>true, 'page_slug'=>'services/surgery', 'content_managed'=>true, 'icon'=>'surgery' ),
		'general'      => array( 'key'=>'general', 'label'=>'درمان عمومی', 'bookable'=>true, 'page_slug'=>'services/general', 'content_managed'=>true, 'icon'=>'general' ),
		'consultation' => array( 'key'=>'consultation', 'label'=>'مشاوره اولیه', 'bookable'=>true, 'page_slug'=>'', 'content_managed'=>false, 'icon'=>'tooth' ),
		'root-canal'   => array( 'key'=>'root-canal', 'label'=>'عصب‌کشی', 'bookable'=>true, 'page_slug'=>'', 'content_managed'=>false, 'icon'=>'tooth' ),
		'cleaning'     => array( 'key'=>'cleaning', 'label'=>'جرم‌گیری', 'bookable'=>true, 'page_slug'=>'', 'content_managed'=>false, 'icon'=>'tooth' ),
		'other'        => array( 'key'=>'other', 'label'=>'سایر', 'bookable'=>true, 'page_slug'=>'', 'content_managed'=>false, 'icon'=>'tooth' ),
	);
}

/** Strict boolean normalization. Returns null for malformed values. */
function alipasandi_service_registry_bool( $value ) {
	if ( is_array( $value ) || is_object( $value ) || is_resource( $value ) ) { return null; }
	if ( true === $value || 1 === $value || '1' === $value || 'true' === strtolower( (string) $value ) ) { return true; }
	if ( false === $value || 0 === $value || '0' === $value || '' === $value || 'false' === strtolower( (string) $value ) ) { return false; }
	return null;
}

/** Validate a slash-separated page slug without silently rewriting it. */
function alipasandi_service_registry_valid_slug( $slug ) {
	$slug = trim( (string) $slug, '/' );
	if ( '' === $slug ) { return ''; }
	return 1 === preg_match( '/^[a-z0-9-]+(?:\/[a-z0-9-]+)*$/', $slug ) ? $slug : false;
}

/**
 * Normalize registry records to the six-field schema. Unknown fields are
 * dropped and recorded. Malformed entries are skipped and surfaced by Health.
 * Legacy string-list filter output is accepted only for known default keys and
 * is reported as a compatibility warning rather than silently creating data.
 */
function alipasandi_normalize_service_registry( $registry, &$issues = null ) {
	$issues = array();
	$defaults = alipasandi_service_registry_defaults();
	$allowed_fields = alipasandi_service_registry_fields();
	$allowed_icons = alipasandi_service_registry_icon_allowlist();
	if ( ! is_array( $registry ) ) {
		$issues[] = 'registry_not_array';
		return array();
	}

	$out = array();
	foreach ( $registry as $index => $value ) {
		if ( is_string( $value ) ) {
			$key = sanitize_key( $value );
			if ( ! isset( $defaults[ $key ] ) ) {
				$issues[] = 'legacy_string_unknown_key:' . $key;
				continue;
			}
			$issues[] = 'legacy_string_entry:' . $key;
			$value = $defaults[ $key ];
		} elseif ( ! is_array( $value ) ) {
			$issues[] = 'entry_not_array:' . sanitize_key( (string) $index );
			continue;
		}

		$entry_has_unknown_field = false;
		foreach ( array_keys( $value ) as $field ) {
			if ( ! in_array( $field, $allowed_fields, true ) ) {
				$issues[] = 'unknown_field:' . sanitize_key( (string) $index ) . ':' . sanitize_key( (string) $field );
				$entry_has_unknown_field = true;
			}
		}
		if ( $entry_has_unknown_field ) { continue; }

		$raw_key = isset( $value['key'] ) ? trim( (string) $value['key'] ) : ( is_string( $index ) ? trim( $index ) : '' );
		$key = sanitize_key( $raw_key );
		if ( '' === $key || $key !== strtolower( $raw_key ) || 1 !== preg_match( '/^[a-z0-9][a-z0-9-]{0,63}$/', $key ) ) {
			$issues[] = 'invalid_key:' . sanitize_key( $raw_key );
			continue;
		}
		if ( isset( $out[ $key ] ) ) {
			$issues[] = 'duplicate_key:' . $key;
			continue;
		}

		$base = isset( $defaults[ $key ] ) ? $defaults[ $key ] : array();
		if ( empty( $base ) ) {
			foreach ( $allowed_fields as $field ) {
				if ( ! array_key_exists( $field, $value ) ) {
					$issues[] = 'missing_field:' . $key . ':' . $field;
					continue 2;
				}
			}
		}

		$label = sanitize_text_field( array_key_exists( 'label', $value ) ? $value['label'] : ( isset( $base['label'] ) ? $base['label'] : '' ) );
		$bookable = alipasandi_service_registry_bool( array_key_exists( 'bookable', $value ) ? $value['bookable'] : ( isset( $base['bookable'] ) ? $base['bookable'] : null ) );
		$content_managed = alipasandi_service_registry_bool( array_key_exists( 'content_managed', $value ) ? $value['content_managed'] : ( isset( $base['content_managed'] ) ? $base['content_managed'] : null ) );
		$page_slug = alipasandi_service_registry_valid_slug( array_key_exists( 'page_slug', $value ) ? $value['page_slug'] : ( isset( $base['page_slug'] ) ? $base['page_slug'] : '' ) );
		$icon = sanitize_key( array_key_exists( 'icon', $value ) ? $value['icon'] : ( isset( $base['icon'] ) ? $base['icon'] : 'tooth' ) );

		if ( '' === trim( $label ) ) { $issues[] = 'empty_label:' . $key; continue; }
		if ( null === $bookable ) { $issues[] = 'invalid_bookable:' . $key; continue; }
		if ( null === $content_managed ) { $issues[] = 'invalid_content_managed:' . $key; continue; }
		if ( false === $page_slug ) { $issues[] = 'invalid_page_slug:' . $key; continue; }
		if ( $content_managed && '' === $page_slug ) { $issues[] = 'managed_service_missing_page_slug:' . $key; continue; }
		if ( ! in_array( $icon, $allowed_icons, true ) ) { $issues[] = 'invalid_icon:' . $key; continue; }

		$out[ $key ] = array(
			'key'             => $key,
			'label'           => $label,
			'bookable'        => $bookable,
			'page_slug'       => $page_slug,
			'content_managed' => $content_managed,
			'icon'            => $icon,
		);
	}
	return $out;
}

/** Return normalized registry plus non-silent validation issues for this request. */
function alipasandi_service_registry_state( $refresh = false ) {
	static $state = null;
	if ( $refresh || null === $state ) {
		$raw = apply_filters( 'alipasandi_service_registry', alipasandi_service_registry_defaults() );
		$issues = array();
		$items = alipasandi_normalize_service_registry( $raw, $issues );
		$state = array( 'items'=>$items, 'issues'=>array_values( array_unique( $issues ) ) );
	}
	return $state;
}

function alipasandi_service_registry() {
	$state = alipasandi_service_registry_state();
	return $state['items'];
}

function alipasandi_service_registry_issues() {
	$state = alipasandi_service_registry_state();
	return $state['issues'];
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

/** Labels accepted by BOTH appointment rendering and server-side validation. */
function alipasandi_allowed_services() {
	$labels = array();
	foreach ( alipasandi_service_registry() as $item ) {
		if ( ! empty( $item['bookable'] ) && '' !== $item['label'] ) {
			$labels[] = $item['label'];
		}
	}
	return array_values( array_unique( $labels ) );
}
