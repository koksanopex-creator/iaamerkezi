<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proje Çalışma Alanı: <span class="text-indigo-600">{{ $iaa->baslik }}</span>
            </h2>
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Geri Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- 1.5. TEKNİK DETAYLAR (YENİ EKLENEN KISIM) --}}
            @include('proje-calisma-alani.partials._technical-details')

            {{-- TALEP YÖNETİM PANELİ (GİZLİ AKIŞ) --}}
            {{-- SADECE ŞİKAYET İSE GÖSTER --}}
            @if(isset($isComplaintProject) && $isComplaintProject) 
                @include('proje-calisma-alani.partials._talep-notification', ['iaa' => $iaa])
                
                {{-- HATALI BİLDİRİM PANELİ (YENİ) --}}
                @include('proje-calisma-alani.partials._faulty-notification', ['iaa' => $iaa])
            @endif

            {{-- 2. SQUAD (PROJE EKİBİ) --}}
            @include('proje-calisma-alani.partials._squad', ['iaa' => $iaa])

            {{-- 3. MÜŞTERİ BİLGİLENDİRME PANELİ --}}
            @include('proje-calisma-alani.partials._customer-notification', ['iaa' => $iaa])

            {{-- 4. MÜŞTERİ ŞİKAYETİ DETAYLARI (Açılır/Kapanır Kart) --}}
            @include('proje-calisma-alani.partials._complaint-details', ['iaa' => $iaa])

            {{-- 5. İŞ AKIŞI ADIMLARI (TIMELINE) --}}
            @include('proje-calisma-alani.partials._timeline', [
                'steps' => $steps,
                'completedStepIds' => $completedStepIds,
                'progressUpdates' => $progressUpdates,
                'isTeamMember' => $isTeamMember,
                'iaa' => $iaa,
                'assignment' => $assignment,
                'takim' => $takim,
                'stepAssignments' => $stepAssignments ?? [],
                'canEdit' => $canEdit,
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
                $formGoster = in_array($iaa->durum, $duzenlemeDurumlari); // Sadece bu durumlarda form açılır
                $kartGoster = in_array($iaa->durum, $onayVeBitisDurumlari); // Sadece bu durumlarda kart açılır

                // İade verisi var mı?
                $iadeVar = $iaa->musteriSikayeti && $iaa->musteriSikayeti->iadeler->isNotEmpty();

                // Yetki
                $isLeaderOrAdmin = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || auth()->user()->hasRole('Superadmin');
            @endphp

            {{-- 1. İADE DETAY KARTI (SADECE ONAY/BİTİŞ DURUMUNDA GÖRÜNÜR) --}}
            @if($kartGoster && $iadeVar)
                @include('proje-calisma-alani.partials._return-details-card')
            @endif

            {{-- 1.5. ONAY DURUM PANELİ (SADECE ONAY/BİTİŞ DURUMUNDA GÖRÜNÜR) --}}
            @if($kartGoster)
                @include('proje-calisma-alani.partials._project-final-status', ['iaa' => $iaa, 'statusDate' => $statusDate ?? null])
            @endif

            {{-- 2. PROJE KAPANIŞ FORMU (SADECE DÜZENLEME DURUMUNDA GÖRÜNÜR) --}}
            @if($formGoster && $isLeaderOrAdmin)
                @include('proje-calisma-alani.partials._project-completion')
            @endif

            {{-- 3. AKSİYON BUTONLARI (HER DURUMDA ÇAĞRILMALI, İÇERİDE KONTROL VAR) --}}
            {{-- Not: Geri alma butonları için tamamlanmış/reddedilmiş durumlar da dahil edilmeli --}}
            @if(in_array($iaa->durum, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Revize Ediliyor', 'Tamamlanması Reddedildi', 'Tamamlandı']))
                @include('proje-calisma-alani.partials._action-buttons')
            @endif

            {{-- ================================================================= --}}

            {{-- 7. LOGLAR (GEÇMİŞ) --}}
            @if(Auth::check() && Auth::user()->is_personnel == 1)
                @include('proje-calisma-alani.partials._logs', [
                    'sonOnLoglar' => $sonOnLoglar,
                    'tumProjeLoglari' => $tumProjeLoglari
                ])
            @endif

        </div>
    </div>

    {{-- 7. SCRIPTS --}}
    @include('proje-calisma-alani.partials._scripts')

</x-app-layout>