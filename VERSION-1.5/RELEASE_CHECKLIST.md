# Fasdent Version 1.5 — Release Checklist

## Gate Semantics (addresses Greptile P1)

Production approval of the **code package** is granted only when:

1. Every requirement with **Blocker = Yes** is in **PASS** status with recorded evidence, **or**
2. The client has given written acceptance of a residual risk that converts a Blocker=Yes item to DEFERRED (explicitly named).

Items with **Blocker = No** may remain PENDING, DEFERRED, or N/A without blocking the code package. They are still tracked.

**Undefined terms are not used.** “Required blocker” means any row whose Blocker column is Yes.

See the Approval Truth Table in `REQUIREMENTS.md`.

## Before Build

- [ ] Confirm `Theme/` is the only production theme source. (V15-001, Blocker=Yes)
- [ ] Confirm `Plugin/` is the only production plugin source. (V15-002, Blocker=Yes)
- [ ] Exclude React and historical folders from the release archive. (V15-004, Blocker=Yes)
- [ ] Confirm no secrets, backups, database dumps, logs, or private uploads are included.
- [ ] Confirm the approved clinic name, doctor data, address, phone, email, services, and opening hours.

## Code Validation

- [ ] Run PHP syntax checks over every PHP file. (V15-401)
- [ ] Scan all PHP files for UTF-8 BOM. (V15-402)
- [ ] Run JavaScript syntax checks. (V15-403)
- [ ] Check theme and plugin version headers. (V15-203)
- [ ] Check all template and asset paths.
- [ ] Confirm logo and hero references use WordPress-safe URLs or verified theme assets. (V15-101–104)
- [ ] Confirm stylesheets are enqueued once and in the correct order.
- [ ] Confirm scripts are enqueued once and do not double-bind navigation. (V15-107)
- [ ] Confirm responsive rules do not create overflow or clipping. (V15-106)
- [ ] Confirm fallback behavior when optional settings or plugin data are absent.

## Staging Validation

- [ ] Activate the exact release package in a clean WordPress installation.
- [ ] Activate the exact companion plugin.
- [ ] Import only approved synthetic/demo data.
- [ ] Verify homepage template and Customizer settings. (V15-201)
- [ ] Verify logo, hero, fonts, icons, CSS, JS, menus, forms, service cards, footer, and floating chat.
- [ ] Test at 320, 360, 375, 390, 414, 768, 820, 1024, 1280, and 1440 pixels.
- [ ] Check browser console and network requests. (V15-105, V15-303)
- [ ] Run keyboard, RTL, contrast, and reduced-motion checks. (V15-404, V15-405)
- [ ] Run booking/contact tests with synthetic data only. (V15-205)
- [ ] Test cache purge and asset version changes.
- [ ] Test backup and restore (Blocker=No for code package; becomes cut-over blocker).

## Production Verification (cut-over)

- [ ] Confirm active theme/plugin versions before replacement.
- [ ] Create and verify a backup.
- [ ] Deploy only the approved package.
- [ ] Purge WordPress/server/CDN/browser caches.
- [ ] Verify HTTP-to-HTTPS and host redirects. (V15-301, V15-302)
- [ ] Verify logo and hero URLs on the live host. (V15-101–104)
- [ ] Verify no mixed-content or console errors.
- [ ] Verify forms, phone links, booking URL, NAP, schema, sitemap, and robots behavior.
- [ ] Capture desktop, tablet, and mobile screenshots.
- [ ] Record deployment commit, artifact hashes, test date, tester, and rollback reference. (V15-003)

## Delivery Gate Decision

Do **not** label Version 1.5 as production-approved until every **Blocker = Yes** requirement has **PASS** evidence (or explicit written client acceptance of residual risk).

Items that cannot be tested from the repository alone must be marked **PENDING**, never silently treated as completed.
