<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\LoginController;
use App\Modules\User\Http\Controllers\RegisterController;
use App\Modules\User\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Social Authentication
    Route::get('/auth/{provider}', [LoginController::class, 'socialRedirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [LoginController::class, 'socialCallback'])->name('social.callback');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Member Management Routes
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| User Module Routes
|--------------------------------------------------------------------------
*/

Route::get('user', 'UserController@welcome');
