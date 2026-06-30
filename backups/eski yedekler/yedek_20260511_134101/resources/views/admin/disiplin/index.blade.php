@push('pageTitle')
    Disiplin Dosyaları & Tutanaklar | 
@endpush

<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Disiplin Dosyaları & Tutanaklar
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Tüm disiplin süreçlerini görüntüleyin ve yönetin</p>
            </div>
            @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Bölüm Lideri', 'Hukuk Yöneticisi']))
                <a href="{{ route('admin.disiplin.create') }}"
                    class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg transition-all hover:shadow-xl active:scale-[0.98] flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Yeni Tutanak Oluştur
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Başarı Mesajı --}}
            @if(session('success'))
                <div class="mb-8 relative overflow-hidden bg-white rounded-3xl border border-emerald-100 shadow-2xl shadow-emerald-500/10 p-1 animate-fade-in-down" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center justify-between bg-emerald-50/50 rounded-[1.25rem] px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/40 transform rotate-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-emerald-900 font-black text-sm uppercase tracking-wider">İşlem Başarılı</h4>
                                <p class="text-emerald-700 text-sm font-medium mt-0.5">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="p-2 hover:bg-emerald-200/50 rounded-xl transition-colors text-emerald-400 hover:text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 bg-emerald-500 transition-all duration-[5000ms] ease-linear w-full" x-init="setTimeout(() => show = false, 5000)"></div>
                </div>
            @endif

            {{-- Kapsam Bilgisi --}}
            @if(isset($filterMessage) && $filterMessage)
                @php
                    $colorMap = [
                        'success' => ['bg-emerald-50 border-emerald-200', 'text-emerald-600', 'text-emerald-800'],
                        'info' => ['bg-blue-50 border-blue-200', 'text-blue-600', 'text-blue-800'],
                        'warning' => ['bg-amber-50 border-amber-200', 'text-amber-600', 'text-amber-800'],
                    ];
                    $cm = $colorMap[$filterType ?? 'info'];
                @endphp
                <div class="mb-6 {{ $cm[0] }} border rounded-2xl p-4 flex items-start gap-3 shadow-sm">
                    <div class="p-1.5 {{ $cm[1] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm {{ $cm[2] }}">Görüntüleme Kapsamı</p>
                        <p class="text-sm {{ $cm[2] }} opacity-80">{{ $filterMessage }}</p>
                    </div>
                </div>
            @endif

            {{-- ==================== İSTATİSTİK KARTLARI ==================== --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                {{-- Toplam --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-red-100 p-2.5 rounded-xl text-red-600 group-hover:bg-red-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-gray-900">{{ $stats['toplam'] }}</div>
                    </div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Toplam Tutanak</div>
                    <div class="flex gap-2 mt-2">
                        <span
                            class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 font-bold">{{ $stats['taslak'] }}
                            Taslak</span>
                        <span
                            class="text-[10px] px-1.5 py-0.5 rounded bg-red-50 text-red-600 font-bold">{{ $stats['iptal'] }}
                            İptal</span>
                    </div>
                </div>

                {{-- Savunma Bekleyen --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-amber-100 p-2.5 rounded-xl text-amber-600 group-hover:bg-amber-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-amber-600">{{ $stats['savunma_bekleyen'] }}</div>
                    </div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Savunma Bekleyen</div>
                    <div class="mt-2">
                        @if($stats['savunma_bekleyen'] > 0)
                            <span
                                class="text-[10px] px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-bold animate-pulse">⚠
                                İşlem Gerekiyor</span>
                        @else
                            <span class="text-[10px] text-gray-400">Bekleyen yok</span>
                        @endif
                    </div>
                </div>

                {{-- İnceleme Bekleyen --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-purple-100 p-2.5 rounded-xl text-purple-600 group-hover:bg-purple-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-purple-600">{{ $stats['inceleme_bekleyen'] ?? 0 }}</div>
                    </div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">İnceleme Bekleyen</div>
                    <div class="mt-2">
                        @if(($stats['inceleme_bekleyen'] ?? 0) > 0)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 font-bold">Hukuk Onayı Bekliyor</span>
                        @else
                            <span class="text-[10px] text-gray-400">Bekleyen yok</span>
                        @endif
                    </div>
                </div>

                {{-- Kurulda --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-blue-100 p-2.5 rounded-xl text-blue-600 group-hover:bg-blue-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-blue-600">{{ $stats['kurulda'] }}</div>
                    </div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kurulda</div>
                    <div class="mt-2">
                        <span
                            class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold">{{ $stats['karar_verildi'] }}
                            Bitti</span>
                    </div>
                </div>

                {{-- Ortalama Puan --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="bg-purple-100 p-2.5 rounded-xl text-purple-600 group-hover:bg-purple-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-purple-700">{{ $stats['ortalama_puan'] }}</div>
                    </div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ort. Puan</div>
                    <div class="mt-2">
                        <span class="text-[10px] text-gray-400">Hesaplanan ortalama matris puanı</span>
                    </div>
                </div>
            </div>

            {{-- ==================== FİLTRELER ==================== --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6"
                x-data="{ showFilters: {{ request()->hasAny(['search', 'durum', 'date_from', 'date_to']) ? 'true' : 'false' }} }">
                <div class="px-6 py-3 flex items-center justify-between cursor-pointer"
                    @click="showFilters = !showFilters">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="text-sm font-bold text-gray-700">Filtreler</span>
                        @if(request()->hasAny(['search', 'durum', 'date_from', 'date_to']))
                            <span
                                class="bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded-full font-bold">Aktif</span>
                        @endif
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="showFilters ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div x-show="showFilters" x-transition x-cloak class="px-6 pb-5 border-t border-gray-100 pt-4">
                    <form method="GET" action="{{ route('admin.disiplin.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            {{-- Arama --}}
                            <div class="lg:col-span-2">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Personel
                                    Ara</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="İsim veya e-posta...">
                                </div>
                            </div>

                            {{-- Durum --}}
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Durum</label>
                                <select name="durum"
                                    class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="">Tümü</option>
                                    <option value="Taslak" {{ request('durum') == 'Taslak' ? 'selected' : '' }}>Taslak
                                    </option>
                                    <option value="Savunma Bekleniyor" {{ request('durum') == 'Savunma Bekleniyor' ? 'selected' : '' }}>Savunma Bekleniyor</option>
                                    <option value="Yönetici Değerlendirmesi" {{ request('durum') == 'Yönetici Değerlendirmesi' ? 'selected' : '' }}>İnceleme Bekleyen</option>
                                    <option value="Kurulda" {{ request('durum') == 'Kurulda' ? 'selected' : '' }}>Kurulda</option>
                                    <option value="Karar Verildi" {{ request('durum') == 'Karar Verildi' ? 'selected' : '' }}>Karar Verildi</option>
                                    <option value="İptal Edildi" {{ request('durum') == 'İptal Edildi' ? 'selected' : '' }}>İptal Edildi</option>
                                </select>
                            </div>

                            {{-- Tarih Başlangıç --}}
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Başlangıç</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>

                            {{-- Tarih Bitiş --}}
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Bitiş</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                    class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-4">
                            <a href="{{ route('admin.disiplin.index') }}"
                                class="text-sm text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Temizle
                            </a>
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-2 px-5 rounded-lg transition flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Filtrele
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ==================== TABLO ==================== --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th
                                    class="px-6 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    No / Raporlayan</th>
                                <th
                                    class="px-6 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    Personel</th>
                                <th
                                    class="px-6 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    Suç / İhlal</th>
                                <th
                                    class="px-6 py-3.5 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    Puan (Öneri)</th>
                                <th
                                    class="px-6 py-3.5 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    Durum</th>
                                <th
                                    class="px-6 py-3.5 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($cases as $case)
                                @php
                                    $durumConfig = match ($case->durum) {
                                        'Taslak' => ['bg-gray-50 text-gray-700 border-gray-200', 'gray'],
                                        'Savunma Bekleniyor' => ['bg-amber-50 text-amber-700 border-amber-200', 'amber'],
                                        'Kurul İncelemesinde', 'Kurulda' => ['bg-blue-50 text-blue-700 border-blue-200', 'blue'],
                                        'Yönetici Değerlendirmesi' => ['bg-purple-50 text-purple-700 border-purple-200', 'purple'],
                                        'Karar Verildi' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'emerald'],
                                        'İptal Edildi' => ['bg-rose-50 text-rose-700 border-rose-200', 'rose'],
                                        default => ['bg-gray-50 text-gray-600 border-gray-200', 'gray']
                                    };
                                    $matrisBilgi = "Etki: " . ($case->impact->puan ?? '?') . " | Kapsam: " . ($case->scope->puan ?? '?') . " | Tekrar: " . $case->tekrar_sayisi . ". Kez";
                                @endphp
                                <tr class="hover:bg-red-50/20 transition-all group">
                                    {{-- No / Raporlayan --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">#{{ $case->id }}</div>
                                        <div class="text-[10px] text-gray-400 flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ Str::limit($case->reporter->name ?? '?', 15) }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $case->created_at->format('d.m.Y') }}
                                        </div>
                                    </td>

                                    {{-- Personel --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-9 w-9 rounded-full bg-red-100 flex items-center justify-center text-red-700 font-bold text-xs uppercase">
                                                {{ substr($case->user->name, 0, 2) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-semibold text-gray-900">{{ $case->user->name }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $case->user->bolum->ad ?? 'Bölümsüz' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Suç / İhlal --}}
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-semibold line-clamp-1"
                                            title="{{ $case->behavior->tanim ?? '' }}">
                                            {{ Str::limit($case->behavior->tanim ?? 'Silinmiş Kayıt', 35) }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $case->behavior->category->ad ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Puan --}}
                                    <td class="px-6 py-4 text-center cursor-help" title="{{ $matrisBilgi }}">
                                        <div
                                            class="inline-flex flex-col items-center bg-gray-50 rounded-xl px-3 py-1.5 border border-gray-100">
                                            <span
                                                class="text-lg font-black text-gray-800">{{ $case->hesaplanan_puan }}</span>
                                            <span
                                                class="text-[9px] text-indigo-600 font-bold uppercase tracking-wide mt-0.5">{{ $case->sistem_oneri_ceza }}</span>
                                        </div>
                                    </td>

                                    {{-- Durum --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-lg border {{ $durumConfig[0] }}">
                                            {{ $case->durum }}
                                        </span>
                                        @if($case->durum == 'Savunma Bekleniyor')
                                            <div
                                                class="text-[9px] text-red-600 mt-1.5 animate-pulse font-extrabold flex justify-center items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                İŞLEM BEKLİYOR
                                            </div>
                                        @elseif($case->durum == 'Yönetici Değerlendirmesi' && Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Disiplin Kurulu Başkanı']))
                                            <div
                                                class="text-[9px] text-rose-600 mt-1.5 animate-pulse font-black flex justify-center items-center gap-1 bg-rose-50 rounded-full px-2 py-0.5 border border-rose-100">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="12"/>
                                                </svg>
                                                DEĞERLENDİRME BEKLİYOR
                                            </div>
                                        @endif
                                    </td>

                                    {{-- İşlemler --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @php
                                            $isSuperAdmin = Auth::user()->hasRole('Superadmin');
                                            $isReporter = Auth::id() == $case->reporter_id;
                                            
                                            // Savunma girilmiş mi? (savunma_tarihi doluluğu en net gösterge)
                                            $hasDefense = (bool) $case->savunma_tarihi;
                                            
                                            // Düzenleme Yetkisi:
                                            // 1. Superadmin her zaman düzenleyebilir.
                                            // 2. Diğerleri: SADECE kaydı oluşturan kişi VE savunma henüz girilmemişse.
                                            $canEdit = $isSuperAdmin || ($isReporter && !$hasDefense);
                                            
                                            // Silme Yetkisi: Düzenleme ile aynı mantık (veya isterseniz farklılaştırılabilir ama kullanıcı ikisi için de benzer kısıtlama istedi)
                                            $canDelete = $isSuperAdmin || ($isReporter && !$hasDefense);
                                        @endphp
                                        <div class="flex justify-end gap-1.5">
                                            <a href="{{ route('admin.disiplin.show', $case->id) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold hover:bg-indigo-100 transition">
                                                Detay
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>

                                            @if($canEdit)
                                                <a href="{{ route('admin.disiplin.edit', $case->id) }}"
                                                    class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold hover:bg-blue-100 transition"
                                                    title="Düzenle">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            @endif

                                            @if($canDelete)
                                                <form action="{{ route('admin.disiplin.destroy', $case->id) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button"
                                                        onclick="if(confirm('Bu tutanağı silmek istediğinize emin misiniz?')) this.closest('form').submit();"
                                                        class="inline-flex items-center px-2.5 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100 transition"
                                                        title="Sil">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="bg-gray-100 p-4 rounded-2xl mb-4">
                                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 font-medium">Kayıt bulunamadı</p>
                                            <p class="text-gray-400 text-sm mt-1">Filtre kriterlerinizi değiştirmeyi deneyin
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Sayfalama --}}
                @if($cases->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $cases->links() }}
                    </div>
                @endif

                {{-- Sonuç bilgisi --}}
                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/30 flex items-center justify-between">
                    <span class="text-xs text-gray-400">
                        Toplam <span class="font-bold text-gray-600">{{ $cases->total() }}</span> kayıttan
                        <span
                            class="font-bold text-gray-600">{{ $cases->firstItem() ?? 0 }}-{{ $cases->lastItem() ?? 0 }}</span>
                        arası gösteriliyor
                    </span>
                    <span class="text-xs text-gray-400">Sayfa {{ $cases->currentPage() }} /
                        {{ $cases->lastPage() }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>