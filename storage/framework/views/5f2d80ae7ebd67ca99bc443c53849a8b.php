<?php
    $cardColor = 'slate'; 
    $cardIcon = 'info-circle';
    $cardTitle = 'Durum Belirsiz';

    if ($case->durum == 'Karar Verildi') {
        if ($case->final_karar == 'Savunma Kabul Edildi (Ceza Yok)') {
            $cardColor = 'emerald';
            $cardIcon = 'check-circle';
            $cardTitle = 'Dosya Kapatıldı - Savunma Kabul Edildi';
        } else {
            $cardColor = 'rose';
            $cardIcon = 'exclamation-circle';
            $cardTitle = 'Dosya Kapatıldı - Ceza Onaylandı';
        }
    } elseif ($case->durum == 'Kurulda') {
        $cardColor = 'amber';
        $cardIcon = 'users';
        $cardTitle = 'Dosya Disiplin Kuruluna Sevk Edildi';
    } elseif ($case->durum == 'İtiraz Edildi') {
        $cardColor = 'indigo';
        $cardIcon = 'reply';
        $cardTitle = 'Dosya İtiraz Aşamasında';
    } elseif ($case->durum == 'Yönetici Değerlendirmesi') {
        $cardColor = 'blue';
        $cardIcon = 'search';
        $cardTitle = 'Yönetici Değerlendirmesi Aşamasında';
    }
?>


