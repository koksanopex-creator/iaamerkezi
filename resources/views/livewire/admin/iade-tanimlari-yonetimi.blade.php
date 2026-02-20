<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- BAŞLIK VE AÇIKLAMA --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">İade Parametreleri</h2>
                <p class="mt-1 text-sm text-gray-500">Bölümlere özel iade seçeneklerini (ürün, sebep, birim) buradan yönetebilirsiniz.</p>
            </div>
            
            {{-- BÖLÜM SEÇİMİ (Sağ Üstte Modern Dropdown) --}}
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Yönetilecek Bölüm</label>
                <div class="relative">
                    <select wire:model.live="secilenBolumId" class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-xl shadow-sm transition ease-in-out duration-150">
                        <option value="">-- Bölüm Seçiniz --</option>
                        @foreach($bolumler as $bolum)
                            <option value="{{ $bolum->id }}">{{ $bolum->ad }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ALERT MESAJLARI --}}
        @if (session()->has('success'))
            <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200 flex items-center gap-3">
                <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if($secilenBolumId)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- 1. KART: ÜRÜN GRUPLARI --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-blue-50 to-white px-6 py-4 border-b border-blue-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-blue-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Ürün Grupları
                        </h3>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ count($tanimlar['urun_grubu'] ?? []) }}</span>
                    </div>
                    
                    <div class="p-6">
                        {{-- EKLEME FORMU --}}
                        <div class="flex gap-2 mb-6">
                            <input type="text" wire:model="yeniTanim.urun_grubu" wire:keydown.enter="kaydet('urun_grubu')" placeholder="Örn: 35gr T.off" class="flex-1 text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
                            <button wire:click="kaydet('urun_grubu')" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg transition-colors shadow-sm" title="Ekle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        @error('yeniTanim.urun_grubu') <span class="text-red-500 text-xs block -mt-4 mb-4">{{ $message }}</span> @enderror

                        {{-- LİSTE --}}
                        <ul class="space-y-2 max-h-[400px] overflow-y-auto custom-scrollbar pr-1">
                            @forelse($tanimlar['urun_grubu'] ?? [] as $item)
                                <li class="group flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100 hover:bg-blue-50 hover:border-blue-200 transition-all duration-200">
                                    
                                    @if($duzenlenenId === $item->id)
                                        {{-- DÜZENLEME MODU --}}
                                        <div class="flex items-center gap-2 w-full">
                                            <input type="text" wire:model="duzenlenenDeger" class="w-full text-sm rounded-md border-blue-300 focus:ring-blue-500 focus:border-blue-500 py-1">
                                            <button wire:click="guncelle" class="text-green-600 hover:text-green-800"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button>
                                            <button wire:click="iptalEt" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                        </div>
                                    @else
                                        {{-- GÖSTERİM MODU --}}
                                        <span class="text-sm text-gray-700 font-medium">{{ $item->deger }}</span>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button wire:click="duzenle({{ $item->id }})" class="text-gray-400 hover:text-indigo-600 transition-colors" title="Düzenle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                            <button wire:click="sil({{ $item->id }})" wire:confirm="Bu tanımı silmek istediğinize emin misiniz?" class="text-gray-400 hover:text-red-600 transition-colors" title="Sil">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    @endif
                                </li>
                            @empty
                                <li class="text-center py-4 text-sm text-gray-400 italic bg-gray-50 rounded-lg border border-dashed border-gray-200">Kayıt bulunamadı.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- 2. KART: İADE SEBEPLERİ --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-red-50 to-white px-6 py-4 border-b border-red-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-red-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            İade Sebepleri
                        </h3>
                        <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ count($tanimlar['iade_sebebi'] ?? []) }}</span>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex gap-2 mb-6">
                            <input type="text" wire:model="yeniTanim.iade_sebebi" wire:keydown.enter="kaydet('iade_sebebi')" placeholder="Örn: Ovallik, Leke" class="flex-1 text-sm rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 placeholder-gray-400">
                            <button wire:click="kaydet('iade_sebebi')" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg transition-colors shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        @error('yeniTanim.iade_sebebi') <span class="text-red-500 text-xs block -mt-4 mb-4">{{ $message }}</span> @enderror

                        <ul class="space-y-2 max-h-[400px] overflow-y-auto custom-scrollbar pr-1">
                            @forelse($tanimlar['iade_sebebi'] ?? [] as $item)
                                <li class="group flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100 hover:bg-red-50 hover:border-red-200 transition-all duration-200">
                                    @if($duzenlenenId === $item->id)
                                        <div class="flex items-center gap-2 w-full">
                                            <input type="text" wire:model="duzenlenenDeger" class="w-full text-sm rounded-md border-red-300 focus:ring-red-500 focus:border-red-500 py-1">
                                            <button wire:click="guncelle" class="text-green-600 hover:text-green-800"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button>
                                            <button wire:click="iptalEt" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-700 font-medium">{{ $item->deger }}</span>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button wire:click="duzenle({{ $item->id }})" class="text-gray-400 hover:text-indigo-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                            <button wire:click="sil({{ $item->id }})" wire:confirm="Silmek istediğinize emin misiniz?" class="text-gray-400 hover:text-red-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                        </div>
                                    @endif
                                </li>
                            @empty
                                <li class="text-center py-4 text-sm text-gray-400 italic bg-gray-50 rounded-lg border border-dashed border-gray-200">Kayıt bulunamadı.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- 3. KART: BİRİMLER --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-green-50 to-white px-6 py-4 border-b border-green-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-green-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            Birimler
                        </h3>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ count($tanimlar['birim'] ?? []) }}</span>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex gap-2 mb-6">
                            <input type="text" wire:model="yeniTanim.birim" wire:keydown.enter="kaydet('birim')" placeholder="Örn: Ton, Kg" class="flex-1 text-sm rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 placeholder-gray-400">
                            <button wire:click="kaydet('birim')" class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-lg transition-colors shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        @error('yeniTanim.birim') <span class="text-red-500 text-xs block -mt-4 mb-4">{{ $message }}</span> @enderror

                        <ul class="space-y-2 max-h-[400px] overflow-y-auto custom-scrollbar pr-1">
                            @forelse($tanimlar['birim'] ?? [] as $item)
                                <li class="group flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100 hover:bg-green-50 hover:border-green-200 transition-all duration-200">
                                    @if($duzenlenenId === $item->id)
                                        <div class="flex items-center gap-2 w-full">
                                            <input type="text" wire:model="duzenlenenDeger" class="w-full text-sm rounded-md border-green-300 focus:ring-green-500 focus:border-green-500 py-1">
                                            <button wire:click="guncelle" class="text-green-600 hover:text-green-800"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button>
                                            <button wire:click="iptalEt" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-700 font-medium">{{ $item->deger }}</span>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button wire:click="duzenle({{ $item->id }})" class="text-gray-400 hover:text-indigo-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                            <button wire:click="sil({{ $item->id }})" wire:confirm="Silmek istediğinize emin misiniz?" class="text-gray-400 hover:text-red-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                        </div>
                                    @endif
                                </li>
                            @empty
                                <li class="text-center py-4 text-sm text-gray-400 italic bg-gray-50 rounded-lg border border-dashed border-gray-200">Kayıt bulunamadı.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        @else
            <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-200 border-dashed">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Yönetilecek Bölümü Seçin</h3>
                <p class="mt-1 text-sm text-gray-500">Üst kısımdaki açılır menüden bir bölüm seçerek parametreleri düzenlemeye başlayabilirsiniz.</p>
            </div>
        @endif
    </div>
</div>