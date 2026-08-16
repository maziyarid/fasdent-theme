# Indexability Matrix قطعی — Theme 1.4.24 / Plugin 1.2.0

SEO Owner Production = Rank Math. Final host candidate = `https://fasdent.ir/` و trailing-slash = WordPress permalink policy؛ Freeze پس از Header crawl واقعی.

| نوع URL | Launch status | Canonical / رفتار |
|---|---|---|
| Home، About، Contact، FAQ، Services Hub `/services/`، چهار Service | Index | Self canonical؛ یک URL در Sitemap |
| Single Post تأییدشده | Index | Self canonical |
| Privacy Policy | Index | Self canonical؛ متن منطبق با فرم/SMTP/Analytics/Clarity |
| Appointment | Noindex, follow | Self canonical؛ Intent مستقل |
| Blog archive | Noindex, follow | Self canonical تا حداقل ۳ مقاله تأییدشده؛ تغییر فقط با SEO Release |
| Category/Tag/Author/Date archive | Noindex, follow | Self canonical؛ Launch policy قطعی |
| Search | Noindex, follow | UI فعال؛ در robots block نشود |
| Blog/category pagination | Noindex, follow | Self canonical هر صفحه؛ خارج Sitemap |
| Feed | خارج index/Sitemap | Functional؛ `X-Robots-Tag: noindex` در Server/SEO layer |
| Media attachment | 301 مستقیم به Parent منتشرشده؛ بدون Parent به Media file | Attachment page با HTTP 200 تولید نشود |
| UTM/tracking parameters | همان صفحه | Canonical به URL پاک |
| Staging | Noindex + Basic Auth/access restriction | robots.txt فقط مکمل |

KML/Local Sitemap = Disabled تا NAP/Geo SSOT evidence. Rank Math inactive = Site Health Critical و Core Sitemap/تمام ردیف‌ها نیازمند regression QA.
