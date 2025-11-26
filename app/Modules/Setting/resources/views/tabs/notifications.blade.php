<div class="settings-content-area">
    <form action="{{ route('settings.update.notifications') }}" method="POST" class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        @csrf
        
        <div class="space-y-6">
            {{-- Notification Channels --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bell text-cyan-500 mr-2"></i>Notification Channels
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_email_notifications" value="1" {{ old('enable_email_notifications', $notificationSettings->where('key', 'enable_email_notifications')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Email Notifications</span>
                            <p class="text-xs text-gray-500">Send notifications via email</p>
                        </span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_push_notifications" value="1" {{ old('enable_push_notifications', $notificationSettings->where('key', 'enable_push_notifications')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Push Notifications</span>
                            <p class="text-xs text-gray-500">Browser push notifications</p>
                        </span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_sms_notifications" value="1" {{ old('enable_sms_notifications', $notificationSettings->where('key', 'enable_sms_notifications')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-700">SMS Notifications</span>
                            <p class="text-xs text-gray-500">Send notifications via SMS</p>
                        </span>
                    </label>
                </div>
            </div>

            {{-- Notification Events --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-flag text-cyan-500 mr-2"></i>Notification Events
                </h3>
                <p class="text-sm text-gray-600 mb-4">Choose which events trigger notifications for administrators.</p>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="notify_on_new_user" value="1" {{ old('notify_on_new_user', $notificationSettings->where('key', 'notify_on_new_user')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">New User Registration</span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="notify_on_new_order" value="1" {{ old('notify_on_new_order', $notificationSettings->where('key', 'notify_on_new_order')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">New Order</span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="notify_on_payment" value="1" {{ old('notify_on_payment', $notificationSettings->where('key', 'notify_on_payment')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3 text-sm text-gray-700">Payment Received</span>
                    </label>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-amber-600 mt-0.5 mr-3"></i>
                    <div class="text-sm text-amber-800">
                        <p class="font-medium mb-1">Pro Tip</p>
                        <p>Configure your email settings first before enabling email notifications to ensure proper delivery.</p>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Notification Settings
                </button>
            </div>
        </div>
    </form>
</div>
