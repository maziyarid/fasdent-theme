# پاسخ نهایی — Release 1.4.28 / Plugin 1.3.3

**تاریخ:** ۱۴ اوت ۲۰۲۶  
**Theme:** 1.4.28  
**Site Plugin:** Alipasandi Service Content 1.3.3  
**Design System:** Locked؛ بدون تغییر Design/Color/Layout/Grid/Typography/Hero/URL  
**Production Approval:** **NO** — این Release فقط پس از Runtime/Staging/Owner Evidence قابل Freeze است.

## جمع‌بندی اجرایی

Code Review کلاینت درباره Plugin 1.3.2 درست بود: literal sequence برابر `\tWP_CLI::add_command(...)` در executable PHP واقعاً داخل Artifact وجود داشت. `php -l` آن را Syntax Error نمی‌دید زیرا PHP آن token را به‌صورت یک نام کلاس fully-qualified متفاوت parse می‌کرد؛ بنابراین Plugin 1.3.2 برای WP-CLI Freeze قابل قبول نبود.

در 1.3.3 این blocker فقط با حذف typo بسته نشده است؛ failure mode به Permanent Gate تبدیل شد:

- command registration صحیح برای `migrate`، `schema-repair` و `health`؛
- source audit مستقل برای literal escaped-whitespace class tokenها؛
- executable local WP-CLI registration/bootstrap smoke؛
- اجرای واقعی callback مربوط به `schema-repair` dry-run در smoke محلی؛
- apply روی environment=`production` در خود کد block می‌شود؛
- apply فقط WP-CLI است و direct wp-admin Apply حذف شده؛
- dry-run اکنون `plan_sha256` تولید می‌کند و Apply فقط با `--confirm-plan-sha=<SHA>` همان plan اجرا می‌شود؛
- plan باید **کامل PASS** باشد؛ وجود حتی یک `unknown_schema_version` / `meta_not_array` / `sanitizer_would_change_payload` یا drift کل Apply را قبل از اولین Write متوقف می‌کند؛
- تمام repairها قبل از اولین Write دوباره revalidate می‌شوند؛
- بعد از Write marker و payload hash verify می‌شوند و در verify failure rollback best-effort انجام می‌شود.

Theme به 1.4.28 bump شد چون Contract فعلی Production Pair را صریحاً version-lock می‌کند. تغییر Theme در این Release Design/UI جدید نیست؛ production companion minimum از Plugin 1.3.2 به 1.3.3 منتقل شده و Documentation/Manifest یک Pair واحد دارند.

## Artifact hashes

| Artifact | SHA-256 |
|---|---|
| Theme 1.4.28 | `1e07600cadd4bbb50de2edd7cc1e2e8379d76fdfcef980c3f1718ea1976a8100` |
| Plugin 1.3.3 | `bf01b35e02abecc6e64800f851e442761e70d8bf3348a3775fcac88df9d4768f` |

هر Code-byte change بعدی = Version/Hash جدید + Targeted Regression جدید.

---

# پاسخ به Review جدید

## BLOCKER 1 — WP-CLI registration typo

**Status: FIXED IN 1.3.3 / final-ZIP source + executable bootstrap PASS; real WP-CLI runtime still required.**

خط معیوب 1.3.2 بازتولید شد و در 1.3.3 برابر `WP_CLI::add_command(...)` است. Final-ZIP audit وجود literal `\tWP_CLI` / `\nWP_CLI` / `\rWP_CLI` را رد می‌کند.

Local bootstrap smoke سه command را register و callback dry-run را invoke کرده است:

- `wp alipasandi service migrate`
- `wp alipasandi service schema-repair`
- `wp alipasandi service health`

این Local executable smoke جای WordPress/WP-CLI واقعی را نمی‌گیرد؛ Staging باید raw output commandهای واقعی را ثبت کند.

## BLOCKER 2 — Permanent CI Gate

**Status: IMPLEMENTED.**

`qa/ci-source-audit.sh` علاوه بر `php -l` موارد زیر را Gate می‌کند:

