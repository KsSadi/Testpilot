<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DemoFrontend\Http\Controllers\LandingController;

// Public routes - No authentication required
// Route::controller(LandingController::class)->group(function () {
//     Route::get('/', 'index')->name('landing.index');
//     Route::get('/docs', 'docs')->name('landing.docs');
// });

// return view('DemoFrontend::landing_v2', compact('features', 'stats', 'authMethods'));
Route::get('/', [LandingController::class, 'index'])->name('landing.index');