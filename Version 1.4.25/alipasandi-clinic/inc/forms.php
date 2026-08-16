<?php
/**
 * Theme-side form compatibility contract.
 *
 * There is intentionally NO form processing, validation, rate limiting or mail
 * logic here. When the companion plugin is unavailable/outdated the templates
 * render an unavailable state and a direct contact CTA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'alipasandi_forms_available' ) ) {
	function alipasandi_forms_available() {
		return function_exists( 'alipasandi_service_plugin_ready' ) && alipasandi_service_plugin_ready();
	}
}
