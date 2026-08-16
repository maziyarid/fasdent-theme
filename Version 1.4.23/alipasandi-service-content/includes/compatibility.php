<?php
/** Runtime compatibility contract. Data is never deleted on deactivate/uninstall. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'ALIPASANDI_SERVICE_SUPPORTED_THEME_MIN', '1.4.22' );
define( 'ALIPASANDI_SERVICE_SUPPORTED_THEME_MAX_API', '1.x' );

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
	echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Alipasandi Service Content 1.1.0 requires Alipasandi Clinic theme 1.4.22 or newer for the supported integration contract.', 'alipasandi-service-content' ) . '</strong></p></div>';
}
add_action( 'admin_notices', 'alipasandi_plugin_compatibility_notice' );

/** No uninstall hook is registered by design: service meta, keys and NAP survive deletion. */
