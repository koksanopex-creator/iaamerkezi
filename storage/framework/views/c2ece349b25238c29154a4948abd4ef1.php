 <?php $__env->slot('header', null, []); ?> 
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Şikayet Analiz Raporu')); ?>

    </h2>
 <?php $__env->endSlot(); ?>

<div class="space-y-6" wire:key="analiz-raporu">

    <style>
        @media print {
            .print-hidden { display: none !important; }
            .print-full-width { width: 100% !important; max-width: 100% !important; }
            body { background-color: white !important; }
        }
    </style>

    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-gray-100 print-hidden">
        <div>
            <h2 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Şikayet Analiz Raporu
            </h2>
            <div class="mt-2 flex items-center gap-2">
                <?php
                    $authResult = \App\Models\ReportRoleAuthorization::getAuthorizationForUser(auth()->user(), 'analiz_raporu');
                    $scopeLabels = \App\Models\ReportRoleAuthorization::DATA_SCOPE_OPTIONS;
                    $scope = $authResult ? $authResult['data_scope'] : 'all';
                    $allowedIds = \App\Models\ReportRoleAuthorization::getAllowedBolumIdsForUser(auth()->user(), 'analiz_raporu');
                    
                    $scopeText = $scopeLabels[$scope] ?? 'Belirlenmedi';
                    if ($allowedIds !== '*' && is_array($allowedIds) && count($allowedIds) > 0) {
                        $bolumNames = \App\Models\Bolum::whereIn('id', $allowedIds)->pluck('ad')->toArray();
                        if (!empty($bolumNames)) {
                            $scopeText .= ' (' . implode(', ', $bolumNames) . ')';
                        }
                    }
                ?>
                <!--[if BLOCK]><![endif]--><?php if(!auth()->user()->hasRole('Superadmin') && !auth()->user()->hasRole('Yonetim') && !auth()->user()->hasRole('Yönetim')): ?>
                    <span class="px-2 py-1 bg-amber-100 text-amber-700 font-bold text-[10px] rounded uppercase tracking-wider border border-amber-200" title="Sadece bu kapsamdaki verileri görebilirsiniz">
                        🔍 Veri Kapsamı: <?php echo e($scopeText); ?>

                    </span>
                <?php else: ?>
                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 font-bold text-[10px] rounded uppercase tracking-wider border border-indigo-200" title="Tüm verileri görebilirsiniz">
                        🔍 Veri Kapsamı: Tüm Veriler (Yönetici)
                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <p class="text-sm text-gray-500 font-medium mt-1">Müşteri şikayetlerini detaylı filtreleyin ve grafiksel olarak analiz edin.</p>
        </div>
        <div class="flex gap-2">
            <!--[if BLOCK]><![endif]--><?php if($isSuperadmin): ?>
            <button wire:click="openYetkiModal" class="px-4 py-2 bg-amber-50 text-amber-700 font-bold text-xs rounded-xl hover:bg-amber-100 transition shadow-sm border border-amber-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Yetki Matrisi
            </button>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if(\App\Models\ReportRoleAuthorization::getAuthorizationForUser(auth()->user(), 'karsilastirma_raporu')): ?>
            <a href="<?php echo e(route('admin.musteri-sikayet-karsilastirma')); ?>" class="px-4 py-2 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-xl hover:bg-indigo-100 transition shadow-sm border border-indigo-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Dinamik Karşılaştırma Modülü
            </a>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <button onclick="window.print()" class="px-4 py-2 bg-red-50 text-red-700 font-bold text-xs rounded-xl hover:bg-red-100 transition shadow-sm border border-red-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                PDF / Çıktı Al
            </button>
        </div>
    </div>

    
    <div class="bg-gradient-to-r from-indigo-50/50 via-white to-white rounded-2xl shadow-sm border border-indigo-100 print-hidden p-3 flex flex-col xl:flex-row items-start xl:items-center gap-4 mb-2">
        <div class="flex items-center gap-2 text-indigo-600 bg-white px-3 py-1.5 rounded-xl border border-indigo-100 shadow-sm relative overflow-hidden flex-shrink-0">
            <div class="absolute inset-0 bg-indigo-50 opacity-50"></div>
            <svg class="w-4 h-4 animate-bounce relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span class="text-[10px] font-black uppercase tracking-widest relative z-10">Hızlı Erişim</span>
        </div>
        <div class="w-px h-6 bg-indigo-100 hidden xl:block"></div>
        <div class="flex gap-2 overflow-x-auto custom-scrollbar w-full pb-1">
            <?php
                $navLinks = [
                    'kpi-kartlari' => ['label' => 'KPI Özet', 'dot' => 'bg-slate-500', 'hover' => 'hover:bg-slate-50 hover:text-slate-700 hover:border-slate-300'],
                    'detay-tablosu' => ['label' => 'Detay Listesi', 'dot' => 'bg-slate-500', 'hover' => 'hover:bg-slate-50 hover:text-slate-700 hover:border-slate-300'],
                    'chart-opex-pareto' => ['label' => 'Pareto Analizi', 'dot' => 'bg-red-500', 'hover' => 'hover:bg-red-50 hover:text-red-600 hover:border-red-300'],
                    'chart-opex-darbogaz' => ['label' => 'Süreç Darboğazı', 'dot' => 'bg-orange-500', 'hover' => 'hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300'],
                    'chart-opex-heatmap' => ['label' => 'Yangın Haritası', 'dot' => 'bg-purple-500', 'hover' => 'hover:bg-purple-50 hover:text-purple-600 hover:border-purple-300'],
                    'chart-aylik-trend' => ['label' => 'Aylık Trend', 'dot' => 'bg-indigo-500', 'hover' => 'hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300'],
                    'chart-durum-dagilimi' => ['label' => 'Durum Dağılımı', 'dot' => 'bg-emerald-500', 'hover' => 'hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300'],
                    'chart-bolum-dagilimi' => ['label' => 'Bölüm Bazlı', 'dot' => 'bg-blue-500', 'hover' => 'hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300'],
                    'chart-kategori-dagilimi' => ['label' => 'Kategori Analizi', 'dot' => 'bg-amber-500', 'hover' => 'hover:bg-amber-50 hover:text-amber-600 hover:border-amber-300'],
                    'chart-alt-kategori-dagilimi' => ['label' => 'Alt Kategoriler', 'dot' => 'bg-cyan-500', 'hover' => 'hover:bg-cyan-50 hover:text-cyan-600 hover:border-cyan-300'],
                    'chart-oncelik-dagilimi' => ['label' => 'Öncelikler', 'dot' => 'bg-rose-500', 'hover' => 'hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300'],
                    'chart-konum-tipi' => ['label' => 'Konum Tipi', 'dot' => 'bg-teal-500', 'hover' => 'hover:bg-teal-50 hover:text-teal-600 hover:border-teal-300'],
                    'chart-musteri-top10' => ['label' => 'Top 10 Müşteri', 'dot' => 'bg-orange-500', 'hover' => 'hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300'],
                    'chart-cozum-suresi-trendi' => ['label' => 'Çözüm Süresi', 'dot' => 'bg-violet-500', 'hover' => 'hover:bg-violet-50 hover:text-violet-600 hover:border-violet-300'],
                    'chart-squad-dagilimi' => ['label' => 'Çözüm Takımı', 'dot' => 'bg-fuchsia-500', 'hover' => 'hover:bg-fuchsia-50 hover:text-fuchsia-600 hover:border-fuchsia-300']
                ];
            ?>
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $navLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="#<?php echo e($id); ?>" class="flex-shrink-0 flex items-center px-3 py-1.5 text-[10.5px] font-bold text-slate-500 bg-white border border-slate-200 rounded-lg <?php echo e($link['hover']); ?> transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full <?php echo e($link['dot']); ?> mr-1.5"></span>
                    <?php echo e($link['label']); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 print-hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filtreler
            </h3>
            <button wire:click="clearFilters" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Temizle
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-6">
            
            
            <div class="col-span-full border-b border-gray-100 pb-2">
                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-1 rounded">Tarih Filtreleri</span>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tarih Kriteri</label>
                <select wire:model.live="tarihAlani" class="w-full text-xs border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 py-2">
                    <option value="created_at">Sisteme Giriş Tarihi</option>
                    <option value="musteri_sikayet_tarihi">Bildirim Tarihi</option>
                    <option value="musteri_cozum_son_tarihi">Son Çözüm Tarihi</option>
                </select>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Başlangıç</label>
                <input type="date" wire:model.live="startDate" class="w-full text-xs border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 py-2">
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Bitiş</label>
                <input type="date" wire:model.live="endDate" class="w-full text-xs border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 py-2">
            </div>

            
            <div class="hidden lg:block"></div>

            
            <div class="col-span-full border-b border-gray-100 pb-2 mt-2">
                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-1 rounded">Şikayet Detayları</span>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Bölüm</label>
                <div x-data="{ open: false }" class="relative w-full">
                    <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="truncate"><?php echo e(count($bolumId) > 0 ? count($bolumId) . ' Seçili' : '-- Tüm Bölümler --'); ?></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['bolumler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label wire:key="filter-bolum-<?php echo e($bolum->id); ?>" class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" wire:model.live="bolumId" value="<?php echo e($bolum->id); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700"><?php echo e($bolum->ad); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Alt Kategori</label>
                <div x-data="{ open: false }" class="relative w-full">
                    <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="truncate"><?php echo e(count($altKategoriId) > 0 ? count($altKategoriId) . ' Seçili' : '-- Tüm Alt Kategoriler --'); ?></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['altKategoriler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label wire:key="filter-altkat-<?php echo e($ak['id']); ?>" class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" wire:model.live="altKategoriId" value="<?php echo e($ak['id']); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700"><?php echo e($ak['ad']); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Durum</label>
                <div x-data="{ open: false }" class="relative w-full">
                    <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="truncate"><?php echo e(count($durum) > 0 ? count($durum) . ' Seçili' : '-- Tüm Durumlar --'); ?></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['durumlar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label wire:key="filter-durum-<?php echo e($key); ?>" class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" wire:model.live="durum" value="<?php echo e($key); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700"><?php echo e($label); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Öncelik</label>
                <div x-data="{ open: false }" class="relative w-full">
                    <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="truncate"><?php echo e(count($oncelik) > 0 ? count($oncelik) . ' Seçili' : '-- Tüm Öncelikler --'); ?></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['oncelikler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label wire:key="filter-oncelik-<?php echo e($o); ?>" class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" wire:model.live="oncelik" value="<?php echo e($o); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700"><?php echo e($o); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Çözüm Takımı</label>
                <div x-data="{ open: false }" class="relative w-full">
                    <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="truncate"><?php echo e(count($takimId) > 0 ? count($takimId) . ' Seçili' : '-- Tüm Takımlar --'); ?></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['takimlar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tkm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label wire:key="filter-takim-<?php echo e($tkm->id); ?>" class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" wire:model.live="takimId" value="<?php echo e($tkm->id); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700"><?php echo e($tkm->ad); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Görevli Personel</label>
                <div x-data="{ open: false }" class="relative w-full">
                    <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="truncate"><?php echo e(count($squadUserId) > 0 ? count($squadUserId) . ' Seçili' : '-- Tüm Personeller --'); ?></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['squadUsers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label wire:key="filter-squad-<?php echo e($sq->id); ?>" class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" wire:model.live="squadUserId" value="<?php echo e($sq->id); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700"><?php echo e($sq->name); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>

            
            <div class="col-span-full border-b border-gray-100 pb-2 mt-2">
                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-1 rounded">Müşteri & Konum</span>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Müşteri (Firma)</label>
                <div x-data="{ open: false }" class="relative w-full">
                    <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="truncate"><?php echo e(count($customerId) > 0 ? count($customerId) . ' Seçili' : '-- Tüm Müşteriler --'); ?></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['musteriler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label wire:key="filter-musteri-<?php echo e($m->id); ?>" class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" wire:model.live="customerId" value="<?php echo e($m->id); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700"><?php echo e($m->name); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>

            
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Konum</label>
                <div x-data="{ open: false }" class="relative w-full">
                    <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="truncate"><?php echo e(count($konumTipi) > 0 ? count($konumTipi) . ' Seçili' : '-- Tümü --'); ?></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['konumTipleri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label wire:key="filter-konum-<?php echo e($kt); ?>" class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" wire:model.live="konumTipi" value="<?php echo e($kt); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700"><?php echo e($kt); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-gradient-to-r from-indigo-50 to-white border-l-4 border-indigo-500 rounded-r-2xl p-4 shadow-sm print-hidden mb-6 mt-4">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl shadow-md flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="flex-1 text-indigo-900 leading-relaxed">
                <?php echo $activeFilterInfo; ?>

            </div>
        </div>
    </div>

    
    <div id="kpi-kartlari" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Toplam</p>
                    <p class="text-xl font-black text-gray-900"><?php echo e(number_format($kpi['toplam'])); ?></p>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-orange-100 p-4 hover:shadow-md transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-orange-500 uppercase tracking-wider">Açık</p>
                    <p class="text-xl font-black text-gray-900"><?php echo e(number_format($kpi['acik'])); ?></p>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-4 hover:shadow-md transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Çözülen</p>
                    <p class="text-xl font-black text-gray-900"><?php echo e(number_format($kpi['cozulen'])); ?></p>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-rose-100 p-4 hover:shadow-md transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.27 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Gecikmiş</p>
                    <p class="text-xl font-black text-gray-900"><?php echo e(number_format($kpi['gecikmis'])); ?></p>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-4 hover:shadow-md transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">Ort. Çözüm</p>
                    <p class="text-xl font-black text-gray-900"><?php echo e($kpi['ortCozumSuresi']); ?> <span class="text-xs font-medium text-gray-400">gün</span></p>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-purple-100 p-4 hover:shadow-md transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-purple-500 uppercase tracking-wider">İade Oranı</p>
                    <p class="text-xl font-black text-gray-900">%<?php echo e($kpi['iadeOrani']); ?></p>
                </div>
            </div>
        </div>
    </div>

    
    <div id="detay-tablosu" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden no-break mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <span class="w-1.5 h-4 bg-slate-500 rounded-full"></span>
                Şikayet Detay Listesi
                <span class="text-[10px] font-bold text-gray-400 normal-case">(<?php echo e($detayTablosu->total()); ?> kayıt)</span>
            </h4>
        </div>
        <div class="w-full">
            <table class="w-full divide-y divide-gray-200 text-[11px]">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-3 py-3 text-left font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">ID</th>
                        <th class="px-3 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Konu</th>
                        <th class="px-3 py-3 text-left font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Bölüm</th>
                        <th class="px-3 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Müşteri</th>
                        <th class="px-3 py-3 text-left font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"><?php echo e($tarihAlani == 'musteri_sikayet_tarihi' ? 'Bildirim' : ($tarihAlani == 'musteri_cozum_son_tarihi' ? 'Çözüm' : 'Giriş')); ?></th>
                        <th class="px-3 py-3 text-left font-bold text-gray-500 uppercase tracking-wider w-[15%]">Durum</th>
                        <th class="px-3 py-3 text-center font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Süre</th>
                        <th class="px-3 py-3 text-left font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Öncelik</th>
                        <th class="px-3 py-3 text-right font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">İşlem</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $detayTablosu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-3 py-3 font-bold text-indigo-600 whitespace-nowrap">#<?php echo e($sikayet->id); ?></td>
                            <td class="px-3 py-3">
                                <div class="max-w-[140px] xl:max-w-[180px] truncate" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>"><?php echo e($sikayet->musteri_sikayet_konusu); ?></div>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <!--[if BLOCK]><![endif]--><?php if($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum): ?>
                                    <div class="flex items-center gap-1.5">
                                        <!--[if BLOCK]><![endif]--><?php if($sikayet->sikayetKategori->bolum->logo_yolu): ?>
                                            <img src="<?php echo e(asset('storage/'.$sikayet->sikayetKategori->bolum->logo_yolu)); ?>" class="w-5 h-5 object-contain rounded border border-gray-100" alt="<?php echo e($sikayet->sikayetKategori->bolum->ad); ?>">
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 leading-tight"><?php echo e($sikayet->sikayetKategori->bolum->ad); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-1.5">
                                    <!--[if BLOCK]><![endif]--><?php if($sikayet->customer && $sikayet->customer->logo_path): ?>
                                        <img src="<?php echo e(asset('storage/'.$sikayet->customer->logo_path)); ?>" class="w-5 h-5 rounded-full object-cover border border-gray-200">
                                    <?php else: ?>
                                        <div class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-[9px] flex-shrink-0">
                                            <?php echo e(mb_substr($sikayet->customer ? $sikayet->customer->name : $sikayet->musteri_adi, 0, 1)); ?>

                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($sikayet->customer): ?>
                                        <a href="<?php echo e(route('musteri.profil.show', $sikayet->customer_id)); ?>" class="text-indigo-600 hover:text-indigo-800 font-medium max-w-[100px] xl:max-w-[140px] truncate block" title="<?php echo e($sikayet->customer->name); ?>">
                                            <?php echo e($sikayet->customer->name); ?>

                                        </a>
                                    <?php else: ?>
                                        <span class="max-w-[100px] xl:max-w-[140px] truncate block" title="<?php echo e($sikayet->musteri_adi); ?>"><?php echo e($sikayet->musteri_adi); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                            <td class="px-3 py-3 text-gray-500 whitespace-nowrap">
                                <?php
                                    $tarihVal = $tarihAlani == 'musteri_sikayet_tarihi' ? $sikayet->musteri_sikayet_tarihi :
                                                ($tarihAlani == 'musteri_cozum_son_tarihi' ? $sikayet->musteri_cozum_son_tarihi : $sikayet->created_at);
                                ?>
                                <?php echo e($tarihVal ? $tarihVal->format('d.m.y') : '-'); ?>

                            </td>
                            <td class="px-3 py-3 align-middle"><?php echo $sikayet->musteri_durum_badge_analiz; ?></td>
                            <td class="px-3 py-3 align-middle justify-center whitespace-nowrap"><?php echo $sikayet->musteri_sure_bilgisi; ?></td>
                            <td class="px-3 py-3 align-middle whitespace-nowrap">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold <?php echo e($sikayet->oncelik_badge_class); ?>"><?php echo e($sikayet->musteri_oncelik ?? '-'); ?></span>
                            </td>
                            <td class="px-3 py-3 text-right space-y-1">
                                <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" target="_blank" class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 rounded text-[9px] font-bold transition-colors w-full justify-center whitespace-nowrap">Detay</a>
                                <!--[if BLOCK]><![endif]--><?php if($sikayet->iaa_id): ?>
                                    <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaa_id)); ?>" target="_blank" class="inline-flex items-center px-2 py-1 bg-purple-50 text-purple-600 hover:bg-purple-100 hover:text-purple-700 rounded text-[9px] font-bold transition-colors w-full justify-center whitespace-nowrap">Proje</a>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400 text-sm">
                                Filtrelere uygun şikayet kaydı bulunamadı.
                            </td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
        <!--[if BLOCK]><![endif]--><?php if($detayTablosu->hasMorePages()): ?>
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-center print-hidden">
                <button wire:click="loadMore" wire:loading.attr="disabled" class="px-6 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:text-indigo-600 hover:border-indigo-200 shadow-sm transition-all flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="loadMore">
                        <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                    <span wire:loading wire:target="loadMore">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </span>
                    Devamını Göster
                </button>
                <!--[if BLOCK]><![endif]--><?php if($perPage > 5): ?>
                    <button wire:click="hideMore" wire:loading.attr="disabled" class="px-6 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:text-red-600 hover:border-red-200 shadow-sm transition-all flex items-center gap-2 disabled:opacity-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        Devamını Gizle
                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <div id="opex-analizleri" class="grid grid-cols-1 gap-6 mb-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2" title="Hangi %20'lik problem, %80'lik hataya sebep oluyor?">
                <span class="w-1.5 h-4 bg-red-500 rounded-full"></span>
                Pareto (80/20) Analizi
            </h4>
            <div id="chart-opex-pareto" class="min-h-[280px]" wire:ignore></div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-orange-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2" title="Şikayetler en çok hangi statüde bekliyor?">
                <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
                Süreç Darboğazı (Bottleneck)
            </h4>
            <div id="chart-opex-darbogaz" class="min-h-[280px]" wire:ignore></div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-purple-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2" title="Hangi bölüm, hangi kategoride daha çok hata üretiyor?">
                <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>
                Yangın Haritası (Heatmap)
            </h4>
            <div id="chart-opex-heatmap" class="min-h-[280px]" wire:ignore></div>
        </div>
    </div>

    
    <div id="genel-analizler" class="grid grid-cols-1 gap-6 mb-6">

    
    <div class="grid grid-cols-1 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span>
                Aylık Şikayet Trendi
            </h4>
            <div id="chart-aylik-trend" class="min-h-[280px]" wire:ignore></div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                Durum Dağılımı
            </h4>
            <div id="chart-durum-dagilimi" class="min-h-[280px]" wire:ignore></div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>
                Bölüm Bazlı Dağılım
            </h4>
            <div id="chart-bolum-dagilimi" class="min-h-[280px]" wire:ignore></div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>
                Kategori Bazlı Analiz
            </h4>
            <div id="chart-kategori-dagilimi" class="min-h-[280px]" wire:ignore></div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-cyan-500 rounded-full"></span>
                Alt Kategori Dağılımı
            </h4>
            <div id="chart-alt-kategori-dagilimi" class="min-h-[280px]" wire:ignore></div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-rose-500 rounded-full"></span>
                Öncelik Dağılımı
            </h4>
            <div id="chart-oncelik-dagilimi" class="min-h-[280px]" wire:ignore></div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-teal-500 rounded-full"></span>
                Konum Tipi (Yurt İçi / Yurt Dışı)
            </h4>
            <div id="chart-konum-tipi" class="min-h-[280px]" wire:ignore></div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
                En Çok Şikayet Alan Müşteriler (Top 10)
            </h4>
            <div id="chart-musteri-top10" class="min-h-[280px]" wire:ignore></div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-violet-500 rounded-full"></span>
                Ortalama Çözüm Süresi Trendi (Gün)
            </h4>
            <div id="chart-cozum-suresi-trend" class="min-h-[280px]" wire:ignore></div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative">
            <div class="absolute top-5 right-5 text-[9px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-100 hidden xl:block print-hidden max-w-[200px] truncate" title="<?php echo e($this->getActiveFilterText()); ?>"><?php echo e($this->getActiveFilterText()); ?></div>
            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-fuchsia-500 rounded-full"></span>
                Çözüm Takımı Dağılımı
            </h4>
            <div id="chart-squad-dagilimi" class="min-h-[280px]" wire:ignore></div>
        </div>
    </div>

</div>

    <?php
        $__scriptKey = '1402817794-0';
        ob_start();
    ?>
<script>
    // Grafik renk paleti
    window.iaaChartPalette = ['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#f97316','#14b8a6','#a855f7'];
    if (!window.iaaChartInstances) {
        window.iaaChartInstances = {};
    }

    window.destroyIaaChart = function(id) {
        if (window.iaaChartInstances[id]) {
            window.iaaChartInstances[id].destroy();
            delete window.iaaChartInstances[id];
        }
    };

    window.renderIaaCharts = function(data) {

        // ================= OPEX GRAFİKLERİ =================
        
        // OPEX: Pareto (80/20) Analizi
        window.destroyIaaChart('chart-opex-pareto');
        if (data.paretoAnalizi && data.paretoAnalizi.labels.length > 0) {
            window.iaaChartInstances['chart-opex-pareto'] = new ApexCharts(document.querySelector('#chart-opex-pareto'), {
                chart: { type: 'line', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [
                    { name: 'Şikayet Hacmi', type: 'column', data: data.paretoAnalizi.barSeries },
                    { name: 'Kümülatif Yüzde', type: 'line', data: data.paretoAnalizi.lineSeries }
                ],
                xaxis: { categories: data.paretoAnalizi.labels, labels: { style: { fontSize: '9px' }, trim: true, hideOverlappingLabels: true } },
                yaxis: [
                    { title: { text: 'Şikayet Sayısı', style: { fontSize: '10px' } } },
                    { opposite: true, title: { text: 'Kümülatif %', style: { fontSize: '10px' } }, max: 100 }
                ],
                colors: ['#ef4444', '#f97316'],
                stroke: { width: [0, 3], curve: 'smooth' },
                dataLabels: { enabled: false },
                legend: { position: 'bottom', fontSize: '10px' }
            });
            window.iaaChartInstances['chart-opex-pareto'].render();
        }

        // OPEX: Süreç Darboğazı Analizi
        window.destroyIaaChart('chart-opex-darbogaz');
        if (data.darBogazAnalizi && data.darBogazAnalizi.labels.length > 0) {
            window.iaaChartInstances['chart-opex-darbogaz'] = new ApexCharts(document.querySelector('#chart-opex-darbogaz'), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Ortalama Bekleme (Gün)', data: data.darBogazAnalizi.series }],
                xaxis: { categories: data.darBogazAnalizi.labels, labels: { style: { fontSize: '10px' } } },
                plotOptions: { bar: { horizontal: true, borderRadius: 4, distributed: true } },
                colors: ['#f97316', '#ef4444', '#f59e0b', '#10b981', '#6366f1'],
                dataLabels: { enabled: true, formatter: function (val) { return val + " Gün"; }, style: { fontSize: '11px', fontWeight: 700 } },
                legend: { show: false },
                grid: { borderColor: '#f1f5f9' }
            });
            window.iaaChartInstances['chart-opex-darbogaz'].render();
        }

        // OPEX: Isı Haritası (Heatmap)
        window.destroyIaaChart('chart-opex-heatmap');
        if (data.bolumKategoriHeatmap && data.bolumKategoriHeatmap.series.length > 0) {
            window.iaaChartInstances['chart-opex-heatmap'] = new ApexCharts(document.querySelector('#chart-opex-heatmap'), {
                chart: { type: 'heatmap', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: data.bolumKategoriHeatmap.series,
                xaxis: { categories: data.bolumKategoriHeatmap.categories, labels: { style: { fontSize: '10px' } } },
                plotOptions: {
                    heatmap: {
                        shadeIntensity: 0.5,
                        colorScale: {
                            ranges: [
                                { from: 0, to: 0, color: '#f8fafc', name: 'Yok' },
                                { from: 1, to: 5, color: '#fecaca', name: 'Düşük' },
                                { from: 6, to: 15, color: '#f87171', name: 'Orta' },
                                { from: 16, to: 1000, color: '#dc2626', name: 'Yüksek' }
                            ]
                        }
                    }
                },
                dataLabels: { enabled: true, style: { colors: ['#000'] } },
                legend: { position: 'bottom', fontSize: '10px' }
            });
            window.iaaChartInstances['chart-opex-heatmap'].render();
        }

        // ================= STANDART GRAFİKLER =================

        // 1. Aylık Trend
        window.destroyIaaChart('chart-aylik-trend');
        if (data.aylikTrend && data.aylikTrend.labels.length > 0) {
            window.iaaChartInstances['chart-aylik-trend'] = new ApexCharts(document.querySelector('#chart-aylik-trend'), {
                chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [
                    { name: 'Gelen Şikayet', data: data.aylikTrend.gelen },
                    { name: 'Çözülen Şikayet', data: data.aylikTrend.cozulen }
                ],
                xaxis: { categories: data.aylikTrend.labels, labels: { style: { fontSize: '10px' } } },
                colors: ['#6366f1', '#10b981'],
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
                dataLabels: { enabled: false },
                legend: { position: 'top', fontSize: '11px' },
                grid: { borderColor: '#f1f5f9' }
            });
            window.iaaChartInstances['chart-aylik-trend'].render();
        }

        // 2. Durum Dağılımı
        window.destroyIaaChart('chart-durum-dagilimi');
        if (data.durumDagilimi && data.durumDagilimi.series.length > 0) {
            window.iaaChartInstances['chart-durum-dagilimi'] = new ApexCharts(document.querySelector('#chart-durum-dagilimi'), {
                chart: { type: 'donut', height: 280, fontFamily: 'Inter, sans-serif' },
                series: data.durumDagilimi.series,
                labels: data.durumDagilimi.labels,
                colors: window.iaaChartPalette,
                legend: { position: 'bottom', fontSize: '11px' },
                dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 700 } },
                plotOptions: { pie: { donut: { size: '55%', labels: { show: true, total: { show: true, label: 'Toplam', fontSize: '13px', fontWeight: 900 } } } } }
            });
            window.iaaChartInstances['chart-durum-dagilimi'].render();
        }

        // 3. Bölüm Bazlı
        window.destroyIaaChart('chart-bolum-dagilimi');
        if (data.bolumDagilimi && data.bolumDagilimi.labels.length > 0) {
            window.iaaChartInstances['chart-bolum-dagilimi'] = new ApexCharts(document.querySelector('#chart-bolum-dagilimi'), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Şikayet Sayısı', data: data.bolumDagilimi.series }],
                xaxis: { categories: data.bolumDagilimi.labels, labels: { style: { fontSize: '10px' } } },
                plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
                colors: ['#6366f1'],
                dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 700 } },
                grid: { borderColor: '#f1f5f9' }
            });
            window.iaaChartInstances['chart-bolum-dagilimi'].render();
        }

        // 4. Kategori Bazlı
        window.destroyIaaChart('chart-kategori-dagilimi');
        if (data.kategoriDagilimi && data.kategoriDagilimi.labels.length > 0) {
            window.iaaChartInstances['chart-kategori-dagilimi'] = new ApexCharts(document.querySelector('#chart-kategori-dagilimi'), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Şikayet Sayısı', data: data.kategoriDagilimi.series }],
                xaxis: { categories: data.kategoriDagilimi.labels, labels: { style: { fontSize: '10px' } } },
                plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
                colors: ['#f59e0b'],
                dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 700 } },
                grid: { borderColor: '#f1f5f9' }
            });
            window.iaaChartInstances['chart-kategori-dagilimi'].render();
        }

        // 5. Alt Kategori
        window.destroyIaaChart('chart-alt-kategori-dagilimi');
        if (data.altKategoriDagilimi && data.altKategoriDagilimi.labels.length > 0) {
            window.iaaChartInstances['chart-alt-kategori-dagilimi'] = new ApexCharts(document.querySelector('#chart-alt-kategori-dagilimi'), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Şikayet Sayısı', data: data.altKategoriDagilimi.series }],
                xaxis: { categories: data.altKategoriDagilimi.labels, labels: { style: { fontSize: '9px' }, trim: true, maxHeight: 100 } },
                plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '65%' } },
                colors: ['#06b6d4'],
                dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700 } },
                grid: { borderColor: '#f1f5f9' },
                tooltip: { y: { formatter: function(v) { return v + ' şikayet'; } } }
            });
            window.iaaChartInstances['chart-alt-kategori-dagilimi'].render();
        }

        // 6. Öncelik Dağılımı
        window.destroyIaaChart('chart-oncelik-dagilimi');
        if (data.oncelikDagilimi && data.oncelikDagilimi.series.length > 0) {
            window.iaaChartInstances['chart-oncelik-dagilimi'] = new ApexCharts(document.querySelector('#chart-oncelik-dagilimi'), {
                chart: { type: 'pie', height: 280, fontFamily: 'Inter, sans-serif' },
                series: data.oncelikDagilimi.series,
                labels: data.oncelikDagilimi.labels,
                colors: ['#ef4444', '#f59e0b', '#3b82f6', '#9ca3af'],
                legend: { position: 'bottom', fontSize: '11px' },
                dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } }
            });
            window.iaaChartInstances['chart-oncelik-dagilimi'].render();
        }

        // 7. Konum Tipi
        window.destroyIaaChart('chart-konum-tipi');
        if (data.konumTipiDagilimi && data.konumTipiDagilimi.series.length > 0) {
            window.iaaChartInstances['chart-konum-tipi'] = new ApexCharts(document.querySelector('#chart-konum-tipi'), {
                chart: { type: 'pie', height: 280, fontFamily: 'Inter, sans-serif' },
                series: data.konumTipiDagilimi.series,
                labels: data.konumTipiDagilimi.labels,
                colors: ['#14b8a6', '#8b5cf6'],
                legend: { position: 'bottom', fontSize: '11px' },
                dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } }
            });
            window.iaaChartInstances['chart-konum-tipi'].render();
        }

        // 8. Müşteri Top 10
        window.destroyIaaChart('chart-musteri-top10');
        if (data.musteriTop10 && data.musteriTop10.labels.length > 0) {
            window.iaaChartInstances['chart-musteri-top10'] = new ApexCharts(document.querySelector('#chart-musteri-top10'), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Şikayet Sayısı', data: data.musteriTop10.series }],
                xaxis: { categories: data.musteriTop10.labels, labels: { style: { fontSize: '9px' }, trim: true, maxHeight: 100 } },
                plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
                colors: ['#f97316'],
                dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 700 } },
                grid: { borderColor: '#f1f5f9' }
            });
            window.iaaChartInstances['chart-musteri-top10'].render();
        }

        // 10. Çözüm Takımı Dağılımı
        window.destroyIaaChart('chart-squad-dagilimi');
        if (data.squadPersonelDagilimi && data.squadPersonelDagilimi.labels.length > 0) {
            window.iaaChartInstances['chart-squad-dagilimi'] = new ApexCharts(document.querySelector('#chart-squad-dagilimi'), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Şikayet / Proje Sayısı', data: data.squadPersonelDagilimi.series }],
                xaxis: { categories: data.squadPersonelDagilimi.labels, labels: { style: { fontSize: '9px' }, trim: true, maxHeight: 100 } },
                plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
                colors: ['#a855f7'],
                dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 700 } },
                grid: { borderColor: '#f1f5f9' }
            });
            window.iaaChartInstances['chart-squad-dagilimi'].render();
        }

        // 9. Ortalama Çözüm Süresi Trendi
        window.destroyIaaChart('chart-cozum-suresi-trend');
        if (data.cozumSuresiTrend && data.cozumSuresiTrend.labels.length > 0) {
            window.iaaChartInstances['chart-cozum-suresi-trend'] = new ApexCharts(document.querySelector('#chart-cozum-suresi-trend'), {
                chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Ort. Çözüm Süresi (Gün)', data: data.cozumSuresiTrend.series }],
                xaxis: { categories: data.cozumSuresiTrend.labels, labels: { style: { fontSize: '10px' } } },
                colors: ['#8b5cf6'],
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1 } },
                dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 700 }, formatter: function(v) { return v + ' gün'; } },
                grid: { borderColor: '#f1f5f9' },
                yaxis: { labels: { formatter: function(v) { return v + ' gün'; } } }
            });
            window.iaaChartInstances['chart-cozum-suresi-trend'].render();
        }
    };

    // İlk render
    $wire.on('updateAnalizGrafikleri', (eventData) => {
        let data = Array.isArray(eventData) ? eventData[0] : eventData;
        setTimeout(() => window.renderIaaCharts(data), 100);
    });
