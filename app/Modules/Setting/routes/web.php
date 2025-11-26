<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Setting\Http\Controllers\SettingController;

// Settings Routes (Protected with authentication and permissions)
Route::middleware(['auth'])->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        // View settings
        Route::get('/', [SettingController::class, 'index'])->name('index');
        
        // Update settings by category
        Route::post('/general', [SettingController::class, 'updateGeneral'])->name('update.general');
        Route::post('/seo', [SettingController::class, 'updateSeo'])->name('update.seo');
        Route::post('/auth', [SettingController::class, 'updateAuth'])->name('update.auth');
        Route::post('/auth-methods', [SettingController::class, 'updateAuthMethods'])->name('update.auth-methods');
        Route::post('/email', [SettingController::class, 'updateEmail'])->name('update.email');
        Route::post('/social', [SettingController::class, 'updateSocial'])->name('update.social');
        Route::post('/notifications', [SettingController::class, 'updateNotifications'])->name('update.notifications');
        Route::post('/backup', [SettingController::class, 'updateBackup'])->name('update.backup');
        Route::post('/developer', [SettingController::class, 'updateDeveloper'])->name('update.developer');
        
        // Cache management
        Route::post('/cache/clear', [SettingController::class, 'clearCache'])->name('cache.clear');
    });
});
