<div> {{-- === KÖK ELEMENT === --}}
    <div class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 min-h-screen p-4 md:p-6">
        <div class="max-w-7xl mx-auto">

            {{-- 1. BAŞLIK --}}
            @include('livewire.admin.sikayetler-partials.header')


            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 overflow-hidden">

                {{-- 2. SEKME MENÜSÜ (STATS YERİNE) --}}
                <div class="border-b border-gray-200 bg-white px-2 sm:px-6 mb-4 rounded-t-xl">
                    <nav class="-mb-px flex flex-wrap lg:flex-nowrap justify-start lg:justify-between items-center gap-x-3 md:gap-x-6"
                        aria-label="Tabs">

                        {{-- 1. TÜMÜ --}}
                        <button wire:click="setTab('tumu')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'tumu' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Tümü
                            <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['tumu'] ?? 0 }}
                            </span>
                        </button>

                        {{-- 2. YENİ (Mavi) --}}
                        <button wire:click="setTab('yeni')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'yeni' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Yeni
                            <span
                                class="{{ $activeTab === 'yeni' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['yeni'] ?? 0 }}
                            </span>
                        </button>

                        {{-- 3. İŞLEMDE (Turuncu) --}}
                        <button wire:click="setTab('islemde')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'islemde' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            İşlemde
                            <span
                                class="{{ $activeTab === 'islemde' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['islemde'] ?? 0 }}
                            </span>
                        </button>

                        {{-- === YENİ: ONAY BEKLEYENLER (MOR/TURUNCU) === --}}
                        <button wire:click="setTab('onay_bekleyenler')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'onay_bekleyenler' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Onay Bekleyen
                            <span
                                class="{{ $activeTab === 'onay_bekleyenler' ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['onay_bekleyenler'] ?? 0 }}
                            </span>
                        </button>

                        {{-- 4. ÇÖZÜLEN (Yeşil) --}}
                        <button wire:click="setTab('cozulmus')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'cozulmus' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Çözülenler
                            <span
                                class="{{ $activeTab === 'cozulmus' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['cozulmus'] ?? 0 }}
                            </span>
                        </button>

                        {{-- === YENİ: TALEP OLARAK KAPATILAN (GRİ) === --}}
                        <button wire:click="setTab('talep_kapali')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'talep_kapali' ? 'border-gray-500 text-gray-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Talep Kapanan
                            <span
                                class="{{ $activeTab === 'talep_kapali' ? 'bg-gray-200 text-gray-800' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['talep_kapali'] ?? 0 }}
                            </span>
                        </button>

                        {{-- === YENİ: HATALI BİLDİRİM OLARAK KAPATILAN (KIRMIZI/GRİ) === --}}
                        <button wire:click="setTab('hatali_bildirim')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'hatali_bildirim' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            🚫 Hatalı Bildirim
                            <span
                                class="{{ $activeTab === 'hatali_bildirim' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['hatali_bildirim'] ?? 0 }}
                            </span>
                        </button>

                        {{-- 5. İPTAL/RED (Kırmızı) --}}
                        <button wire:click="setTab('iptal')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                {{ $activeTab === 'iptal' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            İptal/Red
                            <span
                                class="{{ $activeTab === 'iptal' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2.5 rounded-full text-xs font-bold">
                                {{ $stats['iptal'] ?? 0 }}
                            </span>
                        </button>

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
    </style>
</div>