<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Takımı Düzenle: <span class="text-indigo-600"><?php echo e($takim->ad); ?></span>
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">
                    
                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight mb-6">Takım Bilgilerini Güncelle</h3>

                    <form action="<?php echo e(route('admin.takim-yonetim.update', $takim)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="space-y-6">
                            
                            
                            <div>
                                <label for="ad" class="block text-sm font-medium text-gray-700">Takım Adı <span class="text-red-500">*</span></label>
                                <input type="text" name="ad" id="ad" value="<?php echo e(old('ad', $takim->ad)); ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <?php $__errorArgs = ['ad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div>
                                <label for="lider_user_id" class="block text-sm font-medium text-gray-700">Takım Lideri <span class="text-red-500">*</span></label>
                                <select name="lider_user_id" id="lider_user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <?php $__currentLoopData = $kullanicilar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kullanici): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($kullanici->id); ?>" <?php echo e(old('lider_user_id', $takim->lider_user_id) == $kullanici->id ? 'selected' : ''); ?>>
                                            <?php echo e($kullanici->name); ?> (<?php echo e($kullanici->email); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['lider_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div>
                                <label for="amac" class="block text-sm font-medium text-gray-700">Takımın Amacı</label>
                                <textarea name="amac" id="amac" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?php echo e(old('amac', $takim->amac)); ?></textarea>
                            </div>
                            
                            
                            <div><label for="vizyon" class="block text-sm font-medium text-gray-700">Vizyon</label><textarea name="vizyon" id="vizyon" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?php echo e(old('vizyon', $takim->vizyon)); ?></textarea></div>
                            <div><label for="misyon" class="block text-sm font-medium text-gray-700">Misyon</label><textarea name="misyon" id="misyon" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?php echo e(old('misyon', $takim->misyon)); ?></textarea></div>
                            <div><label for="kurallar" class="block text-sm font-medium text-gray-700">Kurallar</label><textarea name="kurallar" id="kurallar" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?php echo e(old('kurallar', $takim->kurallar)); ?></textarea></div>

                            
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                                <a href="<?php echo e(route('admin.takim-yonetim.index')); ?>" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm">İptal</a>
                                <button type="submit" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow-sm">Değişiklikleri Kaydet</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/takim-yonetim/edit.blade.php ENDPATH**/ ?>