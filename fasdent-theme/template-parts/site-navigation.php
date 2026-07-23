<?php
/**
 * Template part: Responsive site header navigation (Fasdent UI v3).
 *
 * Provides the sticky header, emergency/contact topbar, desktop nav with
 * dropdown submenus, and the accessible off-canvas mobile drawer (focus
 * trap + backdrop handled by assets/js/fasdent-ui.js).
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$phone       = fasdent_phone();
$phone_link  = fasdent_phone_link();
$booking_url = (string) get_theme_mod( 'fasdent_booking_url', home_url( '/appointment/' ) );
$emergency   = (string) get_theme_mod( 'fasdent_emergency_text', 'اورژانس دندانپزشکی ۲۴ ساعته — تماس:' );
?>
<header class="site-header" id="masthead" role="banner">
	<?php if ( $phone ) : ?>
		<div class="topbar">
			<div class="container topbar__inner">
				<div class="topbar__links"><span><i class="fa-solid fa-shield-heart" aria-hidden="true"></i> <?php echo esc_html( $emergency ); ?></span></div>
				<div class="topbar__contact"><a href="tel:<?php echo esc_attr( $phone_link ); ?>"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i> <?php echo esc_html( $phone ); ?></a></div>
			</div>
		</div>
	<?php endif; ?>

	<div class="container header-main">
		<div class="site-branding">
			<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?><strong class="site-title"><?php bloginfo( 'name' ); ?></strong><?php endif; ?>
			</a>
			<div class="site-branding__text">
				<?php $description = get_bloginfo( 'description', 'display' ); if ( $description ) : ?><p class="site-description"><?php echo esc_html( $description ); ?></p><?php endif; ?>
			</div>
		</div>

		<button class="menu-toggle mobile-toggle" id="primary-menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" aria-label="<?php esc_attr_e( 'باز کردن منوی اصلی', 'fasdent' ); ?>">
			<span class="menu-toggle__box" aria-hidden="true"><span class="menu-toggle__line"></span><span class="menu-toggle__line"></span><span class="menu-toggle__line"></span></span>
		</button>

		<nav class="site-nav" id="primary-navigation" aria-label="<?php esc_attr_e( 'منوی اصلی', 'fasdent' ); ?>" aria-hidden="true">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'main-menu',
					'menu_id'        => 'primary-menu',
					'menu_class'     => 'menu primary-menu',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 3,
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<?php if ( $phone ) : ?><a class="btn btn-outline header-phone-button" href="tel:<?php echo esc_attr( $phone_link ); ?>"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i><span class="btn-label"><?php esc_html_e( 'تماس', 'fasdent' ); ?></span></a><?php endif; ?>
			<?php fasdent_booking_button( '', 'header-booking-button' ); ?>
		</div>
	</div>
	<button class="nav-backdrop" type="button" hidden aria-label="<?php esc_attr_e( 'بستن فهرست', 'fasdent' ); ?>"></button>
</header>
