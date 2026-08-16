# Fasdent Version 1.5 — Controlled Requirements Register

## Status Definitions

- **PASS** — verified with recorded evidence.
- **FAIL** — reproduced defect requiring correction before approval.
- **PENDING** — not yet tested or lacking evidence.
- **DEFERRED** — intentionally excluded from this release; must include owner, reason, and (if it was ever Blocker=Yes) a written client acceptance record.
- **N/A** — not applicable after environment confirmation (must include why).

## Blocker Rules (Single Authoritative Gate)

Every requirement has a **Blocker** flag:

- **Yes** — currently a release gate. Status must be **PASS**. FAIL or PENDING blocks approval.
- **No** — not a release gate. May be PENDING, DEFERRED, N/A, or PASS without blocking the code package.

### How client-accepted residual risk is handled (eliminates dual outcomes)

When a client gives **written acceptance** of residual risk for an item that is currently Blocker=Yes:

1. Status is set to **DEFERRED**.
2. **Blocker is changed from Yes to No** in the same update.
3. The Evidence column records the acceptance (date, who accepted, residual risk statement).

After that update the item is no longer a gate. The truth table below then applies to the *current* Blocker column only. There is never a parallel "approve despite Blocker=Yes DEFERRED" path.

### Approval Truth Table (authoritative)

Evaluate only the **current** Blocker column after any acceptance updates:

| Any current Blocker=Yes is FAIL | Any current Blocker=Yes is PENDING | Every current Blocker=Yes is PASS | Non-blockers in any status | Result |
|---|---|---|---|---|
| Yes | * | * | * | **REJECT** — fix the failing blocker |
| No | Yes | * | * | **REJECT** — obtain evidence or convert to DEFERRED + set Blocker=No with written client acceptance |
| No | No | Yes | Allowed | **APPROVE** code package for production deployment |

There is exactly one APPROVE path. No conflicting outcomes.

### Environment items

Infrastructure items that can only be proven on the live host (DNS, SMTP, live CWV, backup restore) may remain PENDING while the *code package* is accepted. They become production cut-over blockers at go-live, not at code-freeze. Mark them Blocker=No for the code-package gate and list them separately in the cut-over section of the checklist.

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
| V15-407 | Backup and restore are rehearsed | Operations | No | PENDING | Restore report (cut-over item) |

## Feedback Control

Every new client or AI finding must be classified before changing code:

1. Confirmed defect — fix and add evidence.
2. Environment-dependent item — test in staging/production; keep Blocker=Yes only if it is a true code-package gate.
3. Duplicate finding — link to an existing requirement.
4. Preference — request a clear design decision.
5. Out of scope — document the reason.
6. Deferred enhancement — set Status=DEFERRED, Blocker=No, record owner and target release.

An AI suggestion does not automatically change the approved design or the canonical source.
