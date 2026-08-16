<?php
/**
 * Integrated operational health, Rank Math observation and rendered NAP probes.
 *
 * @package Alipasandi_Service_Content
 * @since 1.3.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Build a consistently shaped health result. */
function alipasandi_health_result( $status, $code, $message, $details = array() ) {
	return array_merge(
		array(
			'status'  => $status,
			'code'    => $code,
			'message' => $message,
		),
		is_array( $details ) ? $details : array()
	);
}

/** Recursively count LocalBusiness-family entities in a final JSON-LD graph. */
function alipasandi_schema_local_entity_count( $node ) {
	if ( ! is_array( $node ) ) {
		return 0;
	}
	$count = 0;
	if ( isset( $node['@type'] ) && function_exists( 'alipasandi_schema_is_local_entity' ) && alipasandi_schema_is_local_entity( $node['@type'] ) ) {
		$count++;
	}
	foreach ( $node as $value ) {
		if ( is_array( $value ) ) {
			$count += alipasandi_schema_local_entity_count( $value );
		}
	}
	return $count;
}

/** Identify only the two release-gated frontend contexts. */
function alipasandi_rank_math_observation_context() {
	if ( is_front_page() ) {
		return 'front';
	}
	if ( is_page( 'contact' ) ) {
		return 'contact';
	}
	return '';
}

/** Create a short-lived signed marker for an explicit internal release probe. */
function alipasandi_release_probe_token() {
	$timestamp = time();
	return $timestamp . ':' . hash_hmac( 'sha256', (string) $timestamp, wp_salt( 'auth' ) );
}

/** Random public traffic cannot create or refresh release evidence. */
function alipasandi_release_probe_request_is_valid() {
	$token = isset( $_SERVER['HTTP_X_ALIPASANDI_RELEASE_PROBE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_ALIPASANDI_RELEASE_PROBE'] ) ) : '';
	if ( 1 !== preg_match( '/^([0-9]{10}):([a-f0-9]{64})$/', $token, $match ) ) {
		return false;
	}
	$timestamp = (int) $match[1];
	if ( abs( time() - $timestamp ) > 120 ) {
		return false;
	}
	return hash_equals( hash_hmac( 'sha256', (string) $timestamp, wp_salt( 'auth' ) ), $match[2] );
}

