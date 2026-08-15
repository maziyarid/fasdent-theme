# پاسخ نهایی — Design Locked + Production / SEO / Maintainability Requirements

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**نسخه تم روی مسیر تحویل:** ۱.۴.۱۸  
**مبنای UI:** Design System Locked · بدون Redesign · بدون تغییر سلیقه‌ای ظاهر

---

## قفل Design System — تأیید نهایی

موارد زیر بدون درخواست صریح **تغییر نمی‌کنند**:

Color Tokens · Hero Composition · Layout / Grid · Container · Typography Scale · Spacing / Radius / Shadow · Header / Footer · Breakpointها · ساختار چهار Service اصلی

**فقط مجاز:** Bug Fix · SEO Fix · Performance Fix · Accessibility Fix · Content Architecture Fix · Security Fix

هدف: بستر پایدار چندساله برای SEO، محتوای پزشکی، Local SEO نوشهر و Upgradeهای WordPress — نه فقط Launch.

---

## ۱ و ۴۲) Service Content Architecture — اولویت بسیار بالا

### وضعیت فعلی
محتوای چهار Service Page از `inc/service-data.php` خوانده می‌شود (ساختار یافته: H1/Intro، What، Benefits+Icon، Steps، Candidate، Notice، FAQ، CTA، Image/ALT).

### روش پیشنهادی Migration (قبل از اجرا)

| لایه | نقش |
|---|---|
| Template | همچنان Layout · Component · Design System · Visual hierarchy را کنترل می‌کند |
| Storage | **Registered Post Meta** (Native WordPress — بدون Page Builder و بدون Plugin اختصاصی اجباری) |
| UI ویرایش | Meta Box در ویرایشگر صفحه (Page) برای فیلدهای ساختاریافته |
| Fallback | `service-data.php` تا زمان پر شدن Meta باقی می‌ماند → Rollback ساده |
| Populate | یک‌بار از داده‌های فعلی به Meta کپی می‌شود |

**فیلدهای قابل مدیریت پس از Migration:**  
H1 (title + title_gold) · Intro · H2/H3 labels · Paragraphs · Benefits · Steps · FAQ · CTA · Image/ALT · Sections قابل توسعه

**تضمین‌ها:**
- URL تغییر نمی‌کند  
- Design / Class / Hierarchy بصری تغییر نمی‌کند  
- محتوای فعلی حذف نمی‌شود (Diff قبل/بعد)  
- H1 hierarchy حفظ می‌شود  
- SEO metadata تحت تأثیر قرار نمی‌گیرد (Owner = Plugin)  
- Rollback: خالی کردن Meta → بازگشت خودکار به service-data.php  

### ریسک و Estimate

| مورد | ارزیابی |
|---|---|
| ریسک Design/URL | **کم** (Template و CSS دست نخورده؛ فقط منبع داده عوض می‌شود) |
| ریسک از دست رفتن محتوا | **کم** با Fallback + Diff |
| حجم کار | حدود **۱ تا ۱.۵ روز توسعه متمرکز** + QA چهار صفحه روی Staging |
| وابستگی جدید | صفر (Native post meta) |

**پیشنهاد ما:**  
انجام Migration **قبل از Production** با الگوی Fallback — به‌شرط اینکه پس از اعمال، یک دور Staging QA (شامل Diff محتوا) انجام شود.

اگر ترجیح شما تعویق است، آن را به‌عنوان **اولین Sprint پس از Launch با Deadline مشخص (مثلاً ۷–۱۰ روز کاری پس از Go-live)** ثبت می‌کنیم — نه Roadmap نامشخص.

لطفاً یکی از دو مسیر را تأیید کنید تا اجرا شود.

---

## ۲) Author / Reviewer Architecture (محتوای پزشکی)

Data Model وردپرس از ابتدا ظرفیت دارد:

- Author (هسته WP)  
- Professional title / Bio → User meta یا post meta  
- Date Published / Modified → هسته  
- Reviewed by / Medical Review date / References → post meta  

UI سنگین الان ساخته نمی‌شود؛ Template و Model مانع افزودن بعدی نیستند.  
برای Blog و (پس از Migration) Service Pages قابل گسترش است.

---

## ۳) FAQ — اصلاح استراتژی

