@props(['iaa', 'assignment', 'currentStep', 'progressUpdate', 'isTeamMember', 'takim', 'canEdit' => false])

@php
    // KİLİT MANTIĞI: Talep sürecindeyse veya kapandıysa
    $kilitliDurumlar = [
        'talep_onayi_bekliyor_kalite', 
        'talep_onayi_bekliyor_superadmin', 
        'talep_olarak_kapatildi',
    ];
    // Müdahale Yetkisi Kontrolü: Eğer müdahale yetkisi varsa kilit bypass edilebilir mi?
    // Servis tarafında (ProjeAdimIslemleriService) kilitli durumlarda işlem kesinlikle engellendiği için 
    // UI tarafında da tutarlılık adına kimse (Superadmin dahil) işlem yapamamalıdır.
    $isLocked = in_array($iaa->durum, $kilitliDurumlar);
@endphp

<div x-show="open" x-transition>
    @if($isLocked)
        {{-- KİLİTLİ UYARISI --}}
        <div class="bg-gray-50 border-l-4 border-gray-400 p-6 rounded-r-lg shadow mt-4 text-center">
            <h3 class="text-xl font-bold text-gray-700 flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                İşlem Yapılamaz
            </h3>
            <p class="text-gray-600 mt-2">
                Proje şu anda <strong>{{ $iaa->durum }}</strong> durumundadır. <br>
                Talep süreci sonuçlanana kadar bu adım üzerinde işlem yapılamaz.
            </p>
        </div>
    
    @elseif ($canEdit)
        {{-- NORMAL AKIŞ --}}
        @livewire('project.active-step', [
            'iaa' => $iaa, 
            'assignment' => $assignment, 
            'currentStep' => $currentStep, 
            'progressUpdate' => $progressUpdate
        ])

        <div class="mt-8">
            @livewire('project.plan-visit', [
                'iaa' => $iaa, 
                'embedded' => true, 
                'stepId' => $currentStep['id']
            ])
        </div>
    @else
        {{-- GÖZLEMCİ MODU --}}
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg shadow mt-4">
            <h3 class="text-xl font-bold text-yellow-800">Yönetici Gözlem Modu</h3>
            <p class="text-yellow-700 mt-2">
                Bu projenin ilerlemesini izliyorsunuz. Projeyi sadece atanmış olan <strong>{{ $takim->ad }}</strong> takımı ilerletebilir.
                <br>
                Şu anki aktif adım: <strong>{{ $currentStep->name }}</strong>.
            </p>
        </div>
    @endif
</div>