1. suspicious escaped-whitespace class token در executable PHP؛
2. historical WP_CLI literal escape variants؛
3. version/pairing drift؛
4. WP-CLI command registration source؛
5. schema-repair Production guard + confirmed dry-run contract؛
6. Rank Math observation version/freshness؛
7. static front-page Health؛
8. previous Registry/Date/SEO/Form/source ownership contracts.

همچنین `qa/wp-cli-bootstrap-smoke.php` executable path را اجرا می‌کند. Final ZIP نتیجه **40 PASS / 0 FAIL** دارد.

Real CI/Staging gate همچنان باید این‌ها را اجرا کند:

```bash
wp --info
wp plugin status alipasandi-service-content
wp alipasandi service health
wp alipasandi service schema-repair
```

`schema-repair --apply` فقط طبق procedure بخش Schema Repair پایین مجاز است.

## BLOCKER 3 — Production WP_DEBUG_DISPLAY

**Status: REAL PRODUCTION BLOCKER / Hosting-config owner. Source Health guard اضافه شد؛ Environment هنوز باید Fix شود.**

Evidence واقعی Production نشان داد:

- `WP_DEBUG=true`
- `WP_DEBUG_DISPLAY=true`
- `WP_DEBUG_LOG=true`

1.3.3 در Operational Health، `WP_DEBUG_DISPLAY=true` روی `production` را Critical گزارش می‌کند. خود Plugin عمداً `wp-config.php` را تغییر نمی‌دهد.

قبل Freeze:

- `WP_DEBUG_DISPLAY=false`؛
- ترجیح Production: `WP_DEBUG=false` و استفاده از server/private operational logging؛
- اگر `WP_DEBUG_LOG` عمداً حفظ شود: exact log path، عدم web accessibility، file permissions، retention، rotation و PII/secret review ثبت شود.

این مورد با Build جدید به‌تنهایی PASS نمی‌شود.

## BLOCKER 4 — چهار Service Production = schema_invalid

**Status: SAFE TOOLING HARDENED / DATA REPAIR STILL STAGING-ONLY.**

Production snapshot فعلی `migration_completed=true` و چهار Service=`schema_invalid` داشت. Apply روی Live ممنوع است.

### Procedure اجباری

1. Clone ایزوله Staging از Production.
2. DB Backup + uploads/artifact/config backup.
3. `WP_ENVIRONMENT_TYPE` روی Clone صریحاً `staging` باشد؛ اگر unset بماند WordPress ممکن است آن را `production` بداند و Plugin Apply را block می‌کند.
4. Service JSON Export و SHA قبل از Repair ثبت شود.
5. اجرا:

```bash
wp alipasandi service schema-repair > schema-repair-dry-run.json
```

6. تمام چهار Service فقط در یکی از stateهای safe باشند:
   - `skip_current / already_current`؛ یا
   - `repair_missing_v1_marker / safe_content_equivalent`.
7. هر `unknown_schema_version`، `sanitizer_would_change_payload`، `meta_not_array`، `meta_missing`، page/identity conflict یا hash drift = **STOP / NO APPLY**.
8. `plan_sha256` از dry-run ثبت شود.
9. فقط روی همان Clone:

```bash
wp alipasandi service schema-repair --apply --confirm-plan-sha=<PLAN_SHA256>
```

10. بعد Apply: `schema_version=1`، payload SHA بدون marker قبل/بعد برابر، Service Health، H1/Intro/FAQ/CTA/images/links و visual/content diff ثبت شوند.

Plugin 1.3.3 Apply مستقیم روی Production را در code block می‌کند و بدون matching dry-run SHA روی Staging نیز Write نمی‌کند.

## BLOCKER 5 — Operational Production configuration ناقص

**Status: OWNER / DATA BLOCKER.**

Production evidence فعلی missing/invalid برای Notify Email، Mail From، display phone، E.164، address، booking times و appointment config گزارش کرده است. Defaultها Approval نیستند.

قبل Form/Local SEO Runtime QA باید NAP Freeze زیر توسط Owner امضا شود:

| Field | Frozen value | Owner approval |
|---|---|---|
| Business name | **PENDING OWNER** | PENDING |
| Display phone | **PENDING OWNER** | PENDING |
| E.164 phone | **PENDING OWNER** | PENDING |
| Address | **PENDING OWNER** | PENDING |
| Street | **PENDING OWNER** | PENDING |
| City | **PENDING OWNER** | PENDING |
| Region | **PENDING OWNER** | PENDING |
| Country | **PENDING OWNER** | PENDING |
| Postal code | **PENDING OWNER** | PENDING |
| Geo lat/lng | **PENDING OWNER** | PENDING |
| Map URL | **PENDING OWNER** | PENDING |
| Notify Email | **PENDING OWNER** | PENDING |
| Mail From | **PENDING OWNER** | PENDING |
| Mail From Name | **PENDING OWNER** | PENDING |
| Booking Slots | **PENDING OWNER** | PENDING |
| Opening Hours | **PENDING OWNER / intentionally empty decision allowed** | PENDING |
| Instagram | **PENDING OWNER** | PENDING |
| WhatsApp | **PENDING OWNER** | PENDING |
| Telegram | **PENDING OWNER** | PENDING |

`نوشهر`/`مازندران`/`IR` یا هر default دیگر به‌تنهایی sign-off محسوب نمی‌شود.

## BLOCKER 6 — Rank Math Production Owner inactive

**Status: CURRENT PRODUCTION FINAL SEO OWNER NOT ACTIVE.**

Inventory واقعی:

- Rank Math **Free 1.0.276**؛
- auto-update disabled؛
- Search Console permission not granted؛
- token not configured؛
- برای Evidence موقت فعال و سپس deactivated شده است.

بنابراین Current Live state را «Rank Math Owner فعال» اعلام نمی‌کنیم. وقتی Rank Math غیرفعال است، Theme فقط **emergency failure fallback** است؛ این fallback Production SEO ownership نهایی نیست.

Final contract:

1. Rank Math exact version در Staging freeze شود؛
2. Active→Inactive→Reactivated regression کامل اجرا شود؛
3. سپس Rank Math روی Production به‌عنوان Owner فعال بماند؛
4. update policy + operational owner ثبت شود.

## BLOCKER 7 — Loopback 503 / WP-Cron

**Status: REAL HOSTING BLOCKER / no source-level PASS claim.**

Production Site Health loopback=`503` و failed scheduled event=`recovery_mode_clean_expired_keys` را نشان داده است. تا Hosting root cause را Fix نکند:

- WP-Cron/automation/background-cleanup QA PASS اعلام نمی‌شود؛
- loopback باید PASS شود؛
- failed event پاک/Reschedule و سپس اجرا شود؛
- Site Health rerun raw evidence ذخیره شود.

---

# HIGH items

## HIGH 1 — Rank Math local-entity observation freshness

**Status: IMPLEMENTED IN 1.3.3 / Runtime Pending.**

Transient دیگر string ساده نیست و شامل:

- `state`
- `observed_at`
- exact `RANK_MATH_VERSION`
- context

است. Health فقط observation تازه و متعلق به همان Rank Math version را معتبر می‌داند. Version mismatch یا stale/missing observation = operational issue تا Front/Contact با exact Rank Math دوباره observe شود. Timestamp در Health JSON قابل Evidence است.

## HIGH 2 — Static Front Page Health

**Status: IMPLEMENTED / Runtime Pending.**

Health اکنون بررسی می‌کند:

- `show_on_front = page`؛
- `page_on_front` یک Published Page معتبر؛
- `page_for_posts` یک Published Page معتبر؛
- Front و Blog page یک ID نباشند.

هدف جلوگیری از SEO failure-mode اشتباه و noindex شدن ناخواسته Homepage در صورت Reading Settings drift است.

## HIGH 3 — Rank Math deactivation SEO Policy Diff

**Status: RUNBOOK REQUIRED / Runtime Pending.**

روی exact new hashes Active→Inactive→Reactivated برای Home، Appointment، Blog archive، Service، Search، 404 و Feed باید HTTP/robots/canonical/sitemap/schema capture شود. Fatal=0 به‌تنهایی PASS نیست.

