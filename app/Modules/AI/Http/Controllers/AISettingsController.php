<?php

namespace App\Modules\AI\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\AI\Models\AIProvider;
use App\Modules\AI\Models\AISetting;
use App\Modules\AI\Models\AIUsageLog;
use App\Modules\AI\Services\AIProviderFactory;
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
        
        return view('AI::settings', compact('providers', 'activeProvider', 'settings', 'stats'));
    }

    /**
     * Update provider settings
     */
    public function updateProvider(Request $request, $id)
    {
        $provider = AIProvider::findOrFail($id);
        
        // If only updating is_enabled (checkbox toggle)
        if ($request->has('is_enabled') && count($request->all()) <= 2) {
            $provider->update(['is_enabled' => $request->boolean('is_enabled')]);
            
            return response()->json([
                'success' => true,
                'message' => 'Provider status updated successfully',
                'provider' => $provider
            ]);
        }
        
        // Full form validation
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'api_base_url' => 'nullable|string|url',
            'api_key' => 'nullable|string',
            'api_keys' => 'nullable|json',
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
        
        $data = $request->only([
            'display_name',
            'description',
            'api_base_url',
            'default_model',
            'is_enabled',
            'priority',
            'settings'
        ]);

        // Only update API key if provided
        if ($request->filled('api_key')) {
            $data['api_key'] = $request->api_key;
        }

        // Handle multiple API keys
        if ($request->filled('api_keys')) {
            $apiKeys = json_decode($request->api_keys, true);
            if (is_array($apiKeys) && count($apiKeys) > 0) {
                $data['api_keys'] = $apiKeys;
                // Reset current_key_index when updating keys
                $data['current_key_index'] = 0;
            }
        }

        $provider->update($data);

        // Update pricing for each model
        if ($request->has('pricing')) {
            foreach ($request->pricing as $model => $prices) {
                if (isset($prices['input']) && isset($prices['output'])) {
                    $provider->updatePricing(
                        $model,
                        (float) $prices['input'],
                        (float) $prices['output']
                    );
                }
            }
        }

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

    /**
     * Reset API key index to start from first key
     */
    public function resetKeyIndex($id)
    {
        try {
            $provider = AIProvider::findOrFail($id);
            
            if (!is_array($provider->api_keys) || count($provider->api_keys) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This provider does not have multiple API keys configured'
                ], 400);
            }
            
            $provider->resetKeyIndex();
            
            return response()->json([
                'success' => true,
                'message' => 'API key index has been reset to the first key'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset key index: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show detailed analytics for a specific provider
     */
    public function providerDetails($id)
    {
        $provider = AIProvider::with('pricingSettings')->findOrFail($id);
        
        // Get usage statistics for this provider
        $stats = [
            'total_requests' => AIUsageLog::where('provider', $provider->name)->count(),
            'successful_requests' => AIUsageLog::where('provider', $provider->name)->where('status', 'success')->count(),
            'failed_requests' => AIUsageLog::where('provider', $provider->name)->where('status', 'error')->count(),
            'total_cost' => AIUsageLog::where('provider', $provider->name)->sum('cost'),
            'total_tokens' => AIUsageLog::where('provider', $provider->name)->sum('total_tokens'),
            'avg_response_time' => AIUsageLog::where('provider', $provider->name)->avg('response_time'),
            
            // Today's stats
            'today_requests' => AIUsageLog::where('provider', $provider->name)
                ->whereDate('created_at', today())->count(),
            'today_cost' => AIUsageLog::where('provider', $provider->name)
                ->whereDate('created_at', today())->sum('cost'),
            
            // This month's stats
            'month_requests' => AIUsageLog::where('provider', $provider->name)
                ->whereMonth('created_at', now()->month)->count(),
            'month_cost' => AIUsageLog::where('provider', $provider->name)
                ->whereMonth('created_at', now()->month)->sum('cost'),
        ];
        
        // Success rate
        $stats['success_rate'] = $stats['total_requests'] > 0 
            ? round(($stats['successful_requests'] / $stats['total_requests']) * 100, 2) 
            : 0;
        
        // Recent logs
        $recentLogs = AIUsageLog::where('provider', $provider->name)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // Daily usage for last 30 days (for chart)
        $dailyUsage = AIUsageLog::where('provider', $provider->name)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as requests, SUM(cost) as cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Feature breakdown
        $featureBreakdown = AIUsageLog::where('provider', $provider->name)
            ->selectRaw('feature, COUNT(*) as count, SUM(cost) as cost')
            ->groupBy('feature')
            ->get();
        
        return view('AI::provider-details', compact('provider', 'stats', 'recentLogs', 'dailyUsage', 'featureBreakdown'));
    }
}
