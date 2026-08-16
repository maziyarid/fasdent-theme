# FasDent Theme 1.4.29 / Plugin 1.3.4
## Complete Corrected Delivery Report

**Report date:** August 15, 2026  
**Delivery type:** One complete parent bundle containing two canonical WordPress installation ZIPs, manifest, checksums, runbook, validation evidence and this report  
**Source decision:** **ACCEPTED AS A CORRECTED STAGING CANDIDATE**  
**Production decision:** **HOLD UNTIL THE RUNTIME GATES IN THE RUNBOOK PASS**

---

## Executive outcome

The initially supplied `Theme_1.4.29.zip` and `Plugin.zip` were not safe release artifacts. They carried old public versions, noncanonical archive roots, disconnected namespaced patch code, parallel option keys, unregistered form handlers, incomplete schedule/schema logic, a simulated NAP test and an unmerged homepage.

This delivery is a corrective rebuild of the complete Theme and Plugin trees. It resolves the source-level blockers identified in the exact-input audit and packages the pair under the required WordPress slugs:

```text
alipasandi-clinic-1.4.29.zip
└── alipasandi-clinic/

alipasandi-service-content-1.3.4.zip
└── alipasandi-service-content/
```

The bundle is suitable for upload to an isolated Staging clone. It is not represented as production-approved because this workspace has no PHP/WordPress runtime and therefore could not execute activation, PHP lint, PHPUnit, WP-CLI, corrected-artifact browser QA, mail, Rank Math, cron, accessibility, performance or restore lanes. A read-only audit of the current public site was executed separately; it found a rendered Theme 1.4.26 regression followed by origin-wide 502 responses. Those findings are explicit hosting/deployment gates, not evidence against the corrected source tree.

---

## 1. Artifact identity

| Artifact | Version | Canonical root | SHA-256 |
|---|---:|---|---|
| Theme | 1.4.29 | `alipasandi-clinic/` | `a8ffcec325c77abecad87d23dbfba530616e4d4477c2f32ac515ea385a3edd24` |
| Plugin | 1.3.4 | `alipasandi-service-content/` | `2b4750fa75f6e842bbfa4f960c9265b3d926dabd6578a96d59bbd112b3b1baac` |

The authoritative byte hashes are repeated in `SHA256SUMS.txt` and `RELEASE-MANIFEST-1.4.29-1.3.4.txt`. A hash identifies bytes; it does not replace runtime QA.

### Rejected-input provenance

| Input | SHA-256 | Disposition |
|---|---|---|
| `Theme_1.4.29.zip` | `1fae13b2e2c832737d77bd0eb7f4a4f405940f72841396e89e40f6228fbb2214` | Rejected baseline; do not install |
| `Plugin.zip` | `e2735c2e06a9900e405fc2d7989bf1d33ba299ec2a3bc7c15d4be2a65570072a` | Rejected baseline; do not install |

---

## 2. Corrected architecture

| Concern | Final owner | Consumer contract |
|---|---|---|
| Presentation and templates | Theme 1.4.29 | Reads canonical Plugin APIs; emergency mode is read-only |
| Service registry/content writes | Plugin 1.3.4 | One six-field registry and `_alipasandi_service` meta schema |
| Public service links/identity | Plugin 1.3.4 registry | Header, mobile menu, footer, homepage, overview, appointments and SEO consume one normalized list |
| Clinic/NAP settings | Plugin 1.3.4 | `alipasandi_clinic_option()` plus `Alipasandi_NAP_Helper` |
| Display address | Plugin 1.3.4 | `alipasandi_display_address()`; no fabricated fallback |
| Direct telephone route | Plugin 1.3.4 | `alipasandi_phone_href()` returns strict E.164 or empty |
| Contact/appointment processing | Plugin 1.3.4 | Four registered `admin_post`/`nopriv` hooks |
| Booking policy and schedule | Plugin 1.3.4 | One parser/cross-validator used by UI, handlers and Health |
| SEO metadata/canonical/sitemap | Rank Math in production | Theme fallback only when no SEO owner is active |
| Local Schema | Explicit `disabled` or `rank_math` | Partial local entities removed; complete canonical NAP normalized |
| Operational evidence | Plugin 1.3.4 | WP-CLI health, rendered NAP probe and artifact-bound freeze |

