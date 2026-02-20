<div class="space-y-8 animate-fade-in-up mb-16"> {{-- mb-16 ile alta boşluk bırakıldı --}}

    {{-- 1. ÜST BİLGİ KARTLARI (Kişisel + Hukuk Özeti) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Kişisel Karşılama Kartı --}}
        {{-- Kişisel Karşılama Kartı & Filtreleme Alanı --}}
        <div
            class="col-span-1 md:col-span-2 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden group flex flex-col justify-between">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110 duration-500">
            </div>

            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold mb-1">Hukuk Paneli</h3>
                        <p class="text-slate-300 text-sm">Hoşgeldiniz, {{ Auth::user()->name }}</p>
                    </div>
                    {{-- FİLTRELEME FORMU --}}
                    <form action="{{ route('dashboard') }}" method="GET"
                        class="flex items-center gap-2 bg-white/10 p-1.5 rounded-lg backdrop-blur-sm border border-white/10">
                        <input type="date" name="hukuk_start_date" value="{{ request('hukuk_start_date') }}"
                            class="bg-transparent border-0 text-white text-xs p-1 focus:ring-0 placeholder-slate-400 w-24">
                        <span class="text-slate-400 text-xs">-</span>
                        <input type="date" name="hukuk_end_date" value="{{ request('hukuk_end_date') }}"
                            class="bg-transparent border-0 text-white text-xs p-1 focus:ring-0 placeholder-slate-400 w-24">
                        <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white p-1.5 rounded-md transition-colors"
                            title="Filtrele">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                        </button>
                        @if(request('hukuk_start_date') || request('hukuk_end_date'))
                            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white p-1"
                                title="Filtreyi Temizle">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="flex items-center gap-4 mt-6">
                    <div class="bg-white/10 px-4 py-3 rounded-xl backdrop-blur-sm border border-white/5 w-full">
                        <span class="text-xs text-slate-400 block mb-1">Toplam Dosya (Seçili Dönem)</span>
                        <div class="flex items-baseline justify-between">
                            <span
                                class="text-3xl font-bold">{{ $stats['toplam_arabuluculuk'] + $stats['toplam_disiplin'] }}</span>
                            {{-- DETAYLI DAĞILIM --}}
                            <div class="flex gap-3 text-xs">
                                <span
                                    class="flex items-center gap-1.5 px-2 py-1 rounded bg-indigo-500/20 text-indigo-200 border border-indigo-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                    {{ $stats['toplam_arabuluculuk'] }} Arabuluculuk
                                </span>
                                <span
                                    class="flex items-center gap-1.5 px-2 py-1 rounded bg-rose-500/20 text-rose-200 border border-rose-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                    {{ $stats['toplam_disiplin'] }} Disiplin
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Arabuluculuk İstatistik Kartı --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-indigo-50 rounded-xl group-hover:bg-indigo-100 transition-colors">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3">
                        </path>
                    </svg>
                </div>
                <span class="flex items-center text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">
                    {{ $stats['aktif_arabuluculuk'] }} Aktif
                </span>
            </div>
            <h4 class="text-slate-500 text-sm font-medium">Arabuluculuk Dosyaları</h4>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-800">{{ $stats['toplam_arabuluculuk'] }}</span>
                <span class="text-xs text-slate-400">Toplam Kayıt</span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="text-orange-600 font-medium">{{ $stats['bekleyen_arabuluculuk'] }} Bekleyen İşlem</span>
                <a href="{{ route('admin.arabuluculuk.index') }}"
                    class="text-indigo-600 hover:text-indigo-800 font-medium">Dosyalar &rarr;</a>
            </div>
        </div>

        {{-- Disiplin İstatistik Kartı --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-rose-50 rounded-xl group-hover:bg-rose-100 transition-colors">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <span class="flex items-center text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-1 rounded-full">
                    {{ $stats['aktif_disiplin'] }} Aktif
                </span>
            </div>
            <h4 class="text-slate-500 text-sm font-medium">Disiplin Tutanakları</h4>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-800">{{ $stats['toplam_disiplin'] }}</span>
                <span class="text-xs text-slate-400">Toplam Kayıt</span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="text-orange-600 font-medium">{{ $stats['karar_bekleyen_disiplin'] }} Karar Bekleyen</span>
                <a href="{{ route('admin.disiplin.index') }}"
                    class="text-rose-600 hover:text-rose-800 font-medium">Tutanaklar &rarr;</a>
            </div>
        </div>
    </div>

    {{-- 2. DETAYLI LİSTELER (GRID YAPISI) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- SOL: Bekleyen Arabuluculuk Görevleri --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-full">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-800">Son Arabuluculuk İşlemleri</h3>
                    <p class="text-xs text-slate-400 mt-1">İşlem bekleyen veya aktif dosyalar</p>
                </div>
                <a href="{{ route('admin.arabuluculuk.index') }}"
                    class="text-xs font-medium text-indigo-600 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Tümünü
                    Gör</a>
            </div>
            <div class="p-4 flex-1">
                @if(isset($stats['bekleyen_arabuluculuk_listesi']) && count($stats['bekleyen_arabuluculuk_listesi']) > 0)
                    <div class="space-y-3">
                        @foreach($stats['bekleyen_arabuluculuk_listesi'] as $dosya)
                            <div
                                class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-100 transition-all group">
                                <div class="flex items-center gap-3">
                                    {{-- Avatar / Baş Harfler (Tooltip ile Detay) --}}
                                    <div class="relative group/avatar cursor-help">
                                        <div
                                            class="w-10 h-10 rounded-lg flex items-center justify-center bg-indigo-50 text-indigo-600 font-bold text-xs ring-2 ring-transparent group-hover/avatar:ring-indigo-100 transition-all">
                                            @if($dosya->calisan)
                                                {{ substr($dosya->calisan->name, 0, 1) }}{{ substr(explode(' ', $dosya->calisan->name)[1] ?? '', 0, 1) }}
                                            @else
                                                {{ substr($dosya->ad_soyad ?? '?', 0, 1) }}
                                            @endif
                                        </div>

                                        {{-- Detaylı Tooltip --}}
                                        <div
                                            class="absolute left-0 top-full mt-2 w-72 bg-slate-800 text-white text-xs rounded-lg p-3 shadow-xl opacity-0 invisible group-hover/avatar:opacity-100 group-hover/avatar:visible transition-all z-50 pointer-events-none">
                                            <div class="space-y-2">
                                                <div>
                                                    <span
                                                        class="text-slate-400 block text-[10px] uppercase tracking-wider mb-0.5">İlgili
                                                        Çalışan</span>
                                                    @if($dosya->calisan)
                                                        <span
                                                            class="font-semibold text-white block">{{ $dosya->calisan->name }}</span>
                                                        <span
                                                            class="text-slate-400 block text-[10px]">{{ $dosya->calisan->email }}</span>
                                                    @else
                                                        <span class="text-slate-300">{{ $dosya->ad_soyad }} (Sistem Dışı)</span>
                                                    @endif
                                                </div>
                                                <div class="h-px bg-slate-700"></div>
                                                <div>
                                                    <span
                                                        class="text-slate-400 block text-[10px] uppercase tracking-wider mb-0.5">Oluşturan</span>
                                                    <div class="flex justify-between items-center">
                                                        <span
                                                            class="font-semibold text-white">{{ $dosya->creator->name ?? 'Sistem' }}</span>
                                                        <span
                                                            class="text-[10px] text-slate-400">{{ $dosya->updated_at->format('d.m.Y H:i') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Ok işareti --}}
                                            <div class="absolute -top-1 left-4 w-2 h-2 bg-slate-800 rotate-45"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <h5
                                            class="text-sm font-semibold text-slate-800 group-hover:text-indigo-600 transition-colors">
                                            {{ $dosya->ad_soyad }}
                                        </h5>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] px-1.5 py-0.5 rounded 
                                                                            {{ $dosya->type == 'ZORUNLU' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }} 
                                                                            font-medium uppercase tracking-wide">
                                                {{ $dosya->type }}
                                            </span>
                                            <span
                                                class="text-xs text-slate-400">{{ $dosya->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @php
                                        $statusColors = [
                                            'taslak' => 'bg-slate-100 text-slate-600',
                                            'arabulucuda' => 'bg-indigo-100 text-indigo-700',
                                            'imza_asamasinda' => 'bg-yellow-100 text-yellow-700',
                                            'odeme_bekliyor' => 'bg-emerald-100 text-emerald-700',
                                            'kapatildi' => 'bg-green-100 text-green-700',
                                        ];
                                        $statusText = [
                                            'taslak' => 'Taslak',
                                            'arabulucuda' => 'Arabulucuda',
                                            'imza_asamasinda' => 'İmza Aşamasında',
                                            'odeme_bekliyor' => 'Ödeme Bekliyor',
                                            'kapatildi' => 'Tamamlandı',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-block px-2 py-1 rounded text-[10px] font-bold mb-1 {{ $statusColors[$dosya->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $statusText[$dosya->status] ?? $dosya->status }}
                                    </span>
                                    <a href="{{ route('admin.arabuluculuk.show', $dosya->id) }}"
                                        class="block text-[10px] font-medium text-indigo-600 hover:underline">İncele</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm">Bekleyen arabuluculuk dosyası bulunmuyor.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SAĞ: Disiplin Bölümü (İstatistikler + Liste) --}}
        <div class="space-y-6">

            {{-- Bölüm Dağılım İstatistikleri (YENİ) --}}
            @if(isset($stats['disiplin_bolum_dagilimi']) && count($stats['disiplin_bolum_dagilimi']) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        Bölüm Bazlı Disiplin Dağılımı
                    </h3>
                    <div class="space-y-3">
                        @foreach($stats['disiplin_bolum_dagilimi']->take(4) as $dagilim)
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-medium text-slate-700">{{ $dagilim->bolum_adi }}</span>
                                    <span class="font-bold text-slate-900">{{ $dagilim->dosya_sayisi }} Dosya</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5">
                                    <div class="bg-orange-500 h-1.5 rounded-full"
                                        style="width: {{ ($dagilim->dosya_sayisi / $stats['toplam_disiplin']) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Son Disiplin Hareketleri --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800">Son Disiplin Hareketleri</h3>
                        <p class="text-xs text-slate-400 mt-1">Son eklenen veya güncellenen tutanaklar</p>
                    </div>
                    <a href="{{ route('admin.disiplin.index') }}"
                        class="text-xs font-medium text-rose-600 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition-colors">Tümünü
                        Gör</a>
                </div>
                <div class="p-4">
                    @if(isset($stats['son_disiplin_vakalari']) && count($stats['son_disiplin_vakalari']) > 0)
                        <div class="space-y-3">
                            @foreach($stats['son_disiplin_vakalari'] as $vaka)
                                {{-- Tüm kart tıklanabilir (block link) --}}
                                <a href="{{ route('admin.disiplin.show', $vaka->id) }}" class="block">
                                    <div
                                        class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-white hover:shadow-md border border-transparent hover:border-slate-100 transition-all group cursor-pointer transform hover:-translate-y-0.5">
                                        <div class="flex items-center gap-3">
                                            {{-- Avatar --}}
                                            <div
                                                class="w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center overflow-hidden shrink-0">
                                                @if($vaka->user && $vaka->user->profile_photo_path)
                                                    <img src="{{ $vaka->user->profile_photo_path }}"
                                                        class="w-full h-full object-cover">
                                                @elseif($vaka->user)
                                                    <span
                                                        class="text-xs font-bold text-rose-600">{{ substr($vaka->user->name, 0, 1) }}</span>
                                                @else
                                                    <span class="text-xs font-bold text-slate-400">?</span>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <h5
                                                    class="text-sm font-bold text-slate-800 group-hover:text-rose-600 transition-colors truncate">
                                                    {{ $vaka->user ? $vaka->user->name : 'Bilinmiyor' }}
                                                </h5>
                                                <div class="flex flex-col">
                                                    {{-- Bölüm Bilgisi --}}
                                                    <span class="text-[10px] text-slate-500 font-medium">
                                                        {{ $vaka->user && $vaka->user->bolum ? $vaka->user->bolum->ad : 'Bölüm Yok' }}
                                                    </span>
                                                    {{-- Kategori --}}
                                                    <span class="text-[10px] text-slate-400 truncate max-w-[150px]">
                                                        {{ $vaka->behavior ? $vaka->behavior->name : 'Kategori Yok' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium 
                                                                {{ $vaka->durum == 'Karar Verildi' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                                {{ $vaka->durum }}
                                            </span>
                                            <span
                                                class="block text-[10px] text-slate-400 mt-1">{{ $vaka->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm">Kayıtlı disiplin vakası bulunmuyor.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>