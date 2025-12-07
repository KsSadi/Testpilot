# 🛡️ Safe Database Seeding Guide

## ⚠️ IMPORTANT: Understanding Seeder Commands

### **DANGEROUS Commands (Will DELETE All Data):**

```powershell
# ❌ NEVER use these unless you want to lose all data!
php artisan migrate:fresh        # Drops all tables and recreates them
php artisan migrate:fresh --seed # Drops all tables, recreates, and seeds
php artisan migrate:refresh      # Rolls back and re-runs migrations
```

### **SAFE Commands (Preserves Existing Data):**

```powershell
# ✅ Safe - Only adds new data, doesn't delete existing
php artisan db:seed --class=SubscriptionPlansSeeder
php artisan db:seed --class=AIProvidersSeeder
php artisan db:seed --class=RolePermissionSeeder

# ✅ Safe - Only runs new migrations
php artisan migrate
```

---

## 📋 How to Seed Without Losing Data

### **1. Seed Subscription System Only:**

```powershell
# Seed subscription plans (uses updateOrInsert - safe)
php artisan db:seed --class=SubscriptionPlansSeeder

# Seed subscription permissions
php artisan db:seed --class=SubscriptionPermissionsSeeder
```

### **2. Seed AI Providers Only:**

```powershell
# This is SAFE - uses updateOrInsert, won't delete your existing API keys
php artisan db:seed --class=AIProvidersSeeder
```

### **3. Seed Roles & Permissions Only:**

```powershell
# This is SAFE - uses updateOrCreate
php artisan db:seed --class=RolePermissionSeeder
```

### **4. Seed Everything (First Time Only):**

```powershell
# Only run this on FIRST INSTALL - will create test users
php artisan db:seed
```

---

## 🔄 After Code Updates

### **Scenario 1: Added New Permissions**

```powershell
# Just re-run the permission seeder
php artisan db:seed --class=RolePermissionSeeder
```

### **Scenario 2: Added New Subscription Plans**

```powershell
# Re-run subscription plans seeder
php artisan db:seed --class=SubscriptionPlansSeeder
```

### **Scenario 3: Updated AI Provider Models**

```powershell
# Re-run AI providers seeder (won't delete your API keys)
php artisan db:seed --class=AIProvidersSeeder
```

### **Scenario 4: Added New Migration Files**

```powershell
# Run only new migrations
php artisan migrate

# Then seed if needed
php artisan db:seed --class=YourNewSeeder
```

---

## 🆘 I Already Lost My Data - How to Recover?

### **Option 1: Restore from Backup**

If you had automatic backups enabled:

```powershell
# List available backups
php artisan backup:list

# Restore latest backup
php artisan backup:restore
```

### **Option 2: Re-enter AI Provider Data**

1. Go to **Admin → AI Settings**
2. Configure each provider:
   - OpenAI: Add your API key
   - Gemini: Add your API key
   - DeepSeek: Add your API key
3. Activate the provider you want to use

### **Option 3: Manually Insert via Database**

If you have your API keys saved elsewhere:

```sql
UPDATE ai_providers 
SET api_key = 'your-openai-key-here', 
    is_active = 1 
WHERE name = 'openai';
```

---

## 📝 Safe Seeder Examples

All these seeders are **SAFE** and use `updateOrInsert` or `updateOrCreate`:

### **AIProvidersSeeder:**
```php
DB::table('ai_providers')->updateOrInsert(
    ['name' => $provider['name']], // Only matches by name
    $provider                       // Updates if exists, inserts if not
);
```

### **SubscriptionPlansSeeder:**
```php
SubscriptionPlan::updateOrCreate(
    ['slug' => 'free'],           // Matches by slug
    [/* plan data */]             // Updates if exists
);
```

### **RolePermissionSeeder:**
```php
Permission::updateOrCreate(['name' => 'view-users']);
Role::updateOrCreate(['name' => 'superadmin']);
```

---

## ✅ Best Practices

1. **Never use `migrate:fresh` in production** - You will lose ALL data
2. **Always backup before seeding** - Run `php artisan backup:run-now`
3. **Test seeders on local first** - Make sure they use `updateOrInsert`
4. **Commit before risky operations** - So you can rollback if needed
5. **Read seeder code before running** - Check if it uses `delete()` or `truncate()`

---

## 🎯 Quick Reference

| Goal | Safe Command |
|------|--------------|
| Add new permissions | `php artisan db:seed --class=RolePermissionSeeder` |
| Update subscription plans | `php artisan db:seed --class=SubscriptionPlansSeeder` |
| Re-seed AI providers | `php artisan db:seed --class=AIProvidersSeeder` |
| Run new migrations only | `php artisan migrate` |
| Create backup | `php artisan backup:run-now` |

---

## 🚨 Remember

**`migrate:fresh --seed`** = Nuclear option - destroys everything and starts over!

Only use it when:
- Setting up a brand new development environment
- You have a backup and specifically want to reset everything
- You're testing with dummy data that you don't care about

**NEVER** use it on:
- Production servers
- When you have real user data
- When you have configured AI API keys
- When you have active subscriptions
