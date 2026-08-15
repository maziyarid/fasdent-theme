# پاسخ نهایی به نکات معماری قبل از Staging Freeze

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**نسخه تم روی مسیر تحویل:** ۱.۴.۱۶  
**مبنای UI:** بدون Redesign · بدون تغییر Color Token · Layout · Hero Composition · Grid · Container · Typography Scale

---

## وضعیت کلی

مسیر قفل‌شدهٔ قبلی مورد تأیید است و حفظ می‌شود:

| موضوع | وضعیت |
|---|---|
| Design Lock | قفل |
| Operational Location | فقط نوشهر |
| Booking | فقط نوشهر |
| Schema | فقط نوشهر |
| SEO Plugin | Owner اصلی |
| NAP یکپارچه | SSOT |
| Designer Credit | کاملاً Optional |
| postalCode / Geo | فقط داده واقعی |
| URL Architecture / Migration Plan | ثبت‌شده |
| Blog + Service Pages قابل توسعه | ثبت‌شده |
| CWV / A11y / Backup / Redirect QA | Production Gate |

از این مرحله هیچ Redesign یا تغییر UI جدیدی انجام نمی‌شود.

---

## به‌روزرسانی‌های فنی ۱.۴.۱۶ (بدون Redesign)

| موضوع | اقدام |
|---|---|
| **Storage NAP** | انتقال از `theme_mod` به **WordPress Options** (site-level). داده‌ها با تعویض Theme از بین نمی‌روند. UI همچنان Customizer. |
| **Migration یک‌باره** | هر مقدار قبلی theme_mod در صورت خالی بودن option کپی می‌شود. |
| **HTML Email** | حذف کامل jsDelivr / CDN فونت. فقط `Tahoma, Arial, sans-serif`. Frontend همچنان Local Font. |
| Design / Token / Layout | بدون هیچ تغییری |

---

## ۱) Storage نوع NAP و اطلاعات عملیاتی

**وضعیت قبلی (۱.۴.۱۵ و قبل):**  
تنظیمات از طریق Customizer با `get_theme_mod` خوانده می‌شدند → وابسته به Theme فعال بودند.

**وضعیت از ۱.۴.۱۶:**

| کلید منطقی | Option واقعی در دیتابیس | نوع Storage |
|---|---|---|
| clinic_phone | `alipasandi_clinic_phone` | **option** (site-level) |
| clinic_street | `alipasandi_clinic_street` | **option** |
| clinic_city | `alipasandi_clinic_city` | **option** |
| clinic_address | `alipasandi_clinic_address` | **option** |
| clinic_maps | `alipasandi_clinic_maps` | **option** |
| clinic_notify_email | `alipasandi_clinic_notify_email` | **option** |
| clinic_booking_times | `alipasandi_clinic_booking_times` | **option** |
| clinic_postal_code | `alipasandi_clinic_postal_code` | **option** |
| clinic_geo_lat | `alipasandi_clinic_geo_lat` | **option** |
| clinic_geo_lng | `alipasandi_clinic_geo_lng` | **option** |
| clinic_address_legacy | `alipasandi_clinic_address_legacy` | **option** |
| clinic_website / social | `alipasandi_clinic_*` | **option** |
| designer_credit | `alipasandi_designer_credit` | **option** |
| show_treatment_count | `alipasandi_show_treatment_count` | **option** |

- UI همچنان داخل **Appearance → Customize → اطلاعات کلینیک** باقی مانده است.
- Storage مستقل از Theme است؛ با تعویض Theme این مقادیر حفظ می‌شوند.
- یک Migration یک‌باره (`alipasandi_migrate_theme_mods_to_options`) هر مقدار قبلی theme_mod را در صورت خالی بودن option کپی می‌کند.
- در Documentation نهایی نوع Storage هر فیلد همین جدول خواهد بود.

---

## ۲) Redirectها مستقل از Theme

**وضعیت فعلی Theme:**  
فقط Extension Point وجود دارد:

```php
apply_filters( 'alipasandi_redirect_map', array() );
```

هیچ Redirect تولیدی/Migration داخل Theme hard-code یا ذخیره نشده است.

**تصمیم معماری (مستند):**

| لایه | نقش |
|---|---|
| **Production Redirect Owner** | SEO Plugin نهایی (Rank Math / Yoast Redirects) **یا** Server configuration (Nginx/Apache) **یا** Plugin اختصاصی Redirect مستقل از Theme |
| `alipasandi_redirect_map` | فقط Helper / Fallback موقت در صورت نیاز به تست سریع — **نه** محل ذخیره Redirectهای حیاتی SEO |

اگر Theme در آینده عوض شود، Redirectهای ۳۰۱ باید همچنان از لایهٔ مستقل فعال بمانند.  
فایل نهایی `Old URL → Final URL → Status` قبل از Go-live تحویل می‌شود و در همان لایهٔ مستقل پیاده‌سازی خواهد شد.

---

## ۳) URL Migration

تأیید کامل الزامات شما:

- فایل نهایی `Old URL → Final URL → Status` قبل از Go-live تحویل می‌شود.
- Redirectها مستقیم ۳۰۱ (بدون Chain).
- URL حذف‌شده بی‌ربط به Homepage Redirect نمی‌شود.
- URL دارای جایگزین واقعی → نزدیک‌ترین محتوای معادل.
- صفحه بدون جایگزین واقعی → HTTP 404 / 410 صحیح.
- پس از Migration یک Crawl کامل اجرا و گزارش کوتاه تحویل می‌شود.

