# گزارش نهایی QA و تحویل تم — نسخه ۱.۴.۹

**پروژه:** کلینیک دندانپزشکی دکتر کیوان علی‌پسندی  
**نسخه تم:** ۱.۴.۹  
**تاریخ:** ۱۳ اوت ۲۰۲۶  
**وضعیت:** Design System و UI نهایی شده‌اند. مرحله بعد: Staging QA واقعی + آماده‌سازی انتقال دامنه.

---

## ۱. خلاصه اجرایی

مسیر کار و موارد Locked مورد تأیید شما حفظ شده‌اند:

- Color Tokenهای مصوب
- Layout / Grid / Container
- Typography Scale
- ساختار Hero
- چهار Service اصلی
- FAQ و Journey
- H1 و Hierarchy سئو
- Assetهای پزشک و ایمپلنت
- Playfair Display برای لوگو
- عدم استفاده از رنگ قدیمی مشکی–طلایی
- Extend شدن Color System به صفحات داخلی

علاوه بر موارد بالا، در نسخه‌های ۱.۴.۷ تا ۱.۴.۹:

1. باقی‌مانده رنگ‌های قدیمی Hard-code حذف شد.
2. باگ‌های UI گزارش‌شده (کنتراست، هدر موبایل، منو، float تماس، تصویر About، CTAها) برطرف شد.
3. نقطه کنترل مرکزی Tokenها مشخص و تثبیت شد.

تم از نظر **کد و Design System** آماده Staging نهایی است.

---

## ۲. Design System و Color Token — تأیید نهایی

| بررسی | نتیجه |
|---|---|
| رنگ قدیمی مشکی–طلایی در Componentهای فعال | **حذف کامل** |
| تمام صفحات از Token مرکزی | **بله — site-wide** |
| Hard-code صفحه‌ای | **خیر** |

### Tokenهای نهایی (قفل‌شده)

| Token | مقدار |
|---|---|
| Brown Primary | `#4A3022` |
| Brown Light | `#684936` |
| Dark Espresso | `#2C1D16` |
| Cream Primary | `#F2E9DA` |
| Cream Light | `#F8F2E8` |
| Caramel Accent | `#C58D52` |

### نقطه کنترل مرکزی (Source of Truth)

هر تغییر رنگی آینده **فقط** از این دو نقطه:

1. `assets/design-tokens.json`
2. `assets/css/theme.css` → بلوک `:root`

---

## ۳. اصلاحات UI پس از بازخورد شما (۱.۴.۷ → ۱.۴.۹)

### ۳.۱ کنتراست و خوانایی

| مورد | اصلاح |
|---|---|
| صفحه تماس — عناوین و شماره روی پس‌زمینه روشن | متن Espresso روی کارت Cream Light |
| CTA تیره صفحات داخلی («برای انتخاب مسیر درمان…») | تیتر و متن روشن روی پس‌زمینه قهوه‌ای |
| CTA صفحه اصلی («برای دریافت مشاوره یا رزرو نوبت…») | کارت Cream + متن تیره (جدا از CTAهای تیره) |

### ۳.۲ تصویر About

- نسبت پرتره `3/4`
- `object-fit: cover`
- `object-position: center 10%`
- سر و صورت پزشک کامل در کادر؛ بدون کشیدگی غیرطبیعی

### ۳.۳ هدر و منوی موبایل

| مورد | اصلاح |
|---|---|
| جای همبرگر | راست (مناسب RTL) |
| جای لوگو | وسط نوار تیره |
| پوشش منو | Overlay تمام‌صفحه + backdrop تیره |
| فاصله آیتم‌های منو | یکسان (`min-height` و padding ثابت) |
| قفل Scroll هنگام باز بودن منو | فعال |
| Escape | بستن منو |

### ۳.۴ Floating Contact (دسکتاپ)

- موقعیت ثابت پایین‌**راست**
- با باز شدن جابه‌جا نمی‌شود
- آیتم‌ها به سمت **چپ** باز می‌شوند
- انیمیشن آهسته (~۰.۷ ثانیه)
- Badge قرمز «۱» روی دکمه

### ۳.۵ پاکسازی رنگ قدیمی (۱.۴.۷)

- `theme-color` در Header → فقط `#2C1D16`
- رنگ inline کارت بلاگ → `var(--color-caramel-accent)`

---

## ۴. Responsive

Breakpointهای تعریف‌شده:

| عرض | رفتار |
|---|---|
| ≤ ۵۵۹px | موبایل کوچک — فوتر/لوگو خواناتر |
| ≤ ۷۶۷px | Hamburger + Sticky CTA + Safe Area |
| ≥ ۷۶۸px | منوی دسکتاپ |
| ≥ ۱۲۰۰px | Hero سه‌ستونه / فوتر چندستونه |

### چک‌لیست تست واقعی روی Staging

لطفاً روی این عرض‌ها بررسی شود:

`320 · 360 · 375 · 390 · 414 · 430 · 768 · 1024 · 1440`

موارد مشترک در هر عرض:

- [ ] عدم Horizontal Scroll
- [ ] شکست صحیح تیترها
- [ ] Crop صحیح تصاویر Hero
- [ ] فاصله Header تا Hero (seamless)
- [ ] Sticky CTA موبایل + Safe Area
- [ ] Footer موبایل (لوگو، دو آدرس، لینک‌ها، CTA)
- [ ] ترتیب RTL مراحل Journey (۰۱ → ۰۲ → ۰۳)
- [ ] فرم رزرو

---

## ۵. Hero و تصاویر

| مورد | وضعیت |
|---|---|
| Layout Hero | **Locked** — بدون تغییر |
| پزشک | `doctor-keyvan-alipasandi.jpg` + WebP — Asset نهایی، بدون AI retouch روی چهره |
| ایمپلنت | `implant-hero.jpg` + WebP — بدون سنگ / ذرات طلایی / Glow تبلیغاتی |
| کلینیک | `clinic-interior.jpg` روشن‌شده + WebP |

---

## ۶. Logo و فونت

| مورد | وضعیت |
|---|---|
| Typeface | Playfair Display |
| Letter-spacing | حفظ‌شده |
| رقابت با آیکن دندان | کنترل‌شده (gap و وزن ثابت) |
| موبایل | سایز خوانا داخل نوار تیره |

پس از تأیید خوانایی روی دستگاه واقعی موبایل، Typography لوگو نیز **Locked** اعلام می‌شود.

---

## ۷. Icon System

| مورد | وضعیت |
|---|---|
| Emoji / Icon Font | **وجود ندارد** |
| منبع | فقط `alipasandi_icon()` → SVG در `inc/icons.php` |
| Style | Stroke یکسان · `currentColor` · Accent Caramel |
| سطوح | Header · Service Card · Trust · Journey · Footer · Form |

---

## ۸. فرم رزرو نوبت

| مورد | وضعیت کد |
|---|---|
| مسیر ارسال | `admin-post.php` → **wp_mail** |
| Nonce | ✓ |
| Honeypot | ✓ |
| Validation | فیلدهای الزامی + sanitize |
| Success / Error | از طریق `form_status` |
| جمله قانونی | باکس Caramel ظریف — متن Espresso روی زمینه روشن |

**الزامی روی Staging قبل از Production:**

- [ ] Submit واقعی و دریافت ایمیل
- [ ] عدم رفتن ایمیل به Spam (SPF/DKIM هاست)
- [ ] تست Error State
- [ ] خوانایی جمله قانونی در موبایل

---

## ۹. Header و Footer

**Header:** Sticky · منوی موبایل Overlay کامل · Scroll lock · Escape  

**Footer موبایل ۳۹۰ — موارد قابل بررسی:**

- لوگو Playfair
- شماره تماس
- آدرس تهران
- آدرس نوشهر + لینک نقشه
- لینک‌ها و CTA
- خوانایی کافی

*لطفاً یک Screenshot جداگانه از Footer موبایل ۳۹۰px از Staging ضمیمه شود.*

---

## ۱۰. Dual Location

| محل | نمایش |
|---|---|
| Hero | «تهران و نوشهر» — فشرده، بدون شلوغی پیام اصلی |
| Footer | دو خط جدا + لینک نقشه نوشهر |
| Contact | دو بلوک آدرس جدا + لینک نقشه |
| Schema | آدرس اصلی تهران در LocalBusiness (جلوگیری از آدرس اشتباه) |

لینک نقشه نوشهر:  
`https://maps.app.goo.gl/ycPem67fpYbvgdXZ7?g_st=atm`

---

## ۱۱. صفحات داخلی

همه از Header / Footer / Color System / Button / Card / Spacing / Icon مشترک استفاده می‌کنند:

**About · Services · Implant · Crown · Surgery · General · Contact · Appointment · FAQ · Blog · 404**

صفحات خدمات فقط **Content** متفاوت دارند (از `inc/service-data.php`)؛ UI از `template-parts/service-detail.php` مشترک است.

---

## ۱۲. SEO قبل از انتقال دامنه

قبل از go-live این موارد باید آماده شود:

| مورد | وضعیت |
|---|---|
| URL Inventory سایت فعلی | باید تهیه شود |
| URLهای جدید | بر اساس ساختار صفحات تم |
| Redirect Map 301 | قبل از انتقال |
| Title / Meta فعلی | Inventory |
| صفحات Index‌شده | Search Console |
| Sitemap جدید | پس از محتوا |
| Schema تکراری Theme + Plugin | بررسی روی Staging |
| Canonical / Breadcrumb | در تم پیاده شده — تأیید روی Staging |

---

## ۱۳. Performance

