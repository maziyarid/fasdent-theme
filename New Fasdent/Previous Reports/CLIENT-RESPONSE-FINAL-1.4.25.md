# پاسخ نهایی — Release 1.4.25 / Plugin 1.3.0

**تاریخ:** ۱۴ اوت ۲۰۲۶  
**Theme:** Dr Keyvan Alipasandi Dental Clinic **1.4.25**  
**Site Plugin:** Alipasandi Service Content **1.3.0**  
**Design System:** **LOCKED** — بدون تغییر Color/Layout/Grid/Typography/Hero/URL Structure  
**Production Approval:** **NO** — تا تمام BLOCKERهای محیطی روی Staging و Hashهای همین Release PASS شوند.

---

## 1. خلاصه اجرایی

پاسخ ۶۶ مورد Code-level بررسی Release 1.4.24 روی **Artifact جدید واقعی** اعمال شد. برخلاف Draft قبلی، در این سند «Implemented» فقط برای چیزی استفاده می‌شود که داخل ZIP جدید واقعاً وجود دارد؛ موارد Server/SMTP/DNS/Rank Math Runtime/WP Runtime/Restore/CWV/A11y بدون Evidence واقعی PASS اعلام نشده‌اند.

### Artifactهای نهایی این Release

| Component | Version | SHA-256 |
|---|---:|---|
| Theme | 1.4.25 | `00b45209dde2d98f227b4585c0fb71b2adc6ddc9e42a85fb8eb95888f224e35f` |
| Site Plugin | 1.3.0 | `4039615ee34a96b85355938da4dbcd4c8d0f51339810d6bdaedadbb9b0bce25b` |

**Release 1.4.24 / Plugin 1.2.0 اکنون Superseded است.** Release تاریخی دارای canonical fatal، **1.4.22** بود؛ 1.4.24 همان Buildی بود که Client آن را Code Review کرد و Findings این سند از آن استخراج شد.

---

## 2. تصمیم معماری نهایی

| Layer | Owner | Contract 1.4.25 / 1.3.0 |
|---|---|---|
| Theme | Presentation | Render/Layout + read-only emergency compatibility |
| Plugin | Business Logic | Service data/editor/migration/revisions/export/health + NAP + Forms + Validation |
| Rank Math | SEO Production Owner | Metadata/Canonical/Schema/Sitemap/Page redirects |
| SMTP/Hosting | Transport | SMTP delivery, DNS, headers, cache, host redirects |

### Plugin missing/outdated

- Interactive Contact/Appointment forms **fail closed** و Render نمی‌شوند.
- Theme هیچ Form Handler، Rate-limit، Mail، Migration، Meta Box یا Service Write موازی ندارد.
- Site Health/Admin Notice = Critical.
- Existing Service Meta/Options فقط برای emergency render خوانده می‌شوند.
- در rollback به Plugin 1.2.0، Theme 1.4.25 Hookهای شناخته‌شده Write/Form/Rank-Math-NAP/Mail آن نسخه را از مسیر Compatibility خارج می‌کند تا Production path دو Business Logic موازی نداشته باشد.

---

# 3. پاسخ کامل به ۶۶ مورد Client

## 1) Theme هنوز Business Logic fallback کامل داشت

**Status: FIXED IN ARTIFACT.**  
`functions.php`, `inc/service-meta.php`, `inc/forms.php` بازطراحی شدند. Theme دیگر Meta registration/editor/save/migration/form processing ندارد. Theme page auto-provisioning نیز حذف شد؛ Theme صرفاً Presentation و read-only compatibility است.

## 2) Form Handler fallback داخل Theme

**Status: FIXED IN ARTIFACT.**  
`inc/forms.php` در Theme فقط compatibility availability helper است و هیچ `admin_post_*` handler ندارد. Plugin missing/outdated ⇒ form unavailable state + direct contact path. Validation/Security/Rate-limit/Mail فقط Plugin-owned است.

## 3) Service Meta fallback داخل Theme

