<?php
/**
 * Fasdent UI helpers (menu icons only).
 * Floating chat is loaded separately via functions.php.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( file_exists( get_template_directory() . '/inc/menu-icons.php' ) ) {
	require_once get_template_directory() . '/inc/menu-icons.php';
}
