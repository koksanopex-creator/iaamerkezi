<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Şikayet Detayı: #{{ $sikayet->id }} - {{ $sikayet->musteri_sikayet_konusu }}
        </h2>
    </x-slot>

    {{-- === YENİ EKLENDİ: Fancybox CSS Linki === --}}
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

                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                                <dt class="text-sm font-medium text-gray-600 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                                    Şikayet Kategorisi
                                                </dt>
                                                <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">
                                                    {{-- İlişki üzerinden kategori adını alıyoruz --}}
                                                    {{ $sikayet->sikayetKategori->ad ?? 'Belirtilmemiş' }}
                                                </dd>
                                            </div>

                                            {{-- === YENİ EKLENDİ: Konum Tipi Gösterimi === --}}
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
                                    {{-- ========================================== --}}

                                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Şikayeti Giren Personel
                                        </dt>
                                        <dd class="mt-2 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $sikayet->olusturanKurulUyesi->name ?? 'Sistem' }}
                                            </span>
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
                                                // Storage yolu 'sikayet_dosyalari/dosya.png' şeklindedir.
                                                // Storage::url() başarısız olduğu için, dosya yolunu public/storage'a göre yeniden inşa ediyoruz.
                                                // $dosya->dosya_yolu genellikle "public/sikayet_dosyalari/..." içerir. public/ kısmını kaldırıp asset() ile birleştiriyoruz.
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
                                            <!-- === GÜNCELLENDİ: Durum için Model'den gelen hazır HTML badge'i kullan === -->
                                            {!! $sikayet->musteri_durum_badge !!}
                                        </dd>
                                    </div>
                                    <div class="pt-3 border-t border-gray-100">
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Öncelik</dt>
                                        <dd class->
                                            <!-- === GÜNCELLENDİ: Öncelik için Model'den gelen CSS sınıfını kullan === -->
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $sikayet->oncelik_badge_class }}">
                                                {{ $sikayet->musteri_oncelik ?? 'Belirtilmemiş' }}
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
                                            {{-- === GÜNCELLENDİ: 'cozum_son_tarihi' -> 'musteri_cozum_son_tarihi' olmalı (Model'e göre) === --}}
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

                                    {{-- === YENİ EKLENDİ: Çözüm Puanı === --}}
                                        <div class="pt-3 border-t border-gray-100">
                                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Hesaplanan Puan</dt>
                                            <dd class="text-lg text-yellow-700 font-bold flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                {{-- Puan null değilse göster, değilse 'N/A' yaz --}}
                                                {{ $sikayet->musteri_puan ? number_format($sikayet->musteri_puan, 0) : 'N/A' }}
                                            </dd>
                                        </div>
                                        {{-- =================================== --}}                                    

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
    
    {{-- === YENİ EKLENDİ: Fancybox JS Linki ve Başlatma Scripti === --}}
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {
                // Your custom options
            });
        });
    </script>
</x-app-layout>
