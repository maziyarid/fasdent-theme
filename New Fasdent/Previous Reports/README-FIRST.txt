FASDENT COMPLETE DELIVERY BUNDLE
================================

Start here:

1. Read LIVE-SITE-DIAGNOSIS-2026-08-15.md.
   The public origin and WordPress Admin returned 502 during delivery. Hosting
   must restore the upstream before any upload.

2. Read FASDENT-1.4.29-1.3.4-COMPLETE-DELIVERY-REPORT.md.
   It closes every client requirement and distinguishes source acceptance from
   runtime/production proof.

3. Follow STAGING-RUNBOOK-1.4.29-1.3.4.md exactly.
   Verify SHA256SUMS.txt before installation.

Installable WordPress files:

- alipasandi-service-content-1.3.4.zip  (install/activate first)
- alipasandi-clinic-1.4.29.zip          (install/activate second)

Do not upload the parent bundle to WordPress. Extract it locally and upload the
two child ZIPs. Never manually merge, rebuild or edit either child after hash
verification.

Release state:

- Corrected artifacts: staging candidate after origin recovery
- Current public site: hosting/deployment incident
- Production: hold until every critical runbook gate passes
