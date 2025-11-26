@extends('DemoFrontend::layouts.frontend')

@section('title', 'Documentation - ' . ($appName ?? config('app.name')))

@section('content')
    {{-- Documentation Hero --}}
    <section class="bg-gradient-to-br from-cyan-500 via-blue-500 to-purple-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                <i class="fas fa-book mr-3"></i>Documentation
            </h1>
            <p class="text-xl opacity-90 max-w-3xl mx-auto">
                Complete guide to using {{ $appName ?? config('app.name') }} - Everything you need to know to build amazing applications
            </p>
        </div>
    </section>

    {{-- Documentation Content --}}
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Sidebar Navigation --}}
                <aside class="lg:w-64 flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                        <h3 class="font-bold text-gray-900 mb-4">Table of Contents</h3>
                        <nav class="space-y-2">
                            @foreach($sections as $section)
                                <a href="#{{ $section['id'] }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition">
                                    <i class="fas {{ $section['icon'] }} mr-2"></i> {{ $section['title'] }}
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="flex-1 min-w-0">
                    {{-- Installation Section --}}
                    <div id="installation" class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-download text-cyan-600 mr-3"></i> Installation
                        </h2>
                        
                        <div class="prose max-w-none">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Requirements</h3>
                            <ul class="list-disc pl-6 space-y-2 text-gray-700 mb-6">
                                <li>PHP >= 8.2</li>
                                <li>Composer</li>
                                <li>Node.js & NPM</li>
                                <li>MySQL/PostgreSQL</li>
                                <li>Apache/Nginx</li>
                            </ul>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Installation Steps</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-gray-700 mb-2"><strong>1. Clone the repository</strong></p>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>git clone https://github.com/yourusername/larakit.git
cd larakit</code></pre>
                                </div>

                                <div>
                                    <p class="text-gray-700 mb-2"><strong>2. Install dependencies</strong></p>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>composer install
npm install</code></pre>
                                </div>

                                <div>
                                    <p class="text-gray-700 mb-2"><strong>3. Configure environment</strong></p>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>cp .env.example .env
php artisan key:generate</code></pre>
                                </div>

                                <div>
                                    <p class="text-gray-700 mb-2"><strong>4. Configure database in .env</strong></p>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=larakit
DB_USERNAME=root
DB_PASSWORD=</code></pre>
                                </div>

                                <div>
                                    <p class="text-gray-700 mb-2"><strong>5. Run migrations and seed database</strong></p>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>php artisan migrate --seed</code></pre>
                                </div>

                                <div>
                                    <p class="text-gray-700 mb-2"><strong>6. Create storage link</strong></p>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>php artisan storage:link</code></pre>
                                </div>

                                <div>
                                    <p class="text-gray-700 mb-2"><strong>7. Build assets</strong></p>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>npm run build</code></pre>
                                </div>

                                <div>
                                    <p class="text-gray-700 mb-2"><strong>8. Start development server</strong></p>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>php artisan serve</code></pre>
                                </div>
                            </div>

                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-6">
                                <p class="text-blue-800"><strong>Default Login Credentials:</strong></p>
                                <p class="text-blue-700 mt-2">Email: <code class="bg-blue-100 px-2 py-1 rounded">superadmin@example.com</code></p>
                                <p class="text-blue-700">Password: <code class="bg-blue-100 px-2 py-1 rounded">password</code></p>
                            </div>
                        </div>
                    </div>

                    {{-- Settings System Section --}}
                    <div id="settings" class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-cog text-cyan-600 mr-3"></i> Settings System
                        </h2>
                        
                        <div class="prose max-w-none">
                            <p class="text-gray-700 mb-6">
                                LaraKit includes a comprehensive settings management system with 60+ configurable settings organized in 9 groups.
                            </p>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Using Settings in Code</h3>
                            
                            <p class="text-gray-700 mb-3"><strong>Helper Function:</strong></p>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto mb-6"><code>// Get setting value
$appName = setting('app_name');
$email = setting('contact_email', 'default@example.com');

