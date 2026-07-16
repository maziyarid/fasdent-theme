# پلن و چک‌لیست کامل پروژه قالب فس‌دنت

> **نسخه:** 2.1.2 | **تاریخ آخرین بروزرسانی:** 1404-04-25 | **وضعیت:** فازهای ۱–۱۱ کامل ✅ | فاز ۸ (نصب) در انتظار اقدام کارفرما

<div dir="rtl">

## فاز ۱ — زیرساخت قالب ✅ کامل

- [x] ساختار پوشه استاندارد وردپرس (`fasdent-theme/`)
- [x] `style.css` با هدر قالب (Theme Name, Text Domain: fasdent, RTL) — v2.1.2
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
  - [x] باکس «خدمات محبوب» → ۵ خدمت پرطرفدار
  - [x] معرفی دکتر علی‌پسندی + لینک بیوگرافی
  - [x] نظرات بیماران (اسلایدر)
  - [x] گالری قبل/بعد
  - [x] آخرین مقالات بلاگ
  - [x] نقشه گوگل + فرم رزرو سریع
- [x] `archive-service.php`: گرید ۱۰ Pillar + معرفی
- [x] `taxonomy-service_category.php` — **قالب A (Pillar Page)**
- [x] `single-service.php` — **قالب B + C (اورژانس)**
- [x] `single-doctor.php`: بیوگرافی + Physician Schema
- [x] صفحات قالب‌دار: درباره ما، گالری، FAQ، تعرفه‌ها، تماس، رزرو نوبت، 404، صفحات قانونی
- [x] `template-parts/` کامل

## فاز ۴ — سئو تکنیکال ✅ کامل

- [x] H1 یکتای هر صفحه + سلسله‌مراتب H2>H3
- [x] Meta Title < 60 و Meta Description < 155 کاراکتر
- [x] Canonical URL روی همه صفحات
- [x] Open Graph + Twitter Card
- [x] Schema های JSON-LD (۱۲ نوع)
- [x] robots.txt استاندارد + سازگاری Sitemap
- [x] آماده‌سازی ساختار چندزبانگی (hreflang hook — فعلاً فقط fa-IR)

## فاز ۵ — لینک‌سازی داخلی ✅ کامل

- [x] نقشه لینک‌سازی داخلی کامل (خانه → خدمات → Pillar → زیرخدمت + Cross-Cluster)

## فاز ۶ — محتوا و Import (WXR) ✅ کامل

- [x] ۱۰ ترم `service_category` + ۵۹ پست `service` با محتوای واقعی فارسی
- [x] صفحات ثابت + مقالات بلاگ + testimonials + منوها داخل WXR

## فاز ۷ — المنتور ✅ کامل

- [x] پشتیبانی Theme Builder + Dynamic Tags

## فاز ۸ — تحویل نهایی (چک‌لیست کارفرما) 🔄 در جریان

- [ ] نصب قالب روی هاست و فعال‌سازی
- [ ] Import فایل WXR از Tools > Import
- [ ] ذخیره Permalink
- [ ] اختصاص منو به جایگاه Primary
- [ ] تکمیل Customizer
- [ ] جایگزینی تصاویر واقعی کلینیک
- [ ] تست Rich Results / PageSpeed / Accessibility / Forms

## فاز ۹ — قابلیت‌های پیشرفته ✅ کامل (پیاده‌سازی‌شده در v2.0–2.1)

### ۹.۱ سیستم رزرو پزشکی
- [x] `inc/booking.php` — جدول `wp_fasdent_bookings` + AJAX handler
- [x] `inc/admin-bookings.php` — صفحه مدیریت نوبت‌ها با تغییر وضعیت AJAX
- [x] `page-templates/appointment.php` — فرم ۴ مرحله‌ای

### ۹.۲ سیستم نظرسنجی (Poll)
- [x] `inc/polls.php` — جداول + shortcode + AJAX vote
- [x] `template-parts/poll.php`

### ۹.۳ متادیتا و تعامل پست
- [x] شمارش بازدید (`_view_count`) — اصلاح‌شده در v2.1.2
- [x] واکنش‌های مفید/ممنون/دقیق (`fasdent_post_reaction`)
- [x] زمان مطالعه فارسی‌محور (v2.1.2)

### ۹.۴ سایر قابلیت‌ها
- [x] تبدیل خودکار WebP هنگام آپلود
- [x] Live AJAX search
- [x] صفحات قانونی کامل (privacy, patient-rights, cancellation, medical-disclaimer)

## فاز ۱۰ — بهینه‌سازی پیشرفته ✅ کامل

- [x] Schema Speakable / ImageObject / Organization / IndexNow
- [x] Core Web Vitals (fetchpriority, lazy-load, DNS prefetch, emoji removal)
- [x] Google Consent Mode v2
- [x] Permissions-Policy header

## فاز ۱۱ — داشبورد و مدیریت ✅ کامل

- [x] ویجت‌های آمار نوبت، خدمات پرمخاطب، نظرات، دسترسی سریع، وضعیت سئو، خلاصه سایت
- [x] `inc/admin-bookings.php` — مدیریت کامل نوبت‌ها

## رفع باگ‌های مهم (آخرین)

| نسخه | مورد |
|------|------|
| v2.1.2 | حذف BOM از فایل‌های PHP، اصلاح شمارنده بازدید، بهبود زمان مطالعه فارسی |
| v2.1.1 | اصلاح reaction AJAX + enqueue single-post assets |
| v2.0.0 | BUG-001 تا BUG-008 |

</div>
