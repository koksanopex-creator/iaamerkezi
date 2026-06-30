<?php
    // Kullanıcıları bir kez çekip cache'leyebiliriz, şimdilik direkt çekiyoruz
    // Sorumlu kişi listesinden Superadmin, Yonetim ve Müşteri rollerini hariç tutuyoruz
    $users = \App\Models\User::where('onaylandi_mi', true)
        ->whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['Superadmin', 'Yonetim', 'Müşteri', 'Musteri']);
        })
        ->orderBy('name')->get();
?>
<div>
    <label for="widget-<?php echo e($index); ?>-user" class="block text-lg font-semibold text-gray-800">
        <?php echo e($config['title'] ?? 'Sorumlu Kişi'); ?>

        <!--[if BLOCK]><![endif]--><?php if($config['required'] ?? false): ?> <span class="text-red-500">*</span> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </label>
    <div class="mt-1">
        <select wire:model="formData.<?php echo e($index); ?>.user_id" 
                id="widget-<?php echo e($index); ?>-user" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                <?php if($config['required'] ?? false): ?> required <?php endif; ?>
                >
            <option value="">-- Kullanıcı Seçin --</option>
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </select>
    </div>
    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["formData.{$index}.user_id"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/project/widgets/_user-select.blade.php ENDPATH**/ ?>