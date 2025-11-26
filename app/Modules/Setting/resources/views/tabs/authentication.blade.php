<div class="settings-content-area">
    {{-- Authentication Methods --}}
    <form action="{{ route('settings.update.auth-methods') }}" method="POST" class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        @csrf
        
        <div class="space-y-6">
            {{-- Email Authentication --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-envelope text-blue-500 mr-2"></i>Email Authentication
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="email_login_enabled" value="1" {{ old('email_login_enabled', \App\Modules\User\Models\AuthSetting::getBool('email_login_enabled', true)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700 font-medium">Enable Email Login</span>
                    </label>
                    <label class="flex items-center cursor-pointer ml-8">
                        <input type="checkbox" name="email_registration_enabled" value="1" {{ old('email_registration_enabled', \App\Modules\User\Models\AuthSetting::getBool('email_registration_enabled', true)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Enable Email Registration</span>
                    </label>
                    <label class="flex items-center cursor-pointer ml-8">
                        <input type="checkbox" name="email_verification_required" value="1" {{ old('email_verification_required', \App\Modules\User\Models\AuthSetting::getBool('email_verification_required', false)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Require Email Verification</span>
                    </label>
                </div>
            </div>

            {{-- Mobile/OTP Authentication --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-mobile-alt text-green-500 mr-2"></i>Mobile OTP Authentication
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="mobile_login_enabled" value="1" {{ old('mobile_login_enabled', \App\Modules\User\Models\AuthSetting::getBool('mobile_login_enabled', false)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700 font-medium">Enable Mobile OTP Login</span>
                    </label>
                    <label class="flex items-center cursor-pointer ml-8">
                        <input type="checkbox" name="mobile_registration_enabled" value="1" {{ old('mobile_registration_enabled', \App\Modules\User\Models\AuthSetting::getBool('mobile_registration_enabled', false)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Enable Mobile Registration</span>
                    </label>
                    <label class="flex items-center cursor-pointer ml-8">
                        <input type="checkbox" name="mobile_verification_required" value="1" {{ old('mobile_verification_required', \App\Modules\User\Models\AuthSetting::getBool('mobile_verification_required', false)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Require Mobile Verification</span>
                    </label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 ml-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">OTP Length</label>
                            <select name="otp_length" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                                <option value="4" {{ old('otp_length', \App\Modules\User\Models\AuthSetting::get('otp_length', '6')) == '4' ? 'selected' : '' }}>4 digits</option>
                                <option value="6" {{ old('otp_length', \App\Modules\User\Models\AuthSetting::get('otp_length', '6')) == '6' ? 'selected' : '' }}>6 digits</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">OTP Expiry (minutes)</label>
                            <input type="number" name="otp_expiry_minutes" value="{{ old('otp_expiry_minutes', \App\Modules\User\Models\AuthSetting::get('otp_expiry_minutes', '5')) }}" min="1" max="30" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Resend Cooldown (seconds)</label>
                            <input type="number" name="otp_resend_cooldown_seconds" value="{{ old('otp_resend_cooldown_seconds', \App\Modules\User\Models\AuthSetting::get('otp_resend_cooldown_seconds', '60')) }}" min="30" max="300" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Social Authentication --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-share-alt text-purple-500 mr-2"></i>Social Authentication
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="social_login_enabled" value="1" {{ old('social_login_enabled', \App\Modules\User\Models\AuthSetting::getBool('social_login_enabled', false)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700 font-medium">Enable Social Login</span>
                    </label>
                    
                    <div class="ml-8 space-y-3 mt-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="google_login_enabled" value="1" {{ old('google_login_enabled', \App\Modules\User\Models\AuthSetting::getBool('google_login_enabled', false)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                            <span class="ml-3 text-sm text-gray-700 flex items-center">
                                <i class="fab fa-google text-red-500 mr-2"></i>Google Login
                            </span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="facebook_login_enabled" value="1" {{ old('facebook_login_enabled', \App\Modules\User\Models\AuthSetting::getBool('facebook_login_enabled', false)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                            <span class="ml-3 text-sm text-gray-700 flex items-center">
                                <i class="fab fa-facebook text-blue-600 mr-2"></i>Facebook Login
                            </span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="github_login_enabled" value="1" {{ old('github_login_enabled', \App\Modules\User\Models\AuthSetting::getBool('github_login_enabled', false)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                            <span class="ml-3 text-sm text-gray-700 flex items-center">
                                <i class="fab fa-github text-gray-800 mr-2"></i>GitHub Login
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- General Settings --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-cog text-gray-500 mr-2"></i>General Settings
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="allow_registration" value="1" {{ old('allow_registration', \App\Modules\User\Models\AuthSetting::getBool('allow_registration', true)) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Allow User Registration</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Default User Role</label>
                        <input type="text" name="default_user_role" value="{{ old('default_user_role', \App\Modules\User\Models\AuthSetting::get('default_user_role', 'user')) }}" class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <p class="text-xs text-gray-500 mt-1">Role slug to assign to new users</p>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Authentication Methods
                </button>
            </div>
        </div>
    </form>
</div>
