{{-- Top Header Component --}}
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
    <div class="flex items-center justify-between px-4 md:px-6 py-3">
        {{-- Mobile Menu Button --}}
        <div class="flex items-center">
            <button onclick="toggleSidebar()" class="mobile-menu-button items-center justify-center p-2.5 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 rounded-xl transition-all">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>
        
        {{-- Enhanced Search Bar with Live Search --}}
        <div class="header-search flex items-center flex-1 max-w-2xl mx-6" x-data="globalSearch()">
            <div class="relative w-full group">
                <input 
                    type="text" 
                    x-model="query"
                    @input.debounce.300ms="search()"
                    @keydown.escape="clearSearch()"
                    @keydown.down.prevent="navigateDown()"
                    @keydown.up.prevent="navigateUp()"
                    @keydown.enter.prevent="selectResult()"
                    @focus="if (query.length >= 2) showDropdown = true"
                    placeholder="Search projects, modules, tests... (Ctrl + K)" 
                    class="w-full pl-11 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent focus:bg-white transition-all text-base hover:border-gray-300"
                    id="global-search-input">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400 text-base group-focus-within:text-cyan-500 transition-colors" x-show="!loading"></i>
                <div class="absolute left-4 top-3.5" x-show="loading" x-cloak>
                    <svg class="animate-spin h-5 w-5 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <!-- Clear Search Button -->
                <button x-show="query.length > 0" 
                        @click="clearSearch()"
                        class="absolute right-12 top-3 text-gray-400 hover:text-gray-600 p-0.5"
                        x-cloak>
                    <i class="fas fa-times text-sm"></i>
                </button>
                
                <div class="absolute right-3 top-2.5 hidden sm:flex items-center space-x-1" x-show="query.length === 0">
                    <kbd class="px-2 py-0.5 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded">Ctrl</kbd>
                    <span class="text-gray-400 text-xs">+</span>
                    <kbd class="px-2 py-0.5 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded">K</kbd>
                </div>

                {{-- Search Results Dropdown --}}
                <div x-show="showDropdown && query.length >= 2" 
                     @click.outside="closeDropdown()"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 max-h-[500px] overflow-y-auto"
                     x-cloak>
                    
                    {{-- Loading State --}}
                    <div x-show="loading" class="p-8 text-center">
                        <div class="inline-block">
                            <svg class="animate-spin h-8 w-8 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 font-medium mt-3">Searching...</p>
                    </div>
                    
                    {{-- No Results --}}
                    <div x-show="!loading && results.total === 0 && query.length >= 2" class="p-8 text-center">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-search text-4xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium">No results found</p>
                        <p class="text-sm text-gray-500 mt-1">Try different keywords</p>
                    </div>

                    {{-- Projects --}}
                    <div x-show="results.projects && results.projects.length > 0">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">🗂️ Projects</span>
                        </div>
                        <template x-for="(item, index) in results.projects" :key="item.id">
                            <a :href="item.url" 
                               @mouseenter="selectedIndex = getGlobalIndex('project', index)"
                               :class="{'bg-cyan-50': selectedIndex === getGlobalIndex('project', index)}"
                               class="block px-4 py-3 hover:bg-cyan-50 transition-colors border-b border-gray-50 cursor-pointer">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl" x-text="item.icon"></span>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-gray-800 truncate" x-html="highlightMatch(item.name)"></p>
                                            <span x-show="item.status === 'active'" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Active</span>
                                        </div>
                                        <p class="text-sm text-gray-500 truncate" x-text="item.description"></p>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                            <span x-text="item.meta"></span>
                                            <span>•</span>
                                            <span x-text="item.created_at"></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    {{-- Modules --}}
                    <div x-show="results.modules && results.modules.length > 0">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">📦 Modules</span>
                        </div>
                        <template x-for="(item, index) in results.modules" :key="item.id">
                            <a :href="item.url"
                               @mouseenter="selectedIndex = getGlobalIndex('module', index)"
                               :class="{'bg-cyan-50': selectedIndex === getGlobalIndex('module', index)}"
                               class="block px-4 py-3 hover:bg-cyan-50 transition-colors border-b border-gray-50 cursor-pointer">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl" x-text="item.icon"></span>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-gray-800 truncate" x-html="highlightMatch(item.name)"></p>
                                            <span x-show="item.status === 'active'" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Active</span>
                                        </div>
                                        <p class="text-xs text-cyan-600 truncate" x-text="item.parent"></p>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                            <span x-text="item.meta"></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    {{-- Test Cases --}}
                    <div x-show="results.test_cases && results.test_cases.length > 0">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                            <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">✅ Test Cases</span>
                        </div>
                        <template x-for="(item, index) in results.test_cases" :key="item.id">
                            <a :href="item.url"
                               @mouseenter="selectedIndex = getGlobalIndex('test_case', index)"
                               :class="{'bg-cyan-50': selectedIndex === getGlobalIndex('test_case', index)}"
                               class="block px-4 py-3 hover:bg-cyan-50 transition-colors border-b border-gray-50 cursor-pointer">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl" x-text="item.icon"></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-800 truncate" x-html="highlightMatch(item.name)"></p>
                                        <p class="text-xs text-cyan-600 truncate" x-text="item.parent"></p>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                            <span x-text="item.meta"></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    {{-- View All Link --}}
                    <div x-show="results.total > 0" class="px-4 py-3 bg-gray-50 text-center border-t border-gray-100">
                        <span class="text-sm text-gray-600">
                            <span x-text="`Found ${results.total} result${results.total > 1 ? 's' : ''}`"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Right Side Actions --}}
        <div class="flex items-center space-x-2">
            {{-- Notifications --}}
            <div class="relative notification-dropdown">
                <button onclick="toggleNotifications()" class="relative p-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition-all hover:scale-105">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute top-1.5 right-1.5 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold text-[10px] animate-pulse shadow-lg">3</span>
                </button>
                {{-- Notification Dropdown --}}
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 z-50">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-gray-800">Notifications</h3>
                            <span class="text-xs bg-cyan-100 text-cyan-700 px-2 py-1 rounded-full font-semibold">3 New</span>
                        </div>
                    </div>
                    <div class="max-h-96 overflow-y-auto custom-scrollbar">
                        <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50 transition">
                            <div class="flex items-start space-x-3">
                                <div class="primary-color rounded-lg p-2 flex-shrink-0 w-9 h-9 flex items-center justify-center">
                                    <i class="fas fa-shopping-bag text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">New Order Received</p>
                                    <p class="text-xs text-gray-500 mt-1">Order #2847 from Sarah Johnson</p>
                                    <p class="text-xs text-gray-400 mt-1">2 minutes ago</p>
                                </div>
                                <div class="w-2 h-2 bg-cyan-500 rounded-full flex-shrink-0 mt-1"></div>
                            </div>
                        </div>
                        <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50 transition">
                            <div class="flex items-start space-x-3">
                                <div class="bg-green-500 rounded-lg p-2 flex-shrink-0 w-9 h-9 flex items-center justify-center">
                                    <i class="fas fa-user-plus text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">New Customer</p>
                                    <p class="text-xs text-gray-500 mt-1">Michael Chen registered</p>
                                    <p class="text-xs text-gray-400 mt-1">15 minutes ago</p>
                                </div>
                                <div class="w-2 h-2 bg-cyan-500 rounded-full flex-shrink-0 mt-1"></div>
                            </div>
                        </div>
                        <div class="p-3 hover:bg-gray-50 cursor-pointer transition">
                            <div class="flex items-start space-x-3">
                                <div class="bg-orange-500 rounded-lg p-2 flex-shrink-0 w-9 h-9 flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">Low Stock Alert</p>
                                    <p class="text-xs text-gray-500 mt-1">Wireless Headphones - 5 left</p>
                                    <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                                </div>
                                <div class="w-2 h-2 bg-cyan-500 rounded-full flex-shrink-0 mt-1"></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 border-t border-gray-100">
                        <button class="w-full text-center text-sm text-cyan-600 font-medium hover:text-cyan-700 transition">View All Notifications</button>
                    </div>
                </div>
            </div>
            
            <div class="h-8 w-px bg-gray-200 hidden md:block"></div>
            
            {{-- User Profile Dropdown --}}
            <div class="header-user-info relative user-menu-dropdown">
                <button onclick="toggleUserMenu()" class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 px-3 py-2 rounded-xl transition-all">
                    <div class="relative">
                        @if(Auth::check())
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="w-9 h-9 rounded-xl ring-2 ring-gray-100 object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=06b6d4&color=fff&font-size=0.4&bold=true" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="w-9 h-9 rounded-xl ring-2 ring-gray-100">
                            @endif
                        @else
                            <img src="https://ui-avatars.com/api/?name=Guest&background=06b6d4&color=fff&font-size=0.4&bold=true" 
                                 alt="Guest" 
                                 class="w-9 h-9 rounded-xl ring-2 ring-gray-100">
                        @endif
                        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="text-left hidden xl:block">
                        <p class="text-base font-semibold text-gray-800 leading-tight">
                            {{ Auth::user()->name ?? 'Guest User' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            @if(Auth::check() && Auth::user()->getRoleNames()->isNotEmpty())
                                {{ ucfirst(Auth::user()->getRoleNames()->first()) }}
                            @else
                                User
                            @endif
                        </p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-sm ml-1 hidden md:block"></i>
                </button>
                {{-- User Dropdown --}}
                <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-200 z-50">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            @if(Auth::check())
                                @if(Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-12 h-12 rounded-xl object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=06b6d4&color=fff&font-size=0.4&bold=true" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-12 h-12 rounded-xl">
                                @endif
                            @else
                                <img src="https://ui-avatars.com/api/?name=Guest&background=06b6d4&color=fff&font-size=0.4&bold=true" 
                                     alt="Guest" 
                                     class="w-12 h-12 rounded-xl">
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Guest User' }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email ?? 'guest@example.com' }}</p>
                                @if(Auth::user()->getRoleNames()->isNotEmpty())
                                    <span class="inline-block mt-1 text-xs bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full font-medium">
                                        {{ ucfirst(Auth::user()->getRoleNames()->first()) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 rounded-lg transition text-sm">
                            <i class="fas fa-user text-cyan-600 w-5"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 rounded-lg transition text-sm">
                            <i class="fas fa-user-edit text-blue-600 w-5"></i>
                            <span>Edit Profile</span>
                        </a>
                        @can('view-settings')
                        <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 rounded-lg transition text-sm">
                            <i class="fas fa-cog text-purple-600 w-5"></i>
                            <span>Settings</span>
                        </a>
                        @endcan
                    </div>
                    <div class="p-2 border-t border-gray-100">
                        <form action="{{ url('/logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center space-x-3 px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-lg transition text-sm font-medium w-full">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Global Search Component
function globalSearch() {
    return {
        query: '',
        results: {
            projects: [],
            modules: [],
            test_cases: [],
            total: 0
        },
        loading: false,
        showDropdown: false,
        selectedIndex: -1,

        init() {
            // Keyboard shortcut: Ctrl/Cmd + K
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    document.getElementById('global-search-input').focus();
                }
            });

            // Initialize empty results
            this.results = {
                projects: [],
                modules: [],
                test_cases: [],
                total: 0
            };
        },

        async search() {
            if (this.query.length < 2) {
                this.closeDropdown();
                return;
            }

            this.loading = true;
            this.showDropdown = true;

            try {
                const response = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.results = data.results;
                    this.selectedIndex = -1;
                } else {
                    console.error('Search failed:', data.message);
                    this.results = { projects: [], modules: [], test_cases: [], total: 0 };
                }
            } catch (error) {
                console.error('Search error:', error);
                this.results = { projects: [], modules: [], test_cases: [], total: 0 };
            } finally {
                this.loading = false;
            }
        },

        clearSearch() {
            this.query = '';
            this.results = { projects: [], modules: [], test_cases: [], total: 0 };
            this.closeDropdown();
        },

        closeDropdown() {
            this.showDropdown = false;
            this.selectedIndex = -1;
            this.loading = false;
        },

        navigateDown() {
            const totalResults = this.getTotalResults();
            if (totalResults > 0) {
                this.selectedIndex = Math.min(this.selectedIndex + 1, totalResults - 1);
            }
        },

        navigateUp() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
            }
        },

        selectResult() {
            const item = this.getResultByIndex(this.selectedIndex);
            if (item && item.url) {
                window.location.href = item.url;
            }
        },

        getTotalResults() {
            return (this.results.projects?.length || 0) +
                   (this.results.modules?.length || 0) +
                   (this.results.test_cases?.length || 0);
        },

        getGlobalIndex(type, localIndex) {
            let offset = 0;
            if (type === 'module') {
                offset = this.results.projects?.length || 0;
            } else if (type === 'test_case') {
                offset = (this.results.projects?.length || 0) +
                        (this.results.modules?.length || 0);
            }
            return offset + localIndex;
        },

        getResultByIndex(index) {
            const allResults = [
                ...(this.results.projects || []),
                ...(this.results.modules || []),
                ...(this.results.test_cases || [])
            ];
            return allResults[index];
        },

        highlightMatch(text) {
            if (!this.query || !text) return text;
            const regex = new RegExp(`(${this.escapeRegex(this.query)})`, 'gi');
            return text.replace(regex, '<mark class="bg-yellow-200 font-semibold">$1</mark>');
        },

        escapeRegex(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
    }
}
</script>
