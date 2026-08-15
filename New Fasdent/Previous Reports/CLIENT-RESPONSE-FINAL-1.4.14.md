# پاسخ نهایی به بررسی Staging / Production Readiness

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**نسخه تم روی مسیر تحویل:** ۱.۴.۱۴  
**مبنای UI:** بدون Redesign · بدون تغییر Color Token · Layout · Hero Composition

---

## بررسی سریع سایت زنده (fasdent.ir)

پس از آپلود تم، از نظر محتوایی موارد زیر روی Production قابل مشاهده بود:

| محل | وضعیت مشاهده‌شده |
|---|---|
| Hero | **مطب نوشهر** — ستارخان، امیراد ۱، طبقه ۵ |
| پیام دو‌لوکیشنه «تهران و نوشهر» | **حذف‌شده** از Hero |
| Contact | تمرکز روی نوشهر + Map نوشهر |
| Appointment | «محل مراجعه: مطب نوشهر» · بدون فیلد Location |
| About | بخش دقت / شفافیت / پیگیری فعال · بدون ادعای شعبه تهران |

اگر پس از آپلود ۱.۴.۱۴ هنوز چیزی از نسخه قبل دیده می‌شود، یک‌بار **پاک‌سازی کش** (افزونه کش / CDN / مرورگر) لازم است.

---

## تأیید مسیر کسب‌وکار (قفل‌شده)

| موضوع | وضعیت |
|---|---|
| Operational Location | فقط **نوشهر** |
| Booking | فقط نوشهر · بدون فیلد Location |
| Schema Dentist/LocalBusiness | فقط نوشهر |
| تهران | فقط سابقه/برند · غیرعملیاتی |
| Hero / Footer / Contact / Appointment | محور نوشهر |
| Design / Token / Layout / Hero Composition | بدون تغییر از این مرحله به بعد |

---

## ۱) مقادیر Schema و منبع داده

| فیلد | مقدار پیش‌فرض | منبع |
|---|---|---|
| name | کلینیک دندانپزشکی دکتر کیوان علی‌پسندی | `inc/seo.php` |
| telephone | از `clinic_phone` (پیش‌فرض ۰۹۲۰ ۱۴۴ ۱۴۶۹) | Customizer → `alipasandi_phone_href()` |
| url | `home_url` | وردپرس |
| image | تصویر OG تم | `alipasandi_open_graph_image()` |
| streetAddress | **ستارخان، امیراد ۱، طبقه ۵** | `clinic_street` |
| addressLocality | **نوشهر** | `clinic_city` |
| addressRegion | مازندران | ثابت |
| addressCountry | IR | ثابت |
| postalCode | — | در صورت اعلام کدپستی اضافه می‌شود |
| hasMap | لینک Maps نوشهر | `clinic_maps` |

تلفن Footer · Contact · Appointment · Schema همه از **یک** منبع (`clinic_phone`) خوانده می‌شوند.

---

## ۲) ساختار PostalAddress

```
streetAddress   = ستارخان، امیراد ۱، طبقه ۵
addressLocality = نوشهر
addressRegion   = مازندران
```

نمایش UI همچنان آدرس کامل خوانا است: `نوشهر، ستارخان، امیراد ۱، طبقه ۵`.

---

## ۳) Map URL

`https://maps.app.goo.gl/ycPem67fpYbvgdXZ7?g_st=atm`  
روی Staging/Production در iPhone Safari · Android Chrome · Desktop لطفاً باز شدن و لوکیشن صحیح تأیید شود.

---

## ۴) فرم رزرو

کد آماده است و فیکس Submit در ۱.۴.۱۲ اعمال شده است.

**الزامی روی Staging/هاست واقعی:**

- [ ] Submit موفق  
- [ ] دریافت ایمیل در `drkeyvanalipasandi@gmail.com`  
- [ ] Location ثابت = نوشهر در ایمیل  
- [ ] رد تاریخ گذشته  
- [ ] شماره فارسی و انگلیسی  
- [ ] Success / Error  
- [ ] Honeypot / Nonce  

---

## ۵) ساعات رزرو

- Default: ۱۰:۰۰–۱۲:۳۰ و ۱۴:۰۰–۱۸:۳۰  
- مدیریت از **Customizer** بدون Edit کد  
- فرم از همان داده می‌خواند؛ Time Slot در تم Cache نمی‌شود  
- ساعات قطعی نوشهر پس از اعلام شما در Customizer تنظیم می‌شود

---