// In Blade templates
{{ setting('app_name') }}
{{ setting('footer_text', 'Default footer') }}</code></pre>

                            <p class="text-gray-700 mb-3"><strong>Model Methods:</strong></p>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto mb-6"><code>use App\Modules\Setting\Models\Setting;

// Get setting
$value = Setting::get('app_name');

// Set setting
Setting::set('app_name', 'My App', 'string', 'general');

// Get all settings by group
$general = Setting::ofGroup('general')->get();</code></pre>

                            <p class="text-gray-700 mb-3"><strong>ViewServiceProvider Variables:</strong></p>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto mb-6"><code>// Available in all Blade views
{{ $appName }}
{{ $appTagline }}
{{ $appLogo }}
{{ $appFavicon }}
{{ $footerText }}
{{ $copyrightText }}</code></pre>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-6">Settings Groups</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2">General Settings</h4>
                                    <p class="text-sm text-gray-600">App name, logo, contact info, timezone, date/time format</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2">SEO Settings</h4>
                                    <p class="text-sm text-gray-600">Meta tags, Open Graph, sitemap configuration</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2">Auth & Security</h4>
                                    <p class="text-sm text-gray-600">Password rules, session settings, 2FA, login attempts</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2">Email Settings</h4>
                                    <p class="text-sm text-gray-600">SMTP configuration, mail driver, from address</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2">Social Media</h4>
                                    <p class="text-sm text-gray-600">Social media links (Facebook, Twitter, LinkedIn, etc.)</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2">Developer Options</h4>
                                    <p class="text-sm text-gray-600">Debug mode, API settings, cache configuration</p>
                                </div>
                            </div>

                            <div class="bg-green-50 border-l-4 border-green-500 p-4 mt-6">
                                <p class="text-green-800"><strong>UI Access:</strong> Settings → Sidebar Menu → Select Category</p>
                            </div>
                        </div>
                    </div>

                    {{-- Authentication Section --}}
                    <div id="authentication" class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-lock text-cyan-600 mr-3"></i> Authentication System
                        </h2>
                        
                        <div class="prose max-w-none">
                            <p class="text-gray-700 mb-6">
                                LaraKit supports multiple authentication methods that can be enabled/disabled from Settings → Auth & Security.
                            </p>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Available Auth Methods</h3>
                            
                            <div class="space-y-4 mb-6">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-blue-900 mb-2"><i class="fas fa-envelope mr-2"></i>Email Authentication</h4>
                                    <p class="text-sm text-blue-800">Traditional email and password authentication with optional email verification.</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-green-900 mb-2"><i class="fas fa-mobile-alt mr-2"></i>Mobile OTP Authentication</h4>
                                    <p class="text-sm text-green-800">Login with mobile number and OTP. Configurable OTP length, expiry, and resend cooldown.</p>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-purple-900 mb-2"><i class="fas fa-share-alt mr-2"></i>Social Login</h4>
                                    <p class="text-sm text-purple-800">OAuth login with Google, Facebook, and GitHub. Enable individual providers as needed.</p>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Auth Settings Model</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto mb-6"><code>use App\Modules\User\Models\AuthSetting;

// Check if email login is enabled
if (AuthSetting::getBool('email_login_enabled', true)) {
    // Show email login form
}

// Get OTP settings
$otpLength = AuthSetting::get('otp_length', '6');
$otpExpiry = AuthSetting::get('otp_expiry_minutes', '5');

