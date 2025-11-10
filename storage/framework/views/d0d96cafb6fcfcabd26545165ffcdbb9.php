<div> 

    
    <div class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 min-h-screen p-4 md:p-6">
        <div class="max-w-7xl mx-auto">

        
        <div class="mb-8">
            <div class="rounded-2xl border border-gray-200/70 bg-white/80 backdrop-blur p-6 shadow-sm">
                <div class="flex flex-col gap-6 sm:gap-4 lg:flex-row lg:items-center lg:justify-between">
                    
                    <div class="max-w-2xl">
                        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">
                            <span class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-600 bg-clip-text text-transparent">
                                Müşteri Şikayetleri
                            </span>
                        </h1>
                        <p class="text-gray-600 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Gelen şikayetleri yönetin ve çözüme kavuşturun
                        </p>
                    </div>

                    
                    <div class="w-full lg:w-auto">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-row gap-3">
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\MusteriSikayeti::class)): ?>
                                <a href="<?php echo e(route('admin.sikayetler.create')); ?>"
                                   class="inline-flex items-center justify-center px-5 py-3 rounded-xl font-semibold text-white
                                          bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700
                                          shadow-sm hover:shadow transition-all duration-200 focus:outline-none focus-visible:ring
                                          focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                   aria-label="Yeni şikayet ekle">
                                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Yeni Şikayet Ekle
                                </a>
                            <?php endif; ?>

                            <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Müşteri Şikayeti Kurulu')): ?>
                                <a href="<?php echo e(route('admin.sikayetler.kurulGirdileri')); ?>"
                                   class="inline-flex items-center justify-center px-5 py-3 rounded-xl font-semibold
                                          text-indigo-700 bg-white border border-indigo-200 hover:border-indigo-300
                                          hover:bg-indigo-50 shadow-sm transition-all duration-200
                                          focus:outline-none focus-visible:ring focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                   aria-label="Kurul girdilerini görüntüle">
                                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Kurul Girdileri
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        


