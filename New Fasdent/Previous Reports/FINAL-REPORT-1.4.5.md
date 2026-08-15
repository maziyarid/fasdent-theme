# گزارش نهایی تم کلینیک دکتر کیوان علی‌پسندی — نسخه ۱.۴.۵

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**وضعیت:** Color System و Componentها به تمام صفحات Extend شدند. Home قبلاً Final شده بود.  
**دامنه Staging فعلی:** fasdent.ir (در زمان بررسی از این محیط موقتاً unreachable بود؛ لطفاً از سمت خودتان یک بار hard-refresh بزنید)

---

## ۱. خلاصه اجرایی

- پالت تأییدشده Brown / Cream / Caramel از حالت «فقط Home» خارج و **site-wide** شد.
- هیچ رنگ قدیمی مشکی–طلایی (`#101010` / `#d6a54a`) به‌صورت Hard-coded در Componentهای فعال باقی نمانده.
- H1 نهایی Home: **مهارت، برآمده از تجربه**
- لوکیشن دوگانه (تهران + نوشهر) در Hero، Footer و Contact
- تمام آیکن‌ها SVG خطی یکدست (بدون Emoji)
- Playfair Display فقط برای لوگو و اعداد Journey
- Sticky CTA موبایل و فاصله Header→Hero اصلاح شده
- فرم رزرو، Schema پایه، Header/Footer مشترک در همه صفحات یکسان

**آماده برای:** Staging QA کامل → ثبت دامنه drkeyvanalipasandi.ir → انتقال و Redirect کامل.

---

## ۲. Change Log جامع (فایل‌ها و صفحات)

### ۲.۱ فایل‌های هسته (همه صفحات)

| فایل | تغییر |
|------|--------|
| `style.css` | Version → **1.4.5** |
| `functions.php` | Version 1.4.5؛ Google Font Playfair؛ Dual location options (`clinic_address_2`, `clinic_maps_2`)؛ enqueue font |
| `assets/css/theme.css` | پالت مصوب به `:root` منتقل شد (site-wide)؛ header/footer/section-dark/focus/body پایه یکدست؛ فاصله Header–Hero حذف؛ spacing موبایل برای Sticky CTA؛ shadow/border کارت‌ها Premium؛ Trust و Journey emphasis؛ appointment h2 size؛ حذف وابستگی body.home برای رنگ‌های ساختاری |
| `assets/design-tokens.json` | مرجع canonical شش Token |
| `header.php` | بدون تغییر ساختاری؛ از brand_logo مشترک استفاده می‌کند |
| `footer.php` | متن «تهران و نوشهر»؛ دو آدرس + لینک نقشه نوشهر؛ Playfair روی لوگو |
| `inc/icons.php` | SVG خالص (بدون تغییر محتوا؛ تأیید یکدستی) |
| `inc/seo.php` | Schema LocalBusiness/Dentist + Breadcrumb (آدرس اصلی تهران) |
| `inc/forms.php` | فرم‌ها از طریق admin-post + wp_mail |
| `inc/images.php` | Assetهای تأییدشده پزشک/ایمپلنت/کلینیک |

### ۲.۲ صفحه اصلی (`front-page.php`)

- H1 → «مهارت، برآمده از تجربه»
- حذف eyebrow «مشاوره و رزرو نوبت» از CTA پایین
- Hero info: «تهران و نوشهر»
- Composition، Services، Treatment، About+Trust، Journey، FAQ بدون تغییر Layout

### ۲.۳ صفحات داخلی (Extend رنگ + Component مشترک)

| صفحه | فایل | تغییرات ۱.۴.۵ |
|------|------|----------------|
| درباره ما | `page-about.php` | رنگ‌ها از سیستم جدید؛ تصویر پزشک همان Asset؛ Trust/متن هماهنگ |
| خدمات (لیست) | `page-services.php` | کارت‌های Service با border/shadow جدید؛ رنگ‌ها site-wide |
| ایمپلنت | `page-implant.php` | رنگ + template-part مشترک |
| روکش | `page-crown.php` | رنگ + template-part مشترک |
| جراحی و لثه | `page-surgery.php` | رنگ + template-part مشترک |
| درمان عمومی | `page-general.php` | رنگ + template-part مشترک |
| تماس با ما | `page-contact.php` | دو آدرس (تهران + نوشهر با لینک نقشه)؛ فرم؛ رنگ‌ها |
| رزرو نوبت | `page-appointments.php` | فرم + CTA یکدست |
| سوالات متداول | `page-faq.php` | Accordion مشترک؛ رنگ‌ها |
| نوشته‌های بلاگ | `single.php` | رنگ و header/footer مشترک |
| ۴۰۴ / پیش‌فرض | `404.php` / `page.php` | رنگ و header/footer مشترک |

**هیچ UI جدید یا Redesign اضافه نشده.** فقط Color System + Componentهای موجود Extend شده‌اند.

---

## ۳. Design Tokens نهایی (۱.۴.۵)

