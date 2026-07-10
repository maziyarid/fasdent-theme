<?php
/**
 * Template Name: تماس با ما
 *
 * @package Fasdent
 */

get_header(); ?>
<section class="section">
	<div class="container">
		<h1><?php the_title(); ?></h1>
		<div class="grid-2">
			<div class="card">
				<h2>اطلاعات تماس</h2>
				<p>تلفن: <?php echo esc_html( fasdent_phone() ); ?></p>
				<p>ایمیل: <?php echo esc_html( get_theme_mod( 'fasdent_email', 'info@fasdent.ir' ) ); ?></p>
				<p>آدرس: <?php echo esc_html( get_theme_mod( 'fasdent_address', 'تهران' ) ); ?></p>
			</div>
			<div class="card">
				<form class="contact-form" data-ajax-form method="post">
					<input type="hidden" name="action" value="fasdent_handle_form">
					<input type="hidden" name="form_type" value="contact">
					<input type="hidden" name="fasdent_form_nonce" value="<?php echo esc_attr( wp_create_nonce( 'fasdent_form_nonce' ) ); ?>">
					<p><label>نام<br><input type="text" name="name" required></label></p>
					<p><label>شماره تماس<br><input type="tel" name="phone" required></label></p>
					<p><label>پیام<br><textarea name="message" rows="5"></textarea></label></p>
					<p><button type="submit" class="btn">ارسال پیام</button></p>
				</form>
			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>