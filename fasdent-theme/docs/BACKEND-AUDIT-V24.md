# Fasdent Theme — Backend Audit (v2.4.0)

**Project kind:** Classic PHP WordPress theme (not block theme; no theme.json).
**Target:** WP 6.5+, PHP 8.2+.

## Registered content types

| Type | Slug | Admin | Archive | Notes |
|------|------|-------|---------|-------|
| service | services/%category% | Yes | Yes | Hierarchical category |
| doctor | doctors | Yes | No | Single profile |
| testimonial | — | Yes | No | Not publicly queryable |
| faq | — | Yes | No | Knowledge base source |
| **before_after** | gallery/case | Yes | gallery | **NEW** — separate before/after images |
| ba_category | gallery/category | Yes | Yes | **NEW** taxonomy |

## Security baseline (applied)

- Nonces on all admin saves (`fasdent_ba_nonce`, booking, forms)
- `current_user_can( 'edit_post' )` before meta writes
- `absint` for attachment/post IDs
- `sanitize_text_field` / `esc_*` on all outputs
- `$wpdb->insert` with format arrays in booking
- No direct `$_POST` trust without `wp_unslash` + sanitize

## How to use Before/After (admin)

1. **پیشخوان → قبل و بعد → افزودن نمونه**
2. Upload **قبل** and **بعد** images separately (required)
3. Optional: treatment label + related service
4. Assign **دسته گالری** (e.g. ایمپلنت، لمینت)
5. Publish
6. Create/edit page → Template: **گالری قبل و بعد**
7. Front-end shows comparison sliders + category filter

## Flush permalinks

After deploy: Settings → Permalinks → Save (once).

## Next backend priorities

1. Knowledge base CPT (kb_article) with topics taxonomy — currently FAQ-only
2. Floating chat channel repeater in Customizer (add/remove + icons)
3. Page-level editable landing blocks beyond ACF page fields
