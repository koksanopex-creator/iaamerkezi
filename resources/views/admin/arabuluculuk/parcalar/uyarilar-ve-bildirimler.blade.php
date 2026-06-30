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

{{-- FİNANS RED UYARISI --}}
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

{{-- GENEL UYARI ALANI --}}
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

{{-- SON ONAY / KAPANIŞ UYARISI --}}
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
        <button @click="activeTab = 'odeme'" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold shadow-sm transition whitespace-nowrap ml-4">
            İncele ve Onay Ver &rarr;
        </button>
    </div>
@endif