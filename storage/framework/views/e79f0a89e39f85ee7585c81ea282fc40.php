
<div class="mt-6 pt-6 border-t border-gray-200">
    <div class="flex flex-col md:flex-row items-center justify-between text-xs md:text-sm text-gray-500 gap-4">
        <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-4 text-center">
            <span><?php echo e($copyrightText ?? '© ' . date('Y') . ' ' . ($appName ?? config('app.name', 'Dashboard')) . '. All rights reserved.'); ?></span>
            <?php if($footerText ?? null): ?>
                <span class="hidden sm:inline">•</span>
                <span><?php echo e($footerText); ?></span>
            <?php endif; ?>
            <span class="hidden sm:inline">•</span>
            <a href="<?php echo e(url('/privacy-policy')); ?>" class="text-cyan-600 hover:text-cyan-700 hover:underline transition">Privacy Policy</a>
            <span class="hidden sm:inline">•</span>
            <a href="<?php echo e(url('/terms-of-service')); ?>" class="text-cyan-600 hover:text-cyan-700 hover:underline transition">Terms of Service</a>
        </div>
        <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-4">
            <span>Version <?php echo e(config('app.version', '1.0.0')); ?></span>
            <span class="hidden sm:inline">•</span>
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-green-600 font-medium">All Systems Operational</span>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\Arpa\business automation ltd\sadi vai\Testpilot\resources\views/layouts/backend/components/footer.blade.php ENDPATH**/ ?>