{{-- resources/views/proje-calisma-alani/partials/_step-item.blade.php --}}

@props([
    'step',
    'isCompleted',
    'isCurrent',
    'progressUpdate',
    'isTeamMember',
    'iaa',
    'assignment',
    'takim',
    'stepAssignments' => []
])

<div id="step-card-{{ $step->id }}" class="mb-10 ml-6" x-data="{ open: {{ $isCompleted ? 'false' : ($isCurrent ? 'true' : 'false') }} }">
    
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
        
        {{-- Adım Başlığı --}}
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
          
            {{-- === GÖREV ATAMA VE BİLGİ ALANI === --}}
            @php
                $assignmentData = $stepAssignments[$step->id] ?? null;
                
                // === HATA DÜZELTMESİ BURADA YAPILDI ===
                // Önce giriş yapılmış mı (Auth::check()) bakıyoruz.
                // Giriş yapılmadıysa (Misafir ise) sağ taraftaki Auth::user() çalışmaz, hata vermez.
                $isLeader = Auth::check() && ((Auth::id() == $takim->lider_user_id) || Auth::user()->hasRole('Superadmin'));
                
                $sorumluUser = $assignmentData ? \App\Models\User::find($assignmentData->user_id) : null;
                
                // Ben miyim? (Misafirde Auth::id null döner, eşitlik false olur, sorun çıkmaz)
                $isMe = $assignmentData && $assignmentData->user_id == Auth::id();
                
                $waitingSince = $assignmentData ? \Carbon\Carbon::parse($assignmentData->updated_at)->diffForHumans() : '';
            @endphp

            <div class="mt-3 mb-3 flex items-center justify-between bg-gray-50 p-2.5 rounded-lg border border-gray-200 shadow-sm">
                
                {{-- SOL: Durum Bilgisi --}}
                <div class="flex items-center gap-3 text-sm">
                    @if($sorumluUser)
                        @if($isCompleted)
                             <span class="text-gray-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-medium text-gray-900">{{ $sorumluUser->name }}</span> tarafından tamamlandı.
                            </span>
                        @else
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    @if($sorumluUser->profile_photo_path)
                                        <img src="{{ asset('storage/'.$sorumluUser->profile_photo_path) }}" class="w-9 h-9 rounded-full border border-gray-300 shadow-sm">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600 shadow-sm">{{ substr($sorumluUser->name, 0, 1) }}</div>
                                    @endif
                                    <span class="absolute -bottom-1 -right-1 flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500 border border-white"></span>
                                    </span>
                                </div>
                                <div class="flex flex-col leading-tight">
                                    <span class="text-gray-800 font-semibold">
                                        {{ $sorumluUser->name }} bekleniyor...
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $waitingSince }} atandı</span>
                                </div>
                            </div>
                        @endif
                    @else
                        <span class="text-gray-400 italic text-xs flex items-center gap-1.5">
                            <div class="p-1 bg-gray-200 rounded-full">
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            Sorumlu atanmamış (Ortak Görev)
                        </span>
                    @endif
                </div>

                {{-- SAĞ: Atama Formu (Sadece Lider ve Giriş Yapmış Kişiler Görür) --}}
                @if($isLeader && !$isCompleted)
                    <form action="{{ route('proje.workspace.assignUserToStep', ['iaa' => $iaa->id, 'step' => $step->id]) }}" method="POST">
                        @csrf
                        <div class="relative">
                        <select name="user_id" onchange="this.form.submit()" class="appearance-none bg-white border border-gray-300 text-gray-700 text-xs rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-40 py-1.5 pl-3 pr-8 cursor-pointer hover:border-gray-400 transition-colors">
                                <option value="">-- Sorumlu Seç --</option>
                                @foreach($iaa->projeEkibi as $ekipUyesi)
                                    @if($ekipUyesi->pivot->durum == 'onaylandi')
                                        <option value="{{ $ekipUyesi->id }}" {{ ($sorumluUser && $sorumluUser->id == $ekipUyesi->id) ? 'selected' : '' }}>
                                            {{ $ekipUyesi->name }}
                                        </option>
                                    @endif
                                @endforeach
                                 @if(!$iaa->projeEkibi->contains($takim->lider_user_id))
                                    <option value="{{ $takim->lider_user_id }}" {{ ($sorumluUser && $sorumluUser->id == $takim->lider_user_id) ? 'selected' : '' }}>
                                        {{ $takim->lider->name }} (Lider)
                                    </option>
                               @endif
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </form>
                @endif
            </div>

            <p class="text-sm font-normal text-gray-600 mt-2">{{ $step->description }}</p>
        </div>
        
        {{-- Tamamlanmış Adım İçeriği --}}
        @if($isCompleted && $progressUpdate)
            @include('proje-calisma-alani.partials._step-content-completed', [
                'progressUpdate' => $progressUpdate,
                'step' => $step,
                'iaa' => $iaa 
            ])
        @endif

        {{-- Aktif Adım İçeriği (Form) --}}
        @if($isCurrent)
             @include('proje-calisma-alani.partials._step-content-active', [
                'iaa' => $iaa,
                'assignment' => $assignment,
                'currentStep' => $step, 
                'progressUpdate' => $progressUpdate,
                'isTeamMember' => $isTeamMember,
                'takim' => $takim
             ])
        @endif

        {{-- Yorum/Log Bileşeni --}}
        @if($isCompleted || $isCurrent)
        <div class="mt-8 mb-4 px-6 md:px-10">
            @livewire('admin.proje-adim-yorumlari', [
                'iaa' => $iaa, 
                'step' => $step
            ], key($step->id))
        </div>
        @endif
    </div>
</div>