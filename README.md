# قالب وردپرس فس‌دنت (Fasdent Theme)

قالب اختصاصی، کامل و آماده‌ی نصب وردپرس برای **کلینیک دندانپزشکی دکتر کیوان علی‌پسندی** — [fasdent.ir](https://fasdent.ir/)

<div dir="rtl">

## 📌 معرفی پروژه

- **نام قالب:** `fasdent-theme`
- **نسخه:** 1.0.0
- **سازگاری:** WordPress 6.5+ / 7.0 — PHP 8.2+
- **زبان:** فارسی (fa-IR) — راست‌چین کامل (RTL)
- **فونت:** وزیرمتن (Vazirmatn) — کاملاً لوکال (woff2)
- **آیکون:** Font Awesome — کاملاً لوکال (بدون CDN)
- **سازگار با:** المنتور / المنتور پرو (Theme Builder, Nav Menu, Popup Builder)
- **شماره تماس کلینیک:** `09201441469` (Click-to-Call در همه صفحات)

## 📂 ساختار مخزن

```
├── fasdent-theme/                 ← پوشه قالب (برای نصب، همین پوشه را ZIP کنید)
│   ├── style.css                  ← هدر قالب + استایل پایه
│   ├── functions.php              ← بوت‌استرپ قالب
│   ├── header.php / footer.php
│   ├── index.php / front-page.php / page.php / single.php / 404.php / search.php
│   ├── single-service.php         ← قالب صفحه تک‌خدمت (قالب B و C)
│   ├── archive-service.php        ← آرشیو کل خدمات (گرید ۱۰ دسته)
│   ├── taxonomy-service_category.php  ← قالب Pillar Page (قالب A)
│   ├── single-doctor.php
│   ├── page-templates/            ← قالب‌های اختصاصی صفحات (رزرو نوبت، تماس، تعرفه‌ها و ...)
│   ├── template-parts/            ← بخش‌های تکرارشونده (کارت خدمت، FAQ، CTA، Breadcrumb و ...)
│   ├── inc/                       ← ماژول‌های PHP
│   │   ├── setup.php              ← تنظیمات پایه، منوها، پشتیبانی‌ها
│   │   ├── enqueue.php            ← فراخوانی CSS/JS/فونت لوکال
│   │   ├── post-types.php         ← CPT های service, doctor, testimonial, faq
│   │   ├── taxonomies.php         ← service_category (هرارشیک)
│   │   ├── acf-fields.php         ← فیلدهای ACF + متاباکس فال‌بک بدون ACF
│   │   ├── customizer.php         ← تنظیمات قالب (تلفن، آدرس، ساعات کاری و ...)
│   │   ├── seo.php                ← Meta Title/Description, Canonical, OG, Twitter
│   │   ├── schema.php             ← Dentist, MedicalProcedure, FAQPage, BreadcrumbList, Physician, Review
│   │   ├── security.php           ← حذف نسخه WP، هاردنینگ، Sanitize helpers
│   │   ├── performance.php        ← defer JS، lazy load، preload فونت
│   │   ├── breadcrumb.php         ← تولید Breadcrumb + Schema
│   │   ├── forms.php              ← هندلر فرم تماس و رزرو نوبت (Nonce + Sanitize + ایمیل)
│   │   └── elementor.php          ← سازگاری و لوکیشن‌های Theme Builder المنتور
│   └── assets/
│       ├── css/main.css           ← استایل اصلی RTL ریسپانسیو (minified: main.min.css)
│       ├── js/main.js             ← منو، آکاردئون FAQ، لایت‌باکس، فرم چندمرحله‌ای
│       ├── images/                ← لوگو و تصاویر پایه
│       └── fonts/
│           ├── vazirmatn/         ← فونت وزیرمتن woff2 + css
│           └── fontawesome/       ← Font Awesome لوکال (css/all.min.css + webfonts)
├── import/
│   ├── wordpress-import.xml       ← فایل WXR شامل تمام صفحات/خدمات/بلاگ/منو/دسته‌ها
│   └── customizer-settings.json   ← تنظیمات ظاهری قالب برای بازگردانی
├── tools/
│   └── generate_wxr.py            ← اسکریپت تولید فایل WXR (برای توسعه‌دهنده)
├── PLAN.md                        ← پلن و چک‌لیست کامل تحویل پروژه
└── README.md
```

## 🚀 راهنمای نصب گام‌به‌گام

### گام ۱ — نصب قالب
1. پوشه `fasdent-theme/` را ZIP کنید (یا فایل ZIP آماده در Releases).
2. در پیشخوان وردپرس: **نمایش ← پوسته‌ها ← افزودن ← بارگذاری پوسته** → فایل ZIP را آپلود و **فعال** کنید.
3. زبان سایت را در **تنظیمات ← عمومی** روی «فارسی» بگذارید (RTL خودکار فعال می‌شود).

### گام ۲ — نصب افزونه‌های پیشنهادی
| افزونه | ضرورت | توضیح |
|---|---|---|
| Advanced Custom Fields (ACF) | پیشنهادی | فیلدهای قیمت/مراحل/FAQ/گالری خدمات (قالب بدون ACF هم با متاباکس داخلی کار می‌کند) |
| Elementor + Elementor Pro | اختیاری | ویرایش بصری، Theme Builder، Popup فرم رزرو |
| Yoast SEO یا Rank Math | پیشنهادی | Sitemap XML خودکار (قالب Canonical/OG/Schema داخلی دارد) |

### گام ۳ — Import محتوا
1. **ابزارها ← درون‌ریزی ← WordPress** → افزونه Importer را نصب و اجرا کنید.
2. فایل `import/wordpress-import.xml` را انتخاب کنید.
3. گزینه‌ی «Download and import file attachments» را فعال کنید و Import را بزنید.
4. بعد از Import: تمام ۱۰ دسته خدمات + ۵۹ زیرخدمت + صفحات ثابت + ۱۰ مقاله بلاگ + نظرات بیماران + منوی اصلی وارد می‌شوند.

### گام ۴ — تنظیم Permalink
- **تنظیمات ← پیوندهای یکتا** → گزینه «ساختار سفارشی» → مقدار: `/%category%/%postname%/` → ذخیره.
- (قالب به‌صورت خودکار rewrite خدمات را روی `/services/{category}/{service}/` تنظیم می‌کند — فقط یک‌بار Save Permalinks بزنید.)

### گام ۵ — تنظیم منو
- **نمایش ← فهرست‌ها** → منوی «منوی اصلی» (main-menu) که Import شده را به جایگاه **Primary Menu** اختصاص دهید.
- دکمه «رزرو نوبت آنلاین» به‌صورت خودکار استایل CTA می‌گیرد (کلاس `menu-cta`).

### گام ۶ — تنظیمات قالب (Customizer)
- **نمایش ← سفارشی‌سازی ← تنظیمات کلینیک فس‌دنت** → شماره تماس، آدرس، ساعات کاری، شبکه‌های اجتماعی، مختصات نقشه.
- برای بازگردانی تنظیمات آماده: افزونه Customizer Export/Import → فایل `import/customizer-settings.json` را Import کنید.

### گام ۷ — تنظیم ACF Fields
- فیلدهای ACF به‌صورت **PHP-registered** داخل قالب هستند و نیازی به Import جداگانه ندارند.
- در صورت فعال بودن ACF، هنگام ویرایش هر «خدمت»: قیمت پایه، مدت درمان، مراحل، مزایا، FAQ، گالری قبل/بعد و خدمات مرتبط قابل ویرایش‌اند.

### گام ۸ — تست Schema و سرعت
- هر صفحه خدمت را در [Google Rich Results Test](https://search.google.com/test/rich-results) تست کنید (MedicalProcedure + FAQPage + BreadcrumbList).
- صفحه اصلی را در [PageSpeed Insights](https://pagespeed.web.dev/) تست کنید (هدف: Core Web Vitals سبز، بارگذاری < ۳ ثانیه).

## 🗂 معماری محتوا

- **CPT `service`** — ۵۹ خدمت در ۱۰ دسته (Taxonomy هرارشیک `service_category`) با URL: `/services/{category}/{service}/`
- **CPT `doctor`** — صفحه دکتر کیوان علی‌پسندی با Schema نوع Physician
- **CPT `testimonial`** — نظرات بیماران با فیلد امتیاز (Review/AggregateRating Schema)
- **صفحات ثابت:** خانه، درباره ما، گالری، سوالات متداول، تعرفه‌ها، تماس با ما، رزرو نوبت، حریم خصوصی، قوانین
- **بلاگ:** ۱۰ مقاله کامل سئو‌شده فارسی

## 🔗 لینک‌سازی داخلی و Cross-Cluster

نقشه کامل لینک‌سازی (Pillar ↔ Service ↔ Cross-Cluster) در `PLAN.md` مستند شده و در فیلد «خدمات مرتبط» هر خدمت + متن محتوا با انکرتکست طبیعی پیاده‌سازی شده است.

## 🛡 امنیت و سئو

- Sanitize/Escape تمام ورودی‌ها و خروجی‌ها، Nonce در فرم‌ها، حذف نسخه وردپرس
- H1 یکتا، Meta Title < 60 و Description < 155 کاراکتر، Canonical، OG/Twitter Card
- Schema: Dentist (سراسری)، MedicalProcedure، FAQPage، BreadcrumbList، Physician، AggregateRating
- Lazy Load تصاویر، Defer JS، فونت لوکال با `font-display: swap`

## 📞 اطلاعات کلینیک

- **کلینیک دندانپزشکی فس‌دنت** — دکتر کیوان علی‌پسندی
- تلفن: [09201441469](tel:+989201441469)
- وب‌سایت: [https://fasdent.ir/](https://fasdent.ir/)

</div>
