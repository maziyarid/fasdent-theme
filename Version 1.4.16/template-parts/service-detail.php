<?php
/** Shared detailed service layout. */

if ( ! isset( $alipasandi_service_key ) ) {
	return;
}

$service = alipasandi_get_service( $alipasandi_service_key );
if ( ! $service ) {
	return;
}
?>
<main id="main-content">
	<section class="service-hero">
		<div class="site-container service-hero-grid">
			<div class="service-hero-copy" data-reveal>
				<span class="hero-pill"><?php echo esc_html( $service['eyebrow'] ); ?></span>
				<h1><?php echo esc_html( $service['title'] ); ?><br><span class="gold-text"><?php echo esc_html( $service['title_gold'] ); ?></span></h1>
				<p><?php echo esc_html( $service['intro'] ); ?></p>
				<div class="button-row">
					<a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php echo alipasandi_icon( 'calendar', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> رزرو وقت مشاوره</a>
					<a class="button button-outline-light" href="<?php echo esc_url( alipasandi_page_url( 'contact' ) ); ?>"><?php echo alipasandi_icon( 'phone', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> تماس با کلینیک</a>
				</div>
			</div>
			<div class="service-hero-image" data-reveal>
					<?php alipasandi_theme_image( $service['image'], $service['image_alt'], array( 'sizes' => '(min-width: 1200px) 44vw, (min-width: 768px) 46vw, 100vw', 'loading' => '', 'fetchpriority' => 'high' ) ); ?>
			</div>
		</div>
	</section>

	<section class="section section-bone">
		<div class="site-container content-split">
			<div class="split-copy" data-reveal>
				<span class="eyebrow no-lines"><?php echo esc_html( $service['what_label'] ); ?></span>
				<h2><?php echo esc_html( $service['what_title'] ); ?></h2>
				<?php foreach ( $service['what_text'] as $paragraph ) : ?><p><?php echo esc_html( $paragraph ); ?></p><?php endforeach; ?>
			</div>
			<div class="service-diagram" data-reveal>
				<?php foreach ( $service['diagram'] as $index => $label ) : ?>
					<div class="diagram-item"><span class="diagram-number">0<?php echo esc_html( $index + 1 ); ?></span><strong><?php echo esc_html( $label ); ?></strong></div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-bone bordered-section">
		<div class="site-container">
			<header class="section-heading"><span class="eyebrow"><?php echo esc_html( $service['benefit_label'] ); ?></span><h2><?php echo esc_html( $service['benefit_title'] ); ?></h2></header>
			<div class="benefit-grid">
				<?php foreach ( $service['benefits'] as $benefit ) : ?>
					<article class="benefit-card" data-reveal><span class="icon-wrap"><?php echo alipasandi_icon( $benefit[2], 33 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php echo esc_html( $benefit[0] ); ?></h3><p><?php echo esc_html( $benefit[1] ); ?></p></article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-dark">
		<div class="site-container">
			<header class="section-heading"><span class="eyebrow"><?php echo esc_html( $service['steps_label'] ); ?></span><h2>فرآیند درمان</h2></header>
			<div class="steps-grid">
				<?php foreach ( $service['steps'] as $step ) : ?><article class="step-card" data-reveal><div class="step-number"><?php echo esc_html( $step[0] ); ?></div><h3><?php echo esc_html( $step[1] ); ?></h3><p><?php echo esc_html( $step[2] ); ?></p></article><?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-bone">
		<div class="narrow-container">
			<header class="section-heading"><span class="eyebrow">ارزیابی شرایط</span><h2><?php echo esc_html( $service['candidate_title'] ); ?></h2><p><?php echo esc_html( $service['candidate_text'] ); ?></p></header>
			<div class="medical-note" data-reveal><?php echo alipasandi_icon( 'info', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong><?php echo esc_html( $service['notice_title'] ); ?></strong><p><?php echo esc_html( $service['notice_text'] ); ?></p></div></div>
		</div>
	</section>

	<section class="section-compact section-bone trust-strip">
		<div class="site-container stats-grid <?php echo alipasandi_show_treatment_count() ? 'stats-grid--4' : 'stats-grid--3'; ?>">
			<?php if ( alipasandi_show_treatment_count() ) : ?><div class="stat-item"><strong class="stat-number">+۱۰٬۰۰۰</strong><span class="stat-label">کیس درمانی</span><span class="stat-note">تجربه انجام درمان‌های دندانپزشکی</span></div><?php endif; ?>
			<div class="stat-item"><strong class="stat-number">برندهای معتبر</strong><span class="stat-label">انتخاب متناسب</span><span class="stat-note">بر پایه طرح درمان</span></div>
			<div class="stat-item"><strong class="stat-number">برنامه‌ریزی</strong><span class="stat-label">اختصاصی</span><span class="stat-note">متناسب با شرایط هر بیمار</span></div>
			<div class="stat-item"><strong class="stat-number">پیگیری</strong><span class="stat-label">درمان</span><span class="stat-note">از مشاوره تا مراحل پس از درمان</span></div>
		</div>
	</section>

	<section class="section section-bone bordered-section">
		<div class="narrow-container">
			<header class="section-heading"><span class="eyebrow">سوالات متداول</span><h2>پرسش‌های رایج</h2></header>
			<div class="accordion-list">
				<?php foreach ( $service['faqs'] as $index => $faq ) : $panel_id = 'service-faq-' . $alipasandi_service_key . '-' . $index; ?>
					<div class="accordion-item">
						<button class="accordion-trigger" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-accordion-trigger><span><?php echo esc_html( $faq[0] ); ?></span><?php echo alipasandi_icon( 'chevron', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						<div class="accordion-panel" id="<?php echo esc_attr( $panel_id ); ?>" hidden><?php echo esc_html( $faq[1] ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section-compact section-dark">
		<div class="site-container final-cta">
			<h2><?php echo esc_html( $service['cta_title'] ); ?></h2>
			<p><?php echo esc_html( $service['cta_text'] ); ?></p>
			<div class="button-row"><a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php echo alipasandi_icon( 'calendar', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> رزرو وقت مشاوره</a><a class="phone-link" href="tel:<?php echo esc_attr( alipasandi_phone_href() ); ?>"><?php echo alipasandi_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span dir="ltr"><?php echo esc_html( alipasandi_clinic_option( 'clinic_phone' ) ); ?></span></a></div>
		</div>
	</section>
</main>
