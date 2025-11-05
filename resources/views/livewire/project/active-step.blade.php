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

        {{-- Kaydet ve İptal Butonları --}}
        <div class="mt-8 flex justify-end space-x-4 border-t pt-6">
            <button type="button"
                    wire:click.prevent="cancel"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center">
                İptal
            </button>

            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <div wire:loading wire:target="save" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                Adımı Tamamla ve Kaydet
            </button>
        </div>
    </form>
</div>

{{-- Genel Grafik Alpine JS component'ini yükle (sadece bir kere) --}}
@include('livewire.project.widgets._generic-chart-script')

