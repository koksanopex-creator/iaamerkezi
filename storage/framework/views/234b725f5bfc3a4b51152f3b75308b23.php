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
            Bölümü Düzenle: <?php echo e($bolum->ad); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    
                    
                    <?php
                        $isSuperadmin = Auth::user()->hasRole('Superadmin');
                    ?>

                    <form action="<?php echo e(route('admin.bolumler.update', ['bolum' => $bolum])); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        
                        <div class="mb-4">
                            <label for="ad" class="block text-gray-700 text-sm font-bold mb-2">Bölüm Adı:</label>
                            <input type="text" name="ad" id="ad" value="<?php echo e(old('ad', $bolum->ad)); ?>" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo e(!$isSuperadmin ? 'bg-gray-100 cursor-not-allowed' : ''); ?>" 
                                <?php echo e(!$isSuperadmin ? 'readonly' : ''); ?> required>
                        </div>
                        
                        
                        <div class="mb-4">
                            <label for="bolum_kategori_id" class="block text-gray-700 text-sm font-bold mb-2">Kategori:</label>
                            <select name="bolum_kategori_id" id="bolum_kategori_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo e(!$isSuperadmin ? 'bg-gray-100 cursor-not-allowed' : ''); ?>" <?php echo e(!$isSuperadmin ? 'disabled' : ''); ?>>
                                <option value="">Kategori Seçiniz (Opsiyonel)</option>
                                <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kategori->id); ?>" <?php echo e(old('bolum_kategori_id', $bolum->bolum_kategori_id) == $kategori->id ? 'selected' : ''); ?>>
                                        <?php echo e($kategori->ad); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="mb-4">
                            <label for="director_id" class="block text-gray-700 text-sm font-bold mb-2">Direktör (İsteğe Bağlı):</label>
                            <select name="director_id" id="director_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo e(!$isSuperadmin ? 'bg-gray-100 cursor-not-allowed' : ''); ?>" <?php echo e(!$isSuperadmin ? 'disabled' : ''); ?>>
                                <option value="">Direktör Seçiniz (Yok)</option>
                                <?php $__currentLoopData = $directors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $director): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($director->id); ?>" <?php echo e(old('director_id', $bolum->director_id) == $director->id ? 'selected' : ''); ?>>
                                        <?php echo e($director->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="mb-4">
                            <label for="logo_yolu" class="block text-gray-700 text-sm font-bold mb-2">Bölüm Logosu:</label>
                             <?php if($bolum->logo_yolu): ?>
                                <div class="mb-2">
                                    <img src="<?php echo e(Storage::url($bolum->logo_yolu)); ?>" alt="Mevcut Logo" class="h-16 w-16 object-cover rounded">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo_yolu" id="logo_yolu" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo e(!$isSuperadmin ? 'cursor-not-allowed' : ''); ?>" <?php echo e(!$isSuperadmin ? 'disabled' : ''); ?>>
                        </div>

                        
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="has_machines" value="1" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    <?php echo e(old('has_machines', $bolum->has_machines) ? 'checked' : ''); ?>

                                    <?php echo e(!$isSuperadmin ? 'disabled' : ''); ?>>
                                <span class="ml-2 text-gray-700 font-bold">Bu bölümde makine yönetimi yapılsın mı?</span>
                            </label>
                             <?php if(!$isSuperadmin && $bolum->has_machines): ?>
                                <input type="hidden" name="has_machines" value="1">
                            <?php endif; ?>
                        </div>

                        
                        <div class="mb-4">
                            <label for="is_active" class="block text-gray-700 text-sm font-bold mb-2">Durum:</label>
                            <select name="is_active" id="is_active" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo e(!$isSuperadmin ? 'bg-gray-100 cursor-not-allowed' : ''); ?>"
                                <?php echo e(!$isSuperadmin ? 'disabled' : ''); ?>>
                                <option value="1" <?php if(old('is_active', $bolum->is_active) == 1): echo 'selected'; endif; ?>>Aktif</option>
                                <option value="0" <?php if(old('is_active', $bolum->is_active) == 0): echo 'selected'; endif; ?>>Pasif</option>
                            </select>
                        </div>
                        
                        
                        <div class="flex items-center justify-between">
                            <?php if($isSuperadmin): ?>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Güncelle
                            </button>
                            <?php else: ?>
                                <span class="text-sm text-gray-500 italic">Bölüm bilgilerini düzenleme yetkiniz yok.</span>
                            <?php endif; ?>
                            <a href="<?php echo e(route('admin.bolumler.index')); ?>" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                Geri Dön
                            </a>
                        </div>
                    </form>

                    
                    
                    <?php if($bolum->has_machines): ?>
                        <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                            <p class="font-bold">Makine Yönetimi</p>
                            <p>Makine ekleme ve düzenleme işlemleri artık 
                                <a href="<?php echo e(route('admin.bolumler.dashboard', $bolum)); ?>" class="underline font-bold">Bölüm Paneli</a> üzerinden yapılmaktadır.
                            </p>
                        </div>
                    <?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/bolumler/edit.blade.php ENDPATH**/ ?>