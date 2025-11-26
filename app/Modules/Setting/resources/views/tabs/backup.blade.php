<div class="settings-content-area">
    {{-- Backup Settings Form --}}
    <form action="{{ route('settings.update.backup') }}" method="POST" class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        @csrf
        
        <div class="space-y-6">
            {{-- Backup Settings --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-database text-cyan-500 mr-2"></i>Backup Configuration
                </h3>
                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_auto_backup" value="1" {{ old('enable_auto_backup', $backupSettings->where('key', 'enable_auto_backup')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Enable Automatic Backups</span>
                            <p class="text-xs text-gray-500">Automatically backup your database on schedule</p>
                        </span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Backup Frequency</label>
                            <select name="backup_frequency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                                <option value="daily" {{ old('backup_frequency', $backupSettings->where('key', 'backup_frequency')->first()->value ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('backup_frequency', $backupSettings->where('key', 'backup_frequency')->first()->value ?? '') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('backup_frequency', $backupSettings->where('key', 'backup_frequency')->first()->value ?? '') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Storage Location</label>
                            <select name="backup_storage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                                <option value="local" {{ old('backup_storage', $backupSettings->where('key', 'backup_storage')->first()->value ?? 'local') === 'local' ? 'selected' : '' }}>Local Storage</option>
                                <option value="s3" {{ old('backup_storage', $backupSettings->where('key', 'backup_storage')->first()->value ?? '') === 's3' ? 'selected' : '' }}>Amazon S3</option>
                                <option value="dropbox" {{ old('backup_storage', $backupSettings->where('key', 'backup_storage')->first()->value ?? '') === 'dropbox' ? 'selected' : '' }}>Dropbox</option>
                                <option value="google" {{ old('backup_storage', $backupSettings->where('key', 'backup_storage')->first()->value ?? '') === 'google' ? 'selected' : '' }}>Google Drive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Retention Period (days)</label>
                            <input type="number" name="backup_retention_days" value="{{ old('backup_retention_days', $backupSettings->where('key', 'backup_retention_days')->first()->value ?? 30) }}" min="1" max="365" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                            <p class="text-xs text-gray-500 mt-1">Days to keep backups (1-365)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Maintenance Mode --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-wrench text-cyan-500 mr-2"></i>Maintenance Mode
                </h3>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="maintenance_mode" value="1" {{ old('maintenance_mode', $backupSettings->where('key', 'maintenance_mode')->first()->value ?? false) ? 'checked' : '' }} class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                    <span class="ml-3">
                        <span class="text-sm font-medium text-gray-700">Enable Maintenance Mode</span>
                        <p class="text-xs text-gray-500">Put the application in maintenance mode (only admins can access)</p>
                    </span>
                </label>
            </div>

            {{-- Warning Box --}}
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-0.5 mr-3"></i>
                    <div class="text-sm text-red-800">
                        <p class="font-medium mb-1">Important</p>
                        <p>Enabling maintenance mode will make the application inaccessible to regular users. Only administrators will be able to access the system.</p>
                    </div>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-2">Backup Storage Locations</p>
                        <ul class="space-y-1 ml-4 list-disc">
                            <li><strong>Local:</strong> <code class="bg-blue-100 px-1 rounded">storage/app/backups</code></li>
                            <li><strong>Amazon S3:</strong> Configure AWS credentials in .env file</li>
                            <li><strong>Dropbox:</strong> Requires Dropbox API token</li>
                            <li><strong>Google Drive:</strong> Requires Google Drive API credentials</li>
                        </ul>
                        <p class="mt-2">Make sure storage has write permissions and sufficient disk space.</p>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Backup Settings
                </button>
            </div>
        </div>
    </form>

    {{-- Manual Backup Actions (Separate from settings form) --}}
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-play-circle text-cyan-500 mr-2"></i>Manual Actions
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <form action="{{ route('backup.run') }}" method="POST" class="w-full" onsubmit="return confirm('Create backup now? This may take a few moments.')">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-green-50 border-2 border-green-200 text-green-700 rounded-lg hover:bg-green-100 transition">
                    <i class="fas fa-download mr-2"></i>
                    <span class="font-medium">Create Backup Now</span>
                </button>
            </form>
            <a href="{{ route('backup.list') }}" class="flex items-center justify-center px-4 py-3 bg-purple-50 border-2 border-purple-200 text-purple-700 rounded-lg hover:bg-purple-100 transition">
                <i class="fas fa-history mr-2"></i>
                <span class="font-medium">View Backups</span>
            </a>
            <form action="{{ route('backup.cleanup') }}" method="POST" class="w-full" onsubmit="return confirm('Run cleanup of old backups based on retention policy?')">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-blue-50 border-2 border-blue-200 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                    <i class="fas fa-broom mr-2"></i>
                    <span class="font-medium">Clean Old Backups</span>
                </button>
            </form>
        </div>
    </div>
</div>
