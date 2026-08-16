# Fasdent Version 1.5 — Canonical Source

## Purpose

Version 1.5 is a controlled release-candidate track intended to eliminate source confusion and prevent unverified changes from being delivered to Fasdent.ir.

This document defines the source of truth. It does not claim that production approval has already been granted.

## Canonical Runtime

| Layer | Canonical source | Responsibility |
|---|---|---|
| WordPress theme | `Theme/` | Presentation, templates, styles, scripts, assets, navigation, layout |
| WordPress plugin | `Plugin/` | Service content, business logic, validation, forms, health checks, data ownership |
| Documentation | `VERSION-1.5/` and `docs/` | Requirements, evidence, release decisions |

The `React/`, `New Fasdent/`, `Manually updated/`, `Updated 1.4.29/`, `1.4.29/`, and historical `Version 1.4.*` folders are reference or archive material. They must not be used as production deployment sources for Version 1.5.

## Ownership Rules

- The theme must not duplicate plugin-owned business logic.
- The plugin must not replace the approved theme presentation layer.
- Production content must come from WordPress settings, pages, posts, plugin-owned service records, or the approved media library.
- A production ZIP must contain one theme root and one compatible plugin package.
- A file is not considered deployed merely because it exists somewhere in this repository.
- Environment claims require staging or production evidence.

## Version 1.5 Baseline

Version 1.5 is based on the approved WordPress/PHP architecture and the last release candidate whose theme/plugin compatibility and artifact process were documented. It incorporates only verified fixes; it does not automatically inherit every historical experiment.

Before packaging, the release owner must record:

- Active theme version.
- Active plugin version.
- Git commit SHA.
- Theme SHA-256.
- Plugin SHA-256.
- WordPress version.
- PHP version.
- Database backup reference.
- Staging URL or environment reference.

## Known Risk Areas

The following must be explicitly verified before customer delivery:

- Hero image URL, responsive variants, fallback, dimensions, and crop.
- Logo source, Customizer value, URL, dimensions, and mobile rendering.
- CSS and RTL stylesheet loading order.
- JavaScript loading and duplicate event bindings.
- Local font loading and Persian text reflow.
- HTTPS redirects and mixed-content prevention.
- WordPress homepage/template selection.
- Companion plugin activation and compatibility.
- Production cache invalidation.
- Responsive behavior at approved breakpoints.

## Release Rule

No historical folder may be copied into the canonical source during release unless the change is reviewed, tested, and recorded in the Version 1.5 requirements register.

No production approval may be inferred from a repository-only inspection. A release is ready only after code checks and environment evidence both pass.