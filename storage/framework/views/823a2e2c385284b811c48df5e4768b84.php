<div class="<?php echo e($embedded ? '' : 'mt-6'); ?>" x-data="{ 
    showApprove: <?php if ((object) ('showApproveModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showApproveModal'->value()); ?>')<?php echo e('showApproveModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showApproveModal'); ?>')<?php endif; ?>, 
    showReject: <?php if ((object) ('showRejectModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showRejectModal'->value()); ?>')<?php echo e('showRejectModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showRejectModal'); ?>')<?php endif; ?>, 
    showRevision: <?php if ((object) ('showRevisionModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showRevisionModal'->value()); ?>')<?php echo e('showRevisionModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showRevisionModal'); ?>')<?php endif; ?>,
    showCancel: <?php if ((object) ('showCancelModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showCancelModal'->value()); ?>')<?php echo e('showCancelModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showCancelModal'); ?>')<?php endif; ?>,
    showSubmitRevision: <?php if ((object) ('isRevisionSubmitModalOpen') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isRevisionSubmitModalOpen'->value()); ?>')<?php echo e('isRevisionSubmitModalOpen'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isRevisionSubmitModalOpen'); ?>')<?php endif; ?>,
    showUpdateReturnDate: <?php if ((object) ('showUpdateReturnDateModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showUpdateReturnDateModal'->value()); ?>')<?php echo e('showUpdateReturnDateModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showUpdateReturnDateModal'); ?>')<?php endif; ?>,
    showReturnDateRevision: <?php if ((object) ('showReturnDateRevisionModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showReturnDateRevisionModal'->value()); ?>')<?php echo e('showReturnDateRevisionModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showReturnDateRevisionModal'); ?>')<?php endif; ?>,
    showReturnDateRevisionResponse: <?php if ((object) ('showReturnDateRevisionResponseModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showReturnDateRevisionResponseModal'->value()); ?>')<?php echo e('showReturnDateRevisionResponseModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showReturnDateRevisionResponseModal'); ?>')<?php endif; ?>,
    showDetails: false
}"
@open-revision-submit-modal.window="showSubmitRevision = true"
>
    <?php
        $status = $savedVisit['status'] ?? ($this->iaa->ziyaretPlani->status ?? 'Beklemede');
    ?>
    <div class="<?php echo e($embedded ? 'p-4' : 'bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-indigo-500'); ?>">
        <!--[if BLOCK]><![endif]--><?php if(!$embedded): ?>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Müşteri Ziyareti Planla
                </h3>
                <!--[if BLOCK]><![endif]--><?php if(!$savedVisit && !($iaa->ziyaretPlani && $iaa->ziyaretPlani->status === 'Onaylandı')): ?>
                    <button wire:click="toggleForm" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <?php echo e($isOpen ? 'Formu Kapat' : 'Yeni Ziyaret Planı Oluştur'); ?>

                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($successMessage): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p><?php echo e($successMessage); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($errorMessage): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo e($errorMessage); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($savedVisit): ?>
            <div class="bg-indigo-50 rounded-2xl p-6 border-2 border-indigo-100 shadow-sm">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-indigo-900 font-bold text-lg flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Ziyaret Planı Özeti
                            </h4>
                            <p class="text-xs text-indigo-600 mt-1">Ziyaret planı başarıyla Takvim uygulamasına kaydedildi.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="showDetails = !showDetails" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 border border-indigo-200 rounded-lg text-xs font-bold text-indigo-700 hover:bg-indigo-200 transition shadow-sm">
                                <svg class="w-4 h-4 mr-1 transition-transform duration-200" :class="showDetails ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                <span x-text="showDetails ? 'Detayları Gizle' : 'Detayları Göster'"></span>
                            </button>
                            
                            <!--[if BLOCK]><![endif]--><?php if($canEdit): ?>
                                <?php
                                    $isRevizeIsteniyor = ($savedVisit['status'] ?? ($this->iaa->ziyaretPlani->status ?? '')) === 'Revize İsteniyor';
                                ?>
                                <button wire:click="editVisit" class="inline-flex items-center px-3 py-1.5 <?php echo e($isRevizeIsteniyor ? 'bg-orange-100 border-orange-300 text-orange-700 animate-pulse hover:bg-orange-200 ring-2 ring-orange-400 ring-offset-1' : 'bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-50'); ?> rounded-lg text-xs font-bold transition shadow-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <?php echo e($isRevizeIsteniyor ? 'Revize Etmek İçin Tıklayın' : 'Düzenle'); ?>

                                </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div x-show="showDetails" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-4">
                        
                        
                        <!--[if BLOCK]><![endif]--><?php if(!Auth::user()->hasRole(['Müşteri', 'Müşteri Temsilcisi'])): ?>
                            <?php
                                $rejectionMsg = null;
                                $cancelMsg = null;
                                $cancellerName = null;
                                $cancelledAt = null;

                                if ($this->iaa->ziyaretPlani) {
                                    $zp = $this->iaa->ziyaretPlani;
                                    $rejectionMsg = $zp->rejection_reason_director ?: ($zp->rejection_reason_quality ?: ($zp->rejection_reason_superadmin ?: $zp->reject_reason));
                                    
                                    if ($zp->status === 'İptal Edildi') {
                                        $cancelMsg = $zp->cancel_reason;
                                        $cancelledAt = $zp->cancelled_at ? \Carbon\Carbon::parse($zp->cancelled_at)->format('d.m.Y H:i') : null;
                                        $cancellerUser = \App\Models\User::find($zp->cancelled_by);
                                        $cancellerName = $cancellerUser ? $cancellerUser->name : 'Bilinmiyor';
                                    }
                                }
                                $isRejectedOrRevision = in_array($savedVisit['status'] ?? '', ['Reddedildi', 'Revize İsteniyor']);
                            ?>

                            <!--[if BLOCK]><![endif]--><?php if(($savedVisit['status'] ?? '') === 'İptal Edildi'): ?>
                                <div class="mb-4 p-4 rounded-2xl border-2 bg-rose-50 border-rose-100">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-black text-rose-700 uppercase tracking-widest mb-1">Ziyaret İptal Edildi</h5>
                                            <p class="text-[11px] text-rose-800 mb-1">Bu ziyaret planı <strong><?php echo e($cancellerName); ?></strong> tarafından <strong><?php echo e($cancelledAt); ?></strong> tarihinde iptal edilmiştir.</p>
                                            <p class="text-sm font-medium text-rose-900 leading-relaxed italic">"<?php echo e($cancelMsg); ?>"</p>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif($isRejectedOrRevision && $rejectionMsg): ?>
                                <div class="mb-4 p-4 rounded-2xl border-2 <?php echo e($savedVisit['status'] === 'Reddedildi' ? 'bg-red-50 border-red-100' : 'bg-orange-50 border-orange-100'); ?>">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 <?php echo e($savedVisit['status'] === 'Reddedildi' ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600'); ?> rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-black <?php echo e($savedVisit['status'] === 'Reddedildi' ? 'text-red-700' : 'text-orange-700'); ?> uppercase tracking-widest mb-1"><?php echo e($savedVisit['status'] === 'Reddedildi' ? 'Ziyaret Red Gerekçesi' : 'Revizyon Talebi Açıklaması'); ?></h5>
                                            <p class="text-sm font-medium <?php echo e($savedVisit['status'] === 'Reddedildi' ? 'text-red-800' : 'text-orange-800'); ?> leading-relaxed italic">"<?php echo e($rejectionMsg); ?>"</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <?php
                                $revisionHistory = collect($visitLogs)->filter(function($log) {
                                    return in_array($log->eylem, ['Ziyaret Planı Revize İsteniyor', 'Ziyaret Planı Revize Edildi', 'Ziyaret Planı Reddedildi']);
                                })->values();
                            ?>

                            <!--[if BLOCK]><![endif]--><?php if($revisionHistory->count() > 0): ?>
                                <div class="mb-4" x-data="{ showHistory: false }">
                                    <button @click="showHistory = !showHistory" type="button" class="w-full flex items-center justify-between p-4 rounded-2xl border-2 bg-blue-50/50 border-blue-100 hover:bg-blue-100/50 transition-colors text-blue-800 focus:outline-none">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="text-xs font-black uppercase tracking-widest">Revizyon Geçmişi</span>
                                            <span class="ml-2 bg-blue-200 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo e($revisionHistory->count()); ?> Kayıt</span>
                                        </div>
                                        <svg class="w-5 h-5 transform transition-transform duration-200" :class="showHistory ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div x-show="showHistory" x-collapse x-cloak class="mt-2 p-5 rounded-3xl border-2 bg-blue-50/20 border-blue-100">
                                        <div class="space-y-4">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $revisionHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="flex items-start gap-3 relative">
                                                    <!--[if BLOCK]><![endif]--><?php if(!$loop->last): ?>
                                                        <div class="absolute left-4 top-8 bottom-[-16px] w-0.5 bg-blue-100"></div>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center <?php echo e(str_contains($log->eylem, 'Revize Edildi') ? 'bg-blue-200 text-blue-700' : (str_contains($log->eylem, 'Reddedildi') ? 'bg-red-200 text-red-700' : 'bg-orange-200 text-orange-700')); ?> relative z-10 border-2 border-white shadow-sm">
                                                        <!--[if BLOCK]><![endif]--><?php if(str_contains($log->eylem, 'Revize Edildi')): ?>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <?php else: ?>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                    <div class="bg-white p-3 rounded-2xl border border-blue-50 shadow-sm w-full">
                                                        <div class="flex justify-between items-start mb-1">
                                                            <span class="text-[10px] font-black <?php echo e(str_contains($log->eylem, 'Revize Edildi') ? 'text-blue-700' : (str_contains($log->eylem, 'Reddedildi') ? 'text-red-700' : 'text-orange-700')); ?> uppercase tracking-wider"><?php echo e($log->eylem); ?></span>
                                                            <span class="text-[10px] text-gray-400 font-medium"><?php echo e(\Carbon\Carbon::parse($log->created_at)->format('d.m.Y H:i')); ?></span>
                                                        </div>
                                                        <p class="text-[12px] font-medium text-gray-700 leading-relaxed"><?php echo e($log->aciklama); ?></p>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <!--[if BLOCK]><![endif]--><?php if(isset($savedVisit['return_date_revision_status']) && in_array($savedVisit['return_date_revision_status'], ['Bekliyor', 'Direktör Onayı Bekliyor'])): ?>
                            <?php
                                $canApproveRevision = false;
                                if ($savedVisit['return_date_revision_status'] === 'Bekliyor') {
                                    $canApproveRevision = auth()->id() == ($savedVisit['approved_by'] ?? null) || auth()->user()->hasRole('Superadmin');
                                } elseif ($savedVisit['return_date_revision_status'] === 'Direktör Onayı Bekliyor') {
                                    $canApproveRevision = auth()->user()->hasRole('Direktör') || auth()->user()->hasRole('Superadmin');
                                }
                                
                                // Get Requester Name
                                $revisionRequesterName = 'Bilinmiyor';
                                if (!empty($savedVisit['return_date_revision_requested_by'])) {
                                    $reqUser = \App\Models\User::find($savedVisit['return_date_revision_requested_by']);
                                    if ($reqUser) $revisionRequesterName = $reqUser->name;
                                } elseif (!empty($savedVisit['visitors'])) {
                                    $visitorsData = is_string($savedVisit['visitors']) ? json_decode($savedVisit['visitors'], true) : $savedVisit['visitors'];
                                    if (is_array($visitorsData) && count($visitorsData) > 0) {
                                        $v = \App\Models\User::find($visitorsData[0]);
                                        if ($v) $revisionRequesterName = $v->name;
                                    }
                                } elseif (!empty($savedVisit['visitor_id'])) {
                                    $v = \App\Models\User::find($savedVisit['visitor_id']);
                                    if ($v) $revisionRequesterName = $v->name;
                                }
                                
                                // Get Request Date
                                $requestDateStr = '-';
                                $revisionLog = \App\Models\IaaLog::where('iaa_id', $savedVisit['iaa_id'])
                                    ->where('eylem', 'Dönüş Tarihi Revizyonu Talep Edildi')
                                    ->latest()
                                    ->first();
                                if ($revisionLog) {
                                    $requestDateStr = $revisionLog->created_at->format('d.m.Y H:i');
                                }
                                
                                // Get Approver Name
                                $waitingForName = 'Yetkili';
                                if ($savedVisit['return_date_revision_status'] === 'Direktör Onayı Bekliyor') {
                                    $projectBolum = \App\Models\Iaa::find($savedVisit['iaa_id'])->bolum ?? null;
                                    if ($projectBolum && $projectBolum->director) {
                                        $waitingForName = $projectBolum->director->name;
                                    } else {
                                        $waitingForName = 'Direktör';
                                    }
                                } else {
                                    if (!empty($savedVisit['approved_by'])) {
                                        $apprUser = \App\Models\User::find($savedVisit['approved_by']);
                                        if ($apprUser) $waitingForName = $apprUser->name;
                                    }
                                }
                            ?>
                            <div class="mb-6 bg-orange-50 border-2 border-orange-200 p-5 rounded-3xl shadow-sm animate-fade-in">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-white rounded-full flex items-center justify-center border-2 border-orange-100 shadow-sm">
                                        <svg class="w-6 h-6 text-orange-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="w-full">
                                        <h4 class="text-base font-black text-orange-900 tracking-tight mb-1 flex items-center gap-2">
                                            Dönüş Tarihi Revizyon Talebi
                                            <span class="text-[9px] bg-white text-orange-600 px-2 py-0.5 rounded-full border border-orange-200 uppercase"><?php echo e($waitingForName); ?> Onayı Bekleniyor</span>
                                        </h4>
                                        <p class="text-xs text-orange-800 font-medium leading-relaxed mb-3">
                                            Talep <strong class="text-orange-900"><?php echo e($revisionRequesterName); ?></strong> tarafından <strong class="text-orange-900"><?php echo e($requestDateStr); ?></strong> tarihinde iletildi.
                                        </p>
                                        <div class="bg-white p-4 rounded-2xl border border-orange-100 shadow-sm mb-4">
                                            <div class="grid grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <span class="block text-[10px] uppercase font-extrabold text-gray-400">Mevcut Dönüş Tarihi</span>
                                                    <span class="text-sm font-bold text-gray-800"><?php echo e(\Carbon\Carbon::parse($savedVisit['estimated_return_date'])->format('d.m.Y')); ?></span>
                                                </div>
                                                <div>
                                                    <span class="block text-[10px] uppercase font-extrabold text-orange-500">Talep Edilen Tarih</span>
                                                    <span class="text-sm font-black text-orange-600"><?php echo e(\Carbon\Carbon::parse($savedVisit['return_date_revision_requested_date'])->format('d.m.Y')); ?></span>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="block text-[10px] uppercase font-extrabold text-gray-400">Gerekçe</span>
                                                <p class="text-xs font-medium text-gray-700"><?php echo e($savedVisit['return_date_revision_reason']); ?></p>
                                            </div>
                                        </div>
                                        <!--[if BLOCK]><![endif]--><?php if($canApproveRevision): ?>
                                            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-orange-100">
                                                <button wire:click="openReturnDateRevisionResponseModal('approve')" type="button" class="inline-flex items-center px-5 py-2 bg-green-600 text-white rounded-xl font-bold text-xs hover:bg-green-700 transition shadow-sm border border-green-700 group">
                                                    <svg class="w-4 h-4 mr-1.5 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Talebi Onayla
                                                </button>
                                                <button wire:click="openReturnDateRevisionResponseModal('reject')" type="button" class="inline-flex items-center px-5 py-2 bg-red-600 text-white rounded-xl font-bold text-xs hover:bg-red-700 transition shadow-sm border border-red-700 group">
                                                    <svg class="w-4 h-4 mr-1.5 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Talebi Reddet
                                                </button>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div class="bg-white/50 p-3 rounded-xl">
                                    <span class="block text-[10px] uppercase font-extrabold text-gray-400">Ziyaret Tarihi</span>
                                    <span class="text-sm font-bold text-gray-800"><?php echo e(Carbon\Carbon::parse($savedVisit['visit_date'])->format('d.m.Y H:i')); ?></span>
                                </div>
                                <div class="bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                                    <span class="block text-[10px] uppercase font-extrabold text-indigo-400">İşletme / Fabrika</span>
                                    <span class="text-sm font-bold text-indigo-800"><?php echo e($savedVisit['business_unit']['name'] ?? 'Belirtilmedi'); ?></span>
                                </div>
                                <div class="bg-white/50 p-3 rounded-xl">
                                    <span class="block text-[10px] uppercase font-extrabold text-gray-400">Ziyaret Nedeni</span>
                                    <span class="text-sm font-bold text-gray-800"><?php echo e($savedVisit['visit_reason']); ?></span>
                                </div>
                                <div class="bg-white/50 p-3 rounded-xl relative" x-data="{ showCard: false }">
                                    <span class="block text-[10px] uppercase font-extrabold text-gray-400">Ziyareti Gerçekleştirecek</span>
                                    <span class="text-sm font-bold text-indigo-700 cursor-help border-b border-dashed border-indigo-300" 
                                          @mouseenter="showCard = true" 
                                          @mouseleave="showCard = false">
                                        <?php echo e($savedVisit['visitor_name'] ?? ($savedVisit['user']['name'] ?? 'Belirtilmedi')); ?>

                                    </span>

                                    
                                    <div x-show="showCard && !showApprove && !showReject && !showRevision" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="absolute z-[60] left-1/2 -translate-x-1/2 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden ring-1 ring-gray-900/5">
                                        <div class="h-16 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                                        <div class="px-4 pb-4">
                                            <div class="relative flex justify-center">
                                                <div class="-mt-8 h-16 w-16 rounded-full border-4 border-white bg-gray-100 overflow-hidden shadow-sm">
                                                    <!--[if BLOCK]><![endif]--><?php if(isset($savedVisit['user']['photo']) && $savedVisit['user']['photo']): ?>
                                                        <img src="<?php echo e($savedVisit['user']['photo']); ?>" class="h-full w-full object-cover">
                                                    <?php else: ?>
                                                        <div class="h-full w-full flex items-center justify-center text-indigo-300 bg-indigo-50">
                                                            <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996c0-4.469 3.313-8.094 7.5-8.094 1.35 0 2.597.351 3.66 1.053 1.134.744 2.103 1.764 2.84 2.941 4.187 0 7.5 3.625 7.5 8.094zm-12-9.307c3.148 0 5.7-2.552 5.7-5.7s-2.552-5.7-5.7-5.7-5.7 2.552-5.7 5.7 2.552 5.7 5.7 5.7z"/></svg>
                                                        </div>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <h5 class="text-sm font-bold text-gray-900"><?php echo e($savedVisit['visitor_name'] ?? ($savedVisit['user']['name'] ?? 'Belirtilmedi')); ?></h5>
                                                <p class="text-[10px] text-gray-500 font-medium uppercase tracking-tight"><?php echo e($savedVisit['user']['title'] ?? 'Personel'); ?></p>
                                            </div>
                                            <div class="mt-4 space-y-2">
                                                <a href="mailto:<?php echo e($savedVisit['user']['email'] ?? ''); ?>" class="flex items-center gap-2 text-xs text-gray-600 hover:text-indigo-600 transition">
                                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    <?php echo e($savedVisit['user']['email'] ?? 'E-posta belirtilmedi'); ?>

                                                </a>
                                                <a href="tel:<?php echo e($savedVisit['user']['phone'] ?? ''); ?>" class="flex items-center gap-2 text-xs text-gray-600 hover:text-indigo-600 transition">
                                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <?php echo e($savedVisit['user']['phone'] ?? 'Telefon belirtilmedi'); ?>

                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white/50 p-3 rounded-xl col-span-2">
                                    <span class="block text-[10px] uppercase font-extrabold text-gray-400">Ürün Tanımı</span>
                                    <span class="text-sm font-bold text-gray-800"><?php echo e($savedVisit['product']['name'] ?? 'Belirtilmedi'); ?></span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 col-span-2">
                                    <div class="bg-white/50 p-3 rounded-xl border border-white/50">
                                        <span class="block text-[10px] uppercase font-extrabold text-gray-400">Barkod No</span>
                                        <span class="text-[11px] font-bold text-gray-700"><?php echo e($savedVisit['barcode'] ?? '-'); ?></span>
                                    </div>
                                    <div class="bg-white/50 p-3 rounded-xl border border-white/50">
                                        <span class="block text-[10px] uppercase font-extrabold text-gray-400">Lot No</span>
                                        <span class="text-[11px] font-bold text-gray-700"><?php echo e($savedVisit['lot_no'] ?? '-'); ?></span>
                                    </div>
                                    <div class="bg-green-50/50 p-3 rounded-xl border border-green-100">
                                        <span class="block text-[10px] uppercase font-extrabold text-green-500">Dönüş Tarihi</span>
                                        <span class="text-[11px] font-bold text-green-700">
                                            <?php echo e(isset($savedVisit['estimated_return_date']) ? Carbon\Carbon::parse($savedVisit['estimated_return_date'])->format('d.m.Y') : 'Belirlenmedi'); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <!--[if BLOCK]><![endif]--><?php if(!Auth::user()->hasRole(['Müşteri', 'Müşteri Temsilcisi'])): ?>
                                <div class="bg-white/50 p-3 rounded-xl border border-indigo-50">
                                    <span class="block text-[10px] uppercase font-extrabold text-gray-400 mb-2">Görüşülecek Kişiler</span>
                                    <div class="flex flex-wrap gap-1">
                                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $savedVisit['contact_persons'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <span class="px-2 py-1 bg-indigo-100/50 text-indigo-700 rounded-lg text-[10px] font-bold border border-indigo-200"><?php echo e($contact); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <span class="text-xs text-gray-400 font-medium italic">Kişi belirtilmedi</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                
                                <?php
                                    $showNotes = Auth::user()->is_personnel || ($savedVisit['is_visit_notes_visible_to_customer'] ?? false);
                                ?>
                                <!--[if BLOCK]><![endif]--><?php if($showNotes): ?>
                                <div class="bg-amber-50/30 p-3 rounded-xl border border-amber-100/50">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="block text-[10px] uppercase font-extrabold text-amber-500">Ek Notlar</span>
                                        <!--[if BLOCK]><![endif]--><?php if(Auth::user()->is_personnel == 1): ?>
                                            <button wire:click="toggleCustomerVisibility('is_visit_notes_visible_to_customer')" title="<?php echo e(($savedVisit['is_visit_notes_visible_to_customer'] ?? false) ? 'Müşteriye Açık (Gizle)' : 'Müşteriden Gizli (Göster)'); ?>" class="text-xs transition-colors <?php echo e(($savedVisit['is_visit_notes_visible_to_customer'] ?? false) ? 'text-green-500 hover:text-green-700' : 'text-gray-400 hover:text-gray-600'); ?>">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <!--[if BLOCK]><![endif]--><?php if($savedVisit['is_visit_notes_visible_to_customer'] ?? false): ?>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    <?php else: ?>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </svg>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="text-[11px] text-amber-800 italic"><?php echo e($savedVisit['visit_notes'] ?? 'Not yok'); ?></div>
                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                
                                <?php
                                    $showFiles = Auth::user()->is_personnel || ($savedVisit['is_visit_file_visible_to_customer'] ?? false);
                                ?>
                                <!--[if BLOCK]><![endif]--><?php if(!empty($savedVisit['visit_file']) && $showFiles): ?>
                                    <div class="bg-indigo-50/30 p-3 rounded-xl border border-indigo-100/50">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="block text-[10px] uppercase font-extrabold text-indigo-500">Ekli Dosyalar (<?php echo e(count($savedVisit['visit_file'])); ?>)</span>
                                            <!--[if BLOCK]><![endif]--><?php if(Auth::user()->is_personnel == 1): ?>
                                                <button wire:click="toggleCustomerVisibility('is_visit_file_visible_to_customer')" title="<?php echo e(($savedVisit['is_visit_file_visible_to_customer'] ?? false) ? 'Müşteriye Açık (Gizle)' : 'Müşteriden Gizli (Göster)'); ?>" class="text-xs transition-colors <?php echo e(($savedVisit['is_visit_file_visible_to_customer'] ?? false) ? 'text-green-500 hover:text-green-700' : 'text-gray-400 hover:text-gray-600'); ?>">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <!--[if BLOCK]><![endif]--><?php if($savedVisit['is_visit_file_visible_to_customer'] ?? false): ?>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        <?php else: ?>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </svg>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $savedVisit['visit_file']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                ?>
                                                <a wire:key="saved-file-summary-v2-<?php echo e($index); ?>-<?php echo e(md5($file)); ?>" href="<?php echo e(asset('storage/' . $file)); ?>" target="_blank" class="group relative aspect-square bg-white rounded-lg border border-indigo-100 overflow-hidden shadow-sm hover:shadow-md transition">
                                                    <!--[if BLOCK]><![endif]--><?php if($isImage): ?>
                                                        <img src="<?php echo e(asset('storage/' . $file)); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex flex-col items-center justify-center p-1 bg-indigo-50/20">
                                                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span class="text-[7px] font-black text-indigo-500 mt-0.5 uppercase"><?php echo e($extension); ?></span>
                                                        </div>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                
                                <?php
                                    $showFindings = Auth::user()->is_personnel || ($savedVisit['is_findings_visible_to_customer'] ?? false);
                                ?>
                                <!--[if BLOCK]><![endif]--><?php if(!empty($savedVisit['findings']) && $showFindings): ?>
                                    <div class="bg-white/50 p-3 rounded-xl border border-indigo-50">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="block text-[10px] uppercase font-extrabold text-indigo-400">Tespitler / Yapılan İşlemler</span>
                                            <!--[if BLOCK]><![endif]--><?php if(Auth::user()->is_personnel == 1): ?>
                                                <button wire:click="toggleCustomerVisibility('is_findings_visible_to_customer')" title="<?php echo e(($savedVisit['is_findings_visible_to_customer'] ?? false) ? 'Müşteriye Açık (Gizle)' : 'Müşteriden Gizli (Göster)'); ?>" class="text-xs transition-colors <?php echo e(($savedVisit['is_findings_visible_to_customer'] ?? false) ? 'text-green-500 hover:text-green-700' : 'text-gray-400 hover:text-gray-600'); ?>">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <!--[if BLOCK]><![endif]--><?php if($savedVisit['is_findings_visible_to_customer'] ?? false): ?>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        <?php else: ?>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </svg>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="text-[11px] text-gray-700 leading-relaxed whitespace-pre-wrap"><?php echo e($savedVisit['findings']); ?></div>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <?php
                                    $showResult = Auth::user()->is_personnel || ($savedVisit['is_result_visible_to_customer'] ?? false);
                                ?>
                                <!--[if BLOCK]><![endif]--><?php if(!empty($savedVisit['result']) && $showResult): ?>
                                    <div class="bg-emerald-50/50 p-3 rounded-xl border border-emerald-100">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="block text-[10px] uppercase font-extrabold text-emerald-500">Sonuç / Karar</span>
                                            <!--[if BLOCK]><![endif]--><?php if(Auth::user()->is_personnel == 1): ?>
                                                <button wire:click="toggleCustomerVisibility('is_result_visible_to_customer')" title="<?php echo e(($savedVisit['is_result_visible_to_customer'] ?? false) ? 'Müşteriye Açık (Gizle)' : 'Müşteriden Gizli (Göster)'); ?>" class="text-xs transition-colors <?php echo e(($savedVisit['is_result_visible_to_customer'] ?? false) ? 'text-green-500 hover:text-green-700' : 'text-gray-400 hover:text-gray-600'); ?>">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <!--[if BLOCK]><![endif]--><?php if($savedVisit['is_result_visible_to_customer'] ?? false): ?>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        <?php else: ?>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </svg>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="text-[11px] text-emerald-800 font-bold leading-relaxed whitespace-pre-wrap"><?php echo e($savedVisit['result']); ?></div>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                
                                <?php if(!Auth::user()->is_personnel && ($savedVisit['status'] ?? 'Beklemede') == 'Tamamlandı' && (!$showNotes || empty($savedVisit['visit_notes'])) && (!$showFiles || empty($savedVisit['visit_file'])) && (!$showFindings || empty($savedVisit['findings'])) && (!$showResult || empty($savedVisit['result']))): ?>
                                    <div class="bg-indigo-50/50 p-3 rounded-xl border border-indigo-100 mt-3">
                                        <span class="block text-[11px] font-bold text-indigo-700">Ziyaret sonrası gerekli kontroller sağlanmış ve raporlanmıştır (<?php echo e(\Carbon\Carbon::parse($savedVisit['completed_at'] ?? now())->format('d.m.Y')); ?>).</span>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


                            </div>
                        </div>

                        
                        <?php if(!Auth::user()->hasRole(['Müşteri', 'Müşteri Temsilcisi']) && count($visitLogs) > 0): ?>
                            <div class="mt-8 pt-6 border-t border-indigo-100" x-data="{ showAllLogs: false }">
                                <h4 class="text-xs font-black text-indigo-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Ziyaret Süreci Geçmişi
                                </h4>
                                <div class="bg-white rounded-2xl border border-indigo-50 overflow-hidden shadow-sm">
                                    <table class="min-w-full divide-y divide-gray-100">
                                        <thead class="bg-indigo-50/30">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-[10px] font-black text-indigo-400 uppercase tracking-wider">İşlem</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-black text-indigo-400 uppercase tracking-wider">Açıklama</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-black text-indigo-400 uppercase tracking-wider">Yapan</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-black text-indigo-400 uppercase tracking-wider">Tarih</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50 bg-white">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $visitLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="hover:bg-gray-50/50 transition duration-150" <?php if($index >= 5): ?> x-show="showAllLogs" x-cloak <?php endif; ?>>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-tight
                                                            <?php echo e(Str::contains($log->eylem, 'Onayla') ? 'bg-green-100 text-green-700' : 
                                                               (Str::contains($log->eylem, 'Red') ? 'bg-red-100 text-red-700' : 
                                                               (Str::contains($log->eylem, 'Revize') ? 'bg-orange-100 text-orange-700' : 'bg-indigo-100 text-indigo-700'))); ?>">
                                                            <?php echo e($log->eylem); ?>

                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-[11px] text-gray-600 font-medium leading-relaxed">
                                                        <?php echo e($log->aciklama); ?>

                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-[10px] font-bold">
                                                                <?php echo e(substr($log->user->name ?? '?', 0, 1)); ?>

                                                            </div>
                                                            <span class="text-[11px] font-bold text-gray-700"><?php echo e($log->user->name ?? 'Sistem'); ?></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-[10px] text-gray-400 font-bold uppercase">
                                                        <?php echo e($log->created_at->format('d.m.Y H:i')); ?>

                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                    </table>
                                    <!--[if BLOCK]><![endif]--><?php if(count($visitLogs) > 5): ?>
                                        <div class="bg-gray-50/50 p-3 border-t border-gray-100 flex justify-center">
                                            <button @click="showAllLogs = !showAllLogs" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-indigo-200 rounded-xl text-xs font-bold text-indigo-600 hover:bg-indigo-50 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                                                <span x-text="showAllLogs ? 'Daha Az Göster' : 'Daha Fazla Göster (<?php echo e(count($visitLogs) - 5); ?> Kayıt Daha)'"></span>
                                                <svg class="w-4 h-4 transform transition-transform duration-200" :class="showAllLogs ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    </div>
                </div>

                <!--[if BLOCK]><![endif]--><?php if($completerName && !Auth::user()->hasRole(['Müşteri', 'Müşteri Temsilcisi'])): ?>
                    <div class="mt-4 px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-[11px] text-emerald-800 font-medium">
                            Ziyaret sonuçları <strong><?php echo e($completerName); ?></strong> tarafından <strong><?php echo e($completedAt); ?></strong> tarihinde girilmiştir.
                        </p>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                
                
                <?php
                    $user = Auth::user();
                    $isVisitor = (isset($savedVisit['visitor_id']) && $savedVisit['visitor_id'] == $user->id) || (($formData['visitor_id'] ?? '') == $user->id);
                    $isAdmin = $user->hasRole('Superadmin');
                    $isDirector = $user->hasRole('Direktör');
                    $isQualityManager = $user->hasRole('Bölüm Kalite Yöneticisi');
                    $bolum = $this->iaa->bolum ?? null;
                    $catId = $this->iaa->musteriSikayeti->sikayet_kategorisi_id ?? null;
                    
                    // Onay Yetkisi Mantığı:
                    // 1. Superadmin/Yönetim her zaman onaylayabilir.
                    // 2. Direktör onayı aktifse sadece bağlı bölümün direktörü onaylayabilir.
                    // 3. Değilse, şikayetin kategorisinden sorumlu kalite yöneticisi onaylayabilir.
                    // 4. Ancak Ziyareti gerçekleştirecek Kişi (Visitor), Superadmin değilse onaylayamaz.
                    if ($direktorOnayiAktif) {
                        $isAuthorizedApprover = $isAdmin || ($isDirector && $bolum && $bolum->director_id == $user->id);
                    } else {
                        $isAuthorizedApprover = $isAdmin || ($isQualityManager && $user->yonettigiSikayetKategorileri->contains('id', $catId));
                    }
                    $isAuthorizedToApprove = $isAuthorizedApprover;
                    
                    if ($isVisitor && !$isAdmin) {
                        $isAuthorizedToApprove = false;
                    }

                    $canSeeFindings = $isAdmin || $isDirector || $isVisitor;

                    $status = $savedVisit['status'] ?? 'Beklemede';
                    $isCustomer = $user->hasRole(['Müşteri', 'Müşteri Temsilcisi']);
                    
                    $statusColors = [
                        'Beklemede' => 'bg-amber-50 text-amber-600 border border-amber-200',
                        'Direktör Onayı Bekliyor' => 'bg-blue-50 text-blue-600 border border-blue-200',
                        'Yönetim Onayı Bekliyor' => 'bg-blue-50 text-blue-600 border border-blue-200',
                        'Bölüm Onayı Bekliyor' => 'bg-blue-50 text-blue-600 border border-blue-200',
                        'Revize İsteniyor' => 'bg-orange-50 text-orange-600 border border-orange-200',
                        'Onaylandı' => 'bg-emerald-50 text-emerald-600 border border-emerald-200',
                        'Reddedildi' => 'bg-rose-50 text-rose-600 border border-rose-200',
                        'İptal Edildi' => 'bg-rose-50 text-rose-600 border border-rose-200',
                        'Tamamlandı' => 'bg-blue-50 text-blue-600 border border-blue-200'
                    ];
                    $currentColor = $statusColors[$status] ?? 'bg-gray-50 text-gray-600 border border-gray-200';
                    
                    $displayStatus = $status;
                    if (!empty($savedVisit['planner_revision_note']) && in_array($status, ['Beklemede', 'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetim Onayı Bekliyor'])) {
                        $displayStatus = 'Revizyon Sonrası Onay Bekliyor';
                        $currentColor = 'bg-blue-50 text-blue-600 border border-blue-200';
                    }

                    $onaylayacakMakam = $direktorOnayiAktif ? 'Direktör' : 'Bölüm Kalite Yöneticisi';
                    
                    $approvers = [];
                    $catId = $this->iaa->musteriSikayeti->sikayet_kategorisi_id ?? null;
                    if ($direktorOnayiAktif) {
                        $director = $this->iaa->bolum->director ?? null;
                        if ($director) {
                            $approvers[] = $director->name;
                        }
                    } else {
                        if ($catId) {
                            $approvers = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                                ->whereHas('yonettigiSikayetKategorileri', function($q) use ($catId) {
                                    $q->where('sikayet_kategorileri.id', $catId);
                                })->pluck('name')->toArray();
                        }
                    }
                    $approverNames = !empty($approvers) ? implode(', ', $approvers) : 'Yetkili Yönetici';
                    $onaylayacakKisiMakam = $onaylayacakMakam . ' ' . $approverNames;

                    $visitDateFormatted = Carbon\Carbon::parse($savedVisit['visit_date'])->format('d.m.Y');
                    $visitorName = $savedVisit['visitor_name'] ?? ($savedVisit['user']['name'] ?? 'Belirtilmedi');
                    $customerName = $this->iaa->musteriSikayeti->customer->name ?? ($this->iaa->musteriSikayeti->musteri_adi ?? 'Müşteri');
                    $plannerName = $savedVisit['planner_name'] ?? ($savedVisit['user']['name'] ?? 'Planlayan Kişi');
                    $plannedAt = \Carbon\Carbon::parse($savedVisit['created_at'])->format('d.m.Y H:i');
                    
                    $approverUser = \App\Models\User::find($savedVisit['approved_by'] ?? null);
                    $approverName = $approverUser ? $approverUser->name . ' (' . ($approverUser->unvan ?? 'Yetkili') . ')' : 'Belirtilmedi';
                ?>

                
                <!--[if BLOCK]><![endif]--><?php if(in_array($status, ['Beklemede', 'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetim Onayı Bekliyor', 'Revize İsteniyor', 'Onaylandı'])): ?>
                    <div class="mt-4 animate-pulse">
                        <!--[if BLOCK]><![endif]--><?php if($user->is_personnel == 0): ?>
                            
                            <div class="bg-white border <?php echo e($status === 'Revize İsteniyor' ? 'border-orange-200' : ($status === 'Onaylandı' ? 'border-emerald-200' : 'border-blue-200')); ?> rounded-2xl shadow-sm overflow-hidden relative">
                                <!-- Accent Line -->
                                <div class="absolute left-0 top-0 bottom-0 w-1.5 <?php echo e($status === 'Revize İsteniyor' ? 'bg-gradient-to-b from-orange-400 to-red-500' : ($status === 'Onaylandı' ? 'bg-gradient-to-b from-emerald-400 to-green-500' : 'bg-gradient-to-b from-blue-400 to-indigo-500')); ?>"></div>
                                
                                <div class="p-5 sm:p-6 flex flex-col sm:flex-row justify-between gap-6 ml-1.5">
                                    <!-- Sol Kısım: Mesaj İçeriği -->
                                    <div class="flex-1 flex flex-col justify-center">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="flex-shrink-0 w-10 h-10 <?php echo e($status === 'Revize İsteniyor' ? 'bg-orange-50 text-orange-600 border-orange-100' : ($status === 'Onaylandı' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-blue-50 text-blue-600 border-blue-100')); ?> rounded-xl flex items-center justify-center shadow-inner border">
                                                <!--[if BLOCK]><![endif]--><?php if($status === 'Onaylandı'): ?>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <?php else: ?>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            <h4 class="text-base font-black <?php echo e($status === 'Revize İsteniyor' ? 'text-orange-900' : ($status === 'Onaylandı' ? 'text-emerald-900' : 'text-blue-900')); ?> tracking-tight">
                                                <?php echo e($status === 'Revize İsteniyor' ? 'Ziyaret Planı Revizyon Bekliyor' : ($status === 'Onaylandı' ? 'Ziyaret Planı Onaylandı' : 'Ziyaret Onay Sürecinde')); ?>

                                            </h4>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-[13px] text-gray-700 font-medium w-full mt-2">
                                            <!-- Müşteri -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ziyarete Gidilecek Müşteri</span>
                                                <span class="font-black text-gray-900 text-sm"><?php echo e($customerName); ?></span>
                                            </div>

                                            <!-- Ziyaret Tarihi -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ziyaret Tarihi</span>
                                                <span class="font-black text-gray-900 text-sm"><?php echo e($visitDateFormatted); ?></span>
                                            </div>

                                            <!-- Ziyarete Gidecek Personeller -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ziyarete Gidecek Personeller</span>
                                                <span class="font-black text-gray-900 text-sm flex items-center gap-2">
                                                    <img src="<?php echo e(isset($savedVisit['user']['photo']) && $savedVisit['user']['photo'] ? $savedVisit['user']['photo'] : 'https://ui-avatars.com/api/?name='.urlencode($visitorName)); ?>" class="w-5 h-5 rounded-full object-cover">
                                                    <?php echo e($visitorName); ?>

                                                </span>
                                            </div>

                                            <!-- Planlayan -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Planlayan</span>
                                                <span class="font-black text-gray-900 text-sm flex items-center gap-2">
                                                    <img src="<?php echo e('https://ui-avatars.com/api/?name='.urlencode($plannerName)); ?>" class="w-5 h-5 rounded-full object-cover">
                                                    <?php echo e($plannerName); ?>

                                                </span>
                                            </div>

                                            <!-- Onaylayan -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Onaylayan</span>
                                                <span class="font-black text-gray-900 text-sm"><?php echo e($approverName); ?></span>
                                            </div>

                                            <!-- Dönüş Tarihi -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1 relative">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tahmini Tesise Dönüş Tarihi</span>
                                                <div class="flex flex-col w-full gap-2 mt-1">
                                                    <?php
                                                        $showRevisionToCustomer = ($savedVisit['is_return_date_revision_visible_to_customer'] ?? false);
                                                    ?>
                                                    <?php if(isset($savedVisit['return_date_revision_status']) && in_array($savedVisit['return_date_revision_status'], ['Bekliyor', 'Direktör Onayı Bekliyor']) && $showRevisionToCustomer): ?>
                                                        <div class="flex items-center gap-1.5 flex-wrap">
                                                            <span class="font-black text-gray-500 line-through text-xs"><?php echo e(!empty($savedVisit['estimated_return_date']) ? \Carbon\Carbon::parse($savedVisit['estimated_return_date'])->format('d.m.Y') : 'Belirtilmedi'); ?></span>
                                                            <svg class="w-3 h-3 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                            <span class="font-black text-orange-600 text-sm" title="Talep Edilen Yeni Tarih"><?php echo e(\Carbon\Carbon::parse($savedVisit['return_date_revision_requested_date'])->format('d.m.Y')); ?></span>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="font-black text-gray-900 text-sm"><?php echo e(!empty($savedVisit['estimated_return_date']) ? \Carbon\Carbon::parse($savedVisit['estimated_return_date'])->format('d.m.Y') : 'Belirtilmedi'); ?></span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                    <!--[if BLOCK]><![endif]--><?php if(!empty($savedVisit['estimated_return_date'])): ?>
                                                        <?php
                                                            $daysLeft = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($savedVisit['estimated_return_date']), false);
                                                            $isApproverOrAdminInline = (auth()->user()->hasRole('Superadmin') || (isset($savedVisit['approved_by']) && $savedVisit['approved_by'] == auth()->id()) || (auth()->user()->hasRole('Direktör') && $status == 'Onaylandı') || (auth()->user()->hasRole('Bölüm Kalite Yöneticisi') && $status == 'Direktör Onayı Bekliyor'));
                                                            
                                                            $canApproveRevision = false;
                                                            if (isset($savedVisit['return_date_revision_status'])) {
                                                                if ($savedVisit['return_date_revision_status'] === 'Bekliyor') {
                                                                    $canApproveRevision = auth()->id() == ($savedVisit['approved_by'] ?? null) || auth()->user()->hasRole('Superadmin');
                                                                } elseif ($savedVisit['return_date_revision_status'] === 'Direktör Onayı Bekliyor') {
                                                                    $canApproveRevision = auth()->user()->hasRole('Direktör') || auth()->user()->hasRole('Superadmin');
                                                                }
                                                            }
                                                        ?>
                                                        <?php if(isset($savedVisit['return_date_revision_status']) && in_array($savedVisit['return_date_revision_status'], ['Bekliyor', 'Direktör Onayı Bekliyor']) && $showRevisionToCustomer): ?>
                                                            <!--[if BLOCK]><![endif]--><?php if($canApproveRevision): ?>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <button wire:click="openReturnDateRevisionResponseModal('approve')" class="flex-1 text-[11px] bg-green-100 text-green-700 py-1.5 rounded-lg font-bold hover:bg-green-200 transition text-center border border-green-200 shadow-sm" title="Onayla">Onayla</button>
                                                                    <button wire:click="openReturnDateRevisionResponseModal('reject')" class="flex-1 text-[11px] bg-red-100 text-red-700 py-1.5 rounded-lg font-bold hover:bg-red-200 transition text-center border border-red-200 shadow-sm" title="Reddet">Reddet</button>
                                                                </div>
                                                            <?php else: ?>
                                                                <div><span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold inline-block"><?php echo e($savedVisit['return_date_revision_status'] === 'Direktör Onayı Bekliyor' ? 'Direktör Onayı Bekleniyor' : 'Yetkili Onayı Bekleniyor'); ?></span></div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <?php else: ?>
                                                            <!--[if BLOCK]><![endif]--><?php if($isApproverOrAdminInline && ($daysLeft > 1 || auth()->user()->hasRole('Superadmin'))): ?>
                                                                <div><button wire:click="openUpdateReturnDateModal" class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-bold hover:bg-blue-200 transition" title="Dönüş tarihini düzenle">Düzenle</button></div>
                                                            <?php elseif(isset($isVisitor) && $isVisitor && $status == 'Onaylandı'): ?>
                                                                <div><button wire:click="openReturnDateRevisionModal" class="text-[10px] bg-orange-100 text-orange-700 px-2 py-0.5 rounded font-bold hover:bg-orange-200 transition" title="Dönüş Tarihi Revizyonu İste">Revizyon İste</button></div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                                <?php if(!empty($savedVisit['estimated_return_date']) && isset($isApproverOrAdminInline) && $isApproverOrAdminInline): ?>
                                                    <!--[if BLOCK]><![endif]--><?php if($daysLeft > 1): ?>
                                                        <span class="text-[10px] text-orange-600 font-bold mt-1 leading-tight">Düzenlemek için son <?php echo e(floor($daysLeft - 1)); ?> gün</span>
                                                    <?php elseif($daysLeft <= 1 && auth()->user()->hasRole('Superadmin')): ?>
                                                        <span class="text-[10px] text-gray-500 font-bold mt-1 leading-tight">Süresi doldu (S.Admin)</span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Ziyaret Gün Sayısı -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ziyaret Gün Sayısı</span>
                                                <span class="font-black text-gray-900 text-sm">
                                                    <?php if(!empty($savedVisit['estimated_return_date']) && !empty($savedVisit['visit_date'])): ?>
                                                        <?php
                                                            $vDateForDiff = \Carbon\Carbon::parse($savedVisit['visit_date']);
                                                            $rDateForDiff = \Carbon\Carbon::parse($savedVisit['estimated_return_date']);
                                                            $gunSayisi = ceil(abs($vDateForDiff->floatDiffInDays($rDateForDiff)));
                                                            if ($gunSayisi < 1) $gunSayisi = 1;
                                                        ?>
                                                        <?php echo e($gunSayisi); ?> Gün
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mt-4 <?php echo e($status === 'Revize İsteniyor' ? 'text-orange-800 bg-orange-50 border-orange-100' : ($status === 'Onaylandı' ? 'text-emerald-800 bg-emerald-50 border-emerald-100' : 'text-blue-800 bg-blue-50 border-blue-100')); ?> p-3.5 rounded-xl inline-flex items-start gap-2 border shadow-sm w-full">
                                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            <div class="flex-1 text-sm leading-relaxed">
                                                <!--[if BLOCK]><![endif]--><?php if($status === 'Revize İsteniyor'): ?>
                                                    Planlanan ziyarette bir değişiklik yapılmaktadır. Yetkili ekiplerimiz en kısa sürede yeni durumu onaya sunacaktır.
                                                <?php elseif($status === 'Onaylandı'): ?>
                                                    Ziyaret onaylanmıştır. Planlanan tarihte temsilcilerimiz firmanızda olacaktır.
                                                <?php else: ?>
                                                    İlgili ziyaret planı, KÖKSAN yetkilileri tarafından şu an onay sürecindedir.
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sağ Kısım: Tarih Kutucuğu -->
                                    <div class="flex-shrink-0 sm:w-48 bg-gray-50 rounded-xl border border-gray-200 p-4 flex flex-col items-center justify-center text-center shadow-inner">
                                        <svg class="w-6 h-6 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1"><?php echo e($status === 'Onaylandı' ? 'Onaylanma' : 'Onaya Gönderilme'); ?></span>
                                        <span class="block text-sm font-black text-gray-800"><?php echo e(\Carbon\Carbon::parse($savedVisit['updated_at'] ?? $savedVisit['created_at'])->format('d.m.Y')); ?></span>
                                        <span class="block text-xs font-bold text-gray-500"><?php echo e(\Carbon\Carbon::parse($savedVisit['updated_at'] ?? $savedVisit['created_at'])->format('H:i')); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            
                            <div class="bg-white border <?php echo e($status === 'Revize İsteniyor' ? 'border-orange-200' : ($status === 'Onaylandı' ? 'border-emerald-200' : 'border-amber-200')); ?> rounded-2xl shadow-sm overflow-hidden relative">
                                <!-- Accent Line -->
                                <div class="absolute left-0 top-0 bottom-0 w-1.5 <?php echo e($status === 'Revize İsteniyor' ? 'bg-gradient-to-b from-orange-400 to-red-500' : ($status === 'Onaylandı' ? 'bg-gradient-to-b from-emerald-400 to-green-500' : 'bg-gradient-to-b from-amber-400 to-orange-500')); ?>"></div>
                                
                                <div class="p-5 sm:p-6 flex flex-col sm:flex-row justify-between gap-6 ml-1.5">
                                    <!-- Sol Kısım: Mesaj İçeriği -->
                                    <div class="flex-1 flex flex-col justify-center">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="flex-shrink-0 w-10 h-10 <?php echo e($status === 'Revize İsteniyor' ? 'bg-orange-50 text-orange-600 border-orange-100' : ($status === 'Onaylandı' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100')); ?> rounded-xl flex items-center justify-center shadow-inner border">
                                                <!--[if BLOCK]><![endif]--><?php if($status === 'Onaylandı'): ?>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <?php else: ?>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            <h4 class="text-base font-black <?php echo e($status === 'Revize İsteniyor' ? 'text-orange-900' : ($status === 'Onaylandı' ? 'text-emerald-900' : 'text-amber-900')); ?> tracking-tight">
                                                <?php echo e($status === 'Revize İsteniyor' ? 'Ziyaret Planı Revizyon Bekliyor' : ($status === 'Onaylandı' ? 'Ziyaret Planı Onaylandı' : 'Ziyaret Onayı Bekleniyor')); ?>

                                            </h4>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-[13px] text-gray-700 font-medium w-full mt-2">
                                            <!-- Müşteri -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ziyarete Gidilecek Müşteri</span>
                                                <span class="font-black text-gray-900 text-sm"><?php echo e($customerName); ?></span>
                                            </div>

                                            <!-- Ziyaret Tarihi -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ziyaret Tarihi</span>
                                                <span class="font-black text-gray-900 text-sm"><?php echo e($visitDateFormatted); ?></span>
                                            </div>

                                            <!-- Ziyarete Gidecek Personeller -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ziyarete Gidecek Personeller</span>
                                                <span class="font-black text-gray-900 text-sm flex items-center gap-2">
                                                    <img src="<?php echo e(isset($savedVisit['user']['photo']) && $savedVisit['user']['photo'] ? $savedVisit['user']['photo'] : 'https://ui-avatars.com/api/?name='.urlencode($visitorName)); ?>" class="w-5 h-5 rounded-full object-cover">
                                                    <?php echo e($visitorName); ?>

                                                </span>
                                            </div>

                                            <!-- Planlayan -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Planlayan</span>
                                                <span class="font-black text-gray-900 text-sm flex items-center gap-2">
                                                    <img src="<?php echo e('https://ui-avatars.com/api/?name='.urlencode($plannerName)); ?>" class="w-5 h-5 rounded-full object-cover">
                                                    <?php echo e($plannerName); ?>

                                                </span>
                                            </div>

                                            <!-- Onaylayan -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Onaylayan</span>
                                                <span class="font-black text-gray-900 text-sm"><?php echo e($approverName); ?></span>
                                            </div>

                                            <!-- Dönüş Tarihi -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1 relative">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tahmini Tesise Dönüş Tarihi</span>
                                                    <?php if(isset($savedVisit['return_date_revision_status']) && $savedVisit['return_date_revision_status'] === 'Bekliyor'): ?>
                                                        <button wire:click="toggleCustomerVisibility('is_return_date_revision_visible_to_customer')" title="<?php echo e(($savedVisit['is_return_date_revision_visible_to_customer'] ?? false) ? 'Müşteriye Açık (Gizle)' : 'Müşteriden Gizli (Göster)'); ?>" class="text-xs transition-colors <?php echo e(($savedVisit['is_return_date_revision_visible_to_customer'] ?? false) ? 'text-green-500 hover:text-green-700' : 'text-gray-400 hover:text-gray-600'); ?>">
                                                            <!--[if BLOCK]><![endif]--><?php if($savedVisit['is_return_date_revision_visible_to_customer'] ?? false): ?>
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            <?php else: ?>
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </button>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                                <div class="flex flex-col w-full gap-2 mt-1">
                                                    <!--[if BLOCK]><![endif]--><?php if(isset($savedVisit['return_date_revision_status']) && in_array($savedVisit['return_date_revision_status'], ['Bekliyor', 'Direktör Onayı Bekliyor'])): ?>
                                                        <div class="flex items-center gap-1.5 flex-wrap">
                                                            <span class="font-black text-gray-500 line-through text-xs"><?php echo e(!empty($savedVisit['estimated_return_date']) ? \Carbon\Carbon::parse($savedVisit['estimated_return_date'])->format('d.m.Y') : 'Belirtilmedi'); ?></span>
                                                            <svg class="w-3 h-3 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                            <span class="font-black text-orange-600 text-sm" title="Talep Edilen Yeni Tarih"><?php echo e(\Carbon\Carbon::parse($savedVisit['return_date_revision_requested_date'])->format('d.m.Y')); ?></span>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="font-black text-gray-900 text-sm"><?php echo e(!empty($savedVisit['estimated_return_date']) ? \Carbon\Carbon::parse($savedVisit['estimated_return_date'])->format('d.m.Y') : 'Belirtilmedi'); ?></span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                    <!--[if BLOCK]><![endif]--><?php if(!empty($savedVisit['estimated_return_date'])): ?>
                                                        <?php
                                                            $daysLeft = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($savedVisit['estimated_return_date']), false);
                                                            $isApproverOrAdminInline = (auth()->user()->hasRole('Superadmin') || (isset($savedVisit['approved_by']) && $savedVisit['approved_by'] == auth()->id()) || (auth()->user()->hasRole('Direktör') && $status == 'Onaylandı') || (auth()->user()->hasRole('Bölüm Kalite Yöneticisi') && $status == 'Direktör Onayı Bekliyor'));
                                                            
                                                            $canApproveRevision = false;
                                                            if (isset($savedVisit['return_date_revision_status'])) {
                                                                if ($savedVisit['return_date_revision_status'] === 'Bekliyor') {
                                                                    $canApproveRevision = auth()->id() == ($savedVisit['approved_by'] ?? null) || auth()->user()->hasRole('Superadmin');
                                                                } elseif ($savedVisit['return_date_revision_status'] === 'Direktör Onayı Bekliyor') {
                                                                    $canApproveRevision = auth()->user()->hasRole('Direktör') || auth()->user()->hasRole('Superadmin');
                                                                }
                                                            }
                                                        ?>
                                                        <!--[if BLOCK]><![endif]--><?php if(isset($savedVisit['return_date_revision_status']) && in_array($savedVisit['return_date_revision_status'], ['Bekliyor', 'Direktör Onayı Bekliyor'])): ?>
                                                            <?php
                                                                // Get Requester Name
                                                                $revisionRequesterName = 'Bilinmiyor';
                                                                if (!empty($savedVisit['return_date_revision_requested_by'])) {
                                                                    $reqUser = \App\Models\User::find($savedVisit['return_date_revision_requested_by']);
                                                                    if ($reqUser) $revisionRequesterName = $reqUser->name;
                                                                } elseif (!empty($savedVisit['visitors'])) {
                                                                    $visitorsData = is_string($savedVisit['visitors']) ? json_decode($savedVisit['visitors'], true) : $savedVisit['visitors'];
                                                                    if (is_array($visitorsData) && count($visitorsData) > 0) {
                                                                        $v = \App\Models\User::find($visitorsData[0]);
                                                                        if ($v) $revisionRequesterName = $v->name;
                                                                    }
                                                                } elseif (!empty($savedVisit['visitor_id'])) {
                                                                    $v = \App\Models\User::find($savedVisit['visitor_id']);
                                                                    if ($v) $revisionRequesterName = $v->name;
                                                                }
                                                                
                                                                // Get Request Date and Days Waiting
                                                                $requestDateStr = '-';
                                                                $daysWaiting = 0;
                                                                $revisionLog = \App\Models\IaaLog::where('iaa_id', $savedVisit['iaa_id'])
                                                                    ->where('eylem', 'Dönüş Tarihi Revizyonu Talep Edildi')
                                                                    ->latest()
                                                                    ->first();
                                                                if ($revisionLog) {
                                                                    $requestDateStr = $revisionLog->created_at->format('d.m.Y H:i');
                                                                    $daysWaiting = floor($revisionLog->created_at->diffInDays(now()));
                                                                }
                                                                
                                                                $isRequesterOrSuperadmin = (!empty($savedVisit['return_date_revision_requested_by']) && $savedVisit['return_date_revision_requested_by'] == auth()->id()) || (empty($savedVisit['return_date_revision_requested_by']) && isset($isVisitor) && $isVisitor) || auth()->user()->hasRole('Superadmin');
                                                            ?>
                                                            <!--[if BLOCK]><![endif]--><?php if($canApproveRevision): ?>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <button wire:click="openReturnDateRevisionResponseModal('approve')" class="flex-1 text-[11px] bg-green-100 text-green-700 py-1.5 rounded-lg font-bold hover:bg-green-200 transition text-center border border-green-200 shadow-sm" title="Onayla">Onayla</button>
                                                                    <button wire:click="openReturnDateRevisionResponseModal('reject')" class="flex-1 text-[11px] bg-red-100 text-red-700 py-1.5 rounded-lg font-bold hover:bg-red-200 transition text-center border border-red-200 shadow-sm" title="Reddet">Reddet</button>
                                                                </div>
                                                                <div class="mt-1.5 flex flex-col gap-0.5 border-t border-gray-100 pt-1.5">
                                                                    <span class="text-[9px] text-gray-500 font-medium leading-tight">Talep <strong class="text-gray-700"><?php echo e($revisionRequesterName); ?></strong> tarafından <strong class="text-gray-700"><?php echo e($requestDateStr); ?></strong> tarihinde iletildi.</span>
                                                                    <!--[if BLOCK]><![endif]--><?php if($daysWaiting > 0): ?>
                                                                        <span class="text-[9px] text-red-500 font-bold leading-tight"><?php echo e($daysWaiting); ?> gündür onayda bekliyor.</span>
                                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                    <!--[if BLOCK]><![endif]--><?php if($isRequesterOrSuperadmin): ?>
                                                                        <button wire:click="cancelReturnDateRevision" class="mt-1 flex items-center justify-center gap-1 w-full text-[10px] bg-white text-red-600 border border-red-200 hover:bg-red-50 py-1.5 rounded-lg shadow-sm font-bold transition" title="Talebi İptal Et">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                            Talebi İptal Et
                                                                        </button>
                                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                </div>
                                                            <?php else: ?>
                                                                <?php
                                                                    $waitingForName = 'Yetkili';
                                                                    if ($savedVisit['return_date_revision_status'] === 'Direktör Onayı Bekliyor') {
                                                                        $projectBolum = \App\Models\Iaa::find($savedVisit['iaa_id'])->bolum ?? null;
                                                                        if ($projectBolum && $projectBolum->director) {
                                                                            $waitingForName = $projectBolum->director->name;
                                                                        } else {
                                                                            $waitingForName = 'Direktör';
                                                                        }
                                                                    } else {
                                                                        if (!empty($savedVisit['approved_by'])) {
                                                                            $apprUser = \App\Models\User::find($savedVisit['approved_by']);
                                                                            if ($apprUser) $waitingForName = $apprUser->name;
                                                                        }
                                                                    }
                                                                ?>
                                                                <div class="mt-1 flex flex-col gap-1.5 border-t border-gray-100 pt-1.5">
                                                                    <div><span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold inline-block"><?php echo e($waitingForName); ?> Onayı Bekleniyor</span></div>
                                                                    
                                                                    <span class="text-[10px] text-gray-500 font-medium leading-tight">
                                                                        <!--[if BLOCK]><![endif]--><?php if($isRequesterOrSuperadmin): ?>
                                                                            Talebiniz <strong class="text-gray-700"><?php echo e($requestDateStr); ?></strong> tarihinde iletildi.
                                                                        <?php else: ?>
                                                                            Talep <strong class="text-gray-700"><?php echo e($revisionRequesterName); ?></strong> tarafından <strong class="text-gray-700"><?php echo e($requestDateStr); ?></strong> tarihinde iletildi.
                                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                    </span>
                                                                    
                                                                    <!--[if BLOCK]><![endif]--><?php if($isRequesterOrSuperadmin): ?>
                                                                        <button wire:click="cancelReturnDateRevision" class="mt-0.5 flex items-center justify-center gap-1 w-full text-[10px] bg-white text-red-600 border border-red-200 hover:bg-red-50 py-1.5 rounded-lg shadow-sm font-bold transition" title="Talebi İptal Et">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                            Talebi İptal Et
                                                                        </button>
                                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                </div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <?php else: ?>
                                                            <!--[if BLOCK]><![endif]--><?php if($isApproverOrAdminInline && ($daysLeft > 1 || auth()->user()->hasRole('Superadmin'))): ?>
                                                                <div><button wire:click="openUpdateReturnDateModal" class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-bold hover:bg-blue-200 transition" title="Dönüş tarihini düzenle">Düzenle</button></div>
                                                            <?php elseif(isset($isVisitor) && $isVisitor && $status == 'Onaylandı'): ?>
                                                                <div><button wire:click="openReturnDateRevisionModal" class="text-[10px] bg-orange-100 text-orange-700 px-2 py-0.5 rounded font-bold hover:bg-orange-200 transition" title="Dönüş Tarihi Revizyonu İste">Revizyon İste</button></div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                                <?php if(!empty($savedVisit['estimated_return_date']) && isset($isApproverOrAdminInline) && $isApproverOrAdminInline && (!isset($savedVisit['return_date_revision_status']) || !in_array($savedVisit['return_date_revision_status'], ['Bekliyor', 'Direktör Onayı Bekliyor']))): ?>
                                                    <!--[if BLOCK]><![endif]--><?php if($daysLeft > 1): ?>
                                                        <span class="text-[10px] text-orange-600 font-bold mt-1 leading-tight">Düzenlemek için son <?php echo e(floor($daysLeft - 1)); ?> gün</span>
                                                    <?php elseif($daysLeft <= 1 && auth()->user()->hasRole('Superadmin')): ?>
                                                        <span class="text-[10px] text-gray-500 font-bold mt-1 leading-tight">Süresi doldu (S.Admin)</span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Ziyaret Gün Sayısı -->
                                            <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-100 flex flex-col gap-1">
                                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ziyaret Gün Sayısı</span>
                                                <span class="font-black text-gray-900 text-sm">
                                                    <?php if(!empty($savedVisit['estimated_return_date']) && !empty($savedVisit['visit_date'])): ?>
                                                        <?php
                                                            $vDateForDiff = \Carbon\Carbon::parse($savedVisit['visit_date']);
                                                            $rDateForDiff = \Carbon\Carbon::parse($savedVisit['estimated_return_date']);
                                                            $gunSayisi = ceil(abs($vDateForDiff->floatDiffInDays($rDateForDiff)));
                                                            if ($gunSayisi < 1) $gunSayisi = 1;
                                                        ?>
                                                        <?php echo e($gunSayisi); ?> Gün
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mt-4 <?php echo e($status === 'Revize İsteniyor' ? 'text-orange-800 bg-orange-50 border-orange-100' : ($status === 'Onaylandı' ? 'text-emerald-800 bg-emerald-50 border-emerald-100' : 'text-amber-800 bg-amber-50 border-amber-100')); ?> p-3.5 rounded-xl inline-flex items-start gap-2 border shadow-sm w-full">
                                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            <div class="flex-1 text-sm leading-relaxed">
                                                <!--[if BLOCK]><![endif]--><?php if($status === 'Revize İsteniyor'): ?>
                                                    Planı düzenleyerek "Taslağı Kaydet ve Onaya Sun" butonundan tekrar onaya gönderebilirsiniz.
                                                <?php elseif($status === 'Onaylandı'): ?>
                                                    Ziyaret tüm yetkililer tarafından onaylanmıştır. 
                                                    <!--[if BLOCK]><![endif]--><?php if(isset($isVisitor) && $isVisitor): ?>
                                                        <strong class="font-black text-emerald-700">Ziyaretin sonuçlarını girebilirsiniz.</strong>
                                                    <?php elseif(isset($canCompleteVisit) && $canCompleteVisit): ?>
                                                        <strong class="font-black text-emerald-700"><?php echo e($visitorName); ?> adına ziyaret sonuçlarını isterseniz siz de girebilirsiniz.</strong>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <?php else: ?>
                                                    Ziyaretin <strong class="font-black"><?php echo e($onaylayacakKisiMakam); ?></strong> tarafından onaylanması bekleniyor.
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sağ Kısım: Onaya Gönderilme Tarihi -->
                                    <div class="flex-shrink-0 sm:w-48 bg-gray-50 rounded-xl border border-gray-200 p-4 flex flex-col items-center justify-center text-center shadow-inner">
                                        <svg class="w-6 h-6 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1"><?php echo e($status === 'Onaylandı' ? 'Onaylanma' : 'Onaya Gönderilme'); ?></span>
                                        <span class="block text-sm font-black text-gray-800"><?php echo e(\Carbon\Carbon::parse($savedVisit['updated_at'] ?? $savedVisit['created_at'])->format('d.m.Y')); ?></span>
                                        <span class="block text-xs font-bold text-gray-500"><?php echo e(\Carbon\Carbon::parse($savedVisit['updated_at'] ?? $savedVisit['created_at'])->format('H:i')); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                
                <div class="mt-6 pt-4 border-t border-indigo-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-500 uppercase">Durum:</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wide <?php echo e($currentColor); ?>">
                            <?php echo e($displayStatus); ?>

                        </span>
                        <!--[if BLOCK]><![endif]--><?php if(isset($savedVisit['return_date_revision_status']) && in_array($savedVisit['return_date_revision_status'], ['Bekliyor', 'Direktör Onayı Bekliyor'])): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide bg-orange-100 text-orange-800 border border-orange-300 shadow-sm animate-pulse ml-2" title="Yeni bir tesise dönüş tarihi önerisi için onay bekleniyor">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                DÖNÜŞ TARİHİ REVİZYONU BEKLİYOR
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <!--[if BLOCK]><![endif]--><?php if($isAuthorizedToApprove && in_array($status, ['Beklemede', 'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetim Onayı Bekliyor'])): ?>
                            <div class="flex items-center gap-3">
                                <button @click="showRevision = true" type="button" class="inline-flex items-center px-4 py-2 bg-orange-100 border border-orange-200 rounded-xl font-bold text-xs text-orange-700 uppercase tracking-widest hover:bg-orange-200 transition">
                                    Revizyon İste
                                </button>
                                <button @click="showReject = true" type="button" class="inline-flex items-center px-4 py-2 bg-red-100 border border-red-200 rounded-xl font-bold text-xs text-red-700 uppercase tracking-widest hover:bg-red-200 transition">
                                    Reddet
                                </button>
                                <button @click="showApprove = true" type="button" class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition shadow-lg shadow-green-200">
                                    Onayla
                                </button>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if(!$isReadOnly && in_array($status, ['Onaylandı', 'Direktör Onayı Bekliyor'])): ?>
                            <?php
                                $isApproverOrAdmin = (auth()->user()->hasRole('Superadmin') || (isset($savedVisit['approved_by']) && $savedVisit['approved_by'] == auth()->id()) || (auth()->user()->hasRole('Direktör') && $status == 'Onaylandı') || (auth()->user()->hasRole('Bölüm Kalite Yöneticisi') && $status == 'Direktör Onayı Bekliyor'));
                            ?>
                            <!--[if BLOCK]><![endif]--><?php if($isApproverOrAdmin): ?>
                                <div class="flex items-center gap-2 mt-3 sm:mt-0" x-data="{ confirming: false }">
                                    <button x-show="!confirming" @click="confirming = true" type="button" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        Onayı Geri Al
                                    </button>
                                    <div x-show="confirming" class="flex items-center gap-2" x-cloak>
                                        <span class="text-xs text-red-600 font-bold mr-2">Emin misiniz?</span>
                                        <button wire:click="revertApproval" @click="confirming = false" type="button" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                                            Evet
                                        </button>
                                        <button @click="confirming = false" type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-200 border border-transparent rounded-lg font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                            İptal
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        
                        <!--[if BLOCK]><![endif]--><?php if($status === 'Onaylandı' && !$isReadOnly && $canCompleteVisit): ?>
                            
                            <button wire:click="editVisit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Ziyaret Sonuçlarını Gir / Tamamla
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if($status === 'Tamamlandı' && $canCompleteVisit && $canEdit): ?>
                            <button wire:click="completeVisit" wire:loading.attr="disabled" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                                <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <svg wire:loading class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Takvim Kaydını Güncelle
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if($status === 'Beklemede' && $canEdit): ?>
                            <button wire:click="revertVisit" onclick="confirm('Ziyaret planını tamamen silmek ve gönderilen bildirimleri geri almak istediğinize emin misiniz?') || event.stopImmediatePropagation()" class="px-4 py-2 bg-rose-50 text-rose-700 text-xs font-bold rounded-lg border border-rose-200 hover:bg-rose-100 transition shadow-sm">
                                Geri Al
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if($status === 'Onaylandı' && $canEdit): ?>
                            <button wire:click="openCancelModal" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-lg hover:bg-rose-700 transition shadow-sm border border-transparent">
                                İptal Et
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                
            </div>
        <?php elseif($isOpen): ?>
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    <!-- SOL SÜTUN: ZİYARET PLANLAMA BİLGİLERİ -->
                    <div class="space-y-6">
                        <div class="bg-gray-50/50 p-5 rounded-3xl border border-gray-100 space-y-4 <?php echo e(($isReadOnly || $isEnteringResults) ? 'opacity-75 pointer-events-none' : ''); ?>">
                            <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-2">Ziyaret Detayları</h4>
                            
                            <!--[if BLOCK]><![endif]--><?php if($isReadOnly): ?>
                                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-2 text-amber-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m12-3V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2zM9 7V5a3 3 0 016 0v2M9 7h6"/></svg>
                                    <p class="text-[10px] font-bold uppercase tracking-tight">Onay sürecindeki ziyaret düzenlenemez.</p>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <!--[if BLOCK]><![endif]--><?php if($isEnteringResults && Auth::user()->hasRole('Direktör')): ?>
                                <div class="mb-4 p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-start gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="text-[11px] text-blue-800 leading-relaxed">
                                        <p class="font-bold uppercase tracking-tight mb-1">Direktör Bilgilendirme</p>
                                        Bu bilgiler normal şartlarda ziyareti gerçekleştirecek kişi tarafından girilecektir. Dilerseniz siz de onun yerine girebilirsiniz.
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Ziyaret Tarihi (*)</label>
                                <input type="datetime-local" wire:model="formData.visit_date" class="w-full text-sm border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500" required <?php echo e($isReadOnly ? 'disabled' : ''); ?>>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['formData.visit_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-[10px]"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Ziyaret Nedeni (*)</label>
                                <select wire:model="formData.visit_reason" class="w-full text-sm border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500" required <?php echo e($isReadOnly ? 'disabled' : ''); ?>>
                                    <option value="">Nedeni Seçiniz...</option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customerData['visit_reasons'] ?? ['Şikayet İnceleme', 'Ürün Denemesi', 'Teknik Destek', 'Periyodik Ziyaret', 'Rutin Ziyaret', 'Diğer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($reason); ?>"><?php echo e($reason); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 text-indigo-600">İşletme / Fabrika (*)</label>
                                <select wire:model.live="formData.business_unit_id" class="w-full text-sm border-indigo-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-indigo-50/10" required <?php echo e($isReadOnly ? 'disabled' : ''); ?>>
                                    <option value="">İşletme Seçiniz...</option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $businessUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($unit['id']); ?>"><?php echo e($unit['name']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Ürün Tanımı (Takvim'den)</label>
                                <select wire:model="formData.customer_product_id" class="w-full text-sm border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Ürün Seçiniz...</option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customerData['products'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($product['id']); ?>"><?php echo e($product['name']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                            </div>

                            <div x-data="{ 
                                search: '', 
                                open: false, 
                                selected: <?php if ((object) ('formData.visitor_ids') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('formData.visitor_ids'->value()); ?>')<?php echo e('formData.visitor_ids'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('formData.visitor_ids'); ?>')<?php endif; ?>.live,
                                users: <?php echo e(json_encode($customerData['users'] ?? [])); ?>,
                                toggle(id) {
                                    if (!Array.isArray(this.selected)) this.selected = [];
                                    if(id === 'Diğer') {
                                        if(this.selected.includes('Diğer')) {
                                            this.selected = this.selected.filter(i => i !== 'Diğer');
                                        } else {
                                            this.selected.push('Diğer');
                                        }
                                        return;
                                    }
                                    let strId = String(id);
                                    if (this.selected.includes(strId)) {
                                        this.selected = this.selected.filter(i => i !== strId);
                                    } else {
                                        this.selected.push(strId);
                                    }
                                },
                                getSelectedNames() {
                                    if (!this.selected || this.selected.length === 0) return 'Personel Seçiniz...';
                                    let names = [];
                                    this.selected.forEach(id => {
                                        if (id === 'Diğer') names.push('Diğer (Manuel Giriş)');
                                        else {
                                            let user = this.users.find(u => String(u.id) === String(id));
                                            if (user) names.push(user.name.replace(' (İAA Personeli)', ''));
                                        }
                                    });
                                    return names.join(', ') || 'Personel Seçiniz...';
                                }
                            }" class="relative w-full">
                                <label class="block text-xs font-bold text-gray-700 mb-1 text-indigo-600">Ziyarete Kim Gidecek? (*)</label>
                                
                                <div @click="open = !open" @click.away="open = false" class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-indigo-50/20 cursor-pointer flex justify-between items-center" :class="{ 'opacity-50 cursor-not-allowed': <?php echo e($isReadOnly ? 'true' : 'false'); ?> }">
                                    <span x-text="getSelectedNames()" class="truncate block max-w-[90%]"></span>
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>

                                <div x-show="open" x-transition class="absolute z-[100] w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                                    <div class="p-2 sticky top-0 bg-white border-b border-gray-100">
                                        <input type="text" x-model="search" placeholder="İsim ara..." class="w-full text-xs border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-1.5" @click.stop>
                                    </div>
                                    <div class="p-1">
                                        <template x-for="user in users.filter(u => u.name.toLowerCase().includes(search.toLowerCase()))" :key="user.id">
                                            <div @click="toggle(user.id)" class="px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 cursor-pointer rounded-lg flex items-center justify-between" :class="selected.includes(String(user.id)) ? 'bg-indigo-50 font-bold' : ''">
                                                <span x-text="user.name"></span>
                                                <svg x-show="selected.includes(String(user.id))" class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        </template>

                                        <div @click="toggle('Diğer')" class="px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 cursor-pointer rounded-lg mt-1 border-t border-gray-100 flex items-center justify-between" :class="selected.includes('Diğer') ? 'bg-indigo-50 font-bold' : ''">
                                            <span>Diğer (Manuel Giriş)</span>
                                            <svg x-show="selected.includes('Diğer')" class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if(in_array('Diğer', $formData['visitor_ids'] ?? [])): ?>
                                <div class="mt-2">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Diğer Ziyaretçi İsmi (*)</label>
                                    <input type="text" wire:model="formData.other_visitor_name" placeholder="İsim Soyisim (Birden fazlaysa virgülle ayırın)" class="w-full text-sm border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div class="bg-gray-50/50 p-5 rounded-3xl border border-gray-100 space-y-4 <?php echo e(($isReadOnly || $isEnteringResults) ? 'opacity-75 pointer-events-none' : ''); ?>">
                            <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-2">Katılımcılar</h4>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Görüşülecek Kişiler (Rehber)</label>
                                <div class="bg-white p-3 rounded-2xl border border-gray-200 max-h-40 overflow-y-auto space-y-1">
                                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $customerData['contacts'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <label class="flex items-center text-[11px] text-gray-600 cursor-pointer hover:bg-gray-50 p-1 rounded-lg transition">
                                            <input type="checkbox" wire:model="formData.contact_persons" value="<?php echo e($contact['name']); ?>" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-2">
                                            <?php echo e($contact['name']); ?>

                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span class="text-[10px] text-gray-400 italic font-medium">Rehberde kişi bulunamadı.</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Yeni Kişiler (Virgül ile ayırın)</label>
                                <input type="text" wire:model="formData.other_contact_persons" placeholder="İsim Soyisim..." class="w-full text-sm border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500" <?php echo e($isReadOnly ? 'disabled' : ''); ?>>
                            </div>
                        </div>
                    </div>

                    <!-- SAĞ SÜTUN: SONUÇLAR VE TEKNİK DETAYLAR -->
                    <div class="space-y-6">
                        <!--[if BLOCK]><![endif]--><?php if($canSeeFindings): ?>
                            <div class="bg-white p-6 rounded-3xl border-2 border-indigo-100 shadow-sm space-y-5">
                                <h4 class="text-xs font-extrabold text-indigo-600 uppercase tracking-widest mb-2">Ziyaret Sonuçları</h4>
                                
                                <div>
                                    <label class="block text-xs font-bold text-indigo-700 mb-2 uppercase tracking-wider">Tespitler / Yapılan İşlemler</label>
                                    <textarea wire:model="formData.findings" rows="10" class="w-full text-sm border-gray-200 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 shadow-inner bg-gray-50/30" placeholder="Ziyaret sırasında yapılan gözlemler..." <?php echo e(!$canCompleteVisit ? 'disabled' : ''); ?>></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-emerald-700 mb-2 uppercase tracking-wider">Sonuç / Karar</label>
                                    <textarea wire:model="formData.result" rows="5" class="w-full text-sm border-gray-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 shadow-inner bg-gray-50/30" placeholder="Alınan kararlar..." <?php echo e(!$canCompleteVisit ? 'disabled' : ''); ?>></textarea>
                                </div>

                                
                                <!--[if BLOCK]><![endif]--><?php if($canCompleteVisit && !$isReadOnly): ?>
                                    <div class="pt-4 border-t border-gray-100">
                                        <label class="block text-xs font-bold text-indigo-700 mb-3 uppercase tracking-wider flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Sonuç Fotoğraf / Dosyaları Ekle
                                        </label>
                                        
                                        <div class="relative group" x-data="{ isUploading: false, progress: 0 }" 
                                             x-on:livewire-upload-start="isUploading = true" 
                                             x-on:livewire-upload-finish="isUploading = false" 
                                             x-on:livewire-upload-error="isUploading = false" 
                                             x-on:livewire-upload-progress="progress = $event.detail.progress">
                                            
                                            <input type="file" wire:model="visitFiles" multiple accept="image/*,.pdf,.doc,.docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                            
                                            <div class="p-6 border-2 border-dashed border-indigo-100 rounded-2xl bg-indigo-50/30 group-hover:bg-indigo-50 group-hover:border-indigo-200 transition-all text-center">
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="p-3 bg-white rounded-full shadow-sm text-indigo-500 group-hover:scale-110 transition-transform">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    </div>
                                                    <span class="text-xs font-bold text-indigo-700">Dosyaları sürükleyin veya tıklayın</span>
                                                    <span class="text-[10px] text-indigo-400 font-medium">PNG, JPG, PDF, DOCX (Sınırsız Çoklu Seçim)</span>
                                                </div>
                                            </div>

                                            
                                            <div x-show="isUploading" class="mt-4">
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                                                    <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                                                </div>
                                                <div class="flex justify-between items-center text-[10px] font-bold text-indigo-600">
                                                    <span>Dosyalar Yükleniyor...</span>
                                                    <span x-text="progress + '%'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <!--[if BLOCK]><![endif]--><?php if(!empty($visitFiles)): ?>
                                            <div class="grid grid-cols-4 gap-3 mt-4">
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $visitFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="relative aspect-square rounded-xl border border-indigo-100 overflow-hidden shadow-sm group bg-white">
                                                        <?php
                                                            $isImg = in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        ?>
                                                        <!--[if BLOCK]><![endif]--><?php if($isImg && method_exists($file, 'temporaryUrl')): ?>
                                                            <img src="<?php echo e($file->temporaryUrl()); ?>" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <div class="w-full h-full flex flex-col items-center justify-center p-2 text-indigo-500 bg-indigo-50/50">
                                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                                <span class="text-[8px] font-bold mt-1 uppercase"><?php echo e($file->getClientOriginalExtension()); ?></span>
                                                            </div>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <button type="button" wire:click="$set('visitFiles.' . <?php echo e($index); ?>, null)" class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 shadow-sm z-20">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php else: ?>
                            <div class="p-10 bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center text-center opacity-60">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m12-3V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586zM9 7V5a3 3 0 016 0v2M9 7h6"/></svg>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Sonuç Girişi Bekleniyor</p>
                                <p class="text-[10px] text-gray-400 mt-2">Ziyaret onaylandıktan sonra bu alan aktif olacaktır.</p>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if(($savedVisit['status'] ?? ($this->iaa->ziyaretPlani->status ?? '')) === 'Revize İsteniyor'): ?>
                            <div class="mt-6 bg-orange-50/50 p-5 rounded-3xl border border-orange-200 shadow-sm relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-orange-400"></div>
                                <h4 class="text-sm font-extrabold text-orange-700 uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Revizyon Gönderim Notu (İsteğe Bağlı)
                                </h4>
                                <p class="text-[11px] text-orange-600 mb-3 leading-relaxed">Ziyaret planınız bir revizyon talebi içeriyor. Taslağı tekrar onaya sunarken, yaptığınız değişiklikler hakkında kısaca bilgi verebilirsiniz. Bu not geçmiş kayıtlara eklenecektir.</p>
                                <div>
                                    <textarea wire:model="plannerRevisionNote" rows="2" class="w-full text-sm border-orange-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 bg-white placeholder-orange-300/70" placeholder="Örn: Tarih ileri alındı, kişi sayısı güncellendi..."></textarea>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['plannerRevisionNote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-[10px]"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div class="bg-gray-50/50 p-5 rounded-3xl border border-gray-100 space-y-4 <?php echo e($isEnteringResults ? 'opacity-75 pointer-events-none' : ''); ?> mt-6">
                            <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-2">Ek Detaylar</h4>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Açıklama / Notlar</label>
                                <textarea wire:model="formData.visit_notes" rows="3" class="w-full text-sm border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ziyaret hakkında kısa not..." <?php echo e($isReadOnly ? 'disabled' : ''); ?>></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Barkod No</label>
                                    <input type="text" wire:model="formData.barcode" class="w-full text-sm border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" <?php echo e($isReadOnly ? 'disabled' : ''); ?>>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Lot No</label>
                                    <input type="text" wire:model="formData.lot_no" class="w-full text-sm border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" <?php echo e($isReadOnly ? 'disabled' : ''); ?>>
                                </div>
                            </div>

                            
                            <div class="pt-2">
                                <label class="block text-xs font-bold text-gray-700 mb-2 italic">Ekli Dosyalar (Fotoğraf, Tablo, Rapor vs.)</label>
                                
                                <div class="space-y-4">
                                    
                                    <!--[if BLOCK]><![endif]--><?php if(($savedVisit && isset($savedVisit['visit_file']) && count($savedVisit['visit_file']) > 0) || ($iaa->ziyaretPlani && $iaa->ziyaretPlani->visit_file && count($iaa->ziyaretPlani->visit_file) > 0)): ?>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner">
                                            <?php
                                                $filesToShow = ($savedVisit && isset($savedVisit['visit_file'])) ? $savedVisit['visit_file'] : ($iaa->ziyaretPlani ? $iaa->ziyaretPlani->visit_file : []);
                                            ?>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filesToShow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                ?>
                                                <div wire:key="saved-file-edit-<?php echo e($index); ?>-<?php echo e(md5($file)); ?>" class="relative group aspect-square bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm shadow-indigo-100/50">
                                                    <!--[if BLOCK]><![endif]--><?php if($isImage): ?>
                                                        <img src="<?php echo e(asset('storage/' . $file)); ?>" class="w-full h-full object-cover transition group-hover:scale-105 duration-300">
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex flex-col items-center justify-center p-2 bg-indigo-50/20">
                                                            <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span class="text-[9px] font-black text-indigo-400 mt-1 uppercase"><?php echo e($extension); ?></span>
                                                        </div>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    
                                                    
                                                    <div class="absolute inset-0 bg-indigo-900/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                                        <a href="<?php echo e(asset('storage/' . $file)); ?>" target="_blank" class="p-1.5 bg-white text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition shadow-lg">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        </a>
                                                        <!--[if BLOCK]><![endif]--><?php if(!$isReadOnly): ?>
                                                            <button type="button" wire:click="deleteVisitFile(<?php echo e($index); ?>)" class="p-1.5 bg-white text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition shadow-lg">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    
                                    <div class="relative" x-data="{ isUploading: false, progress: 0 }">
                                        
                                        <!--[if BLOCK]><![endif]--><?php if(!$isReadOnly): ?>
                                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-white hover:border-indigo-400 transition-all duration-200">
                                                <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                                    <svg class="w-6 h-6 mb-2 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    <p class="text-[11px] text-gray-500"><span class="font-bold">Yeni Dosyalar Ekle</span></p>
                                                    <p class="text-[9px] text-gray-400 mt-0.5">Maks. 10MB per dosya</p>
                                                </div>
                                                <input type="file" multiple class="hidden" wire:key="visit-files-input-field"
                                                    x-on:change="
                                                        const files = $event.target.files;
                                                        if (files.length === 10) return;
                                                        
                                                        isUploading = true;
                                                        progress = 0;
                                                        
                                                        const formData = new FormData();
                                                        for (let i = 0; i < files.length; i++) {
                                                            formData.append('files[]', files[i]);
                                                        }
                                                        formData.append('_token', '<?php echo e(csrf_token()); ?>');

                                                        fetch('<?php echo e(route('ziyaret.dosya.upload')); ?>', {
                                                            method: 'POST',
                                                            body: formData
                                                        })
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            isUploading = false;
                                                            if (data.files) {
                                                                const currentFiles = $wire.get('uploadedTempFiles') || [];
                                                                $wire.set('uploadedTempFiles', [...currentFiles, ...data.files]);
                                                            }
                                                        })
                                                        .catch(error => {
                                                            isUploading = false;
                                                            alert('Yükleme hatası oluştu.');
                                                            console.error(error);
                                                        });
                                                    " />
                                            </label>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        
                                        <!--[if BLOCK]><![endif]--><?php if(!empty($uploadedTempFiles)): ?>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 animate-in fade-in slide-in-from-top-2 duration-300">
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $uploadedTempFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tempFile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div wire:key="temp-ajax-file-<?php echo e($index); ?>" class="relative group aspect-square bg-indigo-50 rounded-xl border-2 border-indigo-200 overflow-hidden shadow-sm">
                                                        <!--[if BLOCK]><![endif]--><?php if($tempFile['isImage']): ?>
                                                            <img src="<?php echo e($tempFile['url']); ?>" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <div class="w-full h-full flex flex-col items-center justify-center p-2">
                                                                <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                                <p class="text-[8px] font-bold text-indigo-500 mt-1 truncate w-full px-2 text-center"><?php echo e($tempFile['name']); ?></p>
                                                            </div>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        
                                                        <button type="button" wire:click.prevent="removeTempUpload(<?php echo e($index); ?>)" class="absolute top-1.5 right-1.5 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 shadow-md transition">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                        
                                                        <div class="absolute inset-x-0 bottom-0 bg-indigo-600/80 backdrop-blur-sm p-1">
                                                            <p class="text-[8px] text-white font-bold text-center">Henüz Kaydedilmedi</p>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        
                                        <div x-show="isUploading" class="absolute inset-0 bg-white/90 z-20 flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-indigo-400">
                                            <div class="w-48 bg-gray-200 rounded-full h-2 mb-2">
                                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                                            </div>
                                            <span class="text-xs font-bold text-indigo-600" x-text="`Dosyalar işleniyor... %${progress}`"></span>
                                        </div>
                                    </div>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['uploadedTempFiles.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-[10px] mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-100 flex justify-end items-center gap-4">
                    <!--[if BLOCK]><![endif]--><?php if(isset($customerData['existing_visit']) || $savedVisit || $isOpen): ?>
                        <button type="button" wire:click="cancelEdit" class="px-8 py-3 bg-white border-2 border-gray-200 text-gray-500 font-bold rounded-2xl hover:bg-gray-50 transition shadow-sm">
                            Vazgeç / Kapat
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                    <!--[if BLOCK]><![endif]--><?php if(!$isReadOnly): ?>
                        <!--[if BLOCK]><![endif]--><?php if($isEnteringResults || ($savedVisit['status'] ?? ($this->iaa->ziyaretPlani->status ?? '')) === 'Onaylandı'): ?>
                            <button type="button" wire:click="completeVisit" wire:loading.attr="disabled" class="px-10 py-3 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 transition shadow-lg hover:shadow-emerald-500/30 flex items-center gap-2">
                                <span wire:loading.remove>Ziyareti Tamamla & Takvime Gönder</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    İşleniyor...
                                </span>
                            </button>
                        <?php else: ?>
                            <button type="submit" wire:loading.attr="disabled" class="px-10 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition shadow-lg hover:shadow-indigo-500/30 flex items-center gap-2">
                                <span wire:loading.remove>Taslağı Kaydet & Onaya Sun</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Kaydediliyor...
                                </span>
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </form>

        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div x-show="showApprove" x-cloak x-transition.opacity class="fixed inset-0 z-[150] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showApprove = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showApprove" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Ziyaret Planını Onayla
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Tahmini Dönüş Tarihi Doldurulmalıdır (*)</label>
                                    <input type="date" wire:model="estimatedReturnDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['estimatedReturnDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    <p class="text-xs text-gray-500 mt-2">Bu ziyaret onaylandığında proje ekibine bildirim gönderilecektir.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex flex-row-reverse gap-3">
                            <button wire:click="approveVisit" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-green-600 text-base font-bold text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition">
                                Onayla
                            </button>
                            <button @click="showApprove = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                İptal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div x-show="showReject" x-cloak x-transition.opacity class="fixed inset-0 z-[150] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showReject = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showReject" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Ziyaret Planını Reddet
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Reddetme Gerekçesi (*)</label>
                                    <textarea wire:model="rejectionReason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="Plan neden reddedildi?"></textarea>
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
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex flex-row-reverse gap-3">
                            <button wire:click="rejectVisit" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition">
                                Reddet
                            </button>
                            <button @click="showReject = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                İptal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div x-show="showRevision" x-cloak x-transition.opacity class="fixed inset-0 z-[150] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showRevision = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showRevision" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Planı Revizyon İçin Geri Gönder
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Değiştirilmesi İstenenler (*)</label>
                                    <textarea wire:model="revisionReason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" placeholder="Ekibe hangi değişikliklerin yapılmasını istediğinizi açıklayın..."></textarea>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['revisionReason'];
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
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex flex-row-reverse gap-3">
                            <button wire:click="requestRevision" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-orange-600 text-base font-bold text-white hover:bg-orange-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition">
                                Taslağı Geri Gönder
                            </button>
                            <button @click="showRevision = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                İptal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="showCancel" x-cloak x-transition.opacity class="fixed inset-0 z-[150] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showCancel = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showCancel" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Ziyaret Planını İptal Et
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">İptal Gerekçesi (*)</label>
                                    <textarea wire:model="cancelReason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-rose-500 focus:border-rose-500 sm:text-sm" placeholder="Planlanan bu ziyaret neden iptal ediliyor? Açıklayın..."></textarea>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['cancelReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    <p class="text-[11px] text-gray-500 mt-2">İptal edildiğinde ilgili kişilere bilgilendirme yapılacaktır.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex flex-row-reverse gap-3">
                            <button wire:click="cancelVisit" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-rose-600 text-base font-bold text-white hover:bg-rose-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition">
                                Ziyareti İptal Et
                            </button>
                            <button @click="showCancel = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                Vazgeç
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="showSubmitRevision" x-cloak x-transition.opacity class="fixed inset-0 z-[150] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showSubmitRevision = false; $wire.closeRevisionSubmitModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showSubmitRevision" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2" id="modal-title">
                                    Revizyon Sonrası Gönderim
                                </h3>
                                <div class="mt-2 text-[13px] text-gray-500 mb-4 leading-relaxed">
                                    Mevcut plan revizyon talebi içeriyor. Taslağı tekrar onaya sunmadan önce, yaptığınız değişiklikler hakkında kısaca bilgi verebilirsiniz. Bu bilgi onaylayacak kişiye gösterilecektir.
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Yapılan Değişiklikler (Opsiyonel)</label>
                                    <textarea wire:model="plannerRevisionNote" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Örn: Ziyaret tarihi güncellendi ve katılımcı eklendi..."></textarea>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['plannerRevisionNote'];
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
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex flex-row-reverse gap-3 w-full sm:w-auto">
                            <button wire:click="submitRevisedVisit" wire:loading.attr="disabled" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition items-center">
                                <svg wire:loading wire:target="submitRevisedVisit" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span wire:loading.remove wire:target="submitRevisedVisit">Kaydet ve Onaya Sun</span>
                                <span wire:loading wire:target="submitRevisedVisit">Kaydediliyor...</span>
                            </button>
                            <button @click="showSubmitRevision = false; $wire.closeRevisionSubmitModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                İptal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div x-show="showUpdateReturnDate" x-cloak x-transition.opacity class="fixed inset-0 z-[150] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showUpdateReturnDate = false; $wire.closeUpdateReturnDateModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showUpdateReturnDate" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2" id="modal-title">
                                    Dönüş Tarihini Güncelle
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Tahmini Dönüş Tarihi</label>
                                    <input type="date" wire:model="newReturnDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newReturnDate'];
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
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex flex-row-reverse gap-3 w-full sm:w-auto">
                            <button wire:click="updateReturnDate" wire:loading.attr="disabled" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition items-center">
                                <svg wire:loading wire:target="updateReturnDate" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span wire:loading.remove wire:target="updateReturnDate">Güncelle</span>
                                <span wire:loading wire:target="updateReturnDate">Güncelleniyor...</span>
                            </button>
                            <button @click="showUpdateReturnDate = false; $wire.closeUpdateReturnDateModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                İptal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div x-show="showReturnDateRevision" x-cloak x-transition.opacity class="fixed inset-0 z-[150] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showReturnDateRevision = false; $wire.set('showReturnDateRevisionModal', false)" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showReturnDateRevision" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2" id="modal-title">
                                    Dönüş Tarihi Revizyon Talebi
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Talep Edilen Yeni Tarih</label>
                                    <input type="date" wire:model="requestedReturnDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['requestedReturnDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Revizyon Gerekçesi</label>
                                    <textarea wire:model="returnDateRevisionReason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Gerekçenizi detaylı şekilde yazınız..."></textarea>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['returnDateRevisionReason'];
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
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex flex-row-reverse gap-3 w-full sm:w-auto">
                            <button wire:click="requestReturnDateRevision" wire:loading.attr="disabled" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-orange-600 text-base font-bold text-white hover:bg-orange-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition items-center">
                                <svg wire:loading wire:target="requestReturnDateRevision" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span wire:loading.remove wire:target="requestReturnDateRevision">Talebi İlet</span>
                                <span wire:loading wire:target="requestReturnDateRevision">İletiliyor...</span>
                            </button>
                            <button @click="showReturnDateRevision = false; $wire.set('showReturnDateRevisionModal', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                İptal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="showReturnDateRevisionResponse" x-cloak x-transition.opacity class="fixed inset-0 z-[150] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showReturnDateRevisionResponse = false; $wire.set('showReturnDateRevisionResponseModal', false)" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showReturnDateRevisionResponse" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="$wire.returnDateRevisionAction == 'approve' ? 'bg-green-100' : 'bg-red-100'">
                                <svg x-show="$wire.returnDateRevisionAction == 'approve'" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <svg x-show="$wire.returnDateRevisionAction == 'reject'" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2" id="modal-title" x-text="$wire.returnDateRevisionAction == 'approve' ? 'Talebi Onayla' : 'Talebi Reddet'">
                                </h3>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1" x-text="$wire.returnDateRevisionAction == 'approve' ? 'Açıklama / Not (Opsiyonel)' : 'Reddetme Gerekçesi (Zorunlu)'"></label>
                                    <textarea wire:model="returnDateRevisionResponseNote" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="..."></textarea>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['returnDateRevisionResponseNote'];
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
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex flex-row-reverse gap-3 w-full sm:w-auto">
                            <button wire:click="respondToReturnDateRevision" wire:loading.attr="disabled" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 text-base font-bold text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition items-center" :class="$wire.returnDateRevisionAction == 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'">
                                <svg wire:loading wire:target="respondToReturnDateRevision" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span wire:loading.remove wire:target="respondToReturnDateRevision" x-text="$wire.returnDateRevisionAction == 'approve' ? 'Onayla' : 'Reddet'"></span>
                                <span wire:loading wire:target="respondToReturnDateRevision">İşleniyor...</span>
                            </button>
                            <button @click="showReturnDateRevisionResponse = false; $wire.set('showReturnDateRevisionResponseModal', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                İptal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/plan-visit.blade.php ENDPATH**/ ?>