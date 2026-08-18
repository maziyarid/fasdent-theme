<?php
/** Shared base class: isolates in-memory WordPress state between tests. */

use PHPUnit\Framework\TestCase;

abstract class Alipasandi_TestCase extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		WP_Test_State::reset();
		unset( $GLOBALS['alipasandi_test_theme'] );
	}

	protected function tearDown(): void {
		WP_Test_State::reset();
		unset( $GLOBALS['alipasandi_test_theme'] );
		parent::tearDown();
	}

	/** Store an operational option using the plugin prefix. */
	protected function set_clinic_option( string $key, $value ): void {
		WP_Test_State::$options[ 'alipasandi_' . $key ] = $value;
	}
}
