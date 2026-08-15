# Permanent SEO Fallback Regression Matrix

Source test: `tests/test-seo-fallback.php`. The suite is shipped permanently because 1.4.22 had a fatal canonical API regression. It covers published page, published post, Home, Search, 404, Archive, Feed and active/inactive/reactivated SEO-owner behavior. Theme fallback is read-only and suppressed when an SEO plugin owns metadata.

**Build workspace status:** test file syntax can be linted here; WordPress PHPUnit execution remains a CI/Staging gate because no WP test harness is present in this workspace.


Release 1.4.28 test contract علاوه بر Fatal/Canonical شامل Appointment inactive → `noindex,follow`، Search → noindex, 404 → noindex/nofollow + real 404 context، Blog archive طبق Matrix، exact Feed header contract، Core Sitemap disabled during SEO-owner failure، و Active→Inactive→Reactivated deterministic policy است. Theme emergency robots/sitemap guard فقط وقتی Rank Math owner absent باشد فعال است.
