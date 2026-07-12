# پلن و چک‌لیست کامل پروژه قالب فس‌دنت

> **نسخه:** 2.0.0 | **تاریخ آخرین بروزرسانی:** 1404-04-21 | **وضعیت:** فازهای ۱–۷ کامل، فاز ۸ در جریان، فازهای ۹–۱۱ برنامه‌ریزی‌شده

<div dir="rtl">

## فاز ۱ — زیرساخت قالب ✅ کامل

- [x] ساختار پوشه استاندارد وردپرس (`fasdent-theme/`)
- [x] `style.css` با هدر قالب (Theme Name, Text Domain: fasdent, RTL) — v2.0.0
- [x] `functions.php` ماژولار (require فایل‌های `inc/`)
- [x] **فونت ایرانسل لوکال** — ۶ وزن (200–800)، woff2/woff/eot، `font-display: swap` — `assets/fonts/Irancell/irancell.css`
- [x] **Font Awesome 7 Pro لوکال** در `assets/fonts/FontAwesome/css/all.css` — بدون CDN
- [x] enqueue با تابع `fasdent_enqueue_scripts()` روی هوک `wp_enqueue_scripts`
- [x] Preload فونت‌های حیاتی (`Irancell_Regular.woff2`, `Irancell_Bold.woff2`, `fa-solid-900.woff2`)
- [x] RTL کامل در تمام لایه‌ها (front + editor styles)
- [x] حذف نسخه وردپرس از هدر + هاردنینگ امنیتی
- [x] Defer برای JS غیرحیاتی + preload فونت + lazy loading تصاویر
- [x] Print CSS (`assets/css/print.css`) با `media="print"`

## فاز ۲ — انواع محتوا (CPT / Taxonomy / Fields) ✅ کامل

- [x] CPT `service` (خدمات) — آیکون tooth، آرشیو `/services/`
- [x] Taxonomy هرارشیک `service_category` با rewrite: `/services/%category%/`
- [x] Rewrite نهایی خدمات: `/services/{parent-category-slug}/{service-slug}/`
- [x] CPT `doctor` (پزشکان)
- [x] CPT `testimonial` (نظرات بیماران) + فیلد امتیاز ۱–۵
- [x] فیلدهای ACF (PHP-registered): قیمت پایه، مدت درمان، مراحل (Repeater)، مزایا (Repeater)، FAQ (Repeater)، گالری قبل/بعد، خدمات مرتبط (Relationship)، آیکون FontAwesome
- [x] فال‌بک متاباکس داخلی در نبود ACF (قالب مستقل کار می‌کند)
- [x] Permalink پست‌های بلاگ: `/%category%/%postname%/`

## فاز ۳ — قالب‌های نمایش ✅ کامل

- [x] `header.php`: نوار اورژانس چسبان (fa-phone-volume + Click-to-Call 09201441469) + لوگو + منوی اصلی + دکمه CTA رزرو نوبت + منوی موبایل همبرگری
- [x] `footer.php`: درباره کلینیک، لینک‌های سریع، خدمات پرطرفدار، اطلاعات تماس، ساعات کاری، شبکه‌های اجتماعی، Schema سراسری Dentist/LocalBusiness
- [x] `front-page.php` (قالب صفحه اصلی):
  - [x] Hero + دکمه‌های CTA (رزرو نوبت + تماس)
  - [x] آمار کلینیک (بیمار، سال تجربه، ایمپلنت موفق، رضایت)
  - [x] گرید ۱۰ دسته خدمات
  - [x] باکس «خدمات محبوب» → ۵ خدمت پرطرفدار (ایمپلنت تک‌دندان، لمینت، ارتودنسی ثابت، روکش، عصب‌کشی)
  - [x] معرفی دکتر علی‌پسندی + لینک بیوگرافی
  - [x] نظرات بیماران (اسلایدر)
  - [x] گالری قبل/بعد
  - [x] آخرین مقالات بلاگ
  - [x] نقشه گوگل + فرم رزرو سریع
