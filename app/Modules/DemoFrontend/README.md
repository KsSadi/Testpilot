# DemoFrontend Module

## Overview
The DemoFrontend module provides a professional landing page and comprehensive documentation for LaraKit. It showcases all features and provides setup guides for developers.

## Features

### Landing Page (`/`)
- **Hero Section**: Dynamic content using settings (app name, tagline)
- **Stats Display**: Showcases key metrics (5+ modules, 50+ settings, etc.)
- **Features Grid**: 8 core features with icons and descriptions
- **Auth Methods**: Displays enabled/disabled authentication methods from settings
- **Tech Stack**: Shows technologies used (Laravel, PHP, Tailwind, MySQL, etc.)
- **CTA Section**: Call-to-action buttons for docs and dashboard

### Documentation Page (`/docs`)
- **Installation Guide**: Step-by-step setup instructions
- **Settings System**: How to use the settings management system
- **Authentication**: Multi-auth system documentation
- **Roles & Permissions**: RBAC implementation guide
- **Module System**: Creating and managing modules
- **Helper Functions**: Available global helpers
- **Quick Start Checklist**: Interactive checklist for new users

## Routes

```php
GET  /       Landing page (landing.index)
GET  /docs   Documentation (landing.docs)
```

## Controllers

### LandingController
- `index()` - Display landing page with features and stats
- `docs()` - Display documentation with all sections

## Views

### Layout
- `layouts/frontend.blade.php` - Main frontend layout with:
  - Dynamic navigation with settings integration
  - Mobile-responsive menu
  - Footer with social links and settings
  - SEO meta tags
  - Open Graph tags

### Pages
- `landing.blade.php` - Landing page with hero, features, auth methods, tech stack
- `docs.blade.php` - Documentation with sidebar navigation and sections

## Settings Integration

The module fully integrates with LaraKit's settings system:

### Used Settings
- `app_name` - Application name
- `app_tagline` - Application tagline
- `app_logo` - Logo image
- `app_favicon` - Favicon
- `app_description` - Application description
- `contact_email` - Contact email
- `contact_phone` - Contact phone
- `address` - Physical address
- `footer_text` - Footer text
- `copyright_text` - Copyright notice
- `meta_title` - SEO title
- `meta_description` - SEO description
- `meta_keywords` - SEO keywords
- `og_title` - Open Graph title
- `og_description` - Open Graph description
- `og_image` - Open Graph image
- Social media URLs (facebook_url, twitter_url, linkedin_url, github_url)

### Authentication Settings
- `email_login_enabled` - Email auth status
- `mobile_login_enabled` - Mobile OTP status
- `google_login_enabled` - Google OAuth status
- `facebook_login_enabled` - Facebook OAuth status
- `github_login_enabled` - GitHub OAuth status

## Customization

### Changing Content
All content can be customized by editing the views:
- Update feature list in `LandingController::index()`
- Modify documentation sections in `docs.blade.php`
- Change colors in layout styles (cyan/blue theme)

### Adding Sections
To add new documentation sections:
1. Add section to `$sections` array in `LandingController::docs()`
2. Add section HTML in `docs.blade.php`
3. Update sidebar navigation

### Theme Colors
Current theme uses cyan/blue gradient:
- Primary: `#06b6d4` (cyan-500)
- Secondary: `#3b82f6` (blue-500)
- Accent: `#8b5cf6` (purple-500)

## Dependencies

- Laravel 11
- TailwindCSS (via Vite)
- Font Awesome 6.4.0
- Inter font (Google Fonts)

## Usage

### For End Users
1. Visit `/` for landing page
2. Click "View Documentation" for setup guides
3. Click "Try Dashboard" to access admin panel

### For Developers
```php
// Get landing page
Route::get('/', [LandingController::class, 'index']);

// Access settings in views
{{ $appName }}
{{ setting('app_tagline') }}

// Check auth methods
AuthSetting::getBool('email_login_enabled')
```

## SEO Optimized

- Dynamic meta tags from settings
- Open Graph integration
- Semantic HTML structure
- Mobile-responsive design
- Fast loading with optimized assets

## Accessibility

- ARIA labels on interactive elements
- Keyboard navigation support
- Screen reader friendly
- Color contrast ratios meet WCAG standards

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Future Enhancements

- [ ] Add blog/news section
- [ ] Implement search functionality
- [ ] Add video tutorials
- [ ] Create API documentation
- [ ] Add changelog page
- [ ] Multi-language support
- [ ] Dark mode toggle

---

**Module Version**: 1.0.0  
**Created**: November 2025  
**Author**: LaraKit Team
