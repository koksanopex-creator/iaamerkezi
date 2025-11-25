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
            <?php echo e(__('Takım Yönetimi')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">

                    
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Tüm Takımlar</h3>
                                <p class="mt-1 text-base text-gray-600">Sistemde kayıtlı tüm takımları buradan yönetebilirsiniz.</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:mt-0 flex-shrink-0">
                            <a href="<?php echo e(route('admin.takim-yonetim.create')); ?>" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow-sm hover:from-indigo-700 hover:to-blue-600 transform hover:-translate-y-0.5 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Yeni Takım Oluştur
                            </a>
                        </div>
                    </div>
                    
                    
                    <?php if(session('success')): ?><div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert"><p><?php echo e(session('success')); ?></p></div><?php endif; ?>
                    <?php if(session('error')): ?><div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert"><p><?php echo e(session('error')); ?></p></div><?php endif; ?>

                    
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-inner border border-gray-200/80 overflow-hidden">
                        <table class="block sm:table min-w-full">
                            <thead class="hidden sm:table-header-group">
                                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <th class="px-6 py-4">Takım Adı</th>
                                    <th class="px-6 py-4">Lider</th>
                                    <th class="px-6 py-4 text-center">Üye Sayısı</th>
                                    <th class="px-6 py-4">Oluşturulma Tarihi</th>
                                    <th class="px-6 py-4 text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="block sm:table-row-group">
                                <?php $__empty_1 = true; $__currentLoopData = $takimlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-indigo-50 transition-colors">
                                        <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span><span class="text-right sm:text-left font-medium text-indigo-600"><?php echo e($takim->ad); ?></span></td>

                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                        <span class="font-semibold text-sm text-gray-500 sm:hidden">Lider:</span>
                                        <div class="text-right sm:text-left">
                                            <?php if($takim->lider): ?>
                                                <a href="<?php echo e(route('profile.show', $takim->lider->id)); ?>" target="_blank" class="inline-flex items-center gap-2 group">
                                                    
                                                    <?php if($takim->lider->profile_photo_path): ?>
                                                        <img class="h-8 w-8 rounded-full object-cover border border-gray-200 group-hover:border-indigo-500 transition-colors" src="<?php echo e(asset('storage/' . $takim->lider->profile_photo_path)); ?>" alt="<?php echo e($takim->lider->name); ?>">
                                                    <?php else: ?>
                                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs text-indigo-700 font-bold group-hover:bg-indigo-200 transition-colors">
                                                            <?php echo e(substr($takim->lider->name, 0, 1)); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                    <span class="text-sm font-medium text-gray-600 group-hover:text-indigo-600 hover:underline transition-colors">
                                                        <?php echo e($takim->lider->name); ?>

                                                    </span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-sm text-gray-400 italic">Lider Yok</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Üyeler:</span><div class="w-full text-right sm:text-center"><span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800"><?php echo e($takim->uyeler_count); ?> Üye</span></div></td>
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span><span class="text-right sm:text-left text-sm text-gray-500"><?php echo e($takim->created_at->format('d.m.Y')); ?></span></td>
                                        <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center sm:space-x-2 space-y-2 sm:space-y-0">
                                                <a href="<?php echo e(route('admin.takim-yonetim.show', $takim)); ?>" class="w-full sm:w-auto inline-flex justify-center text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 hover:bg-gray-50">
                                                    Detay
                                                </a>
                                                <a href="<?php echo e(route('admin.takim-yonetim.edit', $takim)); ?>" class="w-full sm:w-auto inline-flex justify-center text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-indigo-700">
                                                    Düzenle
                                                </a>

                                                
                                                <form class="inline-block w-full sm:w-auto"
                                                    method="POST"
                                                    action="<?php echo e(route('admin.takim-yonetim.destroy', $takim)); ?>"
                                                    onsubmit="return confirm('\'<?php echo e($takim->ad); ?>\' takımını kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="w-full inline-flex justify-center text-sm font-semibold text-white bg-red-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-red-700">
                                                        Sil
                                                    </button>
                                                </form>
                                                 
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr class="block sm:table-row"><td colspan="5" class="p-12 text-center text-gray-500">Sistemde henüz oluşturulmuş bir takım bulunmamaktadır.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <div class="mt-6">
                        <?php echo e($takimlar->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/takim-yonetim/index.blade.php ENDPATH**/ ?>