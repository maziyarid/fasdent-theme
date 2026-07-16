# Fasdent Theme v2.1.2

> قالب اختصاصی کلینیک دندانپزشکی فس‌دنت — دکتر کیوان علی‌پسندی
> RTL کامل، بدون CDN، 12 نوع Schema.org، WCAG 2.1 AA، PHP 8.2+، WordPress 6.5+

---

## نصب سریع

1. کپی پوشه `fasdent-theme` در `wp-content/themes/`
2. فعال‌سازی در ادمین > نمایش > پوسته‌ها
3. ذخیره Permalink: **Settings → Permalinks → Save**
4. **Appearance → بارگذاری نمونه داده** → کلیک روی «شروع بارگذاری نمونه داده»
5. دوباره **Settings → Permalinks → Save** (برای rewrite قوانین CPT)
6. Appearance → Customize → تنظیمات کلینیک فس‌دنت (در صورت نیاز ویرایش)

منوها، صفحات، خدمات، پزشکان، مقالات، FAQ و نظرات بیماران به‌صورت خودکار ساخته و به `main-menu` / `footer-menu` / `legal-menu` اختصاص داده می‌شوند.

برای جزئیات کامل محتوا و نحوه ریست: `data/demo/README.md`

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

```
fasdent-theme/
  style.css                    v2.1.2
  functions.php                Bootstrap + modules + demo importer (admin)
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

  data/demo/                   ایمپورتر نمونه داده
    import.php                 Appearance → بارگذاری نمونه داده
    taxonomy-terms.php / services.php / doctors.php
    testimonials.php / faqs.php / pages.php / posts.php
    posts-data/batch-*.php     ۲۹ مقاله SEO
    menus.php / options.php

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
    css/main.css     کامپوننت‌ها RTL responsive
    css/print.css    استایل‌های چاپ
    js/main.js       FAQ, nav, forms, booking, search, ToC, polls
    fonts/Irancell/ + FontAwesome/
```

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

### v2.1.2 — 2026-07-16

#### Demo Content System (complete)
- **Appearance → بارگذاری نمونه داده** fully functional
- 10 service categories, ~20 services, 3 doctors, 8 testimonials, 12 FAQs
- 14 pages (Home, Blog, Appointment, Contact, About, FAQ, Pricing, Gallery, legal pages, Sitemap, Knowledge Base)
- **29 SEO blog posts** with trust meta, related_posts linking, categories
- Menus assigned to `main-menu` / `footer-menu` / `legal-menu`
- Customizer options (phone, hours, geo, stats, emergency) pre-filled
- Post-import relationship resolution (related services, testimonials → services, related posts)

#### Bug Fixes & Hardening
- **Removed UTF-8 BOM** from all remaining PHP files (`post-meta`, `booking`, `polls`, `security`, `performance`, `dashboard`, `admin-bookings`, `cookies`, `related-posts`, `ajax-search`, `toc`)
- **View counter** — increments on every real page view (was limited to +1/day)
- **Reading time** — character-based estimate suitable for Persian (`mb_strlen / 1000`)
- **related-posts.php** — now prefers `related_posts` meta (from demo) before category fallback
- Version sync: `style.css` + `FASDENT_VERSION` = 2.1.2

### v2.1.1 — 2026-07-13

#### Bug Fixes
- **`assets/js/single-post.js`** — AJAX `action` key corrected from `fasdent_react` to `fasdent_post_reaction`
- **`inc/post-meta.php`** — reaction handler nonce fix
- **`inc/enqueue.php`** — single-post assets enqueued on singular posts
- **`style.css`** — version header corrected
- **`template-parts/toc-sidebar.php`** — BOM removed
- **`README.md`** — install path typo fixed
- **`data/fasdent-import.xml`** — closing tags restored

#### New Files Added
- **`screenshot.png`** — theme recognition
- **`languages/fasdent.pot`** — translation stub

### v2.1.0 — 2026-07-12
- performance, security, schema, Consent Mode v2, booking admin, medical-disclaimer

### v2.0.0 — 2026-07-12
- Irancell font, Phase 9 features

### v1.0.0 — initial
- Theme scaffold

---

Fasdent Dev Team — https://fasdent.ir
