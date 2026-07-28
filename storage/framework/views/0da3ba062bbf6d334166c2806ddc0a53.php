<?php $__env->startPush('pageTitle'); ?>
    Arabuluculuk Dosyaları | 
<?php $__env->stopPush(); ?>

<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <style>[x-cloak] { display: none !important; }</style>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    Arabuluculuk Dosyaları
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Tüm arabuluculuk süreçlerini görüntüleyin ve yönetin</p>
            </div>
            <?php if(auth()->user()->hasRole('Superadmin') || auth()->user()->canAny(['arabuluculuk.create_ihtiyari', 'arabuluculuk.create_zorunlu'])): ?>
                <a href="<?php echo e(route('admin.arabuluculuk.create')); ?>"
                    class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg transition-all hover:shadow-xl active:scale-[0.98] flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Yeni Dosya Aç
                </a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-indigo-100 p-2.5 rounded-xl text-indigo-600 group-hover:bg-indigo-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-black text-gray-900"><?php echo e($stats['toplam']); ?></div>
                        </div>
                    </div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Toplam Dosya</div>
                    <div class="flex gap-2 mt-2">
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-green-50 text-green-600 font-bold"><?php echo e($stats['ihtiyari']); ?> İhtiyari</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-50 text-red-600 font-bold"><?php echo e($stats['zorunlu']); ?> Zorunlu</span>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-amber-100 p-2.5 rounded-xl text-amber-600 group-hover:bg-amber-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-black text-amber-600"><?php echo e($stats['aktif']); ?></div>
                        </div>
                    </div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Aktif Dosya</div>
                    <div class="mt-2">
                        <?php if($stats['toplam'] > 0): ?>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-amber-400 h-1.5 rounded-full" style="width: <?php echo e(($stats['aktif'] / $stats['toplam']) * 100); ?>%"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-emerald-100 p-2.5 rounded-xl text-emerald-600 group-hover:bg-emerald-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-black text-emerald-600"><?php echo e($stats['tamamlanan']); ?></div>
                        </div>
                    </div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tamamlanan</div>
                    <div class="flex gap-2 mt-2">
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold"><?php echo e($stats['anlasildi']); ?> Anlaşıldı</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 font-bold"><?php echo e($stats['anlasilmadi']); ?> Anlaşılmadı</span>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-purple-100 p-2.5 rounded-xl text-purple-600 group-hover:bg-purple-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-purple-700">₺<?php echo e(number_format($stats['toplam_tutar'], 0, ',', '.')); ?></div>
                        </div>
                    </div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Toplam Talep</div>
                    <div class="mt-2">
                        <?php if($stats['tamamlanan'] > 0): ?>
                            <span class="text-[10px] font-bold text-emerald-600">%<?php echo e($stats['anlasildi'] > 0 ? round(($stats['anlasildi'] / $stats['tamamlanan']) * 100) : 0); ?> Anlaşma Oranı</span>
                        <?php else: ?>
                            <span class="text-[10px] text-gray-400">Henüz tamamlanan yok</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6" x-data="{ showFilters: <?php echo e(request()->hasAny(['search','status','type','date_from','date_to','mutabakat']) ? 'true' : 'false'); ?> }">
                <div class="px-6 py-3 flex items-center justify-between cursor-pointer" @click="showFilters = !showFilters">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span class="text-sm font-bold text-gray-700">Filtreler</span>
                        <?php if(request()->hasAny(['search','status','type','date_from','date_to','mutabakat'])): ?>
                            <span class="bg-indigo-100 text-indigo-700 text-[10px] px-2 py-0.5 rounded-full font-bold">Aktif</span>
                        <?php endif; ?>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>

                <div x-show="showFilters" x-transition x-cloak class="px-6 pb-5 border-t border-gray-100 pt-4">
                    <form method="GET" action="<?php echo e(route('admin.arabuluculuk.index')); ?>">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            
                            <div class="lg:col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Çalışan Ara</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                        class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="İsim veya e-posta...">
                                </div>
                            </div>

                            
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Durum</label>
                                <select name="status" class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Tümü</option>
                                    <option value="taslak" <?php echo e(request('status') == 'taslak' ? 'selected' : ''); ?>>Taslak</option>
                                    <option value="hukuk_incelemesinde" <?php echo e(request('status') == 'hukuk_incelemesinde' ? 'selected' : ''); ?>>Hukuk İncelemesinde</option>
                                    <option value="yonetim_onayinda" <?php echo e(request('status') == 'yonetim_onayinda' ? 'selected' : ''); ?>>Yönetim Onayında</option>
                                    <option value="arabulucuda" <?php echo e(request('status') == 'arabulucuda' ? 'selected' : ''); ?>>Arabulucuda</option>
                                    <option value="imza_asamasinda" <?php echo e(request('status') == 'imza_asamasinda' ? 'selected' : ''); ?>>İmza Aşamasında</option>
                                    <option value="odeme_bekliyor" <?php echo e(request('status') == 'odeme_bekliyor' ? 'selected' : ''); ?>>Ödeme Bekliyor</option>
                                    <option value="kapatildi" <?php echo e(request('status') == 'kapatildi' ? 'selected' : ''); ?>>Kapatıldı</option>
                                    <option value="anlasma_saglanamadi" <?php echo e(request('status') == 'anlasma_saglanamadi' ? 'selected' : ''); ?>>Anlaşma Sağlanamadı</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tür</label>
                                <select name="type" class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Tümü</option>
                                    <option value="ihtiyari" <?php echo e(request('type') == 'ihtiyari' ? 'selected' : ''); ?>>İhtiyari</option>
                                    <option value="zorunlu" <?php echo e(request('type') == 'zorunlu' ? 'selected' : ''); ?>>Zorunlu</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Başlangıç</label>
                                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                                    class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Bitiş</label>
                                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                                    class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-4">
                            <a href="<?php echo e(route('admin.arabuluculuk.index')); ?>"
                                class="text-sm text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Temizle
                            </a>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 px-5 rounded-lg transition flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Filtrele
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dosya No / Tarih</th>
                                <th class="px-6 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Çalışan</th>
                                <th class="px-6 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tür / Sorumlu</th>
                                <th class="px-6 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Arabulucu / Avukat</th>
                                <th class="px-6 py-3.5 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3.5 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Mutabakat</th>
                                <th class="px-6 py-3.5 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php $__empty_1 = true; $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-indigo-50/30 transition-all group">
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900"><?php echo e($case->dosya_no ?? '---'); ?></div>
                                        <div class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <?php echo e($case->created_at->format('d.m.Y')); ?>

                                        </div>
                                    </td>

                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-9 w-9 rounded-full flex items-center justify-center text-xs font-bold
                                                <?php echo e($case->calisan && $case->calisan->is_mavi_yaka ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700'); ?>">
                                                <?php echo e(substr($case->calisan->name ?? '?', 0, 2)); ?>

                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-semibold text-gray-900"><?php echo e($case->calisan->name ?? 'Silinmiş Kullanıcı'); ?></div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-xs text-gray-400"><?php echo e($case->calisan->email ?? ''); ?></span>
                                                    <?php if($case->calisan && $case->calisan->is_mavi_yaka): ?>
                                                        <span class="text-[8px] px-1 py-0.5 rounded bg-blue-100 text-blue-600 font-bold">MY</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($case->type == 'ihtiyari'): ?>
                                            <span class="px-2.5 py-1 inline-flex text-[11px] leading-5 font-bold rounded-lg bg-green-50 text-green-700 border border-green-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                İhtiyari
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 inline-flex text-[11px] leading-5 font-bold rounded-lg bg-red-50 text-red-700 border border-red-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                                Zorunlu
                                            </span>
                                        <?php endif; ?>
                                        <div class="text-[10px] text-gray-400 mt-1">Yöneten: <?php echo e(ucfirst($case->owner_role)); ?></div>
                                        <div class="text-[10px] text-indigo-400 mt-0.5">
                                            <span class="font-semibold">Açan:</span> <?php echo e($case->creator->name ?? 'Sistem'); ?>

                                        </div>
                                    </td>

                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium"><?php echo e($case->arabulucu->name ?? '-'); ?></div>
                                        <?php if($case->external_lawyer_id): ?>
                                            <div class="text-xs text-purple-600 font-semibold flex items-center gap-1 mt-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                Av. <?php echo e($case->externalLawyer->name ?? ''); ?> (Dış)
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php
                                            $statusConfig = [
                                                'taslak' => ['bg-gray-100 text-gray-700 border-gray-200', 'Taslak'],
                                                'hukuk_incelemesinde' => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Hukuk İncelemesinde'],
                                                'yonetim_onayinda' => ['bg-purple-50 text-purple-700 border-purple-200', 'Yönetim Onayında'],
                                                'arabulucuda' => ['bg-blue-50 text-blue-700 border-blue-200', 'Arabulucuda'],
                                                'imza_asamasinda' => ['bg-indigo-50 text-indigo-700 border-indigo-200', 'İmza Aşamasında'],
                                                'odeme_bekliyor' => ['bg-orange-50 text-orange-700 border-orange-300 animate-pulse', 'Ödeme Bekliyor'],
                                                'kapatildi' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Kapatıldı'],
                                                'anlasma_saglanamadi' => ['bg-rose-50 text-rose-700 border-rose-200', 'Anlaşma Sağlanamadı'],
                                                'son_onay_bekliyor' => ['bg-cyan-50 text-cyan-700 border-cyan-200', 'Son Onay Bekliyor'],
                                            ];
                                            $cfg = $statusConfig[$case->status] ?? ['bg-gray-100 text-gray-600 border-gray-200', strtoupper($case->status)];
                                        ?>
                                        <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-lg border <?php echo e($cfg[0]); ?>">
                                            <?php echo e($cfg[1]); ?>

                                        </span>
                                    </td>

                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php if($case->mutabakat == 'anlasildi'): ?>
                                            <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                                ANLAŞILDI
                                            </span>
                                        <?php elseif($case->mutabakat == 'anlasilmadi'): ?>
                                            <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                ANLAŞILMADI
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-lg bg-slate-50 text-slate-400 border border-slate-200">
                                                BİLGİ YOK
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <?php if(!auth()->user()->hasRole('Direktör') || auth()->user()->hasRole('Superadmin')): ?>
                                            <a href="<?php echo e(route('admin.arabuluculuk.show', $case->id)); ?>"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold hover:bg-indigo-100 transition group-hover:bg-indigo-100">
                                                Detay
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-[10px] italic">Erişim Kısıtlı</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="bg-gray-100 p-4 rounded-2xl mb-4">
                                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <p class="text-gray-500 font-medium">Arabuluculuk dosyası bulunamadı</p>
                                            <p class="text-gray-400 text-sm mt-1">Filtre kriterlerinizi değiştirmeyi deneyin</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <?php if($cases->hasPages()): ?>
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        <?php echo e($cases->links()); ?>

                    </div>
                <?php endif; ?>

                
                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/30 flex items-center justify-between">
                    <span class="text-xs text-gray-400">
                        Toplam <span class="font-bold text-gray-600"><?php echo e($cases->total()); ?></span> kayıttan
                        <span class="font-bold text-gray-600"><?php echo e($cases->firstItem() ?? 0); ?>-<?php echo e($cases->lastItem() ?? 0); ?></span> arası gösteriliyor
                    </span>
                    <span class="text-xs text-gray-400">Sayfa <?php echo e($cases->currentPage()); ?> / <?php echo e($cases->lastPage()); ?></span>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabuluculuk/index.blade.php ENDPATH**/ ?>