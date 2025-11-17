<div>
    <label for="widget-<?php echo e($index); ?>-date" class="block text-lg font-semibold text-gray-800">
        <?php echo e($config['title'] ?? 'Termin Tarihi'); ?>

        <!--[if BLOCK]><![endif]--><?php if($config['required'] ?? false): ?> <span class="text-red-500">*</span> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </label>
    <div class="mt-1">
        <input type="date" 
               wire:model="formData.<?php echo e($index); ?>.date" 
               id="widget-<?php echo e($index); ?>-date" 
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
               <?php if($config['required'] ?? false): ?> required <?php endif; ?>
               >
    </div>
    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["formData.{$index}.date"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/widgets/_date-picker.blade.php ENDPATH**/ ?>