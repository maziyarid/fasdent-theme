# Fasdent Version 1.5 — Controlled Requirements Register

## Status Definitions

- **PASS** — verified with recorded evidence.
- **FAIL** — reproduced defect requiring correction.
- **PENDING** — not tested or evidence is unavailable.
- **DEFERRED** — intentionally accepted only through the written exception process below.
- **N/A** — proven not applicable with a recorded reason.

## Authoritative Release Rule

A requirement is a release blocker only when `Required = Yes` and `Blocker = Yes`.

Version 1.5 is **APPROVED** only when one of these two conditions is true:

1. Every required blocker is `PASS` with evidence; or
2. Every required blocker is `PASS`, except explicitly named blockers marked `DEFERRED`, with written client acceptance of each residual risk, an owner, an expiry/review date, and a linked evidence record.

The following states always block approval for a required blocker: `FAIL`, `PENDING`, or undocumented `DEFERRED`/`N/A`.

This rule is authoritative for `RELEASE_CHECKLIST.md` and `docs/ACCEPTANCE-CRITERIA.md`.

## Release Identity

| ID | Requirement | Type | Required | Blocker | Status | Evidence |
|---|---|---|---:|---:|---|---|
| V15-001 | One canonical theme source is selected | Governance | Yes | Yes | PASS | `VERSION-1.5/CANONICAL_SOURCE.md` |
| V15-002 | One compatible companion plugin is selected | Governance | Yes | Yes | PENDING | Staging inventory |
| V15-003 | Release commit and artifact hashes are recorded | Release | Yes | Yes | PENDING | Release manifest |
| V15-004 | Historical and experimental folders are excluded from deployment | Release | Yes | Yes | PENDING | ZIP manifest |

## Visual and Asset Requirements

| ID | Requirement | Type | Required | Blocker | Status | Evidence |
|---|---|---|---:|---:|---|---|
| V15-101 | Approved logo loads on desktop | Visual | Yes | Yes | PENDING | Screenshot + network log |
| V15-102 | Approved logo loads on mobile | Visual | Yes | Yes | PENDING | Mobile screenshot + network log |
| V15-103 | Hero image loads at all required breakpoints | Visual | Yes | Yes | PENDING | Responsive screenshots/network |
| V15-104 | Hero crop, aspect ratio, contrast, and text alignment match approval | Visual | Yes | Yes | PENDING | Design comparison |
| V15-105 | No CSS, JS, image, icon, or font request returns an error | Technical | Yes | Yes | PENDING | Browser network export |
| V15-106 | No horizontal overflow or clipping exists | Responsive | Yes | Yes | PENDING | Breakpoint test matrix |
| V15-107 | Header and mobile navigation do not double-bind scripts | Functional | Yes | Yes | PENDING | Console/test evidence |
| V15-108 | Floating contact/chat behavior is intentional and functional | Functional | Yes | No | PENDING | Browser test |

## WordPress and Plugin Requirements

| ID | Requirement | Type | Required | Blocker | Status | Evidence |
|---|---|---|---:|---:|---|---|
| V15-201 | Correct homepage template is active | WordPress | Yes | Yes | PENDING | Runtime evidence |
| V15-202 | Site logo and site icon are assigned correctly | WordPress | Yes | Yes | PENDING | Admin screenshot/export |
| V15-203 | Theme and companion plugin versions satisfy compatibility rules | WordPress | Yes | Yes | PENDING | Admin inventory |
| V15-204 | Service post types, taxonomies, menus, and settings exist | Functional | Yes | Yes | PENDING | WP-CLI/admin evidence |
| V15-205 | Booking and contact flows work end-to-end | Functional | Yes | Yes | PENDING | Synthetic staging submission |
| V15-206 | No demo, historical, or stale content is exposed | Content | Yes | Yes | PENDING | Crawl/content inventory |

## URL, SEO, and Content Requirements

| ID | Requirement | Type | Required | Blocker | Status | Evidence |
|---|---|---|---:|---:|---|---|
| V15-301 | Canonical HTTPS host is selected | Infrastructure | Yes | Yes | PENDING | Header crawl |
| V15-302 | HTTP, HTTPS, www, and non-www behavior is intentional | Infrastructure | Yes | Yes | PENDING | Redirect evidence |
| V15-303 | No mixed-content warnings occur | Security | Yes | Yes | PENDING | Browser console |
| V15-304 | NAP is consistent across settings, templates, footer, contact page, and schema | SEO | Yes | Yes | PENDING | Entity diff |
| V15-305 | Rank Math metadata/schema does not conflict with theme output | SEO | Yes | No | PENDING | Rendered HTML audit |
| V15-306 | Logo, hero, and content images have correct alt treatment | SEO | Yes | No | PENDING | HTML audit |
| V15-307 | Sitemap, robots, canonical, noindex, and redirects are correct | SEO | Yes | No | PENDING | Crawl report |

## Quality and Accessibility Requirements

| ID | Requirement | Type | Required | Blocker | Status | Evidence |
|---|---|---|---:|---:|---|---|
| V15-401 | PHP syntax and WordPress runtime checks pass | Code | Yes | Yes | PENDING | CI/staging log |
| V15-402 | No UTF-8 BOM exists in PHP files | Code | Yes | Yes | PENDING | BOM scan |
| V15-403 | JavaScript syntax and browser console checks pass | Code | Yes | Yes | PENDING | Node/browser log |
| V15-404 | Keyboard navigation and visible focus work | Accessibility | Yes | No | PENDING | Keyboard test |
| V15-405 | RTL layout and reading order are correct | Accessibility | Yes | No | PENDING | RTL screenshots |
| V15-406 | Mobile and desktop Core Web Vitals are recorded | Performance | Yes | No | PENDING | Lighthouse/field evidence |
| V15-407 | Backup and restore are rehearsed | Operations | Yes | Yes | PENDING | Restore report |

## Decision Table

| Required blocker state | Client exception record | Decision |
|---|---|---|
| All PASS | Not needed | APPROVE |
| No FAIL/PENDING; named DEFERRED blockers only | Written acceptance + owner + expiry + residual-risk record for each | APPROVE WITH ACCEPTED RISK |
| Any FAIL | Any | BLOCK |
| Any PENDING | Any | BLOCK |
| Undocumented DEFERRED or N/A | Any | BLOCK |
| Non-blocker FAIL/PENDING | Not needed for release gate, but record remediation | CONDITIONAL / TRACKED |

## Feedback Control

Every new client or AI finding must be classified as a defect, environment check, duplicate, preference, deferred enhancement, or out-of-scope item before changing code.