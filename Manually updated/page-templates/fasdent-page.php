<?php
/**
 * Template Name: Fasdent Sample Page
 * ---------------------------------------------------------------------------
 * صفحه عمومی — Fasdent (کلینیک دندانپزشکی)
 *
 * قالب تولیدی و آماده‌ی استفاده برای صفحات ایستا (about, services, contact,
 * legal, ...) با ماژول‌های اعتماد و انطباق:
 *   - Reading progress bar
 *   - Breadcrumb (سازگار با Schema.org BreadcrumbList)
 *   - Hero با گرادیانت برند و نشان‌های اعتماد
 *   - Editor's Note / Clinical Review Box (در صورت وجود متادیتا)
 *   - Quick Answer (پاسخ کوتاه ۴۰–۷۰ کلمه)
 *   - Sticky TOC ساخته‌شده از H2/H3 محتوا
 *   - محتوای اصلی (the_content) با کلاس .prose
 *   - Disclaimer stack: آموزشی، اورژانس، نتایج، حریم خصوصی
 *   - CTA + شماره اورژانس + خبرنامه (بدون PHI)
 *   - نمایش مجوز و نظام پزشکی
 *   - Reveal-on-scroll animations (data-reveal)
 *   - JSON-LD WebPage + BreadcrumbList
 *
 * انطباق‌ها:
 *   • HIPAA-aware: بدون دریافت PHI، honeypot، ارسال روی HTTPS، BAA note.
 *   • WCAG 2.1 AA: skip link، heading order، focus visible، aria-labels.
 *   • FTC: بدون تضمین/برتری اثبات‌نشده، «نتایج ممکن است متفاوت باشد».
 *
 * @package Fasdent
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/* -------------------------------------------------------------------------
 * متادیتا و تنظیمات
 * ---------------------------------------------------------------------- */
$post_id                = get_the_ID();
$page_subtitle          = get_post_meta( $post_id, 'fasdent_subtitle', true );
$page_kicker            = get_post_meta( $post_id, 'fasdent_kicker', true );          // برچسب کوچک بالای H1
$quick_answer           = get_post_meta( $post_id, 'fasdent_quick_answer', true );
$reviewer_name          = get_post_meta( $post_id, 'fasdent_reviewer_name', true );
$reviewer_credentials   = get_post_meta( $post_id, 'fasdent_reviewer_credentials', true );
$reviewer_license       = get_post_meta( $post_id, 'fasdent_reviewer_license', true ); // «نظام پزشکی: xxxxx»
$review_date            = get_post_meta( $post_id, 'fasdent_review_date', true );
$reading_time           = get_post_meta( $post_id, 'fasdent_reading_time', true );
$hero_image             = get_post_meta( $post_id, 'fasdent_hero_image', true );       // URL دلخواه، در نبود thumbnail
$hero_badges_raw        = get_post_meta( $post_id, 'fasdent_hero_badges', true );      // «icon|label» در هر خط
$primary_cta_label      = get_post_meta( $post_id, 'fasdent_primary_cta_label', true );
$primary_cta_url        = get_post_meta( $post_id, 'fasdent_primary_cta_url', true );
$emergency_phone        = get_option( 'fasdent_emergency_phone', '۰۲۱-XXXXXXXX' );
$booking_url            = get_option( 'fasdent_booking_url', esc_url( home_url( '/reserve/' ) ) );
$show_toc               = get_post_meta( $post_id, 'fasdent_show_toc', true );         // '1' برای نمایش
$show_toc               = ( '' === $show_toc ) ? '1' : $show_toc;                       // پیش‌فرض: نمایش

// پارس نشان‌های hero
$hero_badges = array();
if ( $hero_badges_raw ) {
	foreach ( preg_split( "/\r\n|\n|\r/", $hero_badges_raw ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line ) );
		if ( count( $parts ) === 2 && $parts[0] && $parts[1] ) {
			$hero_badges[] = array( 'icon' => $parts[0], 'label' => $parts[1] );
		}
	}
}
if ( empty( $hero_badges ) ) {
	// پیش‌فرض‌های اعتماد
	$hero_badges = array(
		array( 'icon' => 'fa-solid fa-user-doctor',         'label' => 'بازبینی بالینی شده' ),
		array( 'icon' => 'fa-solid fa-shield-halved',       'label' => 'انطباق با HIPAA' ),
		array( 'icon' => 'fa-solid fa-universal-access',    'label' => 'دسترس‌پذیر (WCAG 2.1 AA)' ),
		array( 'icon' => 'fa-solid fa-lock',                'label' => 'ارسال رمزنگاری‌شده' ),
	);
}
?>

