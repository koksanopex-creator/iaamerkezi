
<?php if(in_array($case->durum, ['Kurulda', 'Karar Verildi']) && Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])): ?>
    
    <?php
        // A. Kurul Üyelerini Belirle
        $councilRoles = ['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı', 'Hukuk Yöneticisi'];
        $allCouncilMembers = \App\Models\User::role($councilRoles)
            ->whereDoesntHave('roles', function($q){ $q->where('name', 'Superadmin'); })
            ->get();
        $totalMembersCount = $allCouncilMembers->count();

        // B. Oy Durumları
        $votedUserIds = $case->oylar->pluck('user_id')->toArray();
        $votesUsed = $case->oylar->count();
        $waitingVotes = $totalMembersCount - $case->oylar->whereIn('user_id', $allCouncilMembers->pluck('id'))->count();
        if($waitingVotes < 0) $waitingVotes = 0;

        // C. İstatistikler
        $votesPenalty = $case->oylar->where('oy_yonu', 'Ceza Verilsin')->count();
        $votesNoPenalty = $case->oylar->where('oy_yonu', 'Ceza Verilmesin')->count();
        $votesInvestigation = $case->oylar->where('oy_yonu', 'Ek Soruşturma')->count();
        $votesAbstain = $case->oylar->where('oy_yonu', 'Çekimser')->count();

        $totalForCalc = $votesUsed > 0 ? $votesUsed : 1;
        $percPenalty = ($votesPenalty / $totalForCalc) * 100;
        $percNoPenalty = ($votesNoPenalty / $totalForCalc) * 100;
        $percInvestigation = ($votesInvestigation / $totalForCalc) * 100;
        $percAbstain = ($votesAbstain / $totalForCalc) * 100;

        // D. Lider Seçenek
        $maxVote = max($votesPenalty, $votesNoPenalty, $votesInvestigation, $votesAbstain);
        $leaderText = "Bekleniyor";
        $leaderColor = "slate";

        if ($votesUsed > 0) {
            if ($votesPenalty == $maxVote) { $leaderText = "CEZA VERİLSİN"; $leaderColor = "rose"; }
            elseif ($votesNoPenalty == $maxVote) { $leaderText = "CEZA VERİLMESİN"; $leaderColor = "emerald"; }
            elseif ($votesInvestigation == $maxVote) { $leaderText = "EK SORUŞTURMA"; $leaderColor = "amber"; }
            elseif ($votesAbstain == $maxVote) { $leaderText = "ÇEKİMSER"; $leaderColor = "slate"; }
        }
    ?>

    <div class="mt-12 border-t border-gray-200 pt-8">
        
        
        <div class="flex items-center gap-4 mb-8">
            <div class="bg-slate-900 text-white p-3 rounded-2xl shadow-lg shadow-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Disiplin Kurulu Odası</h3>
                <?php if($case->durum == 'Karar Verildi'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                        🔒 Arşiv Kaydı (Oylama Kapalı)
                    </span>
                <?php else: ?>
                    <p class="text-slate-500 text-sm">Aktif Oylama Süreci</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            
            <?php if($case->durum == 'Kurulda'): ?>
                <div class="lg:col-span-3">
                    <?php if(Auth::user()->hasRole(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı', 'Superadmin', 'Hukuk Yöneticisi'])): ?>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                            <div class="flex items-center justify-between mb-6">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Oyunuzu Kullanın</h4>
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            </div>
                            <form action="<?php echo e(route('admin.disiplin.vote.save', $case->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-6 group">
                                    <label class="block text-xs font-bold text-slate-500 mb-2">KARARINIZ</label>
                                    <div class="relative">
                                        <select name="oy_yonu" class="w-full bg-slate-50 border-0 border-b-2 border-slate-200 text-slate-700 text-sm py-3 px-3 focus:ring-0 focus:border-indigo-600 transition rounded-t-lg cursor-pointer">
                                            <option value="">Bir karar seçin...</option>
                                            <option value="Ceza Verilsin">🔴 Ceza Verilsin</option>
                                            <option value="Ceza Verilmesin">🟢 Ceza Verilmesin</option>
                                            <option value="Ek Soruşturma">🟡 Ek Soruşturma</option>
                                            <option value="Çekimser">⚪ Çekimser</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-6 group">
                                    <label class="block text-xs font-bold text-slate-500 mb-2">GÖRÜŞ / NOTUNUZ</label>
                                    <textarea name="yorum" rows="6" class="w-full bg-slate-50 border-0 border-b-2 border-slate-200 text-slate-700 text-sm py-3 px-3 focus:ring-0 focus:border-indigo-600 transition rounded-t-lg placeholder-slate-400 resize-none" placeholder="Gerekçenizi buraya yazınız..."></textarea>
                                </div>
                                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-slate-200 transform active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                                    Oyumu Kaydet
                                </button>
                            </form>
                            <?php $myVote = $case->oylar->where('user_id', Auth::id())->first(); ?>
                            <?php if($myVote): ?>
                                <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                                    <form action="<?php echo e(route('admin.disiplin.vote.delete', $case->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" onclick="return confirm('Silmek istediğinize emin misiniz?')" class="text-xs font-bold text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-3 py-2 rounded-lg transition">Mevcut Oyumu Sil</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                
                <div class="lg:col-span-3">
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <p class="text-sm font-bold">Oylama Kapandı</p>
                        <p class="text-xs mt-1">Dosya hakkında nihai karar verildiği için yeni oy kullanılamaz.</p>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="lg:col-span-5 space-y-6">
                
                <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl p-1 shadow-sm border border-slate-200">
                    <div class="bg-white rounded-xl p-5 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Eğilim / Sonuç</p>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-<?php echo e($leaderColor); ?>-500 shadow-<?php echo e($leaderColor); ?>"></span>
                                <h2 class="text-xl font-black text-slate-800"><?php echo e($leaderText); ?></h2>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Katılım</p>
                            <span class="text-2xl font-black text-slate-200">
                                %<span class="text-slate-800"><?php echo e(number_format(($votesUsed / ($totalMembersCount > 0 ? $totalMembersCount : 1)) * 100, 0)); ?></span>
                            </span>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 min-h-[400px]">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="font-bold text-slate-700">Üye Görüşleri</h4>
                        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold"><?php echo e($votesUsed); ?> Görüş</span>
                    </div>

                    <?php if($case->oylar->isEmpty()): ?>
                        <div class="flex flex-col items-center justify-center h-48 text-slate-300">
                            <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-sm font-medium">Henüz bir görüş paylaşılmadı.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-6 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            <?php $__currentLoopData = $case->oylar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $badgeClass = match($oy->oy_yonu) {
                                        'Ceza Verilsin' => 'bg-rose-100 text-rose-700 ring-rose-200',
                                        'Ceza Verilmesin' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                                        'Ek Soruşturma' => 'bg-amber-100 text-amber-700 ring-amber-200',
                                        default => 'bg-slate-100 text-slate-600 ring-slate-200'
                                    };
                                ?>
                                <div class="relative group">
                                    <div class="flex items-start gap-4">
                                        
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm border-2 border-white shadow-sm ring-1 ring-slate-100">
                                                <?php echo e(substr($oy->user->name, 0, 1)); ?>

                                            </div>
                                        </div>
                                        
                                        
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <div>
                                                    <a href="<?php echo e(route('profile.show', $oy->user->id)); ?>" target="_blank" class="text-sm font-bold text-slate-800 hover:text-indigo-600 transition">
                                                        <?php echo e($oy->user->name); ?>

                                                    </a>
                                                    <p class="text-[10px] text-slate-400 font-medium"><?php echo e($oy->created_at->diffForHumans()); ?></p>
                                                </div>
                                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full ring-1 ring-inset <?php echo e($badgeClass); ?>">
                                                    <?php echo e($oy->oy_yonu); ?>

                                                </span>
                                            </div>
                                            
                                            <?php if($oy->yorum): ?>
                                                <div class="mt-2 text-sm text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-lg rounded-tl-none italic border border-slate-100">
                                                    "<?php echo e($oy->yorum); ?>"
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(Auth::user()->hasRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Yöneticisi']) && $votesUsed > 0 && $case->durum == 'Kurulda'): ?>
                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <h4 class="font-bold text-slate-800 mb-4 text-xs uppercase flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-800"></span>
                                Yetkili Karar Paneli
                            </h4>
                            
                            <form method="POST" enctype="multipart/form-data" class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <?php echo csrf_field(); ?>
                                
                                <div class="mb-4">
                                    <textarea name="yonetici_notu" rows="2" class="w-full bg-white border-0 ring-1 ring-slate-200 rounded-lg text-sm p-3 focus:ring-2 focus:ring-indigo-500 transition" placeholder="Nihai karar gerekçesini yazınız..." required>Kurul oy çokluğu ile karar almıştır.</textarea>
                                </div>

                                <div class="mb-4 flex items-center gap-3">
                                    <label class="cursor-pointer flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-indigo-600 transition bg-white px-3 py-2 rounded-lg border border-slate-200 hover:border-indigo-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span class="truncate max-w-[150px]">Karar Dosyası Ekle</span>
                                        <input type="file" name="karar_dosyasi" class="hidden">
                                    </label>
                                    <span class="text-[10px] text-slate-400 italic">(Opsiyonel, imzalı tutanak)</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <button type="submit" formaction="<?php echo e(route('admin.disiplin.penalty.approve', $case->id)); ?>" onclick="return confirm('CEZA ONAYLANACAK. Emin misiniz?')" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 rounded-lg shadow-sm text-xs flex justify-center items-center gap-2 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Cezayı Onayla
                                    </button>
                                    <button type="submit" formaction="<?php echo e(route('admin.disiplin.defense.accept', $case->id)); ?>" onclick="return confirm('DOSYA KAPATILACAK. Emin misiniz?')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg shadow-sm text-xs flex justify-center items-center gap-2 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Dosyayı Kapat
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="lg:col-span-4 space-y-6">
                
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-slate-700 mb-6 text-sm">Oylama Sonuçları</h4>
                    
                    <?php if($votesUsed > 0): ?>
                        <div class="space-y-5">
                            
                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-600">Ceza Verilsin</span>
                                    <span class="font-bold text-rose-600"><?php echo e($votesPenalty); ?> Oy (%<?php echo e(number_format($percPenalty, 0)); ?>)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-rose-500 h-2 rounded-full shadow-sm shadow-rose-200 transition-all duration-1000" style="width: <?php echo e($percPenalty); ?>%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-600">Ceza Verilmesin</span>
                                    <span class="font-bold text-emerald-600"><?php echo e($votesNoPenalty); ?> Oy (%<?php echo e(number_format($percNoPenalty, 0)); ?>)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full shadow-sm shadow-emerald-200 transition-all duration-1000" style="width: <?php echo e($percNoPenalty); ?>%"></div>
                                </div>
                            </div>

                            <?php if($votesInvestigation > 0): ?>
                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-600">Ek Soruşturma</span>
                                    <span class="font-bold text-amber-600"><?php echo e($votesInvestigation); ?> Oy</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-amber-400 h-2 rounded-full shadow-sm shadow-amber-200 transition-all duration-1000" style="width: <?php echo e($percInvestigation); ?>%"></div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($votesAbstain > 0): ?>
                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-600">Çekimser</span>
                                    <span class="font-bold text-slate-500"><?php echo e($votesAbstain); ?> Oy</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-slate-400 h-2 rounded-full shadow-sm shadow-slate-200 transition-all duration-1000" style="width: <?php echo e($percAbstain); ?>%"></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-xs text-slate-400">Veri yok</p>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="font-bold text-slate-700 text-sm">Üye Listesi</h4>
                        
                        <?php if($waitingVotes > 0): ?>
                            <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-full border border-amber-100 animate-pulse">
                                <?php echo e($waitingVotes); ?> Kişi Bekleniyor
                            </span>
                        <?php else: ?>
                            <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-full border border-emerald-100">
                                Tamamlandı
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
                        <?php $__currentLoopData = $allCouncilMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                $hasVoted = in_array($member->id, $votedUserIds);
                                $isPresident = $member->hasRole('Disiplin Kurulu Başkanı');
                                $rowClass = $isPresident ? 'bg-indigo-50/50 border-indigo-100' : 'hover:bg-slate-50 border-transparent';
                            ?>
                            
                            <div class="flex items-center justify-between p-2.5 rounded-xl border <?php echo e($rowClass); ?> transition group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white border border-slate-100 flex items-center justify-center text-xs font-bold text-slate-600 shadow-sm group-hover:scale-105 transition">
                                        <?php echo e(substr($member->name, 0, 1)); ?>

                                    </div>
                                    <div class="flex flex-col">
                                        <a href="<?php echo e(route('profile.show', $member->id)); ?>" target="_blank" class="text-xs font-bold text-slate-700 hover:text-indigo-600">
                                            <?php echo e($member->name); ?>

                                        </a>
                                        <?php if($isPresident): ?>
                                            <span class="text-[9px] text-indigo-500 font-bold uppercase tracking-wider">BAŞKAN</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <?php if($hasVoted): ?>
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-6 h-6 rounded-full bg-amber-50 text-amber-400 flex items-center justify-center" title="Bekleniyor">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

            </div>

        </div>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/partials/council-room.blade.php ENDPATH**/ ?>