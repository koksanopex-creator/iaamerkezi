<div class="mb-8">
    <div
        class="rounded-2xl border border-gray-200/70 bg-white/80 backdrop-blur p-6 shadow-sm flex flex-col lg:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Müşteri Şikayetleri</h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Şikayet havuzu ve yönetim paneli
            </p>
        </div>

        <div class="flex flex-wrap gap-3 items-center">
            {{-- GÖRÜNÜM DEĞİŞTİRİCİ --}}
            <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200 shadow-inner mr-2">
                {{-- Kart Görünümü --}}
                <button wire:click="setViewMode('card')"
                    class="p-2 rounded-lg transition-all flex items-center gap-2 {{ $viewMode === 'card' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-gray-200 font-bold' : 'text-gray-400 hover:text-gray-600' }}"
                    title="Kart Görünümü">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                {{-- Liste Görünümü --}}
                <button wire:click="setViewMode('list')"
                    class="p-2 rounded-lg transition-all flex items-center gap-2 {{ $viewMode === 'list' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-gray-200 font-bold' : 'text-gray-400 hover:text-gray-600' }}"
                    title="Liste Görünümü">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            @if(!Auth::user()->hasRole(['Yonetim', 'Direktör', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Arabuluculuk Personel']))
                {{-- TOPLU SİLME BUTONU (Sadece şikayet seçiliyse görünür) --}}
                @if(count($selectedSikayetler) > 0)
                    @can('deleteAny', \App\Models\MusteriSikayeti::class)
                        <button type="button" wire:click="confirmBulkDelete"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-red-600 hover:bg-red-700 shadow-lg hover:shadow-red-500/30 transition-all transform hover:-translate-y-0.5 animate-pulse">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Seçilenleri Sil ({{ count($selectedSikayetler) }})
                        </button>
                    @endcan
                @endif

                <a href="{{ route('admin.sikayetler.create') }}"
                    class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Yeni Şikayet
                </a>
            @endif
            @role('Superadmin|Müşteri Şikayeti Kurulu')
            <a href="{{ route('admin.sikayetler.kurulGirdileri') }}"
                class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-indigo-700 bg-white border border-indigo-200 hover:bg-indigo-50 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Kurul Girdileri
            </a>
            @endrole
        </div>
    </div>
</div>