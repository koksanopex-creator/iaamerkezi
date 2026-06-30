{{-- SWOT Analizi Widget --}}
<div>
    <h4 class="text-lg font-semibold text-gray-800 mb-1">{{ $config['title'] ?? 'SWOT Analizi' }}</h4>
    <p class="text-sm text-gray-500 mb-4">Güçlü ve zayıf yönleri, fırsatları ve tehditleri değerlendirin.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Güçlü Yönler --}}
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <span
                    class="w-8 h-8 rounded-lg bg-green-500 text-white flex items-center justify-center text-sm font-black shadow-sm">S</span>
                <label class="text-sm font-bold text-green-700">Güçlü Yönler (Strengths)</label>
            </div>
            <textarea wire:model="toolsData.swot.strengths" rows="4"
                class="block w-full rounded-lg border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm bg-white"
                placeholder="Her satıra bir madde yazın..."></textarea>
        </div>

        {{-- Zayıf Yönler --}}
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <span
                    class="w-8 h-8 rounded-lg bg-red-500 text-white flex items-center justify-center text-sm font-black shadow-sm">W</span>
                <label class="text-sm font-bold text-red-700">Zayıf Yönler (Weaknesses)</label>
            </div>
            <textarea wire:model="toolsData.swot.weaknesses" rows="4"
                class="block w-full rounded-lg border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm bg-white"
                placeholder="Her satıra bir madde yazın..."></textarea>
        </div>

        {{-- Fırsatlar --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <span
                    class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center text-sm font-black shadow-sm">O</span>
                <label class="text-sm font-bold text-blue-700">Fırsatlar (Opportunities)</label>
            </div>
            <textarea wire:model="toolsData.swot.opportunities" rows="4"
                class="block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm bg-white"
                placeholder="Her satıra bir madde yazın..."></textarea>
        </div>

        {{-- Tehditler --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <span
                    class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-sm font-black shadow-sm">T</span>
                <label class="text-sm font-bold text-amber-700">Tehditler (Threats)</label>
            </div>
            <textarea wire:model="toolsData.swot.threats" rows="4"
                class="block w-full rounded-lg border-amber-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm bg-white"
                placeholder="Her satıra bir madde yazın..."></textarea>
        </div>
    </div>
</div>