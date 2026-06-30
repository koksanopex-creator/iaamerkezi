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
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                <?php echo e(__('İşlem Geçmişi ve Denetim')); ?>

            </h2>
            <a href="<?php echo e(route('admin.arabuluculuk.tanim.anlasmaMaddeleri')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition">
                &larr; Geri Dön
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{ showFilters: true }">
                <div class="flex justify-between items-center mb-4 cursor-pointer" @click="showFilters = !showFilters">
                    <h3 class="font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filtreleme Seçenekleri
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>

                <div x-show="showFilters" x-transition>
                    <form action="<?php echo e(route('admin.arabuluculuk.tanim.showAllLogs')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kullanıcı</label>
                            <select name="user_id" class="w-full border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tümü</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">İşlem Türü</label>
                            <select name="islem_turu" class="w-full border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tümü</option>
                                <option value="EKLEME" <?php echo e(request('islem_turu') == 'EKLEME' ? 'selected' : ''); ?>>EKLEME</option>
                                <option value="DÜZENLEME" <?php echo e(request('islem_turu') == 'DÜZENLEME' ? 'selected' : ''); ?>>DÜZENLEME</option>
                                <option value="SİLME" <?php echo e(request('islem_turu') == 'SİLME' ? 'selected' : ''); ?>>SİLME</option>
                            </select>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">İçerik Ara</label>
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="w-full border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Madde içeriği...">
                        </div>

                        
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tarih Aralığı</label>
                            <div class="flex gap-2">
                                <input type="date" name="date_start" value="<?php echo e(request('date_start')); ?>" class="w-full border-gray-300 rounded-lg text-sm">
                                <span class="text-gray-400 self-center">-</span>
                                <input type="date" name="date_end" value="<?php echo e(request('date_end')); ?>" class="w-full border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>

                        
                        <div class="md:col-span-5 flex justify-end gap-2 mt-2">
                            <a href="<?php echo e(route('admin.arabuluculuk.tanim.showAllLogs')); ?>" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 transition">Temizle</a>
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow">Filtrele</button>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="p-6">
                    
                    <div class="flex justify-between mb-4">
                        <span class="text-sm text-gray-500">Toplam <strong><?php echo e($logs->total()); ?></strong> kayıt bulundu.</span>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">Sıra No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48">Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48">Kullanıcı</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">İşlem</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detay</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">IP Adresi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition">
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-gray-400">
                                        <?php echo e($logs->total() - ($logs->firstItem() + $loop->index - 1)); ?>

                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($log->created_at->format('d.m.Y H:i:s')); ?>

                                        <div class="text-[10px] text-gray-400"><?php echo e($log->created_at->diffForHumans()); ?></div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                        <?php echo e($log->user->name ?? 'Silinmiş Kullanıcı'); ?>

                                        <div class="text-[10px] text-gray-400 font-normal"><?php echo e($log->user->email ?? ''); ?></div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-bold rounded border
                                            <?php echo e($log->islem_turu == 'SİLME' ? 'bg-red-50 text-red-700 border-red-200' : 
                                              ($log->islem_turu == 'DÜZENLEME' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                              'bg-green-50 text-green-700 border-green-200')); ?>">
                                            <?php echo e($log->islem_turu); ?>

                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-sm text-gray-600 break-words">
                                        <?php echo e($log->detay); ?>

                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 font-mono">
                                        <?php echo e($log->ip_adresi ?? '-'); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Kayıt bulunamadı.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    
                    <div class="mt-4">
                        <?php echo e($logs->links()); ?>

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
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/arabuluculuk/tanimlar/log_history.blade.php ENDPATH**/ ?>