<?php $__env->startSection('title', ($appName ?? config('app.name')) . ' - Modern Laravel Starter Kit'); ?>

<?php $__env->startSection('content'); ?>
    
    <section class="relative overflow-hidden bg-gradient-to-br from-cyan-50 via-blue-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 section-padding">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                        Welcome to
                        <span class="gradient-text"><?php echo e($appName ?? config('app.name', 'LaraKit')); ?></span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        <?php echo e($appTagline ?? setting('app_tagline', 'Modern Laravel Starter Kit')); ?>

                    </p>
                    <p class="text-lg text-gray-500 mb-8">
                        A powerful Laravel starter kit with modular architecture, role-based permissions, comprehensive settings management, and multi-authentication system. Everything you need to kickstart your next Laravel project.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="<?php echo e(route('landing.docs')); ?>" class="px-8 py-4 gradient-bg text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                            <i class="fas fa-book mr-2"></i> View Documentation
                        </a>
                        <a href="<?php echo e(url('/dashboard')); ?>" class="px-8 py-4 bg-white text-cyan-600 font-semibold rounded-lg shadow-lg hover:shadow-xl transition border-2 border-cyan-600 transform hover:-translate-y-1">
                            <i class="fas fa-rocket mr-2"></i> Try Dashboard
                        </a>
                    </div>
                </div>
                
                
                <div class="relative">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-2xl p-6 shadow-xl hover-scale">
                            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mb-4">
                                <i class="fas fa-layer-group text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800 mb-2">Modular</h3>
                            <p class="text-sm text-gray-500">Clean architecture</p>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-xl hover-scale mt-8">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center mb-4">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800 mb-2">Secure</h3>
                            <p class="text-sm text-gray-500">Role-based access</p>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-xl hover-scale">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-green-500 to-teal-500 flex items-center justify-center mb-4">
                                <i class="fas fa-cog text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800 mb-2">Configurable</h3>
                            <p class="text-sm text-gray-500">50+ settings</p>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-xl hover-scale mt-8">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center mb-4">
                                <i class="fas fa-rocket text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800 mb-2">Fast</h3>
                            <p class="text-sm text-gray-500">Optimized</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full gradient-bg mb-4">
                            <i class="fas <?php echo e($stat['icon']); ?> text-white text-2xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2"><?php echo e($stat['value']); ?></h3>
                        <p class="text-gray-600"><?php echo e($stat['label']); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section class="section-padding bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Powerful Features Out of the Box
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to build modern web applications, already configured and ready to use.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover-scale border border-gray-100">
                        <div class="w-14 h-14 rounded-lg bg-gradient-to-br <?php echo e($feature['color']); ?> flex items-center justify-center mb-4">
                            <i class="fas <?php echo e($feature['icon']); ?> text-white text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3"><?php echo e($feature['title']); ?></h3>
                        <p class="text-gray-600 text-sm"><?php echo e($feature['description']); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section class="section-padding bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Flexible Authentication System
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Multiple authentication methods configured and ready to use. Enable or disable any method from settings.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <?php $__currentLoopData = $authMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-gray-50 rounded-xl p-6 text-center border-2 <?php echo e($method['enabled'] ? 'border-' . $method['color'] . '-500 bg-' . $method['color'] . '-50' : 'border-gray-200'); ?> transition-all">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full <?php echo e($method['enabled'] ? 'bg-' . $method['color'] . '-100' : 'bg-gray-200'); ?> mb-4">
                            <i class="<?php echo e($method['icon']); ?> text-2xl <?php echo e($method['enabled'] ? 'text-' . $method['color'] . '-600' : 'text-gray-400'); ?>"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2"><?php echo e($method['name']); ?></h3>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium <?php echo e($method['enabled'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'); ?>">
                            <?php echo e($method['enabled'] ? 'Enabled' : 'Disabled'); ?>

                        </span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <div class="text-center mt-12">
                <p class="text-gray-600 mb-4">Configure authentication methods from Settings → Auth & Security</p>
                <a href="<?php echo e(route('settings.index', ['tab' => 'auth'])); ?>" class="inline-flex items-center text-cyan-600 hover:text-cyan-700 font-medium">
                    Go to Auth Settings <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    
    <section class="section-padding bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Built with Modern Technologies
                </h2>
                <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                    Powered by the latest and greatest tools in the Laravel ecosystem
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4 hover:bg-gray-700 transition">
                        <i class="fab fa-laravel text-4xl text-red-500"></i>
                    </div>
                    <h4 class="font-semibold">Laravel 11</h4>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4 hover:bg-gray-700 transition">
                        <i class="fab fa-php text-4xl text-purple-400"></i>
                    </div>
                    <h4 class="font-semibold">PHP 8.2+</h4>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4 hover:bg-gray-700 transition">
                        <svg class="w-10 h-10 text-cyan-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.001,4.8c-3.2,0-5.2,1.6-6,4.8c1.2-1.6,2.6-2.2,4.2-1.8c0.913,0.228,1.565,0.89,2.288,1.624 C13.666,10.618,15.027,12,18.001,12c3.2,0,5.2-1.6,6-4.8c-1.2,1.6-2.6,2.2-4.2,1.8c-0.913-0.228-1.565-0.89-2.288-1.624 C16.337,6.182,14.976,4.8,12.001,4.8z M6.001,12c-3.2,0-5.2,1.6-6,4.8c1.2-1.6,2.6-2.2,4.2-1.8c0.913,0.228,1.565,0.89,2.288,1.624 c1.177,1.194,2.538,2.576,5.512,2.576c3.2,0,5.2-1.6,6-4.8c-1.2,1.6-2.6,2.2-4.2,1.8c-0.913-0.228-1.565-0.89-2.288-1.624 C10.337,13.382,8.976,12,6.001,12z"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold">Tailwind CSS</h4>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4 hover:bg-gray-700 transition">
                        <i class="fab fa-js text-4xl text-yellow-400"></i>
                    </div>
                    <h4 class="font-semibold">JavaScript</h4>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4 hover:bg-gray-700 transition">
                        <i class="fas fa-database text-4xl text-blue-400"></i>
                    </div>
                    <h4 class="font-semibold">MySQL</h4>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4 hover:bg-gray-700 transition">
                        <i class="fas fa-code text-4xl text-green-400"></i>
                    </div>
                    <h4 class="font-semibold">Blade</h4>
                </div>
            </div>
        </div>
    </section>

    
    <section class="section-padding bg-gradient-to-br from-cyan-500 via-blue-500 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-bold mb-6">
                Ready to Start Building?
            </h2>
            <p class="text-xl mb-8 opacity-90 max-w-3xl mx-auto">
                Get started with <?php echo e($appName ?? config('app.name')); ?> and build your next Laravel application faster than ever.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo e(route('landing.docs')); ?>" class="px-8 py-4 bg-white text-cyan-600 font-semibold rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                    <i class="fas fa-book mr-2"></i> Read Documentation
                </a>
                <a href="<?php echo e(url('/dashboard')); ?>" class="px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-lg shadow-lg hover:bg-white hover:text-cyan-600 transition transform hover:-translate-y-1">
                    <i class="fas fa-rocket mr-2"></i> Try Dashboard
                </a>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Add smooth animations on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('section').forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('DemoFrontend::layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Arpa Nihan_personal\code_study\Testpilot\app\Modules/DemoFrontend/resources/views/landing.blade.php ENDPATH**/ ?>