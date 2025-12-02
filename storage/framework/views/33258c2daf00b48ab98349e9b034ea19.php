

<?php $__env->startSection('title', $pageTitle); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo e($project->name); ?></h1>
            <?php if($project->description): ?>
            <p class="text-gray-600 mt-1"><?php echo e($project->description); ?></p>
            <?php endif; ?>
            <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                <span><i class="fas fa-clock mr-1"></i> Created <?php echo e($project->created_at->diffForHumans()); ?></span>
                <span><i class="fas fa-flask mr-1"></i> <?php echo e($project->testCases->count()); ?> test cases</span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('projects.edit', $project)); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <form action="<?php echo e(route('projects.destroy', $project)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this project? This will also delete all test cases.');" class="inline">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    
    <?php if(session('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span><?php echo e(session('success')); ?></span>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Test Cases</h2>
            <a href="<?php echo e(route('projects.test-cases.create', $project)); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-1"></i> New Test Case
            </a>
        </div>

        <?php if($project->testCases->count() > 0): ?>
        <div class="divide-y divide-gray-200">
            <?php $__currentLoopData = $project->testCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testCase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="p-4 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1"><?php echo e($testCase->name); ?></h3>
                        <?php if($testCase->url): ?>
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-link mr-1"></i>
                            <a href="<?php echo e($testCase->url); ?>" target="_blank" class="text-blue-600 hover:underline"><?php echo e($testCase->url); ?></a>
                        </p>
                        <?php endif; ?>
                        <?php if($testCase->description): ?>
                        <p class="text-sm text-gray-600"><?php echo e($testCase->description); ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-clock mr-1"></i> Created <?php echo e($testCase->created_at->diffForHumans()); ?>

                        </p>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="<?php echo e(route('cypress.index', ['test_case_id' => $testCase->id])); ?>" 
                           class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition font-medium">
                            <i class="fas fa-play mr-1"></i> Run Test
                        </a>
                        <a href="<?php echo e(route('projects.test-cases.edit', [$project, $testCase])); ?>" 
                           class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?php echo e(route('projects.test-cases.destroy', [$project, $testCase])); ?>" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this test case?');"
                              class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        
        <div class="p-12 text-center">
            <div class="mb-4">
                <i class="fas fa-flask text-6xl text-gray-300"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No Test Cases Yet</h3>
            <p class="text-gray-600 mb-6">Create your first test case to get started</p>
            <a href="<?php echo e(route('projects.test-cases.create', $project)); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus"></i>
                <span>Create Test Case</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Arpa Nihan_personal\code_study\Testpilot\app\Modules/Project/resources/views/show.blade.php ENDPATH**/ ?>