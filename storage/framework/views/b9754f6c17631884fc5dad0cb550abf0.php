<div class="space-y-6">
    <style>
        @keyframes slowPulse {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(0.98); }
            100% { opacity: 1; transform: scale(1); }
        }
        .animate-slow-pulse {
            animation: slowPulse 3s ease-in-out infinite;
        }
    </style>
    
    <!--[if BLOCK]><![endif]--><?php if(session()->has('yeniSikayet')): ?>
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
            <p class="font-bold">🎉 Yeni Şikayet!</p>
            <p><?php echo e(session('yeniSikayet')); ?></p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-2 flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-sm font-bold text-gray-700">Tarih Aralığı:</span>
        </div>
        <div class="flex items-center gap-2">
            <input type="date" wire:model.live="startDate" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-700">
            <span class="text-gray-400">-</span>
            <input type="date" wire:model.live="endDate" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-700">
        </div>
        <!--[if BLOCK]><![endif]--><?php if($startDate || $endDate): ?>
            <button wire:click="clearFilter" class="text-xs text-red-500 hover:text-red-700 font-medium underline">
                Filtreyi Temizle
            </button>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        
        <div wire:click="setFilter('toplam')" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md hover:scale-[1.02] flex flex-col justify-between h-24 <?php echo e($activeFilter === 'toplam' ? 'ring-2 ring-blue-500 bg-blue-50/50' : ''); ?>">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Toplam</span>
                <div class="p-1.5 rounded-lg bg-blue-50 text-blue-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-blue-600"><?php echo e($kpi['toplam']); ?></div>
        </div>

        
        <div wire:click="setFilter('yeni')" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md hover:scale-[1.02] flex flex-col justify-between h-24 <?php echo e($activeFilter === 'yeni' ? 'ring-2 ring-yellow-500 bg-yellow-50/50' : ''); ?>">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Yeni</span>
                <div class="p-1.5 rounded-lg bg-yellow-50 text-yellow-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-yellow-600"><?php echo e($kpi['yeni']); ?></div>
        </div>

        
        <div wire:click="setFilter('islemde')" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md hover:scale-[1.02] flex flex-col justify-between h-24 <?php echo e($activeFilter === 'islemde' ? 'ring-2 ring-indigo-500 bg-indigo-50/50' : ''); ?>">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">İşlemde</span>
                <div class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-indigo-600"><?php echo e($kpi['islemde']); ?></div>
        </div>

        
        <div wire:click="setFilter('cozuldu')" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md hover:scale-[1.02] flex flex-col justify-between h-24 <?php echo e($activeFilter === 'cozuldu' ? 'ring-2 ring-green-500 bg-green-50/50' : ''); ?>">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Çözülen</span>
                <div class="p-1.5 rounded-lg bg-green-50 text-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-green-600"><?php echo e($kpi['cozuldu']); ?></div>
        </div>
        
        <div wire:click="setFilter('talep_kapatilan')" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md hover:scale-[1.02] flex flex-col justify-between h-24 <?php echo e($activeFilter === 'talep_kapatilan' ? 'ring-2 ring-purple-500 bg-purple-50' : ''); ?>">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Talep</span>
                <div class="p-1.5 rounded-lg bg-purple-50 text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-purple-600"><?php echo e($kpi['talep_kapatilan']); ?></div>
        </div>

        
        <div wire:click="setFilter('hatali_bildirim')" class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 cursor-pointer transition-all hover:shadow-md hover:scale-[1.02] flex flex-col justify-between h-24 <?php echo e($activeFilter === 'hatali_bildirim' ? 'ring-2 ring-orange-500 bg-orange-50' : ''); ?>">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Hatalı</span>
                <div class="p-1.5 rounded-lg bg-orange-50 text-orange-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-orange-500"><?php echo e($kpi['hatali_bildirim']); ?></div>
        </div>

        
        <div wire:click="setFilter('gecikmis')" class="bg-white p-3 rounded-xl shadow-sm border <?php echo e($kpi['gecikmis'] > 0 ? 'border-red-300 bg-red-50' : 'border-gray-100'); ?> cursor-pointer transition-all hover:shadow-md hover:scale-[1.02] flex flex-col justify-between h-24 <?php echo e($activeFilter === 'gecikmis' ? 'ring-2 ring-red-500' : ''); ?>">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold <?php echo e($kpi['gecikmis'] > 0 ? 'text-red-700' : 'text-gray-400'); ?> uppercase tracking-tighter">Gecikmiş</span>
                <div class="p-1.5 rounded-lg <?php echo e($kpi['gecikmis'] > 0 ? 'bg-red-100 text-red-600' : 'bg-gray-50 text-gray-400'); ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black <?php echo e($kpi['gecikmis'] > 0 ? 'text-red-600' : 'text-gray-800'); ?>"><?php echo e($kpi['gecikmis']); ?></div>
        </div>
    </div>

    

    
    <div class="space-y-6" x-data="{ open: <?php if ((object) ('tableOpen') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('tableOpen'->value()); ?>')<?php echo e('tableOpen'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('tableOpen'); ?>')<?php endif; ?> }"> 
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            
            
            <div class="flex flex-col md:flex-row md:items-center justify-between p-4 md:p-6 cursor-pointer hover:bg-gray-50/50 transition-colors" @click="open = !open">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <?php echo e(match($activeFilter) {
                            'yeni' => 'Yeni Şikayetler',
                            'islemde' => 'İşlemdeki Şikayetler',
                            'cozuldu' => 'Çözülen/Kapatılan Şikayetler',
                            'gecikmis' => 'Gecikmiş Şikayetler',
                            'talep_kapatilan' => 'Talep Olarak Kapatılan Şikayetler',
                            'hatali_bildirim' => 'Hatalı Bildirim Olarak Kapatılanlar',
                            default => 'Son Şikayet Kayıtları'
                        }); ?>

                    </h3>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            
            
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="border-t border-gray-200"
                 >
                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-gray-100 bg-gray-50/30 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Son Şikayet Kayıtları
                    </h3>
                    <div class="flex flex-wrap items-center gap-4 text-[11px] font-bold uppercase tracking-wide">
                        
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white text-red-700 rounded-xl border border-red-100 shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-6 h-6 flex items-center justify-center bg-red-500 rounded-lg text-white shadow-sm shadow-red-200 animate-pulse">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"/>
                                </svg>
                            </div>
                            <span>İadeli Şikayet</span>
                        </div>
                        
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white text-blue-700 rounded-xl border border-blue-100 shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-6 h-6 flex items-center justify-center bg-blue-500 rounded-lg text-white shadow-sm shadow-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span>Ziyaret Planlı</span>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block overflow-hidden"> 
                    <table class="w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 3%;">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Kayıt Tarihi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 11%;">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Müşteri İsmi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 17%;">Şikayet Başlığı</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Durum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Müşteri Kararı</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Son Tarih / Çözüm Tarihi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%;">Resim</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 7%;">Yorumlar</th> 
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 13%;">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $sonSikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $isExternal = ($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->is_personnel == 0) || ($sikayet->user_id && !$sikayet->olusturanKurulUyesi);
                                    
                                    // === RENKLENDİRME GÜNCELLENDİ ===
                                    $rowBg  = 'bg-white hover:bg-gray-50';
                                    $rowBar = 'border-l-4 border-gray-200';
                                    
                                    if ($isExternal) {
                                        // DIŞ KAYNAKLI - Kırmızı Uyarı
                                        $rowBg  = 'bg-red-50 hover:bg-red-100';
                                        $rowBar = 'border-l-4 border-red-600';
                                    } elseif ($sikayet->musteri_durum === 'İşlemde') {
                                        $rowBg  = 'bg-blue-100/30 hover:bg-blue-100/50';
                                        $rowBar = 'border-l-4 border-blue-500';
                                    } elseif ($sikayet->musteri_durum === 'Yeni') {
                                        $rowBg  = 'bg-yellow-100/30 hover:bg-yellow-100/50';
                                        $rowBar = 'border-l-4 border-yellow-500';
                                    } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi','Kapatıldı'])) {
                                        $rowBg  = 'bg-green-100/30 hover:bg-green-100/50';
                                        $rowBar = 'border-l-4 border-green-500';
                                    } elseif (in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) {
                                        $rowBg  = 'bg-purple-50 hover:bg-purple-100';
                                        $rowBar = 'border-l-4 border-purple-500';
                                    } elseif (in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) {
                                        $rowBg  = 'bg-orange-50 hover:bg-orange-100';
                                        $rowBar = 'border-l-4 border-orange-500';
                                    } else { 
                                        $rowBg  = 'bg-gray-100/50 hover:bg-gray-200/50';
                                        $rowBar = 'border-l-4 border-gray-400';
                                    }
                                ?>

                                <tr class="<?php echo e($rowBg); ?> <?php echo e($rowBar); ?> transition-all duration-200">
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-600">
                                        <?php echo e($loop->iteration); ?>

                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 whitespace-nowrap">
                                        <?php echo e($sikayet->created_at->format('d.m.Y')); ?>

                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 truncate" title="<?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?>">
                                        <?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?>

                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" target="_blank" class="truncate max-w-[180px] block hover:text-indigo-600 transition-colors font-bold" title="<?php echo e($sikayet->musteri_adi); ?>">
                                                <?php echo e($sikayet->musteri_adi); ?>

                                            </a>
                                            <!--[if BLOCK]><![endif]--><?php if($isExternal): ?>
                                                <span class="flex-shrink-0 inline-flex items-center justify-center w-6 h-6 rounded bg-red-100 text-red-600 border border-red-200 cursor-help transition-colors hover:bg-red-200" title="MÜŞTERİ GİRİŞİ (Personel Dışı Kayıt)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 truncate">
                                        <div class="font-bold text-gray-900 truncate" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>">
                                            <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                        </div>
                                        <div class="mt-2 flex items-center flex-nowrap gap-1.5 overflow-x-auto no-scrollbar">
                                            <!--[if BLOCK]><![endif]--><?php if(count($sikayet->iadeler) > 0): ?>
                                                <div class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-red-600 text-white shadow-sm transition-all hover:bg-red-700 group" title="İade Kaydı Mevcut">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"/>
                                                    </svg>
                                                    <span class="text-[8px] font-black uppercase tracking-tighter">İadeli</span>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if(isset($visitStats['visits_by_complaint'][$sikayet->id])): ?>
                                                <div class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-600 text-white shadow-sm transition-all hover:bg-blue-700 group" title="Müşteri ziyareti planlanmış">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="text-[8px] font-black uppercase tracking-tighter">Ziyaret</span>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <div class="flex flex-col gap-1.5">
                                            <div>
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php if($sikayet->musteri_durum == 'Yeni'): ?> bg-yellow-100 text-yellow-800
                                                    <?php elseif($sikayet->musteri_durum == 'İşlemde'): ?> bg-blue-100 text-blue-800
                                                    <?php elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])): ?> bg-green-100 text-green-800
                                                    <?php elseif(in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])): ?> bg-purple-100 text-purple-700 border border-purple-200 font-bold
                                                    <?php elseif(in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])): ?> bg-orange-100 text-orange-800 border border-orange-200 line-through
                                                    <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                                    <?php echo e($sikayet->musteri_durum); ?>

                                                </span>
                                            </div>
                                            <!--[if BLOCK]><![endif]--><?php if(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']) && $sikayet->iaaProjesi): ?>
                                                <div class="flex items-center gap-1 opacity-90 pl-1">
                                                    <?php
                                                        $pDurum = $sikayet->iaaProjesi->durum;
                                                        $isFaulty = Str::contains($pDurum, 'hatali_bildirim');
                                                        $isRequest = Str::contains($pDurum, 'talep');
                                                        $tooltipText = $isFaulty ? 'Hatalı Bildirim Olarak Kapatıldı' : ($isRequest ? 'Talep Olarak Kapatıldı' : 'Proje Durumu: ' . $pDurum);
                                                    ?>
                                                    
                                                    <!--[if BLOCK]><![endif]--><?php if($isFaulty): ?>
                                                        <div class="group relative cursor-help">
                                                            <svg class="w-5 h-5 text-red-500 hover:text-red-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                            </svg>
                                                            <!-- Tooltip -->
                                                            <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                                                <?php echo e($tooltipText); ?>

                                                            </span>
                                                        </div>
                                                    <?php elseif($isRequest): ?>
                                                        <div class="group relative cursor-help">
                                                            <svg class="w-5 h-5 text-blue-500 hover:text-blue-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            <!-- Tooltip -->
                                                            <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                                                <?php echo e($tooltipText); ?>

                                                            </span>
                                                        </div>
                                                    <?php else: ?>
                                                        <div title="<?php echo e($pDurum); ?>">
                                                            <?php echo $sikayet->iaaProjesi->durum_etiketi; ?>

                                                        </div>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <!--[if BLOCK]><![endif]--><?php if($sikayet->musteri_feedback): ?>
                                            <?php
                                                $renk = match($sikayet->musteri_feedback) {
                                                    'Onaylandı' => 'text-green-700 bg-green-50 border-green-200',
                                                    'Reddedildi' => 'text-red-700 bg-red-50 border-red-200',
                                                    default => 'text-yellow-700 bg-yellow-50 border-yellow-200'
                                                };
                                            ?>
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border <?php echo e($renk); ?>">
                                                <!--[if BLOCK]><![endif]--><?php if($sikayet->musteri_feedback == 'Onaylandı'): ?>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <?php elseif($sikayet->musteri_feedback == 'Reddedildi'): ?>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <?php else: ?>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <span class="text-xs font-bold uppercase"><?php echo e($sikayet->musteri_feedback); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">-</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <div class="flex flex-col gap-1">
                                            <?php
                                                $isOverdue = $sikayet->musteri_cozum_son_tarihi && 
                                                            $sikayet->musteri_cozum_son_tarihi->isPast() && 
                                                            !in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']);
                                            ?>
                                            <div class="<?php echo e($isOverdue ? 'text-red-700 font-black' : 'text-gray-600 font-medium'); ?> flex flex-col gap-2 py-1" title="Hedef Çözüm Tarihi">
                                                <span><?php echo e($sikayet->musteri_cozum_son_tarihi ? $sikayet->musteri_cozum_son_tarihi->format('d.m.Y') : 'N/A'); ?></span>
                                                <!--[if BLOCK]><![endif]--><?php if($isOverdue): ?>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black bg-red-100 text-red-600 border border-red-200 animate-pulse uppercase tracking-tighter w-fit shadow-sm">
                                                        ⚠️ Gecikti (<?php echo e((int) $sikayet->musteri_cozum_son_tarihi->diffInDays(now())); ?> Gün)
                                                    </span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            <!--[if BLOCK]><![endif]--><?php if($sikayet->kurul_onay_tarihi || $sikayet->musteri_onay_tarihi): ?>
                                                <div class="text-green-600 font-bold text-[11px] mt-1 border-t border-gray-100 pt-1" title="Kapanış Tarihi">
                                                    <?php echo e(($sikayet->kurul_onay_tarihi ?? $sikayet->musteri_onay_tarihi)->format('d.m.Y')); ?>

                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500">
                                        
                                        <div class="flex items-center space-x-1">
                                            <?php 
                                                $imageFiles = $sikayet->dosyalar->filter(function($dosya) {
                                                    return Str::startsWith($dosya->mime_tipi, 'image/');
                                                });
                                            ?>
                                            
                                            <!--[if BLOCK]><![endif]--><?php $__empty_2 = true; $__currentLoopData = $imageFiles->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?> 
                                                <a href="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" target="_blank" title="<?php echo e($dosya->orijinal_adi); ?>">
                                                    <img src="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" alt="Önizleme" class="h-8 w-8 rounded-md object-cover border border-gray-300 hover:scale-110 transition-transform">
                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <span class="text-xs">Yok</span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            
                                            <!--[if BLOCK]><![endif]--><?php if($imageFiles->count() > 2): ?>
                                                <span class="text-xs text-gray-400 font-bold ml-1">+<?php echo e($imageFiles->count() - 2); ?></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <div class="flex items-center space-x-1">
                                            <!--[if BLOCK]><![endif]--><?php if($sikayet->proje_yorumlari_count > 0): ?>
                                                <span class="font-bold"><?php echo e($sikayet->proje_yorumlari_count); ?></span>
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                
                                                
                                                <!--[if BLOCK]><![endif]--><?php if($sikayet->musteri_proje_yorumlari_count > 0): ?>
                                                    <span class="text-yellow-500" title="Müşteri Yorumu Var">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    </span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400">Yorum Yok</span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-xs">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" target="_blank"
                                               class="inline-flex items-center px-3 py-1.5 font-semibold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                                Detay
                                            </a>
                                            <!--[if BLOCK]><![endif]--><?php if($sikayet->iaaProjesi ?? null): ?>
                                                <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaaProjesi)); ?>" target="_blank"
                                                   class="inline-flex items-center px-3 py-1.5 font-semibold rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200 transition">
                                                    Proje
                                                </a>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Sisteme kayıtlı şikayet bulunamadı.
                                    </td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                
                <div class="md:hidden">
                    <div class="space-y-4 p-4">
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $sonSikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                // === RENKLENDİRME GÜNCELLENDİ (ELSE EKLENDİ) ===
                                $rowBg = 'hover:bg-gray-50';
                                $rowBar = 'border-l-4 border-gray-200'; // Varsayılan (diğer durumlar)
                                if ($sikayet->musteri_durum === 'İşlemde') {
                                    $rowBg = 'bg-blue-100/30 hover:bg-blue-100/50';
                                    $rowBar = 'border-l-4 border-blue-500';
                                } elseif ($sikayet->musteri_durum === 'Yeni') {
                                    $rowBg = 'bg-yellow-100/30 hover:bg-yellow-100/50';
                                    $rowBar = 'border-l-4 border-yellow-500';
                                } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi','Kapatıldı'])) {
                                    $rowBg = 'bg-green-100/30 hover:bg-green-100/50';
                                    $rowBar = 'border-l-4 border-green-500';
                                } else {
                                    $rowBg = 'bg-gray-100/50 hover:bg-gray-200/50';
                                    $rowBar = 'border-l-4 border-gray-400';
                                }
                            ?>

                            <div class="rounded-lg shadow border <?php echo e($rowBg); ?> <?php echo e($rowBar); ?> p-4 space-y-3">
                                
                                
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="font-semibold text-gray-700">#<?php echo e($loop->iteration); ?></span>
                                        <span class="text-sm text-gray-600 ml-2"><?php echo e($sikayet->created_at?->format('d.m.Y H:i')); ?></span>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php if($sikayet->musteri_durum == 'Yeni'): ?> bg-yellow-100 text-yellow-800
                                            <?php elseif($sikayet->musteri_durum == 'İşlemde'): ?> bg-blue-100 text-blue-800
                                            <?php elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])): ?> bg-green-100 text-green-800
                                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                            <?php echo e($sikayet->musteri_durum); ?>

                                        </span>
                                        <!--[if BLOCK]><![endif]--><?php if(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']) && $sikayet->iaaProjesi): ?>
                                            <div class="flex items-center gap-1 opacity-90 justify-end">
                                                <span class="text-[9px] text-gray-400 font-bold uppercase">İAA:</span>
                                                <div class="inline-block whitespace-nowrap overflow-hidden">
                                                    <?php echo $sikayet->iaaProjesi->durum_etiketi; ?>

                                                </div>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                
                                <div>
                                    <p class="text-xs text-gray-500 uppercase truncate" title="<?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?>"><?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?></p>
                                    
                                    <div class="flex flex-col gap-0.5">
                                        <p class="text-base font-semibold text-gray-900 truncate"
                                            title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>">
                                            <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                        </p>
                                        <div class="flex items-center flex-nowrap gap-1.5 overflow-x-auto no-scrollbar">
                                            <!--[if BLOCK]><![endif]--><?php if(count($sikayet->iadeler) > 0): ?>
                                                <div class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-red-600 text-white shadow-sm transition-all hover:bg-red-700 group">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"/>
                                                    </svg>
                                                    <span class="text-[8px] font-black uppercase tracking-tighter">İadeli</span>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if(isset($visitStats['visits_by_complaint'][$sikayet->id])): ?>
                                                <div class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-600 text-white shadow-sm transition-all hover:bg-blue-700 group">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="text-[8px] font-black uppercase tracking-tighter">Ziyaret</span>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" target="_blank" class="text-sm font-medium text-gray-700 truncate hover:text-indigo-600 transition-colors" title="<?php echo e($sikayet->musteri_adi); ?>"><?php echo e($sikayet->musteri_adi); ?></a>
                                        <!--[if BLOCK]><![endif]--><?php if(($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->is_personnel == 0) || ($sikayet->user_id && !$sikayet->olusturanKurulUyesi)): ?>
                                            <span class="flex-shrink-0 inline-flex items-center justify-center w-5 h-5 rounded bg-red-100 text-red-600 border border-red-200" title="MÜŞTERİ GİRİŞİ (Personel Dışı)">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                                
                                
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                    <div class="text-sm">
                                        <span class="text-gray-500">Son Tarih:</span>
                                        <?php
                                            $isOverdue = $sikayet->musteri_cozum_son_tarihi && 
                                                        $sikayet->musteri_cozum_son_tarihi->isPast() && 
                                                        !in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']);
                                        ?>
                                        <span class="font-semibold <?php echo e($isOverdue ? 'text-red-700 font-black' : 'text-gray-500'); ?>">
                                            <?php echo e($sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A'); ?>

                                        </span>
                                        <!--[if BLOCK]><![endif]--><?php if($isOverdue): ?>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-red-100 text-red-600 border border-red-200 animate-pulse uppercase tracking-tighter w-fit">
                                                    ⚠️ Gecikti (<?php echo e((int) $sikayet->musteri_cozum_son_tarihi->diffInDays(now())); ?> Gün)
                                                </span>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <?php
                                        $imageFiles = $sikayet->dosyalar->filter(fn($d) => Str::startsWith($d->mime_tipi, 'image/'));
                                    ?>
                                    <div class="flex items-center space-x-1">
                                        <!--[if BLOCK]><![endif]--><?php $__empty_2 = true; $__currentLoopData = $imageFiles->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <a href="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" target="_blank" title="<?php echo e($dosya->orijinal_adi); ?>" onclick="event.stopPropagation()">
                                                <img src="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>"
                                                     class="h-8 w-8 rounded-md object-cover border border-gray-300"
                                                     alt="Önizleme">
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                            <span class="text-xs text-gray-400">Resim Yok</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($imageFiles->count() > 2): ?>
                                            <span class="text-xs text-gray-400 font-bold ml-1">+<?php echo e($imageFiles->count() - 2); ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                
                                
                                    <div class="flex items-center space-x-1 text-sm text-gray-700">
                                        <!--[if BLOCK]><![endif]--><?php if($sikayet->proje_yorumlari_count > 0): ?>
                                            <span class="font-bold"><?php echo e($sikayet->proje_yorumlari_count); ?></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            
                                            <!--[if BLOCK]><![endif]--><?php if($sikayet->musteri_proje_yorumlari_count > 0): ?>
                                                <span class="text-yellow-500" title="Müşteri Yorumu Var">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">Yorum Yok</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200">
                                    <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                                       onclick="event.stopPropagation()">
                                        Detay
                                    </a>
                                    <!--[if BLOCK]><![endif]--><?php if($sikayet->iaaProjesi ?? null): ?>
                                        <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaaProjesi)); ?>" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                                           onclick="event.stopPropagation()">
                                            Proje
                                        </a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="px-6 py-8 text-center text-sm text-gray-500">
                                Kayıt bulunamadı.
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                
                <div class="p-4 bg-gray-50 border-t border-gray-200 text-center">
                    <a href="<?php echo e(route('admin.sikayet-raporlari.tum-liste')); ?>" target="_blank" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 font-semibold text-sm rounded-lg hover:bg-indigo-200 transition-colors duration-200">
                        Tüm Şikayet Listesini Gör
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
                

            </div>
        </div>
    </div>
    
    
    
    <!--[if BLOCK]><![endif]--><?php if($enCokGecikenler->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-lg border border-red-100 overflow-hidden mt-8">
        <div class="px-6 py-4 bg-red-50 border-b border-red-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-red-600 rounded-lg text-white shadow-lg animate-pulse">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-black text-red-800 uppercase tracking-tight">
                    En Çok Geciken Şikayetler (Top 5)
                </h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black bg-white text-red-600 border border-red-200 px-2 py-1 rounded-full uppercase shadow-sm">KRİTİK GECİKME ANALİZİ</span>
                <a href="<?php echo e(route('admin.sikayet-raporlari.tum-liste', ['gecikmis' => 1])); ?>" target="_blank" class="text-xs font-black text-red-700 hover:underline flex items-center gap-1">
                    Tümünü Gör 
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest" style="width: 25%;">Şikayet / Müşteri</th>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center" style="width: 15%;">Kayıt Tarihi</th>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center" style="width: 15%;">Son Çözüm (Deadline)</th>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest" style="width: 15%;">Sorumlu / Takım</th>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center" style="width: 15%;">Gecikme</th>
                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right" style="width: 15%;">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $enCokGecikenler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $geciken): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-red-50/40 transition-colors group">
                            <td class="px-4 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 group-hover:text-red-700 transition-colors truncate max-w-[200px]"><?php echo e($geciken->musteri_adi); ?></span>
                                    <span class="text-[10px] text-gray-500 truncate max-w-[200px] font-medium"><?php echo e($geciken->musteri_sikayet_konusu); ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="text-xs font-bold text-gray-600"><?php echo e($geciken->created_at->format('d.m.Y')); ?></span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-red-600"><?php echo e($geciken->hesaplanan_deadline->format('d.m.Y')); ?></span>
                                    <span class="text-[9px] font-black <?php echo e($geciken->deadline_type == 'Manuel' ? 'text-blue-500' : 'text-orange-500'); ?> uppercase"><?php echo e($geciken->deadline_type); ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-bold text-indigo-600 truncate max-w-[120px]"><?php echo e($geciken->cozumTakimi->ad ?? 'Atanmamış'); ?></span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase truncate max-w-[120px]"><?php echo e($geciken->sikayetKategori->bolum->ad ?? '-'); ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="inline-flex flex-col items-center bg-red-100 px-3 py-1 rounded-lg border border-red-200">
                                    <span class="text-lg font-black text-red-700 leading-none"><?php echo e($geciken->gecikme_gunu); ?></span>
                                    <span class="text-[9px] font-black text-red-500 uppercase">GÜN</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('admin.sikayetler.show', $geciken)); ?>" target="_blank" class="p-1.5 bg-white border border-red-100 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <!--[if BLOCK]><![endif]--><?php if($geciken->iaa_id): ?>
                                        <a href="<?php echo e(route('proje.workspace.show', $geciken->iaa_id)); ?>" target="_blank" class="p-1.5 bg-white border border-purple-100 text-purple-600 rounded-lg hover:bg-purple-600 hover:text-white transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-center">
            <a href="<?php echo e(route('admin.sikayet-raporlari.tum-liste', ['gecikmis' => 1])); ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-xs font-black rounded-lg hover:bg-red-700 transition-all shadow-md">
                <span>KRİTİK GECİKMELERİN TÜMÜNÜ GÖRÜNTÜLE</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Bölümlere Göre Şikayet Dağılımı</h3>
                <span class="text-xs font-medium bg-indigo-100 text-indigo-800 px-2.5 py-0.5 rounded">Kategori Bazlı</span>
            </div>
            
            <div id="bolumKategoriChart" wire:ignore></div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Sorun Türleri Yoğunluk Haritası</h3>
                <span class="text-xs font-medium bg-purple-100 text-purple-800 px-2.5 py-0.5 rounded">Alt Kategoriler</span>
            </div>
            <div id="altKategoriChart" wire:ignore></div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" wire:ignore>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Müşteri Geri Bildirim Dağılımı</h3>
            <div id="customerFeedbackChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Bölüm Bazlı Müşteri Memnuniyeti</h3>
            <div id="deptSatisfactionChart"></div>
        </div>
    </div>

    
    <div class="space-y-6 mt-8">
        <h3 class="text-xl font-bold text-gray-800 border-b pb-2 flex items-center gap-2">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            İade ve Ürün Red Analizleri
        </h3>
        
        
        
        <?php
            $iadeDateStr = '<span class="font-bold text-gray-700">Tüm Zamanlar</span>';
            if($startDate && $endDate) {
                $iadeDateStr = '<span class="font-bold text-gray-700">' . \Carbon\Carbon::parse($startDate)->format('d.m.Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d.m.Y') . '</span>';
            } elseif($startDate) {
                $iadeDateStr = '<span class="font-bold text-gray-700">' . \Carbon\Carbon::parse($startDate)->format('d.m.Y') . ' sonrası</span>';
            } elseif($endDate) {
                $iadeDateStr = '<span class="font-bold text-gray-700">' . \Carbon\Carbon::parse($endDate)->format('d.m.Y') . ' öncesi</span>';
            }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-gradient-to-br from-red-50 to-white p-6 rounded-xl shadow-lg border border-red-100 flex flex-col justify-center items-center text-center">
                <h4 class="text-xs font-bold text-red-500 uppercase tracking-widest mb-1">Gerçekleşen İade Tutarları</h4>
                <p class="text-[10px] text-gray-500 mb-4">(<?php echo $iadeDateStr; ?> İşlem Tarihli)</p>
                
                <!--[if BLOCK]><![endif]--><?php if(isset($toplamIadeMiktarlari) && count($toplamIadeMiktarlari) > 0): ?>
                    <div class="space-y-3 w-full">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $toplamIadeMiktarlari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tutar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between items-center bg-white px-3 py-2 rounded shadow-sm border border-red-50">
                                <span class="text-sm text-gray-500 font-medium"><?php echo e($tutar->birim); ?></span>
                                <span class="text-lg font-black text-red-700"><?php echo e(number_format($tutar->total, 2, ',', '.')); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php else: ?>
                    <p class="text-xl font-black text-red-300">İade Yok</p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <p class="text-[10px] text-red-400 mt-4 font-medium"><?php echo $startDate || $endDate ? $iadeDateStr . ' tarihleri arasında yapılan toplam iade' : 'Tüm zamanların toplamı'; ?></p>
            </div>

            
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 relative">
                <div class="flex items-start gap-3 mb-6">
                    <div class="bg-blue-50 p-2 rounded-lg border border-blue-100">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-tight">İadeye Dönüşme Oranı</h4>
                        <p class="text-[10px] text-gray-400 mt-0.5 font-medium italic">(<?php echo $iadeDateStr; ?> Açılış Tarihli)</p>
                    </div>
                </div>
                <div id="iadeliOranChart" wire:ignore></div>
            </div>

            
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex items-start gap-3 mb-6">
                    <div class="bg-red-50 p-2 rounded-lg border border-red-100">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v16m-6 0a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-tight">Gerçekleşen İade Miktarları</h4>
                        <p class="text-[10px] text-gray-400 mt-0.5 font-medium italic"><?php echo $startDate || $endDate ? $iadeDateStr . ' aralığında' : $iadeDateStr . 'da'; ?> yapılan iadelerin bölüm dağılımı</p>
                    </div>
                </div>
                <div id="bolumIadeChartsContainer" class="space-y-4 pr-1" wire:ignore>
                    
                </div>
            </div>

            
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                 <div class="flex items-start gap-3 mb-6">
                    <div class="bg-indigo-50 p-2 rounded-lg border border-indigo-100">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-tight">Şikayetlerin İade Dağılımı</h4>
                        <p class="text-[10px] text-gray-400 mt-0.5 font-medium italic"><?php echo $startDate || $endDate ? $iadeDateStr . ' aralığında' : $iadeDateStr . 'da'; ?> açılan şikayetlerin iadeli durumu</p>
                    </div>
                </div>
                 <div id="bolumIadeCountChart" wire:ignore></div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
             <div class="px-6 py-5 border-b border-gray-100 bg-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="mt-1 bg-gray-50 p-2 rounded-lg border border-gray-100">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-bold text-gray-800 tracking-tight">Detaylı İade Listesi</h3>
                                <span class="px-2 py-0.5 rounded bg-red-50 text-red-500 text-[10px] font-black uppercase tracking-wider border border-red-100/50">
                                    TOPLAM <?php echo e($iadeVerileriCount); ?> KAYIT
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5 font-medium italic">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <?php echo $startDate || $endDate ? $iadeDateStr . ' aralığındaki kayıtlar listelenmektedir.' : 'Sistemdeki tüm zamanlara ait kayıtlar listelenmektedir.'; ?>

                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 md:self-start">
                        EN SON KAYITTAN BAŞLAYARAK
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarihler (İade/Kapanış)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri / Bölüm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şikayet & Proje</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün / Sebep</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Miktar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!--[if BLOCK]><![endif]--><?php if(isset($iadeVerileri)): ?>
                            <?php
                                $iadeler = $iadeVerileri->take($iadeLimit);
                            ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $iadeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 transition-colors <?php echo e($loop->index >= 5 ? 'bg-gray-50/50' : ''); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-400">
                                        <?php echo e($loop->iteration); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-1.5" title="İade Tarihi">
                                                <span class="text-[10px] font-bold text-red-500 w-14 uppercase">İade:</span>
                                                <span class="font-bold text-gray-900"><?php echo e($iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : $iade->created_at->format('d.m.Y')); ?></span>
                                            </div>
                                            <div class="flex items-center gap-1.5" title="Şikayet Açılış Tarihi">
                                                <span class="text-[10px] font-bold text-gray-400 w-14 uppercase">Şikayet:</span>
                                                <span><?php echo e($iade->musteriSikayeti->created_at->format('d.m.Y')); ?></span>
                                            </div>
                                            <div class="flex items-center gap-1.5" title="Kapanış Tarihi">
                                                <span class="text-[10px] font-bold text-gray-400 w-14 uppercase">Kapanış:</span>
                                                <span>
                                                    <?php
                                                        $kapanisTarihi = $iade->musteriSikayeti->kurul_onay_tarihi ?? $iade->musteriSikayeti->musteri_onay_tarihi;
                                                        if(!$kapanisTarihi && in_array($iade->musteriSikayeti->musteri_durum, ['Çözümlendi', 'Kapatıldı', 'Talep Olarak Kapatıldı', 'Hatalı Bildirim Olarak Kapatıldı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi'])) {
                                                            $kapanisTarihi = $iade->musteriSikayeti->updated_at;
                                                        }
                                                    ?>
                                                    <!--[if BLOCK]><![endif]--><?php if($kapanisTarihi): ?>
                                                        <?php echo e($kapanisTarihi->format('d.m.Y')); ?>

                                                    <?php else: ?>
                                                        <span class="text-xs font-medium text-yellow-600">Açık</span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-gray-900 line-clamp-1" title="<?php echo e($iade->musteriSikayeti->musteri_adi ?? ''); ?>"><?php echo e($iade->musteriSikayeti->musteri_adi ?? '-'); ?></span>
                                            <!--[if BLOCK]><![endif]--><?php if($iade->musteriSikayeti->sikayetKategori && $iade->musteriSikayeti->sikayetKategori->bolum): ?>
                                                <span class="text-xs inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium w-fit"><?php echo e($iade->musteriSikayeti->sikayetKategori->bolum->ad); ?></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium line-clamp-1 py-1"><?php echo e($iade->musteriSikayeti->musteri_sikayet_konusu ?? '-'); ?></div>
                                        
                                        <div class="flex items-center gap-2 mt-2">
                                            
                                            <a href="<?php echo e(route('admin.sikayetler.show', $iade->musteriSikayeti->id)); ?>" 
                                               class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider rounded border border-gray-200 hover:bg-gray-200 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Şikayet #<?php echo e($iade->musteriSikayeti->id); ?>

                                            </a>
                                            
                                            
                                            <!--[if BLOCK]><![endif]--><?php if($iade->musteriSikayeti->iaaProjesi): ?>
                                                <a href="<?php echo e(route('proje.workspace.show', $iade->musteriSikayeti->iaaProjesi->id)); ?>" 
                                                   class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider rounded border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    Proje #<?php echo e($iade->musteriSikayeti->iaaProjesi->id); ?>

                                                </a>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?php echo e($iade->urun_turu); ?></div>
                                        <div class="text-xs text-red-500"><?php echo e($iade->iade_sebebi); ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full bg-red-100 text-red-800">
                                            <?php echo e(number_format($iade->miktar, 0, ',', '.')); ?> <?php echo e($iade->birim); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            
                            <!--[if BLOCK]><![endif]--><?php if($iadeVerileri->count() == 0): ?>
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Kayıt yok.</td></tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php else: ?>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Veri yok.</td></tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
            <?php if(isset($iadeVerileri) && $iadeVerileri->count() > 5): ?>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-center gap-4">
                    <!--[if BLOCK]><![endif]--><?php if($iadeLimit < $iadeVerileri->count()): ?>
                        <button wire:click="increaseIadeLimit" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            Daha Fazla (+5)
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if($iadeLimit > 5): ?>
                        <button wire:click="decreaseIadeLimit" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            Daha Az (-5)
                        </button>
                        
                        <button wire:click="resetIadeLimit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors ml-4">
                            Hepsini Kısalt
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    

    
    <div class="space-y-6 mt-8">
        <h3 class="text-xl font-bold text-gray-800 border-b pb-2 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Müşteri Ziyaret Analizleri
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-white p-4 rounded-xl shadow border border-gray-100 flex flex-col items-center justify-center">
                <span class="text-xs font-bold text-gray-400 uppercase">Toplam Ziyaret</span>
                <span class="text-3xl font-black text-blue-600"><?php echo e($visitStats['total_visits'] ?? 0); ?></span>
            </div>
            
            <div class="bg-white p-4 rounded-xl shadow border border-gray-100 flex flex-col items-center justify-center">
                <span class="text-xs font-bold text-gray-400 uppercase">Ziyaretli Şikayet</span>
                <span class="text-3xl font-black text-gray-800"><?php echo e($visitStats['visited_count'] ?? 0); ?></span>
            </div>
            
            <div class="bg-white p-4 rounded-xl shadow border border-gray-100 flex flex-col items-center justify-center">
                <span class="text-xs font-bold text-gray-400 uppercase">Ziyaret Oranı</span>
                <span class="text-3xl font-black text-purple-600">%<?php echo e($visitStats['visit_rate'] ?? 0); ?></span>
            </div>
            
            <div class="bg-white p-4 rounded-xl shadow border border-gray-100 flex flex-col items-center justify-center">
                <span class="text-xs font-bold text-gray-400 uppercase">En Çok Ziyaret</span>
                <span class="text-lg font-bold text-gray-700 truncate w-full text-center">
                    <?php echo e(count($visitStats['dept_visit_rates'] ?? []) > 0 ? array_key_first($visitStats['dept_visit_rates']) : '-'); ?>

                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100" wire:ignore>
                <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase">Şikayet/Ziyaret Oranı</h4>
                <div id="visitRateChart"></div>
            </div>

            
            <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100" wire:ignore>
                <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase">Ziyaret Sebepleri Dağılımı</h4>
                <div id="visitReasonChart"></div>
            </div>

            
            <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100" wire:ignore>
                <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase">Bölüm Bazlı Ziyaret Sayıları</h4>
                <div id="deptVisitChart"></div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Son Müşteri Ziyaret Kayıtları</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ziyaret Tarihi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri / Bölüm</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şikayet & Proje</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ziyaret Sebebi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $visitStats['recent_visits'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div class="font-medium text-gray-900"><?php echo e(\Carbon\Carbon::parse($visit['visit_date'])->format('d.m.Y')); ?></div>
                                <div class="text-xs text-gray-400"><?php echo e(\Carbon\Carbon::parse($visit['visit_date'])->diffForHumans()); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900 line-clamp-1" title="<?php echo e($visit['musteri_adi']); ?>"><?php echo e($visit['musteri_adi']); ?></span>
                                    <span class="text-xs inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium w-fit"><?php echo e($visit['bolum_adi']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium line-clamp-1 py-1"><?php echo e($visit['complaint_subject']); ?></div>
                                <div class="flex items-center gap-2 mt-2">
                                    <a href="<?php echo e(route('admin.sikayetler.show', $visit['remote_id'])); ?>" 
                                       class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider rounded border border-gray-200 hover:bg-gray-200 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Şikayet #<?php echo e($visit['remote_id']); ?>

                                    </a>
                                    <!--[if BLOCK]><![endif]--><?php if($visit['iaa_projesi_id']): ?>
                                        <a href="<?php echo e(route('proje.workspace.show', $visit['iaa_projesi_id'])); ?>" 
                                           class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider rounded border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Proje #<?php echo e($visit['iaa_projesi_id']); ?>

                                        </a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-blue-600 font-medium">
                                <?php echo e($visit['visit_reason']); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Ziyaret kaydı bulunamadı.</td></tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Şikayet Durum Dağılımı</h3>
            <div id="sikayetDurumChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Şikayet Kategorisi</h3>
            <div id="sikayetKategoriChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Çözüm Takımı (Şikayet Sayısı)</h3>
            <div id="sikayetTakimChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aylık Şikayet Kayıt Trendi (Son 12 Ay)</h3>
            <div id="sikayetTrendChart"></div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-green-700 mb-4">Çözülen / Kapatılan Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="cozulenChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son Çözülenler Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $cozulenListesi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="<?php echo e($item->musteri_sikayet_konusu); ?>"><?php echo e($item->musteri_sikayet_konusu); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo e($item->musteri_adi); ?></p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="<?php echo e(route('admin.sikayetler.show', $item)); ?>" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    <!--[if BLOCK]><![endif]--><?php if($item->iaaProjesi): ?>
                                    <a href="<?php echo e(route('proje.workspace.show', $item->iaaProjesi)); ?>" title="Proje Çalışma Alanını Gör" class="p-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 000 2h.01a1 1 0 100-2H5zM6 15a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM4 17a1 1 0 100 2h12a1 1 0 100-2H4z"/></svg>
                                    </a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">Çözülmüş şikayet bulunamadı.</p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-indigo-700 mb-4">İşlemde Olan Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="islemdeChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son İşleme Alınanlar Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $islemdeListesi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="<?php echo e($item->musteri_sikayet_konusu); ?>"><?php echo e($item->musteri_sikayet_konusu); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo e($item->musteri_adi); ?></p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="<?php echo e(route('admin.sikayetler.show', $item)); ?>" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    <!--[if BLOCK]><![endif]--><?php if($item->iaaProjesi): ?>
                                    <a href="<?php echo e(route('proje.workspace.show', $item->iaaProjesi)); ?>" title="Proje Çalışma Alanını Gör" class="p-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 000 2h.01a1 1 0 100-2H5zM6 15a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM4 17a1 1 0 100 2h12a1 1 0 100-2H4z"/></svg>
                                    </a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">İşlemde olan şikayet bulunamadı.</p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-yellow-700 mb-4">Yeni (Beklemede) Olan Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="yeniChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son Gelenler Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $yeniListesi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="<?php echo e($item->musteri_sikayet_konusu); ?>"><?php echo e($item->musteri_sikayet_konusu); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo e($item->musteri_adi); ?></p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="<?php echo e(route('admin.sikayetler.show', $item)); ?>" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">Yeni şikayet bulunamadı.</p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-purple-700 mb-4">Projeye Dönüşen Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="projeyeDonusenChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son Dönüşenler Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $projeyeDonusenListesi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="<?php echo e($item->musteri_sikayet_konusu); ?>"><?php echo e($item->musteri_sikayet_konusu); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo e($item->musteri_adi); ?></p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="<?php echo e(route('admin.sikayetler.show', $item)); ?>" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    <!--[if BLOCK]><![endif]--><?php if($item->iaaProjesi): ?>
                                    <a href="<?php echo e(route('proje.workspace.show', $item->iaaProjesi)); ?>" title="Proje Çalışma Alanını Gör" class="p-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 000 2h.01a1 1 0 100-2H5zM6 15a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM4 17a1 1 0 100 2h12a1 1 0 100-2H4z"/></svg>
                                    </a>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">Projeye dönüşen şikayet bulunamadı.</p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl p-6 border border-gray-100 mt-6" wire:ignore>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Şikayet Trend Analizi</h3>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-xs text-gray-500 font-medium">Gelen Şikayet</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-xs text-gray-500 font-medium">Çözülen Şikayet</span>
                </div>
            </div>
        </div>
        <div id="combinedComplaintTrendChart"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        // --- 1. ANA KPI GRAFİKLERİ ---
        window.sikayetDurumChart = new ApexCharts(document.querySelector("#sikayetDurumChart"), {
            series: <?php echo json_encode(array_values($durumData->toArray()), 15, 512) ?>,
            chart: { type: 'pie', height: 300, fontFamily: 'inherit' },
            labels: <?php echo json_encode(array_keys($durumData->toArray()), 15, 512) ?>,
            colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
            legend: { position: 'bottom' }
        });
        window.sikayetDurumChart.render();

        window.sikayetKategoriChart = new ApexCharts(document.querySelector("#sikayetKategoriChart"), {
            series: [{ name: 'Şikayet Sayısı', data: <?php echo json_encode(array_values($kategoriData->toArray()), 15, 512) ?> }],
            chart: { type: 'bar', height: 300, toolbar: {show: false}, fontFamily: 'inherit' },
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            xaxis: { categories: <?php echo json_encode(array_keys($kategoriData->toArray()), 15, 512) ?> },
            colors: ['#10B981']
        });
        window.sikayetKategoriChart.render();

        window.sikayetTakimChart = new ApexCharts(document.querySelector("#sikayetTakimChart"), {
            series: [{ name: 'Şikayet Sayısı', data: <?php echo json_encode(array_values($takimData->toArray()), 15, 512) ?> }],
            chart: { type: 'bar', height: 300, toolbar: {show: false}, fontFamily: 'inherit' },
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            xaxis: { categories: <?php echo json_encode(array_keys($takimData->toArray()), 15, 512) ?> },
            colors: ['#3B82F6']
        });
        window.sikayetTakimChart.render();

        window.sikayetTrendChart = new ApexCharts(document.querySelector("#sikayetTrendChart"), {
            series: [{ name: 'Gelen Şikayet', data: <?php echo json_encode(array_values($aylikTrend->toArray()), 15, 512) ?> }],
            chart: { type: 'area', height: 300, toolbar: {show: false}, fontFamily: 'inherit' },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: { categories: <?php echo json_encode(array_keys($aylikTrend->toArray()), 15, 512) ?> },
            colors: ['#F59E0B'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05 } }
        });
        window.sikayetTrendChart.render();

        // --- 2. DURUM KARTLARI DETAY GRAFİKLERİ (DONUT) ---
        const donutOptions = (data, colors) => ({
            series: Object.values(data),
            chart: { type: 'donut', height: 250, fontFamily: 'inherit' },
            labels: Object.keys(data),
            colors: colors,
            legend: { position: 'bottom', fontSize: '10px' },
            dataLabels: { enabled: false },
            plotOptions: { pie: { donut: { size: '65%' } } }
        });

        window.cozulenChart = new ApexCharts(document.querySelector("#cozulenChart"), donutOptions(<?php echo json_encode($cozulenChartData->toArray(), 15, 512) ?>, ['#10B981', '#3B82F6', '#F59E0B', '#EF4444']));
        window.cozulenChart.render();

        window.islemdeChart = new ApexCharts(document.querySelector("#islemdeChart"), donutOptions(<?php echo json_encode($islemdeChartData->toArray(), 15, 512) ?>, ['#3B82F6', '#10B981', '#F59E0B', '#EF4444']));
        window.islemdeChart.render();

        window.yeniChart = new ApexCharts(document.querySelector("#yeniChart"), donutOptions(<?php echo json_encode($yeniChartData->toArray(), 15, 512) ?>, ['#F59E0B', '#10B981', '#3B82F6', '#EF4444']));
        window.yeniChart.render();

        window.projeyeDonusenChart = new ApexCharts(document.querySelector("#projeyeDonusenChart"), donutOptions(<?php echo json_encode($projeyeDonusenChartData->toArray(), 15, 512) ?>, ['#8B5CF6', '#10B981', '#3B82F6', '#EF4444']));
        window.projeyeDonusenChart.render();

        // --- 3. ANALİZ GRAFİKLERİ ---
        window.bolumKategoriChart = new ApexCharts(document.querySelector("#bolumKategoriChart"), {
            series: <?php echo json_encode($bolumKategoriSeries, 15, 512) ?>,
            chart: { type: 'bar', height: 350, stacked: true, toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { horizontal: false, borderRadius: 4 } },
            xaxis: { 
                categories: <?php echo json_encode($takimlar, 15, 512) ?>,
                labels: { show: false }
            },
            legend: { position: 'bottom', fontSize: '11px' },
            fill: { opacity: 1 }
        });
        window.bolumKategoriChart.render();

        window.altKategoriChart = new ApexCharts(document.querySelector("#altKategoriChart"), {
            series: [{ name: 'Şikayet Sayısı', data: <?php echo json_encode($altKategoriData, 15, 512) ?> }],
            chart: { height: 350, type: 'treemap', toolbar: { show: false }, fontFamily: 'inherit' },
            colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
            plotOptions: { treemap: { distributed: true, enableShades: true } }
        });
        window.altKategoriChart.render();

        // --- 4. MEMNUNİYET VE İADE GRAFİKLERİ ---
        window.customerFeedbackChart = new ApexCharts(document.querySelector("#customerFeedbackChart"), {
            series: [<?php echo e($feedbackCounts['Onaylandı'] ?? 0); ?>, <?php echo e($feedbackCounts['Reddedildi'] ?? 0); ?>, <?php echo e($feedbackCounts['Revizyon İstendi'] ?? 0); ?>],
            chart: { type: 'donut', height: 300, fontFamily: 'inherit' },
            labels: ['Onaylandı', 'Reddedildi', 'Revizyon'],
            colors: ['#10B981', '#EF4444', '#F59E0B'],
            legend: { position: 'bottom' }
        });
        window.customerFeedbackChart.render();

        window.deptSatisfactionChart = new ApexCharts(document.querySelector("#deptSatisfactionChart"), {
            series: [{ name: 'Onaylandı', data: <?php echo json_encode($bolumMemnuniyeti->pluck('onay_sayisi'), 15, 512) ?> }, { name: 'Reddedildi', data: <?php echo json_encode($bolumMemnuniyeti->pluck('red_sayisi'), 15, 512) ?> }, { name: 'Revizyon', data: <?php echo json_encode($bolumMemnuniyeti->pluck('revizyon_sayisi'), 15, 512) ?> }],
            chart: { type: 'bar', height: 300, stacked: true, fontFamily: 'inherit', toolbar: {show: false} },
            xaxis: { categories: <?php echo json_encode($bolumMemnuniyeti->pluck('bolum_adi'), 15, 512) ?> },
            colors: ['#10B981', '#EF4444', '#F59E0B']
        });
        window.deptSatisfactionChart.render();

        window.iadeliOranChart = new ApexCharts(document.querySelector("#iadeliOranChart"), {
            series: [<?php echo e($iadeliSikayetSayisi ?? 0); ?>, <?php echo e($iadesizSikayetSayisi ?? 0); ?>],
            chart: { type: 'donut', height: 220, fontFamily: 'inherit' },
            labels: ['İadesi Var', 'İadesi Yok'],
            colors: ['#EF4444', '#E5E7EB'],
            legend: { position: 'bottom' },
            plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Toplam' } } } } }
        });
        window.iadeliOranChart.render();

        window.renderBolumIadeCharts = function(dataSets) {
            const container = document.querySelector("#bolumIadeChartsContainer");
            if(!container) return;
            container.innerHTML = '';
            if(!dataSets) return;
            Object.keys(dataSets).forEach(unit => {
                const chartData = dataSets[unit];
                const wrapper = document.createElement('div');
                wrapper.className = 'border-b border-gray-50 pb-2 last:border-0';
                const title = document.createElement('h5');
                title.className = 'text-[10px] font-bold text-gray-400 uppercase mb-1';
                title.innerText = unit + ' Bazında';
                wrapper.appendChild(title);
                const chartEl = document.createElement('div');
                wrapper.appendChild(chartEl);
                container.appendChild(wrapper);
                new ApexCharts(chartEl, {
                    series: [{ name: 'Miktar', data: chartData.series }],
                    chart: { type: 'bar', height: 120, toolbar: {show: false}, fontFamily: 'inherit' },
                    plotOptions: { bar: { horizontal: true, barHeight: '60%' } },
                    xaxis: { categories: chartData.labels, labels: { show: false } },
                    colors: ['#EF4444']
                }).render();
            });
        };
        window.renderBolumIadeCharts(<?php echo json_encode($bolumIadeChartData ?? [], 15, 512) ?>);

        window.bolumIadeCountChart = new ApexCharts(document.querySelector("#bolumIadeCountChart"), {
            series: <?php echo json_encode($bolumIadeSayilariSeries ?? [], 15, 512) ?>,
            chart: { type: 'bar', height: 250, stacked: true, toolbar: {show: false}, fontFamily: 'inherit' },
            xaxis: { categories: <?php echo json_encode($bolumIadeSayilariLabels ?? [], 15, 512) ?> },
            colors: ['#EF4444', '#E5E7EB']
        });
        window.bolumIadeCountChart.render();

        // --- 5. ZİYARET GRAFİKLERİ ---
        window.visitRateChart = new ApexCharts(document.querySelector("#visitRateChart"), {
            series: [<?php echo e($visitStats['visited_count'] ?? 0); ?>, <?php echo e($visitStats['non_visited_count'] ?? 0); ?>],
            chart: { type: 'donut', height: 250, fontFamily: 'inherit' },
            labels: ['Ziyaret Edildi', 'Ziyaret Edilmedi'],
            colors: ['#3B82F6', '#F3F4F6']
        });
        window.visitRateChart.render();

        window.visitReasonChart = new ApexCharts(document.querySelector("#visitReasonChart"), {
            series: <?php echo json_encode(array_values($visitStats['reason_distribution'] ?? []), 15, 512) ?>,
            chart: { type: 'pie', height: 250, fontFamily: 'inherit' },
            labels: <?php echo json_encode(array_keys($visitStats['reason_distribution'] ?? []), 15, 512) ?>
        });
        window.visitReasonChart.render();

        window.deptVisitChart = new ApexCharts(document.querySelector("#deptVisitChart"), {
            series: [{ name: 'Ziyaret', data: <?php echo json_encode(array_values($visitStats['dept_visit_rates'] ?? []), 15, 512) ?> }],
            chart: { type: 'bar', height: 250, toolbar: {show: false}, fontFamily: 'inherit' },
            xaxis: { categories: <?php echo json_encode(array_keys($visitStats['dept_visit_rates'] ?? []), 15, 512) ?> },
            colors: ['#8B5CF6']
        });
        window.deptVisitChart.render();

        // --- 6. BİRLEŞİK TREND ANALİZİ ---
        window.combinedTrendChart = new ApexCharts(document.querySelector("#combinedComplaintTrendChart"), {
            series: <?php echo json_encode($combinedTrend['datasets'] ?? [], 15, 512) ?>,
            chart: { height: 350, type: 'area', toolbar: { show: false }, fontFamily: 'inherit' },
            colors: ['#3B82F6', '#10B981'],
            stroke: { curve: 'smooth', width: [3, 3] },
            xaxis: { categories: <?php echo json_encode($combinedTrend['labels'] ?? [], 15, 512) ?> }
        });
        window.combinedTrendChart.render();
    });

    // --- 7. LIVEWIRE GÜNCELLEMELERİ (Bileşen dışı olayları dinle) ---
    window.addEventListener('updateSikayetRaporlari', event => {
        const data = event.detail[0];
        
        // --- 1. ANA KPI GRAFİKLERİ GÜNCELLE ---
        if(data.durumData && window.sikayetDurumChart) {
            window.sikayetDurumChart.updateOptions({
                series: Object.values(data.durumData),
                labels: Object.keys(data.durumData)
            });
        }
        if(data.kategoriData && window.sikayetKategoriChart) {
            window.sikayetKategoriChart.updateOptions({
                series: [{ data: Object.values(data.kategoriData) }],
                xaxis: { categories: Object.keys(data.kategoriData) }
            });
        }
        if(data.takimData && window.sikayetTakimChart) {
            window.sikayetTakimChart.updateOptions({
                series: [{ data: Object.values(data.takimData) }],
                xaxis: { categories: Object.keys(data.takimData) }
            });
        }

        // --- 2. ALT DONUT GRAFİKLERİ GÜNCELLE ---
        const prepareDonut = (d) => ({ series: Object.values(d), labels: Object.keys(d) });
        if(data.cozulenChartData && window.cozulenChart) window.cozulenChart.updateOptions(prepareDonut(data.cozulenChartData));
        if(data.islemdeChartData && window.islemdeChart) window.islemdeChart.updateOptions(prepareDonut(data.islemdeChartData));
        if(data.yeniChartData && window.yeniChart) window.yeniChart.updateOptions(prepareDonut(data.yeniChartData));
        if(data.projeyeDonusenChartData && window.projeyeDonusenChart) window.projeyeDonusenChart.updateOptions(prepareDonut(data.projeyeDonusenChartData));

        // --- 3. ANALİZ GRAFİKLERİ GÜNCELLE ---
        if(data.bolumKategoriSeries && window.bolumKategoriChart) {
            window.bolumKategoriChart.updateOptions({
                series: data.bolumKategoriSeries,
                xaxis: { categories: data.bolumKategoriXaxis || [] }
            });
        }
        if(data.altKategoriSeries && window.altKategoriChart) {
            window.altKategoriChart.updateSeries(data.altKategoriSeries);
        }

        // --- 4. MEMNUNİYET VE İADE GRAFİKLERİ GÜNCELLE ---
        if(data.feedbackCounts && window.customerFeedbackChart) {
            window.customerFeedbackChart.updateSeries([
                data.feedbackCounts['Onaylandı'] || 0,
                data.feedbackCounts['Reddedildi'] || 0,
                data.feedbackCounts['Revizyon İstendi'] || 0
            ]);
        }
        if(data.bolumMemnuniyeti && window.deptSatisfactionChart) {
            const bData = Object.values(data.bolumMemnuniyeti);
            window.deptSatisfactionChart.updateOptions({
                xaxis: { categories: bData.map(i => i.bolum_adi) },
                series: [
                    { name: 'Onaylandı', data: bData.map(i => i.onay_sayisi) },
                    { name: 'Reddedildi', data: bData.map(i => i.red_sayisi) },
                    { name: 'Revizyon', data: bData.map(i => i.revizyon_sayisi) }
                ]
            });
        }
        if(window.iadeliOranChart && (data.iadeliSikayetSayisi !== undefined)) {
            window.iadeliOranChart.updateSeries([data.iadeliSikayetSayisi, data.iadesizSikayetSayisi]);
        }
        if(window.renderBolumIadeCharts && data.bolumIadeChartData) {
            window.renderBolumIadeCharts(data.bolumIadeChartData);
        }
        if(window.bolumIadeCountChart && data.bolumIadeSayilariSeries) {
            window.bolumIadeCountChart.updateOptions({
                xaxis: { categories: data.bolumIadeSayilariLabels || [] },
                series: data.bolumIadeSayilariSeries
            });
        }

        // --- 5. ZİYARET VE TREND GRAFİKLERİ GÜNCELLE ---
        if(data.visitStats && window.visitRateChart) {
            window.visitRateChart.updateSeries([data.visitStats.visited_count, data.visitStats.non_visited_count]);
            window.visitReasonChart.updateOptions({ series: Object.values(data.visitStats.reason_distribution), labels: Object.keys(data.visitStats.reason_distribution) });
            window.deptVisitChart.updateOptions({ series: [{ data: Object.values(data.visitStats.dept_visit_rates) }], xaxis: { categories: Object.keys(data.visitStats.dept_visit_rates) } });
        }
        if(data.combinedTrend && window.combinedTrendChart) {
            window.combinedTrendChart.updateOptions({ series: data.combinedTrend.datasets, xaxis: { categories: data.combinedTrend.labels } });
        }
    });
</script>
</div>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/admin/musteri-sikayet-raporu.blade.php ENDPATH**/ ?>