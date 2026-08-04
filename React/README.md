# Fasdent Theme - React Version

## Overview

This is the React-based version of the Fasdent WordPress theme. It combines the power of React, Vite, and Tailwind CSS to create a modern, fast, and maintainable WordPress theme.

## Features

- ✅ **React 19** with TypeScript
- ✅ **Vite 8** for fast builds and HMR
- ✅ **Tailwind CSS v4** for styling
- ✅ **RTL Support** for Persian content
- ✅ **WordPress Integration** with REST API
- ✅ **Responsive Design** for all devices
- ✅ **Modern UI Components**
- ✅ **Performance Optimized**

## Theme Structure

```
React/
├── style.css              # WordPress theme header
├── functions.php          # WordPress theme functions
├── index.php              # Main template
├── template-react.php     # React template loader
├── header.php             # Header template
├── footer.php             # Footer template
├── front-page.php         # Front page template
├── single.php             # Single post template
├── page.php               # Page template
├── archive.php            # Archive template
├── search.php             # Search template
├── 404.php                # 404 template
├── comments.php           # Comments template
├── inc/                   # WordPress includes
│   ├── setup.php          # Theme setup
│   ├── enqueue.php        # Asset loading
│   ├── post-types.php     # Custom post types
│   ├── taxonomies.php     # Custom taxonomies
│   ├── customizer.php     # Theme customizer
│   └── ...
├── template-parts/        # Template parts
├── page-templates/        # Page templates
├── assets/                # Static assets
│   ├── css/               # CSS files
│   ├── js/                # JavaScript files
│   └── images/            # Images
├── languages/             # Translation files
├── data/                  # Demo data
├── src/                   # React source
│   ├── components/        # React components
│   ├── pages/             # Page components
│   ├── hooks/             # Custom hooks
│   ├── contexts/          # React contexts
│   ├── types/             # TypeScript types
│   ├── App.tsx            # Main app component
│   ├── main.tsx           # Entry point
│   ├── routes.tsx         # Router configuration
│   └── index.css          # Global styles
├── vite.config.ts         # Vite configuration (Figma Make)
├── vite.config.wordpress.ts # WordPress-specific Vite config
├── package.json           # Dependencies
└── README.md              # This file
```

## Installation

### As a WordPress Theme

1. **Build the React app:**
   ```bash
   cd React
   npm install
   npm run build:wordpress
   ```

2. **Copy the React folder to your WordPress themes directory:**
   ```bash
   cp -r React /path/to/wordpress/wp-content/themes/fasdent
   ```

3. **Activate the theme in WordPress admin:**
   - Go to Appearance > Themes
   - Activate "Fasdent Theme"

### Development Mode

For development, you can run the React app with hot reloading:

```bash
cd React
npm install
npm run dev
```

Then configure WordPress to use the development server by adding to `wp-config.php`:

```php
define('FASDENT_DEV_MODE', true);
```

## Configuration

### Theme Customizer

The theme supports WordPress Customizer for:
- Phone number
- Booking URL
- Social media links
- Colors and branding
- Menu configurations

### Required Plugins

- **Advanced Custom Fields (ACF)** - For custom fields
- **WP REST API** - Already included in WordPress core

### Custom Post Types

The theme registers the following custom post types:
- `service` - Dental services
- `doctor` - Doctors/team members
- `testimonial` - Patient testimonials
- `faq` - Frequently asked questions

### Custom Taxonomies

- `service_category` - Service categories
- `kb_topic` - Knowledge base topics

## Building for Production

```bash
npm run build:wordpress
```

This will create a `dist` folder with optimized assets that WordPress will load.

## WordPress Integration

The theme provides:

1. **React Data Context** - Access WordPress data in React components
2. **REST API Integration** - Fetch posts, pages, and other content
3. **Menu Integration** - Use WordPress menus in React
4. **Customizer Integration** - Access theme options

### Using WordPress Data in React

```tsx
import { useWordPress } from './contexts/WordPressContext'

function MyComponent() {
  const { data, isLoaded } = useWordPress()
  
  if (!isLoaded) return <div>Loading...</div>
  
  return (
    <div>
      <h1>{data.site.name}</h1>
      <p>Phone: {data.phone}</p>
    </div>
  )
}
```

### Fetching Posts

```tsx
import { usePosts } from './hooks/useWordPressApi'

function BlogPosts() {
  const { data: posts, loading, error } = usePosts({ per_page: 10 })
  
  if (loading) return <div>Loading...</div>
  if (error) return <div>Error: {error.message}</div>
  
  return (
    <div>
      {posts?.map(post => (
        <article key={post.id}>
          <h2 dangerouslySetInnerHTML={{ __html: post.title.rendered }} />
          <div dangerouslySetInnerHTML={{ __html: post.content.rendered }} />
        </article>
      ))}
    </div>
  )
}
```

## Browser Support

- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and linting
5. Submit a pull request

## License

GNU General Public License v2 or later

## Version History

- **v3.0.0** - React-based theme with Vite + Tailwind CSS
- **v2.6.0** - Previous PHP-based version

## Support

For support, please contact the Fasdent development team.
