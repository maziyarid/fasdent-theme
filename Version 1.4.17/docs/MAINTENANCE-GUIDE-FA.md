# راهنمای نگهداری تم کلینیک دکتر کیوان علی‌پسندی

**نسخه تم:** ۱.۴.۱۷  
**تاریخ:** ۱۳ اوت ۲۰۲۶  
**مخاطب:** ادمین سایت / نگهدارنده آینده (بدون وابستگی به توسعه‌دهنده خاص)

---

## ۱. تغییر اطلاعات عملیاتی کلینیک (NAP)

همه اطلاعات عملیاتی در **Appearance → Customize → اطلاعات کلینیک** ویرایش می‌شوند.

| مورد | فیلد Customizer | محل ذخیره |
|---|---|---|
| شماره تماس | تلفن | WordPress Option (مستقل از Theme) |
| آدرس نمایشی | آدرس نمایشی کامل (UI) | Option |
| خیابان Schema | خیابان/پلاک Schema | Option |
| شهر | شهر عملیاتی | Option |
| نقشه | لینک نقشه نوشهر | Option |
| ایمیل رزرو/تماس | ایمیل دریافت رزرو/تماس | Option |
| ساعات رزرو | ساعات رزرو (هر خط یک ساعت) | Option |
| کدپستی | کدپستی (اختیاری) | Option |
| مختصات | عرض/طول جغرافیایی (اختیاری) | Option |
| اعتبار طراحی فوتر | اعتبار طراحی فوتر (خالی = مخفی) | Option |

**نکته مهم:** این مقادیر در `wp_options` ذخیره می‌شوند و با تعویض Theme از بین نمی‌روند.

پس از تغییر، یک‌بار کش (افزونه کش / CDN / مرورگر) را پاک کنید.

---

## ۲. تغییر SEO

- **Owner اصلی SEO:** افزونه SEO نهایی (Rank Math یا Yoast — فقط یکی).
- وقتی افزونه فعال است، Theme هیچ Title / Meta / Canonical / OG / Schema موازی تولید نمی‌کند.
- تنظیمات صفحه‌ای SEO در افزونه انجام شود.
- Meta box سبک Theme فقط به‌عنوان Fallback در نبود افزونه است.

---

## ۳. افزودن مقاله (Blog)

1. در پیشخوان: نوشته‌ها → افزودن نوشته.
2. عنوان، محتوا، تصویر شاخص، نویسنده را پر کنید.
3. Article Schema به‌صورت خودکار (در نبود افزونه SEO) یا توسط افزونه تولید می‌شود.
4. Blog در منوی اصلی نیست؛ از فوتر و لینک‌های داخلی قابل دسترسی است.
5. Internal link به صفحات خدمات (Implant / Crown / …) را در متن مقاله قرار دهید.

---

## ۴. صفحات خدمات (Implant / Crown / Surgery / General)

- Layout و Componentها توسط Template کنترل می‌شوند.
- محتوای ساختاریافته فعلی در فایل تم (`inc/service-data.php`) قرار دارد.
- **Roadmap پس از Launch:** انتقال محتوای قابل‌ویرایش به Editor وردپرس / Meta بدون Redesign، تا تغییرات متنی بدون Developer انجام شود.
- تا آن زمان، تغییر متن اصلی این چهار صفحه نیاز به ویرایش فایل داده یا هماهنگی با توسعه‌دهنده دارد.

---

## ۵. مدیریت Redirect

- **Owner تولید Redirectهای SEO حیاتی:** افزونه SEO (Redirects) یا تنظیمات Server — **نه** Theme.
- فیلتر `alipasandi_redirect_map` در Theme فقط Helper/Fallback موقت است.
- هر تغییر URL باید با ۳۰۱ مستقیم ثبت شود (بدون Chain).

---

## ۶. Backup و Rollback

قبل از هر به‌روزرسانی مهم:

1. **Files:** کل پوشه `wp-content/themes/alipasandi-clinic` و در صورت نیاز کل `wp-content`.
2. **Database:** Export کامل دیتابیس (شامل `wp_options` برای NAP).
3. **نسخه Theme قبلی** را نگه دارید.
4. در صورت مشکل: بازگرداندن فایل‌های Theme + در صورت نیاز Import دیتابیس.

---

## ۷. به‌روزرسانی Theme

1. Backup بگیرید.
2. Theme جدید را آپلود و فعال کنید (یا فایل‌ها را جایگزین کنید).
3. Appearance → Customize را یک‌بار باز کنید تا Migration NAP (در صورت نیاز) اجرا شود.
4. View-Source چند صفحه اصلی را برای Schema / Meta یکتا بررسی کنید.
5. فرم رزرو را یک‌بار Submit تست کنید.

---

## ۸. افزودن خدمت جدید (Service Page)

1. صفحه جدید در وردپرس بسازید.
2. در صورت نیاز Template اختصاصی یا استفاده از ساختار موجود هماهنگ شود.
3. از همان Design System (Color / Component / Typography) استفاده شود — بدون CSS صفحه‌ای جدید.
4. در Navigation و Internal Linking اضافه شود.
5. Schema Service و SEO صفحه در افزونه SEO تنظیم شود.

---

## ۹. Tracking / Analytics

- Google Analytics و Microsoft Clarity **داخل Theme hard-code نیستند**.
- مدیریت از لایه Site / Plugin / GTM انجام شود تا با تعویض Theme قطع نشوند.

---

## ۱۰. هدف فنی ثبت‌شده

| مورد | مقدار |
|---|---|
| Requires at least WordPress | ۶.۲ |
| Requires PHP | ۷.۴ |
| تست پیشنهادی Staging | PHP ۸.۲ |
| Page Builder | ندارد |
| React Runtime Frontend | ندارد |
| وابستگی به Plugin اختصاصی اجباری | ندارد |

---

## ۱۱. Design System Locked

از نسخه ۱.۴.۱۶ به بعد بدون درخواست رسمی تغییر نمی‌کنند:

Color Token · Layout · Grid · Typography Scale · Spacing · Radius · Shadow · Breakpoint · Header · Footer · Hero Composition

فقط مجاز: Bug Fix · SEO Fix · Performance Fix · Content Fix
