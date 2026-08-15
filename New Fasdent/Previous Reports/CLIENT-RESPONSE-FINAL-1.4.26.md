# پاسخ اجرایی — Targeted Patch + Runtime Evidence Gate — Release 1.4.26 / Plugin 1.3.1

**تاریخ:** ۱۴ اوت ۲۰۲۶  
**Theme:** 1.4.26  
**Plugin:** Alipasandi Service Content 1.3.1  
**Design System:** Locked — بدون تغییر Design/Color/Layout/Grid/Typography/Hero/URL  
**Production Approval:** **NO**

## خلاصه اجرایی

پاسخ جدید کلاینت به‌درستی تأکید کرد که مرحله بعد باید Runtime/Staging Evidence باشد، نه گزارش نظری. هنگام آماده‌سازی همان Evidence، چهار Contract جدید در Source 1.4.25/1.3.0 مشخص شد که قبل از Staging باید بسته می‌شدند؛ در غیر این صورت Evidence روی Buildی جمع می‌شد که خود Contract جدید را کامل رعایت نمی‌کرد:

1. `365` در Theme HTML و Plugin validator دو literal جدا بود و SSOT واقعی نبود.
2. Appointment non-page choices هنوز در secondary hard-coded extras list بودند؛ کلاینت صریحاً secondary list را رد کرد.
3. Registry malformed/unknown fields به‌اندازه کافی fail-closed + Health-visible نبودند.
4. PHP 8.0 genuine runtime در محیط Build قابل اجرا نبود؛ طبق شرط کلاینت، claim پشتیبانی از PHP 8.0 حذف و Minimum به **PHP 8.2** افزایش یافت.

به همین دلیل، طبق Rule خود کلاینت (`code byte change => new version + new hash + targeted regression`) Patch هدفمند **Theme 1.4.26 / Plugin 1.3.1** ساخته شد. هیچ تغییر Design/URL/Content architecture خارج از این scope انجام نشده است.

### Hashهای جدید

- Theme 1.4.26: `eebeea761197881caadce27a3eb92925f8495beaa4640a8c61a8aa0e06bee44a`
- Plugin 1.3.1: `0d08e0cbfd26d6d4e55084c1ffaf32e9e22ad217f39028a63cb81f0467879c29`

دو Build مستقل برای هر Artifact byte-identical شدند و ZIP integrity PASS است.

## Evidence واقعاً اجراشده در Build Workspace

این Evidence **Staging/WP Runtime محسوب نمی‌شود** و فقط همان چیزی است که واقعاً اجرا شده:

- Final-ZIP source audit: **20/20 PASS** روی Extract خود ZIPها.
- Local source-contract smoke: **35/35 PASS** روی PHP 8.4.23 با WordPress API stubs؛ Registry/Horizon/Date/E.164/Country/Geo/Opening Hours.
- Local form-security smoke: **7/7 PASS**؛ 5+1 rate limit، quota مستقل، invalid IP fail-closed، عدم raw-IP در transient key/value.
- Local mail-domain smoke: **6/6 PASS**؛ canonical domain/subdomain policy و reject کردن look-alike/external domain.
- تمام PHP فایل‌های ZIP نهایی روی PHP 8.4.23 syntax PASS هستند.

PHP 8.2 واقعی، WordPress 6.4، Production WordPress، Rank Math، SMTP/DNS، Server Headers، Browser/CWV/A11y و Restore هنوز فقط با محیط واقعی قابل PASS هستند و در این گزارش PASS اعلام نمی‌شوند.

---

## پاسخ به ۵۷ مورد

### 1. PHP compatibility واقعی

**تصمیم اعمال‌شده:** Minimum Supported PHP از **8.0 به 8.2** افزایش یافت. چون PHP 8.0 genuine runtime در Build Workspace وجود نداشت، پشتیبانی از آن حدس زده نشد. Header Theme، Header Plugin و readmeها همگی `Requires PHP: 8.2` هستند.

**PHP 8.2 actual runtime:** **BLOCKED/PENDING Staging/CI**؛ باید `php -l` + Theme activation + Plugin activation + migration + forms + Rank Math + Health روی PHP 8.2 واقعی اجرا شود.

### 2. WordPress 6.4 واقعی

**PENDING واقعی.** `Requires at least: 6.4` حفظ شد. Evidence runner داخل QA Pack برای ثبت `wp core version`، Meta registration، `revisions_enabled`، Migration dry-run/actual، Health و WP-CLI آماده است. PASS فقط پس از execution روی WP 6.4 صادر می‌شود.

### 3. Production WordPress version

**PENDING Owner/Hosting.** Script `collect-wordpress-runtime.sh` نسخه واقعی Production را ثبت می‌کند و باید smoke جدا روی همان exact version اجرا شود.

### 4. PHPUnit execution

