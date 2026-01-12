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
    
    <div class="relative bg-slate-900 pt-6 pb-16 overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')]"></div>
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-indigo-500 blur-3xl opacity-20"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            
                <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Lideri|Bölüm Kalite Yöneticisi')): ?>
                    <div class="mb-6">
                        <a href="<?php echo e(route('admin.musteriler.index')); ?>" class="inline-flex items-center text-slate-400 hover:text-white transition-colors text-sm font-medium group">
                            <svg class="w-4 h-4 mr-1 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Müşteri Listesine Geri Dön
                        </a>
                    </div>
                <?php endif; ?>
            


            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                
                
                <div class="flex items-center gap-5 w-full md:w-auto">
                    <div class="relative group flex-shrink-0">
                        <div class="relative h-20 w-20 bg-white rounded-xl p-2 flex items-center justify-center shadow-2xl">
                            <?php if($customer->logo_path): ?>
                                <img src="<?php echo e(asset('storage/' . $customer->logo_path)); ?>" alt="<?php echo e($customer->name); ?>" class="object-contain h-full w-full">
                            <?php else: ?>
                                <span class="text-2xl font-black text-slate-800"><?php echo e(substr($customer->name, 0, 1)); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="text-white">
                        <h1 class="text-2xl font-bold tracking-tight leading-tight"><?php echo e($customer->name); ?></h1>
                        <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs text-slate-300">
                            <?php if($customer->tax_number): ?>
                                <span class="flex items-center gap-1 bg-white/5 px-2.5 py-0.5 rounded-full border border-white/10">
                                    <svg class="w-3 h-3 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    VN: <?php echo e($customer->tax_number); ?>

                                </span>
                            <?php endif; ?>
                            <span class="flex items-center gap-1 bg-white/5 px-2.5 py-0.5 rounded-full border border-white/10">
                                <svg class="w-3 h-3 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <?php echo e($customer->location_type); ?>

                            </span>
                        </div>
                    </div>
                </div>

                
                <?php if(auth()->user()->is_personnel || auth()->user()->customer_id == $customer->id): ?>
                <div>
                    <a href="<?php echo e(route('admin.sikayetler.create', ['musteri_id' => $customer->id])); ?>" 
                       class="group relative inline-flex items-center px-5 py-2.5 bg-white text-indigo-600 font-bold rounded-lg shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5 text-sm">
                        <svg class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Şikayet Oluştur
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 h-full">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Kurumsal İletişim</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase text-gray-400 font-bold">Adres</span>
                            <span class="text-gray-800 text-sm leading-snug block"><?php echo e($customer->address ?? 'Belirtilmemiş'); ?></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-50 text-green-600 rounded-lg flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] uppercase text-gray-400 font-bold">Telefon</span>
                                <span class="text-gray-800 font-medium text-sm truncate block"><?php echo e($customer->phone ?? '-'); ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] uppercase text-gray-400 font-bold">E-Posta</span>
                                <a href="mailto:<?php echo e($customer->email); ?>" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm truncate block"><?php echo e($customer->email ?? '-'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 h-full flex flex-col relative overflow-hidden">
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-2">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Firma Yetkilileri</h3>
                    
                    
                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Yonetim|Bölüm Lideri')): ?>
                    <button onclick="document.getElementById('addRepModal').classList.remove('hidden')" 
                            class="text-xs flex items-center gap-1 bg-indigo-50 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-100 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Ekle
                    </button>
                    <?php endif; ?>
                </div>
                
                <div class="flex-1 overflow-y-auto max-h-48 space-y-3 pr-2 scrollbar-thin scrollbar-thumb-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $temsilciler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $temsilci): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="group flex items-start justify-between p-2 hover:bg-slate-50 rounded-lg transition border border-transparent hover:border-slate-100">
                            <div class="flex items-start gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600 flex items-center justify-center font-bold text-xs border border-white shadow-sm flex-shrink-0">
                                    <?php echo e(substr($temsilci->name, 0, 1)); ?>

                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-800 text-sm truncate"><?php echo e($temsilci->name); ?></div>
                                    <div class="text-[11px] text-gray-500 truncate"><?php echo e($temsilci->unvan ?? 'Ünvan Yok'); ?></div>
                                    
                                    
                                    <div class="mt-1 flex flex-wrap gap-2 text-[10px] text-gray-400">
                                        <?php if($temsilci->email): ?>
                                            <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg><?php echo e($temsilci->email); ?></span>
                                        <?php endif; ?>
                                        <?php if($temsilci->telefon): ?>
                                            <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg><?php echo e($temsilci->telefon); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            
                            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Yonetim')): ?>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                <form action="<?php echo e(route('musteri.yetkili.destroy', $temsilci->id)); ?>" method="POST" onsubmit="return confirm('Bu yetkiliyi silmek istediğinize emin misiniz?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-sm text-gray-400 italic text-center py-4">Henüz yetkili eklenmemiş.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?php echo e(route('musteri.profil.show', $customer->id)); ?>" class="relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all group">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Toplam</p>
                <p class="text-2xl font-black text-slate-800 mt-1"><?php echo e($toplamSikayet); ?></p>
                <?php if(!request('filtre')): ?> <div class="absolute bottom-0 left-0 w-full h-1 bg-slate-800 rounded-b-xl"></div> <?php endif; ?>
            </a>
            <a href="<?php echo e(route('musteri.profil.show', ['customer' => $customer->id, 'filtre' => 'aktif'])); ?>" class="relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all group">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Aktif Süreç</p>
                <p class="text-2xl font-black text-orange-500 mt-1"><?php echo e($aktifSikayet); ?></p>
                <?php if(request('filtre') == 'aktif'): ?> <div class="absolute bottom-0 left-0 w-full h-1 bg-orange-500 rounded-b-xl"></div> <?php endif; ?>
            </a>
            <a href="<?php echo e(route('musteri.profil.show', ['customer' => $customer->id, 'filtre' => 'tamamlanan'])); ?>" class="relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all group">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Proje (Biten)</p>
                <p class="text-2xl font-black text-green-500 mt-1"><?php echo e($tamamlananProje); ?></p>
                <?php if(request('filtre') == 'tamamlanan'): ?> <div class="absolute bottom-0 left-0 w-full h-1 bg-green-500 rounded-b-xl"></div> <?php endif; ?>
            </a>
            <a href="<?php echo e(route('musteri.profil.show', ['customer' => $customer->id, 'filtre' => 'cozulen'])); ?>" class="relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all group">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Hız (Ort. Gün)</p>
                <p class="text-2xl font-black text-blue-500 mt-1"><?php echo e($ortalamaSure); ?></p>
                <?php if(request('filtre') == 'cozulen'): ?> <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 rounded-b-xl"></div> <?php endif; ?>
            </a>
        </div>
    </div>

    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Şikayet ve Proje Geçmişi</h3>
                    <p class="text-xs text-gray-500">Müşteriye ait tüm süreçlerin detaylı listesi</p>
                </div>
                <?php if(request('filtre')): ?>
                    <a href="<?php echo e(route('musteri.profil.show', $customer->id)); ?>" class="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded-full">Filtreyi Temizle</a>
                <?php endif; ?>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-200 uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-10">#</th>
                            <th class="px-6 py-4">Konu & Oluşturan</th>
                            <th class="px-6 py-4">Takım</th>
                            <th class="px-6 py-4">Sorumlu</th>
                            <th class="px-6 py-4">Durum</th>
                            <th class="px-6 py-4">Proje</th>
                            <th class="px-6 py-4">Tarih</th>
                            <th class="px-6 py-4 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-indigo-50/30 transition group">
                                <td class="px-6 py-4 font-medium text-gray-400 text-xs">
                                    <?php echo e(($sikayetler->currentPage() - 1) * $sikayetler->perPage() + $loop->iteration); ?>

                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-bold bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded">#<?php echo e($sikayet->id); ?></span>
                                        <div class="font-bold text-gray-800 text-sm truncate max-w-[200px]" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>">
                                            <?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 30)); ?>

                                        </div>
                                    </div>
                                    
                                    
                                    <div class="text-[10px] text-gray-400 flex items-center gap-1">
                                        <?php if($sikayet->olusturanKurulUyesi): ?>
                                            <?php if($sikayet->olusturanKurulUyesi->is_personnel): ?>
                                                <svg class="w-3 h-3 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                                
                                                <span>Yönetici: <?php echo e($sikayet->olusturanKurulUyesi->name); ?></span>
                                            <?php else: ?>
                                                <svg class="w-3 h-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                
                                                <span>Müşteri: <?php echo e($sikayet->olusturanKurulUyesi->name); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span>Bilinmiyor</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-4">
                                    <?php if($sikayet->cozumTakimi): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            
                                            <?php echo e($sikayet->cozumTakimi->ad); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs italic">- Atanmadı -</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-4">
                                    <?php if($sikayet->cozumTakimi && $sikayet->cozumTakimi->lider): ?>
                                        <div class="relative group/tooltip inline-block">
                                            
                                            <a href="<?php echo e(route('profile.show', $sikayet->cozumTakimi->lider->id)); ?>" target="_blank" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors group">
                                                <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                                    <?php echo e(substr($sikayet->cozumTakimi->lider->name, 0, 1)); ?>

                                                </div>
                                                <span class="text-sm font-medium border-b border-transparent group-hover:border-indigo-600">
                                                    <?php echo e($sikayet->cozumTakimi->lider->name); ?>

                                                </span>
                                            </a>
                                            
                                            
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-slate-800 text-white text-xs rounded-lg shadow-xl opacity-0 group-hover/tooltip:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                                                <div class="font-bold"><?php echo e($sikayet->cozumTakimi->lider->name); ?></div>
                                                <div class="text-slate-300"><?php echo e($sikayet->cozumTakimi->lider->email); ?></div>
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-300 text-xs">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?php echo $sikayet->musteri_durum_badge; ?>

                                </td>

                                <td class="px-6 py-4">
                                    <?php if($sikayet->iaaProjesi): ?>
                                        
                                        <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaaProjesi->id)); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-emerald-500 to-green-600 text-white hover:from-emerald-600 hover:to-green-700 shadow-sm hover:shadow transition transform hover:-translate-y-0.5">
                                            <span>Proje</span>
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-300 text-xs">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    <?php echo e($sikayet->created_at->format('d.m.Y')); ?>

                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-indigo-600 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-50 p-4 rounded-full mb-3">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Kayıtlı işlem bulunamadı.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <?php echo e($sikayetler->links()); ?>

            </div>
        </div>
    </div>

    
    <?php if(auth()->user()->hasRole(['Superadmin', 'Super Admin', 'Yonetim'])): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-12">
        <div class="bg-slate-800 rounded-xl shadow-lg p-6 text-slate-300 border border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        İşlem Geçmişi
                    </h3>
                    <span class="text-xs bg-slate-700 px-2 py-1 rounded text-slate-400">Son 10 Kayıt</span>
                </div>
                
                
                <a href="<?php echo e(route('musteri-logs.index')); ?>" target="_blank" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-1 transition">
                    Tüm Kayıtları İncele
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-700">
                <table class="w-full text-xs text-left">
                    <thead class="text-slate-400 uppercase bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tarih</th>
                            <th class="px-4 py-3 font-semibold">İşlem</th>
                            <th class="px-4 py-3 font-semibold">Kullanıcı</th>
                            <th class="px-4 py-3 font-semibold">Açıklama</th>
                            <th class="px-4 py-3 font-semibold">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700 bg-slate-800">
                        <?php if(isset($logs) && $logs->count() > 0): ?>
                            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-slate-700/50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap text-slate-400"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></td>
                                    <td class="px-4 py-3 font-bold text-white whitespace-nowrap">
                                        <?php if(Str::contains($log->islem_turu, 'Silme')): ?>
                                            <span class="text-red-400"><?php echo e($log->islem_turu); ?></span>
                                        <?php elseif(Str::contains($log->islem_turu, 'Ekleme') || Str::contains($log->islem_turu, 'Oluşturma')): ?>
                                            <span class="text-green-400"><?php echo e($log->islem_turu); ?></span>
                                        <?php else: ?>
                                            <span class="text-blue-400"><?php echo e($log->islem_turu); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-slate-300">
                                        <?php echo e($log->user ? $log->user->name : 'Sistem/Misafir'); ?>

                                    </td>
                                    <td class="px-4 py-3 text-slate-300"><?php echo e($log->aciklama); ?></td>
                                    <td class="px-4 py-3 font-mono text-slate-500 whitespace-nowrap"><?php echo e($log->ip_adresi); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500 italic">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <span>Bu müşteriye ait henüz bir işlem kaydı bulunmuyor.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Yonetim|Bölüm Lideri')): ?>
    <div id="addRepModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addRepModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="<?php echo e(route('musteri.yetkili.store', $customer->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Yeni Yetkili Ekle</h3>
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700">Ad Soyad *</label><input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
                            <div><label class="block text-sm font-medium text-gray-700">E-Posta *</label><input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-sm font-medium text-gray-700">Telefon</label><input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
                                <div><label class="block text-sm font-medium text-gray-700">Ünvan</label><input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
                            </div>
                            <p class="text-xs text-orange-600 bg-orange-50 p-2 rounded">Kullanıcıya otomatik şifre atanacaktır.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Kaydet</button>
                        <button type="button" onclick="document.getElementById('addRepModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">İptal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if(session('success')): ?>
        <div class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-xl z-50 animate-bounce"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="fixed bottom-5 right-5 bg-red-500 text-white px-6 py-3 rounded-lg shadow-xl z-50"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/musteriler/musteri-profile.blade.php ENDPATH**/ ?>