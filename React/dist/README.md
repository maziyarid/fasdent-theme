# Production build assets — React theme

This folder contains production-ready build output for the React theme.

Preferred install:
- Use the release archive `fasdent-react-theme.zip` (includes optimized app.js + app.css).

Rebuild steps (from repo root):
1. cd React
2. npm install
3. npm run build

Notes:
- The build bundles WordPress REST integration fixes (e.g., blog post fetching).
- Font Awesome 7 Pro may be included locally depending on build configuration — ensure you have the correct licensing for production use.
- If deploying to WP, copy the built output into the theme folder and ensure `functions.php` enqueues the generated CSS/JS.
