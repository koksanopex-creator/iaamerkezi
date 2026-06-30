<?php
    $canViewDiscipline = Auth::id() == $user->id || 
                         Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) ||
                         (Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id == $user->bolum_id);
?>

<?php if($canViewDiscipline): ?>
    <div x-show="activeTab === 'disiplin'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Disiplin Dosyaları ve Geçmişi</h3>
                <p class="text-sm text-gray-500">Kullanıcının tüm disiplin süreçleri aşağıda listelenmiştir.</p>
            </div>
            
            
            <?php if(Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Bölüm Lideri']) && Auth::id() != $user->id): ?>
                <a href="<?php echo e(route('admin.disiplin.create')); ?>" class="group inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Yeni Tutanak
                </a>
            <?php endif; ?>
        </div>
        
        <?php
            $profileCases = \App\Models\DisciplinaryCase::where('user_id', $user->id)
                ->with(['behavior.category', 'reporter'])
                ->orderBy('created_at', 'desc')
                ->get();
        ?>

        <?php if($profileCases->isEmpty()): ?>
            
            
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center shadow-sm">
                <div class="relative inline-block mb-4">
                    <div class="absolute inset-0 bg-green-100 rounded-full animate-ping opacity-75"></div>
                    <div class="relative bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-full border border-green-200">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Sicil Tertemiz!</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-4">Bu kullanıcı adına kayıtlı herhangi bir disiplin ihlali veya tutanak bulunmamaktadır.</p>
                <div class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-bold border border-green-200">
                    Toplam Kayıt: 0
                </div>
            </div>

        <?php else: ?>
            
            
            <div class="grid grid-cols-1 gap-4" x-data="{ limit: 5, total: <?php echo e($profileCases->count()); ?> }">
                <?php $__currentLoopData = $profileCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        // Detay Linki
                        $detailLink = Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı']) 
                            ? route('admin.disiplin.show', $case->id)
                            : route('disiplin.show', $case->id);
                            
                        // Durum Renkleri
                        $statusStyles = match($case->durum) {
                            'Karar Verildi' => 'border-green-500 bg-green-50 text-green-700',
                            'Savunma Bekleniyor' => 'border-red-500 bg-red-50 text-red-700',
                            'Yönetici Değerlendirmesi' => 'border-blue-500 bg-blue-50 text-blue-700',
                            default => 'border-gray-500 bg-gray-50 text-gray-700'
                        };
                    ?>

                    <a href="<?php echo e($detailLink); ?>" class="block group" x-show="<?php echo e($loop->index); ?> < limit" x-transition>
                        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden">
                            
                            
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 <?php echo e(explode(' ', $statusStyles)[0]); ?>"></div>

                            <div class="flex items-start justify-between">
                                <div class="flex gap-4">
                                    
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-lg bg-gray-50 flex flex-col items-center justify-center border border-gray-100 text-gray-600 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition">
                                            <span class="text-[10px] font-bold uppercase">Dosya</span>
                                            <span class="text-lg font-black leading-none">#<?php echo e($case->id); ?></span>
                                        </div>
                                    </div>

                                    
                                    <div>
                                        <h4 class="text-base font-bold text-gray-900 group-hover:text-indigo-600 transition flex items-center gap-2">
                                            <?php echo e($case->behavior->category->ad ?? 'Genel Disiplin'); ?>

                                        </h4>
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-1">
                                            <?php echo e(Str::limit($case->behavior->tanim ?? 'İhlal tanımı bulunamadı.', 80)); ?>

                                        </p>
                                        
                                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <?php echo e($case->olay_tarihi->format('d.m.Y')); ?>

                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                Raportör: <?php echo e($case->reporter->name ?? 'Sistem'); ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="flex flex-col items-end gap-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo e(str_replace('border-', 'border-opacity-20 ', $statusStyles)); ?>">
                                        <?php echo e($case->durum); ?>

                                    </span>
                                    <span class="text-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold flex items-center gap-1">
                                        İncele &rarr;
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if($profileCases->count() > 5): ?>
                    <div class="mt-4 flex justify-center">
                        <button @click="limit = (limit === 5 ? total : 5)" 
                                class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 rounded-xl font-bold text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105 active:scale-95 group">
                            <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-indigo-500 transition-colors transform transition-transform" :class="limit !== 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                            </svg>
                            <span x-text="limit === 5 ? 'Daha Fazla Göster' : 'Daha Az Göster'"></span>
                            <span x-show="limit === 5" class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-500 rounded-lg text-xs" x-text="'(' + (total - 5) + ' kaldı)'"></span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/profile/partials/show/tab-disciplinary.blade.php ENDPATH**/ ?>