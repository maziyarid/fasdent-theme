# Production Preflight — 1.4.28 / Plugin 1.3.3

Status at source-package creation: **NO Production Approval**.

| Gate | Severity | Source-build status | Production/Staging evidence |
|---|---|---|---|
| Artifact integrity / manifest | BLOCKER | generated externally with final ZIP hashes | verify exact deployment hashes |
| PHP syntax | BLOCKER | local PHP version only; see BUILD-VALIDATION | PHP 8.2 required (8.0 support removed in 1.4.28/1.3.3 because genuine 8.0 runtime evidence was unavailable) |
| WP activation/runtime | BLOCKER | NOT PROVEN | WP 6.4+ and actual production WP |
| WPCS/static/deprecated | BLOCKER | NOT PROVEN unless listed in validation | CI reports |
| Migration/revision/autosave/partial POST | BLOCKER | code reviewed | real WP evidence |
| Theme switch / Plugin deactivation/read-only degradation | BLOCKER | contract implemented | runtime screenshots/logs |
| Rank Math edition/version/license/config/entity graph | BLOCKER | NOT PROVEN | inventory + source diff |
| NAP freeze / Schema / GBP | BLOCKER | validation implemented | clinic-approved values + graph diff |
| Forms/date/time/rate-limit/mail | BLOCKER | code implemented | real submissions + SMTP inbox |
| Host redirects/canonical/sitemap | BLOCKER | NOT PROVEN | four-host crawl + matrix crawl |
| Backup/restore rehearsal | BLOCKER | NOT PROVEN | isolated restore + smoke |
| Privacy/consent/medical claims | HIGH | checklists shipped | owner/legal/doctor approval |
| Cache/post_modified/lastmod | HIGH | NOT PROVEN | before/after |
| CWV/hero/third-party/fonts | HIGH | NOT PROVEN | mobile/desktop final hash |
| Browser/A11y/Admin RTL | HIGH | NOT PROVEN | device/a11y audit |
| Security headers | HIGH | NOT PROVEN | response-header crawl |
| SMTP/DNS | HIGH | NOT PROVEN | SPF/DKIM/DMARC + delivered headers |

Production freeze only when all BLOCKER and agreed HIGH gates PASS against exact final hashes.
