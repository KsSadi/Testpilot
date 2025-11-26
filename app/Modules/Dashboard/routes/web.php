<?php

use Illuminate\Support\Facades\Route;

// Dashboard routes - Require authentication
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard.index');
    Route::get('analytics', 'DashboardController@analytics')->name('dashboard.analytics');
});
