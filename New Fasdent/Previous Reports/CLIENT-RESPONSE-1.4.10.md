# پاسخ به بررسی Staging — نسخه ۱.۴.۱۰

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**مبنای UI:** ۱.۴.۹ (بدون Redesign / بدون تغییر Layout / Hero / Color System)  
**نسخه تحویلی این مرحله:** ۱.۴.۱۰ — فقط QA/Fix و آماده‌سازی Production

---

## خلاصه

موارد فنی قابل‌اجرا در کد انجام شد. موارد وابسته به Staging واقعی، Hosting، تصمیم کسب‌وکار و SEO Ownership در ادامه به‌صورت شفاف تفکیک شده‌اند.

**تغییرات کد در ۱.۴.۱۰ (بدون UI Redesign):**
1. Playfair Display محلی (WOFF2 وزن‌های ۶۰۰ و ۷۰۰) — حذف کامل Google Fonts
2. همگام‌سازی `design-tokens.json` با نسخه ۱.۴.۱۰
3. حذف Assetهای deprecated `doctor-charcoal-gold*`
4. تعریف Semantic Token برای رنگ‌های Functional (Error / Success / Muted)
5. شفاف‌سازی Single Source of Truth رنگ‌ها
6. حذف Badge قرمز «۱» از Floating Contact (جلوگیری از تصور پیام خوانده‌نشده)

---

## ۱) Playfair Display / Local Font

| مورد | نتیجه |
|---|---|
| License | **SIL Open Font License 1.1** — Self-host مجاز است |
| فایل‌های Local | `playfair-display-latin-600-normal.woff2` · `playfair-display-latin-700-normal.woff2` |
| وزن‌های Loadشده | فقط **۶۰۰** (لوگو) و **۷۰۰** (اعداد Journey) |
| Google Fonts | **حذف کامل** — هیچ request به fonts.googleapis.com / fonts.gstatic.com نیست |
| font-display | `swap` حفظ شده |

---

## ۲) Version Design Tokens

`assets/design-tokens.json` → `"version": "1.4.10"`  
هم‌تراز با Release تم.

---

## ۳) Asset Cleanup

حذف شد:
- `doctor-charcoal-gold.jpg`
- `doctor-charcoal-gold-480.webp`
- `doctor-charcoal-gold-768.webp`
- `doctor-charcoal-gold-1150.webp`

Asset نهایی پزشک فقط:
- `doctor-keyvan-alipasandi.jpg` + WebPهای ۴۸۰ / ۷۶۸ / ۱۱۵۰

---

## ۴) Hard-coded Colors — تعریف دقیق

**عبارت صحیح برای Documentation:**

> No deprecated/legacy brand color hard-codes remain in active components.

رنگ‌های Functional State (Success / Error و برندهای شبکه اجتماعی مثل واتساپ/تلگرام) برند کلینیک نیستند.

از ۱.۴.۱۰ Semantic Token تعریف شد:

| Token | مقدار | کاربرد |
|---|---|---|
| `--color-error` | `#B42318` | پیام خطا |
| `--color-error-bg` | `#FEF3F2` | پس‌زمینه خطا |
| `--color-success` | `#027A48` | پیام موفقیت |
| `--color-success-bg` | `#ECFDF3` | پس‌زمینه موفقیت |
| `--color-muted` | `#667085` | متن کم‌اهمیت |

فرم Success/Error از این Tokenها استفاده می‌کند. رنگ‌های برند شبکه‌های اجتماعی (واتساپ/تلگرام/اینستاگرام) به‌خاطر هویت شناخته‌شده همان پلتفرم باقی مانده‌اند.

---

## ۵) Single Source of Truth برای Color

| نقش | فایل |
|---|---|
| **Master (Runtime)** | `assets/css/theme.css` → `:root` |
| **Documentation / Handoff Mirror** | `assets/design-tokens.json` |

**Workflow:** هر تغییر Token در همان Release روی هر دو فایل اعمال می‌شود. مرجع توسعه و رفتار واقعی سایت همیشه `:root` در `theme.css` است. فایل JSON برای Figma/مستندات و جلوگیری از ابهام Versioning است.

---

## ۶) فرم رزرو و Contact — تست واقعی

