@php
    // KİLİT MANTIĞI: Eğer süreç 'Talep' aşamasına girdiyse veya Hatalı Bildirim Onayı bekliyorsa Timeline gizlenmeli.
    $kilitliDurumlar = [
        'talep_onayi_bekliyor_kalite',
        'talep_onayi_bekliyor_superadmin',
        'talep_olarak_kapatildi',
        'hatali_bildirim_onayi_bekliyor_kalite',
        'hatali_bildirim_onayi_bekliyor_direktor',
        'hatali_bildirim_onayi_bekliyor_superadmin',
        'hatali_bildirim_olarak_kapatildi'
    ];
    $isLocked = in_array($iaa->durum, $kilitliDurumlar);
@endphp

{{-- KİLİTLİ DEĞİLSE GÖSTER --}}
@if(!$isLocked)
    <div class="w-full">
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Proje Adımları</h3>



            <div class="relative border-l-2 border-gray-200">
                @php $currentStepFound = false; @endphp

                @foreach ($steps as $step)
                    @php
                        $isCompleted = in_array($step->id, $completedStepIds);
                        $progressUpdate = $progressUpdates[$step->id] ?? null;
                        $isCurrent = !$isCompleted && !$currentStepFound;

                        if ($isCurrent) {
                            $currentStepFound = true;
                        }
                    @endphp

                    {{-- ID EKLENDİ: Sayfa kaydırma (scroll) için hedef nokta --}}
                    {{-- scroll-mt-24: Üst bardan pay bırakır --}}
                    <div id="step-{{ $step->id }}" class="scroll-mt-24">
                        @include('proje-calisma-alani.partials._step-item', [
                            'step' => $step,
                            'isCompleted' => $isCompleted,
                            'isCurrent' => $isCurrent,
                            'progressUpdate' => $progressUpdate,
                            'isTeamMember' => $isTeamMember,
                            'iaa' => $iaa,
                            'assignment' => $assignment,
                            'takim' => $takim,
                            'stepAssignments' => $stepAssignments ?? [],
                            'canEdit' => $canEdit
                        ])
                                            </div>
                @endforeach
                        </div>
                    </div>
                </div>
@endif