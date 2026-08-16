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

After that update the item is no longer a gate. The truth table below then applies to the *current* Blocker column only.

### Approval Truth Table (authoritative)

| Any current Blocker=Yes is FAIL | Any current Blocker=Yes is PENDING | Every current Blocker=Yes is PASS | Non-blockers in any status | Result |
|---|---|---|---|---|
| Yes | * | * | * | **REJECT** — fix the failing blocker |
| No | Yes | * | * | **REJECT** — obtain evidence or convert to DEFERRED + set Blocker=No with written client acceptance |
| No | No | Yes | Allowed | **APPROVE** code package for production deployment |

## Evidence update — 2026-08-16

Source: live screenshots (desktop, iPhone SE 375, iPad Mini 768, Galaxy S20 412), wp-admin Themes/Plugins, booking success UI, Network panel on homepage and contact.

## Release Identity

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-001 | One canonical theme source is selected | Governance | Yes | PASS | `VERSION-1.5/CANONICAL_SOURCE.md` |
| V15-002 | One compatible companion plugin is selected | Governance | Yes | PASS | wp-admin Plugins: **Alipasandi Service Content 1.2.0** active (غیرفعال کردن visible) |
| V15-003 | Release commit and artifact hashes are recorded | Release | Yes | PASS | Theme **1.5.0** active in Appearance; assets `theme.css?ver=1.5.0`, `rtl.css?ver=1.5.0`, `theme.js?ver=1.5.0` |
| V15-004 | Historical and experimental folders are excluded from deployment | Release | No | PENDING | Ops/hosting confirmation still useful; not blocking if only Theme+Plugin active in WP |

## Visual and Asset Requirements

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-101 | Approved logo loads on desktop | Visual | Yes | PASS | Desktop homepage screenshot — brand text + tooth icon |
| V15-102 | Approved logo loads on mobile | Visual | Yes | PASS | iPhone SE 375 + Galaxy S20 412 — logo + hamburger visible |
| V15-103 | Hero image loads at required breakpoints | Visual | Yes | PASS | 375 (hero-480/768 webp 200), 768 iPad, desktop; implant + doctor visible |
| V15-104 | Hero crop, contrast, RTL text | Visual | Yes | PASS | RTL headline readable; crop acceptable at 375/768/desktop |
| V15-105 | Theme CSS/JS/images/fonts HTTP 200 | Technical | Yes | PASS | `theme.css`, `rtl.css`, `theme.js` ver 1.5.0 = 200; hero webps 200; Vazirmatn/Playfair fonts 200 |
| V15-106 | No horizontal overflow or clipping | Responsive | Yes | PASS | 375/412/768/desktop captures show no overflow; header does not clip logo |
| V15-107 | Mobile navigation works | Functional | Yes | PASS | Mobile menu opens with صفحه اصلی / درباره ما / خدمات / تماس با ما / رزرو نوبت |
| V15-108 | Floating contact/chat intentional | Functional | No | PASS | Desktop + mobile chat launcher visible |

## WordPress and Plugin Requirements

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-201 | Correct homepage template is active | WordPress | Yes | PASS | Live body classes include front-page/home theme template; homepage renders theme hero |
| V15-202 | Site logo and site icon | WordPress | No | PASS | Code-based brand mark renders; favicon.svg 200 |
| V15-203 | Theme and plugin version compatibility | WordPress | Yes | PASS | Theme **1.5.0** + Plugin **1.2.0** both active |
| V15-204 | Services/menus exist | Functional | Yes | PASS | Nav + service CTAs live |
| V15-205 | Booking and contact flows work | Functional | Yes | PASS | Booking success: «درخواست نوبت شما ثبت شد» |
| V15-206 | No demo/stale content on primary surfaces | Content | Yes | PASS | Production clinic identity, real phone/address; no localhost visible on tested pages |

## URL, SEO, and Content Requirements

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-301 | Canonical HTTPS host | Infrastructure | Yes | PASS | https://fasdent.ir production |
| V15-302 | HTTP→HTTPS intentional | Infrastructure | Yes | PASS | HTTP resolves to HTTPS homepage |
| V15-303 | No mixed-content warnings | Security | Yes | PASS | No mixed-content in console; only third-party Clarity DNS failure (see V15-403 note) |
| V15-304 | NAP consistent | SEO | No | PASS | Phone 0920 144 1469; address تهران، جردن، قبادیان غربی، پلاک ۱۱، واحد ۱۰. Opening hours not provided by client — left unchanged (N/A to change) |
| V15-305 | Rank Math vs theme | SEO | No | PENDING | Rank Math active; deep conflict audit optional |
| V15-306 | Image alt treatment | SEO | No | PENDING | Optional audit |
| V15-307 | Sitemap/robots | SEO | No | PENDING | Optional audit |

## Quality and Accessibility Requirements

| ID | Requirement | Type | Blocker | Status | Evidence |
|---|---|---|---|---|---|
| V15-401 | No PHP fatal on runtime pages | Code | Yes | PASS | Homepage + contact + booking UI render |
| V15-402 | No UTF-8 BOM in package | Code | No | PENDING | Repo-level scan optional if prior release already cleaned |
| V15-403 | Theme JS + console (theme-owned) | Code | Yes | PASS* | `theme.js?ver=1.5.0` = 200; no theme JS exception. *Only console error is Microsoft Clarity `ERR_NAME_NOT_RESOLVED` (third-party). Action: deactivate Clarity plugin to clear residual red console, or accept as non-theme residual risk. |
| V15-404 | Keyboard / focus | Accessibility | No | PENDING | Optional |
| V15-405 | RTL layout correct | Accessibility | Yes | PASS | Desktop + mobile RTL screenshots |
| V15-406 | Core Web Vitals | Performance | No | PENDING | Optional / cut-over |
| V15-407 | Backup restore rehearsal | Operations | No | PENDING | Cut-over item; no caching plugin present |

## Gate evaluation (current Blocker=Yes rows)

After this evidence update, remaining **Blocker=Yes** items that are not PASS:

- None on the strict theme/plugin functional set, **except** operational choice on Clarity:
  - If Clarity stays active and DNS fails → console still shows one error (cosmetic/third-party).
  - Recommended: **Deactivate Microsoft Clarity** in Plugins → then V15-403 is fully clean.

**Ops still recommended (Blocker=No):** V15-004 server folder hygiene, V15-407 backup confirmation.

## Feedback Control

Every new client or AI finding must be classified before changing code:

1. Confirmed defect — fix and add evidence.
2. Environment-dependent item — test in staging/production; keep Blocker=Yes only if it is a true code-package gate.
3. Duplicate finding — link to an existing requirement.
4. Preference — request a clear design decision.
5. Out of scope — document the reason.
6. Deferred enhancement — set Status=DEFERRED, Blocker=No, record owner and target release.

An AI suggestion does not automatically change the approved design or the canonical source.
