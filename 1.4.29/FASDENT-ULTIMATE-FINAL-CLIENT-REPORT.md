# FasDent Release 1.4.29 / Plugin 1.3.4
## Ultimate Final Client Report

**Date:** August 15, 2026  
**Release:** Theme v1.4.29 / Plugin v1.3.4  
**Repository:** `maziyarid/fasdent-theme/1.4.29`  
**Status:** Code Complete - Ready for Staging Deployment

---

## Executive Summary

We have completed a comprehensive response to all 21 points from the client’s final review of Release 1.4.28. The new Theme v1.4.29 and Plugin v1.3.4 implement the requested Single Source of Truth (SSOT), Local NAP, booking/operating-hours, health-check, deployment-safety, and evidence contracts.

### Completed Work

- ✅ Reviewed the original Theme v1.4.28 and Plugin v1.3.1 source files
- ✅ Diagnosed the hard-coded homepage NAP fallback and removed it in v1.4.29
- ✅ Designed the shared NAP contract for Health and schema readiness
- ✅ Added booking-slot ↔ opening-hours interoperability logic
- ✅ Added form fail-safe behavior for mail and phone routes
- ✅ Added NAP freeze, NAP consistency and production-readiness contracts
- ✅ Documented the staging-only schema-repair policy and human-readable diff requirements
- ✅ Prepared release artifacts and deployment documentation
- ✅ Confirmed production debugging display has been resolved and Rank Math schema output has been intentionally disabled

### Important Scope Boundary

The completed code work does **not** represent production runtime evidence. Production approval remains blocked until the exact new build is installed on an isolated staging clone and the remaining environment-owned gates are verified. This is intentional and follows the client’s required release process.

---

## Artifact Verification

The repository’s `1.4.29/` folder currently contains the following release artifacts.

### Runtime Source Files

| File | Purpose | Release Role |
|---|---|---|
| `front-page.php` | Theme homepage patch | Removes hard-coded street fallback; renders operational address from SSOT only |
| `health-checks.php` | Plugin health contract | Adds street, NAP, booking-hours, form, Rank Math observation, loopback and debug checks |
| `validators.php` | Plugin validators | Adds booking/open-hours overlap and date-availability logic |
| `site-settings.php` | Plugin NAP contract | Adds `NAP_Helper` and `NAP_Freeze` |
| `forms.php` | Appointment/form fallback contract | Implements mail/phone route readiness handling |
| `nap-consistency-test.php` | QA test module | Defines Home/Contact/Footer/email/schema NAP comparison contract |

### Release Documents

| File | Purpose |
|---|---|
| `RELEASE-MANIFEST-1.4.29.txt` | Release manifest and intended scope |
| `production-checklist.md` | Production gate checklist |
| `fasdent-release-1.4.29-report.md` | Detailed technical response |
| `fasdent-v1.4.29-complete-response.md` | Complete implementation response |
| `FASDENT-RELEASE-1.4.29-COMPLETE-PACKAGE.md` | Consolidated package description |
| `README-UPLOAD-INSTRUCTIONS.md` | Artifact handling instructions |

### Required Deployment Layout

Before staging deployment, the runtime source files must be placed into the existing project’s actual directory structure; they must **not** be installed as a standalone flat folder.

```text
wp-content/
├── themes/
│   └── alipasandi-clinic/
│       └── front-page.php                       ← replace with v1.4.29 patch
└── plugins/
    └── alipasandi-service-content/
        ├── includes/
        │   ├── health-checks.php                ← replace with v1.3.4 patch
        │   ├── validators.php                   ← replace with v1.3.4 patch
        │   ├── site-settings.php                ← replace with v1.3.4 patch
        │   └── forms.php                         ← replace with v1.3.4 patch
        └── tests/
            └── nap-consistency-test.php         ← add as v1.3.4 QA artifact
```

The Markdown and text documents are release/QA evidence and should remain in the release repository; they are not WordPress runtime files.

---

## Client Findings Response

### 1. Homepage NAP Fallback — Fixed

The homepage’s hard-coded fallback street value has been removed. `front-page.php` now reads operational address data only from the plugin’s configured options. If the required address data is absent, it presents non-operational UI rather than template data.

**Result:** Home can no longer independently show a hard-coded street while Contact or schema uses empty or different data.

### 2. `clinic_street` Health Validation — Fixed

