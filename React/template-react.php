<?php
/**
 * React Template
 * 
 * This template loads the React application for all front-end requests.
 * 
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Load React app
fasdent_react_app();

get_footer();
