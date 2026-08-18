<?php
/**
 * PHPUnit bootstrap for the canonical Version 1.5 theme and plugin modules.
 *
 * The suite is a plain unit-test harness: real WordPress sanitization, escaping and
 * hook code is loaded from a WordPress tarball, while options, post meta, mail and
 * nonces are served by the in-memory environment in wp-environment.php. No database,
 * no wp-config.php and no WordPress test-suite install are required.
 *
 * Run tests/bin/install-wp-core.sh once, or point WP_CORE_DIR at an existing
 * WordPress checkout.
 */

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	fwrite( STDERR, "Run `composer install` inside VERSION-1.5/tests before running the suite.\n" );
	exit( 1 );
}
require_once __DIR__ . '/vendor/autoload.php';

define( 'ALIPASANDI_TESTS_DIR', __DIR__ );
define( 'ALIPASANDI_V15_DIR', dirname( __DIR__ ) );
define( 'ALIPASANDI_THEME_DIR', ALIPASANDI_V15_DIR . '/Theme' );
define( 'ALIPASANDI_PLUGIN_DIR', ALIPASANDI_V15_DIR . '/Plugin' );

$wp_core_dir = getenv( 'WP_CORE_DIR' );
if ( ! $wp_core_dir ) {
	$wp_core_dir = __DIR__ . '/.wp-core';
}
$wp_core_dir = rtrim( $wp_core_dir, '/' ) . '/';

if ( ! file_exists( $wp_core_dir . 'wp-includes/kses.php' ) ) {
	fwrite(
		STDERR,
		"WordPress core files were not found at {$wp_core_dir}.\n"
		. "Run tests/bin/install-wp-core.sh (or set WP_CORE_DIR) before running the suite.\n"
	);
	exit( 1 );
}

// The modules under test guard themselves with ABSPATH; the theme uses it for includes.
define( 'ABSPATH', $wp_core_dir );
define( 'WPINC', 'wp-includes' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS );

$GLOBALS['wp_filter']         = array();
$GLOBALS['wp_actions']        = array();
$GLOBALS['wp_current_filter'] = array();

// Declared before core so core never redeclares the stateful layer.
require_once __DIR__ . '/wp-environment.php';

/*
 * Only the pure-PHP core files are loaded. wp-includes/functions.php is skipped on
 * purpose: it requires the database-backed option layer, and the helpers the modules
 * need from it are provided by wp-environment.php instead.
 */
require_once ABSPATH . 'wp-includes/class-wp-post.php';
require_once ABSPATH . 'wp-includes/compat.php';
require_once ABSPATH . 'wp-includes/plugin.php';
require_once ABSPATH . 'wp-includes/formatting.php';
require_once ABSPATH . 'wp-includes/kses.php';

// Constants the plugin bootstrap file normally defines.
define( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION', '1.2.1' );
define( 'ALIPASANDI_SERVICE_CONTENT_API_VERSION', '1.2' );

// Canonical modules under test.
require_once ALIPASANDI_PLUGIN_DIR . '/includes/service-content.php';
require_once ALIPASANDI_PLUGIN_DIR . '/includes/site-settings.php';
require_once ALIPASANDI_PLUGIN_DIR . '/includes/forms.php';
require_once ALIPASANDI_PLUGIN_DIR . '/includes/compatibility.php';
require_once ALIPASANDI_THEME_DIR . '/inc/icons.php';
require_once ALIPASANDI_THEME_DIR . '/inc/images.php';
require_once ALIPASANDI_THEME_DIR . '/inc/seo.php';
// Read-only legacy fallback the plugin migrates from.
require_once ALIPASANDI_THEME_DIR . '/inc/service-data.php';

/*
 * Theme/inc/service-meta.php and Theme/inc/forms.php are the pre-plugin copies of
 * the plugin modules and declare the same function names, so they cannot be loaded
 * alongside the plugin in one process. The plugin owns that behaviour in 1.5.
 */

require_once __DIR__ . '/includes/Alipasandi_TestCase.php';
