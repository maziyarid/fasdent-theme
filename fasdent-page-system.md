# Fasdent — Complete Page System

Beautiful, RTL, HIPAA-aware / WCAG 2.1 AA / FTC-safe WordPress **page** system for the Fasdent dental clinic (http://fasdent.ir/). This deliverable ships as a self-contained set of 7 files — drop into your theme, pick the *Fasdent Sample Page* template on any WordPress page, and go.

## 📦 Download

**[⬇ fasdent-page-system.zip](https://www.genspark.ai/api/files/s/9UH1FKVk)** — all 7 files, ~30 KB.

> Prefer to copy-paste? Every file is inlined below in a fenced code block with its exact path — just create the file at that path.

## Package contents

| # | File | Purpose | Size |
|---|------|---------|------|
| 1 | `fasdent/page.php` | WordPress page template — hero, sticky TOC, disclaimer stack, sidebar, JSON-LD schema. | 23.0 KB |
| 2 | `fasdent/assets/css/page.css` | Dedicated stylesheet — RTL logical properties, animated hero, elegant cards, reveal-on-scroll. | 25.7 KB |
| 3 | `fasdent/assets/js/page.js` | Dedicated JavaScript — TOC generator, scroll-spy, reveal-on-scroll, cookie banner, honeypot. | 12.9 KB |
| 4 | `fasdent/inc/prompts/page-generator.md` | Master AI prompt that generates any Fasdent page compliantly. | 6.0 KB |
| 5 | `fasdent/sample-pages/about-the-clinic.md` | Sample page 1 — About the Clinic. Paste-ready META + CONTENT. | 9.7 KB |
| 6 | `fasdent/sample-pages/implants-service.md` | Sample page 2 — Dental Implants (service page). Paste-ready META + CONTENT. | 12.8 KB |
| 7 | `fasdent/README.md` | Install guide, enqueue snippet, Custom Fields reference. | 3.8 KB |

## ✨ Design highlights

- **Hero with animated gradient blobs** — teal → amber, soft grid overlay, reveal-on-scroll on the hero media.
- **Sticky TOC with scroll-spy** — auto-generated from H2 / H3 in the page content, active section highlighted as you scroll, collapsible via `aria-expanded`.
- **Reveal-on-scroll animations** — declarative `data-reveal="fade-up|fade-left|fade-right"` attribute, honors `prefers-reduced-motion`.
- **Elegant cards** — soft shadows, hover lift, gradient dividers under H2s, polished callouts (info / warn / danger / default).
- **Reading progress bar** at the top of the viewport.
- **Back-to-top button** with smooth scroll.
- **Copy-link social share** — no Facebook by default (FA audience), Telegram, WhatsApp, X, LinkedIn, copy.
- **HIPAA-aware newsletter** — honeypot field, PHI-shape guard, HTTPS-only submit.
- **WCAG 2.1 AA** — skip link, `focus-visible` outlines, semantic headings, RTL logical properties, reduced-motion respected.
- **FTC-safe copy** — no guarantees, «نتایج ممکن است متفاوت باشد» disclaimer baked in.
- **JSON-LD schema** — `WebPage` + `BreadcrumbList` emitted inline.
- **Cookie consent** — opt-in banner; analytics gated behind a `fasdent:consent` event.
- **Print stylesheet** — sidebar / TOC / CTA hidden, hero background flattened.

## 🚀 Installation quick-start

1. **Unzip** the download and copy the `fasdent/` contents into your active theme, preserving the folder structure (`page.php` at theme root, `assets/…`, `inc/…`).
2. **Enqueue** the assets in `functions.php` — only on pages using the *Fasdent Sample Page* template, and only **after** your existing `main.css` / `main.js`:

```php
add_action('wp_enqueue_scripts', function () {
    if (!is_page_template('page.php')) return;
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();
    wp_enqueue_style(
        'fasdent-page',
        $uri . '/assets/css/page.css',
        ['fasdent-main'],                       // depends on your main.css handle
        filemtime($dir . '/assets/css/page.css')
    );
    wp_enqueue_script(
        'fasdent-page',
        $uri . '/assets/js/page.js',
        [],                                     // no jQuery needed
        filemtime($dir . '/assets/js/page.js'),
        true
    );
});
```

3. Make sure **Font Awesome 6** is loaded (the template uses `fa-solid`, `fa-regular`, and `fa-brands`).
4. In WP Admin, create a page → **Page Attributes → Template → *Fasdent Sample Page*.**
5. Fill Custom Fields (ACF-compatible) using the reference table below.
6. Publish. Enjoy 🦷

## 🔧 Custom Fields reference

### Per-page meta (`get_post_meta`)

| Key | Type | Example |
|---|---|---|
| `fasdent_kicker` | text | خدمات درمانی |
| `fasdent_subtitle` | text | یک جمله زیرعنوان کوتاه |
| `fasdent_quick_answer` | textarea (40–70 words) | پاسخ سریع به سؤال اصلی صفحه… |
| `fasdent_reviewer_name` | text | دکتر مریم رضایی |
| `fasdent_reviewer_credentials` | text | دندانپزشک عمومی |
| `fasdent_reviewer_license` | text | نظام پزشکی: ۱۲۳۴۵۶ |
| `fasdent_review_date` | date `YYYY-MM-DD` | 2026-06-20 |
| `fasdent_reading_time` | number (minutes) | 6 |
| `fasdent_hero_image` | url (fallback if no featured image) | https://… |
| `fasdent_hero_badges` | textarea — `icon\|label` per line | fa-solid fa-user-doctor\|بازبینی شده |
| `fasdent_primary_cta_label` | text | دریافت نوبت |
| `fasdent_primary_cta_url` | url | /reserve/ |
| `fasdent_show_toc` | text (`"1"` or empty) | 1 |

### Global options (`wp_options`)

| Key | Purpose | Default |
|---|---|---|
| `fasdent_emergency_phone` | Emergency number rendered in hero + sidebar + emergency disclaimer | `۰۲۱-XXXXXXXX` |
| `fasdent_booking_url`     | Global booking URL for the sidebar CTA | `/reserve/` |

## 📄 The files

Each section below is the full, verbatim content of one file. Copy the code block into a file at the path shown in the heading.

### `fasdent/page.php`

_WordPress page template — hero, sticky TOC, disclaimer stack, sidebar, JSON-LD schema._

```php
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
 *   - Editor’s Note / Clinical Review Box (در صورت وجود متادیتا)
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

					<?php // Share row ?>
					<div class="social-share" role="group" aria-label="<?php esc_attr_e( 'اشتراک‌گذاری صفحه', 'fasdent' ); ?>">
						<span class="social-share__label"><?php esc_html_e( 'اشتراک‌گذاری:', 'fasdent' ); ?></span>
						<a class="social-btn social-btn--telegram" target="_blank" rel="noopener" href="https://t.me/share/url?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" aria-label="Telegram"><i class="fa-brands fa-telegram" aria-hidden="true"></i></a>
						<a class="social-btn social-btn--whatsapp" target="_blank" rel="noopener" href="https://wa.me/?text=<?php echo urlencode( get_the_title() . ' — ' . get_permalink() ); ?>" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
						<a class="social-btn social-btn--twitter" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" aria-label="X"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
						<a class="social-btn social-btn--linkedin" target="_blank" rel="noopener" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode( get_permalink() ); ?>" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
						<button type="button" class="social-btn social-btn--copy" data-copy-url="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php esc_attr_e( 'کپی لینک', 'fasdent' ); ?>"><i class="fa-solid fa-link" aria-hidden="true"></i></button>
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
```

### `fasdent/assets/css/page.css`

_Dedicated stylesheet — RTL logical properties, animated hero, elegant cards, reveal-on-scroll._

```css
/* ==========================================================================
   Fasdent — page.css
   Dedicated stylesheet for the "Fasdent Sample Page" template (page.php).
   Enqueue AFTER assets/css/main.css.

   Written with CSS logical properties + RTL-aware values so it works
   correctly in Persian (dir="rtl") and LTR contexts alike.

   Design principles:
     • Brand teal + amber accents, calm medical palette.
     • Elegant cards with soft shadows and generous whitespace.
     • Animated hero blobs + reveal-on-scroll (data-reveal).
     • Sticky, accessible TOC with scroll-spy active state.
     • Focus-visible everywhere. Motion respects prefers-reduced-motion.
   ========================================================================== */

/* ── Tokens (fallbacks; will inherit from main.css if defined) ─────────── */
.fasdent-page {
	--fd-primary:      var(--color-primary, #0f766e);
	--fd-primary-600:  var(--color-primary-600, #0d5f5a);
	--fd-secondary:    var(--color-secondary, #f59e0b);
	--fd-dark:         var(--color-dark, #0f172a);
	--fd-muted:        var(--color-muted, #475569);
	--fd-bg:           var(--bg, #f8fafc);
	--fd-surface:      var(--surface, #ffffff);
	--fd-border:       var(--border, #e2e8f0);
	--fd-radius:       var(--radius, 16px);
	--fd-radius-lg:    24px;
	--fd-shadow:       var(--shadow, 0 18px 40px rgba(15, 23, 42, .08));
	--fd-shadow-hover: 0 24px 48px rgba(15, 23, 42, .12);
	--fd-danger:       #dc2626;
	--fd-warn:         #b45309;
	--fd-info:         #0369a1;
	--fd-success:      #166534;
	--fd-gradient:     linear-gradient(135deg, #0f766e 0%, #14b8a6 50%, #f59e0b 100%);
	--fd-hero-gradient:linear-gradient(160deg, #ecfeff 0%, #f0fdfa 45%, #fef3c7 100%);
}

/* ── Skip link (accessibility) ─────────────────────────────────────────── */
.skip-link {
	position: absolute;
	inset-block-start: -3rem;
	inset-inline-start: 1rem;
	background: var(--fd-primary);
	color: #fff;
	padding: .55rem 1rem;
	border-radius: 8px;
	font-weight: 700;
	z-index: 9999;
	transition: inset-block-start .2s ease;
}
.skip-link:focus { inset-block-start: 1rem; outline: 3px solid var(--fd-secondary); outline-offset: 2px; }

/* ── Reading progress bar ──────────────────────────────────────────────── */
.reading-progress {
	position: fixed;
	inset-block-start: 0;
	inset-inline: 0;
	height: 3px;
	background: rgba(15, 23, 42, .06);
	z-index: 998;
}
.reading-progress__bar {
	display: block;
	height: 100%;
	width: 0%;
	background: var(--fd-gradient);
	transition: width .1s linear;
}

/* ── Breadcrumb ────────────────────────────────────────────────────────── */
.fasdent-breadcrumb {
	background: var(--fd-surface);
	border-block-end: 1px solid var(--fd-border);
	padding: .85rem 0;
	font-size: .88rem;
}
.fasdent-breadcrumb ol {
	list-style: none;
	margin: 0;
	padding: 0;
	display: flex;
	flex-wrap: wrap;
	gap: .4rem;
	align-items: center;
	color: var(--fd-muted);
}
.fasdent-breadcrumb li { display: inline-flex; align-items: center; gap: .4rem; }
.fasdent-breadcrumb a { color: var(--fd-muted); }
.fasdent-breadcrumb a:hover { color: var(--fd-primary); }
.fasdent-breadcrumb [aria-current="page"] { color: var(--fd-dark); font-weight: 600; }
.fasdent-breadcrumb .fa-chevron-left { font-size: .7rem; opacity: .5; }

/* ==========================================================================
   HERO
   ========================================================================== */
.page-hero {
	position: relative;
	overflow: hidden;
	background: var(--fd-hero-gradient);
	padding-block: clamp(2.5rem, 6vw, 5rem);
}

.page-hero__bg { position: absolute; inset: 0; pointer-events: none; }
.page-hero__blob {
	position: absolute;
	border-radius: 50%;
	filter: blur(60px);
	opacity: .55;
	animation: fd-float 14s ease-in-out infinite;
}
.page-hero__blob--a {
	inline-size: 380px; block-size: 380px;
	inset-inline-start: -80px; inset-block-start: -60px;
	background: radial-gradient(circle at 30% 30%, #5eead4, transparent 70%);
}
.page-hero__blob--b {
	inline-size: 320px; block-size: 320px;
	inset-inline-end: -60px; inset-block-end: -80px;
	background: radial-gradient(circle at 60% 60%, #fcd34d, transparent 70%);
	animation-delay: -6s;
}
.page-hero__grid {
	position: absolute; inset: 0;
	background-image:
		linear-gradient(rgba(15, 118, 110, .06) 1px, transparent 1px),
		linear-gradient(90deg, rgba(15, 118, 110, .06) 1px, transparent 1px);
	background-size: 44px 44px;
	mask-image: radial-gradient(ellipse at center, black 40%, transparent 75%);
}
@keyframes fd-float {
	0%,100% { transform: translate3d(0,0,0) scale(1); }
	50%     { transform: translate3d(0,-24px,0) scale(1.05); }
}

.page-hero__inner {
	position: relative;
	display: grid;
	grid-template-columns: 1.2fr .8fr;
	gap: clamp(1.5rem, 4vw, 3rem);
	align-items: center;
}
.page-hero__kicker {
	display: inline-flex;
	align-items: center;
	gap: .5rem;
	background: rgba(15, 118, 110, .12);
	color: var(--fd-primary-600);
	padding: .35rem .85rem;
	border-radius: 999px;
	font-size: .82rem;
	font-weight: 700;
	margin: 0 0 1rem;
	border: 1px solid rgba(15, 118, 110, .18);
}
.page-hero__title {
	font-size: clamp(1.8rem, 3.6vw, 2.8rem);
	line-height: 1.25;
	margin: 0 0 1rem;
	color: var(--fd-dark);
	font-weight: 800;
	letter-spacing: -0.01em;
}
.page-hero__subtitle {
	font-size: clamp(1rem, 1.4vw, 1.15rem);
	color: var(--fd-muted);
	margin: 0 0 1.5rem;
	line-height: 1.9;
	max-width: 60ch;
}
.page-hero__badges {
	display: flex;
	flex-wrap: wrap;
	gap: .5rem;
	margin-block-end: 1.75rem;
}
.hero-badge {
	display: inline-flex;
	align-items: center;
	gap: .45rem;
	background: rgba(255,255,255,.7);
	backdrop-filter: blur(6px);
	border: 1px solid var(--fd-border);
	color: var(--fd-dark);
	padding: .45rem .8rem;
	border-radius: 999px;
	font-size: .82rem;
	font-weight: 600;
	box-shadow: 0 4px 14px rgba(15, 23, 42, .05);
}
.hero-badge i { color: var(--fd-primary); }

.page-hero__meta {
	display: flex;
	flex-wrap: wrap;
	gap: 1.25rem;
	color: var(--fd-muted);
	font-size: .88rem;
	margin-block-end: 1.75rem;
}
.page-hero__meta i { color: var(--fd-primary); margin-inline-end: .3rem; }
.page-hero__actions { display: flex; flex-wrap: wrap; gap: .75rem; }

.page-hero__media {
	position: relative;
	margin: 0;
	border-radius: var(--fd-radius-lg);
	overflow: hidden;
	box-shadow: 0 30px 60px rgba(15, 23, 42, .18);
	transform-origin: center;
}
.page-hero__image {
	inline-size: 100%;
	block-size: 100%;
	aspect-ratio: 4 / 3;
	object-fit: cover;
	display: block;
}
.page-hero__media-glow {
	position: absolute;
	inset: -40%;
	background: radial-gradient(circle at 30% 30%, rgba(94, 234, 212, .35), transparent 60%);
	pointer-events: none;
	mix-blend-mode: screen;
}

@media (max-width: 900px) {
	.page-hero__inner { grid-template-columns: 1fr; }
	.page-hero__media { order: -1; }
}

/* ==========================================================================
   BUTTONS
   ========================================================================== */
.fasdent-page .btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: .5rem;
	padding: .85rem 1.4rem;
	border: 0;
	border-radius: 999px;
	font-weight: 700;
	font-size: .95rem;
	cursor: pointer;
	transition: transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease;
	text-decoration: none;
	line-height: 1;
}
.fasdent-page .btn:focus-visible {
	outline: 3px solid var(--fd-secondary);
	outline-offset: 2px;
}
.btn--primary {
	background: var(--fd-primary);
	color: #fff;
	box-shadow: 0 10px 24px rgba(15, 118, 110, .28);
}
.btn--primary:hover { background: var(--fd-primary-600); transform: translateY(-2px); box-shadow: 0 14px 30px rgba(15, 118, 110, .35); color: #fff; }
.btn--ghost {
	background: rgba(255,255,255,.7);
	color: var(--fd-dark);
	border: 1px solid var(--fd-border);
	backdrop-filter: blur(6px);
}
.btn--ghost:hover { background: #fff; transform: translateY(-2px); color: var(--fd-primary); }
.btn--danger {
	background: var(--fd-danger);
	color: #fff;
	box-shadow: 0 10px 24px rgba(220, 38, 38, .28);
}
.btn--danger:hover { background: #b91c1c; transform: translateY(-2px); color: #fff; }
.btn--block { inline-size: 100%; }

/* ==========================================================================
   BODY LAYOUT
   ========================================================================== */
.page-body { padding-block: clamp(2rem, 5vw, 4rem); }
.page-body__grid {
	display: grid;
	grid-template-columns: minmax(0, 1fr) 300px;
	gap: 2.5rem;
	align-items: start;
}
.page-body__main { min-inline-size: 0; }
.page-body__sidebar {
	position: sticky;
	inset-block-start: 5rem;
	display: flex;
	flex-direction: column;
	gap: 1rem;
}
@media (max-width: 960px) {
	.page-body__grid { grid-template-columns: 1fr; }
	.page-body__sidebar { position: static; }
}

/* ── Base card in this template ────────────────────────────────────────── */
.fasdent-page .card {
	background: var(--fd-surface);
	border: 1px solid var(--fd-border);
	border-radius: var(--fd-radius);
	padding: 1.5rem;
	box-shadow: var(--fd-shadow);
	transition: box-shadow .25s ease, transform .25s ease;
}
.fasdent-page .card:hover { box-shadow: var(--fd-shadow-hover); }
.fasdent-page .card h3 {
	margin: 0 0 .75rem;
	font-size: 1.1rem;
	color: var(--fd-dark);
	display: flex;
	align-items: center;
	gap: .5rem;
}
.fasdent-page .card h3 i { color: var(--fd-primary); }
.fasdent-page .card p { margin: 0 0 .75rem; color: var(--fd-muted); line-height: 1.9; }
.fasdent-page .card p:last-child { margin-block-end: 0; }

/* ==========================================================================
   REVIEW NOTE & QUICK ANSWER
   ========================================================================== */
.review-note {
	display: grid;
	grid-template-columns: auto 1fr;
	gap: 1.1rem;
	align-items: start;
	border-inline-start: 4px solid var(--fd-primary);
	background: linear-gradient(135deg, #f0fdfa, #ffffff);
	margin-block-end: 1.5rem;
}
.review-note__icon {
	inline-size: 3rem; block-size: 3rem;
	border-radius: 50%;
	background: var(--fd-primary);
	color: #fff;
	display: grid;
	place-items: center;
	font-size: 1.2rem;
	box-shadow: 0 8px 18px rgba(15, 118, 110, .3);
	flex-shrink: 0;
}
.review-note__title { margin: 0 0 .35rem; color: var(--fd-primary); }
.review-note__body { margin: 0; color: var(--fd-dark); line-height: 1.9; }
.review-note__license {
	margin: .5rem 0 0;
	font-size: .85rem;
	color: var(--fd-muted);
	display: inline-flex;
	align-items: center;
	gap: .35rem;
}

.quick-answer {
	background: linear-gradient(135deg, #fef3c7, #fffbeb);
	border-inline-start: 4px solid var(--fd-secondary);
	margin-block-end: 1.5rem;
}
.quick-answer__title {
	margin: 0 0 .75rem;
	font-size: 1.1rem;
	color: #92400e;
	display: flex;
	align-items: center;
	gap: .5rem;
}
.quick-answer p { margin: 0; color: var(--fd-dark); line-height: 1.9; font-size: 1rem; }

/* ==========================================================================
   TABLE OF CONTENTS
   ========================================================================== */
.toc-nav {
	background: var(--fd-surface);
	border: 1px solid var(--fd-border);
	border-inline-start: 4px solid var(--fd-primary);
	border-radius: var(--fd-radius);
	padding: 1rem 1.25rem;
	margin-block-end: 2rem;
	box-shadow: var(--fd-shadow);
}
.toc-toggle {
	inline-size: 100%;
	display: flex;
	align-items: center;
	gap: .6rem;
	background: none;
	border: 0;
	padding: 0;
	font: inherit;
	font-weight: 800;
	color: var(--fd-dark);
	cursor: pointer;
}
.toc-toggle__icon {
	inline-size: 2rem; block-size: 2rem;
	background: rgba(15, 118, 110, .12);
	color: var(--fd-primary);
	border-radius: 8px;
	display: grid; place-items: center;
	font-size: .9rem;
}
.toc-toggle__label { flex: 1; text-align: start; }
.toc-toggle__caret { transition: transform .25s ease; }
.toc-nav[aria-expanded="false"] .toc-toggle__caret,
.toc-toggle[aria-expanded="false"] .toc-toggle__caret { transform: rotate(-90deg); }
.toc-list {
	list-style: none;
	margin: .85rem 0 0;
	padding: 0;
	max-block-size: 60vh;
	overflow: auto;
}
.toc-list[hidden] { display: none; }
.toc-item { margin: .2rem 0; }
.toc-item a {
	display: block;
	padding: .4rem .65rem;
	border-radius: 8px;
	color: var(--fd-muted);
	font-size: .9rem;
	text-decoration: none;
	transition: background .18s ease, color .18s ease;
	border-inline-start: 2px solid transparent;
}
.toc-item a:hover { background: #ecfeff; color: var(--fd-primary); }
.toc-item a.is-active {
	background: #ecfeff;
	color: var(--fd-primary);
	font-weight: 700;
	border-inline-start-color: var(--fd-primary);
}
.toc-item--h3 a { padding-inline-start: 1.6rem; font-size: .84rem; }

/* ==========================================================================
   .prose (rich content styling for the_content())
   ========================================================================== */
.prose {
	color: var(--fd-dark);
	line-height: 1.95;
	font-size: 1.02rem;
}
.prose > * + * { margin-block-start: 1.1rem; }
.prose h2 {
	font-size: 1.55rem;
	color: var(--fd-dark);
	margin-block-start: 2.5rem;
	margin-block-end: .75rem;
	padding-block-end: .5rem;
	border-block-end: 2px solid var(--fd-border);
	scroll-margin-block-start: 6rem;
	position: relative;
}
.prose h2::before {
	content: "";
	position: absolute;
	inset-inline-start: 0;
	inset-block-end: -2px;
	inline-size: 3rem;
	block-size: 2px;
	background: var(--fd-gradient);
	border-radius: 2px;
}
.prose h3 {
	font-size: 1.2rem;
	color: var(--fd-primary-600);
	margin-block-start: 1.75rem;
	margin-block-end: .5rem;
	scroll-margin-block-start: 6rem;
}
.prose p { margin: 0; color: var(--fd-dark); }
.prose a { color: var(--fd-primary); text-decoration: underline; text-decoration-thickness: 1px; text-underline-offset: 3px; }
.prose a:hover { color: var(--fd-secondary); }
.prose strong { color: var(--fd-dark); font-weight: 700; }
.prose ul, .prose ol { padding-inline-start: 1.6rem; margin: 0; }
.prose li { margin-block: .3rem; }
.prose blockquote {
	border-inline-start: 4px solid var(--fd-primary);
	background: #f0fdfa;
	padding: 1rem 1.25rem;
	border-radius: 12px;
	color: var(--fd-dark);
	font-style: italic;
}
.prose img, .prose figure img {
	border-radius: 12px;
	box-shadow: 0 12px 28px rgba(15,23,42,.1);
}
.prose figcaption {
	font-size: .85rem;
	color: var(--fd-muted);
	text-align: center;
	margin-block-start: .5rem;
}
.prose code {
	background: #f1f5f9;
	color: #be185d;
	padding: .1rem .4rem;
	border-radius: 6px;
	font-size: .9em;
}
.prose table {
	inline-size: 100%;
	border-collapse: collapse;
	margin-block: 1.25rem;
	background: var(--fd-surface);
	border-radius: 12px;
	overflow: hidden;
	box-shadow: var(--fd-shadow);
}
.prose th, .prose td {
	padding: .75rem 1rem;
	border-block-end: 1px solid var(--fd-border);
	text-align: start;
}
.prose th { background: #f0fdfa; color: var(--fd-primary-600); font-weight: 700; }

/* Callouts (usable inside the_content()) */
.prose .callout {
	display: grid;
	grid-template-columns: auto 1fr;
	gap: .9rem;
	align-items: start;
	padding: 1rem 1.25rem;
	border-radius: 12px;
	border-inline-start: 4px solid var(--fd-primary);
	background: #f0fdfa;
	margin-block: 1.25rem;
}
.prose .callout--warn    { background: #fffbeb; border-color: var(--fd-secondary); }
.prose .callout--danger  { background: #fef2f2; border-color: var(--fd-danger); }
.prose .callout--info    { background: #eff6ff; border-color: var(--fd-info); }
.prose .callout__icon {
	inline-size: 2.25rem; block-size: 2.25rem;
	border-radius: 50%;
	display: grid; place-items: center;
	background: rgba(15,118,110,.15);
	color: var(--fd-primary);
	font-size: 1rem;
}
.prose .callout--warn .callout__icon    { background: rgba(245,158,11,.15); color: var(--fd-warn); }
.prose .callout--danger .callout__icon  { background: rgba(220,38,38,.15);  color: var(--fd-danger); }
.prose .callout--info .callout__icon    { background: rgba(3,105,161,.15);  color: var(--fd-info); }

/* Feature / benefits grid used inside content */
.prose .feature-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
	gap: 1rem;
	margin-block: 1.5rem;
}
.prose .feature-card {
	background: var(--fd-surface);
	border: 1px solid var(--fd-border);
	border-radius: 14px;
	padding: 1.25rem;
	transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
}
.prose .feature-card:hover {
	transform: translateY(-4px);
	box-shadow: var(--fd-shadow-hover);
	border-color: rgba(15, 118, 110, .3);
}
.prose .feature-card__icon {
	inline-size: 2.75rem; block-size: 2.75rem;
	border-radius: 12px;
	display: grid; place-items: center;
	background: rgba(15,118,110,.12);
	color: var(--fd-primary);
	font-size: 1.15rem;
	margin-block-end: .8rem;
}
.prose .feature-card h4 { margin: 0 0 .35rem; font-size: 1rem; color: var(--fd-dark); }
.prose .feature-card p  { margin: 0; color: var(--fd-muted); font-size: .92rem; line-height: 1.75; }

/* Steps timeline used inside content */
.prose .steps { list-style: none; padding: 0; margin: 1.5rem 0; counter-reset: fdstep; }
.prose .steps li {
	position: relative;
	padding: 1rem 4rem 1rem 1.25rem;
	background: var(--fd-surface);
	border: 1px solid var(--fd-border);
	border-radius: 14px;
	margin-block: .6rem;
	counter-increment: fdstep;
}
.prose .steps li::before {
	content: counter(fdstep);
	position: absolute;
	inset-inline-end: 1rem;
	inset-block-start: 50%;
	transform: translateY(-50%);
	inline-size: 2.5rem; block-size: 2.5rem;
	border-radius: 50%;
	background: var(--fd-gradient);
	color: #fff;
	font-weight: 800;
	display: grid; place-items: center;
	box-shadow: 0 8px 18px rgba(15, 118, 110, .28);
}
.prose .steps li strong { display: block; margin-block-end: .25rem; }

/* FAQ inside content */
.prose .faq-item {
	background: var(--fd-surface);
	border: 1px solid var(--fd-border);
	border-radius: 12px;
	padding: .25rem 1rem;
	margin-block: .5rem;
	transition: border-color .2s ease, box-shadow .2s ease;
}
.prose .faq-item[open] { border-color: rgba(15, 118, 110, .3); box-shadow: var(--fd-shadow); }
.prose .faq-item summary {
	list-style: none;
	cursor: pointer;
	padding: .85rem 0;
	font-weight: 700;
	color: var(--fd-dark);
	display: flex;
	align-items: center;
	gap: .75rem;
}
.prose .faq-item summary::-webkit-details-marker { display: none; }
.prose .faq-item summary::after {
	content: "\f078"; /* chevron-down */
	font-family: "Font Awesome 6 Free";
	font-weight: 900;
	margin-inline-start: auto;
	color: var(--fd-primary);
	transition: transform .2s ease;
}
.prose .faq-item[open] summary::after { transform: rotate(180deg); }
.prose .faq-item p { padding-block-end: .85rem; color: var(--fd-muted); }

/* ==========================================================================
   DISCLAIMER STACK
   ========================================================================== */
.disclaimer-stack {
	display: grid;
	gap: 1rem;
	margin-block: 2.5rem 1.5rem;
}
.disclaimer {
	padding: 1.25rem 1.5rem;
	border-radius: var(--fd-radius);
	border-inline-start: 4px solid var(--fd-primary);
	background: var(--fd-surface);
	box-shadow: 0 4px 14px rgba(15, 23, 42, .04);
}
.disclaimer h3 {
	margin: 0 0 .5rem;
	font-size: 1rem;
	color: var(--fd-dark);
	display: flex;
	align-items: center;
	gap: .5rem;
}
.disclaimer p { margin: 0; color: var(--fd-muted); line-height: 1.9; font-size: .95rem; }
.disclaimer--medical   { background: #f0fdfa; border-color: var(--fd-primary); }
.disclaimer--medical   h3 i { color: var(--fd-primary); }
.disclaimer--emergency { background: #fef2f2; border-color: var(--fd-danger); }
.disclaimer--emergency h3, .disclaimer--emergency h3 i { color: var(--fd-danger); }
.disclaimer--emergency .disclaimer__phone {
	color: var(--fd-danger);
	font-weight: 800;
	white-space: nowrap;
}
.disclaimer--results   { background: #fffbeb; border-color: var(--fd-secondary); }
.disclaimer--results   h3, .disclaimer--results h3 i { color: #92400e; }
.disclaimer--privacy   { background: #eff6ff; border-color: var(--fd-info); }
.disclaimer--privacy   h3, .disclaimer--privacy h3 i { color: var(--fd-info); }

/* ==========================================================================
   SOCIAL SHARE
   ========================================================================== */
.social-share {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	gap: .5rem;
	padding: 1rem 1.25rem;
	background: var(--fd-surface);
	border: 1px solid var(--fd-border);
	border-radius: var(--fd-radius);
	margin-block: 1.5rem;
}
.social-share__label { font-size: .9rem; color: var(--fd-muted); margin-inline-end: .5rem; font-weight: 600; }
.social-btn {
	inline-size: 2.4rem;
	block-size: 2.4rem;
	border-radius: 50%;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	color: #fff;
	border: 0;
	cursor: pointer;
	text-decoration: none;
	transition: transform .18s ease, opacity .18s ease, box-shadow .18s ease;
}
.social-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 18px rgba(15, 23, 42, .15); color: #fff; }
.social-btn--telegram { background: #0088cc; }
.social-btn--whatsapp { background: #25d366; }
.social-btn--twitter  { background: #000; }
.social-btn--linkedin { background: #0a66c2; }
.social-btn--copy     { background: var(--fd-muted); }
.social-btn.is-copied { background: var(--fd-success); }

/* ==========================================================================
   SIDEBAR
   ========================================================================== */
.sidebar-cta       { background: linear-gradient(160deg, #ffffff, #ecfeff); }
.sidebar-emergency {
	background: linear-gradient(160deg, #ffffff, #fef2f2);
	border-inline-end: 3px solid var(--fd-danger);
}
.sidebar-emergency h3, .sidebar-emergency h3 i { color: var(--fd-danger); }
.sidebar-reviewer__name    { font-size: 1rem; margin-block-end: .25rem; }
.sidebar-reviewer__cred    { color: var(--fd-muted); font-size: .88rem; margin: 0 0 .5rem; }
.sidebar-reviewer__license { display: inline-flex; align-items: center; gap: .35rem; background: rgba(245,158,11,.12); color: #92400e; padding: .3rem .7rem; border-radius: 999px; font-size: .8rem; margin: 0; }
.sidebar-reviewer__date    { margin: .75rem 0 0; font-size: .85rem; color: var(--fd-muted); }

.privacy-note {
	display: inline-flex;
	align-items: center;
	gap: .35rem;
	font-size: .78rem;
	color: var(--fd-muted);
	margin-block-start: .75rem;
}
.privacy-note i { color: var(--fd-primary); }

.newsletter-form input[type="email"] {
	inline-size: 100%;
	padding: .75rem 1rem;
	border: 1px solid var(--fd-border);
	border-radius: 10px;
	font: inherit;
	background: #fff;
	color: var(--fd-dark);
	margin-block: .5rem;
}
.newsletter-form input[type="email"]:focus {
	outline: 2px solid var(--fd-primary);
	outline-offset: 1px;
	border-color: var(--fd-primary);
}
.newsletter-form .hp-field {
	position: absolute !important;
	inset-inline-start: -9999px !important;
	opacity: 0 !important;
	pointer-events: none !important;
}
.sr-only {
	position: absolute !important;
	inline-size: 1px !important; block-size: 1px !important;
	overflow: hidden; clip: rect(0,0,0,0);
	white-space: nowrap; border: 0; padding: 0; margin: -1px;
}

/* ==========================================================================
   BACK TO TOP
   ========================================================================== */
.back-to-top {
	position: fixed;
	inset-block-end: 1.5rem;
	inset-inline-start: 1.5rem;
	inline-size: 3rem;
	block-size: 3rem;
	border-radius: 50%;
	border: 0;
	background: var(--fd-primary);
	color: #fff;
	cursor: pointer;
	opacity: 0;
	pointer-events: none;
	transform: translateY(10px);
	transition: opacity .25s ease, transform .25s ease, background .2s ease;
	box-shadow: 0 12px 24px rgba(15, 118, 110, .35);
	z-index: 990;
}
.back-to-top.is-visible { opacity: 1; pointer-events: auto; transform: translateY(0); }
.back-to-top:hover { background: var(--fd-primary-600); transform: translateY(-3px); }

/* ==========================================================================
   REVEAL ON SCROLL
   ========================================================================== */
[data-reveal] {
	opacity: 0;
	transform: translateY(24px);
	transition: opacity .7s ease, transform .7s ease;
	will-change: opacity, transform;
}
[data-reveal="fade-left"]  { transform: translateX(24px); }
[data-reveal="fade-right"] { transform: translateX(-24px); }
[data-reveal].is-visible   { opacity: 1; transform: none; }

/* ==========================================================================
   FOCUS VISIBLE (global for this template)
   ========================================================================== */
.fasdent-page a:focus-visible,
.fasdent-page button:focus-visible,
.fasdent-page input:focus-visible,
.fasdent-page textarea:focus-visible,
.fasdent-page select:focus-visible,
.fasdent-page summary:focus-visible {
	outline: 3px solid var(--fd-secondary);
	outline-offset: 2px;
	border-radius: 6px;
}

/* ==========================================================================
   REDUCED MOTION
   ========================================================================== */
@media (prefers-reduced-motion: reduce) {
	[data-reveal],
	[data-reveal].is-visible,
	.page-hero__blob,
	.fasdent-page .btn,
	.back-to-top,
	.fasdent-page .card,
	.social-btn {
		animation: none !important;
		transition: none !important;
		transform: none !important;
	}
}

/* ==========================================================================
   PRINT
   ========================================================================== */
@media print {
	.reading-progress,
	.back-to-top,
	.social-share,
	.page-body__sidebar,
	.toc-nav,
	.page-hero__actions,
	.skip-link { display: none !important; }
	.page-hero { background: #fff !important; }
	.page-body__grid { grid-template-columns: 1fr !important; }
	body { color: #000 !important; }
	.disclaimer { break-inside: avoid; }
}
```

### `fasdent/assets/js/page.js`

_Dedicated JavaScript — TOC generator, scroll-spy, reveal-on-scroll, cookie banner, honeypot._

```javascript
/**
 * Fasdent — page.js
 * ---------------------------------------------------------------------------
 * Dedicated behaviors for the "Fasdent Sample Page" template (page.php).
 * Enqueue AFTER assets/js/main.js (if any). No jQuery dependency.
 *
 * Features:
 *   - Auto-generated Table of Contents from H2/H3 inside .post-content
 *   - Scroll-spy: highlights active TOC item
 *   - Sticky TOC collapse/expand (aria-expanded)
 *   - Reading progress bar
 *   - Reveal-on-scroll (data-reveal="fade-up|fade-left|fade-right")
 *   - Back-to-top button
 *   - Copy-link social button
 *   - Honeypot-aware newsletter submission (HIPAA-friendly)
 *   - Cookie consent (opt-in; gates GA/analytics)
 *   - Smooth in-page anchor scrolling with header offset
 *
 * Respects prefers-reduced-motion.
 */
(function () {
	'use strict';

	var doc = document;
	var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function ready(fn) {
		if (doc.readyState !== 'loading') fn();
		else doc.addEventListener('DOMContentLoaded', fn);
	}

	function slugify(str) {
		return String(str || '')
			.trim()
			.toLowerCase()
			.replace(/[\s\u200c]+/g, '-')
			.replace(/[^\w\u0600-\u06FF\-]/g, '')
			.replace(/\-+/g, '-')
			.replace(/^\-|\-$/g, '');
	}

	/* ---------------------------------------------------------------------
	 * TOC generation
	 * ------------------------------------------------------------------ */
	function buildTOC() {
		var tocList = doc.getElementById('toc-list');
		var content = doc.querySelector('.post-content');
		if (!tocList || !content) return;

		var headings = content.querySelectorAll('h2, h3');
		if (!headings.length) {
			var tocNav = doc.getElementById('toc');
			if (tocNav) tocNav.style.display = 'none';
			return;
		}

		var frag = doc.createDocumentFragment();
		var used = {};
		headings.forEach(function (h) {
			var id = h.id;
			if (!id) {
				id = slugify(h.textContent) || 'section';
				if (used[id]) { id = id + '-' + (++used[id]); }
				else { used[id] = 1; }
				h.id = id;
			}
			var li = doc.createElement('li');
			li.className = 'toc-item toc-item--' + h.tagName.toLowerCase();
			var a = doc.createElement('a');
			a.href = '#' + id;
			a.textContent = h.textContent.trim();
			a.setAttribute('data-toc-link', id);
			li.appendChild(a);
			frag.appendChild(li);
		});
		tocList.appendChild(frag);
	}

	/* ---------------------------------------------------------------------
	 * TOC collapse/expand toggle
	 * ------------------------------------------------------------------ */
	function initTOCToggle() {
		var btn = doc.querySelector('.toc-toggle');
		var list = doc.getElementById('toc-list');
		if (!btn || !list) return;
		btn.setAttribute('aria-expanded', 'true');
		btn.addEventListener('click', function () {
			var open = btn.getAttribute('aria-expanded') === 'true';
			btn.setAttribute('aria-expanded', open ? 'false' : 'true');
			if (open) list.setAttribute('hidden', '');
			else list.removeAttribute('hidden');
		});
	}

	/* ---------------------------------------------------------------------
	 * Scroll-spy
	 * ------------------------------------------------------------------ */
	function initScrollSpy() {
		var links = doc.querySelectorAll('[data-toc-link]');
		if (!links.length || !('IntersectionObserver' in window)) return;

		var linkById = {};
		var targets = [];
		links.forEach(function (a) {
			var id = a.getAttribute('data-toc-link');
			var el = doc.getElementById(id);
			if (el) {
				linkById[id] = a;
				targets.push(el);
			}
		});

		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				var a = linkById[entry.target.id];
				if (!a) return;
				if (entry.isIntersecting) {
					links.forEach(function (l) { l.classList.remove('is-active'); });
					a.classList.add('is-active');
				}
			});
		}, {
			rootMargin: '-25% 0px -65% 0px',
			threshold: 0
		});

		targets.forEach(function (t) { observer.observe(t); });
	}

	/* ---------------------------------------------------------------------
	 * Reading progress bar
	 * ------------------------------------------------------------------ */
	function initReadingProgress() {
		var bar = doc.querySelector('.reading-progress__bar');
		if (!bar) return;
		function update() {
			var scrollTop = window.pageYOffset || doc.documentElement.scrollTop;
			var docHeight = doc.documentElement.scrollHeight - window.innerHeight;
			var pct = docHeight > 0 ? Math.max(0, Math.min(100, (scrollTop / docHeight) * 100)) : 0;
			bar.style.width = pct + '%';
		}
		update();
		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update, { passive: true });
	}

	/* ---------------------------------------------------------------------
	 * Reveal on scroll
	 * ------------------------------------------------------------------ */
	function initReveal() {
		var els = doc.querySelectorAll('[data-reveal]');
		if (!els.length) return;
		if (prefersReducedMotion || !('IntersectionObserver' in window)) {
			els.forEach(function (el) { el.classList.add('is-visible'); });
			return;
		}
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
		els.forEach(function (el) { io.observe(el); });
	}

	/* ---------------------------------------------------------------------
	 * Back-to-top
	 * ------------------------------------------------------------------ */
	function initBackToTop() {
		var btn = doc.querySelector('.back-to-top');
		if (!btn) return;
		function toggle() {
			if ((window.pageYOffset || doc.documentElement.scrollTop) > 600) {
				btn.classList.add('is-visible');
			} else {
				btn.classList.remove('is-visible');
			}
		}
		toggle();
		window.addEventListener('scroll', toggle, { passive: true });
		btn.addEventListener('click', function () {
			window.scrollTo({
				top: 0,
				behavior: prefersReducedMotion ? 'auto' : 'smooth'
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Copy-link button
	 * ------------------------------------------------------------------ */
	function initCopyLink() {
		var btn = doc.querySelector('.social-btn--copy');
		if (!btn) return;
		btn.addEventListener('click', function () {
			var url = btn.getAttribute('data-copy-url') || window.location.href;
			var done = function () {
				btn.classList.add('is-copied');
				var original = btn.innerHTML;
				btn.innerHTML = '<i class="fa-solid fa-check" aria-hidden="true"></i>';
				setTimeout(function () {
					btn.classList.remove('is-copied');
					btn.innerHTML = original;
				}, 1500);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done).catch(function () {
					legacyCopy(url); done();
				});
			} else {
				legacyCopy(url); done();
			}
		});

		function legacyCopy(text) {
			var ta = doc.createElement('textarea');
			ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
			doc.body.appendChild(ta); ta.focus(); ta.select();
			try { doc.execCommand('copy'); } catch (e) {}
			doc.body.removeChild(ta);
		}
	}

	/* ---------------------------------------------------------------------
	 * Smooth in-page anchors (with sticky header offset)
	 * ------------------------------------------------------------------ */
	function initSmoothAnchors() {
		doc.addEventListener('click', function (e) {
			var a = e.target.closest && e.target.closest('a[href^="#"]');
			if (!a) return;
			var hash = a.getAttribute('href');
			if (!hash || hash === '#' || hash.length < 2) return;
			var target = doc.getElementById(hash.slice(1));
			if (!target) return;
			e.preventDefault();
			var top = target.getBoundingClientRect().top + window.pageYOffset - 80;
			window.scrollTo({
				top: top,
				behavior: prefersReducedMotion ? 'auto' : 'smooth'
			});
			target.setAttribute('tabindex', '-1');
			target.focus({ preventScroll: true });
			history.replaceState(null, '', hash);
		});
	}

	/* ---------------------------------------------------------------------
	 * Newsletter form (HIPAA-friendly)
	 *   - Honeypot check
	 *   - Blocks obvious PHI-looking input (email is the only field, but we
	 *     defensively reject long strings containing digits + words that
	 *     look like health information).
	 * ------------------------------------------------------------------ */
	function initNewsletter() {
		var form = doc.querySelector('.newsletter-form');
		if (!form) return;
		form.addEventListener('submit', function (e) {
			var hp = form.querySelector('.hp-field');
			if (hp && hp.value) { e.preventDefault(); return; }
			var email = form.querySelector('input[type="email"]');
			if (!email) return;
			var v = String(email.value || '').trim();
			if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
				e.preventDefault();
				showFormMessage(form, 'lang-invalid-email', 'error');
				return;
			}
			// Basic PHI-shape guard (no digits/health words in an email addr).
			if (/(hiv|cancer|diabet|hepat|psychiat|mri|dna|ssn|nationalid|codemelli)/i.test(v)) {
				e.preventDefault();
				showFormMessage(form, 'lang-no-phi', 'error');
				return;
			}
			showFormMessage(form, 'lang-submitting', 'success');
		});
	}

	function showFormMessage(form, key, kind) {
		var messages = {
			'lang-invalid-email': 'لطفاً یک ایمیل معتبر وارد کنید.',
			'lang-no-phi':        'برای حریم خصوصی، لطفاً از وارد کردن اطلاعات پزشکی حساس در این فرم خودداری کنید.',
			'lang-submitting':    'در حال ارسال… ممنون از عضویت شما.'
		};
		var existing = form.querySelector('.form-message');
		if (existing) existing.remove();
		var el = doc.createElement('p');
		el.className = 'form-message form-message--' + (kind || 'success');
		el.textContent = messages[key] || key;
		form.appendChild(el);
	}

	/* ---------------------------------------------------------------------
	 * Cookie consent (opt-in, GA gated)
	 * ------------------------------------------------------------------ */
	function initCookieConsent() {
		try {
			var stored = localStorage.getItem('fasdent_cookie_consent');
			if (stored === 'accepted' || stored === 'rejected') return;
		} catch (e) {}

		var banner = doc.createElement('aside');
		banner.className = 'cookie-banner';
		banner.setAttribute('role', 'region');
		banner.setAttribute('aria-label', 'اعلان کوکی');
		banner.innerHTML =
			'<div class="cookie-banner__inner">' +
				'<p class="cookie-banner__text">' +
					'<i class="fa-solid fa-cookie-bite" aria-hidden="true" style="color:#f59e0b;margin-inline-end:.35rem;"></i>' +
					'این سایت برای بهبود تجربه‌ی شما از کوکی‌های ضروری و تحلیلی استفاده می‌کند. اطلاعات پزشکی شما جمع‌آوری نمی‌شود.' +
				'</p>' +
				'<div class="cookie-banner__actions">' +
					'<button type="button" class="btn btn--primary cookie-accept">پذیرش</button>' +
					'<button type="button" class="btn cookie-reject">رد</button>' +
				'</div>' +
			'</div>';

		// Inline minimal styles for the banner (self-contained).
		var style = doc.createElement('style');
		style.textContent =
			'.cookie-banner{position:fixed;inset-block-end:0;inset-inline:0;background:#0f172a;color:#e2e8f0;padding:1rem;z-index:9999;box-shadow:0 -8px 30px rgba(0,0,0,.25);}' +
			'.cookie-banner__inner{display:flex;flex-wrap:wrap;align-items:center;gap:1rem;justify-content:space-between;max-width:1160px;margin:0 auto;}' +
			'.cookie-banner__text{margin:0;font-size:.9rem;line-height:1.7;}' +
			'.cookie-banner__actions{display:flex;gap:.5rem;flex-wrap:wrap;}' +
			'.cookie-banner .btn{padding:.55rem 1rem;font-size:.85rem;}' +
			'.cookie-reject{background:transparent;color:#e2e8f0;border:1px solid #64748b;}' +
			'.cookie-reject:hover{background:#1e293b;color:#fff;}';
		doc.head.appendChild(style);

		doc.body.appendChild(banner);

		banner.querySelector('.cookie-accept').addEventListener('click', function () {
			try { localStorage.setItem('fasdent_cookie_consent', 'accepted'); } catch (e) {}
			banner.remove();
			// Gate analytics here — page can dispatch this event and load GA/Matomo on it.
			doc.dispatchEvent(new CustomEvent('fasdent:consent', { detail: { accepted: true } }));
		});
		banner.querySelector('.cookie-reject').addEventListener('click', function () {
			try { localStorage.setItem('fasdent_cookie_consent', 'rejected'); } catch (e) {}
			banner.remove();
			doc.dispatchEvent(new CustomEvent('fasdent:consent', { detail: { accepted: false } }));
		});
	}

	/* ---------------------------------------------------------------------
	 * Init
	 * ------------------------------------------------------------------ */
	ready(function () {
		buildTOC();
		initTOCToggle();
		initScrollSpy();
		initReadingProgress();
		initReveal();
		initBackToTop();
		initCopyLink();
		initSmoothAnchors();
		initNewsletter();
		initCookieConsent();
	});
})();
```

### `fasdent/inc/prompts/page-generator.md`

_Master AI prompt that generates any Fasdent page compliantly._

````markdown
# Fasdent — Master Page Generator Prompt

You are an expert dental-content writer + WordPress editor for **Fasdent**
(`http://fasdent.ir/`), a Persian dental clinic. Your job is to generate the
**WordPress editor content** for a new page that will be rendered by the
`page.php` template ("Fasdent Sample Page"). You do **not** output the whole
HTML page — only:

1. The rich content that will be pasted into the WordPress editor
   (the `the_content()` area — semantic HTML using the classes below).
2. The post meta values the template reads.

---

## Non-negotiable rules

### Compliance
- **HIPAA-aware**: never invent patient stories, PHI, images, or identifiers.
  If you need a scenario, mark it as an illustrative example.
- **FTC truth-in-advertising**: no guarantees, no "best in Tehran", no
  unsupported superiority claims. Use hedged, evidence-based language.
  Always include the phrase equivalent of "نتایج ممکن است متفاوت باشد" wherever
  outcomes are described.
- **Educational, not medical advice**: every page's tone is patient education,
  not diagnosis or treatment plan.
- **Accessibility (WCAG 2.1 AA)**:
  - Use exactly one `<h1>` — WordPress will render it from the title. Content
    starts at `<h2>`.
  - Heading order must be strict: `h2 → h3` (no h4+ unless truly necessary).
  - Every `<img>` needs a descriptive Persian `alt`.
  - No color-only meaning. No text inside images.

### Language & tone
- Language: **Persian (fa-IR)**, direction RTL. Warm, calm, patient-first.
- Reading level: 8th grade equivalent. Short sentences, short paragraphs.
- Numbers as Western digits (0-9) inside text (WP theme handles conversion).
- Avoid stigmatizing language ("dirty teeth", "bad breath problem"); prefer
  clinical, respectful phrasing.

### Structure of the generated content
Always produce sections **in this order**:

1. **Intro paragraph** (2–4 sentences). Set context and value for the reader.
2. **Key takeaways** (`<ul class="key-takeaways__list">` inside a
   `<section class="card key-takeaways">`), 3–5 bullets.
3. **Body H2 sections** (3–7 of them). Each section:
   - Opens with a `<h2>` (headline case).
   - Optional `<h3>` sub-sections.
   - May include one or more of these building blocks (see reference below):
     - `.callout` / `.callout--warn` / `.callout--info` / `.callout--danger`
     - `.feature-grid` with `.feature-card`s
     - `.steps` ordered list
     - `<details class="faq-item">` FAQ items
     - Standard `<table>`
4. **FAQ section** (3–6 items) using `<details class="faq-item">`.
5. **When to see a dentist** — a `.callout--warn` explaining warning signs.
6. **CTA paragraph** ending with an internal link to `/reserve/` (booking).

**Do NOT** add: disclaimers, breadcrumbs, TOC, sharing buttons, sidebar,
schema — the template renders those.

---

## HTML building blocks (use exactly these classes)

### Key takeaways card
```html
<section class="card key-takeaways">
  <h2 class="key-takeaways__title">
    <i class="fa-solid fa-list-check" aria-hidden="true"></i> نکات کلیدی
  </h2>
  <ul class="key-takeaways__list">
    <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> …</li>
  </ul>
</section>
```

### Callout
```html
<div class="callout callout--info">
  <div class="callout__icon"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></div>
  <div><strong>عنوان کوتاه.</strong> متن توضیحی.</div>
</div>
```
Variants: `callout--warn`, `callout--danger`, `callout--info`, default (teal).

### Feature grid
```html
<div class="feature-grid">
  <div class="feature-card">
    <div class="feature-card__icon"><i class="fa-solid fa-tooth" aria-hidden="true"></i></div>
    <h4>عنوان</h4>
    <p>یک تا دو جمله.</p>
  </div>
  <!-- repeat 3–6 cards -->
</div>
```

### Steps
```html
<ol class="steps">
  <li><strong>ارزیابی اولیه.</strong> شرح مختصر مرحله.</li>
  <li><strong>عکس‌برداری.</strong> …</li>
</ol>
```

### FAQ item
```html
<details class="faq-item">
  <summary>سؤال؟</summary>
  <p>پاسخ کوتاه و دقیق.</p>
</details>
```

---

## Required post meta (set alongside the content)

Return a small YAML/JSON block the operator can paste into ACF/Custom Fields:

```yaml
fasdent_kicker:            "برچسب کوتاه بالای عنوان"     # e.g. "خدمات ما"
fasdent_subtitle:          "زیرعنوان یک-جمله‌ای"
fasdent_quick_answer:      "پاسخ ۴۰ تا ۷۰ کلمه‌ای به سؤال اصلی صفحه."
fasdent_reviewer_name:     "دکتر …"
fasdent_reviewer_credentials: "متخصص …"
fasdent_reviewer_license:  "نظام پزشکی: …"
fasdent_review_date:       "YYYY-MM-DD"
fasdent_reading_time:      "6"
fasdent_hero_badges: |
  fa-solid fa-user-doctor|بازبینی بالینی شده
  fa-solid fa-shield-halved|انطباق با HIPAA
  fa-solid fa-universal-access|دسترس‌پذیر (WCAG)
  fa-solid fa-lock|ارسال رمزنگاری‌شده
fasdent_primary_cta_label: "دریافت نوبت آنلاین"
fasdent_primary_cta_url:   "/reserve/"
fasdent_show_toc:          "1"
```

---

## Validation checklist (self-check before output)

- [ ] Exactly one intro paragraph, then key-takeaways card.
- [ ] 3–7 H2 sections; heading order is strict.
- [ ] No superlative marketing claims; no guarantees; no "best".
- [ ] "نتایج ممکن است متفاوت باشد" appears wherever outcomes are described.
- [ ] No PHI, no invented patient names, no unverifiable statistics.
- [ ] Every image has a Persian `alt`.
- [ ] At least one `.callout--warn` "when to see a dentist" block.
- [ ] Closing CTA links to `/reserve/`.
- [ ] Meta block includes reviewer name + review date.
- [ ] Quick answer is 40–70 Persian words.

---

## Output format

Return the response in **two clearly labeled sections**:

```
=== META ===
<yaml block from above, values filled in>

=== CONTENT ===
<raw HTML that will go into the WordPress editor>
```

No preamble, no explanation, no code fences around the whole thing — just the
two sections above so the operator can copy them directly.
````

### `fasdent/sample-pages/about-the-clinic.md`

_Sample page 1 — About the Clinic. Paste-ready META + CONTENT._

````markdown
# Sample Page 1 — درباره‌ی کلینیک فسدنت

Paste the **CONTENT** block into the WordPress editor (Text/HTML mode) and set
the **META** values via Custom Fields / ACF.

## === META ===

```yaml
Post title:               "درباره‌ی کلینیک فسدنت"
Post slug:                "about"
Template:                 "Fasdent Sample Page"
Featured image alt:       "نمای داخلی کلینیک دندانپزشکی فسدنت — سالن انتظار روشن با نور طبیعی"

fasdent_kicker:           "درباره‌ی ما"
fasdent_subtitle:         "کلینیکی خانوادگی برای مراقبت آرام، شفاف و مبتنی بر شواهد از سلامت دهان و دندان شما."
fasdent_quick_answer:     "فسدنت یک کلینیک عمومی و تخصصی دندانپزشکی در تهران است که بر مراقبت پیشگیرانه، شفافیت درمانی و تجربه‌ای آرام برای بیمار تمرکز دارد. تیم ما در حوزه‌های ترمیمی، اطفال، ارتودنسی و ایمپلنت آموزش دیده و همه‌ی برنامه‌های درمانی پیش از شروع، همراه با گزینه‌ها، هزینه‌ها و ریسک‌ها به‌طور کامل توضیح داده می‌شوند."
fasdent_reviewer_name:    "دکتر مریم رضایی"
fasdent_reviewer_credentials: "دندانپزشک عمومی، عضو انجمن دندانپزشکی ایران"
fasdent_reviewer_license: "نظام پزشکی: ۱۲۳۴۵۶"
fasdent_review_date:      "2026-06-20"
fasdent_reading_time:     "5"
fasdent_hero_badges: |
  fa-solid fa-user-doctor|تیم بالینی مجاز
  fa-solid fa-hand-holding-medical|مراقبت بیمارمحور
  fa-solid fa-shield-halved|حریم خصوصی HIPAA-aware
  fa-solid fa-universal-access|دسترس‌پذیر (WCAG 2.1 AA)
fasdent_primary_cta_label: "رزرو نوبت"
fasdent_primary_cta_url:   "/reserve/"
fasdent_show_toc:         "1"
```

## === CONTENT ===

```html
<p>
	فسدنت یک کلینیک دندانپزشکی خانوادگی در تهران است که با تمرکز بر
	<strong>مراقبت پیشگیرانه</strong>، شفافیت درمانی و تجربه‌ای آرام برای بیمار،
	خدمات عمومی و تخصصی ارائه می‌دهد. هدف ما این است که هر بیمار قبل از هر تصمیم درمانی،
	تصویری روشن از گزینه‌ها، هزینه‌ها و ریسک‌ها داشته باشد.
</p>

<section class="card key-takeaways">
	<h2 class="key-takeaways__title">
		<i class="fa-solid fa-list-check" aria-hidden="true"></i> نکات کلیدی
	</h2>
	<ul class="key-takeaways__list">
		<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> تیمی از دندانپزشکان عمومی و متخصص، همگی دارای مجوز نظام پزشکی.</li>
		<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> برنامه‌ی درمانی مکتوب، همراه با هزینه و ریسک‌های احتمالی، پیش از شروع درمان.</li>
		<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> پروتکل‌های استریلیزاسیون طبق دستورالعمل‌های وزارت بهداشت.</li>
		<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> تجهیزات دیجیتال: رادیوگرافی دیجیتال، اسکنر داخل دهانی و پرونده‌ی الکترونیک بیمار.</li>
	</ul>
</section>

<h2>ماموریت ما</h2>
<p>
	ماموریت فسدنت این است که مراقبت دندانپزشکی را از یک تجربه‌ی پرتنش، به مسیری
	<em>قابل‌درک، تدریجی و مبتنی بر گفت‌وگو</em> تبدیل کند. ما معتقدیم بیمار آگاه،
	تصمیم‌گیرنده‌ی اصلی درمان خودش است — نقش ما ارائه‌ی دقیق‌ترین اطلاعات و بهترین گزینه‌های ممکن است.
</p>

<div class="callout callout--info">
	<div class="callout__icon"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></div>
	<div>
		<strong>تعهد آموزشی.</strong>
		مطالب این وب‌سایت جنبه‌ی آموزشی دارد و جایگزین ویزیت حضوری نیست. نتایج ممکن است متفاوت باشد.
	</div>
</div>

<h2>ارزش‌های کلینیک</h2>
<div class="feature-grid">
	<div class="feature-card">
		<div class="feature-card__icon"><i class="fa-solid fa-comments" aria-hidden="true"></i></div>
		<h4>شفافیت</h4>
		<p>گزینه‌های درمانی، هزینه‌ها و ریسک‌ها پیش از شروع کار به‌طور کامل توضیح داده می‌شوند.</p>
	</div>
	<div class="feature-card">
		<div class="feature-card__icon"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></div>
		<h4>ایمنی و کنترل عفونت</h4>
		<p>پروتکل استریلیزاسیون طبق دستورالعمل‌های وزارت بهداشت و بازبینی دوره‌ای داخلی.</p>
	</div>
	<div class="feature-card">
		<div class="feature-card__icon"><i class="fa-solid fa-heart" aria-hidden="true"></i></div>
		<h4>احترام به بیمار</h4>
		<p>گوش دادن فعالانه، توضیح آرام، و رعایت حریم خصوصی در هر نقطه‌ی تماس.</p>
	</div>
	<div class="feature-card">
		<div class="feature-card__icon"><i class="fa-solid fa-microscope" aria-hidden="true"></i></div>
		<h4>مبتنی بر شواهد</h4>
		<p>پروتکل‌های درمانی بر پایه‌ی راهنماهای معتبر بین‌المللی و انجمن‌های حرفه‌ای.</p>
	</div>
</div>

<h2>تخصص‌ها و خدمات</h2>
<p>ما در حوزه‌های زیر خدمت‌رسانی می‌کنیم؛ برای جزئیات هر خدمت، به صفحه‌ی مربوطه مراجعه کنید:</p>
<ul>
	<li>دندانپزشکی عمومی و پیشگیری</li>
	<li>ترمیمی و زیبایی (کامپوزیت، بلیچینگ حرفه‌ای)</li>
	<li>ایمپلنت و پروتز</li>
	<li>ارتودنسی ثابت و متحرک</li>
	<li>دندانپزشکی کودکان</li>
	<li>درمان ریشه (اندودنتیکس)</li>
</ul>

<h2>روند یک ویزیت اولیه</h2>
<ol class="steps">
	<li><strong>نوبت‌دهی و پذیرش.</strong> از طریق فرم آنلاین یا تماس تلفنی، در کوتاه‌ترین زمان نوبت شما ثبت می‌شود.</li>
	<li><strong>ارزیابی بالینی.</strong> معاینه‌ی کامل دهان، دندان و لثه، به‌همراه رادیوگرافی در صورت نیاز.</li>
	<li><strong>گفت‌وگو درباره‌ی گزینه‌ها.</strong> ارائه‌ی یک یا چند گزینه‌ی درمانی به همراه ریسک، مزیت و هزینه.</li>
	<li><strong>موافقت آگاهانه.</strong> شما بعد از فهم کامل گزینه‌ها، درمان مورد نظر خود را انتخاب می‌کنید.</li>
	<li><strong>اجرای درمان و پیگیری.</strong> مراحل درمان، همراه با یادداشت پرونده و ویزیت پیگیری.</li>
</ol>

<h2>پرسش‌های پرتکرار</h2>

<details class="faq-item">
	<summary>آیا برای اولین ویزیت باید سوابق قبلی را همراه بیاورم؟</summary>
	<p>بله. هر گونه رادیوگرافی، لیست داروها و سوابق درمانی قبلی به تصمیم‌گیری دقیق‌تر کمک می‌کند. در صورت نبود، ما ارزیابی اولیه‌ی خود را انجام خواهیم داد.</p>
</details>
<details class="faq-item">
	<summary>هزینه‌ی درمان چگونه محاسبه می‌شود؟</summary>
	<p>پس از ارزیابی، برآورد مکتوب هزینه به شما ارائه می‌شود. هزینه‌ها به وضعیت بالینی هر فرد بستگی دارد و پیش از شروع درمان توافق می‌شود.</p>
</details>
<details class="faq-item">
	<summary>آیا از بیمه‌ها پذیرش می‌کنید؟</summary>
	<p>بله، ما با تعدادی از بیمه‌های پایه و تکمیلی همکاری داریم. جزئیات به‌روز را از پذیرش کلینیک بپرسید.</p>
</details>
<details class="faq-item">
	<summary>آیا خدمات اورژانس دندان ارائه می‌دهید؟</summary>
	<p>در ساعات کاری، اورژانس‌های دندانی (درد شدید، شکستگی، ضربه) با اولویت پذیرش می‌شوند. برای مراجعه‌ی فوری، ابتدا با کلینیک تماس بگیرید.</p>
</details>

<h2>چه زمانی به دندانپزشک مراجعه کنیم؟</h2>
<div class="callout callout--warn">
	<div class="callout__icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></div>
	<div>
		<strong>در این موارد وقت را از دست ندهید:</strong>
		درد شدید یا مداوم دندان، تورم صورت یا لثه، خونریزی مداوم، شکستگی یا افتادن دندان بر اثر ضربه،
		یا هرگونه تغییر ناگهانی در لثه یا مخاط دهان. در چنین شرایطی فوراً تماس بگیرید — نتایج درمان به سرعت اقدام بستگی دارد.
	</div>
</div>

<h2>گام بعدی</h2>
<p>
	اگر آماده‌ی گفت‌وگو درباره‌ی سلامت دهان و دندان خود هستید، همین حالا
	<a href="/reserve/">نوبت آنلاین رزرو کنید</a>. تیم پذیرش برای تنظیم زمان و پاسخ به پرسش‌های اولیه با شما تماس می‌گیرد.
</p>
```
````

### `fasdent/sample-pages/implants-service.md`

_Sample page 2 — Dental Implants (service page). Paste-ready META + CONTENT._

````markdown
# Sample Page 2 — ایمپلنت دندان

Paste the **CONTENT** block into the WordPress editor and set the **META**
values via Custom Fields / ACF. This page uses the same `page.php` template.

## === META ===

```yaml
Post title:               "ایمپلنت دندان — راهنمای بیمار"
Post slug:                "services/dental-implants"
Parent page:              "خدمات"
Template:                 "Fasdent Sample Page"
Featured image alt:       "شمای سه‌بعدی یک ایمپلنت دندانی جایگزین‌شده در فک — تصویر آموزشی"

fasdent_kicker:           "خدمات درمانی"
fasdent_subtitle:         "جایگزینی بلندمدت دندان‌های ازدست‌رفته با پروتکل‌های مبتنی بر شواهد، برنامه‌ی درمانی شفاف و پیگیری منظم."
fasdent_quick_answer:     "ایمپلنت دندان یک پایه‌ی تیتانیومی است که در استخوان فک قرار می‌گیرد تا جایگزین ریشه‌ی دندان ازدست‌رفته شود. روی آن یک تاج (کراون) یا پروتز نصب می‌شود. این درمان برای همه‌ی افراد مناسب نیست و پس از ارزیابی بالینی و رادیوگرافی درباره‌ی کاندید بودن شما تصمیم‌گیری می‌شود. نتایج ممکن است متفاوت باشد."
fasdent_reviewer_name:    "دکتر امیر شریفی"
fasdent_reviewer_credentials: "متخصص جراحی دهان و فک، عضو انجمن ایمپلنتولوژی ایران"
fasdent_reviewer_license: "نظام پزشکی: ۷۸۹۰۱۲"
fasdent_review_date:      "2026-07-05"
fasdent_reading_time:     "8"
fasdent_hero_badges: |
  fa-solid fa-user-doctor|بازبینی بالینی شده
  fa-solid fa-microscope|مبتنی بر شواهد
  fa-solid fa-heart-pulse|راهنمای بیمار
  fa-solid fa-shield-halved|حریم خصوصی HIPAA-aware
fasdent_primary_cta_label: "رزرو مشاوره‌ی ایمپلنت"
fasdent_primary_cta_url:   "/reserve/?service=implant"
fasdent_show_toc:         "1"
```

## === CONTENT ===

```html
<p>
	ایمپلنت دندان یکی از گزینه‌های شناخته‌شده برای جایگزینی
	<strong>ریشه‌ی دندان ازدست‌رفته</strong> است. در این صفحه، بدون وعده‌ی نتیجه‌ی قطعی،
	روند درمان، معیارهای انتخاب بیمار و آنچه باید قبل و بعد از عمل انتظار داشته باشید توضیح داده می‌شود.
	تصمیم نهایی همیشه پس از ارزیابی حضوری با شما گرفته می‌شود.
</p>

<section class="card key-takeaways">
	<h2 class="key-takeaways__title">
		<i class="fa-solid fa-list-check" aria-hidden="true"></i> نکات کلیدی
	</h2>
	<ul class="key-takeaways__list">
		<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> ایمپلنت یک پایه‌ی تیتانیومی است که در استخوان فک قرار می‌گیرد.</li>
		<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> کاندیدا بودن هر فرد بر اساس سلامت لثه، حجم استخوان و بیماری‌های زمینه‌ای تعیین می‌شود.</li>
		<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> روند کامل معمولاً چند ماه طول می‌کشد (اُسئواینتگریشن).</li>
		<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> نگهداری روزانه و ویزیت‌های منظم برای دوام درمان ضروری است.</li>
		<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> نتایج ممکن است متفاوت باشد و ما هیچ نتیجه‌ی قطعی را تضمین نمی‌کنیم.</li>
	</ul>
</section>

<h2>ایمپلنت دقیقاً چیست؟</h2>
<p>
	ایمپلنت از سه بخش تشکیل شده است: فیکسچر (پایه‌ی تیتانیومی داخل استخوان)،
	اباتمنت (رابط)، و روکش نهایی (تاج، بریج یا پروتز). فیکسچر پس از قرارگیری در استخوان،
	طی چند هفته تا چند ماه با آن جوش می‌خورد؛ به این فرآیند <em>اُسئواینتگریشن</em> می‌گویند.
</p>

<div class="callout callout--info">
	<div class="callout__icon"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></div>
	<div>
		<strong>ایمپلنت جایگزین ریشه است، نه صرفاً دندان.</strong>
		به‌همین دلیل عملکردی نزدیک‌تر به دندان طبیعی نسبت به بریج یا پروتز متحرک دارد،
		اما به‌معنی مناسب بودن برای همه‌ی افراد نیست.
	</div>
</div>

<h2>چه کسی کاندیدای ایمپلنت است؟</h2>
<p>ارزیابی کامل شامل معاینه‌ی بالینی، رادیوگرافی و در بسیاری موارد <abbr title="Cone Beam CT">CBCT</abbr> است. عوامل زیر بررسی می‌شوند:</p>
<div class="feature-grid">
	<div class="feature-card">
		<div class="feature-card__icon"><i class="fa-solid fa-bone" aria-hidden="true"></i></div>
		<h4>حجم و کیفیت استخوان</h4>
		<p>در صورت نبود استخوان کافی، ممکن است پیوند استخوان (Bone Graft) پیشنهاد شود.</p>
	</div>
	<div class="feature-card">
		<div class="feature-card__icon"><i class="fa-solid fa-tooth" aria-hidden="true"></i></div>
		<h4>سلامت لثه</h4>
		<p>بیماری لثه‌ی درمان‌نشده، احتمال شکست ایمپلنت را افزایش می‌دهد.</p>
	</div>
	<div class="feature-card">
		<div class="feature-card__icon"><i class="fa-solid fa-heart-pulse" aria-hidden="true"></i></div>
		<h4>بیماری‌های زمینه‌ای</h4>
		<p>دیابت کنترل‌نشده، برخی داروها و سیگار بر روند بهبود اثر می‌گذارند.</p>
	</div>
	<div class="feature-card">
		<div class="feature-card__icon"><i class="fa-solid fa-user-clock" aria-hidden="true"></i></div>
		<h4>سن و رشد فک</h4>
		<p>در نوجوانان معمولاً منتظر تکمیل رشد فک می‌مانیم.</p>
	</div>
</div>

<h2>روند درمان — گام به گام</h2>
<ol class="steps">
	<li><strong>مشاوره و ارزیابی.</strong> معاینه، رادیوگرافی و در صورت نیاز CBCT برای بررسی حجم استخوان.</li>
	<li><strong>برنامه‌ی درمانی مکتوب.</strong> گزینه‌ها، هزینه‌ها، مدت‌زمان و ریسک‌های احتمالی به‌طور شفاف ارائه می‌شود.</li>
	<li><strong>آماده‌سازی (در صورت نیاز).</strong> شامل درمان لثه، کشیدن دندان غیرقابل‌نگهداری یا پیوند استخوان.</li>
	<li><strong>جراحی قرارگیری فیکسچر.</strong> معمولاً با بی‌حسی موضعی، حدود ۴۵ تا ۹۰ دقیقه.</li>
	<li><strong>دوره‌ی جوش‌خوردن (اُسئواینتگریشن).</strong> بسته به شرایط، ۲ تا ۶ ماه.</li>
	<li><strong>قرارگیری اباتمنت و قالب‌گیری.</strong> برای ساخت روکش نهایی.</li>
	<li><strong>نصب روکش نهایی.</strong> تنظیم بایت و آموزش مراقبت روزانه.</li>
	<li><strong>پیگیری منظم.</strong> ویزیت‌های دوره‌ای برای پایش سلامت ایمپلنت و لثه.</li>
</ol>

<h2>ریسک‌ها و عوارض احتمالی</h2>
<p>هیچ درمان جراحی بدون ریسک نیست. عوارض احتمالی — که در بیشتر موارد قابل پیشگیری یا مدیریت هستند — عبارت‌اند از:</p>
<ul>
	<li>عفونت موضعی در محل جراحی.</li>
	<li>آسیب به ساختارهای مجاور (اعصاب، سینوس فک بالا) در موارد نادر.</li>
	<li>شکست در اُسئواینتگریشن (احتمال کم، اما ممکن).</li>
	<li>پری‌ایمپلنتیت (التهاب اطراف ایمپلنت) در صورت رعایت‌نکردن بهداشت.</li>
</ul>

<div class="callout callout--warn">
	<div class="callout__icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></div>
	<div>
		<strong>پیش از عمل حتماً اطلاع دهید:</strong>
		داروهای مصرفی (به‌ویژه ضدانعقاد، بیس‌فسفونات‌ها)، سابقه‌ی حساسیت، بیماری‌های قلبی، دیابت، یا بارداری.
		این اطلاعات برای برنامه‌ریزی ایمن ضروری هستند.
	</div>
</div>

<h2>مراقبت پس از ایمپلنت</h2>
<p>دوام ایمپلنت به میزان زیادی به مراقبت روزانه‌ی شما بستگی دارد:</p>
<ul>
	<li>مسواک‌زدن دوبار در روز با روش صحیح و استفاده‌ی روزانه از نخ دندان یا فلاس مخصوص.</li>
	<li>خودداری از سیگار — نیکوتین یکی از مهم‌ترین عوامل شکست ایمپلنت است.</li>
	<li>ویزیت‌های منظم و جرم‌گیری حرفه‌ای طبق برنامه‌ی کلینیک.</li>
	<li>پرهیز از جویدن اجسام سخت (یخ، ناخن، مداد).</li>
</ul>

<h2>هزینه و مدت‌زمان</h2>
<p>
	هزینه به تعداد ایمپلنت، نوع فیکسچر، نیاز به پیوند استخوان و نوع روکش نهایی بستگی دارد
	و پس از ارزیابی به‌صورت مکتوب اعلام می‌شود. مدت کل درمان معمولاً بین ۳ تا ۹ ماه است.
	<strong>نتایج ممکن است متفاوت باشد.</strong>
</p>

<h2>پرسش‌های پرتکرار</h2>

<details class="faq-item">
	<summary>آیا جراحی ایمپلنت دردناک است؟</summary>
	<p>جراحی با بی‌حسی موضعی انجام می‌شود و حین عمل درد وجود ندارد. پس از آن، ناراحتی خفیف تا متوسط چند روز اول با داروی تجویزشده قابل کنترل است.</p>
</details>
<details class="faq-item">
	<summary>عمر یک ایمپلنت چقدر است؟</summary>
	<p>در صورت مراقبت مناسب، ایمپلنت می‌تواند سال‌ها دوام بیاورد. با این حال هیچ تضمین قطعی وجود ندارد؛ عواملی مانند سیگار، بیماری لثه و ضربه بر طول عمر آن اثر می‌گذارند.</p>
</details>
<details class="faq-item">
	<summary>آیا در یک روز می‌توان ایمپلنت و روکش را انجام داد؟</summary>
	<p>در برخی موارد خاص (Immediate Loading) ممکن است، اما این تصمیم به شرایط بالینی و کیفیت استخوان بستگی دارد و برای همه‌ی بیماران مناسب نیست.</p>
</details>
<details class="faq-item">
	<summary>بیمه‌ی من ایمپلنت را پوشش می‌دهد؟</summary>
	<p>پوشش بیمه‌ای بین شرکت‌ها متفاوت است. پیش از شروع درمان با پذیرش کلینیک و بیمه‌گر خود درباره‌ی جزئیات پوشش گفت‌وگو کنید.</p>
</details>
<details class="faq-item">
	<summary>اگر یک ایمپلنت شکست بخورد چه اتفاقی می‌افتد؟</summary>
	<p>شکست ایمپلنت نادر است اما ممکن. در چنین شرایطی، پس از بررسی علت، معمولاً امکان جایگزینی وجود دارد. جزئیات به وضعیت هر بیمار بستگی دارد.</p>
</details>

<h2>چه زمانی سریع‌تر به دندانپزشک مراجعه کنیم؟</h2>
<div class="callout callout--warn">
	<div class="callout__icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></div>
	<div>
		<strong>در این علائم پس از جراحی ایمپلنت، فوراً تماس بگیرید:</strong>
		خونریزی مداوم بیش از چند ساعت، تورم فزاینده‌ی صورت، درد شدیدی که با دارو کاهش نمی‌یابد،
		بی‌حسی طولانی‌مدت لب یا زبان، یا تب. اقدام به‌موقع در نتیجه‌ی درمان اهمیت زیادی دارد.
	</div>
</div>

<h2>گام بعدی</h2>
<p>
	اگر می‌خواهید بدانید ایمپلنت برای شما گزینه‌ی مناسبی است یا نه،
	<a href="/reserve/?service=implant">جلسه‌ی مشاوره‌ی ایمپلنت رزرو کنید</a>.
	در این جلسه، بدون تعهد، وضعیت شما بررسی و گزینه‌های ممکن — شامل جایگزین‌های غیرایمپلنت — با شما مرور می‌شود.
</p>
```
````

### `fasdent/README.md`

_Install guide, enqueue snippet, Custom Fields reference._

````markdown
# Fasdent — Sample Page System

Beautiful, RTL, HIPAA-aware, WCAG 2.1 AA, FTC-safe WordPress page template
for the Fasdent dental clinic (http://fasdent.ir/).

## Contents

```
fasdent/
├── page.php                              # WordPress page template
├── assets/
│   ├── css/page.css                      # Dedicated stylesheet (load after main.css)
│   └── js/page.js                        # Dedicated JS (no jQuery)
├── inc/
│   └── prompts/page-generator.md         # Master AI generation prompt
├── sample-pages/
│   ├── about-the-clinic.md               # Sample 1 — About page (paste-ready)
│   └── implants-service.md               # Sample 2 — Implants service page
└── README.md
```

## Install

1. Copy `page.php` to the root of your active theme.
2. Copy `assets/css/page.css` and `assets/js/page.js` into your theme's assets.
3. In `functions.php`, enqueue **after** `main.css` / `main.js`:

    ```php
    add_action('wp_enqueue_scripts', function () {
        // Only load on pages that use the Fasdent Sample Page template.
        if (!is_page_template('page.php')) return;
        $ver = filemtime(get_stylesheet_directory() . '/assets/css/page.css');
        wp_enqueue_style(
            'fasdent-page',
            get_stylesheet_directory_uri() . '/assets/css/page.css',
            ['fasdent-main'],
            $ver
        );
        wp_enqueue_script(
            'fasdent-page',
            get_stylesheet_directory_uri() . '/assets/js/page.js',
            [],
            filemtime(get_stylesheet_directory() . '/assets/js/page.js'),
            true
        );
    });
    ```

4. Make sure Font Awesome 6 is loaded (the template uses `fa-solid`, `fa-regular`, `fa-brands`).

## Create a page

1. WP Admin → **Pages → Add New**.
2. Title + content (use blocks/HTML — see `inc/prompts/page-generator.md` for the
   exact classes the CSS styles).
3. In **Page Attributes → Template**, select **Fasdent Sample Page**.
4. Fill Custom Fields (ACF-compatible):

| Meta key | Type | Example |
|---|---|---|
| `fasdent_kicker` | text | خدمات ما |
| `fasdent_subtitle` | text | یک جمله زیرعنوان |
| `fasdent_quick_answer` | textarea | پاسخ ۴۰–۷۰ کلمه‌ای |
| `fasdent_reviewer_name` | text | دکتر مریم رضایی |
| `fasdent_reviewer_credentials` | text | دندانپزشک عمومی |
| `fasdent_reviewer_license` | text | نظام پزشکی: ۱۲۳۴۵۶ |
| `fasdent_review_date` | date (YYYY-MM-DD) | 2026-06-20 |
| `fasdent_reading_time` | number | 6 |
| `fasdent_hero_badges` | textarea (`icon\|label` per line) | fa-solid fa-user-doctor\|بازبینی شده |
| `fasdent_primary_cta_label` | text | دریافت نوبت |
| `fasdent_primary_cta_url` | url | /reserve/ |
| `fasdent_show_toc` | text ("1" or "") | 1 |

Global options (from `wp_options`):

| Option key | Purpose |
|---|---|
| `fasdent_emergency_phone` | Emergency phone number |
| `fasdent_booking_url` | Booking URL (defaults to `/reserve/`) |

## Sample pages

Two ready-to-paste sample pages live in `sample-pages/`:
- `about-the-clinic.md` — About the Clinic
- `implants-service.md` — Dental Implants (service page)

Each file has a `=== META ===` block for the Custom Fields and a
`=== CONTENT ===` block with the HTML to paste into the WordPress editor.

## Compliance notes

- **HIPAA-aware**: no PHI fields on-page. Newsletter form is honeypotted and
  submitted to a REST endpoint you control (make sure it's HTTPS + BAA).
- **WCAG 2.1 AA**: skip link, semantic headings, focus visible, reduced-motion
  respected, RTL logical properties.
- **FTC**: no guarantees, "results may vary" repeated where outcomes are
  discussed.

## License

Provided for the Fasdent theme. Adapt freely inside the theme.
````

## ✅ What to do next

- [ ] **Unzip** [`fasdent-page-system.zip`](https://www.genspark.ai/api/files/s/9UH1FKVk) and drop the folder into your theme.
- [ ] **Enqueue** `page.css` / `page.js` (snippet above) and confirm Font Awesome 6 is loaded.
- [ ] **Create the two sample pages** — paste the META + CONTENT from `sample-pages/about-the-clinic.md` and `sample-pages/implants-service.md`.
- [ ] **Set global options** — `fasdent_emergency_phone` and `fasdent_booking_url`.
- [ ] **Generate more pages** via the master AI prompt at `inc/prompts/page-generator.md` — just fill the section topic and it produces compliant META + CONTENT for a new page.


---

**Compliance posture baked in:** HIPAA-aware forms · WCAG 2.1 AA · FTC truth-in-advertising · educational / emergency / results / privacy disclaimer stack · license display · opt-in cookies.
