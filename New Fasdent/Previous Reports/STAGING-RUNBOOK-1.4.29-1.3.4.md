# FasDent 1.4.29 / 1.3.4 Staging and Promotion Runbook

**Artifacts:** `alipasandi-clinic-1.4.29.zip` and `alipasandi-service-content-1.3.4.zip`  
**Policy:** build once, hash once, deploy the exact same bytes to Staging and Production  
**Current public-site status at delivery:** 502 origin hard block; restore hosting before upload

## 0. Hard stops

Do not begin installation when any of these is true:

- `https://fasdent.ir/`, `/contact/` or `/wp-admin/` returns 5xx;
- the Theme or Plugin SHA-256 differs from this bundle’s manifest;
- the staging clone is not isolated/noindex or can email real patients;
- no tested restore point exists;
- PHP/WordPress versions are outside the declared lanes;
- the official NAP, URL decisions or clinical/privacy approvers are unknown.

Never install the rejected original uploads:

- Theme SHA-256 `1fae13b2e2c832737d77bd0eb7f4a4f405940f72841396e89e40f6228fbb2214`
- Plugin SHA-256 `e2735c2e06a9900e405fc2d7989bf1d33ba299ec2a3bc7c15d4be2a65570072a`

## 1. Recover and stabilize the origin

Hosting must first diagnose the observed `502 Bad Gateway / [Errno 111] Connection refused`:

1. preserve reverse-proxy, PHP-FPM/container, WordPress and database logs for the incident window;
2. verify the configured upstream socket/host/port exists and accepts connections;
3. check PHP/application service health, database reachability, disk/inode capacity, memory pressure and file ownership;
4. restore the failed upstream without replacing WordPress content or uploading partial files;
5. confirm normal responses from `/`, `/contact/` and `/wp-admin/`;
6. document CDN, page cache, object cache and OPcache topology plus the exact purge procedure;
7. verify the external scheduler if `DISABLE_WP_CRON` is true.

Record the root cause and recovery evidence. “It works now” without upstream evidence is not a closed incident.

## 2. Prepare the isolated Staging clone

Create a fresh production database/files snapshot with:

- `WP_ENVIRONMENT_TYPE=staging`;
- HTTP authentication and/or IP restriction;
- an allowlist for the server's own signed WordPress loopback probes through that restriction;
- WordPress “Discourage search engines” plus an HTTP `X-Robots-Tag: noindex, nofollow` gate;
- outbound mail disabled or forcibly redirected to a controlled test inbox;
- payment, analytics, external webhook and patient-notification integrations disabled;
- no production secrets copied unless strictly needed and protected;
- private debug logs with controlled retention and a PII review;
- a snapshot/restore point taken before installation.

Verify `home` and `siteurl` are staging URLs. Do not perform schema repair, SMTP experimentation or Rank Math toggling on Production.

## 3. Verify the immutable delivery bytes

From the extracted parent bundle:

    sha256sum -c SHA256SUMS.txt
    unzip -t alipasandi-clinic-1.4.29.zip
    unzip -t alipasandi-service-content-1.3.4.zip

Expected hashes:

| Artifact | SHA-256 |
|---|---|
| `alipasandi-clinic-1.4.29.zip` | `a8ffcec325c77abecad87d23dbfba530616e4d4477c2f32ac515ea385a3edd24` |
| `alipasandi-service-content-1.3.4.zip` | `2b4750fa75f6e842bbfa4f960c9265b3d926dabd6578a96d59bbd112b3b1baac` |

Each child ZIP must have exactly one canonical first-level root:

    alipasandi-clinic/
    alipasandi-service-content/

Stop on any mismatch, traversal path, duplicate member, symlink, extra wrapper directory or byte change. Do not rebuild or manually merge.

## 4. Prove backup restoration before change

Restore the pre-change snapshot into a disposable isolated target. Verify:

- homepage and Admin load;
- database tables and uploads are present;
- active Theme/Plugin list matches the snapshot;
- permalinks and login work;
- the restore procedure and timing are recorded.

A backup without a successful isolated restore rehearsal does not satisfy the rollback gate.

## 5. Runtime floor and lint

Run both required lanes against the final extracted ZIP contents:

| Lane | WordPress | PHP |
|---|---:|---:|
| Compatibility floor | 6.4 | 8.2 |
| Production-identical | 7.0.4 | 8.5.7 |

Before activation:

    find alipasandi-clinic alipasandi-service-content -type f -name '*.php' -print0 | xargs -0 -n1 php -l

Run configured PHPUnit, WPCS/PHPCS, PHPStan and deprecated-API scans. Archive their versions, commands, output and exit codes. Source test presence is not execution.

