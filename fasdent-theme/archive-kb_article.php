<?php
/**
 * Knowledge Base Article Archive Template
 * 
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Use React for all front-end requests
fasdent_react_app();

get_footer();