<a class="skip-link" href="#main"><?php esc_html_e( 'پرش به محتوای اصلی', 'fasdent' ); ?></a>
<div class="reading-progress" aria-hidden="true"><span class="reading-progress__bar"></span></div>

<?php // Breadcrumb ?>
<nav class="fasdent-breadcrumb" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'fasdent' ); ?>">
	<div class="container">
		<ol itemscope itemtype="https://schema.org/BreadcrumbList">
			<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<a itemprop="item" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<i class="fa-solid fa-house" aria-hidden="true"></i>
					<span itemprop="name"><?php esc_html_e( 'خانه', 'fasdent' ); ?></span>
				</a>
				<meta itemprop="position" content="1" />
			</li>
			<?php
			$ancestors = array_reverse( get_post_ancestors( $post_id ) );
			$pos       = 2;
			foreach ( $ancestors as $ancestor_id ) : ?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
					<a itemprop="item" href="<?php echo esc_url( get_permalink( $ancestor_id ) ); ?>">
						<span itemprop="name"><?php echo esc_html( get_the_title( $ancestor_id ) ); ?></span>
					</a>
					<meta itemprop="position" content="<?php echo esc_attr( $pos ); ?>" />
				</li>
				<?php $pos++; endforeach; ?>
			<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
				<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
				<span itemprop="name"><?php the_title(); ?></span>
				<meta itemprop="position" content="<?php echo esc_attr( $pos ); ?>" />
			</li>
		</ol>
	</div>
</nav>

