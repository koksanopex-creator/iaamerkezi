@props(['iaa', 'assignment', 'currentStep', 'progressUpdate', 'isTeamMember', 'takim'])

<div x-show="open" x-transition>
    @if ($isTeamMember)
        @livewire('project.active-step', [
            'iaa' => $iaa, 
            'assignment' => $assignment, 
            'currentStep' => $currentStep, 
            'progressUpdate' => $progressUpdate
        ])
    @else
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