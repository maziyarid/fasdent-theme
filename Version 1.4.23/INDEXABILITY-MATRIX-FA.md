# Indexability Matrix — قبل از Production

**نسخه:** ۱.۴.۱۸
**هدف:** مشخص کردن دقیق اینکه کدام URL باید Index شود و کدام نباید.

| نوع URL | Index | Canonical | Sitemap | یادداشت |
|---|---|---|---|---|
| Homepage | **Index** | Self | Yes | فقط Host نهایی |
| Service: Implant | **Index** | Self | Yes | Landing اصلی |
| Service: Crown | **Index** | Self | Yes | Landing اصلی |
| Service: Surgery | **Index** | Self | Yes | Landing اصلی |
| Service: General | **Index** | Self | Yes | Landing اصلی |
| Services hub (در صورت وجود) | **Index** | Self | Yes | اگر صفحه جدا دارد |
| About | **Index** | Self | Yes | |
| Contact | **Index** | Self | Yes | |
| Appointment | **Noindex** | Self یا به Contact | No | صفحه تراکنشی/فرم — ارزش Index پایین |
| FAQ | **Index** | Self | Yes | محتوا قابل Crawl |
| Blog Archive (صفحه نوشته‌ها) | **Index** | Self | Yes | |
| Single Post | **Index** | Self | Yes | |
| Category Archive | **Noindex** یا Index با محتوا | Self | No (ترجیح) | تا وقتی محتوای غنی ندارند Noindex |
| Tag Archive | **Noindex** | Self | No | معمولاً thin |
| Author Archive | **Noindex** یا Index اگر Bio کامل | Self | No | تا تکمیل Author Bio |
| Date Archive | **Noindex** | — | No | |
| Search Results | **Noindex** | — | No | |
| Media Attachment Pages | **Noindex** + Redirect به parent ترجیحی | Parent | No | |
| 404 | — | — | No | HTTP 404 واقعی |
| Staging / Preview / Test | **Noindex** + Access restriction | — | No | قبل از Launch |

### قوانین کلی

1. فقط URLهای Canonical و Indexable داخل Sitemap.
2. URLهای Staging هرگز وارد Sitemap Production نشوند.
3. Search / 404 / noindex وارد Sitemap نشوند.
4. Owner Sitemap = SEO Plugin نهایی.
5. پس از انتخاب Plugin، تنظیمات فوق در Plugin اعمال و با View-Source / Sitemap XML تأیید شود.

### Staging Index Protection Checklist

- [ ] Access restriction / password (ترجیحی)
- [ ] noindex در تمام صفحات Staging
- [ ] robots.txt Staging مسدودکننده (مکمل — نه تنها لایه)
- [ ] قبل از Go-live: حذف password / noindex
- [ ] تأیید که Sitemap Production فقط دامنه نهایی دارد
- [ ] هیچ URL Staging در Search Console Production ثبت نشود
