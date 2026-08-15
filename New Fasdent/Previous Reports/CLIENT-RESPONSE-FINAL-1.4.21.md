# پاسخ نهایی — Architecture Finalization، Hardening و Staging Build ۱.۴.۲۱

**تاریخ:** ۱۴ اوت ۲۰۲۶  
**Theme:** ۱.۴.۲۱ · **Site Functionality Plugin:** ۱.۰.۰  
**Design System:** Locked؛ بدون تغییر Design/Color/Layout/Grid/Typography/Hero/URL  
**Production Approval:** **خیر**؛ منوط به Evidence واقعی Staging

---

## جمع‌بندی اجرایی

تمام ۷۶ مورد پاسخ جدید بررسی شد. مهم‌ترین تصمیم معماری اجرا شده است: Data و Business/Content Functionality چهار Service از Theme خارج و به Plugin سبک **Alipasandi Service Content** منتقل شد. Plugin مالک Registration، Meta Box، Sanitization/Validation، Getter، Migration، Revision، Export، Health و WP-CLI است؛ Theme فقط Render/Layout را کنترل می‌کند. `_alipasandi_service` و `schema_version:1` حفظ شده‌اند و ورود مجدد Data لازم نیست. `_alipasandi_service_key` نیز Identity پایدار داخلی است.

در این گزارش «انجام‌شده» یعنی داخل Artifact پیاده‌سازی شده؛ «Staging» یعنی بدون محیط واقعی Pass اعلام نمی‌شود؛ و «Owner» یعنی تصمیم/داده‌ای بیرون از ZIP لازم است.

---

## پاسخ کامل به ۷۶ مورد

### ۱) Theme Lock-in

**انجام‌شده.** تمام Data/Business functionality به Plugin مستقل منتقل شد؛ Theme فقط Presentation است. Plugin Page Builder یا Dependency سنگین ندارد. Theme در نبود Plugin فقط compatibility fallback را برای جلوگیری از Frontend break لود می‌کند؛ Production path فعال‌بودن Plugin است.

### ۲) Theme Switch Test

**Staging.** چون Data در `wp_postmeta` و UI/logic در Site Plugin است، تعویض Theme نباید Data/UI را حذف کند. تست: Save و Export/Hash Meta → Theme دیگر → تأیید Meta و Meta Box → Theme اصلی → byte-equivalent Meta و Frontend → rerun Migration و اثبات no-overwrite. بدون Evidence Pass اعلام نمی‌شود.

### ۳) Revision Architecture

**انجام‌شده + Staging matrix.** WP≥6.4 از `revisions_enabled` Registered Meta استفاده می‌کند؛ fallback دستی فقط WP 6.2/6.3 است و شرط نسخه مانع Duplicate logic می‌شود. A/B، before/after migration، empty Meta، FAQ/CTA/link/image ID باید روی WP واقعی Restore شوند.

### ۴) Revision فاقد Meta

**Rule قطعی و انجام‌شده.** Restore یک Revision قدیمی بدون `_alipasandi_service`، Meta فعلی را دست‌نخورده می‌گذارد؛ Meta حذف نمی‌شود و Legacy دوباره فعال نمی‌شود. فقط Revision دارای Meta آن را Restore می‌کند.

### ۵) Autosave

**Policy/UI انجام‌شده.** Autosave ناقص Meta را نمی‌نویسد. Meta Box صریحاً اعلام می‌کند ذخیره فقط با **Update** است و Autosave این Fieldها را Recover نمی‌کند. این انتخاب از overwrite ناقص امن‌تر است.

### ۶) Fail/Retry per-page

**Logic انجام‌شده؛ Staging Evidence لازم.** status مستقل است. در سناریوی surgery failed، فقط surgery Retry می‌شود و سه Page موفق باید قبل/بعد byte-identical باشند.

### ۷) Transaction Safety

**انجام‌شده.** success فقط پس از Write و تأیید `metadata_exists()` ثبت می‌شود؛ `completed=true` فقط وقتی هر چهار Page migrated/existing باشند. قطع Request Recoverable است و Meta موجود overwrite نمی‌شود.

### ۸) Migration Logging

**انجام‌شده.** Log محدود ۱۰۰ رکوردی شامل Page ID، key، status، UTC timestamp و reason کنترل‌شده است؛ فقط Admin در Tools می‌بیند و Content/PII/Secret ندارد.

### ۹) Stable Service Identity

**انجام‌شده.** `_alipasandi_service_key=implant|crown|surgery|general` Identity اصلی است. Slug فقط Bootstrap اولیه است؛ تغییر Slug/Parent Data Layer را نمی‌شکند.

