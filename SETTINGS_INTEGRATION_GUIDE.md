# Settings Integration Guide

## Overview
The settings system has been successfully integrated across the entire application. All general settings (logo, app name, footer, etc.) are now dynamically loaded from the database and cached for performance.

## Features Implemented

### 1. View Service Provider
**File:** `app/Providers/ViewServiceProvider.php`
- Automatically shares settings with all views
- Provides easy access to settings through variables
- Caches settings for 24 hours

**Available Variables in All Views:**
- `$generalSettings` - All general settings
- `$seoSettings` - All SEO settings
- `$authSettings` - All auth settings
- `$emailSettings` - All email settings
- `$socialSettings` - All social settings
- `$notificationSettings` - All notification settings
- `$backupSettings` - All backup settings
- `$developerSettings` - All developer settings
- `$appName` - Application name
- `$appTagline` - Application tagline
- `$appLogo` - Logo path
- `$appFavicon` - Favicon path
- `$footerText` - Footer text
- `$copyrightText` - Copyright text

### 2. Helper Functions
**File:** `app/helpers.php`

Available helper functions:
```php
// Get a setting value
setting('key', 'default')

// Get all settings in a group
settings_group('general')

// Get application name
app_name()

// Get logo URL
app_logo()

// Get favicon URL
app_favicon()
```

### 3. Layout Integration

#### Master Layout (`resources/views/layouts/backend/master.blade.php`)
- ✅ Dynamic page title using `$appName`
- ✅ Favicon integration
- ✅ SEO meta tags from settings

#### Sidebar (`resources/views/layouts/backend/components/sidebar.blade.php`)
- ✅ Logo display (image or icon fallback)
- ✅ Application name from settings
- ✅ Tagline from settings
- ✅ Responsive design with truncation

#### Footer (`resources/views/layouts/backend/components/footer.blade.php`)
- ✅ Copyright text from settings
- ✅ Footer text from settings
- ✅ Dynamic year and app name

## Usage Examples

### In Blade Templates
```blade
{{-- Using variables --}}
<h1>{{ $appName }}</h1>
<p>{{ $appTagline }}</p>

{{-- Using helper functions --}}
<img src="{{ app_logo() }}" alt="{{ app_name() }}">

{{-- Using setting() helper --}}
<p>Contact: {{ setting('contact_email') }}</p>

{{-- With default value --}}
<p>Phone: {{ setting('contact_phone', 'N/A') }}</p>
```

### In Controllers
```php
use App\Modules\Setting\Models\Setting;

// Get single setting
$appName = Setting::get('app_name');

// Get setting with default
$timezone = Setting::get('timezone', 'UTC');

// Get all settings in a group
$generalSettings = Setting::getByGroup('general');

// Set a setting
Setting::set('app_name', 'My App', 'string', 'general');

// Using helper function
$appName = setting('app_name');
```

### In Config Files
```php
// config/app.php
'name' => env('APP_NAME', setting('app_name', 'Laravel')),
```

## Settings Management

### Access Settings Page
```
URL: http://127.0.0.1:8000/settings
Permission: view-settings
```

### Test Settings Integration
```
URL: http://127.0.0.1:8000/settings-test
```
This page displays all loaded settings and verifies integration.

### Clear Settings Cache
1. Via Settings Page: Click "Clear Cache" button
2. Via Artisan: `php artisan cache:clear`
3. Via Route: POST to `/settings/cache/clear`

## Cache System

- **Duration:** 24 hours
- **Auto-refresh:** Cache automatically clears when settings are updated
- **Manual Clear:** Available via settings page or artisan command

## File Structure

```
app/
├── Providers/
│   └── ViewServiceProvider.php     # Shares settings with views
├── helpers.php                      # Helper functions
└── Modules/
    └── Setting/
        ├── Models/
        │   └── Setting.php          # Core setting model with caching
        ├── Http/
        │   └── Controllers/
        │       └── SettingController.php
        └── resources/
            └── views/
                ├── index.blade.php  # Settings management page
                └── tabs/
                    └── general.blade.php

resources/views/
├── layouts/
│   └── backend/
│       ├── master.blade.php         # Updated with settings
│       └── components/
│           ├── sidebar.blade.php    # Logo & app name
│           ├── footer.blade.php     # Footer & copyright
│           └── header.blade.php
└── settings-test.blade.php          # Test page

bootstrap/
└── providers.php                    # ViewServiceProvider registered

composer.json                        # helpers.php autoloaded
```

## What Works Now

✅ **Application Name** - Displays from database in sidebar and header
✅ **Logo** - Shows uploaded logo in sidebar (with fallback icon)
✅ **Favicon** - Browser tab icon from settings
✅ **Tagline** - Subtitle under app name
✅ **Footer Text** - Custom footer message
✅ **Copyright** - Dynamic copyright text
✅ **Contact Info** - Email, phone, address from settings
✅ **SEO Meta Tags** - Description, keywords, author
✅ **All Settings** - Available via helper functions everywhere

## Testing

1. Go to Settings page: `http://127.0.0.1:8000/settings`
2. Update general settings (name, logo, footer, etc.)
3. Check sidebar - Should show updated name/logo
4. Check footer - Should show updated copyright
5. Visit test page: `http://127.0.0.1:8000/settings-test`
6. Verify all settings are displayed correctly

## Performance

- Settings are cached for 24 hours
- Only one database query per 24 hours
- Cache automatically clears on setting updates
- No performance impact on page loads

## Next Steps

1. Update other pages to use settings
2. Add more settings as needed
3. Create settings backup/restore feature
4. Add settings import/export functionality

## Troubleshooting

**Settings not showing?**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
composer dump-autoload
```

**Logo/Favicon not displaying?**
- Ensure files are uploaded via settings page
- Check storage link: `php artisan storage:link`
- Verify file permissions

**Helper functions not working?**
```bash
composer dump-autoload
```

## Support

For issues or questions, check:
1. Settings cache is enabled
2. ViewServiceProvider is registered
3. helpers.php is loaded in composer.json
4. Storage link exists