تأیید می‌شود:

- FAQ visible content حفظ می‌شود (HTML واقعی، Accordion قابل Crawl و Accessible).  
- FAQ **هدف Rich Result / Production Gate اصلی نیست**.  
- FAQPage Schema تولید اجباری Theme نیست؛ اگر SEO Plugin تولید کند فقط در صورت Valid و تطابق با محتوای قابل مشاهده.  
- QAPage جایگزین نمی‌شود مگر صفحه واقعاً پذیرای پاسخ کاربر باشد.  
- روی نمایش Rich Result حساب نمی‌شود.

---

## ۴) Structured Data — فقط واقعی

تأیید:

- Dentist/LocalBusiness فقط نوشهر  
- Breadcrumb صحیح  
- Service در صفحات مربوط  
- Article برای مقالات واقعی  
- بدون Review / AggregateRating / fake testimonial / Geo حدسی / PostalCode حدسی / Opening hours تأییدنشده / تهران فعال  

Owner واحد = SEO Plugin وقتی فعال است؛ در غیر این صورت Fallback Theme.  
Validation با View-Source + ابزار رسمی قبل از Production.

---

## ۵) SEO Plugin = Owner اصلی

معماری تأیید است. قبل از Production فقط **یک** Plugin انتخاب شود.  
پس از انتخاب روی Home · Implant · Crown · Surgery · General · Blog post · Contact فقط یک نمونه از Title / Meta / Canonical / OG / Breadcrumb / Dentist / Service / Article در View-Source دیده شود.  
Plugin نهایی + دلیل انتخاب در Maintenance Guide ثبت می‌شود.

---

## ۶) Indexability Matrix

تحویل شد: **`docs/INDEXABILITY-MATRIX-FA.md`** داخل بسته تم.

خلاصه تصمیم پیشنهادی:

| Index | Noindex (پیشنهادی) |
|---|---|
| Home · 4 Services · About · Contact · FAQ · Blog archive · Single posts | Appointment · Tags · Date · Search · Attachment · Staging |

Category/Author تا غنی نشدن محتوا ترجیحاً Noindex.  
نهایی‌سازی در SEO Plugin روی Staging.

---

## ۷) Staging Index Protection

تأیید الزامات:

- Access restriction / password ترجیحی + noindex  
- robots.txt به‌تنهایی کافی نیست  
- قبل از Go-live: حذف restriction و noindex  
- Staging URL وارد Sitemap Production نشود  
- Checklist در Indexability Matrix ثبت شد

---

## ۸) Canonical Host

قبل از Production یک Host قطعی (www یا non-www) انتخاب شود.  
تمام HTTP / alternate / www/non-www با ۳۰۱ مستقیم به Host نهایی.  
Homepage فقط از یک URL با ۲۰۰.  
Canonical · Sitemap · Internal Links همه Host واحد.

---

## ۹–۱۱) URL Freeze · Redirect Owner · Redirect QA

- URL Structure صفحات اصلی قبل از Go-live Freeze.  
- **Production Redirect Owner = SEO/Redirect Plugin مستقل یا Server** (Theme فقط Helper).  
- فایل `Old URL → New Final URL → HTTP Status` قبل از Migration.  
- پس از Migration: Crawl برای 404 · Soft 404 · Chain · Loop · Wrong destination · HTTP→HTTPS · www · trailing slash · broken internal · mixed canonical.

---

## ۱۲–۱۳) Sitemap · robots.txt

- Sitemap Owner = SEO Plugin نهایی.  
- فقط Canonical + Indexable؛ بدون Staging / Search / 404 / noindex.  
- یک Sitemap index اصلی برای Search Console.  
- بررسی عدم تداخل Core WP و Plugin.  
- robots.txt منابع CSS/JS و صفحات Indexable را مسدود نکند؛ برای Canonicalization استفاده نشود.

---

## ۱۴–۱۶) Local SEO نوشهر · NAP Storage · GBP

NAP قبل از Launch Freeze می‌شود و در Hero · Footer · Contact · Appointment · Email · Schema · Plugin همخوان است.

**Storage (تأیید ۱.۴.۱۶+):** WordPress Options مستقل از Theme.  
نام Optionها در Maintenance Guide مستند است (`alipasandi_clinic_phone` و …).

