=== Alipasandi Service Content ===
Contributors: alipasandi-clinic
Requires at least: 6.4
Requires PHP: 8.2
Stable tag: 1.3.4

Business-logic owner for service content, canonical clinic settings, form processing, validation, migration, schema integration and operational health.

== Production Contract ==
1. Back up database and uploads; export service content and Rank Math settings.
2. Install the exact Plugin 1.3.4 + Theme 1.4.29 pair from the delivery bundle on Staging.
3. Configure Settings > اطلاعات کلینیک without placeholder values.
4. Run the schema-repair dry-run; apply only on Staging with the exact plan SHA-256 if a marker-only repair is offered.
5. Record final artifact hashes, create the owner-approved NAP freeze, then run the blocking health and consistency probes.
6. Promote only if every critical gate passes and rollback has been rehearsed.

No service/NAP data is deleted on deactivate/uninstall. No schema repair or service migration runs merely because an artifact is activated.

== Changelog ==
= 1.3.4 =
* Restored global WordPress hooks/APIs and canonicalized all operational option names.
* Added strict structured NAP, E.164, display-address and artifact-bound freeze contracts.
* Added Rank Math Local Schema ownership normalization plus final-graph observation for Front and Contact.
* Restored nonce/rate-limited Contact and Appointment handlers with domain-aligned mail identity.
* Added one shared booking/opening-hours parser, cross-validator, lead time, horizon and future-slot gate.
* Added actual-render NAP consistency and integrated operational-health CLI probes.
* Added signed explicit-probe evidence and a returned-HTML contract that rejects blank/stale/wrong-version homepage deployments.
* Added staging-only schema-marker repair with dry-run plan hashing and before/after content SHA-256 proof.

= 1.3.1 =
* Previous production candidate; superseded by this release.
