<?php
/** Runtime compatibility contract. Data is never deleted on deactivate/uninstall. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'ALIPASANDI_SERVICE_SUPPORTED_THEME_MIN', '1.4.24' );
define( 'ALIPASANDI_SERVICE_SUPPORTED_THEME_MAX_API', '1.x' );

if ( ! function_exists( 'alipasandi_log' ) ) {
	function alipasandi_log( $message, $context = array() ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}
		$details = is_array( $context ) && $context ? ' ' . wp_json_encode( $context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : '';
		error_log( '[Alipasandi] ' . sanitize_text_field( $message ) . $details ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}

function alipasandi_plugin_theme_compatibility() {
	$theme = wp_get_theme();
	if ( 'alipasandi-clinic' !== $theme->get_stylesheet() ) {
		return 'other_theme';
	}
	return version_compare( $theme->get( 'Version' ), ALIPASANDI_SERVICE_SUPPORTED_THEME_MIN, '>=' ) ? 'compatible' : 'theme_outdated';
}

function alipasandi_plugin_compatibility_notice() {
	if ( ! current_user_can( 'update_themes' ) || 'theme_outdated' !== alipasandi_plugin_theme_compatibility() ) {
		return;
	}
	echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Alipasandi Service Content 1.2.0 requires Alipasandi Clinic theme 1.4.24 or newer for the supported integration contract.', 'alipasandi-service-content' ) . '</strong></p></div>';
}
add_action( 'admin_notices', 'alipasandi_plugin_compatibility_notice' );

/** No uninstall hook is registered by design: service meta, keys and NAP survive deletion. */
