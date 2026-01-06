<?php

use Illuminate\Support\Facades\Route;

// Main Cypress testing page - Requires authentication
Route::middleware(['auth'])->group(function () {
    // Project Management Routes
    // NOTE: Specific routes MUST come before resource routes to avoid conflicts
    Route::get('projects/create', 'ProjectController@create')->name('projects.create');
    Route::post('projects', 'ProjectController@store')->name('projects.store')->middleware('check.project.limit');
    
    Route::resource('projects', 'ProjectController')->only(['index', 'show', 'edit', 'update', 'destroy']);

    // Unified Sharing Routes (supports projects, modules, test cases)
    Route::prefix('share')->name('share.')->group(function () {
        Route::post('/', 'ShareController@store')->name('store');
        Route::get('/', 'ShareController@index')->name('index');
        Route::delete('/{share}', 'ShareController@destroy')->name('destroy');
        Route::put('/{share}/role', 'ShareController@updateRole')->name('update-role');
    });

    // Invitation Routes
    Route::prefix('invitations')->name('invitations.')->group(function () {
        Route::get('pending', 'ShareController@pendingInvitations')->name('pending');
        Route::post('{share}/accept', 'ShareController@accept')->name('accept');
        Route::post('{share}/reject', 'ShareController@reject')->name('reject');
    });

    // Legacy project-specific routes (for backward compatibility)
    Route::prefix('projects/{project}/share')->name('projects.share.')->group(function () {
        Route::get('/', 'ProjectShareController@index')->name('index');
        Route::post('/', 'ProjectShareController@store')->name('store');
        Route::delete('/{share}', 'ProjectShareController@destroy')->name('destroy');
        Route::put('/{share}/role', 'ProjectShareController@updateRole')->name('update-role');
    });

    // Module Management Routes (nested under projects)
    Route::prefix('projects/{project}')->group(function () {
        Route::get('modules', 'ModuleController@index')->name('modules.index');
        Route::get('modules/create', 'ModuleController@create')->name('modules.create');
        Route::post('modules', 'ModuleController@store')->name('modules.store')->middleware('check.module.limit');
        Route::get('modules/{module}', 'ModuleController@show')->name('modules.show');
        Route::get('modules/{module}/edit', 'ModuleController@edit')->name('modules.edit');
        Route::put('modules/{module}', 'ModuleController@update')->name('modules.update');
        Route::delete('modules/{module}', 'ModuleController@destroy')->name('modules.destroy');

        // Test Case Management Routes (nested under modules)
        Route::prefix('modules/{module}')->group(function () {
            Route::get('test-cases', 'TestCaseController@index')->name('test-cases.index');
            Route::get('test-cases/create', 'TestCaseController@create')->name('test-cases.create');
            Route::post('test-cases', 'TestCaseController@store')->name('test-cases.store')->middleware('check.testcase.limit');
            Route::get('test-cases/{testCase}', 'TestCaseController@show')->name('test-cases.show');
            Route::get('test-cases/{testCase}/edit', 'TestCaseController@edit')->name('test-cases.edit');
            Route::put('test-cases/{testCase}', 'TestCaseController@update')->name('test-cases.update');
            Route::delete('test-cases/{testCase}', 'TestCaseController@destroy')->name('test-cases.destroy');

            // Event capture routes
            Route::get('test-cases/{testCase}/events', 'TestCaseController@getEvents')->name('test-cases.events.get');
            Route::post('test-cases/{testCase}/events/clear', 'TestCaseController@clearEvents')->name('test-cases.events.clear');
            Route::post('test-cases/{testCase}/events/save', 'TestCaseController@saveEvents')->name('test-cases.events.save');
            Route::post('test-cases/{testCase}/events/delete', 'TestCaseController@deleteEvents')->name('test-cases.events.delete');
            Route::post('test-cases/{testCase}/events/clear-all', 'TestCaseController@clearAllSavedEvents')->name('test-cases.events.clear-all');
            Route::get('test-cases/{testCase}/saved-events', 'TestCaseController@savedEventsHistory')->name('test-cases.saved-events');
            Route::get('test-cases/{testCase}/capture-instructions', 'TestCaseController@captureInstructions')->name('test-cases.capture-instructions');
            Route::get('test-cases/{testCase}/generate-cypress', 'TestCaseController@generateCypressCode')->name('test-cases.generate-cypress');
            Route::get('test-cases/{testCase}/download-cypress', 'TestCaseController@downloadCypressCode')->name('test-cases.download-cypress');
            
            // Event import routes
            Route::post('test-cases/{testCase}/import-events', 'TestCaseController@importEvents')->name('test-cases.import-events');
            
            // Event management routes (edit, delete, reorder)
            Route::put('test-cases/{testCase}/events/{eventId}/update', 'TestCaseController@updateEvent')->name('test-cases.events.update');
            Route::delete('test-cases/{testCase}/events/{eventId}/delete', 'TestCaseController@deleteEvent')->name('test-cases.events.delete-single');
            Route::post('test-cases/{testCase}/events/{eventId}/move', 'TestCaseController@moveEvent')->name('test-cases.events.move');

            // Code Generator Routes (Playwright-style)
            Route::prefix('test-cases/{testCase}/code-generator')->name('code-generator.')->group(function () {
                Route::get('preview', 'CodeGeneratorController@preview')->name('preview');
                Route::get('download', 'CodeGeneratorController@download')->name('download');
                Route::post('generate', 'CodeGeneratorController@generate')->name('generate');
                Route::get('live-preview', 'CodeGeneratorController@livePreview')->name('live-preview');
                Route::get('events/{eventId}/selectors', 'CodeGeneratorController@suggestSelectors')->name('suggest-selectors');
                Route::get('events/{eventId}/optimize', 'CodeGeneratorController@optimizeSelector')->name('optimize-selector');
                Route::post('validate-selector', 'CodeGeneratorController@validateSelector')->name('validate-selector');
            });

            // Browser Automation Routes (Codegen - Auto-launch browser)
            Route::prefix('test-cases/{testCase}/recording')->name('recording.')->group(function () {
                Route::post('start', 'RecordingController@start')->name('start');
                Route::post('stop', 'RecordingController@stop')->name('stop');
                Route::get('events/{sessionId}', 'RecordingController@getEvents')->name('events');
                Route::post('generate-code', 'RecordingController@generateCode')->name('generate-code');
                Route::post('save-code', 'RecordingController@saveCode')->name('save-code');
                Route::post('save-events', 'RecordingController@saveEventsOnly')->name('save-events');
            });
            
            // Code Generator Page (New Workflow)
            Route::get('test-cases/{testCase}/code-generator', 'RecordingController@codeGeneratorPage')->name('code-generator.page');
            Route::post('test-cases/{testCase}/code-generator/generate', 'RecordingController@generateAndStoreCode')->name('code-generator.generate-basic');
            Route::post('test-cases/{testCase}/code-generator/generate-ai', 'RecordingController@generateWithAI')->name('code-generator.generate-ai-pro');
            Route::post('test-cases/{testCase}/code-generator/polish-ai', 'RecordingController@polishWithAI')->name('code-generator.polish-ai');
            Route::delete('test-cases/{testCase}/code-generator/{generatedCode}/delete', 'RecordingController@deleteGeneratedCode')->name('code-generator.delete-version');
            
            // Event Session Routes (Versioned Events)
            Route::delete('test-cases/{testCase}/event-sessions/{eventSession}/delete', 'RecordingController@deleteEventSession')->name('event-sessions.delete');
            
            // Clear saved generated code
            Route::post('test-cases/{testCase}/clear-code', 'RecordingController@clearCode')->name('test-cases.clear-code');
        });
        
        // Module-level code generator routes
        Route::post('modules/{module}/export-suite', 'CodeGeneratorController@exportSuite')->name('modules.export-suite');
    });

    // Get all test cases from a project for import selection
    Route::get('projects/{project}/test-cases-for-import', 'ProjectController@getTestCasesForImport')->name('projects.test-cases-for-import');

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

// Browser Automation Health Check (NO auth required)
Route::get('recording/health', 'RecordingController@healthCheck')->name('recording.health');
