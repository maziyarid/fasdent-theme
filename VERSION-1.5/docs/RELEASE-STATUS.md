# Fasdent Version 1.5 — Release Status

**Current decision: BLOCKED / NOT PRODUCTION-APPROVED**

## Why

The repository now contains a canonical Version 1.5 source area and controlled acceptance criteria, but GitHub cannot prove the following production facts:

- Which theme is active on the server.
- Which plugin is active on the server.
- Whether historical files remain in the live theme directory.
- Whether a complete backup exists and can be restored.
- Whether the logo and hero load at all required viewports.
- Whether CSS, RTL CSS, JavaScript, images, and fonts return HTTP 200 in production.
- Whether the browser console is clean.
- Whether caches were purged.
- Whether booking and contact submissions succeed on the target environment.
- Whether production phone, address, hours, and floating-chat channels are configured.
- Whether the canonical HTTPS host and redirects are correct.

## Release rule

Version 1.5 may be sent to the customer only after the required P0 evidence is attached to the acceptance register. A code repository, release note, or screenshot from a different environment is not a substitute for live/staging evidence.

## Evidence register

| ID | Requirement | Status | Evidence reference |
|---|---|---|---|
| P0-IDENTITY | Theme 1.5.0 and plugin 1.2.0 active | PENDING | Production admin inventory |
| P0-BACKUP | Full backup and restore evidence | PENDING | Private backup/restore report |
| P0-VISUAL | Logo, hero, header, cards, doctor block, footer | PENDING | Responsive screenshots |
| P0-ASSETS | CSS, RTL, JS, images, fonts HTTP 200 | PENDING | Network export |
| P0-RUNTIME | Console clean, no mixed content, menu works | PENDING | Browser console/video |
| P0-FORMS | Booking and contact pass synthetic test | PENDING | Staging submission evidence |
| P1-DATA | Phone, address, hours, floating-chat channels | PENDING | Settings export/screenshot |
| P1-HOST | Canonical HTTPS and redirects | PENDING | Header crawl |

Until these rows are changed to PASS with evidence, the release remains blocked.