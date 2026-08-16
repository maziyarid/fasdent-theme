# FasDent Public Website Diagnosis

**Target:** https://fasdent.ir/  
**Observation date:** August 15, 2026  
**Scope:** public rendered state, internal-route availability, deployed release identity, indexed URL/NAP reconnaissance, and whether a safe live repair path was available  
**Live modification performed:** **No** — the origin and WordPress Admin both returned 502, so no authenticated or reversible change surface was reachable

## Executive diagnosis

Two independent failures were observed during the same audit window:

1. The homepage initially rendered a partial Theme 1.4.26 state that reproduces the rejected source regression: a skip link without `#main-content`, an almost-empty hero/body, no service cards, no content images, no Local JSON-LD and stale hard-coded footer/location copy.
2. All subsequently checked internal routes, including `/wp-admin/`, returned `502 Bad Gateway` with `[Errno 111] Connection refused`. That is an origin/upstream availability incident and cannot be repaired by changing Theme PHP alone.

The corrected Theme 1.4.29 and Plugin 1.3.4 in this bundle close the source-level deployment regression. The hosting 502 still requires access to the server/control panel and is a hard stop before installation or production verification.

## Evidence matrix

| Surface | Observed public state | Severity | Meaning |
|---|---|---:|---|
| Homepage | Loaded once during the audit; Theme asset query reported `1.4.26` | Critical | The required 1.4.29 artifact was not the rendered release |
| Accessibility landmark | Skip link targeted `#main-content`, but no matching `main` element existed | Critical | Keyboard skip navigation was broken and the merged homepage template was absent |
| Homepage content | No content images, no service cards, large blank area | Critical | Partial/unmerged homepage deployment |
| Local data | Address-missing warning plus stale hard-coded location/doctor footer copy | Critical | Canonical Plugin-owned NAP was not controlling every visible surface |
| Structured data | No JSON-LD block detected on the loaded homepage | High | The selected Local Schema/Rank Math outcome was not demonstrable |
| Internal routes | About, Services, Contact, Appointments, FAQ and Blog returned 502 | Critical | Origin/upstream could not accept the requests |
| WordPress Admin | `/wp-admin/` returned 502 with connection refused | Critical | No safe authenticated live repair/deployment path was available |
| Indexed estate | Search results exposed both older rich content and multiple legacy/current route and NAP footprints | High | Redirect, canonical and NAP reconciliation must precede reindexing |

## Root-cause decision tree

| Branch | Evidence | Diagnosis | Confidence | Required action |
|---|---|---|---:|---|
| Wrong/partial application artifact | Public HTML referenced 1.4.26; expected homepage selectors/content were absent | A stale or partial Theme build was rendered | High | Install the exact hashed 1.4.29/1.3.4 child ZIPs after staging gates |
| Cache or mixed release nodes | Recently indexed richer content conflicted with the sparse rendered page | CDN/page cache/OPcache or multiple nodes may be serving different generations | Medium; not proven | Inventory every cache layer/node, purge after deploy, and compare returned asset versions repeatedly |
| Origin/upstream refusal | Multiple internal paths and WordPress Admin returned 502 + connection refused | Reverse proxy could not reach the WordPress/PHP upstream | High | Hosting must restore the origin, then prove loopback and scheduled events |
| URL estate drift | Indexed `/appointment/` coexists with Theme `/appointments/` and older service/pricing routes | Existing URLs have not yet been reconciled to one approved architecture | High | Export URLs, approve destinations, then implement/test an explicit redirect map |
| NAP drift | Indexed snippets and the rendered footer exposed conflicting location/contact footprints | No approved canonical NAP freeze currently governs all surfaces | High | Owner approves one NAP set; save settings; probe outputs; freeze after artifact deployment |

This is an evidence tree, not a claim that every possible branch is active. Cache/node drift remains a hypothesis until origin and cache logs prove or reject it.

## Fixes built into this delivery

- Theme and asset release identity is 1.4.29; Plugin identity is 1.3.4.
- The homepage uses `main#main-content`, the shipped `home-hero` design system, real bundled images, registry-backed service cards and the existing responsive layout.
- Home, Contact and Footer render canonical NAP markers; visible copy no longer invents a fallback location or direct phone route.
- Header, mobile menu, footer, homepage, service overview, appointment validation and service SEO identity consume the same Plugin-owned service registry.
- Local schema fails closed on partial NAP and Rank Math ownership is explicit.
- `wp alipasandi operations health --probe` now verifies the returned HTML—not only source files—including:
  - Front and Contact availability;
  - `#main-content` on both pages;
  - the `home-hero` contract;
  - registry-aligned service-card count;
  - at least two homepage images;
  - Theme CSS and JavaScript both served with `ver=1.4.29`.
- `wp alipasandi nap consistency --probe` compares actual Home, Contact, Footer, email and observed JSON-LD NAP hashes.

These rendered-output gates are designed to catch a stale cache, wrong release, blank homepage or partial deployment before a future handoff can call it complete.

## Hosting recovery gate

Before uploading the bundle, hosting must:

1. capture reverse-proxy and WordPress/PHP upstream logs for the 502 window;
2. confirm the configured upstream socket/host/port exists and is accepting connections;
3. restore the PHP-FPM/container/application service without changing WordPress data;
4. verify database connectivity, disk/inode capacity and file ownership;
5. prove `/`, `/contact/` and `/wp-admin/` return a normal WordPress response;
6. prove WordPress loopback and WP-Cron/external scheduler operation;
7. identify all CDN/page/object/OPcache layers and record the purge procedure.

Do not upload while the origin is unstable; a failed or partial upload would make artifact diagnosis ambiguous.

## URL, SEO and index reconciliation gate

The indexed estate includes a singular `/appointment/` route while this release resolves the appointment page as `/appointments/`, plus older pricing, implant and deeper service paths. No automatic redirect was added because guessing URL ownership could destroy legitimate rankings or content.

On the isolated staging clone:

1. export every published post/page URL and the Rank Math sitemap;
2. capture status, canonical, robots, H1 and intended destination for each indexed/legacy route;
3. have the owner approve keep/merge/redirect/gone decisions;
4. implement only that explicit map;
5. test no chains, loops, soft 404s or canonical conflicts;
6. preserve query-string behavior while canonicalizing UTM variants;
7. deploy the same reviewed map with the same artifact/configuration promotion.

## NAP and content approval gate

Search snippets exposed mixed location/phone footprints. They are evidence of drift, not authority for the correct clinic location. The release intentionally contains no guessed production NAP. The clinic owner must approve the official business name, display/E.164 phone, street, city, region, country, postal code, map/geo, mail identities and social URLs before the NAP freeze is created.

Pricing and medical claims in older indexed pages must be reviewed by the clinic/doctor and legal/privacy owner. Search-engine snippets are not sufficient approval.

## Final live-site disposition

| Layer | Disposition |
|---|---|
| Corrected source artifacts | Accepted as a staging candidate |
| Current public deployment | Failed diagnosis; wrong/partial rendered generation observed |
| Current hosting availability | Critical 502 hard block |
| Direct live repair | Not possible during audit because origin/Admin were unreachable |
| Next safe action | Restore origin, build isolated staging clone, then upload and test the exact child ZIPs in this bundle |
