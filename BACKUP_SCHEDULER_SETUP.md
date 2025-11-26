# Automatic Backup Scheduler Setup Guide

## ✅ Scheduler Configuration Complete!

The Laravel task scheduler has been configured to run automatic backups based on your settings.

---

## 📋 Scheduled Tasks

### 1. **Automatic Backup** (backup:auto)
- **Schedule**: Daily at 2:00 AM
- **Frequency**: Respects your setting (Daily/Weekly/Monthly)
- **Condition**: Only runs if "Enable Automatic Backups" is checked
- **Action**: Creates database backup and stores it in configured location

### 2. **Backup Cleanup** (backup:cleanup)
- **Schedule**: Daily at 3:00 AM
- **Action**: Removes backups older than retention period (default: 30 days)

---

## 🪟 Windows Task Scheduler Setup

To make Laravel's scheduler run automatically, you need to set up a Windows Task that runs every minute.

### Option 1: Using the Setup Script (Recommended)

1. **Run PowerShell as Administrator**
2. Navigate to your project:
   ```powershell
   cd "E:\Arpa Nihan_personal\code_study\larakit"
   ```
3. Run the setup script:
   ```powershell
   .\setup-scheduler.ps1
   ```

### Option 2: Manual Setup

1. **Open Task Scheduler**
   - Press `Win + R`, type `taskschd.msc`, press Enter

2. **Create a New Task**
   - Click "Create Task" (not "Create Basic Task")
   - Name: `Laravel Scheduler - LaraKit`
   - Description: `Runs Laravel task scheduler every minute`
   - Check "Run whether user is logged on or not"
   - Check "Run with highest privileges"

3. **Triggers Tab**
   - Click "New"
   - Begin the task: `On a schedule`
   - Settings: `Daily`
   - Repeat task every: `1 minute`
   - For a duration of: `Indefinitely`
   - Click OK

4. **Actions Tab**
   - Click "New"
   - Action: `Start a program`
   - Program/script: `C:\xampp\php\php.exe` (adjust path to your PHP installation)
   - Add arguments: `artisan schedule:run`
   - Start in: `E:\Arpa Nihan_personal\code_study\larakit`
   - Click OK

5. **Conditions Tab**
   - Uncheck "Start the task only if the computer is on AC power"

6. **Settings Tab**
   - Check "Allow task to be run on demand"
   - Check "Run task as soon as possible after a scheduled start is missed"
   - If the task is already running: `Do not start a new instance`

7. **Click OK** and enter your Windows password if prompted

---

## 🐧 Linux/macOS Cron Setup

Add this to your crontab (`crontab -e`):

```bash
* * * * * cd /path/to/larakit && php artisan schedule:run >> /dev/null 2>&1
```

---

## ✅ Verification

### Check if scheduler is working:
```bash
php artisan schedule:list
```

### Test backup manually:
```bash
php artisan backup:run-now
```

### View scheduled tasks output (after setup):
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log
```

---

## ⚙️ How It Works

1. **Windows Task Scheduler** runs `php artisan schedule:run` every minute
2. Laravel checks what tasks are due to run
3. If automatic backups are enabled AND it's time (based on frequency):
   - `backup:run-now` command executes
   - Creates database backup
   - Stores in `storage/app/private/Laravel/`
4. Every day at 3 AM, old backups are cleaned based on retention policy

---

## 🎯 Backup Settings

Configure these in **Settings → Backup Configuration**:

- ✅ **Enable Automatic Backups**: Turn on/off scheduled backups
- 📅 **Backup Frequency**: Daily, Weekly, or Monthly
- 💾 **Storage Location**: Local, S3, Dropbox, or Google Drive
- 🗓️ **Retention Period**: How many days to keep backups (1-365)

---

## 🔍 Troubleshooting

### Backups not running automatically?
1. Check if Task Scheduler task is enabled
2. Verify PHP path in the task action
3. Check Laravel logs: `storage/logs/laravel.log`
4. Run manually: `php artisan schedule:run` to test

### Permission issues?
- Ensure `storage/app/private/Laravel/` has write permissions
- Run Task Scheduler as Administrator

### Wrong time zone?
- Set correct timezone in `config/app.php` or Settings → General

---

## 📊 Monitoring Backups

### View all backups:
- **UI**: Settings → Backup Configuration → "View Backups" button
- **Command**: `php artisan backup:list`

### Manual backup:
- **UI**: Settings → Backup Configuration → "Create Backup Now" button
- **Command**: `php artisan backup:run-now`

### Clean old backups:
- **UI**: Settings → Backup Configuration → "Clean Old Backups" button
- **Command**: `php artisan backup:clean`

---

## 🎉 You're All Set!

Once you set up the Windows Task Scheduler, automatic backups will run based on your settings configuration!
