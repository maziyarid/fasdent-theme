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
	$appointment_page = get_page_by_path( 'appointments' );
	if ( $appointment_page instanceof WP_Post && preg_match( '/رزرو\s+نوبت/u', get_the_title( $appointment_page ) ) ) { $issues[] = 'appointment_page_title_requires_request_wording'; }

	$theme = wp_get_theme();
	if ( 'alipasandi-clinic' === $theme->get_stylesheet() && version_compare( $theme->get( 'Version' ), '1.4.26', '<' ) ) { $issues[] = 'theme_version_mismatch'; }
	$legacy_seen = get_transient( 'alipasandi_legacy_fallback_seen' );
	if ( $legacy_seen ) { $warnings[] = 'legacy_service_fallback_seen'; }
	if ( ! defined( 'RANK_MATH_VERSION' ) ) { $issues[] = 'rank_math_inactive'; }

	return array(
		'pass'      => empty( $issues ),
		'issues'    => array_values( array_unique( $issues ) ),
		'warnings'  => array_values( array_unique( $warnings ) ),
		'timezone'  => wp_timezone_string(),
		'home_host' => wp_parse_url( home_url( '/' ), PHP_URL_HOST ),
		'booking_horizon_days' => function_exists( 'alipasandi_booking_horizon_days' ) ? alipasandi_booking_horizon_days() : null,
		'registry_issues' => $registry_issues,
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
