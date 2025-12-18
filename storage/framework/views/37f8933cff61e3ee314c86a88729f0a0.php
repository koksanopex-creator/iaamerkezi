<div class="space-y-6">
    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div x-data="{ openLogs: false }" class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl transition-all duration-300 relative group overflow-hidden">
        
        
        <?php
            $statusClass = match($sikayet->musteri_durum) {
                'Yeni' => 'bg-yellow-400',
                'İşlemde', 'İnceleniyor' => 'bg-blue-500',
                'Tamamlandı', 'Çözümlendi', 'Kapatıldı' => 'bg-green-500',
                'Gecikmiş' => 'bg-red-500',
                default => 'bg-gray-400'
            };
        ?>
        <div class="absolute left-0 top-0 bottom-0 w-2 <?php echo e($statusClass); ?>"></div>

        <div class="p-5 md:p-6 pl-7"> 
            
            
            <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-5">
                <div class="flex items-start gap-4 w-full">
                    
                    <div class="flex-shrink-0">
                        <!--[if BLOCK]><![endif]--><?php if($sikayet->customer && $sikayet->customer->logo): ?>
                            <img class="h-16 w-16 rounded-xl object-contain border border-gray-200 shadow-sm bg-white p-1" 
                                 src="<?php echo e(asset('storage/' . $sikayet->customer->logo)); ?>" 
                                 alt="<?php echo e($sikayet->customer->name); ?>">
                        <?php else: ?>
                            <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-md">
                                <?php echo e($sikayet->customer ? strtoupper(substr($sikayet->customer->name, 0, 1)) : strtoupper(substr($sikayet->musteri_adi, 0, 1))); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="flex-1 min-w-0">
                        
                        <div class="mb-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Firma İsmi:</span>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl font-black text-gray-900 leading-tight truncate">
                                    <?php echo e($sikayet->customer ? $sikayet->customer->name : $sikayet->musteri_adi); ?>

                                </h2>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    #<?php echo e($sikayet->id); ?>

                                </span>
                            </div>
                        </div>

                        
                        <div class="text-sm text-gray-600 mb-4 bg-gray-50 p-2 rounded-lg border border-gray-100 inline-block">
                            <!--[if BLOCK]><![endif]--><?php if($sikayet->yetkili_user): ?>
                                <div class="font-bold text-gray-800 mb-1 flex items-center gap-1">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <?php echo e($sikayet->yetkili_user->name); ?>

                                </div>
                                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                                    <!--[if BLOCK]><![endif]--><?php if($sikayet->yetkili_user->telefon): ?>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        <?php echo e($sikayet->yetkili_user->telefon); ?>

                                    </span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($sikayet->yetkili_user->email): ?>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        <?php echo e($sikayet->yetkili_user->email); ?>

                                    </span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php else: ?>
                                <span class="italic text-gray-500"><?php echo e($sikayet->musteri_iletisim ?? 'İletişim bilgisi yok'); ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Şikayet Konusu:</span>
                            <h3 class="text-base font-bold text-indigo-700 leading-snug">
                                <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                            </h3>
                        </div>
                    </div>
                </div>

                
                <div class="flex-shrink-0">
                    <?php echo $sikayet->musteri_durum_badge; ?>

                </div>
            </div>

            
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 text-sm mb-5">
                
                
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Kategori</span>
                    <div class="font-bold text-gray-700"><?php echo e($sikayet->sikayetKategori->ad ?? 'Genel'); ?></div>
                </div>

                
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Alt Kategori</span>
                    <div class="font-medium text-gray-600"><?php echo e($sikayet->sikayetAltKategori->ad ?? 'N/A'); ?></div>
                </div>

                
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Takım</span>
                    <div class="font-medium text-gray-700"><?php echo e($sikayet->cozumTakimi->ad ?? 'Atanmadı'); ?></div>
                </div>

                
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Öncelik</span>
                    <?php
                        $oncelikClass = match($sikayet->musteri_oncelik) {
                            'Acil' => 'text-red-600 bg-red-50 border-red-100',
                            'Yüksek' => 'text-orange-600 bg-orange-50 border-orange-100',
                            'Normal' => 'text-blue-600 bg-blue-50 border-blue-100',
                            'Düşük' => 'text-green-600 bg-green-50 border-green-100',
                            default => 'text-gray-600 bg-gray-100 border-gray-200'
                        };
                    ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded border text-xs font-bold <?php echo e($oncelikClass); ?>">
                        <!--[if BLOCK]><![endif]--><?php if($sikayet->musteri_oncelik == 'Acil'): ?> 🔥 <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php echo e($sikayet->musteri_oncelik); ?>

                    </span>
                </div>

                
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Konum</span>
                    <div class="font-medium text-gray-700"><?php echo e($sikayet->konum_tipi ?? 'Belirtilmedi'); ?></div>
                </div>

                
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Tarihler</span>
                    <div class="font-medium text-gray-700 text-xs mb-1">
                        <span class="text-gray-400">Kayıt:</span> <?php echo e($sikayet->created_at->format('d.m.Y')); ?>

                    </div>
                    <div class="font-bold text-xs text-red-600">
                        <span class="text-red-400">Son:</span> 
                        <?php echo e($sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A'); ?>

                    </div>
                </div>

                
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Ekleyen</span>
                    <!--[if BLOCK]><![endif]--><?php if($sikayet->olusturanKurulUyesi): ?>
                        <a href="<?php echo e(route('profile.show', $sikayet->olusturanKurulUyesi->id)); ?>" target="_blank" class="text-indigo-600 font-bold hover:underline flex items-center gap-1">
                            <?php echo e(Str::limit($sikayet->olusturanKurulUyesi->name, 15)); ?>

                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    <?php else: ?>
                        <span class="text-gray-600">Sistem</span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Puan</span>
                    <div class="font-bold flex items-center gap-1 <?php echo e($sikayet->musteri_puan ? 'text-yellow-600' : 'text-gray-400'); ?>">
                        <!--[if BLOCK]><![endif]--><?php if($sikayet->musteri_puan): ?> <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php echo e($sikayet->musteri_puan ?? 'N/A'); ?>

                    </div>
                </div>

            </div>

            
            <!--[if BLOCK]><![endif]--><?php if($sikayet->iaaProjesi || $sikayet->musteri_feedback || !$sikayet->iaaProjesi): ?>
                <div class="sm:ml-14 mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-x-4 gap-y-3 text-sm bg-gray-50/70 rounded-lg p-3 border border-gray-200/60 mb-5">
                    
                    
                    <div class="flex flex-wrap items-center gap-2 text-gray-600">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="font-medium mr-1">Proje Durumu:</span>

                        <!--[if BLOCK]><![endif]--><?php if($sikayet->iaaProjesi): ?>
                            
                            <?php
                                $pDurum = $sikayet->iaaProjesi->durum;
                                $pRenk = match($pDurum) {
                                    'Bölüm Onayı Bekliyor' => 'purple',
                                    'Yönetici Onayı Bekliyor', 'Atandı' => 'blue',
                                    'Revize Ediliyor' => 'orange',
                                    'Tamamlandı' => 'green',
                                    'Reddedildi' => 'red',
                                    default => 'gray'
                                };
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-<?php echo e($pRenk); ?>-50 text-<?php echo e($pRenk); ?>-700 border border-<?php echo e($pRenk); ?>-200">
                                <?php echo e($pDurum); ?>

                            </span>

                            
                            <!--[if BLOCK]><![endif]--><?php if($pDurum == 'Tamamlandı'): ?>
                                <?php
                                    $bitisTarihi = $sikayet->iaaProjesi->updated_at; 
                                    $gecenGun = ceil($sikayet->created_at->diffInDays($bitisTarihi));
                                    if($gecenGun == 0) $gecenGun = 1; 
                                ?>
                                <span class="text-xs font-bold text-green-600 flex items-center gap-1 ml-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    (<?php echo e($gecenGun); ?> Günde Çözüldü)
                                </span>
                            <?php else: ?>
                                <?php
                                    $gecenGun = ceil($sikayet->created_at->diffInDays(now()));
                                ?>
                                <span class="text-xs font-bold text-red-500 flex items-center gap-1 ml-1 animate-pulse">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    (<?php echo e($gecenGun); ?> Gündür Sürüyor)
                                </span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <?php else: ?>
                            
                            <?php
                                $gecenGun = ceil($sikayet->created_at->diffInDays(now()));
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-red-50 text-red-700 border border-red-200 animate-pulse">
                                Çözüm Bekliyor (<?php echo e($gecenGun); ?> Gündür)
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    
                    <!--[if BLOCK]><![endif]--><?php if($sikayet->musteri_feedback): ?>
                        <?php
                            $fbRenk = match($sikayet->musteri_feedback) {
                                'Onaylandı' => 'text-green-600 bg-green-50 border-green-100',
                                'Reddedildi' => 'text-red-600 bg-red-50 border-red-100',
                                'Revizyon İstendi' => 'text-amber-600 bg-amber-50 border-amber-100',
                                default => 'text-gray-600 bg-gray-50 border-gray-100'
                            };
                            $fbLog = $sikayet->loglar->where('eylem', 'Müşteri Geri Bildirimi')->sortByDesc('created_at')->first();
                            $fbTarih = $fbLog ? $fbLog->created_at->format('d.m.Y H:i') : $sikayet->updated_at->format('d.m.Y H:i');
                        ?>
                        <div class="mt-2 flex items-start gap-2 p-2 rounded-lg border <?php echo e($fbRenk); ?>">
                            <div class="mt-0.5 flex-shrink-0">
                                <!--[if BLOCK]><![endif]--><?php if($sikayet->musteri_feedback == 'Onaylandı'): ?> <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <?php elseif($sikayet->musteri_feedback == 'Reddedildi'): ?> <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <?php else: ?> <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center w-full">
                                    <span class="text-xs font-bold uppercase">Müşteri: <?php echo e($sikayet->musteri_feedback); ?></span>
                                    <span class="text-[10px] opacity-60 font-medium ml-2 whitespace-nowrap"><?php echo e($fbTarih); ?></span>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if($sikayet->musteri_feedback_note): ?>
                                    <p class="text-xs mt-0.5 italic opacity-90 truncate" title="<?php echo e($sikayet->musteri_feedback_note); ?>">"<?php echo e($sikayet->musteri_feedback_note); ?>"</p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <div class="flex flex-wrap items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button @click="openLogs = !openLogs" class="text-xs font-bold text-gray-500 hover:text-indigo-600 bg-gray-50 hover:bg-indigo-50 px-3 py-2 rounded-lg transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span x-text="openLogs ? 'Geçmişi Gizle' : 'Geçmişi Gör'"></span>
                </button>
                <div class="flex-grow"></div>
                <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Müşteri Şikayeti Kurulu')): ?>
                    <button wire:click="$dispatch('openTriyajModal', { id: <?php echo e($sikayet->id); ?> })" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Yönet
                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all">Detay</a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $sikayet)): ?>
                    <a href="<?php echo e(route('admin.sikayetler.edit', $sikayet)); ?>" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all">Düzenle</a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $sikayet)): ?>
                    <button wire:click="delete(<?php echo e($sikayet->id); ?>)" wire:confirm="Silmek istediğinize emin misiniz?" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition-all">Sil</button>
                <?php endif; ?>
                <!--[if BLOCK]><![endif]--><?php if($sikayet->iaa_id): ?>
                    <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaa_id)); ?>" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg> Projeye Git
                    </a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div x-show="openLogs" x-transition class="mt-4 pt-4 border-t border-gray-100 bg-gray-50/50 -mx-6 -mb-6 p-6">
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">İşlem Geçmişi</h4>
                <div class="space-y-3">
                    <!--[if BLOCK]><![endif]--><?php $__empty_2 = true; $__currentLoopData = $sikayet->loglar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                        <div class="flex gap-3 text-sm">
                            <div class="text-xs font-mono text-gray-400 w-24 flex-shrink-0"><?php echo e($log->created_at->format('d.m H:i')); ?></div>
                            <div class="text-gray-700"><?php echo e($log->aciklama); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                        <div class="text-gray-400 italic text-xs">Henüz bir işlem kaydı yok.</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="text-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Şikayet bulunamadı</h3>
        <p class="mt-1 text-sm text-gray-500">Arama kriterlerinizi değiştirin veya yeni bir şikayet oluşturun.</p>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/sikayetler-partials/cards.blade.php ENDPATH**/ ?>