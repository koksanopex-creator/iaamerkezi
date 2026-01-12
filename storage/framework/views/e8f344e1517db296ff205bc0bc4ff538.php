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
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <div class="bg-yellow-100 p-2 rounded-lg text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <?php echo e(__('Arabulucu Düzenle')); ?>

            </h2>
            <a href="<?php echo e(route('admin.arabulucular.index')); ?>" class="text-gray-500 hover:text-gray-700 font-medium text-sm flex items-center transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Listeye Dön
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                
                <div class="lg:col-span-2">
                    <div class="bg-white p-8 shadow-lg rounded-xl border border-gray-100">
                        
                        <div class="mb-6 border-b border-gray-100 pb-4 flex justify-between items-end">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Kişisel Bilgiler</h3>
                                <p class="text-sm text-gray-500">Mevcut bilgileri aşağıdan güncelleyebilirsiniz.</p>
                            </div>
                            <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded text-gray-500">ID: <?php echo e($arabulucu->id); ?></span>
                        </div>

                        
                        <?php if($errors->any()): ?>
                            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700 font-bold">Lütfen aşağıdaki hataları düzeltin:</p>
                                        <ul class="mt-1 list-disc list-inside text-sm text-red-600">
                                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($error); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo e(route('admin.arabulucular.update', $arabulucu->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?> 

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ad Soyad <span class="text-red-500">*</span></label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <input type="text" name="name" value="<?php echo e(old('name', $arabulucu->name)); ?>" class="focus:ring-yellow-500 focus:border-yellow-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" required>
                                    </div>
                                </div>

                                
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sicil No <span class="text-red-500">*</span></label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                        </div>
                                        <input type="text" name="sicil_no" value="<?php echo e(old('sicil_no', $arabulucu->sicil_no)); ?>" class="focus:ring-yellow-500 focus:border-yellow-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" required>
                                    </div>
                                </div>

                                
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-Posta Adresi</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                                        </div>
                                        <input type="email" name="email" value="<?php echo e(old('email', $arabulucu->email)); ?>" class="focus:ring-yellow-500 focus:border-yellow-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2">
                                    </div>
                                </div>

                                
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Telefon</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <input type="text" name="telefon" value="<?php echo e(old('telefon', $arabulucu->telefon)); ?>" class="focus:ring-yellow-500 focus:border-yellow-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2">
                                    </div>
                                </div>

                                
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Şehir / İl <span class="text-red-500">*</span></label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <input type="text" name="sehir" value="<?php echo e(old('sehir', $arabulucu->sehir)); ?>" class="focus:ring-yellow-500 focus:border-yellow-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" required>
                                    </div>
                                </div>

                                
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Açık Adres</label>
                                    <textarea name="adres" rows="3" class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md"><?php echo e(old('adres', $arabulucu->adres)); ?></textarea>
                                </div>

                            </div>

                            <div class="mt-8 flex justify-between items-center">
                                
                                <div>
                                    
                                </div>

                                <div class="flex gap-3">
                                    <a href="<?php echo e(route('admin.arabulucular.index')); ?>" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Vazgeç
                                    </a>
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        Değişiklikleri Kaydet
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                
                <div class="lg:col-span-1">
                    <div class="space-y-6">
                        
                        
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Performans Özeti
                            </h4>
                            
                            <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Atanan Toplam Dosya</span>
                                <span class="text-lg font-bold text-indigo-600"><?php echo e($arabulucu->cases()->count()); ?></span>
                            </div>
                            
                            <div class="text-xs text-gray-400 italic">
                                * Bu arabulucuya ait sistemdeki toplam dosya sayısıdır.
                            </div>
                        </div>

                        
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                            <h4 class="font-bold text-gray-700 mb-3 text-sm">Sistem Kaydı</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Kayıt Tarihi:</span>
                                    <span class="font-mono text-gray-700"><?php echo e($arabulucu->created_at->format('d.m.Y H:i')); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Son Güncelleme:</span>
                                    <span class="font-mono text-gray-700"><?php echo e($arabulucu->updated_at->format('d.m.Y H:i')); ?></span>
                                </div>
                            </div>
                        </div>

                    </div>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabulucular/edit.blade.php ENDPATH**/ ?>