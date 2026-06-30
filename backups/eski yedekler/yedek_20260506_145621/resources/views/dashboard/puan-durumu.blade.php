@push('pageTitle')
    İAA Puan Durumu & Sıralama | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Puan Durumu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Global Tarih Filtresi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100">
                    <form method="GET" action="{{ route('puan-durumu') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Başlangıç Tarihi</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Bitiş Tarihi</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-indigo-700 transition shadow-sm">
                                Filtrele
                            </button>
                            <a href="{{ route('puan-durumu') }}"
                                class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md text-sm font-bold hover:bg-gray-200 transition">
                                Sıfırla
                            </a>
                        </div>
                        <div class="ml-auto flex gap-2">
                            @php
                                $today = \Carbon\Carbon::today();
                                $thisWeekStart = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
                                $thisMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                            @endphp
                            <a href="{{ route('puan-durumu', ['start_date' => $thisWeekStart]) }}" 
                                class="text-xs px-3 py-1.5 rounded-full {{ request('start_date') == $thisWeekStart ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-gray-50 text-gray-600 border-gray-200' }} border font-medium hover:bg-indigo-50 transition">
                                Bu Hafta
                            </a>
                            <a href="{{ route('puan-durumu', ['start_date' => $thisMonthStart]) }}" 
                                class="text-xs px-3 py-1.5 rounded-full {{ request('start_date') == $thisMonthStart ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-gray-50 text-gray-600 border-gray-200' }} border font-medium hover:bg-indigo-50 transition">
                                Bu Ay
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Hızlı Kaydırma Butonları -->
            <div class="flex flex-wrap gap-2 lg:gap-3">
                <a href="#iaa-takimlari" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-2xl text-xs font-black text-indigo-700 hover:bg-indigo-50 hover:border-indigo-200 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 tracking-tight uppercase">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    İAA Takımları
                </a>
                <a href="#sikayet-takimlari" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-2xl text-xs font-black text-blue-700 hover:bg-blue-50 hover:border-blue-200 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 tracking-tight uppercase">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Şikayet Takımları
                </a>
                <a href="#personel-siralamasi" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-2xl text-xs font-black text-green-700 hover:bg-green-50 hover:border-green-200 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 tracking-tight uppercase">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    En Başarılı Personeller
                </a>
                <a href="#bolum-siralamasi" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-2xl text-xs font-black text-purple-700 hover:bg-purple-50 hover:border-purple-200 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 tracking-tight uppercase">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Bölüm Sıralaması
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sol Kolon: Takım Sıralamaları -->
                <div class="space-y-6">

                    <!-- 1. İAA Takımları -->
                    <div id="iaa-takimlari" class="bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-24">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                <h3 class="font-bold text-lg text-indigo-700">En Başarılı Takımlar (İAA)</h3>
                            </div>

                            <!-- Filtre Formu -->
                            <form method="GET" action="{{ route('puan-durumu') }}"
                                class="mb-4 grid grid-cols-2 gap-2 text-sm">
                                <input type="text" name="iaa_team_name" value="{{ request('iaa_team_name') }}"
                                    placeholder="Takım Adı"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-8 text-xs">
                                <input type="text" name="iaa_team_leader" value="{{ request('iaa_team_leader') }}"
                                    placeholder="Lider"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-8 text-xs">
                                <div class="col-span-2 flex gap-2">
                                    <input type="number" name="iaa_min_score" value="{{ request('iaa_min_score') }}"
                                        placeholder="Min Puan"
                                        class="w-1/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-8 text-xs">
                                    <input type="number" name="iaa_max_score" value="{{ request('iaa_max_score') }}"
                                        placeholder="Max Puan"
                                        class="w-1/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-8 text-xs">
                                    <button type="submit"
                                        class="flex-1 bg-indigo-500 text-white rounded-md text-xs font-bold hover:bg-indigo-600 transition">Filtrele</button>
                                </div>
                                <!-- Diğer filtreleri korumak için -->
                                @foreach(request()->except(['iaa_team_name', 'iaa_team_leader', 'iaa_min_score', 'iaa_max_score']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                            </form>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Sıra</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Takım</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Lider</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($iaaTakimlari as $index => $team)
                                        <tr
                                            class="hover:bg-indigo-50/30 transition {{ $index < 3 ? 'bg-yellow-50/50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                @if($index == 0) <span class="text-yellow-500 text-lg">🥇</span> @endif
                                                @if($index == 1) <span class="text-gray-400 text-lg">🥈</span> @endif
                                                @if($index == 2) <span class="text-orange-400 text-lg">🥉</span> @endif
                                                <span
                                                    class="ml-1 font-bold {{ $index < 3 ? 'text-yellow-600' : 'text-gray-500' }}">{{ $index + 1 }}.</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('takim-puanlari', $team->id) }}"
                                                    class="text-sm font-bold text-gray-900 hover:text-indigo-600 block">
                                                    {{ $team->ad }}
                                                </a>
                                                <div class="text-xs text-gray-500">
                                                    {{ number_format($team->toplam_puan, 0) }} Puan
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    @if($team->lider && $team->lider->profile_photo_path)
                                                        <img class="h-8 w-8 rounded-full object-cover mr-2 border border-gray-200"
                                                            src="{{ asset('storage/' . $team->lider->profile_photo_path) }}"
                                                            alt="">
                                                    @endif
                                                    <a href="{{ $team->lider ? route('profile.show', $team->lider->id) : '#' }}"
                                                        class="hover:text-indigo-600 hover:underline">
                                                        {{ $team->lider->name ?? '-' }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. Şikayet Takımları -->
                    <div id="sikayet-takimlari" class="bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-24">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <h3 class="font-bold text-lg text-blue-700">Müşteri Şikayeti Çözüm Takımları</h3>
                            </div>

                            <!-- Filtre Formu -->
                            <form method="GET" action="{{ route('puan-durumu') }}"
                                class="mb-4 grid grid-cols-2 gap-2 text-sm">
                                <input type="text" name="sikayet_team_name" value="{{ request('sikayet_team_name') }}"
                                    placeholder="Takım Adı"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-8 text-xs">
                                <input type="text" name="sikayet_team_leader"
                                    value="{{ request('sikayet_team_leader') }}" placeholder="Lider"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-8 text-xs">
                                <div class="col-span-2 flex gap-2">
                                    <input type="number" name="sikayet_min_score"
                                        value="{{ request('sikayet_min_score') }}" placeholder="Min Puan"
                                        class="w-1/3 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-8 text-xs">
                                    <input type="number" name="sikayet_max_score"
                                        value="{{ request('sikayet_max_score') }}" placeholder="Max Puan"
                                        class="w-1/3 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-8 text-xs">
                                    <button type="submit"
                                        class="flex-1 bg-blue-500 text-white rounded-md text-xs font-bold hover:bg-blue-600 transition">Filtrele</button>
                                </div>
                                <!-- Diğer filtreleri korumak için -->
                                @foreach(request()->except(['sikayet_team_name', 'sikayet_team_leader', 'sikayet_min_score', 'sikayet_max_score']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                            </form>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Sıra</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Takım</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                            Lider</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($sikayetTakimlari as $index => $team)
                                        <tr
                                            class="hover:bg-blue-50/30 transition {{ $index < 3 ? 'bg-yellow-50/50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <span
                                                    class="font-bold {{ $index < 3 ? 'text-yellow-600' : 'text-gray-500' }}">{{ $index + 1 }}.</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('takim-puanlari', $team->id) }}"
                                                    class="text-sm font-bold text-gray-900 hover:text-indigo-600 block">
                                                    {{ $team->ad }}
                                                </a>
                                                <div class="text-xs text-blue-600 font-bold">
                                                    {{ number_format($team->toplam_puan, 0) }} Puan
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    @if($team->lider && $team->lider->profile_photo_path)
                                                        <img class="h-8 w-8 rounded-full object-cover mr-2 border border-gray-200"
                                                            src="{{ asset('storage/' . $team->lider->profile_photo_path) }}"
                                                            alt="">
                                                    @endif
                                                    <a href="{{ $team->lider ? route('profile.show', $team->lider->id) : '#' }}"
                                                        class="hover:text-indigo-600 hover:underline">
                                                        {{ $team->lider->name ?? '-' }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: En Başarılı Personeller -->
                <div id="personel-siralamasi" class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full scroll-mt-24">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <h3 class="font-bold text-lg text-green-700">En Başarılı Personeller</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör', 'Bölüm Lideri']))
                                    <a href="{{ route('puan.raporu') }}"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-lg text-xs font-bold transition-all duration-200 shadow-sm group">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <span>Puan Analiz Raporu</span>
                                    </a>
                                @endif

                                <a href="{{ route('tum-personel') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200 rounded-lg text-xs font-bold transition-all duration-200 shadow-sm group">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    <span>Tümünü Gör</span>
                                </a>
                            </div>
                        </div>

                        <!-- Kendi Puanım Bölümü -->
                        @if(isset($currentUser) && $currentUser->is_personnel)
                        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        @if($currentUser->profile_photo_path)
                                            <img class="h-12 w-12 rounded-full object-cover border-2 border-green-400" src="{{ asset('storage/' . $currentUser->profile_photo_path) }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold border-2 border-green-400 text-lg uppercase">
                                                {{ substr($currentUser->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="absolute -bottom-1 -right-1 bg-green-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold border border-white">SİZ</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-green-800 uppercase tracking-wider">Senin Puanın</div>
                                        <div class="text-sm font-bold text-gray-900">{{ $currentUser->name }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $currentUser->bolum->ad ?? 'Bölüm Yok' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-black text-green-700">{{ number_format($currentUser->period_puan ?? $currentUser->toplam_puan, 0) }}</div>
                                    <div class="text-[10px] font-bold text-green-600 uppercase">Toplam Puan</div>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between border-t border-green-100 pt-3">
                                <a href="{{ route('profile.puanlar', ['user' => $currentUser->id, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="text-[10px] font-bold text-green-700 hover:text-green-900 underline uppercase">Puan Detaylarını Gör</a>
                                <div class="text-[10px] italic text-gray-400">Puanlar periyodik olarak güncellenir.</div>
                            </div>
                        </div>
                        @endif

                        <!-- Filtre Formu -->
                        <form method="GET" action="{{ route('puan-durumu') }}"
                            class="mb-4 grid grid-cols-2 gap-2 text-sm">
                            <input type="text" name="user_name" value="{{ request('user_name') }}"
                                placeholder="Personel Adı"
                                class="border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 h-8 text-xs">
                            <input type="text" name="user_bolum" value="{{ request('user_bolum') }}" placeholder="Bölüm"
                                class="border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 h-8 text-xs">
                            <div class="col-span-2 flex gap-2">
                                <input type="number" name="user_min_score" value="{{ request('user_min_score') }}"
                                    placeholder="Min Puan"
                                    class="w-1/3 border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 h-8 text-xs">
                                <input type="number" name="user_max_score" value="{{ request('user_max_score') }}"
                                    placeholder="Max Puan"
                                    class="w-1/3 border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 h-8 text-xs">
                                <button type="submit"
                                    class="flex-[2] bg-green-500 text-white rounded-md text-xs font-bold hover:bg-green-600 transition shadow-md shadow-green-100">Filtrele</button>
                                <a href="{{ route('puan-durumu') }}"
                                    class="flex-1 bg-gray-100 text-gray-700 rounded-md text-xs font-bold hover:bg-gray-200 transition flex items-center justify-center border border-gray-200">Temizle</a>
                            </div>
                            <!-- Diğer filtreleri korumak için -->
                            @foreach(request()->except(['user_name', 'user_bolum', 'user_min_score', 'user_max_score']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Sıra</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Personel</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Bölüm</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Puan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($topUsers as $index => $user)
                                    <tr class="hover:bg-green-50/30 transition {{ $index < 3 ? 'bg-green-50/20' : '' }}">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            @if($index == 0) <span class="text-yellow-500 text-base">🥇</span> @endif
                                            @if($index == 1) <span class="text-gray-400 text-base">🥈</span> @endif
                                            @if($index == 2) <span class="text-orange-400 text-base">🥉</span> @endif
                                            <span
                                                class="ml-0.5 font-bold {{ $index < 3 ? 'text-yellow-600' : 'text-gray-500' }} text-xs">{{ $index + 1 }}.</span>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($user->profile_photo_path)
                                                    <img class="h-8 w-8 rounded-full object-cover mr-2 border-2 {{ $index < 3 ? 'border-yellow-400' : 'border-gray-100' }}"
                                                        src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="">
                                                @else
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold mr-2 border border-gray-200 text-xs">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <a href="{{ route('profile.show', $user->id) }}"
                                                    class="text-xs font-bold text-gray-900 hover:text-indigo-600 hover:underline">
                                                    {{ $user->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $user->bolum->ad ?? '-' }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                            <a href="{{ route('profile.puanlar', ['user' => $user->id, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                                class="hover:text-green-800 hover:underline block w-full h-full">
                                                {{ number_format($user->period_puan ?? $user->toplam_puan, 0) }}
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- YENİ: Bölüm Puan Sıralaması -->
            <div id="bolum-siralamasi" class="bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-24" x-data="{ showAllBolum: false }">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h3 class="font-bold text-lg text-purple-700">Bölüm Başarı Sıralaması</h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('tum-bolum-puanlari') }}"
                            class="inline-flex items-center px-4 py-2 bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 rounded-lg text-xs font-black transition-all duration-200 shadow-sm group">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>Tüm Bölümler Analizi</span>
                        </a>
                        <div class="text-[10px] text-gray-400 italic">Bölüm puanı = Personellerin dönem toplamı</div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase w-12">Sıra</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Bölüm</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Bölüm Lideri</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Bölüm 1.si</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Toplam Puan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($bolumPuanListesi as $index => $item)
                                <tr class="hover:bg-purple-50/30 transition border-l-4 {{ $index < 3 ? 'border-purple-400' : 'border-transparent' }}" x-show="showAllBolum || ({{ $index }} < 5 && {{ $item->total_score }} > 0)" x-cloak>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-black text-gray-400">
                                        #{{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if($item->logo_yolu)
                                                <img src="{{ asset('storage/'.$item->logo_yolu) }}" class="w-10 h-10 rounded-xl object-contain shadow-sm bg-gray-50 p-1">
                                            @else
                                                @php
                                                    $colors = ['from-purple-500 to-indigo-600', 'from-blue-500 to-cyan-600', 'from-emerald-500 to-teal-600', 'from-rose-500 to-pink-600', 'from-amber-500 to-orange-600'];
                                                    $gradient = $colors[$item->id % count($colors)];
                                                @endphp
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-white font-black shadow-md">
                                                    {{ mb_substr($item->ad, 0, 1) }}
                                                </div>
                                            @endif
                                            <a href="{{ route('bolum-puanlari', ['bolum' => $item->id, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="text-sm font-bold text-gray-900 hover:text-purple-600 transition">
                                                {{ $item->ad }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($item->lider)
                                            <a href="{{ route('profile.show', $item->lider->id) }}" class="flex items-center gap-2 group">
                                                @if($item->lider->profile_photo_path)
                                                    <img src="{{ asset('storage/'.$item->lider->profile_photo_path) }}" class="w-8 h-8 rounded-full object-cover">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                                        {{ mb_substr($item->lider->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <span class="text-xs font-medium text-gray-600 group-hover:text-purple-600 transition">{{ $item->lider->name }}</span>
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-300 italic">Atanmamış</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($item->birinci && $item->total_score > 0)
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('profile.puanlar', $item->birinci->id) }}" class="flex items-center gap-2 group">
                                                    @if($item->birinci->profile_photo_path)
                                                        <img src="{{ asset('storage/'.$item->birinci->profile_photo_path) }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-purple-100">
                                                    @else
                                                        <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center text-[10px] font-bold text-purple-600 ring-2 ring-purple-100">
                                                            {{ mb_substr($item->birinci->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-bold text-gray-800 group-hover:text-purple-600 transition">{{ $item->birinci->name }}</span>
                                                        <span class="text-[10px] text-purple-500 font-bold">{{ number_format($item->birinci->period_puan, 0) }} Puan <span class="text-gray-400 font-normal">/ %{{ $item->birinci_katki_orani }} Katkı</span></span>
                                                    </div>
                                                </a>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-purple-600 bg-purple-50/5">
                                        {{ number_format($item->total_score, 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($bolumPuanListesi->count() > 5)
                    <div class="p-4 bg-gray-50 text-center border-t border-gray-100">
                        <button @click="showAllBolum = !showAllBolum" class="text-xs font-bold text-purple-600 hover:text-purple-800 transition flex items-center justify-center mx-auto gap-1">
                            <span x-text="showAllBolum ? 'Daha Az Göster' : 'Tüm Bölümleri Görüntüle ({{ $bolumPuanListesi->count() }})'"></span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="showAllBolum ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>