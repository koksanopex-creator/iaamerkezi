<div class="w-full">
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Proje Adımları</h3>
        
        @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert"><p>{{ session('success') }}</p></div>
        @endif
        
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
            @endforeach
            
            {{-- Proje bittiyse (aktif adım yoksa) final durum kutusunu göster --}}
            @if (!$currentStepFound)
                {{-- DÜZELTME: 'sikayet' değişkenini burada tanımlayıp içeri gönderiyoruz --}}
                @include('proje-calisma-alani.partials._project-final-status', [
                    'iaa' => $iaa,
                    'statusDate' => $statusDate ?? null,
                    'sikayet' => $iaa->musteriSikayeti // <--- BU SATIRI EKLEYİN
                ])

                @include('proje-calisma-alani.partials._action-buttons', ['iaa' => $iaa])  
            @endif
        </div>
    </div>
</div>