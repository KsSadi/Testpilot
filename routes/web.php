<?php

use Illuminate\Support\Facades\Route;

// Root route handled by DemoFrontend module
// Route::get('/', function () {
//     return redirect('/login');
// });

// Settings Test Route
Route::get('/settings-test', function () {
    return view('settings-test');
})->middleware('auth')->name('settings.test');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
});

// Backup Routes
Route::middleware(['auth', 'can:edit-settings'])->prefix('backup')->group(function () {
    Route::post('/run', [App\Http\Controllers\BackupController::class, 'runBackup'])->name('backup.run');
    Route::get('/list', [App\Http\Controllers\BackupController::class, 'listBackups'])->name('backup.list');
    Route::get('/download/{filename}', [App\Http\Controllers\BackupController::class, 'downloadBackup'])->name('backup.download');
    Route::delete('/delete/{filename}', [App\Http\Controllers\BackupController::class, 'deleteBackup'])->name('backup.delete');
    Route::post('/cleanup', function() {
        \Illuminate\Support\Facades\Artisan::call('backup:clean');
        return redirect()->back()->with('success', 'Old backups cleaned successfully!');
    })->name('backup.cleanup');
});

