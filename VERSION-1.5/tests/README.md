# Version 1.5 unit tests

PHPUnit suite for the canonical `VERSION-1.5/Plugin` and `VERSION-1.5/Theme` PHP modules.

No database, `wp-config.php` or WordPress test-suite install is needed: real WordPress
sanitization, escaping and hook code is loaded from a WordPress tarball, while the stateful
layer WordPress normally backs with MySQL (options, transients, post meta, mail, nonces,
template context) is served in memory by `wp-environment.php`.

## Setup

```bash
cd VERSION-1.5/tests
composer install
composer install-wp-core        # downloads WordPress core into .wp-core (WP_VERSION overrides)
```

Point `WP_CORE_DIR` at an existing WordPress checkout to skip the download.

## Running

```bash
composer test                   # or: vendor/bin/phpunit
vendor/bin/phpunit --testsuite plugin
```

Line coverage needs a coverage driver. With PCOV, the source directory has to be declared
because the harness runs from `tests/`:

```bash
php -d pcov.enabled=1 -d pcov.directory=.. vendor/bin/phpunit --coverage-text
```

## Layout

| Path | Covers |
| --- | --- |
| `plugin/FormsTest.php` | `Plugin/includes/forms.php` — nonce/honeypot checks, per-IP quotas, choice lists, mail body, contact and appointment handlers |
| `plugin/ServiceContentTest.php` | `Plugin/includes/service-content.php` — registry, sanitizer, read precedence, migration and dry run |
| `plugin/ServiceAdminTest.php` | `Plugin/includes/service-content.php` — meta-box save guards, admin notices, health report, export payload, Site Health tests |
| `plugin/SiteSettingsTest.php` | `Plugin/includes/site-settings.php` — NAP settings, recipients, digit normalization, Rank Math JSON-LD |
| `plugin/CompatibilityTest.php` | `Plugin/includes/compatibility.php` — theme support matrix and notice |
| `theme/SeoTest.php` | `Theme/inc/seo.php` — titles, canonicals, social meta, breadcrumbs, schema graph, SEO field saving |
| `theme/IconsTest.php` | `Theme/inc/icons.php` — icon contract and size clamping |
| `theme/ImagesTest.php` | `Theme/inc/images.php` — bundled responsive image markup |
| `theme/ServiceDataTest.php` | `Theme/inc/service-data.php` — legacy fallback content shape |

`Theme/inc/service-meta.php` and `Theme/inc/forms.php` are the pre-plugin copies of the plugin
modules and declare the same function names, so they cannot be loaded in the same process as the
plugin; the plugin owns that behaviour in 1.5 and is what the suite tests.
