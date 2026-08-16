# Fasdent Version 1.5 — Release Checklist

## Gate Semantics (single authoritative rule)

**Production approval of the code package is granted if and only if:**

> Every requirement whose **current** Blocker column is **Yes** has Status **PASS** with recorded evidence.

There is no second path.

### Handling client-accepted residual risk

If the client gives written acceptance of residual risk for a Blocker=Yes item:

1. Set Status = **DEFERRED**
2. Set Blocker = **No** (same edit)
3. Record the acceptance in the Evidence column (date, who, residual-risk statement)

After that edit the item is no longer a gate. The single rule above then applies to the remaining Blocker=Yes rows. This removes the previous dual-outcome ambiguity identified by Greptile.

Items that start as Blocker=No may stay PENDING / DEFERRED / N/A without blocking.

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
- [ ] Test backup and restore (V15-407, Blocker=No for code package; cut-over item).

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

Apply the single rule only:

> All current Blocker=Yes requirements are PASS with evidence → **APPROVE**.  
> Any current Blocker=Yes is FAIL or PENDING → **REJECT**.

Do not invent a second approval path. Items that cannot be tested from the repository alone must be marked PENDING (or converted via the client-acceptance procedure above), never silently treated as completed.
