# Release 1.4.25 / Plugin 1.3.0

Production candidate **source artifacts**, not Production-approved until Staging evidence passes.

## Files

- `alipasandi-clinic-1.4.25.zip` — Theme
- `alipasandi-service-content-1.3.0.zip` — Site Plugin
- `CLIENT-RESPONSE-FINAL-1.4.25.md` — full 66-item client response
- `RELEASE-MANIFEST-1.4.25.txt` — hashes/compatibility
- `BUILD-VALIDATION-1.4.25.txt` — what was actually validated vs not run
- `PRODUCTION-PREFLIGHT-1.4.25-FA.md` — environmental evidence gates

## Staging order

1. Backup DB + uploads.
2. Install Plugin 1.3.0.
3. Install Theme 1.4.25.
4. Set WordPress timezone to `Asia/Tehran`.
5. Configure clinic-approved NAP, E.164, address, Notify Email, domain From/From Name, booking slots and official opening hours.
6. Run Tools → Service Content → Migration/Retry and Health.
7. Configure/verify Rank Math and keep KML/Local Sitemap disabled until SSOT evidence.
8. Run the full preflight on the exact artifact hashes.

Do not use Theme 1.4.22. Theme 1.4.24 is superseded and compatibility-only; Production candidate pairing is **1.4.25 + 1.3.0**.
