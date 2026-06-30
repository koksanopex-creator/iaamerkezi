<div class="bg-indigo-50/30 p-4 rounded-xl border border-indigo-100 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <div class="p-2 bg-indigo-600 rounded-lg text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <h4 class="text-lg font-bold text-indigo-900">{{ $config['title'] ?? '4M Gelişim Raporu' }}</h4>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- İNSAN (Man) --}}
        <div class="bg-white p-4 rounded-lg border border-indigo-200 shadow-sm">
            <label class="block text-sm font-bold text-indigo-700 mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                İNSAN (Man)
            </label>
            <textarea wire:model.blur="toolsData.4m_report.{{ $index }}.man" 
                      rows="4" 
                      class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="Personel yetkinliği, eğitim, motivasyon vb. faktörleri buraya yazın..."></textarea>
        </div>

        {{-- MAKİNE (Machine) --}}
        <div class="bg-white p-4 rounded-lg border border-indigo-200 shadow-sm">
            <label class="block text-sm font-bold text-indigo-700 mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                MAKİNE (Machine)
            </label>
            <textarea wire:model.blur="toolsData.4m_report.{{ $index }}.machine" 
                      rows="4" 
                      class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="Ekipman durumu, bakım, teknoloji, araçlar vb. faktörleri buraya yazın..."></textarea>
        </div>

        {{-- MALZEME (Material) --}}
        <div class="bg-white p-4 rounded-lg border border-indigo-200 shadow-sm">
            <label class="block text-sm font-bold text-indigo-700 mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                MALZEME (Material)
            </label>
            <textarea wire:model.blur="toolsData.4m_report.{{ $index }}.material" 
                      rows="4" 
                      class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="Hammadde kalitesi, tedarik, sarf malzemeleri vb. faktörleri buraya yazın..."></textarea>
        </div>

        {{-- METOT (Method) --}}
        <div class="bg-white p-4 rounded-lg border border-indigo-200 shadow-sm">
            <label class="block text-sm font-bold text-indigo-700 mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                METOT (Method)
            </label>
            <textarea wire:model.blur="toolsData.4m_report.{{ $index }}.method" 
                      rows="4" 
                      class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="İşlem adımları, standartlar, iş akışı, talimatlar vb. faktörleri buraya yazın..."></textarea>
        </div>
    </div>
</div>
