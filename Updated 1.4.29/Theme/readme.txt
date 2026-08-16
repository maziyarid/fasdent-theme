=== Dr Keyvan Alipasandi Dental Clinic ===
Contributors: clinic-web-team
Requires at least: 6.4
Requires PHP: 8.2
Stable tag: 1.4.29
License: GPLv2 or later

Native RTL presentation theme paired with Alipasandi Service Content 1.3.4.

== Production Contract ==
* Install only the exact Theme 1.4.29 + Plugin 1.3.4 pair from the signed delivery bundle.
* The Theme owns presentation. The Plugin owns service writes, clinic settings, forms, mail, validation, migration and operational health.
* Missing/outdated Plugin: form endpoints fail closed and the Theme remains read-only.
* Local Schema ownership is explicit: disabled or Rank Math. Partial Dentist/PostalAddress entities are never emitted.
* Appointment UI records a request; it does not promise real-time availability or a confirmed booking.
* No pages or business data are fabricated during activation.

== Changelog ==
= 1.4.29 =
* Restored the complete campaign homepage using the existing design system and canonical service registry.
* Made header, mobile menu, footer, service overview and SEO identity consume the Plugin-owned service registry.
* Removed namespaced/unhooked patch code and parallel operational option keys.
* Made Home, Contact, Appointments and Footer consume the same canonical NAP/display-address contract.
* Added actual-render NAP evidence markers and fail-closed phone/form copy.
* Enforced complete-address plus strict E.164 requirements for the emergency schema fallback.
* Raised the production companion requirement to Plugin 1.3.4.

= 1.4.26 =
* Previous production candidate; superseded by this release.
