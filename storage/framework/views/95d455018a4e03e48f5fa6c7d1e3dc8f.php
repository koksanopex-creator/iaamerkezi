<div class="space-y-6">
    
    <div class="bg-white shadow-xl shadow-slate-200/50 sm:rounded-2xl border border-slate-100 overflow-hidden">
        <div class="bg-slate-50 border-b border-slate-100 px-6 py-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-500 text-white flex items-center justify-center shadow-sm shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Sistem Değerlendirmesi</h3>
        </div>
        <div class="p-6">
            <?php if($case->durum == 'Karar Verildi' && $case->final_karar == 'Savunma Kabul Edildi (Ceza Yok)'): ?>
                <div class="text-center py-6 bg-emerald-50 rounded-2xl border border-dashed border-emerald-200">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm font-black text-emerald-700 uppercase tracking-tight">Ceza Verilmedi</p>
                    <p class="text-[10px] text-emerald-600 mt-1 font-bold">Savunma haklı bulunmuştur.</p>
                </div>
            <?php else: ?>
                <?php if($case->durum == 'Karar Verildi'): ?>
                    <div class="mb-6 p-5 bg-rose-600 rounded-2xl border border-rose-700 flex items-center gap-4 shadow-lg shadow-rose-200">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-white shrink-0 border border-white/30">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-black text-white uppercase leading-none tracking-tighter">Ceza Verilmiştir</p>
                            <p class="text-[9px] font-bold text-rose-100 mt-1 uppercase opacity-80">Dosya Kapatıldı</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                    $isAdminOrLider = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Bölüm Lideri', 'Direktör', 'Yönetim']);
                    $hasManualPenalty = !empty($case->manual_penalty_name);
                ?>

                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm text-gray-600">Etki Puanı</span>
                    <span class="font-bold text-gray-800"><?php echo e($case->impact->puan ?? 0); ?></span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Kapsam Puanı</span>
                    <span class="font-bold text-gray-800">x <?php echo e($case->scope->puan ?? 0); ?></span>
                </div>
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <span class="text-sm text-gray-600">Tekrar Katsayısı (<?php echo e($case->tekrar_sayisi); ?>. Kez)</span>
                    <span class="font-bold text-indigo-600">x <?php echo e($case->tekrar_sayisi >= 4 ? 5 : $case->tekrar_sayisi); ?></span>
                </div>
                <div class="flex justify-between items-center bg-gray-100 p-3 rounded mb-4">
                    <span class="font-bold text-gray-700">Toplam Puan</span>
                    <span class="font-black text-2xl text-indigo-700"><?php echo e($case->hesaplanan_puan); ?></span>
                </div>

                <div class="space-y-4">
                    
                    <?php if($isAdminOrLider || !$hasManualPenalty): ?>
                        <div class="text-center p-3 rounded-xl border border-rose-100 bg-rose-50/50">
                            <span class="text-[10px] font-black text-rose-400 uppercase tracking-widest block mb-1">Sistem Önerisi</span>
                            <div class="text-sm font-bold text-rose-700 italic">
                                <?php echo e($case->sistem_oneri_ceza); ?>

                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($hasManualPenalty): ?>
                        <div class="text-center p-4 rounded-xl border-2 border-indigo-200 bg-indigo-50 shadow-sm animate-in zoom-in duration-500">
                            <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block mb-1">Uygulanan Nihai Karar</span>
                            <div class="text-lg font-black text-indigo-800 uppercase tracking-tight">
                                <?php echo e($case->manual_penalty_name); ?>

                            </div>
                            <?php if($isAdminOrLider && $case->adjuster): ?>
                                <div class="mt-2 pt-2 border-t border-indigo-100 flex items-center justify-center gap-1.5">
                                    <div class="w-4 h-4 rounded-full bg-indigo-600 flex items-center justify-center text-[8px] text-white font-bold">
                                        <?php echo e(substr($case->adjuster->name, 0, 1)); ?>

                                    </div>
                                    <span class="text-[9px] text-indigo-400 font-bold">Uygulayan: <?php echo e($case->adjuster->name); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    
    <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Künye</h3>
        <div class="text-sm">
            <p class="text-gray-500 mb-1">Tutanak No:</p>
            <p class="font-mono font-bold text-gray-800 mb-3">#<?php echo e($case->id); ?></p>
            <p class="text-gray-500 mb-1">Raporlayan Amir:</p>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600"><?php echo e(substr($case->reporter->name ?? '?', 0, 1)); ?></div>
                <span class="font-medium text-gray-800"><?php echo e($case->reporter->name ?? 'Bilinmiyor'); ?></span>
            </div>
            <p class="text-gray-500 mt-3 mb-1">Oluşturulma:</p>
            <p class="text-gray-800"><?php echo e($case->created_at->format('d.m.Y H:i')); ?></p>

            
            <?php if($case->durum == 'Karar Verildi'): ?>
                <?php
                    $closedAt = $case->oylama_bitti_at ?? $case->updated_at;
                    // Küsüratı yukarı yuvarlayarak tam gün sayısını alıyoruz
                    $days = ceil($case->created_at->diffInSeconds($closedAt) / 86400);
                ?>
                <div class="mt-4 pt-4 border-t border-slate-100 animate-in fade-in slide-in-from-top-1 duration-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Dosya Kapanışı
                    </p>
                    <p class="text-[13px] font-bold text-slate-700"><?php echo e($closedAt->format('d.m.Y H:i')); ?></p>
                    <div class="mt-2 inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-tighter shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?php echo e($days == 0 ? 'Aynı gün kapatıldı' : ($days . ' günde kapatıldı')); ?>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/partials/sidebar.blade.php ENDPATH**/ ?>