**Status: FIXED IN ARTIFACT.**  
Theme fallback فقط existing `_alipasandi_service` را می‌خواند و در نبود Meta می‌تواند legacy emergency render را بخواند. Register/Meta Box/Save/Migration/Revision ownership فقط Plugin است.

## 4) Legacy `service-data.php` lifecycle

**Status: FIXED CONTRACT + HEALTH SIGNAL.**  
Legacy فایل read-only است. پس از completed migration، استفاده از legacy fallback log/warn می‌شود و Plugin Health آن را قابل مشاهده می‌کند. حذف آن به یک Release آینده و فقط پس از Production migration + backup/export + stable-release evidence موکول است؛ Deadline مصنوعی 1.5.0 در این Release تحمیل نشده است.

## 5) Theme fallback NAP با Plugin Contract برابر نبود

**Status: FIXED.**  
Theme هیچ NAP write/default social/map/mail owner نیست. Read-only option access باقی است. Runtime SSOT در Plugin است. Site URL از `home_url('/')` می‌آید. Outdated Plugin operational filters نیز در compatibility mode خاموش می‌شوند.

## 6) Domain drift در Theme

**Status: FIXED + AUDITED.**  
Theme URI = `https://fasdent.ir/`. Stored `clinic_website` حذف شد و URL از `home_url('/')` derive می‌شود. Runtime scan برای old domain/Gmail/map default Finding ندارد.

## 7) FIGMA documentation domain قدیمی

**Status: FIXED DOCUMENT GOVERNANCE.**  
`docs/FIGMA-PHASE-1-PROMPT-FA.md` با **HISTORICAL / SUPERSEDED — NON-RUNTIME** در ابتدای فایل Label شده است. Domain تاریخی فقط در این context نگهداری شده و Runtime source نیست.

## 8) Service Registry و Appointment Services دو SSOT بودند

**Status: FIXED.**  
`includes/service-registry.php` SSOT ایجاد شد. Appointment core services از Registry derive می‌شوند؛ non-page extras فقط از `alipasandi_appointment_service_extras` به‌صورت explicit اضافه می‌شوند.

## 9) Registry باید Metadata داشته باشد

**Status: FIXED.**  
Contract شامل `key`, `label`, `bookable`, `page_slug`, `content_managed`, `icon` است و legacy string-list filter shape نیز normalize می‌شود تا breaking change بی‌دلیل ایجاد نشود.

## 10) Notify Email hard-coded Gmail fallback

**Status: FIXED.**  
Hard-coded Gmail fallback حذف شد. `clinic_notify_email` باید explicitly configured و valid باشد. Invalid/missing ⇒ form unavailable + Site Health Critical.

## 11) Mail From

**Status: FIXED AT CODE LEVEL; DELIVERY EVIDENCE PENDING.**  
`clinic_mail_from` باید valid و روی canonical home domain باشد. Form channel تا آن زمان operational نمی‌شود. Delivered final headers همچنان Staging/SMTP Gate است.

## 12) Mail From Name

**Status: FIXED.**  
`clinic_mail_from_name` اضافه شد و `wp_mail_from_name` filter از setting یا business name استفاده می‌کند؛ Default عمومی WordPress برای form identity مسیر عادی نیست.

## 13) Rank Math NAP integration — Opening Hours

**Status: FIXED AT CODE LEVEL; ENTITY EVIDENCE PENDING.**  
Validated Opening Hours به `OpeningHoursSpecification` تبدیل و فقط روی entityهای Dentist/LocalBusiness در `rank_math/json_ld` sync می‌شود. Raw textarea مستقیم Schema نمی‌شود.

## 14) Opening Hours Data Contract

**Status: FIXED / DOCUMENTED + VALIDATED.**  
Grammar رسمی در `docs/OPENING-HOURS-CONTRACT-FA.md` و validator Plugin: مثال `MO=09:00-13:00,14:00-18:00` و `FR=CLOSED`; day allowlist, 24h HH:MM, split ranges, overlap rejection, duplicate-day rejection. Timezone = WordPress timezone.

