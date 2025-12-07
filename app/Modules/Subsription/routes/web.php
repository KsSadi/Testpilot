<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Subsription\Http\Controllers\SubscriptionController;
use App\Modules\Subsription\Http\Controllers\Admin\PlanController;
use App\Modules\Subsription\Http\Controllers\Admin\CouponController;
use App\Modules\Subsription\Http\Controllers\Admin\PaymentController;
use App\Modules\Subsription\Http\Controllers\Admin\SettingsController;
use App\Modules\Subsription\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;

// User-facing routes
Route::middleware(['auth'])->group(function () {
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::get('/my-subscription', [SubscriptionController::class, 'mySubscription'])->name('my-subscription');
        Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/validate-coupon', [SubscriptionController::class, 'validateCoupon'])->name('validate-coupon');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
    });
});

// Admin routes
Route::middleware(['auth', 'permission:manage-subscriptions'])->prefix('admin/subscriptions')->name('admin.subscriptions.')->group(function () {
    
    // Subscription Plans
    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [PlanController::class, 'index'])->name('index');
        Route::get('/create', [PlanController::class, 'create'])->name('create');
        Route::post('/', [PlanController::class, 'store'])->name('store');
        Route::get('/{plan}/edit', [PlanController::class, 'edit'])->name('edit');
        Route::put('/{plan}', [PlanController::class, 'update'])->name('update');
        Route::delete('/{plan}', [PlanController::class, 'destroy'])->name('destroy');
        Route::post('/{plan}/toggle', [PlanController::class, 'toggle'])->name('toggle');
    });

    // Coupons
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::get('/create', [CouponController::class, 'create'])->name('create');
        Route::post('/', [CouponController::class, 'store'])->name('store');
        Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [CouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
        Route::post('/{coupon}/toggle', [CouponController::class, 'toggle'])->name('toggle');
    });

    // Manual Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/approve', [PaymentController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [PaymentController::class, 'reject'])->name('reject');
    });

    // Subscription Management
    Route::prefix('manage')->name('manage.')->group(function () {
        Route::get('/', [AdminSubscriptionController::class, 'index'])->name('index');
        Route::get('/{subscription}', [AdminSubscriptionController::class, 'show'])->name('show');
        Route::post('/{subscription}/cancel', [AdminSubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/{subscription}/resume', [AdminSubscriptionController::class, 'resume'])->name('resume');
        Route::post('/override-limits/{user}', [AdminSubscriptionController::class, 'overrideLimits'])->name('override-limits');
    });

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/update', [SettingsController::class, 'update'])->name('update');
    });
});
