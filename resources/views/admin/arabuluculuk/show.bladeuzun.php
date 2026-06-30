<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
                    <span class="text-gray-500">#{{ $case->id }}</span>
                    {{ $case->dosya_no ?? 'Dosya No Bekleniyor' }}
                    
                    {{-- Dinamik Status Badge --}}
                    @php
                        $colors = [
                            'taslak' => 'bg-gray-100 text-gray-600',
                            'hukuk_incelemesinde' => 'bg-blue-100 text-blue-700',
                            'yonetim_onayinda' => 'bg-purple-100 text-purple-700',
                            'arabulucuda' => 'bg-indigo-100 text-indigo-700',
                            'imza_asamasinda' => 'bg-yellow-100 text-yellow-700',
                            'odeme_bekliyor' => 'bg-orange-100 text-orange-800 border border-orange-500 animate-pulse font-black',
                            'kapatildi' => 'bg-green-100 text-green-700',
                            'anlasma_saglanamadi' => 'bg-red-100 text-red-700',
                        ];
                        $statusClass = $colors[$case->status] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="px-3 py-1 text-sm rounded-full {{ $statusClass }} font-semibold shadow-sm">
                        {{ strtoupper(str_replace('_', ' ', $case->status)) }}
                    </span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    İlgili Çalışan: <span class="font-bold text-gray-700">{{ $case->calisan->name }}</span> 
                    ({{ $case->calisan->email }})
                </p>

                {{-- EKLENEN KISIM: Oluşturan ve Tarih --}}
                <p class="text-xs text-gray-400 mt-1 flex items-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Oluşturan: <span class="font-semibold">{{ $case->creator->name ?? 'Sistem' }}</span></span>
                    <span class="text-gray-300">|</span>
                    <span>{{ $case->created_at->format('d.m.Y H:i') }}</span>
                </p>
            </div>
            
            <div class="flex gap-2 items-center">
                
                {{-- 1. HUKUK İŞLEM BUTONLARI --}}
                @if($case->status == 'hukuk_incelemesinde' && (auth()->user()->can('arabuluculuk.approve_legal') || auth()->user()->hasRole('Superadmin')))
                    
                    {{-- Karar Ver Butonu (Modal veya Dropdown Açacak) --}}
                    <div x-data="{ openDecision: false }" class="relative">
                        <button @click="openDecision = !openDecision" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow font-bold flex items-center transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Karar Ver / İşlem Yap
                        </button>

                        {{-- Dropdown Menü --}}
                        <div x-show="openDecision" @click.away="openDecision = false" class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-xl z-50 border border-gray-200 p-4" style="display: none;">
                            <form action="{{ route('admin.arabuluculuk.submitDecision', $case->id) }}" method="POST">
                                @csrf
                                
                                <p class="text-xs font-bold text-gray-400 uppercase mb-2">Kararınız:</p>
                                
                                {{-- Seçenek 1: Yönetime Gönder --}}
                                <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="radio" name="action" value="send_to_board" class="mt-1 mr-2" checked>
                                    <div>
                                        <span class="font-bold text-sm text-gray-800">Yönetim Onayına Gönder</span>
                                        <p class="text-xs text-gray-500">Ben onaylıyorum, son kararı yönetim versin.</p>
                                    </div>
                                </label>

                                {{-- Seçenek 2: Doğrudan Onayla --}}
                                <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="radio" name="action" value="approve_direct" class="mt-1 mr-2">
                                    <div>
                                        <span class="font-bold text-sm text-green-700">Doğrudan Onayla</span>
                                        <p class="text-xs text-gray-500">Yönetimi pas geç, süreç ilerlesin.</p>
                                    </div>
                                </label>

                                {{-- Seçenek 3: Revizyon İste --}}
                                <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="radio" name="action" value="request_revision" class="mt-1 mr-2">
                                    <div>
                                        <span class="font-bold text-sm text-red-700">Revizyon İste (Geri Gönder)</span>
                                        <p class="text-xs text-gray-500">Personele iade et, düzeltme iste.</p>
                                    </div>
                                </label>

                                {{-- Not Alanı --}}
                                <textarea name="note" class="w-full text-xs border-gray-300 rounded mb-3" rows="2" placeholder="Gerekçe veya notunuz (Revizyon için zorunlu)..."></textarea>

                                <button type="submit" class="w-full bg-indigo-600 text-white text-sm font-bold py-2 rounded hover:bg-indigo-700">İşlemi Uygula</button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- 2. YÖNETİM İŞLEM BUTONLARI --}}
                @if($case->status == 'yonetim_onayinda' && (auth()->user()->can('arabuluculuk.approve_board') || auth()->user()->hasRole('Superadmin')))
                    
                    <div x-data="{ openBoard: false }" class="relative">
                        <button @click="openBoard = !openBoard" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow font-bold flex items-center transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Yönetim Kararı
                        </button>

                        <div x-show="openBoard" @click.away="openBoard = false" class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-xl z-50 border border-gray-200 p-4" style="display: none;">
                            <form action="{{ route('admin.arabuluculuk.submitDecision', $case->id) }}" method="POST">
                                @csrf
                                
                                <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="radio" name="action" value="board_approve" class="mt-1 mr-2" checked>
                                    <div>
                                        <span class="font-bold text-sm text-green-700">Onayla</span>
                                        <p class="text-xs text-gray-500">Süreci tamamla ve ödemeye geç.</p>
                                    </div>
                                </label>

                                <label class="flex items-start mb-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="radio" name="action" value="board_reject" class="mt-1 mr-2">
                                    <div>
                                        <span class="font-bold text-sm text-red-700">Hukuka İade Et (Revize)</span>
                                        <p class="text-xs text-gray-500">Dosyayı hukuk birimine geri gönder.</p>
                                    </div>
                                </label>

                                <textarea name="note" class="w-full text-xs border-gray-300 rounded mb-3" rows="2" placeholder="Karar notunuz..."></textarea>

                                <button type="submit" class="w-full bg-purple-600 text-white text-sm font-bold py-2 rounded hover:bg-purple-700">Kararı Kaydet</button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- 3. ARABULUCULUK SONUÇLANDIRMA BUTONLARI --}}
                @if($case->status == 'arabulucuda' && (
                    auth()->user()->can('arabuluculuk.approve_legal') || 
                    auth()->user()->hasRole('Superadmin') || 
                    (auth()->user()->can('arabuluculuk.assign_mediator') && auth()->id() == $case->created_by)
                ))
                    
                    <div x-data="{ openResult: false }" class="relative">
                        <button @click="openResult = !openResult" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow font-bold flex items-center transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Süreci Sonuçlandır
                        </button>

                        <div x-show="openResult" @click.away="openResult = false" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-xl z-50 border border-gray-200 p-4" style="display: none;">
                            <form action="{{ route('admin.arabuluculuk.submitDecision', $case->id) }}" method="POST">
                                @csrf
                                
                                <p class="text-xs font-bold text-gray-400 uppercase mb-3">Arabuluculuk Sonucu:</p>

                                {{-- Seçenek 1: Anlaşma --}}
                                <label class="flex items-start mb-3 cursor-pointer hover:bg-green-50 p-2 rounded border border-transparent hover:border-green-200 transition">
                                    <input type="radio" name="action" value="mediation_agreement" class="mt-1 mr-2" checked>
                                    <div>
                                        <span class="font-bold text-sm text-green-700">Anlaşma Sağlandı</span>
                                        <p class="text-xs text-gray-500">Süreci tamamla ve ödeme planına geç.</p>
                                    </div>
                                </label>

                                {{-- Seçenek 2: Anlaşamama --}}
                                <label class="flex items-start mb-3 cursor-pointer hover:bg-red-50 p-2 rounded border border-transparent hover:border-red-200 transition">
                                    <input type="radio" name="action" value="mediation_disagreement" class="mt-1 mr-2">
                                    <div>
                                        <span class="font-bold text-sm text-red-700">Anlaşma Sağlanamadı</span>
                                        <p class="text-xs text-gray-500">Süreci olumsuz olarak kapat.</p>
                                    </div>
                                </label>

                                <textarea name="note" class="w-full text-xs border-gray-300 rounded mb-3" rows="2" placeholder="Varsa arabulucu notları..."></textarea>

                                <button type="submit" class="w-full bg-green-600 text-white text-sm font-bold py-2 rounded hover:bg-green-700">Kaydet ve İlerlet</button>
                            </form>
                        </div>
                    </div>
                @endif

                <a href="{{ route('admin.arabuluculuk.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg shadow-sm font-medium transition">
                    &larr; Listeye Dön
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ activeTab: window.location.hash === '#files' ? 'dosyalar' : 'genel' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- HATA ve BAŞARI MESAJLARI (ALERT) --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center" role="alert">
                    <svg class="w-6 h-6 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm flex items-start" role="alert">
                    <svg class="w-6 h-6 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-bold">Dikkat!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif
        
       

            {{-- ÜST BİLGİ KARTLARI --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold">Süreç Türü</p>
                    <p class="text-lg font-bold {{ $case->type == 'zorunlu' ? 'text-red-600' : 'text-green-600' }}">
                        {{ ucfirst($case->type) }} Arabuluculuk
                    </p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold">Talep Edilen</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($case->talep_tutari, 2) }} TL</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold">Anlaşılan Tutar</p>
                    <p class="text-lg font-bold text-indigo-600">
                        {{ $case->anlasilan_tutar ? number_format($case->anlasilan_tutar, 2) . ' TL' : '---' }}
                    </p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold">Sorumlu Birim</p>
                    <div class="flex items-center mt-1">
                        @if($case->owner_role == 'hukuk')
                            <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Hukuk
                        @else
                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Personel
                        @endif
                    </div>
                </div>
            </div>

            {{-- FİNANS RED UYARISI (GENEL BAKIŞ) --}}
            @php
                $redOdeme = $case->payments->first();
            @endphp

            @if($redOdeme && !empty($redOdeme->red_gerekcesi))
                <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-5 rounded-r shadow-md flex items-start animate-pulse">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 w-full">
                        <h3 class="text-lg leading-6 font-bold text-red-800">
                            Ödeme İşlemi İade Edildi!
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Finans birimi ödeme planını aşağıdaki gerekçe ile reddetmiştir:</p>
                            <div class="mt-2 p-3 bg-white border border-red-200 rounded text-gray-800 font-bold italic shadow-sm">
                                "{{ $redOdeme->red_gerekcesi }}"
                            </div>
                        </div>
                        <div class="mt-3">
                            <button @click="activeTab = 'odeme'" class="text-sm font-bold text-red-600 hover:text-red-800 underline flex items-center">
                                Düzeltmek için Finans & Ödeme sekmesine git &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- === GENEL UYARI ALANI (HER SEKMEDE GÖRÜNÜR) === --}}
            @if($case->status == 'odeme_bekliyor' && $case->payments->isEmpty())
                @if(auth()->user()->hasRole('Superadmin') || auth()->user()->can('arabuluculuk.manage_payee'))
                    <div class="mb-8 bg-blue-50 border-l-4 border-blue-500 p-4 shadow-sm rounded-r flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-blue-800 text-lg">Eylem Gerekiyor</h3>
                            <p class="text-sm text-blue-700 mt-1">
                                Dosya arabulucudan "Anlaşma" ile dönmüştür. Süreci tamamlamak için lütfen <strong>"Finans & Ödeme"</strong> sekmesine giderek Ödeme Planı oluşturunuz.
                            </p>
                        </div>
                    </div>
                @endif
            @endif
            {{-- SON ONAY / KAPANIŞ UYARISI (Sticky Alert) --}}
            @if($case->status == 'son_onay_bekliyor' && (auth()->user()->can('arabuluculuk.final_check') || auth()->user()->hasRole('Superadmin')))
                <div class="mb-8 bg-indigo-50 border-l-4 border-indigo-500 p-4 shadow-sm rounded-r flex items-center justify-between animate-pulse">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 mr-3">
                            <span class="bg-indigo-100 p-2 rounded-full inline-block">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                        </div>
                        <div>
                            <h3 class="font-bold text-indigo-900 text-lg">Onayınız Bekleniyor</h3>
                            <p class="text-sm text-indigo-700 mt-1">
                                Finans birimi ödemeyi tamamladı ve dekontu yükledi. Dosyayı kapatmak için son onayı vermeniz gerekmektedir.
                            </p>
                        </div>
                    </div>
                    
                    {{-- Butona tıklayınca Finans sekmesine atar --}}
                    <button @click="activeTab = 'odeme'" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold shadow-sm transition whitespace-nowrap ml-4">
                        İncele ve Onay Ver &rarr;
                    </button>
                </div>
            @endif
            {{-- === GENEL UYARI SONU === --}}

            {{-- TAB MENÜSÜ --}}
            <div class="bg-white rounded-t-xl shadow-sm border-b border-gray-200 px-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'genel'" 
                        :class="activeTab === 'genel' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Genel Bakış
                    </button>

                    <button @click="activeTab = 'dosyalar'" 
                        :class="activeTab === 'dosyalar' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Dosyalar ({{ $case->files->count() }})
                    </button>

                    @if($case->board_required)
                    <button @click="activeTab = 'kurul'" 
                        :class="activeTab === 'kurul' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Kurul & Değerlendirme
                    </button>
                    @endif

                    <button @click="activeTab = 'odeme'" 
                        :class="activeTab === 'odeme' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Finans & Ödeme
                    </button>

                    <button @click="activeTab = 'log'" 
                        :class="activeTab === 'log' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center ml-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Tarihçe
                    </button>
                </nav>
            </div>

            {{-- İÇERİK ALANI --}}
            <div class="bg-white shadow-lg rounded-b-xl min-h-[500px]">
                
                {{-- 1. GENEL BAKIŞ --}}
                <div x-show="activeTab === 'genel'" class="p-6 space-y-6" x-transition>

                {{-- === DOSYA KAPANIŞ VE SÜREÇ ÖZETİ (SADECE KAPATILDI İSE GÖRÜNÜR) === --}}
                @if($case->status == 'kapatildi')
                    @php
                        // 1. GEREKLİ VERİLERİ TOPLUYORUZ
                        $odeme = $case->payments->first();
                        
                        // A) SÜRE HESABI
                        $baslangic = $case->created_at;
                        $bitis = $case->updated_at;
                        $farkSaat = $baslangic->diffInHours($bitis);
                        $yuvarlanmisGun = $farkSaat > 0 ? ceil($farkSaat / 24) : 1;
                        $detaySure = $baslangic->diffForHumans($bitis, ['parts' => 2, 'short' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);

                        // B) KİŞİLER (BAŞLATAN VE KAPATAN)
                        $baslatanKisi = $case->creator; // İlişki: creator
                        $kapatanLog = $case->logs->where('islem', 'DOSYA KAPATILDI')->last() ?? $case->logs->last();
                        $kapatanKisi = $kapatanLog ? ($kapatanLog->user->name ?? 'Sistem') : 'Bilinmiyor';

                        // C) ÖDEME VE FİNANSÇI
                        $dekont = $case->files->where('doc_type', 'dekont')->last();
                        if ($dekont) {
                            $odemeTarihi = $dekont->created_at->format('d.m.Y');
                            $finansci = $dekont->uploader->name ?? 'Finans Birimi';
                        } else {
                            $odemeTarihi = ($odeme && $odeme->odeme_tarihi) ? \Carbon\Carbon::parse($odeme->odeme_tarihi)->format('d.m.Y') : 'Belirtilmedi';
                            $finansci = 'Finans Birimi';
                        }

                        // D) YASAL BELGELER (YENİ EKLENDİ)
                        $taslakAnlasma = $case->files->where('doc_type', 'taslak_anlasma')->last();
                        $sonTutanak = $case->files->where('doc_type', 'arabuluculuk_son_tutanak')->last();
                    @endphp

                    <div class="bg-white border border-green-200 rounded-xl shadow-sm mb-8 overflow-hidden">
                        {{-- Başlık Kısmı --}}
                        <div class="bg-gradient-to-r from-green-50 to-white px-6 py-4 border-b border-green-100 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-500 text-white p-2 rounded-full shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-green-900 text-lg">Süreç Başarıyla Tamamlandı</h3>
                                    <p class="text-xs text-green-700">Dosya arşivlendi ve kapatıldı.</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-gray-400 uppercase">Kapanış Tarihi</span>
                                <p class="font-mono text-gray-700 font-bold">{{ $case->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="p-6">
                            {{-- 1. Üst İstatistikler --}}
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                                
                                {{-- Süreç Ömrü --}}
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 flex flex-col justify-center">
                                    <p class="text-xs text-gray-500 font-bold uppercase mb-1">Toplam Süreç</p>
                                    <div class="flex items-baseline gap-2">
                                        <p class="text-3xl font-black text-gray-800">{{ $yuvarlanmisGun }} GÜN</p>
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-medium mt-1">({{ $detaySure }})</p>
                                </div>

                                {{-- Nihai Tutar --}}
                                <div class="p-4 bg-green-50 rounded-lg border border-green-100 flex flex-col justify-center">
                                    <p class="text-xs text-green-600 font-bold uppercase mb-1">Ödenen Tutar</p>
                                    <p class="text-2xl font-black text-green-700">
                                        {{ number_format($case->anlasilan_tutar, 2) }} <span class="text-sm font-normal text-green-600">TL</span>
                                    </p>
                                </div>

                                {{-- Ödeme Tarihi --}}
                                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 flex flex-col justify-center">
                                    <p class="text-xs text-blue-600 font-bold uppercase mb-1">Ödeme Tarihi</p>
                                    <p class="text-xl font-bold text-blue-800 flex items-center gap-2">
                                        <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $odemeTarihi }}
                                    </p>
                                </div>

                                {{-- SÜRECİ BAŞLATAN (Turuncu Tema) --}}
                                <div class="p-4 bg-orange-50 rounded-lg border border-orange-100 flex flex-col justify-center">
                                    <p class="text-xs text-orange-600 font-bold uppercase mb-1">Süreci Başlatan</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-orange-200 flex items-center justify-center text-orange-700 font-bold text-xs uppercase">
                                            {{ substr($baslatanKisi->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-orange-900 truncate" title="{{ $baslatanKisi->name ?? 'Bilinmiyor' }}">
                                                {{ $baslatanKisi->name ?? 'Bilinmiyor' }}
                                            </p>
                                            {{-- Eğer rol varsa buraya eklenebilir --}}
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-orange-400 mt-1">Dosya Oluşturucu</p>
                                </div>

                                {{-- Kapatan Kişi --}}
                                <div class="p-4 bg-purple-50 rounded-lg border border-purple-100 flex flex-col justify-center">
                                    <p class="text-xs text-purple-600 font-bold uppercase mb-1">Dosyayı Kapatan</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-purple-200 flex items-center justify-center text-purple-700 font-bold text-xs uppercase">
                                            {{ substr($kapatanKisi, 0, 1) }}
                                        </div>
                                        <p class="text-sm font-bold text-purple-900 truncate" title="{{ $kapatanKisi }}">
                                            {{ $kapatanKisi }}
                                        </p>
                                    </div>
                                    <p class="text-[10px] text-purple-400 mt-1">Hukuk Onayı</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                
                                {{-- SOL KOLON: Transfer ve Belgeler --}}
                                <div class="flex flex-col gap-8">
                                    {{-- 2.a Transfer Bilgileri --}}
                                    <div>
                                        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            Transfer Bilgileri
                                        </h4>
                                        @if($odeme)
                                            <ul class="space-y-3 text-sm">
                                                <li class="flex justify-between">
                                                    <span class="text-gray-500">Alıcı Adı Soyadı:</span>
                                                    <span class="font-bold text-gray-800">{{ $odeme->odenecek_kisi }}</span>
                                                </li>
                                                <li class="flex justify-between">
                                                    <span class="text-gray-500">Banka:</span>
                                                    <span class="font-bold text-gray-800">{{ $odeme->banka_adi }}</span>
                                                </li>
                                                <li class="flex justify-between">
                                                    <span class="text-gray-500">IBAN:</span>
                                                    <span class="font-mono text-gray-600 bg-gray-100 px-2 rounded">{{ $odeme->iban }}</span>
                                                </li>
                                                <li class="flex justify-between items-center">
                                                    <span class="text-gray-500">Dekont:</span>
                                                    @if($dekont)
                                                        <a href="{{ asset('storage/'.$dekont->dosya_yolu) }}" target="_blank" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200 font-bold flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            Görüntüle
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400 italic">Yok</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        @else
                                            <p class="text-gray-400 italic text-sm">Ödeme bilgisi bulunamadı.</p>
                                        @endif
                                    </div>

                                    {{-- 2.b Yasal Belgeler (Transfer Bilgileri Altına Eklendi) --}}
                                    <div>
                                        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Yasal Belgeler
                                        </h4>
                                        <ul class="space-y-3 text-sm">
                                            {{-- Taslak Anlaşma --}}
                                            <li class="flex justify-between items-center">
                                                <span class="text-gray-500">Taslak Anlaşma:</span>
                                                @if($taslakAnlasma)
                                                    <a href="{{ asset('storage/'.$taslakAnlasma->dosya_yolu) }}" target="_blank" class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200 font-bold flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        Görüntüle
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 italic">Yok</span>
                                                @endif
                                            </li>

                                            {{-- Son Tutanak --}}
                                            <li class="flex justify-between items-center">
                                                <span class="text-gray-500">Son Tutanak:</span>
                                                @if($sonTutanak)
                                                    <a href="{{ asset('storage/'.$sonTutanak->dosya_yolu) }}" target="_blank" class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 font-bold flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                                        Görüntüle
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 italic">Yok</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- SAĞ KOLON: Süreç Zaman Çizelgesi --}}
                                <div>
                                    <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Süreç Geçmişi
                                    </h4>
                                    <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
                                        {{-- A. Başlangıç --}}
                                        <div class="relative pl-6">
                                            <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-gray-200 border-2 border-white"></span>
                                            <p class="text-xs text-gray-500">{{ $case->created_at->format('d.m.Y H:i') }}</p>
                                            <p class="text-sm font-bold text-gray-800">Süreç Başlatıldı</p>
                                            <p class="text-xs text-gray-500">Oluşturan: {{ $case->creator->name ?? 'Sistem' }}</p>
                                        </div>

                                        {{-- B. Arabulucu --}}
                                        @if($case->arabulucu)
                                            <div class="relative pl-6">
                                                <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-blue-200 border-2 border-white"></span>
                                                <p class="text-sm font-bold text-gray-800">Arabulucu Atandı</p>
                                                <p class="text-xs text-gray-500">{{ $case->arabulucu->name }}</p>
                                            </div>
                                        @endif

                                        {{-- C. Finansal İşlem --}}
                                        @if($dekont)
                                        <div class="relative pl-6">
                                            <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-yellow-400 border-2 border-white"></span>
                                            <p class="text-xs text-gray-500">{{ $dekont->created_at->format('d.m.Y H:i') }}</p>
                                            <p class="text-sm font-bold text-gray-800">Ödeme Yapıldı</p>
                                            <p class="text-xs text-gray-500">İşlemi Yapan: {{ $finansci }}</p>
                                        </div>
                                        @endif

                                        {{-- D. Kapanış --}}
                                        <div class="relative pl-6">
                                            <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-green-500 border-2 border-white animate-pulse"></span>
                                            <p class="text-xs text-green-600">{{ $case->updated_at->format('d.m.Y H:i') }}</p>
                                            <p class="text-sm font-bold text-green-800">Süreç Tamamlandı</p>
                                            <p class="text-xs text-gray-500">Kapatan: {{ $kapatanKisi }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                    
                {{-- MUTABAKAT VE DURUM YÖNETİMİ --}}
                @if($case->status != 'kapatildi' && $case->status != 'odeme_bekliyor')
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6">
                        <div class="flex justify-between items-center">
                            
                            {{-- SOL TARAFA: DURUM BİLGİSİ --}}
                            <div>
                                <h4 class="font-bold text-yellow-800">Mutabakat Durumu</h4>
                                <p class="text-sm text-yellow-600">
                                    Şu anki durum: 
                                    <span class="font-bold uppercase text-black">
                                        {{ $case->mutabakat == 'beklemede' ? 'HENÜZ KARAR VERİLMEDİ' : $case->mutabakat }}
                                    </span>
                                </p>
                            </div>

                            {{-- SAĞ TARAFA: İŞLEM BUTONLARI --}}
                            
                            {{-- DURUM 1: HENÜZ KARAR VERİLMEDİYSE (BEKLEMEDE) --}}
                            @if($case->mutabakat == 'beklemede')
                                
                                {{-- Yetki Kontrolü: Hukuk, Admin veya (Yetkili) Dosya Sahibi --}}
                                @if(auth()->user()->can('arabuluculuk.approve_legal') || 
                                    auth()->user()->hasRole('Superadmin') || 
                                    (auth()->user()->can('arabuluculuk.assign_mediator') && auth()->id() == $case->created_by))
                                    
                                    <form action="{{ route('admin.arabuluculuk.updateStatus', $case->id) }}" method="POST" class="flex gap-3">
                                        @csrf @method('PATCH')
                                        <button type="submit" name="mutabakat" value="anlasildi" class="bg-green-600 text-white px-4 py-2 rounded shadow font-bold hover:bg-green-700">Anlaşıldı</button>
                                        <button type="submit" name="mutabakat" value="anlasilmadi" class="bg-red-600 text-white px-4 py-2 rounded shadow font-bold hover:bg-red-700">Anlaşılmadı</button>
                                    </form>

                                @else
                                    {{-- Yetkisi Olmayanlar İçin Bilgi Mesajı --}}
                                    <span class="text-sm text-gray-400 italic bg-white px-3 py-1 rounded border border-gray-200">
                                        Mutabakat kararı bekleniyor...
                                    </span>
                                @endif

                            {{-- DURUM 2: KARAR VERİLMİŞSE (GERİ ALMA İŞLEMİ) --}}
                            @else
                                
                                {{-- Geri Alma Yetkisi: Hukuk, Admin veya (Kendi Dosyasıysa) Personel --}}
                                @if( 
                                    (in_array($case->status, ['hukuk_incelemesinde', 'anlasma_saglanamadi']) && auth()->id() == $case->created_by) || 
                                    (in_array($case->status, ['yonetim_onayinda', 'arabulucuda']) && (auth()->user()->can('arabuluculuk.approve_legal') || auth()->user()->hasRole('Superadmin')))
                                )
                                    <form action="{{ route('admin.arabuluculuk.revertStatus', $case->id) }}" method="POST" onsubmit="return confirm('İşlemi geri alıp taslak moduna dönmek istiyor musunuz?');">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-gray-500 hover:text-red-600 underline flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            Kararı Geri Al (Düzenlemeye Aç)
                                        </button>
                                    </form>
                                @endif

                            @endif

                        </div>
                    </div>
                @endif

                {{-- === YENİ EKLENEN: ARABULUCU ATAMA ALANI === --}}
                @if(in_array($case->status, ['yonetim_onayinda', 'arabulucuda']) && (auth()->user()->can('arabuluculuk.assign_mediator') || auth()->user()->hasRole('Superadmin') || auth()->user()->can('arabuluculuk.approve_legal')))
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-blue-900">Arabulucu Bilgisi</h4>
                                    @if($case->arabulucu)
                                        <p class="text-sm text-blue-700">Atanan: <span class="font-bold">{{ $case->arabulucu->name }}</span></p>
                                    @else
                                        <p class="text-sm text-red-500">Henüz arabulucu atanmadı.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- ATAMA FORMU --}}
                            <form action="{{ route('admin.arabuluculuk.assignMediator', $case->id) }}" method="POST" class="flex gap-2 w-full md:w-auto">
                                @csrf
                                @method('PATCH')
                                <select name="arabulucu_id" class="border-gray-300 rounded text-sm w-full md:w-64">
                                    <option value="">Arabulucu Seçiniz...</option>
                                    @foreach($arabulucular as $arabulucu)
                                        <option value="{{ $arabulucu->id }}" {{ $case->arabulucu_id == $arabulucu->id ? 'selected' : '' }}>
                                            {{ $arabulucu->name }} ({{ $arabulucu->sicil_no ?? 'Sicil Yok' }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-bold text-sm whitespace-nowrap">
                                    {{ $case->arabulucu_id ? 'Değiştir' : 'Ata' }}
                                </button>
                            </form>

                        </div>
                    </div>
                @endif
                {{-- === EKLEME SONU === --}}

                    {{-- TUTAR VE DETAYLAR DÜZENLEME --}}
                    @php
                        $canEditDetails = auth()->user()->can('arabuluculuk.edit') || 
                                          auth()->user()->can('arabuluculuk.approve_legal') || 
                                          auth()->user()->hasRole('Superadmin');
                    @endphp

                    <div x-data="{ editMode: {{ $canEditDetails && !$case->anlasilan_tutar ? 'true' : 'false' }} }" class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 mb-6 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="font-bold text-indigo-800 flex items-center text-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Anlaşma Detayları
                            </h3>
                            
                            {{-- Düzenle Butonu --}}
                            @if($canEditDetails)
                                <button x-show="!editMode" @click="editMode = true" type="button" class="text-sm text-indigo-600 hover:text-indigo-800 font-bold underline flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Düzenle
                                </button>
                            @endif
                        </div>

                        {{-- 1. GÖRÜNTÜLEME MODU (DÜZENLİ LİSTE) --}}
                        <div x-show="!editMode">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- Tutar --}}
                                <div class="bg-white p-4 rounded border border-indigo-100 shadow-sm">
                                    <label class="block text-xs font-bold text-indigo-400 uppercase mb-1">Anlaşma Tutarı</label>
                                    <p class="text-2xl font-bold text-indigo-700">{{ $case->anlasilan_tutar ? number_format($case->anlasilan_tutar, 2) . ' TL' : '---' }}</p>
                                </div>

                                {{-- Maddeler ve Notlar --}}
                                <div class="col-span-2 bg-white p-4 rounded border border-indigo-100 shadow-sm">
                                    <label class="block text-xs font-bold text-indigo-400 uppercase mb-2">Anlaşılan Maddeler ve Notlar</label>
                                    
                                    @if($case->anlasma_maddeleri)
                                        <div class="text-sm text-gray-700 space-y-2 leading-relaxed">
                                            {{-- Metni satır satır bölüp düzgün gösteriyoruz --}}
                                            {!! nl2br(e($case->anlasma_maddeleri)) !!}
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-400 italic">Henüz detay girilmemiş.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 2. DÜZENLEME MODU --}}
                        @if($canEditDetails)
                            <div x-show="editMode" class="mt-4 pt-4 border-t border-indigo-200">
                                <form action="{{ route('admin.arabuluculuk.update', $case->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    {{-- Tutar --}}
                                    <div class="mb-6">
                                        <label class="block text-sm font-bold mb-2 text-gray-700">Anlaşma Tutarı (TL)</label>
                                        <input type="number" step="0.01" name="anlasilan_tutar" value="{{ $case->anlasilan_tutar }}" class="w-full md:w-1/3 border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 font-bold text-lg" placeholder="0.00">
                                    </div>

                                    {{-- Maddeler (Checkbox) --}}
                                    <div class="mb-6">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">
                                            Anlaşma Maddeleri (Zorunlu Seçim) <span class="text-red-500">*</span>
                                        </label>
                                        
                                        @if(isset($anlasmaMaddeleri) && $anlasmaMaddeleri->count() > 0)
                                            <div class="bg-white border border-gray-200 rounded-lg p-4 max-h-60 overflow-y-auto space-y-3 shadow-inner">
                                                @foreach($anlasmaMaddeleri as $madde)
                                                    <label class="flex items-start gap-3 p-2 hover:bg-gray-50 rounded cursor-pointer transition select-none">
                                                        {{-- CHECKBOX LOGİC: Eğer veritabanındaki metin bu maddenin içeriğini barındırıyorsa TİK AT --}}
                                                        <input type="checkbox" name="maddeler_secim[]" value="{{ $madde->icerik }}" 
                                                            {{ Str::contains($case->anlasma_maddeleri ?? '', $madde->icerik) ? 'checked' : '' }}
                                                            class="mt-1 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                        
                                                        <div class="text-sm text-gray-700">
                                                            <span class="font-medium">{{ $madde->icerik }}</span>
                                                            @if($madde->hukuki_dayanak)
                                                                <span class="block text-xs text-gray-400 mt-0.5">({{ $madde->hukuki_dayanak }})</span>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <p class="text-xs text-gray-400 mt-2 italic">* Birden fazla madde seçebilirsiniz. Seçimleriniz otomatik birleştirilecektir.</p>
                                        @else
                                            <div class="text-red-500 text-sm p-2 bg-red-50 rounded">
                                                Sistemde tanımlı madde bulunamadı.
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Ek Notlar (Ayrıştırma) --}}
                                    @php
                                        // "EK NOTLAR:" ifadesinden sonrasını çekip kutuya koyuyoruz
                                        $mevcutNot = '';
                                        if($case->anlasma_maddeleri && Str::contains($case->anlasma_maddeleri, 'EK NOTLAR:')) {
                                            $parts = explode('EK NOTLAR:', $case->anlasma_maddeleri);
                                            $mevcutNot = trim(end($parts));
                                        }
                                    @endphp

                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ek Notlar (Opsiyonel)</label>
                                        <textarea name="ek_notlar" class="w-full border-gray-300 rounded text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" placeholder="Listede olmayan özel bir durum varsa buraya yazınız...">{{ $mevcutNot }}</textarea>
                                    </div>

                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="editMode = false" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded font-medium hover:bg-gray-50 transition">Vazgeç</button>
                                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded font-bold shadow hover:bg-indigo-700 transition">Değişiklikleri Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 2. DOSYALAR --}}
                <div x-show="activeTab === 'dosyalar'" class="p-6" style="display: none;" x-transition>
                    
                    {{-- BLOK 1: YÜKLEME FORMU VE UYARILAR --}}
                    
                    {{-- A) DOSYA KAPATILDI İSE KIRMIZI UYARI --}}
                    @if($case->status == 'kapatildi')
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        <strong>Dosya Kapatıldı:</strong> Bu dosya kapatıldığı için artık yeni belge yüklenemez.
                                    </p>
                                </div>
                            </div>
                        </div>

                    {{-- B) PERSONEL KISITLAMASI (ÖDEME AŞAMASINDA) MAVİ UYARI --}}
                    {{-- Eğer servet bey ise (created_by) VE durum ödeme bekliyorsa VE Hukuk yetkisi yoksa --}}
                    @elseif(auth()->id() == $case->created_by && $case->status == 'odeme_bekliyor' && !auth()->user()->can('arabuluculuk.approve_legal'))
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">Dosya şu an ödeme planı aşamasında olduğu için dosya yükleme yetkiniz geçici olarak kısıtlanmıştır.</p>
                                    <p class="text-xs text-blue-600 mt-1">Bir hata varsa "Finans & Ödeme" sekmesinden süreci geri çekebilirsiniz.</p>
                                </div>
                            </div>
                        </div>

                    {{-- C) NORMAL DURUM: YÜKLEME FORMUNU GÖSTER --}}
                    @else
                        <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                            <h3 class="font-bold mb-3">Yeni Belge Yükle</h3>
                            <form action="{{ route('admin.arabuluculuk.uploadFile', $case->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-4 items-end">
                                @csrf
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Belge Türü</label>
                                    <select name="doc_type" class="w-full border-gray-300 rounded text-sm">
                                        
                                        {{-- 1. TASLAK AŞAMASI (Hukuk Admini de görebilsin düzeltmesi) --}}
                                        @if($case->status == 'taslak' && (
                                            auth()->id() == $case->created_by || 
                                            auth()->user()->hasRole('Superadmin') || 
                                            auth()->user()->can('arabuluculuk.approve_legal')
                                        ))
                                            <option value="taslak_anlasma">Taslak Anlaşma Belgesi</option>
                                            <option value="anlasma_saglanamadi_tutanagi">Anlaşma Sağlanamadı Tutanağı</option>
                                        @endif

                                        {{-- 2. DİĞER AŞAMALAR --}}
                                        @if($case->status != 'taslak') 
                                            @if(auth()->user()->can('arabuluculuk.approve_legal') || 
                                                auth()->user()->can('arabuluculuk.assign_mediator') || 
                                                auth()->user()->hasRole('Superadmin'))
                                                
                                                <option value="imzali_belge">İmzalı Belge (PDF/UDF)</option>
                                                <option value="islak_imza_teslim">Islak İmza Teslim Tutanağı</option>
                                                <option value="arabuluculuk_son_tutanak">Arabuluculuk Son Tutanağı</option>
                                            @endif
                                        
                                            {{-- Dekont sadece plan varsa --}}
                                            @if(auth()->user()->can('arabuluculuk.finance_pay') || auth()->user()->hasRole('Superadmin'))
                                                @if($case->payments->count() > 0)
                                                    <option value="dekont">Ödeme Dekontu</option> 
                                                @endif
                                            @endif
                                        @endif
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dosyalar (Çoklu Seçilebilir)</label>
                                    <input type="file" name="files[]" multiple class="w-full border border-gray-300 rounded p-1 text-sm bg-white" required>
                                </div>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700 h-10">Yükle</button>
                            </form>
                        </div>
                    @endif
                    
                    {{-- BLOK 2: DOSYA LİSTESİ --}}
                    @if($case->files->count() > 0)
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($case->files as $file)
                                <div class="flex justify-between items-center p-3 border rounded hover:bg-gray-50 {{ $file->locked ? 'bg-red-50 border-red-200' : '' }}">
                                    <div class="flex items-center gap-3">
                                        {{-- Dosya İkonları --}}
                                        @if(Str::endsWith($file->dosya_yolu, '.pdf'))
                                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                                        @elseif(Str::endsWith($file->dosya_yolu, ['.doc', '.docx']))
                                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                        @endif
                                        
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">
                                                {{ $file->orijinal_adi }} 
                                                <span class="text-xs text-gray-500 bg-gray-200 px-1 rounded ml-1">{{ $file->doc_type }}</span>
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $file->created_at->format('d.m.Y H:i') }} - {{ $file->uploader->name ?? 'Sistem' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        @if($file->locked)
                                            <span class="text-xs font-bold text-red-600 flex items-center bg-red-100 px-2 py-1 rounded" title="Yasal belge olduğu için kilitli">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                KİLİTLİ
                                            </span>
                                        @endif
                                        
                                        <a href="{{ asset('storage/' . $file->dosya_yolu) }}" target="_blank" class="text-sm font-bold text-blue-600 hover:underline">İndir</a>

                                        {{-- SİLME BUTONU (HİBRİT YETKİ) --}}
                                        @if(
                                            auth()->user()->hasRole('Superadmin') || 
                                            (auth()->id() == $case->created_by && $case->status == 'taslak') || 
                                            (auth()->id() == $file->uploaded_by && $case->status != 'kapatildi')
                                        )
                                            <form action="{{ route('admin.arabuluculuk.deleteFile', ['file' => $file->id]) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Sil">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 text-gray-500">Henüz dosya yüklenmemiş.</div>
                    @endif
                </div>

                {{-- 3. KURUL --}}
                <div x-show="activeTab === 'kurul'" class="p-6" style="display: none;" x-transition>
                    {{-- Kurul Değerlendirme Formu --}}
                    <div class="bg-gray-50 border rounded-lg p-5 mb-6">
                        <h4 class="font-bold text-gray-700 mb-3">Değerlendirme Ekle</h4>
                        <form action="{{ route('admin.arabuluculuk.addComment', $case->id) }}" method="POST">
                            @csrf
                            <textarea name="yorum" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Görüş ve değerlendirmenizi buraya yazınız..."></textarea>
                            <div class="mt-3 flex justify-between items-center">
                                <div class="w-1/3">
                                    <select name="karar" class="w-full border-gray-300 rounded-md text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Karar (Opsiyonel)</option>
                                        <option value="Onay">Onay / Olumlu</option>
                                        <option value="Red">Red / Olumsuz</option>
                                        <option value="Revize">Revize Gerekli</option>
                                    </select>
                                </div>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow text-sm font-bold transition">Kaydet</button>
                            </div>
                        </form>
                    </div>

                    {{-- Geçmiş Değerlendirmeler --}}
                    @foreach($case->kurulDegerlendirmesi as $degerlendirme)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center">
                                    <div class="font-bold text-gray-900">{{ $degerlendirme->user->name ?? 'Bilinmeyen' }}</div>
                                    <span class="text-xs text-gray-500 ml-2">{{ $degerlendirme->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                @if($degerlendirme->karar)
                                    <span class="px-2 py-1 text-xs font-bold rounded {{ $degerlendirme->karar == 'Onay' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $degerlendirme->karar }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-700 text-sm">{{ $degerlendirme->yorum }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- 4. ÖDEME --}}
                <div x-show="activeTab === 'odeme'" class="p-6" style="display: none;" x-transition>
                    {{-- PERSONEL GERİ ALMA BUTONU --}}
                    @if($case->status == 'odeme_bekliyor' && $case->payments->isEmpty() && auth()->id() == $case->created_by)
                        <div class="mb-4 text-right">
                            <form action="{{ route('admin.arabuluculuk.revertToMediation', $case->id) }}" method="POST" onsubmit="return confirm('Emin misiniz?');">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 underline hover:text-red-600">
                                    &larr; Dosyayı Düzenlemek İçin Geri Çek
                                </button>
                            </form>
                        </div>
                    @endif
                    @if($case->mutabakat != 'anlasildi')
                        <div class="text-center py-10">
                            <div class="bg-orange-50 text-orange-800 p-4 rounded-lg inline-block">
                                <p class="font-bold">Ödeme Ekranı Kapalı</p>
                                <p class="text-sm mt-1">Ödeme ekranının açılması için sürecin "Anlaşma Sağlandı" olarak sonuçlandırılması gerekir.</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                            
                        {{-- BAŞLIK, DEKONT VE TUTANAK KONTROLÜ --}}
                        <div class="flex justify-between items-start mb-6 border-b border-green-200 pb-4">
                            {{-- SOL TARAF: BAŞLIK --}}
                            <div>
                                <h3 class="font-bold text-green-900 text-lg">Finansal İşlemler</h3>
                                <p class="text-xs text-green-700">Ödeme planı ve doğrulama ekranı.</p>
                            </div>

                            {{-- SAĞ TARAF: BUTONLAR GRUBU --}}
                            <div class="flex flex-col sm:flex-row items-end gap-2">

                                {{-- 1. DEKONT BUTONU (YENİ EKLENEN) --}}
                                @php
                                    // Dekontu bul (Birden fazla yükleme ihtimaline karşı son yükleneni alıyoruz)
                                    $dekont = $case->files->where('doc_type', 'dekont')->last();
                                @endphp
                                @if($dekont)
                                <a href="{{ asset('storage/' . $dekont->dosya_yolu) }}" target="_blank" class="flex items-center gap-2 bg-white px-3 py-2 rounded border border-green-300 text-green-700 hover:bg-green-50 transition shadow-sm">
                                        {{-- Yeşil Belge İkonu --}}
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <div class="text-left">
                                            <p class="text-xs font-bold">Ödeme Dekontu</p>
                                            <p class="text-[10px] text-gray-500">Görüntüle</p>
                                        </div>
                                    </a>
                                @endif

                                {{-- 2. ARABULUCULUK SON TUTANAĞI (MEVCUT KOD) --}}
                                @php
                                    $sonTutanak = $case->files->where('doc_type', 'arabuluculuk_son_tutanak')->first();
                                @endphp
                                @if($sonTutanak)
                                <a href="{{ asset('storage/' . $sonTutanak->dosya_yolu) }}" target="_blank" class="flex items-center gap-2 bg-white px-3 py-2 rounded border border-green-300 text-green-700 hover:bg-green-100 transition shadow-sm">
                                        {{-- Kırmızı PDF İkonu --}}
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                        <div class="text-left">
                                            <p class="text-xs font-bold">Arabuluculuk Son Tutanağı</p>
                                            <p class="text-[10px] text-gray-500">İncelemek için tıklayın</p>
                                        </div>
                                    </a>
                                @else
                                    <span class="text-xs text-red-600 font-bold bg-red-100 px-2 py-1 rounded self-center">DİKKAT: Son tutanak bulunamadı!</span>
                                @endif

                            </div>
                        </div>
                            
                            {{-- SENARYO A: ÖDEME PLANI HENÜZ YOKSA (FORMU GÖSTER) --}}
                            @php $mevcutOdeme = $case->payments->first(); @endphp @if(!$mevcutOdeme || !empty($mevcutOdeme->red_gerekcesi))
                                
                                @if(auth()->user()->hasRole('Superadmin') || auth()->user()->can('arabuluculuk.manage_payee'))
                                    
                                        {{-- Validasyon Hatalarını Göster --}}
                                        @if ($errors->any())
                                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                                <strong class="font-bold">Lütfen şu hataları düzeltin:</strong>
                                                <ul class="mt-2 list-disc list-inside text-sm">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    <form action="{{ route('admin.arabuluculuk.savePayment', $case->id) }}" method="POST" x-data="{ banka: '' }">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            
                                            {{-- 1. Ödenecek Kişi --}}
                                            <div class="col-span-2 mb-4">
                                                <label class="block text-xs font-bold text-gray-700 uppercase mb-3 flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    Ödeme Yapılacak Kişi
                                                </label>

                                                <div class="flex flex-col sm:flex-row gap-3">
                                                    <div class="w-full sm:w-5/12 flex items-center gap-2 bg-indigo-50 px-4 py-3 rounded-lg border border-indigo-200">
                                                        <label class="text-sm font-semibold text-indigo-700 whitespace-nowrap">Tip:</label>
                                                        <select class="w-full bg-transparent border-none p-0 text-sm font-semibold text-gray-700 focus:ring-0 cursor-pointer"
                                                                name="odeme_alici_tipi"
                                                                id="odeme_alici_tipi"
                                                                onchange="toggleAliciInput()">
                                                            <option value="calisan" selected>Çalışana Öde</option>
                                                            <option value="diger">Diğer / Avukat</option>
                                                        </select>
                                                    </div>

                                                    <input type="text"
                                                        class="w-full sm:w-7/12 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-4 py-3 bg-gray-50"
                                                        name="odenecek_kisi" 
                                                        id="odenecek_kisi_ad_soyad"
                                                        {{-- Mantık: Varsa kayıtlı ödemeyi getir, yoksa personeli getir, o da yoksa BOŞ bırak --}}
                                                        value="{{ $mevcutOdeme ? $mevcutOdeme->odenecek_kisi : ($arabuluculuk->relatedUser->name ?? '') }}"
                                                        placeholder="Ad Soyad Giriniz..."
                                                        readonly>
                                                </div>

                                                <div class="mt-2 flex items-start gap-2 text-xs text-gray-600 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span id="input_aciklama">Ödeme varsayılan olarak ilgili personele ({{ $arabuluculuk->relatedUser->name ?? 'Sinan Poyraz' }}) yapılacaktır.</span>
                                                </div>
                                            </div>

                                            {{-- 2. Tutar (Otomatik ve Readonly) --}}
                                            <div class="col-span-2 md:col-span-1">
                                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ödenecek Tutar (Otomatik)</label>
                                                <div class="relative">
                                                    <input type="text" value="{{ number_format($case->anlasilan_tutar, 2) }} TL" class="w-full bg-gray-100 border-gray-300 rounded text-sm font-bold text-gray-600 cursor-not-allowed" readonly>
                                                    <input type="hidden" name="tutar" value="{{ $mevcutOdeme ? $mevcutOdeme->tutar : $case->anlasilan_tutar }}">
                                                </div>
                                            </div>

                                            {{-- 3. Banka Seçimi --}}
                                            <div class="col-span-2 md:col-span-1">
                                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Banka</label>
                                                <select name="banka_adi" x-model="banka" class="w-full border-gray-300 rounded text-sm focus:ring-green-500 focus:border-green-500" required>
                                                    <option value="">Banka Seçiniz...</option>
                                                    @php $bankalar = ['Ziraat Bankası', 'Garanti BBVA', 'İş Bankası', 'Akbank', 'Yapı Kredi', 'Halkbank', 'Vakıfbank', 'QNB Finansbank', 'Denizbank', 'TEB']; @endphp
                                                    @foreach($bankalar as $b)
                                                        <option value="{{ $b }}" {{ ($mevcutOdeme && $mevcutOdeme->banka_adi == $b) ? 'selected' : '' }}>
                                                            {{ $b }}
                                                        </option>
                                                    @endforeach
                                                    <option value="Diğer" {{ ($mevcutOdeme && !in_array($mevcutOdeme->banka_adi, $bankalar)) ? 'selected' : '' }}>Diğer</option>
                                                </select>
                                                
                                                {{-- Diğer Seçilirse Açılan Input --}}
                                                <div x-show="banka === 'Diğer'" class="mt-2" style="display: none;">
                                                    <input type="text" name="banka_adi_manuel" class="w-full border-gray-300 rounded text-sm placeholder-gray-400" placeholder="Banka adını yazınız...">
                                                </div>
                                            </div>

                                            {{-- 4. IBAN (TR Zorunlu, Boşluklu Girilebilir) --}}
                                            <div class="col-span-2 md:col-span-1">
                                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">IBAN (TR ile Başlayan)</label>
                                                {{-- maxlength'i 34 yaptık ki boşluklu sığsın --}}
                                                <input type="text" name="iban" maxlength="34" class="w-full border-gray-300 rounded text-sm font-mono uppercase focus:ring-green-500 focus:border-green-500" 
                                                    value="{{ $mevcutOdeme ? $mevcutOdeme->iban : '' }}"
                                                    placeholder="TR76 0000..." required 
                                                    oninput="this.value = this.value.toUpperCase()">
                                                <p class="text-[10px] text-gray-500 mt-1">Boşluklu veya bitişik yazabilirsiniz.</p>
                                            </div>

                                            {{-- 5. Son Ödeme Tarihi (Opsiyonel) --}}
                                            <div class="col-span-2">
                                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Son Ödeme Tarihi (Opsiyonel)</label>
                                                <input type="date" name="son_odeme_tarihi" class="w-full border-gray-300 rounded text-sm focus:ring-green-500 focus:border-green-500">
                                                <p class="text-[10px] text-gray-500 mt-1">Belirtilirse finans yetkilisi ekranında uyarı olarak görünecektir.</p>
                                            </div>

                                        </div>
                                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-3 rounded font-bold shadow hover:bg-indigo-700 transition">
                                            Ödeme Planını Kaydet ve Finansa Gönder
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center p-4 bg-white rounded border border-gray-200">
                                        <p class="text-red-500 text-sm font-bold">Ödeme planı henüz oluşturulmadı.</p>
                                        <p class="text-xs text-gray-500">Hukuk birimi tarafından plan oluşturulduğunda burada görünecektir.</p>
                                    </div>
                                @endif

                            {{-- SENARYO B: ÖDEME PLANI OLUŞTURULMUŞSA (DETAYLARI GÖSTER) --}}
                            @else
                                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6 shadow-sm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                                        
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase">Ödenecek Kişi</label>
                                            <p class="text-gray-800 font-bold text-base">{{ $case->payments->first()->odenecek_kisi }}</p>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase">Tutar</label>
                                            <p class="text-gray-800 font-bold text-base">{{ number_format($case->anlasilan_tutar, 2) }} TL</p>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase">Banka & IBAN</label>
                                            <p class="text-gray-800 text-sm">{{ $case->payments->first()->banka_adi }}</p>
                                            <p class="text-gray-600 font-mono text-sm tracking-wide bg-gray-100 px-2 py-1 rounded inline-block mt-1">
                                                {{ $case->payments->first()->iban }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase">Durum</label>
                                            @if($case->payments->first()->odeme_durumu == 'odendi')
                                                <span class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                    ÖDENDİ ({{ \Carbon\Carbon::parse($case->payments->first()->odeme_tarihi)->format('d.m.Y') }})
                                                </span>
                                            @else
                                                <span class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                    ÖDEME BEKLİYOR
                                                </span>
                                            @endif
                                        </div>

                                        {{-- YANIP SÖNEN TARİH UYARISI --}}
                                        @if($case->payments->first()->son_odeme_tarihi && $case->payments->first()->odeme_durumu != 'odendi')
                                            <div class="col-span-2 mt-2">
                                                <div class="flex items-center gap-2 p-2 bg-red-50 border border-red-200 rounded animate-pulse">
                                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="text-red-700 font-bold text-sm">
                                                        SON ÖDEME TARİHİ: {{ \Carbon\Carbon::parse($case->payments->first()->son_odeme_tarihi)->format('d.m.Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>

                                {{-- FİNANS İŞLEMLERİ (ONAY / RED) --}}
                                @if(auth()->user()->can('arabuluculuk.finance_pay') || auth()->user()->hasRole('Superadmin'))
                                    @if($case->payments->first()->odeme_durumu == 'bekliyor')
                                        
                                        <div class="border-t border-green-200 pt-4">
                                            <p class="text-sm text-gray-600 mb-3 text-right">İşlemi tamamlamak için lütfen önce DEKONT yükleyiniz.</p>
                                            
                                            <div class="flex justify-end gap-3">
                                                {{-- RED BUTONU --}}
                                                <button type="button" onclick="document.getElementById('rejectForm').classList.toggle('hidden')" class="bg-red-600 text-white px-4 py-2 rounded font-bold hover:bg-red-700">
                                                    Reddet / İade Et
                                                </button>

                                                {{-- ONAY BUTONU --}}
                                                <form action="{{ route('admin.arabuluculuk.approvePayment', $case->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded font-bold hover:bg-green-800">
                                                        Ödemeyi Onayla ve Son Kontrole İlet
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- GİZLİ RED FORMU --}}
                                            <div id="rejectForm" class="hidden mt-4 bg-red-50 p-4 rounded border border-red-200">
                                                <form action="{{ route('admin.arabuluculuk.rejectPayment', $case->id) }}" method="POST">
                                                    @csrf
                                                    <textarea name="reason" class="w-full border-red-300 rounded mb-2 text-sm" placeholder="Red gerekçesi (IBAN hatalı, Tutar yanlış vb.)..." required></textarea>
                                                    <div class="text-right">
                                                        <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded text-sm font-bold">Gerekçeyi Kaydet ve Geri Gönder</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    @endif
                                @endif

                            @endif
                        </div>
                    @endif

                    {{-- SON ONAY KUTUSU --}}
                    @if($case->status == 'son_onay_bekliyor' && (auth()->user()->can('arabuluculuk.final_check') || auth()->user()->hasRole('Superadmin')))
                        <div class="bg-indigo-900 text-white p-6 rounded-lg mb-6 shadow-xl mt-6">
                            <h3 class="font-bold text-xl mb-2">🏁 Son Kontrol ve Kapanış</h3>
                            <p class="mb-4 text-indigo-200">Ödeme yapılmış ve dekont yüklenmiş. Lütfen son kontrolleri yapıp dosyayı kapatınız.</p>
                            <form action="{{ route('admin.arabuluculuk.finalClose', $case->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-white text-indigo-900 font-bold py-3 rounded hover:bg-gray-100 transition">
                                    Dosyayı Kapat ve Arşivle
                                </button>
                            </form>
                        </div>
                    @endif
                    
                </div>

                {{-- 5. LOG --}}
                <div x-show="activeTab === 'log'" class="p-6" style="display: none;" x-transition>
                    <ul role="list" class="-mb-8">
                        @foreach($case->logs as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    <span class="font-medium text-gray-900">{{ $log->islem }}</span>: {{ $log->detay }}
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $log->created_at }}">{{ $log->created_at->format('d.m.Y H:i') }}</time>
                                                <br>
                                                <span class="text-xs text-gray-400">{{ $log->user->name ?? 'Sistem' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <script>
    var personelAdi = "{{ $arabuluculuk->relatedUser->name ?? $arabuluculuk->personel->name ?? 'Sinan Poyraz' }}";

    function toggleAliciInput() {
        var select = document.getElementById('odeme_alici_tipi');
        var input = document.getElementById('odenecek_kisi_ad_soyad');
        var aciklama = document.getElementById('input_aciklama');

        if (select.value === 'calisan') {
            input.value = personelAdi;
            input.setAttribute('readonly', true);
            input.classList.add('bg-gray-50');
            input.classList.remove('bg-white');
            aciklama.textContent = 'Ödeme varsayılan olarak ilgili personele (' + personelAdi + ') yapılacaktır.';
            aciklama.parentElement.className = "mt-2 flex items-start gap-2 text-xs text-gray-600 bg-blue-50 p-3 rounded-lg border border-blue-100";
        } else {
            input.value = '';
            input.removeAttribute('readonly');
            input.classList.remove('bg-gray-50');
            input.classList.add('bg-white');
            input.focus();
            aciklama.textContent = 'Lütfen alıcının tam adını ve soyadını giriniz.';
            aciklama.parentElement.className = "mt-2 flex items-start gap-2 text-xs text-amber-700 bg-amber-50 p-3 rounded-lg border border-amber-200";
        }
    }
</script>
</x-app-layout>