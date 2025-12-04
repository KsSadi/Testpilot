@extends('layouts.backend.master')

@section('title', 'AI Settings')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">AI Configuration</h1>
            <p class="text-gray-600 mt-2">Manage AI providers and settings</p>
        </div>
        <a href="{{ route('ai.test.playground') }}" class="bg-gradient-to-r from-purple-600 to-cyan-600 hover:from-purple-700 hover:to-cyan-700 text-white font-semibold px-6 py-3 rounded-lg transition shadow-md">
            <i class="fas fa-flask mr-2"></i>Test Playground
        </a>
    </div>

    <!-- Global Settings -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Global Settings</h2>
        <form id="globalSettingsForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" class="form-checkbox h-5 w-5 text-cyan-600" id="aiEnabled" name="ai_enabled" {{ ($settings['ai_enabled']->typed_value ?? false) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700 font-medium">Enable AI Features</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Requests Per Day (Per User)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent" id="maxRequestsPerDay" name="max_requests_per_day" value="{{ $settings['max_requests_per_day']->typed_value ?? 100 }}" min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Tokens Per Request</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent" id="maxTokensPerRequest" name="max_tokens_per_request" value="{{ $settings['max_tokens_per_request']->typed_value ?? 4000 }}" min="100">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Save Global Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Usage Statistics -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Usage Statistics (Last 30 Days)</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Requests</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_requests']) }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-network-wired text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Successful</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['successful_requests']) }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Cost</p>
                        <p class="text-2xl font-bold text-yellow-600">${{ number_format($stats['total_cost'], 4) }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-coins text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Avg Response</p>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['avg_response_time']) }}ms</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-clock text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Providers -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">AI Providers</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($providers as $provider)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden {{ $provider->is_active ? 'ring-2 ring-green-500' : '' }}" id="provider-card-{{ $provider->id }}">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $provider->display_name }}
                            @if($provider->is_active)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @endif
                        </h3>
                        <label class="flex items-center">
                            <input type="checkbox" class="form-checkbox h-5 w-5 text-cyan-600 provider-enabled" id="enabled_{{ $provider->id }}" data-provider-id="{{ $provider->id }}" {{ $provider->is_enabled ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-600">Enabled</span>
                        </label>
                    </div>
                </div>
                <div class="p-6">
                    
                    <!-- Read-Only View (Default) -->
                    <div class="provider-readonly space-y-3" id="readonly-{{ $provider->id }}">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Description</p>
                            <p class="text-sm text-gray-800 mt-1">{{ $provider->description }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Default Model</p>
                            <p class="text-sm text-gray-800 mt-1">{{ $provider->models[$provider->default_model] ?? $provider->default_model }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">API Configuration</p>
                            <p class="text-sm text-gray-800 mt-1">
                                @if(is_array($provider->api_keys) && count($provider->api_keys) > 0)
                                    <span class="text-green-600"><i class="fas fa-check-circle"></i> {{ count($provider->api_keys) }} API keys configured</span>
                                @elseif($provider->hasValidApiKey())
                                    <span class="text-green-600"><i class="fas fa-check-circle"></i> API key configured</span>
                                @else
                                    <span class="text-red-600"><i class="fas fa-times-circle"></i> No API key</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-medium">Priority</p>
                            <p class="text-sm text-gray-800 mt-1">{{ $provider->priority }}</p>
                        </div>
                        
                        <div class="pt-3 border-t border-gray-200 flex gap-2">
                            <button type="button" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-4 py-2 rounded-lg transition edit-provider" data-provider-id="{{ $provider->id }}">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <a href="{{ route('ai.providers.details', $provider->id) }}" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-lg transition text-center">
                                <i class="fas fa-chart-line mr-1"></i> Analytics
                            </a>
                        </div>
                        
                        @if(!$provider->is_active && $provider->is_enabled && $provider->hasValidApiKey())
                        <button type="button" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition activate-provider" data-provider-id="{{ $provider->id }}">
                            <i class="fas fa-check mr-1"></i> Set as Active Provider
                        </button>
                        @endif
                    </div>

                    <!-- Edit Form (Hidden by default) -->
                    <form class="provider-form space-y-4 hidden" id="edit-form-{{ $provider->id }}" data-provider-id="{{ $provider->id }}" data-display-name="{{ $provider->display_name }}">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent" name="description" rows="2">{{ $provider->description }}</textarea>
                        </div>
                        
                        <!-- API Base URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                API Base URL
                                <span class="text-xs text-gray-500 font-normal">(Optional - Leave empty for default)</span>
                            </label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent" name="api_base_url" value="{{ $provider->api_base_url }}" placeholder="https://api.example.com/v1">
                            <p class="text-xs text-gray-500 mt-1">Change API endpoint without code updates</p>
                        </div>

                        <!-- Single API Key (Legacy) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Single API Key (Legacy)</label>
                            <div class="relative">
                                <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent pr-10 api-key-input" name="api_key" placeholder="{{ $provider->hasValidApiKey() ? '••••••••••••' : 'Enter API key' }}">
                                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Leave empty to keep current key</p>
                        </div>

                        <!-- Multiple API Keys (New) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-magic text-cyan-600"></i> Multiple API Keys (Auto-Failover)
                                <span class="text-xs text-gray-500 font-normal">- One per line</span>
                            </label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent font-mono text-sm" name="api_keys" rows="4" placeholder="AIzaSyABC123-first-key&#10;AIzaSyDEF456-second-key&#10;AIzaSyGHI789-third-key">{{ is_array($provider->api_keys) ? implode("\n", $provider->api_keys) : '' }}</textarea>
                            <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
                                    <div class="text-xs text-blue-800">
                                        <strong>Auto-Failover:</strong> System automatically switches to next key when quota/rate limit is hit.
                                        @if(is_array($provider->api_keys) && count($provider->api_keys) > 0)
                                            <div class="mt-1">
                                                <span class="font-semibold">{{ count($provider->api_keys) }} keys configured</span>
                                                <span class="ml-2 text-blue-600">| Currently using: Key #{{ ($provider->current_key_index % count($provider->api_keys)) + 1 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Model</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent" name="default_model">
                                @foreach($provider->models as $modelKey => $modelName)
                                    <option value="{{ $modelKey }}" {{ $provider->default_model == $modelKey ? 'selected' : '' }}>
                                        {{ $modelName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Model Pricing Configuration -->
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-dollar-sign text-green-600"></i> Model Pricing (Per 1M Tokens)
                                </label>
                                <button type="button" class="text-xs text-cyan-600 hover:text-cyan-700 toggle-pricing" data-provider-id="{{ $provider->id }}">
                                    <i class="fas fa-chevron-down"></i> Show/Hide
                                </button>
                            </div>
                            <div class="pricing-section hidden space-y-3" id="pricing-{{ $provider->id }}">
                                @php
                                    $allPricing = $provider->getAllPricing();
                                @endphp
                                @foreach($provider->models as $modelKey => $modelName)
                                    @php
                                        $pricing = $allPricing[$modelKey] ?? ['input' => 0, 'output' => 0];
                                    @endphp
                                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                        <div class="text-xs font-medium text-gray-700 mb-2">{{ $modelName }}</div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Input $</label>
                                                <input type="number" step="0.0001" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-cyan-500" 
                                                       name="pricing[{{ $modelKey }}][input]" 
                                                       value="{{ $pricing['input'] }}" 
                                                       placeholder="0.00">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Output $</label>
                                                <input type="number" step="0.0001" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-cyan-500" 
                                                       name="pricing[{{ $modelKey }}][output]" 
                                                       value="{{ $pricing['output'] }}" 
                                                       placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-2 text-xs text-blue-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pricing is stored in database and can be updated anytime without code changes.
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority (Lower = Higher Priority)</label>
                            <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent" name="priority" value="{{ $provider->priority }}" min="0">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                                <i class="fas fa-save mr-1"></i> Save Changes
                            </button>
                            <button type="button" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg transition cancel-edit" data-provider-id="{{ $provider->id }}">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </button>
                        </div>
                        <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition test-connection" data-provider-id="{{ $provider->id }}">
                            <i class="fas fa-plug mr-1"></i> Test Connection
                        </button>
                        
                        @if(is_array($provider->api_keys) && count($provider->api_keys) > 1)
                        <button type="button" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-lg transition reset-key-index" data-provider-id="{{ $provider->id }}">
                            <i class="fas fa-sync-alt mr-1"></i> Reset to First API Key
                        </button>
                        @endif
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Toggle Edit Mode
    document.querySelectorAll('.edit-provider').forEach(button => {
        button.addEventListener('click', function() {
            const providerId = this.dataset.providerId;
            document.getElementById('readonly-' + providerId).classList.add('hidden');
            document.getElementById('edit-form-' + providerId).classList.remove('hidden');
        });
    });
    
    // Cancel Edit Mode
    document.querySelectorAll('.cancel-edit').forEach(button => {
        button.addEventListener('click', function() {
            const providerId = this.dataset.providerId;
            document.getElementById('edit-form-' + providerId).classList.add('hidden');
            document.getElementById('readonly-' + providerId).classList.remove('hidden');
        });
    });
    
    // Toggle pricing sections
    document.querySelectorAll('.toggle-pricing').forEach(button => {
        button.addEventListener('click', function() {
            const providerId = this.dataset.providerId;
            const pricingSection = document.getElementById('pricing-' + providerId);
            const icon = this.querySelector('i');
            
            pricingSection.classList.toggle('hidden');
            
            if (pricingSection.classList.contains('hidden')) {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });
    });
    
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.relative').querySelector('.api-key-input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Save global settings
    document.getElementById('globalSettingsForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            ai_enabled: document.getElementById('aiEnabled').checked ? 1 : 0,
            max_requests_per_day: document.getElementById('maxRequestsPerDay').value,
            max_tokens_per_request: document.getElementById('maxTokensPerRequest').value,
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route("ai.settings.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Success!', data.message);
            } else {
                showNotification('error', 'Error!', data.message || 'Failed to update settings');
            }
        })
        .catch(error => {
            showNotification('error', 'Error!', 'Failed to update settings');
        });
    });

    // Save provider settings
    document.querySelectorAll('.provider-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const providerId = this.dataset.providerId;
            const displayName = this.dataset.displayName;
            const formData = new FormData(this);
            
            // Convert api_keys textarea (one per line) to JSON array
            const apiKeysText = formData.get('api_keys');
            if (apiKeysText && apiKeysText.trim()) {
                const apiKeysArray = apiKeysText
                    .split('\n')
                    .map(key => key.trim())
                    .filter(key => key.length > 0);
                formData.delete('api_keys');
                formData.append('api_keys', JSON.stringify(apiKeysArray));
            }
            
            formData.append('_method', 'PUT');
            formData.append('display_name', displayName);
            formData.append('is_enabled', document.getElementById(`enabled_${providerId}`).checked ? 1 : 0);

            fetch(`/ai/providers/${providerId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Success!', data.message);
                    // Switch back to readonly mode
                    document.getElementById('edit-form-' + providerId).classList.add('hidden');
                    document.getElementById('readonly-' + providerId).classList.remove('hidden');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('error', 'Error!', data.message || 'Failed to update provider');
                }
            })
            .catch(error => {
                showNotification('error', 'Error!', 'Failed to update provider');
            });
        });
    });

    // Toggle provider enabled/disabled
    document.querySelectorAll('.provider-enabled').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const providerId = this.dataset.providerId;
            const isEnabled = this.checked;
            
            fetch(`/ai/providers/${providerId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    is_enabled: isEnabled ? 1 : 0,
                    _token: '{{ csrf_token() }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Success!', 'Provider ' + (isEnabled ? 'enabled' : 'disabled'));
                } else {
                    this.checked = !isEnabled;
                    showNotification('error', 'Error!', 'Failed to update provider status');
                }
            })
            .catch(error => {
                this.checked = !isEnabled;
                showNotification('error', 'Error!', 'Failed to update provider status');
            });
        });
    });

    // Test connection
    document.querySelectorAll('.test-connection').forEach(button => {
        button.addEventListener('click', function() {
            const providerId = this.dataset.providerId;
            const originalHtml = this.innerHTML;
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Testing...';
            
            fetch(`/ai/providers/${providerId}/test`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ _token: '{{ csrf_token() }}' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Connection Test', data.message);
                } else {
                    showNotification('error', 'Connection Test Failed', data.message);
                }
            })
            .catch(error => {
                showNotification('error', 'Test Failed', 'Connection test failed');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalHtml;
            });
        });
    });

    // Activate provider
    document.querySelectorAll('.activate-provider').forEach(button => {
        button.addEventListener('click', function() {
            const providerId = this.dataset.providerId;
            
            if (!confirm('Are you sure you want to activate this provider?')) {
                return;
            }
            
            fetch(`/ai/providers/${providerId}/activate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ _token: '{{ csrf_token() }}' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Success!', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('error', 'Error!', data.message || 'Failed to activate provider');
                }
            })
            .catch(error => {
                showNotification('error', 'Error!', 'Failed to activate provider');
            });
        });
    });

    // Reset API key index
    document.querySelectorAll('.reset-key-index').forEach(button => {
        button.addEventListener('click', function() {
            const providerId = this.dataset.providerId;
            
            if (!confirm('Reset to first API key? This will restart the failover rotation.')) {
                return;
            }
            
            fetch(`/ai/providers/${providerId}/reset-key-index`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ _token: '{{ csrf_token() }}' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Success!', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('error', 'Error!', data.message || 'Failed to reset key index');
                }
            })
            .catch(error => {
                showNotification('error', 'Error!', 'Failed to reset key index');
            })
            .catch(error => {
                showNotification('error', 'Error!', 'Failed to activate provider');
            });
        });
    });
});
</script>
@endpush
