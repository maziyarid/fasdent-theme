# گزارش اجرای Recolor مرحله اول — نسخه ۱.۴.۱

## نتیجه

درخواست به‌صورت **Recolor نسخه ۱.۴.۰، بدون Redesign** اجرا شد. منبع اجرایی پروژه از این مرحله **WordPress Theme** است. فایل Figma Make فقط در فاز نخست برای تبدیل تصویر/ایده اولیه به مرجع طراحی تهیه شده بود؛ چون تحویل و هماهنگی آن درخواست شده است، Home مرجع Figma نیز با توکن‌ها و Assetهای Theme همگام شد، اما Figma جایگزین Theme یا منبع کد Production نیست.

دامنه این تحویل عمداً فقط شامل موارد زیر است:

- Homepage — Desktop با مبنای عرض `1440px`
- Homepage — Mobile با مبنای عرض `390px`

صفحات داخلی هنوز Recolor نشده‌اند تا رنگ Home ابتدا تأیید شود و دوباره‌کاری ایجاد نشود.

## انجام‌شده

### ۱. Color System مشترک

شش توکن مصوب به‌صورت canonical در `assets/design-tokens.json` ثبت و در CSS Theme و مرجع Figma استفاده شدند:

| Token | مقدار | کاربرد |
| --- | --- | --- |
| Hero Brown Primary | `#4A3022` | Hero و سکشن‌های قهوه‌ای اصلی |
| Hero Brown Light | `#684936` | سطح قهوه‌ای ثانویه و تفکیک ملایم |
| Dark Espresso | `#2C1D16` | Header، Footer و تیره‌ترین Surface |
| Cream Primary | `#F2E9DA` | Background روشن |
| Cream Light | `#F8F2E8` | Card و Surface روشن |
| Caramel Accent | `#C58D52` | CTA، آیکن، Active State و خطوط محدود |

توکن‌های جدید روی `body.home` / صفحه Front Page فعال شده‌اند. Aliasهای پالت ۱.۴.۰ فقط برای صفحات داخلیِ عمداً به‌تعویق‌افتاده نگه داشته شده‌اند و دیگر مبنای طراحی جدید نیستند.

### ۲. عدم Redesign

فایل `front-page.php` و ساختار Home تغییر نکرده است. موارد زیر بدون تغییر باقی مانده‌اند:

- Layout، Grid، ترتیب عناصر و ابعاد
- محل تصویر پزشک و ایمپلنت
- Header، Menu، Footer و Hero
- CTAها، کارت خدمات، FAQ و فرم‌ها
- Typography، Spacing، Radius و Breakpointها
- Containerهای `60 / 32 / 16`
- رفتار Responsive در Desktop و Mobile
- ساختار SEO، Headingها، Breadcrumb، Canonical، Open Graph و Schema

نسخه Theme برای Cache Busting از `1.4.0` به `1.4.1` افزایش یافت؛ این تغییر شماره نسخه است، نه تغییر ساختاری.

### ۳. کنترل استفاده از Caramel

- Caramel به سطح بزرگ یا رنگ غالب تبدیل نشده است.
- Glow و Gradient شدید اضافه نشده است؛ شدت Glow موجود در Hero نیز کاهش یافته است.
- CTA کاراملی با متن Espresso استفاده می‌شود.
- متن کوچک روی زمینه Cream با Brown/Espresso نمایش داده می‌شود، نه Caramel.
- متن کوچک روی Brown با Cream/Off-white نمایش داده می‌شود.

### ۴. بررسی کنتراست WCAG AA

| ترکیب | نسبت | نتیجه |
| --- | ---: | --- |
| Espresso روی Caramel CTA | `5.64:1` | قبول برای متن معمولی |
| Cream روی Brown Primary | `10.04:1` | قبول |
| Cream Light روی Brown Light | `7.27:1` | قبول |
| Espresso روی Cream Primary | `13.48:1` | قبول |
| Caramel روی Cream Primary | `2.39:1` | برای متن معمولی استفاده نشده |
| Caramel روی Brown Primary | `4.21:1` | برای متن کوچک استفاده نشده |

### ۵. تصاویر Theme و Figma

در WordPress Theme از ابتدا URL خارجی Unsplash وجود نداشت و Home از فایل‌های محلی بسته استفاده می‌کرد. در Home فایل Figma Make، هر سه Placeholder خارجی حذف و با همان Assetهای محلی Theme جایگزین شدند:

- `doctor-charcoal-gold.jpg`
- `implant-hero.jpg`
- `clinic-interior.jpg`

Crop، جای تصویر و ساختار Hero تغییر نکرد. در صورت وجود نسخه جدیدتر یا تأیید رسمی متفاوت برای عکس پزشک/رندر ایمپلنت، فقط فایل Asset باید جایگزین شود و Layout نباید تغییر کند.

### ۶. موارد فنی حفظ‌شده

- قالب همچنان Native WordPress و بدون React/Build Step است.
- عدد «بیش از ۱۰٬۰۰۰ کیس درمانی» همچنان به‌صورت پیش‌فرض خاموش است.
- زیرساخت مقالات حذف نشده، اما در منوی اصلی نمایش داده نمی‌شود.
- Horizontal overflow جدیدی به CSS یا ساختار Home اضافه نشده است.
- Theme palette در `theme.json` نیز با شش رنگ مصوب همگام شد.
- Faviconهای SVG/PNG/Apple Touch بدون تغییر شکل، با Espresso و Caramel هم‌رنگ شدند.

## عمداً به مرحله بعد موکول شده

موارد زیر نقص این تحویل نیستند و مطابق دستور کارفرما تا تأیید رنگ Home انجام نشده‌اند:

1. Recolor صفحات داخلی و Templateهای خدمات
2. Recolor About، Contact، Appointment، FAQ و Blog
3. انتشار روی سایت زنده
4. تغییر یا حذف زیرساخت مقالات
5. فعال‌سازی آمار ۱۰٬۰۰۰ کیس

پس از تأیید Home، همین توکن‌ها باید بدون تغییر UI روی همه Templateها اعمال شوند.

## نیازمند تأیید/محیط Staging

1. مشاهده بصری Home در WordPress Staging در Viewportهای دقیق `1440px` و `390px`
2. تأیید رسمی مالکیت و نسخه نهایی تصاویر پزشک، کلینیک و رندر ایمپلنت
3. تست Menu، فرم رزرو، Focus، Keyboard Navigation و Screen Reader روی Staging
4. بررسی نهایی Core Web Vitals با افزونه‌ها و محتوای واقعی
5. تهیه URL Inventory، Title/Meta Inventory، Indexed Pages و Redirect Map پیش از مهاجرت
6. تست واقعی `wp_mail` با ایمیل مدیر سایت روی هاست

## نتیجه تست‌های انجام‌شده

- Build تولیدی Figma Make با Vite: **موفق**
- حذف URLهای Unsplash از Home Figma: **موفق**
- تطابق مقدار شش توکن بین Theme و Figma: **موفق**
- حفظ فایل و ساختار `front-page.php`: **موفق**
- کنترل کنتراست ترکیب‌های اصلی: **موفق با محدودیت‌های ثبت‌شده برای Caramel**
- تست زنده WordPress/PHP/Staging: **در این محیط قابل اجرا نبود و باید روی Staging انجام شود**

## دستور دقیق برای ادامه کار با AI

این متن بدون تغییر به AI مرحله بعد داده شود:

> نسخه ۱.۴.۱ بر پایه ساختار تأییدشده ۱.۴.۰ آماده شده است. این کار Recolor است، نه Redesign. ابتدا فقط Homepage در عرض‌های ۱۴۴۰ و ۳۹۰ بررسی شود. هیچ Layout، Grid، Typography، Spacing، Radius، Component، Asset position، Header/Footer، CTA size یا Responsive behavior تغییر نکند. مرجع رنگ فقط `assets/design-tokens.json` است. Caramel فقط Accent است و برای متن معمولی روی Cream یا متن کوچک روی Brown استفاده نشود. صفحات داخلی تا اعلام تأیید صریح کارفرما Recolor نشوند. پس از تأیید Home، همین Tokenها بدون ساخت UI جدید به تمام Templateهای موجود Extend شوند. هر خروجی بعدی باید همراه با تست عدم Horizontal Scroll، WCAG AA، فرم رزرو، Menu، SEO و Redirect Map ارائه شود.

## وضعیت تأیید

این نسخه برای **ادامه بررسی و تأیید بصری Home چراغ سبز دارد**؛ برای **انتشار نهایی سایت هنوز چراغ سبز ندارد**.
