# پاسخ نهایی — Hard Production Gates و Artifact Release ۱.۴.۲۲

**تاریخ:** ۱۴ اوت ۲۰۲۶
**Theme:** ۱.۴.۲۲ · **Site Plugin:** ۱.۱.۰ · **Service API:** ۱.۱
**Minimum:** WordPress 6.4 · PHP 8.0 · **Target:** PHP 8.2
**Design:** Locked · **Production Approval:** خیر تا تکمیل Staging Evidence

به‌دلیل تغییر Code طبق Build Immutability، شماره Theme از ۱.۴.۲۱ به **۱.۴.۲۲** و Plugin از ۱.۰.۱ به **۱.۱.۰** افزایش یافت. این Release جایگزین ۱.۴.۲۱-r1 است.

## Artifactهای Code Review

- `alipasandi-clinic-1.4.22.zip`
- `alipasandi-service-content-1.1.0.zip`
- `RELEASE-MANIFEST.txt` و `BUILD-VALIDATION.txt`
- `PRODUCTION-PREFLIGHT-1.4.22-FA.md`
- `MAINTENANCE-GUIDE-FA.md` و `INDEXABILITY-MATRIX-FA.md`
- `FUNCTIONALITY-OWNERSHIP-MATRIX-FA.md`، `COMPATIBILITY-MATRIX.md`، Security/Operations Notes، Claim Register template و Build instructions

## پاسخ دقیق ۸۸ Gate

