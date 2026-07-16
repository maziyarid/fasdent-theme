# Fasdent Theme v2.1.1

> قالب اختصاصی کلینیک دندانپزشکی فس‌دنت — دکتر کیوان علی‌پسندی
> RTL کامل، بدون CDN، 12 نوع Schema.org، WCAG 2.1 AA، PHP 8.2+، WordPress 6.5+

---

## نصب سریع

1. کپی پوشه `fasdent-theme` در `wp-content/themes/`
2. فعال‌سازی در ادمین > نمایش > پوسته‌ها
3. ذخیره Permalink: Settings > Permalinks > Save
4. Appearance > Menus: اختصاص به main-menu / footer-menu / legal-menu
5. Appearance > Customize > Clinic Information

---

## پیش‌نیازها

| مورد | نسخه |
|------|------|
| WordPress | 6.5+ |
| PHP | 8.2+ |
| MySQL | 8.0+ |
| ACF Pro | اختیاری — fallback داخلی وجود دارد |
| Elementor | اختیاری |

بدون CDN — همه فونت‌ها و آیکون‌ها از assets/fonts/ سرو می‌شوند.
---

## ساختار فایل‌ها

fasdent-theme/
  style.css                    v2.1.0
  functions.php                Bootstrap + 18 require()
  header.php / footer.php      Skip link, ARIA, widgets
  404.php                      404 Hub
  front-page.php               صفحه اصلی 9 بخش
  single.php                   پست بلاگ (ToC, reactions, share)
  single-service.php           Template B+C (emergency)
  single-doctor.php            پروفایل پزشک
  taxonomy-service_category.php  Pillar Page Template A
  archive-service.php / author.php / tag.php
  comments.php / search.php / index.php / page.php

  inc/
    setup.php / enqueue.php / post-types.php / taxonomies.php
    acf-fields.php / customizer.php / seo.php / schema.php
    breadcrumb.php / security.php / performance.php / forms.php
    elementor.php / toc.php / post-meta.php / related-posts.php
    cookies.php / dashboard.php / booking.php / polls.php
    ajax-search.php / admin-bookings.php

  page-templates/
    appointment.php      رزرو نوبت 4 مرحله
    contact.php          تماس + نقشه + فرم
    faq.php              FAQ با جستجو
    gallery.php          گالری + lightbox
    pricing.php          تعرفه جدول دسته‌بندی
    sitemap.php          نقشه سایت بصری
    knowledge-base.php   مرکز آموزش
    privacy-policy.php   حریم خصوصی
    patient-rights.php   حقوق بیمار
    cancellation-policy.php  لغو نوبت
    medical-disclaimer.php   سلب مسئولیت

  template-parts/
    card-service.php / card-category.php / testimonial-card.php
    cta-banner.php / faq-accordion.php / before-after.php
    toc-sidebar.php / social-share.php / rating-display.php
    key-takeaways.php / poll.php

  assets/
    css/main.css     370+ خط — کامپوننت‌ها RTL responsive
    css/print.css    استایل‌های چاپ
    js/main.js       415+ خط — FAQ, nav, forms, booking, search, ToC, polls
    fonts/Irancell/ + FontAwesome/


---

## Schema Markup — 12 نوع

| Schema | شرط |
|--------|-----|
| Dentist (LocalBusiness) | همه صفحات |
| Organization | صفحه اصلی |
| WebSite + SearchAction | صفحه اصلی |
| MedicalProcedure + HowTo | صفحات خدمت |
| MedicalWebPage | صفحات خدمت |
| FAQPage | خدمت + دسته + صفحه FAQ |
| Physician | صفحه پزشک |
| AggregateRating + Review | صفحات دارای testimonial |
| EmergencyService | خدمات اورژانسی |
| BlogPosting + Article | پست‌های بلاگ |
| BreadcrumbList | همه صفحات غیر از خانه |
| Speakable | پست‌ها و خدمات |
| ImageObject | صفحات دارای تصویر شاخص |

---

## تنظیمات Customizer

| بخش | کلیدهای مهم |
|-----|------------|
| اطلاعات کلینیک | fasdent_clinic_name, fasdent_phone, fasdent_phone_intl, fasdent_email, fasdent_address, fasdent_hours, fasdent_geo_lat/lng, fasdent_map_embed |
| شبکه‌های اجتماعی | fasdent_instagram, fasdent_telegram, fasdent_whatsapp |
| آمار صفحه اصلی | fasdent_stat_patients/years/implants/rating |
| آنالیتیکس | fasdent_ga4_id, fasdent_clarity_id, fasdent_turnstile_key/secret, fasdent_cookie_text, fasdent_indexnow_key |

---

