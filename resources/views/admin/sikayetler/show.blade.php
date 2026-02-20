<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Şikayet Detayı: #{{ $sikayet->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.sikayetler.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Geri Dön
                </a>
                @can('update', $sikayet)
                <a href="{{ route('admin.sikayetler.edit', $sikayet) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-500 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Düzenle
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    {{-- Fancybox CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- BAŞARI MESAJI --}}
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r shadow-sm flex items-center justify-between animate-pulse">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- 1. ÜST BİLGİ KARTI (İSTATİSTİKLER, DURUM ve KATEGORİ) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 mb-6 p-4">
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 text-center divide-x divide-gray-100">
                    
                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Durum</span>
                        <div class="flex flex-col items-center gap-1.5">
                            {!! $sikayet->musteri_durum_badge !!}
                            @if($sikayet->iaaProjesi)
                                <div class="scale-75 origin-top">
                                    {!! $sikayet->iaaProjesi->durum_etiketi !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Öncelik</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold {{ $sikayet->oncelik_badge_class }}">
                            {{ $sikayet->musteri_oncelik }}
                        </span>
                    </div>

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Puan</span>
                        <div class="flex items-center gap-2">
                            {{-- Geri bildirim üzerine 'kazanilan_puan' (3 gibi değerler) gizlendi. --}}
                            {{-- Sadece Müşteri Puanı Varsa Gösteriliyor --}}
                            @if($sikayet->musteri_puan)
                                <span class="text-sm bg-yellow-100 text-yellow-800 px-2 py-1 rounded border border-yellow-200 font-bold" title="Müşteri Puanı">
                                    ★ {{ number_format($sikayet->musteri_puan, 2) }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400 italic">Puanlanmamış</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Kategori / Bölüm</span>
                        <div class="flex flex-col items-center">
                            <span class="text-sm font-bold text-gray-800">{{ $sikayet->sikayetKategori->ad ?? 'Genel' }}</span>
                            @if($sikayet->sikayetAltKategori)
                                <span class="text-xs text-gray-500">{{ $sikayet->sikayetAltKategori->ad }}</span>
                            @elseif($sikayet->sikayet_alt_kategori_diger)
                                <span class="text-xs text-gray-500">{{ $sikayet->sikayet_alt_kategori_diger }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Çözüm Süresi</span>
                        @php
                            $isResolved = in_array(trim($sikayet->musteri_durum), ['Çözümlendi', 'Kapatıldı', 'Tamamlandı']);
                            // Çözüm tarihi yoksa updated_at al, o da yoksa null
                            $solvedDate = $sikayet->musteri_cozum_tarihi ?? $sikayet->updated_at;
                        @endphp

                        {{-- DÜZELTME: Durum 'Çözümlendi' veya 'Kapatıldı' ise MUTLAKA 'Çözüldü' yazsın (tarih olmasa bile hesapla) --}}
                        @if($isResolved && $solvedDate) 
                             @php
                                $created = $sikayet->created_at;
                                // floatDiffInDays ile tam gün farkını al, yukarı yuvarla (örn: 0.1 gün -> 1 gün)
                                $diff = $created->floatDiffInDays($solvedDate);
                                $days = ceil($diff);
                                if ($days < 1) $days = 1;
                             @endphp
                             <span class="text-sm font-bold text-emerald-600 animate-pulse">
                                {{ intval($days) }} Günde Çözüldü
                             </span>
                        
                        {{-- ÇÖZÜLMEDİYSE (SÜRE SAYIYOR) --}}
                        @elseif($sikayet->musteri_cozum_son_tarihi)
                            @php
                                $daysLeft = now()->diffInDays($sikayet->musteri_cozum_son_tarihi, false);
                            @endphp
                            
                            @if($daysLeft < 0)
                                <span class="text-sm font-bold text-red-600 animate-pulse">
                                    {{ abs(intval($daysLeft)) }} Gün Geçti!
                                </span>
                                <span class="text-[10px] text-gray-400 block">{{ $sikayet->musteri_cozum_son_tarihi->format('d.m.Y') }}</span>
                            @else
                                <span class="text-sm font-bold text-green-600">
                                    {{ intval($daysLeft) }} Gün Kaldı
                                </span>
                                <span class="text-[10px] text-gray-400 block">{{ $sikayet->musteri_cozum_son_tarihi->format('d.m.Y') }}</span>
                            @endif
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- 2. YATAY SÜREÇ BARI (RENKLENDİRİLMİŞ) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 mb-6 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Müşteri Şikayeti Bölüm Süreci
                    </h3>
                    
                    {{-- MODERN BUTON --}}
                    @if($sikayet->iaaProjesi)
                        <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:border-indigo-300 transition-colors shadow-sm font-semibold text-xs uppercase tracking-wide">
                            <span class="mr-2">🚀</span>İlgili İyileştirme Projesine Git
                        </a>
                    @endif
                </div>
                
                {{-- Adımlar ve Renkler --}}
                @php
                    $steps = [
                        'Yeni' => ['color' => 'bg-yellow-400', 'text' => 'text-yellow-600', 'icon_bg' => 'bg-yellow-100'],
                        'Atandı' => ['color' => 'bg-blue-500', 'text' => 'text-blue-600', 'icon_bg' => 'bg-blue-100'],
                        'İnceleniyor' => ['color' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'icon_bg' => 'bg-indigo-100'],
                        'Çözümlendi' => ['color' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'icon_bg' => 'bg-emerald-100'],
                        'Kapatıldı' => ['color' => 'bg-gray-600', 'text' => 'text-gray-700', 'icon_bg' => 'bg-gray-100']
                    ];
                    
                    $stepKeys = array_keys($steps);
                    $currentStatus = trim($sikayet->musteri_durum);
                    
                    // Eşleşme düzeltmeleri
                    // EĞER PROJE REVİZE EDİLİYORSA -> İNCELENİYOR ADIMINA GEÇİR
                    if(($sikayet->iaaProjesi && $sikayet->iaaProjesi->durum == 'Revize Ediliyor') || $currentStatus == 'İşlemde' || $currentStatus == 'Devam Ediyor') {
                        $currentStatus = 'İnceleniyor';
                    }
                    
                    $currentIndex = array_search($currentStatus, $stepKeys);
                    if($currentIndex === false) $currentIndex = 0; 
                @endphp

                <div class="relative">
                    {{-- Arka plan çizgisi --}}
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-100 -translate-y-1/2 rounded z-0"></div>
                    
                    {{-- Aktif İlerleme Çizgisi (Rengi duruma göre değişir) --}}
                    @php
                        $activeColorClass = $steps[$stepKeys[$currentIndex]]['color'];
                        $progressWidth = ($currentIndex / (count($steps) - 1)) * 100;
                    @endphp
                    <div class="absolute top-1/2 left-0 h-1 {{ $activeColorClass }} -translate-y-1/2 rounded z-0 transition-all duration-700" style="width: {{ $progressWidth }}%"></div>

                    <div class="relative z-10 flex justify-between">
                        @foreach($steps as $key => $style)
                            @php
                                $index = array_search($key, $stepKeys);
                                $isActive = $index <= $currentIndex;
                                $isCurrent = $index === $currentIndex;
                            @endphp
                            <div class="flex flex-col items-center group">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-all duration-300 {{ $isActive ? $style['color'] . ' border-white shadow-md' : 'bg-white border-gray-200' }}">
                                    @if($isActive)
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-gray-300 group-hover:bg-gray-400"></span>
                                    @endif
                                </div>
                                <span class="mt-2 text-xs font-bold transition-colors {{ $isActive ? $style['text'] : 'text-gray-400' }}">
                                    {{ $key }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- SOL KOLON (ANA İÇERİK) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- 3. ŞİKAYET DETAYLARI --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center border-b pb-2">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Şikayet Detayları
                            </h3>
                            
                            <div class="mb-4">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Konu</span>
                                <p class="text-gray-900 font-medium text-lg">{{ $sikayet->musteri_sikayet_konusu }}</p>
                            </div>

                            <div class="mb-6">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Açıklama</span>
                                <div class="mt-2 text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100 text-sm leading-relaxed whitespace-pre-wrap font-sans">{{ $sikayet->musteri_sikayet_detayi }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center border-b pb-2">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                Üretim ve Ürün Bilgileri
                            </h3>
                            
                            @if($sikayet->teknikDetaylar->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($sikayet->teknikDetaylar as $detay)
                                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 relative group hover:border-indigo-200 transition">
                                            <span class="absolute top-2 right-2 text-[10px] font-bold text-gray-300 group-hover:text-indigo-300">#{{ $loop->iteration }}</span>
                                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                                <div>
                                                    <span class="text-xs text-gray-500 block mb-1">Lot Numarası</span>
                                                    <span class="font-mono text-sm font-bold text-gray-800">{{ $detay->lot_no ?? '-' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 block mb-1">Makine / Hat</span>
                                                    <span class="text-sm font-bold text-gray-800">
                                                        {{ $detay->machine->name ?? '-' }}
                                                        @if($detay->machine && $detay->machine->code)
                                                            <span class="text-xs text-gray-400">({{ $detay->machine->code }})</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 block mb-1">Hammadde</span>
                                                    <span class="text-sm font-bold text-gray-800">{{ $detay->genelHammadde->ad ?? '-' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 block mb-1">Ürün Versiyonu</span>
                                                    <span class="text-sm font-bold text-gray-800">{{ $detay->urunVersiyonu->ad ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($sikayet->lot_no || $sikayet->machine_id || $sikayet->genel_hammadde_id || $sikayet->urun_versiyonu_id)
                                {{-- Eski veri varsa (Migration çalışmadıysa vs) --}}
                                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div>
                                            <span class="text-xs text-gray-500 block mb-1">Lot Numarası</span>
                                            <span class="font-mono text-sm font-bold text-gray-800">{{ $sikayet->lot_no ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 block mb-1">Makine / Hat</span>
                                            <span class="text-sm font-bold text-gray-800">{{ $sikayet->machine->name ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 block mb-1">Hammadde</span>
                                            <span class="text-sm font-bold text-gray-800">{{ $sikayet->genelHammadde->ad ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 block mb-1">Ürün Versiyonu</span>
                                            <span class="text-sm font-bold text-gray-800">{{ $sikayet->urunVersiyonu->ad ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4 text-gray-400 text-sm italic">
                                    Bu şikayet için teknik detay girilmemiştir.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 5. KANIT DOSYALARI --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4 border-b pb-2">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    Kanıt Dosyaları
                                </h3>
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ $sikayet->dosyalar->count() }} Dosya</span>
                            </div>

                            @if($sikayet->dosyalar->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    @foreach($sikayet->dosyalar as $dosya)
                                        @php
                                            $extension = strtolower(pathinfo($dosya->dosya_yolu, PATHINFO_EXTENSION));
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            $isVideo = in_array($extension, ['mp4', 'mov', 'avi']);
                                            $fileUrl = asset('storage/' . $dosya->dosya_yolu);
                                        @endphp
                                        
                                        <a href="{{ $fileUrl }}" 
                                           data-fancybox="gallery" 
                                           data-caption="{{ $dosya->orijinal_adi }}"
                                           class="group relative aspect-square bg-gray-100 rounded-lg overflow-hidden border hover:border-indigo-400 transition cursor-zoom-in">
                                            
                                            @if($isImage)
                                                <img src="{{ $fileUrl }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-105" alt="Evidence">
                                            @elseif($isVideo)
                                                <div class="w-full h-full flex items-center justify-center bg-gray-900">
                                                    <svg class="w-10 h-10 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                            @else
                                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-2">
                                                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    <span class="text-xs text-center text-gray-500 font-medium truncate w-full">{{ $dosya->orijinal_adi }}</span>
                                                </div>
                                            @endif

                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all flex items-end p-2 opacity-0 group-hover:opacity-100">
                                                <span class="text-xs text-white bg-black bg-opacity-50 px-2 py-1 rounded truncate w-full">{{ $dosya->orijinal_adi }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-300 text-gray-500 italic">
                                    Dosya yüklenmemiş.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 6. ZAMAN ÇİZELGESİ / LOGLAR (Sadece Superadmin ve Yönetim) --}}
                    @role('Superadmin|Yonetim')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center border-b pb-2">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Süreç Geçmişi & Loglar
                            </h3>
                            
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach($sikayet->loglar as $log)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm text-gray-500 break-words">
                                                                <span class="font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}</span>: 
                                                                {{ $log->islem_aciklamasi }}
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                            <time datetime="{{ $log->created_at }}">{{ $log->created_at->format('d.m.Y H:i') }}</time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                    {{-- Başlangıç Logu (En altta) --}}
                                    <li>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        <span class="font-medium text-gray-900">Sistem</span>: Şikayet kaydı oluşturuldu.
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $sikayet->created_at }}">{{ $sikayet->created_at->format('d.m.Y H:i') }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endrole

                </div>

                {{-- SAĞ KOLON (SIDEBAR) --}}
                <div class="space-y-6">

                    {{-- 7. MÜŞTERİ KARTI --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Müşteri Bilgileri
                        </h3>

                        <div class="flex items-center mb-4">
                            @if($sikayet->customer && $sikayet->customer->logo_path)
                                <img src="{{ asset('storage/' . $sikayet->customer->logo_path) }}" class="w-12 h-12 rounded-lg object-contain bg-gray-50 border p-1 mr-3" alt="Logo">
                            @else
                                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-700 font-bold text-lg mr-3">
                                    {{ substr($sikayet->musteri_adi, 0, 1) }}
                                </div>
                            @endif
                            <div class="overflow-hidden">
                                <h4 class="font-bold text-gray-900 truncate" title="{{ $sikayet->musteri_adi }}">
                                    @if($sikayet->customer_id)
                                        <a href="{{ route('musteri.profil.show', $sikayet->customer_id) }}" target="_blank" class="hover:underline hover:text-indigo-600">
                                            {{ $sikayet->musteri_adi }}
                                        </a>
                                    @else
                                        {{ $sikayet->musteri_adi }}
                                    @endif
                                </h4>
                                <span class="text-xs text-gray-500 inline-block bg-gray-100 px-1.5 py-0.5 rounded mt-1">{{ $sikayet->konum_tipi ?? 'Belirtilmemiş' }}</span>
                            </div>
                        </div>

                        {{-- FİRMA İSTATİSTİKLERİ --}}
                        <div class="grid grid-cols-2 gap-2 mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="text-center">
                                <span class="block text-xs text-gray-400">Toplam</span>
                                <span class="block text-lg font-bold text-gray-800">{{ $firmaSikayetSayisi ?? '-' }}</span>
                            </div>
                            <div class="text-center border-l border-gray-200">
                                <span class="block text-xs text-gray-400">Bu Şikayet</span>
                                <span class="block text-lg font-bold text-indigo-600">{{ $kacinciSikayet ?? '-' }}.</span>
                            </div>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-gray-100">
                            <div>
                                <span class="text-xs text-gray-400 block">Firma İletişim</span>
                                <span class="text-sm font-medium text-gray-800 block break-words">{{ $sikayet->musteri_iletisim ?? '-' }}</span>
                            </div>
                            
                            @if($sikayet->yetkili_user)
                                <div>
                                    <span class="text-xs text-gray-400 block">Yetkili Kişi</span>
                                    <div class="flex items-center mt-1">
                                        <div class="w-6 h-6 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold mr-2 flex-shrink-0">
                                            {{ substr($sikayet->yetkili_user->name, 0, 1) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <span class="text-sm font-medium text-gray-800 block truncate">{{ $sikayet->yetkili_user->name }}</span>
                                            <span class="text-xs text-gray-500 block truncate">{{ $sikayet->yetkili_user->email }}</span>
                                            @if($sikayet->yetkili_user->phone)
                                                <span class="text-xs text-gray-500 block">{{ $sikayet->yetkili_user->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 8. DAHİLİ BİLGİLER --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Dahili Bilgiler
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <span class="text-xs text-gray-400 block">Şikayeti Oluşturan Personel</span>
                                <a href="{{ route('profile.show', $sikayet->olusturanKurulUyesi->id) }}" class="flex items-center mt-1 group hover:bg-gray-50 p-1 rounded -ml-1 transition">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold mr-2 flex-shrink-0">
                                        {{ substr($sikayet->olusturanKurulUyesi->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-800 group-hover:text-indigo-600 transition">{{ $sikayet->olusturanKurulUyesi->name }}</span>
                                </a>
                            </div>

                            <div>
                                <span class="text-xs text-gray-400 block">Atanan Çözüm Takımı</span>
                                @if($sikayet->cozumTakimi)
                                    <a href="{{ route('admin.cozum-takimlari.show', $sikayet->cozumTakimi->id) }}" class="text-sm font-medium text-indigo-700 font-bold block mt-1 hover:underline">
                                        {{ $sikayet->cozumTakimi->ad }}
                                    </a>
                                @else
                                    <span class="text-sm font-medium text-gray-500 italic block mt-1">Henüz Atanmadı</span>
                                @endif
                            </div>

                            <div>
                                <span class="text-xs text-gray-400 block">Şikayet Tarihi (Sistem)</span>
                                <span class="text-sm text-gray-600">{{ $sikayet->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 9. MÜŞTERİ FEEDBACK --}}
                    @if($sikayet->musteri_feedback)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">
                            <h3 class="font-bold text-gray-900 mb-4">Müşteri Geri Bildirimi</h3>
                            @php
                                $feedbackColors = [
                                    'memnun' => 'bg-green-50 border-green-200 text-green-800',
                                    'kismen_memnun' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                                    'memnun_degil' => 'bg-red-50 border-red-200 text-red-800'
                                ];
                                $feedbackClass = $feedbackColors[$sikayet->musteri_feedback_durumu] ?? 'bg-gray-50 border-gray-200 text-gray-800';
                            @endphp
                            <div class="p-4 rounded-lg border {{ $feedbackClass }}">
                                <p class="text-sm font-medium italic mb-2">"{{ $sikayet->musteri_feedback }}"</p>
                                <div class="flex justify-between items-center text-xs opacity-75">
                                    <span class="font-bold uppercase">{{ str_replace('_', ' ', $sikayet->musteri_feedback_durumu) }}</span>
                                    @if($sikayet->musteri_puan)
                                        <span>Puan: {{ $sikayet->musteri_puan }}/5</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>

    {{-- Fancybox JS --}}
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind("[data-fancybox]", {
            // Your custom options
        });
    </script>
</x-app-layout>