<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
    
    <!--[if BLOCK]><![endif]--><?php if(!$hideHeader): ?>
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Müşteri Ziyaretleri
        </h3>

        <div class="flex flex-wrap items-center gap-3">
            
            <select wire:model.live="status" class="text-xs border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5 pl-3 pr-8 shadow-sm font-semibold text-gray-600">
                <option value="all">Tüm Durumlar</option>
                <option value="Onay Bekliyor">Onay Bekleyenler</option>
                <option value="Onaylandı">Onaylananlar</option>
                <option value="Reddedildi">Reddedilenler</option>
                <option value="Revize İsteniyor">Revize İstenenler</option>
                <option value="Tamamlandı">Tamamlananlar</option>
            </select>

            
            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-2 py-1 shadow-sm">
                <input type="date" wire:model.live="startDate" class="text-xs border-none focus:ring-0 p-0 text-gray-600 font-medium">
                <span class="text-gray-300">-</span>
                <input type="date" wire:model.live="endDate" class="text-xs border-none focus:ring-0 p-0 text-gray-600 font-medium">
            </div>

            
            <button wire:click="resetFilters" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Filtreleri Sıfırla">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="overflow-x-auto relative">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 sticky top-0 z-10 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Proje / Şikayet</th>
                    <th class="px-6 py-4 text-center">Tarih</th>
                    <th class="px-6 py-4">Müşteri</th>
                    <th class="px-6 py-4">Planlayan / Ziyaretçi</th>
                    <th class="px-6 py-4 text-center">Durum</th>
                    <th class="px-6 py-4 text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="bg-white hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <a href="<?php echo e(route('proje.workspace.show', $visit->iaa->id)); ?>" target="_blank" class="text-sm font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                    #<?php echo e($visit->iaa->id); ?> - <?php echo e(Str::limit($visit->iaa->id_label ?? $visit->iaa->baslik, 30)); ?>

                                </a>
                                <span class="text-[10px] text-gray-400 font-medium mt-0.5">
                                     <?php echo e($visit->iaa->atananTakim->ad ?? '-'); ?>

                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                             <div class="inline-flex flex-col items-center justify-center bg-gray-50 rounded-lg p-1.5 border border-gray-100 group-hover:bg-white transition-colors">
                                <span class="text-xs font-black text-gray-800"><?php echo e($visit->visit_date->format('d.m.Y')); ?></span>
                                <span class="text-[9px] text-gray-400 font-bold uppercase"><?php echo e($visit->visit_date->translatedFormat('l')); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                             <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600 border border-blue-200">
                                    <?php echo e(substr($visit->iaa->musteriSikayeti->customer->name ?? '-', 0, 1)); ?>

                                </div>
                                <a href="<?php echo e($visit->iaa->musteriSikayeti->customer ? route('musteri.profil.show', $visit->iaa->musteriSikayeti->customer->id) : '#'); ?>" target="_blank" class="text-xs font-bold text-gray-700 hover:text-blue-600 hover:underline transition-colors truncate max-w-[150px]">
                                    <?php echo e($visit->iaa->musteriSikayeti->customer->name ?? '-'); ?>

                                </a>
                             </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1.5" title="Planlayan">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_5px_rgba(52,211,153,0.5)]"></span>
                                    <span class="text-[10.5px] font-bold text-gray-700"><?php echo e($visit->planner->name ?? 'Bilinmeyen'); ?></span>
                                </div>
                                <div class="flex items-center gap-1.5" title="Ziyaretçi">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 shadow-[0_0_5px_rgba(129,140,248,0.5)]"></span>
                                    <span class="text-[10.5px] font-bold text-gray-600"><?php echo e($visit->visitor->name ?? $visit->visitor_name ?? '-'); ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <?php
                                $statusColors = [
                                    'Beklemede' => 'bg-indigo-100 text-indigo-700 border-indigo-200 animate-pulse',
                                    'Direktör Onayı Bekliyor' => 'bg-amber-100 text-amber-700 border-amber-200 animate-pulse',
                                    'Onay Bekliyor' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'Onaylandı' => 'bg-emerald-100 text-emerald-700 border-emerald-200 border-dashed animate-pulse',
                                    'Tamamlandı' => 'bg-emerald-500 text-white shadow-sm',
                                    'Reddedildi' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    'Revize İsteniyor' => 'bg-purple-100 text-purple-700 border-purple-200'
                                ];
                                $colorClass = $statusColors[$visit->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                            ?>
                            <div class="flex flex-col items-center">
                                <span class="group/status relative px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border <?php echo e($colorClass); ?> cursor-default">
                                    <?php echo e($visit->status); ?>

                                    <!--[if BLOCK]><![endif]--><?php if($visit->status === 'Onaylandı' && $visit->approver): ?>
                                        <div class="invisible group-hover/status:visible absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-[9px] rounded whitespace-nowrap z-50 shadow-xl ring-1 ring-white/20">
                                            Onaylayan: <?php echo e($visit->approver->name); ?>

                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </span>
                                <!--[if BLOCK]><![endif]--><?php if(in_array($visit->return_date_revision_status, ['Bekliyor', 'Direktör Onayı Bekliyor'])): ?>
                                    <span class="text-[9px] font-bold mt-1.5 text-orange-600 animate-pulse text-center leading-tight bg-orange-50 px-2 py-0.5 rounded border border-orange-200" title="Dönüş Tarihi Revizyonu">
                                        Dönüş Tarihi<br>Revizyonu Bekliyor
                                    </span>
                                <?php elseif($visit->status === 'Beklemede'): ?>
                                    <!--[if BLOCK]><![endif]--><?php if(Auth::user()->hasRole('Bölüm Kalite Yöneticisi') || Auth::user()->hasRole('Superadmin')): ?>
                                        <span class="text-[9px] font-bold mt-1 text-indigo-600 animate-pulse">onayınızı bekliyor</span>
                                    <?php else: ?>
                                        <span class="text-[8px] font-bold mt-1 text-gray-500 text-center leading-tight">Kalite Yöneticisi<br>Onayı Bekliyor</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php elseif($visit->status === 'Direktör Onayı Bekliyor'): ?>
                                    <?php if(Auth::user()->hasRole('Direktör') || Auth::user()->hasRole('Superadmin')): ?>
                                        <span class="text-[9px] font-bold mt-1 text-amber-600 animate-pulse">onayınızı bekliyor</span>
                                    <?php else: ?>
                                        <span class="text-[8px] font-bold mt-1 text-gray-500 text-center leading-tight">Direktör<br>Onayı Bekliyor</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                
                                <?php
                                    $user = Auth::user();
                                    $isSuperAdmin = $user->hasRole('Superadmin');
                                    $isAuthorizedDirector = false;
                                    
                                    if ($user->hasRole('Direktör')) {
                                        $managedBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
                                        // Hem IAA hem de Şikayet kategorisindeki bölümü kontrol et
                                        $visitBolumId = $visit->iaa->bolum_id ?? ($visit->iaa->musteriSikayeti->sikayetKategori->bolum_id ?? null);
                                        
                                        if ($visitBolumId && in_array($visitBolumId, $managedBolumIds)) {
                                            $isAuthorizedDirector = true;
                                        }
                                    }
                                    
                                    $isAuthorizedQuality = false;
                                    if ($user->hasRole('Bölüm Kalite Yöneticisi') && $user->bolum_id) {
                                        $visitBolumId = $visit->iaa->bolum_id ?? ($visit->iaa->musteriSikayeti->sikayetKategori->bolum_id ?? null);
                                        if ($visitBolumId == $user->bolum_id) {
                                            $isAuthorizedQuality = true;
                                        }
                                    }
                                    
                                    $canAction = false;
                                    if ($isSuperAdmin) {
                                        $canAction = true;
                                    } elseif ($isAuthorizedQuality && $visit->status === 'Beklemede') {
                                        $canAction = true;
                                    } elseif ($isAuthorizedDirector && $visit->status === 'Direktör Onayı Bekliyor') {
                                        $canAction = true;
                                    }
                                ?>

                                <!--[if BLOCK]><![endif]--><?php if($canAction): ?>
                                    <!--[if BLOCK]><![endif]--><?php if($visit->status === 'Beklemede' || $visit->status === 'Direktör Onayı Bekliyor' || $visit->status === 'Onay Bekliyor'): ?>
                                        
                                        <button wire:click="openApproveModal(<?php echo e($visit->id); ?>)" 
                                                class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100 flex items-center justify-center group/btn" 
                                                title="Onayla">
                                            <svg class="w-4 h-4 transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        
                                        
                                        <button wire:click="openRejectModal(<?php echo e($visit->id); ?>, 'revision')" 
                                                class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white transition-all shadow-sm border border-purple-100 flex items-center justify-center group/btn" 
                                                title="Revize İste">
                                            <svg class="w-4 h-4 transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                        
                                        
                                        <button wire:click="openRejectModal(<?php echo e($visit->id); ?>, 'reject')" 
                                                class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-rose-100 flex items-center justify-center group/btn" 
                                                title="Reddet">
                                            <svg class="w-4 h-4 transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    <?php elseif(in_array($visit->status, ['Onaylandı', 'Reddedildi', 'Revize İsteniyor'])): ?>
                                        
                                        <button wire:click="undoAction(<?php echo e($visit->id); ?>)" 
                                                wire:confirm="Bu işlemi geri almak istediğinize emin misiniz? Durum 'Beklemede' olarak güncellenecektir."
                                                class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white transition-all shadow-sm border border-orange-100 flex items-center justify-center group/btn" 
                                                title="İşlemi Geri Al">
                                            <svg class="w-4 h-4 transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                            </svg>
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <a href="<?php echo e(route('proje.workspace.show', $visit->iaa->id)); ?>" target="_blank" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-sm border border-blue-100 group/btn" 
                                   title="Detayları İncele">
                                    <svg class="w-4 h-4 transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-400 italic">Belirtilen kriterlerde ziyaret kaydı bulunamadı.</p>
                                <button wire:click="resetFilters" class="text-xs font-bold text-blue-600 hover:underline">Filtreleri Temizle</button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($hasMore): ?>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-center gap-3">
            <!--[if BLOCK]><![endif]--><?php if(!$showAll): ?>
                
                <button wire:click="toggleShowAll"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-50 to-blue-50 text-indigo-700 rounded-xl text-xs font-bold border border-indigo-100 hover:from-indigo-100 hover:to-blue-100 hover:shadow-md transition-all duration-300 group/btn">
                    <svg class="w-4 h-4 text-indigo-500 group-hover/btn:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    Devamını Yükle
                    <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-lg text-[10px] font-black">+<?php echo e($totalCount - $initialLimit); ?></span>
                </button>
            <?php else: ?>
                
                <button wire:click="toggleShowAll"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold border border-gray-200 hover:bg-gray-100 hover:shadow-md transition-all duration-300 group/btn">
                    <svg class="w-4 h-4 text-gray-400 group-hover/btn:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    Devamını Gizle
                </button>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    

    
    <!--[if BLOCK]><![endif]--><?php if($showApproveModal): ?>
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showApproveModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Ziyaret Planını Onayla</h3>
                            <div class="mt-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tahmini Dönüş Tarihi</label>
                                <input type="date" wire:model ="estimatedReturnDate" class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['estimatedReturnDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" wire:click="approveVisit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-bold text-white hover:bg-emerald-700 focus:outline-none sm:w-auto sm:text-sm transition-colors">Onayla</button>
                    <button type="button" wire:click="$set('showApproveModal', false)" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-colors">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($showRejectModal): ?>
    <div class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showRejectModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full <?php echo e($actionType === 'revision' ? 'bg-purple-100' : 'bg-rose-100'); ?> sm:mx-0 sm:h-10 sm:w-10">
                            <!--[if BLOCK]><![endif]--><?php if($actionType === 'revision'): ?>
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <?php else: ?>
                                <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900"><?php echo e($actionType === 'revision' ? 'Revize İste' : 'Ziyaret Planını Reddet'); ?></h3>
                            <div class="mt-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Sebep / Açıklama</label>
                                <textarea wire:model="rejectionReason" rows="3" class="w-full border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 shadow-sm" placeholder="Açıklama giriniz..."></textarea>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['rejectionReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" wire:click="processRejectOrRevision" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 <?php echo e($actionType === 'revision' ? 'bg-purple-600 hover:bg-purple-700' : 'bg-rose-600 hover:bg-rose-700'); ?> text-base font-bold text-white focus:outline-none sm:w-auto sm:text-sm transition-colors">
                        <?php echo e($actionType === 'revision' ? 'Revize İste' : 'Reddet'); ?>

                    </button>
                    <button type="button" wire:click="$set('showRejectModal', false)" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-colors">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/dashboard/super-admin-visit-table.blade.php ENDPATH**/ ?>