<?php

use Illuminate\Support\Facades\Route;

// Main Cypress testing page - Requires authentication
Route::middleware(['auth'])->group(function () {
    Route::get('cypress/index', 'CypressController@index')->name('cypress.index');
    Route::get('cypress/bookmarklet', 'CypressController@bookmarklet')->name('cypress.bookmarklet');
    
    // API endpoints for Cypress testing
    Route::post('cypress/start-test', 'CypressController@startTest')->name('cypress.start');
    Route::post('cypress/capture-event', 'CypressController@captureEvent')->name('cypress.capture');
    Route::post('cypress/stop-test', 'CypressController@stopTest')->name('cypress.stop');
    Route::get('cypress/export-results', 'CypressController@exportResults')->name('cypress.export');
    Route::get('cypress/test-status', 'CypressController@getTestStatus')->name('cypress.status');
    Route::get('cypress/get-events', 'CypressController@getEvents')->name('cypress.getEvents');
    Route::get('cypress/current-session', 'CypressController@getCurrentSession')->name('cypress.currentSession');
});

// Bookmarklet capture endpoint (NO auth - needs to accept from any website)
Route::post('cypress/capture-event-bookmarklet', 'CypressController@captureEventBookmarklet')->name('cypress.capture.bookmarklet');
Route::options('cypress/capture-event-bookmarklet', 'CypressController@handleCorsOptions')->name('cypress.capture.bookmarklet.options');
Route::get('cypress/capture-script.js', 'CypressController@captureScript')->name('cypress.capture.script');
Route::get('cypress/cypress-auto-capture.user.js', function() {
    return response()->file(public_path('cypress/cypress-auto-capture.user.js'), [
        'Content-Type' => 'application/javascript'
    ]);
})->name('cypress.userscript');

// Website proxy route (NO auth middleware - needs to work in iframe)
// Accept all HTTP methods (GET, POST, PUT, DELETE, etc.) for AJAX requests
Route::match(['get', 'post', 'put', 'delete', 'patch', 'options'], 'cypress/proxy', 'CypressController@proxyWebsite')->name('cypress.proxy');
