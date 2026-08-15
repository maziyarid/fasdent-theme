# پاسخ نهایی به الزامات Production / SEO / Future-Proofing

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**نسخه تم روی مسیر تحویل:** ۱.۴.۱۵  
**مبنای UI:** بدون Redesign · بدون تغییر Color Token · Layout · Hero Composition · Grid · Container · Typography Scale

---

## تأیید Design Lock

از این مرحله به بعد هیچ‌یک از موارد زیر بدون درخواست صریح انجام نمی‌شود:

- Redesign
- تغییر Color Token
- تغییر Hero Composition
- تغییر Grid / Container / Typography Scale
- تغییر ساختار صفحات خدمات

تمرکز صرفاً روی Production Readiness واقعی، زیرساخت SEO اصولی و قابل‌توسعه، و جلوگیری از Technical Debt / Lock-in است.

---

## Business Rule لوکیشن (قفل‌شده و مستند)

| موضوع | وضعیت |
|---|---|
| Operational Location | فقط **نوشهر** |
| Booking | فقط نوشهر · بدون فیلد Location |
| Schema Dentist / LocalBusiness | فقط نوشهر |
| تهران | فقط سابقه / برند · غیرعملیاتی |
| Hero / Footer / Contact / Appointment | محور نوشهر |
| Design / Token / Layout / Hero Composition | بدون تغییر از این مرحله به بعد |

این منطق به‌عنوان **Business Rule** در Documentation نهایی ثبت می‌شود تا توسعه‌دهنده بعدی نتواند اشتباهاً تهران را به Schema یا فرم رزرو برگرداند.

---

## به‌روزرسانی‌های فنی ۱.۴.۱۵ (بدون Redesign)

| موضوع | اقدام |
|---|---|
| NAP Single Source of Truth | متن خیابان در Hero از Hard-code خارج و به `clinic_city` + `clinic_street` منتقل شد |
| Designer Credit | کاملاً Optional شد — فیلد خالی در Customizer = هیچ خروجی در فوتر |
| postalCode | فیلد Optional در Customizer · فقط در صورت مقدار رسمی در Schema خروجی داده می‌شود |
| Geo Coordinates | فیلدهای Optional `clinic_geo_lat` / `clinic_geo_lng` · فقط با مقدار واقعی عددی در Schema ظاهر می‌شود (هیچ مقدار حدسی وارد نمی‌شود) |
| Design / Token / Layout | بدون هیچ تغییری |

---

## ۱) NAP به‌عنوان Single Source of Truth

تأیید می‌شود. تمام اطلاعات Local Business از Customizer خوانده می‌شوند:

| فیلد | کلید Customizer | وضعیت |
|---|---|---|
| Clinic Phone | `clinic_phone` | مشترک در Footer · Contact · Appointment · Schema · tel: |
| Street (Schema) | `clinic_street` | SSOT |
| City | `clinic_city` | SSOT |
| Address نمایشی UI | `clinic_address` | SSOT |
| Map URL | `clinic_maps` | SSOT |
| Booking / Notify Email | `clinic_notify_email` | SSOT |
| Working Hours / Time slots | `clinic_booking_times` | SSOT (فرم و اعتبارسنجی سمت سرور) |
| Postal Code | `clinic_postal_code` | Optional · خالی = عدم خروجی در Schema |
| Geo Lat / Lng | `clinic_geo_lat` / `clinic_geo_lng` | Optional · فقط مقدار واقعی |
| Legacy تهران | `clinic_address_legacy` | فقط معرفی/برند · در Schema/فرم/Map/ایمیل استفاده نمی‌شود |
| Designer Credit | `designer_credit` | خالی = مخفی کامل |

تغییر شماره، آدرس یا ساعات در آینده فقط از یک محل (Customizer) انجام می‌شود.

---

## ۲) Schema نوشهر

ساختار PostalAddress مورد تأیید است و حفظ شده:

```
streetAddress   = ستارخان، امیراد ۱، طبقه ۵   (از clinic_street)
addressLocality = نوشهر                         (از clinic_city)
addressRegion   = مازندران
addressCountry  = IR
```

- `telephone` از همان منبع UI خوانده می‌شود.
- `url` از `home_url` (دامنه Production).
- `image` از تابع OG تم (Crawlable).
- `hasMap` دقیقاً لینک Maps نوشهر.
- هیچ Schema مربوط به تهران تولید نمی‌شود.
- با فعال بودن SEO Plugin، Theme هیچ نسخه دوم Schema تولید نمی‌کند (`alipasandi_seo_plugin_active()`).
- `postalCode` و `geo` فقط در صورت پر بودن فیلدهای Optional ظاهر می‌شوند.

---

## ۳) SEO Owner — معماری تأییدشده

**SEO Plugin = Owner اصلی · Theme فقط Fallback**

وقتی Yoast / Rank Math / AIOSEO فعال باشد، Theme هیچ خروجی موازی برای موارد زیر تولید نمی‌کند:

- Title
- Meta Description
- Canonical
- Open Graph
- Twitter Cards
- Breadcrumb Schema
- Dentist Schema
- Service Schema
- Article Schema

این رفتار مستند و قابل تست است (View-Source).  
Theme فقط در نبود Plugin، meta box سبک و Schema پایه را به‌عنوان Fallback ارائه می‌دهد.

**انتخاب Plugin:** فقط یک Plugin نصب شود. Yoast و Rank Math همزمان نصب نشوند. پس از انتخاب در Staging، همان Plugin تا پایان پروژه ثابت می‌ماند. از نظر سازگاری با تم تفاوت محسوسی بین آن‌ها وجود ندارد.

---

## ۴) URL Architecture · Inventory · Redirect Map · SEO Migration Package

قبل از Go-live / تغییر دامنه:

- URL Structure Freeze می‌شود.
- Slug صفحات مهم (Implant · Crown · Surgery · General · About · Contact · Appointment · FAQ · Blog) بدون دلیل تغییر نمی‌کند.
- هر تغییر URL فقط با ۳۰۱ مستقیم (بدون Chain A→B→C).
- فایل دقیق `Old URL → New URL → HTTP Status` تحویل داده می‌شود.
- SEO Migration Package (Current/New Inventory، Titles/Meta فعلی، Sitemap فعلی/جدید، Canonical plan، Search Console checklist، ۴۰۴ monitoring) بخشی از تحویل فنی است.

تم فیلتر `alipasandi_redirect_map` را برای اضافه کردن Redirectهای بررسی‌شده دارد. داده‌های Inventory فعلی fasdent.ir از سمت شما لازم است.

---

## ۵) Sitemap / Robots / Indexability · Breadcrumb · Service Pages · Content · Blog

- روی Staging ایندکس مسدود خواهد بود؛ در Production `noindex` پاک، robots.txt و Sitemap صحیح فعال می‌شود.
- Breadcrumb منطقی + Schema بدون Duplicate (در صورت مدیریت توسط Plugin، Theme Schema موازی نمی‌سازد).
- چهار Landing Page مستقل (ایمپلنت · روکش · جراحی و لثه · درمان عمومی) حفظ می‌شوند و Merge نمی‌شوند.
- Templateها اجازه افزودن سکشن محتوایی جدید بدون CSS صفحه‌ای و بدون شکستن Layout را می‌دهند.
- Blog در Navigation اصلی نیست ولی زیرساخت (Single · Archive · Article Schema · Author · Date) حفظ و قابل Crawl است.
- ساختار Theme برای افزودن Author / Professional title / Bio / Reviewed by / References در آینده بدون بازسازی آماده است.
- Internal links واقعی `<a href>` هستند.
- Heading: یک H1 + H2/H3 منطقی رعایت می‌شود.
- تصاویر: ALT قابل مدیریت · width/height · srcset/WebP · lazy برای پایین‌صفحه · Hero بدون lazy بی‌مورد.

