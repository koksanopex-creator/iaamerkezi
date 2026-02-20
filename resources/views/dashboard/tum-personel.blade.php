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
                            class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline">&larr; Puan
                            Durumuna Dön</a>
                    </div>

                    <!-- Filtre Formu -->
                    <form method="GET" action="{{ route('tum-personel') }}"
                        class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Personel
                                    Adı</label>
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
                            <div>
                                <label for="min_score" class="block text-sm font-medium text-gray-700 mb-1">Puan
                                    Aralığı</label>
                                <div class="flex gap-2">
                                    <input type="number" name="min_score" id="min_score"
                                        value="{{ request('min_score') }}" placeholder="Min"
                                        class="w-1/2 border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 text-sm">
                                    <input type="number" name="max_score" id="max_score"
                                        value="{{ request('max_score') }}" placeholder="Max"
                                        class="w-1/2 border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 text-sm">
                                </div>
                            </div>
                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full bg-green-600 text-white rounded-md py-2 px-4 text-sm font-bold hover:bg-green-700 transition shadow-sm">Filtrele</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Personel
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Bölüm
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Toplam Puan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($user->profile_photo_path)
                                                <img class="h-10 w-10 rounded-full object-cover mr-3 border border-gray-200"
                                                    src="{{ '/storage/' . $user->profile_photo_path }}" alt="">
                                            @else
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold mr-3 border border-gray-200">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->bolum->ad ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                        {{ number_format($user->toplam_puan, 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('profile.show', $user->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition">Profil</a>
                                            <a href="{{ route('profile.puanlar', $user->id) }}"
                                                class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition">Puanlar</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
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