### ۱۰) Parent/Trash/Restore/Duplicate

**معماری اصلاح؛ Staging لازم.** Parent دیگر Identity نیست؛ resolver Pageهای trash را انتخاب نمی‌کند. اگر Duplicate plugin کلید را clone و دو Page با یک key بسازد، resolver نباید silently یکی را انتخاب کند و Health/QA باید Conflict را آشکار کند. پنج سناریوی درخواست‌شده در Preflight هستند.

### ۱۱) Object Allowlist

**انجام‌شده.** Object از صفر و فقط با Schema keys ساخته می‌شود؛ `malicious_key` و unknown/obsolete keys حذف می‌شوند.

### ۱۲) Nested Validation

**انجام‌شده.** فقط Array و shape معتبر؛ malformed row رد می‌شود. Benefits=title/text/icon، Steps=number/title/text، FAQ=question/answer؛ icon allowlist؛ row کاملاً خالی حذف می‌شود.

### ۱۳) Resource Limits

**انجام‌شده.** Paragraph 12، Diagram 6، Benefits 12، Steps 12، FAQ 20. UI line-based است و تعداد input جدا ایجاد نمی‌کند؛ limits در Sanitizer enforce می‌شوند.

### ۱۴) Partial POST

**انجام‌شده Fail-safe.** Sentinel انتهای Payload؛ اگر `max_input_vars`/truncation آن را حذف کند هیچ Metaای ذخیره/جایگزین نمی‌شود و Admin error نمایش داده می‌شود.

### ۱۵) Sanitization Policy

**انجام‌شده/مستند.** Title/Label/ALT plain text؛ Intro/Paragraph/FAQ answer/Candidate/Notice/CTA HTML محدود؛ Link protocol فقط http/https/mailto/tel؛ Image فقط Attachment ID معتبر؛ unknown tag/attribute/key حذف.

### ۱۶) Link Security

**انجام‌شده.** `javascript:`/`data:`/malformed protocol حذف؛ `_blank` با `rel="noopener noreferrer"` normalize؛ خروجی دوباره هنگام Render sanitize می‌شود.

### ۱۷) Attachment Validation

**انجام‌شده.** ID باید attachment با image MIME باشد؛ Health وجود فایل را بررسی می‌کند؛ Template ID حذف‌شده/نامعتبر را کنار می‌گذارد تا Broken Image نسازد و bundled fallback باقی می‌ماند.

### ۱۸) ALT Governance

**انجام‌شده.** Toggle صریح `image_decorative`. برای informative image ابتدا Media Library ALT fallback؛ اگر خالی باشد Save رد و پیام واضح داده می‌شود. Blank ALT بدون decorative پذیرفته نمی‌شود.

### ۱۹) Media Migration/CDN

**تأیید.** Primary source Attachment ID است و WordPress URL جاری را تولید می‌کند؛ raw URL منبع اصلی نیست. Domain/CDN migration با DB migration صحیح سازگار است.

### ۲۰) REST=false

**حفظ/مستند.** REST خاموش است؛ آینده فقط با Object/Nested schema محدود و کامل فعال می‌شود، نه `true` ساده.

### ۲۱) Private Meta Naming

**حفظ سازگاری.** `_alipasandi_service` بدون rename به Plugin منتقل شد؛ hidden بودن و داده‌های قبلی حفظ است.

### ۲۲) v1→v2 Contract

**مستند.** Backup، explicit run، idempotent، per-page، no overwrite user edits، log، verify-before-completed و rollback. Theme update Schema migration خودکار اجرا نمی‌کند.

### ۲۳) Silent Legacy Fallback

**اصلاح Governance.** Fallback Availability را حفظ می‌کند، ولی Missing Meta پس از Migration در Health=`FAIL` می‌شود و کاملاً Silent نیست.

### ۲۴) Legacy Freeze

**انجام‌شده.** `service-data.php` deprecated/read-only است؛ Content team نباید آن را ویرایش کند؛ reverse sync/write ندارد.

### ۲۵) Legacy Removal

**Strategy قطعی.** بعد از حداقل دو Release پایدار + Production approval + DB backup + JSON export + Health PASS، runtime fallback در Release نسخه‌دار جدا حذف و فایل Archive می‌شود.

### ۲۶) Service Export

**انجام‌شده.** Tools → Service Content → Export JSON؛ چهار Service، schema version و timestamp. DB/WP Export نیز Meta/Options را حفظ می‌کند.

### ۲۷) post_modified/Sitemap lastmod

