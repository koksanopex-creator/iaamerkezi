<div>
    <div class="space-y-4">
        <div class="flex justify-between items-center mb-2">
            <h5 class="text-sm font-semibold text-gray-700">4M Gelişim Raporu</h5>
            <div x-data="{ saving: false }" 
                 x-on:tool-saved.window="saving = true; setTimeout(() => saving = false, 2000)" 
                 class="text-xs text-gray-500 flex items-center h-4">
                <span x-show="saving" x-transition.opacity class="text-green-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Kaydedildi
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- İnsan (Man) --}}
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h6 class="text-sm font-bold text-blue-800 mb-2">İnsan (Man)</h6>
                @if($canManage)
                    <textarea wire:model.live.debounce.1000ms="items.man" rows="4" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white" placeholder="Eğitim, tecrübe, yetkinlik, motivasyon durumları..."></textarea>
                @else
                    <div class="w-full text-sm bg-white border border-gray-200 rounded-md p-3 text-gray-700 min-h-[100px] whitespace-pre-wrap">{{ $items['man'] ?: 'Veri girilmemiş.' }}</div>
                @endif
            </div>

            {{-- Makine (Machine) --}}
            <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                <h6 class="text-sm font-bold text-emerald-800 mb-2">Makine (Machine)</h6>
                @if($canManage)
                    <textarea wire:model.live.debounce.1000ms="items.machine" rows="4" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white" placeholder="Ekipman durumu, bakım geçmişi, teknolojik yeterlilik..."></textarea>
                @else
                    <div class="w-full text-sm bg-white border border-gray-200 rounded-md p-3 text-gray-700 min-h-[100px] whitespace-pre-wrap">{{ $items['machine'] ?: 'Veri girilmemiş.' }}</div>
                @endif
            </div>

            {{-- Malzeme (Material) --}}
            <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                <h6 class="text-sm font-bold text-amber-800 mb-2">Malzeme (Material)</h6>
                @if($canManage)
                    <textarea wire:model.live.debounce.1000ms="items.material" rows="4" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white" placeholder="Hammadde, sarf malzemeleri, parça kalitesi ve tedarik durumu..."></textarea>
                @else
                    <div class="w-full text-sm bg-white border border-gray-200 rounded-md p-3 text-gray-700 min-h-[100px] whitespace-pre-wrap">{{ $items['material'] ?: 'Veri girilmemiş.' }}</div>
                @endif
            </div>

            {{-- Metot (Method) --}}
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <h6 class="text-sm font-bold text-purple-800 mb-2">Metot (Method)</h6>
                @if($canManage)
                    <textarea wire:model.live.debounce.1000ms="items.method" rows="4" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 bg-white" placeholder="Süreç adımları, talimatlar, kalite kontrol yöntemleri ve prosedürler..."></textarea>
                @else
                    <div class="w-full text-sm bg-white border border-gray-200 rounded-md p-3 text-gray-700 min-h-[100px] whitespace-pre-wrap">{{ $items['method'] ?: 'Veri girilmemiş.' }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
