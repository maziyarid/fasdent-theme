# Fasdent Version 1.5 — Release Checklist

## Single Approval Rule

Use the decision table in `VERSION-1.5/REQUIREMENTS.md`. A required blocker must be `PASS` with evidence. The only exception is a named `DEFERRED` blocker with written client acceptance, residual-risk record, owner, and expiry/review date. Any `FAIL`, `PENDING`, or undocumented `DEFERRED`/`N/A` required blocker blocks approval.

## Before Build

- [ ] Confirm `Theme/` is the only production theme source. **Required/Blocker: Yes**
- [ ] Confirm `Plugin/` is the only production plugin source. **Required/Blocker: Yes**
- [ ] Exclude React and historical folders from the release archive. **Required/Blocker: Yes**
- [ ] Confirm no secrets, backups, database dumps, logs, or private uploads are included. **Required/Blocker: Yes**
- [ ] Confirm approved clinic identity and production content. **Required/Blocker: Yes**

## Code Validation

- [ ] PHP syntax checks pass. **Required/Blocker: Yes**
- [ ] No UTF-8 BOM exists. **Required/Blocker: Yes**
- [ ] JavaScript syntax checks pass. **Required/Blocker: Yes**
- [ ] Version headers and compatibility are correct. **Required/Blocker: Yes**
- [ ] Template and asset paths are valid. **Required/Blocker: Yes**
- [ ] Stylesheets are enqueued once and in the correct order. **Required/Blocker: Yes**
- [ ] Scripts are enqueued once and do not double-bind navigation. **Required/Blocker: Yes**
- [ ] Responsive rules do not create overflow or clipping. **Required/Blocker: Yes**

## Staging and Production Evidence

- [ ] Exact package activated in clean WordPress. **Required/Blocker: Yes**
- [ ] Exact companion plugin activated. **Required/Blocker: Yes**
- [ ] Logo and hero verified at 375/768/1024/1440. **Required/Blocker: Yes**
- [ ] CSS, RTL CSS, JS, images, and fonts return HTTP 200. **Required/Blocker: Yes**
- [ ] Browser console and mixed-content checks pass. **Required/Blocker: Yes**
- [ ] Cache purge is verified. **Required/Blocker: Yes**
- [ ] Mobile menu open/close/Escape/focus behavior passes. **Required/Blocker: Yes**
- [ ] Synthetic booking and contact submissions pass. **Required/Blocker: Yes**
- [ ] Backup/restore and rollback are verified. **Required/Blocker: Yes**
- [ ] Canonical HTTPS and redirects pass. **Required/Blocker: Yes**

## Release Decision

- [ ] All required blockers are PASS; or
- [ ] All required blockers are PASS except explicitly named DEFERRED blockers with complete written acceptance records.

The register, acceptance criteria, and this checklist must always produce the same decision.