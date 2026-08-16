# Fasdent Version 1.5 — Release Status

**Decision: BLOCKED / NOT PRODUCTION-APPROVED**

## Completed in repository

- Canonical Version 1.5 Theme/Plugin source areas exist.
- Canonical source and ownership rules are documented.
- P0/P1 acceptance criteria are documented.
- The approval exception for written acceptance of a named residual risk is now defined identically across the requirements register, checklist, and acceptance criteria.

## Still environment-dependent

The following cannot be marked PASS from GitHub alone:

- Active theme/plugin versions.
- Live directory cleanup.
- Production backup and restore.
- Logo and hero visual rendering at required viewports.
- HTTP 200 status for all rendered assets.
- Browser console and mixed-content state.
- Cache purge.
- Mobile menu and keyboard interaction.
- Booking/contact submissions.
- Production NAP/floating-chat settings.
- Canonical HTTPS redirects.

## Decision authority

`VERSION-1.5/REQUIREMENTS.md` contains the authoritative decision table. `RELEASE_CHECKLIST.md` and `docs/ACCEPTANCE-CRITERIA.md` must not introduce a different approval rule.

Until required P0 evidence is attached, the release remains BLOCKED.