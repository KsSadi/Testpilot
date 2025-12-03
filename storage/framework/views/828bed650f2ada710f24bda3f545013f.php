
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
    <div class="flex items-center justify-between px-4 md:px-6 py-3">
        
        <div class="flex items-center">
            <button onclick="toggleSidebar()" class="mobile-menu-button items-center justify-center p-2.5 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 rounded-xl transition-all">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>
        
        
        <div class="header-search flex items-center flex-1 max-w-2xl mx-6">
            <div class="relative w-full group">
                <input type="text" placeholder="Search anything... (Ctrl + K)" class="w-full pl-11 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent focus:bg-white transition-all text-base hover:border-gray-300">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400 text-base group-focus-within:text-cyan-500 transition-colors"></i>
                <div class="absolute right-3 top-2.5 hidden sm:flex items-center space-x-1">
                    <kbd class="px-2 py-0.5 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded">Ctrl</kbd>
                    <span class="text-gray-400 text-xs">+</span>
                    <kbd class="px-2 py-0.5 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded">K</kbd>
                </div>
            </div>
        </div>
        
        
        <div class="flex items-center space-x-2">
            
            <div class="relative notification-dropdown">
                <button onclick="toggleNotifications()" class="relative p-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition-all hover:scale-105">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute top-1.5 right-1.5 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold text-[10px] animate-pulse shadow-lg">3</span>
                </button>
                
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
            
            
            <div class="header-user-info relative user-menu-dropdown">
                <button onclick="toggleUserMenu()" class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 px-3 py-2 rounded-xl transition-all">
                    <div class="relative">
                        <?php if(Auth::check()): ?>
                            <?php if(Auth::user()->avatar): ?>
                                <img src="<?php echo e(Storage::url(Auth::user()->avatar)); ?>" 
                                     alt="<?php echo e(Auth::user()->name); ?>" 
                                     class="w-9 h-9 rounded-xl ring-2 ring-gray-100 object-cover">
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(Auth::user()->name)); ?>&background=06b6d4&color=fff&font-size=0.4&bold=true" 
                                     alt="<?php echo e(Auth::user()->name); ?>" 
                                     class="w-9 h-9 rounded-xl ring-2 ring-gray-100">
                            <?php endif; ?>
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=Guest&background=06b6d4&color=fff&font-size=0.4&bold=true" 
                                 alt="Guest" 
                                 class="w-9 h-9 rounded-xl ring-2 ring-gray-100">
                        <?php endif; ?>
                        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="text-left hidden xl:block">
                        <p class="text-base font-semibold text-gray-800 leading-tight">
                            <?php echo e(Auth::user()->name ?? 'Guest User'); ?>

                        </p>
                        <p class="text-sm text-gray-500">
                            <?php if(Auth::check() && Auth::user()->getRoleNames()->isNotEmpty()): ?>
                                <?php echo e(ucfirst(Auth::user()->getRoleNames()->first())); ?>

                            <?php else: ?>
                                User
                            <?php endif; ?>
                        </p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-sm ml-1 hidden md:block"></i>
                </button>
                
                <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-200 z-50">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <?php if(Auth::check()): ?>
                                <?php if(Auth::user()->avatar): ?>
                                    <img src="<?php echo e(Storage::url(Auth::user()->avatar)); ?>" 
                                         alt="<?php echo e(Auth::user()->name); ?>" 
                                         class="w-12 h-12 rounded-xl object-cover">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(Auth::user()->name)); ?>&background=06b6d4&color=fff&font-size=0.4&bold=true" 
                                         alt="<?php echo e(Auth::user()->name); ?>" 
                                         class="w-12 h-12 rounded-xl">
                                <?php endif; ?>
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=Guest&background=06b6d4&color=fff&font-size=0.4&bold=true" 
                                     alt="Guest" 
                                     class="w-12 h-12 rounded-xl">
                            <?php endif; ?>
                            <div>
                                <p class="font-semibold text-gray-800"><?php echo e(Auth::user()->name ?? 'Guest User'); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e(Auth::user()->email ?? 'guest@example.com'); ?></p>
                                <?php if(Auth::user()->getRoleNames()->isNotEmpty()): ?>
                                    <span class="inline-block mt-1 text-xs bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full font-medium">
                                        <?php echo e(ucfirst(Auth::user()->getRoleNames()->first())); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="p-2">
                        <a href="<?php echo e(route('profile.show')); ?>" class="flex items-center space-x-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 rounded-lg transition text-sm">
                            <i class="fas fa-user text-cyan-600 w-5"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="<?php echo e(route('profile.edit')); ?>" class="flex items-center space-x-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 rounded-lg transition text-sm">
                            <i class="fas fa-user-edit text-blue-600 w-5"></i>
                            <span>Edit Profile</span>
                        </a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-settings')): ?>
                        <a href="<?php echo e(route('settings.index')); ?>" class="flex items-center space-x-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 rounded-lg transition text-sm">
                            <i class="fas fa-cog text-purple-600 w-5"></i>
                            <span>Settings</span>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="p-2 border-t border-gray-100">
                        <form action="<?php echo e(url('/logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
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
<?php /**PATH D:\Arpa\business automation ltd\sadi vai\Testpilot\resources\views/layouts/backend/components/header.blade.php ENDPATH**/ ?>