// Check social providers
$googleEnabled = AuthSetting::getBool('google_login_enabled');
$facebookEnabled = AuthSetting::getBool('facebook_login_enabled');</code></pre>

                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mt-6">
                                <p class="text-yellow-800"><strong>Note:</strong> For social login, you need to configure OAuth credentials in your .env file and enable the providers from Settings.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Developer Settings Section --}}
                    <div id="developer" class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-code text-cyan-600 mr-3"></i> Developer Settings
                        </h2>
                        
                        <div class="prose max-w-none">
                            <p class="text-gray-700 mb-6">
                                Configure advanced developer options for debugging, APIs, webhooks, and feature toggles. Access via: <strong>Settings → Developer</strong>
                            </p>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Application Settings</h3>
                            <div class="space-y-4 mb-6">
                                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                    <h4 class="font-semibold text-red-900 mb-2"><i class="fas fa-bug mr-2"></i>Debug Mode</h4>
                                    <p class="text-sm text-red-800 mb-3">Enable detailed error messages and stack traces. <strong>⚠️ NEVER enable in production!</strong></p>
                                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code>// Check debug mode in code
if (setting('app_debug', false)) {
    // Debug mode is enabled
    Log::debug('Debug information');
}</code></pre>
                                </div>

                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-blue-900 mb-2"><i class="fas fa-link mr-2"></i>Application URL</h4>
                                    <p class="text-sm text-blue-800 mb-2">Base URL for your application. Used for generating links, emails, and API responses.</p>
                                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code>$appUrl = setting('app_url', config('app.url'));</code></pre>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">API Configuration</h3>
                            <div class="space-y-4 mb-6">
                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-purple-900 mb-2"><i class="fas fa-plug mr-2"></i>Enable API</h4>
                                    <p class="text-sm text-purple-800 mb-3">Toggle API endpoints for external access. Configure rate limiting to prevent abuse.</p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                        <div class="bg-white p-3 rounded border border-purple-200">
                                            <p class="text-xs font-semibold text-purple-900 mb-1">Enable/Disable API</p>
                                            <code class="text-xs text-purple-700">enable_api = true/false</code>
                                        </div>
                                        <div class="bg-white p-3 rounded border border-purple-200">
                                            <p class="text-xs font-semibold text-purple-900 mb-1">Rate Limit</p>
                                            <code class="text-xs text-purple-700">api_rate_limit = 60 req/min</code>
                                        </div>
                                    </div>

                                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto mt-3"><code>// Use in middleware
use App\Modules\Setting\Models\Setting;

if (setting('enable_api', false)) {
    $rateLimit = setting('api_rate_limit', 60);
    // Apply rate limiting
}</code></pre>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Webhooks</h3>
                            <div class="space-y-4 mb-6">
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-green-900 mb-2"><i class="fas fa-link mr-2"></i>Webhook Integration</h4>
                                    <p class="text-sm text-green-800 mb-3">Send notifications to external services on specific events (user registration, orders, etc.)</p>
                                    
                                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code>// Send webhook notification
if (setting('enable_webhooks', false)) {
    $webhookUrl = setting('webhook_url');
    
    Http::post($webhookUrl, [
        'event' => 'user.registered',
        'data' => [
            'user_id' => $user->id,
            'email' => $user->email,
            'timestamp' => now()
        ]
    ]);
}</code></pre>

                                    <div class="bg-white p-3 rounded border border-green-200 mt-3">
                                        <p class="text-xs font-semibold text-green-900 mb-2">Example Webhook Payload</p>
                                        <pre class="bg-gray-900 text-green-400 p-2 rounded text-xs overflow-x-auto"><code>{
  "event": "user.registered",
  "data": {
    "user_id": 123,
    "email": "user@example.com",
    "timestamp": "2025-11-06 10:30:00"
  }
}</code></pre>
                                    </div>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Feature Toggles</h3>
                            <div class="space-y-3 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2"><i class="fas fa-toggle-on mr-2"></i>Module Control</h4>
                                    <p class="text-sm text-gray-700 mb-3">Enable or disable specific modules without changing code.</p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                        <div class="bg-white p-3 rounded border border-gray-300">
                                            <code class="text-xs text-gray-700">enable_analytics</code>
                                            <p class="text-xs text-gray-600 mt-1">Analytics & Reports Module</p>
                                        </div>
                                        <div class="bg-white p-3 rounded border border-gray-300">
                                            <code class="text-xs text-gray-700">enable_chat</code>
                                            <p class="text-xs text-gray-600 mt-1">Chat & Messaging Module</p>
                                        </div>
                                    </div>

                                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code>// Check feature flags in Blade
