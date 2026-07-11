<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- دسترسی‌پذیری: پرش به محتوا (WCAG 2.1 AA) -->
<a class="skip-link" href="#content"><?php esc_html_e( 'رفتن به محتوا', 'fasdent' ); ?></a>

	<div class="topbar">
		<div class="container">
			<span><?php echo esc_html( get_theme_mod( 'fasdent_emergency_text', 'اورژانس دندانپزشکی ۲۴ ساعته — تماس:' ) ); ?> <?php echo esc_html( fasdent_phone() ); ?></span>
			<a href="tel:<?php echo esc_attr( fasdent_phone_link() ); ?>"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i> تماس فوری</a>
		</div>
	</div>
	<header class="site-header" role="banner">
		<div class="container header-main">
			<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?><strong>فس‌دنت</strong><?php endif; ?>
			</a>
			<nav class="site-nav" id="site-navigation" aria-label="<?php esc_attr_e( 'منوی اصلی', 'fasdent' ); ?>">
				<?php wp_nav_menu( array( 'theme_location' => 'main-menu', 'container' => false, 'fallback_cb' => false ) ); ?>
			</nav>
			<div class="header-actions">
				<?php fasdent_booking_button(); ?>
				<button class="mobile-toggle btn" type="button" aria-label="<?php esc_attr_e( 'باز کردن منوی موبایل', 'fasdent' ); ?>" aria-expanded="false" aria-controls="site-navigation">☰</button>
			</div>
		</div>
	</header>
	<?php fasdent_breadcrumb(); ?>
	<main id="content" role="main">
