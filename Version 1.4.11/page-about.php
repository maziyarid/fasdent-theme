<?php
/** About page. */
get_header();

$credentials = array(
	'دکترای دندانپزشکی عمومی',
	'تمرکز حرفه‌ای بر درمان‌های ایمپلنت و پروتز',
	'آموزش و همکاری با سیستم‌های معتبر درمانی',
	'رویکرد مبتنی بر شواهد و طرح درمان اختصاصی',
);
if ( alipasandi_show_treatment_count() ) {
	$credentials[] = 'بیش از ۱۰٬۰۰۰ کیس درمانی';
}
$values = array(
	array( 'درمان مبتنی بر شواهد', 'پروتکل‌های درمانی با تکیه بر یافته‌های علمی و استانداردهای حرفه‌ای انتخاب می‌شوند.' ),
	array( 'طرح درمان اختصاصی', 'هر بیمار بر اساس شرایط دهان، سلامت عمومی و اولویت‌های درمانی خود ارزیابی می‌شود.' ),
	array( 'تجهیزات به‌روز', 'ابزارهای تشخیصی و درمانی با هدف افزایش دقت و آسایش بیمار به کار گرفته می‌شوند.' ),
	array( 'پیگیری پس از درمان', 'فرآیند مراقبت با توضیحات روشن و پیگیری متناسب با نوع درمان ادامه پیدا می‌کند.' ),
);
?>
<main id="main-content">
	<section class="inner-hero">
		<div class="site-container inner-hero-grid">
			<div class="inner-hero-copy" data-reveal>
				<span class="eyebrow no-lines">درباره ما</span>
				<h1>تخصص، تجربه،<br><span class="gold-text">اعتماد.</span></h1>
				<p>کلینیک دندانپزشکی دکتر کیوان علی‌پسندی خدمات تخصصی را با رویکردی مدرن، دقیق و بیمارمحور ارائه می‌دهد. هدف ما یک طرح درمان روشن و تجربه‌ای آرام و قابل اعتماد است.</p>
				<a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php echo alipasandi_icon( 'calendar', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> رزرو نوبت</a>
			</div>
			<div class="inner-hero-image" data-reveal><?php alipasandi_theme_image( 'doctor-keyvan-alipasandi.jpg', 'دکتر کیوان علی‌پسندی', array( 'sizes' => '(min-width: 768px) 46vw, 100vw', 'loading' => '', 'fetchpriority' => 'high' ) ); ?></div>
		</div>
	</section>

	<section class="section section-bone">
		<div class="site-container content-split">
			<div class="split-copy" data-reveal>
				<span class="eyebrow no-lines">سوابق حرفه‌ای</span>
				<h2>دکتر کیوان علی‌پسندی</h2>
				<ul class="credential-list">
					<?php foreach ( $credentials as $credential ) : ?>
						<li><span class="check-circle"><?php echo alipasandi_icon( 'check', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span><?php echo esc_html( $credential ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="feature-image" data-reveal><?php alipasandi_theme_image( 'clinic-interior.jpg', 'محیط پذیرش کلینیک', array( 'sizes' => '(min-width: 768px) 46vw, 100vw' ) ); ?></div>
		</div>
	</section>

	<section class="section section-dark">
		<div class="site-container">
			<header class="section-heading"><span class="eyebrow">ارزش‌های ما</span><h2>چرا کلینیک ما؟</h2></header>
			<div class="value-grid">
				<?php foreach ( $values as $value ) : ?><article class="value-card" data-reveal><h3><?php echo esc_html( $value[0] ); ?></h3><p><?php echo esc_html( $value[1] ); ?></p></article><?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section-compact section-bone trust-strip"><div class="site-container stats-grid <?php echo alipasandi_show_treatment_count() ? 'stats-grid--4' : 'stats-grid--3'; ?>">
		<?php if ( alipasandi_show_treatment_count() ) : ?><div class="stat-item"><strong class="stat-number">+۱۰٬۰۰۰</strong><span class="stat-label">کیس درمانی</span><span class="stat-note">تجربه انجام درمان‌های دندانپزشکی</span></div><?php endif; ?>
		<div class="stat-item"><strong class="stat-number">دقت</strong><span class="stat-label">در تشخیص</span><span class="stat-note">ارزیابی پیش از انتخاب درمان</span></div>
		<div class="stat-item"><strong class="stat-number">شفافیت</strong><span class="stat-label">در ارتباط</span><span class="stat-note">توضیح گزینه‌ها و محدودیت‌ها</span></div>
		<div class="stat-item"><strong class="stat-number">پیگیری</strong><span class="stat-label">پس از درمان</span><span class="stat-note">متناسب با نیاز هر بیمار</span></div>
	</div></section>

	<section class="section-compact section-dark"><div class="site-container final-cta"><h2>برای آشنایی و مشاوره آماده‌اید؟</h2><p>برای ارزیابی اولیه و بررسی شرایط درمان، با کلینیک در تماس باشید.</p><div class="button-row"><a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">رزرو وقت مشاوره</a><a class="phone-link" href="tel:<?php echo esc_attr( alipasandi_phone_href() ); ?>"><?php echo alipasandi_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span dir="ltr"><?php echo esc_html( alipasandi_clinic_option( 'clinic_phone' ) ); ?></span></a></div></div></section>
</main>
<?php get_footer(); ?>
