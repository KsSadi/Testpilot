<?php

use Illuminate\Support\Facades\Route;
use App\Modules\RolePermission\Http\Controllers\RolePermissionController;

/*
|--------------------------------------------------------------------------
| Role & Permission Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Roles Management Routes
    Route::middleware(['permission:view-roles'])->group(function () {
        Route::get('/roles', [RolePermissionController::class, 'rolesIndex'])->name('roles.index');
    });
    
    Route::middleware(['permission:create-roles'])->group(function () {
        Route::get('/roles/create', [RolePermissionController::class, 'createRole'])->name('roles.create');
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    });
    
    Route::middleware(['permission:edit-roles'])->group(function () {
        Route::get('/roles/{role}/edit', [RolePermissionController::class, 'editRole'])->name('roles.edit');
        Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
    });
    
    Route::middleware(['permission:delete-roles'])->group(function () {
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');
    });
    
    // Permissions Management Routes
    Route::middleware(['permission:view-permissions'])->group(function () {
        Route::get('/permissions', [RolePermissionController::class, 'permissionsIndex'])->name('permissions.index');
    });
    
    Route::middleware(['permission:assign-permissions'])->group(function () {
        Route::post('/permissions/update', [RolePermissionController::class, 'updatePermissions'])->name('permissions.update');
    });
    
});

