<?php
/** Front page. */
get_header();

$services = array(
	array( 'implant', 'implant', 'ایمپلنت دندان', 'جایگزینی دندان از دست‌رفته با سیستم‌های معتبر و طرح درمان اختصاصی، بدون تراش دندان‌های مجاور.' ),
	array( 'crown', 'crown', 'روکش دندان', 'بازسازی دندان‌های آسیب‌دیده با روکش زیرکونیا؛ ترکیبی از استحکام و ظاهر طبیعی.' ),
	array( 'surgery', 'surgery', 'جراحی و لثه', 'جراحی دندان عقل، درمان بیماری‌های لثه و آماده‌سازی بافت با رویکرد تخصصی.' ),
	array( 'general', 'general', 'درمان عمومی', 'عصب‌کشی، ترمیم، جرم‌گیری و معاینات دوره‌ای با رویکرد پیشگیرانه.' ),
);

$home_steps = array(
	array( '01', 'گفت‌وگو و ارزیابی', 'نیاز اصلی، وضعیت دهان و تصاویر تشخیصی لازم بررسی می‌شوند.', 'general' ),
	array( '02', 'طرح درمان اختصاصی', 'گزینه‌های درمان، ترتیب مراحل و مراقبت‌های لازم متناسب با شرایط شما توضیح داده می‌شوند.', 'clipboard' ),
	array( '03', 'درمان و پیگیری', 'درمان با رویکرد کنترل‌شده انجام می‌شود و روند بهبود در زمان مناسب پیگیری خواهد شد.', 'shield' ),
);

