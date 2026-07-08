<div>
    @php
        // Bu adım için bir atama var mı diye kontrol et
        $assignmentRecord = \Illuminate\Support\Facades\DB::table('iaa_step_assignments')
            ->where('iaa_id', $iaa instanceof \App\Models\Iaa ? $iaa->id : $iaa['id'])
            ->where('iaa_workflow_step_id', $currentStep['id'])
            ->first();
        
        $userId = auth()->id();
        $liderId = (is_object($iaa) && isset($iaa->atananTakim)) ? $iaa->atananTakim->lider_user_id : ((is_array($iaa) && isset($iaa['atanan_takim']['lider_user_id'])) ? $iaa['atanan_takim']['lider_user_id'] : 0);
        $isAdmin = auth()->user()->hasRole('Superadmin');
        
        $isSquadMember = false;
        if (is_object($iaa)) {
            $isSquadMember = $iaa->projeEkibi()->where('user_id', $userId)->wherePivot('durum', 'onaylandi')->exists();
        } elseif (is_array($iaa) && isset($iaa['id'])) {
            $isSquadMember = \Illuminate\Support\Facades\DB::table('iaa_user')
                ->where('iaa_id', $iaa['id'])
                ->where('user_id', $userId)
                ->where('durum', 'onaylandi')
                ->exists();
        }
        
        // Bu kullanıcının ID'si formData içindeki user_ids alanında var mı?
        $isAssignedViaWidget = false;
        if (isset($formData) && is_array($formData)) {
            foreach ($formData as $index => $data) {
                if (isset($data['user_ids']) && is_array($data['user_ids']) && in_array($userId, $data['user_ids'])) {
                    $isAssignedViaWidget = true;
                    break;
                }
            }
        }

        $isLockedForUser = false;
        $isLockedForUserSelectWidget = false;
        $assigneeName = '';

        if ($assignmentRecord) {
            // Atanan kişi ben değilsem
            if ($assignmentRecord->user_id != $userId) {
                 if ($userId != $liderId && !$isAdmin && !$isAssignedViaWidget) {
                     $isLockedForUser = true;
                     $assigneeName = \App\Models\User::find($assignmentRecord->user_id)->name ?? 'Başkası';
                 }
            }
        }
        
        // Sadece Admin, Lider veya Squad Üyeleri 'user_select' (kullanıcı seçimi) widget'ını düzenleyebilir.
        // Diğerleri (örneğin sadece widget üzerinden yetki alanlar) bu alanı görebilir ama değiştiremez.
        if (!$isAdmin && $userId != $liderId && !$isSquadMember) {
            $isLockedForUserSelectWidget = true;
        }
        // Eğer tüm form kilitliyse, tabii ki user_select de kilitlidir.
        if ($isLockedForUser) {
            $isLockedForUserSelectWidget = true;
        }
    @endphp

    <div class="bg-white p-6 rounded-lg shadow-lg border-2 border-blue-300">
        <h3 class="text-xl font-bold text-blue-700 mb-2">Aktif Adım: {{ $currentStep['name'] }}</h3>
        <p class="text-gray-600 mb-6">{{ $currentStep['description'] }}</p>

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
                @if(empty($currentStep['widgets']))
                    <div class="p-4 text-center bg-gray-50 rounded-lg border">
                        <p class="text-gray-500">Bu adım için herhangi bir form elemanı (widget) tanımlanmamış.</p>
                    </div>
                @else
                    @foreach($currentStep['widgets'] as $index => $widget)
                        @php
                            // Widget config'ini ve index'i partial'a göndermek için hazırla
                            $widgetData = [
                                'index' => $index,
                                'config' => $widget['config'] ?? [],
                                'isLockedForUser' => ($widget['type'] === 'user_select') ? $isLockedForUserSelectWidget : $isLockedForUser
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
                                @case('user_select_info')
                                    @php $widgetData['isLockedForUser'] = $isLockedForUser; @endphp
                                    @include('livewire.project.widgets._user-select-info', $widgetData)
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

                                {{-- === YENİ ANALİZ ARAÇLARI === --}}
                                @case('swot')
                                    @include('livewire.project.widgets._swot', $widgetData)
                                    @break
                                @case('checklist')
                                    @include('livewire.project.widgets._checklist', $widgetData)
                                    @break
                                @case('before_after')
                                    @include('livewire.project.widgets._before-after', $widgetData)
                                    @break
                                @case('risk_matrix')
                                    @include('livewire.project.widgets._risk-matrix', $widgetData)
                                    @break
                                @case('image_upload')
                                    @include('livewire.project.widgets._image-upload', $widgetData)
                                    @break
                                @case('action_list')
                                    @include('livewire.project.widgets._action-list', $widgetData)
                                    @break
                                @case('task_list')
                                    @include('livewire.project.widgets._task-list', $widgetData)
                                    @break
                                @case('prioritization_matrix')
                                    @include('livewire.project.widgets._prioritization-matrix', $widgetData)
                                    @break
                                @case('4m_report')
                                    @include('livewire.project.widgets._4m-report', $widgetData)
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

                @php
                    $pendingVisitLocked = $this->hasPendingVisit();
                    $isButtonDisabled = $isLockedForUser || $pendingVisitLocked;
                    
                    $buttonTitle = "";
                    if ($pendingVisitLocked) {
                        $buttonTitle = "Bu adımda planlanmış ve henüz sonuçlandırılmamış bir ziyaret bulunmaktadır.";
                    } elseif ($isLockedForUser) {
                        $buttonTitle = "Bu adım $assigneeName sorumluluğundadır.";
                    }
                    
                    $buttonClass = $isButtonDisabled 
                        ? 'bg-gray-400 cursor-not-allowed hover:bg-gray-400' 
                        : 'bg-indigo-600 hover:bg-indigo-700';
                        
                    $buttonText = 'Adımı Tamamla ve Kaydet';
                    if ($pendingVisitLocked) {
                        $buttonText = 'Ziyaret Sonucu Bekleniyor';
                    } elseif ($isLockedForUser) {
                        $buttonText = $assigneeName . ' Bekleniyor';
                    }
                @endphp

                {{-- KAYDET BUTONU --}}
                <button type="submit"
                        @if($isButtonDisabled) disabled @endif
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-md transition-colors {{ $buttonClass }}"
                        @if($isButtonDisabled) title="{{ $buttonTitle }}" @endif>
                    
                    @if(!$isButtonDisabled)
                        <div wire:loading wire:target="save" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    @else
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    @endif

                    {{ $buttonText }}
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

    {{-- Bildirim Modal'ı --}}
    @if($isNotificationModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeNotificationModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Bildirim Taslağı</h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-2">Aşağıdaki metin, seçtiğiniz kişilere ve onların yöneticilerine (CC olarak) gönderilecektir:</p>
                                    <div class="bg-gray-50 p-3 rounded text-sm text-gray-700 whitespace-pre-wrap border">{{ $notificationDraft }}</div>
                                    
                                    <div class="mt-4">
                                        <label for="notificationNotes" class="block text-sm font-medium text-gray-700">Eklemek İstediğiniz Notlar (Opsiyonel)</label>
                                        <textarea wire:model="notificationNotes" id="notificationNotes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Ekstra belirtmek istediğiniz bir detay varsa buraya yazın..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="sendUserSelectNotification" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <div wire:loading wire:target="sendUserSelectNotification" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            Gönder
                        </button>
                        <button type="button" wire:click="closeNotificationModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            İptal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Grafik Scriptleri --}}
    @include('livewire.project.widgets._generic-chart-script')
</div>