<?php if(in_array($case->durum, ['Karar Verildi', 'Kurulda', 'İtiraz Edildi'])): ?>
    <div class="mt-6 relative w-full">
        <div class="bg-gradient-to-br from-<?php echo e($cardColor); ?>-50 to-white border-l-[6px] border-<?php echo e($cardColor); ?>-500 p-8 shadow-lg rounded-r-2xl relative overflow-hidden">
            
            <div class="absolute top-0 right-0 w-64 h-64 bg-<?php echo e($cardColor); ?>-100 opacity-20 rounded-full -mr-32 -mt-32 blur-2xl"></div>
            
            <div class="relative flex flex-col md:flex-row justify-between items-start gap-6">
                <div class="flex-1 w-full">
                    
                    
                    <div class="flex items-center gap-4 mb-6">
                        <?php
                            $themeColor = $cardColor;
                            if($cardColor == 'amber') $themeColor = 'orange'; // Yavru ağzı/Şeftali teması için
                        ?>
                        <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-<?php echo e($themeColor); ?>-500 to-<?php echo e($themeColor); ?>-700 rounded-2xl flex items-center justify-center shadow-lg transform -rotate-2">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <?php if($cardIcon == 'check-circle'): ?> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/> 
                                <?php elseif($cardIcon == 'exclamation-circle'): ?> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                <?php elseif($cardIcon == 'reply'): ?> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                <?php elseif($cardIcon == 'search'): ?> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                <?php else: ?> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                <?php endif; ?>
                            </svg>
                        </div>
                        
                        <div>
                            <h3 class="text-2xl font-black text-<?php echo e($themeColor); ?>-900 leading-tight uppercase tracking-tight italic"><?php echo e($cardTitle); ?></h3>
                            
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                
                                <?php
                                    $mainDateLabel = 'Dosya Sevk';
                                    $mainDate = $case->karar_tarihi;
                                    
                                    if ($case->durum == 'Yönetici Değerlendirmesi') {
                                        $mainDateLabel = 'Savunma Alındı';
                                        $mainDate = $case->savunma_tarihi;
                                    } elseif ($case->durum == 'Karar Verildi') {
                                        $mainDateLabel = 'Karar Tarihi';
                                    } elseif ($case->durum == 'İtiraz Edildi') {
                                        $mainDateLabel = 'İtiraz Tarihi';
                                        $mainDate = $case->appeals()->latest()->first()?->created_at ?? $case->karar_tarihi;
                                    }

                                    // FALLBACK: Eğer hala tarih yoksa (özellikle geri alma veya eski kayıtlar için) loglara bak
                                    if (!$mainDate) {
                                        if ($case->durum == 'Kurulda') {
                                            $log = $case->logs()->where('eylem', 'Kurula Sevk Edildi')->latest()->first();
                                            if ($log) $mainDate = $log->created_at;
                                        } elseif ($case->durum == 'Yönetici Değerlendirmesi') {
                                            $log = $case->logs()->whereIn('eylem', ['Savunma Verildi', 'Personel Savunma Verdi', 'Savunma Alındı'])->latest()->first();
                                            if ($log) $mainDate = $log->created_at;
                                        }
                                        
                                        // Hala yoksa created_at son çare
                                        if (!$mainDate) $mainDate = $case->created_at;
                                    }
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-white text-<?php echo e($themeColor); ?>-700 border border-<?php echo e($themeColor); ?>-200 shadow-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <?php echo e($mainDateLabel); ?>: <?php echo e($mainDate ? $mainDate->format('d.m.Y H:i') : 'Tarih Yok'); ?>

                                </span>

                                
                                <?php
                                    $isClosed = ($case->durum == 'Karar Verildi');
                                    $dateToShow = $isClosed ? ($case->oylama_bitti_at ?? $case->updated_at) : $case->toplanti_tarihi;
                                    $dateLabel = $isClosed ? 'Karar Bağlanma' : 'Güncel Toplantı';
                                ?>
                                <?php if($dateToShow): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold <?php echo e($isClosed ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-' . $themeColor . '-50 text-' . $themeColor . '-700 border-' . $themeColor . '-100'); ?> border shadow-sm">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <?php echo e($dateLabel); ?>: <?php echo e($dateToShow->format('d.m.Y H:i')); ?>

                                    </span>
                                <?php endif; ?>

                                
                                <?php if($case->rediscussion_count > 0): ?>
                                    <?php $isClosed = ($case->durum == 'Karar Verildi'); ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-black <?php echo e($isClosed ? 'bg-slate-700' : 'bg-orange-600 animate-pulse'); ?> text-white shadow-sm">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <?php echo e($case->rediscussion_count + 1); ?>. KEZ GÖRÜŞÜL<?php echo e($isClosed ? 'DÜ' : 'ÜYOR'); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <?php
                        $meetings = $case->toplantilar()->orderBy('baslangic_tarihi', 'asc')->get();
                    ?>
                    <?php if($meetings->count() > 1): ?>
                        <div class="mb-5 bg-white/40 rounded-xl p-3 border border-<?php echo e($themeColor); ?>-100">
                            <h4 class="text-[9px] font-black text-<?php echo e($themeColor); ?>-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Kurul Görüşme Geçmişi
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center gap-1.5 px-2 py-1 bg-white border border-<?php echo e($themeColor); ?>-100 rounded-lg shadow-sm">
                                        <span class="text-[8px] font-black text-<?php echo e($themeColor); ?>-400"><?php echo e($index + 1); ?>. Tur:</span>
                                        <span class="text-[10px] font-bold text-slate-700"><?php echo e($meeting->baslangic_tarihi->format('d.m.Y')); ?></span>
                                        <?php if($meeting->durum == 'tamamlandı'): ?>
                                            <svg class="w-2.5 h-2.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                        $canSeeRediscussionReason = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Hukuk Yöneticisi', 'Yonetim']);
                    ?>

                    <?php if($case->rediscussion_count > 0 && $case->rediscussion_reason && $canSeeRediscussionReason): ?>
                        <div class="mb-5 bg-orange-50/50 border border-orange-200 rounded-xl p-4 shadow-sm border-l-4 border-l-orange-500">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-orange-100 text-orange-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-black text-orange-700 uppercase tracking-widest mb-1 flex items-center gap-2">
                                        TEKRAR GÖRÜŞME GEREKÇESİ
                                        <span class="px-1.5 py-0.5 bg-orange-200 text-orange-800 rounded text-[8px]">Dahili Not</span>
                                    </h4>
                                    <p class="text-xs text-orange-800 font-medium leading-relaxed"><?php echo e($case->rediscussion_reason); ?></p>
                                    <?php
                                        $isClosed = ($case->durum == 'Karar Verildi');
                                        $finalDate = $isClosed ? ($case->oylama_bitti_at ?? $case->updated_at) : $case->toplanti_tarihi;
                                    ?>
                                    <?php if($finalDate): ?>
                                        <div class="mt-2 text-[10px] font-bold text-orange-600 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <?php echo e($isClosed ? 'Karar Bağlanma Tarihi' : 'Yeni Toplantı Tarihi'); ?>: <?php echo e($finalDate->format('d.m.Y H:i')); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-5">

                        
                        <?php if($case->is_appealed): ?>
                            <?php $appeal = $case->appeals()->latest()->first(); ?>
                            <?php if($appeal): ?>
                                <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-indigo-200">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-0.5 bg-indigo-600 text-white text-[9px] font-black rounded uppercase tracking-widest">İtiraz Kaydı</span>
                                        <span class="text-[10px] text-indigo-400 font-bold"><?php echo e($appeal->created_at->format('d.m.Y H:i')); ?></span>
                                    </div>
                                    <p class="text-xs text-indigo-900 font-bold mb-1 italic">"<?php echo e($appeal->reason); ?>"</p>
                                    <p class="text-[9px] text-indigo-500 font-medium">İtiraz Eden: <span class="font-bold"><?php echo e($appeal->user->name); ?></span></p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php
                            $isDisciplinedPerson = Auth::id() == $case->user_id;
                            $hasAuthorizedRole = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) || Auth::user()->can('disiplin.degerlendirme.gor');
                            // Tutanak yiyen personel (yetkili değilse) açıklama ve imza detaylarını görmesin
                            $canSeeManagerNotes = $hasAuthorizedRole || !$isDisciplinedPerson; 
                        ?>

                        
                        <?php if($case->durum == 'Karar Verildi' && $case->final_karar != 'Savunma Kabul Edildi (Ceza Yok)'): ?>
                            <div class="w-full bg-white/60 backdrop-blur-sm rounded-xl p-5 border border-rose-200 mb-5 shadow-sm">
                                <span class="block text-[10px] font-bold text-rose-400 uppercase tracking-wider mb-2">NİHAİ KARAR</span>
                                <h4 class="text-xl font-black text-slate-800 tracking-tight">
                                    <?php echo e($case->manual_penalty_name ?? $case->sistem_oneri_ceza); ?>

                                </h4>
                            </div>
                        <?php endif; ?>

                        
                        <?php if($case->yonetici_notu && $canSeeManagerNotes): ?>
                            <?php
                                $yoneticiNotu = $case->yonetici_notu;
                                $signatureData = null;
                                if (preg_match('/\((İşlemi Yapan: (.*?) \[ID:(\d+)\] - (.*?))\)\s*$/', $yoneticiNotu, $match)) {
                                    $signatureData = ['full' => $match[1], 'name' => $match[2], 'id' => $match[3], 'date' => $match[4]];
                                    $yoneticiNotu = trim(str_replace($match[0], '', $yoneticiNotu));
                                } elseif (preg_match('/\((İşlemi Yapan: (.*?) - (.*?))\)\s*$/', $yoneticiNotu, $match)) {
                                    $signatureData = ['full' => $match[1], 'name' => $match[2], 'date' => $match[3]];
                                    $yoneticiNotu = trim(str_replace($match[0], '', $yoneticiNotu));
                                }

                                // --- YEDEK MANTIK (ESKİ KAYITLAR İÇİN) ---
                                if (!$signatureData && $case->durum == 'Karar Verildi') {
                                    if ($case->adjuster) {
                                        $signatureData = [
                                            'name' => $case->adjuster->name,
                                            'id' => $case->adjuster->id,
                                            'date' => $case->karar_tarihi ? $case->karar_tarihi->format('d.m.Y H:i') : $case->updated_at->format('d.m.Y H:i')
                                        ];
                                    } else {
                                        $lastAction = $case->logs()->with('user')->whereIn('eylem', ['Ceza Onaylandı', 'Karar Verildi', 'Puan Düzeltme', 'Puan Düzeltildi'])->latest()->first();
                                        if ($lastAction && $lastAction->user) {
                                            $signatureData = [
                                                'name' => $lastAction->user->name,
                                                'id' => $lastAction->user->id,
                                                'date' => $lastAction->created_at->format('d.m.Y H:i')
                                            ];
                                        }
                                    }
                                }
                            ?>
                            
                            <div class="w-full bg-white/60 backdrop-blur-sm rounded-xl p-5 border border-<?php echo e($cardColor); ?>-200 relative group shadow-sm">
                                <div class="mb-0">
                                    <span class="block text-[10px] font-bold text-<?php echo e($cardColor); ?>-400 uppercase tracking-wider mb-2">YÖNETİCİ / KURUL AÇIKLAMASI</span>
                                    <p class="text-sm text-gray-700 italic leading-relaxed">"<?php echo e($yoneticiNotu); ?>"</p>
                                </div>

                                <?php if($signatureData): ?>
                                    <div class="mt-4 pt-3 border-t border-<?php echo e($cardColor); ?>-50 flex justify-end">
                                        <div class="text-right">
                                            <div class="flex items-center justify-end gap-2 text-[11px] font-medium text-rose-600">
                                                <span class="opacity-60 text-[9px] font-bold uppercase tracking-tighter">İŞLEMİ YAPAN:</span>
                                                <?php if(isset($signatureData['id'])): ?>
                                                    <a href="<?php echo e(url('/kullanici-profil/'.$signatureData['id'])); ?>" class="text-rose-700 font-black hover:underline">
                                                        <?php echo e($signatureData['name']); ?>

                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-rose-700 font-black uppercase"><?php echo e($signatureData['name']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-[10px] text-rose-400/80 font-bold mt-0.5">
                                                <?php echo e($signatureData['date']); ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        
                        <?php if($case->karar_dosyasi && count($case->karar_dosyasi) > 0): ?>
                            <div class="mt-4">
                                <span class="text-[10px] font-bold text-<?php echo e($cardColor); ?>-400 uppercase tracking-wider block mb-2">Karar / Tutanak Dosyaları</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full">
                                    <?php $__currentLoopData = $case->karar_dosyasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kFile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $kUrl = \Illuminate\Support\Facades\Storage::url($kFile);
                                            $kExt = strtolower(pathinfo($kFile, PATHINFO_EXTENSION));
                                            $kIsImage = in_array($kExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        ?>
                                        <a href="<?php echo e($kUrl); ?>" target="_blank" class="flex items-center gap-3 p-2.5 border border-<?php echo e($cardColor); ?>-200 rounded-xl bg-white hover:bg-<?php echo e($cardColor); ?>-50 transition group shadow-sm">
                                            <div class="h-10 w-10 flex-shrink-0 bg-<?php echo e($cardColor); ?>-50 rounded-lg flex items-center justify-center overflow-hidden border border-<?php echo e($cardColor); ?>-100">
                                                <?php if($kIsImage): ?> <img src="<?php echo e($kUrl); ?>" class="w-full h-full object-cover">
                                                <?php else: ?> <span class="text-[10px] font-black text-<?php echo e($cardColor); ?>-500 uppercase"><?php echo e($kExt); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold text-gray-800 truncate" title="<?php echo e(basename($kFile)); ?>"><?php echo e(basename($kFile)); ?></p>
                                                <p class="text-[9px] text-<?php echo e($cardColor); ?>-500 font-medium group-hover:underline">Görüntüle</p>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <?php
                    $isRevertible = in_array($case->durum, ['Karar Verildi', 'Kurulda']);
                ?>
                <?php if((Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']) || Auth::user()->can('disiplin.degerlendirme.kullan')) && $isRevertible): ?>
                    <div class="flex-shrink-0 mt-4 md:mt-0">
                        <form id="revoke_form_<?php echo e($case->id); ?>" action="<?php echo e(route('admin.disiplin.decision.revoke', $case->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="button" onclick="revokeDecision('revoke_form_<?php echo e($case->id); ?>')" class="bg-white/80 backdrop-blur border border-gray-300 text-gray-500 px-4 py-2 rounded-lg font-bold text-xs hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                Kararı Geri Al
                            </button>
                        </form>
                        <script>
                            function revokeDecision(formId) {
                                const status = '<?php echo e($case->durum); ?>';
                                let message = 'İşlemi geri almak üzeresiniz.';
                                
                                if (status === 'Karar Verildi') {
                                    message = 'Dosya tekrar oylama yapılabilmesi için <b>Kurul aşamasına</b> döndürülecek.<br>Varsa düşülen puan iade edilecek.';
                                } else if (status === 'Kurulda') {
                                    message = 'Kurul sevki iptal edilecek ve dosya tekrar <b>Yönetici Değerlendirmesi</b> aşamasına dönecek.';
                                }

                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'DİKKAT: Geri Alınıyor!',
                                        html: message + '<br><br><b>Onaylıyor musunuz?</b>',
                                        showCancelButton: true,
                                        confirmButtonText: 'Evet, Geri Al',
                                        cancelButtonText: 'İptal',
                                        confirmButtonColor: '#ef4444',
                                        cancelButtonColor: '#6b7280',
                                        customClass: {
                                            popup: 'rounded-3xl',
                                            confirmButton: 'rounded-xl px-6 py-3',
                                            cancelButton: 'rounded-xl px-6 py-3'
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            Swal.fire({ title: 'İşleniyor...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                                            document.getElementById(formId).submit();
                                        }
                                    });
                                } else {
                                    const plainMsg = message.replace(/<br>/g, '\n').replace(/<b>/g, '').replace(/<\/b>/g, '');
                                    if (confirm('DİKKAT: Geri Alınıyor!\n\n' + plainMsg + '\n\nOnaylıyor musunuz?')) {
                                        document.getElementById(formId).submit();
                                    }
                                }
                            }
                        </script>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>




<?php if(in_array($case->durum, ['Yönetici Değerlendirmesi', 'İtiraz Edildi'])): ?>
    <?php if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']) || Auth::user()->can('disiplin.degerlendirme.kullan')): ?>
        <div id="manager_evaluation_section" x-data="{ 
            selectedFiles: [],
            handleFileSelect(event) {
                this.selectedFiles = Array.from(event.target.files).map(f => f.name);
            }
        }" class="relative bg-gradient-to-br from-indigo-50 to-white border-2 border-indigo-100 rounded-2xl p-8 shadow-xl overflow-hidden scroll-mt-32">
            
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-100 rounded-full opacity-20 -mr-32 -mt-32 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-100 rounded-full opacity-20 -ml-20 -mb-20 blur-3xl pointer-events-none"></div>

            <div class="flex items-center gap-4 mb-8 relative z-10">
                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl flex items-center justify-center shadow-lg transform -rotate-3">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-gray-800 tracking-tight">Yönetici Değerlendirmesi</h3>
                    <p class="text-sm text-gray-500 font-medium">Lütfen savunmayı inceleyip nihai kararınızı verin.</p>
                </div>
            </div>

            <form id="manager_decision_form" method="POST" enctype="multipart/form-data" class="relative z-10">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="manual_penalty_name" id="manual_penalty_name_input">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="lg:col-span-7">
                        <div class="bg-white/80 rounded-xl p-1 shadow-sm border border-indigo-100 h-full">
                            <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2 px-2 pt-2">
                                Karar Gerekçesi / Notunuz <span class="text-red-500">*</span>
                            </label>
                            <textarea id="manager_note_area" name="yonetici_notu" rows="8" class="w-full border-0 bg-transparent text-gray-700 text-sm focus:ring-0 resize-none p-3 placeholder-gray-400" placeholder="Kararınızın gerekçesini buraya detaylıca yazınız..."></textarea>
                            <div class="border-t border-gray-100 p-2 flex justify-end">
                                <span class="text-[10px] text-gray-400 italic">Yönetici imzası otomatik eklenecektir.</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5 space-y-6">
                        <div class="bg-white/80 rounded-xl p-4 border border-indigo-100 shadow-sm">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Karar Dosyaları (Opsiyonel)</label>
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-indigo-200 border-dashed rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-400 transition-all group">
                                <div class="flex flex-col items-center justify-center pt-2">
                                    <svg class="w-6 h-6 mb-1 text-indigo-300 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <p class="text-[10px] text-gray-500"><span class="font-bold text-indigo-600" x-text="selectedFiles.length > 0 ? selectedFiles.length + ' dosya seçildi' : 'Dosya seçin'">Dosya seçin</span> veya sürükleyin</p>
                                </div>
                                <input type="file" name="karar_dosyalari[]" class="hidden" multiple @change="handleFileSelect" />
                            </label>
                            <template x-if="selectedFiles.length > 0">
                                <div class="mt-3 space-y-1">
                                    <template x-for="name in selectedFiles">
                                        <div class="text-[10px] text-indigo-600 flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded truncate">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                            <span x-text="name"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <div class="bg-white/80 rounded-xl p-4 border border-indigo-100 shadow-sm">
                            <?php
                                $existingMeeting = $case->toplantilar()->whereIn('durum', ['planlandı', 'devam_ediyor'])->first();
                            ?>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Kurul Toplantı Tarihi <span class="text-[9px] font-normal text-gray-400 ml-1">(Sadece Sevk İçin)</span></label>
                            <?php if($existingMeeting): ?>
                                <div class="mb-3 bg-amber-50 text-amber-700 px-4 py-3 rounded-xl border border-amber-200 shadow-sm relative overflow-hidden group">
                                    <div class="absolute top-0 right-0 p-1 opacity-10 group-hover:opacity-20 transition-opacity">
                                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div class="flex flex-col gap-3 relative z-10">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <div class="text-[11px] font-bold leading-tight">
                                                BU DOSYA ZATEN BİR TOPLANTIYA BAĞLIDIR<br>
                                                <span class="font-black text-amber-900 text-xs mt-1 block tracking-tight"><?php echo e($existingMeeting->baslangic_tarihi->format('d.m.Y H:i')); ?></span>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            
                                            <?php if($case->durum != 'Kurulda'): ?>
                                                <button type="button" 
                                                        onclick="managerSubmitForm('<?php echo e(route('admin.disiplin.board.send', $case->id)); ?>', 'Dosya durumu KURULDA olarak güncellenecek. Emin misiniz?')"
                                                        class="flex-1 bg-amber-600 hover:bg-amber-700 text-white text-[10px] font-black py-1.5 px-3 rounded-lg shadow-sm transition-all flex items-center justify-center gap-1.5 uppercase tracking-tighter">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    DURUMU 'KURULDA' YAP
                                                </button>
                                            <?php endif; ?>

                                            
                                            <a href="<?php echo e(route('admin.disiplin.kurul.toplanti.show', $existingMeeting->id)); ?>" 
                                               target="_blank"
                                               class="flex-1 bg-white border border-amber-200 text-amber-700 hover:bg-amber-100 text-[10px] font-black py-1.5 px-3 rounded-lg shadow-sm transition-all flex items-center justify-center gap-1.5 uppercase tracking-tighter">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                TOPLANTI DETAYI
                                            </a>

                                            
                                            <?php if(Str::startsWith($existingMeeting->yer, ['http://', 'https://'])): ?>
                                                <a href="<?php echo e($existingMeeting->yer); ?>" 
                                                   target="_blank"
                                                   class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black py-1.5 px-3 rounded-lg shadow-sm transition-all flex items-center justify-center gap-1.5 uppercase tracking-tighter">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    ONLİNE TOPLANTIYA KATIL
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input type="datetime-local" id="toplanti_tarihi_input" name="toplanti_tarihi" value="<?php echo e($existingMeeting ? $existingMeeting->baslangic_tarihi->format('Y-m-d\TH:i') : ''); ?>" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 mb-4">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mt-4 mb-2">Toplantı Lokasyonu / Linki <span class="text-[9px] font-normal text-gray-400 ml-1">(Sadece Sevk İçin)</span></label>
                            <input type="text" id="toplanti_yeri_input" name="toplanti_yeri" value="<?php echo e($existingMeeting ? $existingMeeting->yer : ''); ?>" placeholder="Örn: Toplantı Odası 1 veya Zoom Linki" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-indigo-100">
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2"><span class="w-8 h-1 bg-indigo-500 rounded-full"></span>Nihai Kararı Verin</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <button type="button" onclick="managerSubmitForm('<?php echo e(route('admin.disiplin.penalty.approve', $case->id)); ?>', 'Ceza onaylanacak ve <?php echo e($case->hesaplanan_puan); ?> puan düşülecek. Emin misiniz?')" id="approve_penalty_btn" class="group relative overflow-hidden bg-white border-2 border-rose-100 rounded-xl p-4 hover:border-rose-500 hover:shadow-lg transition-all duration-300 text-left">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-rose-50 rounded-bl-full -mr-8 -mt-8 transition-all group-hover:bg-rose-100"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 mb-3 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <h5 class="font-bold text-gray-800 group-hover:text-rose-700">Cezayı Onayla</h5>
                                <p class="text-xs text-gray-500 mt-1">Puan düşülür ve dosya kapatılır.</p>
                            </div>
                        </button>
                        <button type="button" onclick="managerSubmitForm('<?php echo e(route('admin.disiplin.defense.accept', $case->id)); ?>', 'Dosya cezasız kapatılacak. Emin misiniz?')" class="group relative overflow-hidden bg-white border-2 border-emerald-100 rounded-xl p-4 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 text-left">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-50 rounded-bl-full -mr-8 -mt-8 transition-all group-hover:bg-emerald-100"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 mb-3 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h5 class="font-bold text-gray-800 group-hover:text-emerald-700">Savunmayı Kabul Et</h5>
                                <p class="text-xs text-gray-500 mt-1">Ceza verilmez, dosya kapatılır.</p>
                            </div>
                        </button>
                        <button type="button" onclick="managerSubmitForm('<?php echo e(route('admin.disiplin.board.send', $case->id)); ?>', 'Dosya Kurulama sevk edilecek. Emin misiniz?')" class="group relative overflow-hidden bg-white border-2 border-slate-200 rounded-xl p-4 hover:border-slate-800 hover:shadow-lg transition-all duration-300 text-left">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-slate-50 rounded-bl-full -mr-8 -mt-8 transition-all group-hover:bg-slate-200"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 mb-3 group-hover:bg-slate-800 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <h5 class="font-bold text-gray-800 group-hover:text-slate-900">Kurula Sevk Et</h5>
                                <p class="text-xs text-gray-500 mt-1">Disiplin Kurulu değerlendirir.</p>
                            </div>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <script>
            function managerSubmitForm(route, confirmMsg) {
                const noteArea = document.getElementById('manager_note_area');
                const noteValue = noteArea ? noteArea.value.trim() : '';
                if (noteValue === '') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'warning', title: 'Eksik Bilgi', text: 'Lütfen bir karar gerekçesi / notu yazınız.', confirmButtonText: 'Tamam' }).then(() => { if(noteArea) noteArea.focus(); });
                    } else {
                        alert('Lütfen bir karar gerekçesi / notu yazınız.');
                        if(noteArea) noteArea.focus();
                    }
                    return;
                }

                // EĞER CEZA ONAYI İSE VE YETKİLİ İSE MODAL AÇ
                const isApprove = route.includes('cezayi-onayla');
                const isAuthorized = <?php echo e(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı']) ? 'true' : 'false'); ?>;

                if (isApprove && isAuthorized) {
                    showPenaltySelectionModal(route);
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'question', title: 'Emin misiniz?', text: confirmMsg, showCancelButton: true, confirmButtonText: 'Evet, Onaylıyorum', cancelButtonText: 'İptal', confirmButtonColor: '#4f46e5', cancelButtonColor: '#ef4444' }).then((result) => { if (result.isConfirmed) { submitTheForm(route); } });
                    } else {
                        if (confirm(confirmMsg)) { submitTheForm(route); }
                    }
                }
            }

            function showPenaltySelectionModal(route) {
                const systemSuggested = "<?php echo e($case->sistem_oneri_ceza); ?>";
                const scales = <?php echo json_encode($scales ?? [], 15, 512) ?>;

                let manualOptionsHtml = '<select id="swal_manual_penalty" class="w-full mt-2 p-2 border rounded-lg text-sm bg-gray-50 border-gray-200">';
                scales.forEach(s => {
                    manualOptionsHtml += `<option value="${s.ceza_adi}">${s.min_puan}-${s.max_puan} Puan - ${s.ceza_adi}</option>`;
                });
                manualOptionsHtml += '</select>';

                Swal.fire({
                    title: 'Ceza Uygulama Yöntemi',
                    html: `
                        <div class="text-left space-y-6">
                            <div class="p-4 rounded-xl border-2 border-rose-100 bg-rose-50/30">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="penalty_choice" value="system" checked class="w-5 h-5 text-rose-600 focus:ring-rose-500 border-rose-300">
                                    <div class="flex-1">
                                        <span class="block text-sm font-black text-rose-900 uppercase tracking-tight">SİSTEMİN ÖNERDİĞİ CEZA</span>
                                        <span class="block text-xs font-bold text-rose-600 italic mt-0.5">${systemSuggested}</span>
                                    </div>
                                </label>
                            </div>

                            <div class="p-4 rounded-xl border-2 border-indigo-100 bg-indigo-50/30">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="penalty_choice" value="manual" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-indigo-300">
                                    <div class="flex-1">
                                        <span class="block text-sm font-black text-indigo-900 uppercase tracking-tight">MANUEL CEZA SEÇİMİ</span>
                                        <span class="block text-[10px] text-indigo-400 font-bold uppercase tracking-tighter mt-0.5 mb-2">Puan Skalasından Seçin</span>
                                        ${manualOptionsHtml}
                                    </div>
                                </label>
                            </div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-center italic">Manuel seçim yapıldığında sistem önerisi gizlenecektir.</p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Devam Et',
                    cancelButtonText: 'İptal',
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#6b7280',
                    width: '500px',
                    preConfirm: () => {
                        const choice = document.querySelector('input[name="penalty_choice"]:checked').value;
                        const manualVal = document.getElementById('swal_manual_penalty').value;
                        return { choice, manualVal };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value.choice === 'manual') {
                            document.getElementById('manual_penalty_name_input').value = result.value.manualVal;
                        } else {
                            document.getElementById('manual_penalty_name_input').value = ''; // empty means use system suggested
                        }
                        submitTheForm(route);
                    }
                });
            }

            function submitTheForm(route) {
                const form = document.getElementById('manager_decision_form');
                if (form) {
                    form.action = route;
                    Swal.fire({ title: 'İşleniyor...', text: 'Lütfen bekleyiniz', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                    form.submit();
                }
            }
        </script>
    <?php else: ?>
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-2xl p-8 shadow-md">
            <div class="flex items-center gap-5">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-blue-900">Yönetici Girişi Tamamlandı</h3>
                    <p class="text-blue-700 text-sm mt-1">Savunma sisteme başarıyla kaydedildi. Şu an <b>Hukuk ve Üst Yönetim</b> tarafından incelenmektedir.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/partials/manager-actions.blade.php ENDPATH**/ ?>