# Authentication Settings Management Guide

## Overview

Your Laravel application now has **two separate authentication configuration systems**:

### 1. **Basic Auth Settings** (in `settings` table)
Located in: Settings → Auth Tab (bottom section)
- Basic registration options
- Password requirements
- Session security
- 2FA settings

### 2. **Authentication Methods** (in `auth_settings` table)
Located in: Settings → Auth Tab (top section)
- Email authentication
- Mobile OTP authentication  
- Social login providers (Google, Facebook, GitHub)
- Advanced authentication options

---

## Database Structure

### `auth_settings` Table
Stores all authentication method configurations:

| Column | Type | Description |
|--------|------|-------------|
| `key` | string | Unique setting key |
| `value` | text | Setting value |
| `is_active` | boolean | Enable/disable setting |
| `group` | string | Category (general, email, mobile, social) |
| `description` | text | Setting description |

### `auth_providers` Table
Stores user's social login connections:

| Column | Type | Description |
|--------|------|-------------|
| `user_id` | bigint | User ID (foreign key) |
| `provider` | string | Provider name (google, facebook, github) |
| `provider_id` | string | Provider's user ID |
| `provider_token` | text | OAuth access token |
| `provider_refresh_token` | text | OAuth refresh token |

---

## Managing Authentication Settings

### Via Web Interface

1. **Navigate to Settings**
   - Login to admin panel
   - Go to Settings (sidebar)
   - Click on "Auth & Security" tab

2. **Authentication Methods Section** (Top Form)
   
   **Email Authentication:**
   - ✅ Enable Email Login
   - ✅ Enable Email Registration
   - ✅ Require Email Verification

   **Mobile OTP Authentication:**
   - ✅ Enable Mobile OTP Login
   - ✅ Enable Mobile Registration
   - ✅ Require Mobile Verification
   - Set OTP Length (4 or 6 digits)
   - Set OTP Expiry (1-30 minutes)
   - Set Resend Cooldown (30-300 seconds)

   **Social Authentication:**
   - ✅ Enable Social Login (master switch)
   - ✅ Enable Google Login
   - ✅ Enable Facebook Login
   - ✅ Enable GitHub Login

   **General Settings:**
   - ✅ Allow User Registration
   - Set Default User Role (slug)

3. **Basic Security Section** (Bottom Form)
   - Registration settings
   - Password requirements
   - Session lifetime
   - Login attempts & lockout

### Via Code

#### Get Authentication Setting Value:
```php
use App\Modules\User\Models\AuthSetting;

// Get boolean value
$emailEnabled = AuthSetting::getBool('email_login_enabled', true);
$otpEnabled = AuthSetting::getBool('mobile_login_enabled', false);

// Get string value
$otpLength = AuthSetting::get('otp_length', '6');
$defaultRole = AuthSetting::get('default_user_role', 'user');
```

#### Update Authentication Setting:
```php
use App\Modules\User\Models\AuthSetting;

AuthSetting::updateOrCreate(
    ['key' => 'email_login_enabled'],
    [
        'value' => '1',
        'is_active' => true,
        'group' => 'email',
        'description' => 'Enable email-based login'
    ]
);
```

#### Check User's Social Providers:
```php
use App\Modules\User\Models\AuthProvider;

// Get all social logins for a user
$providers = AuthProvider::where('user_id', auth()->id())->get();

// Check if user has Google connected
$hasGoogle = AuthProvider::where('user_id', auth()->id())
    ->where('provider', 'google')
    ->exists();

// Disconnect a provider
AuthProvider::where('user_id', auth()->id())
    ->where('provider', 'facebook')
    ->delete();
```

---

## Available Authentication Settings

### Email Authentication Group
```php
'email_login_enabled'         // Enable email-based login
'email_registration_enabled'  // Enable email-based registration
'email_verification_required' // Require email verification
```

### Mobile/OTP Authentication Group
```php
'mobile_login_enabled'          // Enable mobile OTP login
'mobile_registration_enabled'   // Enable mobile registration
'mobile_verification_required'  // Require mobile verification
'otp_length'                    // OTP code length (4 or 6)
'otp_expiry_minutes'           // OTP expiry time
'otp_resend_cooldown_seconds'  // Cooldown before resend
```

### Social Authentication Group
```php
'social_login_enabled'    // Master switch for social login
'google_login_enabled'    // Enable Google OAuth
'facebook_login_enabled'  // Enable Facebook OAuth
'github_login_enabled'    // Enable GitHub OAuth
```

### General Group
```php
'allow_registration'   // Allow new user registration
'default_user_role'    // Default role slug for new users
```

---

## Seeding Default Settings

The `AuthSettingsSeeder` provides default values for all authentication settings:

```bash
# Run the seeder
php artisan db:seed --class=AuthSettingsSeeder

# Or include in DatabaseSeeder
php artisan db:seed
```

