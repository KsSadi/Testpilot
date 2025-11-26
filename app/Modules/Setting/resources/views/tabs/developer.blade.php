<div class="settings-content-area">
    <form action="{{ route('settings.update.developer') }}" method="POST" class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        @csrf
        
        <div class="space-y-6">
            {{-- Application Settings --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-code text-cyan-500 mr-2"></i>Application Settings
                </h3>
                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="app_debug" value="1" {{ old('app_debug', $developerSettings->where('key', 'app_debug')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Debug Mode</span>
                            <p class="text-xs text-red-500">⚠️ WARNING: Never enable in production!</p>
                        </span>
                    </label>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application URL <span class="text-red-500">*</span></label>
                        <input type="url" name="app_url" value="{{ old('app_url', $developerSettings->where('key', 'app_url')->first()->value ?? config('app.url')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="https://example.com">
                    </div>
                </div>
            </div>

            {{-- API Settings --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-plug text-cyan-500 mr-2"></i>API Configuration
                </h3>
                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_api" value="1" {{ old('enable_api', $developerSettings->where('key', 'enable_api')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Enable API</span>
                            <p class="text-xs text-gray-500">Enable API endpoints for external access</p>
                        </span>
                    </label>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">API Rate Limit (requests/minute)</label>
                        <input type="number" name="api_rate_limit" value="{{ old('api_rate_limit', $developerSettings->where('key', 'api_rate_limit')->first()->value ?? 60) }}" min="1" class="w-full md:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    </div>
                </div>
            </div>

            {{-- Webhook Settings --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-link text-cyan-500 mr-2"></i>Webhooks
                </h3>
                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_webhooks" value="1" {{ old('enable_webhooks', $developerSettings->where('key', 'enable_webhooks')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Enable Webhooks</span>
                            <p class="text-xs text-gray-500">Allow webhook notifications to external services</p>
                        </span>
                    </label>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
                        <input type="url" name="webhook_url" value="{{ old('webhook_url', $developerSettings->where('key', 'webhook_url')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="https://example.com/webhook">
                    </div>
                </div>
            </div>

            {{-- Feature Toggles --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-toggle-on text-cyan-500 mr-2"></i>Feature Toggles
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_analytics" value="1" {{ old('enable_analytics', $developerSettings->where('key', 'enable_analytics')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Enable Analytics Module</span>
                    </label>

                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_chat" value="1" {{ old('enable_chat', $developerSettings->where('key', 'enable_chat')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Enable Chat Module</span>
                    </label>
                </div>
            </div>

            {{-- Warning Box --}}
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-amber-600 mt-0.5 mr-3"></i>
                    <div class="text-sm text-amber-800">
                        <p class="font-medium mb-1">Developer Settings</p>
                        <p>These settings affect core application behavior. Changes may require application restart to take effect. Always test in a development environment first.</p>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Developer Options
                </button>
            </div>
        </div>
    </form>
</div>
