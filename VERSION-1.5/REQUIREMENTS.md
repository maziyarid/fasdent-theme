# Fasdent Version 1.5 — Controlled Requirements Register

## Status Definitions

- **PASS** — verified with recorded evidence.
- **FAIL** — reproduced defect requiring correction.
- **PENDING** — not yet tested or lacking evidence.
- **DEFERRED** — intentionally excluded from this release and documented.
- **N/A** — not applicable after environment confirmation.

## Release Identity

| ID | Requirement | Type | Status | Evidence |
|---|---|---|---|---|
| V15-001 | One canonical theme source is selected | Governance | PASS | `VERSION-1.5/CANONICAL_SOURCE.md` |
| V15-002 | One compatible companion plugin is selected | Governance | PENDING | Staging inventory |
| V15-003 | Release commit and artifact hashes are recorded | Release | PENDING | Release manifest |
| V15-004 | Historical and experimental folders are excluded from deployment | Release | PENDING | ZIP manifest |

## Visual and Asset Requirements

| ID | Requirement | Type | Status | Evidence |
|---|---|---|---|---|
| V15-101 | Approved logo loads on desktop | Visual | PENDING | Screenshot + network log |
| V15-102 | Approved logo loads on mobile | Visual | PENDING | Mobile screenshot + network log |
| V15-103 | Hero image loads at all required breakpoints | Visual | PENDING | Responsive screenshots |
| V15-104 | Hero crop, aspect ratio, contrast, and text alignment match approval | Visual | PENDING | Design comparison |
| V15-105 | No CSS, JS, image, icon, or font request returns an error | Technical | PENDING | Browser network export |
| V15-106 | No horizontal overflow or clipping exists | Responsive | PENDING | Breakpoint test matrix |
| V15-107 | Header and mobile navigation do not double-bind scripts | Functional | PENDING | Console/test evidence |
| V15-108 | Floating contact/chat behavior is intentional and functional | Functional | PENDING | Browser test |

## WordPress and Plugin Requirements

| ID | Requirement | Type | Status | Evidence |
|---|---|---|---|---|
| V15-201 | Correct homepage template is active | WordPress | PENDING | Template/runtime evidence |
| V15-202 | Site logo and site icon are assigned correctly | WordPress | PENDING | Admin screenshot/export |
| V15-203 | Theme and companion plugin versions satisfy compatibility rules | WordPress | PENDING | Admin inventory |
| V15-204 | Service post types, taxonomies, menus, and settings exist | Functional | PENDING | WP-CLI/admin evidence |
| V15-205 | Booking and contact flows work end-to-end | Functional | PENDING | Synthetic staging submission |
| V15-206 | No demo, historical, or stale content is exposed | Content | PENDING | Crawl/content inventory |

## URL, SEO, and Content Requirements

| ID | Requirement | Type | Status | Evidence |
|---|---|---|---|---|
| V15-301 | Canonical HTTPS host is selected | Infrastructure | PENDING | Header crawl |
| V15-302 | HTTP, HTTPS, www, and non-www behavior is intentional | Infrastructure | PENDING | Redirect evidence |
| V15-303 | No mixed-content warnings occur | Security | PENDING | Browser console |
| V15-304 | NAP is consistent across settings, templates, footer, contact page, and schema | SEO | PENDING | Entity diff |
| V15-305 | Rank Math metadata/schema does not conflict with theme output | SEO | PENDING | Rendered HTML audit |
| V15-306 | Logo, hero, and content images have correct alt treatment | SEO | PENDING | HTML audit |
| V15-307 | Sitemap, robots, canonical, noindex, and redirects are correct | SEO | PENDING | Crawl report |

## Quality and Accessibility Requirements

| ID | Requirement | Type | Status | Evidence |
|---|---|---|---|---|
| V15-401 | PHP syntax and WordPress runtime checks pass | Code | PENDING | CI/staging log |
| V15-402 | No UTF-8 BOM exists in PHP files | Code | PENDING | BOM scan |
| V15-403 | JavaScript syntax and browser console checks pass | Code | PENDING | Node/browser log |
| V15-404 | Keyboard navigation and visible focus work | Accessibility | PENDING | Keyboard test |
| V15-405 | RTL layout and reading order are correct | Accessibility | PENDING | RTL screenshots |
| V15-406 | Mobile and desktop Core Web Vitals are recorded | Performance | PENDING | Lighthouse/field evidence |
| V15-407 | Backup and restore are rehearsed | Operations | PENDING | Restore report |

## Feedback Control

Every new client or AI finding must be classified before changing code:

1. Confirmed defect — fix and add evidence.
2. Environment-dependent item — test in staging/production.
3. Duplicate finding — link to an existing requirement.
4. Preference — request a clear design decision.
5. Out of scope — document the reason.
6. Deferred enhancement — add to a future release, not Version 1.5.

An AI suggestion does not automatically change the approved design or the canonical source.