- [x] `archive-service.php`: گرید ۱۰ Pillar + معرفی
- [x] `taxonomy-service_category.php` — **قالب A (Pillar Page)**: Hero(H1) → Breadcrumb+Schema → معرفی ۲۰۰–۳۰۰ کلمه → گرید زیرخدمات آیکون‌دار → چرا ما → گالری → نظرات+AggregateRating → FAQ+Schema → CTA پایانی
- [x] `single-service.php` — **قالب B**: Hero(H1) → Breadcrumb ۳سطحی → این خدمت چیست → مزایا آیکون‌دار → مراحل (HowTo Schema) → قیمت/جدول → گالری → مراقبت‌های بعد → FAQ(FAQPage Schema) → خدمات مرتبط (۳ لینک Cross-Cluster) → CTA
- [x] **قالب C (اورژانس)**: نوار قرمز چسبان + OpeningHoursSpecification + راهنمای اقدامات فوری (خودکار برای دسته dental-emergency)
- [x] `single-doctor.php`: بیوگرافی + Physician Schema
- [x] صفحات قالب‌دار: درباره ما، گالری (فیلتر+لایت‌باکس)، FAQ عمومی (آکاردئون+Schema)، تعرفه‌ها (جدول+فیلتر)، تماس با ما (فرم+نقشه)، رزرو نوبت (فرم چندمرحله‌ای)، 404 اختصاصی
- [x] `template-parts/`: card-service, card-category, breadcrumb, faq-accordion, cta-banner, before-after, testimonial-card

## فاز ۴ — سئو تکنیکال ✅ کامل

- [x] H1 یکتای هر صفحه + سلسله‌مراتب H2>H3
- [x] Meta Title < 60 و Meta Description < 155 کاراکتر (شامل کلمه کلیدی + شماره تماس)
- [x] Canonical URL روی همه صفحات
- [x] Open Graph + Twitter Card
- [x] Schema های JSON-LD:
  - [x] `Dentist` (LocalBusiness) سراسری در فوتر: نام، آدرس، تلفن، ساعات، geo
  - [x] `MedicalProcedure` روی هر صفحه خدمت
  - [x] `FAQPage` روی صفحات دارای FAQ
  - [x] `BreadcrumbList` روی همه صفحات
  - [x] `Review`/`AggregateRating` روی صفحات دارای نظرات
  - [x] `Physician` روی صفحه دکتر
  - [x] `OpeningHoursSpecification` در صفحات اورژانس
- [x] robots.txt استاندارد (فیلتر داخلی) + سازگاری Sitemap با Yoast/Rank Math + WP Core Sitemap
- [x] پاسخ مستقیم در ابتدای FAQ برای Featured Snippet / جستجوی صوتی
- [x] آماده‌سازی ساختار چندزبانگی (hreflang hook — فعلاً فقط fa-IR)

## فاز ۵ — لینک‌سازی داخلی (نقشه اجباری) ✅ کامل

- [x] خانه → /services/ + ۵ خدمت پرطرفدار
- [x] /services/ → هر ۱۰ Pillar
- [x] هر Pillar → همه زیرخدمات خودش + Breadcrumb به /services/
- [x] هر خدمت → Pillar والد + ۳ خدمت هم‌دسته + /appointment/
- [x] ۱۰ جفت Cross-Cluster (هاردکد در فیلد خدمات مرتبط + انکر طبیعی در متن):
  | # | از | به | رابطه |
  |---|---|---|---|
  | 1 | single-tooth-implant | dental-bridge | جایگزین درمانی |
  | 2 | single-tooth-implant | implant-prosthesis | ادامه درمان |
  | 3 | root-canal | dental-crown | نیاز پس از عصب‌کشی |
  | 4 | tooth-extraction | single-tooth-implant | جایگزینی دندان |
  | 5 | smile-design | dental-laminate | زیرمجموعه طراحی لبخند |
  | 6 | smile-design | teeth-whitening | بخشی از طراحی لبخند |
  | 7 | gingivitis-treatment | deep-scaling | مسیر درمانی |
  | 8 | wisdom-tooth-surgery | severe-toothache | ارتباط علائم |
  | 9 | all-on-4 | full-mouth-implant | خدمات مشابه |
  | 10 | broken-tooth | dental-crown | راه‌حل ترمیمی |

## فاز ۶ — محتوا و Import (WXR) ✅ کامل

- [x] ۱۰ ترم `service_category` با توضیحات فارسی سئوشده
- [x] ۵۹ پست `service` با محتوای واقعی فارسی (بدون Lorem Ipsum):

  | دسته | Slug | تعداد زیرخدمت |
  |---|---|---|
  | دندانپزشکی عمومی | general-dentistry | ۸ |
  | دندانپزشکی زیبایی | cosmetic-dentistry | ۸ |
  | ایمپلنت دندان | dental-implant | ۸ |
  | ارتودنسی | orthodontics | ۶ |
  | جراحی دهان و فک | oral-surgery | ۵ |
  | درمان ریشه | endodontics | ۴ |
  | لثه‌درمانی | periodontics | ۵ |
  | دندانپزشکی کودکان | pediatric-dentistry | ۵ |
  | پروتز و ترمیمی | prosthodontics | ۶ |
  | اورژانس دندانپزشکی | dental-emergency | ۴ |

