{{-- resources/views/proje-calisma-alani/partials/_step-item.blade.php --}}

@props([
    'step',
    'isCompleted',
    'isCurrent',
    'progressUpdate',
    'isTeamMember',
    'iaa',
    'assignment',
    'takim'
])

<div class="mb-10 ml-6" x-data="{ open: {{ $isCompleted ? 'false' : ($isCurrent ? 'true' : 'false') }} }">
    
    {{-- Zaman Çizgisi İkonu --}}
    <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-4 ring-white shadow-md transition-all duration-300
        {{ $isCompleted ? 'bg-gradient-to-br from-green-400 to-green-600' : ($isCurrent ? 'bg-gradient-to-br from-blue-400 to-blue-600 animate-pulse' : 'bg-gray-300') }}">
        @if ($isCompleted) 
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        @elseif ($isCurrent)
            <span class="w-3 h-3 bg-white rounded-full"></span>
        @endif
    </span>
    
    {{-- Adım Kartı --}}
    <div class="bg-white border-2 {{ $isCurrent ? 'border-blue-300 shadow-lg' : 'border-gray-200' }} rounded-xl p-5 hover:shadow-md transition-shadow duration-300">
        
        {{-- Adım Başlığı (Açılır/Kapanır Tetikleyici) --}}
        <div @if($isCompleted || $isCurrent) @click="open = !open" class="cursor-pointer" @else class="cursor-default" @endif>
            <h4 class="flex items-center justify-between text-base font-semibold {{ $isCurrent ? 'text-blue-700' : 'text-gray-900' }}">
                <span>{{ $step->order }}. {{ $step->name }}</span>
                <div>
                    @if($isCompleted) 
                        <span class="bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm">✓ Tamamlandı</span> 
                    @endif
                    @if($isCurrent) 
                        <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm animate-pulse">● Aktif Adım</span> 
                    @endif
                </div>
            </h4>
            <p class="text-sm font-normal text-gray-600 mt-2">{{ $step->description }}</p>
        </div>
        
        {{-- Tamamlanmış Adım İçeriği --}}
        @if($isCompleted && $progressUpdate)
            @include('proje-calisma-alani.partials._step-content-completed', [
                'progressUpdate' => $progressUpdate,
                'step' => $step
            ])
        @endif

        {{-- Aktif Adım İçeriği (Form) --}}
        @if($isCurrent)
             @include('proje-calisma-alani.partials._step-content-active', [
                'iaa' => $iaa,
                'assignment' => $assignment,
                'currentStep' => $step, // $currentStep yerine $step kullanmak daha doğru
                'progressUpdate' => $progressUpdate,
                'isTeamMember' => $isTeamMember,
                'takim' => $takim
             ])
        @endif

        {{-- === YORUM/LOG BİLEŞENİNİ BURAYA EKLEYİN === --}}
        @if($isCompleted || $isCurrent)
        <div class="mt-8 mb-4 px-6 md:px-10">
            @livewire('admin.proje-adim-yorumlari', [
                'iaa' => $iaa, 
                'step' => $step
            ], key($step->id))
        </div>
        {{-- === YORUM BİLEŞENİ SONU === --}}
        @endif
    </div>
</div>