ساختار Server-side (Nonce، Honeypot، Sanitize، Validation، نرمال‌سازی ارقام فارسی/عربی، Success/Error، wp_mail) در کد موجود است.

**باقی‌مانده — الزامی روی Staging واقعی:**

### Appointment
- [ ] Submit موفق + دریافت ایمیل  
- [ ] Invalid input  
- [ ] تاریخ گذشته  
- [ ] شماره فارسی / انگلیسی  
- [ ] Error State / Success State  

### Contact
- [ ] Submit موفق + دریافت ایمیل  
- [ ] Invalid phone  
- [ ] Error / Success  

**admin_email:** باید روی Staging همان ایمیل نهایی WordPress Settings → General بررسی و اعلام شود (فعلاً در کد از `get_option( 'admin_email' )` استفاده می‌شود).

---

## ۷) Mail Deliverability

Success Message به‌تنهایی کافی نیست. روی Hosting واقعی باید بررسی شود:

- [ ] SMTP / Transactional mail  
- [ ] SPF  
- [ ] DKIM  
- [ ] From domain  
- [ ] Spam/Junk  
- [ ] دریافت یک ایمیل تستی واقعی  

---

## ۸) Booking Time Logic + Dual Location در فرم

ساعات فعلی در کد:

`۱۰:۰۰ تا ۱۲:۳۰` و `۱۴:۰۰ تا ۱۸:۳۰` (بازه ۳۰ دقیقه‌ای)

**نیاز به تأیید کسب‌وکار قبل از هر تغییر UI:**

1. این ساعات با ساعات واقعی پذیرش هماهنگ است؟  
2. تهران و نوشهر ساعات یکسان دارند یا خیر؟  
3. فرم رزرو مربوط به کدام Location است؟  
4. اگر هر دو محل نوبت آنلاین دارند، آیا فیلد انتخاب Location لازم است؟

**تصمیم فعلی (طبق درخواست شما):** تا روشن شدن مدل کاری، **فیلد Location به فرم اضافه نمی‌شود** و UI جدید ساخته نمی‌شود.

---

## ۹) Dual Location / Schema

وضعیت فعلی:
- UI: تهران + نوشهر  
- Schema: فقط تهران به‌عنوان آدرس اصلی LocalBusiness/Dentist  

**نیاز به تأیید کسب‌وکار:**
- نوشهر شعبه/مطب مستقل است یا محل ارائه خدمت ثانویه؟

تا قبل از پاسخ، Schema دوم ایجاد نمی‌شود.  
لینک نقشه نوشهر باید روی Staging در موبایل و دسکتاپ تست شود:
`https://maps.app.goo.gl/ycPem67fpYbvgdXZ7?g_st=atm`

---

## ۱۰) SEO Ownership

Theme می‌تواند Meta / OG / Canonical / Breadcrumb / Dentist / Service / Article Schema تولید کند و در صورت تشخیص Yoast / Rank Math / AIOSEO بخشی از خروجی داخلی را متوقف می‌کند.

**نیاز به تصمیم قبل از Production:**
- Owner اصلی Metadata/Schema = Theme یا SEO Plugin؟

ترجیح: **فقط یک Owner** تا Duplicate ایجاد نشود.

روی Staging باید Source صفحه بررسی شود:
- [ ] Canonical یکتا  
- [ ] Meta Description یکتا  
- [ ] OG یکتا  
- [ ] Dentist/Service Schema یکتا  
- [ ] Breadcrumb Schema یکتا  

---

## ۱۱) URL Migration / Redirect Map

قبل از تغییر دامنه تحویل می‌شود (وابسته به Inventory سایت فعلی):

| مورد | وضعیت |
|---|---|
| Current URL Inventory (fasdent.ir) | باید از Staging/Live استخراج شود |
| New URL Inventory | بر اساس صفحات تم |
| Old → New Mapping | پس از Inventory |
| 301 Redirect Map | پس از Mapping |
| صفحات Index‌شده | Search Console |
| Title/Meta فعلی | Inventory |
| Sitemap فعلی / جدید | پس از محتوا |

Slugهای بدون نیاز منطقی تغییر داده نمی‌شوند.

