<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DemoFrontend\Http\Controllers\LandingController;

// Public routes - No authentication required
Route::controller(LandingController::class)->group(function () {
    Route::get('/', 'index')->name('landing.index');
    Route::get('/docs', 'docs')->name('landing.docs');
});
