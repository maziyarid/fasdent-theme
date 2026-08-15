# Rollback / Compatibility Contract — 1.4.28 / Plugin 1.3.3

- Production-candidate pairing: **Theme 1.4.28 + Plugin 1.3.3**.
- Theme 1.4.25 / Plugin 1.3.0 are superseded staging artifacts after the registry/horizon SSOT patch; do not mix 1.4.28 with Plugin 1.3.0 because the Theme now consumes `alipasandi_booking_horizon_days()` from Plugin 1.3.3.
- Theme 1.4.24 is **superseded compatibility-only**, not the known-critical canonical-fatal release.
- Theme 1.4.22 is the historical **known critical** release because of the unavailable canonical API call; do not deploy it.
- Plugin 1.3.3 can load safely with Theme >=1.4.24, but Production requires Theme 1.4.28.
- If Plugin rolls back below 1.3.3 while Theme 1.4.28 is active, Theme detects it as outdated and forms/write paths remain disabled until the production pair is restored.
- `_alipasandi_service`, `_alipasandi_service_key` and `schema_version:1` remain compatible; no destructive uninstall hook exists.
- Rollback never deletes service/NAP data. Before rollback: DB backup + uploads backup + Service JSON export + artifact hash record.