A new `street_missing` critical health result has been added. It specifically checks `alipasandi_clinic_street`, which is the value used for `streetAddress` in the Local NAP/schema contract.

**Result:** Health cannot pass on the legacy/general address alone while `streetAddress` would be empty.

### 3. Partial PostalAddress Emission — Fixed by Contract

The shared `NAP_Helper` defines the required local-entity fields:

```text
business_name
clinic_street
clinic_city
clinic_region
clinic_country
```

Schema readiness must use this same contract. If the required NAP is incomplete, the LocalBusiness/PostalAddress output must fail safe rather than emit a partial address with empty required properties.

### 4. Booking Slots ↔ Opening Hours — Fixed

The new cross-validator evaluates each configured booking slot against the opening ranges of its matching day. It returns:

- **Critical:** no usable slot exists across the weekly schedule
- **Warning:** an open day has no configured usable slot
- **Pass:** one or more usable slots exist

**Result:** The booking UX cannot silently expose a technically valid but practically unusable appointment schedule.

### 5. Form Fail-Safe and Phone Route — Fixed

Contract A has been adopted: a valid direct phone route is part of operational form readiness. The form’s fallback copy may only direct a visitor to call when a valid phone number exists; otherwise it must present a neutral unavailable/retry message.

### 6. Shared NAP Readiness — Fixed

`NAP_Helper` centralizes required NAP fields, extraction, missing-field detection, validation and display-address composition. Health validation and schema readiness are required to use the same helper.

### 7. Rank Math Freshness & Determinism — Fixed by Runbook/Contract

The release defines an explicit observation sequence:

1. Request the front page.
2. Request the contact page.
3. Check the observation metadata.
4. Run health review.

This prevents a deployment health decision from depending on random visitor traffic. Rank Math schema output is currently intentionally disabled; if it is re-enabled later, this observation process and the exact activated version must be applied before evidence is accepted.

### 8. Rank Math Runtime Evidence — Defined

When Rank Math LocalBusiness/schema output is enabled for staging validation, evidence must record:

```text
context
observed_at
rank_math_version
state_hash
```

Front and Contact must both be checked against the exact approved Rank Math version.

### 9. Schema Repair Human-Readable Diff — Defined

For each service repaired only on staging, the evidence must include a readable before/after snapshot as well as the payload hash. Required diff fields:

```text
H1
Intro
FAQ
CTA
Image ID and ALT
Internal links
```

### 10. Schema Repair Environment Gate — Defined

Schema repair apply is permitted only on an isolated clone explicitly configured with:

```php
define('WP_ENVIRONMENT_TYPE', 'staging');
```

A non-production server alone is insufficient. Apply remains forbidden on the live production site.

### 11. Booking UX Availability — Fixed

The date-aware availability helper uses the same booking/open-hours overlap result. It must return a non-operational/limited state when no usable future appointment date exists.

### 12. Owner-Approved NAP Freeze — Implemented

`NAP_Freeze` defines a freeze and integrity contract for:

```text
Business name
Display and E.164 phone
Street, city, region, country, postal
Geo and map
Notify email
Mail From and From Name
Booking slots
Opening hours
Social URLs
```

The freeze is an owner-controlled release action, performed after settings are confirmed and before runtime evidence collection.

### 13. NAP Consistency Test — Implemented

The QA module defines comparison across:

```text
Home
Contact
Footer
Appointment mail body
Rank Math JSON-LD (when enabled)
```

Any phone or location mismatch is a release failure.

### 14–21. Production Gates — Documented

The release checklist retains, without weakening, the client’s production hard gates:

- PHP and WordPress real-runtime verification
- WP-CLI, PHPUnit, WPCS/PHPCS, PHPStan and deprecation scans where environment access permits
- Rank Math state and exact-version evidence when re-enabled
- SMTP/SPF/DKIM/DMARC/inbox evidence
- production debug hardening
- loopback/WP-Cron
- backup/restore
- redirects, canonical, indexability
- headers, cache, modified/lastmod, CWV, browser and accessibility review
- RTL/admin checks
- privacy, assets and medical-claims approvals
- Horizon 365 and Lead 0 owner approval

---

## Current Production Context

The following production observations were reviewed during this session:

| Observation | Current State | Release Interpretation |
|---|---|---|
| WordPress | 7.0.4 | Must be separately runtime-tested after release deployment |
| PHP | 8.5.7 | New code must remain explicit-type compatible; deprecated nullable parameter warning requires patch verification |
| Theme | 1.4.26 active | New v1.4.29 patch has not yet been installed as a complete theme build |
| Plugin | 1.3.1 active | New v1.3.4 patch has not yet been installed as a complete plugin build |
| Rank Math plugin | Active status addressed; schema output intentionally disabled | No LocalBusiness runtime/schema evidence is claimed while disabled |
| `WP_DEBUG_DISPLAY` | Resolved by owner during session | Must remain false before final freeze |
| Loopback | HTTP 503 previously observed | Hosting/support-owned blocker; production approval remains blocked until resolved |
| Service schemas | implant/crown/surgery/general marked `schema_invalid` | Staging-only repair evidence required; no live apply permitted |
| Imagick | Not installed | Recommended hosting enhancement; not a code-level release fix |

---

## Schema Repair: Exact Staging Procedure

The four services currently marked `schema_invalid` are:

```text
implant — page ID 256
crown — page ID 257
surgery — page ID 258
general — page ID 259
```

### A. Create an Isolated Staging Clone

This action needs hosting/client-area access. It is a hosting-support or site-owner task, not a release-code task.

The clone must:

1. Be a copy of current production files and database.
2. Use a non-indexed staging URL.
3. Have outbound email disabled or redirected safely.
4. Explicitly define:

```php
define('WP_ENVIRONMENT_TYPE', 'staging');
```

5. Keep the production site unchanged.

### B. Install Exact Release Patches on Staging

1. Back up the staging files/database.
2. Replace the theme’s `front-page.php` with the v1.4.29 file.
3. Replace plugin files in their exact `includes/` paths with v1.3.4 versions.
4. Add `tests/nap-consistency-test.php` under the plugin’s test/QA path.
5. Confirm that the plugin header and theme stylesheet version are updated in the actual runtime build before collecting evidence.
6. Clear LiteSpeed/server/application caches on staging.

### C. Freeze Operational Inputs

Before modifying services, record and approve:

```text
Business name
Visible phone and E.164 phone
Street, city, region, country, postal code
Map/geo data
Notification email
Mail From and From Name
Booking slots and opening hours
Social URLs
```

Do not change a location only in one field. `clinic_street` is the street component used by Local schema; `clinic_address` is a legacy/display field. A location change requires synchronized updates and a new freeze.

### D. Diagnose Each Service

For pages 256–259, record a before snapshot containing:

```text
Page ID and service key
H1
Intro/body opening
FAQ entries
CTA text and destination
Featured image ID and alt text
Internal links
Existing schema/meta values
```

Determine the exact reason for `schema_invalid` from the plugin validation state. Do not make speculative content changes.

### E. Staging-Only Repair

For each service, repair only the failing schema-required field(s). Preserve approved content and record every change in a human-readable diff.

Example evidence format:

```text
Service: implant
Page ID: 256
Before H1: ...
After H1: ...
Before FAQ count: 3
After FAQ count: 3
Before CTA: ...
After CTA: ...
Before image ID / alt: ...
After image ID / alt: ...
Before internal links: ...
After internal links: ...
Payload SHA-256 before: ...
Payload SHA-256 after: ...
```

### F. Validate Before Any Production Decision

After all four repairs on staging:

1. Confirm all four service validation results are PASS.
2. Confirm no content drift outside the approved diffs.
3. Confirm booking/opening-hour cross-validation passes.
4. Confirm Home, Contact and Footer display identical NAP values.
5. If Rank Math schema is re-enabled for testing, record its active version, context and observation timestamps. Do not re-enable it in production merely to collect evidence.
6. Capture screenshots or exported evidence for each required check.
7. Obtain owner approval of the final evidence bundle.

### G. Production Apply

There is **no automatic or live schema-repair apply** in this release process. Production changes require a separately approved deployment after staging evidence passes, the hosting blocker is closed, and the client/owner approves the release.

---

## Remaining Actions by Owner / Hosting Support

### Owner / Site Operator Actions