| # | پاسخ/اقدام | وضعیت Evidence |
|---|---|---|
| 1 | Theme/Plugin واقعی و تمام اسناد در Bundle تحویل شده‌اند؛ Code-level review ممکن است. | Artifact |
| 2 | Ownership Matrix کامل تحویل شد. Service/NAP/Form processing/Rank Math integration=Site Plugin؛ UI presentation=Theme؛ SEO=Rank Math؛ SMTP/host redirects/cache edge=Hosting/Server/Other Plugin. | Artifact |
| 3 | NAP UI از Customizer خارج و در Settings→اطلاعات کلینیک داخل Site Plugin قرار گرفت؛ Options همان SSOT هستند. | Artifact؛ Theme-switch QA pending |
| 4 | Validation، nonce/honeypot، rate limit، email payload، booking hours/location و success/error processing به Site Plugin منتقل شد؛ Theme فقط form markup دارد. | Artifact |
| 5 | Theme fallback با `metadata_exists()` ابتدا `_alipasandi_service` فعلی را می‌خواند و فقط در نبود Meta از legacy استفاده می‌کند. Deactivate/Reactivate byte-equivalence در Preflight است. | Code؛ Staging test pending |
| 6 | Theme برای Plugin missing/outdated Notice واضح و Site Health Critical دارد؛ Frontend Meta fallback سالم می‌ماند. | Artifact |
| 7 | Contract: Theme 1.4.22 requires Plugin>=1.1.0؛ Plugin integration requires Theme>=1.4.22؛ runtime warning و API version ثبت شد. | Artifact |
| 8 | هیچ uninstall hook یا `uninstall.php` وجود ندارد؛ deactivate/delete هیچ Service Meta/Key/NAP را پاک نمی‌کند. Cleanup فقط ابزار صریح آینده. | Artifact |
| 9 | Support claim 6.2/6.3 حذف شد؛ minimum واقعی به WP6.4 افزایش یافت تا native revision meta contract باشد. Production target باید در CI/Staging ثبت شود. | Artifact؛ runtime matrix pending |
| 10 | PHP7.4 claim حذف و minimum=8.0، target=8.2 شد. Syntax/runtime CI روی هر دو Gate است. | Artifact؛ CI pending |
| 11 | Revision A/B/FAQ/CTA/link/image/empty/pre-migration/no-meta matrix در Preflight؛ old revision بدون Meta، Meta فعلی را نگه می‌دارد. | Staging pending |
| 12 | با WP minimum 6.4، native `revisions_enabled` مسیر Supported است؛ fallback قدیمی عملاً خارج support contract و همزمان اجرا نمی‌شود. | Code؛ staging confirm |
| 13 | Warning Update-only در بالای Meta Box با inline warning prominent نمایش داده می‌شود. | Artifact؛ screenshot pending |
| 14 | Duplicate stable key: resolver fail-closed، Migration blocked، Meta Box editing disabled، Admin error و Health FAIL. | Artifact؛ duplicate QA pending |
| 15 | Migration قبل Assign، uniqueness را بررسی می‌کند؛ key تکراری write نمی‌شود. | Artifact |
| 16 | پس از stable key، slug/parent/title Identity را تغییر نمی‌دهد. | Code؛ Staging scenarios pending |
| 17 | Delimiter UI حذف شد. FAQ/Benefits/Steps به Structured bounded rows تبدیل شدند؛ multiline/HTML/empty row معتبر و delimiter issue حذف؛ حدود ۱۱۲ input زیر `max_input_vars` معمول است. | Artifact؛ RTL screenshot/UX pending |
| 18 | End sentinel: payload ناقص هیچ write نمی‌کند و Admin error می‌دهد؛ before/after test در Preflight. | Code؛ Evidence pending |
| 19 | Migration status/log/NAP migration flag با autoload=false ساخته می‌شوند و activation نیز autoload را audit/fix می‌کند؛ NAP frontend options می‌توانند autoload باشند. | Artifact |
| 20 | `wp alipasandi service migrate --dry-run` و Admin dry-run، page/action/existing/conflict را بدون write نشان می‌دهند. | Artifact |
| 21 | Health و dry-run JSON machine-readable و Health failure exit code غیرصفر است. | Artifact |
| 22 | Export شامل schema version، key، page ID، slug informational، UTC timestamp، content و SHA-256 content است؛ Secret/PII ندارد و UTF-8 unescaped است. | Artifact؛ round-trip QA pending |
| 23 | Restore strategy در Guide: DB restore روش اصلی؛ Export snapshot برای verification/controlled recovery؛ importer خودکار بدون review اضافه نشد. | Documentation |
| 24 | Allowed attributes: `a[href,title,rel,target]`، span بدون attribute؛ style/on*/data/class ممنوع؛ protocols=http/https/mailto/tel. | Artifact |
| 25 | inline style/arbitrary class/font/color/layout از Content HTML مجاز نیست؛ Design Lock قابل دورزدن نیست. | Artifact |
| 26 | IP پیش‌فرض فقط REMOTE_ADDR؛ forwarded header trusted نیست. Hosting فقط از filter پس از trusted-proxy allowlist Client IP می‌دهد. | Artifact؛ hosting config pending |
| 27 | status اختصاصی `rate_limited` و پیام فارسی + تماس جایگزین؛ shared NAT false-positive باید مشاهده شود. | Artifact؛ production tuning pending |
| 28 | transient get/set race limitation در Security Notes ثبت؛ Abuse واقعی به WAF/Cloudflare atomic rate limit منتقل می‌شود. | Documentation |
| 29 | From از input ساخته نمی‌شود؛ Settings مستقل `clinic_mail_from` برای domain sender؛ Reply-To جعلی ندارد. | Artifact؛ SMTP config pending |
| 30 | DMARC همراه SPF/DKIM Hard Gate است. | DNS evidence pending |
| 31 | Exact SMTP plugin/version/logging/retention/access/deletion باید در Evidence Package ثبت شود؛ Theme/Site Plugin mail log ندارند. | Staging/Owner pending |
| 32 | Rank Math edition/version/license/update/owner باید از Plugin inventory واقعی ثبت شود. Multiple Locations باید خاموش باشد. | Staging pending |
| 33 | KML/Local Sitemap تصمیم پیش‌فرض **Disabled تا SSOT Evidence**؛ Rank Math docs نشان می‌دهد KML/Local Sitemap مسیر جدا و PRO است. | Config evidence pending |
| 34 | Inventory JSON-LD/Local Sitemap/KML/shortcode/block/settings در Preflight؛ فقط JSON-LD در Code sync است، پس سایر Local outputها تا Sync proof خاموش می‌مانند. | Staging pending |
| 35 | Entity-level graph diff برای Dentist/Organization/WebSite/WebPage/Service و @id/NAP ثبت می‌شود. | Staging pending |
| 36 | OpeningHours حدسی ممنوع؛ بعد NAP freeze UI/Schema/KML diff. | Owner/Staging pending |
| 37 | Candidate final host=`https://fasdent.ir/` non-www برای کمترین Migration؛ Search index نیز non-www را نشان می‌دهد. چهار-variant header test روی Server هنوز Gate است. | Server Evidence pending |
| 38 | هر سه variant باید مستقیم 301/308 به final non-www بروند؛ chain ممنوع. | Server pending |
| 39 | Search Console Domain Property/ownership باید ثبت شود. | Owner pending |
| 40 | Live فعلی QA reference نیست و هنوز تهران/+۱۰٬۰۰۰ دارد؛ Staging باید business-rule diff شود. | Confirmed by public crawl; Staging pending |
| 41 | Theme-switch شامل Service+NAP options+Plugin UI می‌شود؛ Rank Math hook اکنون Theme-specific نیست. | Code؛ QA pending |
| 42 | `rank_math/json_ld` از Theme به Site Plugin منتقل و با `RANK_MATH_VERSION` guard شد. | Artifact |
| 43 | admin-post handlers اکنون Site Plugin هستند؛ Theme switch backend processing را حذف نمی‌کند. | Artifact |
| 44 | `clean_post_cache()` Core را پوشش می‌دهد؛ page/object/CDN/browser stack واقعی باید SLA و purge QA داشته باشد. | Staging pending |
| 45 | Service edit باید `post_modified` و Rank Math sitemap lastmod را update کند؛ before/after Evidence لازم. | Staging pending |
| 46 | Plugin update migration خودکار ندارد؛ schema migrations explicit/versioned/logged/backup-aware باقی می‌مانند. | Artifact |
| 47 | Site Health اکنون missing/outdated plugin و service health شامل incomplete migration/duplicate/missing meta/invalid image/schema/oversize را گزارش می‌کند. | Artifact |
| 48 | Notices فقط هنگام action لازم و validation transient نمایش داده می‌شوند؛ permanent success noise نداریم. | Artifact |
| 49 | Git commit/tag/build command و source state در Manifest ثبت می‌شود. | Release artifact |
| 50 | `build-release.sh` deterministic timestamp + `zip -X`; build from tag مستند است. | Artifact |
| 51 | Manifest شامل versions/hashes/WP/PHP target/commit/timestamp است. | Artifact |
| 52 | Deployment command `sha256sum -c` و log equality Gate است. | Deploy pending |
| 53 | Theme min plugin و Plugin min theme checks؛ mismatch silent نیست. | Artifact |
| 54 | uninstall cleanup وجود ندارد؛ Data deletion خودکار صفر. | Artifact |
| 55 | frequent NAP autoload منطقی؛ status/log/admin flags non-autoload و activation correction دارند. | Artifact; DB audit pending |
| 56 | Health برای serialized Service object >100KB `meta_oversized` می‌دهد؛ چهار اندازه واقعی در Evidence ثبت می‌شود. | Artifact؛ DB sizes pending |
| 57 | Migration log PII ندارد؛ form content log نمی‌شود؛ Visitor error details redacted. SMTP/debug log policy external Gate است. | Artifact + Staging |
| 58 | Privacy Policy باید با form/GA/Clarity واقعی همخوان شود. | Content Owner pending |
| 59 | Consent decision فقط بر اساس jurisdiction/tracking واقعی؛ banner بی‌دلیل نصب نمی‌شود. | Owner/legal pending |
| 60 | Admin keyboard/labels/error/RTL و Frontend keyboard/focus/FAQ/form/menu/zoom/contrast/touch matrix تحویل شده؛ runtime evidence لازم. | Staging pending |
| 61 | Site Settings و Meta Box RTL، labels و LTR برای URL/number دارند؛ structured rows جای delimiter را گرفت. | Artifact؛ screenshot pending |
| 62 | Final medical content freeze + JSON snapshot + user/revision/modified trace. | Doctor/Staging pending |
| 63 | `edit_post` object permission؛ launch role inventory لازم. capability اختصاصی فقط در صورت Workflow requirement. | Owner pending |
| 64 | Medical Claim Register template تحویل شد. | Doctor pending |
| 65 | +۱۰٬۰۰۰ default=false و تا approved register خاموش. | Artifact؛ DB evidence pending |
| 66 | Deploy command باید `wp alipasandi service health` exit 0 را require کند. | Pipeline pending |
| 67 | Runbook: Backup→dry-run→explicit migrate→health. | Documentation |
| 68 | Restore Service/NAP/Rank Math/forms/plugin status یک‌بار روی Staging. | Pending |
| 69 | SMTP provider failure monitoring/alert و fallback contact owner باید ثبت شود. | Hosting pending |
| 70 | `wp_mail=false` هرگز success نشان نمی‌دهد؛ error + direct-call path نمایش داده می‌شود. | Artifact؛ mail-failure QA pending |
| 71 | Frontend فقط status allowlist؛ PHP/SMTP/path/trace نمایش داده نمی‌شود. | Artifact |
| 72 | Invalid/missing nonce، honeypot، burst، header injection، oversized notes test matrix ثبت شد. | Staging pending |
| 73 | Rank Math/analytics/map absence نباید Fatal دهد؛ integrations guarded و maps optional. | Code; failure QA pending |
| 74 | Rank Math deactivation→Theme metadata/schema fallback؛ Reactivate→duplicate zero test. | Staging pending |
| 75 | Rank Math update rehearsal و hook compatibility در maintenance workflow. | Future staging |
| 76 | Rank Math hook Site Plugin-owned و guarded؛ API change نباید Fatal شود. | Artifact |
| 77 | KML/Local Sitemap=Disabled تا only-Nowshahr/SSOT evidence؛ سپس decision versioned. | Config pending |
| 78 | Requested/final/status/canonical/sitemap table برای صفحات مهم در Evidence Package. | Staging pending |
| 79 | related replacement=301؛ no replacement=404/410؛ homepage mass redirect ممنوع. | Redirect inventory pending |
| 80 | trailing slash policy باید از WordPress/Rank Math final config Freeze شود. | Staging pending |
| 81 | Persian/encoded variants باید در redirect crawl تست شوند. | Pending |
| 82 | tracking parameters canonicalize؛ filter/search parameters indexability QA. | Pending |
| 83 | Search=noindex ولی UX فعال. | Rank Math config pending |
| 84 | Attachment pages noindex/redirect policy با Rank Math واقعی. | Pending |
| 85 | Sitemap lastmod فقط real modification و نه future/fake. | Pending |
| 86 | Evidence ZIP structure تعریف شد؛ screenshots/source/schema/mail/crawl/plugins/hashes/backup proof پس از Staging داخل آن قرار می‌گیرد. | Pending runtime evidence |
| 87 | پس از QA hash freeze؛ هر تغییر نسخه جدید و targeted regression. | Release policy |
| 88 | Architecture/Artifact gates تکمیل‌تر شده‌اند؛ **Production Approval همچنان NO** تا تمام موارد Runtime/Owner بالا Evidence واقعی بگیرند. | Final status |

