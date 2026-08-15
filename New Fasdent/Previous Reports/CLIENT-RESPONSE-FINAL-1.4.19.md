# پاسخ نهایی — Service Content Migration انجام‌شده (۱.۴.۱۹)

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**نسخه تم (Production Candidate Build):** **۱.۴.۱۹**  
**وضعیت Design:** Locked  
**وضعیت Production Approval:** هنوز صادر نشده — منتظر Staging + Evidence QA

---

## تصمیم اجراشده: گزینه الف

Service Content Migration **قبل از Production** با روش تأییدشده انجام شد.

| اصل | پیاده‌سازی |
|---|---|
| Registered Post Meta | `_alipasandi_service` روی `page` |
| Native WordPress | بدون Page Builder · بدون Plugin اختصاصی اجباری |
| Meta Box | فقط روی چهار صفحه Implant / Crown / Surgery / General |
| Fallback | `inc/service-data.php` فقط خواندنی |
| Populate اولیه | Idempotent · اگر Meta پر باشد overwrite نمی‌شود |
| Priority | **Post Meta → Legacy file** (هرگز برعکس) |

### تضمین‌های رعایت‌شده

- URL / Slug بدون تغییر  
- Layout / CSS Classes / Design / Hero / Typography / Visual hierarchy بدون تغییر  
- Component structure بدون تغییر  
- **یک H1 معنایی واحد** — `title` + `title_gold` فقط Presentation (`<span class="gold-text">`)  
- SEO metadata دست نخورده (Owner = Plugin)  
- Internal links در محتوا حفظ می‌شوند؛ افزودن `<a href>` از Meta Box ممکن است  
- FAQ از Admin قابل ویرایش (Question \|\| Answer) · Accordion همچنان HTML + ARIA  

### جزئیات فنی Meta

| مورد | مقدار |
|---|---|
| Meta key | `_alipasandi_service` |
| type | object (array) · single |
| sanitize_callback | `alipasandi_sanitize_service_meta` |
| auth_callback | `current_user_can( 'edit_pages' )` |
| Migration flag | option `alipasandi_service_meta_migrated_v1` |
| Storage | `wp_postmeta` — در Backup دیتابیس استاندارد |

**Idempotent:** اجرای مجدد Migration یا Update Theme، Meta ویرایش‌شده توسط ادمین را reset/overwrite نمی‌کند.

**Rollback:** خالی کردن Meta صفحه → بازگشت خودکار به legacy file.

---

## Diff محتوا (منطقی)

محتوای چهار صفحه از همان آرایه legacy به Meta کپی می‌شود؛ Template همان `alipasandi_get_service()` را صدا می‌زند.  
روی Staging پس از فعال‌سازی Theme، Migration یک‌بار اجرا می‌شود.  
**لطفاً قبل/بعد View-Source یا Screenshot همان بخش‌ها (H1 · Intro · FAQ · CTA) را برای هر چهار صفحه مقایسه کنید.**

فایل‌های کلیدی:

- `inc/service-meta.php` — register · sanitize · meta box · save · migrate · getter  
- `inc/service-data.php` — فقط `alipasandi_get_service_legacy()` (read-only)  
- `template-parts/service-detail.php` — بدون تغییر Design  

---

## پاسخ به نکات Live vs Staging (۱۰–۱۶)

| نکته | موضع |
|---|---|
| Live فعلی مرجع QA نیست | تأیید — Staging نسخه ۱.۴.۱۹ مرجع است |
| Footer/Hero/Contact تهران‌محور در Live | نسخه نهایی Deployنشده؛ Staging باید فقط نوشهر باشد |
| Navigation قدیمی در Live | Navigation تم از `alipasandi_primary_links()` است (Home · About · Services · Contact)؛ Blog در منوی اصلی نیست |
| عدد +۱۰٬۰۰۰ | فقط با Customizer `show_treatment_count` — پیش‌فرض خاموش تا تأیید Verified Claim |
| شماره ۰۹۲۰ ۱۴۴ ۱۴۶۹ | پیش‌فرض فعلی در Option؛ **قبل از Production تأیید نهایی شماره رسمی نوشهر از شما لازم است** |
| Medical Copy Review | لیست Claimهای مهم را از Content استخراج و برای تأیید پزشک ارسال می‌کنیم (مرحله Staging) |