@if(setting('enable_analytics', false))
    <a href="{{ route('analytics.index') }}">
        <i class="fas fa-chart-line"></i> Analytics
    </a>
@endif

// Check in Controller
if (setting('enable_chat')) {
    // Show chat interface
}</code></pre>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Best Practices</h3>
                            <div class="space-y-3">
                                <div class="bg-amber-50 border-l-4 border-amber-500 p-4">
                                    <p class="text-amber-900 mb-2"><strong><i class="fas fa-exclamation-triangle mr-2"></i>Production Safety</strong></p>
                                    <ul class="list-disc pl-5 space-y-1 text-sm text-amber-800">
                                        <li>Never enable debug mode in production</li>
                                        <li>Always use HTTPS for webhook URLs</li>
                                        <li>Set appropriate API rate limits (60-120 requests/minute recommended)</li>
                                        <li>Monitor webhook failures and implement retry logic</li>
                                        <li>Test feature toggles in staging before production</li>
                                    </ul>
                                </div>

                                <div class="bg-cyan-50 border-l-4 border-cyan-500 p-4">
                                    <p class="text-cyan-900 mb-2"><strong><i class="fas fa-lightbulb mr-2"></i>Development Tips</strong></p>
                                    <ul class="list-disc pl-5 space-y-1 text-sm text-cyan-800">
                                        <li>Use feature toggles to gradually roll out new features</li>
                                        <li>Cache settings for better performance (automatically handled)</li>
                                        <li>Test webhook endpoints with tools like webhook.site before production</li>
                                        <li>Document custom feature flags for team collaboration</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white p-4 rounded-lg mt-6">
                                <p class="font-semibold mb-2"><i class="fas fa-info-circle mr-2"></i>Settings Cache</p>
                                <p class="text-sm">All developer settings are automatically cached for performance. Changes take effect immediately after saving. Clear cache manually via <code class="bg-white bg-opacity-20 px-2 py-1 rounded">php artisan cache:clear</code> if needed.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Backup System Section --}}
                    <div id="backup" class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-database text-cyan-600 mr-3"></i> Backup System
                        </h2>
                        
                        <div class="prose max-w-none">
                            <p class="text-gray-700 mb-6">
                                LaraKit includes a complete database backup system powered by <strong>Spatie Laravel Backup</strong>. Automatically backup your database on schedule or manually trigger backups anytime. Access via: <strong>Settings → Backup Configuration</strong>
                            </p>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Configuration Options</h3>
                            
                            <div class="space-y-4 mb-6">
                                <div class="bg-green-50 border-l-4 border-green-500 p-4">
                                    <h4 class="font-semibold text-green-900 mb-2"><i class="fas fa-cog mr-2"></i>Automatic Backups</h4>
                                    <ul class="list-disc pl-5 space-y-2 text-sm text-green-800">
                                        <li><strong>Enable/Disable:</strong> Toggle automatic scheduled backups</li>
                                        <li><strong>Frequency:</strong> Choose Daily, Weekly, or Monthly backups</li>
                                        <li><strong>Retention Period:</strong> Set how many days to keep backups (1-365)</li>
                                    </ul>
                                </div>

                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                                    <h4 class="font-semibold text-blue-900 mb-2"><i class="fas fa-hdd mr-2"></i>Storage Locations</h4>
                                    <ul class="list-disc pl-5 space-y-2 text-sm text-blue-800">
                                        <li><strong>Local Storage:</strong> Default - <code class="bg-blue-100 px-2 py-1 rounded">storage/app/Laravel</code></li>
                                        <li><strong>Amazon S3:</strong> Configure AWS credentials in .env</li>
                                        <li><strong>Dropbox:</strong> Requires Dropbox API token</li>
                                        <li><strong>Google Drive:</strong> Requires Google Drive API credentials</li>
                                    </ul>
                                </div>

                                <div class="bg-purple-50 border-l-4 border-purple-500 p-4">
                                    <h4 class="font-semibold text-purple-900 mb-2"><i class="fas fa-tools mr-2"></i>Manual Actions</h4>
                                    <ul class="list-disc pl-5 space-y-2 text-sm text-purple-800">
                                        <li><strong>Create Backup Now:</strong> Instant manual backup</li>
                                        <li><strong>View Backups:</strong> See all available backup files</li>
                                        <li><strong>Clean Old Backups:</strong> Remove backups based on retention policy</li>
                                    </ul>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Using Backup Commands</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Run Manual Backup</h4>
                                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code># Run backup now (respects settings)
