<div> {{-- === KÖK ELEMENT === --}}
    <div class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 min-h-screen p-4 md:p-6">
        <div class="max-w-7xl mx-auto">

            {{-- 1. BAŞLIK --}}
            @include('livewire.admin.sikayetler-partials.header')


            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6">

                {{-- 2. SEKME MENÜSÜ (STATS YERİNE) --}}
                <div class="border-b border-gray-200 bg-white px-2 sm:px-6 mb-4 rounded-t-xl">
                    <nav class="-mb-px flex flex-nowrap justify-between items-center w-full overflow-hidden" aria-label="Tabs">

                        {{-- 1. TÜMÜ --}}
                        <button wire:click="setTab('tumu')"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-[10px] lg:text-xs xl:text-sm flex-1 flex justify-center items-center gap-1 xl:gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'tumu' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Tümü
                            <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['tumu'] ?? 0 }}
                            </span>
                        </button>

                        {{-- 2. YENİ (Mavi) --}}
                        <button wire:click="setTab('yeni')"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-[10px] lg:text-xs xl:text-sm flex-1 flex justify-center items-center gap-1 xl:gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'yeni' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Yeni
                            <span
                                class="{{ $activeTab === 'yeni' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['yeni'] ?? 0 }}
                            </span>
                        </button>

                        {{-- 3. İŞLEMDE (Turuncu) --}}
                        <button wire:click="setTab('islemde')"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-[10px] lg:text-xs xl:text-sm flex-1 flex justify-center items-center gap-1 xl:gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'islemde' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            İşlemde
                            <span
                                class="{{ $activeTab === 'islemde' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['islemde'] ?? 0 }}
                            </span>
                        </button>

                        {{-- === YENİ: ONAY BEKLEYENLER (MOR/TURUNCU) === --}}
                        <button wire:click="setTab('onay_bekleyenler')"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-[10px] lg:text-xs xl:text-sm flex-1 flex justify-center items-center gap-1 xl:gap-2 transition-colors focus:outline-none relative
                                {{ $activeTab === 'onay_bekleyenler' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            
                            @if(($stats['onay_bekleyenler'] ?? 0) > 0)
                                <span class="absolute top-2 right-1 flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                </span>
                            @endif

                            Onay Bekleyen
                            <span
                                class="{{ $activeTab === 'onay_bekleyenler' ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['onay_bekleyenler'] ?? 0 }}
                            </span>
                        </button>

                        {{-- 4. ÇÖZÜLEN (Yeşil) --}}
                        <button wire:click="setTab('cozulmus')"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-[10px] lg:text-xs xl:text-sm flex-1 flex justify-center items-center gap-1 xl:gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'cozulmus' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Çözülenler
                            <span
                                class="{{ $activeTab === 'cozulmus' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['cozulmus'] ?? 0 }}
                            </span>
                        </button>

                        {{-- === YENİ: TALEP OLARAK KAPATILAN (GRİ) === --}}
                        <button wire:click="setTab('talep_kapali')"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-[10px] lg:text-xs xl:text-sm flex-1 flex justify-center items-center gap-1 xl:gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'talep_kapali' ? 'border-gray-500 text-gray-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Talep Kapanan
                            <span
                                class="{{ $activeTab === 'talep_kapali' ? 'bg-gray-200 text-gray-800' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['talep_kapali'] ?? 0 }}
                            </span>
                        </button>

                        {{-- === YENİ: HATALI BİLDİRİM OLARAK KAPATILAN (KIRMIZI/GRİ) === --}}
                        <button wire:click="setTab('hatali_bildirim')"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-[10px] lg:text-xs xl:text-sm flex-1 flex justify-center items-center gap-1 xl:gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'hatali_bildirim' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            🚫 Hatalı Bildirim
                            <span
                                class="{{ $activeTab === 'hatali_bildirim' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['hatali_bildirim'] ?? 0 }}
                            </span>
                        </button>

                        {{-- 5. İPTAL/RED (Kırmızı) --}}
                        <button wire:click="setTab('iptal')"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-[10px] lg:text-xs xl:text-sm flex-1 flex justify-center items-center gap-1 xl:gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'iptal' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            İptal/Red
                            <span
                                class="{{ $activeTab === 'iptal' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['iptal'] ?? 0 }}
                            </span>
                        </button>

                        {{-- 6. ÇÖP KUTUSU (Yalnızca SuperAdmin) --}}
                        @if(Auth::user()->hasAnyRole(['Superadmin', 'SuperAdmin', 'SUPERADMIN', 'superadmin', 'Super Admin', 'SUPER ADMIN', 'Yonetim', 'Yönetim']))
                        <button wire:click="setTab('cop_kutusu')"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-[10px] lg:text-xs xl:text-sm flex-1 flex justify-center items-center gap-1 xl:gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'cop_kutusu' ? 'border-gray-800 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            🗑️ Çöp Kutusu
                        </button>
                        @endif

                    </nav>
                </div>

                {{-- 3. FİLTRELER --}}
                @include('livewire.admin.sikayetler-partials.filters')

            </div>

            {{-- 4. KART VEYA LİSTE GÖRÜNÜMÜ --}}
            @if($viewMode === 'list')
                @include('livewire.admin.sikayetler-partials.list')
            @else
                @include('livewire.admin.sikayetler-partials.cards')
            @endif

            {{-- SAYFALAMA --}}
            <div class="mt-6">
                {{ $sikayetler->links() }}
            </div>

        </div>
    </div>

    @livewire('admin.sikayet-triyaj-modal')

    {{-- YENİ SİLME ONAY MODALI --}}
    @if($confirmingDeletionId || $confirmingBulkDelete)
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Arka plan overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" wire:click="cancelDelete"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Heroicon name: outline/exclamation -->
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                Şikayet Silme Onayı
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    @if($activeTab === 'cop_kutusu')
                                        {{ $confirmingBulkDelete ? count($selectedSikayetler) . ' adet seçili şikayeti KALICI OLARAK silmek istiyor musunuz?' : 'Bu şikayeti KALICI OLARAK silmek istediğinize emin misiniz?' }}
                                        <br><br><span class="text-red-600 font-bold">Bu işlem geri alınamaz!</span> İlgili tüm dosyalar ve teknik detaylar veritabanından kalıcı olarak silinecektir.
                                    @else
                                        {{ $confirmingBulkDelete ? count($selectedSikayetler) . ' adet seçili şikayeti Çöp Kutusuna taşımak istiyor musunuz?' : 'Bu şikayeti Çöp Kutusuna taşımak istediğinize emin misiniz?' }}
                                        <br><br>Silinen şikayetler, sistemde kayıtlı kalır ve Çöp Kutusundan istenildiği zaman geri yüklenebilir. Şikayet puanları ve ilişkili kayıtlar silinmez.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                    <button type="button" wire:click="executeDelete" wire:loading.attr="disabled"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-5 py-2.5 {{ $activeTab === 'cop_kutusu' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-orange-500 hover:bg-orange-600 focus:ring-orange-500' }} text-base font-bold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-all flex items-center gap-2">
                        <svg wire:loading wire:target="executeDelete" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        @if($activeTab === 'cop_kutusu')
                            Evet, Kalıcı Olarak Sil
                        @else
                            Evet, Çöp Kutusuna Taşı
                        @endif
                    </button>
                    <button type="button" wire:click="cancelDelete"
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Vazgeç
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- YENİ GERİ AL (UNDO) SNACKBAR --}}
    @if($recentlyDeletedSikayet)
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 15000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-[150] flex items-center justify-between w-full max-w-sm p-4 text-gray-900 bg-white rounded-xl shadow-2xl border border-gray-200">
        
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-gray-800">{{ $recentlyDeletedSikayet['message'] }}</span>
                <span class="text-xs text-gray-500">Bu işlemi geri alabilirsiniz.</span>
            </div>
        </div>
        
        <button wire:click="undoDelete" @click="show = false" class="ml-4 px-4 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors whitespace-nowrap">
            Geri Al
        </button>
        <button @click="show = false" class="ml-2 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    {{-- CSS Animasyonları --}}
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.5s ease-out forwards;
        }

        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-slide-in {
            animation: slide-in 0.4s ease-out forwards;
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slide-up 0.3s ease-out forwards;
        }

        @keyframes pulse {
            50% {
                opacity: .5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes ping {
            75%, 100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .animate-ping {
            animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>
</div>