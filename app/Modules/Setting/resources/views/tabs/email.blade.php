<div class="settings-content-area">
    <form action="{{ route('settings.update.email') }}" method="POST" class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        @csrf
        
        <div class="space-y-6">
            {{-- Mail Driver --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-server text-cyan-500 mr-2"></i>Mail Driver Configuration
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Driver <span class="text-red-500">*</span></label>
                        <select name="mail_mailer" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                            <option value="smtp" {{ old('mail_mailer', $emailSettings->where('key', 'mail_mailer')->first()->value ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ old('mail_mailer', $emailSettings->where('key', 'mail_mailer')->first()->value ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="mailgun" {{ old('mail_mailer', $emailSettings->where('key', 'mail_mailer')->first()->value ?? '') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="ses" {{ old('mail_mailer', $emailSettings->where('key', 'mail_mailer')->first()->value ?? '') === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SMTP Settings --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-cogs text-cyan-500 mr-2"></i>SMTP Configuration
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" name="mail_host" value="{{ old('mail_host', $emailSettings->where('key', 'mail_host')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="smtp.mailtrap.io">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                        <input type="number" name="mail_port" value="{{ old('mail_port', $emailSettings->where('key', 'mail_port')->first()->value ?? 587) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="587">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="mail_username" value="{{ old('mail_username', $emailSettings->where('key', 'mail_username')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="mail_password" value="{{ old('mail_password') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="Leave empty to keep current">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                        <select name="mail_encryption" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                            <option value="">None</option>
                            <option value="tls" {{ old('mail_encryption', $emailSettings->where('key', 'mail_encryption')->first()->value ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('mail_encryption', $emailSettings->where('key', 'mail_encryption')->first()->value ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- From Settings --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-envelope text-cyan-500 mr-2"></i>Default Sender
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Email <span class="text-red-500">*</span></label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $emailSettings->where('key', 'mail_from_address')->first()->value ?? '') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="noreply@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Name <span class="text-red-500">*</span></label>
                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $emailSettings->where('key', 'mail_from_name')->first()->value ?? '') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="LaraKit">
                    </div>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Testing Email Configuration</p>
                        <p>Make sure to test your email configuration after saving changes. You can use a service like <a href="https://mailtrap.io" target="_blank" class="underline">Mailtrap</a> for testing.</p>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Email Settings
                </button>
            </div>
        </div>
    </form>
</div>
