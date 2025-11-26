<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Run manual backup
     */
    public function runBackup()
    {
        try {
            // Run Spatie backup directly for manual backups (ignore settings)
            Artisan::call('backup:run', ['--only-db' => true]);
            
            $output = Artisan::output();
            
            // Check if backup was successful
            if (str_contains($output, 'Backup completed')) {
                return redirect()->back()
                    ->with('success', 'Backup created successfully! The backup file has been saved.');
            } else {
                return redirect()->back()
                    ->with('warning', 'Backup command executed. Output: ' . substr($output, 0, 200));
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * List all backups
     */
    public function listBackups()
    {
        $backups = [];
        
        try {
            $disk = Storage::disk('local');
            
            // Since 'local' disk root is storage/app/private, 
            // backups are stored at Laravel/ (relative to disk root)
            $path = 'Laravel';
            $files = [];
            
            if ($disk->exists($path)) {
                $files = $disk->allFiles($path);
            }
            
            foreach ($files as $file) {
                if (str_ends_with($file, '.zip')) {
                    $backups[] = [
                        'name' => basename($file),
                        'path' => $file,
                        'size' => $this->formatBytes($disk->size($file)),
                        'date' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                    ];
                }
            }
            
            // Sort by date descending
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
        } catch (\Exception $e) {
            // Directory doesn't exist yet
        }
        
        return view('backup.list', compact('backups'));
    }

    /**
     * Download backup file
     */
    public function downloadBackup($filename)
    {
        $disk = Storage::disk('local');
        
        // Backups are stored at Laravel/ (relative to local disk root: storage/app/private)
        $path = 'Laravel/' . $filename;
        
        if ($disk->exists($path)) {
            return $disk->download($path);
        }
        
        return redirect()->back()->with('error', 'Backup file not found!');
    }

    /**
     * Delete backup file
     */
    public function deleteBackup($filename)
    {
        $disk = Storage::disk('local');
        
        // Backups are stored at Laravel/ (relative to local disk root: storage/app/private)
        $path = 'Laravel/' . $filename;
        
        try {
            if ($disk->exists($path)) {
                $disk->delete($path);
                return redirect()->back()->with('success', 'Backup deleted successfully!');
            }
            
            return redirect()->back()->with('error', 'Backup file not found!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