## 6. Install in dependency-safe order

Install and activate the Plugin first, then the Theme. WordPress Admin upload is acceptable; WP-CLI equivalent:

    wp plugin install ./alipasandi-service-content-1.3.4.zip --force --activate
    wp theme install ./alipasandi-clinic-1.4.29.zip --force --activate
    wp plugin status alipasandi-service-content
    wp theme status alipasandi-clinic

Immediately verify:

- Plugin reports 1.3.4;
- Theme reports 1.4.29;
- no PHP fatal/deprecation is displayed;
- no emergency read-only compatibility notice remains;
- Theme CSS and JavaScript URLs contain `ver=1.4.29`;
- there is no file-by-file merge or second build.

Purge the documented CDN/page/object/OPcache layers only after both exact artifacts are installed.

## 7. Configure and approve the canonical operational settings

In the Plugin’s operational settings, enter owner-approved values only:

- official business name;
- display phone and strict E.164 phone;
- display/full address;
- street, city, region, two-letter country and postal code;
- geo coordinates and official map URL;
- public/notification/From email and From name;
- booking times, opening hours, horizon and lead;
- official social URLs;
- explicit Local Schema owner: `disabled` or `rank_math`.

The audit found conflicting indexed location/contact footprints. Do not copy a search snippet as the authority. `clinic_address` is display copy; `clinic_street` is the structured street component. Keep them synchronized.

## 8. Bootstrap and command smoke

Run on the final installed Plugin:

    wp eval 'echo ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION, PHP_EOL;'
    wp alipasandi service health
    wp alipasandi service migrate --dry-run
    wp alipasandi service schema-repair
    wp alipasandi operations health
    wp alipasandi nap consistency
    wp alipasandi nap freeze-status

Expected behavior:

- every command boots without Fatal;
- dry-run commands do not write;
- non-probe operations/NAP reports may warn that fresh rendered evidence is absent;
- incomplete configuration fails closed instead of fabricating values.

## 9. Diagnose and, only if approved, repair service schema markers

Start with:

    wp alipasandi service schema-repair

Archive the complete dry-run output and plan SHA-256. Each service must be classified as one of:

- `missing_schema_marker`;
- `unknown_schema_version`;
- `malformed_meta`;
- `sanitizer_drift`;
- `required_field_content_missing`.

Only a `missing_schema_marker`-only plan with owner-approved existing content may be applied. `unknown_schema_version` is always blocked and requires a separately designed migration:

    wp alipasandi service schema-repair --apply --plan-sha256=<exact-dry-run-plan-hash>

This command is Staging-only. It must prove exactly one meta row, unchanged content since planning, transaction success and identical canonical content SHA-256 before/after. Stop on any H1, intro, FAQ, CTA, image, link or payload change.

Run service migration dry-run before any migration. A write migration requires a separate approved staging change window and snapshot:

    wp alipasandi service migrate --dry-run

## 10. Run blocking rendered-output gates

After cache purge:

    wp alipasandi service health --probe
    wp alipasandi operations health --probe
    wp alipasandi nap consistency --probe

All commands must exit 0. The operations probe must prove returned—not merely source—HTML:

- Front and Contact return 2xx/3xx;
- `main#main-content` exists on both;
- homepage has `home-hero`;
- rendered service-card count covers the normalized managed registry;
- homepage returns at least two images;
- Theme CSS and JavaScript are both `ver=1.4.29`;
- loopback succeeds;
- NAP, forms, booking policy, freeze, Rank Math, debug display and scheduler checks are not critical.

The NAP probe must match the actual Home, Contact, both Footers, actual outbound-email NAP block and Front/Contact JSON-LD observation hashes.

## 11. Mail and form proof

With all staging mail redirected to the controlled inbox:

1. submit valid and invalid Contact forms;
2. submit valid appointment times at opening, before closing and exactly at closing;
3. verify closing is end-exclusive;
4. verify closed days, out-of-horizon dates, inadequate lead, invalid services, nonce failure, honeypot and limiter all fail;
5. verify a valid request redirects correctly and reaches the test inbox;
6. capture From, From Name, Return-Path, SPF, DKIM and DMARC results;
7. confirm no patient/test PII appears in public or long-retention logs.

`wp_mail()` returning true is not inbox-delivery evidence.

## 12. Rank Math and SEO matrix

Rank Math owns titles, descriptions, canonicals, robots and XML sitemaps in Production. Local schema is separately and explicitly `disabled` or `rank_math`.

For both owner states:

