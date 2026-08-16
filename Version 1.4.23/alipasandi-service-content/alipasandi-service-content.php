<?php
/**
 * Plugin Name: Alipasandi Service Content
 * Description: Portable service-page content, editing, migration, revisions, export and health checks.
 * Version: 1.1.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: Alipasandi Clinic
 * Text Domain: alipasandi-service-content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION', '1.1.0' );
define( 'ALIPASANDI_SERVICE_CONTENT_API_VERSION', '1.1' );
define( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_FILE', __FILE__ );

require_once __DIR__ . '/includes/compatibility.php';

/*
 * During activation WordPress has already loaded the active theme. Theme
 * 1.4.21 may therefore have loaded its compatibility module and declared the
 * legacy service functions before plugin_sandbox_scrape() includes this file.
 * Skipping the duplicate include makes that activation request safe. On the
 * following normal request plugins load before the theme, so this plugin owns
 * the functions and the theme does not load its compatibility module.
 */
if ( ! function_exists( 'alipasandi_service_keys' ) ) {
	require_once __DIR__ . '/includes/service-content.php';
}
if ( ! function_exists( 'alipasandi_clinic_option' ) ) {
	require_once __DIR__ . '/includes/site-settings.php';
}
if ( ! function_exists( 'alipasandi_handle_contact' ) ) {
	require_once __DIR__ . '/includes/forms.php';
}

function alipasandi_service_content_activate() {
	global $wpdb;
	foreach ( array( 'alipasandi_service_meta_status_v1', 'alipasandi_service_meta_log_v1', 'alipasandi_nap_migrated' ) as $option_name ) {
		$wpdb->update( $wpdb->options, array( 'autoload' => 'no' ), array( 'option_name' => $option_name ), array( '%s' ), array( '%s' ) );
	}
}
register_activation_hook( __FILE__, 'alipasandi_service_content_activate' );
