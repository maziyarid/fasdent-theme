# پاسخ QA نهایی — نسخه ۱.۴.۷

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**وضعیت:** موارد فنی کد تأیید/اصلاح شد. Staging QA واقعی (فرم ایمیل، دستگاه موبایل، Performance با افزونه) باید روی هاست Staging انجام شود.

---

## وضعیت کلی

مسیر کار و موارد Locked مورد تأیید شما حفظ شده‌اند. در بررسی نهایی کد دو مورد باقی‌مانده از رنگ قدیمی پیدا و اصلاح شد:

1. `theme-color` متا در Header → فقط `#2C1D16` (Espresso)
2. رنگ inline کارت بلاگ → `var(--color-caramel-accent)`

نسخه تحویلی: **۱.۴.۷**

---

## ۱) Design System و Color Token

**تأیید نهایی:**

| بررسی | نتیجه |
|---|---|
| رنگ قدیمی مشکی–طلایی در Componentهای فعال | **حذف شد** (هیچ `#101010` / `#d6a54a` / `#e8c176` / `#835b16` باقی نمانده) |
| تمام صفحات از Token مرکزی | **بله** — site-wide از ۱.۴.۵ |
| Hard-code صفحه‌ای | **خیر** — فقط از CSS variables |

**Tokenهای نهایی (قفل):**

| Token | مقدار |
|---|---|
| Brown Primary | `#4A3022` |
| Brown Light | `#684936` |
| Dark Espresso | `#2C1D16` |
| Cream Primary | `#F2E9DA` |
| Cream Light | `#F8F2E8` |
| Caramel Accent | `#C58D52` |

**نقطه کنترل مرکزی (Source of Truth):**

1. `assets/design-tokens.json` — مرجع canonical مقادیر  
2. `assets/css/theme.css` → بلوک `:root` — پیاده‌سازی CSS (متغیرهای `--color-*` و aliasهای معنایی `--charcoal` / `--gold` / … که همه به همان Tokenها map می‌شوند)

هر تغییر رنگی آینده **فقط** از این دو نقطه انجام شود.

---

## ۲) Responsive

Breakpointهای تعریف‌شده در تم:

| عرض | رفتار |
|---|---|
| ≤ ۵۵۹px | موبایل کوچک |
| ≤ ۷۶۷px | Hamburger + Sticky CTA |
| ≥ ۷۶۸px | منوی دسکتاپ |
| ≥ ۱۲۰۰px | Hero سه‌ستونه |

**چک‌لیست تست واقعی (روی Staging با دستگاه/DevTools):**

| عرض | Horizontal Scroll | تیتر | Crop تصویر | Header→Hero | Sticky CTA | Footer | Journey RTL | فرم |
|---|---|---|---|---|---|---|---|---|
| ۳۲۰ | باید صفر | ✓ | ✓ | seamless | Safe Area | خوانا | ۰۱→۰۳ | ✓ |
| ۳۶۰ | باید صفر | ✓ | ✓ | seamless | Safe Area | خوانا | ۰۱→۰۳ | ✓ |
| ۳۷۵ | باید صفر | ✓ | ✓ | seamless | Safe Area | خوانا | ۰۱→۰۳ | ✓ |
| ۳۹۰ | باید صفر | ✓ | ✓ | seamless | Safe Area | خوانا | ۰۱→۰۳ | ✓ |
| ۴۱۴ | باید صفر | ✓ | ✓ | seamless | Safe Area | خوانا | ۰۱→۰۳ | ✓ |
| ۴۳۰ | باید صفر | ✓ | ✓ | seamless | Safe Area | خوانا | ۰۱→۰۳ | ✓ |
| ۷۶۸ | باید صفر | ✓ | ✓ | seamless | مخفی | ۲ستونه | ۰۱→۰۳ | ✓ |
| ۱۰۲۴ | باید صفر | ✓ | ✓ | seamless | مخفی | ✓ | ۰۱→۰۳ | ✓ |
| ۱۴۴۰ | باید صفر | ✓ | ✓ | seamless | مخفی | ۴ستونه | ۰۱→۰۳ | ✓ |

