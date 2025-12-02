<?php

use Illuminate\Support\Facades\Route;

// Main Cypress testing page - Requires authentication
Route::middleware(['auth'])->group(function () {
    // Project Management Routes
    Route::resource('projects', 'ProjectController');

    // Module Management Routes (nested under projects)
    Route::prefix('projects/{project}')->group(function () {
        Route::get('modules', 'ModuleController@index')->name('modules.index');
        Route::get('modules/create', 'ModuleController@create')->name('modules.create');
        Route::post('modules', 'ModuleController@store')->name('modules.store');
        Route::get('modules/{module}', 'ModuleController@show')->name('modules.show');
        Route::get('modules/{module}/edit', 'ModuleController@edit')->name('modules.edit');
        Route::put('modules/{module}', 'ModuleController@update')->name('modules.update');
        Route::delete('modules/{module}', 'ModuleController@destroy')->name('modules.destroy');

        // Test Case Management Routes (nested under modules)
        Route::prefix('modules/{module}')->group(function () {
            Route::get('test-cases', 'TestCaseController@index')->name('test-cases.index');
            Route::get('test-cases/create', 'TestCaseController@create')->name('test-cases.create');
            Route::post('test-cases', 'TestCaseController@store')->name('test-cases.store');
            Route::get('test-cases/{testCase}', 'TestCaseController@show')->name('test-cases.show');
            Route::get('test-cases/{testCase}/edit', 'TestCaseController@edit')->name('test-cases.edit');
            Route::put('test-cases/{testCase}', 'TestCaseController@update')->name('test-cases.update');
            Route::delete('test-cases/{testCase}', 'TestCaseController@destroy')->name('test-cases.destroy');

            // Event capture routes
            Route::get('test-cases/{testCase}/events', 'TestCaseController@getEvents')->name('test-cases.events.get');
            Route::post('test-cases/{testCase}/events/clear', 'TestCaseController@clearEvents')->name('test-cases.events.clear');
            Route::post('test-cases/{testCase}/events/save', 'TestCaseController@saveEvents')->name('test-cases.events.save');
            Route::post('test-cases/{testCase}/events/delete', 'TestCaseController@deleteEvents')->name('test-cases.events.delete');
            Route::get('test-cases/{testCase}/capture-instructions', 'TestCaseController@captureInstructions')->name('test-cases.capture-instructions');
            Route::get('test-cases/{testCase}/generate-cypress', 'TestCaseController@generateCypressCode')->name('test-cases.generate-cypress');
        });
    });

    // Original Cypress Testing Routes
    Route::get('cypress/index', 'CypressController@index')->name('cypress.index');
    Route::get('cypress/bookmarklet', 'CypressController@bookmarklet')->name('cypress.bookmarklet');
    Route::get('cypress/download-extension', 'TestCaseController@downloadExtension')->name('cypress.download-extension');

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

