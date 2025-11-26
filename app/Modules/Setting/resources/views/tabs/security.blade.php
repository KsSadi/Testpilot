<div class="settings-content-area">
    {{-- Basic Security Settings --}}
    <form action="{{ route('settings.update.auth') }}" method="POST" class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        @csrf
        
        <div class="space-y-6">
            {{-- Registration Settings --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-plus text-cyan-500 mr-2"></i>Basic Registration Settings
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="allow_registration" value="1" {{ old('allow_registration', $authSettings->where('key', 'allow_registration')->first()->value ?? '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Allow User Registration</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="require_email_verification" value="1" {{ old('require_email_verification', $authSettings->where('key', 'require_email_verification')->first()->value ?? '0') == '1' ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Require Email Verification</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="require_mobile_verification" value="1" {{ old('require_mobile_verification', $authSettings->where('key', 'require_mobile_verification')->first()->value ?? '0') == '1' ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Require Mobile Verification</span>
                    </label>
                </div>
            </div>

            {{-- Security Settings --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-shield-alt text-cyan-500 mr-2"></i>Security Settings
                </h3>
                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_2fa" value="1" {{ old('enable_2fa', $authSettings->where('key', 'enable_2fa')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Enable Two-Factor Authentication (2FA)</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Session Lifetime (minutes)</label>
                            <input type="number" name="session_lifetime" value="{{ old('session_lifetime', $authSettings->where('key', 'session_lifetime')->first()->value ?? 120) }}" min="1" max="10080" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Login Attempts</label>
                            <input type="number" name="max_login_attempts" value="{{ old('max_login_attempts', $authSettings->where('key', 'max_login_attempts')->first()->value ?? 5) }}" min="1" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lockout Duration (minutes)</label>
                            <input type="number" name="lockout_duration" value="{{ old('lockout_duration', $authSettings->where('key', 'lockout_duration')->first()->value ?? 15) }}" min="1" max="1440" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Password Requirements --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-key text-cyan-500 mr-2"></i>Password Requirements
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Password Length</label>
                        <input type="number" name="password_min_length" value="{{ old('password_min_length', $authSettings->where('key', 'password_min_length')->first()->value ?? 8) }}" min="6" max="32" class="w-full md:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    </div>
                    <div class="space-y-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="password_require_uppercase" value="1" {{ old('password_require_uppercase', $authSettings->where('key', 'password_require_uppercase')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                            <span class="ml-3 text-sm text-gray-700">Require Uppercase Letters</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="password_require_numbers" value="1" {{ old('password_require_numbers', $authSettings->where('key', 'password_require_numbers')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                            <span class="ml-3 text-sm text-gray-700">Require Numbers</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="password_require_special" value="1" {{ old('password_require_special', $authSettings->where('key', 'password_require_special')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                            <span class="ml-3 text-sm text-gray-700">Require Special Characters</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Security Settings
                </button>
            </div>
        </div>
    </form>
</div>