**Staging.** Save همراه Page Update اجرا می‌شود؛ رفتار `post_modified` و Rank Math lastmod باید با before/after روی نسخه واقعی اثبات شود. از ZIP Pass ادعا نشده.

### ۲۸) Cache Invalidation

**پایه انجام‌شده؛ Staging integration.** `clean_post_cache()` پس از Save. Page cache/CDN/Redis وابسته به Host/Plugin و نیازمند Edit→Update→frontend evidence است.

### ۲۹) Rank Math NAP Single Source

**انجام‌شده + Graph QA.** `rank_math/json_ld`، Dentist/LocalBusiness را از Options مرکزی Phone/Address/City/Map/Postal/Geo تغذیه می‌کند؛ Geo/Postal فقط با داده رسمی. View-Source Staging لازم است.

### ۳۰) Multiple Locations

**Staging blocker.** Code تهران تولید نمی‌کند؛ setting Rank Math باید Single Location باشد. Screenshot config و نبود Entity تهران در Source لازم است.

### ۳۱) Schema Types

**Staging.** Graph Dentist/LocalBusiness/Organization باید Entity متناقض با name/url/NAP متفاوت نداشته باشد. وقتی SEO Plugin فعال است Theme schema موازی نمی‌دهد.

### ۳۲) @id Consistency

**Staging.** WebSite/WebPage/Organization/Dentist/Service @id و روابط باید ثابت و منطقی باشند. NAP filter داده را هماهنگ می‌کند؛ @id نهایی وابسته به Plugin config است.

### ۳۳) Real Local Data

**enforced.** Geo/Postal فقط اگر رسمی و معتبر باشند؛ OpeningHours هنوز Owner input است و نباید حدس زده شود.

### ۳۴) Canonical Host

**Owner blocker.** Final www/non-www از ZIP قابل تعیین نیست. باید قبل Production کتبی Freeze و در WP URLs، Rank Math، Sitemap و Redirectها یکسان شود.

### ۳۵) Server Host Redirects

**Policy.** HTTP→HTTPS و www normalization ترجیحاً Nginx/Apache/CDN و مستقیم 301 قبل WordPress؛ Theme owner آن نیست.

### ۳۶) Redirect Map

**Owner/SEO inventory.** Old URL→Final URL→301 قبل Go-live. Owner=Server/Rank Math؛ Theme filter فقط fallback/helper.

### ۳۷) Redirect Retention

**مستند.** Migration redirects بلندمدت؛ حذف فقط بعد review Crawl/links/index و بدون 404، نه حذف خودکار چندماهه.

### ۳۸) Sitemap/Canonical Alignment

**Staging gate.** Sitemap URL باید عین Final host/canonical باشد؛ mismatch یا staging URL=FAIL.

### ۳۹) Internal URL Normalization

**Staging crawl.** لینک داخلی باید مستقیم Final canonical باشد. Theme از WordPress URL APIs استفاده می‌کند؛ Crawl evidence لازم است.

### ۴۰) Appointment Noindex

**Policy تأیید؛ config QA.** noindex/follow، خارج Sitemap، نه robots disallow صرفاً به‌علت noindex، canonical منطقی، CTA follow.

### ۴۱) Staging Protection

**Hosting blocker.** Password/access restriction + noindex + production sitemap disabled + analytics disabled/separate؛ robots.txt تنها کافی نیست.

### ۴۲) Production Launch Guard

**تحویل‌شده.** `PRODUCTION-PREFLIGHT-1.4.21-FA.md` Critical gates و ستون Evidence برای noindex/staging/NAP/host/schema/form/migration/backup/CWV و غیره دارد.

### ۴۳) Build Immutability

**رعایت‌شده.** هر Code change نسبت به ۱.۴.۲۰ نسخه جدید ۱.۴.۲۱ دارد؛ Artifact نباید بدون Version/Hash تغییر کند.

### ۴۴) Checksums

**انجام‌شده.** SHA-256 جدا برای Theme و Plugin در `RELEASE-MANIFEST.txt`؛ قبل Deploy باید verify شود.

### ۴۵) Git/Source Control

**Owner action؛ ادعای ساختگی نشده.** ZIP وجود Repo/Remote/Tag را اثبات نمی‌کند. قبل Freeze: commit، tag `1.4.21`، changelog، reproducible build command و commit hash. ZIP نباید Source of Truth تنها باشد.

### ۴۶) Secrets

**Build static scan PASS.** Pattern مربوط به API key/SMTP password/private token/secret assignment یافت نشد. Credentialها باید Environment/secure settings باشند.

### ۴۷) Environment Config

