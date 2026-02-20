<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Puan Durumu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sol Kolon: Takım Sıralamaları -->
                <div class="space-y-6">

                    <!-- 1. İAA Takımları -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full">
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
                            <a href="{{ route('tum-personel') }}"
                                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">Tümünü
                                Gör &rarr;</a>
                        </div>

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
                                    class="flex-1 bg-green-500 text-white rounded-md text-xs font-bold hover:bg-green-600 transition">Filtrele</button>
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
                                            <a href="{{ route('profile.puanlar', $user->id) }}"
                                                class="hover:text-green-800 hover:underline block w-full h-full">
                                                {{ number_format($user->toplam_puan, 0) }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>