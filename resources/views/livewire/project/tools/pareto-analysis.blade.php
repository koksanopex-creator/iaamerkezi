<div>
    <div class="space-y-4">
        <div class="flex justify-between items-center mb-2">
            <h5 class="text-sm font-semibold text-gray-700">Pareto Analizi</h5>
            <div x-data="{ saving: false }" 
                 x-on:tool-saved.window="saving = true; setTimeout(() => saving = false, 2000)" 
                 class="text-xs text-gray-500 flex items-center h-4">
                <span x-show="saving" x-transition.opacity class="text-green-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Kaydedildi
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Veri Girişi Tablosu --}}
            <div class="lg:col-span-5 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h6 class="text-sm font-bold text-gray-800">Veri Girişi (Hata / Sıklık)</h6>
                    @if($canManage)
                        <button wire:click="addItem" class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded hover:bg-indigo-200 font-bold transition-colors shadow-sm">
                            + Yeni Satır
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori (Hata / Sorun)</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Frekans (Sayı)</th>
                                @if($canManage)
                                    <th scope="col" class="px-2 py-2 w-10"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">
                                        @if($canManage)
                                            <input type="text" wire:model.live.debounce.1000ms="items.{{ $index }}.category" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-1.5" placeholder="Hata türü">
                                        @else
                                            <div class="text-sm text-gray-900">{{ $item['category'] ?: '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($canManage)
                                            <input type="number" wire:model.live.debounce.1000ms="items.{{ $index }}.frequency" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-1.5" placeholder="0">
                                        @else
                                            <div class="text-sm text-gray-900">{{ $item['frequency'] !== '' ? $item['frequency'] : '-' }}</div>
                                        @endif
                                    </td>
                                    @if($canManage)
                                        <td class="px-2 py-2 text-right">
                                            @if(count($items) > 1)
                                                <button wire:click="removeItem({{ $index }})" class="text-red-400 hover:text-red-600 p-1 transition-colors bg-red-50 hover:bg-red-100 rounded">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(count($analyzedData) > 0)
                    <div class="mt-6 border-t border-gray-200 pt-4">
                        <h6 class="text-xs font-bold text-gray-500 uppercase mb-2">Otomatik Hesaplanan Pareto Tablosu</h6>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-1 text-left text-xs font-medium text-gray-500">Kategori</th>
                                        <th class="px-2 py-1 text-right text-xs font-medium text-gray-500">Frekans</th>
                                        <th class="px-2 py-1 text-right text-xs font-medium text-gray-500">Kümülatif %</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($analyzedData as $data)
                                        <tr class="text-xs">
                                            <td class="px-2 py-1 text-gray-700 truncate max-w-[120px]">{{ $data['category'] }}</td>
                                            <td class="px-2 py-1 text-right text-gray-900 font-medium">{{ $data['frequency'] }}</td>
                                            <td class="px-2 py-1 text-right text-orange-600 font-bold">%{{ $data['cumulative_percentage'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-gray-50 text-xs font-bold">
                                        <td class="px-2 py-1 text-gray-700">Toplam</td>
                                        <td class="px-2 py-1 text-right text-gray-900">{{ $totalFrequency }}</td>
                                        <td class="px-2 py-1 text-right text-gray-500">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Grafik Alanı --}}
            <div class="lg:col-span-7 bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col items-center justify-center min-h-[400px]">
                @if(count($analyzedData) > 0)
                    <div class="w-full min-h-[400px]" wire:ignore
                         x-data="{
                             chartInstance: null,
                             async initChart(data) {
                                 if (!data || data.length === 0) return;
                                 
                                 if (typeof ApexCharts === 'undefined') {
                                     await this.loadScript('https://cdn.jsdelivr.net/npm/apexcharts');
                                 }
                                 
                                 let categories = data.map(d => String(d.category));
                                 let frequencies = data.map(d => Number(d.frequency) || 0);
                                 let percentages = data.map(d => Number(d.cumulative_percentage) || 0);
                                 
                                 let options = {
                                     series: [
                                         { name: 'Frekans', type: 'column', data: frequencies },
                                         { name: 'Kümülatif Yüzde (%)', type: 'line', data: percentages }
                                     ],
                                     chart: { height: 400, type: 'line', toolbar: { show: false } },
                                     stroke: { width: [0, 4] },
                                     title: { text: 'Pareto Analizi', align: 'center', style: { color: '#374151' } },
                                     dataLabels: { enabled: true, enabledOnSeries: [1] },
                                     labels: categories,
                                     xaxis: { type: 'category' },
                                     yaxis: [
                                         { title: { text: 'Frekans' } },
                                         { opposite: true, title: { text: 'Kümülatif Yüzde (%)' }, max: 100, min: 0 }
                                     ],
                                     colors: ['#4f46e5', '#f97316'],
                                     legend: { position: 'bottom' }
                                 };

                                 if (this.chartInstance) {
                                     this.chartInstance.destroy();
                                 }
                                 
                                 this.chartInstance = new ApexCharts(this.$el, options);
                                 this.chartInstance.render();
                             },
                             loadScript(src) {
                                 return new Promise((resolve, reject) => {
                                     const script = document.createElement('script');
                                     script.src = src;
                                     script.onload = resolve;
                                     script.onerror = reject;
                                     document.head.appendChild(script);
                                 });
                             }
                         }" 
                         x-init="$nextTick(() => { initChart(@js($analyzedData)) })"
                         @pareto-data-updated-{{ $tool->id }}.window="initChart($event.detail[0])"
                    ></div>
                @else
                    <div class="text-center text-gray-500 flex flex-col items-center">
                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <p class="text-sm font-medium">Pareto grafiğinin çizilebilmesi için tabloya verileri girin.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
