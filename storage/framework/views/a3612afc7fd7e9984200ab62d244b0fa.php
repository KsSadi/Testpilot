<?php $__env->startSection('title', 'Create Test Case'); ?>

<?php $__env->startSection('content'); ?>
<div style="padding: 24px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 24px;">Create New Test Case</h1>

        <form action="<?php echo e(route('test-cases.store', [$project, $module])); ?>" method="POST" style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px;">
            <?php echo csrf_field(); ?>

            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Test Case Name *</label>
                <input type="text" name="name" id="name" required value="<?php echo e(old('name')); ?>" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Description</label>
                <textarea name="description" id="description" rows="4" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;"><?php echo e(old('description')); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="order" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Execution Order *</label>
                <input type="number" name="order" id="order" required value="<?php echo e(old('order', $nextOrder)); ?>" min="0" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 4px;">Lower numbers execute first. Test cases will share session in order.</p>
                <?php $__errorArgs = ['order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="status" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Status *</label>
                <select name="status" id="status" required style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                    <option value="active" <?php echo e(old('status', 'active') === 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="inactive" <?php echo e(old('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                </select>
                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Create Test Case
                </button>
                <a href="<?php echo e(route('modules.show', [$project, $module])); ?>" style="padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Arpa\business automation ltd\sadi vai\Testpilot\app\Modules/Cypress/resources/views/test-cases/create.blade.php ENDPATH**/ ?>