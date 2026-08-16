<?php
/**
 * Plugin Name: Alipasandi Service Content
 * Description: Portable service content, NAP, form processing, migration, revisions, export and operational health checks.
 * Version: 1.3.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: Alipasandi Clinic
 * Text Domain: alipasandi-service-content
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION', '1.3.0' );
define( 'ALIPASANDI_SERVICE_CONTENT_API_VERSION', '1.3' );
define( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_FILE', __FILE__ );

require_once __DIR__ . '/includes/compatibility.php';

/**
 * Activation-sandbox collision safety.
 *
 * During activation WordPress may already have the active theme's emergency
 * compatibility functions loaded. In that one request, overlapping modules
 * are skipped. On the following normal request plugins load before themes and
 * this plugin becomes the sole business-logic owner.
 */
if ( ! function_exists( 'alipasandi_service_registry' ) && ! function_exists( 'alipasandi_service_keys' ) ) {
	require_once __DIR__ . '/includes/service-registry.php';
}
if ( ! function_exists( 'alipasandi_clinic_option' ) ) {
	require_once __DIR__ . '/includes/site-settings.php';
}
if ( ! function_exists( 'alipasandi_valid_e164' ) ) {
	require_once __DIR__ . '/includes/validators.php';
}
if ( ! function_exists( 'alipasandi_get_service' ) && ! function_exists( 'alipasandi_register_service_meta' ) ) {
	require_once __DIR__ . '/includes/service-content.php';
}
if ( ! function_exists( 'alipasandi_handle_contact' ) ) {
	require_once __DIR__ . '/includes/forms.php';
}
if ( ! function_exists( 'alipasandi_operational_health_report' ) ) {
	require_once __DIR__ . '/includes/health-checks.php';
}

/** Load translations when catalogs are supplied. */
function alipasandi_service_content_load_textdomain() {
	load_plugin_textdomain( 'alipasandi-service-content', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'alipasandi_service_content_load_textdomain' );

function alipasandi_service_content_activate() {
	global $wpdb;
	foreach ( array( 'alipasandi_service_meta_status_v1', 'alipasandi_service_meta_log_v1', 'alipasandi_nap_migrated_v2' ) as $option_name ) {
		$wpdb->update( $wpdb->options, array( 'autoload' => 'no' ), array( 'option_name' => $option_name ), array( '%s' ), array( '%s' ) );
	}
}
register_activation_hook( __FILE__, 'alipasandi_service_content_activate' );
