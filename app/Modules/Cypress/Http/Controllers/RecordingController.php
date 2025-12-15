<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cypress\Models\TestCase;
use App\Modules\Cypress\Services\BrowserAutomation\RecordingSessionService;
use App\Modules\Cypress\Services\CodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            return response()->json([
                'success' => true,
                'message' => 'Browser launched! Start interacting with the website.',
                'sessionId' => $sessionId,
                'wsUrl' => $response['wsUrl'],
                'browserLaunched' => true
            ]);

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
    public function saveCode(Request $request, $project, $module, $testCase)
    {
        $request->validate([
            'test_case_id' => 'required|string',
            'code' => 'required|string',
            'session_id' => 'required|string'
        ]);

        try {
            $testCase = TestCase::where('hash_id', $request->test_case_id)->firstOrFail();

            // Check permission
            if ($testCase->user_id !== auth()->id()) {
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

            // Stop the recording session
            $this->recordingService->stopRecording($request->session_id);

            return response()->json([
                'success' => true,
                'message' => 'Code saved successfully',
                'testCase' => $testCase
            ]);

        } catch (\Exception $e) {
            Log::error('Save code failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save code: ' . $e->getMessage()
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
}