| مورد | وضعیت کد |
|---|---|
| WebP پزشک / ایمپلنت / کلینیک | ✓ نسخه‌های responsive |
| sizes / srcset | از طریق تابع تصویر تم |
| فونت | Vazirmatn محلی + Playfair با `display=swap` |
| LCP Hero | `fetchpriority` روی پزشک — اندازه‌گیری روی Staging |
| CLS | ابعاد تصویر و font-swap — اندازه‌گیری روی Staging |

---

## ۱۴. Staging Checklist (قبل از Production)

- [ ] فرم رزرو واقعی + ایمیل + Spam check
- [ ] منوی موبایل (Overlay / Escape / Scroll lock / Keyboard)
- [ ] Accessibility + Focus + Screen Reader پایه
- [ ] Responsive تمام عرض‌های بخش ۴
- [ ] Performance (LCP / CLS / INP)
- [ ] SEO Technical (Schema یکتا، Canonical، Breadcrumb)
- [ ] Screenshot Footer موبایل ۳۹۰
- [ ] خوانایی لوگو Playfair روی گوشی واقعی
- [ ] Redirect Map آماده‌شده (اجرا پس از دامنه جدید)

---

## ۱۵. Change Log نسخه‌به‌نسخه

### ۱.۴.۲
- تثبیت پالت Brown / Cream / Caramel روی Home
- Integration تصاویر Hero موبایل
- Asset ایمپلنت جدید
- آمار ۱۰٬۰۰۰ خاموش

### ۱.۴.۳
- فونت لوگو Playfair Display
- Micro-adjustment موبایل Hero
- Shadow/Border کارت خدمات
- Trust / Journey emphasis
- تصویر کلینیک روشن‌تر
- Footer موبایل خواناتر
- لوکیشن دوم نوشهر

### ۱.۴.۴
- H1 → **مهارت، برآمده از تجربه**
- حذف eyebrow CTA پایین
- اعداد Journey با Playfair
- حذف فاصله Header→Hero
- Sticky CTA + Safe Area
- تأیید SVG icons و ترتیب RTL Journey

### ۱.۴.۵
- Extend پالت به **تمام صفحات**
- Header / Footer / section-dark یکدست site-wide

### ۱.۴.۶
- باکس قانونی فرم رزرو (Caramel)
- خط موی Caramel زیر Header

### ۱.۴.۷
- حذف کامل Hard-code رنگ قدیمی
- به‌روزرسانی `design-tokens.json` (scope site-wide)

### ۱.۴.۸
- کنتراست صفحه تماس
- CTA تیره صفحات داخلی (متن روشن)
- تصویر About (crop سر)
- Float تماس پایین‌راست
- فاصله یکسان منوی موبایل
- Overlay کامل منو + backdrop
- همبرگر راست / لوگو وسط

### ۱.۴.۹ (جاری)
- CTA صفحه اصلی: کارت Cream + متن تیره (جدا از CTAهای تیره)
- Float: ثابت، باز شدن به چپ، انیمیشن آهسته، Badge
- هدر موبایل: همبرگر داخل نوار تیره، لوگو وسط

---

## ۱۶. فایل‌های تحویل

| مورد | فایل |
|---|---|
| Theme نهایی | `alipasandi-clinic-1.4.9.zip` |
| Design Tokens | `assets/design-tokens.json` + `:root` در `theme.css` |
| Componentهای مشترک | Header · Footer · Button · Service Card · Service Template · Journey · FAQ · CTA · Form · Trust · Breadcrumb · Icons · Mobile Action Bar |
| Assetهای اصلی | `assets/images/` (پزشک، ایمپلنت، کلینیک، favicon) |
| این گزارش | `CLIENT-REPORT-1.4.9.md` |

---

## ۱۷. دامنه و انتقال

برای حفظ سئوی فعلی پیشنهاد می‌شود:

1. ثبت دامنه **`drkeyvanalipasandi.ir`** به نام پزشک در **NIC.ir** (فعال‌سازی ممکن است چند روز طول بکشد — هرچه زودتر بهتر).
2. پس از Staging QA و تأیید نهایی:
   - انتقال کامل روی دامنه جدید
   - Redirect Map کامل ۳۰۱
   - به‌روزرسانی Search Console / Sitemap / Canonical

**سؤال:** آیا حساب NIC.ir دارید؟ در صورت تمایل می‌توانید خودتان ثبت کنید یا دسترسی بدهید تا به نمایندگی انجام شود.

---

## ۱۸. جمع‌بندی

از نظر **Design System، Color Token، Layout Locked، Componentها و اصلاحات UI** تم در نسخه **۱.۴.۹** نهایی شده است.

موارد باقی‌مانده همگی از نوع **QA روی Staging واقعی** هستند:

- تست فرم و ایمیل
- Screenshotهای Responsive و Footer موبایل
- Performance
- SEO Inventory و Redirect Map
- ثبت دامنه

پس از تکمیل چک‌لیست بخش ۱۴ و تأیید شما، سایت آماده انتقال به دامنه جدید و اجرای Redirectهای ۳۰۱ خواهد بود.
