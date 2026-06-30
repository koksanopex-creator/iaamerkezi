<?php $__env->startPush('pageTitle'); ?>
    Takımlar | 
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
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
                    <?php echo e(__('Takımlar')); ?>

                </h2>
                <p class="text-gray-600 mt-1">Takımlarınızı yönetin, yeni takımlar keşfedin ve katılın.</p>
            </div>
            <div class="flex space-x-3">
                <a href="<?php echo e(route('takimlar.davetlerim')); ?>" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm transition-colors">Davetlerim</a>
                <a href="<?php echo e(route('takimlar.create')); ?>" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Yeni Takım Oluştur
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <?php if(session('success')): ?>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 p-4 rounded-r-xl shadow-sm"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg></div><div class="ml-3"><p class="text-green-800 font-medium"><?php echo e(session('success')); ?></p></div></div></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                 <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 p-4 rounded-r-xl shadow-sm"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg></div><div class="ml-3"><p class="text-red-800 font-medium"><?php echo e(session('error')); ?></p></div></div></div>
            <?php endif; ?>

            <?php if($gonderdigimIstekler->isNotEmpty()): ?>
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-yellow-200">
                <div class="bg-gradient-to-r from-yellow-50 to-white px-6 py-5 border-b border-yellow-200"><div class="flex items-center justify-between"><h3 class="text-lg font-semibold text-yellow-800">Gönderdiğim Katılma İstekleri</h3><div class="flex items-center space-x-2 text-sm text-yellow-600"><span><?php echo e($gonderdigimIstekler->count()); ?> Bekleyen İstek</span></div></div></div>
                <div class="bg-white/60 backdrop-blur-sm overflow-hidden">
                <table class="block sm:table min-w-full">
                        <thead class="hidden sm:table-header-group">
                            <tr class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                                <th class="px-6 py-4">Takım Adı</th>
                                <th class="px-6 py-4">Lider</th> 
                                <th class="px-6 py-4">İstek Tarihi</th>
                                <th class="px-6 py-4">Durum</th>
                                <th class="relative px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="block sm:table-row-group">
                            <?php $__currentLoopData = $gonderdigimIstekler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $istek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-yellow-50 transition-colors">
                                    
                                    <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle">
                                        <span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span>
                                        <span class="text-right sm:text-left font-semibold text-gray-800"><?php echo e($istek->takim->ad); ?></span>
                                    </td>

                                    
                                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                        <span class="font-semibold text-sm text-gray-500 sm:hidden">Lider:</span>
                                        <div class="text-right sm:text-left">
                                            <?php if($istek->takim->lider): ?>
                                                <a href="<?php echo e(route('profile.show', $istek->takim->lider->id)); ?>" target="_blank" class="inline-flex items-center gap-2 group">
                                                    <?php if($istek->takim->lider->profile_photo_path): ?>
                                                        <img class="h-8 w-8 rounded-full object-cover border border-gray-200 group-hover:border-indigo-500 transition-colors" src="<?php echo e(asset('storage/' . $istek->takim->lider->profile_photo_path)); ?>" alt="">
                                                    <?php else: ?>
                                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs text-indigo-700 font-bold group-hover:bg-indigo-200 transition-colors">
                                                            <?php echo e(substr($istek->takim->lider->name, 0, 1)); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                    <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600 transition-colors">
                                                        <?php echo e($istek->takim->lider->name); ?>

                                                    </span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-sm text-gray-400 italic">Lider Yok</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    

                                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span><span class="text-right sm:text-left text-sm text-gray-600"><?php echo e($istek->created_at->format('d.m.Y')); ?></span></td>
                                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Durum:</span><span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"><?php echo e(Str::ucfirst($istek->durum)); ?></span></td>
                                    
                                    <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                        <div class="flex justify-end">
                                            <form action="<?php echo e(route('takimlar.istegiGeriCek', $istek)); ?>" method="POST" onsubmit="return confirm('Bu katılma isteğini geri çekmek istediğinizden emin misiniz?');"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">İsteği Geri Çek</button></form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-5 border-b border-gray-200"><div class="flex items-center justify-between"><h3 class="text-lg font-semibold text-gray-900">Katıldığım Takımlar</h3><div class="flex items-center space-x-2 text-sm text-gray-500"><span><?php echo e($katildigimTakimlar->count()); ?> Takım</span></div></div></div>
                <?php echo $__env->make('takimlar.partials.takim-table', ['takimlar' => $katildigimTakimlar, 'type' => 'katildigim'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-5 border-b border-gray-200"><div class="flex items-center justify-between"><h3 class="text-lg font-semibold text-gray-900">Diğer Takımlar</h3><div class="flex items-center space-x-2 text-sm text-gray-500"><span><?php echo e($digerTakimlar->count()); ?> Takım</span></div></div></div>
                <?php echo $__env->make('takimlar.partials.takim-table', ['takimlar' => $digerTakimlar, 'type' => 'diger', 'istekGonderilenTakimIdleri' => $gonderdigimIstekler->pluck('takim_id'), 'davetAlinanTakimIdleri' => $gelenDavetler->pluck('takim_id')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/takimlar/index.blade.php ENDPATH**/ ?>