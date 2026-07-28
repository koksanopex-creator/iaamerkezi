<div class="space-y-8 animate-fade-in-up mb-16">
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="col-span-1 md:col-span-2 bg-gradient-to-br from-indigo-900 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden group flex flex-col justify-between">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110 duration-500"></div>
            <div class="relative z-10">
                <h3 class="text-2xl font-bold mb-1">Disiplin Kurulu Başkanı Paneli</h3>
                <p class="text-indigo-200 text-sm italic">"Adalet ve Disiplin Kurumsal Kültürün Temelidir."</p>
                <div class="mt-8 flex items-center gap-4">
                    <div class="bg-white/10 px-4 py-3 rounded-xl backdrop-blur-sm border border-white/5 w-full">
                        <span class="text-xs text-indigo-300 block mb-1">Toplam İnceleme Kapsamındaki Dosya</span>
                        <span class="text-3xl font-bold"><?php echo e($stats['toplam_tutanak'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-amber-50 rounded-xl group-hover:bg-amber-100 transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg animate-pulse">
                    <?php echo e($stats['toplanti_bekleyen_sayisi'] ?? 0); ?> Bekleyen
                </span>
            </div>
            <h4 class="text-slate-500 text-sm font-medium uppercase tracking-wider">Kurul Toplantıları</h4>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-800"><?php echo e($stats['toplanti_bekleyen_sayisi'] ?? 0); ?></span>
                <span class="text-xs text-slate-400 font-medium">Toplantı Bekliyor</span>
            </div>
        </div>

        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-purple-50 rounded-xl group-hover:bg-purple-100 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-lg">
                    <?php echo e($stats['onay_bekleyen_sayisi'] ?? 0); ?> İşlemde
                </span>
            </div>
            <h4 class="text-slate-500 text-sm font-medium uppercase tracking-wider">Karar Onayları</h4>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-800"><?php echo e($stats['onay_bekleyen_sayisi'] ?? 0); ?></span>
                <span class="text-xs text-slate-400 font-medium">Dosya Kurulda</span>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-full">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-800">Kurul Gündemindeki / Toplantısı Bekleyen Dosyalar</h3>
                    <p class="text-xs text-slate-400 mt-1">Kurul gündemindeki aktif dosyalar</p>
                </div>
                <a href="<?php echo e(route('admin.disiplin.index', ['durum' => 'Kurulda'])); ?>" class="text-xs font-medium text-indigo-600 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Yönet &rarr;</a>
            </div>
            <div class="p-4 flex-1">
                <?php if(isset($stats['yaklasan_toplantilar']) && $stats['yaklasan_toplantilar']->isNotEmpty()): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $stats['yaklasan_toplantilar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vaka): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl hover:bg-white hover:shadow-md border border-transparent hover:border-slate-100 transition-all group gap-4">
                                
                                
                                <div class="flex-shrink-0">
                                    <?php if($vaka->user->profile_photo_path): ?>
                                        <img src="<?php echo e(asset('storage/' . $vaka->user->profile_photo_path)); ?>" 
                                             class="h-11 w-11 rounded-xl object-cover ring-2 ring-white shadow-sm" 
                                             alt="<?php echo e($vaka->user->name); ?>">
                                    <?php else: ?>
                                        <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-indigo-100 to-slate-100 flex items-center justify-center text-indigo-600 font-bold text-sm uppercase ring-2 ring-white shadow-sm">
                                            <?php echo e(substr($vaka->user->name ?? '?', 0, 2)); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>

                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="<?php echo e(route('profile.show', $vaka->user->id)); ?>" 
                                           class="text-sm font-bold text-slate-800 hover:text-indigo-600 transition-colors truncate">
                                            <?php echo e($vaka->user->name ?? 'Bilinmeyen Personel'); ?>

                                        </a>
                                        
                                        <?php
                                            $durumRenk = match($vaka->durum) {
                                                'Kurulda' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'Karar Verildi' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'Savunma Bekleniyor' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'İptal Edildi' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                default => 'bg-slate-100 text-slate-600 border-slate-200'
                                            };
                                        ?>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-lg border <?php echo e($durumRenk); ?> uppercase tracking-tight whitespace-nowrap">
                                            <?php echo e($vaka->durum); ?>

                                        </span>
                                    </div>

                                    
                                    <p class="text-xs text-slate-500 mt-0.5 truncate" title="<?php echo e($vaka->behavior->tanim ?? ''); ?>">
                                        <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wide mr-1">İhlal:</span>
                                        <?php echo e(Str::limit($vaka->behavior->tanim ?? 'İhlal Tanımı Yok', 55)); ?>

                                    </p>

                                    
                                    <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                        <span class="flex items-center gap-1 text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <?php echo e($vaka->user->bolum->ad ?? 'Bölümsüz'); ?>

                                        </span>
                                        <?php if($vaka->toplanti_tarihi): ?>
                                            <span class="flex items-center gap-1 text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                Toplantı: <?php echo e(\Carbon\Carbon::parse($vaka->toplanti_tarihi)->format('d.m.Y H:i')); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="flex items-center gap-1 text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                Tarih Belirlenmedi
                                            </span>
                                        <?php endif; ?>
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            Açıldı: <?php echo e($vaka->created_at->format('d.m.Y')); ?>

                                        </span>
                                    </div>
                                </div>

                                
                                <div class="flex-shrink-0">
                                    <a href="<?php echo e(route('admin.disiplin.show', $vaka->id)); ?>" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-bold hover:bg-indigo-600 hover:text-white transition-all shadow-sm whitespace-nowrap group-hover:shadow-md">
                                        Detay
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 text-slate-400 bg-slate-50/50 rounded-2xl border-2 border-dashed border-slate-100">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-medium">Gündemde bekleyen toplantı bulunmuyor.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="space-y-6">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-slate-800 text-sm mb-6 flex items-center gap-2 uppercase tracking-wider">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Bölüm Bazlı Tutanak Yoğunluğu
                </h3>
                <div class="space-y-5">
                    <?php $__currentLoopData = $stats['bolum_dagilimi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <div class="flex justify-between items-center text-xs mb-2">
                                <span class="font-bold text-slate-700"><?php echo e($bolum->ad); ?></span>
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded font-black"><?php echo e($bolum->disciplinary_cases_count); ?> Dosya</span>
                            </div>
                            <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden border border-slate-100">
                                <div class="bg-gradient-to-r from-rose-400 to-rose-600 h-2 rounded-full shadow-sm" style="width: <?php echo e(($stats['toplam_tutanak'] ?? 0) > 0 ? ($bolum->disciplinary_cases_count / $stats['toplam_tutanak']) * 100 : 0); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($stats['bolum_dagilimi']->isEmpty()): ?>
                   <p class="text-xs text-slate-400 text-center py-4 italic">Veri bulunamadı.</p>
                <?php endif; ?>
            </div>

            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2 uppercase tracking-wider">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Toplantısı Yapılmış Son Kararlar
                </h3>
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['son_kararlar'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $karar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-emerald-50 transition-colors border border-transparent hover:border-emerald-100 group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-emerald-600 shadow-sm font-bold text-xs uppercase border border-slate-100">
                                    <?php echo e(substr($karar->user->name ?? '?', 0, 1)); ?>

                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 line-clamp-1"><?php echo e($karar->user->name ?? '-'); ?></p>
                                    <p class="text-[9px] text-slate-400 font-medium"><?php echo e($karar->karar_tarihi ? \Carbon\Carbon::parse($karar->karar_tarihi)->format('d.m.Y') : '-'); ?></p>
                                </div>
                            </div>
                            <a href="<?php echo e(route('admin.disiplin.show', $karar->id)); ?>" class="p-1.5 text-slate-300 group-hover:text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-[10px] text-slate-400 italic text-center py-2">Henüz sonuçlanmış bir karar bulunmuyor.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Son Hareketler
                    </h3>
                    <a href="<?php echo e(route('admin.disiplin.index')); ?>" class="text-[11px] font-bold text-indigo-600 hover:bg-indigo-50 px-2.5 py-1 rounded-lg transition-colors">
                        Tümünü Gör →
                    </a>
                </div>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = ($stats['son_hareketler'] ?? collect())->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hareket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $hareketDurumRenk = match($hareket->durum) {
                                'Kurulda'               => 'text-blue-600',
                                'Karar Verildi'         => 'text-emerald-600',
                                'Savunma Bekleniyor'    => 'text-amber-600',
                                'İptal Edildi'          => 'text-rose-600',
                                'Yönetici Değerlendirmesi', 'Bölüm Liderinde', 'Yöneticide' => 'text-purple-600',
                                default                 => 'text-slate-500'
                            };
                            $hareketBarRenk = match($hareket->durum) {
                                'Kurulda'               => 'border-blue-300',
                                'Karar Verildi'         => 'border-emerald-300',
                                'Savunma Bekleniyor'    => 'border-amber-300',
                                'İptal Edildi'          => 'border-rose-300',
                                'Yönetici Değerlendirmesi', 'Bölüm Liderinde', 'Yöneticide' => 'border-purple-300',
                                default                 => 'border-slate-200'
                            };
                        ?>
                        <div class="flex items-start gap-3 border-l-2 <?php echo e($hareketBarRenk); ?> pl-3 py-1 hover:bg-slate-50 rounded-r-xl transition-colors group">
                            <div class="min-w-0 flex-1">
                                <a href="<?php echo e(route('admin.disiplin.show', $hareket->id)); ?>" 
                                   class="text-xs font-bold text-slate-800 hover:text-indigo-600 transition-colors truncate block">
                                    <?php echo e($hareket->user->name ?? '-'); ?>

                                </a>
                                <span class="text-[10px] font-bold <?php echo e($hareketDurumRenk); ?> leading-tight">
                                    <?php echo e($hareket->durum); ?>

                                </span>
                                <p class="text-[9px] text-slate-300 mt-0.5"><?php echo e($hareket->updated_at->diffForHumans()); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-[10px] text-slate-400 italic text-center py-4">Henüz hareket bulunmuyor.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard/partials/_disiplin_kurulu_baskani.blade.php ENDPATH**/ ?>