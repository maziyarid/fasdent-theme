# Requirement Register — Fasdent Theme 1.5.0

This register is the only authoritative list of what “done” means.
New AI findings must be classified against this table before any code change is requested.

| ID     | Requirement                                      | Type            | Status   | Evidence required                          | Owner          |
|--------|--------------------------------------------------|-----------------|----------|--------------------------------------------|----------------|
| R-001  | Theme loads without PHP fatal errors             | Code            | Pass     | PHP lint + WP activation log               | Developer      |
| R-002  | Plugin loads and activates cleanly               | Code            | Pass     | Plugin activation + health-check           | Developer      |
| R-003  | Logo (brand text + tooth icon) visible           | UI              | Pending  | Live screenshot desktop + mobile           | Client / Host  |
| R-004  | Hero image loads at 375 / 768 / 1024 / 1440 px   | UI / Assets     | Pending  | Network panel + screenshots                 | Client / Host  |
| R-005  | theme.css + rtl.css return 200, correct MIME     | Assets          | Pending  | Network panel                               | Client / Host  |
| R-006  | No mixed-content warnings                        | Infrastructure  | Pending  | Browser console                            | Host           |
| R-007  | Mobile navigation opens/closes, Escape works     | Functional      | Pending  | Manual test + video or screenshot          | Client         |
| R-008  | Booking form submits and creates one record      | Functional      | Pass*    | Staging test log (*re-verify on prod)      | Developer      |
| R-009  | Contact form works, honeypot + rate-limit        | Functional      | Pass*    | Staging test log                           | Developer      |
| R-010  | NAP / phone / address consistent                 | Data / SEO      | Pending  | Customizer export + schema check           | Client         |
| R-011  | Floating chat channels have production values    | Config          | Pending  | Customizer / DB values                     | Client         |
| R-012  | HTTP → HTTPS redirect + single canonical host    | Infrastructure  | Pending  | Header crawl                               | Host           |
| R-013  | Cache purged after deploy                        | Operations      | Pending  | Cache purge confirmation                   | Host           |
| R-014  | No horizontal overflow on key breakpoints        | UI              | Pending  | Device / DevTools screenshots              | Client         |
| R-015  | Persian fonts load (no 404 / fallback reflow)    | Assets          | Pending  | Network panel                               | Client / Host  |
| R-016  | Accessibility: skip link, focus, ARIA on menu    | a11y            | Pass     | Code inspection + keyboard test            | Developer      |
| R-017  | Production backup taken before deploy             | Operations      | Pending  | Backup file / provider confirmation        | Host / Client  |
| R-018  | Active theme & plugin versions recorded          | Governance      | Pending  | Screenshot of Appearance → Themes          | Client         |

*Staging evidence exists from earlier audits; production re-verification is still required.

## Classification Rules for New AI Comments

1. **Valid blocker** → add row, fix, evidence
2. **Environment / hosting** → mark Pending, assign to Host/Client
3. **Preference / alternative design** → request explicit client decision
4. **Duplicate** → link to existing ID
5. **Out of scope / future enhancement** → defer with reason
6. **Incorrect claim** → respond with code or test evidence

No new work is started until the finding is entered in this register and classified.
