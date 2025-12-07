{{-- Sidebar Component --}}
<aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 flex flex-col" id="sidebar">
    {{-- Logo Section --}}
    <div class="p-5 border-b border-gray-100">
        <div class="flex items-center space-x-3">
            @if($appLogo ?? null)
                <div class="flex-shrink-0">
                    <img src="{{ Storage::url($appLogo) }}" alt="{{ $appName ?? config('app.name') }}" class="h-12 w-12 object-contain rounded-xl shadow-lg">
                </div>
            @else
                <div class="primary-color rounded-xl p-2.5 shadow-lg relative overflow-hidden flex-shrink-0">
                    <i class="fas fa-chart-line text-white text-xl relative z-10"></i>
                    <div class="absolute inset-0 bg-white opacity-20 blur-sm"></div>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight truncate">{{ $appName ?? config('app.name', 'Dashboard') }}</h1>
                <p class="text-sm text-gray-500 font-medium truncate">{{ $appTagline ?? 'Analytics Hub' }}</p>
            </div>
        </div>
    </div>

    {{-- Navigation Section --}}
    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3">
        {{-- Main Menu --}}
        <div class="space-y-1">
            {{-- Dashboard --}}
            <a href="{{ url('/dashboard') }}" class="sidebar-link {{ Request::is('dashboard') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl font-medium text-base">
                <i class="fas fa-home mr-3 text-lg w-5"></i>
                <span>Dashboard</span>
            </a>

            {{-- User Management with Submenu --}}
            @php
                $isUserManagementActive = Request::is('members*') || Request::is('roles*') || Request::is('permissions*');
            @endphp
            <div class="sidebar-submenu {{ $isUserManagementActive ? 'submenu-open' : '' }}">
                <button onclick="toggleSubmenu('userMenu')" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-600 rounded-xl text-base font-medium {{ $isUserManagementActive ? 'active' : '' }}">
                    <div class="flex items-center">
                        <i class="fas fa-users-cog mr-3 text-lg w-5"></i>
                        <span>User Management</span>
                    </div>
                    <i class="fas fa-chevron-down text-sm transition-transform {{ $isUserManagementActive ? 'rotate-180' : '' }}" id="userMenuIcon"></i>
                </button>
                <div id="userMenu" class="{{ $isUserManagementActive ? '' : 'hidden' }} mt-1 ml-8 space-y-1">
                    <a href="{{ route('members.index') }}" class="sidebar-link {{ Request::is('members*') ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-users mr-3 text-base w-4"></i>
                        <span>Members</span>
                    </a>
                    <a href="{{ url('/roles') }}" class="sidebar-link {{ Request::is('roles*') ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-user-shield mr-3 text-base w-4"></i>
                        <span>Roles</span>
                    </a>
                    <a href="{{ url('/permissions') }}" class="sidebar-link {{ Request::is('permissions*') ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-key mr-3 text-base w-4"></i>
                        <span>Permissions</span>
                    </a>
                </div>
            </div>


            {{-- Project Management --}}
            @can('edit-settings')
            <a href="{{ route('projects.index') }}" class="sidebar-link {{ Request::is('projects*') || Request::is('test-cases*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-600 rounded-xl text-base font-medium">
                <i class="fas fa-project-diagram mr-3 text-lg w-5"></i>
                <span>Project</span>
            </a>
            @endcan

            {{-- AI Settings --}}
            @can('edit-settings')
            <a href="{{ route('ai.settings') }}" class="sidebar-link {{ Request::is('ai*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-600 rounded-xl text-base font-medium">
                <i class="fas fa-brain mr-3 text-lg w-5"></i>
                <span>AI Settings</span>
            </a>
            @endcan

            {{-- Subscription (User) --}}
            <a href="{{ route('subscription.index') }}" class="sidebar-link {{ Request::is('subscription') && !Request::is('admin/subscriptions*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-600 rounded-xl text-base font-medium">
                <i class="fas fa-crown mr-3 text-lg w-5 text-yellow-500"></i>
                <span>My Subscription</span>
                @if(!auth()->user()->currentSubscription || auth()->user()->currentSubscription->status === 'cancelled')
                    <span class="ml-auto px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Upgrade</span>
                @endif
            </a>

            {{-- Subscription Management (Admin) --}}
            @can('manage-subscriptions')
            @php
                $isSubscriptionManagementActive = Request::is('admin/subscriptions*');
            @endphp
            <div class="sidebar-submenu {{ $isSubscriptionManagementActive ? 'submenu-open' : '' }}">
                <button onclick="toggleSubmenu('subscriptionMenu')" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-600 rounded-xl text-base font-medium {{ $isSubscriptionManagementActive ? 'active' : '' }}">
                    <div class="flex items-center">
                        <i class="fas fa-credit-card mr-3 text-lg w-5"></i>
                        <span>Subscriptions</span>
                    </div>
                    <i class="fas fa-chevron-down text-sm transition-transform {{ $isSubscriptionManagementActive ? 'rotate-180' : '' }}" id="subscriptionMenuIcon"></i>
                </button>
                <div id="subscriptionMenu" class="{{ $isSubscriptionManagementActive ? '' : 'hidden' }} mt-1 ml-8 space-y-1">
                    <a href="{{ route('admin.subscriptions.plans.index') }}" class="sidebar-link {{ Request::is('admin/subscriptions/plans*') ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-box mr-3 text-base w-4"></i>
                        <span>Plans</span>
                    </a>
                    <a href="{{ route('admin.subscriptions.coupons.index') }}" class="sidebar-link {{ Request::is('admin/subscriptions/coupons*') ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-tags mr-3 text-base w-4"></i>
                        <span>Coupons</span>
                    </a>
                    <a href="{{ route('admin.subscriptions.payments.index') }}" class="sidebar-link {{ Request::is('admin/subscriptions/payments*') ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-money-check-alt mr-3 text-base w-4"></i>
                        <span>Payments</span>
                        @php
                            $pendingPayments = \App\Modules\Subsription\Models\SubscriptionPayment::where('status', 'pending')->count();
                        @endphp
                        @if($pendingPayments > 0)
                            <span class="ml-auto px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-800 rounded-full">{{ $pendingPayments }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.subscriptions.manage.index') }}" class="sidebar-link {{ Request::is('admin/subscriptions/manage*') && !Request::is('admin/subscriptions/settings*') ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-users-cog mr-3 text-base w-4"></i>
                        <span>Manage Subscriptions</span>
                    </a>
                    <a href="{{ route('admin.subscriptions.settings.index') }}" class="sidebar-link {{ Request::is('admin/subscriptions/settings*') ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-cog mr-3 text-base w-4"></i>
                        <span>Currency & Gateways</span>
                    </a>
                </div>
            </div>
            @endcan

            {{-- Settings with Submenu --}}
            @can('view-settings')
            @php
                $isSettingsActive = Request::is('settings*') && !Request::is('ai*');
            @endphp
            <div class="sidebar-submenu {{ $isSettingsActive ? 'submenu-open' : '' }}">
                <button onclick="toggleSubmenu('settingsMenu')" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-600 rounded-xl text-base font-medium {{ $isSettingsActive ? 'active' : '' }}">
                    <div class="flex items-center">
                        <i class="fas fa-cog mr-3 text-lg w-5"></i>
                        <span>Settings</span>
                    </div>
                    <i class="fas fa-chevron-down text-sm transition-transform {{ $isSettingsActive ? 'rotate-180' : '' }}" id="settingsMenuIcon"></i>
                </button>
                <div id="settingsMenu" class="{{ $isSettingsActive ? '' : 'hidden' }} mt-1 ml-8 space-y-1">
                    <a href="{{ route('settings.index', ['tab' => 'general']) }}" class="sidebar-link {{ Request::is('settings*') && request('tab') === 'general' ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-cog mr-3 text-base w-4"></i>
                        <span>General Settings</span>
                    </a>
                    <a href="{{ route('settings.index', ['tab' => 'seo']) }}" class="sidebar-link {{ Request::is('settings*') && request('tab') === 'seo' ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-search mr-3 text-base w-4"></i>
                        <span>SEO Settings</span>
                    </a>
                    <a href="{{ route('settings.index', ['tab' => 'authentication']) }}" class="sidebar-link {{ Request::is('settings*') && request('tab') === 'authentication' ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-user-lock mr-3 text-base w-4"></i>
                        <span>Authentication</span>
                    </a>
                    <a href="{{ route('settings.index', ['tab' => 'security']) }}" class="sidebar-link {{ Request::is('settings*') && request('tab') === 'security' ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-shield-alt mr-3 text-base w-4"></i>
                        <span>Security</span>
                    </a>
                    <a href="{{ route('settings.index', ['tab' => 'email']) }}" class="sidebar-link {{ Request::is('settings*') && request('tab') === 'email' ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-envelope mr-3 text-base w-4"></i>
                        <span>Email Settings</span>
                    </a>
                    <a href="{{ route('settings.index', ['tab' => 'social']) }}" class="sidebar-link {{ Request::is('settings*') && request('tab') === 'social' ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-share-alt mr-3 text-base w-4"></i>
                        <span>Social Media</span>
                    </a>
                    <a href="{{ route('settings.index', ['tab' => 'notifications']) }}" class="sidebar-link {{ Request::is('settings*') && request('tab') === 'notifications' ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-bell mr-3 text-base w-4"></i>
                        <span>Notifications</span>
                    </a>
                    <a href="{{ route('settings.index', ['tab' => 'backup']) }}" class="sidebar-link {{ Request::is('settings*') && request('tab') === 'backup' ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-database mr-3 text-base w-4"></i>
                        <span>Backup Settings</span>
                    </a>
                    <a href="{{ route('settings.index', ['tab' => 'developer']) }}" class="sidebar-link {{ Request::is('settings*') && request('tab') === 'developer' ? 'active' : '' }} flex items-center px-4 py-2.5 text-gray-600 rounded-lg text-sm">
                        <i class="fas fa-code mr-3 text-base w-4"></i>
                        <span>Developer Options</span>
                    </a>
                </div>
            </div>
            @endcan

            {{-- Backups --}}
            @can('edit-settings')
            <a href="{{ route('backup.list') }}" class="sidebar-link {{ Request::is('backup*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-600 rounded-xl text-base font-medium">
                <i class="fas fa-database mr-3 text-lg w-5"></i>
                <span>Backups</span>
            </a>
            @endcan
        </div>
    </nav>

    {{-- User Info Section at Bottom --}}
    <div class="border-t border-gray-100 p-3">
        <div class="user-section flex items-center space-x-3 p-3 rounded-xl bg-gray-50">
            <div class="relative">
                @if(Auth::check())
                    @if(Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}"
                             alt="{{ Auth::user()->name }}"
                             class="w-10 h-10 rounded-xl ring-2 ring-cyan-100 object-cover">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=06b6d4&color=fff&font-size=0.4&bold=true"
                             alt="{{ Auth::user()->name }}"
                             class="w-10 h-10 rounded-xl ring-2 ring-cyan-100">
                    @endif
                @else
                    <img src="https://ui-avatars.com/api/?name=Guest&background=06b6d4&color=fff&font-size=0.4&bold=true"
                         alt="Guest"
                         class="w-10 h-10 rounded-xl ring-2 ring-cyan-100">
                @endif
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-base font-semibold text-gray-800 truncate">
                    {{ Auth::user()->name ?? 'Guest User' }}
                </p>
                <p class="text-sm text-gray-500 truncate">
                    {{ Auth::user()->email ?? 'guest@example.com' }}
                </p>
            </div>
        </div>
    </div>
</aside>
