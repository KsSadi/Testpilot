<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo $__env->yieldContent('title', $appName ?? config('app.name', 'Dashboard')); ?></title>
    
    
    <?php if($appFavicon ?? null): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo e(Storage::url($appFavicon)); ?>">
    <?php endif; ?>
    
    
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', setting('meta_description', '')); ?>">
    <meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords', setting('meta_keywords', '')); ?>">
    <meta name="author" content="<?php echo e(setting('meta_author', $appName ?? config('app.name'))); ?>">
    
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <link rel="stylesheet" href="<?php echo e(asset('assets/backend/css/dashboard.css')); ?>">
    
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-50">
    
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <div class="flex h-screen overflow-hidden bg-gray-50">
        
        <?php echo $__env->make('layouts.backend.components.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <?php echo $__env->make('layouts.backend.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <main class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar">
                
                <?php if(!isset($hideBreadcrumb) || !$hideBreadcrumb): ?>
                    <?php echo $__env->make('layouts.backend.components.breadcrumb', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>

                
                <?php echo $__env->yieldContent('content'); ?>

                
                <?php echo $__env->make('layouts.backend.components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </main>
        </div>
    </div>
    
    
    <script src="<?php echo e(asset('assets/backend/js/dashboard.js')); ?>"></script>
    
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\Arpa Nihan_personal\code_study\Testpilot\resources\views/layouts/backend/master.blade.php ENDPATH**/ ?>