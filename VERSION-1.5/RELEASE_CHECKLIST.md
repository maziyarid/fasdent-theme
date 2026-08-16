# Fasdent Version 1.5 — Release Checklist

## Gate Semantics (single authoritative rule)

**Production approval of the code package is granted if and only if:**

> Every requirement whose **current** Blocker column in `REQUIREMENTS.md` is **Yes** has Status **PASS** with recorded evidence.

There is no second path.

### Handling client-accepted residual risk

If the client gives written acceptance of residual risk for a Blocker=Yes item:

1. Set Status = **DEFERRED**
2. Set Blocker = **No** (same edit)
3. Record the acceptance in the Evidence column (date, who, residual-risk statement)

After that edit the item is no longer a gate.

Items that start as Blocker=No may stay PENDING / DEFERRED / N/A without blocking the package.

Authoritative classifications (must match REQUIREMENTS.md):

| ID | Item | Blocker |
|----|------|--------|
| V15-004 | Historical / prototype folders on disk | **No** |
| V15-407 | Backup **restore** rehearsal | **No** |

## Before Build

- [ ] Confirm `Theme/` is the only production theme source. (V15-001, Blocker=Yes)
- [ ] Confirm `Plugin/` is the only production plugin source. (V15-002, Blocker=Yes)
- [ ] Exclude React and historical folders from the release **archive** (packaging hygiene; V15-004 is **Blocker=No** for package gate — do not reject the release solely if live disk cleanup is still pending).
- [ ] Confirm no secrets, backups, database dumps, logs, or private uploads are included.
- [ ] Confirm the approved clinic name, doctor data, address, phone, email, and services.

## Code Validation

- [ ] Run PHP syntax checks over every PHP file. (V15-401)
- [ ] Scan all PHP files for UTF-8 BOM. (V15-402, Blocker=No)
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
- [ ] Test key viewports including 375, 768, 1024, 1440.
- [ ] Check browser console and network requests. (V15-105, V15-303)
- [ ] Run RTL checks. (V15-405)
- [ ] Run booking/contact tests with synthetic data only. (V15-205)
- [ ] Test cache purge and asset version changes where applicable.
- [ ] Backup restore rehearsal (V15-407, **Blocker=No** — optional / cut-over).

## Production Verification (cut-over)

- [ ] Confirm active theme/plugin versions before replacement.
- [ ] Create a production backup (ops best practice; not a separate package-gate blocker unless elevated in REQUIREMENTS.md).
- [ ] Deploy only the approved package.
- [ ] Purge WordPress/server/CDN/browser caches as configured.
- [ ] Verify HTTP-to-HTTPS and host redirects. (V15-301, V15-302)
- [ ] Verify logo and hero on the live host. (V15-101–104)
- [ ] Verify no mixed-content or theme console errors. (V15-303, V15-403)
- [ ] Verify forms and NAP. (V15-205, V15-304)
- [ ] Capture desktop/tablet/mobile evidence under `docs/evidence/live-browser/`.
- [ ] Record deployment versions (V15-003).

## Delivery Gate Decision

Apply the single rule only:

> All current **Blocker=Yes** requirements in `REQUIREMENTS.md` are **PASS** with evidence → **APPROVE**.  
> Any current **Blocker=Yes** is FAIL or PENDING → **REJECT**.

Do not invent a second approval path.  
**V15-004** and **V15-407** remaining PENDING must not reject the package while Blocker=No.

Package status as of 2026-08-16: **APPROVE** (see REQUIREMENTS.md and docs/RELEASE-STATUS.md).