$home_faqs = array(
	array( 'برای رزرو نوبت از کجا شروع کنم؟', 'می‌توانید درخواست نوبت را از فرم آنلاین ثبت کنید یا با کلینیک تماس بگیرید. تاریخ و ساعت انتخاب‌شده پیشنهادی است و پس از تماس کلینیک قطعی خواهد شد.' ),
	array( 'آیا قبل از انتخاب درمان به معاینه نیاز است؟', 'بله. انتخاب روش مناسب به وضعیت دندان، لثه، استخوان، شرایط عمومی و تصاویر تشخیصی وابسته است و بدون بررسی نمی‌توان طرح درمان دقیقی ارائه کرد.' ),
	array( 'آیا نتیجه و زمان درمان برای همه یکسان است؟', 'خیر. مدت و نتیجه درمان به نوع مشکل، شرایط بافتی، پاسخ بدن و همکاری در مراقبت‌های پس از درمان بستگی دارد.' ),
	array( 'چه اطلاعاتی هنگام مراجعه همراه داشته باشم؟', 'در صورت وجود، تصاویر رادیوگرافی جدید، فهرست داروهای مصرفی و اطلاعات مربوط به بیماری‌ها یا درمان‌های قبلی را همراه داشته باشید.' ),
);
?>
<main id="main-content">
	<section class="home-hero" aria-labelledby="home-hero-title">
		<div class="site-container home-hero-grid">
			<div class="hero-doctor" data-reveal>
				<figure class="doctor-card">
					<?php alipasandi_theme_image( 'doctor-keyvan-alipasandi.jpg', 'دکتر کیوان علی‌پسندی', array( 'sizes' => '(min-width: 1200px) 28vw, (min-width: 768px) 46vw, 58vw', 'loading' => '', 'fetchpriority' => 'high' ) ); ?>
					<figcaption class="doctor-caption"><span>دندانپزشک</span>دکتر کیوان علی‌پسندی</figcaption>
				</figure>
			</div>

			<div class="hero-copy" data-reveal>
				<span class="hero-pill">ایمپلنت تخصصی دندان</span>
				<h1 id="home-hero-title">مهارت،<br><span class="gold-text">برآمده از تجربه</span></h1>
				<p class="hero-subtitle">با برندهای معتبر جهانی</p>
				<p class="hero-description">ایمپلنت دندان راهکاری برای جایگزینی دندان از دست‌رفته است که پس از بررسی دقیق می‌تواند عملکرد و ظاهر طبیعی را بازسازی کند.</p>
				<div class="button-row">
					<a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php echo alipasandi_icon( 'calendar', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> رزرو نوبت</a>
					<a class="button button-outline-light" href="<?php echo esc_url( alipasandi_page_url( 'services' ) ); ?>">مشاهده خدمات <?php echo alipasandi_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
			</div>

			<div class="hero-product" data-reveal>
				<?php alipasandi_theme_image( 'implant-hero.jpg', 'رندر پزشکی ایمپلنت تیتانیومی روی زمینه قهوه‌ای', array( 'sizes' => '(min-width: 1200px) 28vw, (min-width: 768px) 42vw, 52vw', 'loading' => '' ) ); ?>
			</div>
		</div>
		<div class="site-container hero-info-grid" aria-label="اطلاعات سریع کلینیک">
			<div class="hero-info-item"><?php echo alipasandi_icon( 'location', 25 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>مطب نوشهر</strong><small>ستارخان، امیراد ۱، طبقه ۵</small></span></div>
			<?php if ( alipasandi_show_treatment_count() ) : ?>
				<div class="hero-info-item"><?php echo alipasandi_icon( 'general', 25 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>بیش از ۱۰٬۰۰۰</strong><small>کیس درمانی تأییدشده</small></span></div>
			<?php else : ?>
				<div class="hero-info-item"><?php echo alipasandi_icon( 'clipboard', 25 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>طرح درمان اختصاصی</strong><small>متناسب با شرایط هر بیمار</small></span></div>
			<?php endif; ?>
			<a class="hero-info-item" href="tel:<?php echo esc_attr( alipasandi_phone_href() ); ?>"><?php echo alipasandi_icon( 'phone', 25 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>مشاوره و نوبت‌دهی</strong><small dir="ltr"><?php echo esc_html( alipasandi_clinic_option( 'clinic_phone' ) ); ?></small></span></a>
		</div>
	</section>

	<section class="section section-bone" aria-labelledby="services-title">
		<div class="site-container">
			<header class="section-heading" data-reveal>
				<span class="eyebrow">خدمات تخصصی</span>
				<h2 id="services-title">راهکارهای تخصصی برای لبخندی ماندگار</h2>
			</header>
			<div class="service-grid">
				<?php foreach ( $services as $service ) : ?>
					<a class="service-card" href="<?php echo esc_url( alipasandi_page_url( 'services/' . $service[0] ) ); ?>" data-reveal>
						<span class="icon-wrap"><?php echo alipasandi_icon( $service[1], 34 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3><?php echo esc_html( $service[2] ); ?></h3>
						<p><?php echo esc_html( $service[3] ); ?></p>
						<span class="card-link">بیشتر بدانید <?php echo alipasandi_icon( 'arrow', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-white home-treatment" aria-labelledby="treatment-title">
		<div class="site-container treatment-split">
			<div class="treatment-copy" data-reveal>
				<span class="eyebrow no-lines">درمان آگاهانه</span>
				<h2 id="treatment-title">تصمیم درمانی، از یک بررسی دقیق آغاز می‌شود.</h2>
				<p>درمان مناسب برای همه افراد یکسان نیست. وضعیت دندان‌ها، سلامت لثه، استخوان و شرایط عمومی باید پیش از انتخاب مسیر درمان بررسی شوند.</p>
				<ul class="treatment-points">
					<li><?php echo alipasandi_icon( 'check', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>تشخیص پیش از پیشنهاد درمان</strong><small>انتخاب گزینه مناسب بر پایه معاینه و اطلاعات تشخیصی</small></span></li>
					<li><?php echo alipasandi_icon( 'check', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>توضیح شفاف گزینه‌ها</strong><small>آگاهی از مراحل، محدودیت‌ها و مراقبت‌های موردنیاز</small></span></li>
					<li><?php echo alipasandi_icon( 'check', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>برنامه متناسب با هر بیمار</strong><small>بدون وعده قطعی یا درمان یکسان برای همه</small></span></li>
				</ul>
				<div class="button-row">
					<a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">رزرو وقت مشاوره</a>
					<a class="text-link" href="<?php echo esc_url( alipasandi_page_url( 'about' ) ); ?>">آشنایی با رویکرد کلینیک <?php echo alipasandi_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
			</div>
			<figure class="treatment-visual" data-reveal>
				<?php alipasandi_theme_image( 'implant-hero.jpg', 'رندر ایمپلنت تک‌دندان در فضای تیره', array( 'sizes' => '(min-width: 1200px) 44vw, (min-width: 768px) 46vw, 100vw' ) ); ?>
				<figcaption><span>رویکرد تخصصی</span><strong>بررسی، برنامه‌ریزی، درمان</strong></figcaption>
			</figure>
		</div>
	</section>

	<section class="section section-dark home-about" aria-labelledby="about-title">
		<div class="site-container about-split">
			<div class="about-copy" data-reveal>
				<span class="eyebrow no-lines">درباره کلینیک</span>
				<h2 id="about-title">تخصص، تجربه،<br><span class="gold-text">اعتماد.</span></h2>
				<p>کلینیک دندانپزشکی دکتر کیوان علی‌پسندی با رویکردی مدرن و دقیق، خدمات تخصصی را در محیطی آرام و حرفه‌ای ارائه می‌دهد.</p>
				<p>هر طرح درمان پس از معاینه و بررسی شرایط اختصاصی بیمار تنظیم می‌شود؛ اولویت ما درمان صحیح و تجربه‌ای قابل اعتماد است.</p>
				<a class="button button-outline-light" href="<?php echo esc_url( alipasandi_page_url( 'about' ) ); ?>">بیشتر درباره ما <?php echo alipasandi_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</div>
			<div class="clinic-image" data-reveal>
				<?php alipasandi_theme_image( 'clinic-interior.jpg', 'فضای پذیرش کلینیک دندانپزشکی دکتر کیوان علی‌پسندی', array( 'sizes' => '(min-width: 1200px) 44vw, (min-width: 768px) 48vw, 100vw' ) ); ?>
			</div>
		</div>
		<div class="site-container about-trust-grid" aria-label="اصول کلینیک">
			<div class="about-trust-item" data-reveal><?php echo alipasandi_icon( 'shield', 27 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>تجهیزات به‌روز</strong><small>با هدف افزایش دقت درمان</small></span></div>
			<div class="about-trust-item" data-reveal><?php echo alipasandi_icon( 'clipboard', 27 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>برنامه‌ریزی اختصاصی</strong><small>متناسب با شرایط هر بیمار</small></span></div>
			<div class="about-trust-item" data-reveal><?php echo alipasandi_icon( 'crown', 27 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>مواد و برندهای معتبر</strong><small>انتخاب بر پایه طرح درمان</small></span></div>
		</div>
	</section>

	<section class="section section-bone home-journey" aria-labelledby="journey-title">
		<div class="site-container">
			<header class="section-heading" data-reveal>
				<span class="eyebrow">مسیر مراجعه</span>
				<h2 id="journey-title">از نخستین بررسی تا پیگیری درمان</h2>
				<p>یک مسیر روشن و قابل‌فهم، بدون شلوغی و وعده‌های غیرواقعی.</p>
			</header>
			<div class="journey-grid">
				<?php foreach ( $home_steps as $step ) : ?>
					<article class="journey-card" data-reveal>
						<div class="journey-card-head"><span class="journey-number"><?php echo esc_html( $step[0] ); ?></span><span class="journey-icon"><?php echo alipasandi_icon( $step[3], 28 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></div>
						<h3><?php echo esc_html( $step[1] ); ?></h3>
						<p><?php echo esc_html( $step[2] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-white home-faq" aria-labelledby="home-faq-title">
		<div class="site-container faq-split">
			<div class="faq-intro" data-reveal>
				<span class="eyebrow no-lines">پیش از مراجعه</span>
				<h2 id="home-faq-title">پاسخ کوتاه به پرسش‌های رایج</h2>
				<p>این پاسخ‌ها عمومی هستند و جایگزین معاینه، تشخیص یا توصیه اختصاصی پزشک نمی‌شوند.</p>
				<a class="text-link" href="<?php echo esc_url( alipasandi_page_url( 'faq' ) ); ?>">مشاهده همه سوالات متداول <?php echo alipasandi_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</div>
			<div class="accordion-list home-accordion">
				<?php foreach ( $home_faqs as $index => $faq ) : $panel_id = 'home-faq-' . $index; ?>
					<div class="accordion-item">
						<button class="accordion-trigger" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-accordion-trigger><span><?php echo esc_html( $faq[0] ); ?></span><?php echo alipasandi_icon( 'chevron', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						<div class="accordion-panel" id="<?php echo esc_attr( $panel_id ); ?>" hidden><?php echo esc_html( $faq[1] ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section-compact section-dark home-booking-section">
		<div class="site-container final-cta appointment-banner" data-reveal>
			<span class="appointment-banner-icon"><?php echo alipasandi_icon( 'phone', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<div class="appointment-banner-copy"><h2>برای دریافت مشاوره یا رزرو نوبت با ما تماس بگیرید.</h2></div>
			<div class="button-row">
				<a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php echo alipasandi_icon( 'calendar', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> رزرو وقت مشاوره</a>
			</div>
		</div>
	</section>

	</main>
<?php get_footer(); ?>
