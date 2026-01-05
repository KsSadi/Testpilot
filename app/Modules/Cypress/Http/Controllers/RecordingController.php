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
            
            // Build response with VNC info if available
            $jsonResponse = [
                'success' => true,
                'message' => $response['message'] ?? 'Browser launched! Start interacting with the website.',
                'sessionId' => $sessionId,
                'wsUrl' => $response['wsUrl'] ?? null,
                'browserLaunched' => true
            ];
            
            // Include VNC viewer URL if VNC is enabled (for VPS)
            if (isset($response['vncEnabled']) && $response['vncEnabled']) {
                $jsonResponse['vncEnabled'] = true;
                $jsonResponse['viewerUrl'] = $response['viewerUrl'];
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
                ->withMaxTokens(8000)
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
Generate a professional, production-ready Cypress E2E test based on these recorded user interactions:

**Test Case Name:** {$testCase->name}
**Description:** {$testCase->description}

**Recorded Events:**
```json
{$eventsJson}
```

**Requirements:**
1. Generate clean, readable Cypress test code
2. Use best practices and modern Cypress patterns
3. Include proper waits and assertions
4. Handle potential flakiness with proper selectors
5. Add meaningful comments
6. Use data-testid or aria-labels when available
7. Include proper error handling
8. Make tests resilient and maintainable
9. Follow Cypress best practices for E2E testing
10. Use cy.intercept() for API calls if detected

**CRITICAL - CODE COMPLETENESS:**
- You MUST generate COMPLETE code with ALL closing brackets
- Every { MUST have a matching }
- Every ( MUST have a matching )
- Every describe() and it() block MUST be properly closed with });
- NEVER leave code truncated or incomplete
- If the test is long, still complete it fully

Generate ONLY the Cypress test code, no explanations.
PROMPT;
    }

    /**
     * Get the system prompt for AI
     */
    protected function getAISystemPrompt(): string
    {
        return <<<SYSTEM
You are an expert Cypress test automation engineer with 10+ years of experience. You write:

1. **Clean, Production-Ready Code** - No placeholder or example code
2. **Industry Best Practices** - Following Cypress official guidelines
3. **Resilient Tests** - Using proper waits, retries, and selectors
4. **Well-Documented** - With clear comments explaining the test flow
5. **Bug-Free** - Code that runs without errors
6. **COMPLETE Code** - Always finish all code blocks with proper closing brackets

Key patterns you follow:
- Use cy.get() with robust selectors (data-testid > aria-label > css)
- Avoid cy.wait() with arbitrary times when possible
- Use cy.intercept() for network requests
- Include before/beforeEach hooks for setup
- Add proper assertions for each action
- Handle dynamic content gracefully
- Use Cypress commands efficiently

CRITICAL RULES:
- ALWAYS complete all code blocks - never leave code truncated
- ALWAYS close all brackets: every { must have a matching }
- ALWAYS close all parentheses: every ( must have a matching )
- ALWAYS end describe() and it() blocks properly
- If code is getting long, still finish it completely

Output format: Return ONLY the JavaScript/Cypress code, wrapped in a describe() block. No markdown, no explanations before or after the code.
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
