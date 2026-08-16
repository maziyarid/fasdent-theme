# Functionality Ownership Matrix — ۱.۴.۲۲ / ۱.۱.۰

| Functionality | Owner | توضیح |
|---|---|---|
| Service Content Storage | Site Plugin / WordPress DB | `_alipasandi_service` در postmeta |
| Service Meta UI | Site Plugin | Structured bounded rows |
| Service Migration/Dry-run | Site Plugin | Tools + WP-CLI؛ explicit |
| Service Health/Site Health | Site Plugin | JSON + exit code |
| NAP Storage | Site Plugin / WordPress DB | Options مستقل از Theme |
| NAP Admin UI | Site Plugin | Settings → اطلاعات کلینیک |
| Appointment Form UI | Theme | فقط markup/presentation |
| Appointment Processing | Site Plugin | validation/security/mail result |
| Contact Form UI | Theme | فقط markup/presentation |
| Contact Processing | Site Plugin | validation/security/mail result |
| Mail Sending Logic | Site Plugin + WordPress | `wp_mail`; From توسط SMTP/domain config |
| Rate Limiting | Site Plugin؛ Edge/WAF در Abuse | transient سبک؛ proxy filter کنترل‌شده |
| SMTP/SPF/DKIM/DMARC | SMTP Plugin + Hosting/DNS | خارج از Theme/Site Plugin |
| Rank Math NAP Integration | Site Plugin | guarded `rank_math/json_ld` |
| SEO Metadata/Canonical/Sitemap | Rank Math | Owner واحد؛ Theme fallback وقتی غیرفعال |
| Schema | Rank Math | Theme fallback فقط بدون SEO plugin |
| KML/Local Sitemap | Rank Math PRO/Config | پیش‌فرض Freeze: Disabled تا SSOT Evidence |
| Redirects | Server + Rank Math | Host normalization=Server؛ page redirects=Rank Math |
| Analytics/Consent | Other Plugin/GTM + Owner | Theme hard-code ندارد |
| Cache purge | Cache Plugin/Hosting/CDN | Core object cache در Code؛ stack QA خارجی |
| Presentation/Layout | Theme | Design System Locked |

Business Logic حیاتی Service/NAP/Form/Local integration به Theme وابسته نیست.
