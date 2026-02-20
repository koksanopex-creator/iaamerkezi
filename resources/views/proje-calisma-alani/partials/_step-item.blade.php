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
    'stepAssignments' => [],
    'canEdit' => false
])

@php
    // 1. MEVCUT KULLANICIYI VE DURUMU AL
    $currentUser = Auth::user();
    
    // 2. MÜŞTERİ Mİ? (EN KRİTİK KONTROL BURASI)
    // Şart 1: Hiç giriş yapmamışsa (Misafir) -> Müşteridir.
    // Şart 2: Giriş yapmış AMA 'is_personnel' değeri 0 ise -> Müşteridir.
    $isCustomerView = !Auth::check() || ($currentUser && $currentUser->is_personnel == 0);

    // 3. GİZLİLİK DURUMU
    // Veritabanında 'gizli' olarak işaretlenmiş mi?
    $isHidden = $progressUpdate ? $progressUpdate->is_hidden_from_customer : false;

    // 4. YÖNETİCİ BUTONUNU GÖRME YETKİSİ
    // Sadece Personel olanlar (is_personnel=1) ve yetkisi olanlar görebilir.
    $isLeader = $iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id;
    $canManageVisibility = Auth::check() && $currentUser->is_personnel == 1 && ($isLeader || $currentUser->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi']));
    
    // 5. Müşteri Şikayeti Kaynaklı mı?
    $hasCustomer = $iaa->musteriSikayeti ? true : false;
@endphp

<div id="step-card-{{ $step->id }}" class="mb-10 ml-6" x-data="{ open: {{ $isCompleted ? 'false' : ($isCurrent ? 'true' : 'false') }} }">
    
    {{-- ZAMAN ÇİZGİSİ İKONU --}}
    <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-4 ring-white shadow-md transition-all duration-300
        {{ $isCompleted ? 'bg-gradient-to-br from-green-400 to-green-600' : ($isCurrent ? 'bg-gradient-to-br from-blue-400 to-blue-600 animate-pulse' : 'bg-gray-300') }}">
        @if ($isCompleted) 
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        @elseif ($isCurrent)
            <span class="w-3 h-3 bg-white rounded-full"></span>
        @endif
    </span>
    
    {{-- ADIM KARTI --}}
    <div class="bg-white border-2 {{ $isCurrent ? 'border-blue-300 shadow-lg' : 'border-gray-200' }} rounded-xl p-5 hover:shadow-md transition-shadow duration-300">
        
        {{-- ======================================================================== --}}
        {{-- 1. BAŞLIK ALANI (HEADER) --}}
        {{-- ======================================================================== --}}
        <div @if($isCompleted || $isCurrent) @click="open = !open" class="cursor-pointer" @else class="cursor-default" @endif>
            <h4 class="flex items-center justify-between text-base font-semibold {{ $isCurrent ? 'text-blue-700' : 'text-gray-900' }}">
                
                {{-- SOL: Adım Adı ve Gizlilik Butonu --}}
                <div class="flex items-center gap-2">
                    <span>{{ $step->order }}. {{ $step->name }}</span>

                    {{-- YÖNETİCİ BUTONU: Sadece Yetkili PERSONEL Görür --}}
                    @if($canManageVisibility && $hasCustomer)
                        <form action="{{ route('proje.step.toggleVisibility', ['iaa_id' => $iaa->id, 'step_id' => $step->id]) }}" method="POST" class="inline-block" @click.stop>
                            @csrf
                            <button type="submit" class="text-[10px] uppercase font-bold flex items-center gap-1 px-2 py-0.5 rounded border transition-all {{ $isHidden ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100' : 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100' }}" title="{{ $isHidden ? 'Müşteriden GİZLİ. Göstermek için tıkla.' : 'Müşteriye AÇIK. Gizlemek için tıkla.' }}">
                                @if($isHidden)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                    Gizli
                                @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Açık
                                @endif
                            </button>
                        </form>
                    @endif
                </div>

                {{-- SAĞ: Durum Etiketleri --}}
                <div>
                    @if($isCompleted) 
                        <span class="bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm">✓ Tamamlandı</span> 
                    @endif
                    @if($isCurrent) 
                        <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm animate-pulse">● Aktif Adım</span> 
                    @endif
                </div>
            </h4>
            
            {{-- ADIM AÇIKLAMASI --}}
            <p class="text-sm font-normal text-gray-600 mt-2">{{ $step->description }}</p>
        </div>

        {{-- ======================================================================== --}}
        {{-- 2. İÇERİK ALANI (CONTENT) - FİLTRELEME BURADA --}}
        {{-- ======================================================================== --}}
        
        {{-- SENARYO A: GİZLİ VE MÜŞTERİ (is_personnel=0) BAKIYOR (SANSÜRLÜ GÖRÜNÜM) --}}
        @if($isHidden && $isCustomerView)
            
            <div x-show="open" x-transition class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex items-center gap-3">
                    @if($isCompleted)
                        <div class="p-2 bg-green-100 text-green-600 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-gray-700 font-medium">
                            {{ $step->name }} adımı ekip tarafından tamamlanmıştır.
                        </span>
                    @else
                        <div class="p-2 bg-blue-50 text-blue-400 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-gray-600 italic">
                            {{ $step->name }} adımının ekip tarafından tamamlanması beklenmektedir.
                        </span>
                    @endif
                </div>
            </div>

        {{-- SENARYO B: NORMAL GÖRÜNÜM (AÇIK VEYA PERSONEL BAKIYOR) --}}
        @else

            {{-- 1. Atama Bilgisi (SADECE PERSONEL GÖRSÜN) --}}
            @php
                $assignmentData = $stepAssignments[$step->id] ?? null;
                $sorumluUser = $assignmentData ? \App\Models\User::find($assignmentData->user_id) : null;
                $waitingSince = $assignmentData ? \Carbon\Carbon::parse($assignmentData->updated_at)->diffForHumans() : '';
            @endphp

            @if(!$isCustomerView) 
                <div class="mt-3 mb-3 flex items-center justify-between bg-gray-50 p-2.5 rounded-lg border border-gray-200 shadow-sm">
                    {{-- ... (Mevcut Atama Bilgisi Kodları) ... --}}
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

                    @if($isLeader && !$isCompleted && $canEdit)
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
                            </div>
                        </form>
                    @endif
                </div>
            @endif

            {{-- 2. Tamamlanmış Adım İçeriği --}}
            @if($isCompleted && $progressUpdate)
                @include('proje-calisma-alani.partials._step-content-completed', [
                    'progressUpdate' => $progressUpdate,
                    'step' => $step,
                    'iaa' => $iaa 
                ])
            @endif

            {{-- 3. Aktif Adım İçeriği --}}
            @if($isCurrent)
                @if($canEdit && !$isCustomerView)
                    {{-- YETKİLİ PERSONEL: Formu Göster --}}
                        @include('proje-calisma-alani.partials._step-content-active', [
                        'iaa' => $iaa,
                        'assignment' => $assignment,
                        'currentStep' => $step, 
                        'progressUpdate' => $progressUpdate,
                        'isTeamMember' => $isTeamMember,
                        'takim' => $takim,
                        'canEdit' => true
                        ])
                @else
                    {{-- İZLEYİCİ (Yetkisiz Personel) --}}
                    @if(!$isCustomerView)
                        <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <span class="font-bold">İzleyici Modu:</span> Bu proje adımını görüntüleme yetkiniz var ancak müdahale edemezsiniz.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endif

            @endif {{-- GİZLİLİK KONTROLÜ SONU --}}

            {{-- ======================================================================== --}}
            {{-- 3. YORUMLAR (HERKESE AÇIK) - Müşteri de Personel de görür, yazar --}}
            {{-- ======================================================================== --}}

            @if($isCompleted || $isCurrent)
                <div class="mt-8 mb-4 px-6 md:px-10 border-t border-gray-100 pt-6">
                    <h5 class="text-xs font-bold text-gray-500 uppercase mb-3">İletişim & Notlar</h5>
                    @livewire('admin.proje-adim-yorumlari', [
                        'iaa' => $iaa, 
                        'step' => $step
                    ], key($step->id))
                </div>
            @endif

</div>
</div>