## 15) Booking Slots ≠ Availability

**Status: FIXED.**  
UI/CTA/form/email wording به «درخواست نوبت/زمان پیشنهادی» اصلاح شد. Server هیچ Claim درباره Real-time availability ندارد. Confirmation فقط پس از تماس کلینیک. Health نیز migrated service content با wording قطعی `رزرو نوبت/وقت` را flag می‌کند تا DB قدیمی این Claim را silent برنگرداند.

## 16) Date Range

**Status: FIXED.**  
Maximum horizon = **365 days** از Today در WordPress timezone. HTML `max` و Server validator هر دو enforce می‌کنند.

## 17) Day-of-week / closure validation

**Status: FIXED CONDITIONALLY ON OFFICIAL HOURS.**  
اگر official Opening Hours configured باشد، closed day و time خارج از range رد می‌شود. اگر Hours خالی باشد، سیستم availability را حدس نمی‌زند و فقط requested time list را ارائه می‌کند.

## 18) Calendar edge cases

**Status: FIXED IN VALIDATOR; WP RUNTIME MATRIX PENDING.**  
`DateTimeImmutable::createFromFormat` + format round-trip + WordPress timezone استفاده می‌شود. Invalid dates مثل `2026-02-29` و `2026-02-30` رد می‌شوند؛ leap example صحیح برای QA = `2028-02-29`. Today/future/past/+365 نیز contract دارند.

## 19) Rate-limit atomicity

**Status: LIMITATION EXPLICITLY DOCUMENTED.**  
Transient counter هنوز non-atomic است؛ این موضوع دیگر به‌عنوان Security guarantee قوی توصیف نمی‌شود. Abuse at scale owner = Edge/WAF یا atomic store مانند Redis. برای traffic فعلی local guard است.

## 20) Hash algorithm wording

**Status: FIXED.**  
Rate-limit key اکنون HMAC-SHA256 بر action + validated IP با WordPress salt است و در docs «one-way rate-limit identifier» نامیده می‌شود. Raw IP ذخیره نمی‌شود.

## 21) Unknown `REMOTE_ADDR`

**Status: FIXED FAIL-CLOSED.**  
Missing/invalid client identifier ⇒ operational warning در log + status `unavailable`; quota مشترک `unknown` ساخته نمی‌شود.

## 22) Trusted proxy filter

**Status: FIXED RUNBOOK.**  
Filter حفظ شده و safe example در `SECURITY-NOTES-1.4.25-FA.md` فقط پس از allowlist Proxy IPهای رسمی Hosting ارائه شده است. `X-Forwarded-For` arbitrary هرگز مستقیم trust نمی‌شود.

## 23) Rate-limit scope

**Status: FIXED / PRESERVED.**  
Key شامل action است؛ Contact و Appointment quota مستقل‌اند. Counter فقط بعد از validation موفق input مصرف می‌شود.

## 24) Success-after-mail

**Status: CODE CONTRACT PRESERVED; DELIVERY BLOCKER PENDING.**  
Success فقط با `wp_mail=true` برمی‌گردد، اما گزارش صریحاً آن را Delivery proof نمی‌داند. SMTP provider + inbox receipt + full delivered headers + SPF/DKIM/DMARC برای Production لازم است.

## 25) NAP Settings semantic validation

**Status: FIXED.**  
Plugin sanitizer اکنون URL/email/E.164/country/geo/booking times/opening hours را semantic validate می‌کند؛ invalid settings previous valid value را overwrite نمی‌کنند.

## 26) E.164

**Status: FIXED.**  
Pattern: `+` + non-zero country-leading digit + 8–15 total digits after plus range policy (`^\+[1-9][0-9]{7,14}$`). Health invalid/missing را Critical می‌کند.

## 27) Country code

**Status: FIXED.**  
Uppercase two-letter format validate می‌شود (`IR` contract). Invalid input rejected.

## 28) Geo range

**Status: FIXED.**  
Latitude `-90..90`; Longitude `-180..180`. Numeric-but-impossible values مثل `999` پذیرفته نمی‌شوند.

