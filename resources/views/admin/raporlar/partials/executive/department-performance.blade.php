<div x-data="{
        slide: 0,
        total: {{ ceil(count($bolumPerformansi) / 2) }},
        init() {
            setInterval(() => {
                this.slide = (this.slide + 1) % this.total;
            }, 9000);
        }
    }" 
    class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 relative overflow-hidden">
    
    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h2l2 7 4-14 3 9 2-6h5" /></svg>
        Bölüm Performans Karnesi
    </h3>

    <div class="overflow-hidden relative">
        <div class="flex transition-all duration-700" :style="'transform: translateX(-' + (slide * 100) + '%)'">
            @foreach(array_chunk($bolumPerformansi->toArray(), 2) as $chunk)
                <div class="min-w-full grid grid-cols-1 md:grid-cols-2 gap-4 px-1">
                    @foreach($chunk as $bolum)
                        <div class="border rounded-xl p-4 hover:shadow-md transition-shadow bg-white relative overflow-hidden">
                            {{-- Yıllık Mini Özet --}}
                            <div class="absolute top-0 right-0 flex gap-1 p-1">
                                @foreach([now()->year, now()->year - 1, now()->year - 2] as $yy)
                                    <div class="bg-orange-100 text-orange-700 rounded-bl-lg px-2 py-0.5 text-[9px] border-b border-l text-center">
                                        <span class="font-bold">{{ $yy }}</span>
                                        <span class="block text-[8px]">
                                            T: {{ $bolum['yillik_detay'][$yy]['toplam'] ?? 0 }} | Ç: {{ $bolum['yillik_detay'][$yy]['cozulen'] ?? 0 }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <h4 class="font-bold text-gray-800 text-base mb-2">{{ $bolum['ad'] }}</h4>

                            {{-- Başarı Progress --}}
                            <div class="mb-2">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-500">Başarı</span>
                                    <span class="font-bold {{ $bolum['basari_orani'] < 50 ? 'text-red-600' : 'text-green-600' }}">%{{ $bolum['basari_orani'] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full" style="width: {{ $bolum['basari_orani'] }}%"></div>
                                </div>
                            </div>

                            {{-- Mini İstatistikler --}}
                            <div class="flex justify-between text-xs pt-2 border-t border-gray-100">
                                <div class="text-center w-1/4">
                                    <span class="block text-gray-400 text-[11px]">Toplam</span>
                                    <span class="font-bold">{{ $bolum['toplam'] }}</span>
                                </div>
                                <div class="text-center w-1/4">
                                    <span class="block text-gray-400 text-[11px]">Hız</span>
                                    <span class="font-bold">{{ $bolum['ort_sure'] }} Gün</span>
                                </div>
                                <div class="text-center w-1/4">
                                    <span class="block text-gray-400 text-[11px]">İşlemde</span>
                                    <span class="font-bold text-blue-600">{{ $bolum['islemde'] ?? $bolum['acik'] }}</span>
                                </div>
                                <div class="text-center w-1/4">
                                    <span class="block text-gray-400 text-[11px]">Kapanan</span>
                                    <span class="font-bold text-green-600">{{ $bolum['kapandi'] ?? $bolum['cozulen'] }}</span>
                                </div>
                            </div>

                            {{-- YILLIK DETAYLI TABLO --}}
                            <div class="mt-4 border-t pt-3">
                                <h5 class="font-bold text-gray-700 text-xs mb-2">Yıllık Performans Özeti</h5>
                                <table class="w-full text-[11px] text-gray-700 border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100 text-gray-600">
                                        <tr>
                                            <th class="px-2 py-1 text-left">Yıl</th>
                                            <th class="px-2 py-1 text-center w-16">Toplam</th>
                                            <th class="px-2 py-1 text-center w-16">Çözülen</th>
                                            <th class="px-2 py-1 text-center w-16">Açık</th>
                                            <th class="px-2 py-1 text-center w-16">Geciken</th>
                                            <th class="px-2 py-1 text-center w-16">Ort. Süre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach([now()->year, now()->year - 1, now()->year - 2] as $yy)
                                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                                <td class="px-2 py-1 font-bold">{{ $yy }}</td>
                                                <td class="px-2 py-1 text-center font-semibold">{{ $bolum['yillik_detay'][$yy]['toplam'] ?? 0 }}</td>
                                                <td class="px-2 py-1 text-center text-green-600 font-bold">{{ $bolum['yillik_detay'][$yy]['cozulen'] ?? 0 }}</td>
                                                <td class="px-2 py-1 text-center text-blue-600 font-bold">{{ $bolum['yillik_detay'][$yy]['acik'] ?? 0 }}</td>
                                                <td class="px-2 py-1 text-center text-red-600 font-bold">{{ $bolum['yillik_detay'][$yy]['geciken'] ?? 0 }}</td>
                                                <td class="px-2 py-1 text-center text-purple-600 font-bold">{{ $bolum['yillik_detay'][$yy]['ortalama'] ?? 0 }} gün</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Kontrol Okları --}}
        <button @click="slide = (slide - 1 + total) % total" class="absolute top-1/2 -left-2 -translate-y-1/2 bg-white shadow rounded-full p-2 hover:bg-gray-100 transition">
            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click="slide = (slide + 1) % total" class="absolute top-1/2 -right-2 -translate-y-1/2 bg-white shadow rounded-full p-2 hover:bg-gray-100 transition">
            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>