---

## ۱۲) Search Console و Domain Migration — Sequence پیشنهادی

1. Backup فایل‌ها + Database  
2. Staging QA کامل  
3. ثبت/فعال شدن دامنه جدید  
4. Deployment روی دامنه جدید  
5. 301 Redirects  
6. Canonical Update  
7. Sitemap Submit  
8. Search Console (دامنه جدید + تأیید Redirect)  
9. تست Redirectها  
10. مانیتورینگ Crawl / Index / 404 / Redirect Chain  

---

## ۱۳) Responsive QA واقعی

روی Staging در عرض‌های:

`320 / 360 / 375 / 390 / 414 / 430 / 768 / 1024 / 1440`

بررسی شود: Overflow · Header · Mobile Menu · Hero Crop · CTA · Sticky Action Bar · Safe Area · Cards · Journey RTL · FAQ · Forms · Footer · Dual Location  

**الزامی:** Screenshot مستقل Footer در **۳۹۰px**

---

## ۱۴) Mobile Menu Accessibility

کد Focus Trap / Escape / Scroll Lock موجود است. روی دستگاه واقعی تست شود:

- [ ] Tab / Shift+Tab  
- [ ] Escape  
- [ ] Focus return به Hamburger  
- [ ] عدم Focus روی محتوای پشت Overlay  
- [ ] عدم Scroll پشت Menu  
- [ ] Screen Reader label  

---

## ۱۵) Floating Contact

| مورد | وضعیت کد ۱.۴.۱۰ |
|---|---|
| پایین‌راست ثابت | ✓ |
| عدم جابه‌جایی هنگام Open | ✓ |
| باز شدن گزینه‌ها به چپ | ✓ |
| مخفی روی موبایل (جایگزین: Sticky CTA) | ✓ |
| Keyboard / Escape / Outside Click | در JS موجود — تأیید روی Staging |

**Badge «۱»:** حذف شد.  
دلیل: Notification واقعی وجود نداشت و می‌توانست پیام خوانده‌نشده القا کند. در صورت نیاز آینده به Badge معنادار (مثلاً ساعات پاسخ‌گویی)، با تعریف UX مشخص اضافه می‌شود.

---

## ۱۶) Performance / Core Web Vitals

روی Staging واقعی اندازه‌گیری شود (نه فقط Score کلی):

- LCP · CLS · INP — مخصوصاً Mobile  
- Hero Doctor / Implant / Clinic: WebP responsive موجود است — تأیید Load صحیح روی Network  

---

## ۱۷) Font Performance

| مورد | وضعیت |
|---|---|
| فقط Weightهای استفاده‌شده | ✓ ۶۰۰ و ۷۰۰ |
| WOFF2 | ✓ |
| font-display: swap | ✓ |
| Google Fonts request | **صفر** |
| Preload | فعلاً اضافه نشده (فقط در صورت نیاز LCP بعد از اندازه‌گیری) |

---

## ۱۸) Accessibility

قبل از Production حداقل:

- [ ] Keyboard navigation  
- [ ] Visible Focus  
- [ ] Label فیلدهای فرم  
- [ ] Error Message قابل فهم  
- [ ] Contrast  
- [ ] Alt تصاویر  
- [ ] Heading order  
- [ ] Link purpose  
- [ ] Mobile menu semantics  
- [ ] Accordion aria-expanded / aria-controls  
- [ ] Reduced Motion  
- [ ] Zoom تا ۲۰۰٪  

هدف: **WCAG AA** برای بخش‌های اصلی.

---

## ۱۹) 404 و Utility

- [ ] 404: H1 · CTA بازگشت · Header/Footer · Responsive · بدون layout شکسته  
- [ ] Archive / Blog / Single در صورت فعال بودن Blog  

---

## ۲۰) Blog و SEO آینده

- Blog در Navigation اصلی نیست (تصمیم قبلی)  
- URL در صورت انتشار قابل Crawl است  
- Single Post از همان Design System استفاده می‌کند  
- Article Schema فقط برای Post واقعی  

---

## ۲۱) Asset List نهایی (پس از Cleanup)

