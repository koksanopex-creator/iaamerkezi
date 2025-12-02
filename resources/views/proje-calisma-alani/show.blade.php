<x-app-layout>
    {{-- ========================================================= --}}
    {{-- === HEADER (SADELEŞTİRİLDİ, BUTON KALDIRILDI) === --}}
    {{-- ========================================================= --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proje Çalışma Alanı: <span class="text-indigo-600">{{ $iaa->baslik }}</span>
            </h2>
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Geri Dön
            </a>
        </div>
    </x-slot>

    {{-- Arka plan rengini referans koddaki gibi güncelledim --}}
    <div class="py-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. Proje Künyesi (Header) Partial'ı (Mevcut kodunuz) --}}
            @include('proje-calisma-alani.partials._project-header', [
                'iaa' => $iaa,
                'takim' => $takim,
                'assignment' => $assignment,
                'progressPercentage' => $progressPercentage,
                'completedStepsCount' => $completedStepsCount,
                'totalStepsCount' => $totalStepsCount,
                'statusDate' => $statusDate ?? null
            ])

            {{-- ========================================================= --}}
            {{-- === YENİ: SQUAD (PROJE EKİBİ) YÖNETİM ALANI (LİDER) === --}}
            {{-- ========================================================= --}}
            @if($iaa->musteriSikayeti && Auth::id() == $iaa->atananTakim->lider_user_id)
                <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-5 flex items-center justify-between animate-fade-in-up">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        {{-- ... Üst kısım (ikon ve başlık) aynı ... --}}
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Proje Görev Gücü (Squad)</h3>
                            
                            {{-- HESAPLAMALAR --}}
                            @php
                                // Aktif: Lider olanlar VEYA durumu 'onaylandi' olanlar
                                $aktifSayisi = $iaa->projeEkibi->filter(function($uye) {
                                    return $uye->pivot->rol == 'Lider' || $uye->pivot->durum == 'onaylandi';
                                })->count();

                                // Bekleyen: Durumu 'bekliyor' olanlar
                                $bekleyenSayisi = $iaa->projeEkibi->where('pivot.durum', 'bekliyor')->count();
                            @endphp

                            <div class="flex items-center gap-3 mt-2">
                                {{-- Avatarlar --}}
                                <div class="flex -space-x-2 overflow-hidden">
                                    @foreach($iaa->projeEkibi as $uye)
                                        @if($uye->profile_photo_path)
                                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover {{ $uye->pivot->durum == 'bekliyor' ? 'opacity-50 grayscale' : '' }}" 
                                                 src="{{ asset('storage/'.$uye->profile_photo_path) }}" 
                                                 title="{{ $uye->name }} ({{ $uye->pivot->durum == 'bekliyor' ? 'Davet Bekleniyor' : $uye->pivot->rol }})">
                                        @else
                                            <div class="inline-flex items-center justify-center h-8 w-8 rounded-full ring-2 ring-white bg-gray-100 text-xs font-bold text-gray-600 {{ $uye->pivot->durum == 'bekliyor' ? 'opacity-50 grayscale' : '' }}" 
                                                 title="{{ $uye->name }} ({{ $uye->pivot->durum == 'bekliyor' ? 'Davet Bekleniyor' : $uye->pivot->rol }})">
                                                {{ substr($uye->name, 0, 1) }}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- YENİ: Detaylı Durum Bilgisi --}}
                                <div class="flex flex-col text-xs border-l pl-3 border-gray-200">
                                    <span class="text-gray-500 font-semibold mb-0.5">Toplam {{ $iaa->projeEkibi->count() }} Kişi</span>
                                    
                                    <div class="flex items-center gap-2">
                                        {{-- Aktif Sayısı --}}
                                        <span class="inline-flex items-center text-green-700 bg-green-50 px-1.5 py-0.5 rounded font-bold">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                            {{ $aktifSayisi }} Aktif
                                        </span>

                                        {{-- Bekleyen Sayısı (Varsa Göster) --}}
                                        @if($bekleyenSayisi > 0)
                                            <span class="inline-flex items-center text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded font-bold animate-pulse">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1"></span>
                                                {{ $bekleyenSayisi }} Bekliyor
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                  
                    
                    {{-- Modal Tetikleyici Buton --}}
                    @php
                        // Kilitlenecek Durumlar
                        $kilitliDurumlar = ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlandı'];
                        $kilitliMi = in_array($iaa->durum, $kilitliDurumlar);
                    @endphp

                    @if(!$kilitliMi)
                        <button onclick="Livewire.dispatch('openSquadModal', { iaaId: {{ $iaa->id }} })" 
                                class="flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Ekibi Yönet
                        </button>
                    @else
                        <span class="flex items-center px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed" title="Proje onay aşamasında olduğu için ekip kilitlenmiştir.">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Ekip Kilitlendi
                        </span>
                    @endif
                </div>
                
                {{-- Livewire Modal Bileşeni (Sayfanın en altına da koyabilirsin ama burada da çalışır) --}}
                <livewire:admin.squad-yonetim-modal />
            @endif
            {{-- ========================================================= --}}

            {{-- ========================================================= --}}
            {{-- === İSTEDİĞİNİZ MODERN MÜŞTERİ ŞİKAYETİ KARTI === --}}
            {{-- ========================================================= --}}
            @if($iaa->musteriSikayeti)
                <div x-data="{ open: false }" class="group">
                    
                    {{-- Ana Kart Container --}}
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        
                        {{-- Üst Gradient Bar --}}
                        <div class="h-1.5 bg-gradient-to-r from-rose-400 via-pink-500 to-purple-500"></div>
                        
                        {{-- Tıklanabilir Başlık Alanı --}}
                        <div @click="open = !open" class="p-6 cursor-pointer transition-all duration-200 hover:bg-gradient-to-r hover:from-rose-50/50 hover:to-purple-50/50">
                            <div class="flex items-start justify-between gap-4">
                                
                                {{-- Sol Taraf: İkon + Başlık --}}
                                <div class="flex items-start gap-4 flex-1 min-w-0">
                                    
                                    {{-- Modern İkon Container --}}
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-rose-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg shadow-rose-500/30 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    
                                    {{-- Başlık ve Bilgiler --}}
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                                            Müşteri Şikayeti Detayları
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                #{{ $iaa->musteriSikayeti->id }}
                                            </span>
                                        </h3>
                                        
                                        {{-- Özet Bilgiler (Kapalıyken görünür) --}}
                                        <div x-show="!open" class="mt-2 flex flex-wrap items-center gap-3 text-sm">
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span class="font-medium">{{ $iaa->musteriSikayeti->musteri_adi }}</span>
                                            </div>
                                            <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ \Carbon\Carbon::parse($iaa->musteriSikayeti->musteri_sikayet_tarihi)->format('d.m.Y') }}</span>
                                            </div>
                                            <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $iaa->musteriSikayeti->sikayetKategori->ad ?? 'Belirtilmemiş' }}
                                            </span>
                                        </div>
                                        
                                        {{-- Durum Mesajı --}}
                                        <p class="mt-2 text-sm" :class="open ? 'text-purple-600 font-medium' : 'text-gray-500'">
                                            <span x-show="!open">Tüm detayları görmek için tıklayın</span>
                                            <span x-show="open" style="display: none;">Gizlemek için tıklayın</span>
                                        </p>
                                    </div>
                                </div>
                                
                                {{-- Sağ Taraf: Açılır/Kapanır Ok --}}
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center group-hover:from-purple-100 group-hover:to-pink-100 transition-all duration-300">
                                        <svg x-show="!open" class="w-5 h-5 text-gray-600 group-hover:text-purple-600 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        <svg x-show="open" style="display: none;" class="w-5 h-5 text-purple-600 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Genişleyen İçerik Alanı --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             style="display: none;"
                             class="border-t border-gray-100">

                            <div class="p-6 bg-gradient-to-br from-gray-50/50 to-purple-50/30">
                                
                                {{-- Bilgi Kartları Grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    
                                    {{-- Müşteri Bilgileri Kartı --}}
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Müşteri Adı</p>
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $iaa->musteriSikayeti->musteri_adi }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- E-posta Kartı --}}
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">E-posta</p>
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $iaa->musteriSikayeti->musteri_iletisim }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Konum Tipi Kartı --}}
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Konum Tipi</p>
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $iaa->musteriSikayeti->konum_tipi }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Tarih Kartı --}}
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Şikayet Tarihi</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($iaa->musteriSikayeti->musteri_sikayet_tarihi)->format('d.m.Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Kategori Badge --}}
                                <div class="mb-6">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm border border-purple-100">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-500">Kategori:</span>
                                        <span class="text-sm font-bold text-purple-700">{{ $iaa->musteriSikayeti->sikayetKategori->ad ?? 'Belirtilmemiş' }}</span>
                                    </div>
                                </div>
                                
                                {{-- Şikayet Konusu --}}
                                <div class="mb-6 bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Şikayet Konusu</h4>
                                    </div>
                                    <p class="text-base font-semibold text-gray-800 leading-relaxed pl-11">{{ $iaa->musteriSikayeti->musteri_sikayet_konusu }}</p>
                                </div>
                                
                                {{-- Şikayet Detayı --}}
                                <div class="mb-6 bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Detaylı Açıklama</h4>
                                    </div>
                                    <div class="pl-11">
                                        <div class="bg-gradient-to-br from-gray-50 to-blue-50/30 rounded-lg p-4 border border-gray-200">
                                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $iaa->musteriSikayeti->musteri_sikayet_detayi }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Eklenen Dosyalar --}}
                                @if($iaa->musteriSikayeti->dosyalar->isNotEmpty())
                                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100" x-data="{ previewModal: false, previewUrl: '', previewType: '', previewName: '' }">
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Eklenen Dosyalar ({{ $iaa->musteriSikayeti->dosyalar->count() }})</h4>
                                    </div>
                                    <div class="pl-11 grid grid-cols-1 md:grid-cols-2 gap-3">
                                        
                                        @foreach($iaa->musteriSikayeti->dosyalar as $dosya)
                                        @php
                                            $fileUrl = asset('storage/' . $dosya->dosya_yolu);
                                            $isImage = str_starts_with($dosya->mime_tipi, 'image/');
                                            $isPdf = $dosya->mime_tipi === 'application/pdf';
                                            $extension = pathinfo($dosya->orijinal_adi, PATHINFO_EXTENSION);
                                        @endphp
                                        
                                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-100 overflow-hidden hover:shadow-lg transition-all duration-200 group">
                                            {{-- Önizleme Alanı --}}
                                            <div class="relative h-32 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                                @if($isImage)
                                                    {{-- Resim Önizlemesi --}}
                                                    <img src="{{ $fileUrl }}" alt="{{ $dosya->orijinal_adi }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                                @elseif($isPdf)
                                                    {{-- PDF İkonu --}}
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <svg class="w-16 h-16 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    {{-- Genel Dosya İkonu --}}
                                                    <div class="w-full h-full flex flex-col items-center justify-center">
                                                        <svg class="w-12 h-12 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        <span class="text-xs font-bold text-indigo-600 uppercase px-2 py-1 bg-white rounded">{{ strtoupper($extension) }}</span>
                                                    </div>
                                                @endif
                                                
                                                {{-- Hover Butonları --}}
                                                <div class="absolute top-2 right-2 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                    @if($isImage || $isPdf)
                                                    <button @click="previewModal = true; previewUrl = '{{ $fileUrl }}'; previewType = '{{ $isImage ? 'image' : 'pdf' }}'; previewName = '{{ $dosya->orijinal_adi }}'" class="w-8 h-8 bg-white/90 hover:bg-white rounded-lg flex items-center justify-center shadow-lg backdrop-blur-sm transition-colors">
                                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </button>
                                                    @endif
                                                    <a href="{{ $fileUrl }}" target="_blank" class="w-8 h-8 bg-white/90 hover:bg-white rounded-lg flex items-center justify-center shadow-lg backdrop-blur-sm transition-colors">
                                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            {{-- Dosya Bilgileri --}}
                                            <div class="p-3 bg-white">
                                                <p class="text-sm font-semibold text-gray-900 truncate mb-1" title="{{ $dosya->orijinal_adi }}">{{ $dosya->orijinal_adi }}</p>
                                                <div class="flex items-center justify-between">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ $dosya->mime_tipi }}
                                                    </span>
                                                    {{-- Sizin DB'de 'boyut' olmadığı için bu bölüm referans koddan çıkarıldı --}}
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- Önizleme Modal --}}
                                    <div 
                                         x-show="previewModal" 
                                         x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 z-50 overflow-y-auto bg-black/90 flex items-center justify-center p-4" 
                                        style="display: none;" 
                                        @keydown.escape.window="previewModal = false"
                                        @click="previewModal = false"
                                    >
                                        <div class="relative w-full max-w-6xl max-h-[90vh]" @click.stop>
                                            {{-- Başlık Bar --}}
                                            <div class="bg-white/10 backdrop-blur-md rounded-t-xl px-6 py-4 flex items-center justify-between">
                                                <h3 class="text-white font-semibold truncate" x-text="previewName"></h3>
                                                <div class="flex items-center gap-2">
                                                    <a :href="previewUrl" target="_blank" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                    <button @click="previewModal = false" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            {{-- İçerik Alanı --}}
                                            <div class="bg-white rounded-b-xl overflow-hidden shadow-2xl">
                                                <div class="flex items-center justify-center p-4" style="max-height: calc(90vh - 80px);">
                                                    {{-- Resim Önizleme --}}
                                                    <img x-show="previewType === 'image'" :src="previewUrl" :alt="previewName" class="max-w-full max-h-full object-contain rounded-lg shadow-lg">
                                                    
                                                    {{-- PDF Önizleme --}}
                                                    <iframe x-show="previewType === 'pdf'" :src="previewUrl" class="w-full rounded-lg shadow-lg" style="height: calc(90vh - 120px);" frameborder="0"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {{-- ========================================================= --}}
            {{-- === MÜŞTERİ ŞİKAYETİ KARTI SONU === --}}
            {{-- ========================================================= --}}


            {{-- 2. İş Akışı Adımları (Timeline) Partial'ı (Mevcut kodunuz) --}}
            <div class="w-full">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Proje Adımları</h3>
                    
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert"><p>{{ session('success') }}</p></div>
                    @endif
                    
                    <div class="relative border-l-2 border-gray-200">
                        @php $currentStepFound = false; @endphp
                        
                        @foreach ($steps as $step)
                            @php
                                $isCompleted = in_array($step->id, $completedStepIds);
                                $progressUpdate = $progressUpdates[$step->id] ?? null;
                                $isCurrent = !$isCompleted && !$currentStepFound;
                                
                                if ($isCurrent) {
                                    $currentStepFound = true;
                                    $currentStep = $step;
                                }
                            @endphp
                            
                            {{-- Her bir adım kartını (açılır/kapanır) partial ile çağır --}}
                            @include('proje-calisma-alani.partials._step-item', [
                                'step' => $step,
                                'isCompleted' => $isCompleted,
                                'isCurrent' => $isCurrent,
                                'progressUpdate' => $progressUpdate,
                                'isTeamMember' => $isTeamMember,
                                'iaa' => $iaa,
                                'assignment' => $assignment,
                                'takim' => $takim,
                                'stepAssignments' => $stepAssignments ?? [] // <--- YENİ EKLENEN
                            ])
                        @endforeach
                        
                        {{-- Proje bittiyse (aktif adım yoksa) final durum kutusunu göster --}}
                        @if (!$currentStepFound)
                            @include('proje-calisma-alani.partials._project-final-status', [
                                'iaa' => $iaa,
                                'statusDate' => $statusDate ?? null
                            ])
                            {{-- === YENİ EKLENECEK KISIM === --}}
                            @include('proje-calisma-alani.partials._action-buttons', ['iaa' => $iaa])
                            {{-- === BURAYA KADAR === --}}    
                        @endif
                    </div>
                </div>
            </div>

            {{-- ================== GÜNCELLENMİŞ LOG KARTI (Mevcut kodunuz) ================== --}}
            <div class="w-full" x-data="{ logModalOpen: false }">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Son 5 Proje Geçmişi Kaydı</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eylem</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($sonOnLoglar as $log) 
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $log->eylem }}</div>
                                            <div class="text-sm text-gray-500 italic">"{{ $log->aciklama }}"</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Bu proje için henüz bir log kaydı bulunmuyor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Eğer 5'ten fazla log varsa butonu göster --}}
                    @if ($tumProjeLoglari->count() > 5)
                        <div class="text-center">
                            <button 
                                @click="logModalOpen = true" 
                                type="button" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Tüm Logları Gör ({{ $tumProjeLoglari->count() }})
                            </button>
                        </div>
                    @endif
                </div>

                {{-- ================== TÜM LOGLARI GÖSTEREN MODAL (Mevcut kodunuz) ================== --}}
                <div 
                    x-show="logModalOpen" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4"
                    style="display: none;"
                    @keydown.escape.window="logModalOpen = false"
                >
                    <div 
                        class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-4xl" 
                        @click.outside="logModalOpen = false"
                    >
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Proje Geçmişi - Tüm Loglar ({{ $tumProjeLoglari->count() }})
                            </h3>
                        </div>
                        <div class="p-6 max-h-[70vh] overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eylem</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($tumProjeLoglari as $log)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}</td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $log->eylem }}</div>
                                                <div class="text-sm text-gray-500 italic">"{{ $log->aciklama }}"</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Log kaydı bulunamadı.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-right">
                            <button 
                                @click="logModalOpen = false" 
                                type="button" 
                                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                Kapat
                            </button>
                        </div>
                    </div>
                </div>
                {{-- ================== MODAL SONU ================== --}}
            </div>
            {{-- ================== LOG KARTI SONU ================== --}}

        </div> {{-- max-w-7xl kapanışı --}}
    </div> {{-- py-8 kapanışı --}}

    {{-- OTOMATİK SCROLL SCRİPTİ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Controller'dan gelen 'scroll_to_step' verisi varsa çalışır
            @if(session('scroll_to_step'))
                setTimeout(() => {
                    // Tamamlanan adımın ID'sini al (Örn: 25)
                    const stepId = "{{ session('scroll_to_step') }}";
                    
                    // Sayfada bu ID'ye sahip adımı bul (Örn: id="step-card-25")
                    // NOT: Aşağıdaki adımda ID eklemeyi unutma!
                    const element = document.getElementById('step-card-' + stepId);
                    
                    if (element) {
                        // Oraya yumuşakça kaydır ve ortala
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // İstersen kısa bir vurgu efekti de verebilirsin (opsiyonel)
                        element.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
                        setTimeout(() => element.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2'), 2000);
                    }
                }, 500); // Sayfa tam yüklensin diye yarım saniye bekle
            @endif
        });
    </script>

</x-app-layout>