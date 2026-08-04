<?php
/**
 * Cookie Consent + Google Consent Mode v2 — Fasdent
 * Banner in footer; JS in main.js handles accept/reject.
 * Consent Mode v2: default denied, updated on user choice.
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Default consent (denied) injected before GA4 script. */
function fasdent_consent_mode_default(): void {
	if ( ! get_theme_mod( 'fasdent_ga4_id', '' ) ) { return; }
	?>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', {
  analytics_storage:  'denied',
  ad_storage:         'denied',
  ad_user_data:       'denied',
  ad_personalization: 'denied',
  wait_for_update: 500
});
gtag('set', 'ads_data_redaction', true);
gtag('set', 'url_passthrough', true);
</script>
	<?php
}
add_action( 'wp_head', 'fasdent_consent_mode_default', 0 );

/** Cookie consent banner HTML. */
function fasdent_cookie_banner(): void {
	if ( is_admin() ) { return; }
	$text       = get_theme_mod( 'fasdent_cookie_text', 'این سایت از کوکی برای بهبود تجربه کاربری و آنالیتیکس استفاده می‌کند.' );
	$policy_url = home_url( '/privacy-policy/' );
	?>
<div id="fasdent-cookie-banner" class="cookie-banner" hidden role="dialog" aria-label="<?php esc_attr_e( 'رضایت استفاده از کوکی', 'fasdent' ); ?>" aria-live="polite">
  <div class="cookie-banner__inner">
    <p class="cookie-banner__text"><?php echo wp_kses_post( $text ); ?>
      <a href="<?php echo esc_url( $policy_url ); ?>" rel="noopener"><?php esc_html_e( 'سیاست حریم خصوصی', 'fasdent' ); ?></a>
    </p>
    <div class="cookie-banner__actions">
      <button class="btn btn-primary cookie-accept" type="button"><?php esc_html_e( 'می‌پذیرم', 'fasdent' ); ?></button>
      <button class="btn cookie-reject" type="button"><?php esc_html_e( 'رد کردن', 'fasdent' ); ?></button>
    </div>
  </div>
</div>
	<?php
}
add_action( 'wp_footer', 'fasdent_cookie_banner', 100 );
