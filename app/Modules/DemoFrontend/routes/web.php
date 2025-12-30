<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DemoFrontend\Http\Controllers\LandingController;

// Public routes - No authentication required
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/documentation', [LandingController::class, 'documentation'])->name('landing.documentation');
Route::get('/api-reference', [LandingController::class, 'apiReference'])->name('landing.api-reference');
Route::get('/about', [LandingController::class, 'about'])->name('landing.about');
Route::get('/contact', [LandingController::class, 'contact'])->name('landing.contact');
Route::get('/blog', [LandingController::class, 'blog'])->name('landing.blog');
Route::get('/careers', [LandingController::class, 'careers'])->name('landing.careers');
Route::get('/quick-start', [LandingController::class, 'quickStart'])->name('landing.quick-start');
Route::get('/support', [LandingController::class, 'support'])->name('landing.support');
Route::get('/community', [LandingController::class, 'community'])->name('landing.community');