## HIGH 4 — WordPress Core Sitemap failure mode

**Status: SOURCE PASS / Runtime Pending.**

Theme emergency guard فقط وقتی Rank Math absent است Core sitemap را disable می‌کند. Runtime باید ثابت کند active state تداخل ندارد، inactive state Core sitemap expose نمی‌شود و reactivation مالکیت Sitemap را دوباره یک‌لایه می‌کند.

## HIGH 5 — Runtime identity lanes

**Status: CONTRACT FROZEN / execution Pending.**

سه Identity جدا:

1. Minimum compatibility lane: **WP 6.4 + PHP 8.2**؛
2. Production-identical lane: **WP 7.0.4 + PHP 8.5.7**؛
3. Runtime Staging evidence باید exact Theme/Plugin hashes را ثبت کند.

Production 8.5.7 اثبات PHP 8.2 نیست.

## HIGH 6 — Minimum claim

**Status: BLOCKED UNTIL REAL LANE.**

PHP 8.2 و WP 6.4 claims بدون actual lane PASS نمی‌شوند. اگر نگهداری این lane در CI عملی نباشد، در Release آینده minimum باید مطابق support واقعی بالا رود؛ بدون تست claim نگه داشته نمی‌شود.

## HIGH 7 — Real schema-repair WP-CLI execution

**Status: Local bootstrap PASS / real Staging WP-CLI Pending.**

بعد Deploy exact new hashes، procedure Blocker 4 اجرا شود. `--apply` روی Production در code ممنوع است.

## HIGH 8 — NAP Owner Freeze

**Status: OWNER PENDING.**

جدول Freeze در Blocker 5 باید تکمیل، timestamp و signer/owner داشته باشد. Health green جای sign-off را نمی‌گیرد.

## HIGH 9 — Opening Hours

**Status: OWNER DECISION PENDING.**

Production فعلاً Opening Hours ندارد. یکی از دو حالت باید Freeze شود:

- **A:** ساعات رسمی تأیید و تنظیم شوند؛ یا
- **B:** عمداً خالی بمانند و `openingHoursSpecification` intentionally omitted باشد.

حدس/Default ممنوع. Holiday/temporary/date-specific closures در 1.3.3 unsupported هستند و Feature جدا لازم دارند.

## HIGH 10 — Booking Horizon / Lead Time

**Status: BUSINESS APPROVAL PENDING.**

Current code candidate:

- Horizon = **365 days**؛
- Lead time = **0 minutes**, اما selected datetime باید strictly future باشد.

تغییر هرکدام behavior change است و Version/Hash جدید می‌خواهد؛ بنابراین قبل Staging Freeze Owner باید همین دو مقدار یا مقدار جایگزین را کتبی approve کند.

## HIGH 11 — Mail/DNS

**Status: BLOCKED.**

Actual delivered email + full headers باید From، From Name، Return-Path، SPF، DKIM، DMARC و inbox receipt را اثبات کند. `wp_mail=true` delivery proof نیست.

## HIGH 12 — Production Debug Log

**Status: BLOCKED / Hosting.**

اگر logging بعد از خاموش‌شدن display حفظ شود: log path، web accessibility، permissions، retention/rotation و PII/secret review ثبت شود. Publicly retrievable `debug.log` غیرقابل قبول است.

## HIGH 13 — Loopback effect on automation

**Status: BLOCKED until 503 fixed.**

هیچ cron/automation/background-cleanup PASS قبل از loopback fix ثبت نمی‌شود.

## HIGH 14 — Cache strategy

**Status: OWNER/HOSTING PENDING.**

Site Health page cache detect نکرده، ولی این به‌تنهایی Launch blocker نیست. Hosting باید inventory واقعی Page cache/Object cache/CDN و purge/SLA را ثبت کند. Cache صرفاً برای score نصب نمی‌شود.

## HIGH 15 — Medical claims

**Status: DOCTOR/OWNER PENDING.**

Claim Register باید Service Meta **و** Hard-coded Home/Services/About/CTA copy را پوشش دهد؛ از جمله outcome/comparative/expertise/brand statements. `+10,000` تا sign-off = OFF.