<main id="main" role="main" class="fasdent-page">

	<?php while ( have_posts() ) : the_post(); ?>

	<article <?php post_class( 'fasdent-page__article' ); ?> itemscope itemtype="https://schema.org/WebPage">

		<?php /* ---------------------------------------------------------
		 *  HERO
		 * -------------------------------------------------------- */ ?>
		<header class="page-hero" data-reveal="fade-up">
			<div class="page-hero__bg" aria-hidden="true">
				<span class="page-hero__blob page-hero__blob--a"></span>
				<span class="page-hero__blob page-hero__blob--b"></span>
				<span class="page-hero__grid"></span>
			</div>

			<div class="container page-hero__inner">
				<div class="page-hero__content">
					<?php if ( $page_kicker ) : ?>
						<p class="page-hero__kicker">
							<i class="fa-solid fa-tooth" aria-hidden="true"></i>
							<span><?php echo esc_html( $page_kicker ); ?></span>
						</p>
					<?php endif; ?>

					<h1 class="page-hero__title" itemprop="name headline"><?php the_title(); ?></h1>

					<?php if ( $page_subtitle ) : ?>
						<p class="page-hero__subtitle" itemprop="description"><?php echo esc_html( $page_subtitle ); ?></p>
					<?php endif; ?>

					<div class="page-hero__badges" role="list" aria-label="<?php esc_attr_e( 'نشانه‌های اعتماد', 'fasdent' ); ?>">
						<?php foreach ( $hero_badges as $badge ) : ?>
							<span class="hero-badge" role="listitem">
								<i class="<?php echo esc_attr( $badge['icon'] ); ?>" aria-hidden="true"></i>
								<span><?php echo esc_html( $badge['label'] ); ?></span>
							</span>
						<?php endforeach; ?>
					</div>

					<div class="page-hero__meta">
						<?php if ( $reading_time ) : ?>
							<span><i class="fa-regular fa-clock" aria-hidden="true"></i>
								<?php echo esc_html( sprintf( __( '%s دقیقه مطالعه', 'fasdent' ), $reading_time ) ); ?>
							</span>
						<?php endif; ?>
						<span>
							<i class="fa-solid fa-rotate" aria-hidden="true"></i>
							<?php esc_html_e( 'آخرین بروزرسانی:', 'fasdent' ); ?>
							<time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>" itemprop="dateModified">
								<?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?>
							</time>
						</span>
					</div>

					<div class="page-hero__actions">
						<a class="btn btn--primary" href="<?php echo esc_url( $primary_cta_url ? $primary_cta_url : $booking_url ); ?>">
							<i class="fa-solid fa-calendar-plus" aria-hidden="true"></i>
							<?php echo esc_html( $primary_cta_label ? $primary_cta_label : __( 'دریافت نوبت آنلاین', 'fasdent' ) ); ?>
						</a>
						<a class="btn btn--ghost" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $emergency_phone ) ); ?>">
							<i class="fa-solid fa-phone-volume" aria-hidden="true"></i>
							<?php esc_html_e( 'تماس اورژانس', 'fasdent' ); ?>
						</a>
					</div>
				</div>

				<?php
				$has_thumb = has_post_thumbnail();
				if ( $has_thumb || $hero_image ) : ?>
					<figure class="page-hero__media" data-reveal="fade-left">
						<?php if ( $has_thumb ) :
							the_post_thumbnail( 'large', array(
								'class'        => 'page-hero__image',
								'loading'      => 'eager',
								'fetchpriority'=> 'high',
								'alt'          => esc_attr( get_the_title() ),
								'itemprop'     => 'image',
							) );
						else : ?>
							<img class="page-hero__image"
							     src="<?php echo esc_url( $hero_image ); ?>"
							     alt="<?php echo esc_attr( get_the_title() ); ?>"
							     loading="eager" fetchpriority="high" itemprop="image" />
						<?php endif; ?>
						<span class="page-hero__media-glow" aria-hidden="true"></span>
					</figure>
				<?php endif; ?>
			</div>
		</header>

		<?php /* ---------------------------------------------------------
		 *  محتوای اصلی + نوار کناری
		 * -------------------------------------------------------- */ ?>
		<div class="container page-body">
			<div class="page-body__grid">

				<div class="page-body__main">

					<?php // Editor's / Clinical review note ?>
					<?php if ( $reviewer_name && $review_date ) : ?>
						<aside class="card review-note" role="note" aria-label="<?php esc_attr_e( 'یادداشت بازبینی بالینی', 'fasdent' ); ?>" data-reveal="fade-up">
							<div class="review-note__icon" aria-hidden="true"><i class="fa-solid fa-user-doctor"></i></div>
							<div>
								<p class="review-note__title"><strong><?php esc_html_e( 'بازبینی بالینی', 'fasdent' ); ?></strong></p>
								<p class="review-note__body">
									<?php
									printf(
										/* translators: 1: date, 2: reviewer name, 3: credentials */
										esc_html__( 'این صفحه در تاریخ %1$s توسط %2$s%3$s از نظر دقت بالینی و شفافیت برای بیمار بازبینی شده است. محتوا جنبه آموزشی دارد و جایگزین معاینه، تشخیص یا برنامه‌ی درمانی شخصی‌سازی‌شده نیست.', 'fasdent' ),
										'<time datetime="' . esc_attr( $review_date ) . '"><strong>' . esc_html( $review_date ) . '</strong></time>',
										'<strong>' . esc_html( $reviewer_name ) . '</strong>',
										$reviewer_credentials ? '، ' . esc_html( $reviewer_credentials ) : ''
									);
									?>
								</p>
								<?php if ( $reviewer_license ) : ?>
									<p class="review-note__license">
										<i class="fa-solid fa-id-card" aria-hidden="true"></i>
										<?php echo esc_html( $reviewer_license ); ?>
									</p>
								<?php endif; ?>
							</div>
						</aside>
					<?php endif; ?>

					<?php // Quick answer ?>
					<?php if ( $quick_answer ) : ?>
						<section class="card quick-answer" aria-labelledby="quick-answer-title" data-reveal="fade-up">
							<h2 id="quick-answer-title" class="quick-answer__title">
								<i class="fa-solid fa-bolt" aria-hidden="true"></i>
								<?php esc_html_e( 'پاسخ سریع', 'fasdent' ); ?>
							</h2>
							<p><?php echo wp_kses_post( $quick_answer ); ?></p>
						</section>
					<?php endif; ?>

					<?php // TOC (JS-populated from H2/H3) ?>
					<?php if ( '1' === $show_toc ) : ?>
						<nav class="toc-nav" aria-label="<?php esc_attr_e( 'فهرست مطالب', 'fasdent' ); ?>" id="toc" data-reveal="fade-up">
							<button type="button" class="toc-toggle" aria-expanded="true" aria-controls="toc-list">
								<span class="toc-toggle__icon" aria-hidden="true"><i class="fa-solid fa-list"></i></span>
								<span class="toc-toggle__label"><?php esc_html_e( 'فهرست مطالب', 'fasdent' ); ?></span>
								<i class="fa-solid fa-chevron-down toc-toggle__caret" aria-hidden="true"></i>
							</button>
							<ol id="toc-list" class="toc-list" role="list"></ol>
						</nav>
					<?php endif; ?>

					<div class="post-content prose" itemprop="mainContentOfPage">
						<?php the_content(); ?>
					</div>

					<?php // Disclaimer stack ?>
					<section class="disclaimer-stack" aria-label="<?php esc_attr_e( 'اطلاعیه‌های حقوقی و بالینی', 'fasdent' ); ?>">

						<div class="disclaimer disclaimer--medical" role="note" data-reveal="fade-up">
							<h3><i class="fa-solid fa-book-medical" aria-hidden="true"></i> <?php esc_html_e( 'اطلاعیه‌ی آموزشی', 'fasdent' ); ?></h3>
							<p><?php esc_html_e( 'مطالب این صفحه صرفاً جنبه‌ی آموزشی و اطلاع‌رسانی دارد و جایگزین ویزیت حضوری، تشخیص یا برنامه‌ی درمانی شخصی‌سازی‌شده توسط دندانپزشک نیست. برای تصمیم‌های درمانی، حتماً با پزشک خود مشورت کنید.', 'fasdent' ); ?></p>
						</div>

						<div class="disclaimer disclaimer--emergency" role="alert" data-reveal="fade-up">
							<h3><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> <?php esc_html_e( 'اورژانس دندان', 'fasdent' ); ?></h3>
							<p>
								<?php esc_html_e( 'در صورت درد شدید، تورم صورت، خونریزی مداوم یا ترومای دندانی، لطفاً وقت را از دست ندهید و فوراً تماس بگیرید:', 'fasdent' ); ?>
								<a class="disclaimer__phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $emergency_phone ) ); ?>"><?php echo esc_html( $emergency_phone ); ?></a>
							</p>
						</div>

						<div class="disclaimer disclaimer--results" role="note" data-reveal="fade-up">
							<h3><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i> <?php esc_html_e( 'نتایج ممکن است متفاوت باشد', 'fasdent' ); ?></h3>
							<p><?php esc_html_e( 'تصاویر و توصیف‌های درمان تنها نمونه‌ی موارد قبلی هستند. نتیجه‌ی هر درمان به وضعیت بالینی، بهداشت دهان و همکاری بیمار بستگی دارد و ادعای برتری یا تضمین نتیجه نمی‌کنیم.', 'fasdent' ); ?></p>
						</div>

						<div class="disclaimer disclaimer--privacy" role="note" data-reveal="fade-up">
							<h3><i class="fa-solid fa-lock" aria-hidden="true"></i> <?php esc_html_e( 'حریم خصوصی و اطلاعات شما', 'fasdent' ); ?></h3>
							<p><?php esc_html_e( 'لطفاً در فرم‌های عمومی این صفحه، هیچ اطلاعات پزشکی حساس یا شناسه‌ی هویتی (کد ملی، سوابق بیماری، تصاویر بالینی) وارد نکنید. ارتباط ایمن با کلینیک از طریق پنل بیمار و کانال‌های رمزنگاری‌شده انجام می‌شود.', 'fasdent' ); ?></p>
						</div>
					</section>

					<?php // Share row — XSS-safe: esc_url( rawurlencode() ) for all href values ?>
					<div class="social-share" role="group" aria-label="<?php esc_attr_e( 'اشتراک‌گذاری صفحه', 'fasdent' ); ?>">
						<span class="social-share__label"><?php esc_html_e( 'اشتراک‌گذاری:', 'fasdent' ); ?></span>
						<a class="social-btn social-btn--telegram"
						   target="_blank" rel="noopener noreferrer"
						   href="<?php echo esc_url( 'https://t.me/share/url?url=' . rawurlencode( get_permalink() ) . '&text=' . rawurlencode( get_the_title() ) ); ?>"
						   aria-label="Telegram"><i class="fa-brands fa-telegram" aria-hidden="true"></i></a>
						<a class="social-btn social-btn--whatsapp"
						   target="_blank" rel="noopener noreferrer"
						   href="<?php echo esc_url( 'https://wa.me/?text=' . rawurlencode( get_the_title() . ' — ' . get_permalink() ) ); ?>"
						   aria-label="WhatsApp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
						<a class="social-btn social-btn--twitter"
						   target="_blank" rel="noopener noreferrer"
						   href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . rawurlencode( get_permalink() ) . '&text=' . rawurlencode( get_the_title() ) ); ?>"
						   aria-label="X"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
						<a class="social-btn social-btn--linkedin"
						   target="_blank" rel="noopener noreferrer"
						   href="<?php echo esc_url( 'https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode( get_permalink() ) ); ?>"
						   aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
						<button type="button" class="social-btn social-btn--copy"
						        data-copy-url="<?php echo esc_url( get_permalink() ); ?>"
						        aria-label="<?php esc_attr_e( 'کپی لینک', 'fasdent' ); ?>"><i class="fa-solid fa-link" aria-hidden="true"></i></button>
					</div>

				</div><!-- /.page-body__main -->

				<aside class="page-body__sidebar" aria-label="<?php esc_attr_e( 'نوار کناری', 'fasdent' ); ?>">

					<div class="card sidebar-cta" data-reveal="fade-up">
						<h3><i class="fa-solid fa-calendar-plus" aria-hidden="true"></i> <?php esc_html_e( 'نوبت‌دهی آنلاین', 'fasdent' ); ?></h3>
						<p><?php esc_html_e( 'برای مشاوره و ویزیت حضوری، نوبت خود را آنلاین رزرو کنید. تیم پذیرش در سریع‌ترین زمان تماس می‌گیرد.', 'fasdent' ); ?></p>
						<a class="btn btn--primary btn--block" href="<?php echo esc_url( $booking_url ); ?>">
							<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
							<?php esc_html_e( 'رزرو نوبت', 'fasdent' ); ?>
						</a>
						<p class="privacy-note"><i class="fa-solid fa-lock" aria-hidden="true"></i> <?php esc_html_e( 'اطلاعات شما رمزنگاری‌شده منتقل می‌شود.', 'fasdent' ); ?></p>
					</div>

					<div class="card sidebar-emergency" data-reveal="fade-up">
						<h3><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> <?php esc_html_e( 'اورژانس دندان', 'fasdent' ); ?></h3>
						<p><?php esc_html_e( 'در صورت درد شدید، تورم یا ترومای دندانی فوراً تماس بگیرید:', 'fasdent' ); ?></p>
						<a class="btn btn--danger btn--block" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $emergency_phone ) ); ?>">
							<i class="fa-solid fa-phone-volume" aria-hidden="true"></i>
							<?php echo esc_html( $emergency_phone ); ?>
						</a>
					</div>

					<?php if ( $reviewer_name ) : ?>
					<div class="card sidebar-reviewer" data-reveal="fade-up">
						<h3><i class="fa-solid fa-user-doctor" aria-hidden="true"></i> <?php esc_html_e( 'بازبینی بالینی', 'fasdent' ); ?></h3>
						<p class="sidebar-reviewer__name"><strong><?php echo esc_html( $reviewer_name ); ?></strong></p>
						<?php if ( $reviewer_credentials ) : ?>
							<p class="sidebar-reviewer__cred"><?php echo esc_html( $reviewer_credentials ); ?></p>
						<?php endif; ?>
						<?php if ( $reviewer_license ) : ?>
							<p class="sidebar-reviewer__license"><i class="fa-solid fa-id-card" aria-hidden="true"></i> <?php echo esc_html( $reviewer_license ); ?></p>
						<?php endif; ?>
						<?php if ( $review_date ) : ?>
							<p class="sidebar-reviewer__date">
								<i class="fa-regular fa-calendar" aria-hidden="true"></i>
								<?php esc_html_e( 'بازبینی:', 'fasdent' ); ?>
								<time datetime="<?php echo esc_attr( $review_date ); ?>"><?php echo esc_html( $review_date ); ?></time>
							</p>
						<?php endif; ?>
					</div>
					<?php endif; ?>

					<div class="card sidebar-newsletter" data-reveal="fade-up">
						<h3><i class="fa-regular fa-envelope" aria-hidden="true"></i> <?php esc_html_e( 'خبرنامه‌ی سلامت دهان', 'fasdent' ); ?></h3>
						<p><?php esc_html_e( 'مطالب آموزشی سلامت دهان و دندان را در ایمیل خود دریافت کنید.', 'fasdent' ); ?></p>
						<form action="<?php echo esc_url( home_url( '/wp-json/fasdent/v1/newsletter' ) ); ?>"
						      method="post" class="newsletter-form" novalidate>
							<?php wp_nonce_field( 'fasdent_newsletter', 'fasdent_nonce' ); ?>
							<input type="text" name="hp_field" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true" />
							<label class="sr-only" for="nl-email"><?php esc_html_e( 'ایمیل', 'fasdent' ); ?></label>
							<input type="email" id="nl-email" name="email" placeholder="<?php esc_attr_e( 'ایمیل شما', 'fasdent' ); ?>" required autocomplete="email" />
							<button type="submit" class="btn btn--primary btn--block"><?php esc_html_e( 'عضویت', 'fasdent' ); ?></button>
							<p class="privacy-note"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> <?php esc_html_e( 'لطفاً هیچ اطلاعات پزشکی یا هویتی حساس در این فرم وارد نکنید.', 'fasdent' ); ?></p>
						</form>
					</div>

				</aside>
			</div>
		</div>

	</article>
	<?php endwhile; ?>
