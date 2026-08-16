<?php
/** Services overview page. */
get_header();

$service_presentations = array(
	'implant' => array( 'جراحی تخصصی', 'ایمپلنت راهکاری برای جایگزینی دندان از دست‌رفته است. سیستم و روش درمان پس از بررسی استخوان، لثه و شرایط اختصاصی بیمار انتخاب می‌شود.', array( 'بدون تراش دندان مجاور', 'برندهای معتبر', 'برنامه‌ریزی دقیق' ) ),
	'crown'   => array( 'پروتز ثابت', 'روکش زیرکونیا یکی از گزینه‌های بازسازی دندان آسیب‌دیده است که می‌تواند استحکام مناسب را با ظاهری نزدیک به دندان طبیعی ترکیب کند.', array( 'ظاهر هماهنگ', 'استحکام مناسب', 'طراحی اختصاصی' ) ),
	'surgery' => array( 'درمان جراحی', 'جراحی دندان عقل، درمان بیماری‌های لثه و پیوند استخوان پس از تشخیص دقیق و با رویکرد حفظ بافت انجام می‌شوند.', array( 'دندان عقل', 'درمان لثه', 'پیوند استخوان' ) ),
	'general' => array( 'مراقبت پایه', 'معاینات دوره‌ای، درمان ریشه، ترمیم و جرم‌گیری با هدف حفظ سلامت دهان و پیشگیری از مشکلات پیچیده‌تر.', array( 'درمان ریشه', 'ترمیم و جرم‌گیری', 'معاینه پیشگیرانه' ) ),
);
$services = array();
foreach ( alipasandi_service_links() as $service_link ) {
	$key = $service_link['key'];
	$presentation = isset( $service_presentations[ $key ] ) ? $service_presentations[ $key ] : array( 'خدمات دندانپزشکی', '', array() );
	$data = function_exists( 'alipasandi_get_service' ) ? alipasandi_get_service( $key ) : array();
	$summary = ! empty( $data['intro'] ) ? wp_strip_all_tags( $data['intro'] ) : $presentation[1];
	$tags = array();
	if ( ! empty( $data['benefits'] ) && is_array( $data['benefits'] ) ) {
		foreach ( array_slice( $data['benefits'], 0, 3 ) as $benefit ) {
			if ( is_array( $benefit ) && ! empty( $benefit[0] ) ) { $tags[] = wp_strip_all_tags( $benefit[0] ); }
		}
	}
	if ( empty( $tags ) ) { $tags = $presentation[2]; }
	$services[] = array( 'key'=>$key, 'icon'=>$service_link['icon'], 'eyebrow'=>$presentation[0], 'label'=>$service_link['label'], 'summary'=>$summary, 'tags'=>$tags, 'url'=>$service_link['url'] );
}
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
					<div class="service-row-icon"><?php echo alipasandi_icon( $service['icon'], 48 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<div class="service-row-copy">
						<span class="eyebrow no-lines"><?php echo esc_html( $service['eyebrow'] ); ?></span>
						<h2><?php echo esc_html( $service['label'] ); ?></h2>
						<?php if ( '' !== $service['summary'] ) : ?><p><?php echo esc_html( $service['summary'] ); ?></p><?php endif; ?>
						<?php if ( ! empty( $service['tags'] ) ) : ?><div class="tag-list"><?php foreach ( $service['tags'] as $tag ) : ?><span><?php echo esc_html( $tag ); ?></span><?php endforeach; ?></div><?php endif; ?>
					</div>
					<div class="service-row-actions">
						<a class="button button-gold" href="<?php echo esc_url( $service['url'] ); ?>">اطلاعات بیشتر <?php echo alipasandi_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<a class="button button-outline-dark" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">درخواست نوبت</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
	<section class="section-compact section-dark"><div class="site-container final-cta"><h2>برای انتخاب مسیر درمان سوال دارید؟</h2><p>مشاوره و معاینه، نقطه شروع یک تصمیم دقیق و متناسب با شرایط شماست.</p><a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'contact' ) ); ?>">تماس با کلینیک</a></div></section>
</main>
<?php get_footer(); ?>
