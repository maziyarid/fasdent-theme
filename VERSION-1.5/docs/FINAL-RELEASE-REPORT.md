# Fasdent Version 1.5 — Final Release Report

**Date:** 2026-08-16  
**Repository:** maziyarid/fasdent-theme  
**PR:** #6 (merged into `main`)  
**Merge SHA:** `2d282fd`  
**Last gate fix:** `034eeb6`

---

## 1. Executive decision

# APPROVED — Version 1.5 theme and plugin package

This is a **package approval**, not a promise of infinite redesign.

### Authoritative gate (only one)

```text
For every requirement in VERSION-1.5/REQUIREMENTS.md
where Blocker = Yes:
    Status must be PASS
    with recorded evidence

If all such rows PASS → APPROVE
Otherwise → BLOCKED
```

There is **no** second approval path.  
`V15-004` (historical folders) and `V15-407` (backup restore) remain **Blocker = No** and **must not** reject the package.

---

## 2. Delivered identity

| Component | Version | Evidence |
|-----------|---------|----------|
| Theme | **1.5.0** | wp-admin Appearance — active |
| Plugin | **1.2.0** | Alipasandi Service Content — active |
| Frontend assets | `?ver=1.5.0` | theme.css, rtl.css, theme.js HTTP 200 |
| Production URL | https://fasdent.ir | HTTPS live |

Baseline lineage: stable **1.4.24** + critical fixes → frozen as **1.5.0**.

---

## 3. Blocker=Yes results (all PASS)

| ID | Requirement | Status |
|----|-------------|--------|
| V15-001 | Canonical theme source | PASS |
| V15-002 | Companion plugin active | PASS |
| V15-003 | Version recorded | PASS |
| V15-101 | Desktop logo | PASS |
| V15-102 | Mobile logo | PASS |
| V15-103 | Hero breakpoints | PASS |
| V15-104 | Hero crop / RTL | PASS |
| V15-105 | Required assets HTTP 200 | PASS |
| V15-106 | No overflow / clipping | PASS |
| V15-107 | Mobile navigation | PASS |
| V15-201 | Homepage template | PASS |
| V15-203 | Theme + plugin compatibility | PASS |
| V15-204 | Services / menus | PASS |
| V15-205 | Booking form success | PASS |
| V15-206 | No demo content on primary pages | PASS |
| V15-301 | HTTPS host | PASS |
| V15-302 | HTTP → HTTPS | PASS |
| V15-303 | No mixed content | PASS |
| V15-401 | No PHP fatal | PASS |
| V15-403 | Console (post–Clarity disable) | PASS |
| V15-405 | RTL | PASS |

Evidence root: `VERSION-1.5/docs/evidence/live-browser/`

---

## 4. Non-blocking items (correctly left open)

| ID | Item | Blocker | Notes |
|----|------|---------|-------|
| V15-004 | Historical folders on disk | No | Ops optional |
| V15-407 | Backup restore rehearsal | No | Ops optional |
| V15-305–307 | Deep Rank Math / sitemap | No | Post-release optional |
| V15-406 | Core Web Vitals | No | Post-release optional |

These do **not** justify a new version number.

---

## 5. Governance resolution

| Problem | Resolution |
|---------|------------|
| ACCEPTANCE-CRITERIA vs REQUIREMENTS conflict | Fixed — criteria defers to REQUIREMENTS Blocker column |
| RELEASE_CHECKLIST V15-004 marked Blocker=Yes | Fixed in `034eeb6` → Blocker=No |
| Dual deferred-blocker approval path | Removed |
| Clarity console FAIL | Historical retained; plugin deactivated; current PASS |
| Evidence only in chat | Structured evidence committed under `docs/evidence/live-browser/` |

Aligned documents:

- `VERSION-1.5/REQUIREMENTS.md`
- `VERSION-1.5/docs/ACCEPTANCE-CRITERIA.md`
- `VERSION-1.5/RELEASE_CHECKLIST.md`
- `VERSION-1.5/docs/REVIEW-RESULTS.md`
- `VERSION-1.5/docs/EVIDENCE-REGISTER.md`
- `VERSION-1.5/docs/RELEASE-STATUS.md`

---

## 6. What this release is not

- Not a redesign project
- Not Version 1.6
- Not an invitation to re-open visual direction via third-party AI audits
- Not blocked by optional SEO, CWV, or disk hygiene tasks

---

## 7. Controlled change policy going forward

1. New feedback must be logged against an existing requirement ID or opened as a **new written requirement** with Blocker Yes/No.
2. AI suggestions are **not** automatic requirements.
3. Preference and “what if we changed…” items are **out of scope** until the client signs a change order.
4. Production changes after 1.5.0 are **patches** (1.5.x) for defects only — not new product versions.

---

## 8. Sign-off

**Package status:** APPROVED  
**PR status:** Merged and closed  
**Production package:** Theme 1.5.0 + Plugin 1.2.0  

Reproducible from `VERSION-1.5/REQUIREMENTS.md` alone.
