<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Modules\Setting\Models\Setting;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run-now';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database backup now based on settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $enabled = setting('enable_auto_backup', false);
        
        if (!$enabled) {
            $this->warn('⚠️  Automatic backups are disabled in settings.');
            $this->info('Enable it from: Settings → Backup Configuration');
            return 1;
        }

        // Check if backup should run based on frequency
        if (!$this->shouldRunBackup()) {
            $frequency = setting('backup_frequency', 'daily');
            $this->info("ℹ️  Backup not due yet. Frequency: {$frequency}");
            return 0;
        }

        $this->info('🔄 Starting backup process...');
        
        try {
            // Run Spatie backup
            Artisan::call('backup:run', ['--only-db' => true]);
            
            $this->info('✅ Backup completed successfully!');
            $this->line(Artisan::output());
            
            // Update last backup timestamp
            \App\Modules\Setting\Models\Setting::set('last_backup_at', now()->toDateTimeString(), 'string', 'backup');
            
            // Clean old backups based on retention setting
            $retentionDays = setting('backup_retention_days', 30);
            Artisan::call('backup:clean');
            $this->info("🗑️  Cleaned backups older than {$retentionDays} days");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Backup failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if backup should run based on frequency setting
     */
    private function shouldRunBackup(): bool
    {
        $frequency = setting('backup_frequency', 'daily');
        $lastBackup = setting('last_backup_at');
        
        if (!$lastBackup) {
            return true; // No previous backup, run now
        }
        
        $lastBackupTime = \Carbon\Carbon::parse($lastBackup);
        $now = now();
        
        return match($frequency) {
            'daily' => $now->diffInHours($lastBackupTime) >= 24,
            'weekly' => $now->diffInDays($lastBackupTime) >= 7,
            'monthly' => $now->diffInDays($lastBackupTime) >= 30,
            default => $now->diffInHours($lastBackupTime) >= 24,
        };
    }
}
