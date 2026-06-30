@push('pageTitle')
    {{ $iaa->baslik }} | 
@endpush

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
                                        <a href="{{ asset('storage/' . $resim->dosya_yolu) }}" target="_blank" class="block group relative">
                                            <img src="{{ asset('storage/' . $resim->dosya_yolu) }}" alt="İAA Resmi" class="rounded-lg object-cover w-full h-40 transform group-hover:scale-105 transition-transform duration-300">
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
                    {{-- SUPERADMIN AKSİYON PANELİ (YENİ) --}}
                    @role('Superadmin')
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-2 border-indigo-100">
                            <div class="p-6 bg-indigo-50/50">
                                <h3 class="text-lg font-bold text-indigo-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Yönetici İşlemleri
                                </h3>
                                
                                <div class="space-y-3">
                                    @if($iaa->durum === 'Onay Bekliyor')
                                        <button x-data @click="$dispatch('open-modal', 'onayla-modal-{{ $iaa->id }}')" 
                                                class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Öneriyi Onayla
                                        </button>

                                        <button x-data @click="$dispatch('open-modal', 'reddet-modal-{{ $iaa->id }}')" 
                                                class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 transition-all">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Öneriyi Reddet
                                        </button>

                                        <form action="{{ route('admin.iaa-yonetim.destroy', $iaa->id) }}" method="POST" onsubmit="return confirm('Bu öneriyi kalıcı olarak silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition-all">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Kalıcı Olarak Sil
                                            </button>
                                        </form>
                                    @elseif(in_array($iaa->durum, ['Havuzda', 'Reddedildi']))
                                        <form action="{{ route('admin.iaa-yonetim.geriAl', $iaa->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                Kararı Geri Al
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <p class="text-[10px] text-gray-400 text-center mt-2 italic">* Bu panel sadece Superadmin yetkisine sahip kullanıcılara görünür.</p>
                                </div>
                            </div>
                        </div>
                    @endrole

                    {{-- PROJE ÇALIŞMA ALANI BİLGİSİ (YENİ) --}}
                    @if($iaa->atanan_takim_id)
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-emerald-200">
                             <div class="p-6 bg-emerald-50/30">
                                <h3 class="text-lg font-bold text-emerald-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    Proje Atama Bilgileri
                                </h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase">Sorumlu Takım</p>
                                            <a href="{{ route('takimlar.show', $iaa->atanan_takim_id) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors">
                                                {{ $iaa->atananTakim->ad }}
                                            </a>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase">Atanma Tarihi</p>
                                            <p class="text-sm font-bold text-gray-900">{{ $iaa->iaaTalebi?->start_date ? \Carbon\Carbon::parse($iaa->iaaTalebi->start_date)->format('d.m.Y') : $iaa->updated_at->format('d.m.Y') }}</p>
                                        </div>
                                    </div>

                                    @if(in_array($iaa->durum, ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']))
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">İşlem Süresi</p>
                                                <p class="text-sm font-bold text-emerald-700">
                                                    {{ $iaa->completion_duration_in_days ?? 'Belirlenmedi' }}
                                                </p>
                                            </div>
                                        </div>
                                    @elseif($iaa->iaaTalebi?->due_date)
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Kalan Süre</p>
                                                @php
                                                    $dueDate = \Carbon\Carbon::parse($iaa->iaaTalebi->due_date);
                                                    $diff = ceil(now()->diffInDays($dueDate, false));
                                                @endphp
                                                <p class="text-sm font-bold @if($diff < 0) text-red-600 @else text-gray-900 @endif">
                                                    @if($diff < 0)
                                                        {{ abs((int)$diff) }} gün gecikti
                                                    @else
                                                        {{ (int)$diff }} gün kaldı
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    <a href="{{ route('proje.workspace.show', $iaa->id) }}" 
                                       class="mt-2 w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 transition-all transform hover:scale-[1.02]">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Proje Çalışma Alanına Git
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                    <div class="text-sm flex-grow">
                                        <p class="font-semibold text-gray-800">Öneren</p>
                                        @if ($iaa->gonderen)
                                        <div class="flex items-center gap-2 group/oneren">
                                            <p class="text-gray-600">
                                                <a href="{{ route('profile.show', $iaa->gonderen->id) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline transition-colors font-medium">
                                                    {{ $iaa->gonderen->name }}
                                                </a>
                                            </p>
                                            {{-- Kabul edilen öneriler butonu (hover ile görünür) --}}
                                            <button type="button"
                                                x-data
                                                @click="$dispatch('open-modal', 'kabul-edilen-oneriler-modal-{{ $iaa->id }}')"
                                                class="opacity-0 group-hover/oneren:opacity-100 transition-opacity duration-200 inline-flex items-center gap-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-2 py-0.5 hover:bg-emerald-100"
                                                title="Bu kullanıcının kabul edilmiş önerilerini görüntüle">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>{{ $kabul_edilen_oneri_sayisi }} Kabul</span>
                                            </button>
                                        </div>
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
                                                        {!! $iaa->durum_etiketi !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                            </div>
                        </div>
                    </div>

                    {{-- ÖNERİ SAHİBİNİN TAHMİNLERİ KARTI (her zaman göster) --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Öneri Sahibinin Tahminleri</h3>
            
            @if ($iaa->oneren_kazanc_miktar || $iaa->oneren_butce_miktar)
            <div class="grid grid-cols-2 gap-4">
                {{-- TAHMİNİ PUAN ALANI --}}
                <div class="col-span-2 bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-lg text-center shadow-lg">
                    <p class="text-sm text-indigo-100 uppercase font-semibold">Öngörülen Puan</p>
                    <p class="text-4xl font-bold text-white mt-1">
                        @php
                            $tahminiPuan = 0;
                            if($iaa->oneren_butce_miktar > 0) {
                                $tahminiPuan = ($iaa->oneren_kazanc_miktar / $iaa->oneren_butce_miktar) * 3; 
                            }
                        @endphp
                        {{ number_format($tahminiPuan, 0, ',', '.') }}
                    </p>
                </div>

                {{-- TAHMİNİ KAZANÇ --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                    <p class="text-xs text-green-700 uppercase font-semibold">Tahmini Kazanç</p>
                    <p class="text-xl font-bold text-green-800">
                        {{ number_format($iaa->oneren_kazanc_miktar, 0, ',', '.') }} {{ $iaa->oneren_kazanc_birim }}
                    </p>
                </div>

                {{-- TAHMİNİ BÜTÇE --}}
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                    <p class="text-xs text-red-700 uppercase font-semibold">Tahmini Bütçe</p>
                    <p class="text-xl font-bold text-red-800">
                        {{ number_format($iaa->oneren_butce_miktar, 0, ',', '.') }} {{ $iaa->oneren_butce_birim }}
                    </p>
                </div>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-6 text-center">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">Öngörülen puan hesaplanamadı</p>
                <p class="text-xs text-gray-400 mt-1">Öneri sahibi tahmini maliyet ve kazanç bilgisi girmemiştir.</p>
            </div>
            @endif
        </div>
    </div>

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
                                <div class="space-y-2">
                                    <button class="w-full inline-flex justify-center text-sm font-semibold text-gray-500 bg-gray-200 border border-transparent rounded-md shadow-sm px-4 py-2 cursor-not-allowed" disabled>
                                        Talep Edildi
                                    </button>
                                    
                                    <form action="{{ route('iaa.talebiGeriCek', $iaa->id) }}" method="POST" onsubmit="return confirm('Talebinizi geri çekmek istediğinize emin misiniz?');">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex justify-center text-sm font-semibold text-red-600 bg-red-50 border border-red-200 rounded-md shadow-sm px-4 py-2 hover:bg-red-100 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Talebi Geri Çek
                                        </button>
                                    </form>
                                </div>
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
    @include('admin.iaa-yonetim.partials.reddet-modal', ['iaa' => $iaa])
    @include('iaa.partials.kabul-edilen-oneriler-modal', ['iaa' => $iaa, 'kabul_edilen_oneriler' => $kabul_edilen_oneriler])


</x-app-layout>