**نکته:** QA عرض‌های بالا باید روی Staging واقعی با سخت‌افزار/Chrome DevTools ثبت شود و Screenshotهای Breakpoint اصلی ضمیمه گردد. در کد: overflow افقی کنترل شده، Safe Area برای Sticky CTA، و ترتیب RTL Journey از Grid پیش‌فرض RTL است.

---

## ۳) Hero و تصاویر

| مورد | وضعیت |
|---|---|
| Layout Hero | **Locked** — بدون تغییر |
| پزشک | `doctor-keyvan-alipasandi.jpg` (+ WebP) — همان Asset نهایی؛ بدون AI retouch روی پیکسل چهره؛ Crop فقط CSS |
| ایمپلنت | `implant-hero.jpg` (+ WebP) — تیتانیوم/سرامیک؛ بدون سنگ، ذرات طلایی، Glow شدید، تزئینات اضافه |
| کلینیک | `clinic-interior.jpg` روشن‌شده (+ WebP) |

---

## ۴) Logo و فونت

| مورد | وضعیت |
|---|---|
| Typeface | Playfair Display — تأیید شما |
| Letter-spacing | محدود و حفظ‌شده |
| رقابت با آیکن دندان | آیکن و wordmark با gap ثابت؛ وزن ۶۰۰ |
| موبایل ≤۵۵۹ | strong ≈ ۰.۸۸–۰.۹۵rem برای خوانایی |

پس از تأیید خوانایی روی دستگاه واقعی موبایل، **Typography لوگو نیز Locked** اعلام می‌شود.

---

## ۵) Icon System

| مورد | وضعیت |
|---|---|
| Emoji / Icon Font | **وجود ندارد** (جستجوی کد: صفر) |
| منبع | فقط `alipasandi_icon()` → SVG stroke در `inc/icons.php` |
| Style | stroke یکسان، `currentColor`، Accent از `--gold` / Caramel |
| سطوح | Header · Service Card · Trust · Journey · Footer · Form — همه از همان تابع |

---

## ۶) فرم رزرو

پیاده‌سازی کد:

| مورد | وضعیت کد |
|---|---|
| مسیر ارسال | `admin-post.php` → `alipasandi_appointment` → **wp_mail** |
| Nonce | ✓ |
| Honeypot | ✓ |
| Validation | required fields + sanitize |
| Success / Error | از طریق `form_status` در URL |

**باقی‌مانده روی Staging واقعی (الزامی قبل از Production):**
- Submit واقعی و دریافت ایمیل
- عدم رفتن ایمیل به Spam (SPF/DKIM هاست)
- تست Error (فیلد خالی / mail failure)

---

## ۷) جمله قانونی فرم

| مورد | وضعیت |
|---|---|
| متن | «تاریخ و ساعت انتخاب‌شده پیشنهادی است و نوبت پس از تماس کلینیک قطعی خواهد شد.» |
| نمایش | باکس `.form-confirmation-note` با border/background Caramel ظریف |
| Caramel | فقط Accent سطح باکس — متن Espresso روی زمینه روشن |
| موبایل | font-size ۰.۸۲rem · line-height ۱.۷۵ — خوانا |

---

## ۸) Header و Footer

**Header:**
- Sticky ✓
- منوی موبایل: Overlay، `body.menu-is-open { overflow: hidden }` (قفل Scroll)، Escape در JS، aria-expanded

**Footer موبایل ۳۹۰ — موارد قابل بررسی روی Staging:**
- لوگو Playfair
- شماره تماس
- آدرس تهران
- آدرس نوشهر + لینک نقشه
- لینک‌های خدمات / دسترسی سریع
- CTA رزرو
- سایز فونت افزایش‌یافته در ≤۵۵۹px

*Screenshot Footer موبایل باید از Staging واقعی گرفته و ضمیمه شود.*

---

## ۹) Dual Location

| محل | نمایش |
|---|---|
| Hero | «تهران و نوشهر» + جزئیات فشرده (بدون شلوغی پیام اصلی) |
| Footer | دو خط جدا + لینک نقشه نوشهر |
| Contact | دو بلوک آدرس جدا + لینک نقشه |
| Schema | فعلاً آدرس اصلی تهران در LocalBusiness (جلوگیری از آدرس اشتباه) |