/** Capture the final Rank Math graph after the central NAP filter has run. */
function alipasandi_rank_math_observe_final( $data ) {
	$context = alipasandi_rank_math_observation_context();
	if ( '' === $context || ! is_array( $data ) || ! alipasandi_release_probe_request_is_valid() ) {
		return $data;
	}
	$canonical = function_exists( 'alipasandi_canonicalize_snapshot' ) ? alipasandi_canonicalize_snapshot( $data ) : $data;
	$observations = get_option( 'alipasandi_rank_math_observations_v1', array() );
	$observations = is_array( $observations ) ? $observations : array();
	$observations[ $context ] = array(
		'context'            => $context,
		'explicit_probe'     => true,
		'observed_at_utc'    => gmdate( 'c' ),
		'rank_math_version'  => defined( 'RANK_MATH_VERSION' ) ? (string) RANK_MATH_VERSION : '',
		'local_entity_count' => alipasandi_schema_local_entity_count( $data ),
		'graph_sha256'       => hash( 'sha256', wp_json_encode( $canonical, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ),
		'nap_sha256'         => function_exists( 'alipasandi_nap_surface_hash' ) ? alipasandi_nap_surface_hash() : '',
		'owner'              => function_exists( 'alipasandi_sanitize_local_schema_owner' ) ? alipasandi_sanitize_local_schema_owner( alipasandi_clinic_option( 'local_schema_owner' ) ) : 'disabled',
	);
	update_option( 'alipasandi_rank_math_observations_v1', $observations, false );
	return $data;
}
add_filter( 'rank_math/json_ld', 'alipasandi_rank_math_observe_final', 999 );

/** Execute one explicit blocking frontend probe. */
function alipasandi_operational_probe_url( $url ) {
	$probe_url = add_query_arg( '_alipasandi_release_probe', rawurlencode( wp_generate_uuid4() ), $url );
	$response = wp_remote_get(
		$probe_url,
		array(
			'timeout'     => 15,
			'redirection' => 3,
			'blocking'    => true,
			'headers'     => array( 'User-Agent'=>'Alipasandi-Release-Probe/1.3.4', 'X-Alipasandi-Release-Probe'=>alipasandi_release_probe_token() ),
		)
	);
	if ( is_wp_error( $response ) ) {
		return array( 'pass'=>false, 'code'=>'request_error', 'error'=>$response->get_error_message(), 'url'=>$url );
	}
	$code = (int) wp_remote_retrieve_response_code( $response );
	return array(
		'pass' => $code >= 200 && $code < 400,
		'code' => $code >= 200 && $code < 400 ? 'http_ok' : 'http_error',
		'http_status' => $code,
		'url' => $url,
		'body' => (string) wp_remote_retrieve_body( $response ),
	);
}

/**
 * Detect the exact blank-homepage/deployment-drift regression seen on the
 * public site before this release. This inspects returned HTML, not template
 * source, so cache/CDN/OPcache and partial-deploy failures are visible too.
 */
function alipasandi_frontend_render_contract_health( $probe_results ) {
	$issues = array();
	$front = isset( $probe_results['front'] ) && is_array( $probe_results['front'] ) ? $probe_results['front'] : array();
	$contact = isset( $probe_results['contact'] ) && is_array( $probe_results['contact'] ) ? $probe_results['contact'] : array();
	$front_body = ! empty( $front['pass'] ) ? (string) ( $front['body'] ?? '' ) : '';
	$contact_body = ! empty( $contact['pass'] ) ? (string) ( $contact['body'] ?? '' ) : '';
	$card_count = 0;
	$managed_count = 0;

	if ( '' === $front_body ) {
		$issues[] = 'front_http_unavailable';
	} else {
		if ( ! preg_match( '/\bid=["\']main-content["\']/', $front_body ) ) { $issues[] = 'front_missing_main_target'; }
		if ( ! preg_match( '/\bclass=["\'][^"\']*\bhome-hero\b[^"\']*["\']/', $front_body ) ) { $issues[] = 'front_missing_home_hero'; }
		$card_count = preg_match_all( '/\bclass=["\'][^"\']*\bservice-card\b[^"\']*["\']/', $front_body );
		if ( function_exists( 'alipasandi_service_registry' ) ) {
			foreach ( alipasandi_service_registry() as $item ) {
				if ( ! empty( $item['content_managed'] ) && ! empty( $item['page_slug'] ) ) { $managed_count++; }
			}
		}
		if ( $managed_count < 1 || $card_count < $managed_count ) {
			$issues[] = 'front_service_card_count:' . (int) $card_count . '/' . (int) $managed_count;
		}
		if ( preg_match_all( '/<img\b/i', $front_body ) < 2 ) { $issues[] = 'front_images_missing'; }

		$decoded = html_entity_decode( $front_body, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		foreach ( array( 'assets/css/theme.css', 'assets/js/theme.js' ) as $asset ) {
			$pattern = '/' . preg_quote( $asset, '/' ) . '[^"\']*[?&]ver=1\.4\.29(?:[&#"\']|$)/';
			if ( ! preg_match( $pattern, $decoded ) ) { $issues[] = 'front_asset_version_mismatch:' . $asset; }
		}
	}
	if ( '' === $contact_body ) {
		$issues[] = 'contact_http_unavailable';
	} elseif ( ! preg_match( '/\bid=["\']main-content["\']/', $contact_body ) ) {
		$issues[] = 'contact_missing_main_target';
	}

	return empty( $issues )
		? alipasandi_health_result( 'pass', 'frontend_render_contract_ok', 'Returned Front and Contact HTML match the 1.4.29 render contract.', array( 'service_card_count'=>(int)$card_count, 'managed_service_count'=>(int)$managed_count ) )
		: alipasandi_health_result( 'critical', 'frontend_render_contract_failed', 'Returned HTML is blank, incomplete, stale or from the wrong artifact.', array( 'issues'=>$issues ) );
}

/** Test whether configured display copy contains every structured address component. */
function alipasandi_display_address_consistency() {
	$display = trim( (string) alipasandi_clinic_option( 'clinic_address' ) );
	if ( '' === $display ) {
		return array( 'pass'=>true, 'code'=>'display_address_composed' );
	}
	$missing = array();
	foreach ( array( 'clinic_street', 'clinic_city', 'clinic_region' ) as $key ) {
		$value = trim( (string) alipasandi_clinic_option( $key ) );
		$found = function_exists( 'mb_stripos' ) ? false !== mb_stripos( $display, $value ) : false !== stripos( $display, $value );
		if ( '' !== $value && ! $found ) {
			$missing[] = $key;
		}
	}
	return array( 'pass'=>empty( $missing ), 'code'=>empty( $missing )?'display_address_aligned':'display_address_drift', 'missing_components'=>$missing );
}

/** Evaluate final Rank Math observations for both explicit contexts. */
function alipasandi_rank_math_observation_health() {
	$owner = alipasandi_sanitize_local_schema_owner( alipasandi_clinic_option( 'local_schema_owner' ) );
	$active = defined( 'RANK_MATH_VERSION' );
	if ( ! $active ) {
		return alipasandi_health_result( 'critical', 'rank_math_unavailable', 'Rank Math is the production SEO owner and is not active.', array( 'local_schema_owner'=>$owner ) );
	}
	$observations = get_option( 'alipasandi_rank_math_observations_v1', array() );
	$issues = array();
	$now = time();
	foreach ( array( 'front', 'contact' ) as $context ) {
		$observation = isset( $observations[ $context ] ) && is_array( $observations[ $context ] ) ? $observations[ $context ] : array();
		$observed = ! empty( $observation['observed_at_utc'] ) ? strtotime( $observation['observed_at_utc'] ) : 0;
		if ( ! $observed || $now - $observed > 900 ) {
			$issues[] = $context . ':missing_or_stale';
			continue;
		}
		if ( (string) RANK_MATH_VERSION !== (string) ( $observation['rank_math_version'] ?? '' ) ) {
			$issues[] = $context . ':version_changed';
		}
		if ( empty( $observation['explicit_probe'] ) ) {
			$issues[] = $context . ':not_explicit_probe';
		}
		$count = (int) ( $observation['local_entity_count'] ?? -1 );
		if ( ( 'disabled' === $owner && 0 !== $count ) || ( 'rank_math' === $owner && 1 !== $count ) ) {
			$issues[] = $context . ':owner_contract_failed';
		}
		if ( (string) ( $observation['nap_sha256'] ?? '' ) !== alipasandi_nap_surface_hash() ) {
			$issues[] = $context . ':nap_state_changed';
		}
	}
	return empty( $issues )
		? alipasandi_health_result( 'pass', 'rank_math_observations_current', 'Final Rank Math graphs match the selected Local Schema owner.', array( 'observations'=>$observations ) )
		: alipasandi_health_result( 'critical', 'rank_math_observation_failed', 'Final Rank Math graph evidence is missing, stale or inconsistent.', array( 'issues'=>$issues, 'observations'=>$observations ) );
}

/** One integrated, machine-readable production gate. */
function alipasandi_operational_health_report( $probe = false ) {
	$probe_results = array();
	if ( $probe ) {
		$probe_results['front'] = alipasandi_operational_probe_url( home_url( '/' ) );
		$contact_url = function_exists( 'alipasandi_page_url' ) ? alipasandi_page_url( 'contact' ) : home_url( '/contact/' );
		$probe_results['contact'] = alipasandi_operational_probe_url( $contact_url );
	}

	$theme = wp_get_theme();
	$artifact_pass = defined( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION' )
		&& '1.3.4' === ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION
		&& '1.4.29' === (string) $theme->get( 'Version' );
	$checks = array();
	$checks['artifacts'] = $artifact_pass
		? alipasandi_health_result( 'pass', 'artifact_versions_match', 'Theme 1.4.29 and Plugin 1.3.4 are active.' )
		: alipasandi_health_result( 'critical', 'artifact_version_mismatch', 'The active Theme/Plugin versions do not match this release.', array( 'theme_version'=>$theme->get( 'Version' ), 'plugin_version'=>defined( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION' )?ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION:'' ) );

	$missing = Alipasandi_NAP_Helper::missing();
	$checks['nap'] = empty( $missing )
		? alipasandi_health_result( 'pass', 'nap_complete', 'Canonical structured NAP is complete and valid.' )
		: alipasandi_health_result( 'critical', 'nap_incomplete', 'Canonical structured NAP is incomplete or invalid.', array( 'missing_or_invalid'=>$missing ) );

	$address_sync = alipasandi_display_address_consistency();
	$checks['display_address'] = ! empty( $address_sync['pass'] )
		? alipasandi_health_result( 'pass', $address_sync['code'], 'Display and structured address inputs are aligned.' )
		: alipasandi_health_result( 'critical', $address_sync['code'], 'Display address conflicts with structured address inputs.', $address_sync );

	$phone = alipasandi_phone_href();
	$checks['direct_phone'] = alipasandi_valid_e164( $phone )
		? alipasandi_health_result( 'pass', 'direct_phone_valid', 'The direct phone route is strict E.164.', array( 'phone_e164'=>$phone ) )
		: alipasandi_health_result( 'critical', 'direct_phone_invalid', 'The direct phone route is unavailable or invalid.' );

	$interop = alipasandi_validate_booking_hours_interoperability();
	$checks['booking_hours'] = alipasandi_health_result( $interop['status'], $interop['code'], ! empty( $interop['pass'] )?'Booking slots and opening hours interoperate.':'Booking slots and opening hours do not interoperate.', $interop );

	foreach ( array( 'contact', 'appointment' ) as $channel ) {
		$readiness = alipasandi_form_readiness( $channel );
		$checks[ $channel . '_form' ] = ! empty( $readiness['ready'] )
			? alipasandi_health_result( 'pass', $channel . '_form_ready', ucfirst( $channel ) . ' form is ready.', $readiness )
			: alipasandi_health_result( 'critical', $channel . '_form_unavailable', ucfirst( $channel ) . ' form is fail-closed.', $readiness );
	}

	$freeze = Alipasandi_NAP_Freeze::verify();
	$checks['nap_freeze'] = ! empty( $freeze['pass'] )
		? alipasandi_health_result( 'pass', 'freeze_intact', 'Owner-approved artifact-bound NAP freeze is intact.', $freeze )
		: alipasandi_health_result( 'critical', $freeze['code'], 'Owner-approved artifact-bound NAP freeze is absent or invalidated.', $freeze );
	$checks['rank_math'] = alipasandi_rank_math_observation_health();

	if ( $probe ) {
		$probe_ok = ! empty( $probe_results['front']['pass'] ) && ! empty( $probe_results['contact']['pass'] );
		$probe_summary = $probe_results;
		foreach ( $probe_summary as &$probe_item ) { unset( $probe_item['body'] ); }
		unset( $probe_item );
		$checks['loopback'] = $probe_ok
			? alipasandi_health_result( 'pass', 'explicit_probes_ok', 'Blocking Front and Contact probes returned successfully.', array( 'probes'=>$probe_summary ) )
			: alipasandi_health_result( 'critical', 'explicit_probe_failed', 'A blocking Front or Contact probe failed.', array( 'probes'=>$probe_summary ) );
		$checks['frontend_render'] = alipasandi_frontend_render_contract_health( $probe_results );
	} else {
		$checks['loopback'] = alipasandi_health_result( 'warning', 'probe_not_run', 'Run the CLI health command with --probe for fresh loopback and Rank Math evidence.' );
		$checks['frontend_render'] = alipasandi_health_result( 'warning', 'render_probe_not_run', 'Run the CLI health command with --probe to verify returned 1.4.29 HTML and assets.' );
	}

	if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
		$checks['wp_cron'] = alipasandi_health_result( 'warning', 'wp_cron_disabled', 'Built-in WP-Cron is disabled; verify the external scheduler.' );
	} else {
		$events = function_exists( '_get_cron_array' ) ? _get_cron_array() : array();
		$checks['wp_cron'] = empty( $events )
			? alipasandi_health_result( 'warning', 'no_cron_events', 'No WP-Cron events are currently registered.' )
			: alipasandi_health_result( 'pass', 'wp_cron_events_present', 'WP-Cron events are registered.' );
	}

	$debug_display = defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;
	$checks['debug_display'] = $debug_display
		? alipasandi_health_result( 'critical', 'debug_display_enabled', 'WP_DEBUG_DISPLAY must be disabled before production.' )
		: alipasandi_health_result( 'pass', 'debug_display_disabled', 'WP_DEBUG_DISPLAY is disabled.' );

	$critical = 0;
	$warnings = 0;
	foreach ( $checks as $check ) {
		if ( 'critical' === $check['status'] ) { $critical++; }
		if ( 'warning' === $check['status'] ) { $warnings++; }
	}
	return array(
		'pass'            => 0 === $critical,
		'overall_status'  => $critical ? 'critical' : ( $warnings ? 'warning' : 'pass' ),
		'generated_at_utc'=> gmdate( 'c' ),
		'probe_requested' => (bool) $probe,
		'critical_count'  => $critical,
		'warning_count'   => $warnings,
		'checks'          => $checks,
	);
}

/** Extract an exact rendered NAP marker from a response body. */
function alipasandi_extract_nap_marker( $body, $surface ) {
	$pattern = '/data-alipasandi-nap-surface=["\']' . preg_quote( sanitize_key( $surface ), '/' ) . '["\'][^>]*data-alipasandi-nap-sha256=["\']([a-f0-9]{64})["\']/';
	return preg_match( $pattern, (string) $body, $match ) ? $match[1] : '';
}

/** Compare actual Home, Contact, Footer, email and observed JSON-LD NAP states. */
function alipasandi_nap_consistency_report( $probe = false ) {
	$expected = alipasandi_nap_surface_hash();
	$mail_block = function_exists( 'alipasandi_mail_nap_block' ) ? alipasandi_mail_nap_block() : '';
	$mail_fields = alipasandi_nap_surface_fields();
	$mail_matches = '' !== $mail_block;
	foreach ( $mail_fields as $value ) {
		if ( '' === $value || false === strpos( $mail_block, $value ) ) { $mail_matches = false; }
	}
	$surfaces = array( 'email'=>$mail_matches ? $expected : '' );
	$requests = array();
	if ( $probe ) {
		$requests['front'] = alipasandi_operational_probe_url( home_url( '/' ) );
		$requests['contact'] = alipasandi_operational_probe_url( function_exists( 'alipasandi_page_url' ) ? alipasandi_page_url( 'contact' ) : home_url( '/contact/' ) );
		if ( ! empty( $requests['front']['pass'] ) ) {
			$surfaces['home'] = alipasandi_extract_nap_marker( $requests['front']['body'], 'home' );
			$surfaces['footer_front'] = alipasandi_extract_nap_marker( $requests['front']['body'], 'footer' );
		}
		if ( ! empty( $requests['contact']['pass'] ) ) {
			$surfaces['contact'] = alipasandi_extract_nap_marker( $requests['contact']['body'], 'contact' );
			$surfaces['footer_contact'] = alipasandi_extract_nap_marker( $requests['contact']['body'], 'footer' );
		}
	}
	$observations = get_option( 'alipasandi_rank_math_observations_v1', array() );
	foreach ( array( 'front', 'contact' ) as $context ) {
		if ( isset( $observations[ $context ]['nap_sha256'] ) ) {
			$surfaces['jsonld_' . $context] = $observations[ $context ]['nap_sha256'];
		}
	}
	$required = $probe ? array( 'home', 'contact', 'footer_front', 'footer_contact', 'email' ) : array( 'email' );
	if ( defined( 'RANK_MATH_VERSION' ) ) {
		$required[] = 'jsonld_front';
		$required[] = 'jsonld_contact';
	}
	$issues = array();
	foreach ( $required as $surface ) {
		if ( ! isset( $surfaces[ $surface ] ) || ! hash_equals( $expected, (string) $surfaces[ $surface ] ) ) {
			$issues[] = $surface;
		}
	}
	return array( 'pass'=>empty( $issues ), 'expected_sha256'=>$expected, 'surfaces'=>$surfaces, 'missing_or_mismatched'=>$issues, 'probe_requested'=>(bool)$probe );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'alipasandi operations health', function ( $args, $assoc_args ) {
		$report = alipasandi_operational_health_report( ! empty( $assoc_args['probe'] ) );
		WP_CLI::line( wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
		if ( empty( $report['pass'] ) ) { WP_CLI::halt( 1 ); }
	} );
	WP_CLI::add_command( 'alipasandi nap consistency', function ( $args, $assoc_args ) {
		$report = alipasandi_nap_consistency_report( ! empty( $assoc_args['probe'] ) );
		WP_CLI::line( wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
		if ( empty( $report['pass'] ) ) { WP_CLI::halt( 1 ); }
	} );
}
