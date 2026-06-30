{{-- MAVİ YAKA PERSONEL DASHBOARD --}}
<div class="mb-8">
    <div
        class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-6 sm:p-10 text-white relative overflow-hidden">
        {{-- Arka plan deseni --}}
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-blue-300 opacity-20 rounded-full blur-xl"></div>

        <div class="relative z-10">
            <h3 class="text-2xl sm:text-3xl font-bold mb-2">Merhaba, {{ Auth::user()->name }} 👋</h3>
            <p class="text-blue-100 text-sm sm:text-base max-w-2xl mb-6">
                Mavi yaka portalına hoş geldiniz. Buradan aktif iyileştirme fikirlerini (İAA) görebilir ve takım
                çalışmalarına katılabilirsiniz.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('iaa.havuz') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-700 font-bold rounded-xl shadow-md hover:bg-blue-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    İAA Havuzuna Git
                </a>
                <a href="{{ route('iaa.takimProjeleri') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-blue-800/50 text-white font-bold rounded-xl hover:bg-blue-800/70 transition-colors border border-blue-400/30">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Dahil Olduğum Projeler
                </a>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
    {{-- İstatistik Kartı --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="rounded-full bg-indigo-100 p-4 mr-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Tamamlanan Görevlerim</p>
            <p class="text-3xl font-bold text-gray-900">
                {{ $stats['tamamlanan_gorevler'] ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Puan Kartı --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="rounded-full bg-amber-100 p-4 mr-4">
            <svg class="w-8 h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                </path>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Mevcut İAA Puanım</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format(Auth::user()->toplam_puan ?? 0, 0) }}</p>
        </div>
    </div>
</div>