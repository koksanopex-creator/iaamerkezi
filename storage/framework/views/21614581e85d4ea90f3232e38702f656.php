<div x-data="{ mainTab: 'bolum', activeBolum: <?php echo e(request('active_bolum', $stats['direktor_bolumleri']->first()->id ?? 'null')); ?>, currentFilter: 'all' }" class="space-y-8">
    
    <!-- Üst Başlık ve Bilgi -->
    <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 -mr-16 -mt-16">
            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-2xl font-bold">Direktör Genel Bakış</h3>
                <p class="text-indigo-100 mt-1">Sorumlu olduğunuz <?php echo e($stats['direktor_bolumleri']->count()); ?> bölümün performansını sekmelere tıklayarak inceleyebilirsiniz.</p>
            </div>
            <div class="flex items-center gap-2">
                 <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Direktör
                </span>
            </div>
        </div>
    </div>

    <!-- ANA SEKMELER -->
    <div class="flex p-1 bg-gray-100 rounded-xl w-full md:w-max">
        <button @click="mainTab = 'bolum'"
                :class="mainTab === 'bolum' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 md:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            Bölüm Verileri
        </button>
        <button @click="mainTab = 'kisisel'"
                :class="mainTab === 'kisisel' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 md:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Kişisel Durum
        </button>
    </div>

    <div x-show="mainTab === 'bolum'" x-cloak x-transition:enter="transition-opacity duration-300" class="space-y-8 animate-fade-in">
        <!-- GENEL TOPLAM İSTATİSTİKLERİ -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
                $aggregateCards = [
                [
                    'title' => 'Bölümler Toplam Şikayet',
                    'key' => 'sikayet',
                    'color' => 'red',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>'
                ],
                [
                    'title' => 'Bölümler Aktif Projeler',
                    'key' => 'proje',
                    'color' => 'indigo',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>',
                    'desc' => 'İşlemdeki şikayet ve İAA\'ların toplamıdır.'
                ],
                [
                    'title' => 'Bölümler Toplam İAA',
                    'key' => 'saf_iaa',
                    'color' => 'purple',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>'
                ],
                [
                    'title' => 'Bölümler Toplam Disiplin',
                    'key' => 'disiplin',
                    'color' => 'orange',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                ],
            ];
        ?>

        <?php $__currentLoopData = $aggregateCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $cData = $stats['direktor_genel_toplam'][$card['key']] ?? ['total' => 0, 'breakdown' => []]; ?>
            <div x-data="{ showTooltip: false }" 
                 @mouseenter="showTooltip = true" 
                 @mouseleave="showTooltip = false"
                 class="bg-white rounded-2xl p-5 border border-<?php echo e($card['color']); ?>-100 shadow-sm hover:shadow-md transition group relative cursor-pointer">
                <div class="absolute right-0 top-0 h-full w-1 bg-<?php echo e($card['color']); ?>-500"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1"><?php echo e($card['title']); ?></p>
                        <h4 class="text-3xl font-extrabold text-gray-800"><?php echo e($cData['total']); ?></h4>
                        <?php if(isset($card['desc'])): ?>
                            <p class="text-[9px] text-gray-400 mt-1 leading-tight"><?php echo e($card['desc']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="bg-<?php echo e($card['color']); ?>-50 p-3 rounded-xl text-<?php echo e($card['color']); ?>-500 group-hover:scale-110 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?php echo $card['icon']; ?></svg>
                    </div>
                </div>

                <!-- Tooltip (Bölüm Dökümü) -->
                <div x-show="showTooltip" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute z-[100] top-full left-0 right-0 mt-2 p-4 bg-gray-900 shadow-2xl rounded-xl border border-gray-700 text-white text-xs"
                     x-cloak>
                    <div class="flex items-center justify-between border-b border-white/10 pb-2 mb-2">
                        <span class="font-bold text-[10px] uppercase tracking-widest text-<?php echo e($card['color']); ?>-400">Bölüm Bazlı Dağılım</span>
                        <span class="text-[10px] bg-white/10 px-2 py-0.5 rounded-full">Toplam: <?php echo e($cData['total']); ?></span>
                    </div>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto custom-scrollbar">
                        <?php $hasData = false; ?>
                        <?php $__currentLoopData = $cData['breakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bName => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($count > 0): ?>
                                <?php $hasData = true; ?>
                                <div class="flex justify-between items-center group/item hover:bg-white/5 p-1 rounded transition">
                                    <span class="text-gray-300 group-hover/item:text-white"><?php echo e($bName); ?></span>
                                    <span class="font-bold text-<?php echo e($card['color']); ?>-300"><?php echo e($count); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!$hasData): ?>
                            <div class="text-center py-2 text-gray-500 italic">Veri bulunamadı</div>
                        <?php endif; ?>
                    </div>
                    <div class="absolute -top-1 left-6 w-2 h-2 bg-gray-900 rotate-45 border-l border-t border-white/10"></div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- BÖLÜM SEKMELERİ -->
    <div id="draggable-tabs" class="flex flex-wrap gap-2 border-b border-gray-200 pb-1">
        <?php $__currentLoopData = $stats['direktor_bolumleri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                $bDataTab = $stats['bolum_verileri'][$bolum->id] ?? null;
                $pVisitCount = $bDataTab['pending_visit_count'] ?? 0;
                $onayCount = ($bDataTab['dagilim']['iaa']['onay_bekleyen'] ?? 0) + ($bDataTab['dagilim']['sikayet']['onay_bekleyen'] ?? 0);
                $hasAlert = $pVisitCount > 0 || $onayCount > 0;
            ?>
            <button @click="activeBolum = <?php echo e($bolum->id); ?>"
                    data-id="<?php echo e($bolum->id); ?>"
                    :class="activeBolum === <?php echo e($bolum->id); ?> ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 cursor-move relative">
                <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <?php echo e($bolum->ad); ?>

                <?php if($hasAlert): ?>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white <?php echo e($onayCount > 0 ? 'animate-fast-pulse' : ''); ?>"></span>
                    </span>
                <?php endif; ?>
            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>


    <!-- BÖLÜM İÇERİKLERİ -->
    <?php $__currentLoopData = $stats['direktor_bolumleri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $bData = $stats['bolum_verileri'][$bolum->id] ?? null; ?>
        <div x-show="activeBolum === <?php echo e($bolum->id); ?>" x-cloak x-transition:enter="transition-opacity duration-300" class="space-y-8 animate-fade-in">
            <?php if($bData): ?>
                <!-- HIZLI NAVİGASYON (SAYAÇLI) -->
                <div class="sticky top-0 z-40 bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100 py-4 mb-8 -mx-4 px-4 flex flex-wrap items-center gap-2 transition-all">
                    <div class="flex items-center gap-2 mr-2 border-r border-gray-100 pr-4">
                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Hızlı Erişim</span>
                    </div>

                    <a href="<?php echo e(route('admin.bolumler.dashboard', $bolum->id)); ?>" 
                       class="group flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-black transition-all bg-gradient-to-r from-indigo-600 to-blue-700 text-white shadow-md hover:shadow-lg hover:scale-105 active:scale-95 shrink-0 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        <span>Bölüm Paneline Git</span>
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>

                    <?php 
                        $iadeCount = isset($bData['iadeVerileri']) ? ($bData['iadeVerileri'] instanceof \Illuminate\Pagination\LengthAwarePaginator ? $bData['iadeVerileri']->total() : $bData['iadeVerileri']->count()) : 0;
                        
                        $onayBekleyenCount = ($bData['dagilim']['iaa']['onay_bekleyen'] ?? 0) + ($bData['dagilim']['sikayet']['onay_bekleyen'] ?? 0);

                        $navItems = [
                            ['id' => 'bolum-sikayetleri-tablosu', 'label' => 'Şikayetler', 'count' => $bData['bolum_sikayet_count'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'red'],
                            ['id' => 'bolum-iadeleri-tablosu', 'label' => 'İadeler', 'count' => $iadeCount, 'icon' => 'M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m16 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'red'],
                            ['id' => 'bolum-ziyaretleri-tablosu', 'label' => 'Ziyaretler', 'count' => $bData['total_visit_count'] ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'indigo'],
                            ['id' => 'bolum-iaa-tablosu', 'label' => 'İAA Projeleri', 'count' => $bData['bolum_iaa_projeleri']->count() ?? 0, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'indigo'],
                        ];

                        if ($onayBekleyenCount > 0) {
                            $navItems[] = [
                                'id' => 'onay-bekleyen-projeler', 
                                'label' => 'Onay Bekleyenler', 
                                'count' => $onayBekleyenCount, 
                                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 
                                'color' => 'orange',
                                'pulse' => true
                            ];
                        }

                        $navItems = array_merge($navItems, [
                            ['id' => 'bolum-disiplin-tablosu', 'label' => 'Disiplin', 'count' => $bData['bolum_disiplin_olaylari']->count() ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'orange'],
                            ['id' => 'bolum-personelleri-tablosu', 'label' => 'Personel', 'count' => $bData['personel_listesi']->count() ?? 0, 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'gray'],
                            ['id' => 'bolum-personel-gorevleri-tablosu', 'label' => 'Görevler', 'count' => $bData['bolum_personel_gorevleri']->count() ?? 0, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'color' => 'blue'],
                        ]);
                    ?>
                    <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button @click="document.getElementById('<?php echo e($item['id']); ?>-' + activeBolum).scrollIntoView({ behavior: 'smooth', block: 'start' })" 
                                class="group flex items-center gap-1.5 px-2.5 py-1.5 rounded-full text-[11px] font-bold transition-all border border-transparent 
                                       <?php if($item['pulse'] ?? false): ?> animate-fast-pulse bg-orange-100 border-orange-300 text-orange-900 shadow-sm <?php else: ?> hover:bg-<?php echo e($item['color'] ?? 'indigo'); ?>-50 hover:border-<?php echo e($item['color'] ?? 'indigo'); ?>-100 text-gray-600 hover:text-<?php echo e($item['color'] ?? 'indigo'); ?>-700 <?php endif; ?> shrink-0 mb-1">
                            <svg class="w-3.5 h-3.5 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($item['icon']); ?>"></path></svg>
                            <span><?php echo e($item['label']); ?></span>
                            <span class="bg-<?php echo e($item['color'] ?? 'indigo'); ?>-100 text-<?php echo e($item['color'] ?? 'indigo'); ?>-700 px-1.5 py-0.5 rounded-full text-[9px] shadow-sm"><?php echo e($item['count']); ?></span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <!-- İstatistik Kartları -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Aktif İAA -->
                    <div @click="document.getElementById('bolum-iaa-tablosu-<?php echo e($bolum->id); ?>').scrollIntoView({ behavior: 'smooth' })" 
                         class="bg-white p-5 rounded-xl border border-indigo-100 shadow-sm hover:shadow-md transition relative overflow-hidden group cursor-pointer">
                        <div class="absolute right-0 top-0 h-full w-1 bg-indigo-500"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bölüm İAA Projeleri</p>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-extrabold text-gray-800"><?php echo e($bData['bolum_saf_iaa_count'] ?? 0); ?></span>
                            <div class="bg-indigo-50 p-2 rounded-lg text-indigo-600 group-hover:scale-110 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Aktif Şikayet -->
                    <div @click="document.getElementById('bolum-sikayetleri-tablosu-<?php echo e($bolum->id); ?>').scrollIntoView({ behavior: 'smooth' })" 
                         class="bg-white p-5 rounded-xl border border-red-100 shadow-sm hover:shadow-md transition relative overflow-hidden group cursor-pointer">
                        <div class="absolute right-0 top-0 h-full w-1 bg-red-500"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bölüm Şikayetleri</p>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-extrabold text-gray-800"><?php echo e($bData['bolum_sikayet_count'] ?? 0); ?></span>
                            <div class="bg-red-50 p-2 rounded-lg text-red-600 group-hover:scale-110 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </div>
                    </div>


                    <!-- Disiplin -->
                    <div @click="document.getElementById('bolum-disiplin-tablosu-<?php echo e($bolum->id); ?>').scrollIntoView({ behavior: 'smooth' })"
                         class="bg-white p-5 rounded-xl border border-orange-100 shadow-sm hover:shadow-md transition relative overflow-hidden group cursor-pointer">
                        <div class="absolute right-0 top-0 h-full w-1 bg-orange-500"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bölüm Disiplin Olayları</p>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-extrabold text-gray-800"><?php echo e($bData['bolum_disiplin_count'] ?? 0); ?></span>
                            <div class="bg-orange-50 p-2 rounded-lg text-orange-600 group-hover:scale-110 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Toplam İAA (Tarih bazlı) -->
                    <div @click="document.getElementById('bolum-iaa-tablosu-<?php echo e($bolum->id); ?>').scrollIntoView({ behavior: 'smooth' })"
                         class="bg-white p-5 rounded-xl border border-cyan-100 shadow-sm hover:shadow-md transition relative overflow-hidden group cursor-pointer">
                        <div class="absolute right-0 top-0 h-full w-1 bg-cyan-500"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Toplam Veri (Filtreli)</p>
                        <div class="flex items-end justify-between">
                            <?php 
                                $totalData = ($bData['total_iaa_count'] ?? 0) + ($bData['total_sikayet_count'] ?? 0);
                            ?>
                            <span class="text-3xl font-extrabold text-gray-800"><?php echo e($totalData); ?></span>
                            <div class="bg-cyan-50 p-2 rounded-lg text-cyan-600 group-hover:scale-110 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m32-2v-2a4 4 0 00-4-4h-2a4 4 0 00-4 4v2m-9 8a4 4 0 11-8 0 4 4 0 018 0zM12 11a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                

                <!-- Alt Tablolar ve Dağılımlar -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Durum Dağılımı -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2 border-b pb-4">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                            Proje Durum Dağılımı (<?php echo e($bolum->ad); ?>)
                        </h4>
                        
                        <div class="space-y-4">
                            <?php 
                                $categories = [
                                    'yeni' => ['label' => 'Yeni', 'color' => 'blue', 'icon' => 'plus'],
                                    'islemde' => ['label' => 'İşlemdekiler', 'color' => 'indigo', 'icon' => 'clock'],
                                    'tamamlanan' => ['label' => 'Tamamlananlar', 'color' => 'green', 'icon' => 'check'],
                                    'geciken' => ['label' => 'Gecikenler', 'color' => 'red', 'icon' => 'exclamation']
                                ];
                                $totalOverall = array_sum($bData['dagilim']['iaa'] ?? []) + array_sum($bData['dagilim']['sikayet'] ?? []);
                            ?>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php 
                                    $count = ($bData['dagilim']['iaa'][$key] ?? 0) + ($bData['dagilim']['sikayet'][$key] ?? 0);
                                    $percent = $totalOverall > 0 ? round(($count / $totalOverall) * 100) : 0;
                                ?>
                                <div class="relative pt-1 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition" 
                                     @click="currentFilter = (currentFilter === '<?php echo e($key); ?>' ? 'all' : '<?php echo e($key); ?>')">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold inline-block py-1 px-2 uppercase rounded-full"
                                                  :class="currentFilter === '<?php echo e($key); ?>' ? 'bg-<?php echo e($meta['color']); ?>-600 text-white' : 'text-<?php echo e($meta['color']); ?>-600 bg-<?php echo e($meta['color']); ?>-100'">
                                                <?php echo e($meta['label']); ?>

                                            </span>
                                            <template x-if="currentFilter === '<?php echo e($key); ?>'">
                                                <span class="text-[10px] text-gray-400 font-medium">Aktif Filtre</span>
                                            </template>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-bold inline-block text-<?php echo e($meta['color']); ?>-600">
                                                <?php echo e($count); ?> Adet (%<?php echo e($percent); ?>)
                                            </span>
                                        </div>
                                    </div>
                                    <div class="overflow-hidden h-2 mb-1 text-xs flex rounded bg-gray-100">
                                        <div style="width:<?php echo e($percent); ?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-<?php echo e($meta['color']); ?>-500"></div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="pt-2 text-center">
                                <button x-show="currentFilter !== 'all'" @click="currentFilter = 'all'" class="text-[10px] font-bold text-indigo-600 hover:underline">Filtreyi Temizle</button>
                            </div>
                        </div>
                    </div>

                    <!-- Son Hareketler Listesi -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2 border-b pb-4">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Son Eklenen Projeler (<?php echo e($bolum->ad); ?>)
                        </h4>
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                <?php $__empty_1 = true; $__currentLoopData = $bData['bolum_projeleri']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li>
                                        <div class="relative pb-8">
                                            <?php if(!$loop->last): ?>
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            <?php endif; ?>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-<?php echo e($proje->musteri_sikayeti_id ? 'red' : 'indigo'); ?>-100 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-<?php echo e($proje->musteri_sikayeti_id ? 'red' : 'indigo'); ?>-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm text-gray-800 font-bold truncate" title="<?php echo e($proje->baslik); ?>"><?php echo e($proje->baslik); ?></p>
                                                        <p class="text-xs text-gray-500 mt-0.5"><?php echo e($proje->atananTakim->ad ?? 'Takım Atanmadı'); ?></p>
                                                    </div>
                                                    <div class="text-right text-xs whitespace-nowrap text-gray-500 flex-shrink-0">
                                                        <div class="scale-90 origin-right">
                                                            <?php echo $proje->durum_etiketi; ?>

                                                        </div>
                                                        <div class="mt-1"><?php echo e($proje->created_at->diffForHumans()); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <li class="py-10 text-center text-gray-400">Henüz kayıt yok.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div> <!-- Grid sonu -->

                <!-- Bölüm Şikayetleri Listesi (Genişletilmiş) -->
                <div id="bolum-sikayetleri-tablosu-<?php echo e($bolum->id); ?>" x-data="{ showAll: false }" class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden scroll-mt-24">
                    <div class="px-6 py-4 border-b border-red-50 bg-red-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                             <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Bölüm Şikayetleri (<?php echo e($bolum->ad); ?>)
                        </h4>
                        
                        <div class="flex flex-wrap items-center gap-3">
                            
                            <form method="GET" action="<?php echo e(url()->current()); ?>" class="flex items-center gap-2">
                                <input type="hidden" name="active_bolum" value="<?php echo e($bolum->id); ?>">
                                <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-red-500 focus:border-red-500" placeholder="Başlangıç">
                                <span class="text-gray-400 text-xs">-</span>
                                <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-red-500 focus:border-red-500" placeholder="Bitiş">
                                <button type="submit" class="p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                                <?php if(request('start_date') || request('end_date')): ?>
                                    <a href="<?php echo e(url()->current()); ?>?active_bolum=<?php echo e($bolum->id); ?>" class="p-1.5 bg-gray-100 text-gray-500 rounded-lg hover:bg-gray-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                <?php endif; ?>
                            </form>
                            <span class="text-xs font-bold text-red-600 bg-red-100 px-2.5 py-1 rounded-full"><?php echo e($bData['bolum_sikayet_count']); ?> Kayıt</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Şikayet Başlığı</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Müşteri</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ekleyen</th>
                                    <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $bData['bolum_sikayetleri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php 
                                        $rowKey = 'all';
                                        if (in_array($sikayet->musteri_durum ?? '', ['Yeni'])) $rowKey = 'yeni';
                                        elseif (in_array($sikayet->musteri_durum ?? '', ['İşlemde', 'Atandı'])) $rowKey = 'islemde';
                                        elseif (in_array($sikayet->musteri_durum ?? '', ['Kapatıldı', 'İptal Edildi'])) $rowKey = 'tamamlanan';
                                        
                                        $isDelayed = ($sikayet->iaaProjesi && $sikayet->iaaProjesi->talepEdenTakimlar->isNotEmpty() && $sikayet->iaaProjesi->talepEdenTakimlar->first()->pivot->due_date < now() && !in_array($sikayet->iaaProjesi->durum, ['Tamamlandı', 'Talep Olarak Kapatıldı', 'talep_olarak_kapatildi', 'Reddedildi', 'İptal Edildi']));

                                        // Müşteri Girdisi Mantığı
                                        $isCustomerEntry = false;
                                        if ($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->is_personnel == 0) {
                                            $isCustomerEntry = true;
                                        } elseif ($sikayet->user_id && !$sikayet->olusturanKurulUyesi) {
                                            $isCustomerEntry = true;
                                        }
                                    ?>
                                    <tr x-show="(currentFilter === 'all' && (showAll || <?php echo e($loop->index); ?> < 5)) || currentFilter !== 'all'" 
                                        class="hover:bg-gray-50 transition cursor-pointer <?php echo e($isCustomerEntry ? 'bg-red-50/40' : ''); ?>" onclick="window.location='<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>'">
                                        <td class="px-6 py-4 whitespace-nowrap max-w-[300px]">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="text-sm font-bold text-gray-900 truncate" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>"><?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 60)); ?></div>
                                                <?php if($isCustomerEntry): ?>
                                                    <span class="px-1.5 py-0.5 rounded bg-red-600 text-white text-[8px] font-black tracking-tighter uppercase whitespace-nowrap">Müşteri Girdisi</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-[11px] text-gray-500 line-clamp-1"><?php echo e(Str::limit($sikayet->musteri_sikayet_detayi, 50)); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo e($sikayet->customer->name ?? 'Bilinmeyen Müşteri'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-medium <?php echo e($isCustomerEntry ? 'text-red-700 font-bold' : ''); ?>">
                                                    <?php echo e($sikayet->olusturanKurulUyesi->name ?? 'Sistem'); ?>

                                                </span>
                                                <?php if($isCustomerEntry): ?>
                                                    <span class="text-[9px] bg-red-100 text-red-700 px-1 rounded border border-red-200 font-bold">DIŞ</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex flex-col items-center gap-1.5">
                                                <?php if($sikayet->iaaProjesi): ?>
                                                    
                                                    <?php echo $sikayet->iaaProjesi->durum_etiketi; ?>

                                                    
                                                    <?php $progress = $sikayet->iaaProjesi->ilerleme_verisi; ?>
                                                    <?php if($progress['toplam'] > 0): ?>
                                                        <div class="mt-1 flex flex-col items-center w-full max-w-[120px]">
                                                            <?php if($sikayet->iaaProjesi->aktif_adim): ?>
                                                                <span class="text-[9px] text-gray-600 font-bold leading-tight text-center mb-0.5" title="Şu anki Adım">
                                                                    <?php echo e($sikayet->iaaProjesi->aktif_adim->name); ?>

                                                                </span>
                                                            <?php endif; ?>
                                                            
                                                            <div class="flex items-center gap-2 w-full">
                                                                <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                                                                    <div class="h-full bg-<?php echo e($progress['yuzde'] == 100 ? 'green' : 'blue'); ?>-500 transition-all duration-500" style="width: <?php echo e($progress['yuzde']); ?>%"></div>
                                                                </div>
                                                                <span class="text-[8px] font-bold text-gray-500"><?php echo e($progress['yuzde']); ?>%</span>
                                                            </div>
                                                            
                                                            <div class="text-[8px] text-gray-400 mt-0.5 italic">
                                                                Adım: <?php echo e($progress['tamamlanan']); ?>/<?php echo e($progress['toplam']); ?>

                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="px-2 py-1 rounded-full bg-<?php echo e($sikayet->durum_rengi ?? 'gray'); ?>-100 text-<?php echo e($sikayet->durum_rengi ?? 'gray'); ?>-700 font-bold border border-<?php echo e($sikayet->durum_rengi ?? 'gray'); ?>-200 text-[10px]">
                                                    <?php echo $sikayet->musteri_durum_badge; ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs text-gray-500">
                                            <?php echo e($sikayet->created_at->diffForHumans()); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic font-medium">Bu bölüme ait şikayet kaydı bulunamadı.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(count($bData['bolum_sikayetleri']) > 5): ?>
                        <div class="px-6 py-3 border-t border-red-50 bg-red-50/10 flex justify-center">
                            <button @click="showAll = !showAll" class="text-[11px] font-bold text-red-600 hover:text-red-800 transition uppercase tracking-widest flex items-center gap-1">
                                <span x-text="showAll ? 'Daha Az Göster' : 'Devamını Göster (<?php echo e(count($bData['bolum_sikayetleri']) - 5); ?> Kayıt Daha)'"></span>
                                <svg class="w-4 h-4 transition-transform" :class="showAll ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bölüm İadeleri Tablosu (Taşındı) -->
                <div id="bolum-iadeleri-tablosu-<?php echo e($bolum->id); ?>" class="mt-8 border-t border-gray-100 pt-8" x-data="{ iadeShowAll: false }">
                    <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden mb-4">
                        <div class="px-6 py-4 border-b border-red-50 bg-red-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m16 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Bölüm İade Kayıtları (<?php echo e($bolum->ad); ?>)
                            </h4>
                            <div class="flex flex-wrap items-center gap-3">
                                
                                <form method="GET" action="<?php echo e(url()->current()); ?>" class="flex items-center gap-2">
                                    <input type="hidden" name="active_bolum" value="<?php echo e($bolum->id); ?>">
                                    <input type="date" name="return_start_date" value="<?php echo e(request('return_start_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-red-500 focus:border-red-500">
                                    <span class="text-gray-400 text-xs">-</span>
                                    <input type="date" name="return_end_date" value="<?php echo e(request('return_end_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-red-500 focus:border-red-500">
                                    <button type="submit" class="p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </button>
                                </form>
                                <span class="text-xs font-bold text-red-600 bg-red-100 px-2.5 py-1 rounded-full"><?php echo e(isset($bData['iadeVerileri']) ? $bData['iadeVerileri']->total() : 0); ?> Kayıt</span>
                            </div>
                        </div>
                        <?php echo $__env->make('dashboard.partials.iadeler-tablosu', [
                            'iadeVerileri' => $bData['iadeVerileri'] ?? collect(),
                            'hideHeader' => true,
                            'iadeLimit' => 5
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>

                
                <div id="bolum-ziyaretleri-tablosu-<?php echo e($bolum->id); ?>" class="mt-8 border-t border-gray-100 pt-8 scroll-mt-24">
                    <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm overflow-hidden mb-4">
                        <div class="px-6 py-4 border-b border-indigo-50 bg-indigo-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Bölüm Müşteri Ziyaretleri (<?php echo e($bolum->ad); ?>)
                            </h4>
                            <div class="flex flex-wrap items-center gap-3">
                                
                                <form method="GET" action="<?php echo e(url()->current()); ?>" class="flex items-center gap-2">
                                    <input type="hidden" name="active_bolum" value="<?php echo e($bolum->id); ?>">
                                    <input type="date" name="ziyaret_start_date" value="<?php echo e(request('ziyaret_start_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <span class="text-gray-400 text-xs">-</span>
                                    <input type="date" name="ziyaret_end_date" value="<?php echo e(request('ziyaret_end_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="submit" class="p-1.5 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </button>
                                </form>
                                <span class="text-xs font-bold text-indigo-600 bg-indigo-100 px-2.5 py-1 rounded-full">Ziyaret Takibi</span>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto custom-scrollbar">
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.super-admin-visit-table', [
                                'bolumIds' => [$bolum->id], 
                                'hideHeader' => true,
                                'startDate' => request('ziyaret_start_date'),
                                'endDate' => request('ziyaret_end_date')
                            ]);

$__html = app('livewire')->mount($__name, $__params, 'dept-visit-table-'.$bolum->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        </div>
                    </div>
                </div>

                <!-- Bölüm İAA Projeleri Listesi -->
                <div id="bolum-iaa-tablosu-<?php echo e($bolum->id); ?>" 
                     class="bg-white rounded-2xl border border-indigo-100 shadow-sm overflow-hidden scroll-mt-24 mt-8"
                     x-data="{ showAllIaa: false }">
                    <div class="px-6 py-4 border-b border-indigo-50 bg-indigo-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                             <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Bölüm İAA Projeleri (<?php echo e($bolum->ad); ?>)
                        </h4>
                        <div class="flex flex-wrap items-center gap-3">
                            
                            <form method="GET" action="<?php echo e(url()->current()); ?>" class="flex items-center gap-2">
                                <input type="hidden" name="active_bolum" value="<?php echo e($bolum->id); ?>">
                                <input type="date" name="iaa_start_date" value="<?php echo e(request('iaa_start_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="text-gray-400 text-xs">-</span>
                                <input type="date" name="iaa_end_date" value="<?php echo e(request('iaa_end_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <button type="submit" class="p-1.5 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                            </form>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-100 px-2.5 py-1 rounded-full"><?php echo e($bData['bolum_iaa_projeleri']->count()); ?> Kayıt</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Proje Başlığı</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Gönderen / Takım</th>
                                    <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $bData['bolum_iaa_projeleri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php 
                                        $rowKey = 'all';
                                        if (in_array($iaa->durum, ['Havuzda', 'Onay Bekliyor'])) $rowKey = 'yeni';
                                        elseif (in_array($iaa->durum, ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'talep_onayi_bekliyor_kalite'])) $rowKey = 'islemde';
                                        elseif (in_array($iaa->durum, ['Tamamlandı', 'Talep Olarak Kapatıldı', 'talep_olarak_kapatildi', 'Reddedildi'])) $rowKey = 'tamamlanan';
                                        
                                        $isDelayed = ($iaa->talepEdenTakimlar->isNotEmpty() && $iaa->talepEdenTakimlar->first()->pivot->due_date < now() && !in_array($iaa->durum, ['Tamamlandı', 'Talep Olarak Kapatıldı', 'talep_olarak_kapatildi', 'Reddedildi', 'İptal Edildi']));
                                    ?>
                                    <tr x-show="(currentFilter === 'all' && (showAllIaa || <?php echo e($loop->index); ?> < 5)) || currentFilter !== 'all'" 
                                        class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='<?php echo e(route('admin.iaa-yonetim.index', ['search' => $iaa->baslik])); ?>'">
                                        <td class="px-6 py-4 whitespace-nowrap max-w-[300px]">
                                            <div class="text-sm font-bold text-gray-900 truncate" title="<?php echo e($iaa->baslik); ?>"><?php echo e(Str::limit($iaa->baslik, 60)); ?></div>
                                            <div class="text-[11px] text-gray-500 line-clamp-1"><?php echo e(Str::limit($iaa->mevcut_durum, 50)); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex flex-col">
                                                <span class="font-bold"><?php echo e($iaa->gonderen->name ?? 'Misafir'); ?></span>
                                                <span class="text-[10px] text-gray-400"><?php echo e($iaa->atananTakim->ad ?? 'Takım Atanmadı'); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <?php echo $iaa->durum_etiketi; ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs text-gray-500">
                                            <?php echo e($iaa->created_at->diffForHumans()); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic font-medium">Bu bölüme ait İAA projesi bulunamadı.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(count($bData['bolum_iaa_projeleri']) > 5): ?>
                        <div class="px-6 py-3 border-t border-indigo-50 bg-indigo-50/10 flex justify-center">
                            <button @click="showAllIaa = !showAllIaa" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition uppercase tracking-widest flex items-center gap-1">
                                <span x-text="showAllIaa ? 'Daha Az Göster' : 'Devamını Göster (<?php echo e(count($bData['bolum_iaa_projeleri']) - 5); ?> Kayıt Daha)'"></span>
                                <svg class="w-4 h-4 transition-transform" :class="showAllIaa ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bölüm Disiplin Olayları Listesi -->
                <div id="bolum-disiplin-tablosu-<?php echo e($bolum->id); ?>" 
                     class="bg-white rounded-2xl border border-orange-100 shadow-sm overflow-hidden scroll-mt-24 mt-8"
                     x-data="{ showAllDisiplin: false }">
                    <div class="px-6 py-4 border-b border-orange-50 bg-orange-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                             <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Bölüm Disiplin Olayları (<?php echo e($bolum->ad); ?>)
                        </h4>
                        <div class="flex flex-wrap items-center gap-3">
                            
                            <form method="GET" action="<?php echo e(url()->current()); ?>" class="flex items-center gap-2">
                                <input type="hidden" name="active_bolum" value="<?php echo e($bolum->id); ?>">
                                <input type="date" name="disiplin_start_date" value="<?php echo e(request('disiplin_start_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-orange-500 focus:border-orange-500">
                                <span class="text-gray-400 text-xs">-</span>
                                <input type="date" name="disiplin_end_date" value="<?php echo e(request('disiplin_end_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-orange-500 focus:border-orange-500">
                                <button type="submit" class="p-1.5 bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                            </form>
                            <span class="text-xs font-bold text-orange-600 bg-orange-100 px-2.5 py-1 rounded-full"><?php echo e($bData['bolum_disiplin_olaylari']->count()); ?> Kayıt</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Personel</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Olay / İhlal</th>
                                    <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Olay Tarihi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $bData['bolum_disiplin_olaylari'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr x-show="showAllDisiplin || <?php echo e($loop->index); ?> < 5" class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='<?php echo e(route('disiplin.show', $case->id)); ?>'">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <img class="h-8 w-8 rounded-full object-cover" src="<?php echo e($case->user->profile_photo_path ? asset('storage/'.$case->user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($case->user->name).'&color=7F9CF5&background=EBF4FF'); ?>" alt="">
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-bold text-gray-900"><?php echo e($case->user->name); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo e($case->behavior->name ?? 'Belirtilmedi'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-xs">
                                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 font-bold border border-gray-200">
                                                <?php echo e($case->durum); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs text-gray-500">
                                            <?php echo e($case->olay_tarihi ? $case->olay_tarihi->format('d.m.Y') : '-'); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic font-medium">Bu bölüme ait disiplin olayı bulunamadı.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(count($bData['bolum_disiplin_olaylari']) > 5): ?>
                        <div class="px-6 py-3 border-t border-orange-50 bg-orange-50/10 flex justify-center">
                            <button @click="showAllDisiplin = !showAllDisiplin" class="text-[11px] font-bold text-orange-600 hover:text-orange-800 transition uppercase tracking-widest flex items-center gap-1">
                                <span x-text="showAllDisiplin ? 'Daha Az Göster' : 'Devamını Göster (<?php echo e(count($bData['bolum_disiplin_olaylari']) - 5); ?> Kayıt Daha)'"></span>
                                <svg class="w-4 h-4 transition-transform" :class="showAllDisiplin ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if($bData['bolum_disiplin_olaylari']->count() > 10): ?>
                        <div class="px-6 py-3 bg-gray-50 border-t border-orange-50 text-center">
                            <a href="<?php echo e(route('admin.disiplin.index')); ?>" class="text-sm font-bold text-orange-600 hover:text-orange-800 transition flex items-center justify-center gap-1">
                                <span>Tümünü Gör (Toplam <?php echo e($bData['bolum_disiplin_olaylari']->count()); ?> Kayıt)</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bölüm Personelleri Listesi (SEKMELİ: Beyaz Yaka / Mavi Yaka) -->
                <div id="bolum-personelleri-tablosu-<?php echo e($bolum->id); ?>" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible mt-8 scroll-mt-24" x-data="{ personelTab_<?php echo e($bolum->id); ?>: 'beyaz', showAllBeyaz: false }">
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Bölüm Personelleri (<?php echo e($bolum->ad); ?>)
                            </h4>
                            <span class="text-xs font-medium text-gray-500"><?php echo e($bData['personel_listesi']->count()); ?> İsim</span>
                        </div>
                        
                        <div class="flex bg-white rounded-lg p-0.5 border border-gray-200 gap-1">
                            <button @click="personelTab_<?php echo e($bolum->id); ?> = 'beyaz'"
                                :class="personelTab_<?php echo e($bolum->id); ?> === 'beyaz' ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                class="flex-1 px-3 py-1.5 rounded-md text-xs transition flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Beyaz Yaka Personel
                            </button>
                            <button @click="personelTab_<?php echo e($bolum->id); ?> = 'mavi'"
                                :class="personelTab_<?php echo e($bolum->id); ?> === 'mavi' ? 'bg-blue-50 text-blue-700 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                class="flex-1 px-3 py-1.5 rounded-md text-xs transition flex items-center justify-center gap-1">
                                <span class="inline-flex items-center justify-center w-4 h-4 bg-blue-500 rounded-full text-white text-[8px] font-black">MY</span>
                                Mavi Yaka
                            </button>
                        </div>
                    </div>

                    
                    <div x-show="personelTab_<?php echo e($bolum->id); ?> === 'beyaz'" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Personel</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Rol / Görev</th>
                                    <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Aktif Projeler</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $bData['personel_listesi']->where('is_mavi_yaka', false); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $personel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php 
                                        $isLider = ($bolum->lider_user_id && $personel->id == $bolum->lider_user_id);
                                        $isOnline = $personel->isOnline();
                                    ?>
                                    <tr x-show="showAllBeyaz || <?php echo e($loop->index); ?> < 5" class="<?php echo e($isLider ? 'bg-amber-50/70 border-l-4 border-amber-400 sticky top-0 z-20' : 'hover:bg-gray-50'); ?> transition-all border-b border-gray-100 last:border-0">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-11 w-11 relative">
                                                    <img class="h-11 w-11 rounded-full border-2 <?php echo e($isLider ? 'border-amber-500 shadow-sm' : 'border-gray-100'); ?> object-cover" 
                                                         src="<?php echo e($personel->profile_photo_path ? asset('storage/'.$personel->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($personel->name).'&color=7F9CF5&background=EBF4FF'); ?>" 
                                                         alt="<?php echo e($personel->name); ?>">
                                                    <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full ring-2 ring-white <?php echo e($isOnline ? 'bg-green-500' : 'bg-gray-300'); ?>"></span>
                                                </div>
                                                <div class="ml-4">
                                                    <a href="<?php echo e(route('profile.show', $personel->id)); ?>" class="<?php echo e($isLider ? 'text-lg font-black text-amber-900' : 'text-sm font-bold text-gray-900'); ?> hover:underline flex items-center gap-2">
                                                        <?php echo e($personel->name); ?>

                                                        <?php if($isLider): ?>
                                                            <svg class="w-5 h-5 text-amber-500 drop-shadow-sm" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z" />
                                                            </svg>
                                                        <?php endif; ?>
                                                    </a>
                                                    <div class="text-[12px] <?php echo e($isLider ? 'text-amber-700/70 font-bold' : 'text-gray-500 font-medium'); ?>"><?php echo e($personel->email); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                <?php
                                                    $roleColors = [
                                                        'Superadmin' => 'bg-red-50 text-red-700 border-red-100',
                                                        'Yonetim' => 'bg-purple-50 text-purple-700 border-purple-100',
                                                        'Müşteri Şikayeti Kurulu' => 'bg-orange-50 text-orange-700 border-orange-100',
                                                        'Bölüm Lideri' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                                        'Bölüm Kalite Yöneticisi' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                        'Müşteri Şikayeti Çözüm Lideri' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                        'Kullanıcı' => 'bg-gray-50 text-gray-600 border-gray-100',
                                                    ];
                                                ?>
                                                <?php $__currentLoopData = $personel->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $colorClass = $roleColors[$role->name] ?? 'bg-gray-50 text-gray-500 border-gray-100'; ?>
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold <?php echo e($colorClass); ?> border uppercase tracking-tight">
                                                        <?php echo e($role->name); ?>

                                                    </span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div x-data="{ open: false }" class="relative inline-block">
                                                <button @click="open = !open" @click.away="open = false" 
                                                   class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold transition-all
                                                   <?php echo e($personel->gorevli_oldugu_projeler_count > 0 ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200 cursor-pointer' : 'bg-gray-100 text-gray-400 cursor-default'); ?>">
                                                    <?php echo e($personel->gorevli_oldugu_projeler_count); ?>

                                                </button>

                                                <?php if($personel->gorevli_oldugu_projeler_count > 0): ?>
                                                    <div x-show="open" 
                                                         x-transition:enter="transition ease-out duration-200" 
                                                         x-transition:enter-start="opacity-0 scale-95" 
                                                         x-transition:enter-end="opacity-100 scale-100"
                                                         style="display: none;"
                                                         class="absolute z-[100] mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 p-3 text-left
                                                                <?php if($loop->last): ?> bottom-full mb-3 <?php else: ?> top-full <?php endif; ?> left-1/2 -translate-x-1/2">
                                                        
                                                        <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-50">
                                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Aktif Projeler</span>
                                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-600 font-bold"><?php echo e($personel->gorevli_oldugu_projeler_count); ?> Adet</span>
                                                        </div>

                                                        <div class="max-h-56 overflow-y-auto space-y-1.5 custom-scrollbar pr-1">
                                                            <?php $__currentLoopData = $personel->gorevliOlduguProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <a href="<?php echo e(route('proje.workspace.show', $p->id)); ?>" class="block p-2.5 rounded-lg border border-transparent hover:border-indigo-100 hover:bg-indigo-50/30 transition-all group">
                                                                    <div class="text-[11px] font-bold text-gray-800 leading-tight group-hover:text-indigo-700 line-clamp-2"><?php echo e($p->baslik); ?></div>
                                                                        <div class="scale-75 origin-left">
                                                                            <?php echo $p->durum_etiketi; ?>

                                                                        </div>
                                                                </a>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                        
                                                        <?php if($loop->last): ?>
                                                            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-b border-r border-gray-100 rotate-45"></div>
                                                        <?php else: ?>
                                                            <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                            <?php if($isOnline): ?>
                                                <span class="text-green-600 font-bold flex items-center justify-end gap-1">
                                                    <span class="relative flex h-2 w-2">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                    </span>
                                                    Çevrimiçi
                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-400">
                                                    Son görülme: <?php echo e($personel->last_seen_at ? $personel->last_seen_at->diffForHumans() : 'Yok'); ?>

                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic font-medium">Bu bölüme ait beyaz yaka personel bulunamadı.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php $beyazCount = $bData['personel_listesi']->where('is_mavi_yaka', false)->count(); ?>
                    <?php if($beyazCount > 5): ?>
                        <div x-show="personelTab_<?php echo e($bolum->id); ?> === 'beyaz'" class="px-6 py-3 border-t border-gray-50 bg-gray-50/10 flex justify-center">
                            <button @click="showAllBeyaz = !showAllBeyaz" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition uppercase tracking-widest flex items-center gap-1">
                                <span x-text="showAllBeyaz ? 'Daha Az Göster' : 'Devamını Göster (<?php echo e($beyazCount - 5); ?> Personel Daha)'"></span>
                                <svg class="w-4 h-4 transition-transform" :class="showAllBeyaz ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    <?php endif; ?>

                    
                    <div x-show="personelTab_<?php echo e($bolum->id); ?> === 'mavi'" style="display:none;" class="overflow-x-auto max-h-96 overflow-y-auto custom-scrollbar">
                        <?php
                            $maviYakalarDirektor = $bData['personel_listesi']->where('is_mavi_yaka', true);
                        ?>
                        <?php if($maviYakalarDirektor->isEmpty()): ?>
                            <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                                <span class="text-4xl mb-2">👷</span>
                                <p class="text-sm">Bu bölümde mavi yaka personeli yok.</p>
                            </div>
                        <?php else: ?>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-blue-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-blue-500 uppercase tracking-wider">Personel</th>
                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-blue-500 uppercase tracking-wider">Unvan</th>
                                        <th class="px-6 py-3 text-center text-[10px] font-bold text-blue-500 uppercase tracking-wider">Puan</th>
                                        <th class="px-6 py-3 text-right text-[10px] font-bold text-blue-500 uppercase tracking-wider">Durum</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <?php $__currentLoopData = $maviYakalarDirektor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $my): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $isOnline = $my->isOnline(); ?>
                                        <tr class="hover:bg-blue-50/30 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 relative">
                                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">
                                                            <?php echo e(strtoupper(substr($my->name, 0, 2))); ?>

                                                        </div>
                                                        <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white <?php echo e($isOnline ? 'bg-green-500' : 'bg-gray-300'); ?>"></span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-bold text-gray-900"><?php echo e($my->name); ?></div>
                                                        <div class="text-[11px] text-gray-500"><?php echo e($my->email); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-blue-600 font-medium"><?php echo e($my->unvan ?? 'Mavi Yaka'); ?></span>
                                                <?php if($my->sicil_no): ?>
                                                    <div class="text-[10px] text-gray-400">Sicil: <?php echo e($my->sicil_no); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="font-bold text-blue-700"><?php echo e($my->cached_total_score ?? 0); ?> P</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                                <?php if($isOnline): ?>
                                                    <span class="text-green-600 font-bold flex items-center justify-end gap-1">
                                                        <span class="relative flex h-2 w-2">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                        </span>
                                                        Çevrimiçi
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-gray-400">
                                                        Son görülme: <?php echo e($my->last_seen_at ? $my->last_seen_at->diffForHumans() : 'Yok'); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bölüm Personel Bekleyen Görevler Listesi -->
                <div id="bolum-personel-gorevleri-tablosu-<?php echo e($bolum->id); ?>" class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden scroll-mt-24 mt-8" x-data="{ showAllTasks: false }">
                    <div class="px-6 py-4 border-b border-blue-50 bg-blue-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Personellerin Bekleyen Görevleri (<?php echo e($bolum->ad); ?>)
                        </h4>
                        <div class="flex flex-wrap items-center gap-3">
                            
                            <form method="GET" action="<?php echo e(url()->current()); ?>" class="flex items-center gap-2">
                                <input type="hidden" name="active_bolum" value="<?php echo e($bolum->id); ?>">
                                <input type="date" name="gorev_start_date" value="<?php echo e(request('gorev_start_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-blue-500 focus:border-blue-500">
                                <span class="text-gray-400 text-xs">-</span>
                                <input type="date" name="gorev_end_date" value="<?php echo e(request('gorev_end_date')); ?>" class="text-[10px] border-gray-200 rounded-lg px-2 py-1 focus:ring-blue-500 focus:border-blue-500">
                                <button type="submit" class="p-1.5 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                            </form>
                            <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2.5 py-1 rounded-full"><?php echo e($bData['bolum_personel_gorevleri']->count()); ?> Görev</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Görev / Proje</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Sorumlu(lar)</th>
                                    <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Son Güncelleme</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $bData['bolum_personel_gorevleri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gorev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php 
                                        $isSikayet = $gorev->musteri_sikayeti_id ? true : false;
                                        $targetRoute = $isSikayet ? route('admin.sikayetler.show', $gorev->musteri_sikayeti_id) : route('proje.workspace.show', $gorev->id);
                                    ?>
                                    <tr x-show="showAllTasks || <?php echo e($loop->index); ?> < 5" class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='<?php echo e($targetRoute); ?>'">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <div class="text-sm font-bold text-gray-900 line-clamp-1"><?php echo e($gorev->baslik); ?></div>
                                                <div class="text-[10px] text-gray-400 flex items-center gap-1 mt-0.5">
                                                    <?php if($isSikayet): ?>
                                                        <span class="px-1.5 py-0.5 rounded bg-red-50 text-red-600 font-bold border border-red-100 uppercase text-[8px]">Müşteri Şikayeti</span>
                                                    <?php else: ?>
                                                        <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 font-bold border border-blue-100 uppercase text-[8px]">İAA Projesi</span>
                                                    <?php endif; ?>
                                                    <span><?php echo e($gorev->atananTakim->ad ?? 'Takım Atanmadı'); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                            <div class="flex flex-wrap gap-2 items-center">
                                                <?php if($gorev->aktifAdim && $gorev->aktifAdim->sorumlular->isNotEmpty()): ?>
                                                    <?php $__currentLoopData = $gorev->aktifAdim->sorumlular; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sorumlu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <a href="<?php echo e(route('profile.show', $sorumlu->id)); ?>" class="flex items-center gap-2 group/person">
                                                            <img class="h-6 w-6 rounded-full ring-2 ring-white object-cover" 
                                                                 src="<?php echo e($sorumlu->profile_photo_path ? asset('storage/'.$sorumlu->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($sorumlu->name).'&color=7F9CF5&background=EBF4FF'); ?>" 
                                                                 title="<?php echo e($sorumlu->name); ?>">
                                                            <span class="text-xs text-gray-600 group-hover/person:text-indigo-600 font-medium whitespace-nowrap"><?php echo e($sorumlu->name); ?></span>
                                                        </a>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php else: ?>
                                                    <span class="text-xs text-gray-400">Atanmadı</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs text-gray-400">
                                            <?php echo e($gorev->updated_at->diffForHumans()); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Bu bölüme ait bekleyen personel görevi bulunamadı.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php if(count($bData['bolum_personel_gorevleri']) > 5): ?>
                            <div class="px-6 py-3 border-t border-blue-50 bg-blue-50/10 flex justify-center">
                                <button @click="showAllTasks = !showAllTasks" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition uppercase tracking-widest flex items-center gap-1">
                                    <span x-text="showAllTasks ? 'Daha Az Göster' : 'Devamını Göster (<?php echo e(count($bData['bolum_personel_gorevleri']) - 5); ?> Görev Daha)'"></span>
                                    <svg class="w-4 h-4 transition-transform" :class="showAllTasks ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($bData['bolum_personel_gorevleri']->count() >= 15): ?>
                        <div class="px-6 py-3 bg-gray-50 border-t border-blue-50 text-center">
                            <span class="text-[10px] text-gray-400 font-medium">Sadece en güncel 15 görev gösterilmektedir.</span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if(($bData['dagilim']['iaa']['onay_bekleyen'] ?? 0) > 0 || ($bData['dagilim']['sikayet']['onay_bekleyen'] ?? 0) > 0): ?>
                <div id="onay-bekleyen-projeler-<?php echo e($bolum->id); ?>" class="bg-orange-50 rounded-2xl p-6 border border-orange-100 shadow-sm animate-pulse-subtle scroll-mt-24">
                     <h4 class="font-bold text-orange-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Onayınızı Bekleyen Projeler
                     </h4>
                     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php $__currentLoopData = collect($bData['list']['iaa']['onay_bekleyen'] ?? [])->merge($bData['list']['sikayet']['onay_bekleyen'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                if ($item instanceof \App\Models\MusteriSikayeti) {
                                    $isSikayet = true;
                                    $targetRoute = route('admin.sikayetler.show', $item->id);
                                    $displayTitle = $item->musteri_sikayet_konusu ?? "Şikayet #{$item->id}";
                                } else {
                                    // Iaa Modeli
                                    $linkedSikayet = $item->musteriSikayeti;
                                    $isSikayet = $linkedSikayet !== null;
                                    $targetRoute = $isSikayet ? route('admin.sikayetler.show', $linkedSikayet->id) : route('admin.iaa-yonetim.index');
                                    $displayTitle = $item->baslik;
                                }
                            ?>
                            <a href="<?php echo e($targetRoute); ?>" class="group block bg-white border border-orange-200 p-4 rounded-xl hover:shadow-lg transition">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block mb-1">
                                    <?php echo e($isSikayet ? 'Müşteri Şikayeti Kaynaklı' : 'İAA Önerisi'); ?>

                                </span>
                                <p class="text-sm font-bold text-gray-900 line-clamp-2 group-hover:text-indigo-600 transition"><?php echo e($displayTitle); ?></p>
                                <div class="mt-2">
                                    <?php echo $item instanceof \App\Models\MusteriSikayeti ? ($item->iaaProjesi ? $item->iaaProjesi->durum_etiketi : '') : $item->durum_etiketi; ?>

                                </div>
                                <div class="mt-3 flex items-center justify-between text-[11px] text-gray-500">
                                    <span><?php echo e($item->updated_at->diffForHumans()); ?></span>
                                    <span class="font-bold text-indigo-500"><?php echo e($isSikayet ? 'Detaya Git' : 'Yönetime Git'); ?> &rarr;</span>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     </div>
                </div>
                <?php endif; ?>


            <?php else: ?>
                <div class="text-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-200 text-gray-400">
                    <p>Bu bölüme ait istatistik verisi bulunamadı.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- ALT KISIM: KLASİK DASHBOARD KARTLARI (Kişisel Durum) -->
    <div x-show="mainTab === 'kisisel'" x-cloak x-transition:enter="transition-opacity duration-300" class="space-y-8 animate-fade-in">
    <div class="space-y-6">
        <!-- Toplam Puan Tablosu -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                <h4 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Toplam Puanınız
                </h4>
            </div>
            <div class="p-6 text-center">
                <div class="text-3xl font-extrabold text-indigo-600"><?php echo e($stats['toplam_puan'] ?? 0); ?></div>
                <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mt-1">Puan</div>
            </div>
        </div>

        <?php echo $__env->make('dashboard.partials._users-activity', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <h4 class="font-bold text-xl text-gray-800 flex items-center gap-2">
             <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
             Kişisel Durum Özeti
        </h4>
        <?php echo $__env->make('dashboard.partials.standart-kullanici', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    


    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
    
    .animate-pulse-subtle {
        animation: pulseSubtle 3s infinite;
    }
    @keyframes pulseSubtle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }

    @keyframes fastPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.95); }
    }
    .animate-fast-pulse {
        animation: fastPulse 0.7s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    .custom-scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .custom-scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('draggable-tabs');
        if (el) {
            Sortable.create(el, {
                animation: 150,
                ghostClass: 'bg-indigo-50',
                onEnd: function () {
                    const order = Array.from(el.querySelectorAll('button')).map(btn => btn.dataset.id);
                    
                    fetch('<?php echo e(route("dashboard.save-tab-order")); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Sekme sıralaması kaydedildi.');
                        }
                    })
                    .catch(error => console.error('Hata:', error));
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/direktor.blade.php ENDPATH**/ ?>