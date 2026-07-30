<div>
    <div class="space-y-4">
        <div class="flex justify-between items-center mb-2">
            <h5 class="text-sm font-semibold text-gray-700">SWOT Analizi Aracı</h5>
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
            {{-- Strengths (Güçlü Yönler) --}}
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 shadow-sm">
                <h6 class="text-sm font-bold text-blue-800 mb-2 flex items-center">
                    <span class="bg-blue-200 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center mr-2 font-black">S</span>
                    Güçlü Yönler (Strengths)
                </h6>
                @if($canManage)
                    <textarea wire:model.live.debounce.1000ms="strengths" rows="4" class="w-full text-sm rounded-md border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white" placeholder="Her satıra bir madde yazın..."></textarea>
                @else
                    <div class="text-sm text-gray-700 whitespace-pre-wrap min-h-[100px] bg-white p-3 rounded border border-blue-100">{{ $strengths ?: 'Veri girilmemiş.' }}</div>
                @endif
            </div>

            {{-- Weaknesses (Zayıf Yönler) --}}
            <div class="bg-red-50 p-4 rounded-lg border border-red-100 shadow-sm">
                <h6 class="text-sm font-bold text-red-800 mb-2 flex items-center">
                    <span class="bg-red-200 text-red-800 rounded-full w-6 h-6 flex items-center justify-center mr-2 font-black">W</span>
                    Zayıf Yönler (Weaknesses)
                </h6>
                @if($canManage)
                    <textarea wire:model.live.debounce.1000ms="weaknesses" rows="4" class="w-full text-sm rounded-md border-red-200 shadow-sm focus:border-red-500 focus:ring-red-500 bg-white" placeholder="Her satıra bir madde yazın..."></textarea>
                @else
                    <div class="text-sm text-gray-700 whitespace-pre-wrap min-h-[100px] bg-white p-3 rounded border border-red-100">{{ $weaknesses ?: 'Veri girilmemiş.' }}</div>
                @endif
            </div>

            {{-- Opportunities (Fırsatlar) --}}
            <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-100 shadow-sm">
                <h6 class="text-sm font-bold text-emerald-800 mb-2 flex items-center">
                    <span class="bg-emerald-200 text-emerald-800 rounded-full w-6 h-6 flex items-center justify-center mr-2 font-black">O</span>
                    Fırsatlar (Opportunities)
                </h6>
                @if($canManage)
                    <textarea wire:model.live.debounce.1000ms="opportunities" rows="4" class="w-full text-sm rounded-md border-emerald-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white" placeholder="Her satıra bir madde yazın..."></textarea>
                @else
                    <div class="text-sm text-gray-700 whitespace-pre-wrap min-h-[100px] bg-white p-3 rounded border border-emerald-100">{{ $opportunities ?: 'Veri girilmemiş.' }}</div>
                @endif
            </div>

            {{-- Threats (Tehditler) --}}
            <div class="bg-amber-50 p-4 rounded-lg border border-amber-100 shadow-sm">
                <h6 class="text-sm font-bold text-amber-800 mb-2 flex items-center">
                    <span class="bg-amber-200 text-amber-800 rounded-full w-6 h-6 flex items-center justify-center mr-2 font-black">T</span>
                    Tehditler (Threats)
                </h6>
                @if($canManage)
                    <textarea wire:model.live.debounce.1000ms="threats" rows="4" class="w-full text-sm rounded-md border-amber-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white" placeholder="Her satıra bir madde yazın..."></textarea>
                @else
                    <div class="text-sm text-gray-700 whitespace-pre-wrap min-h-[100px] bg-white p-3 rounded border border-amber-100">{{ $threats ?: 'Veri girilmemiş.' }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