No runtime module uses the rejected `Alipasandi\FasDent` namespace or the rejected parallel keys such as `alipasandi_business_name`, `alipasandi_booking_slots`, `alipasandi_opening_hours` or `alipasandi_smtp_host`.

---

## 3. Finding-by-finding closure

| Original finding | Corrective result | Evidence in final source |
|---|---|---|
| F-01 old release identity | Closed | Theme header/constant/readme = 1.4.29; Plugin header/constant/stable tag = 1.3.4 |
| F-02 missing public APIs | Closed | Global APIs are restored and loaded from the production bootstrap |
| F-03 forms unavailable | Closed in source | Nonce, honeypot, privacy-minimized limiter, validation, `wp_mail` and all four hooks restored; delivery remains a Staging test |
| F-04 parallel NAP options | Closed | One `alipasandi_<canonical-key>` contract; only bounded one-time aliases are migrated |
| F-05 NAP Helper unused | Closed | Home, Contact, Footer, email, Rank Math, Theme schema, Health and Freeze consume the shared contract |
| F-06 partial address/unsafe phone | Closed | Local entity requires complete structured address plus display/E.164 NAP; `tel:` never uses the display string |
| F-07 schedule logic incomplete | Closed in source | Shared grammar, end-exclusive close, day overrides, lead, horizon, WP timezone, future-slot gate and UI filtering |
| F-08 weak NAP Freeze | Closed | Canonical payload, environment, exact versions, supplied artifact hashes, owner, timestamp, invalidation and verification |
| F-09 simulated NAP test | Closed | Blocking Front/Contact HTTP probe compares Home, Contact, Footer, actual email block and observed JSON-LD hashes |
| F-10 schema repair absent | Closed | Dry-run plan; exact plan hash; Staging-only apply; marker-only SQL transaction; before/after content SHA equality |
| F-11 nondeterministic Rank Math | Closed in source | Explicit owner, priority-90 normalization/removal and priority-999 final graph observation for Front and Contact |
| F-12 homepage unmerged | Closed | Existing `.home-hero`, grid, cards, images, skip target and service registry are used; no orphan design system |
| F-13 tests incompatible | Closed in source | Tests reference the restored global APIs and cover registry, schedule boundaries, NAP, schema removal and partial address |
| F-14 live blank/stale homepage could recur silently | Closed in source | Explicit rendered probe now rejects missing `#main-content`, hero/cards/images and Theme assets not served as 1.4.29 |

---

## 4. Canonical settings contract

Every setting is stored under a single prefixed WordPress option and has no embedded production value.

| Group | Canonical keys |
|---|---|
| Identity | `clinic_business_name` |
| Phone | `clinic_phone`, `clinic_phone_e164` |
| Address | `clinic_address`, `clinic_street`, `clinic_city`, `clinic_region`, `clinic_country`, `clinic_postal_code` |
| Location extras | `clinic_geo_lat`, `clinic_geo_lng`, `clinic_maps` |
| Mail | `clinic_email`, `clinic_notify_email`, `clinic_mail_from`, `clinic_mail_from_name` |
| Booking | `clinic_booking_times`, `clinic_opening_hours`, `booking_horizon_days`, `booking_lead_minutes` |
| Social | `clinic_instagram`, `clinic_whatsapp`, `clinic_telegram` |
| Ownership | `local_schema_owner` |
| Other | `designer_credit` |

Required NAP is business name, display phone, strict E.164 phone, street, city, region and two-letter country. A display address may be explicitly supplied; otherwise it is composed only when all structured address components are complete. Health rejects a configured display address that omits the canonical street, city or region.

### Freeze semantics