## ۶) Mail Deliverability

گیرنده: `drkeyvanalipasandi@gmail.com`  
قالب: HTML · RTL · پالت برند · تلاش برای Vazirmatn (با fallback Tahoma)

روی هاست واقعی باید SMTP / SPF / DKIM / From / Spam و دریافت واقعی Gmail تست شود. Success Message سایت کافی نیست.

---

## ۷ و ۸) SEO Owner

- با فعال بودن Yoast یا Rank Math، Theme **هیچ** Meta/Schema تولید نمی‌کند  
- فقط **یک** Plugin تا پایان پروژه  
- از نظر سازگاری با تم، هر دو قابل استفاده است  

**پیشنهاد:** یکی را انتخاب و ثابت بگذارید (Rank Math برای Local SEO معمولاً کنترل UI بیشتری دارد؛ Yoast پایدار و آشناست).

پس از فعال‌سازی، View-Source باید فقط یک نمونه Canonical / Meta / OG / Breadcrumb / Dentist / Service / Article داشته باشد.

---

## ۹) Legacy تهران

`clinic_address_legacy` فقط در Customizer است و در Schema · Contact · Footer عملیاتی · فرم · ایمیل · Map استفاده **نمی‌شود**.

---

## ۱۰) About / Bio

در Templateها عبارت «مطب تهران / شعبه تهران / رزرو تهران» وجود ندارد.  
اگر محتوا اضافه شود، فقط با فرم «سابقه فعالیت حرفه‌ای در تهران» مجاز است.

---

## ۱۱) یکسانی Local SEO

نوشهر در Hero · Footer · Contact · Appointment · Schema · Map · ایمیل از تنظیمات یکسان خوانده می‌شود.

---

## ۱۲–۱۴) Responsive / A11y / Performance

روی Staging واقعی با عرض‌های درخواستی و LCP/CLS/INP (مخصوصاً Mobile) گزارش شود. WebP پزشک/ایمپلنت/کلینیک در تم با srcset آماده است.

---

## ۱۵) URL / SEO Migration

قبل از Go-live دامنه جدید: Inventory · Mapping · ۳۰۱ · Titles/Meta · Sitemap همچنان لازم است.

---

## ۱۶) Production Approval

مسیر کسب‌وکار و Local SEO برای Staging تأیید است.  
**Production Approval نهایی** پس از بستن:

تست فرم · ایمیل واقعی · Deliverability · Schema نوشهر · SEO یکتا · Responsive · A11y · Performance · Redirect Map

---

## به‌روزرسانی‌های فنی پس از ۱.۴.۱۱ (بدون Redesign)

| نسخه | موضوع |
|---|---|
| **۱.۴.۱۲** | فیکس Submit فرم رزرو (novalidate + عدم قطع POST با disable دکمه) |
| **۱.۴.۱۳** | تفکیک streetAddress از شهر در Schema |
| **۱.۴.۱۴** | Padding کارت «مراجعه حضوری» · آیکن SVG برای دقت/شفافیت/پیگیری در About · Vazirmatn در قالب ایمیل (با fallback) · خط اعتبار طراحی در فوتر |

---

## اعتبار طراحی (Designer Copyright)

**در فوتر Production، اعتبار طراحی رابط کاربری درج می‌شود.**

- متن از طریق Customizer (`designer_credit`) قابل تنظیم است  
- پیش‌فرض فعلی: «طراحی رابط کاربری این وب‌سایت محفوظ است.»  
- با اعلام نام نهایی طراح، همان متن در فوتر جایگزین و در Release نهایی قفل می‌شود  

این مورد بخشی از تحویل Production است و جدا از Design System برند کلینیک باقی می‌ماند.

---

## فایل‌های مورد نیاز مرحله بعد از سمت شما

1. URL Staging (اگر جدا از Production است)  
2. نتیجه تست فرم + اسکرین Success  
3. View-Source Schema نوشهر  
4. نتیجه SEO duplicate check با Plugin نهایی  
5. Screenshot Footer موبایل ۳۹۰  
6. Performance (LCP / CLS / INP)  
7. Redirect Map اولیه  
8. نام نهایی طراح برای خط کپی‌رایت فوتر (اختیاری تا قبل از Freeze)

---

**جمع‌بندی:** مسیر نوشهر روی سایت اعمال شده و از نظر معماری Local SEO با تصمیم شما هم‌خوان است. مرحله بعد فقط QA واقعی و Production Readiness است — بدون تغییر Design.
