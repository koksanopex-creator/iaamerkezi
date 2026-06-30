<div>
    <label for="widget-<?php echo e($index); ?>-text" class="block text-lg font-semibold text-gray-800">
        <?php echo e($config['title'] ?? 'Açıklama'); ?>

        <!--[if BLOCK]><![endif]--><?php if($config['required'] ?? false): ?> <span class="text-red-500">*</span> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </label>
    <p class="text-sm text-gray-500 mb-2">Lütfen bu adımla ilgili detayları, raporları veya notlarınızı buraya girin.</p>
    <div class="mt-1">
        <textarea wire:model="formData.<?php echo e($index); ?>.text" 
                  id="widget-<?php echo e($index); ?>-text" 
                  rows="10" 
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  <?php if($config['required'] ?? false): ?> required <?php endif; ?>
        ></textarea>
    </div>
    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["formData.{$index}.text"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/project/widgets/_textbox.blade.php ENDPATH**/ ?>