php artisan backup:run-now

# Run Spatie backup directly (database only)
php artisan backup:run --only-db

# Run full backup (database + files)
php artisan backup:run</code></pre>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Clean Old Backups</h4>
                                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code># Remove old backups based on retention policy
php artisan backup:clean</code></pre>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Check Backup Health</h4>
                                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code># Monitor backup health
php artisan backup:monitor</code></pre>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">List All Backups</h4>
                                    <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code># List all backup files
php artisan backup:list</code></pre>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-6">Scheduling Automatic Backups</h3>
                            <p class="text-gray-700 mb-3">Add backup commands to your <code class="bg-gray-100 px-2 py-1 rounded">app/Console/Kernel.php</code>:</p>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded text-sm overflow-x-auto mb-4"><code>protected function schedule(Schedule $schedule)
{
    // Daily backup at 2 AM
    if (setting('enable_auto_backup', false)) {
        $frequency = setting('backup_frequency', 'daily');
        
        $backup = $schedule->command('backup:run-now');
        
        switch ($frequency) {
            case 'daily':
                $backup->daily()->at('02:00');
                break;
            case 'weekly':
                $backup->weekly()->sundays()->at('02:00');
                break;
            case 'monthly':
                $backup->monthly(1, '02:00');
                break;
        }
    }
    
    // Clean old backups daily at 3 AM
    $schedule->command('backup:clean')->daily()->at('03:00');
}</code></pre>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-6">Using in Code</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded text-sm overflow-x-auto mb-4"><code>use Illuminate\Support\Facades\Artisan;

// Check if backups are enabled
if (setting('enable_auto_backup', false)) {
    // Trigger backup
    Artisan::call('backup:run-now');
}

// Get backup settings
$frequency = setting('backup_frequency', 'daily');
$storage = setting('backup_storage', 'local');
$retention = setting('backup_retention_days', 30);

// Check backup files
$disk = Storage::disk('local');
$backups = $disk->allFiles('Laravel');</code></pre>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-6">Cloud Storage Setup</h3>
                            
                            <div class="space-y-3 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2"><i class="fab fa-aws text-orange-500 mr-2"></i>Amazon S3</h4>
                                    <p class="text-sm text-gray-700 mb-2">Add to <code class="bg-gray-200 px-2 py-1 rounded">.env</code>:</p>
                                    <pre class="bg-gray-900 text-green-400 p-2 rounded text-xs overflow-x-auto"><code>AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket
AWS_USE_PATH_STYLE_ENDPOINT=false</code></pre>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2"><i class="fab fa-dropbox text-blue-500 mr-2"></i>Dropbox</h4>
                                    <p class="text-sm text-gray-700 mb-2">Install Flysystem adapter:</p>
                                    <pre class="bg-gray-900 text-green-400 p-2 rounded text-xs overflow-x-auto"><code>composer require spatie/flysystem-dropbox</code></pre>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 mb-2"><i class="fab fa-google text-red-500 mr-2"></i>Google Drive</h4>
                                    <p class="text-sm text-gray-700 mb-2">Install Flysystem adapter:</p>
                                    <pre class="bg-gray-900 text-green-400 p-2 rounded text-xs overflow-x-auto"><code>composer require masbug/flysystem-google-drive-ext</code></pre>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-6">Best Practices</h3>
                            <div class="space-y-3">
                                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                    <p class="text-red-900 mb-2"><strong><i class="fas fa-exclamation-triangle mr-2"></i>Security</strong></p>
                                    <ul class="list-disc pl-5 space-y-1 text-sm text-red-800">
                                        <li>Store backups in multiple locations (3-2-1 rule)</li>
                                        <li>Encrypt backups containing sensitive data</li>
                                        <li>Restrict backup file access permissions</li>
                                        <li>Use secure cloud storage with proper authentication</li>
                                        <li>Test backup restoration regularly</li>
                                    </ul>
                                </div>

                                <div class="bg-green-50 border-l-4 border-green-500 p-4">
                                    <p class="text-green-900 mb-2"><strong><i class="fas fa-check-circle mr-2"></i>Recommendations</strong></p>
                                    <ul class="list-disc pl-5 space-y-1 text-sm text-green-800">
                                        <li>Run daily backups for production databases</li>
                                        <li>Keep at least 7 days of backups</li>
                                        <li>Monitor backup success/failure notifications</li>
                                        <li>Document your backup restoration process</li>
                                        <li>Schedule backups during low-traffic hours</li>
                                    </ul>
                                </div>

                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                                    <p class="text-blue-900 mb-2"><strong><i class="fas fa-info-circle mr-2"></i>Troubleshooting</strong></p>
                                    <ul class="list-disc pl-5 space-y-1 text-sm text-blue-800">
                                        <li>Ensure <code class="bg-blue-100 px-1 rounded">storage/app/Laravel</code> has write permissions</li>
                                        <li>Check disk space before running backups</li>
                                        <li>Verify database credentials in .env file</li>
                                        <li>Enable email notifications for backup failures</li>
                                        <li>Check logs at <code class="bg-blue-100 px-1 rounded">storage/logs/laravel.log</code></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-4 rounded-lg mt-6">
                                <p class="font-semibold mb-2"><i class="fas fa-shield-alt mr-2"></i>Backup Package</p>
                                <p class="text-sm">Powered by <strong>Spatie Laravel Backup</strong> - Industry-standard backup solution. Visit <a href="https://spatie.be/docs/laravel-backup" class="underline" target="_blank">documentation</a> for advanced configuration.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Permissions Section --}}
                    <div id="permissions" class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-shield-alt text-cyan-600 mr-3"></i> Roles & Permissions
                        </h2>
                        
                        <div class="prose max-w-none">
                            <p class="text-gray-700 mb-6">
                                Role-based access control using Spatie Laravel Permission package with pre-configured roles and permissions.
                            </p>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Default Roles</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="bg-red-50 p-4 rounded-lg border-2 border-red-200">
                                    <h4 class="font-semibold text-red-900 mb-2">Super Admin</h4>
                                    <p class="text-sm text-red-800">Full system access with all permissions</p>
                                </div>
                                <div class="bg-orange-50 p-4 rounded-lg border-2 border-orange-200">
                                    <h4 class="font-semibold text-orange-900 mb-2">Admin</h4>
                                    <p class="text-sm text-orange-800">Most permissions except role/permission management</p>
                                </div>
                                <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                                    <h4 class="font-semibold text-blue-900 mb-2">Moderator</h4>
                                    <p class="text-sm text-blue-800">Limited permissions for content moderation</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg border-2 border-green-200">
                                    <h4 class="font-semibold text-green-900 mb-2">User</h4>
                                    <p class="text-sm text-green-800">Basic access with minimal permissions</p>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Using Permissions in Code</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto mb-6"><code>// Check permission
if (auth()->user()->can('edit-users')) {
    // Allow action
}

// In Blade templates
@can('view-settings')
    <a href="{{ route('settings.index') }}">Settings</a>
@endcan

// Assign role
$user->assignRole('admin');

