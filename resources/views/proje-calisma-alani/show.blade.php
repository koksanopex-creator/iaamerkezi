<x-app-layout>
    @push('pageTitle'){{ $iaa->musteriSikayeti ? $iaa->musteriSikayeti->musteri_sikayet_konusu : $iaa->baslik }} | @endpush
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proje Çalışma Alanı: <span class="text-indigo-600">{{ $iaa->baslik }}</span>
            </h2>
            <div class="flex items-center space-x-4">
                @if(!$iaa->musteriSikayeti)
                    <a href="{{ route('iaa.show', $iaa->id) }}"
                        class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors duration-200 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        İyileştirme Önerisine Git
                    </a>
                @endif
                <a href="{{ url()->previous() == url()->current() ? route('dashboard') : url()->previous() }}"
                    class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors duration-200 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Geri Dön
                </a>
                
                {{-- PROJE ÇIKTI ALMA BUTONLARI (ÜST) --}}
                <div class="flex items-center space-x-2 border-l pl-4 ml-4 border-gray-200">
                    <a href="{{ route('proje.export.pdf', $iaa->id) }}" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm" title="PDF Raporu İndir">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        PDF
                    </a>
                    <a href="{{ route('proje.export.excel', $iaa->id) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm" title="Excel Raporu İndir">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Excel
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl shadow-sm mb-6 flex items-start" role="alert">
                    <svg class="w-6 h-6 mr-3 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-bold">Başarılı İşlem</p>
                        <p class="text-sm mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- PROJE DAVET ONAY BANDI --}}
            @if($iaa->projeEkibi()->where('users.id', auth()->id())->where('iaa_user.durum', 'bekliyor')->exists())
                <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl shadow-lg mb-8 overflow-hidden transform transition-all hover:scale-[1.01] duration-300">
                    <div class="px-6 py-5 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-4 text-white">
                            <div class="p-3 bg-white/20 backdrop-blur-md rounded-xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight">Bu projeye davet edildiniz!</h3>
                                <p class="text-indigo-100 text-sm font-medium">Projeye tam erişim sağlamak ve görevleri yönetmek için daveti cevaplayın.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <form action="{{ route('iaa.davetYanitla', $iaa->id) }}" method="POST" class="flex-1 md:flex-none">
                                @csrf
                                <input type="hidden" name="yanit" value="kabul">
                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-white text-indigo-700 rounded-xl font-black text-sm shadow-xl hover:bg-indigo-50 transition-all active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    KABUL ET
                                </button>
                            </form>
                            <form action="{{ route('iaa.davetYanitla', $iaa->id) }}" method="POST" class="flex-1 md:flex-none">
                                @csrf
                                <input type="hidden" name="yanit" value="red">
                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-indigo-500/30 text-white border border-white/30 rounded-xl font-bold text-sm hover:bg-rose-500/40 hover:border-rose-300/50 transition-all active:scale-95">
                                    REDDET
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ÇÖP KUTUSU UYARISI / PROJE DURDURULDU --}}
            @if($iaa->musteriSikayeti && $iaa->musteriSikayeti->trashed())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-red-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h3 class="text-red-800 font-bold text-lg">Bu proje durduruldu! Kaynak Şikayet Çöp Kutusunda!</h3>
                            <p class="text-red-700 text-sm mt-0.5">Bu projenin bağlı olduğu müşteri şikayeti silinmiş durumdadır. Bu nedenle projede yeni bir işlem yapılamaz, adımlar ilerletilemez ve proje tamamlanamaz. İşlem yapabilmek için kaynak şikayetin geri yüklenmesi gerekir.</p>
                        </div>
                    </div>
                    @php
                        $isAuthorizedQM = auth()->user()->hasRole('Bölüm Kalite Yöneticisi') && ($isQualityManagerInterventionPower ?? false);
                    @endphp
                    @if(auth()->user()->hasRole(['Superadmin', 'Super Admin', 'Yonetim', 'Yönetim']) || $isAuthorizedQM)
                        <a href="{{ route('admin.sikayetler.show', $iaa->musteriSikayeti->id) }}" class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Şikayete Git
                        </a>
                    @endif
                </div>
            @endif

            {{-- 1. MÜŞTERİ BİLGİ BANDI (Sadece Şikayet Kaynaklı Projeler İçin) --}}
            @if($iaa->musteriSikayeti && $iaa->musteriSikayeti->customer)
                <div class="bg-white rounded-2xl shadow-sm border-l-8 border-indigo-500 overflow-hidden mb-6 p-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-50 rounded-xl">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">İlgili Müşteri</span>
                            <h2 class="text-lg font-black text-gray-800 uppercase">
                                <a href="{{ route('musteri.profil.show', $iaa->musteriSikayeti->customer_id) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $iaa->musteriSikayeti->customer->name }}
                                </a>
                            </h2>
                        </div>
                    </div>
                    <div class="hidden md:block text-right">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Şikayet Konusu</span>
                        <p class="text-sm font-bold text-gray-700 italic">"{{ $iaa->musteriSikayeti->musteri_sikayet_konusu }}"</p>
                        
                        {{-- MÜŞTERİ HATIRLATMA BUTONU --}}
                        <div class="mt-2">
                            @include('admin.sikayet-hatirlatma.partials._hatirlatma-butonu', ['sikayet' => $iaa->musteriSikayeti])
                        </div>
                    </div>
                </div>
            @endif

            {{-- 1. PROJE KÜNYESİ (Zaten partial idi) --}}
            @include('proje-calisma-alani.partials._project-header', [
                'iaa' => $iaa,
                'takim' => $takim,
                'assignment' => $assignment,
                'progressPercentage' => $progressPercentage,
                'completedStepsCount' => $completedStepsCount,
                'totalStepsCount' => $totalStepsCount,
                'statusDate' => $statusDate ?? null
            ])



            @php
                $isTrashed = $iaa->musteriSikayeti && $iaa->musteriSikayeti->trashed();
            @endphp

            {{-- 2. OPERASYON MERKEZİ (SEKMELİ YAPI) --}}
            @include('proje-calisma-alani.partials._project-tabs')

            {{-- 2.5. MÜŞTERİ ŞİKAYETİ DETAYLARI (Açılır/Kapanır Kart - Varsayılan Kapalı) --}}
            @include('proje-calisma-alani.partials._complaint-details', ['iaa' => $iaa])

            {{-- 2.6. EK SÜRE TALEBİ FORMU VE ONAY KARTI --}}
            @include('proje-calisma-alani.partials._extension-request', [
                'iaa' => $iaa, 
                'isLeaderOrAdmin' => auth()->check() && (($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || auth()->user()->hasRole('Superadmin'))
            ])

            {{-- 3. İŞ AKIŞI ADIMLARI (TIMELINE) --}}
            @include('proje-calisma-alani.partials._timeline', [
                'steps' => $steps,
                'completedStepIds' => $completedStepIds,
                'progressUpdates' => $progressUpdates,
                'isTeamMember' => $isTeamMember,
                'iaa' => $iaa,
                'assignment' => $assignment,
                'takim' => $takim,
                'stepAssignments' => $stepAssignments ?? [],
                'canEdit' => $isTrashed ? false : $canEdit,
                'statusDate' => $statusDate ?? null
            ])

           
            {{-- ================================================================= --}}
            {{-- === KAPANIŞ VE ONAY SÜRECİ ALANI (AKILLI AYRIŞTIRMA) === --}}
            {{-- ================================================================= --}}
            @php
                // Durum Grupları
                $duzenlemeDurumlari = ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor'];
                $onayVeBitisDurumlari = ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Tamamlandı', 'Talep Olarak Kapatıldı', 'Revize Ediliyor', 'Tamamlanması Reddedildi'];

                // Kontroller
                $formGoster = !$isTrashed && in_array($iaa->durum, $duzenlemeDurumlari); // Sadece bu durumlarda form açılır
                $kartGoster = in_array($iaa->durum, $onayVeBitisDurumlari); // Sadece bu durumlarda kart açılır
                $showCompletionForm = $progressPercentage == 100 && in_array($iaa->durum, $duzenlemeDurumlari);

                // İade verisi var mı?
                $iadeVar = $iaa->musteriSikayeti && $iaa->musteriSikayeti->iadeler->isNotEmpty();

                // Yetki
                $isLeaderOrAdmin = false;
                if (auth()->check()) {
                    $isLeaderOrAdmin = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || auth()->user()->hasRole('Superadmin');
                }
            @endphp

            {{-- 1. İADE DETAY KARTLARI (FORM GÖSTERİLMİYORSA VEYA SAHA TEMSİLCİSİ İSE) --}}
            @if(!$showCompletionForm || auth()->user()->hasRole('Müşteri Saha Temsilcisi'))
                @if($iadeVar && $kartGoster)
                    @include('proje-calisma-alani.partials._return-details-card')
                @endif
            @endif

            {{-- 1.5. ONAY DURUM PANELİ (SADECE ONAY/BİTİŞ DURUMUNDA GÖRÜNÜR) --}}
            @if($kartGoster)
                @include('proje-calisma-alani.partials._project-final-status', ['iaa' => $iaa, 'statusDate' => $statusDate ?? null])
            @endif

            {{-- 2. PROJE KAPANIŞ FORMU (TÜM ADIMLAR TAMAMLANDIĞINDA HERKESE GÖRÜNÜR, İÇERİDE YETKİ KONTROLÜ VAR) --}}
            @if($showCompletionForm && !auth()->user()->hasRole('Müşteri Saha Temsilcisi'))
                <div id="ziyaret-bilgileri-alani"></div>
                @include('proje-calisma-alani.partials._project-completion')
            @endif

            {{-- 3. AKSİYON BUTONLARI (HER DURUMDA ÇAĞRILMALI, İÇERİDE KONTROL VAR) --}}
            {{-- Not: Geri alma butonları için tamamlanmış/reddedilmiş durumlar da dahil edilmeli --}}
            @if(!$isTrashed && in_array($iaa->durum, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Revize Ediliyor', 'Tamamlanması Reddedildi', 'Tamamlandı']) && !auth()->user()->hasRole('Müşteri Saha Temsilcisi'))
                @include('proje-calisma-alani.partials._action-buttons')
            @endif

            {{-- 7. LOGLAR (GEÇMİŞ) - Sadece Yetkililer Görebilir (Admin, Yönetim, Direktör, Ekip Lideri) --}}
            @php
                $user = Auth::user();
                $isAdminOrManagement = $user->hasRole(['Superadmin', 'Yonetim']);
                $isDirector = $user->hasRole('Direktör');
                $isTeamLeader = $iaa->projeEkibi()
                    ->where('user_id', $user->id)
                    ->where('iaa_user.rol', 'Lider')
                    ->exists();
                
                $canSeeHistory = $isAdminOrManagement || $isDirector || $isTeamLeader || ($isQualityManagerInterventionPower ?? false);
            @endphp

            @if(Auth::check() && Auth::user()->is_personnel == 1 && $canSeeHistory)
                @include('proje-calisma-alani.partials._logs', [
                    'sonOnLoglar' => $sonOnLoglar,
                    'tumProjeLoglari' => $tumProjeLoglari
                ])
            @endif

            {{-- PROJE ÇIKTI ALMA (EXPORT) KARTI --}}
            <div class="mt-8 mb-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md relative">
                <div class="bg-gradient-to-r from-slate-100 via-white to-white px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between md:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <div class="p-2 bg-slate-200 text-slate-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </div>
                            Proje Çıktıları ve Raporlama
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 pl-11">Projenin tüm loglarını ve verilerini farklı formatlarda indirebilirsiniz.</p>
                    </div>
                </div>
                
                <div class="p-6 md:p-8 flex flex-col sm:flex-row justify-center items-center gap-4 bg-slate-50">
                    <a href="{{ route('proje.export.pdf', $iaa->id) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-red-600 border border-transparent rounded-xl font-bold text-sm text-white tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        PDF RAPORU İNDİR
                    </a>
                    <a href="{{ route('proje.export.excel', $iaa->id) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-green-600 border border-transparent rounded-xl font-bold text-sm text-white tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        EXCEL VERİSİ İNDİR
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- 7. SCRIPTS --}}
    @include('proje-calisma-alani.partials._scripts')

</x-app-layout>