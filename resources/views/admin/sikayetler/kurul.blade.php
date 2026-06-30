<x-app-layout>
    @push('pageTitle')Kurul Girdileri Raporu | @endpush
    <x-slot name="header">
        <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-tight flex items-center gap-3">
            <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
            {{ __('Kurul Girdileri Raporu') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- SEKMELİ BAŞLIK VE ÖZET --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Kurul Performans Takibi</h1>
                    <p class="text-slate-500 font-medium mt-1">Kurul üyeleri tarafından sisteme girilen tüm müşteri şikayetlerinin detaylı analizi.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                {{-- SOL TARAF: FİLTRE VE KURUL LİSTESİ --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- FİLTRELEME --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-50 bg-slate-50/50">
                            <h3 class="font-black text-slate-800 text-xs uppercase tracking-widest">Hızlı Filtre</h3>
                        </div>
                        <div class="p-5">
                            <form method="GET" action="{{ route('admin.sikayetler.kurulGirdileri') }}" class="space-y-4">
                                <div>
                                    <label for="kullanici_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Kurul Üyesi Seç</label>
                                    <select name="kullanici_id" id="kullanici_id" class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold text-slate-700">
                                        @if(auth()->user()->hasRole('Superadmin'))
                                            <option value="all" @if($selectedUserId == 'all') selected @endif>Tüm Kurul Girdileri</option>
                                        @else
                                            <option value="{{ auth()->id() }}" @if($selectedUserId == auth()->id()) selected @endif>Benim Girdiklerim</option>
                                            <option value="all" @if($selectedUserId == 'all') selected @endif>Tüm Kurul Girdileri</option>
                                        @endif
                                        @foreach($kurulUyeleri as $uye)
                                            @if($uye->id != auth()->id()) 
                                                <option value="{{ $uye->id }}" @if($selectedUserId == $uye->id) selected @endif>{{ $uye->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                                    Süzgeci Uygula
                                </button>
                                <button type="button" onclick="window.location.href='{{ route('admin.sikayetler.kurulGirdileri') }}'" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition">
                                    Sıfırla
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- KURUL ÜYELERİ MİNİ TABLO --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-50 bg-amber-50/30">
                            <h3 class="font-black text-amber-900 text-xs uppercase tracking-widest">Kurul Üyeleri Katılımı</h3>
                        </div>
                        <div class="overflow-hidden">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/50">
                                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                        <th class="px-4 py-2">Üye</th>
                                        <th class="px-4 py-2 text-right">Girdi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($kurulUyeleri as $uye)
                                        <tr class="hover:bg-indigo-50 transition-colors cursor-pointer {{ $selectedUserId == $uye->id ? 'bg-indigo-50/80 border-l-4 border-indigo-600' : '' }}"
                                            onclick="window.location.href='{{ route('admin.sikayetler.kurulGirdileri', ['kullanici_id' => $uye->id]) }}'">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <img src="{{ $uye->profile_photo_url }}" class="w-6 h-6 rounded-full border border-slate-100" alt="">
                                                    <span class="text-[11px] font-bold text-slate-700">{{ $uye->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex flex-col items-end">
                                                    <span class="text-[10px] font-black text-indigo-600">{{ (int)$uye->girdigi_sikayetler_count }} Şikayet</span>
                                                    <span class="text-[8px] font-bold text-slate-400">{{ number_format($uye->girdigi_sikayetler_sum_kazanilan_puan ?? 0) }} Puan</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- SAĞ TARAF: İSTATİSTİKLER VE LİSTE --}}
                <div class="lg:col-span-3 space-y-6">
                    
                    {{-- KİŞİSEL İSTATİSTİKLER (Sadece Kurul Üyeleri İçin) --}}
                    @unless(auth()->user()->hasRole('Superadmin'))
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl shadow-xl p-6 text-white overflow-hidden relative group">
                        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-6 opacity-70">Benim Katkım</h3>
                        <div class="grid grid-cols-3 gap-8 relative z-10">
                            <div>
                                <p class="text-3xl font-black">{{ $stats_kisisel['toplam_benim_girdiklerim'] ?? 0 }}</p>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mt-1">Toplam Girdi</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-amber-300">{{ $stats_kisisel['islemde_benim_girdiklerim'] ?? 0 }}</p>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mt-1">İşlemde Olan</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-emerald-400">{{ $stats_kisisel['cozulen_benim_girdiklerim'] ?? 0 }}</p>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mt-1">Çözülen</p>
                            </div>
                        </div>
                    </div>
                    @endunless

                    {{-- FİLTRELENEN VERİ ÖZETİ --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                            <h3 class="font-black text-slate-800 uppercase tracking-tight text-sm italic">
                                @if($selectedUserId == 'all')
                                    Genel Kurul Analizi (Tüm Üyeler)
                                @else
                                    {{ $kurulUyeleri->find($selectedUserId)->name ?? 'Seçili Üye' }} - Analiz Verileri
                                @endif
                            </h3>
                            <span class="text-[10px] font-black bg-slate-100 px-3 py-1 rounded-full text-slate-500">{{ $stats_filtrelenmis['toplam'] }} TOPLAM KAYIT</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-slate-100">
                            <div class="bg-white p-6">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Kategori Dağılımı</p>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($stats_filtrelenmis['kategoriler'] as $kategori)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-tight">
                                            {{ $kategori->ad }} <span class="ml-2 opacity-50">{{ $kategori->toplam }}</span>
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">Veri bulunamadı.</span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="bg-white p-6 flex flex-col justify-center">
                                <div class="flex items-center gap-6">
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-amber-600">{{ $stats_filtrelenmis['islemde'] }}</p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">İşlemde</p>
                                    </div>
                                    <div class="w-px h-8 bg-slate-100"></div>
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-emerald-600">{{ $stats_filtrelenmis['cozulen'] }}</p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Çözülen</p>
                                    </div>
                                    <div class="w-px h-8 bg-slate-100"></div>
                                    <div class="text-center">
                                        @php $successRate = $stats_filtrelenmis['toplam'] > 0 ? round(($stats_filtrelenmis['cozulen'] / $stats_filtrelenmis['toplam']) * 100) : 0; @endphp
                                        <p class="text-2xl font-black text-indigo-600">{{ $successRate }}%</p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Başarı</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ŞİKAYET LİSTESİ --}}
                    <div class="space-y-4">
                        @forelse ($sikayetler as $sikayet)
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
                                <div class="p-5">
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                                        <div class="flex items-center gap-3">
                                            <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-2 py-1 rounded border border-slate-100 tracking-widest uppercase">ID: #{{ $sikayet->id }}</span>
                                            <div class="font-black text-slate-800 text-lg tracking-tight">{{ $sikayet->musteri_adi }}</div>
                                        </div>
                                        <div class="scale-90 origin-right">
                                            {!! $sikayet->musteri_durum_badge !!}
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-slate-600 mb-6 line-clamp-2" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                        {{ $sikayet->musteri_sikayet_konusu }}
                                    </p>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-slate-50">
                                        <div class="space-y-1">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Kategori</p>
                                            <p class="text-[11px] font-bold text-slate-700">{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Sorumlu Takım</p>
                                            <p class="text-[11px] font-bold text-slate-700">{{ $sikayet->cozumTakimi->ad ?? 'Atanmadı' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Oluşturan</p>
                                            <div class="flex items-center gap-2" title="E-posta: {{ $sikayet->olusturanKurulUyesi->email }} {{ $sikayet->olusturanKurulUyesi->telefon ? ' | Tel: ' . $sikayet->olusturanKurulUyesi->telefon : '' }}">
                                                @if($sikayet->olusturanKurulUyesi)
                                                    <img src="{{ $sikayet->olusturanKurulUyesi->profile_photo_url }}" class="w-4 h-4 rounded-full" alt="">
                                                    <p class="text-[11px] font-bold text-slate-700">{{ $sikayet->olusturanKurulUyesi->name }}</p>
                                                @else
                                                    <div class="w-4 h-4 rounded-full bg-slate-100 flex items-center justify-center text-[8px] font-black text-slate-400">S</div>
                                                    <p class="text-[11px] font-bold text-slate-400 italic">Sistem / Dış Kaynak</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.sikayetler.show', $sikayet) }}" class="p-2 bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            @can('update', $sikayet)
                                                <a href="{{ route('admin.sikayetler.edit', $sikayet) }}" class="p-2 bg-slate-50 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                            @endcan
                                            @can('delete', $sikayet)
                                                <form action="{{ route('admin.sikayetler.destroy', $sikayet) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 bg-slate-50 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
                                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Bu kritere uygun kayıt bulunamadı.</p>
                            </div>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                {{ $sikayetler->links() }}
            </div>
        </div>
    </div>
</x-app-layout>