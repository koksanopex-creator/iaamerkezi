@push('pageTitle')
    Müşteri Yönetimi | 
@endpush

<div class="p-6 bg-gray-50 min-h-screen">
    
    {{-- MODAL 1: FİRMA EKLEME/DÜZENLEME --}}
    @if($showModal)
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" wire:ignore.self>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                        <div class="bg-gray-50 px-6 py-5 border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                @if($isEditMode)
                                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </div>
                                    Müşteriyi Düzenle
                                @else
                                    <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    </div>
                                    Yeni Müşteri Oluştur
                                @endif
                            </h3>
                        </div>

                        <div class="px-6 py-6 space-y-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Firma Adı <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="name" maxlength="150" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" placeholder="Şirket ünvanı">
                                    @error('name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Logo</label>
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0 h-16 w-16 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-center overflow-hidden relative group">
                                            @if ($logo)
                                                <img src="{{ $logo->temporaryUrl() }}" class="h-full w-full object-cover">
                                            @elseif($isEditMode && $customer_id && \App\Models\Customer::find($customer_id)->logo_path)
                                                <img src="{{ asset('storage/'.\App\Models\Customer::find($customer_id)->logo_path) }}" class="h-full w-full object-cover">
                                            @else
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" wire:model="logo" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer transition-colors">
                                            <p class="text-xs text-gray-500 mt-1">PNG, JPG (Max 2MB)</p>
                                        </div>
                                    </div>
                                    @error('logo') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Vergi No</label>
                                        <input type="text" wire:model="tax_number" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" placeholder="10-11 Hane">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Konum</label>
                                        <select wire:model="location_type" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                            <option value="Yurt İçi">Yurt İçi</option>
                                            <option value="Yurt Dışı">Yurt Dışı</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Telefon</label>
                                    <input type="tel" wire:model="phone" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" placeholder="05XXXXXXXXX">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Adres</label>
                                    <textarea wire:model="address" maxlength="500" rows="2" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" placeholder="Açık adres..."></textarea>
                                </div>
                            </div>

                            @if(!$isEditMode)
                                <div class="bg-indigo-50/50 rounded-xl p-5 border border-indigo-100">
                                    <h4 class="text-sm font-bold text-indigo-900 mb-4 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        İlk Yetkili Kişi
                                    </h4>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Ad Soyad *</label>
                                                <input type="text" wire:model="rep_name" maxlength="50" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                                @error('rep_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Ünvan</label>
                                                <input type="text" wire:model="rep_title" maxlength="100" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">E-posta (Giriş İçin) *</label>
                                            <input type="email" wire:model="rep_email" maxlength="100" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                            @error('rep_email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Cep Telefonu</label>
                                            <input type="tel" wire:model="rep_phone" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-indigo-800 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                                {{ $isEditMode ? 'Değişiklikleri Kaydet' : 'Kaydet ve Oluştur' }}
                            </button>
                            <button type="button" wire:click="$set('showModal', false)" class="w-full sm:w-auto px-6 py-2.5 bg-white text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 shadow-sm transition-all duration-200">
                                İptal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL 2: YETKİLİLERİ YÖNET --}}
    @if($showRepModal && $selectedCustomer)
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" wire:ignore.self>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="$set('showRepModal', false)"></div>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                    <div class="bg-white px-6 py-6">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $selectedCustomer->name }}</h3>
                                <p class="text-sm text-gray-500">Yetkili Kişi Yönetimi</p>
                            </div>
                            <button wire:click="$set('showRepModal', false)" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full p-2 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- MODAL İÇİ BİLDİRİM ALANI --}}
                        <div class="mb-6">
                            {{-- SENARYO A: Yeni Yetkili Eklendi ve Şifre Var --}}
                            @if(session('generated_password'))
                                <div class="bg-indigo-900 rounded-xl p-5 shadow-lg border border-indigo-500 relative overflow-hidden animate-fadeIn">
                                    {{-- Arkaplan Süsü --}}
                                    <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 bg-indigo-500 rounded-full opacity-20 blur-xl"></div>
                                    
                                    <div class="flex items-start gap-4 relative z-10">
                                        <div class="bg-green-500 rounded-full p-2 text-white shrink-0 shadow-lg">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div class="flex-1 w-full">
                                            <h4 class="text-lg font-bold text-white mb-1">Yetkili Eklendi!</h4>
                                            <p class="text-indigo-200 text-xs mb-3">
                                                Giriş bilgileri <strong>{{ session('generated_user_email') }}</strong> adresine yollandı.
                                            </p>

                                            {{-- Şifre Gösterim Kutusu --}}
                                            <div class="bg-black/20 rounded-lg p-3 border border-white/10 flex items-center justify-between gap-3">
                                                <div>
                                                    <span class="text-[10px] text-indigo-300 uppercase font-bold block">Geçici Şifre</span>
                                                    <span class="text-xl font-mono font-black text-emerald-400 tracking-widest select-all">
                                                        {{ session('generated_password') }}
                                                    </span>
                                                </div>
                                                <button onclick="navigator.clipboard.writeText('{{ session('generated_password') }}'); this.innerText='Kopyalandı!';" 
                                                        class="text-xs bg-white/10 hover:bg-white/20 text-white px-3 py-2 rounded transition-colors border border-white/10">
                                                    Kopyala
                                                </button>
                                            </div>
                                            
                                            <div class="mt-2 text-[10px] text-yellow-200/70 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Şifreyi şimdi not alınız, kapatınca kaybolur.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            {{-- SENARYO B: Sadece Bilgi Mesajı (Güncelleme/Silme vb.) --}}
                            @elseif (session()->has('rep_message'))
                                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center animate-fadeIn">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ session('rep_message') }}
                                </div>
                            @endif
                        </div>

                        <div class="bg-slate-50 p-6 rounded-2xl mb-8 border border-slate-200 shadow-sm">
                            <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center uppercase tracking-wide">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Yeni Yetkili Ekle
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                <div class="md:col-span-3">
                                    <input type="text" wire:model="new_rep_name" maxlength="50" placeholder="Ad Soyad" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('new_rep_name') <span class="text-red-500 text-xs block mt-1 font-medium">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-3">
                                    <input type="text" wire:model="new_rep_title" maxlength="100" placeholder="Ünvan" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="md:col-span-3">
                                    <input type="email" wire:model="new_rep_email" maxlength="100" placeholder="E-posta" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('new_rep_email') <span class="text-red-500 text-xs block mt-1 font-medium">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-3 flex gap-2">
                                    <input type="tel" wire:model="new_rep_phone" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Telefon" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <button wire:click="addRepresentative" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all duration-200">Ekle</button>
                                </div>
                            </div>
                        </div>

                        <h4 class="text-sm font-bold text-gray-700 mb-4 ml-1 uppercase tracking-wide">Mevcut Yetkililer</h4>
                        <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ad Soyad / Ünvan</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">E-posta</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Telefon</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($selectedCustomer->users as $rep)
                                        <tr class="transition-colors {{ $rep->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                                            @if($editingRepId === $rep->id)
                                                <td class="px-6 py-3 align-top">
                                                    <input type="text" wire:model="edit_rep_name" maxlength="50" class="text-sm rounded-lg border-gray-300 w-full mb-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ad Soyad">
                                                    <input type="text" wire:model="edit_rep_title" maxlength="100" class="text-xs rounded-lg border-gray-300 w-full text-gray-600 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ünvan">
                                                    @error('edit_rep_name') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="px-6 py-3 align-top">
                                                    <input type="email" wire:model="edit_rep_email" maxlength="100" class="text-sm rounded-lg border-gray-300 w-full focus:ring-indigo-500 focus:border-indigo-500" placeholder="E-posta">
                                                    @error('edit_rep_email') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="px-6 py-3 align-top">
                                                    <input type="tel" wire:model="edit_rep_phone" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="text-sm rounded-lg border-gray-300 w-full focus:ring-indigo-500 focus:border-indigo-500" placeholder="Telefon">
                                                </td>
                                                <td class="px-6 py-3 text-right whitespace-nowrap align-top">
                                                    <div class="flex justify-end space-x-2">
                                                        <button wire:click="updateRepresentative" class="text-emerald-600 hover:text-emerald-800 p-2 rounded-lg hover:bg-emerald-50 transition-colors" title="Kaydet">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        </button>
                                                        <button wire:click="cancelEditRepresentative" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors" title="İptal">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            @else
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-bold {{ $rep->trashed() ? 'text-red-900 line-through' : 'text-gray-900' }}">{{ $rep->name }}</div>
                                                    <div class="text-xs text-gray-500 mt-0.5 font-medium">
                                                        {{ $rep->unvan ?? '-' }}
                                                        @if($rep->trashed()) <span class="text-red-600 font-bold ml-1">(Pasif)</span> @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">{{ $rep->email }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $rep->telefon ?? '-' }}</td>
                                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                                    <div class="flex justify-end space-x-2">
                                                        {{-- Aktif/Pasif Toggle Butonu --}}
                                                        <button wire:click="toggleRepresentativeStatus({{ $rep->id }})" 
                                                                wire:confirm="{{ $rep->trashed() ? 'Yetkiliyi tekrar aktif etmek istiyor musunuz?' : 'Yetkiliyi pasife almak istiyor musunuz? Giriş yapamayacak.' }}"
                                                                class="p-2 rounded-lg transition-colors {{ $rep->trashed() ? 'text-green-600 hover:bg-green-50' : 'text-gray-500 hover:bg-gray-100' }}" 
                                                                title="{{ $rep->trashed() ? 'Aktifleştir' : 'Pasife Al' }}">
                                                            @if($rep->trashed())
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                            @else
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                            @endif
                                                        </button>

                                                        @if(!$rep->trashed())
                                                            <button wire:click="editRepresentative({{ $rep->id }})" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-lg hover:bg-indigo-50 transition-colors" title="Düzenle">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                            </button>
                                                            
                                                            @if(!auth()->user()->hasRole('Müşteri Şikayeti Kurulu'))
                                                                <button wire:click="deleteRepresentative({{ $rep->id }})" wire:confirm="Bu yetkiliyi silmek istediğinize emin misiniz?" class="text-rose-600 hover:text-rose-900 p-2 rounded-lg hover:bg-rose-50 transition-colors" title="Sil">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                                Henüz yetkili atanmamış.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse border-t border-gray-100">
                        <button type="button" wire:click="$set('showRepModal', false)" class="w-full sm:w-auto px-6 py-2.5 bg-white text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 shadow-sm transition-all duration-200">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL 3: AKTİF/PASİF DURUM DEĞİŞİKLİĞİ (DÜZELTİLEN KISIM) --}}
    @if($showStatusModal && $targetCustomer)
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" wire:ignore.self>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showStatusModal', false)"></div>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <div class="bg-white px-6 py-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                            @if($targetCustomer->is_active)
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Firmayı Pasife Al
                            @else
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Firmayı Aktif Et
                            @endif
                        </h3>
                        
                        @if($targetCustomer->is_active)
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4 text-sm text-orange-800">
                                Bu firma ile çalışmayı durduruyorsunuz. Lütfen aşağıya bunun sebebini detaylıca yazınız.
                            </div>
                        @else
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 mb-4 text-sm text-emerald-800">
                                Bu firma ile tekrar çalışmaya başlıyorsunuz. Firma aktif listeye alınacaktır.
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Açıklama / Sebep</label>
                            <textarea wire:model="statusReason" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" placeholder="Örn: Ödeme düzensizliği, müşteri talebi vb..."></textarea>
                            @error('statusReason') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button wire:click="updateStatus" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-md transition-all">
                            Onayla ve Kaydet
                        </button>
                        <button wire:click="$set('showStatusModal', false)" class="w-full sm:w-auto px-4 py-2 bg-white text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-50">
                            İptal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Başlık ve Buton --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 px-2 sm:px-0">
        <div class="text-center md:text-left">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Müşteri Yönetimi</h2>
            <p class="text-sm text-gray-500 mt-1">Firma durumu, şikayet istatistikleri ve yetkili kişi işlemleri.</p>
        </div>
        @if($isAdmin)
        <button wire:click="create" class="w-full md:w-auto group px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 shadow-lg hover:shadow-indigo-500/30 transition-all duration-200 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="font-semibold">Yeni Müşteri Ekle</span>
        </button>
        @endif
    </div>{{-- End of Header and Button --}}

    {{-- ISTATISTIK KARTLARI --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 px-2 sm:px-0">
        {{-- Toplam Müşteri --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 transition-all hover:shadow-md group">
            <div class="p-4 bg-indigo-50 rounded-2xl text-indigo-600 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">TOPLAM MÜŞTERİ</p>
                <h3 class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</h3>
            </div>
        </div>

        {{-- Yurt İçi Müşteri --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 transition-all hover:shadow-md group">
            <div class="p-4 bg-blue-50 rounded-2xl text-blue-600 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2.5M15 21a9 9 0 11-9-9"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">YURT İÇİ</p>
                <h3 class="text-3xl font-black text-blue-600">{{ $stats['domestic'] }}</h3>
            </div>
        </div>

        {{-- Yurt Dışı Müşteri --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 transition-all hover:shadow-md group">
            <div class="p-4 bg-purple-50 rounded-2xl text-purple-600 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">YURT DIŞI</p>
                <h3 class="text-3xl font-black text-purple-600">{{ $stats['international'] }}</h3>
            </div>
        </div>
    </div>
    {{-- TOP 5 ANALIZ PANELI - KATLANABILIR --}}
    <div class="mb-6 px-2 sm:px-0">
        <button wire:click="$toggle('showTopComplaints')" 
                class="w-full flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            
            {{-- Arkaplan Süsü --}}
            <div class="absolute right-0 top-0 h-full w-24 bg-gradient-to-l from-indigo-50/50 to-transparent transition-all group-hover:w-32"></div>
            
            <div class="flex items-center gap-3 relative z-10">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div class="text-left">
                    <h4 class="text-sm font-bold text-gray-800 tracking-tight">EN ÇOK ŞİKAYET ALAN MÜŞTERİ ANALİZİ</h4>
                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-widest">
                        {{ $showTopComplaints ? 'Detayları Kapat' : 'Detayları Görüntülemek İçin Tıklayın' }}
                    </p>
                </div>
            </div>

            <div class="relative z-10">
                <div class="p-1 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-all duration-300 {{ $showTopComplaints ? 'rotate-180' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </button>

        @if($showTopComplaints)
        <div class="mt-4 grid grid-cols-1 lg:grid-cols-4 gap-6 animate-fadeIn" wire:key="top-complaints-expanded">
        <div class="lg:col-span-1 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 h-full">
            <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center uppercase tracking-wide">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                SIRALAMA FİLTRESİ
            </h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Başlangıç Tarihi</label>
                    <input type="date" wire:model.live="topFilterStartDate" class="w-full text-xs rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Bitiş Tarihi</label>
                    <input type="date" wire:model.live="topFilterEndDate" class="w-full text-xs rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <button wire:click="$set('topFilterStartDate', ''); $set('topFilterEndDate', '');" class="w-full text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase text-center mt-2 p-2 hover:bg-indigo-50 rounded-lg transition-all">
                    FİLTREYİ SIFIRLA
                </button>
            </div>
        </div>

        <div class="lg:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col group">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-sm font-bold text-gray-800 flex items-center uppercase tracking-wide">
                    <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    EN ÇOK ŞİKAYET ALAN İLK 5 MÜŞTERİ
                </h4>
                <div class="text-[10px] text-gray-400 font-bold bg-gray-50 px-3 py-1 rounded-full border border-gray-100 flex items-center gap-2">
                    @if($topFilterStartDate || $topFilterEndDate)
                        <span class="flex items-center gap-1 text-orange-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Filtrelenmiş Veri: 
                            {{ $topFilterStartDate ? \Carbon\Carbon::parse($topFilterStartDate)->format('d.m.Y') : '...' }} 
                            - 
                            {{ $topFilterEndDate ? \Carbon\Carbon::parse($topFilterEndDate)->format('d.m.Y') : '...' }}
                            aralığı gösteriliyor
                        </span>
                    @else
                        <span class="text-gray-400">Tüm Zamanlar</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 h-full">
                @forelse($topComplaints as $top)
                    <a href="{{ route('musteri.profil.show', $top->id) }}" class="relative bg-gray-50/50 hover:bg-white rounded-2xl p-4 border border-gray-100 transition-all hover:shadow-lg hover:-translate-y-1 group/item">
                        {{-- Sıralama Rozeti --}}
                        <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-black text-sm shadow-lg z-10">
                            #{{ $loop->iteration }}
                        </div>
                        
                        <div class="flex flex-col items-center text-center">
                            <div class="h-16 w-16 mb-3 relative">
                                @if($top->logo_path)
                                    <img src="{{ asset('storage/'.$top->logo_path) }}" class="h-full w-full rounded-full object-cover border-2 border-white shadow-md">
                                @else
                                    <div class="h-full w-full rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl border-2 border-white shadow-md">
                                        {{ substr($top->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            
                            <h5 class="text-xs font-bold text-gray-900 mb-1 line-clamp-1 h-8 flex items-center justify-center group-hover/item:text-indigo-600 transition-colors">{{ $top->name }}</h5>
                            
                            <div class="mt-2 space-y-1 w-full">
                                <div class="bg-rose-50 text-rose-700 py-1 px-3 rounded-lg text-lg font-black tracking-tight border border-rose-100">
                                    {{ $top->sikayetler_count }}
                                    <span class="text-[8px] uppercase block -mt-1 font-bold">Şikayet</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-5 flex flex-col items-center justify-center py-12 text-gray-400">
                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-xs font-semibold uppercase tracking-widest">Kayıt Bulunamadı</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
        @endif
    </div>

    {{-- FİLTRE VE ARAMA --}}
    <div class="mb-6 flex flex-col md:flex-row gap-4 px-2 sm:px-0">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200 shadow-sm" placeholder="Firma adı, vergi no, il, ilçe...">
        </div>
        
        <div class="flex items-center justify-center md:justify-end gap-2 bg-white px-4 py-2 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs md:text-sm text-gray-500 font-medium whitespace-nowrap">
                Toplam <strong class="text-indigo-600">{{ $customers->total() }}</strong> Kayıt
            </span>
        </div>
    </div>

    {{-- LİSTE GÖRÜNÜMÜ: MOBİL (KARTLAR) --}}
    <div class="grid grid-cols-1 gap-4 md:hidden mb-8">
        @forelse($customers as $customer)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" wire:key="mobile-customer-{{ $customer->id }}">
                {{-- Kart Başlığı --}}
                <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-indigo-600 text-white text-[10px] font-black w-6 h-6 rounded-lg flex items-center justify-center shadow-sm">
                            {{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}
                        </div>
                        <a href="{{ route('musteri.profil.show', $customer->id) }}" class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($customer->logo_path)
                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200" src="{{ asset('storage/'.$customer->logo_path) }}" alt="">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200 uppercase">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900 leading-tight">{{ Str::limit($customer->name, 25) }}</div>
                                <div class="text-[10px] text-gray-500 uppercase tracking-wider mt-0.5">Kayıt: {{ $customer->created_at->format('d.m.Y') }}</div>
                            </div>
                        </a>
                    </div>
                    @if($customer->is_active)
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-800">AKTİF</span>
                    @else
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-800" title="{{ $customer->passive_reason }}">PASİF</span>
                    @endif
                </div>

                {{-- Kart İçeriği --}}
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Vergi Bilgisi</div>
                            <div class="text-xs font-semibold text-gray-800">{{ $customer->tax_number ?? '-' }}</div>
                            <div class="text-[10px] text-gray-500">{{ $customer->tax_office ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Konum</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $customer->location_type == 'Yurt Dışı' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ strtoupper($customer->location_type) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">İletişim</div>
                        <div class="text-xs text-gray-800 mb-1 flex items-center gap-2">
                             <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                             {{ $customer->phone ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500 flex items-center gap-2">
                             <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                             {{ $customer->address ? Str::limit($customer->address, 50) : '-' }}
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-50">
                        <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">
                            📝 {{ $customer->toplam_sikayet ?? 0 }} ŞİKAYET
                        </span>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">
                            ✅ {{ $customer->cozulmus_sikayet ?? 0 }} ÇÖZÜM
                        </span>
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">
                            🚗 {{ $customer->total_visits ?? 0 }} ZİYARET
                        </span>
                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded">
                            ↩️ {{ $customer->total_returns ?? 0 }} İADE
                        </span>
                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                            👥 {{ $customer->representatives_count ?? 0 }} YETKİLİ
                        </span>
                    </div>
                </div>

                {{-- Kart İşlemleri --}}
                <div class="bg-gray-50 px-4 py-3 flex items-center justify-around border-t border-gray-100">
                    @if($isAdmin)
                        @if(auth()->user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu']))
                            <button wire:click.prevent="confirmStatusChange({{ $customer->id }})" class="p-2 rounded-xl {{ $customer->is_active ? 'text-orange-500 bg-orange-50' : 'text-emerald-600 bg-emerald-50' }}" title="Durum Değiştir">
                                @if($customer->is_active)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                @endif
                            </button>
                        @endif

                        <button wire:click.prevent="manageRepresentatives({{ $customer->id }})" class="p-2 text-indigo-600 bg-indigo-50 rounded-xl" title="Yetkililer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </button>
                        
                        <button wire:click.prevent="edit({{ $customer->id }})" class="p-2 text-blue-600 bg-blue-50 rounded-xl" title="Düzenle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        
                        @if(auth()->user()->hasRole('Superadmin'))
                        <button wire:click.prevent="delete({{ $customer->id }})" wire:confirm="Emin misiniz?" class="p-2 text-red-600 bg-red-50 rounded-xl" title="Sil">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm">
                <p class="text-gray-500 text-sm italic">Kayıtlı müşteri bulunamadı.</p>
            </div>
        @endforelse
    </div>

    {{-- LİSTE GÖRÜNÜMÜ: MASAÜSTÜ (TABLO) --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer group" wire:click="sortBy('name')">
                            <div class="flex items-center gap-1">
                                Firma Bilgileri
                                @if($sortField === 'name')
                                    <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                                @else
                                    <span class="opacity-0 group-hover:opacity-50 transition-opacity">↕</span>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer group" wire:click="sortBy('location_type')">
                            <div class="flex items-center gap-1">
                                Vergi / Konum
                                @if($sortField === 'location_type')
                                    <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                                @else
                                    <span class="opacity-0 group-hover:opacity-50 transition-opacity">↕</span>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">İletişim</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">İstatistikler</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer group" wire:click="sortBy('is_active')">
                            <div class="flex items-center gap-1">
                                Durum
                                @if($sortField === 'is_active')
                                    <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                                @else
                                    <span class="opacity-0 group-hover:opacity-50 transition-opacity">↕</span>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-3 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-indigo-50/20 transition-colors duration-150 group" wire:key="customer-{{ $customer->id }}">
                            <td class="px-2 py-4 whitespace-nowrap text-[10px] font-bold text-gray-400 text-center">
                                {{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}
                            </td>
                            {{-- 1. FİRMA --}}
                            <td class="px-2 py-4 whitespace-nowrap">
                                <a href="{{ route('musteri.profil.show', $customer->id) }}" class="flex items-center group/link p-2 -m-2 rounded-xl transition-all duration-200 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-100">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($customer->logo_path)
                                            <img class="h-10 w-10 rounded-full object-cover border border-gray-200 group-hover/link:border-indigo-300 transition-colors" src="{{ asset('storage/'.$customer->logo_path) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200 group-hover/link:bg-indigo-200 transition-colors">
                                                {{ substr($customer->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 group-hover/link:text-indigo-700 transition-colors">{{ $customer->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-widest mt-0.5">Kayıt: {{ $customer->created_at->format('d.m.Y') }}</div>
                                    </div>
                                </a>
                            </td>

                            {{-- 2. VERGİ / KONUM --}}
                            <td class="px-2 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-900 font-semibold">{{ $customer->tax_number ?? '-' }}</span>
                                    <span class="text-xs text-gray-500 mb-1">{{ $customer->tax_office ?? '-' }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold w-fit {{ $customer->location_type == 'Yurt Dışı' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }} uppercase italic">
                                        {{ $customer->location_type }}
                                    </span>
                                </div>
                            </td>

                            {{-- 3. İLETİŞİM --}}
                            <td class="px-2 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">{{ $customer->phone ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $customer->address ? Str::limit($customer->address, 30) : '-' }}</div>
                            </td>

                            {{-- 4. İSTATİSTİKLER --}}
                            <td class="px-2 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-[10px] font-bold text-gray-500 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-lg w-fit transition-all hover:bg-white" title="Toplam Şikayet">
                                        📝 {{ $customer->toplam_sikayet ?? 0 }} ŞİKAYET
                                    </span>
                                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-lg w-fit transition-all hover:bg-white" title="Çözülen Şikayet">
                                        ✅ {{ $customer->cozulmus_sikayet ?? 0 }} ÇÖZÜM
                                    </span>
                                    <span class="text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-lg w-fit transition-all hover:bg-white" title="Toplam Ziyaret">
                                        🚗 {{ $customer->total_visits ?? 0 }} ZİYARET
                                    </span>
                                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-100 px-2.5 py-1 rounded-lg w-fit transition-all hover:bg-white" title="İade İçeren Şikayet">
                                        ↩️ {{ $customer->total_returns ?? 0 }} İADE
                                    </span>
                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg w-fit transition-all hover:bg-white" title="Yetkili Sayısı">
                                        👥 {{ $customer->representatives_count ?? 0 }} YETKİLİ
                                    </span>
                                </div>
                            </td>

                            {{-- 5. DURUM --}}
                            <td class="px-2 py-4 whitespace-nowrap">
                                @if($customer->is_active)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 shadow-sm border border-green-200">
                                        AKTİF
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 shadow-sm border border-red-200 cursor-help" title="{{ $customer->passive_reason }}">
                                        PASİF
                                    </span>
                                @endif
                            </td>

                            {{-- 6. İŞLEMLER --}}
                            <td class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($isAdmin)
                                <div>
                                    <div class="flex justify-end items-center gap-1.5">
                                        
                                        {{-- DURUM DEĞİŞTİRME BUTONU (Yetkiliye Özel) --}}
                                        @if(auth()->user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu']))
                                            <button type="button" wire:click.prevent="confirmStatusChange({{ $customer->id }})" 
                                                    class="p-2 rounded-xl transition-all duration-200 {{ $customer->is_active ? 'text-orange-400 hover:bg-orange-50 hover:text-orange-600' : 'text-emerald-500 hover:bg-emerald-50 hover:text-emerald-700' }}"
                                                    title="{{ $customer->is_active ? 'Pasife Al / Çalışmayı Durdur' : 'Aktif Et / Çalışmaya Başla' }}">
                                                @if($customer->is_active)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                @endif
                                            </button>
                                        @endif

                                        <button type="button" wire:click.prevent="manageRepresentatives({{ $customer->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200" title="Yetkilileri Yönet">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </button>
                                        
                                        <button type="button" wire:click.prevent="edit({{ $customer->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200" title="Düzenle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        
                                        @if(auth()->user()->hasRole('Superadmin'))
                                        <button type="button" 
                                            wire:click.prevent="delete({{ $customer->id }})" 
                                            wire:confirm="Firma silindiğinde yetkililer de erişimi kaybeder. Emin misiniz?" 
                                            class="p-2 text-red-500 hover:bg-red-50 hover:text-red-700 rounded-xl transition-all duration-200" title="Sil">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 p-6 rounded-full border border-gray-100 mb-4">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-700">Sonuç Bulunamadı</h4>
                                    <p class="text-sm mt-1">Arama kriterlerinize uygun müşteri kaydı bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
        
    <div class="px-6 py-4 border-t border-gray-100 bg-white/50 rounded-2xl shadow-sm">
        {{ $customers->links() }}
    </div>
</div>
