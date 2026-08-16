# Fasdent Theme — React Edition

This folder contains a React-powered theme variant that pairs React + Vite + Tailwind with WordPress so you can deliver an app-like frontend while still leveraging WordPress content via REST API.

Important: The React variant is an alternative front-end approach — it complements the canonical PHP UI in the repository. Use one approach at a time on a given site.

## Key features
- React 19 + TypeScript codebase
- Vite 8 for fast dev / build
- Tailwind CSS v4 styling
- RTL support for Persian content
- REST API integration for posts, menus, custom post types
- Configurable theme customizer integration

## Structure (high-level)
React/
- style.css — WordPress theme header
- functions.php — PHP bootstrapping / REST helpers
- template-react.php — server template to load React root
- header.php / footer.php / index.php — minimal theme wrappers
- inc/ — theme setup, enqueue scripts, CPTs, customizer
- assets/ — compiled assets for WordPress (CSS/JS/images)
- src/ — React source
  - components/, pages/, hooks/, contexts/, types/
  - main.tsx, App.tsx, routes.tsx, index.css
- vite.config.ts & vite.config.wordpress.ts
- package.json

## Installation (production)
1. Build the React app:
   cd React
   npm install
   npm run build:wordpress
2. Copy the built React folder or the generated `dist`/`app` files to your WordPress themes directory:
   cp -r React /path/to/wordpress/wp-content/themes/fasdent
3. Activate the theme in WP admin (Appearance → Themes).

## Development
For development with HMR:
1. cd React
2. npm install
3. npm run dev

Use FASDENT_DEV_MODE or your functions.php to route asset URLs to the dev server (example included in the repo).

## WordPress integration notes
- The theme uses the REST API to fetch menus, posts, and theme options.
- PHP templates provide minimal server-side markup and ensure WordPress hooks remain available.
- Recommended plugins: Advanced Custom Fields (ACF) for complex content models (optional).

## Custom post types & taxonomies
The React variant registers commonly used CPTs for the project:
- service, doctor, testimonial, faq
Taxonomies:
- service_category, kb_topic

## Building for production
Run:
npm run build:wordpress
This outputs a production-ready set of assets that WordPress will reference.

## Browser compatibility
Modern evergreen browsers (Chrome, Firefox, Safari, Edge — latest 2 versions) are supported.

## Contributing & license
- Fork, branch, PR, tests/lint before submitting.
- License: GNU GPL v2 or later.
