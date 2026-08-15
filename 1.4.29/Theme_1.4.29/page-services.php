<?php
/** Services overview page. */
get_header();

$services = array(
	array( 'implant', 'implant', 'جراحی تخصصی', 'ایمپلنت دندان', 'ایمپلنت راهکاری برای جایگزینی دندان از دست‌رفته است. سیستم و روش درمان پس از بررسی استخوان، لثه و شرایط اختصاصی بیمار انتخاب می‌شود.', array( 'بدون تراش دندان مجاور', 'برندهای معتبر', 'برنامه‌ریزی دقیق' ) ),
	array( 'crown', 'crown', 'پروتز ثابت', 'روکش دندان', 'روکش زیرکونیا یکی از گزینه‌های بازسازی دندان آسیب‌دیده است که می‌تواند استحکام مناسب را با ظاهری نزدیک به دندان طبیعی ترکیب کند.', array( 'ظاهر هماهنگ', 'استحکام مناسب', 'طراحی اختصاصی' ) ),
	array( 'surgery', 'surgery', 'درمان جراحی', 'جراحی و لثه', 'جراحی دندان عقل، درمان بیماری‌های لثه و پیوند استخوان پس از تشخیص دقیق و با رویکرد حفظ بافت انجام می‌شوند.', array( 'دندان عقل', 'درمان لثه', 'پیوند استخوان' ) ),
	array( 'general', 'general', 'مراقبت پایه', 'درمان عمومی', 'معاینات دوره‌ای، درمان ریشه، ترمیم و جرم‌گیری با هدف حفظ سلامت دهان و پیشگیری از مشکلات پیچیده‌تر.', array( 'درمان ریشه', 'ترمیم و جرم‌گیری', 'معاینه پیشگیرانه' ) ),
);
?>
<main id="main-content">
	<section class="inner-hero">
		<div class="site-container inner-hero-grid">
			<div class="inner-hero-copy">
				<span class="eyebrow no-lines">خدمات کلینیک</span>
				<h1>خدمات تخصصی ما</h1>
				<p>هر طرح درمان پس از معاینه دقیق، بررسی تصاویر موردنیاز و ارزیابی شرایط اختصاصی بیمار تنظیم می‌شود.</p>
			</div>
			<div class="inner-hero-image"><?php alipasandi_theme_image( 'clinic-interior.jpg', 'کلینیک دندانپزشکی دکتر کیوان علی‌پسندی', array( 'sizes' => '(min-width: 768px) 46vw, 100vw', 'loading' => '', 'fetchpriority' => 'high' ) ); ?></div>
		</div>
	</section>
	<section class="section section-bone">
		<div class="site-container service-list">
			<?php foreach ( $services as $service ) : ?>
				<article class="service-row" data-reveal>
					<div class="service-row-icon"><?php echo alipasandi_icon( $service[1], 48 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<div class="service-row-copy">
						<span class="eyebrow no-lines"><?php echo esc_html( $service[2] ); ?></span>
						<h2><?php echo esc_html( $service[3] ); ?></h2>
						<p><?php echo esc_html( $service[4] ); ?></p>
						<div class="tag-list"><?php foreach ( $service[5] as $tag ) : ?><span><?php echo esc_html( $tag ); ?></span><?php endforeach; ?></div>
					</div>
					<div class="service-row-actions">
						<a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'services/' . $service[0] ) ); ?>">اطلاعات بیشتر <?php echo alipasandi_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<a class="button button-outline-dark" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">درخواست نوبت</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
	<section class="section-compact section-dark"><div class="site-container final-cta"><h2>برای انتخاب مسیر درمان سوال دارید؟</h2><p>مشاوره و معاینه، نقطه شروع یک تصمیم دقیق و متناسب با شرایط شماست.</p><a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'contact' ) ); ?>">تماس با کلینیک</a></div></section>
</main>
<?php get_footer(); ?>
