# Preflight تولید — ۱.۴.۲۳

این Checklist باید روی Staging واقعی تکمیل شود. مقدار «Evidence» را با URL، Screenshot، View-Source یا Log واقعی پر کنید؛ بسته ZIP به‌تنهایی مدرک Runtime نیست.

| Gate | Pass criteria | Evidence |
|---|---|---|
| Artifact | Theme 1.4.23 + Plugin 1.1.0 و SHA-256 مطابق RELEASE-MANIFEST | — |
| Environment | WP/PHP/Plugin inventory ثبت؛ WP_DEBUG بدون Theme error | — |
| Backup | DB قبل Migration + Full backup قبل Production + Restore test | — |
| Migration | چهار Service OK؛ fail/retry فقط Page شکست‌خورده؛ Health PASS | — |
| Theme switch | Meta باقی؛ عدم overwrite؛ برگشت Content سالم | — |
| Revisions | A/B، قبل/بعد Migration، empty، FAQ/CTA/link/image؛ Rule revision بدون Meta | — |
| Save UX | Save/reload، partial POST fail-safe، validation notice، Autosave warning | — |
| HTML/A11y | HTML valid؛ H1 واحد؛ FAQ ID/ARIA unique؛ keyboard | — |
| SEO owner | فقط Rank Math؛ Title/Meta/Canonical/OG/Schema duplicate صفر | — |
| NAP | Rank Math graph از Options مرکزی؛ Multiple Locations/تهران خاموش | — |
| Schema | Entity/@id graph منسجم؛ Geo/Postal/Hours فقط واقعی | — |
| Host | Final www/non-www مشخص؛ HTTP/alternate مستقیم 301 | — |
| Redirects | Old→Final→301، بدون chain/loop؛ retention ثبت | — |
| Indexing | Appointment noindex/follow و خارج sitemap؛ Staging protected/noindex | — |
| Sitemap | فقط canonical/indexable و host یکسان | — |
| Internal URLs | مستقیم به canonical final | — |
| Forms | valid/invalid/past/header injection/rate limit؛ ایمیل واقعی دریافت | — |
| Mail/PII | From ثابت؛ retention/access SMTP log؛ PII حداقلی | — |
| Cache | page/object/CDN invalidation پس از edit | — |
| Performance | Mobile/Desktop LCP/INP/CLS + image network | — |
| Browser/A11y | ماتریس device/browser و WCAG AA اصلی | — |
| Source control | commit/tag v1.4.23؛ changelog؛ reproducible artifact | — |
| Secrets | scan: key/password/token hard-coded صفر | — |

تا زمانی که همه Gateهای Critical مدرک واقعی ندارند، Production Approval = خیر.