| نقش | فایل‌ها |
|---|---|
| Doctor master | `doctor-keyvan-alipasandi.jpg` |
| Doctor WebP | `480` · `768` · `1150` |
| Implant master | `implant-hero.jpg` |
| Implant WebP | `480` · `768` · `920` |
| Clinic master | `clinic-interior.jpg` |
| Clinic WebP | `480` · `768` · `900` |
| Crown (خدمت) | `crown-hero.jpg` + WebP |
| Favicon SVG | `favicon.svg` |
| Favicon PNG | `favicon-32.png` |
| Apple Touch | `apple-touch-icon.png` |
| Fonts | Vazirmatn (۴۰۰/۵۰۰/۷۰۰/۹۰۰) + Playfair (۶۰۰/۷۰۰) WOFF2 |

---

## ۲۲) Security / Update Safety

| مورد | تأیید |
|---|---|
| Modify کردن Core WordPress | **خیر** |
| وابستگی به Page Builder | **خیر** |
| PHP/JS خارجی غیرضروری | Google Fonts حذف شد؛ بدون Builder |
| استقلال بعد از Update وردپرس | Theme مستقل است |

**هدف Production پیشنهادی:** WordPress 6.4+ · PHP 8.1+ (ترجیحاً ۸.۲)

---

## ۲۳) Backup / Rollback

قبل از Go-live آماده شود:

- [ ] Backup کامل فایل‌ها  
- [ ] Backup Database  
- [ ] نگه‌داشتن نسخه Theme قبلی (۱.۴.۹ / ۱.۴.۱۰)  
- [ ] Rollback procedure مکتوب  

---

## ۲۴) Staging تحویلی

پس از آپلود ۱.۴.۱۰ روی محیط Staging، **URL Staging** ارسال می‌شود تا QA نهایی روی محیط واقعی انجام شود.  
در این مرحله Design تغییر نمی‌کند؛ فقط Bug/QA Fix.

---

## ۲۵) تحویل نهایی بعد از QA (چک‌لیست)

- [ ] Theme Release نهایی  
- [ ] Release version نهایی  
- [ ] Design Tokens هم‌نسخه  
- [ ] Change Log نهایی  
- [ ] Asset List  
- [ ] Redirect Map  
- [ ] SEO Migration Checklist  
- [ ] Staging QA Result  
- [ ] Backup / Deployment notes  

---

## سؤالات باز برای تصمیم کسب‌وکار / طراح

### الف) فرم رزرو و دو لوکیشن
مدل کاری کدام است؟
1. نوبت فقط تهران  
2. نوبت فقط نوشهر  
3. نوبت هر دو — نیاز به فیلد Location در فرم  
4. نوبت هر دو با ساعات متفاوت  

تا پاسخ، فیلد جدید اضافه نمی‌شود.

### ب) Badge Floating Contact
حذف شد تا گمراه‌کننده نباشد. اگر هدف دیگری مدنظر است، اعلام شود.

### ج) SEO Owner
Theme یا Plugin؟

### د) ماهیت شعبه نوشهر
مستقل برای Schema یا فقط محل خدمت ثانویه؟

---

## Change Log ۱.۴.۱۰

- Local Playfair Display WOFF2 (۶۰۰/۷۰۰) + حذف Google Fonts  
- `design-tokens.json` → version ۱.۴.۱۰ + functional colors + sourceOfTruth  
- حذف `doctor-charcoal-gold*`  
- Semantic tokens: `--color-error/success/muted` (+ bg)  
- فرم Success/Error بر پایه Tokenهای Semantic  
- حذف Badge قرمز Floating Contact  

**بدون تغییر:** Color Brand Tokens · Layout · Hero · Typography Scale · H1 · Services · Journey · FAQ

---

## جمع‌بندی

نسخه **۱.۴.۱۰** ادامه ۱.۴.۹ برای Staging است؛ Production Approval هنوز منوط به:

1. Staging QA واقعی (فرم، ایمیل، Responsive، A11y، Performance)  
2. تصمیم‌های کسب‌وکار (Location در فرم، Schema نوشهر، SEO Owner)  
3. SEO Migration / Redirect Map  
4. Mail Deliverability روی Host واقعی  

پس از بستن این موارد، Production Approval نهایی قابل صدور است.
