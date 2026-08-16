# Maintenance Guide — Theme 1.4.26 / Plugin 1.3.1

**Design System:** Locked. **Production approval:** فقط پس از Staging Evidence واقعی.

## 1) Ownership

| حوزه | Owner |
|---|---|
| Presentation/Layout | Theme 1.4.26 |
| Service storage/editor/migration/revisions/export/health | Plugin 1.3.1 |
| NAP settings + Rank Math NAP sync | Plugin 1.3.1 |
| Contact/Appointment validation, rate limit, mail request | Plugin 1.3.1 |
| Metadata/Canonical/Schema/Sitemap/Page redirects | Rank Math |
| SMTP transport + DNS | SMTP provider / Hosting |
| Host redirects/security headers/cache/CDN | Hosting |

Theme missing-plugin compatibility فقط existing meta/options را read/render می‌کند. هیچ Write/Migration/Form Handler ندارد.

## 2) NAP SSOT

مسیر: **Settings → اطلاعات کلینیک**. Optionها در `wp_options` با prefix `alipasandi_`:

`alipasandi_clinic_business_name`, `alipasandi_clinic_phone`, `alipasandi_clinic_phone_e164`, `alipasandi_clinic_address`, `alipasandi_clinic_street`, `alipasandi_clinic_maps`, `alipasandi_clinic_city`, `alipasandi_clinic_region`, `alipasandi_clinic_country`, `alipasandi_clinic_email`, `alipasandi_clinic_notify_email`, `alipasandi_clinic_mail_from`, `alipasandi_clinic_mail_from_name`, `alipasandi_clinic_booking_times`, `alipasandi_clinic_opening_hours`, `alipasandi_clinic_postal_code`, `alipasandi_clinic_geo_lat`, `alipasandi_clinic_geo_lng`, social URLs, legacy address, designer credit.

Site URL Option ندارد؛ **`home_url('/')` SSOT** است. Phone/Address/Email/Map/Social حساس به Production default خالی دارند و باید صریحاً configure شوند. City/Region/Country contract فعلی = نوشهر / مازندران / IR.

## 3) Opening hours / appointment request

Grammar کامل در `OPENING-HOURS-CONTRACT-FA.md`. Booking slots هر خط `HH:MM`، unique/sorted، حداکثر 48. انتخاب کاربر **درخواست زمان پیشنهادی** است، نه Availability لحظه‌ای یا رزرو قطعی. Booking horizon یک SSOT در Plugin دارد: `alipasandi_booking_horizon_days()`؛ مقدار candidate فعلی 365 روز است و Theme HTML `max` و Server validator هر دو همین function را مصرف می‌کنند. مقدار 365 قبل Production نیازمند Owner/Clinic confirmation است. اگر official opening hours وجود داشته باشد، روز بسته/ساعت خارج schedule رد می‌شود.

Production WordPress timezone = `Asia/Tehran`; معیار کد `wp_timezone()`/`wp_timezone_string()` است، نه PHP/server timezone.

## 4) Mail

Notify Email، domain From و From Name را configure کنید. Form فقط وقتی operational است که recipient و From معتبر باشند و From با `home_url()` domain match کند. SMTP Plugin می‌تواند header نهایی را force کند؛ PASS فقط با delivered inbox + full headers + SPF/DKIM/DMARC.

## 5) Service content

Tools → **Service Content**: Migration/Retry, JSON Export, Health. Migration explicit و idempotent است؛ Meta موجود overwrite نمی‌شود. Keys: `_alipasandi_service` و `_alipasandi_service_key`; schema version 1 حفظ شده است. Missing Meta پس از completed migration Critical است؛ legacy فقط emergency render fallback و usage آن warning/log می‌شود.

Registry contract در `SERVICE-REGISTRY-CONTRACT-FA.md`. تمام Appointment choices از همان Registry derive می‌شوند؛ secondary extras list حذف شده است. Malformed Registry entry وارد UI نمی‌شود و Health Critical تولید می‌کند.

## 6) SEO

Rank Math Production owner است. Theme هیچ SEO admin field ذخیره نمی‌کند. Emergency fallback فقط هنگام نبود SEO owner و فقط read-only output دارد. Regression suite دائمی: `tests/test-seo-fallback.php` + `SEO-FALLBACK-REGRESSION-MATRIX-FA.md`.

KML/Local Sitemap تا زمانی که Staging اثبات نکند همان NAP SSOT را مصرف می‌کند **Disabled** بماند. Entity graph before/after و `@id` uniqueness باید ثبت شود.

## 7) Indexability

مرجع: `INDEXABILITY-MATRIX-FA.md`. Configuration واقعی Rank Math/Server باید crawl و evidence شود؛ Source ZIP به‌تنهایی PASS نیست.

## 8) Privacy / medical claims

`PRIVACY-POLICY-CONTENT-CHECKLIST-FA.md` و `MEDICAL-CLAIM-REGISTER-FA.md` Gate هستند. `+10,000` default=false. Claim approval باید روی rendered final DB content انجام شود، نه فقط legacy source.

## 9) Security / trusted proxy

مرجع `SECURITY-NOTES-1.4.26-FA.md`. Raw IP ذخیره نمی‌شود؛ transient counter non-atomic است. Proxy header فقط بعد از allowlist Hosting trust شود. Abuse owner در scale = Edge/WAF.

## 10) Updates / rollback

Production pairing = Theme 1.4.26 + Plugin 1.3.1. Contract کامل: `RELEASE-ROLLBACK-CONTRACT-1.4.26-FA.md`. قبل update: DB + uploads backup، Service JSON export، hash artifacts، Rank Math config backup. Update مستقیم Production بدون Staging ممنوع.

## 11) Cache / modified / lastmod

پس از Service edit: purge واقعی stack و proof frontend visibility. `post_modified` و Rank Math sitemap `lastmod` before/after ثبت شود؛ fake lastmod ممنوع.

## 12) Required staging evidence

PHP 8.2 lint/runtime, WP 6.4+ activation + production-WP smoke, WPCS/static/deprecated, migration/revision/autosave/partial POST/theme switch/plugin deactivation, Rank Math inventory + graph, NAP freeze, four-host redirect crawl, forms/timezone/rate-limit/SMTP/DNS, Privacy/Claims, restore rehearsal, cache/lastmod, CWV, browser/mobile/A11y/Admin RTL, security headers, Search Console/GBP/monitoring. Evidence باید به **hash دقیق artifacts نهایی** متصل شود.
