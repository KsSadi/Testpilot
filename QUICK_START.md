# 🚀 LaraKit - Quick Start Guide

Welcome to **LaraKit** - Your Modern Laravel Starter Kit!

## 📋 What You Have

LaraKit is a production-ready Laravel starter kit with:

- ✅ **Modular Architecture** - Clean, organized code with Laravel Modules
- ✅ **Role-Based Permissions** - Complete RBAC with Spatie Permission
- ✅ **Dynamic Settings System** - 50+ configurable settings
- ✅ **Multi-Authentication** - Email, Mobile OTP, Social Login (Google, Facebook, GitHub)
- ✅ **User Management** - Complete CRUD with profile & avatar support
- ✅ **Modern UI** - Beautiful admin dashboard with TailwindCSS v4
- ✅ **Landing Page & Docs** - Professional frontend with documentation

## 🎯 Quick Access

After installation, you can access:

| Page | URL | Description |
|------|-----|-------------|
| **Landing Page** | `http://localhost:8000/` | Public landing page with features showcase |
| **Documentation** | `http://localhost:8000/docs` | Complete setup and usage documentation |
| **Dashboard** | `http://localhost:8000/dashboard` | Admin dashboard (requires login) |
| **Settings** | `http://localhost:8000/settings` | Configure your application |

## 🔐 Default Credentials

```
Email: superadmin@example.com
Password: password
```

## 📁 Project Structure

```
app/
├── Modules/
│   ├── Setting/          # Settings management system
│   ├── User/             # User management & authentication
│   ├── DemoFrontend/     # Landing page & documentation
│   └── RolePermission/   # Role & permission management
├── Http/
├── Models/
└── Providers/

database/
├── migrations/           # All database migrations
└── seeders/
    ├── SettingSeeder.php        # 57 default settings
    ├── AuthSettingsSeeder.php   # Authentication settings
    └── RolePermissionSeeder.php # Roles & permissions

resources/
├── views/
│   └── layouts/
│       └── backend/      # Admin dashboard layout
└── css/
    └── app.css          # TailwindCSS styles
```

## ⚡ First Steps

### 1. Explore the Landing Page
```bash
php artisan serve
# Visit http://localhost:8000
```

### 2. Read Documentation
- Click "View Documentation" on landing page
- Or visit: http://localhost:8000/docs

### 3. Login to Dashboard
- Use default credentials above
- Explore the admin panel

### 4. Configure Settings
1. Go to **Settings** → **General**
2. Update:
   - App Name
   - Logo & Favicon
   - Contact Information
   - Footer Text

### 5. Manage Authentication
1. Go to **Settings** → **Auth & Security**
2. Enable/disable:
   - Email Login
   - Mobile OTP
   - Social Login (Google, Facebook, GitHub)

### 6. Set Up Roles & Permissions
1. Go to **User Management** → **Roles**
2. View existing roles:
   - Super Admin (all permissions)
   - Admin (most permissions)
   - Moderator (limited permissions)
   - User (basic permissions)

## 🎨 Customization

### Change Branding
```php
// Update via UI: Settings → General
App Name: Your App Name
Tagline: Your Tagline
Logo: Upload your logo
Favicon: Upload your favicon
```

### Add New Module
```bash
php artisan make:module YourModule
```

### Modify Landing Page
Edit: `app/Modules/DemoFrontend/resources/views/landing.blade.php`

### Update Documentation
Edit: `app/Modules/DemoFrontend/resources/views/docs.blade.php`

## 📚 Key Features Explained

### Settings System
```php
// Get any setting
$appName = setting('app_name');

// In Blade views
{{ setting('footer_text') }}
{{ $appName }} // Via ViewServiceProvider
```

**Manage via UI**: Settings → Select Category

### Authentication System
- **Email Auth**: Traditional email/password
- **Mobile OTP**: SMS-based login with configurable OTP
- **Social Login**: OAuth with Google, Facebook, GitHub

**Configure via UI**: Settings → Auth & Security

### Permissions System
```php
// Check permission
if (auth()->user()->can('edit-users')) {
    // Allow action
}

// In Blade
@can('view-settings')
    <a href="{{ route('settings.index') }}">Settings</a>
@endcan
```

**Manage via UI**: User Management → Roles

## 🛠️ Development Tips

### Clear Caches
```bash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

### Run Migrations
```bash
php artisan migrate --seed
```

### Build Assets
```bash
npm run build       # Production
npm run dev         # Development with watch
```

### Create New User
```bash
php artisan tinker
User::create([
    'name' => 'New User',
    'email' => 'user@example.com',
    'password' => bcrypt('password')
])->assignRole('user');
```

## 📖 Available Routes

### Frontend (Public)
- `GET /` - Landing page
- `GET /docs` - Documentation

### Dashboard (Auth Required)
- `GET /dashboard` - Dashboard home
- `GET /members` - User management
- `GET /roles` - Role management
- `GET /permissions` - Permission management
- `GET /settings` - Settings management
- `GET /profile` - User profile
- `GET /profile/edit` - Edit profile

### Settings Routes
- `POST /settings/general` - Update general settings
- `POST /settings/seo` - Update SEO settings
- `POST /settings/auth` - Update auth settings
- `POST /settings/auth-methods` - Update auth methods
- `POST /settings/email` - Update email settings
- `POST /settings/social` - Update social media
- And more...

## 🎓 Learning Resources

1. **Landing Page** - See all features showcase
2. **Documentation Page** - Complete setup guide
3. **Settings UI** - Explore all configuration options
4. **Module READMEs** - Check each module's README file

## 🐛 Troubleshooting

### Issue: Settings not showing
```bash
php artisan cache:clear
php artisan view:clear
```

### Issue: Routes not found
```bash
php artisan route:clear
php artisan route:list
```

### Issue: Assets not loading
```bash
npm install
npm run build
php artisan storage:link
```

### Issue: Database connection error
Check `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 📞 Support

- **Documentation**: http://localhost:8000/docs
- **Issues**: Check Laravel logs in `storage/logs`
- **Database**: Use tools like phpMyAdmin or TablePlus

## 🎉 Next Steps

1. ✅ Explore landing page and documentation
2. ✅ Login to dashboard with default credentials
3. ✅ Update general settings (branding, contact)
4. ✅ Configure authentication methods
5. ✅ Create your first module
6. ✅ Start building your application!

---

**Version**: 1.0.0  
**Laravel**: 11.x  
**PHP**: 8.2+  
**Created**: November 2025

**Happy Coding! 🚀**
