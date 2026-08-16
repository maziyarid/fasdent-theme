<?php
/**
 * Front page — visual campaign plus canonical operational data.
 *
 * @package Alipasandi_Clinic
 * @since 1.4.29
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$canonical_name = trim( (string) alipasandi_clinic_option( 'clinic_business_name' ) );
$clinic_name = '' !== $canonical_name ? $canonical_name : get_bloginfo( 'name' );
$city = trim( (string) alipasandi_clinic_option( 'clinic_city' ) );
$address = function_exists( 'alipasandi_display_address' ) ? alipasandi_display_address() : trim( (string) alipasandi_clinic_option( 'clinic_address' ) );
$phone = trim( (string) alipasandi_clinic_option( 'clinic_phone' ) );
$phone_href = trim( (string) alipasandi_phone_href() );
$managed_services = array();
if ( function_exists( 'alipasandi_service_registry' ) ) {
	foreach ( alipasandi_service_registry() as $item ) {
		if ( ! empty( $item['content_managed'] ) && ! empty( $item['page_slug'] ) ) {
			$managed_services[] = $item;
		}
	}
}
$nap_fields = function_exists( 'alipasandi_nap_surface_fields' ) ? alipasandi_nap_surface_fields() : array( 'name'=>$canonical_name, 'address'=>$address, 'phone'=>$phone_href );
?>
<main id="main-content">
	<?php if ( function_exists( 'alipasandi_nap_surface_marker' ) ) { alipasandi_nap_surface_marker( 'home', $nap_fields ); } ?>
	<section class="home-hero">
		<div class="site-container home-hero-grid">
			<figure class="hero-product" aria-hidden="true">
				<?php alipasandi_theme_image( 'crown-hero.jpg', '', array( 'decorative'=>true, 'sizes'=>'(min-width:1100px) 24vw, 44vw' ) ); ?>
			</figure>
			<div class="hero-copy">
				<span class="hero-pill">مراقبت دقیق، تصمیم آگاهانه</span>
				<h1><?php echo esc_html( $clinic_name ); ?></h1>
				<p class="hero-subtitle">خدمات دندانپزشکی با برنامه درمانی متناسب با شرایط هر مراجعه‌کننده</p>
				<p class="hero-description">اطلاعات اولیه، مسیر مراجعه و درخواست نوبت از یک منبع عملیاتی واحد ارائه می‌شود. تصمیم نهایی درمان پس از معاینه حضوری انجام خواهد شد.</p>
				<div class="button-row">
					<a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php echo alipasandi_icon( 'calendar', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> درخواست نوبت</a>
					<a class="button button-outline-light" href="<?php echo esc_url( alipasandi_page_url( 'services' ) ); ?>">مشاهده خدمات</a>
				</div>
			</div>
			<figure class="hero-doctor doctor-card">
				<?php alipasandi_theme_image( 'doctor-keyvan-alipasandi.jpg', 'دندانپزشک کلینیک', array( 'sizes'=>'(min-width:1100px) 29vw, 52vw', 'loading'=>'eager', 'fetchpriority'=>'high' ) ); ?>
				<figcaption class="doctor-caption"><?php echo esc_html( $clinic_name ); ?><span>ارزیابی حضوری و برنامه درمانی شفاف</span></figcaption>
			</figure>
		</div>
		<div class="site-container hero-info-grid">
			<?php if ( '' !== $address ) : ?>
			<div class="hero-info-item"><?php echo alipasandi_icon( 'location', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>نشانی مراجعه</strong><small><?php echo esc_html( $address ); ?></small></span></div>
			<?php endif; ?>
			<?php if ( '' !== $phone_href && '' !== $phone ) : ?>
			<a class="hero-info-item" href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo alipasandi_icon( 'phone', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>تماس مستقیم</strong><small dir="ltr"><?php echo esc_html( $phone ); ?></small></span></a>
			<?php endif; ?>
			<div class="hero-info-item"><?php echo alipasandi_icon( 'calendar', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>درخواست نوبت</strong><small>ثبت زمان پیشنهادی؛ تأیید نهایی پس از تماس کلینیک</small></span></div>
		</div>
	</section>

	<section class="section section-bone">
		<div class="site-container">
			<header class="section-heading"><span class="eyebrow">خدمات کلینیک</span><h2>مسیرهای درمانی اصلی</h2><p>شرح هر خدمت برای آشنایی اولیه است؛ مناسب‌بودن درمان تنها پس از معاینه مشخص می‌شود.</p></header>
			<div class="service-grid">
			<?php foreach ( $managed_services as $item ) :
				$data = function_exists( 'alipasandi_get_service' ) ? alipasandi_get_service( $item['key'] ) : array();
				$summary = isset( $data['intro'] ) ? wp_trim_words( wp_strip_all_tags( $data['intro'] ), 24, '…' ) : '';
				?>
				<article class="service-card" data-reveal>
					<span class="icon-wrap"><?php echo alipasandi_icon( $item['icon'], 27 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<h3><?php echo esc_html( $item['label'] ); ?></h3>
					<?php if ( '' !== $summary ) : ?><p><?php echo esc_html( $summary ); ?></p><?php endif; ?>
					<a class="card-link" href="<?php echo esc_url( alipasandi_page_url( $item['page_slug'] ) ); ?>">اطلاعات بیشتر <span aria-hidden="true">←</span></a>
				</article>
			<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-dark home-about">
		<div class="site-container about-split">
			<div class="about-copy" data-reveal><span class="eyebrow">درباره رویکرد ما</span><h2>درمان با توضیح روشن و ارزیابی مرحله‌به‌مرحله</h2><p>هدف، ارائه اطلاعات قابل فهم، بررسی گزینه‌های متناسب و ثبت دقیق مسیر درمان است. نتیجه و زمان درمان برای هر فرد متفاوت است و وعده قطعی داده نمی‌شود.</p><a class="button button-outline-light" href="<?php echo esc_url( alipasandi_page_url( 'about' ) ); ?>">آشنایی بیشتر</a></div>
			<figure class="clinic-image" data-reveal><?php alipasandi_theme_image( 'clinic-interior.jpg', 'فضای داخلی کلینیک', array( 'sizes'=>'(min-width:900px) 46vw, 92vw' ) ); ?></figure>
		</div>
	</section>

	<section class="section section-bone">
		<div class="site-container treatment-split">
			<div class="treatment-copy" data-reveal><span class="eyebrow">پیش از درمان</span><h2>از معاینه تا انتخاب برنامه مناسب</h2><p>سوابق پزشکی، وضعیت دهان و دندان، تصاویر تشخیصی لازم و اولویت‌های مراجعه‌کننده در تصمیم درمانی بررسی می‌شوند.</p><ul class="treatment-points"><li><?php echo alipasandi_icon( 'shield', 25 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>ارزیابی فردی</strong><small>بررسی شرایط و محدودیت‌های اختصاصی هر مراجعه‌کننده</small></span></li><li><?php echo alipasandi_icon( 'info', 25 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong>رضایت آگاهانه</strong><small>توضیح گزینه‌ها، مراقبت‌ها و ریسک‌های مرتبط پیش از اقدام</small></span></li></ul><a class="text-link" href="<?php echo esc_url( alipasandi_page_url( 'faq' ) ); ?>">پرسش‌های متداول <span aria-hidden="true">←</span></a></div>
			<figure class="treatment-visual" data-reveal><?php alipasandi_theme_image( 'implant-hero.jpg', 'تصویر آموزشی مرتبط با درمان دندان', array( 'sizes'=>'(min-width:900px) 44vw, 92vw' ) ); ?></figure>
		</div>
	</section>

	<section class="section section-dark home-booking-section">
		<div class="site-container">
			<div class="appointment-banner" data-reveal><span class="appointment-banner-icon"><?php echo alipasandi_icon( 'calendar', 27 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><div><span class="eyebrow">هماهنگی مراجعه<?php echo '' !== $city ? ' در ' . esc_html( $city ) : ''; ?></span><h2>زمان پیشنهادی خود را ثبت کنید</h2><p>ثبت فرم به‌معنای قطعی‌شدن نوبت نیست؛ کلینیک برای تأیید نهایی تماس می‌گیرد.</p></div><div class="button-row"><a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">درخواست نوبت</a><?php if ( '' !== $phone_href ) : ?><a class="button button-outline-dark" href="tel:<?php echo esc_attr( $phone_href ); ?>">تماس مستقیم</a><?php endif; ?></div></div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
