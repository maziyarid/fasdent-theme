# Knowledge Base — v2.5.0

## Content model

| Item | Slug | Notes |
|------|------|-------|
| CPT | `kb_article` | Archive: `/knowledge/` |
| Taxonomy | `kb_topic` | `/knowledge/topic/{slug}/` |
| Single | `/knowledge/{slug}/` | |

## Admin fields

- Icon (FA class)
- Reading time (minutes)
- Quick answer (Featured Snippet box)
- Related service
- Key points (repeater / JSON fallback)

## How to use

1. **پیشخوان → مرکز آموزش → افزودن مقاله**
2. Assign **موضوعات آموزش** (create topics first)
3. Fill quick answer + key points
4. Publish
5. Page with template **مرکز آموزش** shows hub (topics + latest)
6. **Settings → Permalinks → Save** once after deploy

## Templates

- `page-templates/knowledge-base.php` — hub
- `archive-kb_article.php` — all articles
- `taxonomy-kb_topic.php` — by topic
- `single-kb_article.php` — article
- `template-parts/kb-card.php` — card