دو suite source وجود دارد:

- Theme: `tests/test-seo-fallback.php` برای Page/Post/Home/Search/404/Archive/Feed + active/inactive/reactivated.
- Plugin: `tests/test-registry-and-validators.php` برای Registry schema/fail-closed، SSOT، horizon، date boundaries، opening hours، E.164/country/geo.

**وجود تست PASS؛ execution روی WordPress PHPUnit هنوز PENDING.** `run-wordpress-phpunit.sh` در QA Pack قرار دارد. نتیجه TestDox باید به Evidence متصل شود.

### 5. Rank Math exact version

**PENDING Staging.** `runtime-inspect.php` و collector مقدار `RANK_MATH_VERSION` / Pro version و plugin inventory را ثبت می‌کنند. Edition، License Owner و Update Policy نیز باید از Staging inventory/owner ثبت شوند.

### 6. OpeningHoursSpecification

Source integration حفظ و validator سخت‌گیرانه است؛ raw invalid value وارد Schema نمی‌شود. اما correct entity targeting، duplicate absence، split ranges، closed day و entity graph **PENDING real Rank Math Staging JSON-LD** هستند.

### 7. Opening Hours contract

**CLOSED در 1.4.26 docs.** `OPENING-HOURS-CONTRACT-FA.md` اکنون day-codeهای `MO TU WE TH FR SA SU`، نمونه `MO=09:00-13:00,14:00-18:00` و `FR=CLOSED` را صریح مستند می‌کند. همچنین صریح شده که holiday/temporary closure و date-specific override در 1.3.1 پشتیبانی نمی‌شوند و Feature جداگانه آینده می‌خواهند.

### 8. Booking horizon = 365 روز

**Code-level SSOT اصلاح شد.** Owner = Plugin function `alipasandi_booking_horizon_days()`؛ Theme HTML `max` و Server validator هر دو همین function را مصرف می‌کنند. Theme دیگر literal `365` ندارد.

Candidate فعلی = **365 روز**؛ **Clinic/Owner confirmation هنوز PENDING**. اگر Owner عدد کوتاه‌تری تصویب کند، چون behavior تغییر می‌کند باید version/hash جدید صادر شود.

### 9. Registry SSOT

**اصلاح شد.** همه گزینه‌های Appointment، شامل `consultation/root-canal/cleaning/other`، اکنون recordهای همان `alipasandi_service_registry()` هستند. Function/list ثانویه `alipasandi_appointment_service_extras()` حذف شد.

Theme dropdown و Server validator هر دو فقط `alipasandi_allowed_services()` را مصرف می‌کنند و آن function فقط از Registry metadata derive می‌شود.

### 10. Registry field contract

**اصلاح + مستند شد.** Schema دقیق شش field:

`key`, `label`, `bookable`, `page_slug`, `content_managed`, `icon`.

Unknown field، invalid/missing field برای entry جدید، invalid key، duplicate key، empty label، invalid bool، invalid page slug، content-managed بدون page، invalid icon => **Fail Closed**. Entry وارد UI/allowlist نمی‌شود و `service_registry_invalid` + `registry_issues` در Health ظاهر می‌شود.

### 11. Form unavailable behavior

Source behavior حفظ شد: mail config نامعتبر => interactive form render نمی‌شود؛ direct phone path باقی می‌ماند. Unavailable state اکنون `role=status`, `aria-live=polite`, `aria-atomic=true` دارد.

**Mobile UX/A11y Evidence: PENDING real browser.**

### 12. Notify Email

Hard-coded fallback وجود ندارد. Missing/invalid notify recipient در Operational Health = Critical. **Runtime Evidence PENDING.**

### 13. Mail From domain check

Policy صریح شد: exact canonical `home_url()` domain یا subdomain زیر همان project host پذیرفته می‌شود؛ look-alike/external domain رد می‌شود. Local smoke 6/6 PASS است. Subdomain استفاده‌شده در Production فقط با Hosting/SPF/DKIM/DMARC approval قابل قبول است.

**Staging/SMTP Evidence PENDING.**

### 14. From Name

Source filter + setting/business-name fallback وجود دارد. **Delivered header Evidence PENDING SMTP/inbox.**

### 15. SMTP/DNS

**HARD BLOCKER — PENDING.** SPF/DKIM/DMARC + SMTP provider + delivered headers لازم است.

### 16. `wp_mail=true`

Contract بدون تغییر: فقط acceptance توسط mail layer است. **Actual inbox receipt PENDING و Production Gate است.**

### 17. Rate limiting

Local isolated smoke روی logic واقعی Plugin: پنج request اول valid، ششم rate-limited، Contact/Appointment quota مستقل، invalid REMOTE_ADDR fail-closed، transient value فقط counter. **Staging form-submission matrix هنوز PENDING.**

