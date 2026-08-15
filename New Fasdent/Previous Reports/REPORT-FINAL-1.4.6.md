# گزارش نهایی تم — کلینیک دندانپزشکی دکتر کیوان علی‌پسندی

**نسخه جاری:** ۱.۴.۶  
**تاریخ:** ۱۳ اوت ۲۰۲۶  
**وضعیت:** Color System و Componentها به تمام صفحات Extend شده‌اند. Home و صفحات داخلی یکپارچه و آماده Staging QA نهایی هستند.

---

## ۱. خلاصه اجرایی

تم WordPress RTL کلینیک بر پایه شش Color Token مصوب (Brown / Cream / Caramel) تثبیت و به **تمام صفحات** Extend شد. تغییرات از نسخه ۱.۴.۲ تا ۱.۴.۶ فقط شامل اصلاحات تأییدشده کلاینت، یکپارچه‌سازی رنگ در صفحات داخلی، بهبود خوانایی/فرم/فاصله‌ها و آماده‌سازی برای انتقال دامنه است.

**آنچه نهایی شده:**
- پالت رنگ site-wide (بدون رنگ قدیمی مشکی–طلایی Hard-coded)
- H1 نهایی Home: **مهارت، برآمده از تجربه**
- لوکیشن دوگانه تهران + نوشهر
- آیکن‌های SVG یکدست
- فونت لوگو Playfair Display
- Service Template مشترک برای صفحات خدمات
- فرم رزرو با جمله قانونی بصری و ارسال واقعی (wp_mail)
- Header / Footer / CTA / FAQ / Journey یکدست در همه صفحات

**آماده برای:** Staging QA کامل → ثبت دامنه `drkeyvanalipasandi.ir` → انتقال و Redirect ۳۰۱.

---

## ۲. Design Tokens نهایی (قفل‌شده)

| Token | مقدار | کاربرد |
|---|---|---|
| Brown Primary | `#4A3022` | Hero و سکشن‌های قهوه‌ای |
| Brown Light | `#684936` | عمق ملایم |
| Dark Espresso | `#2C1D16` | Header، Footer، سکشن تیره، متن روی Cream |
| Cream Primary | `#F2E9DA` | پس‌زمینه سکشن روشن |
| Cream Light | `#F8F2E8` | سطح کارت‌ها |
| Caramel Accent | `#C58D52` | فقط Accent: CTA، آیکن، هایلایت، اعداد Journey |

**قوانین کنتراست:**  
Espresso روی Cream ✓ · Cream روی Brown ✓ · Espresso روی Caramel ✓ (متن UI) · Caramel روی Cream برای متن معمولی ✗ · Caramel روی Brown برای متن کوچک ✗

**Typography:** Vazirmatn (متن فارسی) + Playfair Display (فقط لوگو و اعداد Journey)  
**Container:** max-width ۱۳۲۰px · padding ۶۰ / ۳۲ / ۱۶  
**Radius:** کارت ۱۲px · دکمه ۸px · حداقل ارتفاع دکمه ۴۸px  
**Shadow:** لایه‌ای ملایم و خنثی  

**Breakpointها:**
| عرض | رفتار |
|---|---|
| ≤ ۵۵۹px | موبایل کوچک — لوگو/متن فوتر خواناتر |
| ≤ ۷۶۷px | Hamburger + Sticky CTA + Safe Area |
| ≥ ۷۶۸px | منوی دسکتاپ |
| ≥ ۱۲۰۰px | Hero سه‌ستونه و فوتر ۴ستونه |

---

## ۳. Componentهای مشترک (Extend شده)

| Component | کاربرد |
|---|---|
| Header | Sticky RTL + منوی موبایل Overlay + قفل Scroll |
| Footer | Espresso + لوکیشن دوگانه + لینک‌ها + CTA |
| Mobile Action Bar | تماس / رزرو (با Safe Area) |
| Button | Primary Caramel / Outline Light / Outline Dark |
| Service Card | کارت خدمات Home و لیست خدمات |
| Service Template | قالب مشترک صفحات ایمپلنت / روکش / جراحی / عمومی |
| Journey Card | مراحل ۰۱–۰۳ با عدد Playfair |
| FAQ Accordion | آکاردیون قابل Crawl |
| CTA / Appointment Banner | بنر رزرو مشترک |
| Form fields + Booking shell | فرم چندمرحله‌ای رزرو |
| Trust strip | نوار اعتماد About / Service |
| Breadcrumbs | مسیر صفحات داخلی |
| Icons | SVG خطی یکدست (Accent Caramel) |

---

## ۴. Assetهای نهایی

| نقش | فایل |
|---|---|
| پزشک | `doctor-keyvan-alipasandi.jpg` + نسخه‌های WebP (۴۸۰ / ۷۶۸ / ۱۱۵۰) |
| ایمپلنت Hero | `implant-hero.jpg` + WebP (۴۸۰ / ۷۶۸ / ۹۲۰) |
| کلینیک | `clinic-interior.jpg` (روشن‌شده) + WebP (۴۸۰ / ۷۶۸ / ۹۰۰) |
| Favicon | `favicon.svg` + `favicon-32.png` + `apple-touch-icon.png` |
| آیکن‌ها | همه در `inc/icons.php` به‌صورت SVG |