</script>
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>


<!--[if BLOCK]><![endif]--><?php if($isSuperadmin && $showYetkiModal): ?>
<div class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:key="yetki-modal">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col" @click.away="$wire.closeYetkiModal()">
        
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 rounded-xl p-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">Yetki Matrisi — Kişisel Erişim</h3>
                    <p class="text-xs text-white/80">Hangi kullanıcılar bu sayfaya erişebilir ve hangi verileri görebilir?</p>
                </div>
            </div>
            <button wire:click="closeYetkiModal" class="text-white/80 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 p-6 space-y-6">
            
            <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
                <button wire:click="$set('yetkiReportName', 'analiz_raporu')"
                        class="px-4 py-2 text-xs font-bold rounded-t-lg transition border-b-2 <?php echo e($yetkiReportName === 'analiz_raporu' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                    Analiz Raporu Yetkileri
                </button>
                <button wire:click="$set('yetkiReportName', 'karsilastirma_raporu')"
                        class="px-4 py-2 text-xs font-bold rounded-t-lg transition border-b-2 <?php echo e($yetkiReportName === 'karsilastirma_raporu' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                    Karşılaştırma Raporu Yetkileri
                </button>
            </div>

            
            <!--[if BLOCK]><![endif]--><?php if(session('yetkiSuccess')): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 text-xs font-bold p-3 rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <?php echo e(session('yetkiSuccess')); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if(session('yetkiError')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-bold p-3 rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?php echo e(session('yetkiError')); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <h4 class="text-xs font-black text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-slate-400 rounded-full"></span>
                    Varsayılan Erişimler (Otomatik)
                </h4>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    <strong>Superadmin</strong> ve <strong>Yonetim</strong> rolleri her zaman tüm verilere erişir. Bu roller aşağıdaki kişisel listede gösterilmez.
                </p>
            </div>

            
            <div>
                <h4 class="text-sm font-black text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                    Yetkili Kullanıcılar
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg"><?php echo e($yetkiliKullanicilar->count()); ?> Kişi</span>
                </h4>
                <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left font-black text-gray-600 uppercase tracking-wider">Kullanıcı</th>
                                <th class="px-4 py-3 text-left font-black text-gray-600 uppercase tracking-wider">Rol / Bölüm</th>
                                <th class="px-4 py-3 text-left font-black text-gray-600 uppercase tracking-wider">Veri Kapsamı</th>
                                <th class="px-4 py-3 text-center font-black text-gray-600 uppercase tracking-wider w-24">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $yetkiliKullanicilar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yetki): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-white transition" wire:key="yetki-user-<?php echo e($yetki->user_id); ?>">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <img src="<?php echo e($yetki->user->profile_photo_url ?? ''); ?>" class="w-7 h-7 rounded-full object-cover border border-gray-200" alt="">
                                            <span class="font-bold text-gray-800"><?php echo e($yetki->user->name ?? '-'); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-indigo-600"><?php echo e($yetki->user->roles->pluck('name')->implode(', ')); ?></span>
                                            <span class="text-[10px] text-gray-400"><?php echo e($yetki->user->bolum->ad ?? '-'); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <!--[if BLOCK]><![endif]--><?php if($yetkiEditUserId == $yetki->user_id): ?>
                                            <div class="flex flex-col gap-1">
                                                <select wire:model.live="yetkiDataScope" class="text-[10px] bg-white border border-amber-300 rounded-lg py-1 px-2 focus:ring-amber-500">
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = \App\Models\ReportRoleAuthorization::DATA_SCOPE_OPTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </select>
                                                <!--[if BLOCK]><![endif]--><?php if($yetkiDataScope === 'specific_departments'): ?>
                                                    <div class="max-h-24 overflow-y-auto custom-scrollbar bg-white rounded-lg p-1 border border-gray-200">
                                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tumBolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <label class="flex items-center gap-1 py-0.5 cursor-pointer">
                                                                <input type="checkbox" wire:model.live="yetkiSpecificDeptIds" value="<?php echo e($bolum->id); ?>" class="rounded border-gray-300 text-amber-600 w-3 h-3">
                                                                <span class="text-[9px] text-gray-600"><?php echo e($bolum->ad); ?></span>
                                                            </label>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <div class="flex gap-1 mt-1">
                                                    <button wire:click="updateUserYetki" class="px-2 py-1 bg-amber-500 text-white text-[9px] font-bold rounded-lg hover:bg-amber-600">Kaydet</button>
                                                    <button wire:click="$set('yetkiEditUserId', null)" class="px-2 py-1 bg-gray-200 text-gray-600 text-[9px] font-bold rounded-lg hover:bg-gray-300">İptal</button>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <?php
                                                $scopeLabels = \App\Models\ReportRoleAuthorization::DATA_SCOPE_OPTIONS;
                                                $scopeColors = [
                                                    'all' => 'bg-green-100 text-green-700',
                                                    'own_department' => 'bg-blue-100 text-blue-700',
                                                    'responsible_departments' => 'bg-purple-100 text-purple-700',
                                                    'specific_departments' => 'bg-orange-100 text-orange-700',
                                                ];
                                            ?>
                                            <span class="px-2 py-1 rounded-lg font-bold text-[10px] <?php echo e($scopeColors[$yetki->data_scope] ?? 'bg-gray-100'); ?>">
                                                <?php echo e($scopeLabels[$yetki->data_scope] ?? $yetki->data_scope); ?>

                                            </span>
                                            <!--[if BLOCK]><![endif]--><?php if($yetki->data_scope === 'specific_departments' && !empty($yetki->specific_department_ids)): ?>
                                                <?php $bolumAdlari = \App\Models\Bolum::whereIn('id', $yetki->specific_department_ids)->pluck('ad')->toArray(); ?>
                                                <div class="text-[9px] text-gray-400 mt-0.5"><?php echo e(implode(', ', $bolumAdlari)); ?></div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="editUserYetki(<?php echo e($yetki->user_id); ?>)" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Düzenle">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button wire:click="deleteUserYetki(<?php echo e($yetki->user_id); ?>)" wire:confirm="Bu kullanıcının yetkisini kaldırmak istediğinize emin misiniz?" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Sil">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-400">Henüz kişisel yetki tanımlanmamış.</td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
                <h4 class="text-sm font-black text-gray-700 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>
                    Kullanıcı Ekle
                </h4>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Role Göre Kullanıcı Bul</label>
                    <select wire:model.live="yetkiFilterRole" class="w-full md:w-1/2 bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">-- Rol Seçin --</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tumRoller; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($rol); ?>"><?php echo e($rol); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>

                <!--[if BLOCK]><![endif]--><?php if($yetkiFilterRole && $filtrelenmisKullanicilar->count() > 0): ?>
                    <div class="bg-gray-50 rounded-xl border border-gray-200 max-h-60 overflow-y-auto custom-scrollbar">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-100 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left w-8">
                                        <input type="checkbox"
                                            <?php if(count($yetkiSelectedUserIds) === $filtrelenmisKullanicilar->count() && $filtrelenmisKullanicilar->count() > 0): ?> checked <?php endif; ?>
                                            wire:click="$set('yetkiSelectedUserIds', <?php echo e(count($yetkiSelectedUserIds) === $filtrelenmisKullanicilar->count() ? '[]' : $filtrelenmisKullanicilar->pluck('id')->toJson()); ?>)"
                                            class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                    </th>
                                    <th class="px-3 py-2 text-left font-bold text-gray-600">Kullanıcı</th>
                                    <th class="px-3 py-2 text-left font-bold text-gray-600">Bölüm</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filtrelenmisKullanicilar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-white transition" wire:key="filter-user-<?php echo e($kul->id); ?>">
                                        <td class="px-3 py-2">
                                            <input type="checkbox" wire:model.live="yetkiSelectedUserIds" value="<?php echo e($kul->id); ?>" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <img src="<?php echo e($kul->profile_photo_url); ?>" class="w-6 h-6 rounded-full object-cover" alt="">
                                                <span class="font-bold text-gray-700"><?php echo e($kul->name); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 text-gray-400"><?php echo e($kul->bolum->ad ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>

                    <?php if(count($yetkiSelectedUserIds) > 0): ?>
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-3">
                            <p class="text-xs font-bold text-amber-700">
                                <span class="bg-amber-500 text-white px-2 py-0.5 rounded-lg mr-1"><?php echo e(count($yetkiSelectedUserIds)); ?></span>
                                kullanıcı seçildi — veri kapsamını belirleyin:
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-amber-600 uppercase mb-1">Veri Kapsamı</label>
                                    <select wire:model.live="yetkiBulkDataScope" class="w-full bg-white border border-amber-300 text-xs rounded-xl py-2 px-3 focus:ring-amber-500">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = \App\Models\ReportRoleAuthorization::DATA_SCOPE_OPTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if($yetkiBulkDataScope === 'specific_departments'): ?>
                                    <div>
                                        <label class="block text-[10px] font-bold text-amber-600 uppercase mb-1">Bölümler</label>
                                        <div class="grid grid-cols-2 gap-1 max-h-32 overflow-y-auto custom-scrollbar bg-white rounded-xl p-2 border border-amber-200">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tumBolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <label class="flex items-center gap-1 p-1 cursor-pointer hover:bg-amber-50 rounded">
                                                    <input type="checkbox" wire:model.live="yetkiBulkSpecificDeptIds" value="<?php echo e($bolum->id); ?>" class="rounded border-gray-300 text-amber-600 w-3 h-3">
                                                    <span class="text-[10px] text-gray-600"><?php echo e($bolum->ad); ?></span>
                                                </label>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <button wire:click="saveSelectedUsers" wire:loading.attr="disabled" class="px-5 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold text-xs rounded-xl hover:from-amber-600 hover:to-orange-600 transition shadow-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg wire:loading.remove wire:target="saveSelectedUsers" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <svg wire:loading wire:target="saveSelectedUsers" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="saveSelectedUsers">Seçili Kullanıcılara Yetki Ver</span>
                                <span wire:loading wire:target="saveSelectedUsers">İşleniyor...</span>
                            </button>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php elseif($yetkiFilterRole && $filtrelenmisKullanicilar->count() === 0): ?>
                    <div class="text-center py-4 text-gray-400 text-xs">
                        "<?php echo e($yetkiFilterRole); ?>" rolünde eklenecek kullanıcı bulunamadı (tümü zaten yetkili olabilir).
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-[11px] text-amber-800 leading-relaxed">
                    <strong class="font-black">Nasıl Çalışır:</strong>
                    Rollerden birini seçin → o role ait kullanıcılar listelenir → istediğiniz kişileri işaretleyin → veri kapsamlarını belirleyin → kaydedin.
                    Daha sonra tek kişinin kapsamını düzenleyebilir veya yetkisini kaldırabilirsiniz.
                    <strong>Superadmin</strong> ve <strong>Yonetim</strong> rolleri otomatik olarak tüm verilere erişir.
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->

<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/musteri-sikayet-analiz-raporu.blade.php ENDPATH**/ ?>