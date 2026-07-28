<?php $__env->startPush('pageTitle'); ?>
    Bölüm Kategorileri | 
<?php $__env->stopPush(); ?>

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
            <?php echo e(__('Bölüm Kategorileri')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <?php if(session('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Başarılı!</strong>
                    <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Hata!</strong>
                    <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border">
                        <h3 class="font-bold text-lg mb-4">Yeni Kategori Ekle</h3>
                        <form action="<?php echo e(route('admin.bolum-kategorileri.store')); ?>" method="POST"
                            class="flex gap-4 items-end">
                            <?php echo csrf_field(); ?>
                            <div class="flex-grow">
                                <label for="ad" class="block text-gray-700 text-sm font-bold mb-2">Kategori Adı:</label>
                                <input type="text" name="ad" id="ad"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required placeholder="Örn: Üretim, İdari, Teknik...">
                            </div>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                style="height: 42px;">
                                Ekle
                            </button>
                        </form>
                    </div>

                    
                    <h3 class="font-bold text-lg mb-4">Mevcut Kategoriler</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="py-2 px-4 text-left">ID</th>
                                    <th class="py-2 px-4 text-left">Kategori Adı</th>
                                    <th class="py-2 px-4 text-left">Bölüm Sayısı</th>
                                    <th class="py-2 px-4 text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-4"><?php echo e($kategori->id); ?></td>
                                        <td class="py-2 px-4">
                                            <form action="<?php echo e(route('admin.bolum-kategorileri.update', $kategori->id)); ?>"
                                                method="POST" class="flex gap-2">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <input type="text" name="ad" value="<?php echo e($kategori->ad); ?>"
                                                    class="border rounded px-2 py-1 text-sm w-full focus:outline-none focus:border-blue-500">
                                                <button type="submit"
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-bold">Güncelle</button>
                                            </form>
                                        </td>
                                        <td class="py-2 px-4">
                                            <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs">
                                                <?php echo e($kategori->bolumler->count()); ?> Bölüm
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 text-right">
                                            <form action="<?php echo e(route('admin.bolum-kategorileri.destroy', $kategori->id)); ?>"
                                                method="POST"
                                                onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"
                                                class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 font-bold text-sm">Sil</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">Henüz kategori eklenmemiş.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/bolum_kategorileri/index.blade.php ENDPATH**/ ?>