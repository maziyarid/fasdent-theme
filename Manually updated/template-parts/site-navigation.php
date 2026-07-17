<?php
/**
 * Template part: Responsive site header navigation.
 * Replace the current navigation block inside header.php with this template.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$phone       = (string) get_theme_mod( 'fasdent_phone', '' );
$booking_url = (string) get_theme_mod( 'fasdent_booking_url', home_url( '/booking/' ) );
?>
<header class="site-header" id="masthead">
	<?php if ( $phone ) : ?>
		<div class="topbar">
			<div class="container topbar__inner">
				<div class="topbar__contact"><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><i class="fa-duotone fa-solid fa-phone" aria-hidden="true"></i> <?php echo esc_html( $phone ); ?></a></div>
				<div class="topbar__links"><span><i class="fa-duotone fa-solid fa-shield-heart" aria-hidden="true"></i> <?php esc_html_e( 'مراقبت حرفه‌ای، تجربه‌ای آرام', 'fasdent' ); ?></span></div>
			</div>
		</div>
	<?php endif; ?>

	<div class="container header-main">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) { the_custom_logo(); } ?>
			<div class="site-branding__text">
				<?php if ( is_front_page() && is_home() ) : ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php else : ?>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php endif; ?>
				<?php $description = get_bloginfo( 'description', 'display' ); if ( $description ) : ?><p class="site-description"><?php echo esc_html( $description ); ?></p><?php endif; ?>
			</div>
		</div>

		<button class="menu-toggle mobile-toggle" id="primary-menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" aria-label="<?php esc_attr_e( 'باز کردن فهرست اصلی', 'fasdent' ); ?>">
			<span class="menu-toggle__box" aria-hidden="true"><span class="menu-toggle__line"></span><span class="menu-toggle__line"></span><span class="menu-toggle__line"></span></span>
		</button>

		<nav class="site-nav" id="primary-navigation" aria-label="<?php esc_attr_e( 'فهرست اصلی', 'fasdent' ); ?>" aria-hidden="true">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'menu_class'     => 'menu primary-menu',
					'container'      => false,
					'fallback_cb'    => 'wp_page_menu',
					'depth'          => 3,
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<?php if ( $phone ) : ?><a class="btn btn-outline header-phone-button" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><i class="fa-duotone fa-solid fa-phone-volume" aria-hidden="true"></i><span class="btn-label"><?php esc_html_e( 'تماس', 'fasdent' ); ?></span></a><?php endif; ?>
			<a class="btn header-booking-button" href="<?php echo esc_url( $booking_url ); ?>"><i class="fa-duotone fa-solid fa-calendar-check" aria-hidden="true"></i><span><?php esc_html_e( 'رزرو نوبت', 'fasdent' ); ?></span></a>
		</div>
	</div>
	<button class="nav-backdrop" type="button" hidden aria-label="<?php esc_attr_e( 'بستن فهرست', 'fasdent' ); ?>"></button>
</header>