---

## ۶) Core Web Vitals · Font Performance

قبل از Production نتیجه واقعی LCP / CLS / INP (مخصوصاً Mobile) درخواست می‌شود.  
فونت‌ها local هستند (Playfair + Vazirmatn WOFF2 · فقط weightهای موردنیاز · `font-display: swap` · بدون Google Fonts). این ساختار حفظ می‌شود.

---

## ۷) Theme Lock-in · Design System · WP Upgrade Safety · PHP

- محتوای حیاتی (مقالات · SEO metadata · URLها) در WordPress/Plugin ذخیره می‌شود؛ Customizer عمدتاً برای Branding/Presentation و NAP عملیاتی است. این تفکیک در Documentation نهایی توضیح داده می‌شود.
- Color / Radius / Shadow / Typography / Componentها مرکزی می‌مانند. CSS Patch صفحه‌ای اضافه نمی‌شود.
- Theme Core را Modify نمی‌کند · به Page Builder وابسته نیست · React Runtime برای Frontend ندارد · Plugin اختصاصی اجباری ندارد.
- Target فعلی: Requires at least WP 6.2 · Requires PHP 7.4. روی Staging با PHP 8.2 تست می‌شود. قبل از Production Error Log بررسی می‌گردد (WP_DEBUG در Staging · خاموش در Production).

---

## ۸) فرم رزرو · Mail Deliverability · Security

- فرم فقط نوشهر است. محل مراجعه در UI · ایمیل · Confirmation شفاف است.
- ساعات از Customizer مدیریت می‌شوند — تغییر ساعات نیاز به Edit کد ندارد.
- Nonce + Honeypot + Server-side validation + Sanitize + Escaping حفظ می‌شود.
- قبل از Production: Submit واقعی + دریافت ایمیل در `drkeyvanalipasandi@gmail.com` + بررسی SMTP / SPF / DKIM / From / Spam الزامی است. Success Message بدون ایمیل واقعی Pass محسوب نمی‌شود.

---

## ۹) Accessibility · Local SEO · GBP · Search Console · ۴۰۴ · Redirect Testing · Backup

تمام موارد ثبت شد:

- حداقل WCAG AA در بخش‌های اصلی
- Consistency کامل نوشهر در تمام سیگنال‌ها
- آماده‌سازی برای همسان‌سازی با Google Business Profile نوشهر
- برنامه Search Console / Analytics
- صفحه ۴۰۴ واقعی (HTTP 404 · H1 · لینک بازگشت · خدمات · Header/Footer)
- Crawl پس از Migration برای Broken / Chain / Loop / Mixed Canonical
- قبل از Go-live: Files + Database + Theme قبلی + نسخه نهایی + Rollback Procedure

---

## ۱۰) Designer Credit Footer — کاملاً Optional

تأیید و در ۱.۴.۱۵ اعمال شد:

- حذف Credit هیچ اثر فنی روی Theme ندارد.
- وابستگی فنی نیست.
- لینک اجباری خارجی / hidden backlink وجود ندارد.
- از Customizer قابل خالی‌کردن است.
- فیلد خالی = هیچ خروجی در فوتر.
- تا اعلام نام/متن نهایی هیچ Credit اجباری قفل نمی‌شود.

---

## ۱۱) External Dependencies

لیست نهایی قبل از Freeze ارائه می‌شود. وضعیت فعلی:

| نوع | وضعیت |
|---|---|
| Font خارجی Frontend | صفر (همه local WOFF2) |
| JS خارجی غیرضروری Theme | صفر |
| قالب HTML ایمیل | CDN jsDelivr برای Vazirmatn (با fallback کامل به Tahoma/Arial) — فقط برای نمایش بهتر در کلاینت‌های ایمیل |

---

