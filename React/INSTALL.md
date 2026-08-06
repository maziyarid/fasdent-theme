# Fasdent Theme v3.2.0 — Install

## Upload
1. Upload this **entire folder** to `wp-content/themes/fasdent-theme`
   - Or zip the folder and use Appearance → Themes → Add New → Upload
2. Appearance → Themes → **Activate Fasdent Theme**
3. Settings → Permalinks → **Save Changes** (once — fixes /blog 404s)
4. Hard-refresh the site (Ctrl+Shift+R)

## Requirements
- WordPress 6.0+
- PHP 7.4+
- Pretty permalinks enabled (Post name)

## Included
- React 19 production build (`dist/assets/app.js` + `app.css`)
- Font Awesome 7.2 Pro (local webfonts)
- Irancell font (local)
- Real clinic data (دکتر کیوان علی پسندی)
- Blog via WP REST API (`/blog`, `/blog/:slug`)
- CPTs: service, doctor, testimonial, faq

## Rebuild (only if you edit src/)
```bash
cd wp-content/themes/fasdent-theme
npm install
npm run build
```
