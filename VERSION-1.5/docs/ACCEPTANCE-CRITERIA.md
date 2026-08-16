# Fasdent Version 1.5 — Customer Delivery Acceptance Criteria

This is the only customer-delivery gate for Version 1.5. Other findings are not release blockers unless they map to one of these requirements.

## Status

- `PASS`: verified with evidence.
- `FAIL`: defect reproduced and must be corrected.
- `PENDING`: not yet verified.
- `N/A`: explicitly excluded after review.

No production approval may be claimed while a required P0 item is `FAIL` or `PENDING`.

## P0 — Identity and Deployment

- [ ] Active WordPress theme is exactly Version 1.5.0.
- [ ] Active companion plugin is exactly Version 1.2.0.
- [ ] The live theme directory contains no `Version-*`, historical release, React, prototype, backup, or manual-upload files.
- [ ] The deployed ZIP matches the approved Version 1.5 manifest and SHA-256 hashes.
- [ ] A full production backup of the database and `wp-content/uploads` exists.
- [ ] Backup restoration has been tested or the release remains blocked.
- [ ] Rollback package and procedure are available.

## P0 — Visual Integrity

- [ ] Logo brand text and tooth icon are visible on desktop.
- [ ] Logo brand text and tooth icon are visible on mobile.
- [ ] Hero image returns HTTP 200 at 375px.
- [ ] Hero image returns HTTP 200 at 768px.
- [ ] Hero image returns HTTP 200 at 1024px.
- [ ] Hero image returns HTTP 200 at 1440px.
- [ ] Hero has the approved height and crop.
- [ ] Hero text is readable and correctly aligned RTL.
- [ ] Header does not overlap or clip the hero.
- [ ] Service cards are intact.
- [ ] Doctor block is intact.
- [ ] Footer is intact.
- [ ] No broken-image icon, placeholder, unexpected blank section, or horizontal overflow appears.

## P0 — Technical Runtime

- [ ] `theme.css` returns HTTP 200 with `text/css`.
- [ ] `rtl.css` returns HTTP 200 with `text/css` when loaded.
- [ ] `theme.js` returns HTTP 200 with the expected JavaScript MIME type.
- [ ] Every rendered image request returns HTTP 200.
- [ ] Every rendered font request returns HTTP 200.
- [ ] Browser console has zero uncaught errors.
- [ ] Browser console has zero mixed-content warnings.
- [ ] CSS has no broken `url(...)` references.
- [ ] JavaScript is loaded once and does not double-bind the mobile menu.
- [ ] All WordPress, server, CDN, object, and browser caches have been purged.
- [ ] A clean-browser request renders Version 1.5 after cache purge.
- [ ] Mobile menu opens correctly.
- [ ] Mobile menu closes correctly.
- [ ] Escape closes the mobile menu.
- [ ] Focus returns to the menu trigger after close.
- [ ] Keyboard navigation remains usable.

## P0 — Forms and Core Actions

- [ ] Booking form renders without PHP or JavaScript errors.
- [ ] Booking form validates invalid input safely.
- [ ] Booking form submits a synthetic staging request successfully.
- [ ] Booking success and error states are understandable.
- [ ] Contact form renders without PHP or JavaScript errors.
- [ ] Contact form submits a synthetic staging request successfully.
- [ ] No form test uses real patient data.
- [ ] No user input is exposed in logs or error responses.

## P1 — Production Data and Canonical Host

- [ ] Production phone is correct.
- [ ] Production address is correct.
- [ ] Production opening hours are correct.
- [ ] Production floating-chat channels are configured correctly.
- [ ] Header, footer, contact page, schema, and plugin settings use one NAP source of truth.
- [ ] One canonical HTTPS host is selected.
- [ ] HTTP redirects directly to canonical HTTPS.
- [ ] `www` behavior is intentional and tested.
- [ ] Canonical, sitemap, robots, and form URLs use the canonical host.

## Evidence Required

For each item record:

- Requirement ID.
- PASS/FAIL/PENDING status.
- URL or screen.
- Theme version and plugin version.
- Release commit and artifact hashes.
- Browser/device/viewport.
- Test date and timezone.
- Tester.
- Screenshot, network export, console export, log, or backup reference.
- Corrective action and owner.

## Delivery Decision

- **READY:** all required P0 checks PASS; P1 checks PASS or are explicitly approved as non-blocking.
- **BLOCKED:** any required P0 check is FAIL or PENDING.
- **NOT PRODUCTION-APPROVED:** repository files alone are insufficient evidence.