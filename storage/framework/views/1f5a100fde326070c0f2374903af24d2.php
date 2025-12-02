<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    
    <title><?php echo $__env->yieldContent('title', ($appName ?? config('app.name', 'LaraKit')) . ' - Modern Laravel Starter Kit'); ?></title>
    
    
    <?php if($appFavicon ?? null): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo e(Storage::url($appFavicon)); ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <?php endif; ?>
    
    
    <meta name="description" content="<?php echo $__env->yieldContent('description', setting('meta_description', 'LaraKit - A modern Laravel starter kit with modular architecture, role-based permissions, and comprehensive settings management.')); ?>">
    <meta name="keywords" content="<?php echo e(setting('meta_keywords', 'laravel, starter kit, admin dashboard, modular, role permissions')); ?>">
    <meta name="author" content="<?php echo e(setting('meta_author', $appName ?? config('app.name'))); ?>">
    
    
    <meta property="og:title" content="<?php echo $__env->yieldContent('og_title', setting('og_title', ($appName ?? config('app.name')) . ' - Modern Laravel Starter Kit')); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('og_description', setting('og_description', 'A powerful Laravel starter kit with everything you need to build modern web applications.')); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <?php if(setting('og_image')): ?>
        <meta property="og:image" content="<?php echo e(Storage::url(setting('og_image'))); ?>">
    <?php endif; ?>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cyan: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            200: '#a5f3fc',
                            300: '#67e8f9',
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63',
                        }
                    }
                }
            }
        }
    </script>
    
    
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
        }
        
        .hover-scale {
            transition: transform 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: scale(1.05);
        }
        
        .section-padding {
            padding: 80px 0;
        }
        
        @media (max-width: 768px) {
            .section-padding {
                padding: 40px 0;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <div class="flex items-center space-x-3">
                    <?php if($appLogo ?? null): ?>
                        <img src="<?php echo e(Storage::url($appLogo)); ?>" alt="<?php echo e($appName ?? config('app.name')); ?>" class="h-10 w-10 object-contain">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-lg gradient-bg flex items-center justify-center">
                            <i class="fas fa-cube text-white text-xl"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800"><?php echo e($appName ?? config('app.name', 'LaraKit')); ?></h1>
                        <?php if($appTagline ?? setting('app_tagline')): ?>
                            <p class="text-xs text-gray-500"><?php echo e($appTagline ?? setting('app_tagline')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?php echo e(route('landing.index')); ?>" class="text-gray-700 hover:text-cyan-600 font-medium transition <?php echo e(Request::is('/') ? 'text-cyan-600' : ''); ?>">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                    <a href="<?php echo e(route('landing.docs')); ?>" class="text-gray-700 hover:text-cyan-600 font-medium transition <?php echo e(Request::is('docs') ? 'text-cyan-600' : ''); ?>">
                        <i class="fas fa-book mr-1"></i> Documentation
                    </a>
                    <a href="<?php echo e(url('/dashboard')); ?>" class="px-4 py-2 rounded-lg gradient-bg text-white font-medium hover:shadow-lg transition">
                        <i class="fas fa-sign-in-alt mr-2"></i> Dashboard
                    </a>
                </div>
                
                
                <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-cyan-600">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        
        
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200">
            <div class="px-4 py-4 space-y-3">
                <a href="<?php echo e(route('landing.index')); ?>" class="block text-gray-700 hover:text-cyan-600 font-medium">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
                <a href="<?php echo e(route('landing.docs')); ?>" class="block text-gray-700 hover:text-cyan-600 font-medium">
                    <i class="fas fa-book mr-2"></i> Documentation
                </a>
                <a href="<?php echo e(url('/dashboard')); ?>" class="block px-4 py-2 rounded-lg gradient-bg text-white font-medium text-center">
                    <i class="fas fa-sign-in-alt mr-2"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>
    
    
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    
    
    <footer class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <?php if($appLogo ?? null): ?>
                            <img src="<?php echo e(Storage::url($appLogo)); ?>" alt="<?php echo e($appName ?? config('app.name')); ?>" class="h-10 w-10 object-contain">
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-lg gradient-bg flex items-center justify-center">
                                <i class="fas fa-cube text-white text-xl"></i>
                            </div>
                        <?php endif; ?>
                        <h3 class="text-xl font-bold text-white"><?php echo e($appName ?? config('app.name', 'LaraKit')); ?></h3>
                    </div>
                    <p class="text-gray-400 mb-4">
                        <?php echo e(setting('app_description', 'A modern Laravel starter kit with modular architecture, role-based permissions, and comprehensive settings management.')); ?>

                    </p>
                    <?php if(setting('contact_email') || setting('contact_phone')): ?>
                        <div class="space-y-2">
                            <?php if(setting('contact_email')): ?>
                                <p class="text-sm">
                                    <i class="fas fa-envelope mr-2 text-cyan-500"></i>
                                    <?php echo e(setting('contact_email')); ?>

                                </p>
                            <?php endif; ?>
                            <?php if(setting('contact_phone')): ?>
                                <p class="text-sm">
                                    <i class="fas fa-phone mr-2 text-cyan-500"></i>
                                    <?php echo e(setting('contact_phone')); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="<?php echo e(route('landing.index')); ?>" class="hover:text-cyan-400 transition">Home</a></li>
                        <li><a href="<?php echo e(route('landing.docs')); ?>" class="hover:text-cyan-400 transition">Documentation</a></li>
                        <li><a href="<?php echo e(url('/dashboard')); ?>" class="hover:text-cyan-400 transition">Dashboard</a></li>
                        <li><a href="<?php echo e(route('settings.index')); ?>" class="hover:text-cyan-400 transition">Settings</a></li>
                    </ul>
                </div>
                
                
                <div>
                    <h4 class="text-white font-semibold mb-4">Connect</h4>
                    <div class="flex space-x-4">
                        <?php if(setting('facebook_url')): ?>
                            <a href="<?php echo e(setting('facebook_url')); ?>" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(setting('twitter_url')): ?>
                            <a href="<?php echo e(setting('twitter_url')); ?>" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(setting('linkedin_url')): ?>
                            <a href="<?php echo e(setting('linkedin_url')); ?>" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(setting('github_url', 'https://github.com')): ?>
                            <a href="<?php echo e(setting('github_url', 'https://github.com')); ?>" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition">
                                <i class="fab fa-github"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            
            <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-400">
                    <?php echo e($copyrightText ?? setting('copyright_text', '© ' . date('Y') . ' ' . ($appName ?? config('app.name')) . '. All rights reserved.')); ?>

                </p>
                <?php if($footerText ?? setting('footer_text')): ?>
                    <p class="text-sm text-gray-400 mt-2 md:mt-0">
                        <?php echo e($footerText ?? setting('footer_text')); ?>

                    </p>
                <?php endif; ?>
            </div>
        </div>
    </footer>
    
    
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\Arpa Nihan_personal\code_study\Testpilot\app\Modules/DemoFrontend/resources/views/layouts/frontend.blade.php ENDPATH**/ ?>