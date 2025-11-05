{{-- resources/views/livewire/project/widgets/_bar-chart.blade.php --}}
@props(['index', 'config'])

@php
  $rootId = $this->getId();                      // <-- EKLENDİ
  $chartComponentId = $rootId . '-' . $index;    // <-- GÜNCELLENDİ
@endphp

<div x-data="genericChartComponent('{{ $chartComponentId }}', 'bar', '{{ $rootId }}')" x-init="initChart()">
    {{-- Grafik Başlığı ve Eksen Ayarları --}}
    <h4 class="text-lg font-semibold text-gray-800 mb-1">
        {{-- DÜZELTME: wire:model toolsData'ya bağlandı --}}
        <input type="text" wire:model="toolsData.bar_chart_data.{{ $index }}.title" placeholder="Grafik Başlığı Girin" class="text-lg font-semibold text-gray-800 border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-indigo-500 p-0 w-full">
         @if($config['required'] ?? false) <span class="text-red-500">*</span> @endif
    </h4>
     <div class="grid grid-cols-2 gap-4 mb-4 text-sm mt-2">
         <div>
            <label class="text-xs font-medium text-gray-500">X Ekseni Başlığı</label>
            {{-- DÜZELTME: wire:model toolsData'ya bağlandı --}}
            <input type="text" wire:model="toolsData.bar_chart_data.{{ $index }}.axis_x_label" placeholder="Örn: Aylar, Kategoriler" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
         <div>
            <label class="text-xs font-medium text-gray-500">Y Ekseni Başlığı</label>
            {{-- DÜZELTME: wire:model toolsData'ya bağlandı --}}
            <input type="text" wire:model="toolsData.bar_chart_data.{{ $index }}.axis_y_label" placeholder="Örn: Miktar, Adet" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
    </div>

    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-6">
        <div>
            <div class="flex justify-between items-center mb-2">
                {{-- Başlık toolsData'dan okunacak --}}
                <h5 class="font-semibold text-gray-700">{{ $toolsData['bar_chart_data'][$index]['title'] ?? $config['title'] ?? 'Grafik Önizleme' }}</h5>
                <button type="button" wire:click="generateBarChartData({{ $index }})" class="bg-indigo-600 text-white font-semibold py-1 px-3 rounded-md text-sm hover:bg-indigo-700">Grafiği Oluştur/Güncelle</button>
            </div>
            <div wire:ignore class="bg-white p-2 rounded-lg border" style="height: 300px;">
                <canvas id="genericChart-{{ $chartComponentId }}"></canvas>
            </div>
        </div>
        <div>
            <h5 class="font-semibold text-gray-700 mb-2">Veri Girişi</h5>
            <div class="overflow-x-auto bg-white rounded-lg border">
                <table class="min-w-full"><thead class="bg-gray-100"><tr>
                <th class="p-2 text-left text-sm font-semibold text-gray-600">Etiket (X Ekseni)</th>
                <th class="p-2 text-left text-sm font-semibold text-gray-600">Değer (Y Ekseni)</th>
                <th class="w-16"></th></tr></thead>
                <tbody>
                    @foreach($toolsData['bar_chart_data'][$index]['rows'] ?? [['label'=>'', 'value'=>'']] as $rowIndex => $row)
                    <tr class="border-b" wire:key="bar-chart-row-{{ $index }}-{{ $rowIndex }}">
                        {{-- wire:model doğru yere bağlanmıştı --}}
                        <td class="p-1"><input type="text" wire:model="toolsData.bar_chart_data.{{ $index }}.rows.{{ $rowIndex }}.label" class="w-full text-sm border-gray-300 rounded-md"></td>
                        <td class="p-1"><input type="number" step="any" wire:model="toolsData.bar_chart_data.{{ $index }}.rows.{{ $rowIndex }}.value" class="w-full text-sm border-gray-300 rounded-md"></td>
                        <td class="p-1 text-center">
                            @if(count($toolsData['bar_chart_data'][$index]['rows']) > 1)
                            <button type="button" wire:click="removeBarChartRow({{ $index }}, {{ $rowIndex }})" class="text-red-500 hover:text-red-700 p-1 rounded-full font-bold text-lg">&times;</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            <button type="button" wire:click="addBarChartRow({{ $index }})" class="mt-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Satır Ekle</button>
        </div>
    </div>
</div>

