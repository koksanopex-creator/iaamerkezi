<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <a href="javascript:history.back()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100" title="Geri Dön">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
</a>
                Disiplin Dosyası #{{ $case->id }}
            </h2>
            <div class="flex gap-2">
                {{-- Düzenle Butonu (Sadece Yöneticiler) --}}
                @if($case->durum != 'Karar Verildi' && Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Bölüm Lideri']))
                    <a href="{{ route('admin.disiplin.edit', $case->id) }}" class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-100 border border-indigo-200">Düzenle</a>
                @endif
                <button onclick="window.print()" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 border border-gray-300">Yazdır / PDF</button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- DURUM ÇUBUĞU --}}
            @php
                $durumRenk = match($case->durum) {
                    'Savunma Bekleniyor' => 'yellow',
                    'Yönetici Değerlendirmesi' => 'blue',
                    'Kurulda' => 'purple',
                    'Karar Verildi' => 'green',
                    'İptal' => 'red',
                    default => 'gray'
                };
                $durumMetni = match($case->durum) {
                    'Savunma Bekleniyor' => 'Personelden savunma bekleniyor.',
                    'Yönetici Değerlendirmesi' => 'Savunma girildi, yönetici onayı bekleniyor.',
                    'Karar Verildi' => 'Dosya kapatıldı ve karar kesinleşti.',
                    default => 'İşlem bekleniyor.'
                };
            @endphp
            <div class="bg-{{ $durumRenk }}-50 border-l-4 border-{{ $durumRenk }}-500 p-4 mb-6 rounded-r shadow-sm flex justify-between items-center transition-all duration-500">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white rounded-full text-{{ $durumRenk }}-600 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-{{ $durumRenk }}-800 text-lg">Dosya Durumu: {{ $case->durum }}</p>
                        <p class="text-xs text-{{ $durumRenk }}-600 font-semibold">{{ $durumMetni }}</p>
                    </div>
                </div>
            </div>

            {{-- PERSONEL İÇİN KURUL BİLGİLENDİRMESİ --}}
            @if($case->durum == 'Kurulda' && !Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']))
                <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6 text-center mb-6">
                    <div class="inline-block p-3 bg-gray-200 rounded-full mb-3">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Disiplin Kurulu Değerlendirmesi</h3>
                    <p class="text-gray-600 mt-2">Dosyanız Disiplin Kurulu'na sevk edilmiştir. Kurul üyeleri tarafından incelenmektedir.</p>
                    @if($case->toplanti_tarihi)
                        <p class="mt-3 text-sm font-bold text-indigo-600 bg-indigo-50 inline-block px-3 py-1 rounded">📅 Planlanan Toplantı: {{ $case->toplanti_tarihi->format('d.m.Y H:i') }}</p>
                    @endif
                </div>
            @endif

            {{-- ÜST KISIM: 2 KOLONLU YAPI (SOL VE SAĞ) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- SOL KOLON (MD:COL-SPAN-2) --}}
                <div class="md:col-span-2 space-y-6">
                    
                    {{-- 1. Olay ve Kanıtlar --}}
                    @include('admin.disiplin.partials.case-details')

                    {{-- 2. Savunma Alanı (Form veya Gösterim) --}}
                    @include('admin.disiplin.partials.defense-section')

                    {{-- 3. Yönetici Aksiyonları ve Sonuç Ekranı --}}
                    @include('admin.disiplin.partials.manager-actions')

                    {{-- 4. YORUM VE TARTIŞMA ALANI (YENİ EKLENDİ) --}}
                    @include('admin.disiplin.partials.comments')

                </div>

                {{-- SAĞ KOLON (SIDEBAR) --}}
                <div class="space-y-6">
                    @include('admin.disiplin.partials.sidebar')
                </div>

            </div> {{-- Grid Sonu --}}

            {{-- 
                ===================================================
                ALT KISIM: KURUL ODASI (TAM GENİŞLİK / FULL WIDTH)
                ===================================================
                Bu kısım Grid'in dışındadır, böylece sayfanın tamamını kaplar.
            --}}
            <div class="w-full">
                @include('admin.disiplin.partials.council-room')
            </div>

        </div>
    </div>
</x-app-layout>