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
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Anlaşma Maddeleri Havuzu')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            
            <?php if(session('success')): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            
            <?php if(auth()->user()->can('arabuluculuk.settings_create') || auth()->user()->hasRole('Superadmin')): ?>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-indigo-100">
                <div class="p-6 bg-white">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-indigo-100 text-indigo-700 p-2 rounded-lg mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </span>
                        Yeni Madde Tanımla
                    </h3>
                    
                    <form action="<?php echo e(route('admin.arabuluculuk.tanim.storeMadde')); ?>" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        <?php echo csrf_field(); ?>
                        <div class="flex-grow w-full">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Madde İçeriği</label>
                            <input type="text" name="icerik" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Örn: İşçi, tüm alacaklarını aldığını beyan eder." required>
                        </div>
                        <div class="w-full md:w-1/4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hukuki Dayanak (Opsiyonel)</label>
                            <input type="text" name="hukuki_dayanak" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Örn: İş Kanunu Md. 25">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold transition shadow-md w-full md:w-auto">
                            Kaydet
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Mevcut Maddeler Listesi</h3>
                    <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full"><?php echo e($maddeler->count()); ?> Kayıt</span>
                </div>

                <?php if($maddeler->count() > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Madde İçeriği</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Hukuki Dayanak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Oluşturma</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $maddeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $madde): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr x-data="{ 
                                    editing: false, 
                                    icerik: '<?php echo e(addslashes($madde->icerik)); ?>', 
                                    dayanak: '<?php echo e(addslashes($madde->hukuki_dayanak)); ?>',
                                    // Orjinal verileri yedekliyoruz
                                    oldIcerik: '<?php echo e(addslashes($madde->icerik)); ?>',
                                    oldDayanak: '<?php echo e(addslashes($madde->hukuki_dayanak)); ?>',
                                    
                                    // İptal Fonksiyonu
                                    cancelEdit() {
                                        this.editing = false;
                                        this.icerik = this.oldIcerik;   // Eskiye döndür
                                        this.dayanak = this.oldDayanak; // Eskiye döndür
                                    }
                                }" 
                                class="hover:bg-gray-50 transition">
                                
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">
                                    <?php echo e($index + 1); ?>

                                </td>

                                
                                <td class="px-6 py-4">
                                    <div x-show="!editing" class="text-sm text-gray-900 font-medium">
                                        <span x-text="icerik"></span>
                                    </div>
                                    <div x-show="editing" x-cloak>
                                        <form id="form-update-<?php echo e($madde->id); ?>" action="<?php echo e(route('admin.arabuluculuk.tanim.updateMadde', $madde->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                            <textarea name="icerik" x-model="icerik" class="w-full text-sm border-gray-300 rounded p-1 focus:ring-indigo-500 focus:border-indigo-500" rows="2"></textarea>
                                        </form>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-4">
                                    <div x-show="!editing">
                                        <span x-show="dayanak" class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded border border-blue-200" x-text="dayanak"></span>
                                        <span x-show="!dayanak" class="text-gray-400 text-xs">-</span>
                                    </div>
                                    <div x-show="editing" x-cloak>
                                        <input form="form-update-<?php echo e($madde->id); ?>" type="text" name="hukuki_dayanak" x-model="dayanak" class="w-full text-xs border-gray-300 rounded p-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </td>

                                
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    <?php echo e($madde->created_at->format('d.m.Y')); ?>

                                </td>

                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    
                                    <div x-show="!editing" class="flex justify-end gap-2">
                                        <?php if(auth()->user()->can('arabuluculuk.settings_edit') || auth()->user()->hasRole('Superadmin')): ?>
                                            <button @click="editing = true" class="text-indigo-600 hover:text-indigo-900" title="Düzenle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                        <?php endif; ?>

                                        <?php if(auth()->user()->can('arabuluculuk.settings_delete') || auth()->user()->hasRole('Superadmin')): ?>
                                            <form action="<?php echo e(route('admin.arabuluculuk.tanim.destroyMadde', $madde->id)); ?>" method="POST" onsubmit="return confirm('Bu maddeyi silmek istediğinize emin misiniz?');">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Sil">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <div x-show="editing" class="flex justify-end gap-2" x-cloak>
                                        <button form="form-update-<?php echo e($madde->id); ?>" type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-xs font-bold hover:bg-green-700">Kaydet</button>
                                        
                                        <button type="button" @click="cancelEdit()" class="bg-gray-300 text-gray-700 px-2 py-1 rounded text-xs font-bold hover:bg-gray-400">İptal</button>
                                    </div>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-10 text-center flex flex-col items-center justify-center text-gray-500">
                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-lg font-medium">Henüz kayıtlı anlaşma maddesi bulunmuyor.</p>
                        <p class="text-sm">Yukarıdaki formdan yeni maddeler ekleyebilirsiniz.</p>
                    </div>
                <?php endif; ?>
            </div>

            
            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 mt-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        İşlem Geçmişi (Loglar)
                    </h3>
                </div>
                <div class="p-6 max-h-[300px] overflow-y-auto">
                    <div class="grid grid-cols-1 gap-2">
                        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="text-xs flex items-center p-2 rounded hover:bg-gray-50 border-l-4 <?php echo e($log->islem_turu == 'SİLME' ? 'border-red-400' : ($log->islem_turu == 'DÜZENLEME' ? 'border-blue-400' : 'border-green-400')); ?>">
                                <span class="font-bold text-gray-700 w-32 truncate"><?php echo e($log->user->name); ?></span>
                                <span class="text-gray-400 w-32"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></span>
                                <span class="font-bold <?php echo e($log->islem_turu == 'SİLME' ? 'text-red-600' : ($log->islem_turu == 'DÜZENLEME' ? 'text-blue-600' : 'text-green-600')); ?> w-24">
                                    <?php echo e($log->islem_turu); ?>

                                </span>
                                <span class="text-gray-600 flex-1"><?php echo e($log->detay); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>


                
                    <div class="p-3 border-t border-gray-200 bg-gray-50 text-center">
                        <a href="<?php echo e(route('admin.arabuluculuk.tanim.showAllLogs')); ?>" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                            Tüm Geçmişi Gör &rarr;
                        </a>
                    </div>
                    

            </div>
            <?php endif; ?>

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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabuluculuk/tanimlar/anlasma_maddeleri.blade.php ENDPATH**/ ?>