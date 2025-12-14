<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cypress\Models\Project;
use App\Modules\Cypress\Models\Module;
use App\Modules\Cypress\Models\TestCase;
use App\Modules\Cypress\Models\TestCaseEvent;
use Illuminate\Http\Request;

class TestCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, Module $module)
    {
        $testCases = $module->testCases()->orderBy('order')->get();

        $data = [
            'pageTitle' => 'Test Cases - ' . $module->name,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name, 'url' => route('modules.show', [$project, $module])],
                ['title' => 'Test Cases']
            ],
            'project' => $project,
            'module' => $module,
            'testCases' => $testCases
        ];

        return view('Cypress::test-cases.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project, Module $module)
    {
        $maxOrder = $module->testCases()->max('order') ?? 0;

        // Get test cases from same module that have saved events (for cloning)
        $clonableTestCases = $module->testCases()
            ->where('created_by', auth()->id())
            ->withCount(['events as saved_events_count' => function($query) {
                $query->where('is_saved', true);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'pageTitle' => 'Create Test Case',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name, 'url' => route('modules.show', [$project, $module])],
                ['title' => 'Create Test Case']
            ],
            'project' => $project,
            'module' => $module,
            'nextOrder' => $maxOrder + 1,
            'clonableTestCases' => $clonableTestCases
        ];

        return view('Cypress::test-cases.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'clone_from' => 'nullable|exists:test_cases,id'
        ]);

        $validated['project_id'] = $project->id;
        $validated['module_id'] = $module->id;

        $testCase = TestCase::create($validated);

        // Clone events from selected test case if specified
        if ($request->filled('clone_from')) {
            $sourceTestCase = TestCase::find($request->clone_from);
            
            // Security: Verify source test case belongs to same module and user
            if ($sourceTestCase && 
                $sourceTestCase->module_id === $module->id && 
                $sourceTestCase->created_by === auth()->id()) {
                
                // Get all saved events from source test case
                $sourceEvents = $sourceTestCase->events()
                    ->where('is_saved', true)
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                if ($sourceEvents->isNotEmpty()) {
                    // Generate new session_id for cloned events
                    $newSessionId = 'tc_' . time() . '_' . uniqid();
                    
                    // Clone events with new session_id and current timestamps
                    foreach ($sourceEvents as $sourceEvent) {
                        TestCaseEvent::create([
                            'session_id' => $newSessionId,
                            'event_type' => $sourceEvent->event_type,
                            'selector' => $sourceEvent->selector,
                            'event_data' => $sourceEvent->event_data, // JSON data
                            'is_saved' => true,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    
                    // Update test case with new session_id
                    $testCase->update(['session_id' => $newSessionId]);
                    
                    return redirect()->route('test-cases.show', [$project, $module, $testCase])
                        ->with('success', 'Test case created and ' . $sourceEvents->count() . ' events cloned successfully.');
                }
            }
        }

        return redirect()->route('test-cases.index', [$project, $module])
            ->with('success', 'Test case created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Module $module, TestCase $testCase)
    {
        $previousTestCase = $testCase->previousTestCase();
        $nextTestCase = $testCase->nextTestCase();

        $data = [
            'pageTitle' => $testCase->name,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name, 'url' => route('modules.show', [$project, $module])],
                ['title' => $testCase->name]
            ],
            'project' => $project,
            'module' => $module,
            'testCase' => $testCase,
            'previousTestCase' => $previousTestCase,
            'nextTestCase' => $nextTestCase
        ];

        return view('Cypress::test-cases.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, Module $module, TestCase $testCase)
    {
        $data = [
            'pageTitle' => 'Edit Test Case',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name, 'url' => route('modules.show', [$project, $module])],
                ['title' => $testCase->name, 'url' => route('test-cases.show', [$project, $module, $testCase])],
                ['title' => 'Edit']
            ],
            'project' => $project,
            'module' => $module,
            'testCase' => $testCase
        ];

        return view('Cypress::test-cases.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, Module $module, TestCase $testCase)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $testCase->update($validated);

        return redirect()->route('test-cases.index', [$project, $module])
            ->with('success', 'Test case updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Module $module, TestCase $testCase)
    {
        $testCase->delete();

        return redirect()->route('test-cases.index', [$project, $module])
            ->with('success', 'Test case deleted successfully.');
    }

    /**
     * Get events for a test case session
     */
    public function getEvents(Project $project, Module $module, TestCase $testCase)
    {
        $events = $testCase->events()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'events' => $events,
            'total' => $events->count(),
            'saved' => $testCase->savedEvents()->count(),
            'unsaved' => $testCase->unsavedEvents()->count()
        ]);
    }

    /**
     * Clear all unsaved events for a test case
     */
    public function clearEvents(Project $project, Module $module, TestCase $testCase)
    {
        $deletedCount = $testCase->unsavedEvents()->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared $deletedCount unsaved events",
            'deleted' => $deletedCount
        ]);
    }

    /**
     * Save all unsaved events for a test case
     */
    public function saveEvents(Project $project, Module $module, TestCase $testCase)
    {
        // Get all unsaved events ordered by creation time
        $unsavedEvents = $testCase->unsavedEvents()->orderBy('created_at', 'asc')->get();
        $originalCount = $unsavedEvents->count();
        
        // Clean up events before saving
        $cleanedEvents = $this->cleanupEvents($unsavedEvents);
        
        // Delete events that were removed during cleanup
        $eventsToDelete = $unsavedEvents->pluck('id')->diff($cleanedEvents->pluck('id'));
        if ($eventsToDelete->count() > 0) {
            TestCaseEvent::whereIn('id', $eventsToDelete)->delete();
        }
        
        // Mark remaining events as saved
        $updated = TestCaseEvent::whereIn('id', $cleanedEvents->pluck('id'))
            ->update(['is_saved' => true]);
        
        $cleanedCount = $originalCount - $updated;

        return response()->json([
            'success' => true,
            'message' => "Saved $updated events" . ($cleanedCount > 0 ? " (removed $cleanedCount redundant events)" : ""),
            'saved' => $updated,
            'cleaned' => $cleanedCount
        ]);
    }
    
    /**
     * Clean up redundant events
     */
    private function cleanupEvents($events)
    {
        $cleaned = collect();
        $inputFieldBuffer = []; // Buffer to track sequential input events
        
        foreach ($events as $event) {
            $eventType = $event->event_type;
            $eventData = json_decode($event->event_data, true);
            
            // 1. Handle INPUT events - merge sequential typing on same field
            if ($eventType === 'input') {
                $selector = $eventData['cypressSelector'] ?? $event->selector;
                
                // Check if there's already an input event for this field in buffer
                $existingKey = null;
                foreach ($inputFieldBuffer as $key => $bufferedEvent) {
                    $bufferedData = json_decode($bufferedEvent->event_data, true);
                    $bufferedSelector = $bufferedData['cypressSelector'] ?? $bufferedEvent->selector;
                    
                    if ($bufferedSelector === $selector) {
                        $existingKey = $key;
                        break;
                    }
                }
                
                if ($existingKey !== null) {
                    // Replace old input event with new one (keeps final value)
                    $inputFieldBuffer[$existingKey] = $event;
                } else {
                    // Add new input event to buffer
                    $inputFieldBuffer[] = $event;
                }
                continue;
            }
            
            // Flush input buffer when we encounter non-input event
            if (!empty($inputFieldBuffer)) {
                foreach ($inputFieldBuffer as $bufferedEvent) {
                    $cleaned->push($bufferedEvent);
                }
                $inputFieldBuffer = [];
            }
            
            // 2. Handle CLICK events - remove clicks on non-interactive elements
            if ($eventType === 'click') {
                $tagName = strtoupper($event->tag_name ?? '');
                $isInteractive = false;
                
                // Check if element is interactive
                $interactiveTags = ['A', 'BUTTON', 'INPUT', 'SELECT', 'TEXTAREA'];
                if (in_array($tagName, $interactiveTags)) {
                    $isInteractive = true;
                }
                
                // Check for interactive attributes/classes
                if (!$isInteractive && $eventData) {
                    $hasRole = isset($eventData['role']) && in_array($eventData['role'], ['button', 'link', 'tab', 'menuitem']);
                    $hasOnClick = isset($eventData['onclick']) || (isset($eventData['attributes']) && isset($eventData['attributes']['onclick']));
                    $hasButtonClass = false;
                    
                    if (isset($eventData['selectors']['className'])) {
                        $classes = $eventData['selectors']['className'];
                        $hasButtonClass = preg_match('/\b(btn|button|link|clickable)\b/i', $classes);
                    }
                    
                    $isInteractive = $hasRole || $hasOnClick || $hasButtonClass;
                }
                
                // Skip clicks on non-interactive elements like BODY, DIV, SPAN without handlers
                $ignoreTags = ['BODY', 'HTML'];
                if (in_array($tagName, $ignoreTags) || (!$isInteractive && in_array($tagName, ['DIV', 'SPAN', 'P', 'SECTION', 'ARTICLE', 'HEADER', 'FOOTER', 'NAV']))) {
                    continue; // Skip this useless click
                }
            }
            
            // 3. Keep all other events (checkbox, radio, select, file, submit, etc.)
            $cleaned->push($event);
        }
        
        // Flush any remaining input events in buffer
        if (!empty($inputFieldBuffer)) {
            foreach ($inputFieldBuffer as $bufferedEvent) {
                $cleaned->push($bufferedEvent);
            }
        }
        
        return $cleaned;
    }

    /**
     * Delete selected events
     */
    public function deleteEvents(Request $request, Project $project, Module $module, TestCase $testCase)
    {
        $eventIds = $request->input('event_ids', []);

        if (empty($eventIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No events selected'
            ], 400);
        }

        $deleted = TestCaseEvent::whereIn('id', $eventIds)
            ->where('session_id', $testCase->session_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted $deleted event(s)",
            'deleted' => $deleted
        ]);
    }

    /**
     * Clear all saved events for a test case
     */
    public function clearAllSavedEvents(Project $project, Module $module, TestCase $testCase)
    {
        $deletedCount = $testCase->savedEvents()->delete();

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted all $deletedCount saved events",
            'deleted' => $deletedCount
        ]);
    }

    /**
     * Show saved events history page
     */
    public function savedEventsHistory(Project $project, Module $module, TestCase $testCase)
    {
        $savedEvents = $testCase->savedEvents()
            ->orderBy('event_order', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $data = [
            'pageTitle' => 'Saved Events History - ' . $testCase->name,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name, 'url' => route('modules.show', [$project, $module])],
                ['title' => $testCase->name, 'url' => route('test-cases.show', [$project, $module, $testCase])],
                ['title' => 'Saved Events History']
            ],
            'project' => $project,
            'module' => $module,
            'testCase' => $testCase,
            'savedEvents' => $savedEvents
        ];

        return view('Cypress::test-cases.saved-events', $data);
    }

    /**
     * Show event capture instructions page
     */
    public function captureInstructions(Project $project, Module $module, TestCase $testCase)
    {
        $data = [
            'pageTitle' => 'Event Capture Instructions',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name, 'url' => route('modules.show', [$project, $module])],
                ['title' => $testCase->name, 'url' => route('test-cases.show', [$project, $module, $testCase])],
                ['title' => 'Capture Instructions']
            ],
            'project' => $project,
            'module' => $module,
            'testCase' => $testCase
        ];

        return view('Cypress::test-cases.capture-instructions', $data);
    }

    /**
     * Download Chrome extension ZIP file
     */
    public function downloadExtension()
    {
        $filePath = public_path('cypress/chrome-extension.zip');

        if (!file_exists($filePath)) {
            abort(404, 'Extension file not found');
        }

        return response()->download($filePath, 'chrome-extension.zip');
    }

    /**
     * Generate Cypress test code from saved events
     */
    public function generateCypressCode(Project $project, Module $module, TestCase $testCase)
    {
        // Get only saved events, ordered by creation time
        $events = TestCaseEvent::where('session_id', $testCase->session_id)
            ->where('is_saved', true)
            ->orderBy('created_at', 'asc')
            ->get();

        // Generate Cypress code
        $cypressCode = '';
        $usesXpath = false;
        $filename = $this->sanitizeFilename($testCase->name) . '.cy.js';

        if (!$events->isEmpty()) {
            // Check if any events use xpath
            foreach ($events as $event) {
                $eventData = json_decode($event->event_data, true);
                if ($eventData && isset($eventData['selectors']['xpath'])) {
                    $selectors = $eventData['selectors'];
                    if (empty($selectors['testId']) && empty($selectors['id']) &&
                        empty($selectors['name']) && empty($selectors['ariaLabel']) &&
                        empty($selectors['placeholder'])) {
                        $usesXpath = true;
                        break;
                    }
                }
            }

            $cypressCode = $this->convertEventsToCypressCode($events, $project, $module, $testCase);
        }

        $data = [
            'pageTitle' => 'Generate Cypress Code',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name, 'url' => route('modules.show', [$project, $module])],
                ['title' => $testCase->name, 'url' => route('test-cases.show', [$project, $module, $testCase])],
                ['title' => 'Generate Code']
            ],
            'project' => $project,
            'module' => $module,
            'testCase' => $testCase,
            'events' => $events,
            'cypressCode' => $cypressCode,
            'usesXpath' => $usesXpath,
            'filename' => $filename
        ];

        return view('Cypress::test-cases.generate-code', $data);
    }

    public function downloadCypressCode(Project $project, Module $module, TestCase $testCase)
    {
        // Get only saved events, ordered by creation time
        $events = TestCaseEvent::where('session_id', $testCase->session_id)
            ->where('is_saved', true)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($events->isEmpty()) {
            return redirect()->route('test-cases.generate-cypress', [$project, $module, $testCase])
                ->with('error', 'No saved events found. Please save some events first.');
        }

        // Generate Cypress code
        $cypressCode = $this->convertEventsToCypressCode($events, $project, $module, $testCase);

        // Create filename based on test case
        $filename = $this->sanitizeFilename($testCase->name) . '.cy.js';

        // Return as downloadable file
        return response($cypressCode)
            ->header('Content-Type', 'application/javascript')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Convert events to Cypress test code
     */
    private function convertEventsToCypressCode($events, $project, $module, $testCase)
    {
        // Check if any events use xpath
        $usesXpath = false;
        foreach ($events as $event) {
            $eventData = json_decode($event->event_data, true);
            if ($eventData && isset($eventData['selectors']['xpath'])) {
                $selectors = $eventData['selectors'];
                // Check if xpath will be used (no better selector available)
                if (empty($selectors['testId']) && empty($selectors['id']) &&
                    empty($selectors['name']) && empty($selectors['ariaLabel']) &&
                    empty($selectors['placeholder'])) {
                    $usesXpath = true;
                    break;
                }
            }
        }

        $code = "// Auto-generated Cypress Test\n";
        $code .= "// Project: {$project->name}\n";
        $code .= "// Module: {$module->name}\n";
        $code .= "// Test Case: {$testCase->name}\n";
        $code .= "// Generated: " . now()->format('Y-m-d H:i:s') . "\n";

        if ($usesXpath) {
            $code .= "//\n";
            $code .= "// NOTE: This test uses XPath selectors.\n";
            $code .= "// Install cypress-xpath plugin:\n";
            $code .= "//   npm install -D cypress-xpath\n";
            $code .= "// Then add to cypress/support/e2e.js:\n";
            $code .= "//   require('cypress-xpath')\n";
            $code .= "//\n";
        }

        $code .= "\n";

        $code .= "describe('{$testCase->name}', () => {\n";

        // Get the first URL from events
        $firstUrl = null;
        foreach ($events as $event) {
            if ($event->url) {
                $firstUrl = $event->url;
                break;
            }
        }

        if ($firstUrl) {
            $code .= "  beforeEach(() => {\n";
            $code .= "    cy.visit('{$firstUrl}')\n";
            $code .= "  })\n\n";
        }

        $code .= "  it('should execute test steps', () => {\n";

        $previousUrl = $firstUrl;

        foreach ($events as $index => $event) {
            $eventData = json_decode($event->event_data, true);

            // Skip if event data is invalid
            if (!$eventData) {
                continue;
            }

            // Check if URL changed (navigation)
            if ($event->url && $event->url !== $previousUrl) {
                $code .= "    // Navigation detected\n";
                $code .= "    cy.url().should('include', '" . parse_url($event->url, PHP_URL_PATH) . "')\n";
                $previousUrl = $event->url;
            }

            // Convert event to Cypress command
            $cypressCommand = $this->eventToCypressCommand($event, $eventData);

            if ($cypressCommand) {
                $code .= $cypressCommand;
            }
        }

        $code .= "  })\n";
        $code .= "})\n";

        return $code;
    }

    /**
     * Convert a single event to Cypress command
     */
    private function eventToCypressCommand($event, $eventData)
    {
        $eventType = strtolower($event->event_type);

        // Handle navigation events
        if ($eventType === 'navigation') {
            $url = $eventData['url'] ?? '';
            $path = parse_url($url, PHP_URL_PATH);
            return "    // Navigation to: {$url}\n    cy.url().should('include', '{$path}')\n";
        }

        $selector = $this->getBestSelector($eventData);

        if (!$selector) {
            return "    // Unable to generate selector for {$eventType} event\n";
        }

        // Check if selector is xpath (starts with xpath()
        $isXpath = strpos($selector, 'xpath(') === 0;
        $getCommand = $isXpath ? 'cy.' . $selector : "cy.get('{$selector}')";

        $command = '';

        switch ($eventType) {
            case 'click':
                $text = $eventData['innerText'] ?? $eventData['text'] ?? '';
                $label = $eventData['selectors']['label'] ?? '';
                $comment = $label ? " // Click: {$label}" : ($text ? " // Click: " . substr($text, 0, 30) : '');

                // If clicking a link with external URL, add wait for navigation
                if (isset($eventData['targetUrl']) && $eventData['isExternal']) {
                    $command = "    {$getCommand}.click() // External link: {$eventData['targetUrl']}\n";
                    $command .= "    cy.url().should('not.equal', '" . ($eventData['pageUrl'] ?? '') . "') // Wait for navigation\n";
                } else {
                    $command = "    {$getCommand}.click(){$comment}\n";
                }
                break;

            case 'input':
                $value = $event->value ?? $eventData['value'] ?? '';
                if ($value) {
                    $escapedValue = addslashes($value);
                    $fieldName = $this->getFieldName($eventData);
                    $label = $eventData['selectors']['label'] ?? '';
                    $comment = $label ? " // Input: {$label}" : ($fieldName ? " // Input: {$fieldName}" : '');
                    $command = "    {$getCommand}.clear().type('{$escapedValue}'){$comment}\n";
                }
                break;

            case 'change':
            case 'select':
                if (isset($eventData['selectedText']) || isset($eventData['selectedValue'])) {
                    $selectValue = $eventData['selectedText'] ?? $eventData['selectedValue'] ?? '';
                    $escapedValue = addslashes($selectValue);
                    $label = $eventData['selectors']['label'] ?? '';
                    $comment = $label ? " // {$label}" : '';
                    $command = "    {$getCommand}.select('{$escapedValue}'){$comment} // Select: {$selectValue}\n";
                }
                break;

            case 'checkbox':
            case 'radio':
                if (isset($eventData['checked'])) {
                    $action = $eventData['checked'] ? 'check' : 'uncheck';
                    $label = $eventData['selectors']['label'] ?? '';
                    $fieldName = $this->getFieldName($eventData);
                    $comment = $label ? " // {$label}" : ($fieldName ? " // {$fieldName}" : '');
                    $command = "    {$getCommand}.{$action}(){$comment}\n";
                }
                break;

            case 'file':
            case 'file_upload':
                if (isset($eventData['files']) && !empty($eventData['files'])) {
                    $fileCount = count($eventData['files']);
                    $label = $eventData['selectors']['label'] ?? '';
                    $comment = $label ? " // {$label}" : '';
                    
                    if ($fileCount === 1) {
                        $fileName = $eventData['files'][0]['name'];
                        $fileType = $eventData['files'][0]['type'] ?? '';
                        $fileSize = isset($eventData['files'][0]['size']) ? round($eventData['files'][0]['size'] / 1024, 2) . 'KB' : '';
                        $escapedFileName = addslashes($fileName);
                        
                        $command = "    // Upload file: {$fileName} ({$fileType}, {$fileSize}){$comment}\n";
                        $command .= "    {$getCommand}.selectFile('cypress/fixtures/{$escapedFileName}')\n";
                    } else {
                        $fileNames = array_map(function($file) { return $file['name']; }, $eventData['files']);
                        $filesStr = implode(', ', array_map('addslashes', $fileNames));
                        
                        $command = "    // Upload {$fileCount} files: {$filesStr}{$comment}\n";
                        $command .= "    {$getCommand}.selectFile([\n";
                        
                        foreach ($eventData['files'] as $index => $file) {
                            $escapedFileName = addslashes($file['name']);
                            $comma = ($index < $fileCount - 1) ? ',' : '';
                            $command .= "      'cypress/fixtures/{$escapedFileName}'{$comma}\n";
                        }
                        
                        $command .= "    ])\n";
                    }
                } else if (isset($eventData['fileNames']) && !empty($eventData['fileNames'])) {
                    // Legacy support for old format
                    $files = implode(', ', array_map('addslashes', $eventData['fileNames']));
                    $label = $eventData['selectors']['label'] ?? '';
                    $comment = $label ? " // {$label}" : '';
                    $command = "    // File upload: {$files}{$comment}\n";
                    $command .= "    {$getCommand}.selectFile('cypress/fixtures/your-file')\n";
                }
                break;

            case 'submit':
            case 'form_submit':
                $command = "    {$getCommand}.submit() // Form submission\n";
                break;

            default:
                $command = "    // {$eventType}: {$selector}\n";
        }

        return $command;
    }

    /**
     * Get best selector based on priority: testId > id > name > ariaLabel > placeholder > label > xpath
     */
    private function getBestSelector($eventData)
    {
        $selectors = $eventData['selectors'] ?? [];

        // Priority order - Stable selectors first
        if (!empty($selectors['testId'])) {
            return '[data-testid="' . addslashes($selectors['testId']) . '"]';
        }

        if (!empty($selectors['id'])) {
            return '#' . addslashes($selectors['id']);
        }

        if (!empty($selectors['name'])) {
            return '[name="' . addslashes($selectors['name']) . '"]';
        }

        if (!empty($selectors['ariaLabel'])) {
            return '[aria-label="' . addslashes($selectors['ariaLabel']) . '"]';
        }

        if (!empty($selectors['placeholder'])) {
            return '[placeholder="' . addslashes($selectors['placeholder']) . '"]';
        }

        // Try label (new addition)
        if (!empty($selectors['label'])) {
            // Labels are usually used for form fields, try to find by label text
            // This is a heuristic approach
            return null; // We'll use cypressSelector or xpath instead
        }

        // Fall back to cypressSelector (already contains preferred selector)
        if (!empty($eventData['cypressSelector'])) {
            // If cypressSelector contains xpath, format it for Cypress xpath plugin
            $cypressSelector = $eventData['cypressSelector'];
            if (strpos($cypressSelector, '/html') === 0 || strpos($cypressSelector, '//') === 0 || strpos($cypressSelector, '//*') === 0) {
                // This is an xpath, use xpath() syntax for cypress-xpath plugin
                return "xpath('" . addslashes($cypressSelector) . "')";
            }
            return $cypressSelector;
        }

        // Fall back to xpath from selectors
        if (!empty($selectors['xpath'])) {
            // Use xpath() syntax for cypress-xpath plugin
            return "xpath('" . addslashes($selectors['xpath']) . "')";
        }

        // Last resort - use the tagName with index if available
        if (!empty($eventData['tagName'])) {
            return strtolower($eventData['tagName']);
        }

        return null;
    }

    /**
     * Get field name for better comments
     */
    private function getFieldName($eventData)
    {
        $selectors = $eventData['selectors'] ?? [];

        return $selectors['label'] ??
               $selectors['name'] ??
               $selectors['id'] ??
               $selectors['placeholder'] ??
               $selectors['ariaLabel'] ??
               null;
    }

    /**
     * Sanitize filename
     */
    private function sanitizeFilename($name)
    {
        // Replace spaces and special characters
        $name = preg_replace('/[^a-zA-Z0-9-_]/', '-', $name);
        $name = preg_replace('/-+/', '-', $name);
        $name = trim($name, '-');

        return strtolower($name);
    }

    /**
     * Import events from another test case
     */
    public function importEvents(Request $request, Project $project, Module $module, TestCase $testCase)
    {
        $request->validate([
            'source_test_case_id' => 'required|string'
        ]);

        try {
            // Decode source test case hashid
            $sourceTestCaseId = \Hashids::decode($request->source_test_case_id)[0] ?? null;
            
            if (!$sourceTestCaseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid source test case ID'
                ], 400);
            }

            // Get source test case
            $sourceTestCase = TestCase::findOrFail($sourceTestCaseId);

            // Verify source test case belongs to same project
            if ($sourceTestCase->module->project_id !== $project->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Source test case must be from the same project'
                ], 403);
            }

            // Get all saved events from source test case
            $sourceEvents = TestCaseEvent::where('session_id', $sourceTestCase->session_id)
                ->where('is_saved', true)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($sourceEvents->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No saved events found in source test case'
                ], 404);
            }

            // Copy events to current test case
            $importedCount = 0;
            foreach ($sourceEvents as $sourceEvent) {
                $newEvent = new TestCaseEvent();
                $newEvent->session_id = $testCase->session_id;
                $newEvent->event_type = $sourceEvent->event_type;
                $newEvent->selector = $sourceEvent->selector;
                $newEvent->value = $sourceEvent->value;
                $newEvent->inner_text = $sourceEvent->inner_text;
                $newEvent->tag_name = $sourceEvent->tag_name;
                $newEvent->event_data = $sourceEvent->event_data;
                $newEvent->is_saved = true; // Import as saved events
                $newEvent->save();
                
                $importedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} events",
                'imported_count' => $importedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Error importing events: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while importing events'
            ], 500);
        }
    }

    /**
     * Update a saved event
     */
    public function updateEvent(Request $request, Project $project, Module $module, TestCase $testCase, $eventId)
    {
        $request->validate([
            'selector' => 'required|string',
            'value' => 'nullable|string',
            'inner_text' => 'nullable|string',
            'tag_name' => 'nullable|string',
            'comment' => 'nullable|string'
        ]);

        try {
            $event = TestCaseEvent::where('session_id', $testCase->session_id)
                ->where('id', $eventId)
                ->where('is_saved', true)
                ->firstOrFail();

            // Update event details
            $event->selector = $request->selector;
            $event->value = $request->value;
            $event->inner_text = $request->inner_text;
            $event->tag_name = $request->tag_name;
            $event->comment = $request->comment;

            // Update event_data JSON if it exists
            if ($event->event_data) {
                $eventData = json_decode($event->event_data, true);
                if ($eventData) {
                    $eventData['cypressSelector'] = $request->selector;
                    if ($request->value) $eventData['value'] = $request->value;
                    if ($request->inner_text) $eventData['innerText'] = $request->inner_text;
                    if ($request->tag_name) $eventData['tagName'] = $request->tag_name;
                    $event->event_data = json_encode($eventData);
                }
            }

            $event->save();

            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update event'
            ], 500);
        }
    }

    /**
     * Delete a single saved event
     */
    public function deleteEvent(Project $project, Module $module, TestCase $testCase, $eventId)
    {
        try {
            $event = TestCaseEvent::where('session_id', $testCase->session_id)
                ->where('id', $eventId)
                ->where('is_saved', true)
                ->firstOrFail();

            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete event'
            ], 500);
        }
    }

    /**
     * Move event up or down in order
     */
    public function moveEvent(Request $request, Project $project, Module $module, TestCase $testCase, $eventId)
    {
        $request->validate([
            'direction' => 'required|in:up,down'
        ]);

        try {
            // Get all saved events ordered by created_at (which determines current order)
            $events = TestCaseEvent::where('session_id', $testCase->session_id)
                ->where('is_saved', true)
                ->orderBy('event_order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            // If no event_order set yet, initialize them
            $needsInitialization = $events->every(function($event) {
                return $event->event_order == 0;
            });

            if ($needsInitialization) {
                foreach ($events as $index => $event) {
                    $event->event_order = $index + 1;
                    $event->save();
                }
                $events = $events->fresh();
            }

            // Find current event and swap with adjacent
            $currentIndex = $events->search(function($event) use ($eventId) {
                return $event->id == $eventId;
            });

            if ($currentIndex === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            $direction = $request->direction;
            $swapIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

            if ($swapIndex < 0 || $swapIndex >= $events->count()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot move event in that direction'
                ], 400);
            }

            // Swap event_order values
            $currentEvent = $events[$currentIndex];
            $swapEvent = $events[$swapIndex];

            $tempOrder = $currentEvent->event_order;
            $currentEvent->event_order = $swapEvent->event_order;
            $swapEvent->event_order = $tempOrder;

            $currentEvent->save();
            $swapEvent->save();

            return response()->json([
                'success' => true,
                'message' => 'Event moved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error moving event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to move event'
            ], 500);
        }
    }
}

