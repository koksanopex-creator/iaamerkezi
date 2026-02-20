<div class="space-y-6">
    {{-- Canlı bildirim mesajı --}}
    @if (session()->has('yeniSikayet'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
            <p class="font-bold">🎉 Yeni Şikayet!</p>
            <p>{{ session('yeniSikayet') }}</p>
        </div>
    @endif

    {{-- KPI KARTLARI --}}
    {{-- === TARİH FİLTRESİ === --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-2 flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-sm font-bold text-gray-700">Tarih Aralığı:</span>
        </div>
        <div class="flex items-center gap-2">
            <input type="date" wire:model.live="startDate" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-700">
            <span class="text-gray-400">-</span>
            <input type="date" wire:model.live="endDate" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-700">
        </div>
        @if($startDate || $endDate)
            <button wire:click="clearFilter" class="text-xs text-red-500 hover:text-red-700 font-medium underline">
                Filtreyi Temizle
            </button>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100 cursor-pointer transition-all hover:scale-105 {{ $activeFilter === 'toplam' ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}" wire:click="setFilter('toplam')">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Toplam Şikayet</h4>
            <p class="text-3xl font-black text-blue-600">{{ $kpi['toplam'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100 cursor-pointer transition-all hover:scale-105 {{ $activeFilter === 'yeni' ? 'ring-2 ring-yellow-500 bg-yellow-50' : '' }}" wire:click="setFilter('yeni')">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Yeni (Beklemede)</h4>
            <p class="text-3xl font-black text-yellow-600">{{ $kpi['yeni'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100 cursor-pointer transition-all hover:scale-105 {{ $activeFilter === 'islemde' ? 'ring-2 ring-indigo-500 bg-indigo-50' : '' }}" wire:click="setFilter('islemde')">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">İşlemde</h4>
            <p class="text-3xl font-black text-indigo-600">{{ $kpi['islemde'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100 cursor-pointer transition-all hover:scale-105 {{ $activeFilter === 'cozuldu' ? 'ring-2 ring-green-500 bg-green-50' : '' }}" wire:click="setFilter('cozuldu')">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Çözülen/Kapatılan</h4>
            <p class="text-3xl font-black text-green-600">{{ $kpi['cozuldu'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100 cursor-pointer transition-all hover:scale-105 {{ $activeFilter === 'talep_kapatilan' ? 'ring-2 ring-gray-500 bg-gray-50' : '' }}" wire:click="setFilter('talep_kapatilan')">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Talep Kapanan</h4>
            <p class="text-3xl font-black text-gray-600">{{ $kpi['talep_kapatilan'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100 cursor-pointer transition-all hover:scale-105 {{ $activeFilter === 'hatali_bildirim' ? 'ring-2 ring-rose-500 bg-rose-50' : '' }}" wire:click="setFilter('hatali_bildirim')">
            <h4 class="text-sm font-semibold text-gray-500 uppercase">Hatalı Bildirim</h4>
            <p class="text-3xl font-black text-rose-500">{{ $kpi['hatali_bildirim'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border {{ $kpi['gecikmis'] > 0 ? 'border-red-300 bg-red-50' : 'border-gray-100' }} cursor-pointer transition-all hover:scale-105 {{ $activeFilter === 'gecikmis' ? 'ring-2 ring-red-500' : '' }}" wire:click="setFilter('gecikmis')">
            <h4 class="text-sm font-semibold {{ $kpi['gecikmis'] > 0 ? 'text-red-700' : 'text-gray-500' }} uppercase">Gecikmiş (İşlemde)</h4>
            <p class="text-3xl font-black {{ $kpi['gecikmis'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $kpi['gecikmis'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-100 cursor-pointer transition-all hover:scale-105 {{ $activeFilter === 'projeye_donusen' ? 'ring-2 ring-purple-500 bg-purple-50' : '' }}" wire:click="setFilter('projeye_donusen')">
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
                    {{ match($activeFilter) {
                        'yeni' => 'Yeni Şikayetler',
                        'islemde' => 'İşlemdeki Şikayetler',
                        'cozuldu' => 'Çözülen/Kapatılan Şikayetler',
                        'gecikmis' => 'Gecikmiş Şikayetler',
                        'projeye_donusen' => 'Projeye Dönüşen Şikayetler',
                        'talep_kapatilan' => 'Talep Olarak Kapatılan Şikayetler',
                        'hatali_bildirim' => 'Hatalı Bildirim Olarak Kapatılanlar',
                        default => 'Son Şikayet Kayıtları'
                    } }}
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 3%;">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Kayıt Tarihi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 11%;">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Müşteri İsmi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 17%;">Şikayet Başlığı</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Durum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Müşteri Kararı</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Son Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%;">Resim</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 7%;">Yorumlar</th> {{-- YENİ --}}
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 13%;">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($sonSikayetler as $sikayet)
                                @php
                                    $isExternal = ($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->is_personnel == 0) || ($sikayet->user_id && !$sikayet->olusturanKurulUyesi);
                                    
                                    // === RENKLENDİRME GÜNCELLENDİ ===
                                    $rowBg  = 'bg-white hover:bg-gray-50';
                                    $rowBar = 'border-l-4 border-gray-200';
                                    
                                    if ($isExternal) {
                                        // DIŞ KAYNAKLI - Kırmızı Uyarı
                                        $rowBg  = 'bg-red-50 hover:bg-red-100';
                                        $rowBar = 'border-l-4 border-red-600';
                                    } elseif ($sikayet->musteri_durum === 'İşlemde') {
                                        $rowBg  = 'bg-blue-100/30 hover:bg-blue-100/50';
                                        $rowBar = 'border-l-4 border-blue-500';
                                    } elseif ($sikayet->musteri_durum === 'Yeni') {
                                        $rowBg  = 'bg-yellow-100/30 hover:bg-yellow-100/50';
                                        $rowBar = 'border-l-4 border-yellow-500';
                                    } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi','Kapatıldı'])) {
                                        $rowBg  = 'bg-green-100/30 hover:bg-green-100/50';
                                        $rowBar = 'border-l-4 border-green-500';
                                    } elseif (in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) {
                                        $rowBg  = 'bg-gray-100/50 hover:bg-gray-200/50';
                                        $rowBar = 'border-l-4 border-gray-400';
                                    } elseif (in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) {
                                        $rowBg  = 'bg-rose-50 hover:bg-rose-100';
                                        $rowBar = 'border-l-4 border-rose-600';
                                    } else { 
                                        $rowBg  = 'bg-gray-100/50 hover:bg-gray-200/50';
                                        $rowBar = 'border-l-4 border-gray-400';
                                    }
                                @endphp

                                <tr class="{{ $rowBg }} {{ $rowBar }} transition-all duration-200">
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-600">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 whitespace-nowrap">
                                        {{ $sikayet->created_at->format('d.m.Y') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 truncate" title="{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}">
                                        {{ $sikayet->sikayetKategori->ad ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <span class="truncate max-w-[180px] block" title="{{ $sikayet->musteri_adi }}">{{ $sikayet->musteri_adi }}</span>
                                            @if($isExternal)
                                                <span class="flex-shrink-0 inline-flex items-center justify-center w-6 h-6 rounded bg-red-100 text-red-600 border border-red-200 cursor-help transition-colors hover:bg-red-200" title="MÜŞTERİ GİRİŞİ (Personel Dışı Kayıt)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 truncate" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                        {{ $sikayet->musteri_sikayet_konusu }}
                                        @if($sikayet->iadeler_count > 0)
                                            <span class="ml-2 inline-flex items-center gap-1 text-red-700 bg-red-100 px-2 py-0.5 rounded-full border border-red-200 font-bold text-[10px]" title="Bu şikayet ile ilişkili iade kaydı mevcut">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                                                İADE VAR
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <div class="flex flex-col gap-1.5">
                                            <div>
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($sikayet->musteri_durum == 'Yeni') bg-yellow-100 text-yellow-800
                                                    @elseif($sikayet->musteri_durum == 'İşlemde') bg-blue-100 text-blue-800
                                                    @elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) bg-green-100 text-green-800
                                                    @elseif(in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) bg-gray-100 text-gray-800 border border-gray-300 font-bold
                                                    @elseif(in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) bg-rose-100 text-rose-800 border border-rose-200 line-through
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $sikayet->musteri_durum }}
                                                </span>
                                            </div>
                                            @if(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']) && $sikayet->iaaProjesi)
                                                <div class="flex items-center gap-1 opacity-90 pl-1">
                                                    @php
                                                        $pDurum = $sikayet->iaaProjesi->durum;
                                                        $isFaulty = Str::contains($pDurum, 'hatali_bildirim');
                                                        $isRequest = Str::contains($pDurum, 'talep');
                                                        $tooltipText = $isFaulty ? 'Hatalı Bildirim Olarak Kapatıldı' : ($isRequest ? 'Talep Olarak Kapatıldı' : 'Proje Durumu: ' . $pDurum);
                                                    @endphp
                                                    
                                                    @if($isFaulty)
                                                        <div class="group relative cursor-help">
                                                            <svg class="w-5 h-5 text-red-500 hover:text-red-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                            </svg>
                                                            <!-- Tooltip -->
                                                            <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                                                {{ $tooltipText }}
                                                            </span>
                                                        </div>
                                                    @elseif($isRequest)
                                                        <div class="group relative cursor-help">
                                                            <svg class="w-5 h-5 text-blue-500 hover:text-blue-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            <!-- Tooltip -->
                                                            <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                                                {{ $tooltipText }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div title="{{ $pDurum }}">
                                                            {!! $sikayet->iaaProjesi->durum_etiketi !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        @if($sikayet->musteri_feedback)
                                            @php
                                                $renk = match($sikayet->musteri_feedback) {
                                                    'Onaylandı' => 'text-green-700 bg-green-50 border-green-200',
                                                    'Reddedildi' => 'text-red-700 bg-red-50 border-red-200',
                                                    default => 'text-yellow-700 bg-yellow-50 border-yellow-200'
                                                };
                                            @endphp
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border {{ $renk }}">
                                                @if($sikayet->musteri_feedback == 'Onaylandı')
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                @elseif($sikayet->musteri_feedback == 'Reddedildi')
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                @endif
                                                <span class="text-xs font-bold uppercase">{{ $sikayet->musteri_feedback }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
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
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($sikayet->musteri_durum == 'Yeni') bg-yellow-100 text-yellow-800
                                            @elseif($sikayet->musteri_durum == 'İşlemde') bg-blue-100 text-blue-800
                                            @elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $sikayet->musteri_durum }}
                                        </span>
                                        @if(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']) && $sikayet->iaaProjesi)
                                            <div class="flex items-center gap-1 opacity-90 justify-end">
                                                <span class="text-[9px] text-gray-400 font-bold uppercase">İAA:</span>
                                                <div class="inline-block whitespace-nowrap overflow-hidden">
                                                    {!! $sikayet->iaaProjesi->durum_etiketi !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Kart Gövdesi: Kategori, Başlık, Müşteri --}}
                                <div>
                                    <p class="text-xs text-gray-500 uppercase truncate" title="{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}">{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}</p>
                                    
                                    <div class="flex items-start gap-1">
                                        <p class="text-base font-semibold text-gray-900 truncate" title="{{ $sikayet->musteri_sikayet_konusu }}">{{ $sikayet->musteri_sikayet_konusu }}</p>
                                        @if($sikayet->iadeler_count > 0)
                                            <span class="flex-shrink-0 inline-flex items-center text-[10px] text-red-600 bg-red-50 px-1 rounded border border-red-100" title="İade Kaydı Var">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <p class="text-sm font-medium text-gray-700 truncate" title="{{ $sikayet->musteri_adi }}">{{ $sikayet->musteri_adi }}</p>
                                        @if(($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->is_personnel == 0) || ($sikayet->user_id && !$sikayet->olusturanKurulUyesi))
                                            <span class="flex-shrink-0 inline-flex items-center justify-center w-5 h-5 rounded bg-red-100 text-red-600 border border-red-200" title="MÜŞTERİ GİRİŞİ (Personel Dışı)">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </span>
                                        @endif
                                    </div>
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

    {{-- === YENİ EKLENEN DETAYLI ANALİZ GRAFİKLERİ (BURAYA TAŞINDI) === --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Bölümlere Göre Şikayet Dağılımı</h3>
                <span class="text-xs font-medium bg-indigo-100 text-indigo-800 px-2.5 py-0.5 rounded">Kategori Bazlı</span>
            </div>
            {{-- ÖNEMLİ: wire:ignore ekliyoruz ki Livewire güncellemelerinde grafik silinmesin --}}
            <div id="bolumKategoriChart" wire:ignore></div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Sorun Türleri Yoğunluk Haritası</h3>
                <span class="text-xs font-medium bg-purple-100 text-purple-800 px-2.5 py-0.5 rounded">Alt Kategoriler</span>
            </div>
            <div id="altKategoriChart" wire:ignore></div>
        </div>
    </div>

    {{-- === YENİ: MÜŞTERİ MEMNUNİYET GRAFİKLERİ === --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" wire:ignore>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Müşteri Geri Bildirim Dağılımı</h3>
            <div id="customerFeedbackChart"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Bölüm Bazlı Müşteri Memnuniyeti</h3>
            <div id="deptSatisfactionChart"></div>
        </div>
    </div>

    {{-- === YENİ: İADE İSTATİSTİKLERİ VE TABLOSU === --}}
    <div class="space-y-6 mt-8">
        <h3 class="text-xl font-bold text-gray-800 border-b pb-2 flex items-center gap-2">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            İade ve Ürün Red Analizleri
        </h3>
        
        {{-- İade Kartları ve Grafikleri --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 1. Kart: Toplam İade Tutarları (Carousel veya Liste) --}}
            <div class="bg-gradient-to-br from-red-50 to-white p-6 rounded-xl shadow-lg border border-red-100 flex flex-col justify-center items-center text-center">
                <h4 class="text-xs font-bold text-red-400 uppercase tracking-widest mb-4">Toplam İade Tutarları</h4>
                
                @if(isset($toplamIadeMiktarlari) && count($toplamIadeMiktarlari) > 0)
                    <div class="space-y-3 w-full">
                        @foreach($toplamIadeMiktarlari as $tutar)
                            <div class="flex justify-between items-center bg-white px-3 py-2 rounded shadow-sm border border-red-50">
                                <span class="text-sm text-gray-500 font-medium">{{ $tutar->birim }}</span>
                                <span class="text-lg font-black text-red-700">{{ number_format($tutar->total, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xl font-black text-red-300">İade Yok</p>
                @endif
                <p class="text-[10px] text-red-400 mt-4 font-medium">Tüm zamanların toplamı</p>
            </div>

            {{-- 2. Kart: İadeli Şikayet Oranı (Donut) --}}
            <div class="bg-white p-4 rounded-xl shadow-lg border border-gray-100 relative" wire:ignore>
                <h4 class="text-xs font-bold text-gray-500 uppercase absolute top-4 left-4 z-10">İadeli Şikayet Oranı</h4>
                <div id="iadeliOranChart" class="mt-4"></div>
            </div>

            {{-- 3. Kart: Bölümlere Göre İade Tutarı (Çoklu Bar) --}}
            <div class="bg-white p-4 rounded-xl shadow-lg border border-gray-100" wire:ignore>
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Bölümlere Göre İade Miktarı</h4>
                <div id="bolumIadeChartsContainer" class="space-y-4 pr-1">
                    {{-- JS ile buraya grafikler basılacak --}}
                </div>
            </div>

            {{-- 4. Kart: Bölüm Bazlı Şikayet Sayıları (Stacked Bar) --}}
            <div class="bg-white p-4 rounded-xl shadow-lg border border-gray-100" wire:ignore>
                 <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Bölüm İadeli/İadesiz Şikayet</h4>
                 <div id="bolumIadeCountChart"></div>
            </div>
        </div>

        {{-- İade Tablosu (Inline Copy of Partial) --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
             <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Son İade ve Ürün Red Kayıtları</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İade Tarihi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri / Bölüm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şikayet & Proje</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün / Sebep</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Miktar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(isset($iadeVerileri))
                            @forelse($iadeVerileri as $iade)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="font-medium text-gray-900">{{ $iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : $iade->created_at->format('d.m.Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $iade->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-gray-900 line-clamp-1" title="{{ $iade->musteriSikayeti->musteri_adi ?? '' }}">{{ $iade->musteriSikayeti->musteri_adi ?? '-' }}</span>
                                            @if($iade->musteriSikayeti->sikayetKategori && $iade->musteriSikayeti->sikayetKategori->bolum)
                                                <span class="text-xs inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium w-fit">{{ $iade->musteriSikayeti->sikayetKategori->bolum->ad }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium line-clamp-1 py-1">{{ $iade->musteriSikayeti->musteri_sikayet_konusu ?? '-' }}</div>
                                        
                                        <div class="flex items-center gap-2 mt-2">
                                            {{-- Şikayet Detay Butonu --}}
                                            <a href="{{ route('admin.sikayetler.show', $iade->musteriSikayeti->id) }}" 
                                               class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider rounded border border-gray-200 hover:bg-gray-200 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Şikayet #{{ $iade->musteriSikayeti->id }}
                                            </a>
                                            
                                            {{-- Proje Linki (Varsa) --}}
                                            @if($iade->musteriSikayeti->iaaProjesi)
                                                <a href="{{ route('proje.workspace.show', $iade->musteriSikayeti->iaaProjesi->id) }}" 
                                                   class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider rounded border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    Proje #{{ $iade->musteriSikayeti->iaaProjesi->id }}
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $iade->urun_turu }}</div>
                                        <div class="text-xs text-red-500">{{ $iade->iade_sebebi }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full bg-red-100 text-red-800">
                                            {{ number_format($iade->miktar, 0, ',', '.') }} {{ $iade->birim }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Kayıt yok.</td></tr>
                            @endforelse
                        @else
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Veri yok.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($iadeVerileri) && method_exists($iadeVerileri, 'links'))
                <div class="px-6 py-4 border-t border-gray-100">{{ $iadeVerileri->links() }}</div>
            @endif
        </div>
    </div>
    {{-- ============================================================== --}}

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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        // 1. Pasta Grafik
        var feedbackOptions = {
            series: [
                {{ $feedbackCounts['Onaylandı'] ?? 0 }}, 
                {{ $feedbackCounts['Reddedildi'] ?? 0 }}, 
                {{ $feedbackCounts['Revizyon İstendi'] ?? 0 }}
            ],
            chart: { type: 'donut', height: 300, fontFamily: 'inherit' },
            labels: ['Onaylandı', 'Reddedildi', 'Revizyon'],
            colors: ['#10B981', '#EF4444', '#F59E0B'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
            plotOptions: { pie: { donut: { size: '60%' } } }
        };
        window.customerFeedbackChart = new ApexCharts(document.querySelector("#customerFeedbackChart"), feedbackOptions);
        window.customerFeedbackChart.render();

        // 2. Sütun Grafik
        var deptOptions = {
            series: [{
                name: 'Onaylandı',
                data: @json($bolumMemnuniyeti->pluck('onay_sayisi'))
            }, {
                name: 'Reddedildi',
                data: @json($bolumMemnuniyeti->pluck('red_sayisi'))
            }, {
                name: 'Revizyon',
                data: @json($bolumMemnuniyeti->pluck('revizyon_sayisi'))
            }],
            chart: { type: 'bar', height: 300, stacked: true, fontFamily: 'inherit', toolbar: {show: false} },
            plotOptions: { bar: { horizontal: false, borderRadius: 4, columnWidth: '50%' } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: @json($bolumMemnuniyeti->pluck('bolum_adi')),
            },
            colors: ['#10B981', '#EF4444', '#F59E0B'],
            legend: { position: 'top' },
            fill: { opacity: 1 }
        };
        window.deptSatisfactionChart = new ApexCharts(document.querySelector("#deptSatisfactionChart"), deptOptions);
        window.deptSatisfactionChart.render();

        // 3. İadeli Şikayet Oranı (Donut)
        var iadeliOranOptions = {
            series: [{{ $iadeliSikayetSayisi ?? 0 }}, {{ $iadesizSikayetSayisi ?? 0 }}],
            chart: { type: 'donut', height: 220, fontFamily: 'inherit' },
            labels: ['İadesi Var', 'İadesi Yok'],
            colors: ['#EF4444', '#E5E7EB'], // Kırmızı vs Gri
            legend: { position: 'bottom', fontSize: '12px' },
            dataLabels: { enabled: false },
            plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, showAlways: true, label: 'Toplam', fontSize: '12px', fontWeight: 600, color: '#373d3f' } } } } }
        };
        window.iadeliOranChart = new ApexCharts(document.querySelector("#iadeliOranChart"), iadeliOranOptions);
        window.iadeliOranChart.render();

        // 4. Bölüm İade Miktarları (Birim Bazlı - Çoklu Grafik)
        window.renderBolumIadeCharts = function(dataSets) {
            const container = document.querySelector("#bolumIadeChartsContainer");
            container.innerHTML = ''; // Temizle
            
            // dataSets: { 'KG': {labels:[], series:[]}, 'Adet': ... }
            if(!dataSets) return;
            
            Object.keys(dataSets).forEach(unit => {
                const chartData = dataSets[unit];
                // Wrapper Div
                const wrapper = document.createElement('div');
                wrapper.className = 'border-b border-gray-50 pb-2 last:border-0';
                
                // Başlık
                const title = document.createElement('h5');
                title.className = 'text-[10px] font-bold text-gray-400 uppercase mb-1';
                title.innerText = unit + ' Bazında';
                wrapper.appendChild(title);
                
                // Chart Div
                const chartEl = document.createElement('div');
                wrapper.appendChild(chartEl);
                container.appendChild(wrapper);
                
                var options = {
                    series: [{ name: 'Miktar', data: chartData.series }],
                    chart: { type: 'bar', height: 120, toolbar: {show: false}, fontFamily: 'inherit' },
                    plotOptions: { bar: { borderRadius: 2, horizontal: true, barHeight: '60%' } },
                    dataLabels: { enabled: true, formatter: function (val) { return val.toLocaleString('tr-TR') + ' ' + unit; }, offsetX: 5, style: { fontSize: '10px' } },
                    xaxis: { categories: chartData.labels, labels: { show: false }, axisBorder: {show:false}, axisTicks: {show:false} },
                    colors: ['#EF4444'],
                    grid: { show: false, padding: { top: 0, bottom: 0 } },
                    tooltip: { theme: 'light' }
                };
                new ApexCharts(chartEl, options).render();
            });
        };
        // Initial render
        window.renderBolumIadeCharts(@json($bolumIadeChartData ?? []));

        // 5. Bölüm İadeli/İadesiz Şikayet (Stacked Bar)
        var bolumIadeCountOptions = {
            series: @json($bolumIadeSayilariSeries ?? []),
            chart: { type: 'bar', height: 250, stacked: true, toolbar: {show: false}, fontFamily: 'inherit' },
            plotOptions: { bar: { horizontal: false, borderRadius: 2, columnWidth: '50%' } },
            xaxis: { categories: @json($bolumIadeSayilariLabels ?? []), labels: { rotate: -45, style: { fontSize: '10px' } } },
            legend: { position: 'top', fontSize: '11px' },
            colors: ['#EF4444', '#E5E7EB'], // Kırmızı (İadeli), Gri (İadesiz)
            dataLabels: { enabled: false }, // Karmaşayı önlemek için kapattım
            tooltip: { y: { formatter: function (val) { return val + " Şikayet" } } }
        };
        window.bolumIadeCountChart = new ApexCharts(document.querySelector("#bolumIadeCountChart"), bolumIadeCountOptions);
        window.bolumIadeCountChart.render();
    });
</script>
</div>