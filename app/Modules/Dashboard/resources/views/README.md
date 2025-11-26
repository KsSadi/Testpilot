# Dashboard Module - Using Common Backend Layout

## ✅ Correct Structure

### Common Layouts (Shared by ALL Modules)
```
resources/views/layouts/backend/
├── master.blade.php              ← Main layout (SHARED)
└── components/
    ├── header.blade.php          ← Header (SHARED)
    ├── sidebar.blade.php         ← Sidebar (SHARED)
    ├── breadcrumb.blade.php      ← Breadcrumb (SHARED)
    └── footer.blade.php          ← Footer (SHARED)
```

### Module-Specific Views (Dashboard Only)
```
app/Modules/Dashboard/resources/views/
├── index.blade.php               ← Dashboard page
├── analytics.blade.php           ← Analytics page
└── _page_template.blade.php      ← Template for new pages
```

## 📁 Old Structure (Removed)

```
app/Modules/Dashboard/
├── Http/
│   └── Controllers/
│       └── DashboardController.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── master.blade.php          # Main master layout
│       ├── components/
│       │   ├── header.blade.php          # Header component (reusable)
│       │   ├── sidebar.blade.php         # Sidebar component (reusable)
│       │   ├── breadcrumb.blade.php      # Breadcrumb component (reusable)
│       │   └── footer.blade.php          # Footer component (reusable)
│       └── index.blade.php               # Example dashboard page
└── routes/
    └── web.php

public/
└── assets/
    └── backend/
        ├── css/
        │   └── dashboard.css              # All dashboard styles
        └── js/
            └── dashboard.js               # All dashboard scripts
```

## 🎨 Design Architecture

### Master Layout (`layouts/master.blade.php`)
- Main layout structure
- Includes all components
- Loads CSS and JS files
- Supports `@yield` and `@stack` for page content and scripts

### Reusable Components

1. **Header** (`components/header.blade.php`)
   - Top navigation bar
   - Search functionality
   - Notifications dropdown
   - User menu dropdown
   - Responsive mobile menu button

2. **Sidebar** (`components/sidebar.blade.php`)
   - Left navigation menu
   - Logo section
   - Menu items with active states
   - Submenu support
   - User profile at bottom
   - Dynamic auth user data

3. **Breadcrumb** (`components/breadcrumb.blade.php`)
   - Navigation breadcrumbs
   - Current date/time display
   - Auto-generates from `$breadcrumbs` array

4. **Footer** (`components/footer.blade.php`)
   - Copyright information
   - Quick links
   - System status indicator
   - Version display

## 🚀 How to Use

### 1. Create a New Page

Create a new blade file that extends the master layout:

```php
@extends('Dashboard::layouts.master')

@section('title', 'Your Page Title')

@section('content')
    {{-- Page Title --}}
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Your Page Title</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Page description here</p>
        </div>
    </div>

    {{-- Your page content here --}}
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        <p>Your content goes here</p>
    </div>
@endsection

@push('scripts')
<script>
    // Page-specific JavaScript
    console.log('Page loaded');
</script>
@endpush

@push('styles')
<style>
    /* Page-specific CSS (if needed) */
</style>
@endpush
```

### 2. Custom Breadcrumbs

Pass breadcrumbs from controller:

```php
public function yourMethod()
{
    $data = [
        'breadcrumbs' => [
            ['title' => 'Dashboard', 'url' => url('/dashboard')],
            ['title' => 'Users', 'url' => url('/users')],
            ['title' => 'Edit User'] // Last item without URL = active
        ]
    ];

    return view('Dashboard::your-view', $data);
}
```

### 3. Hide Breadcrumb (if needed)

```php
@extends('Dashboard::layouts.master')

@section('content')
    @php
        $hideBreadcrumb = true;
    @endphp
    
    {{-- Your content --}}
@endsection
```

### 4. Add Custom Styles

Add to `public/assets/backend/css/dashboard.css` or use `@push('styles')`:

```php
@push('styles')
<style>
    .your-custom-class {
        /* Your styles */
    }
</style>
@endpush
```

### 5. Add Custom Scripts

Add to `public/assets/backend/js/dashboard.js` or use `@push('scripts')`:

```php
@push('scripts')
<script>
    // Your JavaScript code
    function yourFunction() {
        console.log('Custom function');
    }
</script>
@endpush
```

