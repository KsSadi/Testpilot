<div class="settings-content-area">
    <form action="{{ route('settings.update.seo') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        @csrf
        
        <div class="space-y-6">
            {{-- Meta Tags --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-tags text-cyan-500 mr-2"></i>Meta Tags
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $seoSettings->where('key', 'meta_title')->first()->value ?? '') }}" maxlength="60" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <p class="text-xs text-gray-500 mt-1">Recommended length: 50-60 characters</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                        <textarea name="meta_description" rows="3" maxlength="160" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">{{ old('meta_description', $seoSettings->where('key', 'meta_description')->first()->value ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Recommended length: 120-160 characters</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seoSettings->where('key', 'meta_keywords')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <p class="text-xs text-gray-500 mt-1">Comma separated keywords</p>
                    </div>
                </div>
            </div>

            {{-- Open Graph --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fab fa-facebook text-cyan-500 mr-2"></i>Open Graph (Social Sharing)
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OG Title</label>
                        <input type="text" name="og_title" value="{{ old('og_title', $seoSettings->where('key', 'og_title')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OG Description</label>
                        <textarea name="og_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">{{ old('og_description', $seoSettings->where('key', 'og_description')->first()->value ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OG Image</label>
                        <input type="file" name="og_image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <p class="text-xs text-gray-500 mt-1">Recommended: 1200x630px | Max: 2MB</p>
                    </div>
                </div>
            </div>

            {{-- Twitter Card --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fab fa-twitter text-cyan-500 mr-2"></i>Twitter Card
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Card Type</label>
                        <select name="twitter_card" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                            <option value="summary">Summary</option>
                            <option value="summary_large_image">Summary Large Image</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Site Handle</label>
                        <input type="text" name="twitter_site" value="{{ old('twitter_site', $seoSettings->where('key', 'twitter_site')->first()->value ?? '') }}" placeholder="@username" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    </div>
                </div>
            </div>

            {{-- Analytics --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-cyan-500 mr-2"></i>Analytics & Verification
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $seoSettings->where('key', 'google_analytics_id')->first()->value ?? '') }}" placeholder="G-XXXXXXXXXX" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Google Site Verification</label>
                        <input type="text" name="google_site_verification" value="{{ old('google_site_verification', $seoSettings->where('key', 'google_site_verification')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save SEO Settings
                </button>
            </div>
        </div>
    </form>
</div>