## جداول پایگاه داده

| جدول | هدف |
|------|-----|
| wp_fasdent_bookings | نوبت‌های رزرو شده با status workflow |
| wp_fasdent_polls | سوالات نظرسنجی |
| wp_fasdent_poll_votes | آرای نظرسنجی با IP hash |

جداول به‌صورت خودکار هنگام فعال‌سازی قالب ایجاد می‌شوند.

---

## جریان رزرو نوبت

  مرحله 1: اطلاعات شخصی (نام، تلفن، ایمیل، سن، جنسیت)
  مرحله 2: پزشکی (شرح مشکل، سابقه، دارو، آلرژی)
  مرحله 3: نوبت (خدمت، پزشک، تاریخ، بازه، اورژانسی)
  مرحله 4: خلاصه + حریم خصوصی + ارسال
    => DB row در wp_fasdent_bookings
    => wp_mail به ادمین
    => gtag booking_submitted + clarity event
    => ادمین: admin.php?page=fasdent-bookings
    => تغییر وضعیت: pending/confirmed/completed/cancelled

---

## راهنمای clone برای کلینیک مشابه

1. رنگ: فقط --color-primary و --color-secondary در :root در main.css
2. فونت: پوشه Irancell/ را جایگزین + irancell.css بنویسید + enqueue.php بروز کنید
3. Schema: Dentist را به MedicalBusiness تغییر دهید
4. CPT slugs: service را در post-types.php تغییر دهید
5. Text Domain: جستجوی سراسری fasdent به slug جدید

---

## Changelog

### v2.1.1 — 2026-07-13

#### Bug Fixes
- **`assets/js/single-post.js`** — AJAX `action` key corrected from `fasdent_react` to `fasdent_post_reaction` (matched the registered handler)
- **`inc/post-meta.php`** — reaction handler now reads `post_id` before calling `check_ajax_referer('fasdent_react_{id}', 'nonce')`, fixing a nonce-action mismatch that rejected every reaction vote
- **`inc/enqueue.php`** — `single-post.css` and `single-post.js` were never enqueued; added conditional block on `is_singular('post')` — step comments also renumbered (duplicate `// ۵)`)
- **`style.css`** — version header corrected from `2.0.0` to `2.1.0` (matched `FASDENT_VERSION` constant)
- **`template-parts/toc-sidebar.php`** — removed BOM; `FASDENT_TOC_SIDEBAR` constant now documented as intentionally defined before `remove_filter`
- **`README.md`** — install path typo `asdent-theme` → `fasdent-theme`
- **`data/fasdent-import.xml`** — file was truncated (missing `</channel></rss>`); closing tags appended, file now valid XML

#### New Files Added
- **`screenshot.png`** (880×660) — required by WordPress for theme recognition in Appearance → Themes
- **`languages/fasdent.pot`** — translation template stub; satisfies `load_theme_textdomain` reference and suppresses WP notice

#### Cleanup (outside-theme artefacts removed from repo)
- Deleted `Fasdent Pages/` — duplicate of `page-templates/fasdent-page.php` + `assets/css/page.css` + `assets/js/page.js`
- Deleted `fasdent-page-system/` — mirror of above
- Deleted `fasdent-page-system.md` + `.bak` — dev planning documents
- Deleted `data/fasdent-import-p3.xml` — contained only the 10 `service_category` terms already present in `fasdent-import.xml` (strict subset, nothing new)
- Deleted `languages/fasdent.pot` at repo root — correct file is `fasdent-theme/languages/fasdent.pot`

### v2.1.0 — 2026-07-12
- inc/performance.php: WebP upload conversion, fetchpriority on first image, DNS prefetch, emoji removal
- inc/security.php: Permissions-Policy header added
- inc/schema.php: +Organization, +Speakable, +ImageObject, +IndexNow ping
- inc/customizer.php: +IndexNow Key setting
- inc/cookies.php: Google Consent Mode v2 (default denied before user consent)
- inc/admin-bookings.php: NEW — booking management with status workflow AJAX
- inc/dashboard.php: +booking stats widget (today/7d/30d + pending/confirmed)
- page-templates/medical-disclaimer.php: NEW
- FASDENT_VERSION: 2.0.0 => 2.1.0

### v2.0.0 — 2026-07-12
- Font: Irancell replaces Vazirmatn — irancell.css created, all paths fixed
- 15 new files, 8 templates rewritten, 11 modules enhanced
- BUG-001 to BUG-008 all resolved
- Phase 9: booking, polls, live search, legal pages, all template rewrites

### v1.0.0 — initial
- Theme scaffold: CPTs, schemas, customizer, security, Elementor support

---

Fasdent Dev Team — https://fasdent.ir