Desktop و Mobile از **همان** Asset اصلی استفاده می‌کنند (فقط CSS crop/mask). نسخه AI جداگانه در تم وجود ندارد.

---

## ۵. Change Log نسخه‌به‌نسخه

### نسخه ۱.۴.۲ (پایه تأیید رنگ)
- تثبیت پالت Brown / Cream / Caramel روی Home
- Integration تصاویر Hero موبایل (blend CSS بدون دست‌کاری پیکسل چهره)
- Asset ایمپلنت جدید (تیتانیوم + تاج سرامیکی، بدون سنگ/طلا)
- Rename عکس پزشک به `doctor-keyvan-alipasandi`
- QA موبایل ۳۲۰–۴۳۰ بدون Horizontal Overflow
- آمار ۱۰٬۰۰۰ خاموش

### نسخه ۱.۴.۳
- فونت لوگو: **Playfair Display** (جایگاه/سایز ثابت)
- Mobile Hero: micro-adjustment فاصله تیتر/توضیح/تصاویر + blend طبیعی‌تر
- کارت خدمات: border و shadow ظریف‌تر و Premium
- Trust / Journey: تأکید بصری بیشتر روی آیکن و اعداد (بدون شلوغی)
- تصویر کلینیک روشن‌تر (asset + filter)
- Footer موبایل: خوانایی لوگو و متن‌ها بهتر
- **لوکیشن دوم:** نوشهر، ستارخان، امیراد ۱، طبقه ۵ + لینک نقشه  
  در Hero (فشرده)، Footer و صفحه تماس

### نسخه ۱.۴.۴
- H1 Home → **مهارت، برآمده از تجربه**
- حذف eyebrow «مشاوره و رزرو نوبت» از CTA پایین (هم‌ترازی متن با آیکن و دکمه)
- تنظیم سایز متن CTA رزرو
- اعداد Journey با Playfair Display (هم‌فونت لوگو)
- حذف فاصله کرم‌رنگ Header → Hero (اتصال منسجم)
- هماهنگی Bottom spacing موبایل با Sticky CTA و Safe Area
- تأیید آیکن‌ها: همه SVG (بدون Emoji)
- تأیید ترتیب RTL مراحل ۰۱ → ۰۲ → ۰۳ در همه Breakpointها

### نسخه ۱.۴.۵
- **Extend پالت مصوب به تمام صفحات** (خروج از حالت فقط-Home)
- Header / Footer / section-dark / focus / body پایه یکدست در کل سایت
- هیچ رنگ قدیمی مشکی–طلایی Hard-coded روی Componentهای فعال باقی نماند
- صفحات داخلی تحت پوشش کامل:  
  About · Services · Implant · Crown · Surgery · General · Contact · Appointments · FAQ · Single · 404
- Dual location در Contact و Footer حفظ و تقویت شد

### نسخه ۱.۴.۶ (جاری)
- باکس ظریف Caramel برای **جمله قانونی فرم رزرو**  
  («تاریخ و ساعت انتخاب‌شده پیشنهادی است و نوبت پس از تماس کلینیک قطعی خواهد شد.»)
- خط موی ۱px Caramel زیر Header در Home برای اتصال seamless به Hero
- تثبیت نهایی Tokenها و Componentها برای Staging

---

## ۶. Change Log صفحه‌به‌صفحه (وضعیت ۱.۴.۶)

| صفحه | فایل اصلی | وضعیت |
|---|---|---|
| Home | `front-page.php` | H1 نهایی، Hero، Services، Treatment، About+Trust، Journey، FAQ، CTA، Dual location |
| درباره ما | `page-about.php` | رنگ site-wide، تصویر پزشک، ساختار ارزش‌ها |
| خدمات (لیست) | `page-services.php` | کارت‌ها با border/shadow جدید |
| ایمپلنت | `page-implant.php` + `service-detail.php` | Service Template کامل |
| روکش | `page-crown.php` + `service-detail.php` | Service Template کامل |
| جراحی و لثه | `page-surgery.php` + `service-detail.php` | Service Template کامل |
| درمان عمومی | `page-general.php` + `service-detail.php` | Service Template کامل |
| تماس | `page-contact.php` | دو آدرس + لینک نقشه نوشهر + فرم |
| رزرو نوبت | `page-appointments.php` | فرم چندمرحله‌ای + جمله قانونی در باکس + wp_mail |
| سوالات متداول | `page-faq.php` | Accordion مشترک |
| بلاگ / تک‌نوشته | `single.php` | Header/Footer و رنگ یکدست |
| ۴۰۴ | `404.php` | رنگ و CTA یکدست |
| Header / Footer | `header.php` / `footer.php` | مشترک همه صفحات |

