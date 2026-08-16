# راهنمای نگهداری تم کلینیک دکتر کیوان علی‌پسندی

**نسخه تم:** ۱.۴.۲۳
**تاریخ:** ۱۳ اوت ۲۰۲۶
**مخاطب:** ادمین سایت / نگهدارنده آینده (بدون وابستگی به توسعه‌دهنده خاص)

---

## ۱. تغییر اطلاعات عملیاتی کلینیک (NAP)

منبع اصلی مدیریت اطلاعات عملیاتی مستقل از Theme است: **Settings → اطلاعات کلینیک** (Site Plugin). Customizer فقط compatibility UI در نبود Plugin است.

| مورد | فیلد Site Settings | Option در دیتابیس (`wp_options`) |
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

ساعات رزرو از Settings مستقل Site Plugin (`alipasandi_clinic_booking_times`) مدیریت می‌شوند.

---

## ۶. افزودن مقاله (Blog)

1. نوشته‌ها → افزودن نوشته.
2. عنوان، محتوا، تصویر شاخص، نویسنده.
3. Article Schema توسط Plugin SEO (ترجیحی) یا Fallback Theme.
4. Blog در منوی اصلی نیست؛ از فوتر و لینک داخلی در دسترس است.
5. Internal link به Service Pages در متن مقاله.

**Author / Reviewer (Data Model — UI کامل بعداً):**

| داده | محل پیشنهادی |
|---|---|
| Author name | هسته WP (Author) |
| Author Bio | Users → Profile / biographical info |
| Professional Title | User meta (آینده) |
| Reviewed By | Post meta (آینده) |
| Review Date | Post meta (آینده) |
| References | Post content یا post meta (آینده) |

Template مانع افزودن این فیلدها نیست؛ UI کامل فعلاً فعال نشده است.

---

## ۷. صفحات خدمات (Service Content) — از ۱.۴.۲۱

**اولویت منبع داده:**
`metadata_exists(_alipasandi_service)` → Meta به‌عنوان منبع کامل صفحه (حتی اگر فیلدی عمداً خالی باشد)
در غیر این صورت → `inc/service-data.php` فقط Emergency/Read-only

| مورد | جزئیات |
|---|---|
| Owner | Plugin سبک `Alipasandi Service Content`؛ Theme فقط Render |
| محل ویرایش | Meta Box «محتوای صفحه خدمت» فقط روی ۴ صفحه Service |
| تشخیص صفحه | کلید پایدار `_alipasandi_service_key`؛ Slug فقط Bootstrap اولیه |
| Meta key | `_alipasandi_service` + `schema_version` |
| Permission | `current_user_can('edit_post', $post_id)` + فقط Service pages |
| Migration | Per-page status در option `alipasandi_service_meta_status_v1`؛ completed فقط وقتی هر ۴ OK |
| Idempotent | Meta موجود overwrite نمی‌شود |
| Revision | WordPress 6.4+؛ `revisions_enabled` هسته |
| Restore قدیمی | Revision بدون Meta، Meta فعلی را حذف نمی‌کند |
| Autosave | Meta را تغییر نمی‌دهد؛ ذخیره فقط با Update و این موضوع در UI اعلام شده |
| Image | Attachment ID معتبر + ALT یا decorative صریح؛ Media ALT fallback |
| REST | `show_in_rest = false` |
| H1 | یک تگ معنایی واحد |

**Restore اشتباه ویرایش:** Revisions صفحه → بازگردانی Revision (Meta همراه بازیابی می‌شود).

**Migration/Health/Export:** Tools → Service Content. Migration خودکار روی Request اجرا نمی‌شود. ابتدا Backup بگیرید، سپس دکمه Migration را اجرا و Health را PASS کنید. WP-CLI: `wp alipasandi service migrate` و `wp alipasandi service health`.

**Contract آینده v1→v2:** Backup قبل از اجرا؛ migration صریح، idempotent و per-page؛ عدم overwrite ویرایش کاربر؛ ثبت status/log؛ verify قبل از completed. `_alipasandi_service` و `_alipasandi_service_key` برای سازگاری حفظ شوند. `show_in_rest` بدون تعریف schema محدود object نباید true شود.

**Legacy:** `inc/service-data.php` deprecated/read-only است. پس از حداقل دو Release پایدار، Backup و Export JSON موفق و تأیید Production، fallback runtime در Release جدا حذف و فایل فقط Archive می‌شود. Missing Meta پس از Migration باید در Health به‌صورت FAIL دیده شود.

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
3. فعال‌بودن Plugin همراه و اجرای صریح Tools → Service Content → Migration در صورت نیاز
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
| Requires at least WordPress | ۶.۴ |
| Requires PHP | ۸.۰ |
| تست پیشنهادی Staging | PHP ۸.۲ |
| Page Builder | ندارد |
| React Runtime Frontend | ندارد |
| Plugin Site Functionality | `Alipasandi Service Content 1.1.0` |

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
