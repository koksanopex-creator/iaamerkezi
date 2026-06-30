<?php
    // Dosyanın dahil olduğu TAMAMLANMIŞ bir interaktif kurul toplantısı var mı?
    // SADECE "Karar Verildi" durumundaki dosyalar için arşiv görünümünü (toplantı özetini) getiriyoruz.
    // "Kurulda" olan dosyalar için, geçmişte bir toplantıya katılmış olsa bile CANLI odayı görmeliyiz.
    $interactiveMeeting = ($case->durum === 'Karar Verildi') 
        ? $case->toplantilar()->where('durum', 'tamamlandı')->latest()->first()
        : null;

    // B. Kurul Üyelerini Belirle
    $councilRoles = ['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'];
    $allCouncilMembers = \App\Models\User::role($councilRoles)
        ->whereDoesntHave('roles', function($q){ $q->where('name', 'Superadmin'); })
        ->get();
    $totalMembersCount = $allCouncilMembers->count();

    // C. Oy Durumları (Eski Yapı İçin)
    $votedUserIds = $case->oylar->pluck('user_id')->toArray();
    $votesUsed = $case->oylar->count();
    $waitingVotes = $totalMembersCount - $case->oylar->whereIn('user_id', $allCouncilMembers->pluck('id'))->count();
    if($waitingVotes < 0) $waitingVotes = 0;

    // D. İstatistikler (Eski Yapı İçin)
    $votesPenalty = $case->oylar->where('oy_yonu', 'Ceza Verilsin')->count();
    $votesNoPenalty = $case->oylar->where('oy_yonu', 'Ceza Verilmesin')->count();
    $votesInvestigation = $case->oylar->where('oy_yonu', 'Ek Soruşturma')->count();
    $votesAbstain = $case->oylar->where('oy_yonu', 'Çekimser')->count();

    $totalForCalc = $votesUsed > 0 ? $votesUsed : 1;
    $percPenalty = ($votesPenalty / $totalForCalc) * 100;
    $percNoPenalty = ($votesNoPenalty / $totalForCalc) * 100;
    $percInvestigation = ($votesInvestigation / $totalForCalc) * 100;
    $percAbstain = ($votesAbstain / $totalForCalc) * 100;

    // E. Lider Seçenek (Eski Yapı İçin)
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

<?php
    $inModal = $inModal ?? false;