تهران فقط سابقه؛ بدون Local intent عملیاتی.  
NAP واقعی برای Match با Google Business Profile نوشهر — بدون Name/Address/Phone مصنوعی.

---

## ۱۷–۱۸) About / Trust · Medical Content Quality

صفحه About از Editor وردپرس قابل توسعه است (تحصیلات، سوابق، حوزه‌ها، مدارک، Bio) بدون Redesign اجباری.  
Template Blog تیم را به Word Count یا Keyword stuffing مجبور نمی‌کند؛ ساختار اجازه Original · Complete · Expert-reviewed · Useful · References واقعی می‌دهد.

---

## ۱۹–۲۰) Internal Linking · Breadcrumb

لینک‌های مهم `<a href>` واقعی هستند.  
امکان Blog→Service · Service→Article · Service→FAQ/Contact · Article→Author/About.  
Navigation حیاتی JavaScript-only نیست.  
Breadcrumb Service: Home → Services → [Service]؛ UI و Schema هم‌خوان.

---

## ۲۱–۲۳) Core Web Vitals · Image Delivery

قبل از Production گزارش واقعی Mobile + Desktop: **LCP · INP · CLS** (نه فقط Lighthouse کلی).  
شامل Hero · Fonts · CSS · JS · Third-party · Sticky CTA · Header · Images.  
پس از Launch: مانیتور Field Data در Search Console — در Post-launch checklist.

Image: responsive source موبایل · بدون دانلود Master بی‌دلیل · Hero بدون lazy · زیر Fold lazy · width/height · ALT محتوایی قابل ویرایش · تزئینی ALT خالی.

---

## ۲۴) OG / Social Image

Crawlable · Public · HTTPS · کیفیت مناسب · قابل Override per Service/Article · بدون اشاره به فایل Staging.

---

## ۲۵–۲۷) Analytics · Consent · Third-party

- GA/Clarity خارج از Theme؛ Owner = Plugin/GTM/Site-level.  
- Duplicate Load بررسی شود.  
- Consent در صورت نیاز منطقه‌ای — بدون Plugin سنگین بی‌بررسی Performance.  
- قبل از Freeze: لیست کامل Third-party (Analytics · Clarity · Map · Social · CDN · Fonts · Chat).  
- Font خارجی Frontend = صفر.

---

## ۲۸–۳۱) Form · Mail · Hours · Data Retention

تست واقعی Appointment/Contact روی هاست واقعی (Valid · Invalid phone · فارسی/انگلیسی · Past date · Invalid time · Missing · Success · Error · Nonce · Honeypot).

**Pass فقط با دریافت واقعی ایمیل** + SMTP/SPF/DKIM/From/Reply-To/Spam.

ساعات از Customizer — بدون Edit کد. ساعات واقعی نوشهر قبل از Launch وارد شود.

**Form Data Retention:** فقط Email — **ذخیره در Database/Log توسط Theme انجام نمی‌شود.**  
مستند در Maintenance Guide.

---

## ۳۲–۳۵) Security · PHP · Plugin Inventory · Lock-in

- Staging: PHP Warning/Notice/Fatal = صفر با WP_DEBUG.  
- Escaping · Sanitization · Nonce · Server-side validation.  
- Core/Vendor دستکاری نمی‌شود.  
- Target: PHP ۸.۲ ترجیحی؛ Requires PHP ۷.۴ / WP ۶.۲.  
- قبل از Production: لیست Pluginها با کاربرد؛ حذف بلااستفاده/Duplicate/قدیمی.  
- محتوای اصلی به Shortcode/Page Builder وابسته نیست.

---

## ۳۶–۳۸) Update Strategy · Backup · 404

فرآیند Update در Maintenance Guide: Backup → Staging update → Smoke → QA → Production.  
Backup: Database · Uploads · Theme · Plugins/config.  
404 واقعی HTTP 404 + Header/Footer + لینک‌های مهم — Soft 404 نباشد.

---

## ۳۹–۴۱) A11y · Mobile · Browser QA

هدف WCAG AA بخش‌های اصلی.  
Mobile: 320 / 360 / 375 / 390 / 414 / 430 + Tablet 768 / 1024.  
Browser Smoke: Safari iOS · Chrome Android · Chrome Desktop · Safari Desktop · Firefox Desktop.

