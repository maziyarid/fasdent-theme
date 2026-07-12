	</main>
	<footer class="footer" role="contentinfo">
		<div class="container">
			<div class="grid-4">
				<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
					<?php dynamic_sidebar( 'footer-1' ); ?>
				<?php else : ?>
				<div>
					<h3><?php echo esc_html( get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ) ); ?></h3>
					<p><?php esc_html_e( 'درمان‌های تخصصی، ایمپلنت، زیبایی و اورژانس با رویکرد دقیق و مراقبت حرفه‌ای.', 'fasdent' ); ?></p>
					<?php $hours = get_theme_mod( 'fasdent_hours', '' ); if ( $hours ) : ?><p><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( $hours ); ?></p><?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
					<?php dynamic_sidebar( 'footer-2' ); ?>
				<?php else : ?>
				<div>
					<h3><?php esc_html_e( 'لینک‌های سریع', 'fasdent' ); ?></h3>
					<?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => false, 'fallback_cb' => false ) ); ?>
				</div>
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
					<?php dynamic_sidebar( 'footer-3' ); ?>
				<?php else : ?>
				<div>
					<h3><?php esc_html_e( 'تماس با ما', 'fasdent' ); ?></h3>
					<p><i class="fa-solid fa-phone" aria-hidden="true"></i> <a href="tel:<?php echo esc_attr( fasdent_phone_link() ); ?>"><?php echo esc_html( fasdent_phone() ); ?></a></p>
					<p><i class="fa-solid fa-envelope" aria-hidden="true"></i> <?php echo esc_html( get_theme_mod( 'fasdent_email', 'info@fasdent.ir' ) ); ?></p>
					<p><i class="fa-solid fa-location-dot" aria-hidden="true"></i> <?php echo esc_html( get_theme_mod( 'fasdent_address', 'تهران' ) ); ?></p>
				</div>
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
					<?php dynamic_sidebar( 'footer-4' ); ?>
				<?php else : ?>
				<div>
					<h3><?php esc_html_e( 'شبکه‌های اجتماعی', 'fasdent' ); ?></h3>
					<div class="footer-social">
						<?php
						$socials = array(
							'fasdent_instagram' => array( 'fa-brands fa-instagram', 'اینستاگرام' ),
							'fasdent_telegram'  => array( 'fa-brands fa-telegram', 'تلگرام' ),
							'fasdent_whatsapp'  => array( 'fa-brands fa-whatsapp', 'واتس‌اپ' ),
						);
						foreach ( $socials as $mod => $data ) {
							$url = get_theme_mod( $mod, '' );
							if ( $url ) {
								printf(
									'<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s"><i class="%s" aria-hidden="true"></i></a>',
									esc_url( $url ),
									esc_attr( $data[1] ),
									esc_attr( $data[0] )
								);
							}
						}
						?>
					</div>
					<nav aria-label="<?php esc_attr_e( 'منوی قوانین', 'fasdent' ); ?>">
						<?php wp_nav_menu( array( 'theme_location' => 'legal-menu', 'container' => false, 'fallback_cb' => false ) ); ?>
					</nav>
				</div>
				<?php endif; ?>
			</div>

			<div class="footer-bottom">
				<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ) ); ?> — <?php esc_html_e( 'تمام حقوق محفوظ است.', 'fasdent' ); ?></p>
			</div>
		</div>
	</footer>

	<!-- دکمه بازگشت به بالا -->
	<button class="back-to-top" aria-label="<?php esc_attr_e( 'بازگشت به بالا', 'fasdent' ); ?>">
		<i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
	</button>

	<?php wp_footer(); ?>
</body>
</html>
