<x-app-layout>
    <x-slot name="header">
        {{-- ÜST BAŞLIK, ROZETLER VE AKSİYON BUTONLARI --}}
        @include('admin.arabuluculuk.parcalar.ust-baslik-ve-butonlar')
    </x-slot>

    <div class="py-8" x-data="{ activeTab: window.location.hash === '#files' ? 'dosyalar' : 'genel' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. UYARILAR VE BİLDİRİMLER (Success, Error, Finans Red, Son Onay vb.) --}}
            @include('admin.arabuluculuk.parcalar.uyarilar-ve-bildirimler')

            {{-- 2. TAB (SEKME) MENÜSÜ --}}
            @include('admin.arabuluculuk.parcalar.sekme-menusu')

            {{-- İÇERİK ALANI --}}
            <div class="bg-white shadow-lg rounded-b-xl min-h-[500px]">
                
                {{-- SEKME 1: GENEL BAKIŞ --}}
                <div x-show="activeTab === 'genel'" class="p-6 space-y-6" x-transition>
                    
                    {{-- A. İstatistik Kartları (En üstteki 4 kutu) --}}
                    @include('admin.arabuluculuk.parcalar.genel-istatistik-kartlari')

                    {{-- B. Süreç Tamamlanma Özeti (Renkli büyük kart - Sadece kapatıldıysa görünür) --}}
                    @include('admin.arabuluculuk.parcalar.genel-surec-sonuc-ekrani')

                    {{-- C. Mutabakat ve Arabulucu Atama Yönetimi --}}
                    @include('admin.arabuluculuk.parcalar.genel-mutabakat-ve-atama')

                    {{-- D. Anlaşma Detayları ve Düzenleme Formu --}}
                    @include('admin.arabuluculuk.parcalar.genel-anlasma-detaylari')
                </div>

                {{-- SEKME 2: DOSYALAR --}}
                <div x-show="activeTab === 'dosyalar'" class="p-6" style="display: none;" x-transition>
                    @include('admin.arabuluculuk.parcalar.sekme-dosyalar')
                </div>

                {{-- SEKME 3: KURUL & DEĞERLENDİRME --}}
                @if($case->board_required)
                    <div x-show="activeTab === 'kurul'" class="p-6" style="display: none;" x-transition>
                        @include('admin.arabuluculuk.parcalar.sekme-kurul-degerlendirme')
                    </div>
                @endif

                {{-- SEKME 4: FİNANS & ÖDEME --}}
                <div x-show="activeTab === 'odeme'" class="p-6" style="display: none;" x-transition>
                    @include('admin.arabuluculuk.parcalar.sekme-finans-ve-odeme')
                </div>

                {{-- SEKME 5: TARİHÇE (LOG) --}}
                <div x-show="activeTab === 'log'" class="p-6" style="display: none;" x-transition>
                    @include('admin.arabuluculuk.parcalar.sekme-gecmis-log')
                </div>

            </div>
        </div>
    </div>

    {{-- SAYFAYA ÖZEL SCRIPTLER --}}
    @include('admin.arabuluculuk.parcalar.sayfa-scriptleri')

</x-app-layout>