// Check role
if ($user->hasRole('superadmin')) {
    // Admin actions
}</code></pre>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Available Permissions</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-6">
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">view-dashboard</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">view-users</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">create-users</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">edit-users</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">delete-users</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">view-roles</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">create-roles</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">edit-roles</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">view-settings</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">edit-settings</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">view-reports</span>
                                <span class="bg-gray-100 px-3 py-1 rounded text-sm">export-reports</span>
                            </div>
                        </div>
                    </div>

                    {{-- Module System Section --}}
                    <div id="modules" class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-layer-group text-cyan-600 mr-3"></i> Module System
                        </h2>
                        
                        <div class="prose max-w-none">
                            <p class="text-gray-700 mb-6">
                                LaraKit uses Laravel Modules for a clean, maintainable architecture. Each module is self-contained with its own controllers, models, views, and routes.
                            </p>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Creating a New Module</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto mb-6"><code>php artisan make:module BlogModule</code></pre>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Module Structure</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto mb-6"><code>app/Modules/YourModule/
├── Http/
│   └── Controllers/
├── Models/
├── resources/
│   ├── views/
│   └── lang/
└── routes/
    ├── web.php
    └── api.php</code></pre>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Existing Modules</h3>
                            <div class="space-y-3">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold">Setting Module</h4>
                                    <p class="text-sm text-gray-600">Manages application settings with comprehensive UI</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold">User Module</h4>
                                    <p class="text-sm text-gray-600">User management, profiles, authentication</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold">DemoFrontend Module</h4>
                                    <p class="text-sm text-gray-600">Landing page and documentation</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Helper Functions Section --}}
                    <div id="helpers" class="bg-white rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-code text-cyan-600 mr-3"></i> Helper Functions
                        </h2>
                        
                        <div class="prose max-w-none">
                            <p class="text-gray-700 mb-6">
                                Global helper functions available throughout the application.
                            </p>

                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Available Helpers</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">setting($key, $default = null)</h4>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>$appName = setting('app_name');
$email = setting('contact_email', 'default@example.com');</code></pre>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">app_name()</h4>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>echo app_name(); // Returns application name from settings</code></pre>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">app_logo()</h4>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>$logoPath = app_logo(); // Returns logo storage path</code></pre>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">app_favicon()</h4>
                                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>$faviconPath = app_favicon(); // Returns favicon storage path</code></pre>
                                </div>
                            </div>

                            <div class="bg-cyan-50 border-l-4 border-cyan-500 p-4 mt-6">
                                <p class="text-cyan-800"><strong>Location:</strong> All helpers are defined in <code class="bg-cyan-100 px-2 py-1 rounded">app/helpers.php</code></p>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Start Guide --}}
                    <div class="bg-gradient-to-br from-cyan-500 via-blue-500 to-purple-600 text-white rounded-xl p-8 mb-8">
                        <h2 class="text-3xl font-bold mb-6">
                            <i class="fas fa-rocket mr-3"></i> Quick Start Checklist
                        </h2>
                        <div class="space-y-3">
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" class="mt-1 w-5 h-5 rounded">
                                <span>Install and configure LaraKit following installation steps</span>
                            </label>
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" class="mt-1 w-5 h-5 rounded">
                                <span>Run migrations and seed database with default data</span>
                            </label>
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" class="mt-1 w-5 h-5 rounded">
                                <span>Login with default credentials and explore dashboard</span>
                            </label>
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" class="mt-1 w-5 h-5 rounded">
                                <span>Configure general settings (app name, logo, contact info)</span>
                            </label>
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" class="mt-1 w-5 h-5 rounded">
                                <span>Set up authentication methods as needed</span>
                            </label>
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" class="mt-1 w-5 h-5 rounded">
                                <span>Create your first custom module</span>
                            </label>
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" class="mt-1 w-5 h-5 rounded">
                                <span>Start building your application!</span>
                            </label>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // Smooth scroll with offset for fixed header
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offset = 100;
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = target.getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Highlight active section in sidebar
    const sections = document.querySelectorAll('main > div[id]');
    const navLinks = document.querySelectorAll('aside a[href^="#"]');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.pageYOffset >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('bg-cyan-50', 'text-cyan-600');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('bg-cyan-50', 'text-cyan-600');
            }
        });
    });
</script>
@endpush