`wp alipasandi nap freeze` refuses incomplete NAP or missing artifact hashes. Its canonical payload includes every setting above plus:

- WordPress environment type;
- canonical `home_url()` and WordPress timezone;
- active Theme version;
- Plugin version;
- final Theme ZIP SHA-256;
- final Plugin ZIP SHA-256;
- approver, note and UTC timestamp.

Any add, update or delete of a tracked setting invalidates the freeze. Artifact/environment/version drift also makes verification fail. A new freeze must be created after production promotion because the environment is part of the evidence.

---

## 5. Forms, mail and privacy

Both public form channels are fail-closed. A form becomes interactive only when its relevant contract passes.

### Shared requirements

- valid notification address;
- valid From address;
- nonempty From name;
- From domain equal to or a subdomain of the WordPress home host;
- canonical direct phone route in strict E.164;
- registered POST action and nonce;
- honeypot;
- per-action 15-minute transient limiter using anonymized IP plus HMAC;
- sanitized patient inputs;
- plain-text mail;
- canonical NAP footer in the actual outbound body;
- Post/Redirect/Get status handling.

### Appointment-only requirements

- explicit owner-approved horizon and lead values;
- syntactically valid booking slots;
- syntactically valid weekly opening hours;
- at least one weekly overlap;
- at least one future date/slot in the WordPress timezone;
- submitted service from the one registry allowlist;
- submitted date/time valid against the exact day, hours, lead and horizon.

`wp_mail()` returning true is only application acceptance. Staging must still prove SMTP transport, inbox receipt, headers and authentication alignment.

---

## 6. Booking and opening-hours contract

### Booking slots

```text
09:00
10:30
```

Plain `HH:MM` lines apply to every open day. Day-specific lines override the global list for that day:

```text
MO=09:00,10:30
FR=CLOSED
```

### Opening hours

```text
MO=09:00-13:00,14:00-18:00
FR=CLOSED
```

The close boundary is end-exclusive: `09:00-17:00` accepts `16:59` but rejects `17:00`. Duplicate days/times, invalid days, invalid 24-hour values, reverse ranges, overlaps, empty schedules, zero overlap and out-of-policy dates fail closed. The browser filters time buttons by selected weekday; the server remains authoritative.

Special-date closures, vacations and real-time capacity are not claimed by this release.

---

## 7. Schema and Rank Math ownership

`local_schema_owner` accepts only:

- `disabled`: all `LocalBusiness`, `Dentist` and `MedicalBusiness` nodes are recursively removed; or
- `rank_math`: a local entity is retained only when canonical NAP is complete, then name, URL, address, telephone, map, geo and validated opening hours are normalized from the Plugin.

List arrays are reindexed after node removal so JSON does not drift into an object. The Theme emergency fallback never emits a partial `Dentist`/`PostalAddress`; Service and Article references to the clinic are included only when the clinic entity exists.

The priority-999 observer records Front and Contact separately with UTC time, exact Rank Math version, selected owner, final graph SHA-256, local entity count and canonical NAP SHA-256. A blocking probe is required; cached or random traffic is not accepted as release evidence.

---

## 8. Service schema diagnosis and repair

The repair workflow distinguishes:

- `missing_schema_marker`;
- `unknown_schema_version`;
- `malformed_meta`;
- `sanitizer_drift`;
- `required_field_content_missing`.

Only a missing-marker case with no sanitizer/content drift is repairable. An unknown schema version is diagnosed but deliberately blocked because it may belong to a future/different schema. Dry-run is the default. Apply requires:

1. `WP_ENVIRONMENT_TYPE=staging`;
2. the exact current `--plan-sha256`;
3. exactly one meta row;
4. unchanged content hash since planning;
5. a database transaction;
6. the same content SHA-256 after the marker write.

Any mismatch rolls back and stops. The workflow never auto-rewrites H1, intro, FAQ, CTA, image or internal links.

---

## 9. Commands supplied by Plugin 1.3.4