- probe Front and Contact explicitly;
- record exact Rank Math version, context, UTC observation time, graph SHA-256, local-entity count and NAP SHA-256;
- when disabled, assert zero Dentist/MedicalBusiness/LocalBusiness nodes;
- when enabled, assert at least one complete normalized entity and no duplicates;
- verify breadcrumbs have one owner per context.

Also test:

- HTTPS/www/non-www/trailing-host redirects without chains;
- canonical on home, services, posts, pagination and UTM URLs;
- robots and XML sitemap;
- appointment indexing policy;
- blog archive, search, 404, feeds and attachments;
- no partial PostalAddress when street, city, region or country is absent.

## 13. URL inventory and redirect approval

The public index currently exposes both `/appointment/` and this release’s `/appointments/` resolution, plus legacy pricing, implant and deeper service URLs. Do not guess redirects.

Export all published URLs and sitemap entries, then record for each:

| Source URL | Current status | Canonical | Keep/Merge/Redirect/Gone | Approved destination | Owner |
|---|---:|---|---|---|---|

After owner/SEO approval, implement and test the map for:

- no redirect chains or loops;
- no soft 404s;
- no accidental loss of a legitimate service page;
- query-string/UTM handling;
- canonical equal to the final destination;
- internal links and sitemap use only final URLs.

## 14. Visual, accessibility and performance QA

Test mobile and desktop in current Chrome, Firefox, Safari and Edge:

- homepage content/images/cards and no blank dark region;
- keyboard skip link lands on `#main-content`;
- visible focus and logical order;
- desktop/mobile service navigation;
- menu, FAQ, forms and contact controls;
- 200% zoom/reflow, contrast and touch targets;
- RTL public pages and WordPress Admin;
- image aspect ratio, lazy/eager behavior and missing-asset console/network errors;
- CWV/Lighthouse mobile and desktop using production-like cache.

Review all medical/pricing claims, doctor identity, image rights, privacy/cookie copy and the removal of any unapproved “+10,000” claim.

## 15. Freeze the staging evidence

Only after the exact settings and artifacts pass:

    wp alipasandi nap freeze \
      --owner=<approver-name> \
      --theme-sha256=a8ffcec325c77abecad87d23dbfba530616e4d4477c2f32ac515ea385a3edd24 \
      --plugin-sha256=2b4750fa75f6e842bbfa4f960c9265b3d926dabd6578a96d59bbd112b3b1baac \
      --note='Staging release candidate approval'

Then:

    wp alipasandi nap freeze-status
    wp alipasandi operations health --probe
    wp alipasandi nap consistency --probe

Archive output and exit codes. Any tracked setting, environment, version or artifact change invalidates the freeze and requires re-approval.

## 16. Production promotion

Production promotion is allowed only when all critical gates are green and approvals are signed:

1. schedule maintenance/rollback window;
2. take a fresh production snapshot;
3. verify the same two child hashes again;
4. install Plugin first, Theme second—no rebuild/manual merge;
5. apply the separately approved configuration/redirect changes;
6. purge every documented cache layer;
7. run read-only smoke, operations health `--probe` and NAP consistency `--probe`;
8. create a new Production NAP freeze because environment is part of the evidence;
9. monitor HTTP status, PHP/proxy logs, mail, cron and key conversions.

Never run service schema-repair `--apply` in Production.

## 17. Rollback

Rollback immediately if:

- 5xx, fatal/deprecation display or upstream refusal appears;
- rendered assets are not 1.4.29/1.3.4;
- homepage/main/cards/images regress;
- a critical Health/NAP/Rank Math check fails;
- mail or appointment validation fails;
- URL map loops/chains or removes a legitimate page;
- content SHA changes unexpectedly.

Restore the proven pre-change files and database snapshot together, purge all caches, run smoke checks, and preserve failed-deployment evidence. Do not “repair forward” with manual file replacement.

## Acceptance record

| Gate | Owner | Evidence location | Result |
|---|---|---|---|
| Origin recovery/root cause | Hosting |  |  |
| Artifact hashes/integrity | Release owner |  |  |
| Restore rehearsal | Hosting |  |  |
| PHP/WP lanes | Engineering |  |  |
| Automated QA | Engineering |  |  |
| Rendered operations/NAP probes | Engineering |  |  |
| Mail/authentication | Mail owner |  |  |
| SEO/redirect map | SEO owner |  |  |
| NAP/horizon/lead/social settings | Clinic owner |  |  |
| Medical/privacy/assets | Doctor/legal owner |  |  |
| Accessibility/performance/browser/Admin RTL | QA |  |  |
| Staging freeze | Release owner |  |  |
| Production restore point/promotion | Release + hosting |  |  |
