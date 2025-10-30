<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
            <span class="text-gray-500">{{ __('İAA Detayı:') }}</span> {{ $iaa->baslik }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- İki sütunlu ana layout. Mobilde tek sütuna düşer. --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-8">

                {{-- Sol Sütun (Ana İçerik) --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- ANA BAŞLIK KARTI --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                        <div class="p-6 sm:p-8">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">İyileştirme Önerisi</p>
                                    <h1 class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $iaa->baslik }}</h1>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    {{-- MEVCUT DURUM KARTI --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Mevcut Durum / Problem Tanımı</h3>
                            </div>
                            <div class="mt-4 pl-14 text-gray-600 leading-relaxed prose max-w-none">
                                {!! nl2br(e($iaa->mevcut_durum)) !!}
                            </div>
                        </div>
                    </div>

                    {{-- İYİLEŞTİRME ÖNERİSİ KARTI --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">İyileştirme Önerisi</h3>
                            </div>
                            <div class="mt-4 pl-14 text-gray-600 leading-relaxed prose max-w-none">
                                {!! nl2br(e($iaa->oneri)) !!}
                            </div>
                        </div>
                    </div>

                    {{-- EKLENEN RESİMLER KARTI --}}
                    @if($iaa->resimler->isNotEmpty())
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                            <div class="p-6 sm:p-8">
                                <h3 class="text-xl font-bold text-gray-800 mb-4">Eklenen Resimler</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($iaa->resimler as $resim)
                                        <a href="{{ Storage::url($resim->dosya_yolu) }}" target="_blank" class="block group relative">
                                            <img src="{{ Storage::url($resim->dosya_yolu) }}" alt="İAA Resmi" class="rounded-lg object-cover w-full h-40 transform group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                                <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sağ Sütun --}}
                <div class="mt-8 lg:mt-0 space-y-8">
                    {{-- ÖNERİ BİLGİLERİ KARTI --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Öneri Bilgileri</h3>
                            <div class="space-y-4">
                                    <div class="flex items-center space-x-3"><div class="flex-shrink-0 w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-semibold text-gray-800">Öneren</p>
                                        @if ($iaa->gonderen)
                                        <p class="text-gray-600">{{ $iaa->gonderen->name }}</p>
                                        @else
                                        <p class="text-gray-600">{{ $iaa->guest_name }} 
                                            <span class="text-xs text-white bg-gray-500 px-1.5 py-0.5 rounded-full ml-1">Misafir</span>
                                        </p>
                                        @endif
                                    </div></div>
                                    <div class="flex items-center space-x-3"><div class="flex-shrink-0 w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div><div class="text-sm"><p class="font-semibold text-gray-800">İlgili Alan / Bölüm</p>@if ($iaa->bolum)<p class="text-gray-600">{{ $iaa->bolum->ad }}</p>@else<p class="text-gray-600">{{ $iaa->ilgili_alan }}</p>@endif</div></div>
                                    <div class="flex items-center space-x-3"><div class="flex-shrink-0 w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                                <p class="font-semibold text-gray-800">Gönderim Tarihi</p><p class="text-gray-600">{{ $iaa->created_at->format('d.m.Y H:i') }}</p>
                                            </div>
                                    </div>

                                        {{-- ========================================================================= --}}
                                        {{-- === YENİ VE DİNAMİK DURUM ROZETİNİ TAM OLARAK BURAYA YAPIŞTIRIN === --}}
                                        {{-- ========================================================================= --}}
                                        <div class="border-t border-gray-200 mt-4 pt-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0 w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="text-sm flex-grow">
                                                    <div class="flex justify-between items-center">
                                                        <p class="font-semibold text-gray-800">Mevcut Durum</p>

                                                        @php
                                                            $statusInfo = match($iaa->durum) {
                                                                'Onay Bekliyor' => ['text' => 'Onay Bekliyor', 'class' => 'bg-yellow-100 text-yellow-800 ring-1 ring-inset ring-yellow-200'],
                                                                'Havuzda' => ['text' => 'Havuzda', 'class' => 'bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-200'],
                                                                'Atandı' => ['text' => 'Atandı', 'class' => 'bg-indigo-100 text-indigo-800 ring-1 ring-inset ring-indigo-200'],
                                                                'Revize Ediliyor' => ['text' => 'Revizyonda', 'class' => 'bg-orange-100 text-orange-800 ring-1 ring-inset ring-orange-200'],
                                                                'Tamamlandı' => ['text' => 'Tamamlandı', 'class' => 'bg-green-100 text-green-800 ring-1 ring-inset ring-green-200'],
                                                                'Reddedildi' => ['text' => 'Reddedildi', 'class' => 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-200'],
                                                                'Yönetici Onayı Bekliyor' => ['text' => 'Yönetici Onayında', 'class' => 'bg-cyan-100 text-cyan-800 ring-1 ring-inset ring-cyan-200'],
                                                                default => ['text' => $iaa->durum, 'class' => 'bg-gray-100 text-gray-800 ring-1 ring-inset ring-gray-200'],
                                                            };
                                                        @endphp

                                                        <span class="px-2.5 py-0.5 inline-flex text-xs font-medium rounded-full {{ $statusInfo['class'] }}">
                                                            {{ $statusInfo['text'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                            </div>
                        </div>
                    </div>

                    {{-- ÖNERİ SAHİBİNİN TAHMİNLERİ KARTI --}}
                    @if ($iaa->oneren_kazanc_miktar || $iaa->oneren_butce_miktar)
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                             <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Öneri Sahibinin Tahminleri</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center"><p class="text-xs text-green-700 uppercase font-semibold">Tahmini Kazanç</p><p class="text-xl font-bold text-green-800">{{ number_format($iaa->oneren_kazanc_miktar, 0, ',', '.') }} {{ $iaa->oneren_kazanc_birim }}</p></div>
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center"><p class="text-xs text-red-700 uppercase font-semibold">Tahmini Bütçe</p><p class="text-xl font-bold text-red-800">{{ number_format($iaa->oneren_butce_miktar, 0, ',', '.') }} {{ $iaa->oneren_butce_birim }}</p></div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- YÖNETİCİ PUANLAMASI KARTI --}}
                    @if(in_array($iaa->durum, ['Havuzda', 'Talep Edildi', 'Atandı']))
                         <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                             <div class="p-6">

                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Yönetici Puanlaması</h3>

                                    {{-- Sadece Superadmin rolündeki kullanıcılar için ve projenin puanı varsa butonu göster --}}
                                    @auth
                                        @role('Superadmin')
                                            @if($iaa->puan)
                                                <button x-data @click.prevent="$dispatch('open-modal', 'puan-duzenle-modal-{{ $iaa->id }}')" 
                                                        class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-slate-600 hover:bg-slate-700">
                                                    Düzenle
                                                </button>
                                            @endif
                                        @endrole
                                    @endauth
                                </div>

                                 <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2 bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-lg text-center shadow-lg"><p class="text-sm text-indigo-100 uppercase font-semibold">Toplam Puan</p><p class="text-4xl font-bold text-white mt-1">{{ number_format($iaa->puan, 0, ',', '.') }}</p></div>
                                    <div class="bg-gray-100 border border-gray-200 rounded-lg p-3 text-center"><p class="text-xs text-gray-600 uppercase font-semibold">Risk</p><p class="text-xl font-bold text-gray-800">{{ $iaa->risk }} / 5</p></div>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center"><p class="text-xs text-green-700 uppercase font-semibold">Kazanç</p><p class="text-lg font-bold text-green-800">{{ number_format($iaa->kazanc_miktar, 0, ',', '.') }} {{ $iaa->kazanc_birim }}</p></div>
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center col-span-2"><p class="text-xs text-red-700 uppercase font-semibold">Bütçe</p><p class="text-lg font-bold text-red-800">{{ number_format($iaa->butce_miktar, 0, ',', '.') }} {{ $iaa->butce_birim }}</p></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
            
            {{-- ALT BUTONLAR BÖLÜMÜ --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ (url()->previous() && url()->previous() !== url()->current()) ? url()->previous() : route('iaa.havuz') }}" class="inline-flex items-center space-x-2 bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Geri Dön</span>
                </a>
                
                @if ($iaa->durum === 'Havuzda')
                    <div class="w-full sm:w-auto">
                        @if(auth()->user()->lideriOlduguTakimlar->isNotEmpty())
                            @if($talepEdilenIaaIdleri->contains($iaa->id))
                                <button class="w-full inline-flex justify-center text-sm font-semibold text-gray-500 bg-gray-200 border border-transparent rounded-md shadow-sm px-4 py-2 cursor-not-allowed" disabled>
                                    Talep Edildi
                                </button>
                            @else
                                <button x-data @click="$dispatch('open-modal', 'talep-et-modal-{{ $iaa->id }}')" class="w-full inline-flex justify-center text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-md shadow-sm px-4 py-2 hover:bg-indigo-700">
                                    Takımın Adına Talep Et
                                </button>
                            @endif
                        @else
                            <div title="Öneri talep edebilmek için bir takım lideri olmalısınız.">
                                <button class="w-full inline-flex justify-center text-sm font-semibold text-white bg-indigo-300 border border-transparent rounded-md shadow-sm px-4 py-2 cursor-not-allowed">
                                    Takımın Adına Talep Et
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL BÖLÜMÜ --}}
    @if ($iaa->durum === 'Havuzda' && auth()->user()->lideriOlduguTakimlar->isNotEmpty())
        @include('iaa.partials.talep-et-modal', ['iaa' => $iaa, 'liderOlduguTakimlar' => $liderOlduguTakimlar])
    @endif

    @include('admin.iaa-yonetim.partials.onayla-modal', ['iaa' => $iaa, 'paraBirimleri' => $paraBirimleri])

</x-app-layout>