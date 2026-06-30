<div class="space-y-6">
    {{-- Özet Kartları (Sadece varsa gösterelim) --}}
    @if(isset($chartData['ozet']))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Toplam Şikayet --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Toplam Şikayet</h3>
                    <div class="text-3xl font-black text-gray-900">{{ $chartData['ozet']['toplam'] }}</div>
                </div>
                <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>

            {{-- Çözülen Şikayet --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Çözülen Şikayet</h3>
                    <div class="text-3xl font-black text-green-600">{{ $chartData['ozet']['cozulen'] }}</div>
                </div>
                <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            {{-- Bekleyen Şikayet --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Bekleyen/İşlemde</h3>
                    <div class="text-3xl font-black text-orange-600">{{ $chartData['ozet']['bekleyen'] }}</div>
                </div>
                <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            {{-- Ort. Çözüm Hızı --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Ort. Çözüm Hızı</h3>
                    <div class="text-3xl font-black text-indigo-600">{{ $chartData['ozet']['ortalamaHiz'] }} <span class="text-lg font-medium text-gray-500">Gün</span></div>
                </div>
                <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>
    @endif

    {{-- Filtreler --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri Ara</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11A8 8 0 1111 3a8 8 0 018 8z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35"></path>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.500ms="filtreMusteri" type="text"
                        class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Firma adı...">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlık / Konu Ara</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.500ms="filtreBaslik" type="text"
                        class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Arama yapın...">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                <select wire:model.live="filtreDurum"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Tümü</option>
                    <option value="Yeni">Yeni</option>
                    <option value="İşlemde">İşlemde</option>
                    <option value="Çözümlendi">Çözümlendi</option>
                    <option value="Kapatıldı">Kapatıldı</option>
                    <option value="İptal Edildi">İptal Edildi</option>
                    <option value="talep_kapali">Talep Olarak Kapatıldı</option>
                    <option value="hatali_bildirim">Hatalı Bildirim Olarak Kapatıldı</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                <input wire:model.live.debounce.500ms="filtreBaslangicTarihi" type="date"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                <input wire:model.live.debounce.500ms="filtreBitisTarihi" type="date"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="flex items-center">
                <button wire:click="filtreleriTemizle" title="Tüm Filtreleri Temizle"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <svg class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Filtreleri Temizle
                </button>
            </div>
        </div>
    </div>


    {{-- Aktif Filtre Bilgilendirmesi --}}
    @if(isset($chartData['ozet']['dateText']))
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700 font-medium">Bu sayfadaki tablolar ve istatistikler, <strong class="text-blue-900 border-b border-blue-300">{{ $chartData['ozet']['dateText'] }}</strong>.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Tablo --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
        <div class="h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12 whitespace-nowrap">Sıra</th>
                        <th class="px-3 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Firma İsmi</th>
                        <th class="px-3 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Şikayet Konusu</th>
                        <th class="px-3 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Bölüm</th>
                        <th class="px-3 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Tarih Aralığı</th>
                        <th class="px-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Çözüm Süresi</th>
                        <th class="px-3 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Durum</th>
                        <th class="px-3 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sikayetler as $index => $sikayet)
                        @php
                            // Bitiş Tarihi ve Süre Hesaplama
                            $isClosed = in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı', 'Tamamlandı']) ||
                                ($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']));

                            $baslangic = clone $sikayet->created_at;
                            $bitis = $isClosed ? clone $sikayet->updated_at : null;
                            $sureGun = null;

                            if ($isClosed && $bitis) {
                                $gunFarki = ceil($baslangic->diffInDays($bitis));
                                $sureGun = $gunFarki < 1 ? 1 : $gunFarki;
                            } else {
                                $gunFarki = ceil($baslangic->diffInDays(now()));
                                $sureGun = $gunFarki < 1 ? 1 : $gunFarki;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-3 text-sm font-medium text-gray-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    @if($sikayet->customer && $sikayet->customer->logo_path)
                                        <img src="{{ asset('storage/' . $sikayet->customer->logo_path) }}" class="w-7 h-7 rounded-lg object-cover shadow-sm border border-gray-100" alt="Logo">
                                    @else
                                        <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center text-gray-300 border border-gray-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        @if($sikayet->customer_id)
                                            <a href="{{ route('musteri.profil.show', $sikayet->customer_id) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline transition-colors block max-w-[120px] lg:max-w-[150px] font-bold truncate text-sm" title="{{ $sikayet->musteri_adi }}">
                                                {{ $sikayet->musteri_adi }}
                                            </a>
                                        @else
                                            <span class="block max-w-[120px] lg:max-w-[150px] truncate font-bold text-gray-900 text-sm" title="{{ $sikayet->musteri_adi }}">{{ $sikayet->musteri_adi }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-700">
                                <div class="max-w-[150px] lg:max-w-[200px] truncate group relative cursor-help" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                    {{ $sikayet->musteri_sikayet_konusu }}
                                </div>
                            </td>
                            <td class="px-3 py-3 text-sm font-medium">
                                @if(isset($sikayet->sikayetKategori->bolum_id))
                                    <a href="{{ route('admin.bolumler.dashboard', $sikayet->sikayetKategori->bolum_id) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline transition-colors block max-w-[100px] truncate">
                                        {{ $sikayet->sikayetKategori->bolum->ad ?? 'Bölüm Yok' }}
                                    </a>
                                @else
                                    <span class="text-gray-500 block max-w-[100px] truncate">{{ $sikayet->sikayetKategori->bolum->ad ?? 'Bölüm Yok' }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-600 space-y-1 whitespace-nowrap">
                                <div class="flex items-center gap-1.5" title="Başlangıç Tarihi">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $baslangic->format('d.m.Y') }}
                                </div>
                                @if($bitis)
                                    <div class="flex items-center gap-1.5 text-green-700" title="Bitiş Tarihi">
                                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $bitis->format('d.m.Y') }}
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5 text-gray-400 text-xs italic">
                                        Devam Ediyor
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if($isClosed)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-bold bg-green-100 text-green-800">
                                        {{ $sureGun }} Gün
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-bold bg-orange-100 text-orange-800">
                                        {{ $sureGun }} Gün
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                @if($sikayet->iaaProjesi && $sikayet->iaaProjesi->durum == 'talep_olarak_kapatildi')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-300">⚪
                                        Talep</span>
                                @elseif($sikayet->iaaProjesi && $sikayet->iaaProjesi->durum == 'hatali_bildirim_olarak_kapatildi')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 line-through">🚫
                                        Hatalı Bildirim</span>
                                @else
                                    @php
                                        $class = match ($sikayet->musteri_durum) {
                                            'Yeni' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'İşlemde', 'İnceleniyor', 'Atandı', 'Devam Ediyor' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'Çözümlendi', 'Kapatıldı', 'Tamamlandı' => 'bg-green-100 text-green-800 border-green-200',
                                            'İptal Edildi', 'Reddedildi', 'Revize' => 'bg-red-100 text-red-800 border-red-200',
                                            default => 'bg-gray-100 text-gray-800 border-gray-200'
                                        };
                                        $metin = in_array($sikayet->musteri_durum, ['İnceleniyor', 'Atandı', 'Devam Ediyor']) ? 'İşlemde' : $sikayet->musteri_durum;
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $class }}">
                                        {{ $metin }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-right space-y-2 lg:space-y-0 lg:space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.sikayetler.show', $sikayet) }}" target="_blank"
                                    class="inline-flex items-center px-2.5 py-1.5 border border-blue-200 text-xs font-bold rounded-xl shadow-sm text-blue-700 bg-white hover:bg-blue-50 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Git
                                </a>
                                @if($sikayet->iaaProjesi)
                                    <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi) }}" target="_blank"
                                        class="inline-flex items-center px-2.5 py-1.5 border border-purple-200 text-xs font-bold rounded-xl shadow-sm text-purple-700 bg-white hover:bg-purple-50 hover:border-purple-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all">
                                        <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Projesi
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Criterlerinize uygun bir şikayet bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($totalCount > $perPage)
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-center">
                <button wire:click="loadMore"
                    class="inline-flex items-center px-6 py-2.5 border border-gray-300 shadow-sm text-sm font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    <svg wire:loading wire:target="loadMore" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Daha Fazla Göster (<span class="text-indigo-600 ml-1">{{ $totalCount - $perPage }}</span> kaldı)
                </button>
            </div>
        @endif
    </div>

    {{-- Grafikler Grubu --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        {{-- Durum Grafiği (Pie) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                Şikayet Durumları
            </h3>
            <div id="durumChart" class="h-64" wire:ignore></div>
        </div>

        {{-- Bölüm Şikayet Grafiği (Bar) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Departman Şikayet Sayısı
            </h3>
            <div id="bolumSikayetChart" class="h-64" wire:ignore></div>
        </div>

        {{-- Bölüm Çözüm Süresi (Bar) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Ortalama Çözüm Süresi (Gün)
            </h3>
            <div id="bolumCozumChart" class="h-64" wire:ignore></div>
        </div>

        {{-- Müşteri Şikayetleri (Bar) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                En Çok Şikayet Eden Müşteriler (Top 10)
            </h3>
            <div id="musteriChart" class="h-64" wire:ignore></div>
        </div>

        {{-- Şikayet Trend Analizi (Gelen vs Çözülen) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 md:col-span-2">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                Şikayet Trend Analizi (Gelen vs Çözülen)
            </h3>
            <div id="combinedComplaintTrendChart" class="h-80" wire:ignore></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @script
    <script>
        let durumChart, bolumSikayetChart, bolumCozumChart, musteriChart, combinedComplaintTrendChart;

        function initCharts(data) {
            if(!data) return;

            // 1. Durum Chart (Pie)
            const durumEl = document.querySelector("#durumChart");
            if (durumEl && !durumChart) {
                durumChart = new ApexCharts(durumEl, {
                    chart: { type: 'pie', height: 250 },
                    series: data.durum.data,
                    labels: data.durum.labels,
                    legend: { position: 'bottom' },
                    colors: ['#3b82f6', '#f59e0b', '#10b981', '#6b7280', '#ef4444', '#8b5cf6', '#f43f5e']
                });
                durumChart.render();
            } else if (durumChart) {
                durumChart.updateSeries(data.durum.data);
                durumChart.updateOptions({ labels: data.durum.labels });
            }

            // 2. Bölüm Şikayet Chart (Bar)
            const bolumSikayetEl = document.querySelector("#bolumSikayetChart");
            if (bolumSikayetEl && !bolumSikayetChart) {
                bolumSikayetChart = new ApexCharts(bolumSikayetEl, {
                    chart: { type: 'bar', height: 250, toolbar: { show: false } },
                    series: [{ name: 'Şikayet Sayısı', data: data.bolumSikayet.data }],
                    xaxis: { categories: data.bolumSikayet.labels },
                    colors: ['#3b82f6'],
                    plotOptions: { bar: { borderRadius: 4, horizontal: true } }
                });
                bolumSikayetChart.render();
            } else if (bolumSikayetChart) {
                bolumSikayetChart.updateSeries([{ name: 'Şikayet Sayısı', data: data.bolumSikayet.data }]);
                bolumSikayetChart.updateOptions({ xaxis: { categories: data.bolumSikayet.labels } });
            }

            // 3. Bölüm Çözüm Chart (Bar)
            const bolumCozumEl = document.querySelector("#bolumCozumChart");
            if (bolumCozumEl && !bolumCozumChart) {
                bolumCozumChart = new ApexCharts(bolumCozumEl, {
                    chart: { type: 'bar', height: 250, toolbar: { show: false } },
                    series: [{ name: 'Ortalama Gün', data: data.bolumCozum.data }],
                    xaxis: { categories: data.bolumCozum.labels },
                    colors: ['#10b981'],
                    plotOptions: { bar: { borderRadius: 4 } },
                    dataLabels: { enabled: true, formatter: function (val) { return val + " Gün"; } }
                });
                bolumCozumChart.render();
            } else if (bolumCozumChart) {
                bolumCozumChart.updateSeries([{ name: 'Ortalama Gün', data: data.bolumCozum.data }]);
                bolumCozumChart.updateOptions({ xaxis: { categories: data.bolumCozum.labels } });
            }
            // 4. Müşteri Chart (Bar)
            const musteriEl = document.querySelector("#musteriChart");
            if (musteriEl && !musteriChart) {
                musteriChart = new ApexCharts(musteriEl, {
                    chart: { type: 'bar', height: 250, toolbar: { show: false } },
                    series: [{ name: 'Şikayet Sayısı', data: data.musteri.data }],
                    xaxis: { categories: data.musteri.labels },
                    colors: ['#8b5cf6'],
                    plotOptions: { bar: { borderRadius: 4, horizontal: true } }
                });
                musteriChart.render();
            } else if (musteriChart) {
                musteriChart.updateSeries([{ name: 'Şikayet Sayısı', data: data.musteri.data }]);
                musteriChart.updateOptions({ xaxis: { categories: data.musteri.labels } });
            }

            // 5. Trend Chart (Area/Line)
            const trendEl = document.querySelector("#combinedComplaintTrendChart");
            if (trendEl && !combinedComplaintTrendChart) {
                combinedComplaintTrendChart = new ApexCharts(trendEl, {
                    chart: { 
                        type: 'area', 
                        height: 320, 
                        toolbar: { show: false },
                        fontFamily: 'inherit'
                    },
                    series: data.trend.datasets.map(ds => ({
                        name: ds.name,
                        data: ds.data,
                        type: ds.name === 'Gelen Şikayet' ? 'area' : 'line'
                    })),
                    xaxis: { categories: data.trend.labels },
                    colors: ['#3b82f6', '#10b981'],
                    stroke: { curve: 'smooth', width: [2, 2] },
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05 }
                    },
                    legend: { position: 'top', horizontalAlign: 'right' }
                });
                combinedComplaintTrendChart.render();
            } else if (combinedComplaintTrendChart) {
                combinedComplaintTrendChart.updateSeries(data.trend.datasets);
                combinedComplaintTrendChart.updateOptions({ xaxis: { categories: data.trend.labels } });
            }

        }

        // Livewire initialized then render charts
        setTimeout(() => {
            if (window.ApexCharts && $wire.chartData) {
                initCharts($wire.chartData);
            } else {
                let inter = setInterval(() => {
                    if(window.ApexCharts && $wire.chartData) {
                        initCharts($wire.chartData);
                        clearInterval(inter);
                    }
                }, 100);
            }
        }, 100);

        // Filter updates
        $wire.on('update-charts', (e) => {
            let data = e;
            // Handle wrapper payload array from Livewire 3
            if(Array.isArray(e) && e.length > 0) {
               data = e[0];
            }
            initCharts(data);
        });
    </script>
    @endscript
</div>