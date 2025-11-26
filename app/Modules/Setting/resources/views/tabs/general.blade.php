<div class="settings-content-area">
    <form action="{{ route('settings.update.general') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        {{-- Site Identity Card --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center mr-3">
                    <i class="fas fa-building text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Site Identity</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application Name <span class="text-red-500">*</span></label>
                    <input type="text" name="app_name" value="{{ old('app_name', setting('app_name', '')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                    <input type="text" name="app_tagline" value="{{ old('app_tagline', setting('app_tagline', '')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="app_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">{{ old('app_description', setting('app_description', '')) }}</textarea>
            </div>
        </div>

        {{-- Branding Card --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mr-3">
                    <i class="fas fa-image text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Branding</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    <p class="text-xs text-gray-500 mt-1">Recommended: PNG, JPG, SVG | Max: 2MB</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                    <input type="file" name="favicon" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    <p class="text-xs text-gray-500 mt-1">Recommended: ICO, PNG | Max: 1MB</p>
                </div>
            </div>
        </div>

        {{-- Contact Information Card --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mr-3">
                    <i class="fas fa-address-book text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Contact Information</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', setting('contact_email', '')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', setting('contact_phone', '')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">{{ old('address', setting('address', '')) }}</textarea>
            </div>
        </div>

        {{-- Regional Settings Card --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center mr-3">
                    <i class="fas fa-globe text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Regional Settings</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                    <select name="timezone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        @php
                            $currentTimezone = setting('timezone', 'UTC');
                            $popularTimezones = [
                                'UTC' => 'UTC (Coordinated Universal Time)',
                                'Asia/Dhaka' => 'Asia/Dhaka (Bangladesh)',
                                'Asia/Kolkata' => 'Asia/Kolkata (India)',
                                'Asia/Karachi' => 'Asia/Karachi (Pakistan)',
                                'Asia/Dubai' => 'Asia/Dubai (UAE)',
                                'Asia/Singapore' => 'Asia/Singapore',
                                'Asia/Tokyo' => 'Asia/Tokyo (Japan)',
                                'Asia/Shanghai' => 'Asia/Shanghai (China)',
                                'Europe/London' => 'Europe/London (UK)',
                                'Europe/Paris' => 'Europe/Paris (France)',
                                'America/New_York' => 'America/New York (US Eastern)',
                                'America/Chicago' => 'America/Chicago (US Central)',
                                'America/Los_Angeles' => 'America/Los Angeles (US Pacific)',
                                'Australia/Sydney' => 'Australia/Sydney',
                            ];
                        @endphp
                        <optgroup label="Popular Timezones">
                            @foreach($popularTimezones as $value => $label)
                                <option value="{{ $value }}" {{ $currentTimezone == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="All Timezones">
                            @foreach(timezone_identifiers_list() as $timezone)
                                @if(!array_key_exists($timezone, $popularTimezones))
                                    <option value="{{ $timezone }}" {{ $currentTimezone == $timezone ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $timezone) }}
                                    </option>
                                @endif
                            @endforeach
                        </optgroup>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select your local timezone</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                    <select name="date_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <option value="Y-m-d" {{ setting('date_format') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2025-11-06)</option>
                        <option value="d/m/Y" {{ setting('date_format') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (06/11/2025)</option>
                        <option value="m/d/Y" {{ setting('date_format') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (11/06/2025)</option>
                        <option value="d-m-Y" {{ setting('date_format') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (06-11-2025)</option>
                        <option value="F j, Y" {{ setting('date_format') == 'F j, Y' ? 'selected' : '' }}>Month Day, Year (November 6, 2025)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">How dates will be displayed</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Format</label>
                    <select name="time_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <option value="H:i:s" {{ setting('time_format') == 'H:i:s' ? 'selected' : '' }}>24 Hour (15:30:00)</option>
                        <option value="h:i:s A" {{ setting('time_format') == 'h:i:s A' ? 'selected' : '' }}>12 Hour (03:30:00 PM)</option>
                        <option value="H:i" {{ setting('time_format') == 'H:i' ? 'selected' : '' }}>24 Hour Short (15:30)</option>
                        <option value="h:i A" {{ setting('time_format') == 'h:i A' ? 'selected' : '' }}>12 Hour Short (03:30 PM)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">How times will be displayed</p>
                </div>
            </div>
        </div>

        {{-- Footer Card --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center mr-3">
                    <i class="fas fa-align-center text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Footer</h3>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Footer Text</label>
                    <input type="text" name="footer_text" value="{{ old('footer_text', setting('footer_text', '')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Copyright Text</label>
                    <input type="text" name="copyright_text" value="{{ old('copyright_text', setting('copyright_text', '')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-info-circle text-cyan-500 mr-2"></i>
                    Changes will take effect immediately after saving
                </p>
                <button type="submit" class="btn-primary group">
                    <i class="fas fa-save mr-2 transition-transform group-hover:scale-110"></i>
                    Save General Settings
                </button>
            </div>
        </div>
    </form>
</div>
