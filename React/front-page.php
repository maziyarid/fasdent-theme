<?php
/**
 * Front Page Template
 * 
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Use React for front page
fasdent_react_app();

get_footer();