---

## ۴) GA / Microsoft Clarity — Ownership

**وضعیت فعلی:**

| مورد | وضعیت |
|---|---|
| اسکریپت‌ها داخل فایل‌های Theme | **خیر** — هیچ `gtag` / Clarity / Tracking ID داخل Theme hard-code نشده |
| محل Inject فعلی | خارج از Theme (احتمالاً Plugin، Header/Footer injection توسط ادمین، یا روش دیگر سایت) |
| Analytics Owner پیشنهادی | **Site-level / Plugin یا GTM** — نه Theme |

ترجیح شما کاملاً پذیرفته است: Tracking IDها و Scripts نباید داخل Theme باشند.  
اگر Theme عوض شود، Analytics نباید قطع یا Duplicate شود.  
Ownership این بخش در Documentation نهایی به‌صورت **Site / Plugin / GTM (خارج از Theme)** ثبت می‌شود.

دسترسی فعلی به `drkeyvanalipasandi@gmail.com` داده شده و پس از دامنهٔ جدید propertyها منتقل می‌شوند.

---

## ۵) Consent / Privacy Architecture

ثبت شد. قبل از Production بررسی می‌شود:

- آیا ترافیک مشمول الزام Consent است؟
- در صورت نیاز: معماری سبک با پشتیبانی Google Consent Mode
- Tagها بر اساس Consent رفتار کنند
- بدون نصب اسکریپت سنگین غیرضروری
- حفظ Performance

این مورد می‌تواند مرحله‌ای باشد ولی در Documentation Production مشخص خواهد شد.

---

## ۶) External Font در HTML Email

**اعمال‌شده در ۱.۴.۱۶:**

- لینک `cdn.jsdelivr.net` / Vazirmatn از قالب ایمیل **کاملاً حذف** شد.
- فونت ایمیل: `Tahoma, Arial, sans-serif` فقط.
- ظاهر ایمیل ساده‌تر است؛ Reliability اولویت دارد.
- Frontend سایت همچنان کاملاً Local Font (Playfair + Vazirmatn WOFF2) باقی می‌ماند.

---

## ۷) Content و PHP Template

**وضعیت صادقانه فعلی:**

| بخش | وضعیت |
|---|---|
| صفحات استاندارد WordPress (About، Contact، FAQ، Blog/Posts، …) | محتوای اصلی از Editor وردپرس قابل ویرایش است |
| چهار Service Landing (Implant / Crown / Surgery / General) | محتوای ساختاریافته (عنوان، Intro، سکشن‌ها، FAQ، …) فعلاً از `inc/service-data.php` خوانده می‌شود تا Layout و Component System کنترل‌شده و یکنواخت بماند |
| Template | Layout / Component را کنترل می‌کند |

**پیامد:**  
تغییر متن‌های اصلی Service Pages در حال حاضر نیاز به ویرایش فایل داده (یا توسعه‌دهنده) دارد و ۱۰۰٪ از Editor معمولی وردپرس قابل افزایش نیست.

**مسیر آینده (بدون Redesign الان):**  
برای جلوگیری از Lock-in محتوایی، در فاز بعدی (پس از Production یا در یک Sprint جدا) می‌توان بخش‌های محتوایی Service را به‌تدریج به:

- محتوای صفحه (post_content) + بلوک‌های کنترل‌شده، یا  
- Post Meta / فیلدهای سبک  

منتقل کرد تا بدون تغییر Source Code قابل ویرایش شوند، در حالی که Layout همچنان از Component System تم پیروی کند.

این محدودیت فعلی و مسیر آینده در Documentation نهایی شفاف ثبت می‌شود.  
الان هیچ Redesign یا بازسازی قالب برای این مورد انجام نمی‌شود تا Design Lock نقض نشود.

---

## فایل‌های تحویل این مرحله

- تم **۱.۴.۱۶** (NAP → Options + Email بدون CDN)
- این سند معماری

---

## مرحله بعد — Staging واقعی

لطفاً موارد زیر را ارسال کنید تا QA واقعی و تصمیم Production Approval انجام شود:

1. URL واقعی Staging  
2. WordPress Version  
3. PHP Version  
4. Plugin List  
5. SEO Plugin نهایی  
6. نوع Storage تنظیمات NAP → **اکنون option (مستند بالا)**  
7. Redirect Owner → **SEO Plugin / Server / مستقل از Theme (مستند بالا)**  
8. Analytics Owner → **خارج از Theme (مستند بالا)**  
9. نتیجه Form Test  
10. Screenshot ایمیل واقعی  
11. Schema Source (View-Source)  
12. Duplicate SEO Check  
13. ۳۹۰px Mobile Footer  
14. Responsive QA  
15. LCP / CLS / INP  
16. Error / Debug Log  
17. URL Inventory / Redirect Draft  
18. External Request List  
19. Maintenance Guide Draft  

تا زمان بسته‌شدن این موارد و تأیید شما، **Production Freeze صادر نشده** است.

---

**جمع‌بندی:**  
نسخه ۱.۴.۱۶ دو نقطهٔ معماری مهم (Storage مستقل از Theme برای NAP و حذف CDN ایمیل) را بدون هیچ Redesign برطرف کرده است.  
Ownership Redirect و Analytics و محدودیت فعلی محتوای Service Pages شفاف مستند شد.  
مسیر Design Lock و Business Rule نوشهر بدون تغییر باقی است.

آمادهٔ دریافت Staging URL و شروع QA واقعی هستیم.
