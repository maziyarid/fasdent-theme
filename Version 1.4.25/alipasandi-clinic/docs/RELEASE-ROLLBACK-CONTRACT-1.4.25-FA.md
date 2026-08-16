# Rollback / Compatibility Contract — 1.4.25 / Plugin 1.3.0

- Production candidate pairing: **Theme 1.4.25 + Plugin 1.3.0**.
- Theme 1.4.24 is **superseded compatibility-only**, not the known-critical canonical-fatal release.
- Theme 1.4.22 is the historical **known critical** release because of the unavailable canonical API call; do not deploy it.
- Plugin 1.3.0 can load safely with Theme >=1.4.24, but Production requires 1.4.25.
- If Plugin rolls back to 1.2.0 while Theme 1.4.25 is active, Theme detects it as outdated and disables known write/form/Rank-Math-NAP/mail filters, leaving read-only emergency rendering. Upgrade back to 1.3.0 before Production traffic.
- `_alipasandi_service`, `_alipasandi_service_key` and `schema_version:1` remain compatible; no destructive uninstall hook exists.
- Rollback never deletes service/NAP data. Before rollback: DB backup + Service JSON export + artifact hash record.
