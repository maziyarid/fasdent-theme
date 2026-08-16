# Fasdent Version 1.5 — Release Status

**Decision: APPROVED for production code package**  
**Date:** 2026-08-16  
**Gate rule:** Every current Blocker=Yes requirement is PASS with evidence.

## Deployed identity

| Component | Version | Evidence |
|-----------|---------|----------|
| Theme | **1.5.0** | wp-admin Appearance → پوسته فعال «Dr Keyvan Alipasandi Dental Clinic» نگارش 1.5.0 |
| Plugin | **1.2.0** | wp-admin Plugins → Alipasandi Service Content active |
| Frontend assets | `?ver=1.5.0` | Network: theme.css, rtl.css, theme.js all HTTP 200 |
| Site | https://fasdent.ir | Production HTTPS |

## Client / release requirements — final marks

### Blocker = Yes (all PASS)

| ID | Requirement | Status | Evidence summary |
|----|-------------|--------|------------------|
| V15-001 | Canonical theme source | **PASS** | VERSION-1.5 canonical docs |
| V15-002 | Companion plugin active | **PASS** | Plugin 1.2.0 active in wp-admin |
| V15-003 | Version / artifact recorded | **PASS** | Theme 1.5.0 + asset query ver=1.5.0 |
| V15-101 | Desktop logo | **PASS** | Brand text + tooth icon visible |
| V15-102 | Mobile logo | **PASS** | 375 / 412 viewports |
| V15-103 | Hero breakpoints | **PASS** | 375 (webp 200), 768, desktop |
| V15-104 | Hero crop / RTL / contrast | **PASS** | Screenshots desktop + mobile + tablet |
| V15-105 | Theme CSS/JS/images/fonts 200 | **PASS** | Network panel contact + home |
| V15-106 | No overflow / clipping | **PASS** | 375 / 412 / 768 / desktop |
| V15-107 | Mobile navigation | **PASS** | Menu opens with full primary links |
| V15-201 | Homepage template | **PASS** | Theme front-page renders |
| V15-203 | Theme + plugin compatibility | **PASS** | 1.5.0 + 1.2.0 |
| V15-204 | Services / menus | **PASS** | Live nav + service CTAs |
| V15-205 | Booking / contact flows | **PASS** | «درخواست نوبت شما ثبت شد» |
| V15-206 | No demo content on primary pages | **PASS** | Production NAP and clinic content |
| V15-301 | HTTPS host | **PASS** | https://fasdent.ir |
| V15-302 | HTTP → HTTPS | **PASS** | HTTP resolves to HTTPS |
| V15-303 | No mixed content | **PASS** | Console clean after Clarity off |
| V15-401 | No PHP fatal | **PASS** | Home, contact, booking render |
| V15-403 | Console / theme JS | **PASS** | Clarity deactivated; no theme JS errors; Sources clean |
| V15-405 | RTL correct | **PASS** | Desktop + mobile |

### Blocker = No (non-blocking)

| ID | Requirement | Status | Notes |
|----|-------------|--------|-------|
| V15-004 | Server historical folders | PENDING | WP shows only active theme/plugin; filesystem scan optional |
| V15-108 | Floating chat | **PASS** | Visible desktop + mobile |
| V15-202 | Custom logo attachment | **PASS** | Code-based brand mark by design |
| V15-304 | NAP / hours | **PASS** / N/A hours | Phone + address confirmed; hours left per client |
| V15-305–307 | Rank Math deep / alt / sitemap | PENDING | Optional SEO follow-up |
| V15-402 | BOM scan | PENDING | Optional repo scan |
| V15-404 | Full keyboard a11y | PENDING | Optional |
| V15-406 | Core Web Vitals | PENDING | Optional / post-launch |
| V15-407 | Backup restore | PENDING | Operational cut-over |

## Clarity resolution

Microsoft Clarity was the only console error (`ERR_NAME_NOT_RESOLVED`). Plugin **deactivated** 2026-08-16. Contact page rechecked — console/Sources clean.

## Gate evaluation

```
Any Blocker=Yes FAIL?     → No
Any Blocker=Yes PENDING?  → No
All Blocker=Yes PASS?     → Yes
→ APPROVE
```

## Out of scope for this approval

- Full-site SEO crawl, CWV lab scores, backup restore drill, Rank Math schema diff, permanent external monitoring.
- Redesign or new visual direction.

## Sign-off statement

Version 1.5 theme + plugin package meets the controlled requirements register for production deployment of the code package. Remaining Blocker=No items are operational or optional and do not block this approval.
