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
    <?php $__env->startPush('pageTitle'); ?>Kurul Girdileri Raporu | <?php $__env->stopPush(); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-tight flex items-center gap-3">
            <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
            <?php echo e(__('Kurul Girdileri Raporu')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Kurul Performans Takibi</h1>
                    <p class="text-slate-500 font-medium mt-1">Kurul üyeleri tarafından sisteme girilen tüm müşteri şikayetlerinin detaylı analizi.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                
                <div class="lg:col-span-1 space-y-6">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-50 bg-slate-50/50">
                            <h3 class="font-black text-slate-800 text-xs uppercase tracking-widest">Hızlı Filtre</h3>
                        </div>
                        <div class="p-5">
                            <form method="GET" action="<?php echo e(route('admin.sikayetler.kurulGirdileri')); ?>" class="space-y-4">
                                <div>
                                    <label for="kullanici_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Kurul Üyesi Seç</label>
                                    <select name="kullanici_id" id="kullanici_id" class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold text-slate-700">
                                        <?php if(auth()->user()->hasRole('Superadmin')): ?>
                                            <option value="all" <?php if($selectedUserId == 'all'): ?> selected <?php endif; ?>>Tüm Kurul Girdileri</option>
                                        <?php else: ?>
                                            <option value="<?php echo e(auth()->id()); ?>" <?php if($selectedUserId == auth()->id()): ?> selected <?php endif; ?>>Benim Girdiklerim</option>
                                            <option value="all" <?php if($selectedUserId == 'all'): ?> selected <?php endif; ?>>Tüm Kurul Girdileri</option>
                                        <?php endif; ?>
                                        <?php $__currentLoopData = $kurulUyeleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($uye->id != auth()->id()): ?> 
                                                <option value="<?php echo e($uye->id); ?>" <?php if($selectedUserId == $uye->id): ?> selected <?php endif; ?>><?php echo e($uye->name); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                                    Süzgeci Uygula
                                </button>
                                <button type="button" onclick="window.location.href='<?php echo e(route('admin.sikayetler.kurulGirdileri')); ?>'" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition">
                                    Sıfırla
                                </button>
                            </form>
                        </div>
                    </div>

                    
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-50 bg-amber-50/30">
                            <h3 class="font-black text-amber-900 text-xs uppercase tracking-widest">Kurul Üyeleri Katılımı</h3>
                        </div>
                        <div class="overflow-hidden">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/50">
                                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                        <th class="px-4 py-2">Üye</th>
                                        <th class="px-4 py-2 text-right">Girdi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php $__currentLoopData = $kurulUyeleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-indigo-50 transition-colors cursor-pointer <?php echo e($selectedUserId == $uye->id ? 'bg-indigo-50/80 border-l-4 border-indigo-600' : ''); ?>"
                                            onclick="window.location.href='<?php echo e(route('admin.sikayetler.kurulGirdileri', ['kullanici_id' => $uye->id])); ?>'">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <img src="<?php echo e($uye->profile_photo_url); ?>" class="w-6 h-6 rounded-full border border-slate-100" alt="">
                                                <div class="flex flex-col">
                                                    <span class="text-[11px] font-bold text-slate-700"><?php echo e($uye->name); ?></span>
                                                    <?php 
                                                        $roleBadge = 'Genel';
                                                        if($uye->hasRole(['Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt İçi'])) $roleBadge = 'Yurt İçi';
                                                        elseif($uye->hasRole(['Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Müşteri Şikayeti Kurulu - Yurt Dışı'])) $roleBadge = 'Yurt Dışı';
                                                        elseif($uye->hasRole(['Superadmin', 'Yonetim'])) $roleBadge = 'Yönetici';
                                                    ?>
                                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest"><?php echo e($roleBadge); ?></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex flex-col items-end">
                                                    <span class="text-[10px] font-black text-indigo-600"><?php echo e((int)$uye->girdigi_sikayetler_count); ?> Şikayet</span>
                                                    <span class="text-[8px] font-bold text-slate-400"><?php echo e(number_format($uye->girdigi_sikayetler_sum_kazanilan_puan ?? 0)); ?> Puan</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="lg:col-span-3 space-y-6">
                    
                    
                    <?php if (! (auth()->user()->hasRole('Superadmin'))): ?>
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl shadow-xl p-6 text-white overflow-hidden relative group">
                        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-6 opacity-70">Benim Katkım</h3>
                        <div class="grid grid-cols-3 gap-8 relative z-10">
                            <div>
                                <p class="text-3xl font-black"><?php echo e($stats_kisisel['toplam_benim_girdiklerim'] ?? 0); ?></p>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mt-1">Toplam Girdi</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-amber-300"><?php echo e($stats_kisisel['islemde_benim_girdiklerim'] ?? 0); ?></p>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mt-1">İşlemde Olan</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-emerald-400"><?php echo e($stats_kisisel['cozulen_benim_girdiklerim'] ?? 0); ?></p>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mt-1">Çözülen</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($isManager): ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-50 bg-indigo-50/50">
                                <h3 class="font-black text-indigo-900 text-[10px] uppercase tracking-widest">En Aktif Raportörler</h3>
                            </div>
                            <div class="p-4 space-y-3">
                                <?php $__empty_1 = true; $__currentLoopData = $toplamGirenLiderler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-full <?php echo e($index == 0 ? 'bg-amber-400 text-white' : ($index == 1 ? 'bg-slate-300 text-white' : 'bg-amber-600 text-white')); ?> flex items-center justify-center text-[9px] font-black"><?php echo e($index + 1); ?></span>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-700 truncate w-32" title="<?php echo e($lider->name); ?>"><?php echo e($lider->name); ?></span>
                                                <span class="text-[8px] font-black text-slate-400 uppercase"><?php echo e($lider->role_label); ?></span>
                                            </div>
                                        </div>
                                        <span class="text-xs font-black text-indigo-600"><?php echo e($lider->toplam); ?> <span class="text-[8px] text-slate-400">Kayıt</span></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-[10px] text-slate-400">Veri yok.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-50 bg-emerald-50/50">
                                <h3 class="font-black text-emerald-900 text-[10px] uppercase tracking-widest">Sistem Kullanımı</h3>
                            </div>
                            <div class="p-4 space-y-3">
                                <?php $__empty_1 = true; $__currentLoopData = $enCokLoginOlanlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[9px] font-black"><?php echo e($index + 1); ?></span>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-700 truncate w-32" title="<?php echo e($lider->name); ?>"><?php echo e($lider->name); ?></span>
                                                <span class="text-[8px] font-black text-slate-400 uppercase"><?php echo e($lider->role_label); ?></span>
                                            </div>
                                        </div>
                                        <span class="text-xs font-black text-emerald-600"><?php echo e($lider->login_count); ?> <span class="text-[8px] text-slate-400">Giriş</span></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-[10px] text-slate-400">Veri yok.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-50 bg-rose-50/50">
                                <h3 class="font-black text-rose-900 text-[10px] uppercase tracking-widest">En Düşük Hata Oranı</h3>
                            </div>
                            <div class="p-4 space-y-3">
                                <?php $__empty_1 = true; $__currentLoopData = $enAzFireVerenler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-[9px] font-black"><?php echo e($index + 1); ?></span>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-700 truncate w-32" title="<?php echo e($lider->name); ?>"><?php echo e($lider->name); ?></span>
                                                <span class="text-[8px] font-black text-slate-400 uppercase"><?php echo e($lider->role_label); ?></span>
                                            </div>
                                        </div>
                                        <span class="text-xs font-black text-rose-600">%<?php echo e($lider->fire_orani); ?> <span class="text-[8px] text-slate-400">Hata</span></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-[10px] text-slate-400">Veri yok.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-slate-50 bg-indigo-50/30">
                            <h3 class="font-black text-indigo-900 text-sm uppercase tracking-widest">Ekip Performans Raporu</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/50">
                                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                        <th class="px-4 py-3">Personel Adı</th>
                                        <th class="px-4 py-3 text-center">Bölge/Yetki</th>
                                        <th class="px-4 py-3 text-center">Son Aktivite</th>
                                        <th class="px-4 py-3 text-center">Toplam Girilen</th>
                                        <th class="px-4 py-3 text-center">Hatalı Bildirim</th>
                                        <th class="px-4 py-3 text-center">Talep Kapanan</th>
                                        <th class="px-4 py-3 text-right">Son 7 Gün Performansı</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php $__empty_1 = true; $__currentLoopData = $ekipPerformansi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekipUyesi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-slate-700 text-xs">
                                                <?php echo e($ekipUyesi->name); ?>

                                                <div class="text-[9px] text-slate-400 font-normal mt-0.5">Toplam Login: <?php echo e($ekipUyesi->login_count); ?></div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest <?php echo e($ekipUyesi->role_label == 'Yurt İçi' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : ($ekipUyesi->role_label == 'Yurt Dışı' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-slate-100 text-slate-500 border border-slate-200')); ?>">
                                                    <?php echo e($ekipUyesi->role_label); ?>

                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center font-bold text-[10px] text-slate-500">
                                                <?php if($ekipUyesi->last_seen): ?>
                                                    <?php echo e($ekipUyesi->last_seen->diffForHumans()); ?>

                                                <?php else: ?>
                                                    Bilinmiyor
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3 text-center font-black text-slate-600"><?php echo e($ekipUyesi->toplam); ?></td>
                                            <td class="px-4 py-3 text-center font-black text-rose-500">
                                                <?php echo e($ekipUyesi->hatali_bildirim); ?>

                                                <div class="text-[8px] text-slate-400 font-normal">%<?php echo e($ekipUyesi->fire_orani); ?></div>
                                            </td>
                                            <td class="px-4 py-3 text-center font-black text-emerald-600"><?php echo e($ekipUyesi->talep_kapanan); ?></td>
                                            <td class="px-4 py-3 text-right font-black text-indigo-600"><?php echo e($ekipUyesi->son_7_gun); ?> Şikayet</td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 text-center text-xs text-slate-400 font-bold uppercase tracking-widest italic">Ekipte personel bulunmamaktadır.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                            <h3 class="font-black text-slate-800 uppercase tracking-tight text-sm italic">
                                <?php if($selectedUserId == 'all'): ?>
                                    Genel Kurul Analizi (Tüm Üyeler)
                                <?php else: ?>
                                    <?php echo e(optional($kurulUyeleri->firstWhere('id', $selectedUserId))->name ?? 'Seçili Üye'); ?> - Analiz Verileri
                                <?php endif; ?>
                            </h3>
                            <span class="text-[10px] font-black bg-slate-100 px-3 py-1 rounded-full text-slate-500"><?php echo e($stats_filtrelenmis['toplam']); ?> TOPLAM KAYIT</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-slate-100">
                            <div class="bg-white p-6">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Kategori Dağılımı</p>
                                <div class="flex flex-wrap gap-2">
                                    <?php $__empty_1 = true; $__currentLoopData = $stats_filtrelenmis['kategoriler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-tight">
                                            <?php echo e($kategori->ad); ?> <span class="ml-2 opacity-50"><?php echo e($kategori->toplam); ?></span>
                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span class="text-xs text-slate-400 italic">Veri bulunamadı.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="bg-white p-6 flex flex-col justify-center">
                                <div class="flex items-center gap-6">
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-amber-600"><?php echo e($stats_filtrelenmis['islemde']); ?></p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">İşlemde</p>
                                    </div>
                                    <div class="w-px h-8 bg-slate-100"></div>
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-emerald-600"><?php echo e($stats_filtrelenmis['cozulen']); ?></p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Çözülen</p>
                                    </div>
                                    <div class="w-px h-8 bg-slate-100"></div>
                                    <div class="text-center">
                                        <?php $successRate = $stats_filtrelenmis['toplam'] > 0 ? round(($stats_filtrelenmis['cozulen'] / $stats_filtrelenmis['toplam']) * 100) : 0; ?>
                                        <p class="text-2xl font-black text-indigo-600"><?php echo e($successRate); ?>%</p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Başarı</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
                                <div class="p-5">
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                                        <div class="flex items-center gap-3">
                                            <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-2 py-1 rounded border border-slate-100 tracking-widest uppercase">ID: #<?php echo e($sikayet->id); ?></span>
                                            <div class="font-black text-slate-800 text-lg tracking-tight"><?php echo e($sikayet->musteri_adi); ?></div>
                                        </div>
                                        <div class="scale-90 origin-right">
                                            <?php echo $sikayet->musteri_durum_badge; ?>

                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-slate-600 mb-6 line-clamp-2" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>">
                                        <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                    </p>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-slate-50">
                                        <div class="space-y-1">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Kategori</p>
                                            <p class="text-[11px] font-bold text-slate-700"><?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?></p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Sorumlu Takım</p>
                                            <p class="text-[11px] font-bold text-slate-700"><?php echo e($sikayet->cozumTakimi->ad ?? 'Atanmadı'); ?></p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Oluşturan</p>
                                            <div class="flex items-center gap-2" title="E-posta: <?php echo e($sikayet->olusturanKurulUyesi->email); ?> <?php echo e($sikayet->olusturanKurulUyesi->telefon ? ' | Tel: ' . $sikayet->olusturanKurulUyesi->telefon : ''); ?>">
                                                <?php if($sikayet->olusturanKurulUyesi): ?>
                                                    <img src="<?php echo e($sikayet->olusturanKurulUyesi->profile_photo_url); ?>" class="w-4 h-4 rounded-full" alt="">
                                                    <p class="text-[11px] font-bold text-slate-700"><?php echo e($sikayet->olusturanKurulUyesi->name); ?></p>
                                                <?php else: ?>
                                                    <div class="w-4 h-4 rounded-full bg-slate-100 flex items-center justify-center text-[8px] font-black text-slate-400">S</div>
                                                    <p class="text-[11px] font-bold text-slate-400 italic">Sistem / Dış Kaynak</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" class="p-2 bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $sikayet)): ?>
                                                <a href="<?php echo e(route('admin.sikayetler.edit', $sikayet)); ?>" class="p-2 bg-slate-50 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $sikayet)): ?>
                                                <form action="<?php echo e(route('admin.sikayetler.destroy', $sikayet)); ?>" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="p-2 bg-slate-50 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
                                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Bu kritere uygun kayıt bulunamadı.</p>
                            </div>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                <?php echo e($sikayetler->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/sikayetler/kurul.blade.php ENDPATH**/ ?>