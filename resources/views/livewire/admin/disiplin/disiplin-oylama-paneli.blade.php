<div x-data="{ 
    showCaseModal: false, 
    showSystemModal: false, 
    showDefenseModal: false 
}" wire:poll.3s class="{{ $inModal ? '' : 'mt-12 border-t border-gray-200 pt-8' }}">
    
    {{-- Üst Bar / Başlık ve Hızlı Bilgi Butonları --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="flex items-center gap-5">
            <div class="bg-slate-900 text-white p-4 rounded-[1.5rem] shadow-xl shadow-slate-200 ring-4 ring-white">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                    <h3 class="text-3xl font-black text-slate-900 tracking-tighter">Disiplin Kurulu Odası</h3>

                </div>
                <div class="flex items-center flex-wrap gap-x-3 gap-y-1 mt-1">
                    <p class="text-slate-500 text-sm font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        İlgili Personel: <span class="text-slate-900 font-black">{{ $case->user->name }}</span>
                    </p>
                    <span class="hidden md:inline text-slate-300">•</span>
                    @if($case->durum == 'Karar Verildi' || $case->durum == 'Dosya Kapatıldı')
                        <span class="inline-flex items-center text-xs font-black text-slate-500 uppercase tracking-widest">
                            🔒 Arşiv Kaydı (Oylama Kapalı)
                        </span>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            <p class="text-slate-500 text-sm font-bold">Aktif Oylama Süreci</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- HIZLI BİLGİ BUTONLARI (Sadece Yetkili Üyelere) --}}
        @if(Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Hukuk Admini', 'Hukuk Yöneticisi']))
            <div class="flex flex-wrap md:flex-nowrap items-center justify-start md:justify-end gap-2 w-full md:w-auto shrink-0">
                <div class="flex items-center bg-slate-100/50 p-1 rounded-2xl border border-slate-200">
                    <button wire:click="togglePresentationMode" class="group flex items-center justify-center gap-2 px-3 md:px-4 py-2.5 {{ $presentationMode ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-xl transition-all">
                        <svg class="w-4 h-4 {{ $presentationMode ? 'text-white' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest whitespace-nowrap">Sunum Modu</span>
                    </button>
                </div>

                <button @click="showCaseModal = true" class="group flex items-center justify-center gap-2 px-3 md:px-4 py-3 bg-indigo-50/50 border border-indigo-100 rounded-2xl hover:border-indigo-500 hover:bg-white hover:shadow-md transition-all">
                    <div class="p-1.5 bg-white text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-black text-indigo-900 uppercase tracking-tight whitespace-nowrap">Olay & Tutanak</span>
                </button>

                <button @click="showSystemModal = true" class="group flex items-center justify-center gap-2 px-3 md:px-4 py-3 bg-emerald-50/50 border border-emerald-100 rounded-2xl hover:border-emerald-500 hover:bg-white hover:shadow-md transition-all">
                    <div class="p-1.5 bg-white text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-[10px] font-black text-emerald-900 uppercase tracking-tight whitespace-nowrap">Sistem Önerisi</span>
                </button>

                <button @click="showDefenseModal = true" class="group flex items-center justify-center gap-2 px-3 md:px-4 py-3 bg-amber-50/50 border border-amber-100 rounded-2xl hover:border-amber-500 hover:bg-white hover:shadow-md transition-all">
                    <div class="p-1.5 bg-white text-amber-600 rounded-lg group-hover:bg-amber-600 group-hover:text-white transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    </div>
                    <span class="text-[10px] font-black text-amber-900 uppercase tracking-tight whitespace-nowrap">Savunma</span>
                </button>
            </div>
        @endif
    </div>

    {{-- BAŞKANIN VEYA YETKİLİNİN OYLAMA NOTU --}}
    @if($case->oylama_notu)
        <div class="mb-10">
            <div class="bg-indigo-50/50 border border-indigo-100 rounded-[2rem] p-6 shadow-sm flex items-start gap-5 ring-1 ring-white">
                <div class="w-14 h-14 bg-white border border-indigo-100 rounded-2xl flex items-center justify-center shrink-0 shadow-sm text-indigo-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-indigo-900 font-black text-xs uppercase tracking-[0.2em] mb-2">OYLAMA İLE İLGİLİ ÖZEL NOT</h4>
                    <p class="text-indigo-800/80 text-sm font-bold leading-relaxed italic">"{{ $case->oylama_notu }}"</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- KOLON 1: OY KULLANMA KARTI --}}
        <div class="lg:col-span-3">
            @if($case->durum == 'Kurulda')
                @if(!$case->oylama_aktif)
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 text-center space-y-6">
                        <div class="w-20 h-20 bg-amber-50 rounded-[1.5rem] flex items-center justify-center mx-auto text-amber-500 shadow-inner">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-black text-slate-800 text-lg uppercase tracking-tight">Oylama Bekliyor</h4>
                            <p class="text-xs text-slate-400 font-bold leading-relaxed">Başkan oylamayı başlattığında buradan katılım sağlayabilirsiniz.</p>
                        </div>
                        @if(Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini', 'Hukuk Yöneticisi']))
                            <button wire:click="startCaseVoting" wire:loading.attr="disabled" class="group relative w-full bg-slate-900 hover:bg-slate-800 disabled:bg-slate-700 text-white font-black py-4 px-4 rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 text-sm uppercase tracking-widest overflow-hidden">
                                <div wire:loading.remove wire:target="startCaseVoting" class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Oylamayı Başlat</span>
                                </div>
                                <div wire:loading wire:target="startCaseVoting" class="flex items-center gap-3">
                                    <svg class="animate-spin h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="animate-pulse">Oylama Başlatılıyor...</span>
                                </div>
                            </button>
                        @endif
                    </div>
                @else
                    {{-- OY KULLANMA FORMU --}}
                    <div id="voting_section" class="bg-white rounded-[2rem] md:rounded-[2.5rem] shadow-2xl shadow-slate-200/40 border border-slate-100 p-5 md:p-8 md:sticky md:top-10 scroll-mt-20 mb-8 md:mb-0">
                        @if(!$myVote)
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Oyunuzu Kullanın</h4>
                            </div>
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Karar Tercihi</label>
                                    <select wire:model="tempOyYonu" class="w-full bg-slate-50 border-2 border-slate-100 text-slate-700 text-sm font-bold py-4 px-4 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none">
                                        <option value="">Bir karar seçin...</option>
                                        <option value="Ceza Verilsin">🔴 Ceza Verilsin</option>
                                        <option value="Ceza Verilmesin">🟢 Ceza Verilmesin</option>
                                        <option value="Ek Soruşturma">🟡 Ek Soruşturma</option>
                                        <option value="Çekimser">⚪ Çekimser</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Görüş & Gerekçe</label>
                                    <textarea wire:model="tempYorum" rows="5" class="w-full bg-slate-50 border-2 border-slate-100 text-slate-700 text-sm font-bold py-4 px-4 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none shadow-inner" placeholder="Kararınızın nedenini buraya detaylıca yazınız..."></textarea>
                                </div>

                                <button wire:click="castCaseVote" wire:loading.attr="disabled" class="group relative w-full bg-slate-900 hover:bg-slate-800 disabled:bg-slate-400 text-white font-black py-5 px-4 rounded-[1.5rem] shadow-xl shadow-slate-900/20 transition-all overflow-hidden uppercase tracking-widest text-xs">
                                    <div wire:loading.remove wire:target="castCaseVote" class="flex items-center justify-center gap-2">
                                        <span>Oyumu Kaydet</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </div>
                                    <div wire:loading wire:target="castCaseVote" class="flex items-center justify-center gap-2 whitespace-nowrap">
                                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span class="text-xs font-black text-white uppercase tracking-tight">Kayıt Yapılıyor...</span>
                                    </div>
                                </button>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="w-24 h-24 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white shadow-xl shadow-emerald-100">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h4 class="text-xl font-black text-slate-800 tracking-tight">Oyunuz Alındı</h4>
                                <p class="text-xs text-slate-400 font-bold mt-2 mb-10 px-4">Bu tur için katılımınız başarıyla sisteme işlendi.</p>
                                
                                <button wire:click="deleteCaseVote" wire:loading.attr="disabled"
                                    class="group relative w-full overflow-hidden bg-white hover:bg-rose-50 border-2 border-rose-100 hover:border-rose-200 disabled:opacity-50 py-4 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-rose-100/50">
                                    <div wire:loading.remove wire:target="deleteCaseVote" class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-rose-500 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="text-xs font-black text-rose-600 uppercase tracking-[0.1em]">Oyu Sil ve Değiştir</span>
                                    </div>
                                    <div wire:loading wire:target="deleteCaseVote" class="flex items-center justify-center gap-2 whitespace-nowrap">
                                        <svg class="animate-spin h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span class="text-xs font-black text-rose-600 uppercase tracking-tight">Siliniyor...</span>
                                    </div>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <div class="bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 p-10 text-center text-slate-300">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <p class="text-sm font-black uppercase tracking-widest mb-4">Oylama Kapalı</p>

                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini', 'Hukuk Yöneticisi']) && $case->durum === 'Karar Verildi')
                        <button onclick="handleRevertDecision()" 
                                class="mt-4 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-amber-200 transition-all duration-300 flex items-center gap-3 mx-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l5 5m-5-5l5-5"/></svg>
                            Kararı Geri Al
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- KOLON 2: SONUÇLAR & GÖRÜŞLER & KARAR PANELİ --}}
        <div class="lg:col-span-6 space-y-8">
            {{-- Eğilim Özeti --}}
            <div class="bg-white rounded-[2rem] p-7 border border-slate-100 shadow-xl shadow-slate-200/30 flex justify-between items-center relative overflow-hidden group">
                <div class="absolute left-0 top-0 bottom-0 w-2 bg-indigo-500"></div>
                <div class="flex-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Anlık Eğilim Durumu</p>
                    <div class="flex items-center gap-3">
                        @if($canSeeResults)
                            @php
                                $counts = ['Ceza Verilsin' => $votesPenalty, 'Ceza Verilmesin' => $votesNoPenalty, 'Ek Soruşturma' => $votesInvestigation];
                                arsort($counts);
                                $leader = array_key_first($counts);
                                $max = reset($counts);
                                $leaderText = ($votesUsed == 0) ? 'Bekleniyor' : (($max > 0 && count(array_keys($counts, $max)) > 1) ? 'Eşitlik' : $leader);
                                
                                $statusLabel = match($leaderText) {
                                    'Ceza Verilsin' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'border' => 'border-rose-200'],
                                    'Ceza Verilmesin' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
                                    'Ek Soruşturma' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
                                    'Eşitlik' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200'],
                                    default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200']
                                };
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="px-4 py-1.5 {{ $statusLabel['bg'] }} {{ $statusLabel['text'] }} {{ $statusLabel['border'] }} border-2 rounded-2xl text-xl font-black uppercase tracking-tighter shadow-sm">
                                    {{ $leaderText }}
                                </span>
                                @if($votesUsed > 0 && $leaderText != 'Eşitlik' && $leaderText != 'Bekleniyor')
                                    <span class="text-sm font-black text-slate-300">({{ $max }}/{{ $votesUsed }} Oy)</span>
                                @endif
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <h2 class="text-xl font-black text-slate-300 italic uppercase tracking-widest">Sonuçlar Gizli</h2>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Katılım</p>
                    <div class="flex flex-col items-end">
                        <span class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-1">
                            {{ $votesUsed }}<span class="text-slate-300 text-lg mx-1">/</span>{{ $totalMembersCount }}
                        </span>
                        <span class="text-[11px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">
                            %{{ $participationRate }} Katılım
                        </span>
                    </div>
                </div>
            </div>

            {{-- Görüş Listesi --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/20 border border-slate-50 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="font-black text-slate-800 text-lg uppercase tracking-tight flex items-center gap-3">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                        Üye Görüşleri
                    </h4>
                    <span class="px-3 py-1 bg-slate-50 text-slate-400 text-[10px] font-black rounded-xl border border-slate-100">{{ $votesUsed }} Görüş Kayıtlı</span>
                </div>

                <div class="space-y-5 max-h-[600px] overflow-y-auto pr-3 custom-scrollbar">
                    @forelse($caseVotes as $oy)
                        <div class="relative bg-slate-50/40 border border-slate-100 rounded-3xl p-5 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-500 group">
                            <div class="flex items-start gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-slate-800 font-black shrink-0 shadow-sm border border-slate-50 group-hover:scale-105 transition-transform">
                                    {{ substr($oy->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-base font-black text-slate-900 tracking-tight">{{ $oy->user->name }}</span>
                                        @if($canSeeResults)
                                            <span class="text-[10px] font-black px-3 py-1.5 rounded-xl {{ $oy->oy_yonu == 'Ceza Verilsin' ? 'bg-rose-50 text-rose-600 border border-rose-100' : ($oy->oy_yonu == 'Ceza Verilmesin' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-amber-50 text-amber-600 border border-amber-100') }}">
                                                {{ $oy->oy_yonu }}
                                            </span>
                                        @else
                                            <span class="text-[11px] font-black text-white uppercase bg-green-600 px-4 py-2 rounded-xl flex items-center gap-2 shadow-sm shadow-green-200">
                                                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                                OY KULLANDI
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-[10px] font-bold text-slate-300 tracking-tight">{{ $oy->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    
                                    @if($oy->yorum)
                                        <div class="bg-white rounded-[1.5rem] p-5 border-2 border-slate-200 shadow-md relative mt-4 overflow-hidden group-hover:border-indigo-200 transition-colors">
                                            <div class="absolute top-0 right-0 p-1 bg-slate-100 rounded-bl-xl opacity-40">
                                                <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                            </div>
                                            <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 border-b border-slate-100 pb-1">Üye Görüşü / Gerekçe</h5>
                                            <p class="text-[15px] text-slate-800 font-bold leading-relaxed relative z-10 italic">
                                                @if($canSeeResults)
                                                    {{ $oy->yorum }}
                                                @else
                                                    <span class="text-slate-400 italic font-medium tracking-tight">Görüşleri görmek için oyunuzu kullanmalısınız.</span>
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 bg-slate-50/50 rounded-[3rem] border-3 border-dashed border-slate-100">
                            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-sm">
                                <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="text-slate-300 font-black uppercase tracking-widest text-xs">Henüz Görüş Belirtilmedi</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- YETKİLİ KARAR PANELİ --}}
            @if(Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini']) && $case->durum === 'Kurulda')
                <div class="bg-slate-50 rounded-[3rem] p-6 shadow-2xl shadow-slate-200/40 border border-slate-200 overflow-hidden relative group">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-white rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-1000 opacity-50"></div>
                    
                    <div class="relative z-10">
                        {{-- PANEL BAŞLIĞI VE YÖNETİM ARAÇLARI --}}
                        <div class="mb-4 pb-4 border-b border-slate-200">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2 bg-white text-indigo-600 rounded-xl shadow-sm border border-slate-200 text-[10px] font-black uppercase tracking-widest px-3">
                                    Yetkili Karar Paneli
                                </div>
                                <div class="h-px flex-1 bg-slate-200"></div>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic bg-slate-200/50 px-3 py-1 rounded-lg">Nihai Karar Aşaması</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                @if($case->oylama_aktif)
                                    <button wire:click="finishCaseVoting" class="group flex items-center gap-2 bg-white hover:bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-widest px-4 py-2 rounded-xl border border-rose-100 hover:border-rose-200 transition-all shadow-sm">
                                        Oylamayı Kapat
                                    </button>
                                @endif
                                
                                <button wire:click="toggleManualIntervention" class="flex items-center gap-2 px-4 py-2 {{ $manualInterventionMode ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-white text-slate-500 border-slate-200 shadow-sm' }} border rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                                    Manuel Müdahale: {{ $manualInterventionMode ? 'Açık' : 'Kapalı' }}
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            {{-- Sistem Önerisi Bilgi Kutusu --}}
                            <div class="bg-indigo-600 rounded-[1.5rem] p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xl shadow-indigo-100/50 border border-indigo-500/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-md border border-white/20">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black text-indigo-200 uppercase tracking-widest leading-none mb-1">SİSTEM ÖNERİSİ</span>
                                        <span class="block text-base font-black text-white tracking-tight uppercase leading-none">{{ $case->sistem_oneri_ceza }}</span>
                                    </div>
                                </div>
                                @if($manualInterventionMode)
                                    <div class="flex-1 max-w-sm">
                                        <select wire:model="selectedManualPenalty" class="w-full bg-white/10 border-2 border-white/20 text-white text-[10px] font-black py-2 px-3 rounded-lg focus:ring-amber-500 focus:border-amber-500 appearance-none shadow-sm backdrop-blur-md">
                                            @foreach($penaltyScale as $p)
                                                <option value="{{ $p->ceza_adi }}" class="text-slate-800">{{ $p->ceza_adi }} ({{ $p->min_puan }}-{{ $p->max_puan }} Puan)</option>
                                            @endforeach
                                            <option value="Diğer" class="text-slate-800">Diğer (Manuel Giriş)</option>
                                        </select>
                                    </div>
                                @endif
                            </div>

                            {{-- KRİTİK ALAN: KARAR GEREKÇESİ --}}
                            <div class="bg-indigo-50/30 p-4 rounded-2xl border border-indigo-100/50">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-indigo-600 rounded-full animate-pulse shadow-[0_0_8px_rgba(79,70,229,0.5)]"></div>
                                        <label class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Nihai Karar Gerekçesi</label>
                                    </div>
                                    <span class="px-2 py-0.5 bg-indigo-600 text-white text-[8px] font-black rounded-md uppercase tracking-tighter">Zorunlu Alan</span>
                                </div>
                                <textarea wire:model="decisionNote" rows="2" class="w-full bg-white border-2 border-indigo-100 rounded-xl text-slate-700 text-xs font-bold p-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 placeholder:text-slate-300 transition-all shadow-sm" placeholder="Kararınızı ve dayandığı gerekçeleri buraya detaylandırın..."></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
                                {{-- CEZAYI ONAYLA --}}
                                <div class="flex flex-col gap-2">
                                    <button onclick="handleResolveAction('approve')" class="group relative bg-rose-600 hover:bg-rose-700 p-3.5 rounded-xl shadow-lg shadow-rose-200 transition-all active:scale-95 text-center overflow-hidden border-b-4 border-rose-800">
                                        <span class="relative z-10 block text-[10px] font-black text-white uppercase tracking-widest">CEZAYI ONAYLA</span>
                                    </button>
                                    <div class="bg-blue-50/80 border border-blue-100 p-2 rounded-lg flex items-start gap-2">
                                        <div class="w-4 h-4 bg-blue-500 text-white rounded-md flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-[8px] text-blue-700 font-bold leading-tight uppercase tracking-tighter">Önerilen veya seçilen disiplin cezasını kesinleştirir.</p>
                                    </div>
                                </div>

                                {{-- TEKRAR GÖRÜŞ --}}
                                <div class="flex flex-col gap-2">
                                    <button onclick="handlePostponeAction()" class="group relative bg-amber-500 hover:bg-amber-600 p-3.5 rounded-xl shadow-lg shadow-amber-100 transition-all active:scale-95 text-center overflow-hidden border-b-4 border-amber-700">
                                        <span class="relative z-10 block text-[10px] font-black text-white uppercase tracking-widest">TEKRAR GÖRÜŞ</span>
                                    </button>
                                    <div class="bg-blue-50/80 border border-blue-100 p-2 rounded-lg flex items-start gap-2">
                                        <div class="w-4 h-4 bg-blue-500 text-white rounded-md flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-[8px] text-blue-700 font-bold leading-tight uppercase tracking-tighter">Dosyayı bir sonraki kurula kadar beklemeye alır.</p>
                                    </div>
                                </div>

                                {{-- DOSYAYI KAPAT --}}
                                <div class="flex flex-col gap-2">
                                    <button onclick="handleResolveAction('close')" class="group relative bg-emerald-600 hover:bg-emerald-700 p-3.5 rounded-xl shadow-lg shadow-emerald-100 transition-all active:scale-95 text-center overflow-hidden border-b-4 border-emerald-800">
                                        <span class="relative z-10 block text-[10px] font-black text-white uppercase tracking-widest">DOSYAYI KAPAT</span>
                                    </button>
                                    <div class="bg-blue-50/80 border border-blue-100 p-2 rounded-lg flex items-start gap-2">
                                        <div class="w-4 h-4 bg-blue-500 text-white rounded-md flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-[8px] text-blue-700 font-bold leading-tight uppercase tracking-tighter">Savunmayı kabul eder ve ceza vermeden dosyayı arşive çeker.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- KOLON 3: SONUÇ GRAFİĞİ & ÜYE LİSTESİ --}}
        <div class="lg:col-span-3 space-y-8">
            {{-- Oylama Sonuçları Grafiği --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/20 border border-slate-50 p-8">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Oylama Sonuç Grafiği</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Genel Katılım Durumu</p>
                    </div>

                </div>
                
                @if($votesUsed > 0)
                    @if($canSeeResults)
                        <div class="space-y-8">
                            {{-- Ceza Verilsin --}}
                            <div class="group">
                                <div class="flex justify-between text-xs mb-3">
                                    <span class="font-black text-slate-600 uppercase tracking-tight">Ceza Verilsin</span>
                                    <span class="font-black text-rose-600 bg-rose-50 px-2 py-0.5 rounded-lg">{{ $votesPenalty }} Oy (%{{ number_format($percPenalty, 0) }})</span>
                                </div>
                                <div class="w-full bg-slate-50 rounded-full h-3 relative overflow-hidden shadow-inner border border-slate-100">
                                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-rose-400 to-rose-600 rounded-full shadow-lg transition-all duration-1000" style="width: {{ $percPenalty }}%"></div>
                                </div>
                            </div>

                            {{-- Ceza Verilmesin --}}
                            <div class="group">
                                <div class="flex justify-between text-xs mb-3">
                                    <span class="font-black text-slate-600 uppercase tracking-tight">Ceza Verilmesin</span>
                                    <span class="font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">{{ $votesNoPenalty }} Oy (%{{ number_format($percNoPenalty, 0) }})</span>
                                </div>
                                <div class="w-full bg-slate-50 rounded-full h-3 relative overflow-hidden shadow-inner border border-slate-100">
                                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full shadow-lg transition-all duration-1000" style="width: {{ $percNoPenalty }}%"></div>
                                </div>
                            </div>

                            {{-- Ek Soruşturma --}}
                            <div class="group">
                                <div class="flex justify-between text-xs mb-3">
                                    <span class="font-black text-slate-600 uppercase tracking-tight">Ek Soruşturma</span>
                                    <span class="font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg">{{ $votesInvestigation }} Oy (%{{ number_format($percInvestigation, 0) }})</span>
                                </div>
                                <div class="w-full bg-slate-50 rounded-full h-3 relative overflow-hidden shadow-inner border border-slate-100">
                                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-amber-300 to-amber-500 rounded-full shadow-lg transition-all duration-1000" style="width: {{ $percInvestigation }}%"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16 bg-slate-50/50 rounded-[2.5rem] border-3 border-dashed border-slate-100">
                            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-slate-300 mx-auto mb-6">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3">Veriler Gizlendi</p>
                            <p class="text-[10px] text-slate-400 font-bold px-6 italic leading-relaxed">Bağımsız oylama prensibi gereği grafiği görmek için katılım sağlamalısınız.</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 bg-slate-50/50 rounded-[2rem] border-2 border-dashed border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Bekleme Modu</p>
                    </div>
                @endif
            </div>

            {{-- Üye Listesi --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/20 border border-slate-50 p-8">
                <div class="flex justify-between items-center mb-8">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Üye Listesi</h4>
                    @if($case->durum === 'Karar Verildi')
                        <span class="bg-rose-50 text-rose-600 text-[9px] font-black px-3 py-1 rounded-full border border-rose-100 tracking-tighter uppercase">OYLAMA KAPANDI</span>
                    @elseif($waitingVotes > 0)
                        <span class="bg-amber-50 text-amber-700 text-[9px] font-black px-3 py-1 rounded-full border border-amber-100 animate-pulse tracking-tighter uppercase">{{ $waitingVotes }} BEKLENİYOR</span>
                    @endif
                </div>
                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($allCouncilMembers as $member)
                        @php $voted = in_array($member->id, $votedUserIds); @endphp
                        <div class="group flex items-center justify-between p-3 rounded-2xl {{ $voted ? 'bg-emerald-50/30 border-emerald-100' : 'bg-slate-50/50 border-slate-50' }} border-2 transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-xs font-black text-slate-400 shadow-sm border border-slate-100 group-hover:scale-110 transition-transform">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-800 tracking-tight">{{ $member->name }}</span>
                                    @if($member->hasRole('Disiplin Kurulu Başkanı'))
                                        <span class="text-[8px] font-black text-indigo-500 uppercase tracking-widest">BAŞKAN</span>
                                    @endif
                                </div>
                            </div>
                            @if($voted)
                                <div class="w-6 h-6 bg-emerald-500 text-white rounded-lg flex items-center justify-center shadow-lg shadow-emerald-200">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                </div>
                            @else
                                @if($case->durum === 'Karar Verildi')
                                    <span class="text-[8px] font-black text-rose-600 uppercase tracking-tighter bg-rose-50 px-2 py-1 rounded-md border border-rose-100">OY KULLANMADI</span>
                                @else
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-200 animate-pulse"></span>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- MODALLAR --}}
    
    {{-- 1. OLAY & TUTANAK MODALI --}}
    <div x-show="showCaseModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCaseModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCaseModal = false" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showCaseModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-indigo-600 px-8 py-6 flex justify-between items-center">
                    <h3 class="text-xl font-black text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Olay Açıklaması / Tutanak Metni
                    </h3>
                    <button @click="showCaseModal = false" class="text-white/60 hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-8 py-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-indigo-600 shadow-sm font-black text-xs border border-slate-100">
                                {{ substr($case->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">İLGİLİ PERSONEL</span>
                                <span class="block text-sm font-bold text-slate-800 leading-tight">{{ $case->user->name }}</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-0.5">SİSTEM PUANI</span>
                                <span class="inline-flex items-center gap-1 text-sm font-black text-slate-900 bg-white px-3 py-1 rounded-xl shadow-sm border border-slate-100">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    {{ number_format($case->user->toplam_puan, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-amber-600 shadow-sm border border-slate-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">OLAY TARİHİ</span>
                                <span class="block text-sm font-bold text-slate-800">{{ $case->olay_tarihi->format('d.m.Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-10">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">Tutanak İçeriği</h4>
                            <div class="text-[10px] font-bold text-slate-400 italic">
                                Raporlayan: <span class="text-slate-600">{{ $case->reporter->name }}</span> | Tarih: {{ $case->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                        <div class="bg-white border-2 border-slate-50 rounded-3xl p-6 shadow-inner min-h-[150px]">
                            <p class="text-sm text-slate-700 font-bold leading-relaxed whitespace-pre-wrap">"{{ $case->olay_aciklamasi }}"</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Ekli Belgeler & Kanıtlar</h4>
                        @if(!empty($case->kanit_dosyalari))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($case->kanit_dosyalari as $kanit)
                                    <a href="{{ asset('storage/' . $kanit) }}" target="_blank" class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-white hover:shadow-lg transition-all group">
                                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-sm border border-slate-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.293 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div class="flex-1 truncate">
                                            <span class="block text-xs font-black text-slate-700 truncate">{{ basename($kanit) }}</span>
                                            <span class="block text-[9px] text-slate-400 font-bold uppercase">Görüntüle</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center gap-3 p-5 bg-slate-50 border border-dashed border-slate-200 rounded-2xl text-slate-400 italic text-xs">
                                <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                Bu tutanakla ilişkili herhangi bir ek dosya veya kanıt bulunmamaktadır.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. SİSTEM ÖNERİSİ MODALI --}}
    <div x-show="showSystemModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showSystemModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showSystemModal = false" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showSystemModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-emerald-600 px-8 py-6 flex justify-between items-center">
                    <h3 class="text-xl font-black text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Sistem Değerlendirmesi & Önerisi
                    </h3>
                    <button @click="showSystemModal = false" class="text-white/60 hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-8 py-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-rose-50 rounded-2xl p-4 border-2 border-rose-100 text-center shadow-sm">
                            <span class="block text-[9px] font-black text-rose-600 uppercase tracking-widest mb-1">CEZA PUANI</span>
                            <div class="flex items-center justify-center gap-1">
                                <span class="text-3xl font-black text-rose-900 tracking-tighter">{{ $case->hesaplanan_puan }}</span>
                                <span class="text-[10px] font-bold text-rose-400 mt-1">puan</span>
                            </div>
                        </div>
                        <div class="bg-indigo-50 rounded-2xl p-4 border-2 border-indigo-100 text-center shadow-sm flex flex-col justify-center">
                            <span class="block text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1">SİSTEM ÖNERİSİ</span>
                            <span class="text-xs font-black text-indigo-900 leading-tight uppercase">{{ $case->sistem_oneri_ceza }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-rose-400 rounded-full"></span>
                            Hesaplama Kriterleri
                        </h4>
                        
                        <div class="grid grid-cols-1 gap-1.5">
                            {{-- DAVRANIŞ --}}
                            <div class="flex items-start gap-3 p-3 bg-white border border-slate-100 rounded-xl">
                                <div class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-tighter">İHLAL DAVRANIŞI</span>
                                    <span class="text-[10px] font-bold text-slate-700 leading-snug">{{ $case->behavior->category->ad ?? 'Genel' }} - {{ $case->behavior->tanim }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-1.5">
                                {{-- ETKİ --}}
                                <div class="flex items-center justify-between p-2.5 bg-slate-50/50 border border-slate-100 rounded-xl">
                                    <div class="flex flex-col">
                                        <span class="text-[8px] font-black text-slate-400 uppercase">ETKİ</span>
                                        <span class="text-[10px] font-black text-slate-800 truncate max-w-[80px]">{{ $case->impact->ad ?? 'Genel' }}</span>
                                    </div>
                                    <span class="text-[10px] font-black text-rose-600">+{{ $case->impact->puan ?? 0 }}</span>
                                </div>

                                {{-- KAPSAM --}}
                                <div class="flex items-center justify-between p-2.5 bg-slate-50/50 border border-slate-100 rounded-xl">
                                    <div class="flex flex-col">
                                        <span class="text-[8px] font-black text-slate-400 uppercase">KAPSAM</span>
                                        <span class="text-[10px] font-black text-slate-800 truncate max-w-[80px]">{{ $case->scope->ad ?? 'Standart' }}</span>
                                    </div>
                                    <span class="text-[10px] font-black text-rose-600">+{{ $case->scope->puan ?? 0 }}</span>
                                </div>

                                {{-- TEKRAR --}}
                                <div class="flex items-center justify-between p-2.5 bg-slate-50/50 border border-slate-100 rounded-xl">
                                    <div class="flex flex-col">
                                        <span class="text-[8px] font-black text-slate-400 uppercase">TEKRAR</span>
                                        <span class="text-[10px] font-black text-slate-800">{{ $case->tekrar_sayisi ?? 1 }}. Kez</span>
                                    </div>
                                    @if(($case->tekrar_sayisi ?? 1) > 1)
                                        <span class="px-1.5 py-0.5 bg-rose-600 text-white text-[9px] font-black rounded-lg">x{{ $case->tekrar_sayisi }}</span>
                                    @else
                                        <span class="text-[10px] font-black text-slate-300">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. PERSONEL SAVUNMASI MODALI --}}
    <div x-show="showDefenseModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDefenseModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDefenseModal = false" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showDefenseModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-amber-500 px-8 py-6 flex justify-between items-center">
                    <h3 class="text-xl font-black text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Personel Savunması
                    </h3>
                    <button @click="showDefenseModal = false" class="text-white/60 hover:text-white transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-8 py-10">
                    @if($case->savunma_tarihi)
                        <div class="bg-amber-50 rounded-2xl p-4 border-2 border-amber-100 mb-6 flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-amber-600 shadow-sm border border-amber-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-black text-amber-600 uppercase tracking-widest">SAVUNMA TARİHİ</span>
                                    <span class="text-sm font-black text-amber-900 tracking-tight">{{ $case->savunma_tarihi->format('d.m.Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-emerald-500 text-white text-[9px] font-black rounded-full uppercase tracking-widest">KAYITLI</span>
                        </div>
                        
                        <div class="mb-8">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Savunma Açıklaması</h4>
                            <div class="bg-white border-2 border-slate-50 rounded-3xl p-6 shadow-inner relative overflow-hidden">
                                @php
                                    $savunmaText = $case->savunma_aciklamasi;
                                    $hasNote = str_contains($savunmaText, '(Not:');
                                    $mainContent = $hasNote ? trim(Str::before($savunmaText, '(Not:')) : $savunmaText;
                                    $noteContent = $hasNote ? Str::between($savunmaText, '(Not:', ')') : null;
                                    
                                    if($noteContent) {
                                        $noteContent = preg_replace('/(\d{2}\.\d{2}\.\d{4} \d{2}:\d{2})/', '<strong class="text-indigo-600">$1</strong>', $noteContent);
                                        $noteContent = preg_replace('/tarihinde (.*?) tarafından/', 'tarihinde <strong class="text-slate-900 font-black italic">$1</strong> tarafından', $noteContent);
                                    }
                                @endphp
                                
                                <p class="text-sm text-slate-700 font-bold leading-relaxed whitespace-pre-wrap italic">"{{ $mainContent }}"</p>
                                
                                @if($noteContent)
                                    <div class="mt-6 p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-indigo-600 shrink-0 shadow-sm border border-indigo-100">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="text-[12px] text-indigo-900 font-medium leading-relaxed italic">
                                            <span class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1.5 border-b border-indigo-100 pb-1">Kayıt Notu / Müdahale Bilgisi</span>
                                            {!! $noteContent !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Savunma Ekleri</h4>
                            @if(!empty($case->savunma_dosyalari))
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($case->savunma_dosyalari as $sDosya)
                                        <a href="{{ asset('storage/' . $sDosya) }}" target="_blank" class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-white hover:shadow-lg transition-all group">
                                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-amber-600 shadow-sm border border-slate-100">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div class="flex-1 truncate">
                                                <span class="block text-xs font-black text-slate-700 truncate">{{ basename($sDosya) }}</span>
                                                <span class="block text-[9px] text-slate-400 font-bold uppercase">Görüntüle</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex items-center gap-3 p-5 bg-slate-50 border border-dashed border-slate-200 rounded-2xl text-slate-400 italic text-xs">
                                    <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    Henüz bir savunma dosyası eklenmemiştir.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-20 bg-amber-50/50 rounded-[3rem] border-3 border-dashed border-amber-100">
                            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-sm text-amber-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-amber-600 font-black uppercase tracking-widest text-xs">Henüz Savunma Girilmemiştir</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.handleResolveAction = function(type) {
        if (type === 'approve') {
            const manualMode = @this.get('manualInterventionMode');
            const penalty = manualMode ? @this.get('selectedManualPenalty') : '{{ $case->sistem_oneri_ceza }}';
            
            Swal.fire({
                title: 'Kararı Kesinleştir',
                html: `
                    <div class="text-center">
                        <p class="text-slate-500 mb-4 font-medium">Aşağıdaki karar dosyaya nihai ceza olarak işlenecektir.</p>
                        <div class="px-6 py-4 bg-rose-50 border-2 border-rose-100 rounded-2xl">
                            <span class="block text-[10px] font-black text-rose-400 uppercase tracking-widest mb-1">KESİNLEŞECEK KARAR</span>
                            <span class="block text-xl font-black text-rose-900 uppercase tracking-tighter">${penalty}</span>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Evet, Onayla ve Bitir',
                cancelButtonText: 'Vazgeç'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.resolveCase('approve');
                }
            });
            return;
        }

        const messages = {
            'close': { title: 'Dosyayı Kapat', text: 'Savunma kabul edilecek ve dosya cezasız kapatılacaktır. Emin misiniz?', icon: 'info', confirmColor: '#059669' }
        };
        const msg = messages[type];
        
        Swal.fire({
            title: msg.title,
            text: msg.text,
            icon: msg.icon,
            showCancelButton: true,
            confirmButtonColor: msg.confirmColor,
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Evet, Onayla',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.resolveCase(type);
            }
        });
    }

    window.handlePostponeAction = function() {
        Swal.fire({
            title: 'Tekrar Görüşme Kararı',
            html: `
                <div class="text-left space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Yeni Toplantı Tarihi</label>
                        <input type="datetime-local" id="swal-postpone-date" class="w-full rounded-xl border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Toplantı Lokasyonu / Linki</label>
                        <input type="text" id="swal-postpone-location" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Örn: Toplantı Odası 1 veya Zoom Linki">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Erteleme Sebebi</label>
                        <textarea id="swal-postpone-reason" class="w-full rounded-xl border-slate-200 text-sm" rows="3"></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            confirmButtonText: 'Ertele',
            cancelButtonText: 'Vazgeç',
            preConfirm: () => {
                const date = document.getElementById('swal-postpone-date').value;
                const location = document.getElementById('swal-postpone-location').value;
                const reason = document.getElementById('swal-postpone-reason').value;
                if (!date || !location || !reason) {
                    Swal.showValidationMessage('Lütfen tüm alanları doldurun');
                    return false;
                }
                return { date, location, reason };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.postponeCase(result.value.date, result.value.location, result.value.reason);
            }
        });
    }

    window.handleRevertDecision = function() {
        Swal.fire({
            title: 'Kararı Geri Al',
            html: `
                <div class="text-center">
                    <p class="text-slate-500 mb-4 font-medium">Bu kararı geri alıp dosyayı tekrar oylamaya açmak istediğinize emin misiniz?</p>
                    <div class="px-6 py-4 bg-amber-50 border-2 border-amber-100 rounded-2xl">
                        <span class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">DİKKAT</span>
                        <span class="block text-sm font-bold text-amber-900 leading-relaxed">Puan iadesi yapılacak ve dosya tekrar oylamaya açılacaktır.</span>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Evet, Geri Al',
            cancelButtonText: 'Vazgeç',
            customClass: {
                popup: 'rounded-[2rem]',
                confirmButton: 'rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest',
                cancelButton: 'rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.revertDecision();
            }
        });
    }
</script>
@endpush
