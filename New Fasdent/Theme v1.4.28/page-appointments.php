<?php
/** Appointment request page. */
get_header();
$status     = isset( $_GET['form_status'] ) ? sanitize_key( wp_unslash( $_GET['form_status'] ) ) : '';
$form_ready = alipasandi_service_plugin_ready() && function_exists( 'alipasandi_form_channel_ready' ) && function_exists( 'alipasandi_bookable_services' ) && function_exists( 'alipasandi_allowed_times' ) && function_exists( 'alipasandi_appointment_ui_policy' ) && alipasandi_form_channel_ready( 'appointment' );
$services   = $form_ready ? alipasandi_bookable_services() : array();
$times      = $form_ready ? alipasandi_allowed_times() : array();
$ui_policy  = $form_ready ? alipasandi_appointment_ui_policy() : array();
$today        = current_datetime();
$horizon_days = $form_ready && function_exists( 'alipasandi_booking_horizon_days' ) ? alipasandi_booking_horizon_days() : 0;
$max_date     = $horizon_days ? $today->modify( '+' . $horizon_days . ' days' ) : $today;
?>
<main id="main-content">
	<section class="inner-hero">
		<div class="narrow-container section-heading" style="padding-block:64px;position:relative">
			<span class="eyebrow">نوبت‌دهی آنلاین</span><h1>درخواست نوبت</h1><p>درخواست نوبت برای <strong>مطب نوشهر</strong> ثبت می‌شود. تاریخ و ساعت انتخاب‌شده پیشنهادی است و نوبت پس از تماس کلینیک قطعی خواهد شد.</p>
		</div>
	</section>

	<section class="section section-bone">
		<div class="narrow-container" id="clinic-form">
			<?php if ( 'success' === $status ) : ?>
				<div class="form-message success" role="status"><strong>درخواست نوبت شما ثبت شد.</strong><br>تیم کلینیک برای بررسی زمان پیشنهادی و تأیید نهایی با شما تماس می‌گیرد.</div>
			<?php elseif ( 'rate_limited' === $status ) : ?>
				<div class="form-message error" role="alert" tabindex="-1" autofocus>تعداد درخواست‌های پذیرفته‌شده بیش از حد مجاز است. لطفاً ۱۵ دقیقه دیگر تلاش کنید یا مستقیماً با کلینیک تماس بگیرید.</div>
			<?php elseif ( 'invalid' === $status ) : ?>
				<div class="form-message error" role="alert">برخی اطلاعات یا زمان پیشنهادی معتبر نیست. لطفاً تاریخ، ساعت و اطلاعات تماس را دوباره بررسی کنید.</div>
			<?php elseif ( 'mail_error' === $status ) : ?>
				<div class="form-message error" role="alert">سامانه ارسال پیام پاسخ نداد و درخواست شما تأیید نشده است. لطفاً دوباره تلاش کنید یا مستقیماً با کلینیک تماس بگیرید.</div>
			<?php elseif ( in_array( $status, array( 'configuration_error', 'unavailable' ), true ) ) : ?>
				<div class="form-message error" role="alert">سامانه ثبت درخواست نوبت موقتاً در دسترس نیست. لطفاً مستقیماً با کلینیک تماس بگیرید.</div>
			<?php elseif ( 'error' === $status ) : ?>
				<div class="form-message error" role="alert">ارسال معتبر نبود. لطفاً صفحه را تازه‌سازی کرده و دوباره تلاش کنید؛ در صورت تداوم مشکل با کلینیک تماس بگیرید.</div>
			<?php endif; ?>

			<div class="booking-location-note" role="note">
				<strong>محل مراجعه: مطب نوشهر</strong>
				<span><?php echo esc_html( alipasandi_clinic_option( 'clinic_address' ) ); ?></span>
			</div>

			<?php if ( $form_ready ) : ?>
				<div class="booking-shell">
					<noscript><style>[data-booking-step][hidden]{display:block!important;margin-top:28px}.booking-progress,.booking-navigation{display:none!important}</style><div class="form-message error">جاوااسکریپت غیرفعال است؛ همه بخش‌ها و زمان‌های پیشنهادی نمایش داده می‌شوند و اعتبار تاریخ/ساعت هنگام ارسال در سرور بررسی می‌شود.</div></noscript>
					<div class="booking-progress" aria-label="مراحل درخواست نوبت">
						<div class="progress-step is-active" data-step-indicator="1"><span class="progress-number">۱</span><span>انتخاب خدمت</span></div>
						<div class="progress-step" data-step-indicator="2"><span class="progress-number">۲</span><span>تاریخ و زمان</span></div>
						<div class="progress-step" data-step-indicator="3"><span class="progress-number">۳</span><span>اطلاعات شما</span></div>
					</div>
					<form class="booking-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" data-booking-form novalidate>
						<input type="hidden" name="action" value="alipasandi_appointment">
						<?php wp_nonce_field( 'alipasandi_appointment', 'alipasandi_appointment_nonce' ); ?>
						<div class="honeypot" aria-hidden="true"><label>وب‌سایت <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></div>

						<section class="booking-step" data-booking-step="1">
							<h2>کدام خدمت را نیاز دارید؟</h2>
							<div class="choice-grid">
								<?php foreach ( $services as $service_key => $service_label ) : $id = 'service-choice-' . sanitize_html_class( $service_key ); ?>
									<div><input class="choice-input" id="<?php echo esc_attr( $id ); ?>" type="radio" name="service" value="<?php echo esc_attr( $service_key ); ?>" data-service-label="<?php echo esc_attr( $service_label ); ?>"><label class="choice-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $service_label ); ?></label></div>
								<?php endforeach; ?>
							</div>
						</section>

						<section class="booking-step" data-booking-step="2" hidden>
							<h2>تاریخ و زمان پیشنهادی</h2>
							<div class="field"><label for="appointment-date">تاریخ درخواستی *</label><input id="appointment-date" type="date" name="date" min="<?php echo esc_attr( $today->format( 'Y-m-d' ) ); ?>" max="<?php echo esc_attr( $max_date->format( 'Y-m-d' ) ); ?>"></div>
							<p class="field-label">ساعت پیشنهادی *</p>
							<p class="form-confirmation-note" role="status" aria-live="polite" aria-atomic="true" data-time-guidance>ابتدا تاریخ را انتخاب کنید تا ساعت‌های پیشنهادی همان روز نمایش داده شوند.</p>
							<div class="time-grid">
								<?php foreach ( $times as $index => $time ) : $id = 'time-choice-' . $index; ?>
									<div data-booking-time-option><input class="choice-input" id="<?php echo esc_attr( $id ); ?>" type="radio" name="time" value="<?php echo esc_attr( $time ); ?>"><label class="choice-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $time ); ?></label></div>
								<?php endforeach; ?>
							</div>
							<script type="application/json" data-booking-policy><?php echo wp_json_encode( $ui_policy, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
						</section>

						<section class="booking-step" data-booking-step="3" hidden>
							<h2>اطلاعات شما</h2>
							<div class="form-grid">
								<div class="field"><label for="appointment-name">نام و نام خانوادگی *</label><input id="appointment-name" type="text" name="name" required autocomplete="name"></div>
								<div class="field"><label for="appointment-phone">شماره تماس *</label><input id="appointment-phone" type="tel" name="phone" required inputmode="tel" dir="ltr" autocomplete="tel" placeholder="09xx xxx xxxx"></div>
								<div class="field field-full"><label for="appointment-notes">توضیحات بیشتر (اختیاری)</label><textarea id="appointment-notes" name="notes" placeholder="در صورت نیاز، توضیح کوتاهی بنویسید..."></textarea></div>
							</div>
							<div class="booking-summary"><div><small>خدمت</small><strong data-summary="service">—</strong></div><div><small>تاریخ</small><strong data-summary="date">—</strong></div><div><small>ساعت پیشنهادی</small><strong data-summary="time">—</strong></div></div>
						</section>

						<div class="validation-message" role="alert">لطفاً اطلاعات ضروری این مرحله را کامل کنید.</div>
						<div class="booking-navigation"><button class="button button-outline-dark" type="button" data-booking-previous hidden>مرحله قبل</button><span></span><button class="button button-gold" type="button" data-booking-next>مرحله بعد</button><button class="button button-gold" type="submit" data-booking-submit hidden><?php echo alipasandi_icon( 'calendar', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> ثبت درخواست نوبت</button></div>
					</form>
				</div>
			<?php else : ?>
				<div class="booking-shell">
					<div class="form-message error" role="status" aria-live="polite" aria-atomic="true"><strong>سامانه ثبت درخواست نوبت موقتاً در دسترس نیست.</strong><br>برای هماهنگی، لطفاً مستقیماً با کلینیک تماس بگیرید.</div>
				</div>
			<?php endif; ?>

			<?php $phone_href = alipasandi_phone_href(); ?>
			<?php if ( $phone_href ) : ?><p style="text-align:center;margin-top:24px;color:var(--ink-soft);font-size:.82rem">تماس مستقیم: <a class="phone-link" href="tel:<?php echo esc_attr( $phone_href ); ?>"><span dir="ltr"><?php echo esc_html( alipasandi_clinic_option( 'clinic_phone' ) ); ?></span></a></p><?php endif; ?>
			<p class="form-confirmation-note">تاریخ و ساعت انتخاب‌شده پیشنهادی است و نوبت پس از تماس کلینیک قطعی خواهد شد.</p>
			<p class="form-privacy">اطلاعات این فرم فقط برای هماهنگی نوبت استفاده می‌شود.</p>
		</div>
	</section>
</main>
<?php get_footer(); ?>
