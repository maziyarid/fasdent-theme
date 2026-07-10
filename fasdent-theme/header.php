<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div class="topbar">
		<div class="container">
			<span>اورژانس دندانپزشکی ۲۴ ساعته — تماس: <?php echo esc_html( fasdent_phone() ); ?></span>
			<a href="tel:<?php echo esc_attr( fasdent_phone_link() ); ?>"><i class="fa-solid fa-phone-volume"></i> تماس فوری</a>
		</div>
	</div>
	<header class="site-header">
		<div class="container header-main">
			<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?><strong>فس‌دنت</strong><?php endif; ?>
			</a>
			<nav class="site-nav" aria-label="منوی اصلی">
				<?php wp_nav_menu( array( 'theme_location' => 'main-menu', 'container' => false, 'fallback_cb' => false ) ); ?>
			</nav>
			<div class="header-actions">
				<?php fasdent_booking_button(); ?>
				<button class="mobile-toggle btn" type="button" aria-label="باز کردن منوی موبایل">☰</button>
			</div>
		</div>
	</header>
	<?php fasdent_breadcrumb(); ?>
	<main id="content">
