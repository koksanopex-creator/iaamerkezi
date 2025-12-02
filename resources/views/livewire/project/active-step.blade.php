<div class="bg-white p-6 rounded-lg shadow-lg border-2 border-blue-300">
    <h3 class="text-xl font-bold text-blue-700 mb-2">Aktif Adım: {{ $currentStep->name }}</h3>
    <p class="text-gray-600 mb-6">{{ $currentStep->description }}</p>

    {{-- Hata mesajları için alan --}}
     @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg">
            {{ session('error') }}
        </div>
    @endif
    <div x-data="{ errorMessage: '' }" @show-error.window="errorMessage = $event.detail; setTimeout(() => errorMessage = '', 5000)">
        <template x-if="errorMessage">
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg" x-text="errorMessage"></div>
        </template>
    </div>


    <form wire:submit="save" id="main-form">

        <div class="space-y-6">
            @if(empty($currentStep->widgets))
                <div class="p-4 text-center bg-gray-50 rounded-lg border">
                    <p class="text-gray-500">Bu adım için herhangi bir form elemanı (widget) tanımlanmamış.</p>
                </div>
            @else
                @foreach($currentStep->widgets as $index => $widget)
                    @php
                        // Widget config'ini ve index'i partial'a göndermek için hazırla
                        $widgetData = [
                            'index' => $index,
                            'config' => $widget['config'] ?? []
                        ];
                    @endphp

                    <div class="border-t border-gray-200 pt-6">
                        @switch($widget['type'])
                            @case('textbox')
                                @include('livewire.project.widgets._textbox', $widgetData)
                                @break
                            @case('five_whys')
                                @include('livewire.project.widgets._five-whys', $widgetData)
                                @break
                            @case('fishbone')
                                @include('livewire.project.widgets._fishbone', $widgetData)
                                @break
                            @case('pareto')
                                {{-- Pareto için ilk veriyi global değişkene yaz --}}
                                @php $paretoId = $this->getId(); @endphp
                                <script> window['initialParetoData_{{ $paretoId }}'] = @json($initialChartData['pareto'][$paretoId] ?? null); </script>

                                @include('livewire.project.widgets._pareto', $widgetData)
                                @break
                            @case('user_select')
                                @include('livewire.project.widgets._user-select', $widgetData)
                                @break
                            @case('date_picker')
                                @include('livewire.project.widgets._date-picker', $widgetData)
                                @break
                            @case('file_upload')
                                @include('livewire.project.widgets._file-upload', $widgetData)
                                @break
                            @case('info_text')
                                @include('livewire.project.widgets._info-text', $widgetData)
                                @break

                            {{-- === YENİ GRAFİK CASE'LERİ === --}}
                            @case('bar_chart')
                                {{-- Sütun grafiği için ilk veriyi global değişkene yaz --}}
                                @php $chartId = $this->getId() . '-' . $index; @endphp
                                {{-- $initialChartData PHP component'inden geliyor --}}
                                <script> window['initialChartData_{{ $chartId }}'] = @json($initialChartData['bar_chart'][$chartId] ?? null); </script>

                                @include('livewire.project.widgets._bar-chart', $widgetData)
                                @break
                            @case('line_chart')
                                {{-- Çizgi grafiği için ilk veriyi global değişkene yaz --}}
                                @php $chartId = $this->getId() . '-' . $index; @endphp
                                <script> window['initialChartData_{{ $chartId }}'] = @json($initialChartData['line_chart'][$chartId] ?? null); </script>
                                @include('livewire.project.widgets._line-chart', $widgetData)
                                @break
                            {{-- ============================== --}}

                            @default
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                                    <strong>Hata:</strong> Tanımsız widget türü: '{{ $widget['type'] }}'
                                </div>
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div>

        {{-- ANA FORMUN BUTON ALANI --}}
        <div class="mt-8 flex justify-end items-center gap-3 border-t pt-6">
            
            {{-- Bu buton sadece GÖRSEL amaçlıdır, tıklandığında aşağıdaki gizli formu tetikler --}}
            @if(isset($progressUpdate) && $progressUpdate && $progressUpdate->id)
                <button type="button" 
                        onclick="if(confirm('Değişiklikleri iptal edip adımı tekrar kapatmak istiyor musunuz?')) { document.getElementById('form-vazgec-{{ $progressUpdate->id }}').submit(); }"
                        class="bg-red-50 border border-red-200 text-red-700 hover:bg-red-100 hover:border-red-300 py-2 px-4 rounded-md shadow-sm text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Vazgeç (Kapat)
                </button>
            @else
                {{-- Normal İptal --}}
                 <button type="button" wire:click.prevent="cancel" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    İptal
                </button>
            @endif

            {{-- === BUTON KİLİTLEME MANTIĞI === --}}
            @php
                // Bu adım için bir atama var mı diye kontrol et
                $assignmentRecord = \Illuminate\Support\Facades\DB::table('iaa_step_assignments')
                    ->where('iaa_id', $iaa instanceof \App\Models\Iaa ? $iaa->id : $iaa['id'])
                    ->where('iaa_workflow_step_id', $currentStep->id)
                    ->first();
                
                $isLockedForUser = false;
                $assigneeName = '';

                if ($assignmentRecord) {
                    $userId = auth()->id();
                    // Atanan kişi ben değilsem
                    if ($assignmentRecord->user_id != $userId) {
                         // Lider miyim? (Lider her şeyi yapabilir)
                         $liderId = $iaa->atananTakim->lider_user_id ?? 0;
                         $isAdmin = auth()->user()->hasRole('Superadmin');
                         
                         if ($userId != $liderId && !$isAdmin) {
                             $isLockedForUser = true;
                             // Atanan kişinin ismini bul (Uyarı için)
                             $assigneeName = \App\Models\User::find($assignmentRecord->user_id)->name ?? 'Başkası';
                         }
                    }
                }
            @endphp

            {{-- KAYDET BUTONU --}}
            <button type="submit"
                    @if($isLockedForUser) disabled @endif
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-md transition-colors
                    {{ $isLockedForUser ? 'bg-gray-400 cursor-not-allowed hover:bg-gray-400' : 'bg-indigo-600 hover:bg-indigo-700' }}"
                    @if($isLockedForUser) title="Bu adım {{ $assigneeName }} sorumluluğundadır." @endif>
                
                @if(!$isLockedForUser)
                    <div wire:loading wire:target="save" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                @endif

                {{ $isLockedForUser ? ($assigneeName . ' Bekleniyor') : 'Adımı Tamamla ve Kaydet' }}
            </button>
        </div>
    </form> 
    {{-- DİKKAT: </form> BURADA BİTTİ. VAZGEÇ FORMU BUNUN ALTINDA OLMALI --}}


    {{-- === GİZLİ VAZGEÇME FORMU (BAĞIMSIZ) === --}}
    @if(isset($progressUpdate) && $progressUpdate && $progressUpdate->id)
        <form id="form-vazgec-{{ $progressUpdate->id }}" 
              action="{{ route('proje.workspace.cancelReopenStep', ['id' => $progressUpdate->id]) }}" 
              method="POST" 
              style="display: none;">
            @csrf
        </form>
    @endif

</div>

{{-- Grafik Scriptleri --}}
@include('livewire.project.widgets._generic-chart-script')



