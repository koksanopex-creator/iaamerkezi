<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Şikayet Detayı: #{{ $sikayet->id }} - {{ $sikayet->musteri_sikayet_konusu }}
        </h2>
    </x-slot>

    {{-- Fancybox CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-gradient-to-br from-white to-gray-50 border-b border-gray-200">

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                        <div class="flex items-start space-x-4">
                            {{-- Logo Alanı --}}
                            <div class="flex-shrink-0">
                                @if($sikayet->customer && $sikayet->customer->logo_path)
                                    <img class="w-16 h-16 rounded-xl object-cover border border-gray-200 shadow-sm" src="{{ asset('storage/' . $sikayet->customer->logo_path) }}" alt="Firma Logo">
                                @else
                                    <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center shadow-sm border border-indigo-200">
                                        <span class="text-2xl font-bold text-indigo-600">{{ substr($sikayet->musteri_adi, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Firma İsmi ve Konu --}}
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2 group">
                                    @if($sikayet->customer_id)
                                        <a href="{{ route('musteri.profil.show', $sikayet->customer_id) }}" target="_blank" class="hover:text-indigo-600 hover:underline transition-colors flex items-center gap-2">
                                            {{ $sikayet->musteri_adi }}
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @else
                                        {{ $sikayet->musteri_adi }} <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded">Misafir</span>
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-600 mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
                                    {{ $sikayet->musteri_sikayet_konusu }}
                                </p>
                            </div>
                        </div>

                        {{-- Butonlar --}}
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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <div class="md:col-span-2 space-y-6">

                            {{-- PROJE DURUM KARTI --}}
                            @if($sikayet->iaaProjesi)
                                @php
                                    $pDurum = $sikayet->iaaProjesi->durum;
                                    $pRenk = match($pDurum) {
                                        'Bölüm Onayı Bekliyor' => 'purple',
                                        'Yönetici Onayı Bekliyor' => 'blue',
                                        'Revize Ediliyor' => 'orange',
                                        'Tamamlandı' => 'green',
                                        'Tamamlanması Reddedildi', 'Reddedildi' => 'red',
                                        'Yeni', 'Atandı' => 'blue',
                                        default => 'gray'
                                    };
                                @endphp
                                
                                <div class="border border-{{ $pRenk }}-200 rounded-xl p-6 bg-{{ $pRenk }}-50 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-{{ $pRenk }}-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-{{ $pRenk }}-800 uppercase tracking-wide">Proje Süreç Durumu</h4>
                                            <p class="text-lg font-semibold text-{{ $pRenk }}-900">{{ $pDurum }}</p>
                                            
                                            {{-- ATAMA BİLGİSİ --}}
                                            @if($sikayet->iaaProjesi->olusturanUser)
                                                <div class="flex items-center gap-1 mt-1 text-xs text-{{ $pRenk }}-700/80">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span class="font-bold">{{ $sikayet->iaaProjesi->olusturanUser->name }}</span> tarafından
                                                    <span class="font-bold">{{ $sikayet->iaaProjesi->created_at->format('d.m.Y') }}</span> tarihinde atandı.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" target="_blank" class="px-4 py-2 bg-white border border-{{ $pRenk }}-300 rounded-lg text-sm font-medium text-{{ $pRenk }}-700 hover:bg-white/50 transition-colors shadow-sm">
                                        Projeye Git &rarr;
                                    </a>
                                </div>
                            @endif

                            {{-- MÜŞTERİ GERİ BİLDİRİMİ --}}
                            @if($sikayet->musteri_feedback)
                                @php
                                    $feedbackColor = match($sikayet->musteri_feedback) {
                                        'Onaylandı' => 'green',
                                        'Reddedildi' => 'red',
                                        'Revizyon İstendi' => 'yellow',
                                        'Otomatik Onaylandı' => 'green',
                                        default => 'gray'
                                    };
                                @endphp
                                <div class="p-5 rounded-xl shadow-sm border-l-4 bg-{{ $feedbackColor }}-50 border-{{ $feedbackColor }}-500">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 text-{{ $feedbackColor }}-600">
                                            @if($sikayet->musteri_feedback == 'Onaylandı' || $sikayet->musteri_feedback == 'Otomatik Onaylandı')
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @elseif($sikayet->musteri_feedback == 'Reddedildi')
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-bold text-{{ $feedbackColor }}-800">Müşteri Kararı: {{ $sikayet->musteri_feedback }}</h4>
                                            <p class="text-sm text-{{ $feedbackColor }}-700 mt-1 italic">
                                                "{{ $sikayet->musteri_feedback_note ?? 'Ek açıklama girilmedi.' }}"
                                            </p>
                                            <div class="mt-2 text-xs font-semibold text-{{ $feedbackColor }}-600">
                                                İşlem Tarihi: {{ $sikayet->updated_at->format('d.m.Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- ŞİKAYET DETAYLARI PANELI --}}
                            <div class="border border-gray-200 rounded-xl p-6 bg-white shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center mb-5">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-800">Şikayet Detayları</h4>
                                </div>
                                <dl class="divide-y divide-gray-100">
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600">Açıklama</dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2 bg-gray-50 p-3 rounded-lg">
                                            {!! nl2br(e($sikayet->musteri_sikayet_detayi)) !!}
                                        </dd>
                                    </div>

                                    {{-- FİRMA İLETİŞİM BİLGİLERİ --}}
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600 flex items-center pt-2">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            Firma İletişim Bilgileri
                                        </dt>
                                        <dd class="mt-2 sm:mt-0 sm:col-span-2">
                                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                                <p class="text-sm text-gray-800 font-semibold">{{ $sikayet->musteri_iletisim ?? 'Belirtilmemiş' }}</p>
                                            </div>
                                        </dd>
                                    </div>

                                    @if($sikayet->customer && $sikayet->customer->users->first())
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600 flex items-center pt-2">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            Firma Yetkilisi İletişim Bilgisi
                                        </dt>
                                        <dd class="mt-2 sm:mt-0 sm:col-span-2">
                                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold">
                                                    {{ substr($sikayet->customer->users->first()->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900">{{ $sikayet->customer->users->first()->name }}</p>
                                                    <div class="flex items-center gap-3 text-xs text-gray-600 mt-0.5">
                                                        <span class="flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>{{ $sikayet->customer->users->first()->email }}</span>
                                                        <span class="flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>{{ $sikayet->customer->users->first()->telefon }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </dd>
                                    </div>
                                    @endif

                                    {{-- KATEGORİLER --}}
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600">Şikayet Kategorisi</dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">
                                            {{ $sikayet->sikayetKategori->ad ?? 'Belirtilmemiş' }}
                                            @if($sikayet->sikayetAltKategori)
                                                <span class="ml-2 text-gray-400">/</span> 
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ $sikayet->sikayetAltKategori->ad }}
                                                </span>
                                            @elseif($sikayet->sikayet_alt_kategori_diger)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Diğer: {{ $sikayet->sikayet_alt_kategori_diger }}
                                                </span>
                                            @endif
                                        </dd>
                                    </div>

                                    {{-- KONUM TİPİ --}}
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600">Konum Tipi</dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $sikayet->konum_tipi ?? 'Belirtilmemiş' }}
                                            </span>
                                        </dd>
                                    </div>

                                    {{-- ŞİKAYETİ GİREN PERSONEL --}}
                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600">Şikayeti Giren Personel</dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            {{-- Eğer Yönetim Panelinden Birisi Girdiyse --}}
                                            @if($sikayet->olusturanKurulUyesi)
                                                <a href="{{ route('profile.show', $sikayet->olusturanKurulUyesi->id) }}" target="_blank" class="inline-flex items-center gap-2 group">
                                                    @if($sikayet->olusturanKurulUyesi->profile_photo_path)
                                                        <img class="h-8 w-8 rounded-full object-cover border border-gray-300" src="{{ asset('storage/' . $sikayet->olusturanKurulUyesi->profile_photo_path) }}" alt="">
                                                    @else
                                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">
                                                            {{ substr($sikayet->olusturanKurulUyesi->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div class="flex flex-col">
                                                        <span class="font-semibold text-indigo-600 hover:underline">{{ $sikayet->olusturanKurulUyesi->name }}</span>
                                                        <span class="text-[10px] text-gray-500">Personel / Yönetici</span>
                                                    </div>
                                                </a>
                                            {{-- Eğer Müşteri Kendisi Girdiyse (İAA/Public) --}}
                                            @elseif($sikayet->user_id) 
                                                <span class="flex items-center gap-2">
                                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700">M</div>
                                                    <span class="font-semibold text-gray-800">{{ $sikayet->musteri_adi }} (Müşteri)</span>
                                                </span>
                                            @else
                                                <span class="text-gray-500 italic">Sistem / Bilinmiyor</span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            
                            {{-- KANITLAR --}}
                            @if ($sikayet->dosyalar->isNotEmpty())
                                <div class="border border-gray-200 rounded-xl p-6 bg-white shadow-sm">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        Kanıt Dosyaları
                                    </h4>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                        @foreach ($sikayet->dosyalar as $dosya)
                                            @php
                                                $storagePath = str_replace('public/', '', $dosya->dosya_yolu);
                                                $fullAssetUrl = asset('storage/' . $storagePath);
                                            @endphp
                                            <a href="{{ $fullAssetUrl }}" data-fancybox="gallery" class="group block relative rounded-lg overflow-hidden border border-gray-200">
                                                @if (Str::startsWith($dosya->mime_tipi, 'image/'))
                                                    <img src="{{ $fullAssetUrl }}" class="h-24 w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                @else
                                                    <div class="h-24 w-full bg-gray-50 flex flex-col items-center justify-center p-2 group-hover:bg-gray-100 transition-colors">
                                                        <svg class="w-8 h-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                        <span class="text-[10px] text-gray-500 truncate w-full text-center">{{ $dosya->orijinal_adi }}</span>
                                                    </div>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>

                        <div class="md:col-span-1 space-y-6">
                            
                            {{-- 1. ZAMAN ÇİZELGESİ VE SÜRE --}}
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm sticky top-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Zaman Çizelgesi
                                </h4>

                                @php
                                    $isClosed = in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']);
                                    $daysDiff = ceil($sikayet->created_at->diffInDays(now(), false)); 
                                    if($daysDiff < 0) $daysDiff = 0;
                                    $closedDays = $isClosed ? ceil($sikayet->created_at->diffInDays($sikayet->updated_at)) : 0;
                                @endphp

                                {{-- SÜRE (EN ÜSTTE) --}}
                                <div class="bg-{{ $isClosed ? 'green' : 'amber' }}-50 rounded-xl p-4 border border-{{ $isClosed ? 'green' : 'amber' }}-100 mb-4 text-center">
                                    <span class="block text-3xl font-bold text-{{ $isClosed ? 'green' : 'amber' }}-700">
                                        {{ $isClosed ? $closedDays : $daysDiff }} Gün
                                    </span>
                                    <span class="text-sm font-medium text-{{ $isClosed ? 'green' : 'amber' }}-600">
                                        {{ $isClosed ? 'içinde çözüldü' : 'çözüm bekliyor' }}
                                    </span>
                                </div>

                                <dl class="space-y-3 relative before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                                    {{-- ŞİKAYET TARİHİ --}}
                                    <div class="relative pl-6">
                                        <span class="absolute left-0 top-1.5 w-4 h-4 bg-gray-200 rounded-full border-2 border-white"></span>
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Şikayet Tarihi</dt>
                                        <dd class="text-sm font-medium text-gray-800">
                                            {{ $sikayet->musteri_sikayet_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->format('d.m.Y') : '-' }}
                                        </dd>
                                    </div>

                                    {{-- KAYIT TARİHİ --}}
                                    <div class="relative pl-6">
                                        <span class="absolute left-0 top-1.5 w-4 h-4 bg-blue-200 rounded-full border-2 border-white"></span>
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sisteme Kayıt</dt>
                                        <dd class="text-sm font-medium text-gray-800">{{ $sikayet->created_at->format('d.m.Y H:i') }}</dd>
                                    </div>

                                    {{-- ÇÖZÜM İÇİN SON TARİH --}}
                                    <div class="relative pl-6">
                                        <span class="absolute left-0 top-1.5 w-4 h-4 bg-red-200 rounded-full border-2 border-white"></span>
                                        <dt class="text-xs font-medium text-red-600 uppercase tracking-wider">Çözüm İçin Son Tarih</dt>
                                        <dd class="text-sm font-bold text-red-600">
                                            {{ $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'Belirlenmedi' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- 2. OPERASYONEL DURUM VE DETAYLAR --}}
                            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Operasyonel Durum
                                </h4>
                                
                                @php
                                    $durumRenk = match($sikayet->musteri_durum) {
                                        'Yeni' => 'blue',
                                        'İşlemde', 'İnceleniyor' => 'orange',
                                        'Çözümlendi', 'Kapatıldı' => 'green',
                                        'İptal Edildi' => 'red',
                                        default => 'gray'
                                    };
                                @endphp

                                <dl class="space-y-4">
                                    {{-- MEVCUT DURUM --}}
                                    <div class="bg-{{ $durumRenk }}-50 rounded-lg p-3 border border-{{ $durumRenk }}-100">
                                        <dt class="text-xs font-medium text-{{ $durumRenk }}-700 uppercase tracking-wider mb-1">Mevcut Durum</dt>
                                        <dd>{!! $sikayet->musteri_durum_badge !!}</dd>
                                    </div>

                                    {{-- ÖNCELİK --}}
                                    <div class="pt-3 border-t border-gray-100">
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Öncelik</dt>
                                        <dd>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $sikayet->oncelik_badge_class }}">
                                                {{ $sikayet->musteri_oncelik ?? 'Normal' }}
                                            </span>
                                        </dd>
                                    </div>

                                    {{-- ATANAN ÇÖZÜM TAKIMI --}}
                                    <div class="pt-3 border-t border-gray-100">
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Atanan Çözüm Takımı</dt>
                                        <dd>
                                            @if($sikayet->cozumTakimi)
                                                <a href="{{ route('admin.cozum-takimlari.show', $sikayet->cozumTakimi->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors w-full justify-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                    {{ $sikayet->cozumTakimi->ad }}
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Henüz atanmadı</span>
                                            @endif
                                        </dd>
                                    </div>

                                    {{-- HESAPLANAN PUAN --}}
                                    <div class="pt-3 border-t border-gray-100">
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Hesaplanan Puan</dt>
                                        <dd class="text-lg text-yellow-700 font-bold flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                            {{ $sikayet->musteri_puan ? number_format($sikayet->musteri_puan, 0) : 'N/A' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- 3. FİRMA İSTATİSTİKLERİ (YENİ) --}}
                            @if($sikayet->customer)
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-inner">
                                    @php
                                        $customerTotal = $sikayet->customer->sikayetler()->count();
                                        // Bu şikayetin kaçıncı olduğunu bul (Created at'e göre)
                                        $customerRank = $sikayet->customer->sikayetler()->where('created_at', '<=', $sikayet->created_at)->count();
                                    @endphp
                                    <div class="flex justify-between items-center text-sm mb-2">
                                        <span class="text-gray-500">Firma Toplam Şikayet:</span>
                                        <span class="font-bold text-gray-800">{{ $customerTotal }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Bu Şikayetin Sırası:</span>
                                        <span class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                            {{ $customerRank }}. Şikayet
                                        </span>
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {});
        });
    </script>
</x-app-layout>