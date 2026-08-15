# Indexability Matrix — Release 1.4.26

Owner اجرایی: **Rank Math** (Host normalization = Server). این جدول Contract است؛ Staging crawl باید implementation را اثبات کند.

| URL type | Robots | Canonical | Sitemap | Notes |
|---|---|---|---|---|
| Home / About / Contact / FAQ / Services Hub / 4 Services / Privacy / approved Posts | index,follow | self | yes | 200 |
| Appointment request | noindex,follow | self | no | conversion/request page |
| Blog archive | noindex,follow | self | no | تا حداقل 3 مقاله reviewed؛ تغییر فقط SEO release |
| Category / Tag / Author / Date / Search | noindex,follow | self | no | Search UI functional |
| Pagination | noindex,follow | self | no | no canonical-to-page-1 shortcut |
| RSS/Atom Feeds | noindex,follow via HTTP header | none | no | functional feed |
| Attachment with published parent | redirect | parent | no | 301 to parent |
| Attachment without parent | no attachment page index | media file policy | no | verify Rank Math/orphan media behavior |
| UTM/tracking variant | indexable response | clean URL | no duplicate | canonical strips tracking params |
| 404 | noindex | none | no | 404 status |
| Staging | noindex + access restriction | none | no | Basic Auth/401/403 preferred; robots alone insufficient |

KML/Local Sitemap remains disabled until its NAP source is proven identical to the Plugin SSOT. Four-host HTTP/HTTPS + www/non-www redirect chain and trailing-slash/query behavior require server evidence.