- [x] صفحات ثابت: خانه، درباره ما، دکتر علی‌پسندی (CPT doctor)، گالری، بلاگ، FAQ، تعرفه‌ها، تماس، رزرو نوبت، حریم خصوصی، قوانین
- [x] ۱۰ مقاله کامل بلاگ سئوشده (مراقبت بعد از ایمپلنت، لمینت vs کامپوزیت و...)
- [x] ۶+ نظر بیمار (testimonial) با امتیاز
- [x] منوی «main-menu» کامل (والد/فرزند + دکمه CTA + همه آیتم‌ها) داخل WXR
- [x] متادیتای ACF/متاباکس (قیمت، FAQ، مراحل، مزایا، خدمات مرتبط) داخل WXR
- [x] `customizer-settings.json`
- [x] اعتبارسنجی XML (well-formed + سازگار با WordPress Importer)

## فاز ۷ — المنتور ✅ کامل

- [x] `add_theme_support('elementor')` + لوکیشن‌های Theme Builder (header/footer/single/archive)
- [x] Z-Index هدر (9999) بالاتر از محتوا — زیرمنوی افتاده به پایین
- [x] ثبت CPT ها با `show_in_rest` برای Elementor Dynamic Tags
- [x] فرم رزرو اختصاصی + قابلیت جایگزینی با Elementor Pro Form / Popup

## فاز ۸ — تحویل نهایی (چک‌لیست کارفرما) 🔄 در جریان

- [ ] نصب قالب روی هاست و فعال‌سازی
- [ ] Import فایل WXR از Tools > Import
- [ ] ذخیره Permalink (`/%category%/%postname%/`)
- [ ] اختصاص منو به جایگاه Primary
- [ ] تکمیل Customizer (آدرس دقیق، مختصات نقشه، شبکه‌های اجتماعی، GA4 ID، Clarity ID)
- [ ] جایگزینی تصاویر واقعی کلینیک (WebP + Alt Text — بدون IMG_001.jpg)
- [ ] تست Rich Results (MedicalProcedure/FAQ/LocalBusiness/Breadcrumb/BlogPosting/WebSite)
- [ ] تست PageSpeed Insights (LCP < 2.5s، CLS < 0.1، INP < 200ms)
- [ ] تست Click-to-Call روی موبایل (نوار اورژانس + همه CTA ها)
- [ ] تست ریسپانسیو در ۳۶۰ / ۷۶۸ / ۱۰۲۴ / ۱۴۴۰
- [ ] تست کیبورد‌ناوبری (Tab, Enter, Escape روی همه کنترل‌ها)
- [ ] تست با screen reader (NVDA یا VoiceOver) روی هدر، فرم‌ها، accordion
- [ ] تست فرم تماس: honeypot، rate limit، ذخیره در CPT، ارسال ایمیل
- [ ] تست امتیازدهی بیمار در testimonial-card.php
- [ ] تأیید Schema ها در Google Rich Results Test

## نگاتیوها (ممنوعیت‌های رعایت‌شده)

- ✅ هیچ CDN خارجی (فونت/آیکون) استفاده نشده
- ✅ هیچ Slug فارسی یا فاصله‌دار وجود ندارد
- ✅ هیچ Lorem Ipsum در محتوا نیست
- ✅ محتوا یونیک و تولیدی است (کپی از رقبا نیست)
- ✅ RTL کامل بدون میکس LTR
- ✅ همه تصاویر Alt Text توصیفی دارند
- ✅ انکرتکست‌ها طبیعی و کلیدواژه‌دار هستند
- ✅ هر ۱۰ دسته + همه زیرمجموعه‌ها موجودند
- ✅ Mobile-first ریسپانسیو
- ✅ بدون پلاگین سنگین اجباری
- ✅ H1 یکتا در هر صفحه
- ✅ CTA + شماره تماس در همه صفحات


## رفع باگ‌های v2.0.0 (کامل‌شده)

| باگ | فایل | وضعیت |
|-----|------|--------|
| BUG-001: فرم‌های AJAX به URL صفحه POST می‌کردند | `assets/js/main.js` | ✅ رفع |
| BUG-002: تابع preload تکراری (تداخل) | `inc/performance.php` | ✅ رفع |
| BUG-003: صفحه گالری، CPT service را نمایش می‌داد | `page-templates/gallery.php` | ✅ رفع |
| BUG-004: مسیر فونت ایرانسل وجود نداشت | `inc/enqueue.php` | ✅ رفع |
| BUG-006: footer widget areas هیچ‌گاه رندر نمی‌شدند | `footer.php` | ✅ رفع |
| BUG-007: امتیاز ستاره در testimonial-card نمایش نمی‌یافت | `template-parts/testimonial-card.php` | ✅ رفع |
| BUG-008: ارسال فرم فقط ایمیل می‌فرستاد، دیتابیس ذخیره نمی‌کرد | `inc/forms.php` | ✅ رفع |

