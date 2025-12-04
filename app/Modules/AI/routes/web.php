<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AI\Http\Controllers\AISettingsController;
use App\Modules\AI\Http\Controllers\AITestController;

// AI Settings Routes
Route::middleware(['auth', 'permission:edit-settings'])->prefix('ai')->name('ai.')->group(function () {
    Route::get('/settings', [AISettingsController::class, 'index'])->name('settings');
    Route::put('/providers/{id}', [AISettingsController::class, 'updateProvider'])->name('providers.update');
    Route::post('/providers/{id}/activate', [AISettingsController::class, 'setActive'])->name('providers.activate');
    Route::post('/providers/{id}/test', [AISettingsController::class, 'testConnection'])->name('providers.test');
    Route::post('/providers/{id}/reset-key-index', [AISettingsController::class, 'resetKeyIndex'])->name('providers.reset-key');
    Route::get('/providers/{id}/details', [AISettingsController::class, 'providerDetails'])->name('providers.details');
    Route::put('/settings', [AISettingsController::class, 'updateSettings'])->name('settings.update');
    Route::get('/usage-logs', [AISettingsController::class, 'usageLogs'])->name('usage.logs');
    Route::get('/statistics', [AISettingsController::class, 'statistics'])->name('statistics');
    
    // AI Test Playground Routes
    Route::get('/test-playground', [AITestController::class, 'index'])->name('test.playground');
    Route::post('/test-playground/generate', [AITestController::class, 'generate'])->name('test.generate');
});