## 29) Partial Geo

**Status: FIXED.**  
Lat بدون Lng یا برعکس = Health Critical. Schema فقط pair معتبر را output می‌کند.

## 30) Website Option duplicate SSOT

**Status: FIXED.**  
`clinic_website` از Plugin fields حذف شد. Compatibility alias فقط `home_url('/')` برمی‌گرداند و value مستقل ذخیره نمی‌شود.

## 31) Business Name fallback hard-coded در Theme Schema

**Status: FIXED.**  
Theme emergency Schema از read-only NAP option استفاده می‌کند؛ business data مستقل hard-coded parallel ندارد.

## 32) Region/Country fallback drift

**Status: FIXED.**  
Theme emergency Schema region/country را از read-only NAP options می‌گیرد و fields ناموجود را fabricate نمی‌کند.

## 33) Theme SEO fallback regression

**Status: IMPLEMENTED AS PERMANENT TEST SOURCE; WP TEST EXECUTION PENDING.**  
`tests/test-seo-fallback.php` برای 8 context shipped است: published Page, published Post, Home, Search, 404, Archive, Feed, و active/inactive/reactivated SEO-owner behavior. Theme SEO admin/write ownership حذف شده؛ fallback read-only است.

## 34) Rank Math entity detection

**Status: CODE CORRECT; STAGING EVIDENCE PENDING.**  
Filter فقط Dentist/LocalBusiness را target می‌کند. Exact Rank Math version باید در Staging ثابت کند entity type موردنظر واقعاً وجود دارد.

## 35) `@id` preservation

**Status: CODE PRESERVES; GRAPH DIFF PENDING.**  
Filter `@id` را overwrite نمی‌کند. Production Gate = before/after entity graph و عدم duplicate identity.

## 36) KML / Local Sitemap

**Status: CONFIGURATION GATE — NOT CLAIMED PASS.**  
تا وقتی Rank Math inventory و SSOT evidence واقعی نداریم باید Disabled بماند. Source filter به‌تنهایی KML sync را اثبات نمی‌کند.

## 37) Feed Indexability

**Status: SOURCE IMPLEMENTED; HEADER CRAWL PENDING.**  
Theme `X-Robots-Tag: noindex, follow` برای Feed می‌فرستد. Real HTTP header و sitemap exclusion باید روی Staging/Production crawl شود.

## 38) Attachment redirect

**Status: POLICY DOCUMENTED; RANK MATH RUNTIME PENDING.**  
Parent exists ⇒ 301 Parent. Orphan attachment page index نمی‌شود؛ media-file behavior باید روی نسخه واقعی Rank Math verify شود. Theme Handler موازی برای redirect ساخته نشده چون SEO owner = Rank Math.

## 39) Privacy Policy

**Status: CHECKLIST SHIPPED; LEGAL/OWNER CONTENT PENDING.**  
`docs/PRIVACY-POLICY-CONTENT-CHECKLIST-FA.md` Contact, appointment request, SMTP/provider logs, analytics/consent, Clarity, server logs و third parties را پوشش می‌دهد. متن نهایی واقعی باید Owner/Legal تأیید کند.

## 40) Theme Header Metadata

**Status: FIXED.**  
Theme URI و version current هستند؛ stale runtime domain حذف شده است.

## 41) Text Domain

**Status: CODE PATH CORRECT; RUNTIME SMOKE PENDING.**  
Theme = `alipasandi-clinic`; Plugin = `alipasandi-service-content`. Plugin `load_plugin_textdomain` اضافه شد. Runtime catalog loading هنوز Staging gate است.

## 42) Language folder / I18n claim

**Status: FIXED GOVERNANCE.**  
Theme و Plugin `languages/README.md` دارند؛ compiled catalog در این release وجود ندارد و Release ادعای translation-ready کامل نمی‌کند.

## 43) `accessibility-ready` tag

**Status: FIXED.**  
این tag از Theme Header حذف شد تا قبل از audit کامل WordPress/WCAG claim غیراثبات‌شده نداشته باشیم. A11y همچنان Production HIGH Gate است.

