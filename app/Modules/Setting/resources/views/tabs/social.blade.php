<div class="settings-content-area">
    <form action="{{ route('settings.update.social') }}" method="POST" class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        @csrf
        
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-share-alt text-cyan-500 mr-2"></i>Social Media Links
                </h3>
                <p class="text-sm text-gray-600 mb-6">Add your social media profile URLs to display on your website.</p>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-facebook text-blue-600 mr-2"></i>Facebook URL
                        </label>
                        <input type="url" name="facebook_url" value="{{ old('facebook_url', $socialSettings->where('key', 'facebook_url')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="https://facebook.com/yourpage">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-twitter text-blue-400 mr-2"></i>Twitter/X URL
                        </label>
                        <input type="url" name="twitter_url" value="{{ old('twitter_url', $socialSettings->where('key', 'twitter_url')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="https://twitter.com/yourhandle">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-instagram text-pink-600 mr-2"></i>Instagram URL
                        </label>
                        <input type="url" name="instagram_url" value="{{ old('instagram_url', $socialSettings->where('key', 'instagram_url')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="https://instagram.com/yourprofile">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-linkedin text-blue-700 mr-2"></i>LinkedIn URL
                        </label>
                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $socialSettings->where('key', 'linkedin_url')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="https://linkedin.com/company/yourcompany">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-youtube text-red-600 mr-2"></i>YouTube URL
                        </label>
                        <input type="url" name="youtube_url" value="{{ old('youtube_url', $socialSettings->where('key', 'youtube_url')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="https://youtube.com/channel/yourchannel">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-github text-gray-800 mr-2"></i>GitHub URL
                        </label>
                        <input type="url" name="github_url" value="{{ old('github_url', $socialSettings->where('key', 'github_url')->first()->value ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="https://github.com/yourusername">
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Social Media Settings
                </button>
            </div>
        </div>
    </form>
</div>
