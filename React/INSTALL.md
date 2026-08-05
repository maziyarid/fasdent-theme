# Fasdent React Theme v3.1.0 — Ready to Install

## Install
1. Upload/unzip this folder to `wp-content/themes/fasdent-theme` (or any name)
2. Appearance → Themes → Activate **Fasdent Theme**
3. Settings → Permalinks → Save (flush rewrites — fixes blog 404s)
4. Clear browser + server cache

## What is included
- React 19 + Vite production build (`dist/assets/app.js`, `app.css`)
- Font Awesome 7.2 Pro (local, no CDN)
- Irancell font (local, no Google Fonts)
- Real clinic data (دکتر کیوان علی پسندی, نوشهر address, phone, map, brands)
- Blog routes `/blog` and `/blog/:slug` via WP REST API
- Custom post types: service, doctor, testimonial, faq
- Emojis replaced with FA Pro icons
- All prior fatal errors fixed (module script, container #root, undefined constants)

## Rebuild (optional, only if you edit src/)
```bash
cd wp-content/themes/fasdent-theme
npm install
npm run build
```
