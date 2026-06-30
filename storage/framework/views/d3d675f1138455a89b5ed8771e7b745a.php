<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900">
            <?php echo e(__('Gözlemci Yönetimi (Bölüm Gözlemcisi)')); ?>

        </h2>
        <p class="mt-1 text-sm text-gray-600">
            <?php echo e(__('Sizi izlemesine izin verdiğiniz personelleri buradan yönetebilirsiniz. Gözlemciler sizin yetkilerinizle sistemi salt okunur olarak görebilir.')); ?>

        </p>
    </header>

    <?php if(auth()->user()->observers->isNotEmpty()): ?>
        <div class="mt-4 p-4 bg-indigo-50 border border-indigo-100 rounded-xl flex items-start gap-3 animate-fade-in">
            <div class="mt-0.5 text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="text-sm text-indigo-800">
                <p>Şu an <strong><?php echo e(auth()->user()->observers->count()); ?> personel</strong> profilinizi izleme yetkisine sahip: 
                   <span class="font-bold text-indigo-900"><?php echo e(auth()->user()->observers->pluck('name')->join(', ')); ?></span>.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <?php if(Auth::user()->isShadowing()): ?>
        <div class="mt-6 p-6 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 flex items-center gap-4">
            <div class="p-3 bg-amber-100 rounded-full">
                <span class="text-2xl">⚠️</span>
            </div>
            <div>
                <p class="text-sm font-bold">Kısıtlı Alan</p>
                <p class="text-sm opacity-90">Başka bir kullanıcının hesabını izlerken (Shadowing) gözlemci ayarlarını değiştiremezsiniz. Bu alan sadece kendi hesabınızdayken erişilebilirdir.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="mt-6 space-y-6">
            
            <form action="<?php echo e(route('observer.add')); ?>" method="POST" class="flex flex-col sm:flex-row gap-3 items-end">
                <?php echo csrf_field(); ?>
                <div class="flex-1 w-full">
                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'observer_id','value' => __('Yeni Gözlemci Ekle')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'observer_id','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Yeni Gözlemci Ekle'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                    <select id="observer_id" name="observer_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm select2-searchable">
                        <option value=""><?php echo e(__('Personel Seçin...')); ?></option>
                        <?php $__currentLoopData = \App\Models\User::where('id', '!=', auth()->id())->whereNull('customer_id')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->email); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['class' => 'sm:mb-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'sm:mb-0.5']); ?>
                    <?php echo e(__('Ekle')); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
            </form>

            
            <div class="bg-gray-50 rounded-xl border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider"><?php echo e(__('Personel')); ?></th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider"><?php echo e(__('Eklenme Tarihi')); ?></th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider"><?php echo e(__('İşlem')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = auth()->user()->observers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $observer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                            <?php echo e(substr($observer->name, 0, 1)); ?>

                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900"><?php echo e($observer->name); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo e($observer->email); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e(($observer->pivot && $observer->pivot->created_at) ? $observer->pivot->created_at->format('d.m.Y H:i') : '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="<?php echo e(route('observer.remove', $observer->id)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" onclick="return confirm('Bu personelin gözlemci yetkisini kaldırmak istediğinize emin misiniz?')" class="text-red-600 hover:text-red-900">
                                            <?php echo e(__('Kaldır')); ?>

                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">
                                    <?php echo e(__('Henüz size atanmış bir gözlemci bulunmamaktadır.')); ?>

                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php $__env->startPush('scripts'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-searchable').select2({
            placeholder: "Personel arayın...",
            allowClear: true,
            theme: "classic"
        });
    });
</script>
<style>
    .select2-container--classic .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding-top: 6px !important;
    }
    .select2-container--classic .select2-selection--single .select2-selection__arrow {
        top: 8px !important;
    }
</style>
<?php $__env->stopPush(); ?>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/profile/partials/manage-observers.blade.php ENDPATH**/ ?>