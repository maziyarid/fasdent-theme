<?php
/**
 * Template part: Social share controls.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$permalink   = (string) get_permalink();
$title       = (string) get_the_title();
$clinic_name = (string) get_theme_mod( 'fasdent_clinic_name', __( 'کلینیک فس‌دنت', 'fasdent' ) );
$share_text  = $title . ' — ' . $clinic_name;
?>
<div class="social-share" aria-label="<?php esc_attr_e( 'اشتراک‌گذاری مطلب', 'fasdent' ); ?>">
	<span class="social-share__label"><i class="fa-duotone fa-solid fa-share-nodes" aria-hidden="true"></i> <?php esc_html_e( 'اشتراک‌گذاری', 'fasdent' ); ?></span>
	<a href="<?php echo esc_url( add_query_arg( array( 'url' => $permalink, 'text' => $share_text ), 'https://t.me/share/url' ) ); ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--telegram" aria-label="<?php esc_attr_e( 'اشتراک در تلگرام', 'fasdent' ); ?>"><i class="fa-brands fa-telegram" aria-hidden="true"></i></a>
	<a href="<?php echo esc_url( add_query_arg( 'text', $share_text . ' ' . $permalink, 'https://wa.me/' ) ); ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--whatsapp" aria-label="<?php esc_attr_e( 'اشتراک در واتس‌اپ', 'fasdent' ); ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
	<a href="<?php echo esc_url( add_query_arg( array( 'text' => $share_text, 'url' => $permalink ), 'https://twitter.com/intent/tweet' ) ); ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--twitter" aria-label="<?php esc_attr_e( 'اشتراک در ایکس', 'fasdent' ); ?>"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
	<a href="<?php echo esc_url( add_query_arg( array( 'mini' => 'true', 'url' => $permalink, 'title' => $title ), 'https://www.linkedin.com/shareArticle' ) ); ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--linkedin" aria-label="<?php esc_attr_e( 'اشتراک در لینکدین', 'fasdent' ); ?>"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
	<button class="social-btn social-btn--copy" data-copy-url="<?php echo esc_attr( $permalink ); ?>" aria-label="<?php esc_attr_e( 'کپی پیوند', 'fasdent' ); ?>" type="button"><i class="fa-duotone fa-solid fa-link" aria-hidden="true"></i></button>
	<span class="social-share__status" aria-live="polite"></span>
</div>