---

## ۴۳) Content Migration Test (در صورت اجرا)

قبل/بعد Diff برای هر چهار Service: H1 · Text · FAQ · CTA · Internal links · Images · HTML hierarchy.

---

## ۴۴–۴۵) Search Console · Migration Monitoring

پس از Go-live: Domain verification · Sitemap submit · URL Inspection · Index coverage · Crawl errors · Canonical · CWV.  
اگر Domain/URL Migration: مانیتور 404 · Redirect · Indexed · Crawl · Traffic · Canonical selection.

---

## ۴۶) Production Freeze — شرط Pass

Form واقعی · Mail واقعی · SMTP/SPF/DKIM · SEO Owner نهایی · Duplicate SEO = صفر · Schema نوشهر · Indexability Matrix · URL Inventory · Redirect Map · Canonical Host · Sitemap · Staging noindex/protection · Responsive · A11y · CWV · PHP/Error Log · Backup/Rollback · Asset License · **تصمیم Service Content Migration**

---

## ۴۷) Final Handoff — اقلام تحویل

- Final Theme ZIP + Version + Changelog  
- Maintenance Guide (با نام Optionها)  
- Design Token reference  
- Asset list + license confirmation (سمت شما)  
- NAP option list  
- Plugin list  
- SEO plugin/config owner  
- Indexability Matrix  
- URL Inventory · Redirect Map  
- Sitemap URL · robots.txt status  
- Tracking owner · Third-party list  
- Form/mail QA · Performance · A11y summary  
- Backup/Rollback notes  
- Service content roadmap **یا** نتیجه Migration  

---

## ۴۸) گزارش Staging مورد انتظار (Evidence-based)

| # | مورد |
|---|---|
| 1 | Staging URL |
| 2 | WP Version |
| 3 | PHP Version |
| 4 | Plugin List |
| 5 | SEO Plugin نهایی |
| 6 | Canonical Host |
| 7 | Indexability Matrix (اعمال‌شده) |
| 8 | Form QA Result |
| 9 | Screenshot ایمیل دریافتی |
| 10 | SMTP/SPF/DKIM status |
| 11 | View-Source نمونه صفحات |
| 12 | Schema validation |
| 13 | Duplicate SEO check |
| 14 | Sitemap |
| 15 | robots/noindex status |
| 16 | Responsive QA |
| 17 | Browser QA |
| 18 | Mobile Footer 390 |
| 19 | LCP / INP / CLS |
| 20 | Network image check |
| 21 | Error log status |
| 22 | URL Inventory |
| 23 | Redirect draft |
| 24 | Analytics/Clarity owner |
| 25 | Third-party request list |
| 26 | **Service Content Migration decision** |
| 27 | Asset license confirmation |

تا Pass شدن این موارد، **Production Approval نهایی صادر نمی‌شود**.

---

## فایل‌های این مرحله

| فایل | توضیح |
|---|---|
| `alipasandi-clinic-1.4.18.zip` | تم + Indexability Matrix + Maintenance Guide به‌روز (بدون تغییر Design) |
| این سند | پاسخ کامل به الزامات ۱–۴۸ |

---

## تصمیم فوری مورد نیاز از شما

**Service Content Migration:**

- **الف)** انجام قبل از Production با روش Post Meta + Fallback (Estimate ۱–۱.۵ روز + QA Staging)  
- **ب)** ثبت به‌عنوان اولین Sprint پس از Launch با Deadline مشخص (مثلاً ۷–۱۰ روز کاری پس از Go-live)

پس از اعلام انتخاب شما، مسیر الف اجرا می‌شود یا Deadline مسیر ب در Documentation قفل می‌شود.

---

**جمع‌بندی:**  
نسخه ۱.۴.۱۸ Design را قفل نگه داشته، Indexability Matrix و Maintenance Guide کامل‌تر را تحویل داده، و برای مهم‌ترین Blocker محتوایی (Service Content) روش Migration با تضمین عدم تغییر URL/Design/محتوا و Estimate شفاف ارائه کرده است.  

تمرکز از این به بعد فقط Evidence-based QA، Technical SEO، Performance، Accessibility و Future Maintainability است.
