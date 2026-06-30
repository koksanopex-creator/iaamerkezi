<div>
    {{-- ÜST BİLGİ KARTI --}}
    <div class="mb-8 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-2xl p-6 shadow-lg text-white">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black">{{ $workflow->name }}</h1>
                <p class="text-indigo-100 text-sm mt-1">{{ $workflow->description }}</p>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <p class="text-3xl font-black">{{ $steps->count() }}</p>
                    <p class="text-xs text-indigo-200 uppercase font-semibold tracking-wider">Toplam Adım</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-black">{{ $steps->sum('default_duration_days') }}</p>
                    <p class="text-xs text-indigo-200 uppercase font-semibold tracking-wider">Toplam Gün</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ═══════════════════════════════════════════ --}}
        {{-- SOL: ADIM LİSTESİ (TİMELİNE) --}}
        {{-- ═══════════════════════════════════════════ --}}
        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        Tanımlanmış Adımlar
                    </h3>
                    <span class="text-xs text-slate-400 font-medium">Sürükle-bırak ile sıralayabilirsiniz</span>
                </div>

                @if(session()->has('success'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2"
                        role="alert">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                {{-- TIMELINE LİSTESİ --}}
                <div class="relative" wire:sortable="updateStepOrder" wire:sortable-group="steps">
                    {{-- Timeline Çizgisi --}}
                    @if($steps->count() > 1)
                        <div
                            class="absolute left-[22px] top-6 bottom-6 w-0.5 bg-gradient-to-b from-indigo-300 via-blue-200 to-slate-200 rounded-full">
                        </div>
                    @endif

                    @forelse($steps as $step)
                        <div wire:sortable.item="{{ $step->id }}" wire:key="step-{{ $step->id }}"
                            wire:sortable-group.item="steps" class="relative pl-14 pb-6 last:pb-0 group">

                            {{-- Timeline Nokta --}}
                            <div class="absolute left-2.5 top-1 z-10">
                                <div
                                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white text-sm font-black shadow-md group-hover:shadow-indigo-300 transition-shadow">
                                    {{ $step->order }}
                                </div>
                            </div>

                            {{-- Adım Kartı --}}
                            <div wire:sortable.handle wire:sortable-group.handle
                                class="bg-white border-2 border-slate-100 rounded-xl p-5 cursor-grab hover:border-indigo-200 hover:shadow-lg transition-all duration-300 group-hover:bg-gradient-to-r group-hover:from-white group-hover:to-indigo-50/30">

                                <div class="flex justify-between items-start">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="text-base font-bold text-slate-800">{{ $step->name }}</h4>
                                        </div>
                                        <p class="text-sm text-slate-500 leading-relaxed">{{ $step->description }}</p>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 flex-shrink-0 ml-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="editStep({{ $step->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Düzenle
                                        </button>
                                        <button wire:click="deleteStep({{ $step->id }})"
                                            wire:confirm="Bu adımı silmek istediğinizden emin misiniz?"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Sil
                                        </button>
                                    </div>
                                </div>

                                {{-- Alt Bilgi: Süre + Widgetlar --}}
                                <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-3">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $step->default_duration_days }} gün
                                    </span>

                                    @if(!empty($step->widgets))
                                        <span class="text-slate-300">|</span>
                                        @foreach($step->widgets as $widget)
                                            @php
                                                $widgetColors = [
                                                    'textbox' => 'bg-blue-50 text-blue-700',
                                                    'fishbone' => 'bg-amber-50 text-amber-700',
                                                    'pareto' => 'bg-purple-50 text-purple-700',
                                                    'five_whys' => 'bg-rose-50 text-rose-700',
                                                    'file_upload' => 'bg-teal-50 text-teal-700',
                                                    'date_picker' => 'bg-cyan-50 text-cyan-700',
                                                    'info_text' => 'bg-slate-100 text-slate-600',
                                                    'user_select' => 'bg-green-50 text-green-700',
                                                    'bar_chart' => 'bg-orange-50 text-orange-700',
                                                    'line_chart' => 'bg-orange-50 text-orange-700',
                                                    'swot' => 'bg-emerald-50 text-emerald-700',
                                                    'checklist' => 'bg-lime-50 text-lime-700',
                                                    'before_after' => 'bg-sky-50 text-sky-700',
                                                    'risk_matrix' => 'bg-pink-50 text-pink-700',
                                                    '4m_report' => 'bg-indigo-100 text-indigo-800',
                                                ];
                                                $colorClass = $widgetColors[$widget['type']] ?? 'bg-indigo-50 text-indigo-700';

                                                $widgetIcons = [
                                                    'textbox' => 'M4 6h16M4 12h16M4 18h7',
                                                    'fishbone' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                                                    'pareto' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                                                    'five_whys' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                                    'file_upload' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
                                                    'date_picker' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                                    'info_text' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                                    'user_select' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                                    'bar_chart' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                                                    'line_chart' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4v16',
                                                    'swot' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z',
                                                    'checklist' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                                                    'before_after' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                                                    'risk_matrix' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                                                    '4m_report' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                                ];
                                                $iconPath = $widgetIcons[$widget['type']] ?? 'M4 6h16M4 10h16M4 14h16M4 18h16';
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-md {{ $colorClass }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="{{ $iconPath }}" />
                                                </svg>
                                                {{ $availableWidgets[$widget['type']]['label'] ?? $widget['type'] }}
                                                @if(!empty($widget['config']['title']))
                                                    : {{ Str::limit($widget['config']['title'], 12) }}
                                                @endif
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">Bu şablon için henüz hiç adım tanımlanmamış.</p>
                            <p class="text-slate-400 text-sm mt-1">Sağdaki formdan ilk adımınızı ekleyin.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════ --}}
        {{-- SAĞ: YENİ ADIM EKLEME FORMU --}}
        {{-- ═══════════════════════════════════════════ --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden sticky top-6">
                {{-- Form Başlık --}}
                <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Yeni Adım Ekle
                    </h3>
                </div>

                <form wire:submit="addStep" class="p-6">
                    <div class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Adım Adı</label>
                            <input type="text" wire:model="name" id="name"
                                class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm placeholder:text-slate-400"
                                placeholder="Örn: Problemin Analizi" required>
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="description"
                                class="block text-sm font-semibold text-slate-700 mb-1.5">Açıklama</label>
                            <textarea wire:model="description" id="description" rows="3"
                                class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm placeholder:text-slate-400"
                                placeholder="Bu adımda neler yapılacak?"></textarea>
                        </div>
                        <div>
                            <label for="default_duration_days"
                                class="block text-sm font-semibold text-slate-700 mb-1.5">Varsayılan Süre (Gün)</label>
                            <input type="number" wire:model="default_duration_days" id="default_duration_days"
                                class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required min="1">
                        </div>

                        {{-- Widget Yönetimi --}}
                        <div class="border-t border-slate-100 pt-5">
                            <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Adım İçeriği (Widgetlar)
                            </h4>

                            <div class="flex items-center gap-2 mb-3">
                                <select wire:model="selectedWidgetType"
                                    class="flex-grow block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach($availableWidgets as $type => $details)
                                        <option value="{{ $type }}">{{ $details['label'] }}</option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="addWidget"
                                    class="p-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-sm transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Eklenmiş Widget Listesi --}}
                            <div class="space-y-2 max-h-60 overflow-y-auto rounded-xl border border-slate-200 p-2 bg-slate-50/50"
                                wire:sortable="updateWidgetOrder"
                                wire:sortable-group="editingStepWidgets.{{ $editingStepId }}">
                                @forelse($widgets as $index => $widget)
                                    <div wire:sortable.item="{{ $index }}" wire:key="widget-edit-{{ $index }}"
                                        wire:sortable-group.item="editingStepWidgets.{{ $editingStepId }}"
                                        class="p-3 bg-white rounded-lg border border-slate-200 shadow-sm hover:border-indigo-200 transition-colors">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <span wire:sortable.handle wire:sortable-group.handle
                                                    class="cursor-grab text-slate-300 hover:text-slate-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 8h16M4 16h16" />
                                                    </svg>
                                                </span>
                                                <span
                                                    class="text-xs font-bold text-slate-700">{{ $availableWidgets[$widget['type']]['label'] ?? $widget['type'] }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button type="button" wire:click.stop="moveWidgetUp({{ $index }})"
                                                    class="p-1 text-slate-400 hover:text-slate-600 disabled:opacity-30"
                                                    @if($loop->first) disabled @endif>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                </button>
                                                <button type="button" wire:click.stop="moveWidgetDown({{ $index }})"
                                                    class="p-1 text-slate-400 hover:text-slate-600 disabled:opacity-30"
                                                    @if($loop->last) disabled @endif>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <button type="button" wire:click="removeWidget({{ $index }})"
                                                    class="p-1 text-red-400 hover:text-red-600">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        {{-- Widget Konfigürasyon --}}
                                        <div class="space-y-2 pl-6 border-l-2 border-indigo-100 ml-1">
                                            @foreach($availableWidgets[$widget['type']]['options'] ?? [] as $optionKey => $optionLabel)
                                                <div class="text-xs">
                                                    <label
                                                        for="widget-{{ $editingStepId ? 'edit-' : '' }}{{ $index }}-{{ $optionKey }}"
                                                        class="block font-semibold text-slate-600 mb-0.5">{{ $optionLabel }}</label>
                                                    @if(str_contains($optionLabel, '(checkbox)'))
                                                        <input type="checkbox"
                                                            wire:model="widgets.{{ $index }}.config.{{ $optionKey }}"
                                                            id="widget-{{ $editingStepId ? 'edit-' : '' }}{{ $index }}-{{ $optionKey }}"
                                                            class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                    @elseif(str_contains($optionLabel, '(textarea)'))
                                                        <textarea
                                                            wire:model.debounce.500ms="widgets.{{ $index }}.config.{{ $optionKey }}"
                                                            id="widget-{{ $editingStepId ? 'edit-' : '' }}{{ $index }}-{{ $optionKey }}"
                                                            rows="2"
                                                            class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs"></textarea>
                                                    @else
                                                        <input type="text"
                                                            wire:model.debounce.500ms="widgets.{{ $index }}.config.{{ $optionKey }}"
                                                            id="widget-{{ $editingStepId ? 'edit-' : '' }}{{ $index }}-{{ $optionKey }}"
                                                            class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                                    @endif
                                                    @error("widgets.{$index}.config.{$optionKey}") <span
                                                    class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                                </div>
                                            @endforeach
                                            @if(empty($availableWidgets[$widget['type']]['options']))
                                                <p class="text-xs text-slate-400 italic">Ek ayar yok.</p>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-xs text-slate-400 py-4 italic">Henüz widget eklenmedi.</p>
                                @endforelse
                            </div>
                            @error('widgets') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            @error('widgets.*') <span class="text-red-500 text-xs mt-1">Widget yapılandırmasında hata
                            var.</span> @enderror
                        </div>

                        {{-- Kaydet Butonu --}}
                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit"
                                class="w-full py-3 px-4 rounded-xl text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 font-bold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Adımı Ekle
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- DÜZENLEME MODALI --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($editingStepId)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center"
            x-data="{ showModal: @entangle('editingStepId'), activeTab: 'settings' }" x-show="showModal"
            x-on:keydown.escape.window="showModal = null" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <div class="relative mx-auto border-0 w-full max-w-5xl shadow-2xl rounded-2xl bg-white m-4 overflow-hidden"
                @click.outside="showModal = null" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                {{-- Modal Başlık --}}
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Adımı Düzenle
                    </h3>
                    <div class="flex items-center gap-4">
                        {{-- Tab Butonları --}}
                        <div class="flex bg-white/20 rounded-xl p-0.5">
                            <button type="button" @click="activeTab = 'settings'"
                                    :class="activeTab === 'settings' ? 'bg-white text-indigo-700 shadow-sm' : 'text-white/80 hover:text-white'"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all">
                                ⚙️ Ayarlar
                            </button>
                            <button type="button" @click="activeTab = 'preview'"
                                    :class="activeTab === 'preview' ? 'bg-white text-indigo-700 shadow-sm' : 'text-white/80 hover:text-white'"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all">
                                👁️ Önizleme
                            </button>
                        </div>
                        <button type="button" @click="showModal = null" class="text-white/70 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ═══ TAB 1: AYARLAR ═══ --}}
                <form wire:submit="updateStep" x-show="activeTab === 'settings'" class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label for="editingName" class="block text-sm font-semibold text-slate-700 mb-1.5">Adım Adı</label>
                        <input type="text" wire:model="editingName" id="editingName"
                            class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        @error('editingName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="editingDescription" class="block text-sm font-semibold text-slate-700 mb-1.5">Açıklama</label>
                        <textarea wire:model="editingDescription" id="editingDescription" rows="3"
                            class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label for="editingDuration" class="block text-sm font-semibold text-slate-700 mb-1.5">Varsayılan Süre (Gün)</label>
                        <input type="number" wire:model="editingDuration" id="editingDuration"
                            class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required min="1">
                    </div>

                    {{-- Widget Yönetimi --}}
                    <div class="border-t border-slate-100 pt-5">
                        <h4 class="text-sm font-bold text-slate-700 mb-3">Adım İçeriği (Widgetlar)</h4>
                        <div class="flex items-center gap-2 mb-3">
                            <select wire:model="selectedWidgetType"
                                class="flex-grow block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach($availableWidgets as $type => $details)
                                    <option value="{{ $type }}">{{ $details['label'] }}</option>
                                @endforeach
                            </select>
                            <button type="button" wire:click="addWidget"
                                class="p-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </button>
                        </div>
                        <div class="space-y-2 max-h-60 overflow-y-auto rounded-xl border border-slate-200 p-2 bg-slate-50/50" wire:sortable="updateWidgetOrder">
                            @forelse($widgets as $index => $widget)
                                <div wire:sortable.item="{{ $index }}" wire:key="widget-edit-{{ $index }}"
                                     class="p-3 bg-white rounded-lg border border-slate-200 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span wire:sortable.handle class="cursor-grab text-slate-300 hover:text-slate-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                                            </span>
                                            <span class="text-xs font-bold text-slate-700">{{ $availableWidgets[$widget['type']]['label'] ?? $widget['type'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button type="button" wire:click="moveWidgetUp({{ $index }})" class="p-1 text-slate-400 hover:text-slate-600 disabled:opacity-30" @if($loop->first) disabled @endif>↑</button>
                                            <button type="button" wire:click="moveWidgetDown({{ $index }})" class="p-1 text-slate-400 hover:text-slate-600 disabled:opacity-30" @if($loop->last) disabled @endif>↓</button>
                                            <button type="button" wire:click="removeWidget({{ $index }})" class="p-1 text-red-400 hover:text-red-600">✕</button>
                                        </div>
                                    </div>
                                    <div class="space-y-2 pl-6 border-l-2 border-indigo-100 ml-1">
                                        @foreach($availableWidgets[$widget['type']]['options'] ?? [] as $optionKey => $optionLabel)
                                            <div class="text-xs">
                                                <label for="widget-edit-{{ $index }}-{{ $optionKey }}" class="block font-semibold text-slate-600 mb-0.5">{{ $optionLabel }}</label>
                                                @if(str_contains($optionLabel, '(checkbox)'))
                                                    <input type="checkbox" wire:model="widgets.{{ $index }}.config.{{ $optionKey }}" id="widget-edit-{{ $index }}-{{ $optionKey }}" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                @elseif(str_contains($optionLabel, '(textarea)'))
                                                    <textarea wire:model.debounce.500ms="widgets.{{ $index }}.config.{{ $optionKey }}" id="widget-edit-{{ $index }}-{{ $optionKey }}" rows="2" class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs"></textarea>
                                                @else
                                                    <input type="text" wire:model.debounce.500ms="widgets.{{ $index }}.config.{{ $optionKey }}" id="widget-edit-{{ $index }}-{{ $optionKey }}" class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                                @endif
                                                @error("widgets.{$index}.config.{$optionKey}") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        @endforeach
                                        @if(empty($availableWidgets[$widget['type']]['options']))
                                            <p class="text-xs text-slate-400 italic">Ek ayar yok.</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-xs text-slate-400 py-4 italic">Henüz widget eklenmedi.</p>
                            @endforelse
                        </div>
                        @error('widgets') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Alt Butonlar --}}
                    <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                        <button type="button" wire:click="cancelEdit" @click="showModal = null"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">İptal</button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl hover:from-indigo-700 hover:to-blue-700 shadow-md transition-all">Adımı Güncelle</button>
                    </div>
                </form>

                {{-- ═══ TAB 2: ÖNİZLEME ═══ --}}
                <div x-show="activeTab === 'preview'" class="p-6 max-h-[70vh] overflow-y-auto">
                    <div class="mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-amber-700 font-medium">Bu bir mockup önizlemedir. Gerçek proje çalışma alanında widget'lar interaktif olacaktır.</p>
                    </div>

                    @if(count($widgets) === 0)
                        <div class="text-center py-16">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <p class="text-slate-500 font-medium">Henüz widget eklenmedi</p>
                            <p class="text-slate-400 text-sm mt-1">Ayarlar sekmesinden widget ekleyin.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($widgets as $index => $widget)
                                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                                    <h5 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-md bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-black">{{ $index + 1 }}</span>
                                        {{ $availableWidgets[$widget['type']]['label'] ?? $widget['type'] }}
                                        @if(!empty($widget['config']['title']))
                                            <span class="text-slate-400 font-normal">— {{ $widget['config']['title'] }}</span>
                                        @endif
                                    </h5>

                                    {{-- ═══ WIDGET ÖNİZLEMELERİ ═══ --}}

                                    @if($widget['type'] === 'textbox')
                                        {{-- METİN ALANI --}}
                                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $widget['config']['title'] ?? 'Metin Alanı' }} @if(!empty($widget['config']['required']))<span class="text-red-500">*</span>@endif</label>
                                            <div class="w-full h-24 bg-white border-2 border-dashed border-slate-300 rounded-lg flex items-center justify-center text-slate-400 text-sm italic">Metin girişi alanı</div>
                                        </div>

                                    @elseif($widget['type'] === 'five_whys')
                                        {{-- 5 NEDEN --}}
                                        <div class="bg-rose-50/50 rounded-lg p-4 border border-rose-100">
                                            @for($i = 1; $i <= 5; $i++)
                                                <div class="flex items-start gap-3 mb-2 last:mb-0">
                                                    <span class="w-7 h-7 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">{{ $i }}</span>
                                                    <div class="flex-grow h-8 bg-white border border-rose-200 rounded-lg"></div>
                                                </div>
                                            @endfor
                                        </div>

                                    @elseif($widget['type'] === 'fishbone')
                                        {{-- BALIK KILÇIĞI --}}
                                        <div class="bg-amber-50/50 rounded-lg p-4 border border-amber-100 text-center">
                                            <div class="grid grid-cols-3 gap-2 mb-3">
                                                @foreach(['Malzeme', 'Makine', 'Metot'] as $cat)
                                                    <div class="bg-white border border-amber-200 rounded-lg p-2 text-xs font-semibold text-amber-700">{{ $cat }}</div>
                                                @endforeach
                                            </div>
                                            <div class="h-2 bg-amber-400 rounded-full mx-8 my-2"></div>
                                            <div class="grid grid-cols-3 gap-2 mt-3">
                                                @foreach(['İnsan', 'Ölçüm', 'Çevre'] as $cat)
                                                    <div class="bg-white border border-amber-200 rounded-lg p-2 text-xs font-semibold text-amber-700">{{ $cat }}</div>
                                                @endforeach
                                            </div>
                                        </div>

                                    @elseif($widget['type'] === 'pareto')
                                        {{-- PARETO --}}
                                        <div class="bg-purple-50/50 rounded-lg p-4 border border-purple-100">
                                            <div class="flex items-end gap-1 h-24 justify-center">
                                                @foreach([85, 65, 45, 30, 20, 10] as $h)
                                                    <div class="w-10 bg-purple-400 rounded-t-md" style="height: {{ $h }}%"></div>
                                                @endforeach
                                            </div>
                                            <p class="text-center text-xs text-purple-500 mt-2 font-medium">Pareto Grafiği + Tablo</p>
                                        </div>

                                    @elseif($widget['type'] === 'swot')
                                        {{-- SWOT ANALİZİ --}}
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="w-7 h-7 rounded-lg bg-green-500 text-white flex items-center justify-center text-xs font-black">S</span>
                                                    <span class="text-sm font-bold text-green-700">Güçlü Yönler</span>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="h-3 bg-green-200/60 rounded w-full"></div>
                                                    <div class="h-3 bg-green-200/60 rounded w-4/5"></div>
                                                    <div class="h-3 bg-green-200/60 rounded w-3/5"></div>
                                                </div>
                                            </div>
                                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="w-7 h-7 rounded-lg bg-red-500 text-white flex items-center justify-center text-xs font-black">W</span>
                                                    <span class="text-sm font-bold text-red-700">Zayıf Yönler</span>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="h-3 bg-red-200/60 rounded w-full"></div>
                                                    <div class="h-3 bg-red-200/60 rounded w-3/4"></div>
                                                    <div class="h-3 bg-red-200/60 rounded w-1/2"></div>
                                                </div>
                                            </div>
                                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="w-7 h-7 rounded-lg bg-blue-500 text-white flex items-center justify-center text-xs font-black">O</span>
                                                    <span class="text-sm font-bold text-blue-700">Fırsatlar</span>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="h-3 bg-blue-200/60 rounded w-full"></div>
                                                    <div class="h-3 bg-blue-200/60 rounded w-5/6"></div>
                                                    <div class="h-3 bg-blue-200/60 rounded w-2/3"></div>
                                                </div>
                                            </div>
                                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="w-7 h-7 rounded-lg bg-amber-500 text-white flex items-center justify-center text-xs font-black">T</span>
                                                    <span class="text-sm font-bold text-amber-700">Tehditler</span>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="h-3 bg-amber-200/60 rounded w-full"></div>
                                                    <div class="h-3 bg-amber-200/60 rounded w-2/3"></div>
                                                    <div class="h-3 bg-amber-200/60 rounded w-1/3"></div>
                                                </div>
                                            </div>
                                        </div>

                                    @elseif($widget['type'] === 'checklist')
                                        {{-- KONTROL LİSTESİ --}}
                                        <div class="bg-lime-50/50 rounded-lg p-4 border border-lime-100">
                                            @php
                                                $items = !empty($widget['config']['items']) ? array_filter(explode("\n", $widget['config']['items'])) : ['Madde 1 kontrol edildi', 'Madde 2 kontrol edildi', 'Madde 3 kontrol edildi'];
                                            @endphp
                                            <div class="space-y-2">
                                                @foreach($items as $item)
                                                    <label class="flex items-center gap-3 bg-white border border-lime-200 rounded-lg px-4 py-2.5 cursor-pointer hover:bg-lime-50 transition-colors">
                                                        <input type="checkbox" disabled class="rounded border-slate-300 text-lime-600">
                                                        <span class="text-sm text-slate-700">{{ trim($item) }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <p class="text-xs text-lime-600 mt-3 font-medium text-center">Kullanıcılar maddeleri işaretleyerek tamamlayacak</p>
                                        </div>

                                    @elseif($widget['type'] === 'before_after')
                                        {{-- ÖNCE / SONRA KARŞILAŞTIRMA --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-red-50 border-2 border-dashed border-red-200 rounded-xl p-4 text-center">
                                                <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-red-100 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <p class="text-sm font-bold text-red-700 mb-1">ÖNCE</p>
                                                <div class="h-20 bg-white border border-red-200 rounded-lg mb-2 flex items-center justify-center text-xs text-slate-400 italic">Fotoğraf / Açıklama</div>
                                                <div class="h-3 bg-red-100 rounded w-full"></div>
                                                <div class="h-3 bg-red-100 rounded w-3/4 mt-1"></div>
                                            </div>
                                            <div class="bg-green-50 border-2 border-dashed border-green-200 rounded-xl p-4 text-center">
                                                <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-green-100 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <p class="text-sm font-bold text-green-700 mb-1">SONRA</p>
                                                <div class="h-20 bg-white border border-green-200 rounded-lg mb-2 flex items-center justify-center text-xs text-slate-400 italic">Fotoğraf / Açıklama</div>
                                                <div class="h-3 bg-green-100 rounded w-full"></div>
                                                <div class="h-3 bg-green-100 rounded w-3/4 mt-1"></div>
                                            </div>
                                        </div>

                                    @elseif($widget['type'] === 'risk_matrix')
                                        {{-- RİSK MATRİSİ --}}
                                        @php $matrixSize = ($widget['config']['size'] ?? '5') == '3' ? 3 : 5; @endphp
                                        <div class="bg-pink-50/30 rounded-lg p-4 border border-pink-100">
                                            <div class="flex items-end gap-1">
                                                {{-- Y Ekseni --}}
                                                <div class="flex flex-col items-center justify-between pr-2" style="height: {{ $matrixSize * 36 }}px">
                                                    <span class="text-[10px] font-bold text-slate-500 -rotate-90 whitespace-nowrap">OLASILIK →</span>
                                                </div>
                                                {{-- Matris --}}
                                                <div class="flex-grow">
                                                    <div class="grid gap-0.5" style="grid-template-columns: repeat({{ $matrixSize }}, 1fr); grid-template-rows: repeat({{ $matrixSize }}, 1fr);">
                                                        @for($row = $matrixSize; $row >= 1; $row--)
                                                            @for($col = 1; $col <= $matrixSize; $col++)
                                                                @php
                                                                    $riskScore = $row * $col;
                                                                    $maxScore = $matrixSize * $matrixSize;
                                                                    $pct = $riskScore / $maxScore;
                                                                    if ($pct >= 0.6) $cellColor = 'bg-red-400 text-white';
                                                                    elseif ($pct >= 0.35) $cellColor = 'bg-amber-300 text-amber-900';
                                                                    elseif ($pct >= 0.15) $cellColor = 'bg-yellow-200 text-yellow-800';
                                                                    else $cellColor = 'bg-green-200 text-green-800';
                                                                @endphp
                                                                <div class="h-8 rounded-sm flex items-center justify-center text-[10px] font-bold {{ $cellColor }}">{{ $riskScore }}</div>
                                                            @endfor
                                                        @endfor
                                                    </div>
                                                    <p class="text-center text-[10px] font-bold text-slate-500 mt-1">ETKİ →</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-center gap-3 mt-3">
                                                <span class="flex items-center gap-1 text-[10px]"><span class="w-3 h-3 rounded bg-green-200"></span> Düşük</span>
                                                <span class="flex items-center gap-1 text-[10px]"><span class="w-3 h-3 rounded bg-yellow-200"></span> Orta</span>
                                                <span class="flex items-center gap-1 text-[10px]"><span class="w-3 h-3 rounded bg-amber-300"></span> Yüksek</span>
                                                <span class="flex items-center gap-1 text-[10px]"><span class="w-3 h-3 rounded bg-red-400"></span> Kritik</span>
                                            </div>
                                        </div>

                                    @elseif($widget['type'] === 'user_select')
                                        <div class="bg-green-50/50 rounded-lg p-4 border border-green-100">
                                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $widget['config']['title'] ?? 'Kullanıcı Seçimi' }}</label>
                                            <div class="bg-white border border-green-200 rounded-lg px-3 py-2.5 text-sm text-slate-400">-- Kullanıcı Seç --</div>
                                        </div>

                                    @elseif($widget['type'] === 'date_picker')
                                        <div class="bg-cyan-50/50 rounded-lg p-4 border border-cyan-100">
                                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $widget['config']['title'] ?? 'Tarih' }}</label>
                                            <div class="bg-white border border-cyan-200 rounded-lg px-3 py-2.5 text-sm text-slate-400 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                gg.aa.yyyy
                                            </div>
                                        </div>

                                    @elseif($widget['type'] === 'file_upload')
                                        <div class="bg-teal-50/50 rounded-lg p-4 border border-teal-100">
                                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $widget['config']['title'] ?? 'Dosya Yükleme' }}</label>
                                            <div class="border-2 border-dashed border-teal-300 rounded-lg p-6 text-center bg-white">
                                                <svg class="w-8 h-8 mx-auto text-teal-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                <p class="text-xs text-teal-600 font-medium">Dosya sürükleyin veya tıklayın</p>
                                            </div>
                                        </div>

                                    @elseif($widget['type'] === 'info_text')
                                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <p class="text-sm text-slate-600">{{ $widget['config']['content'] ?? 'Bilgi metni içeriği burada görünecek.' }}</p>
                                            </div>
                                        </div>

                                    @elseif(in_array($widget['type'], ['bar_chart', 'line_chart']))
                                        <div class="bg-orange-50/50 rounded-lg p-4 border border-orange-100">
                                            <div class="flex items-end gap-1 h-20 justify-center">
                                                @foreach([60, 80, 45, 90, 55, 70] as $h)
                                                    <div class="w-8 bg-orange-400 rounded-t-md" style="height: {{ $h }}%"></div>
                                                @endforeach
                                            </div>
                                            <p class="text-center text-xs text-orange-500 mt-2 font-medium">{{ $widget['type'] === 'bar_chart' ? 'Sütun' : 'Çizgi' }} Grafiği (Dinamik veri)</p>
                                        </div>
                                    
                                    @elseif($widget['type'] === '4m_report')
                                        {{-- 4M RAPORU ÖNİZLEME --}}
                                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="bg-white p-3 rounded-lg border border-indigo-200">
                                                    <p class="text-[10px] font-bold text-indigo-700 mb-1">İNSAN (Man)</p>
                                                    <div class="h-10 bg-slate-50 border border-dashed border-slate-200 rounded"></div>
                                                </div>
                                                <div class="bg-white p-3 rounded-lg border border-indigo-200">
                                                    <p class="text-[10px] font-bold text-indigo-700 mb-1">MAKİNE (Machine)</p>
                                                    <div class="h-10 bg-slate-50 border border-dashed border-slate-200 rounded"></div>
                                                </div>
                                                <div class="bg-white p-3 rounded-lg border border-indigo-200">
                                                    <p class="text-[10px] font-bold text-indigo-700 mb-1">MALZEME (Material)</p>
                                                    <div class="h-10 bg-slate-50 border border-dashed border-slate-200 rounded"></div>
                                                </div>
                                                <div class="bg-white p-3 rounded-lg border border-indigo-200">
                                                    <p class="text-[10px] font-bold text-indigo-700 mb-1">METOT (Method)</p>
                                                    <div class="h-10 bg-slate-50 border border-dashed border-slate-200 rounded"></div>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-center text-indigo-500 mt-3 font-medium">4M Analiz Raporu Formu</p>
                                        </div>

                                    @else
                                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200 text-center text-sm text-slate-500 italic">
                                            {{ $availableWidgets[$widget['type']]['label'] ?? $widget['type'] }} widget önizlemesi
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endif

</div>