**Architecture مناسب؛ config لازم.** URL/indexing/SMTP/analytics/cache/debug/recipient بیرون Theme release هستند. Staging/Production matrix باید ثبت شود.

### ۴۸) Form Mail Security

**Code انجام‌شده؛ Staging attack test.** From از input ساخته نمی‌شود؛ header ثابت Content-Type؛ user fields sanitize و فقط Body. CRLF/header injection و دریافت واقعی Mail باید تست شود.

### ۴۹) Rate Limit/Spam

**انجام‌شده.** Nonce+Honeypot+۵ Request/۱۵ دقیقه per action/IP؛ فقط salted one-way hash موقت، بدون raw IP/Content log. Captcha سنگین اضافه نشده.

### ۵۰) PII Minimization

**تأیید.** فقط Name/Phone/Service/Date/Time/optional notes؛ ذخیره DB ندارد؛ اطلاعات پزشکی حساس غیرضروری جمع نمی‌شود.

### ۵۱) Mail Logs

**Plugin inventory لازم.** Theme log ندارد. اگر SMTP log فعال است، status/retention/access role/deletion process باید مستند شود.

### ۵۲) Backup قبل Migration

**Gate اجباری.** DB backup قبل اولین explicit migration Staging؛ Full backup قبل Production. Migration خودکار حذف شد تا Backup ممکن باشد.

### ۵۳) Restore Test

**Staging evidence.** حداقل یک Restore DB+files در محیط امن و smoke test Service/NAP/SEO/forms؛ Backup بدون Restore اثبات‌شده نیست.

### ۵۴) Database Portability

**معماری تأیید؛ QA لازم.** NAP=`wp_options`، Service=`wp_postmeta`، SEO=plugin data. Search-replace باید serialization-safe باشد؛ SQL text replace خام ممنوع.

### ۵۵) WP-CLI

**انجام‌شده.** `wp alipasandi service migrate` و `wp alipasandi service health`; Health failure exit code غیرصفر؛ migration وابسته به page load نیست.

### ۵۶) Activation Migration

**اصلاح‌شده.** نه Theme activation، نه frontend/admin request migration اجرا نمی‌کند؛ فقط Tools یا WP-CLI صریح.

### ۵۷) Error Handling

**انجام‌شده.** Page/legacy/write failure=`failed`+reason؛ completed=false؛ retry ممکن؛ successful pages untouched؛ frontend legacy fallback؛ Visitor error نمی‌بیند.

### ۵۸) PHP 8.2/WP_DEBUG

**Staging/CI.** PHP CLI در Build workspace نبود؛ lint/runtime Pass ادعا نشده و در `BUILD-VALIDATION.txt` ثبت است. Target واقعی: PHP 8.2 و Fatal/Warning/Notice/Deprecated صفر.

### ۵۹) Static Analysis/WPCS

**بخشی PASS، بخشی pending.** Secret/version/archive checks انجام شد. PHP lint، WPCS و static analysis باید در CI/Staging دارای PHP با Critical=0 ثبت شوند.

### ۶۰) DB Query Performance

**پایه بهینه؛ Query Monitor لازم.** Stable-key lookup bounded و Health admin-only است. تعداد Query/duplicate query هر Service باید در Runtime ثبت شود.

### ۶۱) Object Cache

**پایه انجام‌شده؛ Staging.** `update_post_meta()`+`clean_post_cache()`؛ Redis/Memcached/CDN واقعی نیازمند Edit→Update→Frontend test.

### ۶۲) Meta Box Accessibility

**بهبود انجام‌شده؛ QA لازم.** Label/for، group headings، descriptions، native input/textarea/checkbox و Admin notices؛ keyboard/screen-reader smoke روی Admin واقعی.

### ۶۳) Validation UX

**انجام‌شده.** Partial POST، required fields و informative ALT نامعتبر Save را متوقف و پیام فارسی واضح می‌دهند؛ Content silently truncate/replace نمی‌شود.

### ۶۴) Required/Optional

**تعریف‌شده.** Required=`title`,`intro`; informative image نیازمند ALT. سایر sections/labels/CTA/FAQ/benefits/steps optional.

### ۶۵) Empty Sections

**انجام‌شده.** Overview/Benefits/Steps/FAQ/CTA و Candidate/Notice وقتی data ندارند Render نمی‌شوند؛ container/heading خالی نداریم.

### ۶۶) HTML Validity

**Code hardening؛ Staging validator.** چهار URL باید بعد Migration برای nested headings، duplicate IDs، invalid anchor و ARIA validate شوند.

### ۶۷) FAQ IDs

