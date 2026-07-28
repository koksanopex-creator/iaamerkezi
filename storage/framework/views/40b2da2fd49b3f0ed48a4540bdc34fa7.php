<?php $__env->startPush('pageTitle'); ?>
    Dinamik Karşılaştırma Analizi | 
<?php $__env->stopPush(); ?>

 <?php $__env->slot('header', null, []); ?> 
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <?php echo e(__('Dinamik Karşılaştırma Analizi')); ?>

    </h2>
 <?php $__env->endSlot(); ?>

<div class="space-y-6" wire:key="karsilastirma-raporu">

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
                Dinamik Karşılaştırma Analizi
            </h2>
            <div class="mt-2 flex items-center gap-2">
                <?php
                    $authResult = \App\Models\ReportRoleAuthorization::getAuthorizationForUser(auth()->user(), 'karsilastirma_raporu');
                    $scopeLabels = \App\Models\ReportRoleAuthorization::DATA_SCOPE_OPTIONS;
                    $scope = $authResult ? $authResult['data_scope'] : 'all';
                    $allowedIds = \App\Models\ReportRoleAuthorization::getAllowedBolumIdsForUser(auth()->user(), 'karsilastirma_raporu');
                    
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
            <p class="text-sm text-gray-500 font-medium mt-1">Sınırsız boyut ile şikayet verilerini kıyaslayın.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.musteri-sikayet-analiz-raporu')); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold text-xs rounded-xl hover:bg-gray-200 transition shadow-sm border border-gray-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Tekli Analize Dön
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-red-50 text-red-700 font-bold text-xs rounded-xl hover:bg-red-100 transition shadow-sm border border-red-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                PDF / Çıktı Al
            </button>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 print-hidden">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
            <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Kıyaslama Boyutu ve Filtreler
            </h3>
            <button wire:click="clearFilters" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Temizle
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            
            <div class="lg:col-span-1 bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                <label class="block text-xs font-black text-indigo-800 uppercase tracking-wider mb-2">1. Kıyaslama Boyutu</label>
                <select wire:model.live="kiyaslamaKriteri" class="w-full text-sm font-bold border-indigo-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-white py-2 shadow-sm text-indigo-700">
                    <option value="bolum">Bölümlere Göre</option>
                    <option value="durum">Durumlara Göre</option>
                    <option value="oncelik">Önceliklere Göre</option>
                    <option value="alt_kategori">Alt Kategorilere Göre</option>
                    <option value="takim">Çözüm Takımlarına Göre</option>
                    <option value="squad">Görevli Personele Göre</option>
                </select>
                <div class="mt-4 border-t border-indigo-200 pt-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="kiyaslaGenelOrtalama" class="rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-[10px] font-bold text-indigo-800 leading-tight">Genel Ortalamayı da Ekle</span>
                    </label>
                </div>
                <p class="text-[10px] text-indigo-600 mt-2 font-medium">Grafikler bu kritere göre çoklu seri olarak çizilir.</p>
            </div>

            
            <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Bölüm Seçimi</label>
                    <div x-data="{ open: false }" class="relative w-full">
                        <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="truncate"><?php echo e(count($bolumId) > 0 ? count($bolumId) . ' Seçili' : '-- Tüm Bölümler --'); ?></span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['bolumler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label wire:key="kar-bolum-<?php echo e($bolum->id); ?>" class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox" wire:model.live="bolumId" value="<?php echo e($bolum->id); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-700"><?php echo e($bolum->ad); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Durum Seçimi</label>
                    <div x-data="{ open: false }" class="relative w-full">
                        <button @click="open = !open" type="button" class="w-full bg-gray-50 border border-gray-200 text-xs rounded-xl py-2 px-3 text-left flex justify-between items-center focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="truncate"><?php echo e(count($durum) > 0 ? count($durum) . ' Seçili' : '-- Tüm Durumlar --'); ?></span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto custom-scrollbar p-2">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['durumlar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label wire:key="kar-durum-<?php echo e($key); ?>" class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox" wire:model.live="durum" value="<?php echo e($key); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-700"><?php echo e($label); ?></span>
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
                                <label wire:key="kar-altkat-<?php echo e($ak['id']); ?>" class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox" wire:model.live="altKategoriId" value="<?php echo e($ak['id']); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-700"><?php echo e($ak['ad']); ?></span>
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
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filterData['takimlar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label wire:key="kar-takim-<?php echo e($takim->id); ?>" class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox" wire:model.live="takimId" value="<?php echo e($takim->id); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-700"><?php echo e($takim->ad); ?></span>
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
                                <label wire:key="kar-squad-<?php echo e($sq->id); ?>" class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox" wire:model.live="squadUserId" value="<?php echo e($sq->id); ?>" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-700"><?php echo e($sq->name); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-2">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tarih Başlangıç</label>
                        <input type="date" wire:model.live="startDate" class="w-full text-xs border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 py-1.5">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tarih Bitiş</label>
                        <input type="date" wire:model.live="endDate" class="w-full text-xs border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 py-1.5">
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-gradient-to-r from-indigo-50 to-white border-l-4 border-indigo-500 rounded-r-2xl p-5 shadow-sm print-hidden">
        <div class="flex flex-col lg:flex-row gap-5 items-start lg:items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-indigo-600 text-white rounded-xl shadow-md flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="flex-1">
                <div class="text-indigo-900 leading-relaxed">
                    <?php echo $activeFilterInfo; ?>

                </div>
                <!--[if BLOCK]><![endif]--><?php if($algorithmicSummary): ?>
                    <div class="mt-4 text-[13px] font-bold text-indigo-800 bg-white p-3 rounded-xl border border-indigo-100 shadow-sm inline-block">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <?php echo $algorithmicSummary; ?>

                        </span>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if(count($comparisonData) > 0): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <div class="flex flex-nowrap min-w-full">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $comparisonData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex-1 min-w-[280px] max-w-[400px] border-r border-gray-100 last:border-r-0 p-6 flex flex-col hover:bg-slate-50/50 transition-colors group">
                        
                        <div class="text-center mb-6 pb-6 border-b border-gray-100 flex-grow">
                            <!--[if BLOCK]><![endif]--><?php if(isset($item['details']['logo']) && $item['details']['logo']): ?>
                                <img src="<?php echo e($item['details']['logo']); ?>" class="h-16 mx-auto mb-4 object-contain group-hover:scale-110 transition-transform" alt="<?php echo e($item['label']); ?> Logo">
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-2xl mx-auto mb-4 group-hover:scale-110 transition-transform shadow-sm border border-indigo-100">
                                    <?php echo e(mb_substr($item['label'], 0, 1)); ?>

                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <h3 class="font-black text-lg text-slate-800"><?php echo e($item['label']); ?></h3>
                            <!--[if BLOCK]><![endif]--><?php if(isset($item['details']['subtitle'])): ?>
                                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-1"><?php echo e($item['details']['subtitle']); ?></p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <!--[if BLOCK]><![endif]--><?php if(isset($item['details']['director']) && $item['details']['director'] !== '-'): ?>
                                <div class="mt-4 text-xs bg-white rounded-xl p-3 border border-gray-100 shadow-sm text-left">
                                    <p class="text-gray-500 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span><span class="font-bold text-gray-700">Direktör:</span> <?php echo e($item['details']['director']); ?></p>
                                    <!--[if BLOCK]><![endif]--><?php if(isset($item['details']['liderler']) && $item['details']['liderler'] !== '-'): ?>
                                        <p class="text-gray-500 mt-2 flex items-center gap-2" title="<?php echo e($item['details']['liderler']); ?>"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span><span class="font-bold text-gray-700">Lider:</span> <?php echo e(Str::limit($item['details']['liderler'], 25)); ?></p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php elseif(isset($item['details']['roles'])): ?>
                                <div class="mt-4 text-xs bg-white rounded-xl p-3 border border-gray-100 shadow-sm text-left">
                                    <p class="text-gray-500 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span><span class="font-bold text-gray-700">Rol:</span> <?php echo e(Str::limit($item['details']['roles'], 25)); ?></p>
                                    <p class="text-gray-500 mt-2 truncate flex items-center gap-2" title="<?php echo e($item['details']['email']); ?>"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span><?php echo e($item['details']['email']); ?></p>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl border border-gray-100/50">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Toplam Şikayet</span>
                                <span class="text-base font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md"><?php echo e($item['kpi']['toplam']); ?></span>
                            </div>
                            <div class="flex justify-between items-center p-3">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Açık / Çözülen</span>
                                <span class="text-sm font-black flex items-center gap-1">
                                    <span class="text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md"><?php echo e($item['kpi']['acik']); ?></span> 
                                    <span class="text-gray-300">/</span> 
                                    <span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md"><?php echo e($item['kpi']['cozulen']); ?></span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl border border-gray-100/50">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider" title="Ortalama Çözüm Süresi">Ort. Çözüm</span>
                                <span class="text-sm font-black flex items-center gap-1 <?php echo e($item['kpi']['ortCozumSuresi'] > 15 ? 'text-red-600 bg-red-50' : 'text-emerald-600 bg-emerald-50'); ?> px-2 py-0.5 rounded-md">
                                    <?php echo e($item['kpi']['ortCozumSuresi']); ?> <span class="text-[10px] font-bold opacity-70">GÜN</span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">İade Oranı</span>
                                <span class="text-sm font-black <?php echo e($item['kpi']['iadeOrani'] > 5 ? 'text-red-600 bg-red-50' : 'text-emerald-600 bg-emerald-50'); ?> px-2 py-0.5 rounded-md">
                                    %<?php echo e($item['kpi']['iadeOrani']); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div wire:ignore class="space-y-6">
        
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4">Temel Performans Karşılaştırması</h3>
            <div id="chart-kpi-comparison"></div>
        </div>

        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4">Ortalama Çözüm Süresi Trendi (Aylık)</h3>
            <div id="chart-trend-comparison"></div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <?php
        $__scriptKey = '2766869415-0';
        ob_start();
    ?>
    <script>
        window.iaaChartPalette = ['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#f97316','#14b8a6','#a855f7'];
        if (!window.iaaChartInstances) window.iaaChartInstances = {};

        function destroyChart(id) {
            if (window.iaaChartInstances[id]) {
                window.iaaChartInstances[id].destroy();
                delete window.iaaChartInstances[id];
            }
        }

        function renderComparisonCharts(dataList) {
            
            // ================= 1. KPI Karşılaştırması =================
            destroyChart('chart-kpi-comparison');
            
            var kpiCategories = ['Açık', 'Kapalı', 'Toplam', 'Çözüm Süresi (Gün)', 'İade Oranı (%)'];
            var kpiSeries = [];
            
            dataList.forEach(function(item) {
                kpiSeries.push({
                    name: item.label,
                    data: [
                        item.kpi.acik, 
                        item.kpi.cozulen, 
                        item.kpi.toplam, 
                        item.kpi.ortCozumSuresi || 0,
                        item.kpi.iadeOrani || 0
                    ]
                });
            });

            var kpiOptions = {
                chart: { type: 'bar', height: 350, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: kpiSeries,
                xaxis: { categories: kpiCategories, labels: { style: { fontWeight: 600, colors: '#64748b' } } },
                plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                colors: window.iaaChartPalette,
                legend: { position: 'top', horizontalAlign: 'right', fontWeight: 'bold' }
            };
            window.iaaChartInstances['chart-kpi-comparison'] = new ApexCharts(document.querySelector('#chart-kpi-comparison'), kpiOptions);
            window.iaaChartInstances['chart-kpi-comparison'].render();

            // ================= 2. Trend Karşılaştırması =================
            destroyChart('chart-trend-comparison');
            
            // Tüm ayları birleştirip X eksenini oluştur
            var allMonths = new Set();
            dataList.forEach(function(item) {
                if(item.trend && item.trend.labels) {
                    item.trend.labels.forEach(function(l) { allMonths.add(l); });
                }
            });
            var monthsArray = Array.from(allMonths).sort();

            var trendSeries = [];
            dataList.forEach(function(item) {
                var sData = [];
                monthsArray.forEach(function(month) {
                    var idx = item.trend && item.trend.labels ? item.trend.labels.indexOf(month) : -1;
                    sData.push(idx !== -1 ? parseFloat(item.trend.series[idx]) : null);
                });
                trendSeries.push({
                    name: item.label,
                    data: sData
                });
            });

            var trendOptions = {
                chart: { type: 'line', height: 350, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: trendSeries,
                stroke: { width: 3, curve: 'smooth' },
                markers: { size: 5, strokeWidth: 0, hover: { size: 7 } },
                xaxis: { categories: monthsArray, labels: { style: { fontWeight: 600, colors: '#64748b' } } },
                yaxis: { title: { text: 'Ortalama Gün' } },
                colors: window.iaaChartPalette,
                legend: { position: 'top', horizontalAlign: 'right', fontWeight: 'bold' },
                tooltip: { shared: true, intersect: false }
            };
            window.iaaChartInstances['chart-trend-comparison'] = new ApexCharts(document.querySelector('#chart-trend-comparison'), trendOptions);
            window.iaaChartInstances['chart-trend-comparison'].render();
        }

        Livewire.on('livewire:initialized', () => {
            renderComparisonCharts(<?php echo json_encode($comparisonData, 15, 512) ?>);
        });

        Livewire.on('karsilastirma-updated', (event) => {
            // Livewire 3 event data access
            let data = event[0]?.data || event?.data || event[0] || event;
            if (data) {
                renderComparisonCharts(data);
            }
        });
    </script>
        <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>

</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/musteri-sikayet-karsilastirma.blade.php ENDPATH**/ ?>