## فاز ۹ — قابلیت‌های پیشرفته (برنامه‌ریزی‌شده)

### ۹.۱ سیستم رزرو پزشکی کامل
- [ ] `inc/booking.php` — کلاس اصلی رزرو با جدول پایگاه داده `wp_fasdent_bookings`
- [ ] `inc/booking-admin.php` — صفحه مدیریت نوبت‌ها (جدول sortable، نمای تقویم)
- [ ] `inc/booking-sync.php` — REST API sync با نرم‌افزار ویندوزی کلینیک (SQL Server)
- [ ] `assets/js/booking.js` — فرم چندمرحله‌ای با انیمیشن slide
- [ ] `page-templates/appointment.php` — بازنویسی کامل: ۴ مرحله، date picker شمسی، سابقه پزشکی، CAPTCHA
- [ ] تقویم شمسی (Jalali) با `persian-datepicker` بدون CDN
- [ ] انتخاب ساعت بر اساس ساعات کاری کلینیک
- [ ] پیامک خودکار (hook آماده برای ارائه‌دهنده SMS)
- [ ] ثبت رویداد GA4 در هر مرحله فرم

### ۹.۲ سیستم نظرسنجی (Poll)
- [ ] `inc/polls.php` — کلاس اصلی با جداول `wp_fasdent_polls` و `wp_fasdent_poll_votes`
- [ ] `inc/polls-ajax.php` — handler رأی‌گیری
- [ ] `template-parts/poll.php` — نمایش نظرسنجی
- [ ] جلوگیری از رأی تکراری (IP hash + fingerprint)
- [ ] صادرات نتایج به CSV / Power BI
- [ ] یکپارچگی با Microsoft Clarity custom events

### ۹.۳ متادیتا و تعامل پست
- [ ] جدول `wp_fasdent_post_views` — شمارش بازدید بدون کوکی
- [ ] جدول `wp_fasdent_post_reactions` — واکنش‌های مفید/ممنون/دقیق
- [ ] نمایش بازدید و واکنش در sidebar پست
- [ ] نشانه «بروزرسانی شده» / «بررسی شده» با تاریخ

### ۹.۴ سایر قابلیت‌ها
- [ ] تبدیل خودکار WebP هنگام آپلود تصویر (`inc/performance.php`)
- [ ] Live AJAX search با debounce و highlight نتایج (`inc/ajax-search.php`)
- [ ] Knowledge Base با taxonomy اختصاصی و فیدبک «مفید بود؟»
- [ ] سیستم رأی‌گیری برای نظرات (comment reactions)

## فاز ۱۰ — بهینه‌سازی پیشرفته (برنامه‌ریزی‌شده)

### ۱۰.۱ سئو پیشرفته
- [ ] Schema `Speakable` روی بخش خلاصه پست‌ها
- [ ] Schema `VideoObject` برای صفحات دارای ویدیو
- [ ] Schema `ImageObject` برای تصاویر کلیدی
- [ ] IndexNow ping هنگام انتشار/بروزرسانی محتوا
- [ ] hreflang برای نسخه انگلیسی (زمانی که محتوا آماده شد)

### ۱۰.۲ Core Web Vitals
- [ ] استخراج Critical CSS و inline در `<head>`
- [ ] Resource Hints: `dns-prefetch` برای Google Analytics / Clarity
- [ ] تصاویر hero با `loading="eager"` و `fetchpriority="high"`
- [ ] تبدیل و ارائه تصاویر WebP با `<picture>` و fallback

### ۱۰.۳ قانونی و انطباق
- [ ] `page-templates/privacy-policy.php` — سیاست حریم خصوصی کامل فارسی
- [ ] صفحه حقوق بیمار (براساس قوانین ایران)
- [ ] صفحه قوانین لغو نوبت
- [ ] صفحه سلب مسئولیت پزشکی
- [ ] Google Consent Mode v2 — block analytics تا تأیید کاربر

## فاز ۱۱ — داشبورد و مدیریت (برنامه‌ریزی‌شده)

- [ ] ویجت «آمار نوبت‌ها» — امروز / هفته / ماه با auto-refresh AJAX
- [ ] ویجت «خدمات پرمخاطب» — از جدول بازدیدها
- [ ] ویجت «نظرات اخیر بیماران» — با ستاره
- [ ] ویجت «خلاصه نظرسنجی‌ها»
- [ ] ویجت «سلامت سئو» — بررسی Schema، Canonical، SSL
- [ ] ویجت «دسترسی سریع» — لینک‌های مستقیم به عملیات رایج
- [ ] صفحه تنظیمات `inc/admin.php` — GA4 ID، Clarity، API Keys، SMS config
- [ ] Export نوبت‌ها به Excel/PDF


</div>