**هسته مشترک تغییر یافته در همه صفحات:**
- `assets/css/theme.css` — Tokenها site-wide، spacing، shadow، legal note، header hairline
- `functions.php` — Version، فونت لوگو، گزینه‌های آدرس دوم
- `inc/icons.php` — SVG یکدست
- `inc/seo.php` — Schema پایه
- `inc/forms.php` — رزرو و تماس
- `inc/service-data.php` — محتوای چهار خدمت
- `inc/images.php` — Assetهای تأییدشده

---

## ۷. موارد Locked (بدون تغییر از این به بعد مگر درخواست صریح)

- شش Color Token مصوب
- Layout / Grid / Container Home
- Composition سه‌قسمتی Hero دسکتاپ
- Typography Scale اصلی
- چهار Service (بدون افزودن خدمت جدید)
- ساختار Treatment / About / Journey / FAQ
- H1 نهایی Home و سلسله‌مراتب SEO
- تصاویر تأییدشده پزشک و ایمپلنت
- Playfair Display برای لوگو
- آمار ۱۰٬۰۰۰ کیس (خاموش تا تأیید مستندات)

---

## ۸. فرم رزرو

- مسیر: `admin-post.php` → `inc/forms.php` → **wp_mail**
- Nonce + honeypot
- حالت‌های Success / Error از طریق query string
- جمله قانونی در Hero صفحه و در باکس Caramel پایین فرم
- **الزامی روی Staging:** یک Submit واقعی و تأیید رسیدن ایمیل

---

## ۹. SEO و Schema

- هر صفحه یک H1
- `lang="fa"` + `dir="rtl"`
- Breadcrumb در صفحات داخلی
- Schema در Theme: LocalBusiness / Dentist (آدرس تهران) + BreadcrumbList
- FAQ به‌صورت HTML واقعی (قابل Crawl)
- در Staging بررسی شود که افزونه SEO خارجی Schema تکراری تولید نکند

**قبل از Production لازم است:**
- URL Inventory (قدیم → جدید)
- Title / Meta فعلی
- لیست صفحات Index‌شده
- Redirect Map کامل ۳۰۱

---

## ۱۰. دامنه و انتقال

سایت فعلی (مثلاً fasdent.ir) برای نام دکتر در Google دیده می‌شود. برای انتقال تمیز:

1. ثبت دامنه **`drkeyvanalipasandi.ir`** به نام خود پزشک در **NIC.ir**  
   - فعال‌سازی `.ir` معمولاً چند روز کاری طول می‌کشد → **هرچه زودتر شروع شود بهتر است.**  
   - سؤال از کارفرما: آیا حساب NIC.ir دارید؟ در صورت تمایل می‌توانید خودتان ثبت کنید یا دسترسی بدهید تا به نمایندگی انجام شود.

2. پس از Staging QA و تأیید نهایی تم:
   - انتقال کامل روی دامنه جدید
   - Redirect ۳۰۱ از تمام URLهای قدیمی
   - به‌روزرسانی Search Console / Sitemap / Canonical

---

## ۱۱. Staging QA Checklist (قبل از Production)

- [ ] فرم رزرو: Submit واقعی + دریافت ایمیل + Error/Success
- [ ] منوی موبایل: باز/بسته، Focus Trap، Keyboard، Escape، قفل Scroll
- [ ] Accessibility پایه (Focus ring دو‌رنگ، Screen Reader)
- [ ] عدم Horizontal Scroll در ۳۲۰ / ۳۶۰ / ۳۷۵ / ۳۹۰ / ۴۱۴ / ۴۳۰ / ۷۶۸ / ۱۴۴۰
- [ ] Mobile LCP / CLS / INP (پزشک و ایمپلنت WebP + sizes)
- [ ] خوانایی لوگو Playfair روی دستگاه واقعی موبایل
- [ ] Footer موبایل ۳۹۰: لوگو، دو آدرس، لینک‌ها، CTA
- [ ] Tablet ~۷۶۸: Header، Hero، کارت خدمات، Journey
- [ ] Schema بدون تکرار با افزونه SEO
- [ ] Dual location در Contact و Footer

---

## ۱۲. فایل‌های تحویل این مرحله

| فایل | توضیح |
|---|---|
| `alipasandi-clinic-1.4.6.zip` | تم کامل WordPress نسخه جاری |
| `REPORT-FINAL-1.4.6.md` | این گزارش (خلاصه + Tokens + Componentها + Change Log کامل) |

---

## ۱۳. مسیر پیشنهادی تا Final

1. آپلود ۱.۴.۶ روی Staging و hard-refresh / purge cache  
2. اجرای Staging QA بر اساس چک‌لیست بالا  
3. اقدام ثبت دامنه `drkeyvanalipasandi.ir`  
4. تهیه SEO Migration Plan و Redirect Map  
5. پس از تأیید کلاینت → انتقال Production + Redirect کامل  

پس از تأیید بصری و QA، تم از نظر طراحی و ساختار **Final** محسوب می‌شود و فقط کارهای دامنه، Redirect و مانیتورینگ سئو باقی می‌ماند.
