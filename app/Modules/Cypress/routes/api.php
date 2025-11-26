<?php

use Illuminate\Support\Facades\Route;

// API routes for Cypress testing (if needed for external integrations)
Route::middleware(['api'])->prefix('api/cypress')->group(function () {
    
    // API endpoints
    Route::post('start-test', 'CypressController@startTest');
    Route::post('capture-event', 'CypressController@captureEvent');
    Route::post('stop-test', 'CypressController@stopTest');
    Route::get('test-status', 'CypressController@getTestStatus');
    
});