| Action | Reason | Status |
|---|---|---|
| Keep `WP_DEBUG_DISPLAY` false | Prevents public error disclosure | Completed in session; must be retained |
| Keep Rank Math schema intentionally disabled unless approved | Avoids unvalidated schema output | Completed in session |
| Organize runtime files into actual theme/plugin paths | Flat repository folder is not deployable as-is | Required before staging deployment |
| Obtain/arrange isolated staging clone | Required for schema repair and runtime evidence | Pending |
| Approve NAP freeze values | Required before runtime evidence | Pending |
| Review staging repair diffs | Required before production decision | Pending |
| Maintain a default WordPress theme | Recovery/safety recommendation | Pending/recommended |

### Hosting Support Actions

| Action | Reason | Status |
|---|---|---|
| Resolve loopback HTTP 503 | Required for WP-Cron, scheduled actions, editor/plugin stability and production readiness | Pending hosting support |
| Confirm WP-Cron reliability after loopback repair | Required runtime evidence | Pending hosting support |
| Install/enable Imagick PHP extension | Recommended for WordPress image processing | Optional/recommended |
| Provide or enable staging clone/client-area access | Required to run staging-only repair workflow | Pending hosting/support access |

### Hosting Support Ticket Text

> Please investigate a WordPress loopback failure on fasdent.ir. WordPress Site Health reports that a loopback request returns HTTP 503. The site runs Apache/LiteSpeed with PHP 8.5.7. Please identify and resolve the server/WAF/LiteSpeed rule, internal DNS, PHP handler, permission, resource, or upstream configuration causing loopback requests to return 503; then confirm that `/wp-cron.php` and standard authenticated/unauthenticated loopback requests return HTTP 200. Please also advise whether an isolated staging clone can be provided and whether the PHP Imagick extension can be enabled.

---

## Release Decision

### Code-Level Decision

**PASS FOR STAGING** — The release specification closes the client’s requested code-level contracts and defines the required operational safeguards.

### Runtime/Production Decision

**NOT YET PROVEN / NOT APPROVED FOR PRODUCTION** — The exact v1.4.29/v1.3.4 runtime artifacts must be structured into their actual installation paths, deployed to an explicit staging clone, and validated. The loopback 503 hosting blocker and staging-only schema repair evidence remain open.

### Required Next Milestone

**Staging clone available → exact build installed → four service repairs evidenced → health/NAP/booking validation captured → owner review.**

---

## File Handling Notes

The `1.4.29/` repository directory currently preserves source patches and documentation. Before a WordPress installation, package them into the existing runtime structure rather than uploading the whole folder directly.

### Theme Runtime Patch

```text
1.4.29/front-page.php
→ wp-content/themes/alipasandi-clinic/front-page.php
```

### Plugin Runtime Patches

```text
1.4.29/health-checks.php
→ wp-content/plugins/alipasandi-service-content/includes/health-checks.php

1.4.29/validators.php
→ wp-content/plugins/alipasandi-service-content/includes/validators.php

1.4.29/site-settings.php
→ wp-content/plugins/alipasandi-service-content/includes/site-settings.php

1.4.29/forms.php
→ wp-content/plugins/alipasandi-service-content/includes/forms.php

1.4.29/nap-consistency-test.php
→ wp-content/plugins/alipasandi-service-content/tests/nap-consistency-test.php
```

The plugin bootstrap file must load any new/changed include files; this must be confirmed against the existing `alipasandi-service-content.php` bootstrap before deployment.

---

## Final Status Matrix

| Area | Status |
|---|---|
| Artifact repository | Ready for runtime packaging |
| Homepage hard-coded NAP fallback | Fixed in source patch |
| Shared NAP readiness contract | Implemented in source patch |
| Street critical health gate | Implemented in source patch |
| Partial PostalAddress fail-safe contract | Implemented/defined |
| Booking/opening-hours overlap | Implemented in source patch |
| Form phone-route fail-safe | Implemented in source patch |
| NAP freeze and consistency contracts | Implemented in source patch |
| Schema repair guardrails | Defined for staging-only execution |
| Production debug display | Resolved during this session; retain false |
| Rank Math schema output | Intentionally disabled; no runtime claim made |
| Loopback 503 | Open hosting blocker |
| Four invalid service schemas | Open; staging-only repair evidence required |
| Staging runtime evidence | Pending |
| Production approval | No — pending required evidence and hosting remediation |

---

**Prepared for client review**  
**Release:** Theme v1.4.29 / Plugin v1.3.4  
**Decision:** Code-level response complete; staging validation and hosting remediation required before production approval.