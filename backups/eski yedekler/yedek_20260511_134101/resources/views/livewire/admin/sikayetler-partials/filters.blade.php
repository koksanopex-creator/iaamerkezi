<div x-data="{ open: false }" class="border-t border-gray-100 bg-gradient-to-br from-gray-50/80 to-white">
    <div @click="open = !open"
        class="p-4 md:p-6 flex items-center justify-between cursor-pointer hover:bg-gray-50/50 transition-colors duration-150">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-800">Filtreler</h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full" x-show="!open">Genişletmek için
                tıklayın</span>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click.stop="resetFilters" type="button"
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Temizle
            </button>
            <div class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>

    <div x-show="open" x-collapse style="display: none;" class="px-4 md:px-6 pb-6 border-t border-gray-200">

        {{-- 0. GRUP: HIZLI ARAMA (KONU) --}}
        <div class="mb-5 mt-5">
            <div class="relative group">
                <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="filtreKonu"
                    placeholder="Şikayet Konusunda Ara... (Min 2 karakter)"
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:placeholder-gray-300 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm transition-all duration-200">
            </div>
        </div>

        {{-- 1. GRUP: Durum & Öncelik & Konum --}}
        <div class="mb-5 mt-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                Durum & Öncelik & Konum
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Durum</label>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="relative w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150">
                            <span class="block truncate">
                                @if(empty($filtreDurum))
                                    Tüm Durumlar
                                @else
                                    {{ count($filtreDurum) }} Durum Seçildi
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg border border-gray-200">
                            <div class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach(['Yeni', 'İşlemde', 'Çözümlendi', 'Kapatıldı', 'Onay Bekleyenler'] as $durum)
                                    <label class="flex items-center px-4 py-2 hover:bg-indigo-50 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model.live="filtreDurum" value="{{ $durum }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $durum }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Öncelik</label>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="relative w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150">
                            <span class="block truncate">
                                @if(empty($filtreOncelik))
                                    Tüm Öncelikler
                                @else
                                    {{ count($filtreOncelik) }} Seçildi
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg border border-gray-200">
                            <div class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach(['Acil', 'Yüksek', 'Normal', 'Düşük'] as $oncelik)
                                    <label class="flex items-center px-4 py-2 hover:bg-indigo-50 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model.live="filtreOncelik" value="{{ $oncelik }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $oncelik }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konum Tipi</label>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="relative w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150">
                            <span class="block truncate">
                                @if(empty($filtreKonumTipi))
                                    Tümü
                                @else
                                    {{ count($filtreKonumTipi) }} Seçildi
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg border border-gray-200">
                            <div class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach(['Yurt İçi', 'Yurt Dışı'] as $konum)
                                    <label class="flex items-center px-4 py-2 hover:bg-indigo-50 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model.live="filtreKonumTipi" value="{{ $konum }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $konum }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. GRUP: Proje Durumu & Bekleme Süresi --}}
        <div class="mb-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <div class="w-1 h-4 bg-pink-500 rounded-full"></div>
                Proje & Süreç
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Proje Durumu</label>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="relative w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150">
                            <span class="block truncate">
                                @if(empty($filtreProjeDurumu))
                                    Tümü
                                @else
                                    {{ count($filtreProjeDurumu) }} Seçildi
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg border border-gray-200">
                            <div class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach(['Atandı', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Revize Ediliyor', 'Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi'] as $durum)
                                    <label class="flex items-center px-4 py-2 hover:bg-indigo-50 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model.live="filtreProjeDurumu" value="{{ $durum }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $durum }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- YENİ: SÜREÇ ÖZELLİKLERİ (İADE / ZİYARET) --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ek Süreç Filtreleri</label>
                    <div class="flex flex-wrap items-center gap-4 bg-white p-2 rounded-lg border border-gray-100 shadow-sm">
                        <label class="inline-flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" wire:model.live="filtreIadeVar" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rose-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                            </div>
                            <span class="ml-3 text-sm font-bold text-gray-700 group-hover:text-rose-600 transition-colors flex items-center gap-1">
                                ♻️ İadeli Şikayetler
                            </span>
                        </label>

                        <div class="w-px h-6 bg-gray-200 hidden sm:block"></div>

                        <label class="inline-flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" wire:model.live="filtreZiyaretVar" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </div>
                            <span class="ml-3 text-sm font-bold text-gray-700 group-hover:text-indigo-600 transition-colors flex items-center gap-1">
                                📅 Ziyaretli Şikayetler
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="filtreBeklemeMin" class="block text-sm font-medium text-gray-700 mb-1.5">Bekleme (Min
                        Gün)</label>
                    <input type="number" wire:model.live.debounce.500ms="filtreBeklemeMin" id="filtreBeklemeMin"
                        placeholder="Örn: 1"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                </div>
                <div>
                    <label for="filtreBeklemeMax" class="block text-sm font-medium text-gray-700 mb-1.5">Bekleme (Max
                        Gün)</label>
                    <input type="number" wire:model.live.debounce.500ms="filtreBeklemeMax" id="filtreBeklemeMax"
                        placeholder="Örn: 30"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                </div>
            </div>
        </div>

        {{-- 3. GRUP: Kategorizasyon --}}
        <div class="mb-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
                Kategorizasyon
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Çözüm Takımı</label>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="relative w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150">
                            <span class="block truncate">
                                @if(empty($filtreTakim))
                                    Tüm Takımlar
                                @else
                                    {{ count($filtreTakim) }} Seçildi
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg border border-gray-200">
                            <div class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach($cozumTakimlari as $takim)
                                    <label class="flex items-center px-4 py-2 hover:bg-indigo-50 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model.live="filtreTakim" value="{{ $takim->id }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $takim->ad }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="relative w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150">
                            <span class="block truncate">
                                @if(empty($filtreKategori))
                                    Tüm Kategoriler
                                @else
                                    {{ count($filtreKategori) }} Seçildi
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg border border-gray-200">
                            <div class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach($kategoriler as $kategori)
                                    <label class="flex items-center px-4 py-2 hover:bg-indigo-50 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model.live="filtreKategori" value="{{ $kategori->id }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $kategori->ad }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. GRUP: Kullanıcı & Müşteri --}}
        <div class="mb-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <div class="w-1 h-4 bg-amber-500 rounded-full"></div>
                Kullanıcı & Müşteri
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="filtreMusteriAdi" class="block text-sm font-medium text-gray-700 mb-1.5">Müşteri
                        Adı</label>
                    <input type="text" wire:model.live.debounce.500ms="filtreMusteriAdi" id="filtreMusteriAdi"
                        placeholder="Müşteri adında ara..."
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ekleyen Kişi</label>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                            class="relative w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150">
                            <span class="block truncate">
                                @if(empty($filtreEkleyen))
                                    Tüm Kullanıcılar
                                @else
                                    {{ count($filtreEkleyen) }} Seçildi
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg border border-gray-200">
                            <div class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach($ekleyenKullanicilar as $kullanici)
                                    <label class="flex items-center px-4 py-2 hover:bg-indigo-50 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model.live="filtreEkleyen" value="{{ $kullanici->id }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $kullanici->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. GRUP: Tarihler --}}
        <div class="mb-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <div class="w-1 h-4 bg-blue-500 rounded-full"></div>
                Tarih Aralıkları
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div class="text-xs font-medium text-gray-600 mb-1">Son Tarih</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="filtreSonTarihBaslangic"
                                class="block text-xs text-gray-500 mb-1">Başlangıç</label>
                            <input type="date" wire:model.live="filtreSonTarihBaslangic" id="filtreSonTarihBaslangic"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition duration-150 ease-in-out">
                        </div>
                        <div>
                            <label for="filtreSonTarihBitis" class="block text-xs text-gray-500 mb-1">Bitiş</label>
                            <input type="date" wire:model.live="filtreSonTarihBitis" id="filtreSonTarihBitis"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition duration-150 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="text-xs font-medium text-gray-600 mb-1">Kayıt Tarihi</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="filtreKayitTarihBaslangic"
                                class="block text-xs text-gray-500 mb-1">Başlangıç</label>
                            <input type="date" wire:model.live="filtreKayitTarihBaslangic"
                                id="filtreKayitTarihBaslangic"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition duration-150 ease-in-out">
                        </div>
                        <div>
                            <label for="filtreKayitTarihBitis" class="block text-xs text-gray-500 mb-1">Bitiş</label>
                            <input type="date" wire:model.live="filtreKayitTarihBitis" id="filtreKayitTarihBitis"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition duration-150 ease-in-out">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. GRUP: Puan --}}
        <div>
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <div class="w-1 h-4 bg-purple-500 rounded-full"></div>
                Puan Aralığı
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="filtrePuanMin" class="block text-sm font-medium text-gray-700 mb-1.5">Minimum
                        Puan</label>
                    <input type="number" wire:model.live.debounce.500ms="filtrePuanMin" id="filtrePuanMin"
                        placeholder="En az..." min="0"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                </div>
                <div>
                    <label for="filtrePuanMax" class="block text-sm font-medium text-gray-700 mb-1.5">Maksimum
                        Puan</label>
                    <input type="number" wire:model.live.debounce.500ms="filtrePuanMax" id="filtrePuanMax"
                        placeholder="En çok..." min="0"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                </div>
            </div>
        </div>

        {{-- Yükleniyor Göstergesi --}}
        <div wire:loading class="pt-5 mt-5 border-t border-gray-200 w-full">
            <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Filtreleniyor...
            </div>
        </div>
    </div>
</div>