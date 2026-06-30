<div class="space-y-8">

    
    <div class="space-y-4">
        
        
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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-data="{ 
            search: '',
            isOpen: false
        }">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl flex items-center justify-center shadow-indigo-100 shadow-xl transform -rotate-2">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div class="cursor-pointer" @click="isOpen = !isOpen">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight flex items-center gap-2">
                            Bölüm Filtresi
                            <svg class="w-4 h-4 text-indigo-500 transition-transform duration-300" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <?php if(isset($seciliBolumId) && $seciliBolumId): ?>
                                <?php $seciliBolum = $tumBolumler->firstWhere('id', $seciliBolumId); ?>
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                <p class="text-xs text-indigo-600 font-bold">Seçili: <?php echo e($seciliBolum->ad ?? '-'); ?></p>
                            <?php else: ?>
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                <p class="text-xs text-gray-500 font-medium">Birim bazlı performans takibi</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="relative w-full md:w-80 group">
                    <input type="text" x-model="search" @input="if(search.length > 0) isOpen = true" placeholder="Birim ara..." 
                           class="w-full pl-12 pr-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-sm focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-300">
                    <div class="absolute left-4 top-3.5 text-gray-400 group-focus-within:text-indigo-600">
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
                        <a href="<?php echo e(route('dashboard', request()->except('bolum_id'))); ?>"
                           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 border
                           <?php echo e(!isset($seciliBolumId) || !$seciliBolumId ? 'bg-indigo-600 text-white border-transparent shadow-lg shadow-indigo-100' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'); ?>">
                            Genel Toplam (Tümü)
                        </a>

                        <?php if(isset($seciliBolumId) && $seciliBolumId): ?>
                            <?php $seciliBolum = $tumBolumler->firstWhere('id', $seciliBolumId); ?>
                            <div class="flex items-center gap-3 px-6 py-2.5 bg-indigo-50 text-indigo-700 rounded-xl border border-indigo-100 shadow-sm font-black text-sm">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <?php echo e($seciliBolum->ad ?? 'Seçili Birim'); ?>

                                <a href="<?php echo e(route('dashboard', request()->except('bolum_id'))); ?>" class="ml-2 text-indigo-300 hover:text-indigo-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                        <?php
                            $groupedBolumler = $tumBolumler->groupBy(fn($b) => $b->kategori->ad ?? 'Diğer');
                        ?>

                        <?php $__currentLoopData = $groupedBolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategoriAd => $bolumler): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $jsonNames = json_encode($bolumler->pluck('ad')->toArray()); ?>
                            <div x-show="search === '' || <?php echo e($jsonNames); ?>.some(name => name.toLowerCase().includes(search.toLowerCase())) || '<?php echo e(strtolower($kategoriAd)); ?>'.includes(search.toLowerCase())"
                                 class="space-y-4">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <span class="w-2 h-2 bg-indigo-400 rounded-sm"></span>
                                    <?php echo e($kategoriAd); ?>

                                </h4>
                                <div class="flex flex-col gap-1.5">
                                    <?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(route('dashboard', array_merge(request()->all(), ['bolum_id' => $bolum->id]))); ?>"
                                           x-show="search === '' || '<?php echo e(strtolower($bolum->ad)); ?>'.includes(search.toLowerCase()) || '<?php echo e(strtolower($kategoriAd)); ?>'.includes(search.toLowerCase())"
                                           class="group flex items-center justify-between px-4 py-2 rounded-xl text-sm transition-all duration-200
                                           <?php echo e((isset($seciliBolumId) && $seciliBolumId == $bolum->id) ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'); ?>">
                                            <span class="truncate"><?php echo e($bolum->ad); ?></span>
                                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>



        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <form action="<?php echo e(route('dashboard')); ?>" method="GET" class="flex flex-wrap items-end gap-4">
                <?php if(request('bolum_id')): ?> <input type="hidden" name="bolum_id" value="<?php echo e(request('bolum_id')); ?>"> <?php endif; ?>
                
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 px-1">Tarih Aralığı (Başlangıç)</label>
                    <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" 
                           class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all font-semibold text-gray-700">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 px-1">Tarih Aralığı (Bitiş)</label>
                    <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" 
                           class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all font-semibold text-gray-700">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-md hover:bg-indigo-700 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filtrele
                    </button>
                    <a href="<?php echo e(route('dashboard', request()->only('bolum_id'))); ?>" class="px-4 py-2 bg-slate-50 text-slate-500 rounded-xl text-sm font-bold hover:bg-slate-100 hover:text-slate-700 transition-all border border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Sıfırla
                    </a>
                </div>
            </form>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- PERSONEL -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded-full">Aktif</span>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Toplam Çalışan</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight"><?php echo e(number_format($stats['toplam_calisan'], 0)); ?></h3>
        </div>

        <!-- İAA PROJELERİ -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-2 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-20 h-20 text-indigo-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest"><?php echo e($stats['tamamlanan_proje']); ?> Tamamlanan</p>
                    <p class="text-[9px] text-gray-400 italic"><?php echo e($stats['devam_eden_proje']); ?> Devam Eden</p>
                </div>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">İAA Projeleri</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight"><?php echo e(number_format($stats['toplam_proje'], 0)); ?></h3>
        </div>

        <!-- MÜŞTERİ ŞİKAYETLERİ -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-2 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-20 h-20 text-red-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            </div>
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest"><?php echo e($stats['cozulen_sikayet']); ?> Çözüldü</p>
                    <p class="text-[9px] text-gray-400 italic"><?php echo e($stats['toplam_sikayet'] - $stats['cozulen_sikayet']); ?> Bekleyen</p>
                </div>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Müşteri Şikayetleri</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight"><?php echo e(number_format($stats['toplam_sikayet'], 0)); ?></h3>
        </div>

        <!-- ONLINE DURUMU (KISA ÖZET) -->
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-5 text-white relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-20">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-white/20 backdrop-blur-md rounded-lg flex items-center justify-center shadow-inner">
                    <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest">Sistem Durumu</span>
            </div>
            <p class="text-xs font-bold text-green-100 uppercase tracking-wider">Online Kullanıcı</p>
            <h3 class="text-3xl font-black tracking-tight"><?php echo e($stats['online_users_list']->count()); ?></h3>
            <p class="text-[9px] mt-1 text-green-50/70 font-medium">Anlık sistem kullanımı aktif</p>
        </div>
    </div>
        <div class="px-6 py-4 border-b border-gray-100 bg-green-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                Online Kullanıcılar & Son Aktiviteler
            </h3>
            <a href="<?php echo e(route('logs.login.index')); ?>"
                class="text-[10px] font-bold text-green-600 hover:text-green-800 uppercase tracking-widest bg-white/50 px-2 py-1 rounded border border-green-100 shadow-sm transition-all">&larr;
                Tüm Giriş Kayıtları</a>
            <span
                class="text-xs font-bold text-green-700 bg-white px-3 py-1 rounded-full border border-green-200 shadow-sm">
                <?php echo e($stats['online_users_list']->count()); ?> Kişi Aktif
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 divide-x divide-gray-100">
            
            <div class="max-h-60 overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2">Kullanıcı</th>
                            <th class="px-4 py-2">Bölüm</th>
                            <th class="px-4 py-2 text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $stats['online_users_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="bg-white hover:bg-gray-50 transition-colors group relative">
                                <td class="px-4 py-3 font-medium text-gray-900 flex items-center gap-3">
                                    <div class="relative">
                                        <?php if($user->profile_photo_path): ?>
                                            <img src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>"
                                                alt="<?php echo e($user->name); ?>"
                                                class="w-8 h-8 rounded-full object-cover border border-gray-200">
                                        <?php else: ?>
                                            <div
                                                class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold border border-indigo-200">
                                                <?php echo e(substr($user->name, 0, 1)); ?>

                                            </div>
                                        <?php endif; ?>
                                        <span
                                            class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <a href="<?php echo e(route('profile.show', $user->id)); ?>"
                                            class="hover:text-indigo-600 hover:underline text-sm font-semibold">
                                            <?php echo e($user->name); ?>

                                        </a>
                                        <span class="text-[10px] text-gray-500"><?php echo e($user->unvan ?? 'Kullanıcı'); ?></span>
                                    </div>

                                    
                                    <div
                                        class="absolute left-14 top-10 z-50 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200 ease-in-out">
                                        <div
                                            class="bg-white rounded-lg shadow-xl border border-gray-100 w-64 p-3 ring-1 ring-black ring-opacity-5">
                                            <div class="flex items-center gap-3 mb-3 pb-2 border-b border-gray-50">
                                                <?php if($user->profile_photo_path): ?>
                                                    <img src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>"
                                                        class="w-10 h-10 rounded-full object-cover">
                                                <?php else: ?>
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                                                        <?php echo e(substr($user->name, 0, 1)); ?>

                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-800"><?php echo e($user->name); ?></h4>
                                                    <p class="text-[10px] text-gray-500"><?php echo e($user->email); ?></p>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Son
                                                    7 Giriş</h5>
                                                <ul class="space-y-1.5">
                                                    <?php $__empty_2 = true; $__currentLoopData = $user->loginActivities->take(7); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                        <li class="flex justify-between items-center text-[10px] text-gray-600">
                                                            <span
                                                                class="font-medium"><?php echo e($activity->created_at->format('d.m H:i')); ?></span>
                                                            <span
                                                                class="text-gray-400 font-mono"><?php echo e($activity->ip_address); ?></span>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                        <li class="text-[10px] text-gray-400 italic">Kayıt bulunamadı.</li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600 font-medium bg-gray-50/50">
                                    <?php echo e($user->bolum ? Str::limit($user->bolum->ad, 20) : '-'); ?>

                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span
                                        class="bg-green-100 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-full border border-green-200 shadow-sm">
                                        Çevrimiçi
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-400 italic">Şu an aktif kullanıcı
                                    yok.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
            
            <div class="max-h-60 overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2">Son Aktif Kullanıcı</th>
                            <th class="px-4 py-2 text-right">Son Görülme</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $stats['last_active_users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="bg-white hover:bg-gray-50 transition-colors group relative">
                                <td class="px-4 py-3 font-medium text-gray-900 flex items-center gap-3">
                                    <div class="relative">
                                        <?php if($user->profile_photo_path): ?>
                                            <img src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>"
                                                alt="<?php echo e($user->name); ?>"
                                                class="w-8 h-8 rounded-full object-cover border border-gray-200 grayscale group-hover:grayscale-0 transition-all">
                                        <?php else: ?>
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 group-hover:bg-indigo-100 group-hover:text-indigo-600 flex items-center justify-center text-xs font-bold border border-gray-200 transition-colors">
                                                <?php echo e(substr($user->name, 0, 1)); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-semibold text-gray-700 group-hover:text-indigo-600 transition-colors">
                                            <?php echo e($user->name); ?>

                                        </span>
                                        <span class="text-[10px] text-gray-400 group-hover:text-gray-500">
                                            <?php echo e($user->bolum ? Str::limit($user->bolum->ad, 20) : '-'); ?>

                                        </span>
                                    </div>

                                    
                                    <div
                                        class="absolute left-10 top-8 z-50 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200 ease-in-out">
                                        <div
                                            class="bg-white rounded-lg shadow-xl border border-gray-100 w-64 p-3 ring-1 ring-black ring-opacity-5">
                                            <div class="flex items-center gap-3 mb-3 pb-2 border-b border-gray-50">
                                                <?php if($user->profile_photo_path): ?>
                                                    <img src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>"
                                                        class="w-10 h-10 rounded-full object-cover">
                                                <?php else: ?>
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                                                        <?php echo e(substr($user->name, 0, 1)); ?>

                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-800"><?php echo e($user->name); ?></h4>
                                                    <p class="text-[10px] text-gray-500"><?php echo e($user->email); ?></p>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Son
                                                    7 Giriş</h5>
                                                <ul class="space-y-1.5">
                                                    <?php $__empty_1 = true; $__currentLoopData = $user->loginActivities->take(7); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <li class="flex justify-between items-center text-[10px] text-gray-600">
                                                            <span
                                                                class="font-medium"><?php echo e($activity->created_at->format('d.m H:i')); ?></span>
                                                            <span
                                                                class="text-gray-400 font-mono"><?php echo e($activity->ip_address); ?></span>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <li class="text-[10px] text-gray-400 italic">Kayıt bulunamadı.</li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-gray-500 font-mono">
                                    <?php echo e($user->last_seen_at ? $user->last_seen_at->diffForHumans() : '-'); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">İAA Projeleri</h3>
                    <p class="text-xs text-gray-500">Devam Eden & Onay Bekleyen</p>
                </div>
                <a href="<?php echo e(route('admin.iaa-raporlari.index')); ?>"
                    class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase">Tüm Rapor &rarr;</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Proje</th>
                        <th class="px-4 py-2">Bölüm</th>
                        <th class="px-4 py-2">Durum</th>
                        <th class="px-4 py-2 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['iaa']['active_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">
                                <a href="<?php echo e(route('proje.workspace.show', $iaa->id)); ?>"
                                    class="hover:text-indigo-600 hover:underline block truncate max-w-[150px]"
                                    title="<?php echo e($iaa->baslik); ?>">
                                    <?php echo e($iaa->baslik); ?>

                                </a>
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    <?php echo e($iaa->atananTakim ? $iaa->atananTakim->ad : ($iaa->gonderen ? $iaa->gonderen->name : '-')); ?>

                                </div>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                <?php echo e($iaa->bolum ? Str::limit($iaa->bolum->ad, 15) : '-'); ?>

                            </td>
                            <td class="px-4 py-2 relative"> <!-- Tooltip için relative -->
                                    <?php echo $iaa->durum_etiketi; ?>

                            </td>
                            <td class="px-4 py-2 text-right text-xs whitespace-nowrap">
                                <?php echo e($iaa->updated_at->format('d.m')); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-400 italic">Aktif proje yok.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-red-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Müşteri Şikayetleri</h3>
                    <p class="text-xs text-gray-500">İşlemdeki Kayıtlar</p>
                </div>
                <a href="<?php echo e(route('admin.sikayet-raporlari.index')); ?>"
                    class="text-xs font-bold text-red-600 hover:text-red-800 uppercase">Tüm Rapor &rarr;</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Müşteri / Konu</th>
                        <th class="px-4 py-2">Bölüm</th>
                        <th class="px-4 py-2">Durum</th>
                        <th class="px-4 py-2 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['sikayetler']['active_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">
                                <div class="text-xs font-bold text-gray-800 truncate max-w-[150px]">
                                    <?php echo e($sikayet->customer ? $sikayet->customer->name : 'Bilinmeyen Müşteri'); ?>

                                </div>
                                <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>"
                                    class="text-[11px] text-gray-500 hover:text-red-600 block truncate max-w-[200px]"
                                    title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>">
                                    <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                </a>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                <?php echo e($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum ? Str::limit($sikayet->sikayetKategori->bolum->ad, 15) : '-'); ?>

                            </td>
                            <td class="px-4 py-2">
                                    <?php echo $sikayet->musteri_durum_badge; ?>

                            </td>
                            <td class="px-4 py-2 text-right text-xs whitespace-nowrap">
                                <?php echo e($sikayet->created_at->format('d.m')); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-400 italic">Aktif şikayet yok.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-rose-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Disiplin Süreçleri</h3>
                    <p class="text-xs text-gray-500">Aktif Dosyalar</p>
                </div>
                <a href="<?php echo e(route('admin.disiplin.index')); ?>"
                    class="text-xs font-bold text-rose-600 hover:text-rose-800 uppercase">Yönetim &rarr;</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Personel</th>
                        <th class="px-4 py-2">Bölüm</th>
                        <th class="px-4 py-2">İhlal</th>
                        <th class="px-4 py-2 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['disiplin']['active_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr
                            class="bg-white hover:bg-gray-50 border-l-4 <?php echo e($case->durum == 'Karar Verildi' || $case->durum == 'Kapandı' ? 'border-gray-300 bg-gray-50' : 'border-rose-500'); ?>">
                            <td class="px-4 py-2 font-medium text-gray-900">
                                <a href="<?php echo e(route('admin.disiplin.show', $case->id)); ?>"
                                    class="hover:text-rose-600 hover:underline flex items-center gap-2">
                                    <?php echo e($case->user ? $case->user->name : '-'); ?>

                                    <?php if($case->durum == 'Karar Verildi' || $case->durum == 'Kapandı'): ?>
                                        <span class="text-[8px] px-1.5 py-0.5 rounded bg-gray-200 text-gray-600">KAPALI</span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                <?php echo e($case->user && $case->user->bolum ? Str::limit($case->user->bolum->ad, 15) : '-'); ?>

                            </td>
                            <td class="px-4 py-2 text-xs text-gray-600">
                                <span title="<?php echo e($case->behavior ? $case->behavior->tanim : '-'); ?>">
                                    <?php echo e($case->behavior ? Str::limit($case->behavior->tanim, 20) : '-'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-2 text-right text-xs whitespace-nowrap">
                                <?php echo e($case->created_at->format('d.m')); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400 italic">Dosya bulunamadı.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center gap-4 overflow-x-auto">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Yoğunluk:</span>
                <?php $__currentLoopData = $stats['disiplin']['bolum_dagilimi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span
                        class="text-[10px] text-gray-600 bg-white border border-gray-200 px-2 py-1 rounded shadow-sm whitespace-nowrap">
                        <?php echo e($bolum->bolum_adi); ?>: <strong class="text-rose-600"><?php echo e($bolum->toplam); ?></strong>
                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-blue-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Arabuluculuk</h3>
                    <p class="text-xs text-gray-500">Devam Eden Süreçler</p>
                </div>
                <a href="<?php echo e(route('admin.arabuluculuk.index')); ?>"
                    class="text-xs font-bold text-blue-600 hover:text-blue-800 uppercase">Yönetim &rarr;</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Taraf / Dosya</th>
                        <th class="px-4 py-2">Durum</th>
                        <th class="px-4 py-2 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['arabuluculuk']['active_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">
                                <a href="<?php echo e(route('admin.arabuluculuk.show', $case->id)); ?>"
                                    class="hover:text-blue-600 hover:underline block truncate max-w-[150px]">
                                    <?php echo e($case->calisan ? $case->calisan->name : 'Personel Dosyası'); ?>

                                </a>
                            </td>
                            <td class="px-4 py-2">
                                <?php
                                    $statusColorMed = 'bg-gray-100 text-gray-600';
                                    if ($case->status == 'gorusuluyor' || $case->status == 'yonetim_onayinda')
                                        $statusColorMed = 'bg-blue-100 text-blue-700 border border-blue-200';
                                    elseif ($case->status == 'anlasildi' || $case->status == 'odeme_yapildi')
                                        $statusColorMed = 'bg-green-100 text-green-700 border border-green-200';
                                    elseif ($case->status == 'dava_acildi' || $case->status == 'anlasilamadi')
                                        $statusColorMed = 'bg-red-100 text-red-700 border border-red-200';
                                ?>
                                <span class="text-[10px] px-1.5 py-0.5 rounded border <?php echo e($statusColorMed); ?>">
                                    <?php echo e(ucwords(str_replace('_', ' ', $case->status))); ?>

                                </span>
                            </td>
                            <td class="px-4 py-2 text-right text-xs whitespace-nowrap">
                                <?php echo e($case->updated_at->format('d.m')); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-400 italic">Aktif süreç yok.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-amber-50 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Bekleyen İşler Listesi</h3>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Aksiyon Bekleyen Süreçler</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.tum-bekleyen-isler')); ?>"
                class="text-xs font-bold text-amber-600 hover:text-amber-800 uppercase tracking-wide">Tümünü İncele &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-[10px] text-indigo-900 uppercase bg-indigo-50/50 border-b border-indigo-100">
                    <tr>
                        <th class="px-6 py-4 font-black tracking-widest text-center">Tür</th>
                        <th class="px-6 py-4 font-black tracking-widest w-1/3">Konu / Başlık</th>
                        <th class="px-6 py-4 font-black tracking-widest">Bekleyen Taraf</th>
                        <th class="px-6 py-4 font-black tracking-widest">Durum</th>
                        <th class="px-6 py-4 font-black tracking-widest">Süre</th>
                        <th class="px-6 py-4 text-right font-black tracking-widest">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['waiting_tasks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <?php if($task['type'] == 'Müşteri Şikayeti'): ?>
                                    <span
                                        class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded border border-red-200">MÜŞTERİ
                                        ŞİKAYETİ</span>
                                <?php elseif($task['type'] == 'İAA'): ?>
                                    <span
                                        class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-1 rounded border border-indigo-200">İAA</span>
                                <?php elseif($task['type'] == 'Arabuluculuk'): ?>
                                    <span
                                        class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded border border-blue-200">ARABULUCULUK</span>
                                <?php else: ?>
                                    <span
                                        class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded border border-gray-200"><?php echo e(strtoupper($task['type'])); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-900 font-medium block"
                                    title="<?php echo e($task['subject']); ?>"><?php echo e(Str::limit($task['subject'], 50)); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800 text-xs"><?php echo e($task['waiting_person']); ?></span>
                                    <span class="text-[10px] text-gray-500 uppercase"><?php echo e($task['waiting_dept']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded inline-block">
                                    <?php echo e($task['status']); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="flex items-center gap-1 font-bold <?php echo e($task['days'] > 5 ? 'text-red-500' : 'text-amber-500'); ?>">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo e(number_format($task['days'], 0)); ?> Gün
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?php echo e($task['link']); ?>"
                                    class="text-indigo-600 hover:text-indigo-900 font-bold text-xs flex items-center justify-end gap-1">
                                    İNCELE <span class="text-lg leading-none">&rsaquo;</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 bg-gray-50/50">
                                Bekleyen iş bulunmamaktadır.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-10">
        
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-emerald-50 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Müşteri Portföyü</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">En Çok Şikayet Bildirenler</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full uppercase">Top 5</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Firma Adı</th>
                        <th class="px-6 py-3 text-right">Şikayet Sayısı</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $stats['musteriler']['en_cok_sikayet']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $musteri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900 truncate">
                                <a href="<?php echo e(route('musteri.profil.show', $musteri->id)); ?>"
                                    class="hover:underline hover:text-indigo-600">
                                    <?php echo e($musteri->name); ?>

                                </a>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span
                                    class="bg-red-50 text-red-600 px-2 py-0.5 rounded font-bold text-xs border border-red-100">
                                    <?php echo e($musteri->sikayetler_count); ?> Adet
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 text-orange-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">İade Raporu</h3>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Bölümlere Göre İade Miktarları</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-orange-600 bg-orange-100 px-2 py-0.5 rounded-full uppercase">Analiz</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Bölüm</th>
                        <th class="px-6 py-3">Birim</th>
                        <th class="px-6 py-3 text-right">Miktar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['musteriler']['iadeler_bolum_bazli']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolumAdi => $iadeler): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $__currentLoopData = $iadeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $iade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="bg-white hover:bg-gray-50 <?php echo e($loop->last ? 'border-b border-gray-100' : ''); ?>">
                                <td class="px-6 py-3 font-medium text-gray-900 border-r border-gray-50">
                                    <?php echo e($index === 0 ? $bolumAdi : ''); ?>

                                </td>
                                <td class="px-6 py-3 text-xs text-gray-600 uppercase"><?php echo e($iade->birim); ?></td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-gray-800">
                                    <?php echo e(number_format($iade->toplam_miktar, 0, ',', '.')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">İade kaydı bulunamadı.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    
    <div class="mt-8">
        <?php echo $__env->make('dashboard.partials.top5-performers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/dashboard/partials/yonetim.blade.php ENDPATH**/ ?>