## 44) Editor styles

**Status: FIXED.**  
Full frontend `theme.css`/`rtl.css` دیگر به Editor inject نمی‌شود؛ dedicated `assets/css/editor.css` استفاده می‌شود. Block Editor Runtime QA همچنان انجام شود.

## 45) Hard-coded URLs audit

**Status: SOURCE AUDIT PASS.**  
Runtime old-domain/Gmail/map/social defaults حذف شدند. Inventory در `docs/DOMAIN-URL-INVENTORY-1.4.25.md` است. تنها old domain باقی‌مانده در Historical FIGMA document با label صریح است.

## 46) Social defaults

**Status: FIXED.**  
Instagram/WhatsApp/Telegram default خالی است؛ فقط explicit official Plugin settings render می‌شوند. Empty value ⇒ link اصلاً output نمی‌شود.

## 47) Map default

**Status: FIXED.**  
Hard-coded Maps URL از Theme/design handoff runtime data حذف شده. فقط explicit `clinic_maps` setting استفاده می‌شود.

## 48) Content / Medical Claims

**Status: GOVERNANCE SHIPPED; DOCTOR APPROVAL PENDING.**  
`docs/MEDICAL-CLAIM-REGISTER-FA.md` shipped است. `+10,000` default=false می‌ماند. Final register باید از rendered DB content استخراج و توسط پزشک review شود؛ source ZIP به‌تنهایی claim approval نیست.

## 49) Production timezone

**Status: CODE CONTRACT FIXED; ENVIRONMENT PENDING.**  
Validator از `wp_timezone()` استفاده می‌کند. Health می‌خواهد `wp_timezone_string() === Asia/Tehran`. PHP/server `date_default_timezone_get()` معیار Production نیست.

## 50) Health Check توسعه

**Status: FIXED.**  
Operational Health شامل notify email, From, From-domain, display phone, E.164, address, country, business/city/region, partial/out-of-range geo, booking slots, opening hours validity, WP timezone, Theme version, Rank Math active, legacy fallback usage و appointment-title request wording است. Service Health نیز duplicate key/meta/schema/image/size/booking-language را بررسی می‌کند.

## 51) Site Plugin Deactivation Test

**Status: EXPECTED BEHAVIOR IMPLEMENTED; REAL TEST PENDING.**  
Expected: Home/Service render from existing data; Contact/Appointment UI render ولی forms disabled; no form processing; admin/site health critical. Runtime deactivation matrix باید روی Staging ثبت شود.

## 52) Plugin Missing Form UX

**Status: FIXED.**  
Interactive form اصلاً render نمی‌شود؛ unavailable message + configured direct contact path. No stale Theme Handler.

## 53) Plugin Update Rollback

**Status: FIXED CONTRACT.**  
`docs/RELEASE-ROLLBACK-CONTRACT-1.4.25-FA.md` shipped. Data schema v1 محفوظ است. Theme 1.4.25 با Plugin 1.2.0 فقط compatibility/read-only path را مجاز می‌کند. Theme 1.4.24 compatibility-only است؛ Production pairing 1.4.25/1.3.0 است.

## 54) Service Registry future migration

**Status: FIXED CONTRACT.**  
افزودن Service پنجم باید Registry + page association + form + navigation + sitemap/indexability + schema + migration/health + content approval + redirect/SEO + Staging QA را طی کند. Contract در `SERVICE-REGISTRY-CONTRACT-FA.md`.

## 55) Runtime Blockers

**Status: STILL BLOCKER / HONESTLY PENDING.**  
Local workspace: تمام PHP files با **PHP 8.4.23** syntax-lint PASS؛ JS با Node 22 syntax PASS؛ JSON parse PASS. اما این‌ها جای PHP 8.0/8.2 runtime، WP 6.4 activation، production-WP smoke، WPCS/static/deprecated و WordPress PHPUnit را نمی‌گیرند. این tests باید CI/Staging اجرا شوند.

