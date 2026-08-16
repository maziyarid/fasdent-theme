<?php
/** Contact page with a native WordPress form. */
get_header();
$status = isset( $_GET['form_status'] ) ? sanitize_key( wp_unslash( $_GET['form_status'] ) ) : '';
?>
<main id="main-content">
	<section class="inner-hero">
		<div class="site-container inner-hero-grid">
			<div class="inner-hero-copy"><span class="eyebrow no-lines">تماس با ما</span><h1>در کنار شما هستیم.</h1><p>برای پرسش‌های اولیه، هماهنگی مراجعه یا پیگیری درمان با کلینیک تماس بگیرید. برای تصمیم درمانی، معاینه حضوری لازم است.</p></div>
			<div class="contact-info-list">
				<div class="contact-info-item"><?php echo alipasandi_icon( 'phone', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong>شماره تماس</strong><a href="tel:<?php echo esc_attr( alipasandi_phone_href() ); ?>" dir="ltr"><?php echo esc_html( alipasandi_clinic_option( 'clinic_phone' ) ); ?></a></div></div>
				<div class="contact-info-item"><?php echo alipasandi_icon( 'location', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong>آدرس تهران</strong><span><?php echo esc_html( alipasandi_clinic_option( 'clinic_address' ) ); ?></span></div></div>
				<?php if ( alipasandi_clinic_option( 'clinic_address_2' ) ) : ?>
				<div class="contact-info-item"><?php echo alipasandi_icon( 'location', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong>آدرس نوشهر</strong>
					<?php if ( alipasandi_clinic_option( 'clinic_maps_2' ) ) : ?>
						<a href="<?php echo esc_url( alipasandi_clinic_option( 'clinic_maps_2' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( alipasandi_clinic_option( 'clinic_address_2' ) ); ?></a>
					<?php else : ?>
						<span><?php echo esc_html( alipasandi_clinic_option( 'clinic_address_2' ) ); ?></span>
					<?php endif; ?>
				</div></div>
				<?php endif; ?>
				<div class="contact-info-item"><?php echo alipasandi_icon( 'clock', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong>پاسخ‌گویی</strong><span>در ساعات کاری کلینیک</span></div></div>
			</div>
		</div>
	</section>

	<section class="section section-bone">
		<div class="site-container contact-grid">
			<div class="form-card" id="clinic-form" data-reveal>
				<h2>پیام خود را ارسال کنید</h2>
				<p class="text-muted">اطلاعات خود را ثبت کنید تا تیم کلینیک با شما تماس بگیرد.</p>
				<?php if ( 'success' === $status ) : ?><div class="form-message success" role="status">پیام شما ثبت شد. تیم کلینیک در اولین فرصت با شما تماس می‌گیرد.</div><?php endif; ?>
				<?php if ( in_array( $status, array( 'error', 'invalid', 'mail_error' ), true ) ) : ?><div class="form-message error" role="alert">ارسال پیام کامل نشد. لطفاً اطلاعات را بررسی کنید یا با کلینیک تماس بگیرید.</div><?php endif; ?>
				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="alipasandi_contact">
					<?php wp_nonce_field( 'alipasandi_contact', 'alipasandi_contact_nonce' ); ?>
					<div class="honeypot" aria-hidden="true"><label>وب‌سایت <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></div>
					<div class="form-grid">
						<div class="field"><label for="contact-name">نام و نام خانوادگی *</label><input id="contact-name" name="name" type="text" required autocomplete="name"></div>
						<div class="field"><label for="contact-phone">شماره تماس *</label><input id="contact-phone" name="phone" type="tel" required inputmode="tel" dir="ltr" autocomplete="tel" placeholder="09xx xxx xxxx"></div>
						<div class="field field-full"><label for="contact-subject">موضوع</label><select id="contact-subject" name="subject"><option value="مشاوره اولیه">مشاوره اولیه</option><option value="رزرو نوبت">رزرو نوبت</option><option value="پیگیری درمان">پیگیری درمان</option><option value="سایر">سایر</option></select></div>
						<div class="field field-full"><label for="contact-message">پیام</label><textarea id="contact-message" name="message" placeholder="پیام خود را بنویسید..."></textarea></div>
					</div>
					<button class="button button-gold" type="submit">ارسال پیام</button>
					<p class="form-privacy">اطلاعات شما فقط برای پاسخ‌گویی و هماهنگی با کلینیک استفاده می‌شود.</p>
				</form>
			</div>

			<div data-reveal>
				<div class="clinic-map">
					<span><?php echo esc_html( alipasandi_clinic_option( 'clinic_address' ) ); ?></span>
					<?php if ( alipasandi_clinic_option( 'clinic_address_2' ) ) : ?>
						<span style="display:block;margin-top:8px;"><?php echo esc_html( alipasandi_clinic_option( 'clinic_address_2' ) ); ?>
						<?php if ( alipasandi_clinic_option( 'clinic_maps_2' ) ) : ?>
							 — <a href="<?php echo esc_url( alipasandi_clinic_option( 'clinic_maps_2' ) ); ?>" target="_blank" rel="noopener noreferrer">مشاهده روی نقشه</a>
						<?php endif; ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="hours-card">
					<h2>ساعات کاری</h2>
					<ul class="hours-list"><li><span>شنبه تا چهارشنبه</span><strong>۱۰ صبح — ۸ شب</strong></li><li><span>پنجشنبه</span><strong>۱۰ صبح — ۵ عصر</strong></li><li><span>جمعه</span><strong>تعطیل</strong></li></ul>
				</div>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