```text
wp alipasandi service migrate --dry-run
wp alipasandi service migrate
wp alipasandi service schema-repair
wp alipasandi service schema-repair --apply --plan-sha256=<exact-plan-hash>
wp alipasandi service health --probe
wp alipasandi operations health --probe
wp alipasandi nap consistency --probe
wp alipasandi nap freeze --owner=<name> --theme-sha256=<hash> --plugin-sha256=<hash> [--note=<text>]
wp alipasandi nap freeze-status
```

Nonzero exit status is used for a failed critical health, NAP consistency or freeze verification gate.

---

## 10. Complete response to the 33 client requirements

| # | Requirement | Final disposition |
|---:|---|---|
| 1 | Complete installable ZIPs | Implemented; canonical child ZIPs are inside one parent bundle |
| 2 | Correct versions before packaging | Implemented across headers, constants, readmes and compatibility notices |
| 3 | Bootstrap/runtime dependency verification | Static pass; activation must run on Staging |
| 4 | New hashes and hash-bound evidence | Implemented in manifest/freeze; final hashes are unique to this rebuild |
| 5 | Repeat full-ZIP source audit | Performed through final archive structure/integrity/static checks; PHP/WP runtime remains pending |
| 6 | Schema-repair policy | Implemented and documented |
| 7 | Cause-specific schema diagnosis | Implemented with five explicit categories |
| 8 | Preserve content payload | Implemented with plan, transaction and SHA-256 equality |
| 9 | NAP Helper call-site audit | Implemented across required consumers |
| 10 | Address/street contract | Implemented; structured completeness plus display-drift health |
| 11 | Partial PostalAddress tests | Added; execution pending Staging test runner |
| 12 | E.164 readiness/tel route | Implemented; invalid routes emit no telephone action |
| 13 | Schedule edge tests/close semantics | Added; end-exclusive source contract implemented |
| 14 | Future availability | Implemented with WP time, lead, horizon, hours, slots and weekday UI filtering |
| 15 | Evidence-producing NAP freeze | Implemented, artifact/environment-bound and invalidating |
| 16 | Actual-output NAP consistency | Implemented as blocking WP-CLI probe with nonzero failure exit |
| 17 | Exact Rank Math ownership | Implemented for production SEO owner plus explicit Local Schema state |
| 18 | Rank Math disabled Local Schema | Source contract implemented; Staging probe required |
| 19 | Deterministic Front/Contact observation | Implemented with blocking probe and two-context final graph records |
| 20 | Debug hardening | Health checks public display; current 502 requires proxy/upstream logs, and server log access/rotation/PII review remains hosting evidence |
| 21 | Loopback/cron | Explicit blocking probes implemented; actual cron execution remains hosting evidence |
| 22 | Imagick recommendation | Nonblocking unless Staging image/performance QA fails |
| 23 | Isolated noindex Staging | Required by runbook; environment evidence pending |
| 24 | No destructive Production QA | Enforced for schema repair and stated in runbook |
| 25 | WP/PHP compatibility lanes | Headers require WP 6.4/PHP 8.2; requested runtime lanes remain NOT RUN |
| 26 | Final-ZIP WP-CLI commands | Supplied; execution pending Staging |
| 27 | PHPUnit/WPCS/PHPStan/deprecations | Source tests corrected; tools and execution pending configured CI/Staging |
| 28 | SMTP/inbox/SPF/DKIM/DMARC | Source mail identity corrected; end-to-end delivery evidence pending |
| 29 | SEO runtime matrix | Ownership corrected; crawl/render evidence pending |
| 30 | Backup/restore rehearsal | Required before promotion; pending hosting execution |
| 31 | Performance/accessibility/browser/Admin RTL | Current public deployment failed landmark/content availability checks; corrected homepage source is repaired and the full Staging runtime matrix remains pending |
| 32 | Medical/privacy/assets approvals | No fabricated results/identity/location; final owner/doctor/legal approvals remain required |
| 33 | Horizon/lead approval | Values must now be explicitly saved; no implicit production approval is accepted |

---

