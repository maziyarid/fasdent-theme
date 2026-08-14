# Preflight تولید — 1.4.24 / Plugin 1.2.0

این Checklist باید روی Staging واقعی تکمیل شود. مقدار «Evidence» را با URL، Screenshot، View-Source یا Log واقعی پر کنید؛ بسته ZIP به‌تنهایی مدرک Runtime نیست.

| Gate | Severity | Status | Owner | Tested by/date | Environment | Theme/Plugin hash | Evidence path | Notes |
|---|---|---|---|---|---|---|---|---|
| Artifact/manifest/reproducibility | BLOCKER | PENDING BUILD | Release | — | clean workspace | — | BUILD-VALIDATION | دو Build مستقل |
| WP6.4+PHP8.0 / target WP+PHP8.2 runtime | BLOCKER | PENDING STAGING | DevOps | — | Staging | — | — | activation/smoke |
| Backup + restore rehearsal | BLOCKER | PENDING STAGING | Hosting | — | isolated restore | — | — | DB/wp-content/media |
| Migration/revision/partial POST/Health | BLOCKER | PENDING STAGING | QA | — | Staging | — | — | health exit=0 |
| Rank Math inventory/deactivation/Core sitemap | BLOCKER | PENDING STAGING | SEO | — | Staging | — | — | 8 URL contexts |
| NAP/Schema/KML/GBP graph diff | BLOCKER | PENDING STAGING | SEO/Clinic | — | Staging | — | — | KML disabled |
| Host/redirect/canonical/sitemap | BLOCKER | PENDING SERVER | Hosting/SEO | — | Production host | — | — | four-host direct 301/308 |
| Forms/timezone/rate-limit/mail/SPF/DKIM/DMARC | BLOCKER | PENDING STAGING | DevOps/Clinic | — | Staging+DNS | — | — | no false success |
| Privacy/consent/claims/content freeze | HIGH | PENDING APPROVAL | Clinic/Legal | — | Production content | — | — | +10,000 off |
| Cache/post_modified/lastmod | HIGH | PENDING STAGING | Hosting/SEO | — | real cache stack | — | — | before/after |
| CWV/hero/third-party/fonts | HIGH | PENDING FIELD TEST | QA | — | Mobile/Desktop | — | — | LCP/INP/CLS |
| Browser/A11y/Admin RTL/HTML | HIGH | PENDING FIELD TEST | QA | — | device matrix | — | — | WCAG/validity |
| Git/static/WPCS/deprecated/secrets | BLOCKER | PENDING BUILD | Release | — | clean tag | — | BUILD-VALIDATION | zero fatal/secret |

تا زمانی که همه Gateهای Critical مدرک واقعی ندارند، Production Approval = خیر.
