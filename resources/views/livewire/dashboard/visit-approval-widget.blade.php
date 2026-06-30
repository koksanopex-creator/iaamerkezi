<div class="mt-8 space-y-6">
    @php
        $actualPending = $pendingVisits->where('status', 'Beklemede');
        $handledVisits = $pendingVisits->whereIn('status', ['Onaylandı', 'Reddedildi', 'Revize İsteniyor']);
    @endphp

    {{-- BEKLEYENLER BÖLÜMÜ --}}
    @if($actualPending->count() > 0)
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-4 text-white shadow-lg mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h5 class="font-bold text-lg">Ziyaret Onayı Bekleyenler</h5>
                    <p class="text-blue-100 text-xs">Bu bölüme ait {{ $actualPending->count() }} adet ziyaret planı onayınızı bekliyor.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($actualPending as $visit)
                <div class="bg-white border border-blue-100 rounded-xl p-4 shadow-sm hover:shadow-md transition group overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-2 opacity-5 group-hover:opacity-10 transition">
                        <svg class="w-16 h-16 text-indigo-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                    </div>

                    <div class="flex flex-col h-full justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider border border-blue-100 mb-1 inline-block">Müşteri Ziyareti</span>
                                    <h6 class="font-bold text-gray-900 leading-tight line-clamp-2">
                                        {{ $visit->iaa->baslik }}
                                    </h6>
                                </div>
                                <div class="text-right flex-shrink-0 ml-2">
                                    <span class="text-[10px] font-bold text-gray-400 block uppercase">Ziyaret Tarihi</span>
                                    <span class="text-xs font-black text-indigo-600">{{ $visit->visit_date ? $visit->visit_date->format('d.m.Y H:i') : '-' }}</span>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-tighter">Müşteri:</span>
                                    <span class="text-[11px] text-gray-800 font-black">{{ $visit->iaa->musteriSikayeti->customer->name ?? 'Bilinmeyen' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-tighter">Planlayan:</span>
                                    <span class="text-[11px] text-gray-800 font-black">{{ $visit->planner->name ?? 'Bilinmeyen' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-tighter">Ziyaretçi:</span>
                                    <span class="text-[11px] text-gray-800 font-black">{{ $visit->visitor->name ?? $visit->visitor_name ?? 'Bilinmeyen' }}</span>
                                </div>
                                <div class="pt-1 border-t border-gray-200">
                                    <p class="text-[10px] text-gray-500 line-clamp-2 italic font-medium">"{{ $visit->visit_reason }}"</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <button wire:click="openApproveModal({{ $visit->id }})" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-[11px] font-bold py-2 rounded-lg shadow-sm transition flex items-center justify-center gap-1 group/btn">
                                <svg class="w-3 h-3 group-hover/btn:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Onayla
                            </button>
                            <button wire:click="openRejectModal({{ $visit->id }}, 'revision')" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold py-2 rounded-lg shadow-sm transition flex items-center justify-center gap-1 group/btn">
                                <svg class="w-3 h-3 group-hover/btn:rotate-45 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Revize
                            </button>
                            <button wire:click="openRejectModal({{ $visit->id }}, 'reject')" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold py-2 rounded-lg shadow-sm transition flex items-center justify-center gap-1 group/btn">
                                <svg class="w-3 h-3 group-hover/btn:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Reddet
                            </button>
                            <a href="{{ route('proje.workspace.show', $visit->iaa_id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 py-2 px-3 rounded-lg transition" title="İncele">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- İŞLEM GÖRENLER BÖLÜMÜ --}}
    @if($handledVisits->count() > 0)
        <div class="pt-4 border-t border-gray-100">
            <h5 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Son İşlem Gördüğüm Ziyaretler ({{ $handledVisits->count() }})
            </h5>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($handledVisits as $visit)
                    @php
                        $statusMeta = [
                            'Onaylandı' => ['color' => 'green', 'label' => 'Onaylandı', 'icon' => 'check'],
                            'Reddedildi' => ['color' => 'red', 'label' => 'Reddedildi', 'icon' => 'x'],
                            'Revize İsteniyor' => ['color' => 'amber', 'label' => 'Revize İstendi', 'icon' => 'refresh'],
                        ];
                        $meta = $statusMeta[$visit->status] ?? ['color' => 'gray', 'label' => $visit->status, 'icon' => 'info'];
                    @endphp
                    <div class="bg-gray-50/50 border border-gray-100 rounded-xl p-3 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-0.5 rounded bg-{{ $meta['color'] }}-100 text-{{ $meta['color'] }}-700 text-[9px] font-bold uppercase tracking-wider border border-{{ $meta['color'] }}-200 flex items-center gap-1">
                                    {{ $meta['label'] }}
                                </span>
                                <span class="text-[9px] font-bold text-gray-400">{{ $visit->updated_at->format('d.m H:i') }}</span>
                            </div>
                            <div class="text-[11px] font-bold text-gray-800 line-clamp-1 mb-1">{{ $visit->iaa->baslik }}</div>
                            <div class="text-[10px] text-gray-500 line-clamp-1">{{ $visit->iaa->musteriSikayeti->customer->name ?? 'Müşteri' }}</div>
                        </div>
                        <div class="flex items-center gap-2 mt-3 pt-2 border-t border-gray-100/50">
                            <button wire:click="undoAction({{ $visit->id }})" class="flex-1 bg-white text-gray-600 hover:text-red-600 border border-gray-200 hover:border-red-100 py-1.5 rounded-lg text-[10px] font-bold transition flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                Geri Al
                            </button>
                            <a href="{{ route('proje.workspace.show', $visit->iaa_id) }}" class="p-1.5 text-gray-400 hover:text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Approve Modal --}}
    @if($showApproveModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showApproveModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Ziyareti Onayla</h3>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 italic mb-4">Ziyaret sonrası beklenen tahmini dönüş tarihini belirleyerek onayı tamamlayın.</p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-tight mb-1">Tahmini Dönüş Tarihi</label>
                                        <input type="date" wire:model="estimatedReturnDate" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                                        @error('estimatedReturnDate') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="approveVisit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-sm font-bold text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto">Onayla</button>
                    <button type="button" wire:click="$set('showApproveModal', false)" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Reject Modal --}}
    @if($showRejectModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showRejectModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-{{ $actionType === 'revision' ? 'amber' : 'red' }}-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-{{ $actionType === 'revision' ? 'amber' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($actionType === 'revision')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                @endif
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                {{ $actionType === 'revision' ? 'Revizyon İste' : 'Ziyareti Reddet' }}
                            </h3>
                            <div class="mt-2 text-left">
                                <p class="text-[11px] text-gray-500 italic mb-4">Lütfen işlem sebebinizi ilgili personele bildirmek üzere kısaca açıklayın.</p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-tight mb-1">Açıklama / Sebep</label>
                                        <textarea wire:model="rejectionReason" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-{{ $actionType === 'revision' ? 'amber' : 'red' }}-500 focus:border-{{ $actionType === 'revision' ? 'amber' : 'red' }}-500 text-sm" placeholder="Buraya yazın..."></textarea>
                                        @error('rejectionReason') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="processRejectOrRevision" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-{{ $actionType === 'revision' ? 'amber' : 'red' }}-600 text-sm font-bold text-white hover:bg-{{ $actionType === 'revision' ? 'amber' : 'red' }}-700 focus:outline-none sm:ml-3 sm:w-auto">Gönder</button>
                    <button type="button" wire:click="$set('showRejectModal', false)" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto">Vazgeç</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