## 11. Validation performed in this workspace

| Check | Result |
|---|---|
| Correct public versions | PASS |
| Canonical source roots | PASS |
| Runtime namespace/parallel-key scan | PASS |
| Required global APIs/hooks/commands static discovery | PASS |
| Homepage established-selector and referenced-image scan | PASS |
| JSON parsing | PASS |
| JavaScript `node --check` | PASS |
| PHP delimiter/string/comment structural scan | PASS — 38 PHP files |
| Focused release static assertions | PASS — 25/25, rerun on extracted final child ZIPs |
| Current public homepage read-only diagnosis | FAIL — rendered Theme asset `1.4.26`, missing `#main-content`, cards/images and Local JSON-LD |
| Current public internal routes | FAIL — About, Services, Contact, Appointments, FAQ and Blog returned 502 during the audit window |
| Current WordPress Admin reachability | FAIL — `/wp-admin/` returned 502 / connection refused |
| PHP `php -l` | NOT RUN — PHP unavailable in audit workspace |
| WordPress activation/WP-CLI/PHPUnit/WPCS/PHPStan | NOT RUN — runtime/tooling unavailable |
| Corrected-artifact browser, mail, Rank Math, cron, accessibility, performance, backup/restore | NOT RUN — requires isolated Staging/hosting |

The full machine-readable summary is reproduced in `SOURCE-VALIDATION-EVIDENCE.txt`.

## 12. Public website diagnosis and source closure

The live audit found two distinct layers:

| Layer | Observed evidence | Closure |
|---|---|---|
| Deployment/application | Homepage served Theme assets as 1.4.26 and lacked the established homepage/main/content contract | Corrected by the exact 1.4.29 Theme; a rendered-output health gate now checks returned landmarks, design selectors, card/image counts and asset versions |
| Hosting/origin | Internal routes and `/wp-admin/` returned `502 Bad Gateway` with `[Errno 111] Connection refused` | Not fixable from Theme/Plugin source; hosting must restore the upstream before upload |
| Cache/node consistency | Search-index evidence showed richer recent content than the sparse live render | Treat as a cache/mixed-node hypothesis until cache and origin logs prove the cause; purge and multi-request checks are mandatory |
| URL/NAP estate | Indexed singular `/appointment/`, plural `/appointments/` use, legacy service/pricing paths and mixed NAP footprints | No guessed redirect or NAP was embedded; owner-approved URL map and canonical NAP freeze are staging gates |

The full evidence, diagnosis tree and recovery sequence are in `LIVE-SITE-DIAGNOSIS-2026-08-15.md`. No live files or settings were modified because neither the origin nor WordPress Admin exposed a functioning repair surface.

---

## 13. Release decision

### Upload to isolated Staging

**YES, after the origin is stable**, using the single parent delivery bundle and the exact order in `STAGING-RUNBOOK-1.4.29-1.3.4.md`.

### Upload directly to Production

**NO.** Production promotion requires all critical runtime gates, mail receipt, Rank Math Front/Contact observation, rendered NAP consistency, backup restore rehearsal and owner/clinical/privacy approvals.

### Stop conditions

Stop before upload while 502/upstream refusal remains. After hosting recovery, stop and roll back if any artifact hash differs, PHP lint fails, activation produces an error, returned HTML is stale/blank/wrong-version, a critical health item remains, content SHA changes during schema repair, NAP consistency differs, forms do not reach the redirected staging inbox, Rank Math ownership is ambiguous, loopback fails, or the exact rollback restore cannot be demonstrated.

---

## Final handoff statement

This bundle closes the source-level failures of the supplied candidates and the reproducible application-layer findings from the live audit. It provides a coherent, installable 1.4.29/1.3.4 Staging candidate and a rendered-output probe specifically designed to reject the stale/blank deployment that was observed. It deliberately separates artifact acceptance, hosting recovery and environmental proof. That separation is the release control: the source artifacts are complete enough to test, while the current origin incident and production approval remain evidence-driven.