## HIGH 16 — Privacy

**Status: OWNER/LEGAL PENDING.**

Privacy باید stack واقعی Forms، SMTP/mail logs، Clarity، Analytics و server/debug logs را منعکس کند.

## HIGH 17 — Asset rights

**Status: OWNER PENDING.**

Doctor/implant/clinic imagery ownership/license sign-off لازم است.

## HIGH 18 — Security headers

**Status: RUNTIME/HOSTING PENDING.**

Raw response برای HSTS، `X-Content-Type-Options`، frame policy، Referrer-Policy و mixed-content review لازم است.

## HIGH 19 — Backup/Restore

**Status: BLOCKED.**

قبل deploy جدید: DB، uploads، current 1.4.26/1.3.1، new artifacts، Rank Math config و server/SMTP/cache config backup شود. سپس isolated restore rehearsal و Service/NAP/Form/Rank Math/Media/Health smoke.

## HIGH 20 — WPCS/PHPStan

**Status: BLOCKED.**

این workspace PHPCS/PHPStan ندارد؛ PASS ادعا نشده است. CI/Staging باید WordPress Coding Standards + basic PHPStan/static/deprecated scan را اجرا و findings را disposition کند.

## HIGH 21 — PHPUnit

**Status: SOURCE TESTS SHIPPED / ACTUAL WORDPRESS EXECUTION BLOCKED.**

Local custom smokeها PHPUnit نیستند. WordPress PHPUnit باید با TestDox/JUnit raw output اجرا شود؛ WP-CLI typo fix این requirement را حذف نمی‌کند.

---

# MEDIUM items

## Callback phone policy

Patient callback phone intentionally flexible است و با Clinic E.164 SSOT متفاوت است. Validator اکنون 7–15 digit واقعی می‌خواهد، punctuation-only/letters را رد می‌کند، ولی local/international formatting را به یک Iran-only format محدود نمی‌کند. Clinic E.164 همچنان validator جدا و سخت‌گیر دارد.

## Rank Math observation evidence

Health JSON شامل exact version و `observed_at` است؛ بنابراین Evidence باید timestamp raw output را ذخیره کند.

## Feed Header

Source hook کافی نیست. روی exact new hashes actual response `X-Robots-Tag: noindex, follow` باید capture و conflict با Rank Math/Server بررسی شود.

## Production baseline mutation discipline

Production محیط آزمایش مخرب نیست. هر تغییر Live برای Evidence—مانند Rank Math activation/deactivation، Debug config یا Settings—باید before state، action، after state و rollback/restore ثبت کند. Schema repair Apply روی Production به‌صورت code-level blocked است.

---

# Schema Repair — Staging-only exact procedure

این بخش جایگزین هر دستور قدیمی Apply می‌شود.

```bash
# Staging clone only; environment identity must be staging.
wp --info
wp plugin status alipasandi-service-content
wp alipasandi service health

# Mandatory read-only dry-run.
wp alipasandi service schema-repair | tee schema-repair-dry-run.json

# Review pass=true + per-service actions/reasons + plan_sha256.
# Apply only after DB backup + Service JSON export + payload SHA record.
wp alipasandi service schema-repair --apply --confirm-plan-sha=<PLAN_SHA256> | tee schema-repair-apply.json

wp alipasandi service health | tee service-health-after.json
```

**Never run Apply on Production.** Plugin 1.3.3 rejects it when WordPress environment type is `production`.

---

# Executed local evidence on final ZIPs

| Check | Result |
|---|---|
| Deterministic Theme rebuild | PASS / byte-identical |
| Deterministic Plugin rebuild | PASS / byte-identical |
| ZIP integrity | PASS |
| PHP syntax | PASS on local PHP 8.4.23 only |
| Source audit | **40 PASS / 0 FAIL** |
| WP-CLI executable bootstrap | PASS: 3 commands registered |
| schema-repair dry-run callback | PASS / write=false |
| Production schema Apply guard | PASS / blocked |
| Staging confirm-plan guard | PASS |
| Registry/Date/OpeningHours/Phone custom smoke | 27/27 PASS |
| Rate-limit/form policy custom smoke | 13/13 PASS |
| stale domain/Gmail/Maps scan | PASS |

