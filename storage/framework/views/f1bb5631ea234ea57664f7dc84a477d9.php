<?php $__env->startPush('pageTitle'); ?>
    Müşteri Ziyaretleri | 
<?php $__env->stopPush(); ?>

<div class="py-6 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-extrabold leading-7 text-gray-900 sm:text-3xl sm:truncate flex items-center gap-3">
                    <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-200">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    Müşteri Ziyaretleri
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Planlanan ve gerçekleşen müşteri ziyaretlerini yönetin
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="<?php echo e(route('admin.sikayet-raporlari.tablo')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
                    Analiz Görünümü
                </a>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                <div class="space-y-1">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">Başlangıç Tarihi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <input type="date" wire:model.live="startDate" class="pl-10 block w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5 transition">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">Bitiş Tarihi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <input type="date" wire:model.live="endDate" class="pl-10 block w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5 transition">
                    </div>
                </div>

                
                <div class="space-y-1">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">Ara (Müşteri, Ürün, Not)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Arama yapın..." class="pl-10 block w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5 transition">
                    </div>
                </div>

                
                <div class="flex items-end">
                    <button wire:click="clearFilters" class="w-full flex justify-center items-center px-4 py-2.5 border border-gray-200 text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm active:scale-95 duration-100">
                        <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Filtreleri Temizle
                    </button>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-xl shadow-indigo-100/20 border border-gray-100 overflow-hidden w-full hidden lg:block">
            <div class="w-full">
                <table class="w-full table-fixed" style="border-collapse: separate; border-spacing: 0 10px;">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="w-[4%] px-2 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">#</th>
                            <th scope="col" class="w-[12%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Tarihler</th>
                            <th scope="col" class="w-[18%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Müşteri / Ürün</th>
                            <th scope="col" class="w-[13%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Ziyaret Nedeni</th>
                            <th scope="col" class="w-[13%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Ziyareti Gerçekleştirecek Personel</th>
                            <th scope="col" class="w-[18%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">İlgili Şikayet</th>
                            <th scope="col" class="w-[12%] px-4 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Durum</th>
                            <th scope="col" class="w-[10%] px-4 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $complaint = $complaints[$visit['remote_id']] ?? null;
                                
                                $waitingText = '';
                                $waitingColor = '';
                                $outlineStyle = '';
                                
                                if (in_array($visit['return_date_revision_status'] ?? '', ['Bekliyor', 'Direktör Onayı Bekliyor'])) {
                                    $waitingText = 'Tahmini Dönüş Revizyonu Bekliyor';
                                    $waitingColor = 'text-white bg-orange-600';
                                    $outlineStyle = 'outline: 2px solid #f97316; outline-offset: -2px;';
                                } elseif (($visit['status'] ?? '') === 'Beklemede') {
                                    $waitingText = 'Ziyaret Planı Onayı Bekliyor';
                                    $waitingColor = 'text-white bg-amber-500';
                                    $outlineStyle = 'outline: 2px solid #f59e0b; outline-offset: -2px;';
                                } elseif (($visit['status'] ?? '') === 'Revizyon Bekliyor') {
                                    $waitingText = 'Ziyaret Revizyonu Bekleniyor';
                                    $waitingColor = 'text-white bg-purple-600';
                                    $outlineStyle = 'outline: 2px solid #9333ea; outline-offset: -2px;';
                                } elseif (($visit['status'] ?? '') === 'Onaylandı') {
                                    $waitingText = 'Müşteri Ziyareti Sonuçlarının Girilmesi Bekleniyor';
                                    $waitingColor = 'text-white bg-indigo-600';
                                    $outlineStyle = 'outline: 2px solid #6366f1; outline-offset: -2px;';
                                } elseif (($visit['status'] ?? '') === 'Tamamlandı') {
                                    $outlineStyle = 'outline: 2px solid #10b981; outline-offset: -2px;';
                                } elseif (($visit['status'] ?? '') === 'Reddedildi' || ($visit['status'] ?? '') === 'İptal Edildi') {
                                    $outlineStyle = 'outline: 2px solid #f43f5e; outline-offset: -2px;';
                                }

                                $rowNumber = (($pagination['current_page'] - 1) * $pagination['per_page']) + $index + 1;
                            ?>
                            <tr class="bg-white hover:bg-indigo-50/30 transition duration-150 group rounded-lg" style="<?php echo e($outlineStyle); ?>">
                                
                                <td class="px-2 py-4 text-center">
                                    <span class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-gray-100 text-[11px] font-black text-gray-500"><?php echo e($rowNumber); ?></span>
                                </td>
                                
                                <td class="px-4 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">Planlanan</span>
                                        <div class="flex flex-wrap items-center gap-1">
                                            <span class="text-sm font-bold text-gray-900 whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($visit['visit_date'])->translatedFormat('d F Y')); ?></span>
                                            <span class="text-[10px] text-gray-500 font-medium whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($visit['visit_date'])->format('H:i')); ?></span>
                                        </div>
                                        
                                        <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest mt-2 mb-0.5 whitespace-nowrap">Tahmini Dönüş</span>
                                        <div class="flex flex-wrap items-center gap-1">
                                            <!--[if BLOCK]><![endif]--><?php if(!empty($visit['estimated_return_date'])): ?>
                                                <span class="text-xs font-bold text-emerald-700 whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($visit['estimated_return_date'])->translatedFormat('d F Y')); ?></span>
                                            <?php else: ?>
                                                <!--[if BLOCK]><![endif]--><?php if(in_array($visit['status'], ['Onaylandı', 'Tamamlandı'])): ?>
                                                    <span class="text-xs font-bold text-gray-400 whitespace-nowrap">-</span>
                                                <?php else: ?>
                                                    <span class="text-[9px] text-gray-400 italic">Onay sonrası eklenecek</span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>

                                        <span class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest mt-2 mb-0.5 whitespace-nowrap">Onay Talebi</span>
                                        <div class="flex flex-wrap items-center gap-1">
                                            <span class="text-xs font-bold text-indigo-700 whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($visit['created_at'])->translatedFormat('d F Y')); ?></span>
                                            <span class="text-[10px] text-indigo-500/70 whitespace-nowrap"><?php echo e(Carbon\Carbon::parse($visit['created_at'])->format('H:i')); ?></span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 h-10 w-10 bg-white rounded-lg border border-gray-100 p-1 flex items-center justify-center overflow-hidden shadow-sm">
                                            <!--[if BLOCK]><![endif]--><?php if(!empty($visit['customer']['logo_path'])): ?>
                                                <img src="<?php echo e(asset('storage/' . $visit['customer']['logo_path'])); ?>" alt="Logo" class="max-h-full max-w-full object-contain">
                                            <?php else: ?>
                                                <div class="h-full w-full bg-indigo-50 rounded flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                    <?php echo e(strtoupper(substr($visit['customer']['name'] ?? 'M', 0, 1))); ?>

                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-sm font-bold text-gray-800 truncate" title="<?php echo e(!empty($visit['customer']['name']) ? $visit['customer']['name'] : 'Müşteri Belirtilmedi'); ?>"><?php echo e(!empty($visit['customer']['name']) ? $visit['customer']['name'] : 'Müşteri Belirtilmedi'); ?></span>
                                            <!--[if BLOCK]><![endif]--><?php if(!empty($visit['product']['name'])): ?>
                                                <span class="text-[11px] text-indigo-600 font-bold bg-indigo-50 self-start px-2 py-0.5 rounded-full mt-1 truncate max-w-full" title="<?php echo e($visit['product']['name']); ?>"><?php echo e($visit['product']['name']); ?></span>
                                            <?php else: ?>
                                                <span class="text-[10px] text-gray-400 font-medium italic mt-1">Ürün belirtilmedi</span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-4 py-4">
                                    <div class="flex flex-col w-full">
                                        <span class="text-xs font-black text-gray-500 uppercase tracking-tight truncate" title="<?php echo e($visit['visit_reason']); ?>"><?php echo e($visit['visit_reason']); ?></span>
                                        <!--[if BLOCK]><![endif]--><?php if($visit['visit_notes']): ?>
                                            <span class="text-[11px] text-gray-500 line-clamp-2 mt-0.5" title="<?php echo e($visit['visit_notes']); ?>"><?php echo e($visit['visit_notes']); ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold text-xs">
                                            <?php echo e(strtoupper(substr($visit['user']['name'] ?? 'P', 0, 1))); ?>

                                        </div>
                                        <div class="ml-3">
                                            <div class="text-xs font-bold text-gray-900"><?php echo e($visit['user']['name'] ?? 'Belirtilmedi'); ?></div>
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
                                    <?php
                                        $statusStr = $visit['status'] ?? '';
                                        $statusClass = match($statusStr) {
                                            'Beklemede' => 'bg-amber-50 text-amber-600 border-amber-200',
                                            'Revizyon Bekliyor' => 'bg-purple-50 text-purple-600 border-purple-200',
                                            'Onaylandı' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                            'Tamamlandı' => 'bg-blue-50 text-blue-600 border-blue-200',
                                            'Reddedildi', 'İptal Edildi' => 'bg-rose-50 text-rose-600 border-rose-200',
                                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                                        };
                                    ?>
                                    <div class="flex flex-col items-start gap-1.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black border uppercase tracking-widest <?php echo e($statusClass); ?>">
                                            <?php echo e($statusStr ?: 'Bilinmiyor'); ?>

                                        </span>
                                        <?php
                                            $statusDate = null;
                                            $statusLabel = null;
                                            if ($statusStr === 'Onaylandı') {
                                                $statusDate = $visit['updated_at'];
                                                $statusLabel = 'Onaylandı';
                                            } elseif ($statusStr === 'Tamamlandı') {
                                                $statusDate = $visit['completed_at'] ?? $visit['updated_at'];
                                                $statusLabel = 'Tamamlandı';
                                            } elseif ($statusStr === 'Reddedildi') {
                                                $statusDate = $visit['updated_at'];
                                                $statusLabel = 'Reddedildi';
                                            } elseif ($statusStr === 'Revizyon Bekliyor') {
                                                $statusDate = $visit['updated_at'];
                                                $statusLabel = 'İstendi';
                                            } elseif ($statusStr === 'İptal Edildi') {
                                                $statusDate = $visit['cancelled_at'] ?? $visit['updated_at'];
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
                                
                                <td class="px-4 py-4 text-center text-sm font-medium relative overflow-visible">
                                    <!--[if BLOCK]><![endif]--><?php if($waitingText): ?>
                                        <div class="absolute -top-[2px] -right-[2px] z-10">
                                            <div class="px-2.5 py-1 rounded-bl-lg text-[9px] font-black uppercase whitespace-nowrap <?php echo e($waitingColor); ?> shadow-lg animate-pulse" style="letter-spacing: 0.3px;">
                                                ⚠ <?php echo e($waitingText); ?>

                                            </div>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <div class="flex justify-center items-center gap-2">
                                        <!--[if BLOCK]><![endif]--><?php if($complaint): ?>
                                            <a href="<?php echo e(route('admin.sikayetler.show', $complaint->id)); ?>" target="_blank" class="p-2 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition shadow-sm border border-blue-100">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Şikayet Detayı"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($complaint && $complaint->iaa_id): ?>
                                            <a href="<?php echo e(route('proje.workspace.show', $complaint->iaa_id)); ?>" target="_blank" class="p-2 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl transition shadow-sm border border-indigo-100">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Proje Çalışma Alanı"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-3 bg-gray-50 rounded-full mb-3">
                                            <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 17.242L12.001 14.414l2.828 2.828m-4.242-8.484l4.242 4.242m-4.242 0l4.242-4.242M12 21a9 9 0 110-18 9 9 0 010 18z"/></svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">Kayıt Bulunamadı</h3>
                                        <p class="text-xs text-gray-500 mt-1">Seçilen kriterlere uygun ziyaret kaydı bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="lg:hidden space-y-4">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $complaint = $complaints[$visit['remote_id']] ?? null;
                    
                    $waitingText = '';
                    $waitingColor = '';
                    $outlineColor = '';
                    $bgTint = 'bg-white';
                    
                    if (in_array($visit['return_date_revision_status'] ?? '', ['Bekliyor', 'Direktör Onayı Bekliyor'])) {
                        $waitingText = 'Tahmini Dönüş Revizyonu Bekliyor';
                        $waitingColor = 'text-white bg-orange-600';
                        $outlineColor = 'border-orange-500';
                        $bgTint = 'bg-orange-50/30';
                    } elseif (($visit['status'] ?? '') === 'Beklemede') {
                        $waitingText = 'Ziyaret Planı Onayı Bekliyor';
                        $waitingColor = 'text-white bg-amber-500';
                        $outlineColor = 'border-amber-400';
                        $bgTint = 'bg-amber-50/30';
                    } elseif (($visit['status'] ?? '') === 'Revizyon Bekliyor') {
                        $waitingText = 'Ziyaret Revizyonu Bekleniyor';
                        $waitingColor = 'text-white bg-purple-600';
                        $outlineColor = 'border-purple-500';
                        $bgTint = 'bg-purple-50/30';
                    } elseif (($visit['status'] ?? '') === 'Onaylandı') {
                        $waitingText = 'Müşteri Ziyareti Sonuçlarının Girilmesi Bekleniyor';
                        $waitingColor = 'text-white bg-indigo-600';
                        $outlineColor = 'border-indigo-500';
                        $bgTint = 'bg-indigo-50/30';
                    } elseif (($visit['status'] ?? '') === 'Tamamlandı') {
                        $outlineColor = 'border-emerald-500';
                    } elseif (($visit['status'] ?? '') === 'Reddedildi' || ($visit['status'] ?? '') === 'İptal Edildi') {
                        $outlineColor = 'border-rose-500';
                    }

                    $statusStr = $visit['status'] ?? '';
                    $statusClass = match($statusStr) {
                        'Beklemede' => 'bg-amber-50 text-amber-600 border-amber-200',
                        'Revizyon Bekliyor' => 'bg-purple-50 text-purple-600 border-purple-200',
                        'Onaylandı' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                        'Tamamlandı' => 'bg-blue-50 text-blue-600 border-blue-200',
                        'Reddedildi', 'İptal Edildi' => 'bg-rose-50 text-rose-600 border-rose-200',
                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                    };

                    $rowNumber = (($pagination['current_page'] - 1) * $pagination['per_page']) + $index + 1;
                ?>
                <div class="relative rounded-xl border-2 <?php echo e($outlineColor ?: 'border-gray-200'); ?> <?php echo e($bgTint); ?> shadow-sm overflow-hidden">
                    
                    <!--[if BLOCK]><![endif]--><?php if($waitingText): ?>
                        <div class="w-full px-3 py-1.5 <?php echo e($waitingColor); ?> text-[10px] font-black uppercase text-center animate-pulse" style="letter-spacing: 0.3px;">
                            ⚠ <?php echo e($waitingText); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <div class="p-4 space-y-3">
                        
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-gray-100 text-[11px] font-black text-gray-500"><?php echo e($rowNumber); ?></span>
                                <div class="flex-shrink-0 h-10 w-10 bg-white rounded-lg border border-gray-100 p-1 flex items-center justify-center overflow-hidden shadow-sm">
                                    <!--[if BLOCK]><![endif]--><?php if(!empty($visit['customer']['logo_path'])): ?>
                                        <img src="<?php echo e(asset('storage/' . $visit['customer']['logo_path'])); ?>" alt="Logo" class="max-h-full max-w-full object-contain">
                                    <?php else: ?>
                                        <div class="h-full w-full bg-indigo-50 rounded flex items-center justify-center text-indigo-700 font-bold text-sm">
                                            <?php echo e(strtoupper(substr($visit['customer']['name'] ?? 'M', 0, 1))); ?>

                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-900 truncate"><?php echo e(!empty($visit['customer']['name']) ? $visit['customer']['name'] : 'Müşteri Belirtilmedi'); ?></p>
                                    <!--[if BLOCK]><![endif]--><?php if(!empty($visit['product']['name'])): ?>
                                        <span class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-full inline-block mt-0.5 truncate max-w-full"><?php echo e($visit['product']['name']); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black border uppercase tracking-widest flex-shrink-0 <?php echo e($statusClass); ?>">
                                <?php echo e($statusStr ?: 'Bilinmiyor'); ?>

                            </span>
                        </div>

                        
                        <div class="grid grid-cols-3 gap-2 bg-gray-50/80 rounded-lg p-2.5">
                            <div>
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest block">Planlanan</span>
                                <span class="text-xs font-bold text-gray-900"><?php echo e(Carbon\Carbon::parse($visit['visit_date'])->translatedFormat('d M Y')); ?></span>
                                <span class="text-[10px] text-gray-500 block"><?php echo e(Carbon\Carbon::parse($visit['visit_date'])->format('H:i')); ?></span>
                            </div>
                            <div>
                                <span class="text-[9px] text-emerald-500 font-bold uppercase tracking-widest block">Tahmini Dönüş</span>
                                <!--[if BLOCK]><![endif]--><?php if(!empty($visit['estimated_return_date'])): ?>
                                    <span class="text-xs font-bold text-emerald-700"><?php echo e(Carbon\Carbon::parse($visit['estimated_return_date'])->translatedFormat('d M Y')); ?></span>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">-</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <span class="text-[9px] text-indigo-400 font-bold uppercase tracking-widest block">Onay Talebi</span>
                                <span class="text-xs font-bold text-indigo-700"><?php echo e(Carbon\Carbon::parse($visit['created_at'])->translatedFormat('d M Y')); ?></span>
                                <span class="text-[10px] text-indigo-500/70 block"><?php echo e(Carbon\Carbon::parse($visit['created_at'])->format('H:i')); ?></span>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest block mb-0.5">Ziyaret Nedeni</span>
                                <span class="text-xs font-black text-gray-600 uppercase"><?php echo e($visit['visit_reason']); ?></span>
                                <!--[if BLOCK]><![endif]--><?php if($visit['visit_notes']): ?>
                                    <p class="text-[11px] text-gray-500 line-clamp-2 mt-0.5"><?php echo e($visit['visit_notes']); ?></p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest block mb-0.5">Personel</span>
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0 h-7 w-7 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold text-[10px]">
                                        <?php echo e(strtoupper(substr($visit['user']['name'] ?? 'P', 0, 1))); ?>

                                    </div>
                                    <span class="text-xs font-bold text-gray-900"><?php echo e($visit['user']['name'] ?? 'Belirtilmedi'); ?></span>
                                </div>
                            </div>
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if($complaint): ?>
                            <div class="bg-white rounded-lg border border-gray-100 p-2.5">
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest block mb-1">İlgili Şikayet</span>
                                <p class="text-xs font-bold text-gray-700 line-clamp-2">#<?php echo e($complaint->id); ?> - <?php echo e($complaint->musteri_sikayet_konusu); ?></p>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <!--[if BLOCK]><![endif]--><?php if($complaint->bolum && $complaint->bolum->logo_yolu): ?>
                                        <img src="<?php echo e(asset('storage/' . $complaint->bolum->logo_yolu)); ?>" alt="<?php echo e($complaint->bolum->ad); ?>" class="h-4 w-4 object-contain rounded-sm">
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <span class="text-[10px] text-amber-600 font-black tracking-widest uppercase"><?php echo e($complaint->bolum->ad ?? 'Bölüm Yok'); ?></span>
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-100">
                            <!--[if BLOCK]><![endif]--><?php if($complaint): ?>
                                <a href="<?php echo e(route('admin.sikayetler.show', $complaint->id)); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Şikayet
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php if($complaint && $complaint->iaa_id): ?>
                                <a href="<?php echo e(route('proje.workspace.show', $complaint->iaa_id)); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Proje
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <div class="p-3 bg-gray-50 rounded-full mb-3 inline-block">
                        <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 17.242L12.001 14.414l2.828 2.828m-4.242-8.484l4.242 4.242m-4.242 0l4.242-4.242M12 21a9 9 0 110-18 9 9 0 010 18z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Kayıt Bulunamadı</h3>
                    <p class="text-xs text-gray-500 mt-1">Seçilen kriterlere uygun ziyaret kaydı bulunmuyor.</p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($pagination['total'] > $pagination['per_page']): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mt-6 px-6 py-4 flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button wire:click="previousPage" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Önceki</button>
                    <button wire:click="nextPage" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Sonraki</button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-500">
                            Toplam <span class="font-bold text-indigo-600"><?php echo e($pagination['total']); ?></span> kayıttan 
                            <span class="font-bold"><?php echo e(($pagination['current_page'] - 1) * $pagination['per_page'] + 1); ?></span> ile 
                            <span class="font-bold"><?php echo e(min($pagination['current_page'] * $pagination['per_page'], $pagination['total'])); ?></span> arası gösteriliyor.
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button wire:click="previousPage" <?php echo e($pagination['current_page'] == 1 ? 'disabled' : ''); ?> class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                                <span class="sr-only">Önceki</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 14.14L7.707 10l5.000-4.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            </button>
                            
                            <!--[if BLOCK]><![endif]--><?php for($i = 1; $i <= $pagination['last_page']; $i++): ?>
                                <button wire:click="gotoPage(<?php echo e($i); ?>)" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-bold <?php echo e($pagination['current_page'] == $i ? 'text-indigo-600 bg-indigo-50 border-indigo-500 z-10' : 'text-gray-700 hover:bg-gray-50'); ?>">
                                    <?php echo e($i); ?>

                                </button>
                            <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->

                            <button wire:click="nextPage" <?php echo e($pagination['current_page'] == $pagination['last_page'] ? 'disabled' : ''); ?> class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                                <span class="sr-only">Sonraki</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L12.293 10 7.293 5.293a1 1 0 011.414-1.414l5.707 5.707a1 1 0 010 1.414l-5.707 5.707a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/ziyaret-listesi.blade.php ENDPATH**/ ?>