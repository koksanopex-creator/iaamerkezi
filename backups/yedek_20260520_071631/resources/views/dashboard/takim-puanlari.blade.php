<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <span class="text-gray-800">{{ $takim->ad }}</span>
                <span class="ml-2 text-sm font-normal text-gray-500">Puan Detayları</span>
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('puan-durumu') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Puan Durumuna Dön
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Üst Bilgi Kartı -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                        <div class="p-4 bg-indigo-50 rounded-lg">
                            <div class="text-xs text-indigo-500 uppercase font-bold tracking-wider mb-1">Takım Lideri
                            </div>
                            <div class="font-bold text-gray-800 flex items-center justify-center gap-2">
                                @if($takim->lider && $takim->lider->profile_photo_path)
                                    <img class="h-8 w-8 rounded-full object-cover"
                                        src="{{ '/storage/' . $takim->lider->profile_photo_path }}" alt="">
                                @endif
                                <a href="{{ route('profile.show', $takim->lider->id) }}"
                                    class="hover:text-indigo-600 underline">
                                    {{ $takim->lider->name ?? '-' }}
                                </a>
                            </div>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="text-xs text-green-500 uppercase font-bold tracking-wider mb-1">Toplam Puan
                            </div>
                            <div class="text-2xl font-bold text-green-600">{{ number_format($hesaplananPuan, 0) }}</div>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-xs text-blue-500 uppercase font-bold tracking-wider mb-1">Üye Sayısı</div>
                            <div class="text-xl font-bold text-blue-600">{{ $uyeler->count() }}</div>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-lg">
                            <div class="text-xs text-orange-500 uppercase font-bold tracking-wider mb-1">Tamamlanan
                                Proje</div>
                            <div class="text-xl font-bold text-orange-600">{{ $tamamlananProjeler->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sol Kolon: Takım Üyeleri -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            Takım Üyeleri
                        </h3>
                        <span
                            class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $uyeler->count() }}
                            Kişi</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($uyeler as $uye)
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        @if($uye->profile_photo_path)
                                            <img class="h-10 w-10 rounded-full object-cover border border-gray-200"
                                                src="{{ '/storage/' . $uye->profile_photo_path }}" alt="">
                                        @else
                                            <div
                                                class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold border border-indigo-200">
                                                {{ substr($uye->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('profile.show', $uye->id) }}"
                                            class="font-medium text-gray-800 hover:text-indigo-600 transition block">
                                            {{ $uye->name }}
                                        </a>
                                        <div class="text-xs text-gray-500">{{ $uye->bolum->ad ?? 'Bölüm Yok' }}</div>
                                    </div>
                                </div>
                                <div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $uye->pivot->gorev_tanimi ?? 'Üye' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach

                        @if($uyeler->isEmpty())
                            <div class="p-8 text-center text-gray-500 italic">
                                Henüz üye bulunmuyor.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sağ Kolon: Puan Kaynağı (Projeler) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tamamlanan Projeler (Puan Kaynağı)
                        </h3>
                        <span
                            class="text-xs font-medium bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ $tamamlananProjeler->count() }}
                            Adet</span>
                    </div>

                    <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto custom-scrollbar">
                        <!-- Şikayet Çözümleri (Varsa) -->
                        @if(!empty($cozulenSikayetler) && $cozulenSikayetler->count() > 0)
                            <div class="bg-yellow-50 p-3 text-xs font-bold text-yellow-800 border-b border-yellow-100">
                                Çözülen Müşteri Şikayetleri
                            </div>
                            @foreach($cozulenSikayetler as $sikayet)
                                <div class="p-4 hover:bg-yellow-50/50 transition border-l-4 border-yellow-400">
                                    <div class="flex justify-between items-start mb-1">
                                        <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}"
                                            class="font-medium text-gray-800 hover:text-indigo-600 line-clamp-1"
                                            title="{{ $sikayet->musteri_sikayet_konusu }}">
                                            #{{ $sikayet->id }} - {{ $sikayet->musteri_sikayet_konusu }}
                                        </a>
                                        <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                            {{ $sikayet->updated_at->format('d.m.Y') }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 flex justify-between items-center">
                                        <span>{{ $sikayet->musteri_adi }}</span>
                                        <div class="text-right">
                                            @if($sikayet->iaa_id && $sikayet->iaaProjesi)
                                                <div class="flex flex-col items-end">
                                                    <span class="text-indigo-500 font-bold text-[10px] bg-indigo-50 px-1 rounded border border-indigo-100">Müşteri Şikayeti</span>
                                                    <span class="font-bold text-green-600" title="Puan İAA Projesinden Gelmektedir">
                                                        +{{ number_format($sikayet->iaaProjesi->puan, 0) }} Puan
                                                    </span>
                                                </div>
                                            @else
                                                <span class="font-bold text-green-600">+{{ number_format($sikayet->kazanilan_puan, 0) }} Puan</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <!-- İAA Projeleri -->
                        @if($tamamlananProjeler->count() > 0)
                            @foreach($tamamlananProjeler as $proje)
                                <div class="p-4 hover:bg-gray-50 transition border-l-4 border-green-400">
                                    <div class="flex justify-between items-start mb-1">
                                        <a href="{{ route('iaa.show', $proje->id) }}"
                                            class="font-medium text-gray-800 hover:text-indigo-600 line-clamp-2"
                                            title="{{ $proje->baslik }}">
                                            {{ $proje->baslik }}
                                        </a>
                                        <span class="font-bold text-green-600 ml-2 whitespace-nowrap">+{{ number_format($proje->puan, 0) }} P</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs text-gray-500 mt-2">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-white font-bold bg-gray-400">
                                                İAA Projesi
                                            </span>
                                            <span>Onaya Gönderme: {{ \Carbon\Carbon::parse($proje->onaya_gonderilme_tarihi ?? $proje->onaylanma_tarihi)->format('d.m.Y') }}</span>
                                        </div>
                                        <a href="{{ route('iaa.show', $proje->id) }}" class="text-indigo-500 hover:underline">Detay</a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @if(empty($cozulenSikayetler) || $cozulenSikayetler->count() == 0)
                                <div class="p-8 text-center text-gray-500 italic">
                                    Henüz tamamlanmış ve puanlanmış proje bulunmuyor.
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
</x-app-layout>