## تصمیم Rank Math/KML

Rank Math Owner واحد SEO باقی می‌ماند و حذف نمی‌شود. Edition و exact version از Staging inventory ثبت می‌شود. Multiple Locations خاموش است. طبق مستند رسمی Rank Math، KML/Local Sitemap مسیر جداگانه و قابلیت PRO است؛ بنابراین تا زمانی که NAP/Geo نوشهر در همه خروجی‌ها Diff و SSOT اثبات نشده، KML/Local Sitemap **Disabled** باقی می‌ماند.

## Final Host Candidate

Candidate: `https://fasdent.ir/` non-www برای حفظ host فعلی و کاهش Migration. Search index عمومی نیز صفحات non-www را نشان می‌دهد؛ ولی چهار variant باید روی Server با status/location مستقیم ثبت شوند. تا آن Evidence، Canonical Host Gate از نظر عملیاتی بسته نیست.

## جمع‌بندی

Code-level ownership و lock-inهای باقی‌مانده اصلاح شدند و Artifact قابل Code Review/Rebuild است. اما موارد وابسته به WordPress/Hosting/SMTP/Rank Math/Search Console/Cache/Browser/Doctor/Legal قابل جعل از ZIP نیستند. گزارش صادقانه آن‌ها را Pending نگه می‌دارد؛ پس از Deploy Staging و تکمیل Evidence Package، Production Freeze ممکن است.
