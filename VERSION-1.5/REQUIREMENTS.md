# Fasdent Version 1.5 — Controlled Requirements Register

## Status Definitions

- **PASS** — verified with recorded evidence.
- **FAIL** — reproduced defect requiring correction before approval.
- **PENDING** — not yet tested or lacking evidence.
- **DEFERRED** — intentionally excluded from this release; must include owner, reason, and (if it was ever Blocker=Yes) a written client acceptance record.
- **N/A** — not applicable after environment confirmation (must include why).

## Blocker Rules (Single Authoritative Gate)

- **Yes** — Status must be **PASS**. FAIL or PENDING blocks approval.
- **No** — may remain PENDING/DEFERRED/N/A without blocking the code package.

Client-accepted residual risk: set Status=DEFERRED and Blocker=No in the same edit; record acceptance in Evidence.

### Approval Truth Table

| Any Blocker=Yes FAIL | Any Blocker=Yes PENDING | All Blocker=Yes PASS | Result |
|---|---|---|---|
| Yes | * | * | REJECT |
| No | Yes | * | REJECT |
| No | No | Yes | **APPROVE** |

## FINAL GATE RESULT — 2026-08-16

**APPROVED** — all current Blocker=Yes rows are PASS.  
See `docs/RELEASE-STATUS.md` for the full evidence matrix.

## Release Identity

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---|---|---|
| V15-001 | Canonical theme source | Yes | **PASS** | CANONICAL_SOURCE.md |
| V15-002 | Companion plugin active | Yes | **PASS** | Alipasandi Service Content **1.2.0** active |
| V15-003 | Version recorded | Yes | **PASS** | Theme **1.5.0**; assets `?ver=1.5.0` |
| V15-004 | No historical folders on disk | No | PENDING | Optional hosting check |

## Visual and Assets

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---|---|---|
| V15-101 | Desktop logo | Yes | **PASS** | Screenshots |
| V15-102 | Mobile logo | Yes | **PASS** | 375 / 412 |
| V15-103 | Hero breakpoints | Yes | **PASS** | 375 / 768 / desktop; webp 200 |
| V15-104 | Hero RTL / crop | Yes | **PASS** | Screenshots |
| V15-105 | CSS/JS/fonts/images 200 | Yes | **PASS** | theme.css, rtl.css, theme.js 200 |
| V15-106 | No overflow | Yes | **PASS** | Multi-viewport |
| V15-107 | Mobile nav | Yes | **PASS** | Menu open with links |
| V15-108 | Floating chat | No | **PASS** | Desktop + mobile |

## WordPress / Forms

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---|---|---|
| V15-201 | Homepage template | Yes | **PASS** | Live front page |
| V15-202 | Brand mark | No | **PASS** | Code-based logo |
| V15-203 | Theme+plugin versions | Yes | **PASS** | 1.5.0 + 1.2.0 |
| V15-204 | Services/menus | Yes | **PASS** | Live |
| V15-205 | Booking/contact | Yes | **PASS** | Success message recorded |
| V15-206 | No demo content | Yes | **PASS** | Production NAP |

## URL / SEO / Content

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---|---|---|
| V15-301 | HTTPS | Yes | **PASS** | fasdent.ir |
| V15-302 | HTTP→HTTPS | Yes | **PASS** | Redirect confirmed |
| V15-303 | No mixed content | Yes | **PASS** | Console clean |
| V15-304 | NAP | No | **PASS** | Phone + address; hours unchanged per client |
| V15-305–307 | Rank Math / alt / sitemap | No | PENDING | Optional |

## Quality

| ID | Requirement | Blocker | Status | Evidence |
|---|---|---|---|---|
| V15-401 | No PHP fatal | Yes | **PASS** | Pages render |
| V15-402 | BOM scan | No | PENDING | Optional |
| V15-403 | Console / theme JS | Yes | **PASS** | Clarity deactivated 2026-08-16; clean |
| V15-404 | Keyboard a11y | No | PENDING | Optional |
| V15-405 | RTL | Yes | **PASS** | Multi-viewport |
| V15-406 | CWV | No | PENDING | Optional |
| V15-407 | Backup restore | No | PENDING | Ops |

## Feedback control

New AI findings must be classified (blocker / environment / preference / duplicate / out of scope) before any code change. An AI suggestion does not change the approved design or this gate result.
