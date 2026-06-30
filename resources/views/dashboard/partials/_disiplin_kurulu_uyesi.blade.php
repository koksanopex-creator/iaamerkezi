<div class="space-y-8 animate-fade-in-up mb-16">
    {{-- 1. ÜST BİLGİ KARTLARI --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Karşılama Kartı --}}
        <div class="col-span-1 md:col-span-2 bg-gradient-to-br from-slate-700 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden group flex flex-col justify-between">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110 duration-500"></div>
            <div class="relative z-10">
                <h3 class="text-2xl font-bold mb-1">Disiplin Kurulu Üyesi Paneli</h3>
                <p class="text-slate-300 text-sm">Hoşgeldiniz, disiplin kurulu üyesi</p>
                <div class="mt-8 flex items-center gap-4">
                    <div class="bg-white/10 px-4 py-3 rounded-xl backdrop-blur-sm border border-white/5 w-full">
                        <span class="text-xs text-slate-300 block mb-1">Toplam Dosya Sayısı</span>
                        <span class="text-3xl font-bold">{{ $stats['toplam_tutanak'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toplantı İstatistik Kartı --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-amber-50 rounded-xl group-hover:bg-amber-100 transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg animate-pulse">
                    {{ $stats['toplanti_bekleyen_sayisi'] ?? 0 }} Bekleyen
                </span>
            </div>
            <h4 class="text-slate-500 text-sm font-medium uppercase tracking-wider">Toplantılarım</h4>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-800">{{ $stats['toplanti_bekleyen_sayisi'] ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-medium">Katılım Bekleyen</span>
            </div>
        </div>

        {{-- Onay Bekleyen Kartı --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">
                    {{ $stats['karar_verilen_sayisi'] ?? 0 }} Tamamlanan
                </span>
            </div>
            <h4 class="text-slate-500 text-sm font-medium uppercase tracking-wider">Tamamlanan Toplantılar</h4>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-800">{{ $stats['karar_verilen_sayisi'] ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-medium">Toplam Karar</span>
            </div>
        </div>
    </div>

    {{-- 2. DETAYLI LİSTELER --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- SOL: Bekleyen Toplantılar --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-full">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm italic uppercase tracking-wider">Kurul Gündemindeki / Katılım Bekleyen Dosyalar</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Gündeminizdeki katılım bekleyen dosyalar</p>
                </div>
            </div>
            <div class="p-4 flex-1">
                @if(isset($stats['yaklasan_toplantilar']) && $stats['yaklasan_toplantilar']->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($stats['yaklasan_toplantilar'] as $vaka)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl hover:bg-white hover:shadow-md border border-transparent hover:border-slate-100 transition-all group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center justify-center text-indigo-600">
                                        <div class="text-center">
                                            <span class="block text-[10px] font-bold uppercase leading-none">{{ $vaka->toplanti_tarihi ? \Carbon\Carbon::parse($vaka->toplanti_tarihi)->format('M') : '-' }}</span>
                                            <span class="block text-lg font-black leading-tight">{{ $vaka->toplanti_tarihi ? \Carbon\Carbon::parse($vaka->toplanti_tarihi)->format('d') : '-' }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-bold text-slate-800">{{ $vaka->user->name ?? '-' }}</h5>
                                        <p class="text-xs text-slate-500 line-clamp-1">{{ $vaka->behavior->tanim ?? '-' }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($vaka->toplanti_tarihi)
                                                <span class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-1.5 py-0.5 rounded">{{ \Carbon\Carbon::parse($vaka->toplanti_tarihi)->format('H:i') }}</span>
                                            @endif
                                            <span class="text-[10px] text-slate-400 font-medium">{{ $vaka->user->bolum->ad ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    <a href="{{ route('admin.disiplin.show', $vaka->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-slate-400 bg-slate-50/50 rounded-2xl border-2 border-dashed border-slate-100">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-medium">Gündeminizde bekleyen toplantı bulunmuyor.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SAĞ: Bölüm Analizi & Son Hareketler --}}
        <div class="space-y-6">
            {{-- Bölüm Dağılım Listesi --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-slate-800 text-sm mb-6 flex items-center gap-2 uppercase tracking-wider">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Bölüm Bazlı Tutanak Dağılımı
                </h3>
                <div class="space-y-4">
                    @foreach(($stats['bolum_dagilimi'] ?? []) as $bolum)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-semibold text-slate-700">{{ $bolum->ad }}</span>
                            </div>
                            <span class="text-xs font-black text-slate-800">{{ $bolum->disciplinary_cases_count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Karar Verilen Son Dosyalar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2 uppercase tracking-wider">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Toplantısı Yapılmış Son Kararlarım
                </h3>
                <div class="space-y-4">
                    @forelse($stats['son_kararlar'] ?? [] as $karar)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-emerald-50 transition-colors border border-transparent hover:border-emerald-100 group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-emerald-600 shadow-sm font-bold text-xs uppercase">
                                    {{ substr($karar->user->name ?? '?', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $karar->user->name ?? '-' }}</p>
                                    <p class="text-[9px] text-slate-400 font-medium">{{ $karar->karar_tarihi ? \Carbon\Carbon::parse($karar->karar_tarihi)->format('d.m.Y') : '-' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.disiplin.show', $karar->id) }}" class="p-1 text-slate-300 group-hover:text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    @empty
                        <p class="text-[10px] text-slate-400 italic text-center py-2">Henüz sonuçlanmış bir karar bulunmuyor.</p>
                    @endforelse
                </div>
            </div>

            {{-- Son Hareketler --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2 uppercase tracking-wider">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Son Hareketler
                </h3>
                <div class="space-y-4">
                    @foreach(($stats['son_hareketler'] ?? collect())->take(5) as $hareket)
                        <div class="flex items-start gap-4 border-l-2 border-slate-100 pl-4 py-1">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $hareket->user->name ?? '-' }}</p>
                                <p class="text-[10px] text-slate-500 font-semibold leading-tight">{{ $hareket->durum }}</p>
                                <p class="text-[9px] text-slate-300">{{ $hareket->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
