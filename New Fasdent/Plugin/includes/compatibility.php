<?php
/** Runtime compatibility contract. Data is never deleted on deactivate/uninstall. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'ALIPASANDI_SERVICE_SUPPORTED_THEME_MIN', '1.4.24' );
define( 'ALIPASANDI_SERVICE_PRODUCTION_THEME_MIN', '1.4.28' );
define( 'ALIPASANDI_SERVICE_SUPPORTED_THEME_MAX_API', '1.x' );

function alipasandi_plugin_theme_compatibility() {
	$theme = wp_get_theme();
	if ( 'alipasandi-clinic' !== $theme->get_stylesheet() ) {
		return 'other_theme';
	}
	$version = $theme->get( 'Version' );
	if ( version_compare( $version, ALIPASANDI_SERVICE_SUPPORTED_THEME_MIN, '<' ) ) {
		return 'theme_outdated';
	}
	if ( version_compare( $version, ALIPASANDI_SERVICE_PRODUCTION_THEME_MIN, '<' ) ) {
		return 'compatibility_only';
	}
	return 'production_pair';
}

function alipasandi_plugin_compatibility_notice() {
	if ( ! current_user_can( 'update_themes' ) ) {
		return;
	}
	$state = alipasandi_plugin_theme_compatibility();
	if ( 'theme_outdated' === $state ) {
		echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Alipasandi Service Content 1.3.3 requires Alipasandi Clinic theme 1.4.24 or newer to load safely. Production pairing requires theme 1.4.28.', 'alipasandi-service-content' ) . '</strong></p></div>';
	} elseif ( 'compatibility_only' === $state ) {
		echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'Theme 1.4.24 is a superseded compatibility build. Use Theme 1.4.28 with Plugin 1.3.3 for the production-candidate contract.', 'alipasandi-service-content' ) . '</strong></p></div>';
	}
}
add_action( 'admin_notices', 'alipasandi_plugin_compatibility_notice' );

/** No uninstall hook is registered by design: service meta, keys and NAP survive deletion. */
