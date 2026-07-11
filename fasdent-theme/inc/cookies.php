<?php
/**
 * Cookie Consent — GDPR — Fasdent
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Banner HTML در footer تزریق می‌شود. */
function fasdent_cookie_banner(): void {
	if ( is_admin() ) { return; }
	$text = get_theme_mod( 'fasdent_cookie_text', 'این سایت از کوکی برای بهبود تجربه استفاده می‌کند.' );
	$policy_url = home_url( '/privacy-policy/' );
	?>
	<div id="fasdent-cookie-banner" class="cookie-banner" hidden role="dialog" aria-label="رضایت استفاده از کوکی">
		<div class="cookie-banner__inner">
			<p><?php echo wp_kses_post( $text ); ?> <a href="<?php echo esc_url( $policy_url ); ?>"><?php esc_html_e( 'سیاست حریم خصوصی', 'fasdent' ); ?></a></p>
			<div class="cookie-banner__actions">
				<button class="btn btn-primary cookie-accept"><?php esc_html_e( 'می‌پذیرم', 'fasdent' ); ?></button>
				<button class="btn cookie-reject"><?php esc_html_e( 'رد کردن', 'fasdent' ); ?></button>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'fasdent_cookie_banner', 100 );