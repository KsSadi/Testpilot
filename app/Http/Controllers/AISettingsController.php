<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AIProvider;
use App\Models\AISetting;
use App\Models\AIUsageLog;
use App\Services\AI\AIProviderFactory;
use Illuminate\Support\Facades\Validator;

class AISettingsController extends Controller
{
    /**
     * Display AI settings page
     */
    public function index()
    {
        $providers = AIProvider::orderBy('priority')->get();
        $activeProvider = AIProvider::getActive();
        $settings = AISetting::all()->keyBy('key');
        
        // Get usage statistics
        $stats = AIUsageLog::getStatistics(30);
        
        return view('ai.settings', compact('providers', 'activeProvider', 'settings', 'stats'));
    }

    /**
     * Update provider settings
     */
    public function updateProvider(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'api_key' => 'nullable|string',
            'default_model' => 'required|string',
            'is_enabled' => 'boolean',
            'priority' => 'required|integer|min:0',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $provider = AIProvider::findOrFail($id);
        
        $data = $request->only([
            'display_name',
            'description',
            'default_model',
            'is_enabled',
            'priority',
            'settings'
        ]);

        // Only update API key if provided
        if ($request->filled('api_key')) {
            $data['api_key'] = $request->api_key;
        }

        $provider->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Provider updated successfully',
            'provider' => $provider
        ]);
    }

    /**
     * Set active provider
     */
    public function setActive(Request $request, $id)
    {
        $provider = AIProvider::findOrFail($id);

        if (!$provider->is_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot activate a disabled provider'
            ], 400);
        }

        if (!$provider->hasValidApiKey()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot activate provider without API key'
            ], 400);
        }

        $provider->setAsActive();

        return response()->json([
            'success' => true,
            'message' => "{$provider->display_name} is now the active provider"
        ]);
    }

    /**
     * Test provider connection
     */
    public function testConnection($id)
    {
        try {
            $provider = AIProvider::findOrFail($id);
            
            if (!$provider->hasValidApiKey()) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key not configured'
                ], 400);
            }

            $result = AIProviderFactory::make($provider->name)->testConnection();

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update general settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ai_enabled' => 'required|boolean',
            'max_requests_per_day' => 'required|integer|min:1',
            'max_tokens_per_request' => 'required|integer|min:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        AISetting::set('ai_enabled', $request->ai_enabled, 'boolean');
        AISetting::set('max_requests_per_day', $request->max_requests_per_day, 'integer');
        AISetting::set('max_tokens_per_request', $request->max_tokens_per_request, 'integer');

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Get usage logs
     */
    public function usageLogs(Request $request)
    {
        $query = AIUsageLog::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->has('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(50);

        return response()->json($logs);
    }

    /**
     * Get usage statistics
     */
    public function statistics(Request $request)
    {
        $days = $request->get('days', 30);
        $stats = AIUsageLog::getStatistics($days);

        return response()->json($stats);
    }
}
