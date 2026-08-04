<?php
/**
 * Site header + navigation — Fasdent
 * Mobile: logo centered, hamburger top-right, drawer slides from right (RTL).
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$phone       = function_exists( 'fasdent_phone' ) ? fasdent_phone() : '';
$phone_link  = function_exists( 'fasdent_phone_link' ) ? fasdent_phone_link() : '';
$emergency   = (string) get_theme_mod( 'fasdent_emergency_text', 'اورژانس دندانپزشکی — تماس فوری:' );
?>
<header class="site-header" id="masthead" role="banner">

	<?php if ( $phone ) : ?>
	<div class="topbar">
		<div class="container topbar__inner">
			<span class="topbar__text">
				<i class="fa-solid fa-shield-heart" aria-hidden="true"></i>
				<?php echo esc_html( $emergency ); ?>
			</span>
			<a class="topbar__phone" href="tel:<?php echo esc_attr( $phone_link ); ?>">
				<i class="fa-solid fa-phone-volume" aria-hidden="true"></i>
				<?php echo esc_html( $phone ); ?>
			</a>
		</div>
	</div>
	<?php endif; ?>

	<div class="container header-main">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<strong class="site-title"><?php bloginfo( 'name' ); ?></strong>
			<?php endif; ?>
		</a>

		<nav class="site-nav" id="primary-navigation" aria-label="<?php esc_attr_e( 'منوی اصلی', 'fasdent' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'main-menu',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'menu primary-menu',
				'container'      => false,
				'fallback_cb'    => false,
				'depth'          => 3,
			) );
			?>
		</nav>

		<div class="header-actions">
			<?php if ( $phone ) : ?>
				<a class="btn btn-call header-phone-btn" href="tel:<?php echo esc_attr( $phone_link ); ?>">
					<i class="fa-solid fa-phone-volume" aria-hidden="true"></i>
					<span class="btn-label"><?php esc_html_e( 'تماس', 'fasdent' ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( function_exists( 'fasdent_booking_button' ) ) { fasdent_booking_button( '', 'header-booking-btn' ); } ?>

			<button
				class="mobile-toggle"
				id="primary-menu-toggle"
				type="button"
				aria-expanded="false"
				aria-controls="primary-navigation"
				aria-label="<?php esc_attr_e( 'باز کردن منوی اصلی', 'fasdent' ); ?>"
				data-open-label="<?php esc_attr_e( 'باز کردن منوی اصلی', 'fasdent' ); ?>"
				data-close-label="<?php esc_attr_e( 'بستن منوی اصلی', 'fasdent' ); ?>"
			>
				<span class="mobile-toggle__bars" aria-hidden="true">
					<span></span><span></span><span></span>
				</span>
			</button>
		</div>
	</div>

	<div class="nav-backdrop" id="nav-backdrop" hidden aria-hidden="true"></div>
</header>
