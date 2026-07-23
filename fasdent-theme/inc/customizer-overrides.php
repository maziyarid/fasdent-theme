<?php
/**
 * Override legacy placeholder defaults after theme load.
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_fix_placeholder_mods(): void {
	$em = get_theme_mod( 'fasdent_emergency_phone', '' );
	if ( ! $em || false !== strpos( $em, 'XXXX' ) || false !== strpos( $em, 'xxxx' ) ) {
		set_theme_mod( 'fasdent_emergency_phone', get_theme_mod( 'fasdent_phone', '09201441469' ) );
	}
}
add_action( 'after_setup_theme', 'fasdent_fix_placeholder_mods', 20 );

function fasdent_filter_emergency_phone( $value ) {
	if ( ! $value || false !== strpos( (string) $value, 'XXXX' ) ) {
		return get_theme_mod( 'fasdent_phone', '09201441469' );
	}
	return $value;
}
add_filter( 'theme_mod_fasdent_emergency_phone', 'fasdent_filter_emergency_phone' );
