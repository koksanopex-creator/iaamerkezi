<div class="space-y-6">
    {{-- Canlı bildirim mesajı --}}
    @if (session()->has('yeniSikayet'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
            <p class="font-bold">🎉 Yeni Şikayet!</p>
            <p>{{ session('yeniSikayet') }}</p>
        </div>
    @endif

    {{-- KPI KARTLARI --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Toplam Şikayet</h4>
            <p class="text-3xl font-black text-blue-600">{{ $kpi['toplam'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Yeni (Beklemede)</h4>
            <p class="text-3xl font-black text-yellow-600">{{ $kpi['yeni'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">İşlemde</h4>
            <p class="text-3xl font-black text-indigo-600">{{ $kpi['islemde'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Çözülen/Kapatılan</h4>
            <p class="text-3xl font-black text-green-600">{{ $kpi['cozuldu'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border {{ $kpi['gecikmis'] > 0 ? 'border-red-300 bg-red-50' : 'border-gray-100' }}">
            <h4 class="text-sm font-semibold {{ $kpi['gecikmis'] > 0 ? 'text-red-700' : 'text-gray-500' }} uppercase">Gecikmiş (İşlemde)</h4>
            <p class="text-3xl font-black {{ $kpi['gecikmis'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $kpi['gecikmis'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Projeye Dönüşen</h4>
            <p class="text-3xl font-black text-purple-600">{{ $kpi['projeye_donusen'] }}</p>
        </div>
    </div>

    {{-- === YENİ EKLENEN SON 10 ŞİKAYET TABLOSU (GÜNCELLENDİ) === --}}
    <div class="space-y-6" x-data="{ open: true }"> {{-- Varsayılan açık (open: true) --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            
            {{-- Başlık (Açma/Kapatma Butonu) --}}
            <div class="flex justify-between items-center p-4 md:p-6 cursor-pointer hover:bg-gray-50/50 transition-colors" @click="open = !open">
                <h3 class="text-lg font-semibold text-gray-900">
                    Son 10 Şikayet Kaydı
                </h3>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            
            {{-- Açılır/Kapanır İçerik --}}
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="border-t border-gray-200"
                 style="display: none;" {{-- Alpine'in yönetmesi için başlangıçta gizli --}}
                 >
                
                {{-- === 1. MASAÜSTÜ TABLO (Mobilde gizli) === --}}
                {{-- DÜZELTME: overflow-x-auto kaldırıldı --}}
                <div class="hidden md:block overflow-hidden"> 
                    {{-- DÜZELTME: table-auto min-w-full yerine table-fixed w-full --}}
                    <table class="w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%;">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Kayıt Tarihi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">Müşteri İsmi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 20%;">Şikayet Başlığı</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Durum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Son Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Resim</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%;">Yorumlar</th> {{-- YENİ --}}
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($sonSikayetler as $sikayet)
                                @php
                                    // === RENKLENDİRME GÜNCELLENDİ (ELSE EKLENDİ) ===
                                    $rowBg  = 'bg-white hover:bg-gray-50';
                                    $rowBar = 'border-l-4 border-gray-200'; // Varsayılan (diğer durumlar)
                                    if ($sikayet->musteri_durum === 'İşlemde') {
                                        $rowBg  = 'bg-blue-100/30 hover:bg-blue-100/50';
                                        $rowBar = 'border-l-4 border-blue-500';
                                    } elseif ($sikayet->musteri_durum === 'Yeni') {
                                        $rowBg  = 'bg-yellow-100/30 hover:bg-yellow-100/50';
                                        $rowBar = 'border-l-4 border-yellow-500';
                                    } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi','Kapatıldı'])) {
                                        $rowBg  = 'bg-green-100/30 hover:bg-green-100/50';
                                        $rowBar = 'border-l-4 border-green-500';
                                    } else { 
                                        // Diğer tüm durumlar için (örn: Yeniden Açıldı)
                                        $rowBg  = 'bg-gray-100/50 hover:bg-gray-200/50';
                                        $rowBar = 'border-l-4 border-gray-400';
                                    }
                                @endphp

                                <tr class="{{ $rowBg }} {{ $rowBar }} transition-all duration-200">
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-600">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 whitespace-nowrap">
                                        {{ $sikayet->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 truncate" title="{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}">
                                        {{ $sikayet->sikayetKategori->ad ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 truncate" title="{{ $sikayet->musteri_adi }}">
                                        {{ $sikayet->musteri_adi }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 truncate" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                        {{ $sikayet->musteri_sikayet_konusu }}
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($sikayet->musteri_durum == 'Yeni') bg-yellow-100 text-yellow-800
                                            @elseif($sikayet->musteri_durum == 'İşlemde') bg-blue-100 text-blue-800
                                            @elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $sikayet->musteri_durum }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                        {{ $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500">
                                        {{-- Resim Önizlemeleri --}}
                                        <div class="flex items-center space-x-1">
                                            @php 
                                                $imageFiles = $sikayet->dosyalar->filter(function($dosya) {
                                                    return Str::startsWith($dosya->mime_tipi, 'image/');
                                                });
                                            @endphp
                                            
                                            @forelse ($imageFiles->take(2) as $dosya) {{-- En fazla 2 resim göster --}}
                                                <a href="{{ asset('storage/' . $dosya->dosya_yolu) }}" target="_blank" title="{{ $dosya->orijinal_adi }}">
                                                    <img src="{{ asset('storage/' . $dosya->dosya_yolu) }}" alt="Önizleme" class="h-8 w-8 rounded-md object-cover border border-gray-300 hover:scale-110 transition-transform">
                                                </a>
                                            @empty
                                                <span class="text-xs">Yok</span>
                                            @endforelse
                                            
                                            @if($imageFiles->count() > 2)
                                                <span class="text-xs text-gray-400 font-bold ml-1">+{{ $imageFiles->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    {{-- YENİ YORUM SÜTUNU --}}
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <div class="flex items-center space-x-1">
                                            @if($sikayet->proje_yorumlari_count > 0)
                                                <span class="font-bold">{{ $sikayet->proje_yorumlari_count }}</span>
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                
                                                {{-- Müşteri Yorumu Varsa İkon Ekle --}}
                                                @if($sikayet->musteri_proje_yorumlari_count > 0)
                                                    <span class="text-yellow-500" title="Müşteri Yorumu Var">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-xs text-gray-400">Yorum Yok</span>
                                            @endif
                                        </div>
                                    </td>
                                    {{-- === YENİ BUTON SÜTUNU === --}}
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-xs">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.sikayetler.show', $sikayet) }}" target="_blank"
                                               class="inline-flex items-center px-3 py-1.5 font-semibold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                                Detay
                                            </a>
                                            @if($sikayet->iaaProjesi ?? null)
                                                <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi) }}" target="_blank"
                                                   class="inline-flex items-center px-3 py-1.5 font-semibold rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200 transition">
                                                    Proje
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Sisteme kayıtlı şikayet bulunamadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- === 2. MOBİL KART GÖRÜNÜMÜ (Masaüstünde gizli) === --}}
                <div class="md:hidden">
                    <div class="space-y-4 p-4">
                        @forelse ($sonSikayetler as $sikayet)
                            @php
                                // === RENKLENDİRME GÜNCELLENDİ (ELSE EKLENDİ) ===
                                $rowBg = 'hover:bg-gray-50';
                                $rowBar = 'border-l-4 border-gray-200'; // Varsayılan (diğer durumlar)
                                if ($sikayet->musteri_durum === 'İşlemde') {
                                    $rowBg = 'bg-blue-100/30 hover:bg-blue-100/50';
                                    $rowBar = 'border-l-4 border-blue-500';
                                } elseif ($sikayet->musteri_durum === 'Yeni') {
                                    $rowBg = 'bg-yellow-100/30 hover:bg-yellow-100/50';
                                    $rowBar = 'border-l-4 border-yellow-500';
                                } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi','Kapatıldı'])) {
                                    $rowBg = 'bg-green-100/30 hover:bg-green-100/50';
                                    $rowBar = 'border-l-4 border-green-500';
                                } else {
                                    $rowBg = 'bg-gray-100/50 hover:bg-gray-200/50';
                                    $rowBar = 'border-l-4 border-gray-400';
                                }
                            @endphp

                            <div class="rounded-lg shadow border {{ $rowBg }} {{ $rowBar }} p-4 space-y-3">
                                
                                {{-- Kart Başı: Tarih ve Durum --}}
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="font-semibold text-gray-700">#{{ $loop->iteration }}</span>
                                        <span class="text-sm text-gray-600 ml-2">{{ $sikayet->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($sikayet->musteri_durum == 'Yeni') bg-yellow-100 text-yellow-800
                                        @elseif($sikayet->musteri_durum == 'İşlemde') bg-blue-100 text-blue-800
                                        @elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $sikayet->musteri_durum }}
                                    </span>
                                </div>

                                {{-- Kart Gövdesi: Kategori, Başlık, Müşteri --}}
                                <div>
                                    <p class="text-xs text-gray-500 uppercase truncate" title="{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}">{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}</p>
                                    <p class="text-base font-semibold text-gray-900 truncate" title="{{ $sikayet->musteri_sikayet_konusu }}">{{ $sikayet->musteri_sikayet_konusu }}</p>
                                    <p class="text-sm font-medium text-gray-700 truncate" title="{{ $sikayet->musteri_adi }}">{{ $sikayet->musteri_adi }}</p>
                                </div>
                                
                                {{-- Kart Altı: Son Tarih ve Resimler --}}
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                    <div class="text-sm">
                                        <span class="text-gray-500">Son Tarih:</span>
                                        <span class="font-semibold {{ $sikayet->musteri_cozum_son_tarihi ? 'text-red-600' : 'text-gray-500' }}">
                                            {{ $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A' }}
                                        </span>
                                    </div>
                                    @php
                                        $imageFiles = $sikayet->dosyalar->filter(fn($d) => Str::startsWith($d->mime_tipi, 'image/'));
                                    @endphp
                                    <div class="flex items-center space-x-1">
                                        @forelse ($imageFiles->take(2) as $dosya)
                                            <a href="{{ asset('storage/' . $dosya->dosya_yolu) }}" target="_blank" title="{{ $dosya->orijinal_adi }}" onclick="event.stopPropagation()">
                                                <img src="{{ asset('storage/' . $dosya->dosya_yolu) }}"
                                                     class="h-8 w-8 rounded-md object-cover border border-gray-300"
                                                     alt="Önizleme">
                                            </a>
                                        @empty
                                            <span class="text-xs text-gray-400">Resim Yok</span>
                                        @endforelse
                                        @if($imageFiles->count() > 2)
                                            <span class="text-xs text-gray-400 font-bold ml-1">+{{ $imageFiles->count() - 2 }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- === YENİ BUTON ve YORUM BÖLÜMÜ === --}}
                                {{-- Yorumlar --}}
                                    <div class="flex items-center space-x-1 text-sm text-gray-700">
                                        @if($sikayet->proje_yorumlari_count > 0)
                                            <span class="font-bold">{{ $sikayet->proje_yorumlari_count }}</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            
                                            @if($sikayet->musteri_proje_yorumlari_count > 0)
                                                <span class="text-yellow-500" title="Müşteri Yorumu Var">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">Yorum Yok</span>
                                        @endif
                                    </div>
                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200">
                                    <a href="{{ route('admin.sikayetler.show', $sikayet) }}" target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                                       onclick="event.stopPropagation()">
                                        Detay
                                    </a>
                                    @if($sikayet->iaaProjesi ?? null)
                                        <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi) }}" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                                           onclick="event.stopPropagation()">
                                            Proje
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-sm text-gray-500">
                                Kayıt bulunamadı.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- === YENİ EKLENEN BUTON === --}}
                <div class="p-4 bg-gray-50 border-t border-gray-200 text-center">
                    <a href="{{ route('admin.sikayet-raporlari.tum-liste') }}" target="_blank" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 font-semibold text-sm rounded-lg hover:bg-indigo-200 transition-colors duration-200">
                        Tüm Şikayet Listesini Gör
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
                {{-- === YENİ BUTON SONU === --}}

            </div>
        </div>
    </div>
    {{-- === SON 10 ŞİKAYET TABLOSU BİTİŞİ === --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Şikayet Durum Dağılımı</h3>
            <div id="sikayetDurumChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Şikayet Kategorisi</h3>
            <div id="sikayetKategoriChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Çözüm Takımı (Şikayet Sayısı)</h3>
            <div id="sikayetTakimChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aylık Şikayet Kayıt Trendi (Son 12 Ay)</h3>
            <div id="sikayetTrendChart"></div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-green-700 mb-4">Çözülen / Kapatılan Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="cozulenChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son Çözülenler Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    @forelse ($cozulenListesi as $item)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $item->musteri_sikayet_konusu }}">{{ $item->musteri_sikayet_konusu }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->musteri_adi }}</p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="{{ route('admin.sikayetler.show', $item) }}" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    @if($item->iaaProjesi)
                                    <a href="{{ route('proje.workspace.show', $item->iaaProjesi) }}" title="Proje Çalışma Alanını Gör" class="p-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 000 2h.01a1 1 0 100-2H5zM6 15a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM4 17a1 1 0 100 2h12a1 1 0 100-2H4z"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Çözülmüş şikayet bulunamadı.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-indigo-700 mb-4">İşlemde Olan Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="islemdeChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son İşleme Alınanlar Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    @forelse ($islemdeListesi as $item)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $item->musteri_sikayet_konusu }}">{{ $item->musteri_sikayet_konusu }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->musteri_adi }}</p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="{{ route('admin.sikayetler.show', $item) }}" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    @if($item->iaaProjesi)
                                    <a href="{{ route('proje.workspace.show', $item->iaaProjesi) }}" title="Proje Çalışma Alanını Gör" class="p-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 000 2h.01a1 1 0 100-2H5zM6 15a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM4 17a1 1 0 100 2h12a1 1 0 100-2H4z"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">İşlemde olan şikayet bulunamadı.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-yellow-700 mb-4">Yeni (Beklemede) Olan Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="yeniChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son Gelenler Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    @forelse ($yeniListesi as $item)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $item->musteri_sikayet_konusu }}">{{ $item->musteri_sikayet_konusu }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->musteri_adi }}</p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="{{ route('admin.sikayetler.show', $item) }}" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Yeni şikayet bulunamadı.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-purple-700 mb-4">Projeye Dönüşen Şikayetler</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Önceliğe Göre Dağılım</h4>
                <div id="projeyeDonusenChart" class="w-full h-64"></div>
            </div>
            <div>
                <h4 class="text-base font-medium text-gray-700 mb-2">Son Dönüşenler Listesi</h4>
                <div class="max-h-64 overflow-y-auto pr-2 space-y-3">
                    @forelse ($projeyeDonusenListesi as $item)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800 truncate" title="{{ $item->musteri_sikayet_konusu }}">{{ $item->musteri_sikayet_konusu }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->musteri_adi }}</p>
                                </div>
                                <div class="flex space-x-2 flex-shrink-0 ml-2">
                                    <a href="{{ route('admin.sikayetler.show', $item) }}" title="Şikayet Detayını Gör" class="p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    @if($item->iaaProjesi)
                                    <a href="{{ route('proje.workspace.show', $item->iaaProjesi) }}" title="Proje Çalışma Alanını Gör" class="p-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM5 11a1 1 0 000 2h.01a1 1 0 100-2H5zM6 15a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM4 17a1 1 0 100 2h12a1 1 0 100-2H4z"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Projeye dönüşen şikayet bulunamadı.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aylık Çözülen Şikayet Trendi (Son 12 Ay)</h3>
        <div id="aylikCozulenChart"></div>
    </div>
</div>