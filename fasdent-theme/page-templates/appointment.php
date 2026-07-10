<?php
/**
 * Template Name: رزرو نوبت
 *
 * @package Fasdent
 */

get_header(); ?>
<section class="section">
	<div class="container">
		<h1><?php the_title(); ?></h1>
		<div class="card">
			<form id="fasdent-appointment-form" class="contact-form" data-ajax-form method="post">
				<input type="hidden" name="action" value="fasdent_handle_form">
				<input type="hidden" name="form_type" value="appointment">
				<input type="hidden" name="fasdent_form_nonce" value="<?php echo esc_attr( wp_create_nonce( 'fasdent_form_nonce' ) ); ?>">
				<p><label>نام و نام خانوادگی<br><input type="text" name="name" required></label></p>
				<p><label>شماره تماس<br><input type="tel" name="phone" required></label></p>
				<p><label>ایمیل (اختیاری)<br><input type="email" name="email"></label></p>
				<p><label>توضیحات<br><textarea name="message" rows="4"></textarea></label></p>
				<p><button type="submit" class="btn">ارسال درخواست</button></p>
			</form>
		</div>
	</div>
</section>
<?php get_footer(); ?>