### 18. Rate-limit HTTP/status UX

UI پیام فارسی واضح و direct contact path دارد. Architecture فعلی POST/Redirect/GET است؛ document نهایی عمداً 429 نمی‌شود. Security Notes توضیح می‌دهد اگر API/XHR endpoint آینده ساخته شود، آن endpoint باید `429` + `Retry-After` داشته باشد. **Browser Evidence PENDING.**

### 19. Non-atomic limitation

در `SECURITY-NOTES-1.4.26-FA.md` حفظ شد. Abuse-at-scale Owner = Edge/WAF/atomic counter.

### 20. IP privacy

Source و local smoke تأیید می‌کنند raw IP در transient value ذخیره نمی‌شود؛ transient key HMAC است و log invalid-IP فقط action را ثبت می‌کند. **Hosting/runtime log review PENDING.**

### 21. Trusted proxy

هیچ X-Forwarded-For arbitrary trust وجود ندارد. Runbook نمونه allowlisted proxy دارد. **Cloudflare/reverse-proxy topology از Hosting PENDING.**

### 22. Date validation

Local contract smoke PASS برای yesterday/today/+365/+366، `2026-02-29` invalid، malformed invalid و `2028-02-29` با horizon test مناسب. **همین matrix باید روی WP/PHP 8.2 Staging تکرار شود.**

### 23. Timezone

Code فقط `wp_timezone()`/`wp_timezone_string()` را معیار قرار می‌دهد؛ Health اگر `Asia/Tehran` نباشد Critical می‌شود. PHP/server timezone معیار validation نیست. **Staging Freeze value PENDING.**

### 24. Closed day

Parser Source و local smoke `FR=CLOSED` و split ranges را PASS کرده‌اند. اگر official hours تنظیم شود، closed day/time outside range reject می‌شود. اگر setting خالی باشد Availability ادعا نمی‌شود. **Real form test PENDING.**

### 25. Booking wording

Theme Source فقط «درخواست نوبت / تاریخ و ساعت پیشنهادی / تأیید پس از تماس» را استفاده می‌کند. Health wording flag حفظ شده است. **Migrated production-like DB scan PENDING** تا مطمئن شویم DB قدیمی wording قطعی ندارد.

### 26. NAP semantic validation

Source validator و local contract smoke E.164/country/geo/opening-hours را پوشش می‌دهند؛ Settings API validation موجود است. **Real Settings save/reload + booking/email/partial geo matrix PENDING WP Staging.**

### 27. E.164

Local PASS: `+989...` valid؛ missing `+`، letters، too short و leading zero after `+` reject. **Admin/Settings real save Evidence PENDING.**

### 28. Geo

Local PASS: lat `-90/90` و lng `-180/180` accepted؛ beyond boundary rejected. **Settings + Schema staging Evidence PENDING.**

### 29. Country

Local PASS: `IR` accepted، lowercase normalize/validate، invalid length rejected. **Settings persistence Evidence PENDING.**

### 30. Business Name / Address

Operational Health source برای business name، city/Nowshahr، region و address Critical issue دارد. **Staging health JSON PENDING.**

### 31. No stale domain — permanent CI

یک source-audit executable و repeatable در QA Pack اضافه شد: `ci-source-audit.sh`. روی **Extract خود ZIPهای نهایی** 20/20 PASS شده و stale old-domain/Gmail/Maps runtime references را Gate می‌کند.

### 32. Theme URI

Final ZIP Theme Header = `https://fasdent.ir/`. Readme/Manifest current pairing هم‌راستا هستند. Final-ZIP source audit version consistency PASS است.

### 33. Text domain

Source load paths وجود دارند و compiled catalog claim نداریم. **Runtime `load_theme_textdomain/load_plugin_textdomain` smoke PENDING WP.** `runtime-inspect.php` وضعیت loaded domain را ثبت می‌کند.

### 34. I18n claim

Header/Source scan PASS: claim `translation-ready` وجود ندارد. Catalog کامل ادعا نشده است.

### 35. accessibility-ready

Final source audit PASS: tag وجود ندارد. فقط پس از Audit کامل قابل بازگشت است.

### 36. Editor CSS

Dedicated `assets/css/editor.css` حفظ شده است. **Block Editor Desktop/RTL actual Evidence PENDING.**

### 37. Feed noindex

Source hook قبلاً تأیید شده؛ **actual `X-Robots-Tag: noindex, follow` curl/header PENDING.** `collect-http-evidence.sh` Feed headers را ذخیره می‌کند.

### 38. Attachment policy

**PENDING real Rank Math behavior** برای parented/orphan attachment و media index noise.

### 39. Indexability Matrix

Matrix current باقی است، اما PASS فقط با crawl status/robots/canonical/sitemap برای همه URL types. **PENDING.**

### 40. Canonical Host

