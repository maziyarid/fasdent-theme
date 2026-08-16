# Fasdent Version 1.5 — Acceptance Criteria

**Authoritative gate:** `VERSION-1.5/REQUIREMENTS.md` (current `Blocker` column only).

```text
For every requirement where Blocker = Yes:
    Status must be PASS
    AND reviewable evidence must be recorded (path or admin capture).

If all such rows PASS → APPROVE
Otherwise → BLOCKED
```

There is **no** second approval path for `DEFERRED` while `Blocker` remains `Yes`.
To defer a blocker: set `Status=DEFERRED` **and** `Blocker=No` in the same edit, with residual-risk acceptance recorded.

This file must not invent blockers. Classifications for operational items:

| ID | Item | Blocker |
|----|------|--------|
| V15-004 | Historical folders on disk | **No** |
| V15-407 | Backup **restore** rehearsal | **No** |

Those may be documented and improved; their absence does **not** reject the package while Blocker=No.

## Blocker=Yes checklist (must be PASS)

Identity: V15-001, V15-002, V15-003
Visual/assets: V15-101–V15-107
WordPress/forms: V15-201, V15-203, V15-204, V15-205, V15-206
URL/security: V15-301, V15-302, V15-303
Quality: V15-401, V15-403, V15-405

Evidence directory: `VERSION-1.5/docs/evidence/live-browser/`
Status matrix: `VERSION-1.5/docs/RELEASE-STATUS.md`
Register: `VERSION-1.5/REQUIREMENTS.md`

## Non-blocking (Blocker=No)

V15-004, V15-108, V15-202, V15-304–307, V15-402, V15-404, V15-406, V15-407 — optional/ops.

## Decision

Reproduce the decision only from `REQUIREMENTS.md` Blocker=Yes rows.
As of 2026-08-16 all Blocker=Yes rows are **PASS** → **APPROVE** (see RELEASE-STATUS.md).
