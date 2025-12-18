<div> {{-- === KÖK ELEMENT === --}}

    <div class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 min-h-screen p-4 md:p-6">
        <div class="max-w-7xl mx-auto">

        {{-- === 1. BAŞLIK VE ÜST BUTONLAR === --}}
        <div class="mb-8">
            <div class="rounded-2xl border border-gray-200/70 bg-white/80 backdrop-blur p-6 shadow-sm flex flex-col lg:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Müşteri Şikayetleri</h1>
                    <p class="text-gray-500 mt-1 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Şikayet havuzu ve yönetim paneli
                    </p>
                </div>
                
                <div class="flex gap-3">
                    @if(!Auth::user()->hasRole('Yonetim'))
                        <a href="{{ route('admin.sikayetler.create') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Yeni Şikayet
                        </a>
                    @endif
                    @role('Superadmin|Müşteri Şikayeti Kurulu')
                        <a href="{{ route('admin.sikayetler.kurulGirdileri') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-indigo-700 bg-white border border-indigo-200 hover:bg-indigo-50 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                            Kurul Girdileri
                        </a>
                    @endrole
                </div>
            </div>
        </div>

        {{-- === 2. İSTATİSTİKLER VE FİLTRELEME === --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
             {{-- İstatistik Bar --}}
             <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100 bg-gray-50/50 border-b border-gray-200/70">
                <div class="p-4 text-center group hover:bg-blue-50 transition-colors">
                    <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Toplam
                    </p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">{{ $stats['toplam'] }}</p>
                </div>
                <div class="p-4 text-center group hover:bg-yellow-50 transition-colors">
                    <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Beklemede
                    </p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent">{{ $stats['beklemede'] }}</p>
                </div>
                <div class="p-4 text-center group hover:bg-blue-50 transition-colors">
                    <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        İşlemde
                    </p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">{{ $stats['islemde'] }}</p>
                </div>
                <div class="p-4 text-center group hover:bg-green-50 transition-colors">
                    <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Çözümlenmiş
                    </p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">{{ $stats['cozulmus'] }}</p>
                </div>
            </div>

            {{-- FİLTRELEME ALANI (Görseldeki Gibi Detaylı) --}}
            <div x-data="{ open: false }" class="border-t border-gray-100 bg-gradient-to-br from-gray-50/80 to-white">
                <div @click="open = !open" class="p-4 md:p-6 flex items-center justify-between cursor-pointer hover:bg-gray-50/50 transition-colors duration-150">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-800">Filtreler</h3>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full" x-show="!open">Genişletmek için tıklayın</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button wire:click.stop="resetFilters" type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Temizle
                        </button>
                        <div class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div x-show="open" x-collapse style="display: none;" class="px-4 md:px-6 pb-6 border-t border-gray-200">
                    
                    {{-- 1. GRUP: Durum & Öncelik & Konum --}}
                    <div class="mb-5 mt-5">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                            Durum & Öncelik & Konum
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="filtreDurum" class="block text-sm font-medium text-gray-700 mb-1.5">Durum</label>
                                <select wire:model.live="filtreDurum" id="filtreDurum" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="Yeni">Yeni</option>
                                    <option value="İşlemde">İşlemde</option>
                                    <option value="Çözümlendi">Çözümlendi</option>
                                    <option value="Kapatıldı">Kapatıldı</option>
                                    <option value="Onay Bekleyenler">Onay Bekleyenler</option> {{-- EKLENDİ --}}
                                </select>
                            </div>
                            <div>
                                <label for="filtreOncelik" class="block text-sm font-medium text-gray-700 mb-1.5">Öncelik</label>
                                <select wire:model.live="filtreOncelik" id="filtreOncelik" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                    <option value="">Tüm Öncelikler</option>
                                    <option value="Acil">Acil</option>
                                    <option value="Yüksek">Yüksek</option>
                                    <option value="Normal">Normal</option>
                                    <option value="Düşük">Düşük</option>
                                </select>
                            </div>
                            <div>
                                <label for="filtreKonumTipi" class="block text-sm font-medium text-gray-700 mb-1.5">Konum Tipi</label>
                                <select wire:model.live="filtreKonumTipi" id="filtreKonumTipi" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                    <option value="">Tümü</option>
                                    <option value="Yurt İçi">Yurt İçi</option>
                                    <option value="Yurt Dışı">Yurt Dışı</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 2. GRUP: Proje Durumu & Bekleme Süresi (YENİ EKLENDİ) --}}
                    <div class="mb-5">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <div class="w-1 h-4 bg-pink-500 rounded-full"></div>
                            Proje & Süreç
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="filtreProjeDurumu" class="block text-sm font-medium text-gray-700 mb-1.5">Proje Durumu</label>
                                <select wire:model.live="filtreProjeDurumu" id="filtreProjeDurumu" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                    <option value="">Tümü</option>
                                    <option value="Atandı">Atandı</option>
                                    <option value="Bölüm Onayı Bekliyor">Bölüm Onayı Bekliyor</option>
                                    <option value="Yönetici Onayı Bekliyor">Yönetici Onayı Bekliyor</option>
                                    <option value="Revize Ediliyor">Revize Ediliyor</option>
                                    <option value="Tamamlandı">Tamamlandı</option>
                                    <option value="Reddedildi">Reddedildi</option>
                                </select>
                            </div>
                            <div>
                                <label for="filtreBeklemeMin" class="block text-sm font-medium text-gray-700 mb-1.5">Bekleme (Min Gün)</label>
                                <input type="number" wire:model.live.debounce.500ms="filtreBeklemeMin" id="filtreBeklemeMin" placeholder="Örn: 1" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                            </div>
                            <div>
                                <label for="filtreBeklemeMax" class="block text-sm font-medium text-gray-700 mb-1.5">Bekleme (Max Gün)</label>
                                <input type="number" wire:model.live.debounce.500ms="filtreBeklemeMax" id="filtreBeklemeMax" placeholder="Örn: 30" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
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
                                <label for="filtreTakim" class="block text-sm font-medium text-gray-700 mb-1.5">Çözüm Takımı</label>
                                <select wire:model.live="filtreTakim" id="filtreTakim" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                    <option value="">Tüm Takımlar</option>
                                    @foreach($cozumTakimlari as $takim)
                                        <option value="{{ $takim->id }}">{{ $takim->ad }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="filtreKategori" class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                                <select wire:model.live="filtreKategori" id="filtreKategori" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                    <option value="">Tüm Kategoriler</option>
                                    @foreach($kategoriler as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->ad }}</option>
                                    @endforeach
                                </select>
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
                                <label for="filtreMusteriAdi" class="block text-sm font-medium text-gray-700 mb-1.5">Müşteri Adı</label>
                                <input type="text" wire:model.live.debounce.500ms="filtreMusteriAdi" id="filtreMusteriAdi" placeholder="Müşteri adında ara..." class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                            </div>
                            <div>
                                <label for="filtreEkleyen" class="block text-sm font-medium text-gray-700 mb-1.5">Ekleyen Kişi</label>
                                <select wire:model.live="filtreEkleyen" id="filtreEkleyen" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                    <option value="">Tüm Kullanıcılar</option>
                                    @foreach($ekleyenKullanicilar as $kullanici)
                                        <option value="{{ $kullanici->id }}">{{ $kullanici->name }}</option>
                                    @endforeach
                                </select>
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
                                        <label for="filtreSonTarihBaslangic" class="block text-xs text-gray-500 mb-1">Başlangıç</label>
                                        <input type="date" wire:model.live="filtreSonTarihBaslangic" id="filtreSonTarihBaslangic" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition duration-150 ease-in-out">
                                    </div>
                                    <div>
                                        <label for="filtreSonTarihBitis" class="block text-xs text-gray-500 mb-1">Bitiş</label>
                                        <input type="date" wire:model.live="filtreSonTarihBitis" id="filtreSonTarihBitis" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition duration-150 ease-in-out">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="text-xs font-medium text-gray-600 mb-1">Kayıt Tarihi</div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="filtreKayitTarihBaslangic" class="block text-xs text-gray-500 mb-1">Başlangıç</label>
                                        <input type="date" wire:model.live="filtreKayitTarihBaslangic" id="filtreKayitTarihBaslangic" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition duration-150 ease-in-out">
                                    </div>
                                    <div>
                                        <label for="filtreKayitTarihBitis" class="block text-xs text-gray-500 mb-1">Bitiş</label>
                                        <input type="date" wire:model.live="filtreKayitTarihBitis" id="filtreKayitTarihBitis" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition duration-150 ease-in-out">
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
                                <label for="filtrePuanMin" class="block text-sm font-medium text-gray-700 mb-1.5">Minimum Puan</label>
                                <input type="number" wire:model.live.debounce.500ms="filtrePuanMin" id="filtrePuanMin" placeholder="En az..." min="0" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                            </div>
                            <div>
                                <label for="filtrePuanMax" class="block text-sm font-medium text-gray-700 mb-1.5">Maksimum Puan</label>
                                <input type="number" wire:model.live.debounce.500ms="filtrePuanMax" id="filtrePuanMax" placeholder="En çok..." min="0" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                            </div>
                        </div>
                    </div>

                    {{-- Yükleniyor Göstergesi --}}
                    <div wire:loading class="pt-5 mt-5 border-t border-gray-200 w-full">
                        <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Filtreleniyor...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === 3. ŞİKAYET KARTLARI LİSTESİ === --}}
        <div class="space-y-6">
            @forelse ($sikayetler as $sikayet)
            <div x-data="{ openLogs: false }" class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl transition-all duration-300 relative group overflow-hidden">
                
                {{-- SOL ŞERİT (Durum Rengi) --}}
                @php
                    $statusClass = match($sikayet->musteri_durum) {
                        'Yeni' => 'bg-yellow-400',
                        'İşlemde', 'İnceleniyor' => 'bg-blue-500',
                        'Tamamlandı', 'Çözümlendi', 'Kapatıldı' => 'bg-green-500',
                        'Gecikmiş' => 'bg-red-500',
                        default => 'bg-gray-400'
                    };
                @endphp
                <div class="absolute left-0 top-0 bottom-0 w-2 {{ $statusClass }}"></div>

                <div class="p-5 md:p-6 pl-7"> {{-- Sol şerit için padding --}}
                    
                    {{-- ÜST KISIM --}}
                    <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-5">
                        <div class="flex items-start gap-4 w-full">
                            {{-- LOGO ALANI --}}
                            <div class="flex-shrink-0">
                                @if($sikayet->customer && $sikayet->customer->logo)
                                    <img class="h-16 w-16 rounded-xl object-contain border border-gray-200 shadow-sm bg-white p-1" 
                                         src="{{ asset('storage/' . $sikayet->customer->logo) }}" 
                                         alt="{{ $sikayet->customer->name }}">
                                @else
                                    <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-md">
                                        {{ $sikayet->customer ? strtoupper(substr($sikayet->customer->name, 0, 1)) : strtoupper(substr($sikayet->musteri_adi, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                {{-- Firma İsmi --}}
                                <div class="mb-2">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Firma İsmi:</span>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-xl font-black text-gray-900 leading-tight truncate">
                                            {{ $sikayet->customer ? $sikayet->customer->name : $sikayet->musteri_adi }}
                                        </h2>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            #{{ $sikayet->id }}
                                        </span>
                                    </div>
                                </div>

                                {{-- YETKİLİ KİŞİ BİLGİLERİ --}}
                                <div class="text-sm text-gray-600 mb-4 bg-gray-50 p-2 rounded-lg border border-gray-100 inline-block">
                                    @if($sikayet->yetkili_user)
                                        <div class="font-bold text-gray-800 mb-1 flex items-center gap-1">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            {{ $sikayet->yetkili_user->name }}
                                        </div>
                                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                                            @if($sikayet->yetkili_user->telefon)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                                {{ $sikayet->yetkili_user->telefon }}
                                            </span>
                                            @endif
                                            @if($sikayet->yetkili_user->email)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                                {{ $sikayet->yetkili_user->email }}
                                            </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="italic text-gray-500">{{ $sikayet->musteri_iletisim ?? 'İletişim bilgisi yok' }}</span>
                                    @endif
                                </div>

                                {{-- Şikayet Konusu --}}
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Şikayet Konusu:</span>
                                    <h3 class="text-base font-bold text-indigo-700 leading-snug">
                                        {{ $sikayet->musteri_sikayet_konusu }}
                                    </h3>
                                </div>
                            </div>
                        </div>

                        {{-- Durum Badge --}}
                        <div class="flex-shrink-0">
                            {!! $sikayet->musteri_durum_badge !!}
                        </div>
                    </div>

                    {{-- ORTA KISIM: DETAY GRID --}}
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 text-sm mb-5">
                        
                        {{-- 1. Kategori --}}
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Kategori</span>
                            <div class="font-bold text-gray-700">{{ $sikayet->sikayetKategori->ad ?? 'Genel' }}</div>
                        </div>

                        {{-- 2. Alt Kategori --}}
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Alt Kategori</span>
                            <div class="font-medium text-gray-600">{{ $sikayet->sikayetAltKategori->ad ?? 'N/A' }}</div>
                        </div>

                        {{-- 3. Takım --}}
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Takım</span>
                            <div class="font-medium text-gray-700">{{ $sikayet->cozumTakimi->ad ?? 'Atanmadı' }}</div>
                        </div>

                        {{-- 4. Öncelik --}}
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Öncelik</span>
                            @php
                                $oncelikClass = match($sikayet->musteri_oncelik) {
                                    'Acil' => 'text-red-600 bg-red-50 border-red-100',
                                    'Yüksek' => 'text-orange-600 bg-orange-50 border-orange-100',
                                    'Normal' => 'text-blue-600 bg-blue-50 border-blue-100',
                                    'Düşük' => 'text-green-600 bg-green-50 border-green-100',
                                    default => 'text-gray-600 bg-gray-100 border-gray-200'
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded border text-xs font-bold {{ $oncelikClass }}">
                                @if($sikayet->musteri_oncelik == 'Acil') 🔥 @endif
                                {{ $sikayet->musteri_oncelik }}
                            </span>
                        </div>

                        {{-- 5. Konum --}}
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Konum</span>
                            <div class="font-medium text-gray-700">{{ $sikayet->konum_tipi ?? 'Belirtilmedi' }}</div>
                        </div>

                        {{-- 6. Tarihler --}}
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Tarihler</span>
                            <div class="font-medium text-gray-700 text-xs mb-1">
                                <span class="text-gray-400">Kayıt:</span> {{ $sikayet->created_at->format('d.m.Y') }}
                            </div>
                            <div class="font-bold text-xs text-red-600">
                                <span class="text-red-400">Son:</span> 
                                {{ $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A' }}
                            </div>
                        </div>

                        {{-- 7. Ekleyen --}}
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Ekleyen</span>
                            @if($sikayet->olusturanKurulUyesi)
                                <a href="{{ route('profile.show', $sikayet->olusturanKurulUyesi->id) }}" target="_blank" class="text-indigo-600 font-bold hover:underline flex items-center gap-1">
                                    {{ Str::limit($sikayet->olusturanKurulUyesi->name, 15) }}
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            @else
                                <span class="text-gray-600">Sistem</span>
                            @endif
                        </div>

                        {{-- 8. Puan --}}
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Puan</span>
                            <div class="font-bold flex items-center gap-1 {{ $sikayet->musteri_puan ? 'text-yellow-600' : 'text-gray-400' }}">
                                @if($sikayet->musteri_puan) <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg> @endif
                                {{ $sikayet->musteri_puan ?? 'N/A' }}
                            </div>
                        </div>

                    </div>

                    {{-- EK BİLGİLER: PROJE DURUMU VE FEEDBACK --}}
                    @if($sikayet->iaaProjesi || $sikayet->musteri_feedback || !$sikayet->iaaProjesi)
                        <div class="sm:ml-14 mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-x-4 gap-y-3 text-sm bg-gray-50/70 rounded-lg p-3 border border-gray-200/60 mb-5">
                            
                            {{-- PROJE DURUMU & SÜRE HESABI --}}
                            <div class="flex flex-wrap items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span class="font-medium mr-1">Proje Durumu:</span>

                                @if($sikayet->iaaProjesi)
                                    {{-- 1. DURUM ETİKETİ --}}
                                    @php
                                        $pDurum = $sikayet->iaaProjesi->durum;
                                        $pRenk = match($pDurum) {
                                            'Bölüm Onayı Bekliyor' => 'purple',
                                            'Yönetici Onayı Bekliyor', 'Atandı' => 'blue',
                                            'Revize Ediliyor' => 'orange',
                                            'Tamamlandı' => 'green',
                                            'Reddedildi', 'Tamamlanması Reddedildi' => 'red',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-{{ $pRenk }}-50 text-{{ $pRenk }}-700 border border-{{ $pRenk }}-200">
                                        {{ $pDurum }}
                                    </span>

                                    {{-- 2. SÜRE HESABI (TAMAMLANDI MI DEVAM MI EDİYOR?) --}}
                                    @if($pDurum == 'Tamamlandı')
                                        {{-- BİTTİ: Kaç günde bittiğini göster --}}
                                        @php
                                            // Projenin bittiği tarih (updated_at veya özel bir bitiş kolonu varsa o)
                                            $bitisTarihi = $sikayet->iaaProjesi->updated_at; 
                                            $gecenGun = ceil($sikayet->created_at->diffInDays($bitisTarihi));
                                            if($gecenGun == 0) $gecenGun = 1; // Aynı gün bittiyse 1 yazsın
                                        @endphp
                                        <span class="text-xs font-bold text-green-600 flex items-center gap-1 ml-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            ({{ $gecenGun }} Günde Çözüldü)
                                        </span>
                                    @else
                                        {{-- DEVAM EDİYOR: Kaç gündür sürdüğünü göster --}}
                                        @php
                                            $gecenGun = ceil($sikayet->created_at->diffInDays(now()));
                                        @endphp
                                        <span class="text-xs font-bold text-red-500 flex items-center gap-1 ml-1 animate-pulse">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            ({{ $gecenGun }} Gündür Sürüyor)
                                        </span>
                                    @endif

                                @else
                                    {{-- HİÇ PROJE YOKSA: Sadece Bekleme Süresi --}}
                                    @php
                                        $gecenGun = ceil($sikayet->created_at->diffInDays(now()));
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-red-50 text-red-700 border border-red-200 animate-pulse">
                                        Çözüm Bekliyor ({{ $gecenGun }} Gündür)
                                    </span>
                                @endif
                            </div>

                            {{-- MÜŞTERİ GERİ BİLDİRİMİ --}}
                            @if($sikayet->musteri_feedback)
                                @php
                                    $fbRenk = match($sikayet->musteri_feedback) {
                                        'Onaylandı' => 'text-green-600 bg-green-50 border-green-100',
                                        'Reddedildi' => 'text-red-600 bg-red-50 border-red-100',
                                        'Revizyon İstendi' => 'text-amber-600 bg-amber-50 border-amber-100',
                                        default => 'text-gray-600 bg-gray-50 border-gray-100'
                                    };
                                    $fbLog = $sikayet->loglar->where('eylem', 'Müşteri Geri Bildirimi')->sortByDesc('created_at')->first();
                                    $fbTarih = $fbLog ? $fbLog->created_at->format('d.m.Y H:i') : $sikayet->updated_at->format('d.m.Y H:i');
                                @endphp
                                <div class="mt-2 flex items-start gap-2 p-2 rounded-lg border {{ $fbRenk }}">
                                    <div class="mt-0.5 flex-shrink-0">
                                        @if($sikayet->musteri_feedback == 'Onaylandı') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($sikayet->musteri_feedback == 'Reddedildi') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @else <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center w-full">
                                            <span class="text-xs font-bold uppercase">Müşteri: {{ $sikayet->musteri_feedback }}</span>
                                            <span class="text-[10px] opacity-60 font-medium ml-2 whitespace-nowrap">{{ $fbTarih }}</span>
                                        </div>
                                        @if($sikayet->musteri_feedback_note)
                                            <p class="text-xs mt-0.5 italic opacity-90 truncate" title="{{ $sikayet->musteri_feedback_note }}">"{{ $sikayet->musteri_feedback_note }}"</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- ALT KISIM: BUTONLAR --}}
                    <div class="flex flex-wrap items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button @click="openLogs = !openLogs" class="text-xs font-bold text-gray-500 hover:text-indigo-600 bg-gray-50 hover:bg-indigo-50 px-3 py-2 rounded-lg transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span x-text="openLogs ? 'Geçmişi Gizle' : 'Geçmişi Gör'"></span>
                        </button>
                        <div class="flex-grow"></div>
                        @role('Superadmin|Müşteri Şikayeti Kurulu')
                            <button wire:click="$dispatch('openTriyajModal', { id: {{ $sikayet->id }} })" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Yönet
                            </button>
                        @endrole
                        <a href="{{ route('admin.sikayetler.show', $sikayet) }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all">Detay</a>
                        @can('update', $sikayet)
                            <a href="{{ route('admin.sikayetler.edit', $sikayet) }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all">Düzenle</a>
                        @endcan
                        @can('delete', $sikayet)
                            <button wire:click="delete({{ $sikayet->id }})" wire:confirm="Silmek istediğinize emin misiniz?" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition-all">Sil</button>
                        @endcan
                        @if($sikayet->iaa_id)
                            <a href="{{ route('proje.workspace.show', $sikayet->iaa_id) }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-all">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg> Projeye Git
                            </a>
                        @endif
                    </div>

                    {{-- LOGLAR --}}
                    <div x-show="openLogs" x-transition class="mt-4 pt-4 border-t border-gray-100 bg-gray-50/50 -mx-6 -mb-6 p-6">
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">İşlem Geçmişi</h4>
                        <div class="space-y-3">
                            @forelse($sikayet->loglar as $log)
                                <div class="flex gap-3 text-sm">
                                    <div class="text-xs font-mono text-gray-400 w-24 flex-shrink-0">{{ $log->created_at->format('d.m H:i') }}</div>
                                    <div class="text-gray-700">{{ $log->aciklama }}</div>
                                </div>
                            @empty
                                <div class="text-gray-400 italic text-xs">Henüz bir işlem kaydı yok.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Şikayet bulunamadı</h3>
                <p class="mt-1 text-sm text-gray-500">Arama kriterlerinizi değiştirin veya yeni bir şikayet oluşturun.</p>
            </div>
            @endforelse
        </div>

        {{-- SAYFALAMA --}}
        <div class="mt-6">
            {{ $sikayetler->links() }}
        </div>
    </div>
</div>
    
    @livewire('admin.sikayet-triyaj-modal')

    {{-- CSS Animasyonları --}}
    <style>
        @keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in 0.5s ease-out forwards; }
        @keyframes slide-in { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .animate-slide-in { animation: slide-in 0.4s ease-out forwards; }
        @keyframes slide-up { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slide-up 0.3s ease-out forwards; }
        @keyframes pulse { 50% { opacity: .5; } }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>

</div>