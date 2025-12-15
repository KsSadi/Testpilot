<?php

namespace App\Modules\Cypress\Services\BrowserAutomation;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecordingSessionService
{
    protected $serviceUrl;
    protected $timeout;

    public function __construct()
    {
        $this->serviceUrl = config('services.browser_automation.url', 'http://localhost:3031');
        $this->timeout = config('services.browser_automation.timeout', 30);
    }

    /**
     * Start a recording session
     * Calls Node.js service to launch browser
     */
    public function startRecording(string $sessionId, string $url, string $testCaseId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->serviceUrl}/start", [
                    'sessionId' => $sessionId,
                    'url' => $url,
                    'testCaseId' => $testCaseId
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Browser automation service error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to start browser: ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Browser automation connection failed', [
                'error' => $e->getMessage(),
                'service_url' => $this->serviceUrl
            ]);

            return [
                'success' => false,
                'message' => 'Cannot connect to browser automation service. Make sure it\'s running: npm run recorder'
            ];
        }
    }

    /**
     * Stop a recording session
     * Closes browser and retrieves events
     */
    public function stopRecording(string $sessionId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->serviceUrl}/stop", [
                    'sessionId' => $sessionId
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Failed to stop recording'
            ];

        } catch (\Exception $e) {
            Log::error('Stop recording failed', [
                'error' => $e->getMessage(),
                'sessionId' => $sessionId
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get events from a session
     */
    public function getEvents(string $sessionId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->serviceUrl}/events/{$sessionId}");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Session not found'
            ];

        } catch (\Exception $e) {
            Log::error('Get events failed', [
                'error' => $e->getMessage(),
                'sessionId' => $sessionId
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all active sessions
     */
    public function getActiveSessions(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->serviceUrl}/sessions");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'sessions' => []
            ];

        } catch (\Exception $e) {
            Log::error('Get sessions failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'sessions' => []
            ];
        }
    }

    /**
     * Check if browser automation service is running
     */
    public function checkHealth(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->serviceUrl}/health");

            return $response->successful();

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get service status with details
     */
    public function getServiceStatus(): array
    {
        try {
            $health = $this->checkHealth();
            $sessions = $health ? $this->getActiveSessions() : ['sessions' => []];

            return [
                'running' => $health,
                'url' => $this->serviceUrl,
                'activeSessions' => $sessions['sessions'] ?? [],
                'sessionCount' => count($sessions['sessions'] ?? [])
            ];

        } catch (\Exception $e) {
            return [
                'running' => false,
                'url' => $this->serviceUrl,
                'activeSessions' => [],
                'sessionCount' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}