لینک نقشه: `https://maps.app.goo.gl/ycPem67fpYbvgdXZ7?g_st=atm`

---

## ۱۰) صفحات داخلی

همه از Header / Footer / Color System / Button / Card / Spacing / Icon مشترک استفاده می‌کنند:

About · Services · Implant · Crown · Surgery · General · Contact · Appointment · FAQ · Blog (single) · 404

صفحات خدمات فقط **Content** از `inc/service-data.php` متفاوت است؛ UI از `template-parts/service-detail.php` مشترک است.

---

## ۱۱) SEO قبل از انتقال دامنه

**آماده‌سازی لازم (خارج از تم، روی محتوا/هاست):**

| مورد | مسئول |
|---|---|
| URL Inventory سایت فعلی | تیم / Staging |
| URLهای جدید | بر اساس ساختار صفحات تم |
| Redirect Map 301 | قبل از go-live |
| Title / Meta فعلی | Inventory |
| صفحات Index‌شده | Search Console |
| Sitemap جدید | پس از محتوا |
| Schema تکراری Theme + Plugin | بررسی روی Staging |
| Canonical / Breadcrumb | در تم پیاده شده؛ تأیید روی Staging |

---

## ۱۲) Performance

| مورد | وضعیت کد |
|---|---|
| WebP پزشک / ایمپلنت / کلینیک | ✓ نسخه‌های responsive موجود |
| sizes / srcset | از طریق `alipasandi_theme_image()` |
| فونت | Vazirmatn محلی + Playfair با `display=swap` |
| LCP Hero | fetchpriority high روی پزشک؛ تأیید روی Staging |
| CLS | ابعاد تصویر و فونت swap — اندازه‌گیری روی Staging |

---

## ۱۳) Staging Checklist (قبل از Production)

- [ ] فرم رزرو واقعی + ایمیل + Spam check
- [ ] منوی موبایل (Overlay / Escape / Scroll lock / Keyboard)
- [ ] Accessibility + Focus + Screen Reader پایه
- [ ] Responsive تمام عرض‌های بخش ۲
- [ ] Performance (LCP / CLS / INP)
- [ ] SEO Technical (Schema یکتا، Canonical، Breadcrumb)
- [ ] Redirect Map آماده‌شده (اجرا پس از دامنه جدید)

---

## ۱۴) فایل‌های تحویل نهایی این مرحله

| مورد | فایل |
|---|---|
| Theme نهایی | `alipasandi-clinic-1.4.7.zip` |
| Design Tokens | `assets/design-tokens.json` + `:root` در `theme.css` |
| لیست Componentها | بخش ۳ گزارش FINAL و این سند |
| Assetهای اصلی | داخل `assets/images/` |
| لیست تغییرات نسخه‌ها | `REPORT-FINAL-1.4.6.md` + این Change در ۱.۴.۷ |
| Figma | در صورت وجود نسخه نهایی جداگانه ضمیمه شود |
| Deployment / Backup | روی Staging: بکاپ DB + فایل‌ها قبل از آپلود تم |

### Change ۱.۴.۷ (نسبت به ۱.۴.۶)
- حذف کامل باقی‌مانده رنگ قدیمی در `header.php` (theme-color) و `post-card.php`
- به‌روزرسانی `design-tokens.json` به version ۱.۴.۷ و scope site-wide
- Version bump تم

---

## جمع‌بندی برای Final Production

از نظر **کد و Design System** تم آماده Staging نهایی است. موارد باقی‌مانده همگی از نوع **QA روی هاست واقعی** هستند (فرم ایمیل، Screenshotهای Breakpoint، Performance، SEO Inventory، دامنه NIC.ir).

پس از تکمیل چک‌لیست ۱۳ و تأیید Screenshotهای Responsive + Footer موبایل، سایت آماده انتقال به دامنه جدید و اجرای Redirectهای ۳۰۱ خواهد بود.
