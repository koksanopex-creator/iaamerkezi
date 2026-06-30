{{-- resources/views/proje-calisma-alani/partials/_project-tabs.blade.php --}}
@php
    $hasComplaintDetails = isset($iaa) && $iaa->musteriSikayeti;
    $hasFaultyPanel = isset($isComplaintProject) && $isComplaintProject && Auth::check() && !Auth::user()->hasAnyRole(['Müşteri', 'Müşteri Temsilcisi']) && is_null(Auth::user()->customer_id); // Müşteri ve Temsilcisine gizle
    $hasCustomerNotification = $hasComplaintDetails && Auth::check() && Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi']);
    $isGuestOrCustomer = !Auth::check();
    $defaultTab = $isGuestOrCustomer ? 'squad' : 'details';
@endphp

<div x-data="{ activeTab: '{{ $defaultTab }}' }" class="mb-8">
    {{-- SEKME BAŞLIKLARI --}}
    <div
        class="bg-white rounded-t-xl shadow-sm border border-b-0 border-indigo-100 flex flex-nowrap overflow-x-auto hide-scrollbar">
        {{-- 1. Detaylar ve Şikayet (Sadece giriş yapmış kullanıcılara) --}}
        @if(!$isGuestOrCustomer)
            <button @click="activeTab = 'details'" :class="{ 
                            'text-indigo-700 font-bold border-b-2 border-indigo-600 bg-indigo-50/50': activeTab === 'details', 
                            'text-gray-500 font-medium hover:text-indigo-600 hover:bg-gray-50': activeTab !== 'details' 
                        }"
                class="flex items-center gap-2 px-6 py-4 transition-colors whitespace-nowrap focus:outline-none flex-1 justify-center relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Proje Detayları
            </button>
        @endif

        {{-- 2. Proje Ekibi (Squad) --}}
        @if($hasComplaintDetails)
            <button @click="activeTab = 'squad'" :class="{ 
                                'text-indigo-700 font-bold border-b-2 border-indigo-600 bg-indigo-50/50': activeTab === 'squad', 
                                'text-gray-500 font-medium hover:text-indigo-600 hover:bg-gray-50': activeTab !== 'squad' 
                            }"
                class="flex items-center gap-2 px-6 py-4 transition-colors whitespace-nowrap focus:outline-none flex-1 justify-center relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Proje Ekibi (Squad)
                @php $aktifUyeCount = $iaa->projeEkibi->filter(fn($u) => $u->pivot->rol == 'Lider' || $u->pivot->durum == 'onaylandi')->count(); @endphp
                @if($aktifUyeCount > 0)
                    <span
                        class="ml-1 inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-indigo-100 bg-indigo-600 rounded-full">{{ $aktifUyeCount }}</span>
                @endif
            </button>
        @endif

        {{-- 3. Müşteri İletişimi --}}
        @if($hasCustomerNotification)
            <button @click="activeTab = 'customer'" :class="{ 
                                'text-indigo-700 font-bold border-b-2 border-indigo-600 bg-indigo-50/50': activeTab === 'customer', 
                                'text-gray-500 font-medium hover:text-indigo-600 hover:bg-gray-50': activeTab !== 'customer' 
                            }"
                class="flex items-center gap-2 px-6 py-4 transition-colors whitespace-nowrap focus:outline-none flex-1 justify-center relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Müşteri İletişimi
            </button>
        @endif

        {{-- 4. Sorun / Hata Bildirimleri --}}
        @if($hasFaultyPanel)
            <button @click="activeTab = 'issues'" :class="{ 
                                'text-red-700 font-bold border-b-2 border-red-500 bg-red-50/50': activeTab === 'issues', 
                                'text-gray-500 font-medium hover:text-red-600 hover:bg-gray-50': activeTab !== 'issues' 
                            }"
                class="flex items-center gap-2 px-6 py-4 transition-colors whitespace-nowrap focus:outline-none flex-1 justify-center relative">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Operasyonel Sorunlar
                @if(str_contains($iaa->durum, 'hatali_bildirim_onayi_bekliyor') || str_contains($iaa->durum, 'talep_onayi_bekliyor'))
                    <span class="absolute top-3 right-4 w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                    <span class="absolute top-3 right-4 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </button>
        @endif
    </div>

    {{-- SEKME İÇERİKLERİ --}}
    <div class="bg-white rounded-b-xl shadow-sm border border-indigo-100 p-6">

        {{-- İÇERİK 1: DETAYLAR (Teknik + Şikayet İçeriği) --}}
        @if(!$isGuestOrCustomer)
            <div x-show="activeTab === 'details'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">
                @include('proje-calisma-alani.partials._technical-details')
            </div>
        @endif

        {{-- İÇERİK 2: PROJE EKİBİ YAPISI --}}
        @if($hasComplaintDetails)
            <div x-show="activeTab === 'squad'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">
                @include('proje-calisma-alani.partials._squad', ['iaa' => $iaa])
            </div>
        @endif

        {{-- İÇERİK 3: MÜŞTERİ BİLDİRİMİ --}}
        @if($hasCustomerNotification)
            <div x-show="activeTab === 'customer'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">
                @include('proje-calisma-alani.partials._customer-notification', ['iaa' => $iaa])
            </div>
        @endif

        {{-- İÇERİK 4: HATALI VE TALEP YÖNETİM PANELİ --}}
        @if($hasFaultyPanel)
            <div x-show="activeTab === 'issues'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">
                <div class="space-y-6">
                    @include('proje-calisma-alani.partials._talep-notification', ['iaa' => $iaa])
                    @include('proje-calisma-alani.partials._faulty-notification', ['iaa' => $iaa])
                </div>
            </div>
        @endif

    </div>
</div>