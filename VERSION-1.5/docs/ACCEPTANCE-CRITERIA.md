# Fasdent Version 1.5 — Customer Delivery Acceptance Criteria

This document and `VERSION-1.5/RELEASE_CHECKLIST.md` use the same authoritative decision table in `VERSION-1.5/REQUIREMENTS.md`.

## Required P0 Gates

Each item below is `Required = Yes` and `Blocker = Yes` unless explicitly marked otherwise. A required blocker must be `PASS` with evidence. A named `DEFERRED` blocker can be approved only with written client acceptance, residual-risk record, owner, and expiry/review date. Any `FAIL`, `PENDING`, or undocumented exception blocks approval.

### Identity and deployment

- [ ] Theme exactly Version 1.5.0.
- [ ] Plugin exactly Version 1.2.0.
- [ ] No historical/prototype/backup/manual-upload files in live theme directory.
- [ ] Release package manifest and hashes match.
- [ ] Full database and uploads backup exists.
- [ ] Backup restoration or rollback is verified.

### Visual and technical integrity

- [ ] Logo brand text and tooth icon visible on desktop and mobile.
- [ ] Hero returns HTTP 200 and has approved crop at 375/768/1024/1440.
- [ ] Hero text is readable and RTL; header does not overlap or clip it.
- [ ] Service cards, doctor block, footer, and floating chat are intact.
- [ ] CSS, RTL CSS, JS, images, icons, and fonts return HTTP 200.
- [ ] Console is clean; no mixed-content warnings exist.
- [ ] Caches are purged and the clean-browser page is the intended release.
- [ ] Mobile menu open, close, Escape, focus, and keyboard behavior passes.
- [ ] Synthetic booking and contact tests pass.

### Production data and host

- [ ] Phone, address, opening hours, and floating-chat channels are correct.
- [ ] NAP is consistent across settings, templates, contact page, and schema.
- [ ] One canonical HTTPS host and redirect policy is verified.

## Evidence rule

Screenshots, network exports, console logs, backup/restore records, admin inventory, form test records, and redirect crawls are environment evidence. They cannot be fabricated or marked PASS from GitHub source inspection alone.

## Decision

Use the decision table in `VERSION-1.5/REQUIREMENTS.md`. Until required blocker evidence is attached, the release remains BLOCKED.