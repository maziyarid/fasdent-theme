<?php
/**
 * Header Template
 * 
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Favicon -->
	<link rel="icon" type="image/webp" href="<?php echo esc_url( FASDENT_URI ); ?>/assets/images/Favicon.webp" />
	<!-- Local Fonts -->
	<link rel="stylesheet" href="<?php echo esc_url( FASDENT_URI ); ?>/assets/fonts/Irancell/irancell.css" />
	<link rel="stylesheet" href="<?php echo esc_url( FASDENT_URI ); ?>/assets/fonts/FontAwesome/css/all.css" />
	<link rel="stylesheet" href="<?php echo esc_url( FASDENT_URI ); ?>/assets/fonts/FontAwesome/css/solid.css" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'fasdent' ); ?></a>
	<div id="page" class="site">
		<header id="masthead" class="site-header" role="banner">
			<?php get_template_part( 'template-parts/site-navigation' ); ?>
		</header>
		<div id="main-content" class="site-content">
