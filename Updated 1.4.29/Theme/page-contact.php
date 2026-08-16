<?php
/** Contact page — canonical operational location and fail-closed form. */
get_header();
$status     = isset( $_GET['form_status'] ) ? sanitize_key( wp_unslash( $_GET['form_status'] ) ) : '';
$maps       = alipasandi_clinic_option( 'clinic_maps' );
$form_ready = alipasandi_service_plugin_ready() && function_exists( 'alipasandi_form_channel_ready' ) && alipasandi_form_channel_ready( 'contact' );
$city       = trim( (string) alipasandi_clinic_option( 'clinic_city' ) );
$address    = function_exists( 'alipasandi_display_address' ) ? alipasandi_display_address() : trim( (string) alipasandi_clinic_option( 'clinic_address' ) );
$phone_href = trim( (string) alipasandi_phone_href() );
$phone      = trim( (string) alipasandi_clinic_option( 'clinic_phone' ) );
$location_label = '' !== $city ? $city : 'کلینیک';
?>
<main id="main-content">
	<?php if ( function_exists( 'alipasandi_nap_surface_marker' ) ) { alipasandi_nap_surface_marker( 'contact' ); } ?>
	<section class="inner-hero">
		<div class="site-container inner-hero-grid">
				<div class="inner-hero-copy"><span class="eyebrow no-lines">تماس با ما</span><h1>در کنار شما هستیم.</h1><p>برای پرسش‌های اولیه، هماهنگی مراجعه یا پیگیری درمان با کلینیک تماس بگیرید. برای تصمیم درمانی، معاینه حضوری لازم است.</p></div>
			<div class="contact-info-list">
					<div class="contact-info-item"><?php echo alipasandi_icon( 'phone', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong>شماره تماس</strong><?php if ( $phone_href && $phone ) : ?><a href="tel:<?php echo esc_attr( $phone_href ); ?>" dir="ltr"><?php echo esc_html( $phone ); ?></a><?php else : ?><span>مسیر تماس مستقیم هنوز آماده نیست</span><?php endif; ?></div></div>
					<div class="contact-info-item"><?php echo alipasandi_icon( 'location', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong>آدرس مراجعه</strong>
						<?php if ( '' !== $address ) : ?>
						<?php if ( $maps ) : ?>
							<a href="<?php echo esc_url( $maps ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $address ); ?></a>
						<?php else : ?>
							<span><?php echo esc_html( $address ); ?></span>
						<?php endif; ?>
						<?php else : ?><span>نشانی مراجعه هنوز آماده نیست</span><?php endif; ?>
					</div></div>
					<div class="contact-info-item"><?php echo alipasandi_icon( 'clock', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong>پاسخ‌گویی</strong><span>در ساعات کاری ثبت‌شده کلینیک</span></div></div>
			</div>
		</div>
	</section>

	<section class="section section-bone">
		<div class="site-container contact-grid">
			<div class="form-card" id="clinic-form" data-reveal>
				<h2>پیام خود را ارسال کنید</h2>
				<p class="text-muted">اطلاعات خود را ثبت کنید تا تیم کلینیک با شما تماس بگیرد.</p>
				<?php if ( 'success' === $status ) : ?><div class="form-message success" role="status">پیام شما ثبت شد. تیم کلینیک در اولین فرصت با شما تماس می‌گیرد.</div>
					<?php elseif ( 'rate_limited' === $status ) : ?><div class="form-message error" role="alert" tabindex="-1" autofocus>تعداد درخواست‌ها بیش از حد مجاز است. لطفاً ۱۵ دقیقه دیگر تلاش کنید<?php echo $phone_href ? ' یا با کلینیک تماس بگیرید' : ''; ?>.</div>
					<?php elseif ( in_array( $status, array( 'error', 'invalid', 'mail_error', 'configuration_error', 'unavailable' ), true ) ) : ?><div class="form-message error" role="alert">ارسال پیام کامل نشد. لطفاً اطلاعات را بررسی و دوباره تلاش کنید<?php echo $phone_href ? ' یا با کلینیک تماس بگیرید' : ''; ?>.</div><?php endif; ?>
				<?php if ( $form_ready ) : ?>
					<form class="contact-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
						<input type="hidden" name="action" value="alipasandi_contact">
						<?php wp_nonce_field( 'alipasandi_contact', 'alipasandi_contact_nonce' ); ?>
						<div class="honeypot" aria-hidden="true"><label>وب‌سایت <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></div>
						<div class="form-grid">
							<div class="field"><label for="contact-name">نام و نام خانوادگی *</label><input id="contact-name" name="name" type="text" required autocomplete="name"></div>
							<div class="field"><label for="contact-phone">شماره تماس *</label><input id="contact-phone" name="phone" type="tel" required inputmode="tel" dir="ltr" autocomplete="tel" placeholder="09xx xxx xxxx"></div>
							<div class="field field-full"><label for="contact-subject">موضوع</label><input id="contact-subject" name="subject" type="text"></div>
							<div class="field field-full"><label for="contact-message">پیام *</label><textarea id="contact-message" name="message" required rows="5"></textarea></div>
						</div>
						<button class="button button-gold" type="submit">ارسال پیام</button>
					</form>
				<?php else : ?>
						<div class="form-message error" role="status" aria-live="polite" aria-atomic="true"><strong>فرم تماس موقتاً در دسترس نیست.</strong><?php if ( $phone_href ) : ?><br>لطفاً از شماره تماس رسمی کلینیک استفاده کنید.<?php else : ?><br>تنظیمات ارتباطی باید توسط مدیر سایت تکمیل شود.<?php endif; ?></div>
				<?php endif; ?>
			</div>
			<div class="contact-side" data-reveal>
				<div class="content-card">
					<h2>مراجعه حضوری</h2>
						<p>محل مراجعه ثبت‌شده: <strong><?php echo esc_html( $location_label ); ?></strong></p>
						<?php if ( '' !== $address ) : ?><p><?php echo esc_html( $address ); ?>
						<?php if ( $maps ) : ?> — <a href="<?php echo esc_url( $maps ); ?>" target="_blank" rel="noopener noreferrer">مشاهده روی نقشه</a><?php endif; ?>
						</p><?php endif; ?>
						<p><a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">درخواست نوبت</a></p>
				</div>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
