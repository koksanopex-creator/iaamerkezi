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

        
        <div class="mb-5 mt-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                Durum & Öncelik & Konum
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="filtreDurum" class="block text-sm font-medium text-gray-700 mb-1.5">Durum</label>
                    <select wire:model.live="filtreDurum" id="filtreDurum"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                        <option value="">Tüm Durumlar</option>
                        <option value="Yeni">Yeni</option>
                        <option value="İşlemde">İşlemde</option>
                        <option value="Çözümlendi">Çözümlendi</option>
                        <option value="Kapatıldı">Kapatıldı</option>
                        <option value="Onay Bekleyenler">Onay Bekleyenler</option>
                    </select>
                </div>
                <div>
                    <label for="filtreOncelik" class="block text-sm font-medium text-gray-700 mb-1.5">Öncelik</label>
                    <select wire:model.live="filtreOncelik" id="filtreOncelik"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                        <option value="">Tüm Öncelikler</option>
                        <option value="Acil">Acil</option>
                        <option value="Yüksek">Yüksek</option>
                        <option value="Normal">Normal</option>
                        <option value="Düşük">Düşük</option>
                    </select>
                </div>
                <div>
                    <label for="filtreKonumTipi" class="block text-sm font-medium text-gray-700 mb-1.5">Konum
                        Tipi</label>
                    <select wire:model.live="filtreKonumTipi" id="filtreKonumTipi"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                        <option value="">Tümü</option>
                        <option value="Yurt İçi">Yurt İçi</option>
                        <option value="Yurt Dışı">Yurt Dışı</option>
                    </select>
                </div>
            </div>
        </div>

        
        <div class="mb-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <div class="w-1 h-4 bg-pink-500 rounded-full"></div>
                Proje & Süreç
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="filtreProjeDurumu" class="block text-sm font-medium text-gray-700 mb-1.5">Proje
                        Durumu</label>
                    <select wire:model.live="filtreProjeDurumu" id="filtreProjeDurumu"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                        <option value="">Tümü</option>
                        <option value="Atandı">Atandı</option>
                        <option value="Bölüm Onayı Bekliyor">Bölüm Onayı Bekliyor</option>
                        <option value="Yönetici Onayı Bekliyor">Yönetici Onayı Bekliyor</option>
                        <option value="Revize Ediliyor">Revize Ediliyor</option>
                        <option value="Tamamlandı">Tamamlandı</option>
                        <option value="talep_olarak_kapatildi">Talep Olarak Kapatıldı</option>
                        <option value="hatali_bildirim_olarak_kapatildi">Hatalı Bildirim Olarak Kapatıldı</option>
                        <option value="Reddedildi">Reddedildi</option>
                    </select>
                </div>
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

        
        <div class="mb-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
                Kategorizasyon
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="filtreTakim" class="block text-sm font-medium text-gray-700 mb-1.5">Çözüm Takımı</label>
                    <select wire:model.live="filtreTakim" id="filtreTakim"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                        <option value="">Tüm Takımlar</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cozumTakimlari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($takim->id); ?>"><?php echo e($takim->ad); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                <div>
                    <label for="filtreKategori" class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                    <select wire:model.live="filtreKategori" id="filtreKategori"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                        <option value="">Tüm Kategoriler</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($kategori->id); ?>"><?php echo e($kategori->ad); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
            </div>
        </div>

        
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
                    <label for="filtreEkleyen" class="block text-sm font-medium text-gray-700 mb-1.5">Ekleyen
                        Kişi</label>
                    <select wire:model.live="filtreEkleyen" id="filtreEkleyen"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                        <option value="">Tüm Kullanıcılar</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $ekleyenKullanicilar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kullanici): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($kullanici->id); ?>"><?php echo e($kullanici->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
            </div>
        </div>

        
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
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/sikayetler-partials/filters.blade.php ENDPATH**/ ?>