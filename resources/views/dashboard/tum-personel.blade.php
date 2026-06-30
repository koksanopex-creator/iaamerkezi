<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tüm Personeller') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <h3 class="font-bold text-lg text-green-700">Tüm Personel Listesi</h3>
                        </div>
                        <a href="{{ route('puan-durumu') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg text-xs font-bold transition-all duration-200 shadow-md shadow-indigo-100 group">
                            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                            </svg>
                            <span>Puan Durumuna Dön</span>
                        </a>
                    </div>

                    <!-- Kendi Puanım Bölümü -->
                    @if(isset($currentUser) && $currentUser->is_personnel)
                    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6 shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    @if($currentUser->profile_photo_path)
                                        <img class="h-16 w-16 rounded-full object-cover border-2 border-green-400" src="{{ asset('storage/' . $currentUser->profile_photo_path) }}" alt="">
                                    @else
                                        <div class="h-16 w-16 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold border-2 border-green-400 text-2xl uppercase">
                                            {{ substr($currentUser->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="absolute -bottom-1 -right-1 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full font-bold border-2 border-white shadow-sm">SİZ</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-green-700 uppercase tracking-widest mb-1">Mevcut Durumunuz</div>
                                    <div class="text-xl font-bold text-gray-900">{{ $currentUser->name }}</div>
                                    <div class="text-xs text-gray-500 font-medium flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $currentUser->bolum->ad ?? 'Bölüm Yok' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-6">
                                <div class="bg-white/60 backdrop-blur-sm rounded-lg px-6 py-3 border border-green-100 text-right shadow-sm">
                                    <div class="text-[10px] font-bold text-green-600 uppercase mb-1">Dönemlik Puan</div>
                                    <div class="text-3xl font-black text-green-700">{{ number_format($currentUser->period_puan ?? $currentUser->toplam_puan, 0) }}</div>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('profile.puanlar', ['user' => $currentUser->id, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
                                       class="bg-green-600 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-md shadow-green-100 text-center">
                                       Puan Detayları
                                    </a>
                                    <a href="{{ route('profile.show', $currentUser->id) }}" 
                                       class="bg-white text-green-600 text-xs font-bold px-4 py-2 rounded-lg border border-green-200 hover:bg-green-50 transition text-center">
                                       Profilini Gör
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Filtre Formu -->
                    <form method="GET" action="{{ route('tum-personel') }}"
                        class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Personel Adı</label>
                                <input type="text" name="name" id="name" value="{{ request('name') }}"
                                    placeholder="Ad Soyad Ara"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 text-sm">
                            </div>
                            <div>
                                <label for="bolum" class="block text-sm font-medium text-gray-700 mb-1">Bölüm</label>
                                <input type="text" name="bolum" id="bolum" value="{{ request('bolum') }}"
                                    placeholder="Bölüm Ara"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 text-sm">
                            </div>
                            <div class="flex items-end gap-2">
                                <button type="submit"
                                    class="flex-1 bg-green-600 text-white rounded-md py-2 px-4 text-sm font-bold hover:bg-green-700 transition shadow-sm">Filtrele</button>
                                <a href="{{ route('tum-personel') }}"
                                    class="flex-1 bg-gray-200 text-gray-700 rounded-md py-2 px-4 text-sm font-bold hover:bg-gray-300 text-center transition">Sıfırla</a>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-200 pt-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Başlangıç Tarihi</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Bitiş Tarihi</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 text-sm">
                            </div>
                            <div class="flex items-end gap-2 pb-1">
                                @php
                                    $thisWeekStart = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
                                    $thisMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                                @endphp
                                <a href="{{ route('tum-personel', array_merge(request()->query(), ['start_date' => $thisWeekStart])) }}" 
                                    class="text-xs px-3 py-1.5 rounded-full {{ request('start_date') == $thisWeekStart ? 'bg-green-100 text-green-700 border-green-200' : 'bg-white text-gray-600 border-gray-200' }} border font-medium hover:bg-green-50 transition">
                                    Bu Hafta
                                </a>
                                <a href="{{ route('tum-personel', array_merge(request()->query(), ['start_date' => $thisMonthStart])) }}" 
                                    class="text-xs px-3 py-1.5 rounded-full {{ request('start_date') == $thisMonthStart ? 'bg-green-100 text-green-700 border-green-200' : 'bg-white text-gray-600 border-gray-200' }} border font-medium hover:bg-green-50 transition">
                                    Bu Ay
                                </a>
                            </div>
                        </div>
                    </form>

                </div>

                @php
                    $getHeaderUrl = function($column, $defaultDir = 'asc') use ($sortBy, $sortDir) {
                        $dir = ($sortBy === $column) ? ($sortDir === 'asc' ? 'desc' : 'asc') : $defaultDir;
                        return route('tum-personel', array_merge(request()->except(['page']), [
                            'sort_by' => $column,
                            'sort_dir' => $dir,
                        ]));
                    };
                
                    $renderHeader = function($column, $label) use ($sortBy, $sortDir, $getHeaderUrl) {
                        $isActive = $sortBy === $column;
                        $url = $getHeaderUrl($column);
                        $icon = '';
                        if ($isActive) {
                            $icon = $sortDir === 'asc' 
                                ? '<svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>'
                                : '<svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>';
                        } else {
                            $icon = '<svg class="w-3.5 h-3.5 text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 10l5-5 5 5M7 14l5 5 5-5"/></svg>';
                        }
                        return '<a href="' . $url . '" class="group inline-flex items-center gap-1 hover:text-green-600 transition-colors select-none">' . e($label) . $icon . '</a>';
                    };
                @endphp

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    {!! $renderHeader('toplam_puan', 'Sıra') !!}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    {!! $renderHeader('name', 'Personel') !!}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    {!! $renderHeader('bolum', 'Bölüm') !!}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    <div class="flex justify-end">{!! $renderHeader('toplam_puan', 'Toplam Puan') !!}</div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $index => $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-gray-400">
                                        #{{ (($users->currentPage() - 1) * $users->perPage()) + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($user->profile_photo_path)
                                                <img class="h-10 w-10 rounded-full object-cover mr-3 border border-gray-200"
                                                    src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="">
                                            @else
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold mr-3 border border-gray-200">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $user->name }}
                                                    @if($user->trashed())
                                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-red-100 text-red-600 border border-red-200 uppercase tracking-tighter">PASİF</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->bolum->ad ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                        {{ number_format($user->period_puan ?? $user->toplam_puan, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('profile.show', $user->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition">Profil</a>
                                            <a href="{{ route('profile.puanlar', ['user' => $user->id, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                                class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition">Puanlar</a>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        Filtreleme kriterlerine uygun personel bulunamadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>