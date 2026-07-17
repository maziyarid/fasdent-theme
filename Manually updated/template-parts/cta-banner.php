<?php
/**
 * Template part: Appointment call-to-action banner.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="cta-banner" aria-labelledby="fasdent-cta-title">
	<span class="cta-banner__icon" aria-hidden="true"><i class="fa-duotone fa-solid fa-calendar-check"></i></span>
	<h2 id="fasdent-cta-title"><?php esc_html_e( 'برای یک لبخند سالم، همین امروز شروع کنید', 'fasdent' ); ?></h2>
	<p><?php esc_html_e( 'مشاوره تخصصی، بررسی دقیق و برنامه درمانی متناسب با نیاز شما در کلینیک فس‌دنت.', 'fasdent' ); ?></p>
	<div class="cta-banner__actions">
		<?php if ( function_exists( 'fasdent_booking_button' ) ) { fasdent_booking_button( __( 'رزرو نوبت آنلاین', 'fasdent' ), 'btn-primary' ); } ?>
		<?php if ( function_exists( 'fasdent_call_button' ) ) { fasdent_call_button( __( 'تماس با کلینیک', 'fasdent' ), 'btn-secondary' ); } ?>
	</div>
</section>
