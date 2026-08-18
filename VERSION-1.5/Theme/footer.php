<?php
/** Site footer. */
?>
<footer class="site-footer">
	<div class="site-container footer-grid">
		<section class="footer-brand" aria-label="<?php esc_attr_e( 'درباره کلینیک', 'alipasandi-clinic' ); ?>">
			<?php alipasandi_brand_logo( 'footer-logo' ); ?>
			<p>کلینیک دندانپزشکی تخصصی با رویکرد مدرن، دقیق و قابل اعتماد — مراجعه حضوری در نوشهر.</p>
			<div class="social-links">
				<?php foreach ( array( 'instagram', 'whatsapp', 'telegram' ) as $channel ) : alipasandi_contact_channel( $channel, 'social' ); endforeach; ?>
			</div>
		</section>

		<section>
			<h2>خدمات</h2>
			<ul class="footer-links">
				<li><a href="<?php echo esc_url( alipasandi_page_url( 'services/implant' ) ); ?>">ایمپلنت دندان</a></li>
				<li><a href="<?php echo esc_url( alipasandi_page_url( 'services/crown' ) ); ?>">روکش دندان</a></li>
				<li><a href="<?php echo esc_url( alipasandi_page_url( 'services/surgery' ) ); ?>">جراحی و لثه</a></li>
				<li><a href="<?php echo esc_url( alipasandi_page_url( 'services/general' ) ); ?>">درمان عمومی</a></li>
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
			<?php alipasandi_phone_link( '', 18 ); ?>
						<p>
				<?php alipasandi_the_icon( 'location', 18 ); ?>
				<?php if ( alipasandi_clinic_option( 'clinic_maps' ) ) : ?>
					<a href="<?php echo esc_url( alipasandi_clinic_option( 'clinic_maps' ) ); ?>" target="_blank" rel="noopener noreferrer"><span><?php echo esc_html( alipasandi_clinic_option( 'clinic_address' ) ); ?></span></a>
				<?php else : ?>
					<span><?php echo esc_html( alipasandi_clinic_option( 'clinic_address' ) ); ?></span>
				<?php endif; ?>
			</p>
			<a class="button button-gold footer-book" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">رزرو نوبت آنلاین</a>
		</section>
	</div>

	<div class="footer-bottom">
		<div class="site-container">
			<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> — تمام حقوق این وب‌سایت متعلق به کلینیک دندانپزشکی دکتر کیوان علی‌پسندی است.</p>
			<?php
			$designer_credit = alipasandi_clinic_option( 'designer_credit' );
			if ( is_string( $designer_credit ) && '' !== trim( $designer_credit ) ) :
				?>
			<p class="footer-design-credit"><?php echo esc_html( $designer_credit ); ?></p>
			<?php endif; ?>
			<a href="<?php echo esc_url( alipasandi_clinic_option( 'clinic_website' ) ); ?>">drkeyvanalipasandi.com</a>
		</div>
	</div>
</footer>

<nav class="mobile-action-bar" aria-label="<?php esc_attr_e( 'دسترسی سریع موبایل', 'alipasandi-clinic' ); ?>">
	<?php alipasandi_phone_link( '', 19, 'تماس با کلینیک', 'text-span' ); ?>
	<a class="mobile-book" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php alipasandi_the_icon( 'calendar', 19 ); ?><span>رزرو نوبت</span></a>
</nav>

<aside class="contact-float" data-contact-float>
	<div class="contact-options" id="contact-options" hidden>
		<?php foreach ( array( 'whatsapp', 'telegram', 'instagram' ) as $channel ) : alipasandi_contact_channel( $channel, 'float' ); endforeach; ?>
		<?php alipasandi_phone_link( 'contact-option direct', 20, 'تماس مستقیم', 'text' ); ?>
	</div>
	<button type="button" class="contact-toggle" aria-label="تماس با ما" aria-expanded="false" aria-controls="contact-options" data-contact-toggle>
		<span class="contact-chat-icon"><?php alipasandi_the_icon( 'chat', 25 ); ?></span>
		<span class="contact-close-icon"><?php alipasandi_the_icon( 'close', 23 ); ?></span>
	</button>
</aside>

<?php wp_footer(); ?>
</body>
</html>
