<?php
/**
 * Elementor compatibility — Fasdent
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * پشتیبانی از Elementor Theme Builder.
 */
function fasdent_elementor_support(): void {
	add_theme_support( 'elementor' );
	add_theme_support( 'elementor-pro' );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 220, 'flex-height' => true, 'flex-width' => true ) );
}
add_action( 'after_setup_theme', 'fasdent_elementor_support' );

/**
 * ثبت مکان‌های Theme Builder.
 */
function fasdent_register_elementor_locations( array $locations ): array {
	$locations['header'] = __( 'هدر', 'fasdent' );
	$locations['footer'] = __( 'فوتر', 'fasdent' );
	$locations['single'] = __( 'تک‌پست', 'fasdent' );
	$locations['archive'] = __( 'آرشیو', 'fasdent' );
	return $locations;
}
add_filter( 'elementor/theme/register_locations', 'fasdent_register_elementor_locations' );