## ۱۲) وضعیت Analytics و Backlink (اطلاع‌رسانی)

سایت در وضعیت فعلی **بدون پروفایل backlink قابل توجه** است (سایت تازه / بدون سابقه لینک‌سازی خارجی معنادار).

تنها اسکریپت‌های تحلیلی فعال:

**Microsoft Clarity**
```html
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "xr1l3ayt1m");
</script>
```

**Google Analytics (gtag)**
```html
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-FTC344TFKY"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-FTC344TFKY');
</script>
```

دسترسی GA و Clarity به ایمیل دکتر (`drkeyvanalipasandi@gmail.com`) داده شده است.  
پس از آماده‌شدن دامنه جدید، همین propertyها روی دامنه جدید منتقل/پیکربندی می‌شوند. هیچ Consent banner یا اسکریپت سنگین اضافی بدون بررسی Performance/Consent اضافه نخواهد شد.

---

## ۱۳) تحویل Staging

لطفاً URL واقعی Staging را ارسال کنید. از این مرحله QA روی نسخه واقعی WordPress انجام می‌شود.

همراه Staging این موارد درخواست می‌شود:

1. Staging URL  
2. WordPress / PHP Version  
3. Plugin List  
4. SEO Plugin انتخاب‌شده  
5. نتیجه تست فرم + اسکرین‌شات ایمیل دریافتی  
6. Schema output / View-Source  
7. Mobile Footer screenshot (عرض ۳۹۰)  
8. Responsive QA result  
9. LCP / CLS / INP (مخصوصاً Mobile)  
10. پیش‌نویس URL / Redirect Map  
11. وضعیت Error / Debug Log  

---

## ۱۴) Production Gate

تا زمانی که موارد زیر Pass نشده‌اند، Production Approval نهایی صادر نمی‌شود:

- [ ] Form واقعی  
- [ ] Email واقعی  
- [ ] SMTP / SPF / DKIM  
- [ ] Schema فقط نوشهر  
- [ ] SEO Output یکتا  
- [ ] URL Migration Plan  
- [ ] Redirect Map  
- [ ] Responsive QA  
- [ ] Accessibility  
- [ ] Core Web Vitals  
- [ ] Backup / Rollback  
- [ ] Debug / Error Check  

پس از بسته‌شدن این موارد، Production Freeze انجام می‌شود و از آن مرحله فقط Critical Bug Fix مجاز است.

---

## فایل‌های مورد نیاز مرحله بعد از سمت شما

1. URL Staging (اگر جدا از Production است)  
2. نتیجه تست فرم + اسکرین Success + اسکرین ایمیل دریافتی  
3. View-Source Schema نوشهر  
4. نتیجه SEO duplicate check با Plugin نهایی  
5. Screenshot Footer موبایل ۳۹۰  
6. Performance (LCP / CLS / INP)  
7. Redirect Map اولیه / URL Inventory فعلی fasdent.ir  
8. نام نهایی طراح برای خط کپی‌رایت فوتر (اختیاری — خالی = بدون نمایش)  
9. کدپستی رسمی و/یا مختصات جغرافیایی واقعی (در صورت وجود — اختیاری)

---

**جمع‌بندی:**  
نسخه ۱.۴.۱۵ مسیر طراحی و معماری Local SEO را حفظ کرده و الزامات NAP کامل‌تر، Designer Credit کاملاً اختیاری، و فیلدهای Optional postalCode/geo را بدون هیچ Redesign اضافه کرده است.  

تمام موارد ۱–۳۸ شما به‌عنوان **Production / SEO / Future Upgrade Requirements** پروژه ثبت شد و در Documentation نهایی تحویل موجود خواهد بود.  

هدف ما فقط Launch نیست؛ زیرساختی است که بتواند در سال‌های بعد روی Google رشد کند و بدون بازسازی فنی توسعه پیدا کند.

آماده دریافت Staging URL و شروع QA واقعی هستیم.
