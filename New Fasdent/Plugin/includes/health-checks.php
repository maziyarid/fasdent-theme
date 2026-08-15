<?php
/** Production-oriented operational health checks. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function alipasandi_operational_health_report() {
	$issues = array();
	$warnings = array();
	$notify = sanitize_email( alipasandi_clinic_option( 'clinic_notify_email' ) );
	$from = sanitize_email( alipasandi_clinic_option( 'clinic_mail_from' ) );
	$phone = trim( (string) alipasandi_clinic_option( 'clinic_phone' ) );
	$e164 = trim( (string) alipasandi_clinic_option( 'clinic_phone_e164' ) );
	$address = trim( (string) alipasandi_clinic_option( 'clinic_address' ) );
	$country = strtoupper( trim( (string) alipasandi_clinic_option( 'clinic_country' ) ) );
	$lat = trim( (string) alipasandi_clinic_option( 'clinic_geo_lat' ) );
	$lng = trim( (string) alipasandi_clinic_option( 'clinic_geo_lng' ) );
	$city = trim( (string) alipasandi_clinic_option( 'clinic_city' ) );
	$region = trim( (string) alipasandi_clinic_option( 'clinic_region' ) );
	$business = trim( (string) alipasandi_clinic_option( 'clinic_business_name' ) );
	$slots = alipasandi_validate_booking_slots( alipasandi_clinic_option( 'clinic_booking_times' ) );
	$hours = alipasandi_parse_opening_hours( alipasandi_clinic_option( 'clinic_opening_hours' ) );
	$registry_issues = function_exists( 'alipasandi_service_registry_issues' ) ? alipasandi_service_registry_issues() : array( 'registry_api_unavailable' );
	$registry_warnings = function_exists( 'alipasandi_service_registry_warnings' ) ? alipasandi_service_registry_warnings() : array();
	$registry_pages = array();

	if ( ! is_email( $notify ) ) { $issues[] = 'notify_email_missing_or_invalid'; }
	if ( ! is_email( $from ) ) { $issues[] = 'mail_from_missing_or_invalid'; }
	elseif ( ! alipasandi_email_matches_home_domain( $from ) ) { $issues[] = 'mail_from_domain_mismatch'; }
	if ( '' === $phone ) { $issues[] = 'phone_display_missing'; }
	if ( ! alipasandi_valid_e164( $e164 ) ) { $issues[] = 'phone_e164_invalid'; }
	if ( '' === $address ) { $issues[] = 'address_missing'; }
	if ( ! alipasandi_valid_country_code( $country ) ) { $issues[] = 'country_invalid'; }
	if ( '' === $business ) { $issues[] = 'business_name_missing'; }
	if ( '' === $city || 'نوشهر' !== $city ) { $issues[] = 'nap_city_mismatch'; }
	if ( '' === $region ) { $issues[] = 'region_missing'; }
	if ( ( '' === $lat ) xor ( '' === $lng ) ) { $issues[] = 'geo_partial'; }
	elseif ( '' !== $lat && ( ! alipasandi_valid_geo( $lat, 'lat' ) || ! alipasandi_valid_geo( $lng, 'lng' ) ) ) { $issues[] = 'geo_out_of_range'; }
	if ( is_wp_error( $slots ) || '' === trim( is_wp_error( $slots ) ? '' : $slots ) ) { $issues[] = 'booking_times_empty_or_invalid'; }
	if ( ! empty( $hours['errors'] ) ) { $issues[] = 'opening_hours_invalid'; }
	elseif ( '' === trim( (string) alipasandi_clinic_option( 'clinic_opening_hours' ) ) ) { $warnings[] = 'opening_hours_not_configured_schema_omitted'; }
	if ( 'Asia/Tehran' !== wp_timezone_string() ) { $issues[] = 'wordpress_timezone_not_asia_tehran'; }
	if ( ! empty( $registry_issues ) ) { $issues[] = 'service_registry_invalid'; }
	foreach ( $registry_warnings as $registry_warning ) { $warnings[] = 'registry:' . $registry_warning; }

	if ( function_exists( 'alipasandi_service_registry' ) ) {
		$migration = function_exists( 'alipasandi_get_service_migration_status' ) ? alipasandi_get_service_migration_status() : array();
		foreach ( alipasandi_service_registry() as $key => $item ) {
			if ( empty( $item['content_managed'] ) ) { continue; }
			$stable_posts = function_exists( 'alipasandi_service_key_posts' ) ? alipasandi_service_key_posts( $key ) : array();
			$page = function_exists( 'alipasandi_get_service_page' ) ? alipasandi_get_service_page( $key ) : null;
			$registry_pages[ $key ] = array(
				'page_slug'    => $item['page_slug'],
				'page_id'      => $page instanceof WP_Post ? (int) $page->ID : 0,
				'stable_count' => count( $stable_posts ),
			);
			if ( count( $stable_posts ) > 1 ) { $issues[] = 'registry_page_identity_duplicate:' . $key; }
			if ( ! $page instanceof WP_Post ) { $issues[] = 'registry_page_missing:' . $key; }
			if ( ! empty( $migration['completed'] ) && 1 !== count( $stable_posts ) ) { $issues[] = 'registry_stable_identity_missing_after_migration:' . $key; }
		}
	}

	$rank_math_target = get_transient( 'alipasandi_rank_math_local_entity_state' );
	if ( defined( 'RANK_MATH_VERSION' ) ) {
		$rank_state = is_array( $rank_math_target ) ? (string) ( $rank_math_target['state'] ?? '' ) : '';
		$rank_seen = is_array( $rank_math_target ) ? absint( $rank_math_target['observed_at'] ?? 0 ) : 0;
		$rank_version = is_array( $rank_math_target ) ? (string) ( $rank_math_target['rank_math_version'] ?? '' ) : '';
		$rank_fresh = $rank_seen > 0 && ( time() - $rank_seen ) <= ( 15 * MINUTE_IN_SECONDS );
		if ( $rank_version !== (string) RANK_MATH_VERSION ) {
			$issues[] = 'rank_math_local_entity_observation_version_mismatch';
		} elseif ( ! $rank_fresh ) {
			$issues[] = 'rank_math_local_entity_observation_stale_or_missing';
		} elseif ( 'missing' === $rank_state ) {
			$issues[] = 'rank_math_local_entity_target_missing';
		} elseif ( 'matched' !== $rank_state ) {
			$issues[] = 'rank_math_local_entity_target_not_observed';
		}
	}

	// Production architecture requires a static front page. This guard prevents
	// emergency SEO failure-mode rules from accidentally treating the homepage
	// as the Posts index if Reading Settings drift.
	$show_on_front = (string) get_option( 'show_on_front', 'posts' );
	$front_page_id = absint( get_option( 'page_on_front', 0 ) );
	$posts_page_id = absint( get_option( 'page_for_posts', 0 ) );
	$front_page = $front_page_id ? get_post( $front_page_id ) : null;
	$posts_page = $posts_page_id ? get_post( $posts_page_id ) : null;
	if ( 'page' !== $show_on_front ) { $issues[] = 'front_page_mode_not_static'; }
	if ( ! $front_page instanceof WP_Post || 'publish' !== $front_page->post_status || 'page' !== $front_page->post_type ) { $issues[] = 'front_page_missing_or_invalid'; }
	if ( ! $posts_page instanceof WP_Post || 'publish' !== $posts_page->post_status || 'page' !== $posts_page->post_type ) { $issues[] = 'blog_page_missing_or_invalid'; }
	if ( $front_page_id && $front_page_id === $posts_page_id ) { $issues[] = 'front_and_blog_page_must_differ'; }

	$appointment_page = get_page_by_path( 'appointments' );
	if ( $appointment_page instanceof WP_Post && preg_match( '/رزرو\s+نوبت/u', get_the_title( $appointment_page ) ) ) { $issues[] = 'appointment_page_title_requires_request_wording'; }

	$theme = wp_get_theme();
	if ( 'alipasandi-clinic' === $theme->get_stylesheet() && version_compare( $theme->get( 'Version' ), '1.4.28', '<' ) ) { $issues[] = 'theme_version_mismatch'; }
	$legacy_seen = get_transient( 'alipasandi_legacy_fallback_seen' );
	if ( $legacy_seen ) { $warnings[] = 'legacy_service_fallback_seen'; }
	if ( ! defined( 'RANK_MATH_VERSION' ) ) { $issues[] = 'rank_math_inactive'; }

	// Production must never display PHP/WordPress diagnostics to visitors. Logging
	// may remain enabled only as an operational choice with a protected path and
	// retention/PII review documented outside the public web root.
	$environment_type = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';
	$debug_enabled = defined( 'WP_DEBUG' ) && WP_DEBUG;
	$debug_display = defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;
	$debug_log = defined( 'WP_DEBUG_LOG' ) ? WP_DEBUG_LOG : false;
	if ( 'production' === $environment_type && $debug_display ) { $issues[] = 'production_debug_display_enabled'; }
	if ( 'production' === $environment_type && $debug_enabled ) { $warnings[] = 'production_debug_enabled_review_required'; }
	if ( 'production' === $environment_type && $debug_log ) { $warnings[] = 'production_debug_log_security_retention_review_required'; }

	return array(
		'pass'      => empty( $issues ),
		'issues'    => array_values( array_unique( $issues ) ),
		'warnings'  => array_values( array_unique( $warnings ) ),
		'timezone'  => wp_timezone_string(),
		'home_host' => wp_parse_url( home_url( '/' ), PHP_URL_HOST ),
		'booking_horizon_days' => function_exists( 'alipasandi_booking_horizon_days' ) ? alipasandi_booking_horizon_days() : null,
		'booking_min_lead_minutes' => function_exists( 'alipasandi_booking_min_lead_minutes' ) ? alipasandi_booking_min_lead_minutes() : null,
		'registry_issues' => $registry_issues,
		'registry_warnings' => $registry_warnings,
		'registry_pages' => $registry_pages,
		'rank_math_local_entity_state' => $rank_math_target,
		'front_page_config' => array(
			'show_on_front' => $show_on_front,
			'page_on_front' => $front_page_id,
			'page_for_posts' => $posts_page_id,
		),
		'environment' => array(
			'type' => $environment_type,
			'wp_debug' => $debug_enabled,
			'wp_debug_display' => $debug_display,
			'wp_debug_log' => $debug_log,
		),
	);
}

function alipasandi_operational_site_health_tests( $tests ) {
	$tests['direct']['alipasandi_operational_health'] = array(
		'label' => 'Alipasandi production operational health',
		'test'  => function () {
			$report = alipasandi_operational_health_report();
			return array(
				'label'       => ! empty( $report['pass'] ) ? 'Operational configuration آماده است' : 'Operational configuration نیاز به اقدام دارد',
				'status'      => ! empty( $report['pass'] ) ? ( empty( $report['warnings'] ) ? 'good' : 'recommended' ) : 'critical',
				'badge'       => array( 'label'=>'Alipasandi', 'color'=>'blue' ),
				'description' => '<pre>' . esc_html( wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) . '</pre>',
				'test'        => 'alipasandi_operational_health',
			);
		},
	);
	return $tests;
}
add_filter( 'site_status_tests', 'alipasandi_operational_site_health_tests' );