**PENDING Hosting.** چهار variant باید direct 301/308 باشند و فقط final host=200. HTTP collector این Evidence را ذخیره می‌کند.

### 41. Security headers

**PENDING real response:** HSTS، nosniff، frame protection، Referrer-Policy و mixed-content review.

### 42. WPCS/PHPCS

**BLOCKED/PENDING.** `run-static-analysis.sh` در QA Pack fail-closed است اگر `phpcs` نصب نباشد؛ WPCS report واقعی هنوز تولید نشده و PASS اعلام نمی‌شود.

### 43. PHPStan/static/deprecated

**BLOCKED/PENDING.** همان runner PHPStan level مناسب را اجرا می‌کند وقتی tool نصب باشد؛ report واقعی لازم است.

### 44. Backup

**PENDING Hosting:** DB + uploads + exact Theme/Plugin + config inventory.

### 45. Restore rehearsal

**PENDING isolated restore:** Service/NAP/form/Rank Math/media/Health smoke پس از Restore لازم است.

### 46. Cache

**PENDING Production stack inventory + purge test.** Service edit visibility SLA باید ثبت شود.

### 47. `post_modified`

**PENDING real WP DB before/after evidence.**

### 48. Sitemap `lastmod`

**PENDING Rank Math sitemap before/after evidence.** تغییر جعلی روی page load ممنوع است.

### 49. CWV

**HIGH PENDING** روی exact new hashes؛ Mobile/Desktop LCP/INP/CLS.

### 50. Network images

Source assets تغییر نکرده‌اند. **Actual mobile network evidence PENDING:** responsive source، Hero non-lazy و dimensions.

### 51. Browser matrix

**HIGH PENDING:** iOS Safari، Android Chrome، Desktop Chrome/Firefox و Safari Desktop در صورت دسترسی.

### 52. A11y

**HIGH PENDING:** Frontend + Admin RTL، keyboard/focus/errors/menu/FAQ/zoom/contrast/touch targets.

### 53. Privacy Policy

Checklist وجود دارد ولی **Owner/Legal + actual stack verification PENDING** برای Form/SMTP logging/Analytics/Clarity/server logs.

### 54. Medical Claims

**HIGH PENDING:** Register باید از rendered final DB content تکمیل و پزشک sign-off کند. `+10,000` تا تأیید خاموش باقی می‌ماند.

### 55. Asset approvals

**HIGH PENDING:** doctor/implant/clinic imagery ownership/license approval.

### 56. Exact hash evidence

از این Release به بعد Evidence فقط برای Hashهای زیر معتبر است:

Theme 1.4.26  
`eebeea761197881caadce27a3eb92925f8495beaa4640a8c61a8aa0e06bee44a`

Plugin 1.3.1  
`0d08e0cbfd26d6d4e55084c1ffaf32e9e22ad217f39028a63cb81f0467879c29`

Hashهای 1.4.25/1.3.0 **SUPERSEDED FOR NEW EVIDENCE** هستند. هر Code byte بعدی => version/hash جدید + targeted regression.

### 57. Production Approval

| Gate | وضعیت فعلی |
|---|---|
| Source Architecture | **PASS WITH CONDITIONS** |
| Targeted Registry/Horizon Patch | **IMPLEMENTED + LOCAL REGRESSION PASS** |
| Artifact Integrity/Reproducibility | **PASS** |
| PHP 8.2 Runtime | **BLOCKED / PENDING** |
| WordPress 6.4 Runtime | **BLOCKED / PENDING** |
| Production WP Runtime | **BLOCKED / PENDING** |
| WordPress PHPUnit | **BLOCKED / PENDING** |
| Rank Math Runtime | **BLOCKED / PENDING** |
| SMTP/DNS | **BLOCKED / PENDING** |
| Server/Host | **BLOCKED / PENDING** |
| Backup/Restore | **BLOCKED / PENDING** |
| CWV/A11y/Browser | **HIGH PENDING** |
| Privacy/Medical/Asset approvals | **HIGH PENDING** |
| Production Approval | **NO** |

## مرحله بعد

از اینجا **هیچ گزارش نظری دیگری لازم نیست**. QA Pack همراه Release برای جمع‌آوری Evidence واقعی آماده شده است. مرحله بعد باید Deploy همین exact hashes روی Staging واقعی و اجرای:

- PHP 8.2 runtime/lint/activation
- WP 6.4 + Production WP smoke
- WordPress PHPUnit
- Migration/Health
- Rank Math inventory + JSON-LD graph
- forms/rate/date/timezone/NAP
- SMTP/DNS inbox
- HTTP/indexability/headers
- restore/cache/lastmod
- CWV/browser/A11y

باشد. فقط خروجی واقعی این اجراها می‌تواند Gateهای باقیمانده را PASS کند.