```
--color-hero-brown-primary: #4A3022
--color-hero-brown-light:   #684936
--color-dark-espresso:      #2C1D16
--color-cream-primary:      #F2E9DA
--color-cream-light:        #F8F2E8
--color-caramel-accent:     #C58D52
```

Typography: Vazirmatn + Playfair Display (لوگو + اعداد Journey)  
Radius: 12px / Button 8px  
Container: 1320px  
Breakpoints: 559 / 767 / 768 / 1200 (جزئیات در گزارش قبلی)

---

## ۴. Componentهای مشترک (برای Extend بعدی)

Header · Footer · Button (Gold / Outline) · Service Card · Journey Card · FAQ Accordion · CTA Banner · Form fields · Treatment list · About Trust strip · Service Template · Breadcrumbs · Mobile Action Bar · Icons (SVG)

---

## ۵. Assetهای نهایی

- `doctor-keyvan-alipasandi.jpg` (+ WebPهای responsive)
- `implant-hero.jpg` (+ WebP)
- `clinic-interior.jpg` (روشن‌شده) (+ WebP)
- Favicon SVG + PNG
- تمام آیکن‌ها در `inc/icons.php` (SVG)

Desktop و Mobile از همان فایل اصلی استفاده می‌کنند.

---

## ۶. Icon System

**همه SVG** (stroke، `currentColor`، Accent Caramel). هیچ Emoji یا Icon Font در Production وجود ندارد.

---

## ۷. فرم رزرو

Submit → `admin-post.php` → handler در `inc/forms.php` → **wp_mail** (ایمیل).  
لطفاً روی Staging یک Submit واقعی انجام دهید تا هم ظاهر Success و هم تحویل ایمیل تأیید شود.

---

## ۸. Schema

- `LocalBusiness` / `Dentist` (آدرس تهران)
- `BreadcrumbList`
- FAQ در صورت وجود  
منطق فقط در Theme (`inc/seo.php`). در Staging بررسی شود که افزونه SEO خارجی Schema تکراری تولید نکند.

---

## ۹. دامنه و انتقال (اقدام فوری پیشنهادی)

سایت فعلی روی Google برای نام دکتر ایندکس شده است. برای انتقال تمیز:

1. **ثبت دامنه `drkeyvanalipasandi.ir`**  
   - باید به نام خود پزشک ثبت شود (قوانین NIC.ir).  
   - **سؤال از کارفرما:** آیا حساب NIC.ir دارید؟  
     - اگر بله: می‌توانید خودتان ثبت کنید یا یوزرنیم/پسورد را در اختیار ما بگذارید تا به نمایندگی ثبت کنیم.  
     - اگر خیر: یک حساب NIC.ir بسازید (با کد ملی/اطلاعات پزشک) و سپس دامنه را ثبت کنید.  
   - فعال‌سازی دامنه `.ir` معمولاً چند روز طول می‌کشد؛ **هرچه زودتر شروع شود بهتر است.**

2. بعد از Final تم و Staging QA:
   - انتقال کامل به دامنه جدید
   - Redirect Map کامل ۳۰۱ از fasdent.ir (و هر URL قدیمی) به ساختار جدید
   - به‌روزرسانی Search Console / Sitemap / Canonical

---

## ۱۰. Staging QA Checklist (قبل از Production)

- [ ] فرم رزرو: Submit واقعی + ایمیل دریافتی + Error/Success
- [ ] منوی موبایل + Focus Trap + Escape
- [ ] Accessibility پایه (Keyboard، Screen Reader، Focus ring)
- [ ] عدم Horizontal Scroll در 320–1440
- [ ] Mobile LCP / CLS / INP (عکس پزشک و ایمپلنت WebP + sizes)
- [ ] Schema بدون تکرار
- [ ] خوانایی لوگو Playfair روی دستگاه واقعی موبایل
- [ ] Footer موبایل ۳۹۰ (آدرس دوگانه + CTA)
- [ ] Tablet ~۷۶۸ (Header، Hero، کارت‌ها، Journey)

---

## ۱۱. فایل تحویل

- تم کامل: `alipasandi-clinic-1.4.5.zip`
- این گزارش: `FINAL-REPORT-1.4.5.md`

---

**پیام پیشنهادی برای کارفرما (دامنه):**

> برای انتقال نهایی و حفظ سئوی فعلی، دامنه `drkeyvanalipasandi.ir` باید به نام خودتان (پزشک) در NIC.ir ثبت شود. آیا حساب NIC.ir دارید؟ در صورت تمایل می‌توانید یوزرنیم و پسورد را در اختیار ما بگذارید تا ثبت را انجام دهیم، یا خودتان ثبت کنید. چون فعال‌سازی `.ir` ممکن است چند روز طول بکشد، پیشنهاد می‌کنیم همین الان اقدام شود. بعد از نهایی شدن تم، انتقال کامل سایت و Redirectهای ۳۰۱ را انجام می‌دهیم.

