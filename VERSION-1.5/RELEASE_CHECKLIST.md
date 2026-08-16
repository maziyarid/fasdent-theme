# Fasdent Version 1.5 — Release Checklist

## Before Build

- [ ] Confirm `Theme/` is the only production theme source.
- [ ] Confirm `Plugin/` is the only production plugin source.
- [ ] Exclude React and historical folders from the release archive.
- [ ] Confirm no secrets, backups, database dumps, logs, or private uploads are included.
- [ ] Confirm the approved clinic name, doctor data, address, phone, email, services, and opening hours.

## Code Validation

- [ ] Run PHP syntax checks over every PHP file.
- [ ] Scan all PHP files for UTF-8 BOM.
- [ ] Run JavaScript syntax checks.
- [ ] Check theme and plugin version headers.
- [ ] Check all template and asset paths.
- [ ] Confirm logo and hero references use WordPress-safe URLs or verified theme assets.
- [ ] Confirm stylesheets are enqueued once and in the correct order.
- [ ] Confirm scripts are enqueued once and do not double-bind navigation.
- [ ] Confirm responsive rules do not create overflow or clipping.
- [ ] Confirm fallback behavior when optional settings or plugin data are absent.

## Staging Validation

- [ ] Activate the exact release package in a clean WordPress installation.
- [ ] Activate the exact companion plugin.
- [ ] Import only approved synthetic/demo data.
- [ ] Verify homepage template and Customizer settings.
- [ ] Verify logo, hero, fonts, icons, CSS, JS, menus, forms, service cards, footer, and floating chat.
- [ ] Test at 320, 360, 375, 390, 414, 768, 820, 1024, 1280, and 1440 pixels.
- [ ] Check browser console and network requests.
- [ ] Run keyboard, RTL, contrast, and reduced-motion checks.
- [ ] Run booking/contact tests with synthetic data only.
- [ ] Test cache purge and asset version changes.
- [ ] Test backup and restore.

## Production Verification

- [ ] Confirm active theme/plugin versions before replacement.
- [ ] Create and verify a backup.
- [ ] Deploy only the approved package.
- [ ] Purge WordPress/server/CDN/browser caches.
- [ ] Verify HTTP-to-HTTPS and host redirects.
- [ ] Verify logo and hero URLs on the live host.
- [ ] Verify no mixed-content or console errors.
- [ ] Verify forms, phone links, booking URL, NAP, schema, sitemap, and robots behavior.
- [ ] Capture desktop, tablet, and mobile screenshots.
- [ ] Record deployment commit, artifact hashes, test date, tester, and rollback reference.

## Delivery Gate

Do not label Version 1.5 as production-approved until every required blocker has PASS evidence. Items that cannot be tested from GitHub must be marked PENDING rather than represented as completed.