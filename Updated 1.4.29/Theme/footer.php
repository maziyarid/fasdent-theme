<?php
/** Site footer. */
$phone = trim( (string) alipasandi_clinic_option( 'clinic_phone' ) );
$phone_href = trim( (string) alipasandi_phone_href() );
$instagram = trim( (string) alipasandi_clinic_option( 'clinic_instagram' ) );
$whatsapp = trim( (string) alipasandi_clinic_option( 'clinic_whatsapp' ) );
$telegram = trim( (string) alipasandi_clinic_option( 'clinic_telegram' ) );
$address = function_exists( 'alipasandi_display_address' ) ? alipasandi_display_address() : trim( (string) alipasandi_clinic_option( 'clinic_address' ) );
$city = trim( (string) alipasandi_clinic_option( 'clinic_city' ) );
$business_name = trim( (string) alipasandi_clinic_option( 'clinic_business_name' ) );
$business_name = '' !== $business_name ? $business_name : get_bloginfo( 'name' );
?>
<footer class="site-footer">
	<?php if ( function_exists( 'alipasandi_nap_surface_marker' ) ) { alipasandi_nap_surface_marker( 'footer' ); } ?>
	<div class="site-container footer-grid">
		<section class="footer-brand" aria-label="<?php esc_attr_e( 'درباره کلینیک', 'alipasandi-clinic' ); ?>">
			<?php alipasandi_brand_logo( 'footer-logo' ); ?>
			<p>خدمات دندانپزشکی با رویکرد دقیق، توضیح روشن و برنامه درمانی متناسب<?php echo '' !== $city ? ' — مراجعه حضوری در ' . esc_html( $city ) : ''; ?>.</p>
			<?php if ( $instagram || $whatsapp || $telegram ) : ?>
			<div class="social-links">
				<?php if ( $instagram ) : ?><a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="اینستاگرام"><?php echo alipasandi_icon( 'instagram', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><?php endif; ?>
				<?php if ( $whatsapp ) : ?><a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer" aria-label="واتساپ"><?php echo alipasandi_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><?php endif; ?>
				<?php if ( $telegram ) : ?><a href="<?php echo esc_url( $telegram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="تلگرام"><?php echo alipasandi_icon( 'telegram', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><?php endif; ?>
			</div>
			<?php endif; ?>
		</section>

		<section>
			<h2>خدمات</h2>
			<ul class="footer-links">
				<?php foreach ( alipasandi_service_links() as $service_link ) : ?>
				<li><a href="<?php echo esc_url( $service_link['url'] ); ?>"><?php echo esc_html( $service_link['label'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</section>

		<section>
			<h2>دسترسی سریع</h2>
			<ul class="footer-links">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">صفحه اصلی</a></li>
				<li><a href="<?php echo esc_url( alipasandi_page_url( 'about' ) ); ?>">درباره ما</a></li>
				<li><a href="<?php echo esc_url( alipasandi_page_url( 'services' ) ); ?>">خدمات</a></li>
				<li><a href="<?php echo esc_url( alipasandi_page_url( 'contact' ) ); ?>">تماس با ما</a></li>
				<li><a href="<?php echo esc_url( alipasandi_page_url( 'faq' ) ); ?>">سوالات متداول</a></li>
				<li><a href="<?php echo esc_url( alipasandi_articles_url() ); ?>">مقالات آموزشی</a></li>
			</ul>
		</section>

		<section class="footer-contact">
			<h2>اطلاعات تماس</h2>
			<?php if ( $phone_href && $phone ) : ?><a href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo alipasandi_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span dir="ltr"><?php echo esc_html( $phone ); ?></span></a><?php endif; ?>
				<?php if ( '' !== $address ) : ?><p>
					<?php echo alipasandi_icon( 'location', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php if ( alipasandi_clinic_option( 'clinic_maps' ) ) : ?>
						<a href="<?php echo esc_url( alipasandi_clinic_option( 'clinic_maps' ) ); ?>" target="_blank" rel="noopener noreferrer"><span><?php echo esc_html( $address ); ?></span></a>
					<?php else : ?>
						<span><?php echo esc_html( $address ); ?></span>
					<?php endif; ?>
				</p><?php endif; ?>
			<a class="button button-gold footer-book" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">درخواست نوبت آنلاین</a>
		</section>
	</div>

	<div class="footer-bottom">
		<div class="site-container">
				<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> — تمام حقوق این وب‌سایت متعلق به <?php echo esc_html( $business_name ); ?> است.</p>
			<?php
			$designer_credit = alipasandi_clinic_option( 'designer_credit' );
			if ( is_string( $designer_credit ) && '' !== trim( $designer_credit ) ) :
				?>
			<p class="footer-design-credit"><?php echo esc_html( $designer_credit ); ?></p>
			<?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( wp_parse_url( home_url( '/' ), PHP_URL_HOST ) ); ?></a>
		</div>
	</div>
</footer>

<nav class="mobile-action-bar" aria-label="<?php esc_attr_e( 'دسترسی سریع موبایل', 'alipasandi-clinic' ); ?>">
	<a href="<?php echo esc_url( $phone_href ? 'tel:' . $phone_href : alipasandi_page_url( 'contact' ) ); ?>"><?php echo alipasandi_icon( 'phone', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>تماس با کلینیک</span></a>
	<a class="mobile-book" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php echo alipasandi_icon( 'calendar', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>درخواست نوبت</span></a>
</nav>

<aside class="contact-float" data-contact-float>
	<div class="contact-options" id="contact-options" hidden>
		<?php if ( $whatsapp ) : ?><a class="contact-option whatsapp" href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer"><?php echo alipasandi_icon( 'whatsapp', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> واتساپ</a><?php endif; ?>
		<?php if ( $telegram ) : ?><a class="contact-option telegram" href="<?php echo esc_url( $telegram ); ?>" target="_blank" rel="noopener noreferrer"><?php echo alipasandi_icon( 'telegram', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> تلگرام</a><?php endif; ?>
		<?php if ( $instagram ) : ?><a class="contact-option instagram" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer"><?php echo alipasandi_icon( 'instagram', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> اینستاگرام</a><?php endif; ?>
		<?php if ( $phone_href ) : ?><a class="contact-option direct" href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo alipasandi_icon( 'phone', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> تماس مستقیم</a><?php endif; ?>
	</div>
	<button type="button" class="contact-toggle" aria-label="تماس با ما" aria-expanded="false" aria-controls="contact-options" data-contact-toggle>
		<span class="contact-chat-icon"><?php echo alipasandi_icon( 'chat', 25 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<span class="contact-close-icon"><?php echo alipasandi_icon( 'close', 23 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	</button>
</aside>

<?php wp_footer(); ?>
</body>
</html>
