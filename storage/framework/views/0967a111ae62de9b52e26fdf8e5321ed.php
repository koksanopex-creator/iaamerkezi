<?php $__env->startPush('pageTitle'); ?>
    Ziyaret Planlarım | 
<?php $__env->stopPush(); ?>

<div class="py-6 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-extrabold leading-7 text-gray-900 sm:text-3xl sm:truncate flex items-center gap-3">
                    <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-200">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    Ziyaret Planlarım
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Size atanan ziyaret planlarını ve onay süreçlerini takip edin
                    </div>
                </div>
            </div>
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if(Auth::user()->hasRole(['Superadmin', 'Yönetim', 'Direktör', 'Bölüm Kalite Yöneticisi'])): ?>
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button wire:click="setTab('my_visits')" class="<?php echo e($activeTab === 'my_visits' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5 <?php echo e($activeTab === 'my_visits' ? 'text-indigo-500' : 'text-gray-400'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Ziyaretlerim
                </button>
                <button wire:click="setTab('pending_approval')" class="<?php echo e($activeTab === 'pending_approval' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5 <?php echo e($activeTab === 'pending_approval' ? 'text-indigo-500' : 'text-gray-400'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Onay Bekleyenler
                </button>
            </nav>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div class="bg-white rounded-2xl shadow-xl shadow-indigo-100/20 border border-gray-100 overflow-hidden w-full">
            <div class="w-full">
                <table class="w-full divide-y divide-gray-100 table-fixed">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="w-[12%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Tarihler</th>
                            <th scope="col" class="w-[20%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Müşteri / Ürün</th>
                            <th scope="col" class="w-[15%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Ziyaret Nedeni</th>
                            <th scope="col" class="w-[15%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Ziyareti Gerçekleştirecek Personel</th>
                            <th scope="col" class="w-[20%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">İlgili Şikayet</th>
                            <th scope="col" class="w-[10%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Durum</th>
                            <th scope="col" class="w-[8%] px-4 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $complaint = $visit->iaa->musteriSikayeti ?? null;
                                $statusStr = $visit->status ?? '';
                                $statusClass = match($statusStr) {
                                    'Beklemede' => 'bg-amber-50 text-amber-600 border-amber-200',
                                    'Direktör Onayı Bekliyor' => 'bg-blue-50 text-blue-600 border-blue-200',
                                    'Yönetim Onayı Bekliyor' => 'bg-blue-50 text-blue-600 border-blue-200',
                                    'Bölüm Onayı Bekliyor' => 'bg-blue-50 text-blue-600 border-blue-200',
                                    'Revizyon Bekliyor' => 'bg-purple-50 text-purple-600 border-purple-200',
                                    'Onaylandı' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                    'Tamamlandı' => 'bg-blue-50 text-blue-600 border-blue-200',
                                    'Reddedildi', 'İptal Edildi' => 'bg-rose-50 text-rose-600 border-rose-200',
                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                };
                            ?>
                            <tr class="hover:bg-indigo-50/30 transition duration-150 group">
                                <td class="px-4 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">Planlanan</span>
                                        <div class="flex flex-wrap items-center gap-1">
                                            <span class="text-sm font-bold text-gray-900 whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($visit->visit_date)->translatedFormat('d F Y')); ?></span>
                                            <span class="text-[10px] text-gray-500 font-medium whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($visit->visit_date)->format('H:i')); ?></span>
                                        </div>
                                        <span class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest mt-2 mb-0.5 whitespace-nowrap">Onay Talebi</span>
                                        <div class="flex flex-wrap items-center gap-1">
                                            <span class="text-xs font-bold text-indigo-700 whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($visit->created_at)->translatedFormat('d F Y')); ?></span>
                                            <span class="text-[10px] text-indigo-500/70 whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($visit->created_at)->format('H:i')); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 h-10 w-10 bg-white rounded-lg border border-gray-100 p-1 flex items-center justify-center overflow-hidden shadow-sm">
                                            <!--[if BLOCK]><![endif]--><?php if($complaint && !empty($complaint->customer->logo_path)): ?>
                                                <img src="<?php echo e(asset('storage/' . $complaint->customer->logo_path)); ?>" alt="Logo" class="max-h-full max-w-full object-contain">
                                            <?php else: ?>
                                                <div class="h-full w-full bg-indigo-50 rounded flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                    <?php echo e(strtoupper(substr($complaint ? ($complaint->customer->name ?? $complaint->musteri_adi) : 'M', 0, 1))); ?>

                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-sm font-bold text-gray-800 truncate" title="<?php echo e($complaint ? ($complaint->customer->name ?? $complaint->musteri_adi) : 'Müşteri Bilinmiyor'); ?>">
                                                <?php echo e($complaint ? ($complaint->customer->name ?? $complaint->musteri_adi) : 'Müşteri Bilinmiyor'); ?>

                                            </span>
                                            <!--[if BLOCK]><![endif]--><?php if(!empty($visit->customer_product_id)): ?>
                                                <span class="text-[11px] text-indigo-600 font-bold bg-indigo-50 self-start px-2 py-0.5 rounded-full mt-1 truncate max-w-full" title="<?php echo e($visit->customer_product_id); ?>"><?php echo e($visit->customer_product_id); ?></span>
                                            <?php else: ?>
                                                <span class="text-[10px] text-gray-400 font-medium italic mt-1">Ürün belirtilmedi</span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col w-full">
                                        <span class="text-xs font-black text-gray-500 uppercase tracking-tight truncate" title="<?php echo e($visit->visit_reason); ?>"><?php echo e($visit->visit_reason); ?></span>
                                        <!--[if BLOCK]><![endif]--><?php if($visit->visit_notes): ?>
                                            <span class="text-[11px] text-gray-500 line-clamp-2 mt-0.5" title="<?php echo e($visit->visit_notes); ?>"><?php echo e($visit->visit_notes); ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold text-xs">
                                            <?php echo e(strtoupper(substr($visit->visitor_name ?? 'P', 0, 1))); ?>

                                        </div>
                                        <div class="ml-3">
                                            <div class="text-xs font-bold text-gray-900"><?php echo e($visit->visitor_name ?? 'Dinamik Personel'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <!--[if BLOCK]><![endif]--><?php if($complaint): ?>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs font-bold text-gray-700 line-clamp-2" title="#<?php echo e($complaint->id); ?> - <?php echo e($complaint->musteri_sikayet_konusu); ?>">#<?php echo e($complaint->id); ?> - <?php echo e($complaint->musteri_sikayet_konusu); ?></span>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <!--[if BLOCK]><![endif]--><?php if($complaint->bolum && $complaint->bolum->logo_yolu): ?>
                                                    <img src="<?php echo e(asset('storage/' . $complaint->bolum->logo_yolu)); ?>" alt="<?php echo e($complaint->bolum->ad); ?>" class="h-4 w-4 object-contain rounded-sm">
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <span class="text-[10px] text-amber-600 font-black tracking-widest uppercase"><?php echo e($complaint->bolum->ad ?? 'Bölüm Yok'); ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 italic">Şikayet Bulunamadı</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col items-start gap-1.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black border uppercase tracking-widest <?php echo e($statusClass); ?>">
                                            <?php echo e($statusStr ?: 'Bilinmiyor'); ?>

                                        </span>
                                        <?php
                                            $statusDate = null;
                                            $statusLabel = null;
                                            if ($statusStr === 'Onaylandı') {
                                                $statusDate = $visit->updated_at;
                                                $statusLabel = 'Onaylandı';
                                            } elseif ($statusStr === 'Tamamlandı') {
                                                $statusDate = $visit->completed_at ?? $visit->updated_at;
                                                $statusLabel = 'Tamamlandı';
                                            } elseif ($statusStr === 'Reddedildi') {
                                                $statusDate = $visit->updated_at;
                                                $statusLabel = 'Reddedildi';
                                            } elseif ($statusStr === 'Revizyon Bekliyor') {
                                                $statusDate = $visit->updated_at;
                                                $statusLabel = 'İstendi';
                                            } elseif ($statusStr === 'İptal Edildi') {
                                                $statusDate = $visit->cancelled_at ?? $visit->updated_at;
                                                $statusLabel = 'İptal Edildi';
                                            }
                                        ?>
                                        <!--[if BLOCK]><![endif]--><?php if($statusDate): ?>
                                            <div class="flex flex-col">
                                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest"><?php echo e($statusLabel); ?></span>
                                                <span class="text-[10px] text-gray-600 font-medium whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($statusDate)->format('d.m.Y H:i')); ?></span>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center text-sm font-medium">
                                    <div class="flex justify-center items-center gap-2">
                                        <!--[if BLOCK]><![endif]--><?php if($complaint): ?>
                                            <a href="<?php echo e(route('admin.sikayetler.show', $complaint->id)); ?>" target="_blank" class="p-2 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition shadow-sm border border-blue-100">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Şikayet Detayı"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($visit->iaa_id): ?>
                                            <a href="<?php echo e(route('proje.workspace.show', $visit->iaa_id)); ?>" target="_blank" class="p-2 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl transition shadow-sm border border-indigo-100">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Proje Çalışma Alanı"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-3 bg-gray-50 rounded-full mb-3">
                                            <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 17.242L12.001 14.414l2.828 2.828m-4.242-8.484l4.242 4.242m-4.242 0l4.242-4.242M12 21a9 9 0 110-18 9 9 0 010 18z"/></svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">Kayıt Bulunamadı</h3>
                                        <p class="text-xs text-gray-500 mt-1">Görünümünüze ait kayıtlı bir ziyaret planı bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <?php echo e($visits->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/ziyaret-planlarim.blade.php ENDPATH**/ ?>