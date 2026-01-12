<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                Puan Durumu ve Liderlik Tablosu
            </h2>
            <p class="text-sm md:text-base text-gray-600">En yüksek puana sahip kullanıcıları görüntüleyin</p>
        </div>
    </x-slot>

    <div class="py-4 md:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 p-4 md:p-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 md:w-7 md:h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg md:text-xl font-bold text-white">Liderlik Sıralaması</h3>
                            <p class="text-xs md:text-sm text-orange-100 mt-0.5">Toplam {{ $kullanicilar->count() }} kullanıcı</p>
                        </div>
                    </div>
                </div>

                <!-- Table Section - Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-20">Sıra</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kullanıcı</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Toplam Puan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($kullanicilar as $index => $kullanici)
                                <tr class="hover:bg-orange-50/50 transition-colors duration-150 {{ $index < 3 ? 'bg-gradient-to-r from-yellow-50/50 to-orange-50/50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($index === 0)
                                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 shadow-lg">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </div>
                                        @elseif($index === 1)
                                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 shadow-lg">
                                                <span class="text-white font-bold text-lg">2</span>
                                            </div>
                                        @elseif($index === 2)
                                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-500 shadow-lg">
                                                <span class="text-white font-bold text-lg">3</span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 font-bold text-sm">
                                                {{ $index + 1 }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="relative flex-shrink-0">
                                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                                    {{ strtoupper(substr($kullanici->name, 0, 1)) }}
                                                </div>
                                                @if($index === 0)
                                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-yellow-400 rounded-full border-2 border-white flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ $kullanici->name }}
                                                    @if($index < 3)
                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-200 text-gray-800' : 'bg-orange-100 text-orange-800') }}">
                                                            TOP {{ $index + 1 }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500 truncate">{{ $kullanici->email }}</div>

                                                <div class="text-xs text-indigo-600 font-medium truncate mt-0.5">
                                                    {{ $kullanici->bolum->ad ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('kullanici.puanlari', $kullanici) }}" 
                                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-lg transition-all duration-200 {{ $index === 0 ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white hover:shadow-lg transform hover:scale-105' : 'text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50' }}">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            {{ number_format($kullanici->toplam_puan, 0) }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="p-3 bg-gray-100 rounded-full">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 font-medium">Henüz puan kazanan bir kullanıcı bulunmuyor</p>
                                                <p class="text-gray-400 text-sm mt-1">İlk puan kazanan siz olun!</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Card View - Mobile -->
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse ($kullanicilar as $index => $kullanici)
                        <div class="p-4 {{ $index < 3 ? 'bg-gradient-to-r from-yellow-50/50 to-orange-50/50' : '' }} hover:bg-orange-50 transition-colors duration-150">
                            <div class="flex items-center gap-3 mb-3">
                                <!-- Sıra Badge -->
                                @if($index === 0)
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 shadow-lg flex-shrink-0">
                                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                @elseif($index === 1)
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 shadow-lg flex-shrink-0">
                                        <span class="text-white font-bold text-xl">2</span>
                                    </div>
                                @elseif($index === 2)
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-500 shadow-lg flex-shrink-0">
                                        <span class="text-white font-bold text-xl">3</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-700 font-bold text-base flex-shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                @endif

                                <!-- Avatar ve İsim -->
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-base shadow-md">
                                            {{ strtoupper(substr($kullanici->name, 0, 1)) }}
                                        </div>
                                        @if($index === 0)
                                            <div class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-yellow-400 rounded-full border-2 border-white flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-900 text-sm truncate">
                                            {{ $kullanici->name }}
                                            @if($index < 3)
                                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-200 text-gray-800' : 'bg-orange-100 text-orange-800') }}">
                                                    TOP {{ $index + 1 }}
                                                </span>
                                            @endif
                                        </h4>
                                        <p class="text-xs text-gray-500 truncate">{{ $kullanici->email }}</p>
                                        <p class="text-[10px] text-indigo-600 font-medium truncate mt-0.5">{{ $kullanici->bolum->ad ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Puan -->
                            <div class="ml-14">
                                <a href="{{ route('kullanici.puanlari', $kullanici) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-base transition-all duration-200 w-full justify-center {{ $index === 0 ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-md' : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100' }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    {{ number_format($kullanici->toplam_puan, 0) }} Puan
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="p-3 bg-gray-100 rounded-full">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-600 font-medium text-sm">Henüz puan kazanan bir kullanıcı bulunmuyor</p>
                                    <p class="text-gray-400 text-xs mt-1">İlk puan kazanan siz olun!</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>