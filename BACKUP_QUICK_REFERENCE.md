# Quick Reference: Automatic Backup System

## 🚀 Quick Start

### 1. Enable Automatic Backups
1. Go to **Settings → Backup & System**
2. Check ✅ **"Enable Automatic Backups"**
3. Select **Backup Frequency**: Daily, Weekly, or Monthly
4. Set **Retention Period**: 1-365 days
5. Click **"Save Backup Settings"**

### 2. Set Up Windows Scheduler
```powershell
# Run PowerShell as Administrator, then:
cd "E:\Arpa Nihan_personal\code_study\larakit"
.\setup-scheduler.ps1
```

## 📅 Backup Schedule

| Setting | When It Runs |
|---------|--------------|
| **Daily** | Every day at 2:00 AM |
| **Weekly** | Every Sunday at 2:00 AM |
| **Monthly** | 1st day of month at 2:00 AM |

**Cleanup**: Old backups are automatically removed daily at 3:00 AM based on your retention period.

## 🛠️ Manual Commands

```bash
# Create backup now (checks if auto-backup is enabled)
php artisan backup:run-now

# Create backup (always runs, ignores settings)
php artisan backup:run --only-db

# View all scheduled tasks
php artisan schedule:list

# Manually run scheduler (for testing)
php artisan schedule:run

# Clean old backups
php artisan backup:clean

# List all backup files
php artisan backup:list

# Check backup health
php artisan backup:monitor
```

## 📁 Backup Location

**Default**: `storage/app/private/Laravel/`

Files are named: `YYYY-MM-DD-HH-MM-SS.zip`

Example: `2025-11-18-14-30-00.zip`

## 🎛️ Settings Reference

| Setting | Default | Description |
|---------|---------|-------------|
| `enable_auto_backup` | false | Enable/disable automatic backups |
| `backup_frequency` | daily | How often to backup (daily/weekly/monthly) |
| `backup_storage` | local | Where to store (local/s3/dropbox/google) |
| `backup_retention_days` | 30 | Days to keep old backups |
| `last_backup_at` | null | Timestamp of last backup (auto-updated) |

## ✅ Verification Checklist

- [ ] Automatic backups enabled in Settings
- [ ] Windows Task Scheduler task created
- [ ] Task runs every 1 minute
- [ ] PHP path is correct in task
- [ ] Project path is correct in task
- [ ] Test: `php artisan schedule:list` shows tasks
- [ ] Storage folder has write permissions
- [ ] Backups appear in `storage/app/private/Laravel/`

## 🔍 Troubleshooting

### Problem: Backups not running automatically
**Solutions**:
1. Check if "Enable Automatic Backups" is ON in Settings
2. Verify Task Scheduler task is enabled
3. Check last run time in Task Scheduler
4. Review `storage/logs/laravel.log`

### Problem: Permission denied
**Solutions**:
1. Run `chmod -R 775 storage` (Linux/Mac)
2. Ensure Windows user has write access to `storage` folder
3. Run Task Scheduler with elevated privileges

### Problem: Backups not visible in UI
**Solutions**:
1. Check `storage/app/private/Laravel/` folder exists
2. Verify backups are `.zip` files
3. Check file permissions
4. Clear cache: `php artisan cache:clear`

## 📊 Monitoring

### Check Last Backup
```bash
# In tinker
php artisan tinker
>>> setting('last_backup_at')
```

### View Backup Files
```bash
# List all backups with details
Get-ChildItem storage\app\private\Laravel\*.zip | 
  Select-Object Name, LastWriteTime, @{N='Size';E={"{0:N2} MB" -f ($_.Length/1MB)}}
```

### Storage Space
```bash
# Check available disk space
Get-PSDrive C | Select-Object Used, Free
```

## 🎯 Best Practices

1. ✅ Enable automatic backups for production
2. ✅ Set retention period between 7-30 days
3. ✅ Store backups off-site (S3, Dropbox, etc.)
4. ✅ Test restore process regularly
5. ✅ Monitor backup success/failure notifications
6. ✅ Keep at least 3 backup copies (3-2-1 rule)

## 📞 Support

For issues or questions, check:
- `BACKUP_SCHEDULER_SETUP.md` - Detailed setup guide
- `storage/logs/laravel.log` - Application logs
- Task Scheduler logs - Windows Event Viewer

---

**Last Updated**: November 18, 2025
**Laravel Version**: 11.x
**Backup Package**: Spatie Laravel Backup v9.3
