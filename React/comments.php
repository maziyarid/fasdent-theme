<?php
/**
 * Comments Template
 * 
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}

// Load React application for comments
fasdent_react_app();
