	</main>
	<footer class="footer">
		<div class="container">
			<div class="grid-3">
				<div>
					<h3>کلینیک دندانپزشکی فس‌دنت</h3>
					<p>درمان‌های تخصصی دندانپزشکی، ایمپلنت، زیبایی و اورژانس با رویکرد دقیق و مراقبت حرفه‌ای.</p>
				</div>
				<div>
					<h3>لینک‌های سریع</h3>
					<?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => false, 'fallback_cb' => false ) ); ?>
				</div>
				<div>
					<h3>تماس با ما</h3>
					<p>تلفن: <?php echo esc_html( fasdent_phone() ); ?></p>
					<p>ایمیل: <?php echo esc_html( get_theme_mod( 'fasdent_email', 'info@fasdent.ir' ) ); ?></p>
					<p>آدرس: <?php echo esc_html( get_theme_mod( 'fasdent_address', 'تهران' ) ); ?></p>
				</div>
			</div>
		</div>
	</footer>
	<?php wp_footer(); ?>
</body>
</html>
