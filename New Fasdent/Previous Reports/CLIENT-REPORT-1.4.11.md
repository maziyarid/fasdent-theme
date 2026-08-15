# گزارش نسخه ۱.۴.۱۱ — محور عملیاتی نوشهر

**تاریخ:** ۱۳ اوت ۲۰۲۶  
**نوع Release:** QA / Operational Location Fix  
**بدون:** Redesign · تغییر Color Token · تغییر Layout · تغییر Hero Composition

---

## ۱. تصمیم‌های اعمال‌شده

| موضوع | تصمیم نهایی | وضعیت در ۱.۴.۱۱ |
|---|---|---|
| محل عملیاتی | فقط نوشهر | ✓ |
| رزرو | فقط نوشهر · بدون فیلد Location | ✓ |
| Schema | Dentist/LocalBusiness = نوشهر | ✓ |
| تهران | فقط سابقه/برند (غیرعملیاتی) | ✓ |
| ساعات رزرو | قابل مدیریت از Customizer | ✓ |
| ایمیل رزرو/تماس | `drkeyvanalipasandi@gmail.com` | ✓ |
| SEO Owner | Theme فقط وقتی Plugin فعال نباشد | از قبل ✓ |

---

## ۲. تغییرات Template / Component (مرجع Location)

| فایل | تغییر |
|---|---|
| `front-page.php` | Hero Info Slot: «مطب نوشهر» + «ستارخان، امیراد ۱، طبقه ۵» (جایگزین «تهران و نوشهر») |
| `footer.php` | متن معرفی بدون دو شعبه فعال · آدرس عملیاتی فقط نوشهر + لینک نقشه · حذف نمایش آدرس دوم به‌عنوان Branch |
| `page-contact.php` | تمرکز کامل روی مطب نوشهر · Map نوشهر · CTA رزرو نوشهر · بدون آدرس تهران عملیاتی |
| `page-appointments.php` | باکس مینیمال «محل مراجعه: مطب نوشهر» + آدرس · متن Hero فرم روی نوشهر |
| `inc/seo.php` | Schema Dentist: `addressLocality=نوشهر` · `addressRegion=مازندران` · `streetAddress` از آدرس نوشهر · `hasMap` نقشه نوشهر · **بدون** Schema تهران |
| `inc/forms.php` | ساعات از Customizer · ایمیل HTML برنددار · فیلد ثابت Location=نوشهر در ایمیل · گیرنده `clinic_notify_email` |
| `functions.php` | Primary options = نوشهر · `clinic_maps` · `clinic_city` · `clinic_notify_email` · `clinic_booking_times` · `clinic_address_legacy` (فقط برند) |
| `assets/css/theme.css` | استایل `.booking-location-note` (مینیمال، بدون UI جدید غیرضروری) |
| `assets/design-tokens.json` | version ۱.۴.۱۱ + `operationalLocation` |

**Reference عملیاتی به تهران در Templateهای فعال:** حذف شده (به‌جز `clinic_address_legacy` در Customizer برای سابقه غیرعملیاتی).

---

## ۳. فرم رزرو

- محل مراجعه در بالای فرم: **مطب نوشهر** + آدرس کامل  
- بدون فیلد Location  
- پیام قانونی حفظ شده  
- ایمیل HTML شامل: محل مراجعه، آدرس، خدمت، تاریخ، ساعت، نام، تلفن، توضیحات  
- گیرنده پیش‌فرض: **drkeyvanalipasandi@gmail.com** (قابل تغییر از Customizer)

---

## ۴. ساعات رزرو

Default همچنان:
- ۱۰:۰۰–۱۲:۳۰ و ۱۴:۰۰–۱۸:۳۰ (بازه ۳۰ دقیقه)

قابل ویرایش از:  
**ظاهر → سفارشی‌سازی → جزئیات کلینیک → ساعات رزرو**  
(هر خط یک ساعت — Hard-code نیست)

---

## ۵. Schema / Local SEO

```
@type: Dentist
name: کلینیک دندانپزشکی دکتر کیوان علی‌پسندی
addressLocality: نوشهر
addressRegion: مازندران
streetAddress: نوشهر، ستارخان، امیراد ۱، طبقه ۵
hasMap: لینک نقشه نوشهر
telephone / url / image: از تنظیمات کلینیک
```

- **هیچ** LocalBusiness/Dentist جدا برای تهران تولید نمی‌شود.  
- اگر Yoast / Rank Math / AIOSEO فعال باشد، Schema و Meta داخلی Theme به‌طور کامل متوقف می‌شود (یک Owner).

**پیشنهاد Plugin برای Production:** Rank Math یا Yoast SEO — هر کدام که تیم محتوا با آن راحت‌تر است. روی Staging فقط یکی نصب شود و خروجی Source بررسی شود.

---

## ۶. نقش تهران

| مجاز | غیرمجاز |
|---|---|
| Customizer → آدرس سابقه (legacy) | فرم رزرو |
| About / Bio در صورت نیاز محتوایی | Schema |
| | Footer به‌عنوان شعبه فعال |
| | Hero دو‌لوکیشنه |
| | Contact به‌عنوان مقصد مراجعه |

---

## ۷. SEO Owner

معماری فعلی:
- `alipasandi_seo_plugin_active()` → در صورت فعال بودن Plugin، Theme هیچ Meta/Schema تولید نمی‌کند.
- Staging: فقط یک Owner را فعال کنید و Source را برای Canonical / Meta / OG / Schema یکتا چک کنید.

---

## ۸. موارد باقی‌مانده برای Staging QA

- [ ] Submit واقعی Appointment + دریافت ایمیل در Gmail  
- [ ] Submit واقعی Contact  
- [ ] SMTP / SPF / DKIM / Spam  
- [ ] Schema فقط نوشهر در View-Source  
- [ ] عدم Duplicate با SEO Plugin  
- [ ] Responsive / A11y / Performance (طبق چک‌لیست قبلی)  
- [ ] تأیید نهایی ساعات پذیرش نوشهر در Customizer  

---

## ۹. خارج از این نسخه (عمداً انجام نشد)

- تقویم شمسی هوشمند + قفل اسلات (فاز جدا در صورت درخواست)  
- Redesign / تغییر Token / تغییر Layout Hero  
- فیلد Location در فرم  
- Schema تهران  

---

## ۱۰. فایل تحویل

- `alipasandi-clinic-1.4.11.zip`
- این گزارش

**جمع‌بندی:** ۱.۴.۱۱ برای ورود به Staging QA با محور عملیاتی **نوشهر** آماده است. Production Approval همچنان منوط به تست فرم، ایمیل، Schema، SEO یکتا و QA واقعی است.
