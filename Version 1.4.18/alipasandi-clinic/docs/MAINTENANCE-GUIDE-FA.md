# راهنمای نگهداری تم کلینیک دکتر کیوان علی‌پسندی

**نسخه تم:** ۱.۴.۱۸  
**تاریخ:** ۱۳ اوت ۲۰۲۶  
**مخاطب:** ادمین سایت / نگهدارنده آینده (بدون وابستگی به توسعه‌دهنده خاص)

---

## ۱. تغییر اطلاعات عملیاتی کلینیک (NAP)

همه اطلاعات عملیاتی در **Appearance → Customize → اطلاعات کلینیک** ویرایش می‌شوند.

| مورد | فیلد Customizer | Option در دیتابیس (`wp_options`) |
|---|---|---|
| شماره تماس | تلفن | `alipasandi_clinic_phone` |
| آدرس نمایشی UI | آدرس نمایشی کامل (UI) | `alipasandi_clinic_address` |
| خیابان Schema | خیابان/پلاک Schema | `alipasandi_clinic_street` |
| شهر عملیاتی | شهر عملیاتی | `alipasandi_clinic_city` |
| لینک نقشه | لینک نقشه نوشهر | `alipasandi_clinic_maps` |
| ایمیل رزرو/تماس | ایمیل دریافت رزرو/تماس | `alipasandi_clinic_notify_email` |
| ساعات رزرو | ساعات رزرو (هر خط یک ساعت) | `alipasandi_clinic_booking_times` |
| کدپستی (اختیاری) | کدپستی | `alipasandi_clinic_postal_code` |
| عرض جغرافیایی | عرض جغرافیایی | `alipasandi_clinic_geo_lat` |
| طول جغرافیایی | طول جغرافیایی | `alipasandi_clinic_geo_lng` |
| آدرس سابقه تهران | آدرس سابقه تهران | `alipasandi_clinic_address_legacy` |
| وب‌سایت | وب‌سایت | `alipasandi_clinic_website` |
| اینستاگرام | اینستاگرام | `alipasandi_clinic_instagram` |
| واتساپ | واتساپ | `alipasandi_clinic_whatsapp` |
| تلگرام | تلگرام | `alipasandi_clinic_telegram` |
| اعتبار طراحی فوتر | اعتبار طراحی فوتر (خالی = مخفی) | `alipasandi_designer_credit` |
| آمار درمان | نمایش آمار بیش از ۱۰٬۰۰۰ کیس | `alipasandi_show_treatment_count` |
| پرچم Migration | (داخلی) | `alipasandi_nap_migrated` |

**Storage:** همه **WordPress Options** (site-level) هستند — با تعویض Theme از بین نمی‌روند.

**Backup NAP:** در Export دیتابیس، جدول `wp_options` شامل این کلیدهاست. می‌توان با WP-CLI نیز export کرد:
```bash
wp option get alipasandi_clinic_phone
```

پس از تغییر، کش (افزونه کش / CDN / مرورگر) را پاک کنید.

---

## ۲. تغییر SEO

- **SEO Owner نهایی:** یکی از Rank Math یا Yoast (فقط یکی — همزمان نصب نشوند).
- Plugin نهایی و دلیل انتخاب در همین Guide پس از تصمیم Staging ثبت شود.
- وقتی Plugin فعال است، Theme هیچ Title / Meta / Canonical / OG / Schema موازی تولید نمی‌کند.
- تنظیمات صفحه‌ای SEO در Plugin انجام شود.

---

## ۳. Redirect Owner

- **Production Redirect Owner:** SEO/Redirect Plugin مستقل **یا** Server (Nginx/Apache).
- فیلتر Theme (`alipasandi_redirect_map`) فقط Helper/Fallback است — محل ذخیره Redirectهای حیاتی SEO نیست.
- هر تغییر URL: ۳۰۱ مستقیم، بدون Chain، بدون Loop، مقصد مرتبط.

---

## ۴. Tracking / Analytics Owner

