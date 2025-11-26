<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Schedule automatic database backups
 * Runs based on settings configured in Settings -> Backup Configuration
 */
Schedule::call(function () {
    // Only run if automatic backups are enabled
    if (!setting('enable_auto_backup', false)) {
        return;
    }

    // Run the backup command
    Artisan::call('backup:run-now');
})->when(function () {
    // Check if backups are enabled
    return setting('enable_auto_backup', false);
})->daily()->at('02:00')
->name('backup:auto')
->description('Automatic database backup (daily at 2 AM)');

/**
 * Clean old backups daily
 * Removes backups older than the retention period set in settings
 */
Schedule::command('backup:clean')
    ->daily()
    ->at('03:00')
    ->name('backup:cleanup')
    ->description('Clean old backups based on retention policy');
