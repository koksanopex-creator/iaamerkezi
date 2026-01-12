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
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Yeni Arabuluculuk Dosyası Başlat')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                
                <?php if($errors->any()): ?>
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4">
                        <p class="font-bold">Dikkat!</p>
                        <ul class="list-disc ml-5 text-sm">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('admin.arabuluculuk.store')); ?>" method="POST" enctype="multipart/form-data" 
                      x-data="{ type: 'ihtiyari' }">
                    <?php echo csrf_field(); ?>

                    <div class="space-y-6">
                        
                        
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Süreç Türü Seçiniz <span class="text-red-500">*</span></label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('arabuluculuk.create_ihtiyari')): ?>
                                <label class="relative border-2 rounded-lg p-4 cursor-pointer hover:bg-white transition flex items-start"
                                    :class="type === 'ihtiyari' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                                    <div class="flex items-center h-5">
                                        <input type="radio" name="type" value="ihtiyari" x-model="type" class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <span class="block font-bold text-gray-900">İhtiyari (Gönüllü) Arabuluculuk</span>
                                        <span class="block text-gray-500 mt-1">Personel birimi yönetir. Taraflar gönüllü olarak masaya oturur.</span>
                                    </div>
                                </label>
                                <?php endif; ?>

                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('arabuluculuk.create_zorunlu')): ?>
                                <label class="relative border-2 rounded-lg p-4 cursor-pointer hover:bg-white transition flex items-start"
                                    :class="type === 'zorunlu' ? 'border-red-500 bg-red-50' : 'border-gray-200'">
                                    <div class="flex items-center h-5">
                                        <input type="radio" name="type" value="zorunlu" x-model="type" class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <span class="block font-bold text-gray-900">Zorunlu (Dava Şartı)</span>
                                        <span class="block text-gray-500 mt-1">Hukuk birimi yönetir. Mahkeme öncesi zorunlu adımdır.</span>
                                    </div>
                                </label>
                                <?php endif; ?>

                                
                                <?php if(!auth()->user()->can('arabuluculuk.create_ihtiyari') && !auth()->user()->can('arabuluculuk.create_zorunlu')): ?>
                                    <div class="col-span-2 text-center text-red-500 font-bold p-4 bg-red-50 rounded">
                                        Dosya oluşturma yetkiniz bulunmamaktadır.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">İlgili Çalışan <span class="text-red-500">*</span></label>
                                <select name="calisan_user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Çalışan Seçiniz...</option>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->email); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Talep Edilen Tutar (TL)</label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" step="0.01" name="talep_tutari" class="w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">TL</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        
                        
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('arabuluculuk.create_zorunlu')): ?>
                            <div x-show="type === 'zorunlu'" x-transition x-cloak
                                 class="bg-red-50 border border-red-200 rounded-lg p-6 mt-6">
                                
                                <h4 class="font-bold text-red-800 border-b border-red-200 pb-2 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Resmi Dosya Bilgileri
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dosya No (Zorunlu) <span class="text-red-500">*</span></label>
                                        <input type="text" name="dosya_no" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Örn: 2024/123">
                                    </div>

                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Atanan Arabulucu <span class="text-red-500">*</span></label>
                                        <select name="arabulucu_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            <option value="">Seçiniz...</option>
                                            <?php $__currentLoopData = $arabulucular; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arabulucu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($arabulucu->id); ?>"><?php echo e($arabulucu->name); ?> (<?php echo e($arabulucu->sehir); ?>)</option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Şirket İçi Avukat</label>
                                        <select name="internal_lawyer_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            <option value="">Seçiniz...</option>
                                            <?php $__currentLoopData = $internalLawyers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lawyer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($lawyer->id); ?>"><?php echo e($lawyer->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dış Avukat (Opsiyonel)</label>
                                        <select name="external_lawyer_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            <option value="">Atanmayacak</option>
                                            <?php $__currentLoopData = $externalLawyers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lawyer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($lawyer->id); ?>"><?php echo e($lawyer->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    
                    <div class="mt-8 flex justify-end gap-3 border-t pt-4">
                        <a href="<?php echo e(route('admin.arabuluculuk.index')); ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 transition">İptal</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 shadow-lg transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span x-text="type === 'ihtiyari' ? 'İhtiyari Dosya Oluştur' : 'Zorunlu Dosya Başlat'"></span>
                        </button>
                    </div>
                </form>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabuluculuk/create.blade.php ENDPATH**/ ?>