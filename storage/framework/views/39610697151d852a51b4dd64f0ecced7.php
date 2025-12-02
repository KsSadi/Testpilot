
<div class="breadcrumb-section flex items-center justify-between mb-4">
    <div class="flex items-center space-x-2 text-sm">
        <a href="<?php echo e(url('/dashboard')); ?>" class="text-gray-500 hover:text-cyan-600">Home</a>
        <?php if(isset($breadcrumbs) && is_array($breadcrumbs)): ?>
            <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <?php if(isset($breadcrumb['url'])): ?>
                    <a href="<?php echo e($breadcrumb['url']); ?>" class="text-gray-500 hover:text-cyan-600"><?php echo e($breadcrumb['title']); ?></a>
                <?php else: ?>
                    <span class="text-gray-800 font-medium"><?php echo e($breadcrumb['title']); ?></span>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="text-gray-800 font-medium"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></span>
        <?php endif; ?>
    </div>
    <div class="flex items-center space-x-3 text-sm text-gray-600">
        <div class="flex items-center space-x-2">
            <i class="fas fa-calendar text-cyan-600"></i>
            <span><?php echo e(now()->format('F j, Y')); ?></span>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-clock text-cyan-600"></i>
            <span id="currentTime"><?php echo e(now()->format('g:i A')); ?></span>
        </div>
    </div>
</div>
<?php /**PATH E:\Arpa Nihan_personal\code_study\Testpilot\resources\views/layouts/backend/components/breadcrumb.blade.php ENDPATH**/ ?>