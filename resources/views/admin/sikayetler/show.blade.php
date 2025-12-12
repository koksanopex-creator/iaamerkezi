<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Şikayet Detayı: #{{ $sikayet->id }} - {{ $sikayet->musteri_sikayet_konusu }}
        </h2>
    </x-slot>

    {{-- Fancybox CSS Linki --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-gradient-to-br from-white to-gray-50 border-b border-gray-200">

                    <!-- Başlık ve Butonlar -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    {{ $sikayet->musteri_adi }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    {{ $sikayet->musteri_sikayet_konusu }}
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.sikayetler.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 disabled:opacity-25 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Geri Dön
                            </a>

                            @can('update', $sikayet)
                            <a href="{{ route('admin.sikayetler.edit', $sikayet) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-500 hover:to-indigo-600 active:from-indigo-700 active:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-25 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Düzenle
                            </a>
                            @endcan
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- Sol Taraf - Şikayet Detayları -->
                        <div class="md:col-span-2 space-y-6">

                        {{-- === YENİ EKLENEN PROJE DURUM KARTI === --}}
    @if($sikayet->iaaProjesi)
        @php
            $pDurum = $sikayet->iaaProjesi->durum;
            $pRenk = match($pDurum) {
                'Bölüm Onayı Bekliyor' => 'purple',
                'Yönetici Onayı Bekliyor' => 'blue',
                'Revize Ediliyor' => 'orange',
                'Tamamlandı' => 'green',
                'Tamamlanması Reddedildi', 'Reddedildi' => 'red',
                default => 'gray'
            };
        @endphp
        
        <div class="border border-{{ $pRenk }}-200 rounded-xl p-6 bg-{{ $pRenk }}-50 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-{{ $pRenk }}-600">
                    @if($pDurum == 'Bölüm Onayı Bekliyor')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    @elseif($pDurum == 'Yönetici Onayı Bekliyor')
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($pDurum == 'Tamamlandı')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div>
                    <h4 class="text-sm font-bold text-{{ $pRenk }}-800 uppercase tracking-wide">Proje Süreç Durumu</h4>
                    <p class="text-lg font-semibold text-{{ $pRenk }}-900">{{ $pDurum }}</p>
                    @if($pDurum == 'Bölüm Onayı Bekliyor')
                        <p class="text-xs text-{{ $pRenk }}-700 mt-1">Bölüm yöneticisi onayı bekleniyor.</p>
                    @elseif($pDurum == 'Yönetici Onayı Bekliyor')
                        <p class="text-xs text-{{ $pRenk }}-700 mt-1">Üst yönetici onayı bekleniyor.</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" target="_blank" class="hidden sm:inline-flex items-center px-4 py-2 bg-white border border-{{ $pRenk }}-300 rounded-lg text-sm font-medium text-{{ $pRenk }}-700 hover:bg-white/50 transition-colors">
                Projeye Git <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    @endif
    {{-- === PROJE DURUM KARTI SONU === --}}

    @if($sikayet->musteri_feedback)
    @php
        // Duruma göre renk ve ikon belirleme mantığı
        $feedbackColor = match($sikayet->musteri_feedback) {
            'Onaylandı' => 'green',
            'Reddedildi' => 'red',
            'Revizyon İstendi' => 'yellow',
            default => 'gray'
        };
        
        // Tarih formatı (Veritabanındaki güncellenme tarihini kullanıyoruz)
        $islemTarihi = $sikayet->updated_at->format('d.m.Y H:i');
    @endphp

    <div class="mt-5 p-5 rounded-xl shadow-sm border-l-4 bg-{{ $feedbackColor }}-50 border-{{ $feedbackColor }}-500 transition-all hover:shadow-md">
        <div class="flex items-start gap-4">
            
            {{-- 1. İKON ALANI --}}
            <div class="flex-shrink-0">
                <div class="w-10 h-10 flex justify-center items-center rounded-full bg-white shadow-sm text-{{ $feedbackColor }}-600">
                    @if($sikayet->musteri_feedback == 'Onaylandı')
                        {{-- Onay İkonu --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @elseif($sikayet->musteri_feedback == 'Reddedildi')
                        {{-- Red İkonu --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @elseif($sikayet->musteri_feedback == 'Revizyon İstendi')
                        {{-- Revizyon İkonu --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    @endif
                </div>
            </div>

            {{-- 2. İÇERİK ALANI --}}
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-lg font-bold text-{{ $feedbackColor }}-800">
                            Müşteri Kararı: {{ $sikayet->musteri_feedback }}
                        </h4>
                        @if($sikayet->musteri_feedback_note)
                            <p class="text-sm text-{{ $feedbackColor }}-700 mt-1 italic">
                                "{{ $sikayet->musteri_feedback_note }}"
                            </p>
                        @else
                            <p class="text-sm text-{{ $feedbackColor }}-600/70 mt-1 italic">
                                (Ek açıklama girilmedi)
                            </p>
                        @endif
                    </div>
                    
                    {{-- Tarih ve Saat Rozeti --}}
                    <div class="flex items-center gap-1.5 text-xs font-medium text-{{ $feedbackColor }}-700 bg-white px-3 py-1 rounded-full shadow-sm border border-{{ $feedbackColor }}-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $islemTarihi }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

                            <div class="border border-gray-200 rounded-xl p-6 bg-white shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center mb-5">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-800">Şikayet Detayları</h4>
                                </div>
                                <dl class="divide-y divide-gray-100">
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600">Detaylı Açıklama</dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2 bg-gray-50 p-3 rounded-lg">
                                            {!! nl2br(e($sikayet->musteri_sikayet_detayi)) !!}
                                        </dd>
                                    </div>
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            İletişim Bilgisi
                                        </dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-medium">
                                            {{ $sikayet->musteri_iletisim ?? 'Belirtilmemiş' }}
                                        </dd>
                                    </div>

                                    {{-- Şikayet Kategorisi --}}
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                            Şikayet Kategorisi
                                        </dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">
                                            {{ $sikayet->sikayetKategori->ad ?? 'Belirtilmemiş' }}
                                        </dd>
                                    </div>

                                    {{-- === GÜNCELLENDİ: Alt Kategori Gösterimi (Her zaman görünür) === --}}
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 bg-gray-50/50 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-600 flex items-center pl-2">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                            Alt Kategori
                                        </dt>
                                        <dd class="mt-2 text-sm text-gray-800 sm:mt-0 sm:col-span-2 pl-2 font-medium">
                                            @if($sikayet->sikayetAltKategori)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ $sikayet->sikayetAltKategori->ad }}
                                                </span>
                                            @elseif($sikayet->sikayet_alt_kategori_diger)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Diğer: {{ $sikayet->sikayet_alt_kategori_diger }}
                                                </span>
                                            @else
                                                <span class="text-gray-500 italic">Belirtilmemiş</span>
                                            @endif
                                        </dd>
                                    </div>

                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-6.998l-6 .75m-.75-7.5l6 .75m6-.75l-6 .75M3 12h18M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                            </svg>
                                            Konum Tipi
                                        </dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">
                                            {{ $sikayet->konum_tipi ?? 'Belirtilmemiş' }}
                                        </dd>
                                    </div>
 
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Şikayeti Giren Personel
                                        </dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2 flex items-center">
                                            
                                            {{-- SENARYO 1: İAA Üzerinden Gelen Kayıtlı Kullanıcı --}}
                                            @if($sikayet->iaa && $sikayet->iaa->gonderen)
                                                <a href="{{ route('profile.show', $sikayet->iaa->gonderen->id) }}" target="_blank" class="inline-flex items-center gap-2 group">
                                                    @if($sikayet->iaa->gonderen->profile_photo_path)
                                                        <img class="h-6 w-6 rounded-full object-cover border border-gray-300 group-hover:border-indigo-500 transition-colors" src="{{ asset('storage/' . $sikayet->iaa->gonderen->profile_photo_path) }}" alt="">
                                                    @else
                                                        <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] text-indigo-700 font-bold group-hover:bg-indigo-200 transition-colors">
                                                            {{ substr($sikayet->iaa->gonderen->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <span class="font-semibold text-indigo-600 hover:underline transition-colors">
                                                        {{ $sikayet->iaa->gonderen->name }}
                                                    </span>
                                                </a>
                                            
                                            {{-- SENARYO 2: İAA Üzerinden Gelen Misafir --}}
                                            @elseif($sikayet->iaa && $sikayet->iaa->guest_name)
                                                <span class="font-semibold text-gray-800 flex items-center gap-2">
                                                    <div class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] text-gray-600 font-bold">M</div>
                                                    {{ $sikayet->iaa->guest_name }}
                                                    <span class="text-[10px] bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 text-gray-500">Misafir</span>
                                                </span>

                                            {{-- SENARYO 3: Admin Panelinden Direkt Ekleyen Üye --}}
                                            @elseif($sikayet->olusturanKurulUyesi)
                                                <a href="{{ route('profile.show', $sikayet->olusturanKurulUyesi->id) }}" target="_blank" class="inline-flex items-center gap-2 group">
                                                    @if($sikayet->olusturanKurulUyesi->profile_photo_path)
                                                        <img class="h-6 w-6 rounded-full object-cover border border-gray-300 group-hover:border-indigo-500 transition-colors" src="{{ asset('storage/' . $sikayet->olusturanKurulUyesi->profile_photo_path) }}" alt="">
                                                    @else
                                                        <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] text-indigo-700 font-bold group-hover:bg-indigo-200 transition-colors">
                                                            {{ substr($sikayet->olusturanKurulUyesi->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <span class="font-semibold text-indigo-600 hover:underline transition-colors">
                                                        {{ $sikayet->olusturanKurulUyesi->name }}
                                                    </span>
                                                </a>

                                            {{-- SENARYO 4: Hiçbiri Yoksa --}}
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Sistem
                                                </span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            
                            <!-- Kanıtlar (Ek Dosyalar) Bölümü -->
                            <div class="border border-gray-200 rounded-xl p-6 bg-white shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center mb-5">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-800">Kanıtlar</h4>
                                </div>
                                
                                @if ($sikayet->dosyalar->isNotEmpty())
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                        @foreach ($sikayet->dosyalar as $dosya)
                                            @php
                                                $storagePath = str_replace('public/', '', $dosya->dosya_yolu);
                                                $fullAssetUrl = asset('storage/' . $storagePath);
                                            @endphp

                                            <div class="relative group bg-gray-100 rounded-lg overflow-hidden border">
                                                @if (Str::startsWith($dosya->mime_tipi, 'image/'))
                                                    <a href="{{ $fullAssetUrl }}" data-fancybox="gallery" data-caption="{{ $dosya->orijinal_adi }}">
                                                        <img src="{{ $fullAssetUrl }}" alt="{{ $dosya->orijinal_adi }}" class="object-cover h-24 w-full group-hover:opacity-75 transition-opacity">
                                                    </a>
                                                @else
                                                    <a href="{{ $fullAssetUrl }}" target="_blank" class="flex flex-col items-center justify-center h-24 bg-gray-200 p-2 group-hover:bg-gray-300 transition-colors">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                        <p class="text-xs text-gray-500 mt-1 truncate w-full text-center">{{ $dosya->orijinal_adi }}</p>
                                                    </a>
                                                @endif
                                                <div class="absolute bottom-0 left-0 right-0 p-1 bg-black bg-opacity-50">
                                                    <p class="text-xs text-white truncate" title="{{ $dosya->orijinal_adi }}">{{ $dosya->orijinal_adi }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-500">Bu şikayet için herhangi bir kanıt eklenmemiş.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Sağ Taraf - Durum Bilgileri -->
                        <div class="md:col-span-1">
                            <div class="border border-gray-200 rounded-xl p-6 bg-white shadow-sm hover:shadow-md transition-shadow sticky top-6">
                                <div class="flex items-center mb-5">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-800">Durum Bilgileri</h4>
                                </div>
                                <dl class="space-y-4">
                                    <div class="bg-blue-50 rounded-lg p-3">
                                        <dt class="text-xs font-medium text-blue-700 uppercase tracking-wider mb-2">Mevcut Durum</dt>
                                        <dd>
                                            {!! $sikayet->musteri_durum_badge !!}
                                        </dd>
                                    </div>
                                    <div class="pt-3 border-t border-gray-100">
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Öncelik</dt>
                                        <dd class->
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $sikayet->oncelik_badge_class }}">
                                                {{ $sikayet->musteri_oncelik ?? 'Normal' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="pt-3 border-t border-gray-100">
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Şikayet Tarihi</dt>
                                        <dd class="text-sm text-gray-900 font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $sikayet->musteri_sikayet_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->format('d.m.Y') : '-' }}
                                        </dd>
                                    </div>
                                    <div class="pt-3 border-t border-gray-100">
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Kayıt Tarihi</dt>
                                        <dd class="text-sm text-gray-900 font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $sikayet->created_at->format('d.m.Y H:i') }}
                                        </dd>
                                    </div>
                                    <div class="pt-3 border-t border-gray-100 bg-red-50 -mx-3 px-3 pb-3 rounded-lg">
                                        <dt class="text-xs font-medium text-red-700 uppercase tracking-wider mb-1">Çözüm İçin Son Tarih</dt>
                                        <dd class="text-sm text-red-600 font-bold flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y H:i') : 'N/A' }}
                                        </dd>
                                    </div>
                                    <div class="pt-3 border-t border-gray-100">
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Atanan Çözüm Takımı</dt>
                                        <dd class="text-sm">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-indigo-100 text-indigo-800">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                {{ $sikayet->cozumTakimi->ad ?? 'Atanmadı' }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div class="pt-3 border-t border-gray-100">
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Hesaplanan Puan</dt>
                                        <dd class="text-lg text-yellow-700 font-bold flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            {{ $sikayet->musteri_puan ? number_format($sikayet->musteri_puan, 0) : 'N/A' }}
                                        </dd>
                                    </div>

                                </dl>
                            </div>
                        </div>

                    </div>

                    <!-- Çözüm Notları -->
                    @if($sikayet->musteri_cozum_notlari)
                    <div class="mt-6 border border-green-200 rounded-xl p-6 bg-gradient-to-br from-green-50 to-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Çözüm Notları</h4>
                        </div>
                        <div class="text-sm text-gray-800 bg-white rounded-lg p-4 border border-green-100">
                            {!! nl2br(e($sikayet->musteri_cozum_notlari)) !!}
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    
    {{-- Fancybox JS Linki ve Başlatma Scripti --}}
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {
                // Your custom options
            });
        });
    </script>
</x-app-layout>