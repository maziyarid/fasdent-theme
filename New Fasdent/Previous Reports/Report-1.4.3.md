# گزارش اصلاحات Homepage — نسخه ۱.۴.۳

**وضعیت:** Production Candidate پس از بازخورد AI/Client روی ۱.۴.۲  
**تاریخ:** ۱۱ اوت ۲۰۲۶  
**مرجع رنگ:** شش Token مصوب بدون تغییر (ColourTokens01 / design-tokens.json)

---

## خلاصه تأیید و تغییرات

مسیر رنگ‌بندی Brown / Cream / Caramel، Layout، Hero Composition، Responsive و ساختار کلی تأیید شده بود. این نسخه فقط اصلاحات جزئی درخواستی + افزودن لوکیشن دوم را اعمال می‌کند.

| مورد | اقدام | نتیجه |
|------|--------|--------|
| فونت لوگو | Typeface فقط تغییر کرد؛ جایگاه/سایز/ساختار ثابت | Playfair Display پیاده‌سازی شد + ۴ گزینه جایگزین مستند |
| Mobile Hero | Micro-adjustment فاصله و Blend طبیعی‌تر | فاصله تیتر/توضیح/تصاویر بهینه؛ mask و gradient نرم‌تر |
| کارت خدمات | Shadow و Border ظریف‌تر | border opacity پایین‌تر + shadow لایه‌ای Premium |
| Trust / آمار | تاکید بصری روی آیکن و اعداد | آیکن‌های Trust در دایره، اعداد Journey بزرگ‌تر و Caramel |
| تصویر کلینیک | روشن‌تر | asset + CSS filter brightness/contrast |
| Footer موبایل | خوانایی لوگو و متن‌ها | سایز فونت و logo در ≤۵۵۹px افزایش یافت |
| لوکیشن دوم | اضافه شد | نوشهر، ستارخان، امیراد ۱ طبقه ۵ + لینک نقشه |

---

## ۱. فونت لوگو (فقط Typeface)

**پیاده‌سازی فعلی:** `Playfair Display` (wght 500–700) — Modern/Premium Serif  
هماهنگی با Vazirmatn: تضاد serif/sans کلاسیک برای برند پزشکی لوکس، خوانایی لاتین نام دکتر عالی، وزن ۶۰۰ برای strong.

**گزینه‌های جایگزین (فقط با تغییر `font-family` روی `.brand-copy strong`):**

1. **Playfair Display** (فعلی) — Premium editorial، حس کلینیک لوکس  
2. **Cormorant Garamond** — ظریف‌تر و سبک‌تر، حس اروپایی  
3. **Lora** — Contemporary serif، متعادل و خوانا  
4. **Libre Baskerville** — کلاسیک قابل‌اعتماد، کمی سنتی‌تر  
5. **Source Serif 4** — مدرن، فنی، خوب برای پزشکی  

جایگاه، سایز، letter-spacing محدود، و ساختار دوخطی (نام + DENTAL CLINIC) بدون تغییر باقی مانده است. فونت فقط از Google Fonts با `display=swap` لود می‌شود.

---

## ۲. Mobile Hero — Micro Adjustment

- `gap` گرید Hero در موبایل از ۲۲/۲۶px به ۱۸/۲۰px کاهش یافت.
- فاصله زیر H1، subtitle و description کمی فشرده‌تر شد تا Composition تصویری زودتر در First View قرار گیرد.
- Mask شعاعی و gradientهای blend نرم‌تر و با opacity کنترل‌شده‌تر شدند تا لبه‌های تصویر پزشک و ایمپلنت با Background Brown Primary طبیعی‌تر ادغام شوند (بدون تغییر پیکسل چهره یا Layout).
- ارتفاع تصاویر کمی تنظیم شد؛ ترتیب و Grid areas دست‌نخورده.

---

## ۳. کارت خدمات

- Border: از `var(--line)` به `rgba(dark, 0.06)` — ظریف‌تر.
- Shadow پایه: دو لایه سبک (`0 2px 12px` + `0 1px 3px`) به‌جای shadow واحد.
- Hover: جابه‌جایی کمتر (`-2px`)، shadow ملایم‌تر، border Caramel با opacity پایین‌تر.
- حس Premium بدون شلوغی یا تغییر پس‌زمینه/رنگ.

---

## ۴. بخش Trust (about-trust) و Journey (اعداد)

- آیکن‌های Trust داخل دایره با border و background Caramel ملایم؛ سایز متن strong کمی بزرگ‌تر و weight بالاتر.
- اعداد Journey: سایز ۲.۳۵rem، رنگ Caramel، فونت Playfair، letter-spacing منفی ظریف.
- آیکن Journey بزرگ‌تر و با background واضح‌تر.
- بدون اضافه کردن عنصر جدید یا شلوغی.

---

## ۵. تصویر کلینیک (About)

- Asset `clinic-interior.jpg` و نسخه‌های WebP با brightness/contrast ملایم روشن‌تر شد.
- CSS filter تکمیلی `brightness(1.06) contrast(1.03)` روی `.clinic-image` برای نمایش جزئیات بیشتر و حس اعتماد بالاتر.

---

## ۶. Footer موبایل

- در breakpoint ≤۵۵۹px:  
  - لوگو strong از ۰.۸ به ۰.۸۸–۰.۹۵rem  
  - small از ۰.۴۳ به ۰.۴۸–۰.۵rem  
  - متن‌های footer و لینک‌ها به ۰.۸۲–۰.۸۴rem  
  - عناوین h2 به ۰.۸۸–۰.۹rem  
- خوانایی بهتر بدون تغییر ساختار یا spacing کلی.

---

## ۷. لوکیشن دوم

**آدرس جدید:** نوشهر، ستارخان، امیراد ۱، طبقه ۵  
**لینک نقشه:** https://maps.app.goo.gl/ycPem67fpYbvgdXZ7?g_st=atm

- Customizer: فیلدهای `clinic_address_2` و `clinic_maps_2` اضافه شد.
- Hero info-bar: «تهران و نوشهر» با جزئیات فشرده (جردن · ستارخان، امیراد ۱) — Layout سه‌ستونه حفظ شد.
- Footer: دو خط آدرس + لینک نقشه برای نوشهر؛ متن برند «در تهران و نوشهر».
- صفحه تماس: دو آدرس جداگانه + لینک نقشه.

---

## موارد عمداً بدون تغییر

- شش Color Token
- Layout / Grid / Typography scale اصلی / Container 60-32-16
- ترتیب و تعداد Serviceها، FAQ، Journey steps
- H1 و hierarchy سئو
- آمار ۱۰٬۰۰۰ کیس (خاموش)
- صفحات داخلی (Recolor هنوز پس از تأیید Home)

---

## فایل‌های تحویل

- `alipasandi-clinic-1.4.3.zip` — تم کامل WordPress
- این گزارش

**Pending پیش از Final Production:** تأیید بصری اسکرین‌شات‌های جدید + تست فرم/دسترسی روی Staging.

پس از تأیید صریح، Color System به Templateهای داخلی Extend می‌شود.