## 🎨 Customization

### Change Primary Color

Edit `public/assets/backend/css/dashboard.css`:

```css
:root {
    /* Change these colors */
    --primary-color: #06B6D4;  /* Main color */
    --primary-dark: #0891B2;   /* Darker shade */
    --primary-light: #22D3EE;  /* Lighter shade */
}
```

**Example color themes:**
- **Purple Theme**: `#8B5CF6` (primary), `#7C3AED` (dark)
- **Green Theme**: `#10B981` (primary), `#059669` (dark)
- **Orange Theme**: `#F59E0B` (primary), `#D97706` (dark)

### Modify Sidebar Menu

Edit `app/Modules/Dashboard/resources/views/components/sidebar.blade.php`:

```php
<a href="{{ url('/your-route') }}" class="sidebar-link {{ Request::is('your-route') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-600 rounded-xl text-base font-medium">
    <i class="fas fa-your-icon mr-3 text-lg w-5"></i>
    <span>Your Menu Item</span>
</a>
```

### Add Submenu

```php
<div class="sidebar-submenu">
    <button onclick="toggleSubmenu('yourMenu')" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-600 rounded-xl text-base font-medium">
        <div class="flex items-center">
            <i class="fas fa-your-icon mr-3 text-lg w-5"></i>
            <span>Your Menu</span>
        </div>
        <i class="fas fa-chevron-down text-sm transition-transform" id="yourMenuIcon"></i>
    </button>
    <div id="yourMenu" class="hidden mt-1 ml-8 space-y-1">
        <a href="{{ url('/sub-item-1') }}" class="sidebar-link flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
            <i class="fas fa-circle mr-3 text-base w-4"></i>
            <span>Sub Item 1</span>
        </a>
    </div>
</div>
```

## 📝 Available CSS Classes

### Button Classes
- `.btn-primary` - Primary button with gradient
- `.btn-secondary` - Secondary gray button
- `.btn-success` - Success green button
- `.btn-warning` - Warning orange button
- `.btn-danger` - Danger red button

### Badge Classes
- `.badge-primary` - Primary badge
- `.badge-success` - Success badge
- `.badge-warning` - Warning badge
- `.badge-danger` - Danger badge
- `.badge-info` - Info badge

### Card Classes
- `.card` - Basic card with border and shadow
- `.card-hover` - Card with hover effect
- `.stat-card` - Statistics card style

### Utility Classes
- `.primary-color` - Primary gradient background
- `.primary-bg` - Solid primary background
- `.primary-text` - Primary text color
- `.custom-scrollbar` - Custom styled scrollbar

## 📱 Responsive Features

- Mobile-friendly sidebar (slides from left)
- Hamburger menu for mobile
- Responsive grid layouts
- Collapsible search on mobile
- Touch-friendly buttons and links

## 🔧 JavaScript Functions

Available global functions in `dashboard.js`:

- `toggleSidebar()` - Toggle mobile sidebar
- `toggleNotifications()` - Toggle notifications dropdown
- `toggleUserMenu()` - Toggle user menu dropdown
- `toggleSubmenu(menuId)` - Toggle sidebar submenu
- `updateTime()` - Update current time display

## 🎯 Best Practices

1. **Always extend the master layout** for consistency
2. **Use components** - Don't recreate header/sidebar/footer
3. **Keep styles in CSS files** - Avoid inline styles
4. **Use Tailwind utility classes** for quick styling
5. **Add page-specific scripts** using `@push('scripts')`
6. **Pass dynamic data** from controller, not in views
7. **Use route names** instead of hardcoded URLs

## 🔄 Future Updates

When you need to update the design globally:

1. **Header changes**: Edit `components/header.blade.php`
2. **Sidebar changes**: Edit `components/sidebar.blade.php`
3. **Footer changes**: Edit `components/footer.blade.php`
4. **Global styles**: Edit `public/assets/backend/css/dashboard.css`
5. **Global scripts**: Edit `public/assets/backend/js/dashboard.js`

All pages using the master layout will automatically update! 🎉

## 📞 Support

For questions or issues, check:
- Laravel Blade documentation
- Tailwind CSS documentation
- Font Awesome icon library

---

**Created**: November 4, 2025
**Version**: 1.0.0
**Framework**: Laravel with Modular Structure
