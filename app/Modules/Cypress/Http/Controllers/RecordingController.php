<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cypress\Models\TestCase;
use App\Modules\Cypress\Models\TestCaseEvent;
use App\Modules\Cypress\Models\GeneratedCode;
use App\Modules\Cypress\Models\EventSession;
use App\Modules\Cypress\Services\BrowserAutomation\RecordingSessionService;
use App\Modules\Cypress\Services\CodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use App\Modules\AI\Facades\AI;

class RecordingController extends Controller
{
    protected $recordingService;
    protected $codeGenerator;

    public function __construct(RecordingSessionService $recordingService, CodeGeneratorService $codeGenerator)
    {
        $this->recordingService = $recordingService;
        $this->codeGenerator = $codeGenerator;
    }

    /**
     * Start a new recording session
     * Launches browser automatically
     */
    public function start(Request $request, $project, $module, $testCase)
    {
        Log::info('Recording start requested', [
            'url' => $request->url,
            'test_case_id' => $request->test_case_id,
            'project' => $project,
            'module' => $module,
            'testCase' => $testCase
        ]);

        $request->validate([
            'url' => 'required|url',
            'test_case_id' => 'required|string'
        ]);

        try {
            $testCaseId = $request->test_case_id;
            $url = $request->url;

            // Generate unique session ID
            $sessionId = Str::uuid()->toString();
            Log::info('Generated session ID', ['sessionId' => $sessionId]);

            // Call Node.js service to launch browser
            Log::info('Calling Node.js service to start recording');
            $response = $this->recordingService->startRecording($sessionId, $url, $testCaseId);
            Log::info('Node.js service response', ['response' => $response]);

            if (!$response['success']) {
                Log::error('Node.js service returned error', ['response' => $response]);
                return response()->json([
                    'success' => false,
                    'message' => $response['message'] ?? 'Failed to start recording'
                ], 500);
            }

            Log::info('Recording started successfully', ['sessionId' => $sessionId]);
            
            // Get the app URL for secure WebSocket connection
            $appUrl = config('app.url');
            $wsProtocol = str_starts_with($appUrl, 'https') ? 'wss' : 'ws';
            $appHost = parse_url($appUrl, PHP_URL_HOST);
            
            // Build response with VNC info if available
            $jsonResponse = [
                'success' => true,
                'message' => $response['message'] ?? 'Browser launched! Start interacting with the website.',
                'sessionId' => $sessionId,
                // Use secure WebSocket through Nginx proxy
                'wsUrl' => "{$wsProtocol}://{$appHost}/recorder-ws/{$sessionId}",
                'browserLaunched' => true
            ];
            
            // Include VNC viewer URL if VNC is enabled (for VPS)
            if (isset($response['vncEnabled']) && $response['vncEnabled']) {
                $jsonResponse['vncEnabled'] = true;
                // Use HTTPS URL through Nginx proxy for VNC viewer
                $jsonResponse['viewerUrl'] = "{$appUrl}/vnc/vnc.html?autoconnect=true";
                $jsonResponse['message'] = 'Browser launched! Click the link below to view and interact with the browser.';
            }
            
            return response()->json($jsonResponse);

        } catch (\Exception $e) {
            Log::error('Recording start failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to start recording: ' . $e->getMessage(),
                'hint' => 'Make sure Node.js service is running: npm run recorder'
            ], 500);
        }
    }

    /**
     * Stop recording session
     * Closes browser and retrieves all events
     */
    public function stop(Request $request, $project, $module, $testCase)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $sessionId = $request->session_id;

            // Call Node.js service to stop recording
            $response = $this->recordingService->stopRecording($sessionId);

            if (!$response['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to stop recording'
                ], 500);
            }

            $events = $response['events'] ?? [];

            return response()->json([
                'success' => true,
                'message' => 'Recording stopped successfully',
                'eventsCount' => count($events),
                'events' => $events
            ]);

        } catch (\Exception $e) {
            Log::error('Recording stop failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop recording: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get events from active session
     */
    public function getEvents($project, $module, $testCase, $sessionId)
    {
        try {
            $response = $this->recordingService->getEvents($sessionId);

            if (!$response['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Get events failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get events'
            ], 500);
        }
    }

    /**
     * Generate Cypress code from events
     */
    public function generateCode(Request $request, $project, $module, $testCase)
    {
        Log::info('Generate code called', [
            'session_id' => $request->session_id,
            'project' => $project,
            'module' => $module,
            'testCase' => $testCase
        ]);

        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $sessionId = $request->session_id;

            // Get events from Node.js service
            Log::info('Fetching events from Node.js', ['sessionId' => $sessionId]);
            $response = $this->recordingService->getEvents($sessionId);
            Log::info('Events response', ['response' => $response]);

            if (!$response['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            $events = $response['events'] ?? [];
            Log::info('Generating code from events', ['eventCount' => count($events)]);

            // Generate Cypress code
            $code = $this->codeGenerator->generateFromEvents($events);
            Log::info('Code generated successfully', ['codeLength' => strlen($code)]);

            return response()->json([
                'success' => true,
                'code' => $code,
                'eventsCount' => count($events)
            ]);

        } catch (\Exception $e) {
            Log::error('Code generation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save generated code to test case
     */
    public function saveCode(Request $request, $project, $module, TestCase $testCase)
    {
        $request->validate([
            'code' => 'required|string',
            'session_id' => 'nullable|string',
            'events' => 'nullable|array'
        ]);

        try {
            Log::info('Saving code to test case', [
                'test_case_id' => $testCase->id,
                'test_case_hash' => $testCase->hash_id,
                'code_length' => strlen($request->code),
                'session_id' => $request->session_id,
                'events_count' => $request->has('events') ? count($request->events) : 0
            ]);

            // Check permission (route model binding already filtered by created_by)
            if ($testCase->created_by !== auth()->id()) {
                Log::warning('Unauthorized save attempt', [
                    'test_case_id' => $testCase->id,
                    'user_id' => auth()->id(),
                    'created_by' => $testCase->created_by
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Append or replace code
            if ($request->has('append') && $request->append) {
                $testCase->generated_code = ($testCase->generated_code ?? '') . "\n\n" . $request->code;
            } else {
                $testCase->generated_code = $request->code;
            }

            $testCase->save();
            
            // Save captured events if provided
            $savedEventsCount = 0;
            if ($request->has('events') && is_array($request->events)) {
                // Clear existing events for this session
                \App\Modules\Cypress\Models\TestCaseEvent::where('session_id', $testCase->session_id)->delete();
                
                foreach ($request->events as $index => $event) {
                    \App\Modules\Cypress\Models\TestCaseEvent::create([
                        'session_id' => $testCase->session_id,
                        'event_type' => $event['type'] ?? 'unknown',
                        'selector' => $event['selector'] ?? null,
                        'tag_name' => $event['tagName'] ?? null,
                        'url' => $event['url'] ?? null,
                        'value' => $event['value'] ?? null,
                        'inner_text' => $event['text'] ?? null,
                        'attributes' => $event['attributes'] ?? null,
                        'event_data' => $event,
                        'is_saved' => true
                    ]);
                    $savedEventsCount++;
                }
                
                Log::info('Saved events to database', [
                    'test_case_id' => $testCase->id,
                    'events_count' => $savedEventsCount
                ]);
            }
            
            Log::info('Code saved successfully', [
                'test_case_id' => $testCase->id,
                'events_saved' => $savedEventsCount
            ]);

            // Stop the recording session if provided
            if ($request->session_id) {
                $this->recordingService->stopRecording($request->session_id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Code saved successfully',
                'testCase' => [
                    'id' => $testCase->hash_id,
                    'name' => $testCase->name,
                    'code_length' => strlen($testCase->generated_code),
                    'events_saved' => $savedEventsCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Save code failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear saved generated code from test case
     */
    public function clearCode(Request $request, $project, $module, TestCase $testCase)
    {
        try {
            // Check permission
            if ($testCase->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $testCase->generated_code = null;
            $testCase->save();

            Log::info('Generated code cleared', ['test_case_id' => $testCase->id]);

            return response()->json([
                'success' => true,
                'message' => 'Code cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Clear code failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if Node.js service is running
     */
    public function healthCheck()
    {
        try {
            $health = $this->recordingService->checkHealth();

            return response()->json([
                'success' => true,
                'serviceStatus' => $health ? 'running' : 'stopped',
                'message' => $health ? 'Browser automation service is running' : 'Service is not running. Run: npm run recorder'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'serviceStatus' => 'error',
                'message' => 'Service check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save events as a NEW VERSION (versioned event sessions)
     * Each save creates a new event session version
     */
    public function saveEventsOnly(Request $request, $project, $module, TestCase $testCase)
    {
        $request->validate([
            'events' => 'required|array|min:1',
            'session_id' => 'nullable|string'
        ]);

        try {
            // Check permission
            if ($testCase->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Create a new event session (versioned)
            $nextVersion = EventSession::getNextVersion($testCase->id);
            $eventSession = EventSession::create([
                'test_case_id' => $testCase->id,
                'session_uuid' => Str::uuid()->toString(),
                'name' => null, // Can be set later by user
                'version' => $nextVersion,
                'events_count' => count($request->events),
                'recorded_at' => Carbon::now(),
            ]);

            $savedEventsCount = 0;
            foreach ($request->events as $index => $event) {
                TestCaseEvent::create([
                    'session_id' => $testCase->session_id,
                    'event_session_id' => $eventSession->id,
                    'event_type' => $event['type'] ?? 'unknown',
                    'selector' => $event['selector'] ?? null,
                    'tag_name' => $event['tagName'] ?? null,
                    'url' => $event['url'] ?? null,
                    'value' => $event['value'] ?? null,
                    'inner_text' => $event['text'] ?? null,
                    'attributes' => $event['attributes'] ?? null,
                    'event_data' => $event,
                    'is_saved' => true
                ]);
                $savedEventsCount++;
            }

            Log::info('Events saved as new version', [
                'test_case_id' => $testCase->id,
                'event_session_id' => $eventSession->id,
                'version' => $nextVersion,
                'events_count' => $savedEventsCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Events saved as Session v{$nextVersion}",
                'events_saved' => $savedEventsCount,
                'event_session' => [
                    'id' => $eventSession->hash_id,
                    'version' => $nextVersion,
                    'version_label' => $eventSession->version_label,
                    'recorded_at' => $eventSession->formatted_recorded_at,
                    'events_count' => $savedEventsCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Save events failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save events: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the Code Generator page with event session versions
     */
    public function codeGeneratorPage(Request $request, $project, $module, TestCase $testCase)
    {
        // Decode hash_ids to get actual IDs
        $projectId = Hashids::decode($project)[0] ?? null;
        $moduleId = Hashids::decode($module)[0] ?? null;
        
        if (!$projectId || !$moduleId) {
            abort(404, 'Project or Module not found');
        }
        
        // Load the project and module models
        $projectModel = \App\Modules\Cypress\Models\Project::findOrFail($projectId);
        $moduleModel = \App\Modules\Cypress\Models\Module::findOrFail($moduleId);
        
        // Get all event sessions for this test case (versioned)
        $eventSessions = EventSession::where('test_case_id', $testCase->id)
            ->orderBy('version', 'desc')
            ->with('events')
            ->get();
        
        // Get the selected session (from query param or latest)
        $selectedSessionId = $request->query('session');
        $selectedSession = null;
        
        if ($selectedSessionId) {
            $decoded = Hashids::decode($selectedSessionId);
            if (!empty($decoded)) {
                $selectedSession = EventSession::find($decoded[0]);
            }
        }
        
        // Default to latest session if none selected
        if (!$selectedSession && $eventSessions->isNotEmpty()) {
            $selectedSession = $eventSessions->first();
        }
        
        // Get events for selected session
        $events = $selectedSession 
            ? $selectedSession->events()->orderBy('created_at', 'asc')->get()
            : collect();

        // Get all generated code versions
        $generatedCodes = $testCase->generatedCodes()->latest('generated_at')->get();

        return view('Cypress::test-cases.code-generator', [
            'project' => $projectModel,
            'module' => $moduleModel,
            'testCase' => $testCase,
            'eventSessions' => $eventSessions,
            'selectedSession' => $selectedSession,
            'events' => $events,
            'generatedCodes' => $generatedCodes
        ]);
    }

    /**
     * Generate code from selected event session and store as new version
     */
    public function generateAndStoreCode(Request $request, $project, $module, TestCase $testCase)
    {
        try {
            // Check permission
            if ($testCase->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Get event session if specified
            $eventSessionId = $request->input('event_session_id');
            $eventSession = null;
            
            if ($eventSessionId) {
                $decoded = Hashids::decode($eventSessionId);
                if (!empty($decoded)) {
                    $eventSession = EventSession::find($decoded[0]);
                }
            }
            
            // Get events from session or fall back to legacy
            if ($eventSession) {
                $events = $eventSession->events()->orderBy('created_at', 'asc')->get();
            } else {
                // Legacy: get from session_id
                $events = TestCaseEvent::where('session_id', $testCase->session_id)
                    ->where('is_saved', true)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No saved events to generate code from'
                ], 400);
            }

            // Convert events to array format for code generator
            $eventsArray = $events->map(function ($event) {
                $data = is_string($event->event_data) ? json_decode($event->event_data, true) : $event->event_data;
                return $data ?: [
                    'type' => $event->event_type,
                    'selector' => $event->selector,
                    'url' => $event->url,
                    'value' => $event->value,
                    'text' => $event->inner_text,
                ];
            })->toArray();

            // Generate Cypress code
            $code = $this->codeGenerator->generateFromEvents($eventsArray);

            // Store as new version (linked to event session if available)
            $generatedCode = GeneratedCode::create([
                'test_case_id' => $testCase->id,
                'event_session_id' => $eventSession?->id,
                'code' => $code,
                'generated_at' => Carbon::now()
            ]);

            Log::info('Code generated and stored', [
                'test_case_id' => $testCase->id,
                'generated_code_id' => $generatedCode->id,
                'event_session_id' => $eventSession?->id,
                'events_count' => count($eventsArray)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Code generated successfully' . ($eventSession ? " from {$eventSession->version_label}" : ''),
                'generated_code' => [
                    'id' => $generatedCode->hash_id,
                    'code' => $code,
                    'version_label' => $generatedCode->version_label,
                    'generated_at' => $generatedCode->formatted_generated_at,
                    'event_session' => $eventSession ? $eventSession->version_label : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Generate and store code failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific generated code version
     */
    public function deleteGeneratedCode(Request $request, $project, $module, $testCase, string $generatedCodeHash)
    {
        try {
            // Resolve hash to model
            $decoded = \Hashids::decode($generatedCodeHash);
            if (empty($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid code reference'
                ], 404);
            }
            
            $generatedCode = GeneratedCode::find($decoded[0]);
            if (!$generatedCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code version not found'
                ], 404);
            }

            // Check permission through test case
            $testCaseModel = $generatedCode->testCase;
            if (!$testCaseModel || $testCaseModel->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $generatedCode->delete();

            Log::info('Generated code version deleted', [
                'generated_code_id' => $generatedCode->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Code version deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete generated code failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an event session (and its events)
     */
    public function deleteEventSession(Request $request, $project, $module, $testCase, string $eventSessionHash)
    {
        try {
            // Resolve hash to model
            $decoded = Hashids::decode($eventSessionHash);
            if (empty($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session reference'
                ], 404);
            }
            
            $eventSession = EventSession::find($decoded[0]);
            if (!$eventSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event session not found'
                ], 404);
            }

            // Check permission through test case
            $testCaseModel = $eventSession->testCase;
            if (!$testCaseModel || $testCaseModel->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $version = $eventSession->version;
            $eventSession->delete(); // Cascades to events

            Log::info('Event session deleted', [
                'event_session_id' => $decoded[0],
                'version' => $version
            ]);

            return response()->json([
                'success' => true,
                'message' => "Session v{$version} deleted successfully"
            ]);

        } catch (\Exception $e) {
            Log::error('Delete event session failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Polish existing basic code with AI
     * This preserves correct cy.origin() handling while optimizing the code
     */
    public function polishWithAI(Request $request, $project, $module, TestCase $testCase)
    {
        try {
            // Check permission
            if ($testCase->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $basicCode = $request->input('basic_code');
            $codeHashId = $request->input('code_hash_id');

            if (empty($basicCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No code provided to polish'
                ], 400);
            }

            Log::info('Polishing code with AI', [
                'test_case_id' => $testCase->id,
                'code_hash_id' => $codeHashId,
                'code_length' => strlen($basicCode)
            ]);

            // Build the polish prompt
            $prompt = $this->buildPolishPrompt($testCase, $basicCode);

            // Call AI Service
            $aiResponse = AI::withTemperature(0.2) // Lower temperature for more consistent output
                ->withMaxTokens(16000)
                ->generateCode($prompt, 'javascript', [
                    'system_prompt' => $this->getPolishSystemPrompt()
                ]);

            if (!$aiResponse['success'] ?? false) {
                Log::error('AI polish failed', ['response' => $aiResponse]);
                return response()->json([
                    'success' => false,
                    'message' => $aiResponse['error'] ?? 'AI service error'
                ], 500);
            }

            // Extract code from AI response
            $polishedCode = $this->extractCodeFromAIResponse($aiResponse['content'] ?? '');

            if (empty($polishedCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI generated empty response'
                ], 500);
            }

            // Ensure code is complete
            $polishedCode = $this->ensureCompleteCode($polishedCode);

            // Get the original generated code to link event session
            $originalCode = null;
            if ($codeHashId) {
                $decoded = Hashids::decode($codeHashId);
                if (!empty($decoded)) {
                    $originalCode = GeneratedCode::find($decoded[0]);
                }
            }

            // Store as new AI-polished version
            $generatedCode = GeneratedCode::create([
                'test_case_id' => $testCase->id,
                'event_session_id' => $originalCode?->event_session_id,
                'code' => $polishedCode,
                'is_ai_generated' => true,
                'generated_at' => Carbon::now()
            ]);

            Log::info('AI polished code stored', [
                'test_case_id' => $testCase->id,
                'generated_code_id' => $generatedCode->id,
                'tokens_used' => $aiResponse['usage']['total_tokens'] ?? 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Code polished with AI successfully!',
                'generated_code' => [
                    'id' => $generatedCode->hash_id,
                    'code' => $polishedCode,
                    'version_label' => $generatedCode->version_label . ' (AI Polished)',
                    'generated_at' => $generatedCode->formatted_generated_at,
                    'ai_generated' => true
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI polish failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'AI polish failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build the prompt for AI polish (optimizing existing code)
     */
    protected function buildPolishPrompt(TestCase $testCase, string $basicCode): string
    {
        return <<<PROMPT
⚠️⚠️⚠️ CRITICAL WARNING ⚠️⚠️⚠️
If you see cy.origin() blocks in the code below, you MUST preserve them EXACTLY.
Changing cy.origin() will cause "USERNAME is not defined" errors.
Copy/paste cy.origin() blocks WITHOUT any changes!
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️

Polish and optimize this existing Cypress E2E test code following **Cypress Industry Best Practices**.
The code is already working - make it production-ready, maintainable, and following professional standards.

**Test Case:** {$testCase->name}
**Description:** {$testCase->description}

**EXISTING WORKING CODE TO POLISH:**
```javascript
{$basicCode}
```

**═══════════════════════════════════════════════════════════════════════**
**⚠️ CRITICAL: cy.origin() PATTERN - NEVER CHANGE THIS! ⚠️**
**═══════════════════════════════════════════════════════════════════════**

If you see this pattern in the original code, PRESERVE IT EXACTLY:

**CORRECT PATTERN (from original code):**
```javascript
const USERNAME = 'user@test.com';
const PASSWORD = 'secret123';

cy.origin('https://auth.site.com', { args: { USERNAME, PASSWORD } }, ({ USERNAME, PASSWORD }) => {
    cy.get('#identifier').type(USERNAME);
    cy.get('#password').type(PASSWORD);
});
```

**WHAT YOU MUST PRESERVE:**
✅ Keep `const USERNAME = 'user@test.com';` BEFORE cy.origin()
✅ Keep `{ args: { USERNAME, PASSWORD } }` in cy.origin()
✅ Keep `({ USERNAME, PASSWORD }) =>` destructuring
✅ DO NOT add `const USERNAME =` inside the cy.origin() callback

**WRONG - WILL CAUSE ERROR:**
```javascript
// ❌ WRONG: Removed args
cy.origin('https://auth.site.com', () => {
    cy.get('#identifier').type(USERNAME); // ERROR: USERNAME not defined
});

// ❌ WRONG: Redeclared variable inside
cy.origin('https://auth.site.com', { args: { USERNAME } }, ({ USERNAME }) => {
    const USERNAME = 'user@test.com'; // ERROR: already declared
});

// ❌ WRONG: Missing destructuring
cy.origin('https://auth.site.com', { args: { USERNAME } }, () => {
    cy.get('#identifier').type(USERNAME); // ERROR: USERNAME not defined
});
```

**═══════════════════════════════════════════════════════════════════════**

**OTHER CRITICAL RULES - DO NOT BREAK:**
1. PRESERVE all cy.origin() blocks EXACTLY - copy/paste them unchanged
2. PRESERVE all variable passing via args: {} - this is mandatory for Cypress
3. PRESERVE the overall test logic and flow
4. DO NOT move variables inside cy.origin() if they're passed via args
5. DO NOT remove any cy.origin() blocks
6. DO NOT declare variables that are already declared
7. DO NOT use 'baseUrl' as a variable name - use APP_URL or BASE_URL instead
8. **DO NOT add new URL assertions** - only keep URL assertions that exist in the original code
9. **DO NOT change existing URL paths** - if code has '/dashboard', keep '/dashboard'
10. **DO NOT guess or invent assertions** - only add assertions for visible elements/states

**CRITICAL: URL ASSERTIONS**
- If the original code has `cy.url().should('include', '/dashboard')` → KEEP '/dashboard'
- DO NOT change '/dashboard' to '/bida-registration' or any other path
- DO NOT add `cy.url().should()` if it wasn't in the original code
- Only add URL assertions if the original code navigates and you can see the exact path
- When in doubt, DO NOT add URL assertions

**ENSURE ERROR HANDLER EXISTS:**
If not already present, add this at the start of describe():
```javascript
Cypress.on('uncaught:exception', (err) => {
  const ignore = ['baseUrl', 'already been declared', 'ResizeObserver', 'Script error', 'NetworkError'];
  return !ignore.some(p => err.message.includes(p));
});
```

**═══════════════════════════════════════════════════════════════════════**
**CYPRESS INDUSTRY BEST PRACTICES TO APPLY:**
**═══════════════════════════════════════════════════════════════════════**

**1. TEST STRUCTURE (AAA Pattern):**
   - Arrange: Setup/visit at top (beforeEach)
   - Act: User interactions in logical groups
   - Assert: Verify outcomes after actions

**2. SELECTOR PRIORITY (Most Stable → Least Stable):**
   - data-testid, data-cy, data-test (best - won't change)
   - #id (good - usually stable)
   - [name="..."] (good for forms)
   - [aria-label="..."] (good for accessibility)
   - .class (avoid if possible - can change)
   - tag (avoid - too generic)
   - :contains("text") (use with .first())

**3. COMMAND CHAINING:**
   - Chain related commands: cy.get().should().click()
   - Use aliases for repeated elements: cy.get('#form').as('loginForm')
   - Reference aliases: cy.get('@loginForm').find('input')

**4. ASSERTIONS:**
   - Always assert after critical actions
   - Use specific assertions: .should('have.text', 'exact') over .should('contain')
   - **ONLY preserve URL assertions from original code** - do NOT add new ones
   - **DO NOT change URL paths** - keep exact paths from original code
   - Verify element state: .should('be.visible'), .should('be.enabled')
   - Only add assertions for things you can verify from the original code

**5. WAITS (Anti-Pattern Avoidance):**
   - AVOID: cy.wait(5000) - arbitrary waits
   - USE: cy.get().should('be.visible') - implicit waits
   - USE: cy.intercept().as('api'); cy.wait('@api') - wait for API
   - OK: cy.wait(500-1000) only after cy.origin() for redirect

**6. VARIABLES & CONSTANTS:**
   - Define at top of describe(): const APP_URL = '...'
   - Use SCREAMING_SNAKE_CASE for constants
   - Group related constants together
   - Use descriptive names: LOGIN_USERNAME not USER

**7. LOGGING & DOCUMENTATION:**
   - cy.log('Step N: Description') for each major action
   - Add comments for complex logic
   - Document why, not what

**8. ERROR HANDLING:**
   - Include global uncaught:exception handler
   - Add .should('exist') before optional elements
   - Use conditional checks: cy.get('body').then((\$body) => { if (\$body.find('.modal').length) {...} })

**9. CODE ORGANIZATION:**
   - One describe() per feature/flow
   - One it() per test scenario (or comprehensive flow)
   - Use beforeEach() for common setup
   - Group related actions with comment headers

**10. PERFORMANCE:**
    - Minimize cy.wait() usage
    - Use .first() to avoid multiple element issues
    - Don't over-assert (slow tests)

**OUTPUT FORMAT:**
Return ONLY the complete polished JavaScript code.
- No markdown code fences (```)
- No explanations before or after
- Start with describe() and end with });
- Production-ready, industry-standard code
PROMPT;
    }

    /**
     * Get the system prompt for AI polish
     */
    protected function getPolishSystemPrompt(): string
    {
        return <<<SYSTEM
You are a **Senior QA Automation Engineer** specializing in Cypress E2E testing.
Your task is to POLISH existing working code into **production-ready, industry-standard** test code.

⚠️⚠️⚠️ CRITICAL RULE #1: NEVER TOUCH cy.origin() BLOCKS ⚠️⚠️⚠️
If you see cy.origin() in the code, COPY IT EXACTLY AS IS.
DO NOT remove args, DO NOT change destructuring, DO NOT modify it at all.
Breaking this rule causes "USERNAME is not defined" errors.
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️

=== YOUR EXPERTISE ===
- 10+ years of test automation experience
- Cypress official documentation contributor
- Expert in testing best practices (AAA pattern, Page Object Model concepts)
- Focus on maintainability, readability, and reliability

=== CRITICAL: DO NOT BREAK WORKING CODE ===
The code you receive is ALREADY WORKING. Improve it WITHOUT breaking anything.

**MOST COMMON MISTAKE TO AVOID:**
- DO NOT modify cy.origin() blocks in ANY way
- DO NOT remove { args: { ... } } from cy.origin()
- DO NOT remove ({ VAR1, VAR2 }) => destructuring
- DO NOT add cy.url().should('include', '/some-path') unless it's in the original code
- DO NOT change URL paths (if original has '/dashboard', keep '/dashboard')
- DO NOT guess what URLs should be - preserve exact paths from original
- DO NOT invent assertions - only enhance what exists

=== RULES FOR cy.origin() BLOCKS (ABSOLUTELY NEVER CHANGE) ===
When you see this pattern, PRESERVE IT EXACTLY:
```
cy.origin('https://site.com', { args: { VAR } }, ({ VAR }) => {
    // code using VAR
});
```

1. Keep variable declarations BEFORE cy.origin()
2. Keep { args: { VAR } } - this passes variables into cy.origin()
3. Keep ({ VAR }) => destructuring - this receives the variables
4. DO NOT add const/let inside cy.origin() for args variables
5. DO NOT remove any part of the cy.origin() structure

=== INDUSTRY STANDARDS TO APPLY ===

**1. AAA Pattern:**
   - Arrange (setup), Act (interactions), Assert (verify)

**2. Selector Priority:**
   data-testid > #id > [name] > [aria-label] > .class
   Always use .first() with :contains() or class selectors

**3. Assertions After Actions:**
   - **PRESERVE existing URL assertions** - do NOT change paths
   - **DO NOT add new cy.url() assertions** unless clearly needed
   - Verify element state after interactions (.should('be.visible'))
   - Use specific assertions (.should('have.text') over .contain())

**4. Avoid Anti-Patterns:**
   - NO arbitrary cy.wait(5000) - use implicit waits
   - NO flaky selectors (index-based, nth-child)
   - NO hard-coded values scattered in code

**5. Code Organization:**
   - Constants at top (SCREAMING_SNAKE_CASE)
   - Logical grouping with comment headers
   - cy.log() for step documentation

**6. Error Resilience:**
   - Global uncaught:exception handler
   - .should('be.visible') before interactions
   - .filter(':visible').first() for ambiguous selectors

=== OUTPUT FORMAT ===
Return ONLY executable JavaScript code.
- No markdown fences (```)
- No explanations before/after
- Complete, working code
- Start with describe(), end with });
SYSTEM;
    }

    /**
     * Generate optimized Cypress code using AI
     */
    public function generateWithAI(Request $request, $project, $module, TestCase $testCase)
    {
        try {
            // Check permission
            if ($testCase->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Get event session if specified
            $eventSessionId = $request->input('event_session_id');
            $eventSession = null;
            
            if ($eventSessionId) {
                $decoded = Hashids::decode($eventSessionId);
                if (!empty($decoded)) {
                    $eventSession = EventSession::find($decoded[0]);
                }
            }
            
            // Get events from session or fall back to legacy
            if ($eventSession) {
                $events = $eventSession->events()->orderBy('created_at', 'asc')->get();
            } else {
                // Legacy: get from session_id
                $events = TestCaseEvent::where('session_id', $testCase->session_id)
                    ->where('is_saved', true)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No saved events to generate code from'
                ], 400);
            }

            // Prepare events data for AI
            $eventsData = $events->map(function ($event) {
                $data = is_string($event->event_data) ? json_decode($event->event_data, true) : $event->event_data;
                return $data ?: [
                    'type' => $event->event_type,
                    'selector' => $event->selector,
                    'url' => $event->url,
                    'value' => $event->value,
                    'text' => $event->inner_text,
                ];
            })->toArray();

            // Build the AI prompt
            $prompt = $this->buildAIPrompt($testCase, $eventsData);

            Log::info('Generating code with AI', [
                'test_case_id' => $testCase->id,
                'event_session_id' => $eventSession?->id,
                'events_count' => count($eventsData)
            ]);

            // Call AI Service - use higher max tokens for complete code generation
            $aiResponse = AI::withTemperature(0.3)
                ->withMaxTokens(16000)
                ->generateCode($prompt, 'javascript', [
                    'system_prompt' => $this->getAISystemPrompt()
                ]);

            if (!$aiResponse['success'] ?? false) {
                Log::error('AI code generation failed', ['response' => $aiResponse]);
                return response()->json([
                    'success' => false,
                    'message' => $aiResponse['error'] ?? 'AI service error'
                ], 500);
            }

            // Extract code from AI response
            $code = $this->extractCodeFromAIResponse($aiResponse['content'] ?? '');

            if (empty($code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI generated empty response'
                ], 500);
            }

            // Check if code was truncated and fix incomplete brackets
            $code = $this->ensureCompleteCode($code);

            // Store as new version with AI badge (linked to event session)
            $generatedCode = GeneratedCode::create([
                'test_case_id' => $testCase->id,
                'event_session_id' => $eventSession?->id,
                'code' => $code,
                'is_ai_generated' => true,
                'generated_at' => Carbon::now()
            ]);

            Log::info('AI code generated and stored', [
                'test_case_id' => $testCase->id,
                'event_session_id' => $eventSession?->id,
                'generated_code_id' => $generatedCode->id,
                'tokens_used' => $aiResponse['usage']['total_tokens'] ?? 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'AI generated optimized code successfully' . ($eventSession ? " from {$eventSession->version_label}" : ''),
                'generated_code' => [
                    'id' => $generatedCode->hash_id,
                    'code' => $code,
                    'version_label' => $generatedCode->version_label . ' (AI)',
                    'generated_at' => $generatedCode->formatted_generated_at,
                    'ai_generated' => true,
                    'event_session' => $eventSession ? $eventSession->version_label : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI code generation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'AI generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build the prompt for AI code generation
     */
    protected function buildAIPrompt(TestCase $testCase, array $events): string
    {
        $eventsJson = json_encode($events, JSON_PRETTY_PRINT);
        
        return <<<PROMPT
Generate a professional, production-ready Cypress E2E test based on these recorded user interactions.

**Test Case:** {$testCase->name}
**Description:** {$testCase->description}

**Recorded Events:**
```json
{$eventsJson}
```

**CRITICAL REQUIREMENTS - CODE MUST BE COMPLETE:**
1. Generate COMPLETE code with ALL closing brackets - no truncation allowed
2. Every opening { MUST have a matching closing }
3. Every opening ( MUST have a matching closing )
4. Every describe() block MUST end with });
5. Every it() block MUST end with });
6. Include ALL steps from the recorded events
7. If the test has 50+ steps, still complete ALL of them

**CRITICAL: MUST ADD ERROR SUPPRESSION:**
At the start of describe(), ALWAYS add this global error handler:
```javascript
describe('Test Name', () => {
  // Global handler to suppress common third-party errors
  Cypress.on('uncaught:exception', (err) => {
    const ignoredPatterns = [
      'baseUrl', 'has already been declared', 'ResizeObserver',
      'Script error', 'NetworkError', 'Load failed', 'ChunkLoadError',
      'cancelled', 'TypeError', 'Cannot read prop'
    ];
    if (ignoredPatterns.some(p => err.message.includes(p))) {
      return false;
    }
    return true;
  });
  // ... rest of test
});
```

**CRITICAL: AVOID DUPLICATE VARIABLE DECLARATIONS:**
- DO NOT declare the same variable twice (const, let, var)
- DO NOT use 'baseUrl' as a variable name (it's often declared by third-party scripts)
- Use unique names like BASE_URL, LOGIN_URL, APP_URL instead
- Variables in args destructuring ARE declared - don't redeclare them inside

**CRITICAL: cy.origin() VARIABLE HANDLING:**
When using cy.origin() for cross-origin pages, you MUST pass variables via args:
- Variables defined OUTSIDE cy.origin() cannot be accessed inside
- ALWAYS pass needed variables via { args: { VAR1, VAR2 } }
- The callback MUST destructure args: ({ VAR1, VAR2 }) => { ... }
- DO NOT redeclare destructured variables with const/let inside the callback

CORRECT PATTERN:
```javascript
const USERNAME = 'user@example.com';
const PASSWORD = 'secret123';

cy.origin('https://login.example.com', { args: { USERNAME, PASSWORD } }, ({ USERNAME, PASSWORD }) => {
    // DO NOT write: const USERNAME = 'something'; // ERROR: already declared!
    cy.get('#identifier').type(USERNAME);
    cy.get('#password').type(PASSWORD);
});
```

WRONG (causes "already declared" error):
```javascript
cy.origin('https://login.example.com', { args: { USERNAME } }, ({ USERNAME }) => {
    const USERNAME = 'test'; // ERROR: Identifier 'USERNAME' has already been declared
});
```

**Code Quality Requirements:**
1. Use constants for credentials and test data at the top
2. Use clear, descriptive step logging with cy.log()
3. Add visibility checks before interactions: .should('be.visible')
4. Handle cross-origin authentication with cy.origin()
5. Use proper selectors: ID > data-testid > aria-label > CSS
6. Include beforeEach() hook for setup
7. Handle modals and dynamic elements with conditional checks
8. Use strategic waits (cy.wait()) only when necessary
9. Add meaningful comments explaining complex steps
10. Follow Cypress best practices
11. **ALWAYS add .first() after selectors that might match multiple elements**
12. **ALWAYS use .first() when using :contains() or text selectors**
13. **ALWAYS use .first() when using class-based selectors**
14. **For elements that may have hidden duplicates (mobile/desktop), use .filter(':visible').first() instead of .should('be.visible')**

**Output Format:**
Return ONLY the complete JavaScript code. Start with describe() and end with the closing });
Do NOT include markdown code fences (```).
Do NOT add explanations before or after the code.
Just pure, complete, executable Cypress test code.
PROMPT;
    }

    /**
     * Get the system prompt for AI
     */
    protected function getAISystemPrompt(): string
    {
        return <<<SYSTEM
You are an expert Cypress E2E test automation engineer. Your #1 priority is generating COMPLETE, FULLY-FUNCTIONAL test code.

=== CRITICAL RULES (MUST FOLLOW) ===
1. **CODE COMPLETENESS IS MANDATORY**
   - Every describe() block MUST end with });
   - Every it() block MUST end with });
   - Every { MUST have a matching }
   - Every ( MUST have a matching )
   - NEVER truncate code - complete ALL steps
   - Even if test has 100+ steps, write them ALL

2. **Code Structure**
   - Start with: describe('Test Name', () => {
   - Define constants at top (URLs, credentials, test data)
   - Include beforeEach() for common setup
   - Create one comprehensive it() block
   - End with: });

3. **Best Practices**
   - Add cy.log('Step X: description') for each major step
   - Use .should('be.visible') before clicking/typing ONLY for unique elements
   - **For elements with potential hidden duplicates, use .filter(':visible').first() instead of .should('be.visible')**
   - **ALWAYS add .first() after selectors that might match multiple elements**
   - **ALWAYS use .first() with :contains() selectors (e.g., cy.get('button:contains("Text")').first())**
   - **ALWAYS use .first() with class-based selectors that may have duplicates**
   - Handle cross-origin auth with cy.origin()
   - Check for optional elements with cy.get('body').then()
   - Use strategic cy.wait() for page transitions
   - Prefer IDs and data attributes for selectors
   - Add comments for complex logic

4. **MANDATORY: Global Error Handler**
   ALWAYS add this at the start of describe():
   ```
   Cypress.on('uncaught:exception', (err) => {
     const ignore = ['baseUrl', 'already been declared', 'ResizeObserver', 'Script error', 'NetworkError'];
     return !ignore.some(p => err.message.includes(p));
   });
   ```

5. **CRITICAL: No Duplicate Variables**
   - NEVER declare the same variable twice
   - NEVER use 'baseUrl' as a variable name (conflicts with third-party scripts)
   - Use unique names: BASE_URL, APP_URL, LOGIN_URL
   - Destructured args variables ARE declared - don't redeclare inside callback

6. **CRITICAL: cy.origin() Variable Passing**
   - Variables from outside cy.origin() CANNOT be accessed inside
   - ALWAYS pass variables via args: { args: { VAR1, VAR2 } }
   - ALWAYS destructure in callback: ({ VAR1, VAR2 }) => { ... }
   - DO NOT redeclare destructured variables inside the callback!
   
   CORRECT:
   const USERNAME = 'test';
   cy.origin('https://auth.site.com', { args: { USERNAME } }, ({ USERNAME }) => {
       // USERNAME is already declared via destructuring - just use it!
       cy.get('#user').type(USERNAME);
   });
   
   WRONG (causes "already declared" error):
   cy.origin('https://auth.site.com', { args: { USERNAME } }, ({ USERNAME }) => {
       const USERNAME = 'test'; // ERROR: already declared!
   });

7. **Output Format**
   - Return ONLY executable JavaScript code
   - NO markdown code fences
   - NO explanations or comments outside the code
   - NO placeholder text like "... rest of code ..."
   - Just complete, working Cypress test code

=== REQUIRED STRUCTURE ===
describe('Test Name', () => {
  // MUST include: Global error handler
  Cypress.on('uncaught:exception', (err) => {
    const ignore = ['baseUrl', 'already been declared', 'ResizeObserver', 'Script error'];
    return !ignore.some(p => err.message.includes(p));
  });

  const APP_URL = 'https://example.com'; // Use APP_URL not baseUrl!
  
  beforeEach(() => {
    cy.visit(APP_URL);
  });
  
  it('should complete the flow', () => {
    cy.log('Step 1: Click button');
    cy.get('button.btn:contains("Click Me")').filter(':visible').first().click();
    // ... ALL steps ...
  });
});

IMPORTANT SELECTOR RULES:
- If selector is an ID (starts with #), use .should('be.visible') - IDs are unique
- If selector uses :contains(), use .filter(':visible').first() - text may appear multiple times
- If selector uses class names only, use .filter(':visible').first() - classes may have duplicates
- If element may have mobile/desktop versions, ALWAYS use .filter(':visible').first()
- If selector might match multiple elements, ALWAYS add .first() or .filter(':visible').first()

Remember: Code must be 100% complete and ready to run. No truncation allowed.
SYSTEM;
    }

    /**
     * Extract clean code from AI response
     */
    protected function extractCodeFromAIResponse(string $response): string
    {
        // Remove markdown code blocks if present
        $code = $response;
        
        // Remove ```javascript or ```js markers
        $code = preg_replace('/^```(?:javascript|js|cypress)?\s*\n?/m', '', $code);
        $code = preg_replace('/\n?```\s*$/m', '', $code);
        
        // Trim whitespace
        $code = trim($code);
        
        // Ensure it starts with describe or it block
        if (!preg_match('/^(describe|it|context)\s*\(/', $code)) {
            // Try to find the start of the test code
            if (preg_match('/(describe\s*\([\'"].*?[\'"]\s*,\s*\(\)\s*=>\s*\{.*)/s', $code, $matches)) {
                $code = $matches[1];
            }
        }
        
        return $code;
    }

    /**
     * Ensure generated code is complete with proper closing brackets
     * This fixes truncated AI responses
     */
    protected function ensureCompleteCode(string $code): string
    {
        // Count opening and closing braces/parentheses
        $openBraces = substr_count($code, '{');
        $closeBraces = substr_count($code, '}');
        $openParens = substr_count($code, '(');
        $closeParens = substr_count($code, ')');

        // Check if code appears truncated (missing closings)
        $missingBraces = $openBraces - $closeBraces;
        $missingParens = $openParens - $closeParens;

        if ($missingBraces > 0 || $missingParens > 0) {
            Log::warning('AI generated truncated code, auto-fixing', [
                'missing_braces' => $missingBraces,
                'missing_parens' => $missingParens
            ]);

            // Trim any incomplete line at the end
            $code = rtrim($code);
            
            // Remove incomplete last line if it doesn't end with ; or { or }
            $lines = explode("\n", $code);
            $lastLine = trim(end($lines));
            
            // If last line seems incomplete (doesn't end properly), remove it
            if (!empty($lastLine) && !preg_match('/[;{}\)]\s*$/', $lastLine)) {
                array_pop($lines);
                $code = implode("\n", $lines);
                // Recalculate after removing incomplete line
                $openBraces = substr_count($code, '{');
                $closeBraces = substr_count($code, '}');
                $openParens = substr_count($code, '(');
                $closeParens = substr_count($code, ')');
                $missingBraces = $openBraces - $closeBraces;
                $missingParens = $openParens - $closeParens;
            }

            // Add closing brackets in the correct order
            // Typically for Cypress: first close it() blocks, then describe()
            $closings = '';
            
            // Add missing });  combinations (for it/describe arrow functions)
            while ($missingBraces > 0 && $missingParens > 0) {
                $closings .= "\n  });";
                $missingBraces--;
                $missingParens--;
            }
            
            // Add any remaining braces
            while ($missingBraces > 0) {
                $closings .= "\n}";
                $missingBraces--;
            }
            
            // Add any remaining parentheses
            while ($missingParens > 0) {
                $closings .= ")";
                $missingParens--;
            }

            $code .= $closings;
        }

        return $code;
    }
}
