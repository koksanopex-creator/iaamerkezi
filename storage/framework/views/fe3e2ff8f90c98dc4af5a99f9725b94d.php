<div class="space-y-8">

    <?php if(isset($tumBolumler) && $tumBolumler->count() > 0): ?>
    <?php if(isset($seciliBolumId) && $seciliBolumId): ?>
        <?php $seciliBolumLabel = $tumBolumler->firstWhere('id', $seciliBolumId); ?>
        <div class="flex items-center gap-2 mb-3 px-1 animate-fade-in">
            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] bg-indigo-50 px-2 py-1 rounded-lg border border-indigo-100 italic">Aktif Filtre:</span>
            <span class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-full shadow-md shadow-indigo-100 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                <?php echo e($seciliBolumLabel->ad ?? '-'); ?>

            </span>
        </div>
    <?php endif; ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6" x-data="{ 
        search: '',
        isOpen: false
    }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl flex items-center justify-center shadow-indigo-100 shadow-xl transform -rotate-3 hover:rotate-0 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div class="cursor-pointer" @click="isOpen = !isOpen">
                    <h3 class="text-lg font-black text-gray-900 tracking-tight flex items-center gap-2">
                        Bölüm Filtresi
                        <svg class="w-5 h-5 text-indigo-500 transition-transform duration-300" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </h3>
                    <div class="flex items-center gap-2 mt-0.5">
                        <?php if(isset($seciliBolumId) && $seciliBolumId): ?>
                            <?php $seciliBolum = $tumBolumler->firstWhere('id', $seciliBolumId); ?>
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            <p class="text-xs text-indigo-600 font-bold">Seçili: <?php echo e($seciliBolum->ad ?? '-'); ?></p>
                        <?php else: ?>
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <p class="text-xs text-gray-500 font-medium">Toplam <?php echo e($tumBolumler->count()); ?> aktif birim listeleniyor</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="relative w-full md:w-80 group">
                <input type="text" x-model="search" @input="if(search.length > 0) isOpen = true" placeholder="Hızlıca birim bul..." 
                       class="w-full pl-12 pr-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-sm focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-300">
                <div class="absolute left-4 top-3.5 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="overflow-hidden transition-all duration-500" x-show="isOpen || search.length > 0" x-cloak
             x-transition:enter="transition-all ease-out duration-300"
             x-transition:enter-start="opacity-0 max-h-0"
             x-transition:enter-end="opacity-100 max-h-[2000px]"
             x-transition:leave="transition-all ease-in duration-200"
             x-transition:leave-start="opacity-100 max-h-[2000px]"
             x-transition:leave-end="opacity-0 max-h-0">
            
            <div class="mt-8 space-y-8 pt-6 border-t border-gray-50">
                
                <div class="flex flex-wrap gap-3 items-center">
                    <a href="<?php echo e(route('dashboard')); ?>"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 border shadow-sm
                       <?php echo e(!isset($seciliBolumId) || !$seciliBolumId ? 'bg-indigo-600 text-white border-transparent shadow-indigo-100' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                        Tüm Fabrika ve Birimler
                    </a>

                    <?php if(isset($seciliBolumId) && $seciliBolumId): ?>
                        <?php $seciliBolum = $tumBolumler->firstWhere('id', $seciliBolumId); ?>
                        <div class="flex items-center gap-3 px-6 py-2.5 bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 rounded-xl border border-indigo-100 shadow-sm animate-fade-in font-black text-sm">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            <?php echo e($seciliBolum->ad ?? 'Bilinmeyen Bölüm'); ?>

                            <a href="<?php echo e(route('dashboard')); ?>" class="ml-2 text-indigo-300 hover:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-10">
                    <?php
                        $groupedBolumler = $tumBolumler->groupBy(fn($b) => $b->kategori->ad ?? 'Genel / Diğer');
                    ?>

                    <?php $__currentLoopData = $groupedBolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategoriAd => $bolumler): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $jsonNames = json_encode($bolumler->pluck('ad')->toArray()); ?>
                        <div x-show="search === '' || <?php echo e($jsonNames); ?>.some(name => name.toLowerCase().includes(search.toLowerCase())) || '<?php echo e(strtolower($kategoriAd)); ?>'.includes(search.toLowerCase())"
                             class="space-y-4">
                            <div class="flex items-center justify-between border-b-2 border-slate-50 pb-3">
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                                    <span class="w-3 h-1 bg-indigo-500 rounded-full"></span>
                                    <?php echo e($kategoriAd); ?>

                                </h4>
                                <span class="text-[10px] font-bold text-slate-300 bg-slate-50 px-2 py-0.5 rounded-full"><?php echo e($bolumler->count()); ?></span>
                            </div>
                            <div class="grid grid-cols-1 gap-1.5">
                                <?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('dashboard', ['bolum_id' => $bolum->id])); ?>"
                                       x-show="search === '' || '<?php echo e(strtolower($bolum->ad)); ?>'.includes(search.toLowerCase()) || '<?php echo e(strtolower($kategoriAd)); ?>'.includes(search.toLowerCase())"
                                       class="group flex items-center justify-between px-4 py-2.5 rounded-xl text-sm transition-all duration-200
                                       <?php echo e((isset($seciliBolumId) && $seciliBolumId == $bolum->id) ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-100' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600'); ?>">
                                        <span class="truncate pr-4"><?php echo e($bolum->ad); ?></span>
                                        <svg class="w-4 h-4 transform transition-all duration-300 <?php echo e((isset($seciliBolumId) && $seciliBolumId == $bolum->id) ? 'opacity-100 rotate-90' : 'opacity-0 group-hover:opacity-100 group-hover:translate-x-1'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <div x-show="search !== ''" class="hidden" :class="{ 'hidden': false }">
                    <?php
                        $allNames = json_encode($tumBolumler->pluck('ad')->toArray());
                        $allKats = json_encode($tumBolumler->pluck('kategori.ad')->filter()->unique()->toArray());
                    ?>
                    <div x-show="!<?php echo e($allNames); ?>.some(n => n.toLowerCase().includes(search.toLowerCase())) && !<?php echo e($allKats); ?>.some(k => k.toLowerCase().includes(search.toLowerCase()))"
                         class="flex flex-col items-center justify-center py-10 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                        <p class="text-slate-500 font-bold">Aradığınız kriterlere uygun bölüm bulunamadı.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <!-- 1. ÜST SATIR: GENEL KAYNAKLAR (3 KART) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- KULLANICILAR -->
        <a href="<?php echo e(route('admin.users.index')); ?>" class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>
                    </div>
                    <?php if($stats['onay_bekleyen_kullanici'] > 0): ?>
                        <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm animate-pulse"><?php echo e($stats['onay_bekleyen_kullanici']); ?> Onay</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-blue-600/80">Toplam Kullanıcı</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['toplam_kullanici']); ?></h3>
                    </div>
                </div>
                <div class="space-y-2 pt-3 border-t border-blue-200/50">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Son Eklenenler</p>
                    <?php $__empty_1 = true; $__currentLoopData = $stats['son_kullanicilar']->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-xs">
                            <div class="flex items-center gap-2 truncate">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                                <span class="text-gray-700 font-medium truncate"><?php echo e(Str::limit($user->name, 15)); ?></span>
                            </div>
                            <span class="text-gray-500"><?php echo e($user->created_at->format('d.m')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-gray-400 italic">Kayıt yok.</p>
                    <?php endif; ?>
                </div>
            </div>
        </a>

        <!-- MÜŞTERİLER -->
        <a href="<?php echo e(route('admin.musteriler.index')); ?>" class="group relative bg-gradient-to-br from-cyan-50 to-sky-50 border border-cyan-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-600/5 to-sky-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-cyan-600/80">Toplam Müşteri</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['toplam_musteri'] ?? 0); ?></h3>
                    </div>
                </div>
                <div class="space-y-2 pt-3 border-t border-cyan-200/50">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Son Eklenenler</p>
                    <?php $__empty_1 = true; $__currentLoopData = $stats['son_musteriler_listesi'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-xs">
                            <div class="flex items-center gap-2 truncate">
                                <div class="w-1.5 h-1.5 rounded-full bg-cyan-400"></div>
                                <span class="text-gray-700 font-medium truncate"><?php echo e(Str::limit($customer->name, 15)); ?></span>
                            </div>
                            <span class="text-gray-500"><?php echo e($customer->created_at->format('d.m')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-gray-400 italic">Müşteri yok.</p>
                    <?php endif; ?>
                </div>
            </div>
        </a>

        <!-- TAKIMLAR -->
        <a href="<?php echo e(route('admin.takim-yonetim.index')); ?>" class="group relative bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-violet-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-purple-600/80">Toplam Takım</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['toplam_takim']); ?></h3>
                    </div>
                </div>
                <div class="space-y-2 pt-3 border-t border-purple-200/50">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Son Kurulan Takımlar</p>
                    <?php $__empty_1 = true; $__currentLoopData = $stats['son_takimlar']->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-xs">
                            <div class="flex items-center gap-2 truncate">
                                <div class="w-1.5 h-1.5 rounded-full bg-purple-400"></div>
                                <span class="text-gray-700 font-medium truncate"><?php echo e(Str::limit($takim->ad, 15)); ?></span>
                            </div>
                            <span class="bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold"><?php echo e($takim->uyeler_count); ?> üye</span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-gray-400 italic">Takım yok.</p>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>

    <!-- 2. ALT SATIR: SÜREÇLER VE ÖNERİLER (4 KART) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- MÜŞTERİ ŞİKAYETLERİ -->
        <a href="<?php echo e(route('admin.sikayetler.index')); ?>" class="group relative bg-gradient-to-br from-red-50 to-orange-50 border border-red-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-600/5 to-orange-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <?php if(($stats['onay_bekleyen_sikayet'] ?? 0) > 0): ?>
                            <span class="bg-purple-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse"><?php echo e($stats['onay_bekleyen_sikayet']); ?> Onay</span>
                        <?php endif; ?>
                        <?php if($stats['yeni_sikayet'] > 0): ?>
                            <span class="bg-yellow-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse"><?php echo e($stats['yeni_sikayet']); ?> Yeni</span>
                        <?php endif; ?>
                        <?php if($stats['islemde_sikayet'] > 0): ?>
                            <span class="bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse"><?php echo e($stats['islemde_sikayet']); ?> İşlemde</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-red-600/80">Toplam Müşteri Şikayeti</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['toplam_sikayet']); ?></h3>
                    </div>
                </div>
                <div class="space-y-2 pt-3 border-t border-red-200/50">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Son Gelen Şikayetler</p>
                    <?php $__empty_1 = true; $__currentLoopData = $stats['son_sikayetler']->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-xs">
                            <div class="flex items-center gap-2 truncate w-3/4">
                                <div class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></div>
                                <span class="text-gray-700 font-medium truncate" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>"><?php echo e($sikayet->musteri_sikayet_konusu); ?></span>
                            </div>
                            <span class="text-gray-500 flex-shrink-0"><?php echo e($sikayet->created_at->format('d.m')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-gray-400 italic">Şikayet yok.</p>
                    <?php endif; ?>
                </div>
            </div>
        </a>

        <!-- TOPLAM İAA ÖNERİLERİ (KONSOLİDE KART) -->
        <a href="<?php echo e(route('admin.iaa-yonetim.index')); ?>" class="group relative bg-gradient-to-br from-amber-50 to-emerald-50 border border-amber-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-600/5 to-emerald-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-emerald-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <?php if($stats['yeni_iaa_onerileri'] > 0): ?>
                            <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse"><?php echo e($stats['yeni_iaa_onerileri']); ?> Yeni Öneri</span>
                        <?php endif; ?>
                        <?php if($stats['onay_bekleyen_tamamlanmis_iaa'] > 0): ?>
                            <span class="bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse"><?php echo e($stats['onay_bekleyen_tamamlanmis_iaa']); ?> Onay Bekleyen</span>
                        <?php endif; ?>
                        <?php if(isset($stats['toplam_iaa_talep_sayisi']) && $stats['toplam_iaa_talep_sayisi'] > 0): ?>
                            <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse"><?php echo e($stats['toplam_iaa_talep_sayisi']); ?> Talep Edilen</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-amber-600/80">Toplam İAA Önerileri</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['toplam_iaa']); ?></h3>
                    </div>
                </div>
                <div class="space-y-2 pt-3 border-t border-amber-200/50">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Son Öneriler</p>
                    <?php $__empty_1 = true; $__currentLoopData = $stats['son_iaalar']->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-xs">
                            <div class="flex items-center gap-2 truncate">
                                <div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div>
                                <span class="text-gray-700 font-medium truncate"><?php echo e(Str::limit($iaa->baslik, 15)); ?></span>
                            </div>
                            <span class="text-gray-500"><?php echo e($iaa->created_at->format('d.m')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-gray-400 italic">Öneri yok.</p>
                    <?php endif; ?>
                </div>
            </div>
        </a>

        <!-- ARABULUCULUK -->
        <a href="<?php echo e(route('admin.arabuluculuk.index')); ?>" class="group relative bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600/5 to-amber-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-orange-600/80">Arabuluculuk</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['aktif_arabuluculuk']); ?> <span class="text-sm text-gray-500 font-normal">/ <?php echo e($stats['toplam_arabuluculuk']); ?></span></h3>
                    </div>
                </div>
                <div class="pt-3 border-t border-orange-200/50">
                    <p class="text-xs text-orange-600">Aktif Süreçler / Toplam</p>
                </div>
            </div>
        </a>

        <!-- DİSİPLİN -->
        <a href="<?php echo e(route('admin.disiplin.index')); ?>" class="group relative bg-gradient-to-br from-rose-50 to-pink-50 border border-rose-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-rose-600/5 to-pink-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-rose-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-rose-600/80">Disiplin Süreci</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['aktif_disiplin']); ?> <span class="text-sm text-gray-500 font-normal">/ <?php echo e($stats['toplam_disiplin']); ?></span></h3>
                    </div>
                </div>
                <div class="pt-3 border-t border-rose-200/50">
                    <p class="text-xs text-rose-600">Aktif Vakalar / Toplam</p>
                </div>
            </div>
        </a>

    </div>

    
    <?php echo $__env->make('dashboard.partials._users-activity', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- SON TAMAMLANAN IAA -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-purple-50 flex justify-between items-center"><h3 class="font-bold text-gray-800">Son Tamamlanan İAA (Saf) (<?php echo e($ekstraTablolar['son_tamamlanan_iaa']->count()); ?>)</h3><a href="<?php echo e(route('admin.iaa-yonetim.index')); ?>" onclick="localStorage.setItem('activeTab', 'tamamlananlar')" class="text-xs text-purple-600 hover:underline font-semibold cursor-pointer">Tümünü Gör →</a></div>
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-3">Proje</th><th class="px-4 py-3">Takım</th><th class="px-4 py-3 text-right">Tarih</th></tr></thead><tbody class="divide-y divide-gray-100"><?php $__empty_1 = true; $__currentLoopData = $ekstraTablolar['son_tamamlanan_iaa'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr class="bg-white hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-900"><a href="<?php echo e(route('proje.workspace.show', $iaa->id)); ?>" target="_blank" class="hover:text-purple-600 hover:underline block truncate max-w-[180px]"><?php echo e(Str::limit($iaa->baslik, 25)); ?></a></td><td class="px-4 py-3 text-xs"><?php echo e($iaa->atananTakim->ad ?? '-'); ?></td><td class="px-4 py-3 text-right text-xs"><?php echo e($iaa->updated_at->format('d.m.Y')); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr><?php endif; ?></tbody></table>
        </div>
        <!-- SON ÇÖZÜLEN ŞİKAYETLER (MÜŞTERİ EKLENDİ) -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-red-50 flex justify-between items-center"><h3 class="font-bold text-gray-800">Son Çözülen Şikayetler (<?php echo e($ekstraTablolar['son_cozulen_sikayetler']->count()); ?>)</h3><a href="<?php echo e(route('admin.sikayetler.index')); ?>" class="text-xs text-red-600 hover:underline font-semibold">Tümünü Gör →</a></div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Konu</th>
                        <th class="px-4 py-3">Müşteri</th>
                        <th class="px-4 py-3">Takım</th>
                        <th class="px-4 py-3 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $ekstraTablolar['son_cozulen_sikayetler'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" target="_blank" class="hover:text-red-600 hover:underline block truncate max-w-[150px]"><?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 20)); ?></a>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <?php if($sikayet->customer): ?>
                                    <?php echo e($sikayet->customer->name); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs"><?php echo e($sikayet->cozumTakimi->ad ?? '-'); ?></td>
                            <td class="px-4 py-3 text-right text-xs"><?php echo e($sikayet->updated_at->format('d.m.Y')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SON KURULAN TAKIMLAR (SEKMELİ) -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden" x-data="{ activeTab: 'iaa' }">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Son Kurulan Takımlar</h3>
                <div class="flex space-x-2 text-xs">
                    <button @click="activeTab = 'iaa'" :class="activeTab === 'iaa' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-3 py-1 rounded-md transition-colors font-semibold shadow-sm border border-transparent">
                        Bireysel (İAA)
                    </button>
                    <button @click="activeTab = 'sikayet'" :class="activeTab === 'sikayet' ? 'bg-purple-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-3 py-1 rounded-md transition-colors font-semibold shadow-sm border border-transparent">
                        Şikayet Çözüm
                    </button>
                </div>
            </div>
            
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Takım</th>
                        <th class="px-4 py-3">Lider</th>
                        <th class="px-4 py-3 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody x-show="activeTab === 'iaa'" class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $ekstraTablolar['son_iaa_takimlari'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900 truncate max-w-[150px]">
                                <?php echo e($takim->ad); ?>

                            </td>
                            <td class="px-4 py-3 text-xs">
                                <?php echo e($takim->lider->name ?? '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-right text-xs"><?php echo e($takim->created_at->format('d.m.Y')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">İAA takımı bulunamadı.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tbody x-show="activeTab === 'sikayet'" style="display: none;" class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $ekstraTablolar['son_sikayet_takimlari'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900 truncate max-w-[150px]">
                                <?php echo e($takim->ad); ?>

                            </td>
                            <td class="px-4 py-3 text-xs">
                                <?php echo e($takim->lider->name ?? '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-right text-xs"><?php echo e($takim->created_at->format('d.m.Y')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">Şikayet takımı bulunamadı.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- SON DİSİPLİN VAKALARI -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-rose-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Son Disiplin Vakaları</h3>
                <a href="<?php echo e(route('admin.disiplin.index')); ?>" class="text-xs text-rose-600 hover:underline font-semibold">Tümünü Gör →</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Kişi</th>
                        <th class="px-4 py-3 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $ekstraTablolar['son_disiplin_vakalari'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900 truncate max-w-[150px]">
                                <?php echo e($case->user->name ?? '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-right text-xs"><?php echo e($case->created_at->format('d.m.Y')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="2" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if(isset($iadeVerileri)): ?>
        <div class="col-span-1 xl:col-span-2">
            <?php echo $__env->make('dashboard.partials.iadeler-tablosu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    <?php endif; ?>


    
    <div class="animate-fade-in-up">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard.super-admin-visit-table');

$__html = app('livewire')->mount($__name, $__params, 'lw-3409301054-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>

    <!-- SON KAZANILAN PUANLAR -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-emerald-500 text-white"><h3 class="font-bold">Son Kazanılan Puanlar</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">TİP</th>
                        <th class="px-6 py-3">AÇIKLAMA</th>
                        <th class="px-6 py-3">Kişi / Takım</th>
                        <th class="px-6 py-3 text-right">PUAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $ekstraTablolar['son_kazanilan_puanlar'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $puan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold <?php echo e($puan['badge_color']); ?>"><?php echo e($puan['tip']); ?></span></td>
                            <td class="px-6 py-4 truncate max-w-[200px]"><?php echo e($puan['baslik']); ?></td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-700"><?php echo e($puan['user'] ? $puan['user']->name : '-'); ?></td>
                            <td class="px-6 py-4 text-right"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-bold">+<?php echo e($puan['puan']); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">Kayıt yok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php echo $__env->make('dashboard.partials.top5-performers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('dashboard.partials.birthdays', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/superadmin.blade.php ENDPATH**/ ?>