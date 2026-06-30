@push('pageTitle')
    Toplu Mail Ayarları | 
@endpush

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-2xl border border-white/20">
            <div class="p-8">
                <div class="mb-8 border-b border-gray-200 pb-4 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Toplu Mail Yetki & Kısıtlama Ayarları</h2>
                        <p class="text-sm text-slate-500 font-medium mt-1">Sistemdeki hangi rollerin ve kişilerin toplu mail atabileceğini ve hangi firmalardan muaf tutulacaklarını belirleyin.</p>
                    </div>
                    <button wire:click="saveSettings" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Ayarları Kaydet
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Roller -->
                    <div class="bg-slate-50/50 rounded-2xl p-6 border border-slate-100">
                        <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Rol Bazlı Yetkiler
                        </h3>
                        <div class="space-y-4">
                            @foreach($roles as $role)
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="font-bold text-slate-800">{{ $role->name }}</div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model.defer="rolePermissions.{{ $role->id }}" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                            <span class="ml-3 text-xs font-bold text-gray-500 uppercase">Yetkili</span>
                                        </label>
                                    </div>
                                    <div class="mt-2 pt-3 border-t border-slate-100">
                                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Muaf Tutulacak (Gönderilemeyecek) Müşteriler</label>
                                        <select wire:model.defer="roleRestrictions.{{ $role->id }}" multiple class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 h-24">
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-[10px] text-slate-400 mt-1">CTRL/CMD ile çoklu seçim yapabilirsiniz.</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kullanıcılar -->
                    <div class="bg-slate-50/50 rounded-2xl p-6 border border-slate-100">
                        <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Kullanıcı Bazlı Yetkiler (İstisnai)
                        </h3>
                        <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($users as $user)
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $user->name }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $user->bolum->ad ?? 'Bölüm Yok' }}</div>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model.defer="userPermissions.{{ $user->id }}" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                        </label>
                                    </div>
                                    <div class="mt-2 pt-3 border-t border-slate-100">
                                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Muaf Tutulacak (Gönderilemeyecek) Müşteriler</label>
                                        <select wire:model.defer="userRestrictions.{{ $user->id }}" multiple class="w-full border-slate-200 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 h-24">
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