**Default Configuration:**
- ✅ Email Login: Enabled
- ✅ Email Registration: Enabled
- ❌ Email Verification: Disabled
- ❌ Mobile Login: Disabled
- ❌ Social Login: Disabled
- ✅ User Registration: Enabled
- Default Role: `user`
- OTP Length: 6 digits
- OTP Expiry: 5 minutes
- Resend Cooldown: 60 seconds

---

## Caching

Authentication settings are **automatically cached** for 1 hour (3600 seconds) to improve performance.

### Clear Cache:
```bash
# Clear all cache
php artisan cache:clear

# Or via Settings page
# Settings → Developer Tab → Clear Cache button
```

---

## Routes

### Settings Management:
```
GET  /settings                    → View settings page
POST /settings/auth-methods       → Update authentication methods
POST /settings/auth               → Update basic security settings
```

### Profile & Social Accounts:
```
GET  /profile                     → View profile
GET  /profile/social-accounts     → Manage connected accounts (future)
POST /profile/connect/{provider}  → Connect social account (future)
DELETE /profile/disconnect/{provider} → Disconnect social account (future)
```

---

## Implementation Examples

### Example 1: Check if Email Login is Enabled
```php
use App\Modules\User\Models\AuthSetting;

if (AuthSetting::getBool('email_login_enabled', true)) {
    // Show email login form
} else {
    // Hide email login form
}
```

### Example 2: Validate OTP Configuration
```php
$otpLength = AuthSetting::get('otp_length', '6');
$otpExpiry = AuthSetting::get('otp_expiry_minutes', '5');

// Generate OTP
$otp = rand(
    pow(10, $otpLength - 1), 
    pow(10, $otpLength) - 1
);

// Set expiry
$expiresAt = now()->addMinutes($otpExpiry);
```

### Example 3: Check Available Social Providers
```php
$socialEnabled = AuthSetting::getBool('social_login_enabled', false);

if ($socialEnabled) {
    $providers = [];
    
    if (AuthSetting::getBool('google_login_enabled')) {
        $providers[] = 'google';
    }
    if (AuthSetting::getBool('facebook_login_enabled')) {
        $providers[] = 'facebook';
    }
    if (AuthSetting::getBool('github_login_enabled')) {
        $providers[] = 'github';
    }
    
    // Display social login buttons for enabled providers
}
```

### Example 4: Assign Default Role on Registration
```php
use App\Modules\User\Models\AuthSetting;
use App\Models\User;

$user = User::create([...]);

$defaultRole = AuthSetting::get('default_user_role', 'user');
$user->assignRole($defaultRole);
```

---

## Best Practices

1. **Always use helper methods:**
   - Use `AuthSetting::getBool()` for boolean values
   - Use `AuthSetting::get()` for string values
   - Don't access the database directly

2. **Check master switches first:**
   ```php
   // Check master switch before checking individual providers
   if (AuthSetting::getBool('social_login_enabled')) {
       if (AuthSetting::getBool('google_login_enabled')) {
           // Show Google login
       }
   }
   ```

3. **Cache invalidation:**
   - Clear cache after updating settings
   - Settings are auto-cached for 1 hour

4. **Security considerations:**
   - Always validate OAuth tokens
   - Store sensitive tokens encrypted
   - Implement rate limiting for OTP requests

5. **User experience:**
   - Show clear messages when features are disabled
   - Provide alternative login methods
   - Allow users to manage connected accounts

---

## Troubleshooting

### Settings not updating?
```bash
php artisan cache:clear
php artisan view:clear
```

### Migration already ran?
```bash
# Check migration status
php artisan migrate:status

# Force re-run if needed (caution: drops tables)
php artisan migrate:refresh --path=database/migrations/2025_11_03_165716_create_auth_settings_table.php
```

### Seed data missing?
```bash
php artisan db:seed --class=AuthSettingsSeeder
```

### Route not found?
```bash
# Clear route cache
php artisan route:clear

# View all routes
php artisan route:list --name=settings
```

---

## Future Enhancements

Consider implementing:
- [ ] User profile page to manage connected social accounts
- [ ] OAuth callback handlers for social login
- [ ] OTP verification service
- [ ] Email verification workflow
- [ ] Mobile verification workflow
- [ ] 2FA with authenticator apps
- [ ] Login activity logs
- [ ] Device management
- [ ] Suspicious activity detection

---

## Summary

You now have a comprehensive authentication settings system that allows you to:

✅ **Manage authentication methods** via web interface
✅ **Toggle email, mobile, and social login** independently
✅ **Configure OTP settings** (length, expiry, cooldown)
✅ **Enable/disable social providers** (Google, Facebook, GitHub)
✅ **Track user's social connections** via auth_providers table
✅ **Access settings via code** using AuthSetting model
✅ **Cache settings** for better performance

All managed from: **Settings → Auth & Security Tab**

Happy authenticating! 🔐