## 56) Restore rehearsal

**Status: PENDING ENVIRONMENT.**  
DB + uploads + exact artifact restore در isolated environment، سپس Service/NAP/Form/Media/Rank Math/Sitemap smoke و Health exit status لازم است.

## 57) Rank Math inventory

**Status: PENDING STAGING.**  
Edition/version/license/update owner/modules/entity types/redirect config/sitemap/meta samples باید از actual install ثبت شوند.

## 58) Server evidence

**Status: PENDING HOSTING.**  
HTTP/HTTPS و www/non-www status/location/chain count، HSTS/security headers و canonical host از ZIP قابل اثبات نیستند.

## 59) Sitemap / Canonical matrix

**Status: CONTRACT UPDATED; CRAWL PENDING.**  
`docs/INDEXABILITY-MATRIX-FA.md` current است. برای تمام URL types باید status/robots/canonical/sitemap inclusion روی Staging crawl شود.

## 60) CWV

**Status: PENDING FINAL HASH RUNTIME.**  
Mobile/Desktop LCP/INP/CLS روی همین final hash و production-like cache/third-party stack لازم است.

## 61) A11y

**Status: PENDING AUDIT.**  
Keyboard/focus/form errors/mobile menu/FAQ/zoom/contrast/touch/Admin RTL و reduced-motion audit لازم است. به همین دلیل `accessibility-ready` tag حذف شد.

## 62) Security headers

**Status: PENDING SERVER EVIDENCE.**  
هیچ Header به‌دلیل وجود در Draft یا checklist PASS اعلام نشده است. Response-header crawl واقعی لازم است.

## 63) SMTP / DNS

**Status: PENDING DELIVERY EVIDENCE.**  
SPF/DKIM/DMARC + provider config + inbox receipt + full delivered headers لازم است. Source code فقط configuration contract را enforce می‌کند.

## 64) Cache

**Status: PENDING HOSTING.**  
Page/Object/CDN stack inventory + purge test + Service edit visibility باید ثبت شود.

## 65) `post_modified` / `lastmod`

**Status: PENDING STAGING.**  
Service edit before/after `post_modified` و Rank Math Sitemap lastmod باید واقعی ثبت شود. Fake lastmod ممنوع است.

## 66) Final Evidence tied to exact hashes

**Status: NEW HASHES GENERATED — FUTURE EVIDENCE MUST MATCH THEM.**

- Theme 1.4.25: `00b45209dde2d98f227b4585c0fb71b2adc6ddc9e42a85fb8eb95888f224e35f`
- Plugin 1.3.0: `4039615ee34a96b85355938da4dbcd4c8d0f51339810d6bdaedadbb9b0bce25b`

اگر بعد از این build حتی یک byte کد/مستند داخل Theme/Plugin تغییر کند، Version/Hash و Evidence باید دوباره تولید شود.

---

# 4. موارد تکمیلی که علاوه بر Draft اولیه اصلاح شدند

1. Theme page auto-provisioning حذف شد؛ Theme دیگر Page/Option write روی activation ندارد.
2. Theme SEO Meta Box/Save logic حذف شد؛ Rank Math owner واقعی شد، نه فقط owner در documentation.
3. Theme 1.4.24 به‌درستی **Superseded compatibility build** طبقه‌بندی شد؛ Known Critical canonical release = 1.4.22.
4. Timezone gate به‌جای PHP/server timezone از WordPress `wp_timezone()`/`wp_timezone_string()` استفاده می‌کند.
5. Leap-date QA اصلاح شد: `2026-02-29` نامعتبر است؛ نمونه leap معتبر `2028-02-29` است.
6. New-build Artifact Integrity فقط بعد از ساخت ZIP و Hash جدید PASS اعلام شد؛ Hash قدیمی 1.4.24 برای Release جدید reuse نشده است.
7. Security headers/DNS/SMTP/CWV/A11y هیچ‌کدام بدون environment evidence با علامت PASS ثبت نشده‌اند.
8. Plugin 1.2 rollback path علاوه بر form/admin hooks، Rank Math NAP و mail filters قدیمی را نیز در Theme 1.4.25 compatibility mode غیرفعال می‌کند.
9. Social/Map/Phone/Address حساس به Production از Theme/design-token defaults حذف شدند تا unverified operational data live نشود.
10. Historical docs با label صریح جدا شدند؛ current runtime docs به old domain متکی نیستند.

