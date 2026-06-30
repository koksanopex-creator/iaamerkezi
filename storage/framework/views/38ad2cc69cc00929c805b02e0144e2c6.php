
            <div class="w-full" x-data="{ logModalOpen: false }">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Son 5 Proje Geçmişi Kaydı</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eylem</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $sonOnLoglar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?> 
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($log->user->name ?? 'Sistem'); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900"><?php echo e($log->eylem); ?></div>
                                            <div class="text-sm text-gray-500 italic">"<?php echo e($log->aciklama); ?>"</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Bu proje için henüz bir log kaydı bulunmuyor.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($tumProjeLoglari->count() > 5): ?>
                        <div class="text-center">
                            <button 
                                @click="logModalOpen = true" 
                                type="button" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Tüm Logları Gör (<?php echo e($tumProjeLoglari->count()); ?>)
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div 
                    x-show="logModalOpen" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4"
                    style="display: none;"
                    @keydown.escape.window="logModalOpen = false"
                >
                    <div 
                        class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-4xl" 
                        @click.outside="logModalOpen = false"
                    >
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Proje Geçmişi - Tüm Loglar (<?php echo e($tumProjeLoglari->count()); ?>)
                            </h3>
                        </div>
                        <div class="p-6 max-h-[70vh] overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eylem</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__empty_1 = true; $__currentLoopData = $tumProjeLoglari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($log->user->name ?? 'Sistem'); ?></td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900"><?php echo e($log->eylem); ?></div>
                                                <div class="text-sm text-gray-500 italic">"<?php echo e($log->aciklama); ?>"</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Log kaydı bulunamadı.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-right">
                            <button 
                                @click="logModalOpen = false" 
                                type="button" 
                                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                Kapat
                            </button>
                        </div>
                    </div>
                </div>
                
            </div>
            <?php /**PATH /var/www/kys_koksan/iaa/resources/views/proje-calisma-alani/partials/_logs.blade.php ENDPATH**/ ?>