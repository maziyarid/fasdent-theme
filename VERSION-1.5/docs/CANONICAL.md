# Canonical Source of Truth — Version 1.5.0

**Effective immediately.**

## Single Deliverable

- **Theme folder:** `Theme/` (Version 1.5.0)
- **Plugin folder:** `Plugin/` (Stable tag 1.2.0)
- **No other folders** from the repository history may be uploaded to production.

## Historical / Experimental Folders (Do Not Deploy)

- `1.4.29/`, `Updated 1.4.29/`, `New Fasdent/`
- `Version 1.4.10` … `Version 1.4.xx`
- `Manually updated/`
- `React/`
- Any “final report”, “ultimate package”, or parallel UI experiment

These exist only as historical record of the development and AI-review process.

## Why This Freeze Exists

Multiple parallel tracks and repeated “final” packages created deployment ambiguity. The live site can load an older, incomplete, or mixed artifact even when the design itself is correct. Freezing identity is the only way to make subsequent verification meaningful.

## Compatibility Contract

- Theme 1.5.0 requires Plugin ≥ 1.2.0
- Plugin provides service content, form handlers, validators, health checks
- Theme owns presentation, RTL CSS, icon system, image helpers, SEO output fallbacks

## Change Policy After 1.5.0

Any future change must:

1. Be recorded in a new version number
2. Update this CANONICAL.md
3. Include a clear “why this change is required” entry in the requirement register
4. Be accompanied by concrete evidence (screenshot, network log, or test output)

No new parallel folder. No silent overwrites.
