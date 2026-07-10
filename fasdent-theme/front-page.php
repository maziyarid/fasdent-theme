<?php get_header(); ?>
<section class="hero">
	<div class="container hero-grid">
		<div>
			<h1>کلینیک دندانپزشکی فس‌دنت</h1>
			<p>ایمپلنت، لمینت، ارتودنسی، درمان ریشه و اورژانس دندانپزشکی با کادر حرفه‌ای و تجهیزات مدرن.</p>
			<div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
				<?php fasdent_booking_button( 'رزرو نوبت', 'btn-primary' ); ?>
				<?php fasdent_call_button( 'تماس با کلینیک', 'btn-secondary' ); ?>
			</div>
		</div>
		<div class="card">
			<h2>چرا فس‌دنت؟</h2>
			<ul>
				<li>پشتیبانی ۲۴ ساعته برای اورژانس</li>
				<li>درمان‌های تخصصی با کیفیت بالا</li>
				<li>مشاوره و رزرو آنلاین سریع</li>
			</ul>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="stats">
			<div class="stat-box"><strong>۱۲,۰۰۰+</strong><div>بیمار درمان‌شده</div></div>
			<div class="stat-box"><strong>۱۵</strong><div>سال تجربه</div></div>
			<div class="stat-box"><strong>۳,۵۰۰+</strong><div>ایمپلنت موفق</div></div>
			<div class="stat-box"><strong>۴.۹/۵</strong><div>رضایت بیماران</div></div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<h2>خدمات محبوب</h2>
		<div class="grid-3">
			<?php
			$services = get_posts( array( 'post_type' => 'service', 'numberposts' => 6, 'post_status' => 'publish' ) );
			foreach ( $services as $service ) :
				$icon = fasdent_field( 'service_icon', $service->ID );
			?>
				<article class="service-card">
					<h3><i class="<?php echo esc_attr( $icon ?: 'fa-solid fa-tooth' ); ?>"></i> <?php echo esc_html( $service->post_title ); ?></h3>
					<p><?php echo esc_html( wp_trim_words( $service->post_content, 18 ) ); ?></p>
					<a href="<?php echo esc_url( get_permalink( $service ) ); ?>">مشاهده جزئیات</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php get_footer(); ?>