- GA و Clarity **داخل Theme hard-code نیستند**.
- **Owner:** Site-level / Plugin / GTM (خارج از Theme).
- با تعویض Theme نباید قطع یا Duplicate شوند.

---

## ۵. فرم رزرو و نگهداری داده

- فرم‌ها فقط از طریق `wp_mail` ایمیل می‌فرستند.
- **هیچ ذخیره در Database / Log داخلی Theme انجام نمی‌شود.**
- داده‌های ارسالی فقط در صندوق ایمیل گیرنده (`alipasandi_clinic_notify_email`) قرار می‌گیرند.
- اطلاعات پزشکی غیرضروری جمع‌آوری نمی‌شود (فیلدها: خدمت، تاریخ، ساعت، نام، تلفن، توضیحات اختیاری).

ساعات رزرو از Customizer (`alipasandi_clinic_booking_times`) مدیریت می‌شوند — بدون تغییر Source Code.

---

## ۶. افزودن مقاله (Blog)

1. نوشته‌ها → افزودن نوشته.
2. عنوان، محتوا، تصویر شاخص، نویسنده.
3. Article Schema توسط Plugin SEO (ترجیحی) یا Fallback Theme.
4. Blog در منوی اصلی نیست؛ از فوتر و لینک داخلی در دسترس است.
5. Internal link به Service Pages در متن مقاله.

**Author/Reviewer آینده:** Data model وردپرس (Author + post meta) ظرفیت افزودن Professional title / Bio / Reviewed by / References را دارد؛ UI سنگین الان ساخته نشده است.

---

## ۷. صفحات خدمات (Service Content)

- Layout / Component توسط Template کنترل می‌شود.
- محتوای ساختاریافته فعلی در `inc/service-data.php` است.
- **Migration به Editor/Meta:** طبق تصمیم پروژه (قبل یا اولین Sprint پس از Launch) — URL و Design تغییر نمی‌کند؛ Rollback با نگه‌داشتن service-data به‌عنوان Fallback ممکن است.

---

## ۸. Backup و Rollback

قبل از هر به‌روزرسانی مهم:

1. **Database** (شامل `wp_options` برای NAP)
2. **Uploads**
3. **Theme** (`wp-content/themes/alipasandi-clinic`)
4. **Plugins / config**

روش Restore: بازگرداندن فایل‌های Theme + در صورت نیاز Import دیتابیس.

---

## ۹. به‌روزرسانی Theme

1. Backup  
2. آپلود/جایگزینی Theme جدید  
3. یک‌بار باز کردن Customizer (اجرای Migration در صورت نیاز)  
4. View-Source صفحات اصلی (Schema / Meta یکتا)  
5. یک Submit تست فرم  

---

## ۱۰. افزودن خدمت جدید

1. صفحه جدید در وردپرس  
2. استفاده از Design System موجود (بدون CSS صفحه‌ای)  
3. Navigation + Internal Linking  
4. SEO صفحه در Plugin  

---

## ۱۱. Design System Locked

بدون درخواست رسمی تغییر نمی‌کنند:

Color Token · Layout · Grid · Typography Scale · Spacing · Radius · Shadow · Breakpoint · Header · Footer · Hero Composition · ساختار چهار Service اصلی

فقط مجاز: Bug Fix · SEO Fix · Performance Fix · Accessibility Fix · Content Architecture Fix · Security Fix

---

## ۱۲. هدف فنی

| مورد | مقدار |
|---|---|
| Requires at least WordPress | ۶.۲ |
| Requires PHP | ۷.۴ |
| تست پیشنهادی Staging | PHP ۸.۲ |
| Page Builder | ندارد |
| React Runtime Frontend | ندارد |
| Plugin اختصاصی اجباری | ندارد |

---

## ۱۳. فرآیند Update پیشنهادی

1. Backup کامل  
2. Update روی Staging  
3. Smoke test (فرم، چند صفحه اصلی، View-Source)  
4. QA  
5. Production update  

Update مستقیم WordPress/Plugin/PHP روی Production بدون Backup انجام نشود.

---

## ۱۴. Indexability

جزئیات در `docs/INDEXABILITY-MATRIX-FA.md`.
