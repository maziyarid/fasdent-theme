<?php
/** Shared detailed service layout. */

if ( ! isset( $alipasandi_service_key ) ) {
	return;
}

$service = alipasandi_get_service( $alipasandi_service_key );
if ( ! $service ) {
	return;
}
$svc = function ( $key, $default = '' ) use ( $service ) {
	return isset( $service[ $key ] ) ? $service[ $key ] : $default;
};
$kses = function ( $html ) {
	if ( function_exists( 'alipasandi_service_sanitize_html' ) ) {
		return alipasandi_service_sanitize_html( $html );
	}
	return wp_kses( (string) $html, function_exists( 'alipasandi_service_allowed_html' ) ? alipasandi_service_allowed_html() : array() );
};
$image_alt = (string) $svc( 'image_alt', '' );
$image_id  = absint( $svc( 'image_id', 0 ) );
if ( $image_id && ( ! wp_attachment_is_image( $image_id ) || ! get_attached_file( $image_id ) ) ) {
	$image_id = 0;
}
if ( $image_id && empty( $image_alt ) && empty( $svc( 'image_decorative', 0 ) ) ) {
	$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
}
$what_text = array_filter( (array) $svc( 'what_text', array() ), 'strlen' );
$diagram   = array_filter( (array) $svc( 'diagram', array() ), 'strlen' );
$benefits  = (array) $svc( 'benefits', array() );
$steps     = (array) $svc( 'steps', array() );
$faqs      = (array) $svc( 'faqs', array() );
?>
<main id="main-content">
	<section class="service-hero">
		<div class="site-container service-hero-grid">
			<div class="service-hero-copy" data-reveal>
				<span class="hero-pill"><?php echo esc_html( $svc( 'eyebrow' ) ); ?></span>
				<h1><?php echo esc_html( $svc( 'title' ) ); ?><br><span class="gold-text"><?php echo esc_html( $svc( 'title_gold' ) ); ?></span></h1>
				<p><?php echo $kses( $svc( 'intro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<div class="button-row">
					<a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php echo alipasandi_icon( 'calendar', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> درخواست وقت مشاوره</a>
					<a class="button button-outline-light" href="<?php echo esc_url( alipasandi_page_url( 'contact' ) ); ?>"><?php echo alipasandi_icon( 'phone', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> تماس با کلینیک</a>
				</div>
			</div>
			<div class="service-hero-image" data-reveal>
				<?php
				if ( $image_id ) {
					echo wp_get_attachment_image(
						$image_id,
						'full',
						false,
						array(
							'alt'           => $image_alt,
							'sizes'         => '(min-width: 1200px) 44vw, (min-width: 768px) 46vw, 100vw',
							'fetchpriority' => 'high',
							'decoding'      => 'async',
						)
					);
				} else {
					alipasandi_theme_image( $svc( 'image' ), $image_alt, array( 'sizes' => '(min-width: 1200px) 44vw, (min-width: 768px) 46vw, 100vw', 'loading' => '', 'fetchpriority' => 'high' ) );
				}
				?>
			</div>
		</div>
	</section>

	<?php if ( $svc( 'what_title' ) || $what_text || $diagram ) : ?>
	<section id="service-overview" class="section section-bone">
		<div class="site-container content-split">
			<div class="split-copy" data-reveal>
				<span class="eyebrow no-lines"><?php echo esc_html( $svc( 'what_label' ) ); ?></span>
				<h2><?php echo esc_html( $svc( 'what_title' ) ); ?></h2>
				<?php
				if ( is_array( $what_text ) ) :
					foreach ( $what_text as $paragraph ) :
						?><p><?php echo $kses( $paragraph ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php
					endforeach;
				endif;
				?>
			</div>
			<div class="service-diagram" data-reveal>
				<?php foreach ( $diagram as $index => $label ) : ?>
					<div class="diagram-item"><span class="diagram-number">0<?php echo esc_html( $index + 1 ); ?></span><strong><?php echo esc_html( $label ); ?></strong></div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $benefits ) : ?>
	<section id="service-benefits" class="section section-bone bordered-section">
		<div class="site-container">
			<header class="section-heading"><span class="eyebrow"><?php echo esc_html( $svc( 'benefit_label' ) ); ?></span><h2><?php echo esc_html( $svc( 'benefit_title' ) ); ?></h2></header>
			<div class="benefit-grid">
				<?php foreach ( $benefits as $benefit ) : ?>
					<article class="benefit-card" data-reveal><span class="icon-wrap"><?php echo alipasandi_icon( isset( $benefit[2] ) ? $benefit[2] : 'tooth', 33 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php echo esc_html( isset( $benefit[0] ) ? $benefit[0] : '' ); ?></h3><p><?php echo $kses( isset( $benefit[1] ) ? $benefit[1] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $steps ) : ?>
	<section id="service-steps" class="section section-dark">
		<div class="site-container">
			<header class="section-heading"><span class="eyebrow"><?php echo esc_html( $svc( 'steps_label' ) ); ?></span><h2>فرآیند درمان</h2></header>
			<div class="steps-grid">
				<?php foreach ( $steps as $step ) : ?><article class="step-card" data-reveal><div class="step-number"><?php echo esc_html( isset( $step[0] ) ? $step[0] : '' ); ?></div><h3><?php echo esc_html( isset( $step[1] ) ? $step[1] : '' ); ?></h3><p><?php echo $kses( isset( $step[2] ) ? $step[2] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></article><?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $svc( 'candidate_title' ) || $svc( 'candidate_text' ) || $svc( 'notice_title' ) || $svc( 'notice_text' ) ) : ?>
	<section id="service-candidacy" class="section section-bone">
		<div class="narrow-container">
			<header class="section-heading"><span class="eyebrow">ارزیابی شرایط</span><h2><?php echo esc_html( $svc( 'candidate_title' ) ); ?></h2><p><?php echo $kses( $svc( 'candidate_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></header>
			<div class="medical-note" data-reveal><?php echo alipasandi_icon( 'info', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong><?php echo esc_html( $svc( 'notice_title' ) ); ?></strong><p><?php echo $kses( $svc( 'notice_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></div></div>
		</div>
	</section>
	<?php endif; ?>

	<section class="section-compact section-bone trust-strip">
		<div class="site-container stats-grid <?php echo alipasandi_show_treatment_count() ? 'stats-grid--4' : 'stats-grid--3'; ?>">
			<?php if ( alipasandi_show_treatment_count() ) : ?><div class="stat-item"><strong class="stat-number">+۱۰٬۰۰۰</strong><span class="stat-label">کیس درمانی</span><span class="stat-note">تجربه انجام درمان‌های دندانپزشکی</span></div><?php endif; ?>
			<div class="stat-item"><strong class="stat-number">برندهای معتبر</strong><span class="stat-label">انتخاب متناسب</span><span class="stat-note">بر پایه طرح درمان</span></div>
			<div class="stat-item"><strong class="stat-number">برنامه‌ریزی</strong><span class="stat-label">اختصاصی</span><span class="stat-note">متناسب با شرایط هر بیمار</span></div>
			<div class="stat-item"><strong class="stat-number">پیگیری</strong><span class="stat-label">درمان</span><span class="stat-note">از مشاوره تا مراحل پس از درمان</span></div>
		</div>
	</section>

	<?php if ( $faqs ) : ?>
	<section id="service-faq" class="section section-bone bordered-section">
		<div class="narrow-container">
			<header class="section-heading"><span class="eyebrow">سوالات متداول</span><h2>پرسش‌های رایج</h2></header>
			<div class="accordion-list">
				<?php foreach ( $faqs as $index => $faq ) : $panel_id = 'service-faq-' . $alipasandi_service_key . '-' . substr( md5( (string) ( isset( $faq[0] ) ? $faq[0] : '' ) ), 0, 10 ) . '-' . $index; $trigger_id = $panel_id . '-trigger'; ?>
					<div class="accordion-item">
						<button id="<?php echo esc_attr( $trigger_id ); ?>" class="accordion-trigger" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-accordion-trigger><span><?php echo esc_html( isset( $faq[0] ) ? $faq[0] : '' ); ?></span><?php echo alipasandi_icon( 'chevron', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						<div class="accordion-panel" id="<?php echo esc_attr( $panel_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $trigger_id ); ?>" hidden><?php echo $kses( isset( $faq[1] ) ? $faq[1] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $svc( 'cta_title' ) || $svc( 'cta_text' ) ) : ?>
	<section id="service-cta" class="section-compact section-dark">
		<div class="site-container final-cta">
			<h2><?php echo esc_html( $svc( 'cta_title' ) ); ?></h2>
			<p><?php echo $kses( $svc( 'cta_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<div class="button-row"><a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>"><?php echo alipasandi_icon( 'calendar', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> درخواست وقت مشاوره</a><?php if ( alipasandi_phone_href() ) : ?><a class="phone-link" href="tel:<?php echo esc_attr( alipasandi_phone_href() ); ?>"><?php echo alipasandi_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span dir="ltr"><?php echo esc_html( alipasandi_clinic_option( 'clinic_phone' ) ); ?></span></a><?php endif; ?></div>
		</div>
	</section>
	<?php endif; ?>
</main>