این نتایج **WordPress Runtime/PHP 8.2/WP-CLI واقعی/PHPUnit** نیستند و جای آن Gates را نمی‌گیرند.

# Production Environment evidence already observed

Snapshot قبلی مربوط به Theme 1.4.26 / Plugin 1.3.1 بود و برای 1.4.28/1.3.3 Runtime PASS محسوب نمی‌شود، اما Environment identity و blockers واقعی را ثابت می‌کند:

- WordPress 7.0.4؛
- PHP 8.5.7 64-bit / LiteSpeed SAPI؛
- Apache reported؛ MariaDB 10.6.24؛
- timezone Asia/Tehran؛
- Rank Math Free 1.0.276؛
- WP_DEBUG/WP_DEBUG_DISPLAY/WP_DEBUG_LOG=true؛
- loopback 503؛
- scheduled recovery event failure؛
- چهار Service schema_invalid؛
- operational mail/NAP/booking config ناقص؛
- Opening Hours unset.

# Remaining execution gates — no theoretical PASS

1. PHP 8.2 واقعی: lint + activation + migration/forms/Health/Rank Math.
2. WP 6.4 واقعی: activation + revisions/meta/Settings/Site Health/WP-CLI.
3. Production-identical WP 7.0.4 / PHP 8.5.7 Staging lane روی **new hashes**.
4. Real WP-CLI commands including schema repair dry-run/confirmed apply on clone.
5. WordPress PHPUnit TestDox/JUnit.
6. PHPCS/WPCS + PHPStan/deprecated scan.
7. Rank Math 1.0.276 active/inactive/reactivated SEO/indexability/entity graph/sitemap diff.
8. Owner-approved NAP + Opening Hours + Horizon/Lead snapshot.
9. SMTP/DNS/inbox full headers.
10. Debug-display/log hardening.
11. Loopback/WP-Cron fix.
12. Backup + isolated restore.
13. Cache/post_modified/sitemap lastmod.
14. Four-host/trailing slash/feed/attachment/UTM/full Indexability crawl/security headers.
15. CWV/network images/browser/A11y/Admin RTL.
16. Privacy/Medical Claims/Assets approvals.

# Final Status

| Area | Status |
|---|---|
| Artifact integrity | **PASS** |
| WP-CLI 1.3.2 blocker | **FIXED SOURCE + LOCAL BOOTSTRAP PASS** |
| Permanent CI escape-token gate | **PASS SOURCE** |
| Schema repair safety contract | **PASS SOURCE / REAL STAGING PENDING** |
| Rank Math observation freshness | **PASS SOURCE / RUNTIME PENDING** |
| Static front-page Health | **PASS SOURCE / RUNTIME PENDING** |
| Production Debug readiness | **FAIL / BLOCKER until config fixed** |
| Production NAP/Mail | **FAIL / BLOCKER until Owner freeze/config** |
| Production Service DB schema | **FAIL / requires Staging repair evidence** |
| Rank Math runtime | **NOT PROVEN** |
| PHP 8.2 / WP 6.4 | **NOT PROVEN** |
| Production-identical new-build smoke | **NOT PROVEN** |
| Loopback/WP-Cron | **FAIL/PENDING FIX** |
| WPCS/PHPStan/PHPUnit | **NOT PROVEN** |
| SMTP/Restore/CWV/A11y/Browser | **NOT PROVEN** |
| Production Approval | **NO** |

## مرحله بعد

دیگر Release نظری جدید لازم نیست مگر Staging یک defect code-level جدید پیدا کند. **Exact Theme 1.4.28 / Plugin 1.3.3 hashes** باید روی clone/Staging Deploy شوند و Runtime Runbook بالا اجرا شود. Owner/Hosting/SMTP/Medical/Privacy gates با code version bump بسته نمی‌شوند مگر تصمیم آنها واقعاً behavior/code را تغییر دهد.