**انجام‌شده.** Service key + question hash + collision index؛ `aria-controls`/`aria-labelledby`/`role=region` متصل و در هر Render یکتا.

### ۶۸) Anchor Stability

**انجام‌شده.** IDهای ثابت: `service-overview`, `service-benefits`, `service-steps`, `service-candidacy`, `service-faq`, `service-cta`.

### ۶۹) SEO Title ≠ H1

**تأیید.** H1 در Service Meta؛ SEO title در Rank Math/SEO Meta مستقل؛ Service save هیچ SEO metadata نمی‌نویسد.

### ۷۰) Modified Governance

**پشتیبانی + Evidence.** WordPress user/revision/post_modified؛ Plugin revision restore. Last editor/modified behavior باید در workflow واقعی ثبت شود.

### ۷۱) Approval Flow/Roles

**Permission پایه.** Save نیازمند `edit_post` همان Page؛ Author معمولی به‌طور پیش‌فرض Page را edit نمی‌کند. اگر سخت‌گیری بیشتر لازم است capability اختصاصی/editorial workflow بر اساس Owner roles طراحی شود.

### ۷۲) Claim Governance

**Data integrity تأیید؛ medical approval Owner.** Update Theme/Plugin Meta موجود را overwrite نمی‌کند. Claim register/reviewer/date باید توسط پزشک ثبت شود؛ +۱۰٬۰۰۰ پیش‌فرض خاموش است.

### ۷۳) Maintenance Guide

**به‌روزرسانی‌شده.** Owner/plugin، Meta location، edit/save، revisions، old-revision rule، fallback، migration/log/status، schema، image/ALT/decorative، FAQ، export، health، rollback و legacy removal.

### ۷۴) Health Check

**انجام‌شده.** Admin+WP-CLI: page exists، meta exists، schema valid، required fields، image valid، migration completed؛ machine-readable PASS/FAIL.

### ۷۵) Production Evidence

**Checklist تحویل؛ pending.** Staging URL، exact hash، WP/PHP، migration/save/autosave/revision/fallback/idempotency/diff، Source/HTML/ARIA، mail، Rank Math/NAP/schema، sitemap/host/redirect/indexability، CWV/browser/a11y/logs/plugins/third-party، backup/restore و Git release info.

### ۷۶) Production Approval

**خیر.** ۱.۴.۲۱ Code/Architecture candidate است؛ Approval فقط پس از Staging deploy، destructive/edge QA، SEO/Schema، Performance/A11y، Backup/Restore و Critical Evidence.

---

## Sanitization/Data Contract

| Data | Policy |
|---|---|
| Title/Label/ALT | Plain text |
| Long copy/FAQ/CTA | `a,strong,em,b,i,br,span` |
| Protocol | http/https/mailto/tel |
| `_blank` | `noopener noreferrer` |
| Image | Valid Attachment ID؛ bundled filename fallback |
| Decorative | Explicit toggle |
| Limits | Paragraph 12؛ Diagram 6؛ Benefits 12؛ Steps 12؛ FAQ 20 |
| Unknown input | حذف |
| REST | false؛ آینده فقط با schema محدود |

## اقلام تحویل

- `alipasandi-clinic-1.4.21.zip`
- `alipasandi-service-content-1.0.0.zip`
- `CLIENT-RESPONSE-FINAL-1.4.21.md`
- `RELEASE-MANIFEST.txt`
- `BUILD-VALIDATION.txt`
- `docs/PRODUCTION-PREFLIGHT-1.4.21-FA.md`

## ترتیب Deploy Staging

1. DB Backup.
2. نصب/فعال‌سازی Plugin 1.0.0.
3. نصب Theme 1.4.21.
4. Tools → Service Content → Migration/Retry.
5. Health=PASS.
6. Theme switch/revision/fail-retry/partial-POST QA.
7. Rank Math/NAP/schema/canonical/sitemap/redirect QA.
8. Form/mail/cache/PHP 8.2/HTML/A11y/browser/CWV.
9. Restore test.
10. Evidence + Git tag + artifact hash؛ سپس Production Freeze.

---

**نتیجه:** تمام تغییرات Code-level قابل انجام اعمال شده‌اند؛ موارد وابسته به Hosting، Rank Math config، NAP رسمی، Canonical host، Redirect inventory، SMTP، Cache/CDN، Browser/CWV، Backup Restore و Git به‌درستی بدون ادعای Pass به Staging/Owner سپرده شده‌اند.

**Design System = Locked · Staging Candidate = ۱.۴.۲۱ · Production Approval = خیر**
