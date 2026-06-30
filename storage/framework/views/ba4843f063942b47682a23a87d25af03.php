<div class="premium-card" id="sikayetler-section">
    <div class="p-card-header border-b border-gray-100">
        <h3 class="p-card-title">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Müşteri Şikayetleri (<?php echo e($sikayetler->total()); ?>)
        </h3>
    </div>
    
    
    <div class="p-4 bg-gray-50/50 border-b border-gray-100">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col gap-1 flex-1 min-w-[200px] max-w-md">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">KONU ARA</span>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Konu ile ara..." class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 w-full pl-8 py-2">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex flex-col gap-1">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">MÜŞTERİ</span>
                    <select wire:model.live="customerId" class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 min-w-[150px] py-1.5">
                        <option value="">Tümü</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                
                <div class="flex flex-col gap-1">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">DURUM</span>
                    <select wire:model.live="status" class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 min-w-[120px] py-1.5">
                        <option value="">Tümü</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s); ?>"><?php echo e($s); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">BAŞLANGIÇ</span>
                    <input type="date" wire:model.live="startDate" class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 py-1.5">
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">BİTİŞ</span>
                    <input type="date" wire:model.live="endDate" class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 py-1.5">
                </div>

                <!--[if BLOCK]><![endif]--><?php if($customerId || $status || $startDate || $endDate || $search): ?>
                    <button wire:click="resetFilters" 
                            class="flex items-center gap-2 px-4 py-2 bg-white text-red-600 border border-red-100 rounded-xl text-[10px] font-black hover:bg-red-600 hover:text-white hover:border-red-600 hover:shadow-lg hover:shadow-red-200/50 transition-all duration-300 group shadow-sm uppercase tracking-tighter mb-0.5">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>Filtreleri Temizle</span>
                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
    
    <!--[if BLOCK]><![endif]--><?php if($customerId || $status || $startDate || $endDate || $search): ?>
    <div class="px-4 py-2 bg-amber-50/80 border-b border-amber-100 flex items-center gap-3">
        <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            <span class="text-[10px] font-black text-amber-700 uppercase tracking-tighter">Aktif Filtreler:</span>
        </div>
        <div class="flex flex-wrap gap-2">
            <!--[if BLOCK]><![endif]--><?php if($search): ?>
                <span class="inline-flex items-center px-2 py-0.5 bg-white border border-amber-200 rounded-md text-[10px] text-amber-600 font-bold shadow-sm">Arama: "<?php echo e($search); ?>"</span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if($customerId): ?>
                <span class="inline-flex items-center px-2 py-0.5 bg-white border border-amber-200 rounded-md text-[10px] text-amber-600 font-bold shadow-sm">Müşteri: <?php echo e($customers->find($customerId)->name ?? 'Bilinmiyor'); ?></span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if($status): ?>
                <span class="inline-flex items-center px-2 py-0.5 bg-white border border-amber-200 rounded-md text-[10px] text-amber-600 font-bold shadow-sm">Durum: <?php echo e($status); ?></span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if($startDate): ?>
                <span class="inline-flex items-center px-2 py-0.5 bg-white border border-amber-200 rounded-md text-[10px] text-amber-600 font-bold shadow-sm">Başlangıç: <?php echo e(\Carbon\Carbon::parse($startDate)->format('d.m.Y')); ?></span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if($endDate): ?>
                <span class="inline-flex items-center px-2 py-0.5 bg-white border border-amber-200 rounded-md text-[10px] text-amber-600 font-bold shadow-sm">Bitiş: <?php echo e(\Carbon\Carbon::parse($endDate)->format('d.m.Y')); ?></span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <div class="ml-auto text-[10px] text-amber-500 italic font-medium">Toplam <?php echo e($sikayetler->total()); ?> sonuç listeleniyor</div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="overflow-x-auto">
        <table class="p-table">
            <thead>
                <tr>
                    <th class="text-left">ID</th>
                    <th class="text-left">Müşteri</th>
                    <th class="text-left">Konu</th>
                    <th class="text-left">Durum</th>
                    <th class="text-left">Tarih</th>
                    <th class="text-right pr-6">İŞLEMLER</th>
                </tr>
            </thead>
            <tbody>
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr wire:key="sikayet-<?php echo e($sikayet->id); ?>">
                    <td class="font-bold text-gray-400">#<?php echo e($sikayet->id); ?></td>
                    <td>
                        <a href="<?php echo e($sikayet->customer ? route('musteri.profil.show', $sikayet->customer->id) : '#'); ?>" class="text-indigo-600 font-bold hover:underline">
                            <?php echo e($sikayet->customer->name ?? $sikayet->musteri_adi ?? 'Bilinmiyor'); ?>

                        </a>
                    </td>
                    <td>
                        <div class="text-xs font-medium truncate max-w-[200px]"><?php echo e($sikayet->musteri_sikayet_konusu); ?></div>
                    </td>
                    <td><?php echo $sikayet->musteri_durum_badge; ?></td>
                    <td><?php echo e($sikayet->musteri_sikayet_tarihi ? $sikayet->musteri_sikayet_tarihi->format('d.m.Y') : '-'); ?></td>
                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" class="inline-flex items-center px-3 py-1 bg-gray-50 text-gray-700 rounded-lg text-xs font-bold border border-gray-200 hover:bg-white hover:shadow-sm transition-all shadow-sm">Detay</a>
                            <!--[if BLOCK]><![endif]--><?php if($sikayet->iaaProjesi): ?>
                                <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaaProjesi->id)); ?>" class="inline-flex items-center px-3 py-1 bg-purple-50 text-purple-700 rounded-lg text-[10px] font-black border border-purple-100 hover:bg-purple-600 hover:text-white transition-all shadow-sm">PROJE</a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-400 italic">Kayıtlı şikayet bulunamadı.</td>
                </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>
    
    <!--[if BLOCK]><![endif]--><?php if($sikayetler->hasMorePages()): ?>
    <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
        <button wire:click="loadMore" wire:loading.attr="disabled" class="text-xs font-black text-indigo-600 hover:text-indigo-800 transition-all flex items-center justify-center gap-2 mx-auto group">
            <span wire:loading.remove>Devamını Göster (<?php echo e($sikayetler->total() - $sikayetler->count()); ?> kayıt daha)</span>
            <span wire:loading>Yükleniyor...</span>
            <svg wire:loading.remove class="w-4 h-4 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            <svg wire:loading class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/bolum-sikayet-listesi.blade.php ENDPATH**/ ?>