<!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
                <div class="mb-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-xl shadow-sm animate-slide-in" role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium"><?php echo e(session('success')); ?></span>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-6 bg-gradient-to-r from-blue-50/50 via-indigo-50/50 to-purple-50/50 border-b border-gray-200/70">
                    <div class="text-center group hover:scale-105 transition-transform duration-200">
                        <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Toplam
                        </p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent"><?php echo e($stats['toplam']); ?></p>
                    </div>
                    <div class="text-center group hover:scale-105 transition-transform duration-200">
                        <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Beklemede
                        </p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent"><?php echo e($stats['beklemede']); ?></p>
                    </div>
                    <div class="text-center group hover:scale-105 transition-transform duration-200">
                        <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            İşlemde
                        </p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent"><?php echo e($stats['islemde']); ?></p>
                    </div>
                    <div class="text-center group hover:scale-105 transition-transform duration-200">
                        <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Çözümlenmiş
                        </p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent"><?php echo e($stats['cozulmus']); ?></p>
                    </div>
                </div>

                
                <div class="border-b border-gray-200/70 bg-gradient-to-br from-gray-50/80 to-white" x-data="{ open: false }">
                    <div class="p-4 md:p-6 flex items-center justify-between cursor-pointer hover:bg-gray-50/50 transition-colors duration-150"
                         @click="open = !open">
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

                    
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="px-4 md:px-6 pb-6"
                         style="display: none;" 
                         >

                        
                        <div class="mb-5">
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                                Durum & Öncelik
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
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cozumTakimlari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($takim->id); ?>"><?php echo e($takim->ad); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>

                                <div>
                                    <label for="filtreKategori" class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                                    <select wire:model.live="filtreKategori" id="filtreKategori" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
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
                                    <label for="filtreMusteriAdi" class="block text-sm font-medium text-gray-700 mb-1.5">Müşteri Adı</label>
                                    <input type="text" wire:model.live.debounce.500ms="filtreMusteriAdi" id="filtreMusteriAdi" placeholder="Müşteri adında ara..." class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                </div>

                                <div>
                                    <label for="filtreEkleyen" class="block text-sm font-medium text-gray-700 mb-1.5">Ekleyen Kişi</label>
                                    <select wire:model.live="filtreEkleyen" id="filtreEkleyen" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
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

                        
                        <div wire:loading class="pt-5 mt-5 border-t border-gray-200">
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
                
                <div class="hidden overflow-x-auto">
                    <table class="hidden min-w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b-2 border-gray-200">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Müşteri</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Konu</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ekleyen</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Öncelik</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Takım</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-blue-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-semibold text-sm"><?php echo e(($sikayetler->currentPage() - 1) * $sikayetler->perPage() + $loop->iteration); ?></span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white font-semibold flex-shrink-0"><?php echo e(strtoupper(substr($sikayet->musteri_adi, 0, 1))); ?></div>
                                            <span class="ml-3 text-sm font-medium text-gray-900"><?php echo e($sikayet->musteri_adi); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate"><span class="text-sm text-gray-600" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>"><?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 30)); ?></span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($sikayet->olusturanKurulUyesi->name ?? 'Sistem'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $sikayet->musteri_durum_badge; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($sikayet->oncelik_badge_class); ?>">
                                            <?php echo e($sikayet->musteri_oncelik ?? 'Normal'); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($sikayet->cozumTakimi->ad ?? '—'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($sikayet->created_at->format('d.m.Y')); ?> <span class="block text-xs text-gray-400"><?php echo e($sikayet->created_at->format('H:i')); ?></span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        
                                        <div class="flex justify-end gap-2">
                                            
                                            <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Şikayet Yöneticisi|Müşteri Şikayeti Kurulu')): ?>
                                                <button wire:click="$dispatch('openTriyajModal', { id: <?php echo e($sikayet->id); ?> })" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-green-600 bg-green-50 hover:bg-green-100 transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                    Yönet
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            
                                            <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors duration-200"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> Detay</a>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $sikayet)): ?><a href="<?php echo e(route('admin.sikayetler.edit', $sikayet)); ?>" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors duration-200"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Düzenle</a><?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $sikayet)): ?><form action="<?php echo e(route('admin.sikayetler.destroy', $sikayet)); ?>" method="POST" onsubmit="return confirm('Bu şikayeti silmek istediğinizden emin misiniz?');" class="inline"><button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition-colors duration-200"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Sil</button><?php echo method_field('DELETE'); ?><?php echo csrf_field(); ?></form><?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="9" class="px-6 py-16 text-center"><div class="flex flex-col items-center justify-center"><svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-gray-500 font-medium">Filtre kriterlerine uygun şikayet bulunamadı.</p></div></td></tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4 p-4 md:p-6" wire:loading.class.delay="opacity-50 transition-opacity duration-300">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div x-data="{ openLogs: false }" class="bg-white rounded-xl border border-gray-200/80 shadow-sm hover:shadow-lg transition-all duration-300 p-4 md:p-6 group animate-slide-up">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 gap-3">
                                <div class="flex items-center gap-4">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-sm shadow-md group-hover:shadow-lg transition-shadow duration-200">
                                        #<?php echo e(($sikayetler->currentPage() - 1) * $sikayetler->perPage() + $loop->iteration); ?> 
                                    </span>
                                    <div class="font-semibold text-lg text-gray-900"><?php echo e($sikayet->musteri_adi); ?></div>
                                </div>
                                <?php echo $sikayet->musteri_durum_badge; ?>

                            </div>

                            <p class="text-base text-gray-700 mb-4 sm:ml-14 font-medium" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>">
                                <?php echo e(Str::limit($sikayet->musteri_sikayet_konusu, 60)); ?>

                            </p>

                            <div class="sm:ml-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-3 text-sm bg-gray-50/70 rounded-lg p-3 border border-gray-200/60">
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                    <span class="font-medium">Kategori:</span>
                                    <span class="font-semibold text-gray-800">
                                        <?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?>

                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span class="font-medium">Takım:</span>
                                    <span class="font-semibold text-gray-800"><?php echo e($sikayet->cozumTakimi->ad ?? 'Atanmadı'); ?></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"></path></svg>
                                    <span class="font-medium">Öncelik:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?php echo e($sikayet->oncelik_badge_class); ?>">
                                        <?php echo e($sikayet->musteri_oncelik ?? 'Normal'); ?>

                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-6.998l-6 .75m-.75-7.5l6 .75m6-.75l-6 .75M3 12h18M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                    </svg>
                                    <span class="font-medium">Konum:</span>
                                    <span class="font-semibold text-gray-800">
                                        <?php echo e($sikayet->konum_tipi ?? 'N/A'); ?>

                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="font-medium">Ekleyen:</span>
                                    <span class="font-semibold text-gray-800"><?php echo e($sikayet->olusturanKurulUyesi->name ?? 'Sistem'); ?></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="font-medium">Kayıt Tarihi:</span>
                                    <span class="font-semibold text-gray-800"><?php echo e($sikayet->created_at->format('d.m.Y')); ?></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600 lg:col-span-1">
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="font-medium text-red-600">Son Tarih:</span>
                                    <span class="font-semibold text-red-700">
                                        <?php echo e($sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y H:i') : 'N/A'); ?>

                                    </span>
                                </div>
                                 <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                         <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                     <span class="font-medium">Puan:</span>
                                     <span class="font-bold text-yellow-700">
                                          <?php echo e($sikayet->musteri_puan ? number_format($sikayet->musteri_puan, 0) : 'N/A'); ?>

                                     </span>
                                </div>
                            </div>

                            
                            <div class="mt-5 flex flex-wrap justify-end gap-2 pt-4 border-t border-gray-200/70">
                                
                                
                                <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Müşteri Şikayeti Kurulu')): ?>
                                    <button wire:click="$dispatch('openTriyajModal', { id: <?php echo e($sikayet->id); ?> })"
                                            class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-green-700 bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 border border-green-200/70 hover:border-green-300 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                        Yönet
                                    </button>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $sikayet)): ?>
                                        <a href="<?php echo e(route('admin.sikayetler.edit', $sikayet)); ?>"
                                           class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-indigo-700 bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 border border-indigo-200/70 hover:border-indigo-300 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Düzenle
                                        </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $sikayet)): ?>
                                        <form action="<?php echo e(route('admin.sikayetler.destroy', $sikayet)); ?>" method="POST"
                                              onsubmit="return confirm('Bu şikayeti silmek istediğinizden emin misiniz?');" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-red-700 bg-gradient-to-r from-red-50 to-rose-50 hover:from-red-100 hover:to-rose-100 border border-red-200/70 hover:border-red-300 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Sil
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                
                                <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>"
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-blue-700 bg-gradient-to-r from-blue-50 to-cyan-50 hover:from-blue-100 hover:to-cyan-100 border border-blue-200/70 hover:border-blue-300 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detay
                                </a>

                                
                                <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Müşteri Şikayeti Kurulu')): ?>
                                <button @click="openLogs = !openLogs"
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold border transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md"
                                   :class="openLogs ? 'bg-gray-200 text-gray-800 border-gray-300' : 'bg-gray-50 text-gray-700 border-gray-200/70'">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    <span x-text="openLogs ? 'Kayıtları Kapat' : 'Kayıtları Gör'">Kayıtları Gör</span>
                                </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                

                                
                                <!--[if BLOCK]><![endif]--><?php if($sikayet->iaa_id): ?>
                                    <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaa_id)); ?>"
                                       class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-purple-700 bg-gradient-to-r from-purple-50 to-violet-50 hover:from-purple-100 hover:to-violet-100 border border-purple-200/70 hover:border-purple-300 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Projeye Git
                                    </a>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                
                             
                            </div>

                            
                            <div x-show="openLogs" x-transition
                                 class="sm:ml-14 mt-4 pt-4 border-t border-gray-200" style="display: none;">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Yönetim Değişiklik Kayıtları</h4>
                                <div class="flow-root max-h-48 overflow-y-auto pr-2">
                                    <ul role="list" class="-mb-4">
                                        <!--[if BLOCK]><![endif]--><?php $__empty_2 = true; $__currentLoopData = $sikayet->loglar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <li>
                                                <div class="relative pb-4">
                                                    <!--[if BLOCK]><![endif]--><?php if(!$loop->last): ?>
                                                        <span class="absolute top-4 left-3 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    <div class="relative flex space-x-3">
                                                        <div>
                                                            <span class="h-6 w-6 rounded-full bg-gray-100 flex items-center justify-center ring-4 ring-white">
                                                                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="min-w-0 flex-1 pt-0.5">
                                                            <p class="text-xs text-gray-500">
                                                                <?php echo e($log->aciklama ?? 'Kayıt bulunamadı.'); ?>

                                                            </p>
                                                            <p class="mt-0.5 text-xs text-gray-400">
                                                                <?php echo e($log->created_at->format('d.m.Y H:i')); ?>

                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                            <li>
                                                <p class="text-sm text-gray-500">Bu şikayet için (atama/puanlama) değişikliği kaydı bulunamadı.</p>
                                            </li>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </ul>
                                </div>
                            </div>
                            
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-16 text-center">
                           <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-slate-100 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-gray-600 font-semibold text-lg">
                                    <!--[if BLOCK]><![endif]--><?php if(Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri'])): ?>
                                        Filtre kriterlerine uygun şikayet bulunamadı.
                                    <?php else: ?>
                                        Size atanmış bir şikayet projesi bulunmamaktadır.
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </p>
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="px-6 py-4 border-t border-gray-200/70 bg-gray-50/50">
                    <?php echo e($sikayetler->links()); ?>

                </div>
            </div>
        </div>
    </div>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.sikayet-triyaj-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-1186699771-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    
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

</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/sikayetler-tablosu.blade.php ENDPATH**/ ?>