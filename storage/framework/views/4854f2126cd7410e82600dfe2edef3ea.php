

<?php $__env->startSection('title', $pageTitle); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Projects</h1>
            <p class="text-gray-600 mt-1">Manage your testing projects</p>
        </div>
        <a href="<?php echo e(route('projects.create')); ?>" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>New Project</span>
        </a>
    </div>

    
    <?php if(session('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span><?php echo e(session('success')); ?></span>
    </div>
    <?php endif; ?>

    
    <?php if($projects->count() > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo e($project->name); ?></h3>
                        <?php if($project->description): ?>
                        <p class="text-gray-600 text-sm mb-3"><?php echo e(Str::limit($project->description, 100)); ?></p>
                        <?php endif; ?>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span><i class="fas fa-flask mr-1"></i> <?php echo e($project->testCases->count()); ?> test cases</span>
                            <span><i class="fas fa-clock mr-1"></i> <?php echo e($project->created_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2 pt-4 border-t border-gray-100">
                    <a href="<?php echo e(route('projects.show', $project)); ?>" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <a href="<?php echo e(route('projects.edit', $project)); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm font-medium">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="<?php echo e(route('projects.destroy', $project)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this project? This will also delete all test cases.');" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-12 text-center">
        <div class="mb-4">
            <i class="fas fa-folder-open text-6xl text-gray-300"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Projects Yet</h3>
        <p class="text-gray-600 mb-6">Get started by creating your first project</p>
        <a href="<?php echo e(route('projects.create')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i>
            <span>Create Your First Project</span>
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Arpa Nihan_personal\code_study\Testpilot\app\Modules/Project/resources/views/index.blade.php ENDPATH**/ ?>