?>

    <div class="{{ $inModal ? '' : 'mt-12 border-t border-gray-200 pt-8' }}">
        
        @if(!$inModal)
            {{-- Başlık --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="bg-slate-900 text-white p-3 rounded-2xl shadow-lg shadow-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Disiplin Kurulu Odası</h3>
                    @if($case->durum == 'Karar Verildi')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                            🔒 Arşiv Kaydı (Oylama Kapalı)
                        </span>
                    @else
                        <p class="text-slate-500 text-sm">Aktif Oylama Süreci</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- İNTERAKTİF TOPLANTI VARSA YENİ ARŞİV GÖRÜNÜMÜ --}}
        @if($interactiveMeeting)
            <div class="space-y-8">
                {{-- Üst Özet Kartları --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Toplantı Durumu</p>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-lg font-bold text-slate-800 uppercase">TAMAMLANDI</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Toplantı Tarihi</p>
                        <span class="text-lg font-bold text-slate-800">{{ $interactiveMeeting->baslangic_tarihi->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Katılım Oranı</p>
                        <?php
                            $totalK = $interactiveMeeting->katilimcilar->count();
                            // Katıldı olarak işaretlenenler + Toplantı bazlı oylardan bağımsız olarak ana dosyaya oy kullananları da katıldı sayabiliriz
                            $voterIds = $case->oylar->pluck('user_id')->toArray();
                            $presents = $interactiveMeeting->katilimcilar->filter(function($k) use ($voterIds) {
                                return $k->katilim_durumu === 'katıldı' || in_array($k->user_id, $voterIds);
                            })->count();
                            $perc = $totalK > 0 ? round(($presents / $totalK) * 100) : 0;
                        ?>
                        <span class="text-lg font-bold text-slate-800">%{{ $perc }} ({{ $presents }}/{{ $totalK }})</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {{-- SOL: KARAR MADDELERİ (8/12) --}}
                    <div class="lg:col-span-8 space-y-6">
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Alınan Karar Maddeleri
                                </h4>
                            </div>
                            <div class="divide-y divide-slate-100">
                                @forelse($interactiveMeeting->kararMaddeleri as $index => $madde)
                                    <div class="p-6 hover:bg-slate-50/50 transition-colors">
                                        <div class="flex items-start gap-4">
                                            <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0">{{ $index + 1 }}</span>
                                            <div class="flex-1">
                                                <p class="text-slate-800 font-medium leading-relaxed">{{ $madde->icerik }}</p>
                                                @if($madde->sorumlu_user_id)
                                                    <div class="mt-3 flex items-center gap-2">
                                                        <span class="text-[10px] font-bold text-slate-400 uppercase">SORUMLU:</span>
                                                        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $madde->sorumlu->name }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-12 text-center text-slate-400 italic">
                                        Toplantıda kayıtlı bir karar maddesi bulunamadı.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Dosya Nihai Kararı --}}
                        @if($case->yonetici_notu)
                            @php
                                $yoneticiNotu = $case->yonetici_notu;
                                $signatureData = null;
                                // Yeni format: (İşlemi Yapan: Name [ID:5] - Date)
                                if (preg_match('/\((İşlemi Yapan: (.*?) \[ID:(\d+)\] - (.*?))\)\s*$/', $yoneticiNotu, $match)) {
                                    $signatureData = [
                                        'full' => $match[1],
                                        'name' => $match[2],
                                        'id' => $match[3],
                                        'date' => $match[4]
                                    ];
                                    $yoneticiNotu = trim(str_replace($match[0], '', $yoneticiNotu));
                                } 
                                // Eski format: (İşlemi Yapan: Name - Date)
                                elseif (preg_match('/\((İşlemi Yapan: (.*?) - (.*?))\)\s*$/', $yoneticiNotu, $match)) {
                                    $signatureData = [
                                        'full' => $match[1],
                                        'name' => $match[2],
                                        'date' => $match[3]
                                    ];
                                    $yoneticiNotu = trim(str_replace($match[0], '', $yoneticiNotu));
                                }
                            @endphp
                            <div class="bg-emerald-50 border border-emerald-100 rounded-3xl p-8">
                                <h4 class="text-sm font-black text-emerald-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Dosya Nihai Kararı
                                </h4>
                                <div class="prose prose-slate max-w-none text-emerald-800 leading-relaxed font-bold italic text-lg mb-4">
                                    "{!! nl2br(e($yoneticiNotu)) !!}"
                                </div>
                                @if($signatureData)
                                    <div class="flex justify-end pt-4 border-t border-emerald-100/50">
                                        <div class="text-right">
                                            <p class="text-[10px] font-bold text-emerald-600/60 uppercase tracking-widest mb-1 text-right">İşlemi Yapan Yetkili</p>
                                            <div class="flex items-center justify-end gap-2 text-xs font-medium text-emerald-900/70">
                                                @if(isset($signatureData['id']))
                                                    <a href="{{ route('profile.show', $signatureData['id']) }}" class="text-emerald-700 font-black hover:underline decoration-emerald-400 decoration-2 underline-offset-2 transition-all">
                                                        {{ $signatureData['name'] }}
                                                    </a>
                                                @else
                                                    <span class="text-emerald-700 font-black">{{ $signatureData['name'] }}</span>
                                                @endif
                                                <span class="w-1 h-1 rounded-full bg-emerald-300"></span>
                                                <span class="text-[10px] opacity-70">{{ $signatureData['date'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Toplantı Notu / İcmal --}}
                        @if($interactiveMeeting->toplanti_karari)
                            <div class="bg-indigo-50/50 rounded-3xl border border-indigo-100 p-8">
                                <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-widest mb-4">Toplantı Özeti & İcmali</h4>
                                <div class="prose prose-slate max-w-none text-indigo-900/80 leading-relaxed font-medium">
                                    {!! nl2br(e($interactiveMeeting->toplanti_karari)) !!}
                                </div>
                            </div>
                        @endif

                        {{-- ÜYE GÖRÜŞLERİ - ARŞİVDE DE GÖRÜNSÜN --}}
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
                            <h4 class="font-bold text-slate-700 mb-6 flex items-center justify-between">
                                Üye Görüşleri Arşivi
                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">{{ $case->oylar->count() }} Görüş</span>
                            </h4>
                            <div class="space-y-6 max-h-[400px] overflow-y-auto pr-2">
                                @foreach($case->oylar as $oy)
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold shrink-0">
                                            {{ substr($oy->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-bold text-slate-800">{{ $oy->user->name }}</span>
                                                <x-disciplinary-opinion-badge :opinion="$oy->oy_yonu" />
                                            </div>
                                            @if($oy->yorum)
                                                <div class="mt-2 text-[13px] text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl rounded-tl-none italic border-l-4 border-slate-200">
                                                    "{{ $oy->yorum }}"
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- SAĞ: YOKLAMA VE DOSYALAR (4/12) --}}
                    <div class="lg:col-span-4 space-y-6">
                        {{-- Yoklama Kartı --}}
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                            <h4 class="font-bold text-slate-700 mb-6 text-sm flex items-center justify-between">
                                Katılım Listesi
                                <span class="bg-emerald-100 text-emerald-700 text-[10px] px-2 py-0.5 rounded-full ring-1 ring-emerald-200">ONAYLANDI</span>
                            </h4>
                            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                                @foreach($interactiveMeeting->katilimcilar as $k)
                                    <?php
                                        $hasVoted = in_array($k->user_id, $voterIds);
                                        $isAttended = ($k->katilim_durumu === 'katıldı' || $hasVoted);
                                    ?>
                                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-400">
                                                {{ substr($k->user->name, 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-slate-700">{{ $k->user->name }}</span>
                                                <span class="text-[9px] text-slate-400 uppercase">
                                                    {{ $k->is_moderator ? 'MODERATÖR' : 'ÜYE' }}
                                                    @if($hasVoted) <span class="text-emerald-500 ml-1">(OY KULLANDI)</span> @endif
                                                </span>
                                            </div>
                                        </div>
                                        @if($isAttended)
                                            <span class="text-emerald-500 bg-emerald-100 p-1 rounded-full"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg></span>
                                        @else
                                            <span class="text-rose-500 bg-rose-100 p-1 rounded-full" title="{{ $k->katilmama_nedeni ?? 'Neden belirtilmedi' }}"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg></span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Karar Dosyası (Eğer varsa) --}}
                        @if($interactiveMeeting->karar_dosya_yolu)
                            <div class="bg-slate-900 rounded-3xl p-6 shadow-xl shadow-slate-200">
                                <h4 class="text-white font-bold text-sm mb-4">Nihai Karar Tutanağı</h4>
                                <a href="{{ Storage::url($interactiveMeeting->karar_dosya_yolu) }}" target="_blank" class="flex items-center gap-4 bg-white/10 hover:bg-white/20 p-4 rounded-2xl border border-white/20 transition-all group">
                                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="flex-1 overflow-hidden">
                                        <p class="text-white font-bold text-sm truncate">Karar_Tutanağı.pdf</p>
                                        <p class="text-white/40 text-[10px] font-medium tracking-widest uppercase">İndirmek için tıklayın</p>
                                    </div>
                                    <svg class="w-5 h-5 text-white/50 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            {{-- A. BAŞKANIN OYLAMA NOTU (EĞER VARSA) --}}
            @if($case->oylama_aktif && $case->oylama_notu)
                <div class="lg:col-span-12 mb-6">
                    <div class="bg-indigo-50 border border-indigo-100 rounded-3xl p-6 flex flex-col md:flex-row items-center gap-6 shadow-sm">
                        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center shrink-0 shadow-inner">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                        <div class="flex-1 space-y-1">
                            <h4 class="text-indigo-900 font-black text-sm uppercase tracking-widest">Kurul Başkanının Notu</h4>
                            <p class="text-indigo-800/70 text-sm font-medium leading-relaxed italic">"{{ $case->oylama_notu }}"</p>
                        </div>
                        <div class="hidden md:block">
                            <span class="text-[10px] font-black text-indigo-300 uppercase tracking-tighter">BİLGİLENDİRME</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- KOLON 1: OY KULLANMA KARTI (SADECE KURULDA İSE GÖRÜNÜR) --}}
            @if($case->durum == 'Kurulda')
                <div class="lg:col-span-3">
                    @if(Auth::user()->hasRole(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı', 'Superadmin', 'Hukuk Admini']) || Auth::user()->can('disiplin.oylama.baslat'))
                        @if(!$case->oylama_aktif)
                            {{-- OYLAMA HENÜZ BAŞLATILMADI --}}
                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-6 text-center space-y-4">
                                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-700 text-sm">Oylama Henüz Başlamadı</h4>
                                    <p class="text-xs text-slate-400 mt-1">Disiplin Kurulu Başkanının oylamayı başlatması bekleniyor.</p>
                                </div>

                                @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı']) || Auth::user()->can('disiplin.oylama.baslat'))
                                    <form id="start-voting-form-{{ $case->id }}" action="{{ route('admin.disiplin.voting.start', $case->id) }}?reopen_modal={{ $case->id }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="oylama_notu" id="oylama_notu_input_{{ $case->id }}">
                                        <button type="button"
                                            onclick="startVotingWithConfirm('{{ $case->id }}')"
                                            class="w-full mt-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 text-sm group">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Oylamayı Başlat
                                        </button>
                                    </form>
                                @else
                                    <div class="inline-flex items-center gap-2 text-xs text-amber-600 bg-amber-50 border border-amber-100 px-3 py-2 rounded-lg">
                                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                        Başkanın başlatması bekleniyor...
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- OYLAMA AKTİF --}}
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Oyunuzu Kullanın</h4>
                                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                </div>
                                <form action="{{ route('admin.disiplin.vote.save', $case->id) }}?reopen_modal={{ $case->id }}" method="POST" data-case-id="{{ $case->id }}" class="disiplin-action-form">
                                    @csrf
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
                                @if($myVote)
                                    <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                                        <form action="{{ route('admin.disiplin.vote.delete', $case->id) }}?reopen_modal={{ $case->id }}" method="POST" data-case-id="{{ $case->id }}" class="disiplin-action-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Silmek istediğinize emin misiniz?')" class="text-xs font-bold text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-3 py-2 rounded-lg transition">Mevcut Oyumu Sil</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            @else
                {{-- KARAR VERİLDİ DURUMUNDA SOL KOLON BOŞ VEYA BİLGİ --}}
                <div class="lg:col-span-3">
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <p class="text-sm font-bold">Oylama Kapandı</p>
                        <p class="text-xs mt-1">Dosya hakkında nihai karar verildiği için yeni oy kullanılamaz.</p>
                    </div>
                </div>
            @endif

            {{-- KOLON 2: ORTA AKIŞ - DURUM & YORUMLAR (5/12) --}}
            <div class="lg:col-span-5 space-y-6">
                @if($case->durum == 'Karar Verildi' && $case->yonetici_notu)
                    @php
                        $yoneticiNotu = $case->yonetici_notu;
                        $signatureData = null;
                        if (preg_match('/\((İşlemi Yapan: (.*?) \[ID:(\d+)\] - (.*?))\)\s*$/', $yoneticiNotu, $match)) {
                            $signatureData = [
                                'full' => $match[1],
                                'name' => $match[2],
                                'id' => $match[3],
                                'date' => $match[4]
                            ];
                            $yoneticiNotu = trim(str_replace($match[0], '', $yoneticiNotu));
                        } elseif (preg_match('/\((İşlemi Yapan: (.*?) - (.*?))\)\s*$/', $yoneticiNotu, $match)) {
                            $signatureData = [
                                'full' => $match[1],
                                'name' => $match[2],
                                'date' => $match[3]
                            ];
                            $yoneticiNotu = trim(str_replace($match[0], '', $yoneticiNotu));
                        }
                    @endphp
                    <div class="bg-emerald-600 rounded-2xl p-6 shadow-lg shadow-emerald-100 text-white animate-in zoom-in duration-300">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 bg-white/20 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="font-black text-sm uppercase tracking-widest">Dosya Nihai Kararı Alındı</h4>
                        </div>
                        <p class="text-sm font-bold leading-relaxed italic border-l-4 border-white/30 pl-4 py-1 mb-4">
                            "{!! nl2br(e($yoneticiNotu)) !!}"
                        </p>
                        @if($signatureData)
                            <div class="flex justify-end pt-3 border-t border-white/10">
                                <div class="text-right">
                                    <div class="flex items-center justify-end gap-2 text-[11px] font-medium text-emerald-50">
                                        <span class="opacity-60">İşlemi Yapan:</span>
                                        @if(isset($signatureData['id']))
                                            <a href="{{ route('profile.show', $signatureData['id']) }}" class="text-white font-black hover:underline decoration-white/50 underline-offset-2">
                                                {{ $signatureData['name'] }}
                                            </a>
                                        @else
                                            <span class="text-white font-black">{{ $signatureData['name'] }}</span>
                                        @endif
                                        <span class="w-1 h-1 rounded-full bg-white/30"></span>
                                        <span class="opacity-60">{{ $signatureData['date'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Anlık Durum Kartı --}}
                <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl p-1 shadow-sm border border-slate-200">
                    <div class="bg-white rounded-xl p-5 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Eğilim / Sonuç</p>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-{{ $leaderColor }}-500 shadow-{{ $leaderColor }}"></span>
                                <h2 class="text-xl font-black text-slate-800">{{ $leaderText }}</h2>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Katılım</p>
                            <span class="text-2xl font-black text-slate-200">
                                %<span class="text-slate-800">{{ number_format(($votesUsed / ($totalMembersCount > 0 ? $totalMembersCount : 1)) * 100, 0) }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Görüşler Listesi --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 min-h-[400px]">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="font-bold text-slate-700">Üye Görüşleri</h4>
                        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">{{ $votesUsed }} Görüş</span>
                    </div>

                    @if($case->oylar->isEmpty())
                        <div class="flex flex-col items-center justify-center h-48 text-slate-300">
                            <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-sm font-medium">Henüz bir görüş paylaşılmadı.</p>
                        </div>
                    @else
                        <div class="space-y-6 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($case->oylar as $oy)
                                <div class="relative group">
                                    <div class="flex items-start gap-4">
                                        {{-- Avatar --}}
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm border-2 border-white shadow-sm ring-1 ring-slate-100">
                                                {{ substr($oy->user->name, 0, 1) }}
                                            </div>
                                        </div>
                                        
                                        {{-- İçerik --}}
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <div>
                                                    <a href="{{ route('profile.show', $oy->user->id) }}" target="_blank" class="text-sm font-bold text-slate-800 hover:text-indigo-600 transition">
                                                        {{ $oy->user->name }}
                                                    </a>
                                                    <p class="text-[10px] text-slate-400 font-medium">{{ $oy->created_at->diffForHumans() }}</p>
                                                </div>
                                                <x-disciplinary-opinion-badge :opinion="$oy->oy_yonu" />
                                            </div>
                                            
                                            @if($oy->yorum)
                                                <div class="mt-2 text-[13px] text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl rounded-tl-none italic border-l-4 border-slate-200">
                                                    "{{ $oy->yorum }}"
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- C) KARARI BAĞLA (YÖNETİCİ PANELİ) --}}
                    @if((Auth::user()->hasRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini']) || Auth::user()->can('disiplin.kurul.toplanti.yonet')) && $votesUsed > 0 && $case->durum == 'Kurulda')
                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <h4 class="font-bold text-slate-800 mb-4 text-xs uppercase flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-800"></span>
                                Yetkili Karar Paneli
                            </h4>
                            
                            <form method="POST" enctype="multipart/form-data" data-case-id="{{ $case->id }}" class="bg-slate-50 p-4 rounded-xl border border-slate-200 disiplin-action-form">
                                @csrf
                                
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
                                    <button type="submit" formaction="{{ route('admin.disiplin.penalty.approve', $case->id) }}?reopen_modal={{ $case->id }}" onclick="return confirm('CEZA ONAYLANACAK. Emin misiniz?')" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 rounded-lg shadow-sm text-xs flex justify-center items-center gap-2 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Cezayı Onayla
                                    </button>
                                    <button type="submit" formaction="{{ route('admin.disiplin.defense.accept', $case->id) }}?reopen_modal={{ $case->id }}" onclick="return confirm('DOSYA KAPATILACAK. Emin misiniz?')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg shadow-sm text-xs flex justify-center items-center gap-2 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Dosyayı Kapat
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- KOLON 3: GRAFİK & ÜYE LİSTESİ (4/12) --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- A) İstatistik Grafiği --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-slate-700 mb-6 text-sm">Oylama Sonuçları</h4>
                    
                    @if($votesUsed > 0)
                        <div class="space-y-5">
                            {{-- Bar Item --}}
                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-600">Ceza Verilsin</span>
                                    <span class="font-bold text-rose-600">{{ $votesPenalty }} Oy (%{{ number_format($percPenalty, 0) }})</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-rose-500 h-2 rounded-full shadow-sm shadow-rose-200 transition-all duration-1000" style="width: {{ $percPenalty }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-600">Ceza Verilmesin</span>
                                    <span class="font-bold text-emerald-600">{{ $votesNoPenalty }} Oy (%{{ number_format($percNoPenalty, 0) }})</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full shadow-sm shadow-emerald-200 transition-all duration-1000" style="width: {{ $percNoPenalty }}%"></div>
                                </div>
                            </div>

                            @if($votesInvestigation > 0)
                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-600">Ek Soruşturma</span>
                                    <span class="font-bold text-amber-600">{{ $votesInvestigation }} Oy</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-amber-400 h-2 rounded-full shadow-sm shadow-amber-200 transition-all duration-1000" style="width: {{ $percInvestigation }}%"></div>
                                </div>
                            </div>
                            @endif

                            @if($votesAbstain > 0)
                            <div>
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-600">Çekimser</span>
                                    <span class="font-bold text-slate-500">{{ $votesAbstain }} Oy</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-slate-400 h-2 rounded-full shadow-sm shadow-slate-200 transition-all duration-1000" style="width: {{ $percAbstain }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-xs text-slate-400">Veri yok</p>
                        </div>
                    @endif
                </div>

                {{-- B) Üye Listesi --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="font-bold text-slate-700 text-sm">Üye Listesi</h4>
                        
                        @if($waitingVotes > 0)
                            <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-full border border-amber-100 animate-pulse">
                                {{ $waitingVotes }} Kişi Bekleniyor
                            </span>
                        @else
                            <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-full border border-emerald-100">
                                Tamamlandı
                            </span>
                        @endif
                    </div>

                    <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
                        @foreach($allCouncilMembers as $member)
                            <?php 
                                $hasVoted = in_array($member->id, $votedUserIds);
                                $isPresident = $member->hasRole('Disiplin Kurulu Başkanı');
                                $rowClass = $isPresident ? 'bg-indigo-50/50 border-indigo-100' : 'hover:bg-slate-50 border-transparent';
                            ?>
                            
                            <div class="flex items-center justify-between p-2.5 rounded-xl border {{ $rowClass }} transition group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white border border-slate-100 flex items-center justify-center text-xs font-bold text-slate-600 shadow-sm group-hover:scale-105 transition">
                                        {{ substr($member->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <a href="{{ route('profile.show', $member->id) }}" target="_blank" class="text-xs font-bold text-slate-700 hover:text-indigo-600">
                                            {{ $member->name }}
                                        </a>
                                        @if($isPresident)
                                            <span class="text-[9px] text-indigo-500 font-bold uppercase tracking-wider">BAŞKAN</span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    @if($hasVoted)
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-amber-50 text-amber-400 flex items-center justify-center" title="Bekleniyor">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        if (typeof window.startVotingWithConfirm !== 'function') {
            window.startVotingWithConfirm = function(caseId) {
                Swal.fire({
                    title: 'Oylamayı Başlat?',
                    html: `
                        <div class="text-slate-500 text-sm mb-4">Üyelere iletilecek ve dosyada saklanacak bir dipnot ekleyebilirsiniz:</div>
                        <textarea id="swal-oylama-notu" class="w-full h-32 px-4 py-3 rounded-2xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-sm font-medium text-slate-700" placeholder="Örn: Oylama şu sebeple gerçekleşecektir, şunlara dikkat edilmelidir..."></textarea>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Evet, Oylamayı Başlat',
                    cancelButtonText: 'İptal',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-3xl border-none shadow-2xl',
                        confirmButton: 'rounded-xl font-bold px-6 py-3',
                        cancelButton: 'rounded-xl font-bold px-6 py-3'
                    },
                    preConfirm: () => {
                        return document.getElementById('swal-oylama-notu').value;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const oylamaNotu = result.value;

                        const meetingContainer = document.getElementById('toplanti-odasi-container');
                        if (meetingContainer && window.Livewire) {
                            const lw = Livewire.find(meetingContainer.getAttribute('wire:id'));
                            if (lw) {
                                Swal.fire({
                                    title: 'Başlatılıyor...',
                                    html: 'Lütfen bekleyin, bildirimler gönderiliyor.',
                                    allowOutsideClick: false,
                                    didOpen: () => { Swal.showLoading(); }
                                });
                                lw.startCaseVoting(caseId, oylamaNotu).then(() => {
                                    Swal.close();
                                });
                                return;
                            }
                        }
                        
                        document.getElementById('oylama_notu_input_' + caseId).value = oylamaNotu;
                        
                        Swal.fire({
                            title: 'Başlatılıyor...',
                            html: 'Lütfen bekleyin, bildirimler gönderiliyor.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        document.getElementById('start-voting-form-' + caseId).submit();
                    }
                });
            }
        }

        if (typeof window.disiplinFormInterceptorAttached === 'undefined') {
            window.disiplinFormInterceptorAttached = true;
            document.addEventListener('submit', function(e) {
                const form = e.target;
                const meetingContainer = document.getElementById('toplanti-odasi-container');
                
                if (!meetingContainer || !window.Livewire) return;
                
                const action = form.getAttribute('action') || '';
                const submitter = e.submitter;
                const formaction = submitter ? submitter.getAttribute('formaction') : null;
                const finalAction = formaction || action;

                if (finalAction.includes('/disiplin/') && (finalAction.includes('/vote') || finalAction.includes('/approve') || finalAction.includes('/accept'))) {
                    e.preventDefault();
                    
                    const lw = Livewire.find(meetingContainer.getAttribute('wire:id'));
                    if (!lw) return;

                    const formData = new FormData(form);
                    const caseId = form.dataset.caseId;

                    if (!caseId) {
                        form.submit();
                        return;
                    }

                    if (finalAction.includes('/vote') && form.method.toUpperCase() === 'POST' && formData.get('_method') !== 'DELETE') {
                        lw.castCaseVote(caseId, formData.get('oy_yonu'), formData.get('yorum'));
                    }
                    else if (finalAction.includes('/vote') && (formData.get('_method') === 'DELETE' || finalAction.includes('DELETE'))) {
                        lw.deleteCaseVote(caseId);
                    }
                    else if (finalAction.includes('/approve') || finalAction.includes('/accept')) {
                        const type = finalAction.includes('/approve') ? 'approve' : 'accept';
                        lw.resolveCase(caseId, type, formData.get('yonetici_notu'));
                    }
                }
            });

            // [OTO-MODAL] Sayfa yüklendiğinde parametre varsa modalı aç
            window.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const reopenId = urlParams.get('reopen_modal');
                if (reopenId) {
                    const btn = document.querySelector(`[onclick*="open-modal"][onclick*="modal-vote-${reopenId}"]`) || 
                                document.querySelector(`button[data-modal-target="modal-vote-${reopenId}"]`);
                    
                    // Alpine.js modal tetikleyicisi için dispatch de deneyebiliriz
                    if (window.Alpine) {
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: `modal-vote-${reopenId}` }));
                    } else if (btn) {
                        btn.click();
                    }
                }
            });
        }
    </script>
    @endpush