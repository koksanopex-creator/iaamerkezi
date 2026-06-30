<div class="space-y-8 animate-fade-in" wire:poll.3s id="toplanti-odasi-container">
    <!-- TOP BAR: DURUM VE SAYAÇ -->
    <div class="bg-white/70 backdrop-blur-2xl border border-white/50 rounded-[2.5rem] p-6 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/5 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                @php
                    $percent = 100;
                    $remaining = $this->remainingTime;
                    if ($toplanti->baslatilma_at && $remaining !== null) {
                        $percent = ($remaining / ($toplanti->planlanan_sure_dk * 60)) * 100;
                    }
                    $isUrgent = ($remaining !== null && $remaining <= 300 && $remaining > 0);
                    $isFinished = $toplanti->durum === 'tamamlandı';
                @endphp
                <div class="w-20 h-20 rounded-full border-4 border-gray-100 flex items-center justify-center relative overflow-hidden {{ $isUrgent ? 'animate-pulse border-rose-500/30' : '' }} {{ $isFinished ? 'bg-emerald-50 border-emerald-500/30' : '' }}">
                    <div class="text-center">
                        <span class="block text-[10px] font-black {{ $isFinished ? 'text-emerald-600' : 'text-gray-400' }} uppercase tracking-tighter">{{ $isFinished ? 'BİTTİ' : 'KALAN' }}</span>
                        <span class="text-lg font-black {{ $isUrgent ? 'text-rose-600' : ($isFinished ? 'text-emerald-700' : 'text-indigo-600') }}">
                            {{ $remaining !== null ? gmdate("i:s", $remaining) : '--:--' }}
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h3 class="text-xl font-black text-gray-800 tracking-tighter uppercase">{{ $toplanti->baslik }}</h3>
                    
                    {{-- Mail İletildi Simgesi (Hukuk Admin ve Superadmin için) --}}
                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Hukuk Admini']))
                        @if($toplanti->davet_maili_gonderildi)
                            <span class="flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-500 rounded-lg text-[9px] font-black ring-1 ring-blue-100" title="Davet mailleri gönderildi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                MAİLLER İLETİLDİ
                            </span>
                        @else
                            <span class="flex items-center gap-1 px-2 py-0.5 bg-rose-50 text-rose-500 rounded-lg text-[9px] font-black ring-1 ring-rose-100" title="Davet mailleri henüz gönderilmedi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                MAİLLER GÖNDERİLMEDİ
                            </span>
                        @endif
                    @endif
                </div>
                <div class="flex items-center gap-3 mt-1">
                    <span class="px-3 py-1 {{ $isFinished ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-50 text-indigo-600' }} text-[9px] font-black tracking-widest rounded-lg ring-1 ring-black/5 uppercase">
                        {{ $toplanti->durum }}
                    </span>
                    <span class="text-xs font-bold text-gray-400 italic">{{ $toplanti->yer }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-6">
            @if($canManage && !$isFinished && $toplanti->durum !== 'iptal')
                <!-- MODERATÖR ARAÇ PANELİ -->
                <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mr-2">Araçlar:</p>
                    <button wire:click="toggleWidget('pano')" class="p-2 rounded-xl transition-all {{ in_array('pano', $activeWidgets) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-gray-400 border border-gray-100' }}" title="Pano">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </button>
                    <button wire:click="toggleWidget('oylama')" class="p-2 rounded-xl transition-all {{ in_array('oylama', $activeWidgets) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-gray-400 border border-gray-100' }}" title="Oylama">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                    <div class="w-px h-6 bg-gray-200 mx-1"></div>
                </div>
            @endif

            <div class="flex items-center gap-3">
                @if($toplanti->durum !== 'tamamlandı' && $toplanti->durum !== 'iptal')
                    @if($canManage)
                        @if(!$toplanti->baslatilma_at)
                            <button wire:click="startMeeting" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-emerald-500/20 transition-all active:scale-95">
                                Toplantıyı Başlat
                            </button>
                        @else
                            <button onclick="confirm('Toplantıyı sonlandırmak istediğinize emin misiniz? Alınan kararlar kesinleşecektir.') || event.stopImmediatePropagation()" wire:click="endMeeting" class="px-8 py-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-rose-500/20 transition-all active:scale-95">
                                Toplantıyı Sonlandır
                            </button>
                        @endif
                        
                        <div class="flex gap-2">
                            <button @click="$dispatch('open-modal', 'ertele-modal')" class="p-3 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition-all ring-1 ring-amber-500/20" title="Ertele">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <button @click="$dispatch('open-modal', 'iptal-modal')" class="p-3 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all ring-1 ring-rose-500/20" title="İptal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @else
                        <span class="px-4 py-2 bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-xl">Katılımcı Modu</span>
                    @endif
                @else
                    <span class="px-6 py-2 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-lg shadow-emerald-500/20 shadow-inner">
                        TOPLANTI TAMAMLANDI (OKUMA MODU)
                    </span>
                @endif

                {{-- [TAŞINDI] SUNUM MODU BUTONU (HER YETKİLİ İÇİN GÖRÜNÜR) --}}
                @if(Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini', 'Hukuk Yöneticisi']))
                    <button wire:click="togglePresentationMode" 
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl transition-all {{ $presentationMode ? 'bg-amber-600 text-white shadow-xl shadow-amber-500/30 ring-2 ring-amber-300 scale-105' : 'bg-white text-amber-600 border border-amber-200 hover:bg-amber-50 shadow-sm' }}" 
                        title="{{ $presentationMode ? 'Sunum Modunu Kapat' : 'Sunum Modunu Aç' }}">
                        @if($presentationMode)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest">SUNUM MODU: AKTİF</span>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest">SUNUM MODU</span>
                        @endif
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- GÜNDEMDEKİ DİSİPLİN DOSYALARI --}}
    <div class="bg-white/70 backdrop-blur-2xl border border-white/50 rounded-[2.5rem] p-8 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/5">
        <div class="flex items-center justify-between mb-8">
            <h4 class="text-gray-800 font-black flex items-center gap-3 uppercase tracking-tighter">
                <div class="p-2 bg-rose-600 text-white rounded-xl shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                Gündemdeki Disiplin Dosyaları
            </h4>
            <span class="px-4 py-1.5 bg-gray-100 text-gray-500 text-[10px] font-black rounded-lg uppercase tracking-widest">{{ $toplanti->disiplinDosyalari->count() }} DOSYA</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($toplanti->disiplinDosyalari as $dosya)
                <div class="bg-gray-50/50 rounded-3xl p-6 border {{ ($dosya->oylama_aktif && $dosya->durum !== 'Karar Verildi') ? 'border-rose-500 ring-2 ring-rose-500/20 animate-pulse' : 'border-gray-100' }} shadow-sm hover:shadow-md transition-all group relative">
                    @if($dosya->oylama_aktif && $dosya->durum !== 'Karar Verildi')
                        <div class="absolute -top-3 -right-3 flex items-center gap-2 bg-rose-600 text-white px-3 py-1 rounded-full shadow-lg z-10 ring-4 ring-white">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                            </span>
                            <span class="text-[9px] font-black uppercase tracking-widest">CANLI OYLAMA</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-4 mb-4">
                        <img src="{{ $dosya->user->profile_photo_url }}" class="w-12 h-12 rounded-2xl object-cover shadow-sm ring-2 ring-white">
                        <div class="flex-1 min-w-0">
                            <h5 class="text-sm font-black text-gray-800 truncate">{{ $dosya->user->name }}</h5>
                            <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ $dosya->user->bolum->ad ?? '-' }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            @php
                                $dosyaStatusColor = match($dosya->durum) {
                                    'Karar Verildi' => 'bg-emerald-50 text-emerald-600 ring-emerald-500/20',
                                    'Kurulda' => 'bg-indigo-50 text-indigo-600 ring-indigo-500/20',
                                    'Savunma Bekleniyor' => 'bg-amber-50 text-amber-600 ring-amber-500/20',
                                    'İptal' => 'bg-rose-50 text-rose-600 ring-rose-500/20',
                                    default => 'bg-gray-50 text-gray-600 ring-gray-500/20'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-tighter ring-1 {{ $dosyaStatusColor }}">
                                {{ $dosya->durum }}
                            </span>
                            <span class="text-[9px] font-mono text-gray-400">#{{ $dosya->id }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full"></span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Suç:</span>
                            <span class="text-[10px] font-black text-gray-700 truncate">{{ $dosya->behavior->tanim ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-rose-400 rounded-full"></span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Puan:</span>
                            <span class="text-[10px] font-black text-rose-600">{{ $dosya->hesaplanan_puan }} PUAN</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button @click="$dispatch('open-modal', 'modal-vote-{{ $dosya->id }}')" class="flex-1 py-2.5 bg-{{ $dosya->durum === 'Karar Verildi' ? 'emerald-600' : 'indigo-600' }} hover:bg-black text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-500/10">
                            {{ $dosya->durum === 'Karar Verildi' ? 'Kararı Gör' : 'İncele ve Oyla' }}
                        </button>
                    </div>
                    </div>
                @endforeach
            </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <div class="lg:col-span-3 space-y-8">
            @if(in_array('pano', $activeWidgets) || ($isFinished && !empty($panoIcerik)))
                <div class="bg-white/70 backdrop-blur-2xl border border-white/50 rounded-[3rem] p-10 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/5 min-h-[400px] flex flex-col">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-6 mb-8">
                        <h4 class="text-gray-800 font-black flex items-center gap-3 uppercase tracking-tighter">
                            <div class="p-2 bg-indigo-600 text-white rounded-xl shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            </div>
                            Beyin Fırtınası Panosu
                        </h4>
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest italic animate-pulse">EŞ ZAMANLI SENKRONİZE EDİLİYOR...</span>
                    </div>
                    <div class="flex-1">
                        <textarea wire:model.live.debounce.1000ms="panoIcerik" 
                            {{ (!$canManage || $isFinished) ? 'readonly' : '' }}
                            class="w-full h-full min-h-[300px] bg-gray-50/50 border-none ring-2 ring-gray-100 focus:ring-4 focus:ring-indigo-500 text-gray-800 rounded-[2rem] py-8 px-8 font-medium shadow-inner transition-all placeholder:text-gray-300 resize-none" placeholder="Tüm katılımcıların ekranına anlık yansıyacak notları ve fikirleri buraya yazın..."></textarea>
                    </div>
                </div>
            @endif

            <!-- TOPLANTI KARARLARI VE NOTLARI (MADDELİ) -->
            <div class="bg-white/70 backdrop-blur-2xl border border-white/50 rounded-[3rem] p-10 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/5 flex flex-col">
                <div class="flex items-center justify-between border-b border-gray-100 pb-6 mb-8">
                    <h4 class="text-gray-800 font-black flex items-center gap-3 uppercase tracking-tighter">
                        <div class="p-2 bg-emerald-600 text-white rounded-xl shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        Alınan Kararlar & Toplantı Çıktıları
                    </h4>
                    @if($canManage && !$isFinished)
                        <button wire:click="saveResolution" wire:loading.attr="disabled" class="px-6 py-2 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-black transition-all disabled:opacity-50">Kaydet (Genel)</button>
                    @endif
                </div>

                <div class="space-y-8">
                    <!-- MADDE EKLEME (SADECE MODERATOR) -->
                    @if($canManage && !$isFinished)
                        <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 shadow-inner space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <textarea wire:model="yeniMadde" class="w-full bg-white border-gray-200 rounded-2xl text-xs font-medium py-3 px-4 focus:ring-emerald-500" placeholder="Karar maddesi metnini yazın..." rows="2"></textarea>
                                </div>
                                <div class="space-y-4">
                                    <select wire:model="yeniMaddeSorumlu" class="w-full bg-white border-gray-200 rounded-xl text-xs font-bold text-gray-500">
                                        <option value="">Sorumlu Seç (Opsiyonel)</option>
                                        @foreach($toplanti->katilimcilar as $kat)
                                            @if($kat->user)
                                                <option value="{{ $kat->user_id }}">{{ $kat->user->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button wire:click="addDecisionItem" class="w-full py-3 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-black transition-all shadow-lg shadow-emerald-500/10">Maddeyi Ekle</button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- KARAR MADDELERİ LİSTESİ -->
                    <div class="space-y-4">
                        @foreach($toplanti->kararMaddeleri as $madde)
                            <div class="flex items-start gap-4 p-5 bg-white rounded-3xl border border-gray-100 shadow-sm relative group">
                                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl font-bold text-xs">{{ $loop->iteration }}</div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-800 leading-relaxed">{{ $madde->madde_metni }}</p>
                                    @if($madde->sorumlu)
                                        <div class="mt-2 flex items-center gap-2">
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Sorumlu:</span>
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[9px] font-black rounded-lg">{{ $madde->sorumlu->name }}</span>
                                        </div>
                                    @endif
                                </div>
                                @if($canManage && !$isFinished)
                                    <button wire:click="deleteDecisionItem({{ $madde->id }})" class="p-2 text-gray-300 hover:text-rose-600 transition-all opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @endif
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 flex flex-col items-end gap-1">
                                    <span class="text-[8px] font-black uppercase tracking-widest {{ $madde->durum === 'tamamlandı' ? 'text-emerald-500' : 'text-amber-500' }}">{{ $madde->durum }}</span>
                                </div>
                            </div>
                        @endforeach
                        @if($toplanti->kararMaddeleri->isEmpty())
                            <div class="py-12 text-center bg-gray-50/50 rounded-[2rem] border border-dashed border-gray-200">
                                <p class="text-xs font-bold text-gray-400">Henüz eklenmiş bir karar maddesi bulunmuyor.</p>
                            </div>
                        @endif
                    </div>

                    <!-- GENEL KARAR VE DOSYA (OPSİYONEL) -->
                    <div class="border-t border-gray-100 pt-8">
                        <textarea wire:model="toplantiKarari" 
                            {{ (!$canManage || $isFinished) ? 'readonly' : '' }}
                            class="w-full min-h-[150px] bg-gray-50/30 border-none ring-2 ring-gray-100 focus:ring-4 focus:ring-emerald-500 text-gray-800 rounded-[2rem] py-8 px-8 font-medium shadow-inner transition-all placeholder:text-gray-400 resize-none mb-6" placeholder="Özet karar veya genel toplantı notlarını buraya ekleyin..."></textarea>
                        
                        <div class="flex flex-wrap items-center justify-between gap-4 p-6 bg-gray-50/50 rounded-[2rem] border border-gray-100 shadow-inner">
                            <div class="flex-1 min-w-[200px]">
                                @if($canManage && !$isFinished)
                                    <label class="block">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 block ml-1">Karar Tutanağı / Ek Dosya</span>
                                        <input type="file" wire:model="kararDosya" class="block w-full text-[10px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all cursor-pointer"/>
                                    </label>
                                @endif
                            </div>

                            @if($toplanti->karar_dosya_yolu)
                                <div class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-sm ring-1 ring-gray-100 transition-all hover:shadow-md">
                                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest"> Ekli Karar Belgesi</span>
                                        <a href="{{ Storage::url($toplanti->karar_dosya_yolu) }}" target="_blank" class="text-xs font-black text-indigo-600 hover:text-black transition-all underline decoration-2 underline-offset-4">Dosyayı Görüntüle</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- KATILIMCILAR LİSTESİ & YOKLAMA -->
            <div class="bg-white/70 backdrop-blur-2xl border border-white/50 rounded-[3rem] p-10 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/5">
                <h4 class="text-gray-800 font-black flex items-center gap-3 uppercase tracking-tighter border-b border-gray-50 pb-6 mb-6">
                    <div class="p-2 bg-indigo-500 text-white rounded-xl shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    Katılımcı Durumları (Yoklama)
                </h4>

                @php
                    // Mükerrer kayıtları temizle ve durumları birleştir (Katıldı öncelikli)
                    $katilimcilar = $toplanti->katilimcilar->groupBy(function($item) {
                        return $item->user_id ?: 'ext_' . $item->id;
                    })->map(function($group) {
                        if ($group->count() > 1) {
                            return $group->sortByDesc(function($k) {
                                return $k->katilim_durumu === 'katildi' ? 2 : ($k->katilim_durumu === 'katilmadi' ? 1 : 0);
                            })->first();
                        }
                        return $group->first();
                    });
                    
                    // 1. Kurul Heyeti (Rolü başkan veya üye olanlar)
                    $kurulHeyeti = $katilimcilar->filter(function($k) {
                        return $k->user && $k->user->hasAnyRole(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Superadmin', 'Hukuk Admini']);
                    });

                    // 2. Dosya Bazlı Katılımcılar
                    $digerleri = $katilimcilar->reject(fn($k) => $kurulHeyeti->contains('id', $k->id));
                    $dosyaKatilimcilari = [];
                    
                    foreach($toplanti->disiplinDosyalari as $case) {
                        $ilgiliIds = [$case->user_id, $case->reporter_id];
                        $buDosyaKatilimcilari = $digerleri->filter(fn($k) => in_array($k->user_id, $ilgiliIds));
                        if($buDosyaKatilimcilari->isNotEmpty()) {
                            $dosyaKatilimcilari[$case->id] = [
                                'case' => $case,
                                'katilimcilar' => $buDosyaKatilimcilari
                            ];
                            // Atananları listeden çıkar
                            $digerleri = $digerleri->reject(fn($k) => $buDosyaKatilimcilari->contains('id', $k->id));
                        }
                    }

                    // 3. Geri Kalanlar (Şahitler, Misafirler vb.)
                    $genelKatilimcilar = $digerleri;
                @endphp

                <div class="space-y-10">
                    {{-- GRUP: KURUL HEYETİ --}}
                    @if($kurulHeyeti->isNotEmpty())
                        <div class="space-y-4">
                            <h5 class="flex items-center gap-2 text-[10px] font-black text-indigo-500 uppercase tracking-[0.3em] bg-indigo-50/50 w-fit px-4 py-1.5 rounded-full ring-1 ring-indigo-100">
                                <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
                                Kurul Heyeti
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($kurulHeyeti as $kat)
                                    @include('livewire.admin.disiplin.partials.katilimci-card', ['kat' => $kat])
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- GRUP: DOSYA BAZLI --}}
                    @foreach($dosyaKatilimcilari as $data)
                        <div class="space-y-4">
                            <h5 class="flex items-center gap-2 text-[10px] font-black text-emerald-600 uppercase tracking-[0.3em] bg-emerald-50/50 w-fit px-4 py-1.5 rounded-full ring-1 ring-emerald-100">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                Dosya #{{ $data['case']->id }} - {{ $data['case']->user->name }}
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($data['katilimcilar'] as $kat)
                                    @include('livewire.admin.disiplin.partials.katilimci-card', ['kat' => $kat])
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- GRUP: DİĞER --}}
                    @if($genelKatilimcilar->isNotEmpty())
                        <div class="space-y-4">
                            <h5 class="flex items-center gap-2 text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] bg-gray-50 w-fit px-4 py-1.5 rounded-full ring-1 ring-gray-100">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                Diğer Davetliler / Şahitler
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($genelKatilimcilar as $kat)
                                    @include('livewire.admin.disiplin.partials.katilimci-card', ['kat' => $kat])
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- AKSİYONLAR LİSTESİ -->
            @if(in_array('aksiyon', $activeWidgets) || ($isFinished && $toplanti->aksiyonlar->isNotEmpty()))
                <div class="bg-white/70 backdrop-blur-2xl border border-white/50 rounded-[3rem] p-10 shadow-2xl shadow-indigo-500/5 ring-1 ring-black/5">
                    <h4 class="text-gray-800 font-black flex items-center gap-3 uppercase tracking-tighter border-b border-gray-50 pb-6 mb-6">
                        <div class="p-2 bg-emerald-500 text-white rounded-xl shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        Toplantı Aksiyonları
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($toplanti->aksiyonlar as $aksiyon)
                            <div class="p-6 bg-gray-50/50 rounded-[2rem] border border-gray-100 shadow-sm group">
                                <div class="flex items-center gap-3 mb-3">
                                    <img src="{{ $aksiyon->user->profile_photo_url }}" class="w-8 h-8 rounded-full">
                                    <span class="text-[10px] font-black text-gray-800 truncate">{{ $aksiyon->user->name }}</span>
                                </div>
                                <p class="text-xs text-gray-600 font-medium leading-relaxed">{{ $aksiyon->icerik }}</p>
                                <div class="mt-4 pt-4 border-t border-gray-200/50 flex justify-between items-center">
                                    <span class="text-[8px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">{{ $aksiyon->durum }}</span>
                                    <span class="text-[8px] text-gray-400 font-bold">{{ $aksiyon->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                        @if($toplanti->aksiyonlar->isEmpty())
                            <div class="col-span-full py-10 text-center text-gray-300 italic text-sm">Henüz bir aksiyon atanmadı.</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- SAĞ: OYLAMA, KATILIMCILAR VE GİZLİ NOTLAR -->
        <div class="space-y-8">
            
            @if(in_array('oylama', $activeWidgets) || ($isFinished && $toplanti->oylamalar->isNotEmpty()))
                <!-- CANLI OYLAMA WIDGET -->
                <div class="bg-indigo-600 rounded-[2.5rem] p-8 shadow-2xl shadow-indigo-500/30 text-white relative overflow-hidden">
                    <div class="relative z-10 space-y-6">
                        <h4 class="font-black text-lg uppercase tracking-tighter flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Canlı Oylama
                        </h4>
                        
                        @php $aktifOylama = $toplanti->oylamalar->where('aktif', true)->first(); @endphp
                        
                        @if($aktifOylama)
                            <div class="space-y-4">
                                <div class="bg-white/10 p-4 rounded-2xl border border-white/20">
                                    <p class="text-sm font-bold leading-relaxed">{{ $aktifOylama->konu }}</p>
                                </div>
                                
                                @if(!$isFinished)
                                    <div class="grid grid-cols-3 gap-2">
                                        <button wire:click="castVote({{ $aktifOylama->id }}, 'ceza_verilsin')" class="p-3 bg-rose-500/20 hover:bg-rose-700 transition-colors rounded-xl border border-white/20 text-[10px] font-black uppercase tracking-tighter">CEZA VERİLSİN</button>
                                        <button wire:click="castVote({{ $aktifOylama->id }}, 'ceza_verilmesin')" class="p-3 bg-emerald-500/20 hover:bg-emerald-500 transition-colors rounded-xl border border-white/20 text-[10px] font-black uppercase tracking-tighter">CEZA VERİLMESİN</button>
                                        <button wire:click="castVote({{ $aktifOylama->id }}, 'cekimser')" class="p-3 bg-gray-500/20 hover:bg-gray-500 transition-colors rounded-xl border border-white/20 text-[10px] font-black uppercase tracking-tighter">ÇEKİMSER</button>
                                    </div>
                                @endif

                                <div class="space-y-2 pt-4">
                                    @php
                                        $toplamOy = $aktifOylama->oylar->count();
                                        $cezaVerilsin = $aktifOylama->oylar->whereIn('oy', ['ceza_verilsin', 'aleyhte'])->count();
                                        $cezaVerilmesin = $aktifOylama->oylar->whereIn('oy', ['ceza_verilmesin', 'lehte'])->count();
                                        $pVerilsin = $toplamOy > 0 ? ($cezaVerilsin / $toplamOy) * 100 : 0;
                                        $pVerilmesin = $toplamOy > 0 ? ($cezaVerilmesin / $toplamOy) * 100 : 0;
                                    @endphp
                                    <div class="flex justify-between text-[10px] font-black uppercase">
                                        <span>Sonuçlar</span>
                                        <span>{{ $toplamOy }} Oy</span>
                                    </div>
                                    <div class="h-2 bg-white/10 rounded-full overflow-hidden flex">
                                        <div class="h-full bg-rose-400" style="width:{{ $pVerilsin }}%"></div>
                                        <div class="h-full bg-emerald-400" style="width:{{ $pVerilmesin }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-6 space-y-4">
                                <p class="text-indigo-100 text-xs font-bold opacity-60">Şu an aktif bir oylama bulunmuyor.</p>
                                @if($canManage && !$isFinished)
                                    <input wire:model="oylamaKonu" type="text" class="w-full bg-white/10 border-white/20 rounded-xl text-xs placeholder:text-white/40 mb-2" placeholder="Oylama konusu...">
                                    <button wire:click="startVote" class="w-full py-3 bg-white text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg">Oylama Başlat</button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif



            <!-- GİZLİ NOTLARIM -->
            <div class="bg-amber-500 rounded-[2.5rem] p-8 shadow-2xl shadow-amber-500/30 text-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-700"></div>
                <div class="relative z-10 space-y-4">
                    <h4 class="font-black text-lg uppercase tracking-tighter flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Gizli Notlarım
                    </h4>
                    <p class="text-[9px] font-black text-white/60 uppercase tracking-widest">Sadece siz görebilirsiniz</p>
                    <textarea wire:model.live.debounce.1000ms="gizliNot" class="w-full h-40 bg-white/10 border-white/20 rounded-[1.5rem] text-xs placeholder:text-white/40 py-4 px-4 font-medium" placeholder="Kimsenin görmesini istemediğiniz özel notlarınızı buraya alın..."></textarea>
                </div>
            </div>

        </div>


    <!-- MODALLAR (ERTELEME / İPTAL) -->
    @if($canManage)
        <x-modal name="ertele-modal" :show="$showErteleModal" focusable>
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">TOPLANTIYI ERTELE</h2>
                <p class="mt-2 text-sm text-gray-500">Yeni bir tarih ve erteleme sebebini belirtin. Katılımcılara bildirim gidecektir.</p>
                <div class="mt-6 space-y-4">
                    <input type="datetime-local" wire:model="ertelemeTarihi" class="w-full rounded-xl border-gray-200">
                    <textarea wire:model="ertelemeSebepi" class="w-full rounded-xl border-gray-200" rows="3" placeholder="Erteleme sebebi..."></textarea>
                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="$dispatch('close')" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-black text-[10px] uppercase">Vazgeç</button>
                        <button wire:click="reschedule" class="px-6 py-3 bg-amber-600 text-white rounded-xl font-black text-[10px] uppercase shadow-lg shadow-amber-500/20">Toplantıyı Ertele</button>
                    </div>
                </div>
            </div>
        </x-modal>

        <x-modal name="iptal-modal" :show="$showIptalModal" focusable>
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter text-rose-600">TOPLANTIYI İPTAL ET</h2>
                <p class="mt-2 text-sm text-gray-500">Toplantının iptal edilme sebebini belirtin.</p>
                <div class="mt-6 space-y-4">
                    <textarea wire:model="iptalSebepi" class="w-full rounded-xl border-gray-200" rows="3" placeholder="İptal sebebi..."></textarea>
                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="$dispatch('close')" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-black text-[10px] uppercase">Vazgeç</button>
                        <button wire:click="cancel" class="px-6 py-3 bg-rose-600 text-white rounded-xl font-black text-[10px] uppercase shadow-lg shadow-rose-500/20">Toplantıyı İptal Et</button>
                    </div>
                </div>
            </div>
        </x-modal>
    @endif

    {{-- DİSİPLİN DOSYASI İNCELEME MODALLARI (TOP LEVEL) --}}
    @foreach($toplanti->disiplinDosyalari as $dosya)
        <x-modal name="modal-vote-{{ $dosya->id }}" :show="false" maxWidth="5xl" focusable>
            <div class="bg-white rounded-3xl overflow-hidden shadow-2xl">
                <div class="px-8 py-6 bg-slate-900 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ $dosya->user->profile_photo_url }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-white/20">
                        <div>
                            <h3 class="text-white font-black text-lg tracking-tighter uppercase">{{ $dosya->user->name }}</h3>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Dosya İnceleme & Karar Oylaması</p>
                        </div>
                    </div>
                    <button type="button" @click="$dispatch('close')" class="p-2 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-0 max-h-[85vh] overflow-y-auto custom-scrollbar">
                    <div class="px-8 pb-12">
                        <livewire:admin.disiplin.disiplin-oylama-paneli :case="$dosya" :inModal="true" :key="'voting-modal-'.$dosya->id" />
                    </div>
                </div>
            </div>
        </x-modal>
    @endforeach

    </div>
</div>
