# Fasdent Version 1.5 — Controlled Requirements Register

## Status Definitions

- **PASS** — verified with recorded evidence.
- **FAIL** — reproduced defect requiring correction before approval.
- **PENDING** — not yet tested or lacking evidence.
- **DEFERRED** — intentionally excluded from this release and documented with an owner and target release.
- **N/A** — not applicable after environment confirmation (must include why).

## Blocker Rules (Release Gate Semantics)

Every requirement has a **Blocker** flag:

- **Yes** — must be PASS with evidence before production approval. FAIL or PENDING blocks the release.
- **No** — important, but may remain PENDING or DEFERRED without blocking the code package itself. Still recorded for transparency.

### Approval Truth Table

| Any Blocker = FAIL | Any Blocker = PENDING | All Blockers = PASS | Non-blockers PENDING/DEFERRED/N/A | Result |
|---|---|---|---|---|
| Yes | * | * | * | **REJECT** — fix or reclassify |
| No | Yes | * | * | **REJECT** — obtain evidence or reclassify to DEFERRED with client sign-off |
| No | No | Yes | Allowed | **APPROVE** for production deployment of the code package |
| No | No | Yes | Client explicitly accepts residual risk | **APPROVE** with documented residual risks |

**Authorization rules for DEFERRED / N/A on Blocker=Yes items:**

- A Blocker=Yes item may **never** be marked DEFERRED or N/A without written client approval that names the residual risk.
- Environment-only items (DNS, SMTP, live CWV, backup restore) may stay PENDING while the *code package* is accepted; they become production-go blockers at the final cut-over, not at code-freeze.

## Release Identity

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-001 | One canonical theme source is selected | Governance | Yes | PASS | `VERSION-1.5/CANONICAL_SOURCE.md` |
| V15-002 | One compatible companion plugin is selected | Governance | Yes | PENDING | Staging inventory |
| V15-003 | Release commit and artifact hashes are recorded | Release | Yes | PENDING | Release manifest |
| V15-004 | Historical and experimental folders are excluded from deployment | Release | Yes | PENDING | ZIP manifest |

## Visual and Asset Requirements

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-101 | Approved logo loads on desktop | Visual | Yes | PENDING | Screenshot + network log |
| V15-102 | Approved logo loads on mobile | Visual | Yes | PENDING | Mobile screenshot + network log |
| V15-103 | Hero image loads at all required breakpoints | Visual | Yes | PENDING | Responsive screenshots |
| V15-104 | Hero crop, aspect ratio, contrast, and text alignment match approval | Visual | Yes | PENDING | Design comparison |
| V15-105 | No CSS, JS, image, icon, or font request returns an error | Technical | Yes | PENDING | Browser network export |
| V15-106 | No horizontal overflow or clipping exists | Responsive | Yes | PENDING | Breakpoint test matrix |
| V15-107 | Header and mobile navigation do not double-bind scripts | Functional | Yes | PENDING | Console/test evidence |
| V15-108 | Floating contact/chat behavior is intentional and functional | Functional | No | PENDING | Browser test |

## WordPress and Plugin Requirements

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-201 | Correct homepage template is active | WordPress | Yes | PENDING | Template/runtime evidence |
| V15-202 | Site logo and site icon are assigned correctly | WordPress | No | PENDING | Admin screenshot/export |
| V15-203 | Theme and companion plugin versions satisfy compatibility rules | WordPress | Yes | PENDING | Admin inventory |
| V15-204 | Service post types, taxonomies, menus, and settings exist | Functional | Yes | PENDING | WP-CLI/admin evidence |
| V15-205 | Booking and contact flows work end-to-end | Functional | Yes | PENDING | Synthetic staging submission |
| V15-206 | No demo, historical, or stale content is exposed | Content | Yes | PENDING | Crawl/content inventory |

## URL, SEO, and Content Requirements

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-301 | Canonical HTTPS host is selected | Infrastructure | Yes | PENDING | Header crawl |
| V15-302 | HTTP, HTTPS, www, and non-www behavior is intentional | Infrastructure | Yes | PENDING | Redirect evidence |
| V15-303 | No mixed-content warnings occur | Security | Yes | PENDING | Browser console |
| V15-304 | NAP is consistent across settings, templates, footer, contact page, and schema | SEO | Yes | PENDING | Entity diff |
| V15-305 | Rank Math metadata/schema does not conflict with theme output | SEO | No | PENDING | Rendered HTML audit |
| V15-306 | Logo, hero, and content images have correct alt treatment | SEO | No | PENDING | HTML audit |
| V15-307 | Sitemap, robots, canonical, noindex, and redirects are correct | SEO | No | PENDING | Crawl report |

## Quality and Accessibility Requirements

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-401 | PHP syntax and WordPress runtime checks pass | Code | Yes | PENDING | CI/staging log |
| V15-402 | No UTF-8 BOM exists in PHP files | Code | Yes | PENDING | BOM scan |
| V15-403 | JavaScript syntax and browser console checks pass | Code | Yes | PENDING | Node/browser log |
| V15-404 | Keyboard navigation and visible focus work | Accessibility | No | PENDING | Keyboard test |
| V15-405 | RTL layout and reading order are correct | Accessibility | Yes | PENDING | RTL screenshots |
| V15-406 | Mobile and desktop Core Web Vitals are recorded | Performance | No | PENDING | Lighthouse/field evidence |
| V15-407 | Backup and restore are rehearsed | Operations | No* | PENDING | Restore report |

\* V15-407 becomes a production cut-over blocker; it does not block acceptance of the code package itself.

## Feedback Control

Every new client or AI finding must be classified before changing code:

1. Confirmed defect — fix and add evidence.
2. Environment-dependent item — test in staging/production; mark Blocker=Yes only if it is a true go-live condition.
3. Duplicate finding — link to an existing requirement.
4. Preference — request a clear design decision.
5. Out of scope — document the reason.
6. Deferred enhancement — add to a future release, not Version 1.5.

An AI suggestion does not automatically change the approved design or the canonical source.