**NAP Master قبل از Production:** پس از تأیید شما روی Phone / Hours / Postal / Geo، در Options Freeze می‌شود و Consistency Check روی Staging انجام می‌شود.

---

## SEO Plugin — پیشنهاد نهایی

برای این پروژه پیشنهاد ما:

**Rank Math (یک Plugin فقط)**

| معیار | دلیل |
|---|---|
| Local SEO نوشهر | کنترل UI قوی‌تر برای Local / NAP |
| Schema | انعطاف برای Dentist / Service / Article |
| Sitemap · Canonical · Breadcrumb | کامل و قابل تنظیم |
| Redirect | ماژول Redirect داخلی (می‌تواند Redirect Owner باشد) |
| Service + Blog | سازگار با ساختار فعلی |
| Performance / Maintainability | یک Plugin به‌جای چند لایه |

**جایگزین قابل قبول:** Yoast — اگر تیم با آن آشناتر است و Local را با تنظیمات دستی می‌پذیرد.

پس از انتخاب روی Staging: View-Source هفت صفحه نمونه برای یکتایی Title/Meta/Canonical/OG/Schema.

Theme SEO Fallback وقتی Plugin فعال است خاموش است (`alipasandi_seo_plugin_active()`).

---

## Indexability · Appointment Noindex · Canonical Host · Redirect Owner

- Matrix در `docs/INDEXABILITY-MATRIX-FA.md` است؛ **Evidence اعمال واقعی روی Staging لازم است.**  
- **Appointment Noindex:** چون صفحه تراکنشی/فرم کم‌محتواست و Intent آن تبدیل است نه جذب organic — پیشنهاد Noindex آگاهانه است. در صورت تمایل به Index، Intent و محتوا باید تقویت شود.  
- **Canonical Host:** قبل از Production باید دقیقاً اعلام شود (`www` یا non-www) — از سمت شما/هاست.  
- **Redirect Owner برای این پروژه:** پیشنهاد **Rank Math Redirects** (یا ماژول معادل) به‌عنوان Owner عملیاتی؛ Theme فقط Helper. اگر Server-level ترجیح شماست، در Staging اعلام کنید تا همان قفل شود.

---

## موارد باقی‌مانده برای Staging Evidence (خلاصه)

همه موارد Evidence محور شما (Schema View-Source، Form+Mail screenshot، LCP/INP/CLS، Network images، A11y، Browser QA، PHP 8.2 Error Log، Plugin Inventory، Third-party list، SPF/DKIM/DMARC، Cache purge checklist، Deployment plan، Rollback triggers، Post-launch monitoring) **پس از Deploy استیج ۱.۴.۱۹** باید ارسال شوند.

Maintenance Guide به‌روز شده شامل: ویرایش Service Content · نام Optionهای NAP · Author/Reviewer data model · Priority منبع داده · Backup/Update.

---

## Production Approval

| وضعیت | |
|---|---|
| Design | **Locked** |
| معماری ۱.۴.۱۸/۱۹ | **تأیید** |
| Service Content Migration | **انجام‌شده در ۱.۴.۱۹** |
| Production Approval | **صادر نشده** |

**مرحله بعد:**  
Deploy **۱.۴.۱۹** روی Staging → Evidence-based QA (لیست قبلی شما) → در صورت Pass، همان Build به‌عنوان Production Candidate.

---

## فایل تحویل

| فایل | توضیح |
|---|---|
| `alipasandi-clinic-1.4.19.zip` | Build شامل Migration + Meta Box + Fallback |
| این سند | تأیید شرایط Migration و مسیر Staging |

---

**جمع‌بندی:**  
گزینه الف اجرا شد. Content Layer چهار Service به Post Meta منتقل‌پذیر و قابل ویرایش از Admin است، بدون تغییر URL/Design/H1 معنایی.  
Staging باید روی **۱.۴.۱۹** بالا بیاید و Evidence QA ارسال شود. تا آن زمان Design Locked / Production Not Approved.
