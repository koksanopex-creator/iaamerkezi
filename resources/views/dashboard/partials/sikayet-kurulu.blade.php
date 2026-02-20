<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 border border-gray-200/50">
    <div class="px-6 py-5">
        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
                </path>
            </svg>
            Hızlı Erişim (Kurul)
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            <a href="{{ route('admin.sikayetler.create') }}"
                class="flex flex-col items-center justify-center p-4 bg-indigo-50 text-indigo-700 font-semibold rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Yeni Şikayet Gir</span>
            </a>
            <a href="{{ route('admin.sikayetler.index') }}"
                class="flex flex-col items-center justify-center p-4 bg-gray-50 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200">
                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                    </path>
                </svg>
                <span>Tüm Şikayetler</span>
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 border border-gray-200/50">
    <div class="px-6 py-5 bg-gray-50/70 border-b border-gray-200/70">
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z">
                </path>
            </svg>
            Müşteri Şikayet Raporu (Özet)
        </h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-gray-200/70">
        <div class="bg-white px-4 py-5 text-center transition-all duration-300 hover:bg-blue-50/50">
            <p class="text-sm font-medium text-gray-500">Toplam Şikayet</p>
            <p class="mt-1 text-3xl font-bold text-blue-600">{{ $stats['toplam_sikayet'] }}</p>
        </div>
        <div class="bg-white px-4 py-5 text-center transition-all duration-300 hover:bg-yellow-50/50">
            <p class="text-sm font-medium text-gray-500">Yeni (Atanmamış)</p>
            <p class="mt-1 text-3xl font-bold text-yellow-500">{{ $stats['yeni_sikayet'] }}</p>
        </div>
        <div class="bg-white px-4 py-5 text-center transition-all duration-300 hover:bg-cyan-50/50">
            <p class="text-sm font-medium text-gray-500">Çözüm Sürecinde</p>
            <p class="mt-1 text-3xl font-bold text-cyan-600">{{ $stats['islemde_sikayet'] }}</p>
        </div>
    </div>
    <div class="px-6 py-4 border-t border-gray-200/70">
        <h4 class="text-md font-semibold text-gray-800">Son Gelen Şikayetler</h4>
    </div>
    <div class="flow-root">
        <ul role="list" class="divide-y divide-gray-200/70">
            @forelse($stats['son_sikayetler'] as $sikayet)
                <li class="hover:bg-gray-50/70 transition-colors duration-150">
                    <a href="{{ route('admin.sikayetler.show', $sikayet) }}"
                        class="flex items-center justify-between p-4 sm:p-6 space-x-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-indigo-700 truncate"
                                title="{{ $sikayet->musteri_sikayet_konusu }}">{{ $sikayet->musteri_sikayet_konusu }}</p>
                            <p class="text-sm text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                                <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg><span>{{ $sikayet->musteri_adi }}</span></span>
                                @if($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Talep Olarak Kapatıldı']))
                                    <span class="ml-2">
                                        {!! $sikayet->iaaProjesi->durum_etiketi !!}
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="flex-shrink-0 flex flex-col items-end space-y-1">
                            {!! $sikayet->musteri_durum_badge !!}
                            <span class="text-xs text-gray-400 mt-1">{{ $sikayet->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                </li>
            @empty
                <li class="p-6 text-center text-gray-500">Henüz sisteme girilmiş bir şikayet bulunmuyor.</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-gray-50/70 px-6 py-4 border-t border-gray-200/70 text-center">
        <a href="{{ route('admin.sikayetler.index') }}"
            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">Tüm Şikayetleri Yönet
            →</a>
    </div>
</div>