---

# 5. Build Validation واقعی این Workspace

### PASS

- Deterministic Theme ZIP creation + archive integrity.
- Deterministic Plugin ZIP creation + archive integrity.
- PHP syntax sweep: **27 Theme PHP + 8 Plugin PHP** با PHP **8.4.23**.
- Theme JS syntax: Node **22.16.0**.
- `assets/design-tokens.json` parse.
- Static source checks: no stale runtime old-domain/Gmail/map defaults; no Theme business-data write hooks; no Theme form-handler hooks; versions/headers; dedicated editor CSS; Opening Hours schema integration; WP-timezone validator; Mail From Name; Registry metadata; rate key; fail-closed client IP; Feed noindex source; permanent SEO regression test source.

### NOT RUN / STILL REQUIRED

- PHP 8.0 runtime/lint.
- PHP 8.2 runtime/lint.
- WordPress 6.4 activation smoke.
- Actual Production WordPress version smoke.
- WordPress PHPUnit execution of SEO regression suite.
- WPCS / PHPStan / deprecated scan (tools absent in this workspace).
- Backup/restore rehearsal.
- Rank Math live inventory/entity graph/sitemap/redirect behavior.
- Host redirects/security headers.
- SMTP/DNS/inbox delivery.
- Cache/CWV/A11y/browser/mobile/Admin RTL.
- Search Console/GBP/post-launch monitoring.

---

# 6. Deployment order for Staging

1. Record DB + uploads backup and rollback point.
2. Install **Plugin 1.3.0**.
3. Install **Theme 1.4.25**.
4. Confirm WordPress timezone = `Asia/Tehran`.
5. Configure NAP/Phone/E.164/Address/Notify Email/domain From/From Name/Booking Times/Official Opening Hours with clinic-approved values.
6. Run explicit Service Migration/Retry.
7. Run Service + Operational Health; resolve all Critical items.
8. Configure/verify Rank Math against Indexability Matrix; keep KML/Local Sitemap disabled until SSOT evidence.
9. Run Theme switch, Plugin deactivation/outdated rollback, revision/autosave/partial POST, forms/date/rate-limit tests.
10. Run SMTP/DNS delivered-inbox test.
11. Run host/canonical/sitemap/security-header crawl.
12. Restore rehearsal in isolated environment.
13. CWV/A11y/browser/mobile/Admin RTL/cache/lastmod tests.
14. Approve Privacy Policy + Medical Claim Register + Asset licenses.
15. Collect Evidence Register tied to the two SHA-256 hashes above.
16. Only then request Production Approval.

---

# 7. Final Status

| Area | Status |
|---|---|
| Design System | ✅ LOCKED |
| New Theme Artifact | ✅ 1.4.25 BUILT |
| New Plugin Artifact | ✅ 1.3.0 BUILT |
| Artifact integrity | ✅ PASS for generated ZIPs |
| Code architecture changes | ✅ IMPLEMENTED |
| Security/data validation changes | ✅ IMPLEMENTED at source level |
| Local syntax/static validation | ✅ PASS where listed |
| PHP 8.0/8.2 + WP runtime | ❌ PENDING |
| Staging environmental evidence | ❌ PENDING |
| Privacy/Claim/Asset owner approvals | ❌ PENDING |
| Production Approval | ❌ **NO** |

**نتیجه:** Release 1.4.25 / Plugin 1.3.0 یک Build واقعی جدید با اصلاحات Code-level Client است؛ مرحله بعد فقط Evidence-based Staging QA روی Hashهای دقیق همین سند است. هیچ Gate محیطی از Source ZIP به‌صورت مصنوعی PASS اعلام نشده است.
