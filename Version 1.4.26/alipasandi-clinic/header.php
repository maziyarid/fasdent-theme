<?php
/** Site header. */
?><!doctype html>
<html dir="rtl" lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#2C1D16">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'رفتن به محتوای اصلی', 'alipasandi-clinic' ); ?></a>

<header class="site-header" data-site-header>
	<div class="site-container header-inner">
		<?php alipasandi_brand_logo(); ?>

		<nav class="primary-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'alipasandi-clinic' ); ?>">
			<ul class="nav-list"><?php alipasandi_render_primary_links(); ?></ul>
		</nav>

		<div class="header-actions">
			<a class="button button-gold header-book" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">
				<?php echo alipasandi_icon( 'calendar', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span>درخواست نوبت</span>
			</a>
			<button class="menu-toggle" type="button" aria-label="<?php esc_attr_e( 'باز کردن منو', 'alipasandi-clinic' ); ?>" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
				<span class="menu-open-icon"><?php echo alipasandi_icon( 'menu', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="menu-close-icon"><?php echo alipasandi_icon( 'close', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</button>
		</div>
	</div>

	<div id="mobile-menu" class="mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منوی موبایل', 'alipasandi-clinic' ); ?>" data-mobile-menu hidden>
		<nav class="site-container" aria-label="<?php esc_attr_e( 'منوی موبایل', 'alipasandi-clinic' ); ?>">
			<?php alipasandi_render_primary_links( true ); ?>
			<a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">درخواست نوبت</a>
		</nav>
	</div>
</header>
<div class="header-spacer" aria-hidden="true"></div>
<?php if ( ! is_front_page() && ! is_404() ) : alipasandi_breadcrumbs(); endif; ?>