</main>

<button type="button" class="back-to-top" aria-label="<?php esc_attr_e( 'بازگشت به بالا', 'fasdent' ); ?>">
	<i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
</button>

<?php
/* -------------------------------------------------------------------------
 * JSON-LD: WebPage + BreadcrumbList (سبک و بی‌نیاز از پارتیال جانبی)
 * ---------------------------------------------------------------------- */
$breadcrumb_items = array();
$breadcrumb_items[] = array(
	'@type'    => 'ListItem',
	'position' => 1,
	'name'     => __( 'خانه', 'fasdent' ),
	'item'     => home_url( '/' ),
);
$pos = 2;
foreach ( array_reverse( get_post_ancestors( $post_id ) ) as $aid ) {
	$breadcrumb_items[] = array(
		'@type'    => 'ListItem',
		'position' => $pos++,
		'name'     => get_the_title( $aid ),
		'item'     => get_permalink( $aid ),
	);
}
$breadcrumb_items[] = array(
	'@type'    => 'ListItem',
	'position' => $pos,
	'name'     => get_the_title( $post_id ),
	'item'     => get_permalink( $post_id ),
);

$schema = array(
	'@context'      => 'https://schema.org',
	'@graph'        => array(
		array(
			'@type'         => 'WebPage',
			'@id'           => get_permalink( $post_id ) . '#webpage',
			'url'           => get_permalink( $post_id ),
			'name'          => get_the_title( $post_id ),
			'description'   => $page_subtitle ? $page_subtitle : wp_strip_all_tags( get_the_excerpt() ),
			'inLanguage'    => 'fa-IR',
			'isPartOf'      => array( '@id' => home_url( '/' ) . '#website' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'datePublished' => get_the_date( 'c' ),
		),
		array(
			'@type'           => 'BreadcrumbList',
			'@id'             => get_permalink( $post_id ) . '#breadcrumb',
			'itemListElement' => $breadcrumb_items,
		),
	),
);
?>
<script type="application/ld+json">
<?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</script>

<?php get_footer(); ?>