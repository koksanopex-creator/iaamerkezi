<div>
    {{-- ANA SEÇİM ALANI --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
        
        {{-- 1. FİRMA SEÇİMİ --}}
        <div class="group">
            <label class="flex items-center font-bold text-sm text-gray-700 mb-2">
                <div class="p-1.5 bg-indigo-100 rounded-md text-indigo-600 mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                Firma Seçimi <span class="ml-1 text-red-500">*</span>
            </label>
            <div class="flex gap-2">
                <div class="relative w-full">
                    <select wire:model.live="selectedCustomerId" name="customer_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 pl-4 pr-10 text-sm">
                        <option value="">-- Firma Seçiniz --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" wire:click="$set('showCreateModal', true)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 flex items-center justify-center" title="Yeni Firma Ekle">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>
        </div>

        {{-- 2. YETKİLİ SEÇİMİ --}}
        <div class="group">
            <label class="flex items-center font-bold text-sm text-gray-700 mb-2">
                <div class="p-1.5 bg-green-100 rounded-md text-green-600 mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                Yetkili Kişi
            </label>
            <select wire:model.live="selectedRepId" name="yetkili_user_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 py-2.5 pl-4 pr-10 text-sm bg-white disabled:bg-gray-100 disabled:text-gray-400" @if(empty($representatives)) disabled @endif>
                <option value="">-- @if(empty($representatives)) Önce Firma Seçiniz @else Yetkili Seçiniz @endif --</option>
                @foreach($representatives as $rep)
                    <option value="{{ $rep->id }}">{{ $rep->name }} ({{ $rep->unvan ?? 'Yetkili' }})</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- MESAJ --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r text-emerald-700 text-sm flex items-center animate-fadeIn">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- HIZLI EKLEME MODALI --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showCreateModal', false)"></div>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600 mr-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            Hızlı Müşteri Ekle
                        </h3>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 space-y-6">
                        
                        {{-- BÖLÜM 1: FİRMA BİLGİLERİ --}}
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b pb-1">Firma Bilgileri</h4>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Firma Adı <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Örn: ABC Lojistik A.Ş.">
                                @error('name') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Vergi No</label>
                                    <input type="text" wire:model="tax_number" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Konum</label>
                                    <select wire:model="location_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="Yurt İçi">Yurt İçi</option>
                                        <option value="Yurt Dışı">Yurt Dışı</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- BÖLÜM 2: YETKİLİ KİŞİLER (DİNAMİK LİSTE) --}}
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-4">
                            <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Yetkili Kişiler
                                </h4>
                                <button wire:click="addRepRow" type="button" class="text-xs flex items-center text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Yeni Kişi Ekle
                                </button>
                            </div>

                            <div class="space-y-6">
                                @foreach($reps as $index => $rep)
                                    <div class="relative bg-white p-3 rounded-lg border border-slate-200 shadow-sm transition-all hover:shadow-md">
                                        
                                        {{-- Silme Butonu (Sadece birden fazla satır varsa veya opsiyonel) --}}
                                        @if(count($reps) > 1)
                                            <button type="button" wire:click="removeRepRow({{ $index }})" type="button" class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1 hover:bg-red-200 shadow-sm" title="Bu kişiyi sil">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        @endif

                                        <div class="grid grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Ad Soyad <span class="text-red-500">*</span></label>
                                                {{-- Dizi İndeksine Göre Model Bağlama --}}
                                                <input type="text" wire:model="reps.{{ $index }}.name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Ad Soyad">
                                                @error("reps.{$index}.name") <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Ünvan</label>
                                                <input type="text" wire:model="reps.{{ $index }}.title" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Örn: Müdür">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">E-posta <span class="text-red-500">*</span></label>
                                                <input type="email" wire:model="reps.{{ $index }}.email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="mail@ornek.com">
                                                @error("reps.{$index}.email") <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Telefon</label>
                                                <input type="tel" wire:model="reps.{{ $index }}.phone" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="05...">
                                            </div>
                                        </div>
                                        
                                        {{-- Satır Numarası (Görsel Güzellik) --}}
                                        <div class="absolute -left-2 top-1/2 transform -translate-y-1/2">
                                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-slate-500 text-[10px] font-bold">
                                                {{ $loop->iteration }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- Alt Ekleme Butonu (Alternatif Yer) --}}
                            <div class="text-center mt-2">
                                <button wire:click="addRepRow" type="button" class="text-xs text-indigo-600 hover:underline">+ Başka bir yetkili daha ekle</button>
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="button" wire:click="storeNewCustomer" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-indigo-800 shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                            Kaydet
                        </button>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="w-full sm:w-auto px-6 py-2.5 bg-white text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 shadow-sm transition-all duration-200">
                            İptal
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- FORM SUBMIT OLDUĞUNDA GİDECEK GİZLİ VERİLER --}}
        {{-- Bu inputlar olmadan ana form seçilen firmayı göremez! --}}
        
        <input type="hidden" name="customer_id" value="{{ $selectedCustomerId }}">
        <input type="hidden" name="yetkili_user_id" value="{{ $selectedRepId }}">
    
    </div> {{-- Ana div kapanışı --}}
</div>