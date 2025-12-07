@extends('layouts.backend.master')

@section('title', 'System Settings')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">System Settings</h2>
        <p class="text-gray-600 mt-1">Manage currency conversion rates and payment gateway settings</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.subscriptions.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Currency Settings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-dollar-sign text-blue-600 text-xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-800">Currency Settings</h3>
            </div>
            
            <div class="space-y-4">
                @foreach($currencySettings as $setting)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $setting->label }}
                            @if($setting->description)
                                <span class="block text-xs text-gray-500 font-normal mt-1">{{ $setting->description }}</span>
                            @endif
                        </label>
                        
                        @if($setting->type === 'boolean')
                            <select name="settings[{{ $setting->key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="true" {{ $setting->value === 'true' ? 'selected' : '' }}>Enabled</option>
                                <option value="false" {{ $setting->value === 'false' ? 'selected' : '' }}>Disabled</option>
                            </select>
                        @elseif($setting->type === 'number')
                            <input type="number" 
                                   name="settings[{{ $setting->key }}]" 
                                   value="{{ $setting->value }}" 
                                   step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   required>
                        @else
                            <input type="text" 
                                   name="settings[{{ $setting->key }}]" 
                                   value="{{ $setting->value }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   required>
                        @endif
                        
                        @if($setting->key === 'currency_usd_to_bdt_rate')
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Current rate: 1 USD = {{ $setting->value }} BDT
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Payment Gateway Settings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-credit-card text-green-600 text-xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-800">Payment Gateway Settings</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($paymentSettings as $setting)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $setting->label }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $setting->description }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="settings[{{ $setting->key }}]" 
                                   value="true" 
                                   {{ $setting->value === 'true' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Payment Instructions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-info-circle text-purple-600 text-xl mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-800">Payment Instructions & Details</h3>
            </div>
            <p class="text-sm text-gray-600 mb-6">Configure payment account details and instructions that will be shown to customers during checkout</p>
            
            {{-- bKash Instructions --}}
            <div class="mb-6 p-5 border-l-4 border-pink-500 bg-pink-50 rounded-r-lg">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center text-lg">
                    <img src="https://download.logo.wine/logo/BKash/BKash-Logo.wine.png" alt="bKash" class="h-6 w-16 object-contain mr-2">
                    bKash Payment Details
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-lg">
                    @foreach($bkashSettings as $setting)
                        <div class="{{ $setting->key === 'payment_bkash_instructions' ? 'md:col-span-2' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $setting->label }}
                                <span class="text-red-500">*</span>
                            </label>
                            @if($setting->key === 'payment_bkash_instructions')
                                <textarea name="settings[{{ $setting->key }}]" rows="7" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 font-mono text-sm" placeholder="Enter step-by-step payment instructions...">{{ $setting->value }}</textarea>
                                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-lightbulb mr-1"></i>Each line will be shown as a separate step</p>
                            @elseif($setting->key === 'payment_bkash_type')
                                <select name="settings[{{ $setting->key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                    <option value="Personal" {{ $setting->value === 'Personal' ? 'selected' : '' }}>Personal</option>
                                    <option value="Merchant" {{ $setting->value === 'Merchant' ? 'selected' : '' }}>Merchant</option>
                                    <option value="Agent" {{ $setting->value === 'Agent' ? 'selected' : '' }}>Agent</option>
                                </select>
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Enter bKash number">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Nagad Instructions --}}
            <div class="mb-6 p-5 border-l-4 border-orange-500 bg-orange-50 rounded-r-lg">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center text-lg">
                    <img src="https://seeklogo.com/images/N/nagad-logo-7A70CCFEE8-seeklogo.com.png" alt="Nagad" class="h-6 w-16 object-contain mr-2">
                    Nagad Payment Details
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-lg">
                    @foreach($nagadSettings as $setting)
                        <div class="{{ $setting->key === 'payment_nagad_instructions' ? 'md:col-span-2' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $setting->label }}
                                <span class="text-red-500">*</span>
                            </label>
                            @if($setting->key === 'payment_nagad_instructions')
                                <textarea name="settings[{{ $setting->key }}]" rows="7" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 font-mono text-sm" placeholder="Enter step-by-step payment instructions...">{{ $setting->value }}</textarea>
                                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-lightbulb mr-1"></i>Each line will be shown as a separate step</p>
                            @elseif($setting->key === 'payment_nagad_type')
                                <select name="settings[{{ $setting->key }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                    <option value="Personal" {{ $setting->value === 'Personal' ? 'selected' : '' }}>Personal</option>
                                    <option value="Merchant" {{ $setting->value === 'Merchant' ? 'selected' : '' }}>Merchant</option>
                                </select>
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" placeholder="Enter Nagad number">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Rocket Instructions --}}
            <div class="mb-6 p-5 border-l-4 border-purple-500 bg-purple-50 rounded-r-lg">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center text-lg">
                    <img src="https://seeklogo.com/images/D/dutch-bangla-rocket-logo-B4D1CC458D-seeklogo.com.png" alt="Rocket" class="h-6 w-16 object-contain mr-2">
                    Rocket Payment Details
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-lg">
                    @foreach($rocketSettings as $setting)
                        <div class="{{ $setting->key === 'payment_rocket_instructions' ? 'md:col-span-2' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $setting->label }}
                                <span class="text-red-500">*</span>
                            </label>
                            @if($setting->key === 'payment_rocket_instructions')
                                <textarea name="settings[{{ $setting->key }}]" rows="7" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 font-mono text-sm" placeholder="Enter step-by-step payment instructions...">{{ $setting->value }}</textarea>
                                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-lightbulb mr-1"></i>Each line will be shown as a separate step</p>
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Enter Rocket number">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Bank Transfer Instructions --}}
            <div class="mb-6 p-5 border-l-4 border-green-500 bg-green-50 rounded-r-lg">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center text-lg">
                    <i class="fas fa-university text-green-600 text-xl mr-2"></i>
                    Bank Transfer Details
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-lg">
                    @foreach($bankSettings as $setting)
                        <div class="{{ $setting->key === 'payment_bank_instructions' ? 'md:col-span-2' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $setting->label }}
                                <span class="text-red-500">*</span>
                            </label>
                            @if($setting->key === 'payment_bank_instructions')
                                <textarea name="settings[{{ $setting->key }}]" rows="7" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 font-mono text-sm" placeholder="Enter step-by-step payment instructions...">{{ $setting->value }}</textarea>
                                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-lightbulb mr-1"></i>Each line will be shown as a separate step</p>
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="{{ $setting->description }}">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.subscriptions.plans.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 primary-color text-white rounded-lg hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i>
                Save Settings
            </button>
        </div>
    </form>

    {{-- Exchange Rate Info --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-lightbulb text-blue-600 mt-1 mr-3"></i>
            <div>
                <h4 class="font-semibold text-blue-900 mb-1">Currency Conversion Tips</h4>
                <p class="text-sm text-blue-800">
                    The USD to BDT conversion rate is used for displaying prices in Bangladeshi Taka. 
                    You can update this rate based on current market exchange rates. 
                    Common sources: Bangladesh Bank, XE.com, or Google Finance.
                </p>
                <p class="text-xs text-blue-700 mt-2">
                    <strong>Note:</strong> This rate is cached for 1 hour. After updating, it may take up to 60 minutes for changes to reflect across the system.
                </p>
            </div>
        </div>
    </div>
@endsection
