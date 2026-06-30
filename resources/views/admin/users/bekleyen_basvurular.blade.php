<x-app-layout>
    @push('pageTitle') Bekleyen Başvurular | @endpush
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <span>📋</span> Bekleyen Uygulama Kayıt Başvuruları
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Başarı veya Hata Mesajları --}}
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">✅</span>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">❌</span>
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-2xl border border-slate-200" x-data="paginationComponent()" x-init="initPagination()">
                
                {{-- Üst Alan: Arama Kutusu --}}
                <div class="p-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl flex items-center justify-between">
                    <div class="relative w-full md:w-1/3">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" x-model="search" placeholder="İsim, e-posta veya TC ara..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white shadow-sm">
                    </div>
                </div>

                {{-- Tab Başlıkları ve Genel Sayım --}}
                <div class="border-b border-slate-200 bg-slate-50/50 px-6 pt-0 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex border-b border-transparent">
                        <button @click="activeTab = 'pending'" 
                                :class="activeTab === 'pending' ? 'border-indigo-600 text-indigo-600 font-bold bg-white rounded-t-xl border-t border-x border-slate-200 -mb-[1px]' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" 
                                class="py-3 px-6 border-b-2 text-sm transition-all flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Onay Bekleyenler
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold transition-colors"
                                  :class="activeTab === 'pending' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-600'">
                                {{ $bekleyenler->count() }}
                            </span>
                        </button>
                        
                        <button @click="activeTab = 'rejected'" 
                                :class="activeTab === 'rejected' ? 'border-rose-600 text-rose-600 font-bold bg-white rounded-t-xl border-t border-x border-slate-200 -mb-[1px]' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" 
                                class="py-3 px-6 border-b-2 text-sm transition-all flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                            Reddedilenler
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold transition-colors"
                                  :class="activeTab === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600'">
                                {{ $reddedilenler->count() }}
                            </span>
                        </button>

                        <button @click="activeTab = 'approved'" 
                                :class="activeTab === 'approved' ? 'border-emerald-600 text-emerald-600 font-bold bg-white rounded-t-xl border-t border-x border-slate-200 -mb-[1px]' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" 
                                class="py-3 px-6 border-b-2 text-sm transition-all flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Onaylananlar
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold transition-colors"
                                  :class="activeTab === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'">
                                {{ isset($onaylananlar) ? $onaylananlar->count() : 0 }}
                            </span>
                        </button>

                        @role('Superadmin')
                        <button @click="activeTab = 'central'" 
                                :class="activeTab === 'central' ? 'border-amber-600 text-amber-600 font-bold bg-white rounded-t-xl border-t border-x border-slate-200 -mb-[1px]' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" 
                                class="py-3 px-6 border-b-2 text-sm transition-all flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Merkezden Çek
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold transition-colors"
                                  :class="activeTab === 'central' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600'">
                                {{ isset($merkezBekleyenler) ? $merkezBekleyenler->count() : 0 }}
                            </span>
                        </button>
                        
                        <button @click="activeTab = 'logs'" 
                                :class="activeTab === 'logs' ? 'border-teal-600 text-teal-600 font-bold bg-white rounded-t-xl border-t border-x border-slate-200 -mb-[1px]' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" 
                                class="py-3 px-6 border-b-2 text-sm transition-all flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Geçmiş / Loglar
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold transition-colors"
                                  :class="activeTab === 'logs' ? 'bg-teal-100 text-teal-700' : 'bg-slate-200 text-slate-600'">
                                {{ (isset($departmentLogs) ? $departmentLogs->count() : 0) + (isset($actionLogs) ? $actionLogs->count() : 0) }}
                            </span>
                        </button>
                        @endrole
                    </div>

                    <div class="pb-3 md:pb-0 text-xs text-slate-500 italic">
                        Toplam {{ $bekleyenler->count() + $reddedilenler->count() + (isset($merkezBekleyenler) ? $merkezBekleyenler->count() : 0) }} adet kayıt başvurusu yönetiliyor.
                    </div>
                </div>

                <div class="p-6 bg-white">
                    
                    {{-- ⏳ ONAY BEKLEYENLER SEKMESİ --}}
                    <div id="tab-pending" x-show="activeTab === 'pending'" class="space-y-6">
                        @if($bekleyenler->count() > 0)
                            <div class="space-y-4">
                                @foreach($bekleyenler as $index => $user)
                                <div class="border border-slate-200 rounded-2xl p-5 hover:border-indigo-200 hover:shadow-md transition-all duration-200 bg-white list-item-row">
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                        
                                        {{-- Sol Taraf: Sıra No ve Kullanıcı Detayları --}}
                                        <div class="flex items-start gap-4 flex-shrink-0 lg:max-w-xs">
                                            {{-- Numaralandırma --}}
                                            <div class="flex items-center justify-center bg-indigo-50 text-indigo-700 border border-indigo-100 font-extrabold rounded-xl w-9 h-9 flex-shrink-0 text-sm shadow-sm">
                                                #{{ $index + 1 }}
                                            </div>
                                            {{-- İsim, E-posta ve Talep Edilen Bölüm --}}
                                            <div class="space-y-1">
                                                <p class="text-sm font-bold text-slate-900 leading-snug">
                                                    {{ $user->name }}
                                                    @if($user->is_mavi_yaka)
                                                        <span class="ml-2 inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-blue-200">
                                                            👷 Mavi Yaka
                                                        </span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-500 break-all leading-none">{{ $user->email }}</p>
                                                <p class="text-[11px] text-slate-500 font-medium leading-none mt-1.5 flex items-center gap-1">
                                                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">TC:</span> {{ $user->tc_kimlik_no ?? 'Belirtilmemiş' }}
                                                </p>
                                                <div class="inline-flex items-center gap-1 bg-slate-100 border border-slate-200 text-slate-600 font-semibold px-2 py-0.5 rounded-md text-[10px] uppercase tracking-wider mt-1.5">
                                                    Talep Edilen: {{ $user->bolum ? $user->bolum->ad : 'Belirtilmemiş' }}
                                                </div>
                                                @role('Superadmin')
                                                    @if($user->bolum_id)
                                                    @php
                                                        $liderler = \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $user->bolum_id)->pluck('name')->implode(', ');
                                                    @endphp
                                                    <div class="mt-2 text-[10px] text-amber-600 font-medium bg-amber-50 border border-amber-100 px-2 py-1 rounded">
                                                        ⏳ Bölüm lideri <strong>{{ $liderler ?: 'Atanmamış' }}</strong>'nin {{ $user->created_at ? $user->created_at->diffForHumans() : 'bir süredir' }} onayını bekliyor.
                                                    </div>
                                                    @endif
                                                @endrole
                                            </div>
                                        </div>

                                        {{-- Orta/Sağ Taraf: Tek Bir Hizalı Satırda Form ve Butonlar --}}
                                        <form action="{{ route('admin.users.basvuru_onayla', $user->id) }}" method="POST" class="flex flex-col md:flex-row items-stretch md:items-end gap-3 flex-1" x-data="{ accountType: '{{ $user->is_mavi_yaka ? 'mavi_yaka' : ($user->customer_id ? 'musteri' : 'personel') }}' }">
                                            @csrf
                                            
                                            {{-- Hesap Türü Seçimi --}}
                                            <div class="flex-1 min-w-[140px]">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>👤</span> Hesap Türü
                                                </label>
                                                <select name="account_type" x-model="accountType" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                                    <option value="personel">Personel</option>
                                                    <option value="mavi_yaka">Mavi Yaka</option>
                                                    @if(Auth::user()->hasRole(['Superadmin', 'Yonetim']))
                                                    <option value="musteri">Müşteri Yetkilisi</option>
                                                    @endif
                                                </select>
                                            </div>

                                            {{-- Departman Seçimi (Genişlik Kısıtlamalı ve Hizalı) --}}
                                            <div class="flex-1 min-w-[170px]" x-show="accountType !== 'musteri'">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>🏢</span> Departman <span class="text-slate-400 font-normal italic">(Ops)</span>
                                                </label>
                                                @if(Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Yonetim'))
                                                    <select name="bolum_id" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                                        <option value="">-- Seçilmedi --</option>
                                                        @foreach($bolumler as $b)
                                                            <option value="{{ $b->id }}" {{ $user->bolum_id == $b->id ? 'selected' : '' }}>
                                                                {{ $b->ad }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    {{-- Bölüm Lideri sadece kendi departmanını seçili ve kilitli görür --}}
                                                    <select disabled class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow bg-slate-50 text-slate-500">
                                                        @foreach($bolumler->where('id', Auth::user()->bolum_id) as $b)
                                                            <option value="{{ $b->id }}" selected>
                                                                {{ $b->ad }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="bolum_id" value="{{ Auth::user()->bolum_id }}">
                                                @endif
                                            </div>

                                            {{-- Firma Seçimi (Müşteri Yetkilisi ise gösterilecek) --}}
                                            <div class="flex-1 min-w-[170px]" x-show="accountType === 'musteri'" style="display: none;">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>🏢</span> Firma <span class="text-slate-400 font-normal italic">(Zorunlu)</span>
                                                </label>
                                                <select name="customer_id" :required="accountType === 'musteri'" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                                    <option value="">-- Firma Seçin --</option>
                                                    @foreach($customers as $c)
                                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Personel / Mavi Yaka Ek Bilgiler --}}
                                            <div class="flex-1 min-w-[200px]" x-show="accountType === 'personel' || accountType === 'mavi_yaka'">
                                                <div class="flex gap-2 mb-1.5">
                                                    <div class="flex-1">
                                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sicil No <span class="text-slate-400 font-normal italic">(Ops)</span></label>
                                                        <input type="text" name="sicil_no" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Örn: 1234">
                                                    </div>
                                                    <div class="flex-1">
                                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">İşe Giriş <span class="text-slate-400 font-normal italic">(Ops)</span></label>
                                                        <input type="date" name="hire_date" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Ünvan/Görevi <span class="text-slate-400 font-normal italic">(Ops)</span></label>
                                                    <input type="text" name="unvan" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Örn: Paketleme Personeli">
                                                </div>
                                            </div>

                                            {{-- Rol Seçimi (Çoklu Dropdown Liste) Sadece Superadmin/Yönetim --}}
                                            @if(Auth::user()->hasRole(['Superadmin', 'Yonetim']))
                                            <div class="flex-1 min-w-[220px] relative" x-data="{ 
                                                open: false, 
                                                selectedRoles: [],
                                                toggleRole(role) {
                                                    if (this.selectedRoles.includes(role)) {
                                                        this.selectedRoles = this.selectedRoles.filter(r => r !== role);
                                                    } else {
                                                        this.selectedRoles.push(role);
                                                    }
                                                }
                                            }">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>🔑</span> Yetki Rolleri <span class="text-slate-400 font-normal italic">(Opsiyonel)</span>
                                                </label>
                                                
                                                <!-- Dropdown Seçici Butonu -->
                                                <button type="button" @click="open = !open" class="w-full text-left bg-white text-xs border border-slate-300 rounded-xl px-3 py-2 flex items-center justify-between shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all h-[38px] select-none">
                                                    <span class="truncate text-slate-700 font-medium" x-text="selectedRoles.length > 0 ? selectedRoles.join(', ') : '-- Rol Seçilmedi --'"></span>
                                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                
                                                <!-- Form Gönderimi İçin Gizli Inputlar -->
                                                <template x-for="role in selectedRoles" :key="role">
                                                    <input type="hidden" name="roles[]" :value="role">
                                                </template>
                                                
                                                <!-- Dropdown Seçenekleri -->
                                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-56 overflow-y-auto custom-scrollbar p-1.5 space-y-0.5" style="display: none;">
                                                    @foreach($roles as $r)
                                                        <div @click="toggleRole('{{ $r->name }}')" class="flex items-center gap-2 px-3 py-2 hover:bg-indigo-50 rounded-lg cursor-pointer transition-colors select-none text-xs">
                                                            <input type="checkbox" :checked="selectedRoles.includes('{{ $r->name }}')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 pointer-events-none transition-all">
                                                            <span class="text-slate-700 font-medium">{{ $r->name }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            {{-- İşlem Butonları (Mükemmel Hizalı ve Modern) --}}
                                            <div class="flex items-center gap-2 flex-shrink-0 pt-2 md:pt-0">
                                                <button type="submit" class="flex-1 md:flex-initial inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-150 h-[38px]">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Onayla
                                                </button>
                                            </form>
                                                
                                            <div class="flex-1 md:flex-initial">
                                                <button type="button" @click="$dispatch('open-reject-modal', { id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}' })" class="w-full inline-flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-600 active:bg-rose-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-150 h-[38px]">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Reddet
                                                </button>
                                            </div>
                                            </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 flex flex-col items-center">
                                <div class="h-16 w-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-700">Tüm Başvurular İşlendi</h3>
                                <p class="text-slate-500 text-xs mt-1">Şu an onay bekleyen yeni bir kayıt başvurusu bulunmuyor.</p>
                            </div>
                        @endif
                    </div>

                    {{-- 🚫 REDDEDİLENLER SEKMESİ --}}
                    <div id="tab-rejected" x-show="activeTab === 'rejected'" class="space-y-6" style="display: none;">
                        @if($reddedilenler->count() > 0)
                            <div class="space-y-4">
                                @foreach($reddedilenler as $index => $user)
                                <div class="border border-rose-100 rounded-2xl p-5 hover:border-rose-200 hover:shadow-md transition-all duration-200 bg-rose-50/10 list-item-row">
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                        
                                        {{-- Sol Taraf: Sıra No, Kullanıcı Detayları ve Reddedilme Tarihi --}}
                                        <div class="flex items-start gap-4 flex-shrink-0 lg:max-w-xs">
                                            {{-- Numaralandırma --}}
                                            <div class="flex items-center justify-center bg-rose-50 text-rose-700 border border-rose-100 font-extrabold rounded-xl w-9 h-9 flex-shrink-0 text-sm shadow-sm">
                                                #{{ $index + 1 }}
                                            </div>
                                            {{-- İsim, E-posta ve Red Tarihi --}}
                                            <div class="space-y-1">
                                                <p class="text-sm font-bold text-slate-900 leading-snug">
                                                    {{ $user->name }}
                                                    @if($user->is_mavi_yaka)
                                                        <span class="ml-2 inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-blue-200">
                                                            👷 Mavi Yaka
                                                        </span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-500 break-all leading-none">{{ $user->email }}</p>
                                                
                                                <div class="text-[10px] text-rose-600 font-medium flex items-center gap-1 mt-1">
                                                    <span>🚫 Reddedilme:</span>
                                                    <span class="font-bold">{{ $user->rejected_at ? $user->rejected_at->format('d.m.Y H:i') : 'Belirtilmemiş' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Sağ Taraf: Tekrar Onayla Formu --}}
                                        <form action="{{ route('admin.users.basvuru_onayla', $user->id) }}" method="POST" class="flex flex-col md:flex-row items-stretch md:items-end gap-3 flex-1" x-data="{ accountType: '{{ $user->is_mavi_yaka ? 'mavi_yaka' : ($user->customer_id ? 'musteri' : 'personel') }}' }">
                                            @csrf
                                            
                                            {{-- Hesap Türü Seçimi --}}
                                            <div class="flex-1 min-w-[140px]">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>👤</span> Hesap Türü
                                                </label>
                                                <select name="account_type" x-model="accountType" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                                    <option value="personel">Personel</option>
                                                    <option value="mavi_yaka">Mavi Yaka</option>
                                                    @if(Auth::user()->hasRole(['Superadmin', 'Yonetim']))
                                                    <option value="musteri">Müşteri Yetkilisi</option>
                                                    @endif
                                                </select>
                                            </div>

                                            {{-- Departman Seçimi --}}
                                            <div class="flex-1 min-w-[170px]" x-show="accountType !== 'musteri'">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>🏢</span> Departman <span class="text-slate-400 font-normal italic">(Ops)</span>
                                                </label>
                                                @if(Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Yonetim'))
                                                    <select name="bolum_id" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                                        <option value="">-- Seçilmedi --</option>
                                                        @foreach($bolumler as $b)
                                                            <option value="{{ $b->id }}" {{ $user->bolum_id == $b->id ? 'selected' : '' }}>
                                                                {{ $b->ad }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    {{-- Bölüm Lideri sadece kendi departmanını seçili ve kilitli görür --}}
                                                    <select disabled class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow bg-slate-50 text-slate-500">
                                                        @foreach($bolumler->where('id', Auth::user()->bolum_id) as $b)
                                                            <option value="{{ $b->id }}" selected>
                                                                {{ $b->ad }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="bolum_id" value="{{ Auth::user()->bolum_id }}">
                                                @endif
                                            </div>

                                            {{-- Firma Seçimi (Müşteri Yetkilisi ise gösterilecek) --}}
                                            <div class="flex-1 min-w-[170px]" x-show="accountType === 'musteri'" style="display: none;">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>🏢</span> Firma <span class="text-slate-400 font-normal italic">(Zorunlu)</span>
                                                </label>
                                                <select name="customer_id" :required="accountType === 'musteri'" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                                    <option value="">-- Firma Seçin --</option>
                                                    @foreach($customers as $c)
                                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Rol Seçimi (Çoklu Dropdown Liste) Sadece Superadmin/Yönetim --}}
                                            @if(Auth::user()->hasRole(['Superadmin', 'Yonetim']))
                                            <div class="flex-1 min-w-[220px] relative" x-data="{ 
                                                open: false, 
                                                selectedRoles: [],
                                                toggleRole(role) {
                                                    if (this.selectedRoles.includes(role)) {
                                                        this.selectedRoles = this.selectedRoles.filter(r => r !== role);
                                                    } else {
                                                        this.selectedRoles.push(role);
                                                    }
                                                }
                                            }">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>🔑</span> Yetki Rolleri <span class="text-slate-400 font-normal italic">(Opsiyonel)</span>
                                                </label>
                                                
                                                <!-- Dropdown Seçici Butonu -->
                                                <button type="button" @click="open = !open" class="w-full text-left bg-white text-xs border border-slate-300 rounded-xl px-3 py-2 flex items-center justify-between shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all h-[38px] select-none">
                                                    <span class="truncate text-slate-700 font-medium" x-text="selectedRoles.length > 0 ? selectedRoles.join(', ') : '-- Rol Seçilmedi --'"></span>
                                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                
                                                <!-- Form Gönderimi İçin Gizli Inputlar -->
                                                <template x-for="role in selectedRoles" :key="role">
                                                    <input type="hidden" name="roles[]" :value="role">
                                                </template>
                                                
                                                <!-- Dropdown Seçenekleri -->
                                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-56 overflow-y-auto custom-scrollbar p-1.5 space-y-0.5" style="display: none;">
                                                    @foreach($roles as $r)
                                                        <div @click="toggleRole('{{ $r->name }}')" class="flex items-center gap-2 px-3 py-2 hover:bg-indigo-50 rounded-lg cursor-pointer transition-colors select-none text-xs">
                                                            <input type="checkbox" :checked="selectedRoles.includes('{{ $r->name }}')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 pointer-events-none transition-all">
                                                            <span class="text-slate-700 font-medium">{{ $r->name }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Geri Al/Onayla Butonu --}}
                                            <div class="flex-shrink-0 pt-2 md:pt-0">
                                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-150 h-[38px]">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 15H19"></path>
                                                    </svg>
                                                    Kurtar & Onayla
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 flex flex-col items-center">
                                <div class="h-16 w-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-700">Reddedilen Başvuru Yok</h3>
                                <p class="text-slate-500 text-xs mt-1">Sistemde reddedilmiş bir kayıt başvurusu bulunmuyor.</p>
                            </div>
                        @endif
                    </div>

                    {{-- ✅ ONAYLANANLAR SEKMESİ --}}
                    <div id="tab-approved" x-show="activeTab === 'approved'" class="space-y-6" style="display: none;">
                        @if(isset($onaylananlar) && $onaylananlar->count() > 0)
                            <div class="space-y-4">
                                @foreach($onaylananlar as $index => $user)
                                <div class="border border-emerald-100 rounded-2xl p-5 hover:border-emerald-200 hover:shadow-md transition-all duration-200 bg-emerald-50/10 list-item-row">
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                        
                                        {{-- Sol Taraf: Sıra No ve Kullanıcı Detayları --}}
                                        <div class="flex items-start gap-4 flex-shrink-0 lg:max-w-xs">
                                            <div class="flex items-center justify-center bg-emerald-50 text-emerald-700 border border-emerald-100 font-extrabold rounded-xl w-9 h-9 flex-shrink-0 text-sm shadow-sm">
                                                #{{ $index + 1 }}
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-sm font-bold text-slate-900 leading-snug">
                                                    {{ $user->name }}
                                                    @if($user->is_mavi_yaka)
                                                        <span class="ml-2 inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-blue-200">
                                                            👷 Mavi Yaka
                                                        </span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-500 break-all leading-none">{{ $user->email }}</p>
                                                
                                                <div class="text-[10px] text-emerald-600 font-medium flex items-center gap-1 mt-1">
                                                    <span>✅ Onay Tarihi:</span>
                                                    <span class="font-bold">{{ $user->updated_at ? $user->updated_at->format('d.m.Y H:i') : 'Belirtilmemiş' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Sağ Taraf: Geri Al Formu --}}
                                        @php
                                            $canRevoke = true;
                                            if ($user->updated_at) {
                                                $daysSinceApproval = $user->updated_at->diffInDays(now());
                                                if ($daysSinceApproval > 3) {
                                                    $canRevoke = false;
                                                }
                                            }
                                        @endphp
                                        
                                        @if($canRevoke)
                                            <form action="{{ route('admin.users.basvuru_gerial', $user->id) }}" method="POST" class="flex flex-col md:flex-row items-stretch md:items-end gap-3 flex-1 justify-end">
                                                @csrf
                                                <div class="flex-shrink-0 pt-2 md:pt-0">
                                                    <button type="button" @click="$dispatch('open-gerial-modal', { id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}' })" class="w-full inline-flex items-center justify-center gap-2 bg-slate-600 hover:bg-slate-700 active:bg-slate-800 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-150 h-[38px]">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                        </svg>
                                                        Geri Al (İptal Et)
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="flex flex-col items-end gap-1 flex-1 justify-end text-right">
                                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 text-slate-500 rounded-lg text-[10px] font-bold border border-slate-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    İptal Süresi Doldu
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 flex flex-col items-center">
                                <div class="h-16 w-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-700">Henüz Onaylanan Başvuru Yok</h3>
                                <p class="text-slate-500 text-xs mt-1">Son zamanlarda onaylanmış bir kullanıcı başvurusu bulunmuyor.</p>
                            </div>
                        @endif
                    </div>

                    {{-- 📥 MERKEZDEN ÇEK SEKMESİ (SADECE SUPERADMIN) --}}
                    @role('Superadmin')
                    <div id="tab-central" x-show="activeTab === 'central'" class="space-y-6" style="display: none;">
                        @if(isset($merkezBekleyenler) && $merkezBekleyenler->count() > 0)
                            <div class="space-y-4">
                                @foreach($merkezBekleyenler as $index => $cUser)
                                <div class="border border-amber-200 rounded-2xl p-5 hover:border-amber-300 hover:shadow-md transition-all duration-200 bg-amber-50/30 list-item-row">
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                        
                                        {{-- Sol Taraf --}}
                                        <div class="flex items-start gap-4 flex-shrink-0 lg:max-w-xs">
                                            <div class="flex items-center justify-center bg-amber-100 text-amber-700 border border-amber-200 font-extrabold rounded-xl w-9 h-9 flex-shrink-0 text-sm shadow-sm">
                                                #{{ $index + 1 }}
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-sm font-bold text-slate-900 leading-snug">
                                                    {{ $cUser['first_name'] }} {{ $cUser['last_name'] ?? '' }}
                                                    @if(isset($cUser['is_mavi_yaka']) && $cUser['is_mavi_yaka'])
                                                        <span class="ml-2 inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-blue-200">
                                                            👷 Mavi Yaka
                                                        </span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-500 break-all leading-none">{{ $cUser['email'] }}</p>
                                                <div class="text-[10px] text-amber-600 font-medium flex items-center gap-1 mt-1">
                                                    <span>⚠️</span>
                                                    <span class="font-bold">Merkezi sistemde yetkisi var ancak İAA'ya henüz hiç giriş yapmadı.</span>
                                                </div>
                                                @if(isset($cUser['app_users']) && count($cUser['app_users']) > 0)
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        @foreach($cUser['app_users'] as $appUser)
                                                            @if(isset($appUser['application']['name']))
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                                    {{ $appUser['application']['name'] }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Sağ Taraf: Form --}}
                                        <form action="{{ route('admin.users.merkezden_cek') }}" method="POST" class="flex flex-col md:flex-row items-stretch md:items-end gap-3 flex-1" x-data="{ accountType: '{{ (isset($cUser['is_mavi_yaka']) && $cUser['is_mavi_yaka']) ? 'mavi_yaka' : 'personel' }}' }">
                                            @csrf
                                            <input type="hidden" name="email" value="{{ $cUser['email'] }}">
                                            <input type="hidden" name="first_name" value="{{ $cUser['first_name'] }}">
                                            <input type="hidden" name="last_name" value="{{ $cUser['last_name'] ?? '' }}">
                                            <input type="hidden" name="tc_no" value="{{ $cUser['tc_no'] ?? '' }}">
                                            
                                            <div class="flex-1 min-w-[140px]">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>👤</span> Hesap Türü
                                                </label>
                                                <select name="account_type" x-model="accountType" class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                                    <option value="personel">Personel</option>
                                                    <option value="mavi_yaka">Mavi Yaka</option>
                                                </select>
                                            </div>

                                            <div class="flex-1 min-w-[170px]">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>🏢</span> Departman <span class="text-rose-500">*</span>
                                                </label>
                                                <select name="bolum_id" required class="w-full text-xs border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                                    <option value="">-- Bölüm Seç --</option>
                                                    @foreach($bolumler as $b)
                                                        <option value="{{ $b->id }}">{{ $b->ad }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Rol Seçimi (Sadece Superadmin) --}}
                                            @if(Auth::user()->hasRole(['Superadmin', 'Yonetim']))
                                            <div class="flex-1 min-w-[220px] relative" x-data="{ 
                                                open: false, 
                                                selectedRoles: [],
                                                toggleRole(role) {
                                                    if (this.selectedRoles.includes(role)) {
                                                        this.selectedRoles = this.selectedRoles.filter(r => r !== role);
                                                    } else {
                                                        this.selectedRoles.push(role);
                                                    }
                                                }
                                            }">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                    <span>🔑</span> Yetki Rolleri <span class="text-slate-400 font-normal italic">(Opsiyonel)</span>
                                                </label>
                                                
                                                <button type="button" @click="open = !open" class="w-full text-left bg-white text-xs border border-slate-300 rounded-xl px-3 py-2 flex items-center justify-between shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all h-[38px] select-none">
                                                    <span class="truncate text-slate-700 font-medium" x-text="selectedRoles.length > 0 ? selectedRoles.join(', ') : '-- Rol Seçilmedi --'"></span>
                                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                
                                                <template x-for="role in selectedRoles" :key="role">
                                                    <input type="hidden" name="roles[]" :value="role">
                                                </template>
                                                
                                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-56 overflow-y-auto custom-scrollbar p-1.5 space-y-0.5" style="display: none;">
                                                    @foreach($roles as $r)
                                                        <div @click="toggleRole('{{ $r->name }}')" class="flex items-center gap-2 px-3 py-2 hover:bg-indigo-50 rounded-lg cursor-pointer transition-colors select-none text-xs">
                                                            <input type="checkbox" :checked="selectedRoles.includes('{{ $r->name }}')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 pointer-events-none transition-all">
                                                            <span class="text-slate-700 font-medium">{{ $r->name }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif


                                            <div class="flex-shrink-0 pt-2 md:pt-0">
                                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-150 h-[38px]">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                    Çek & Onayla
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 flex flex-col items-center">
                                <div class="h-16 w-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-700">Tüm Kullanıcılar Aktif</h3>
                                <p class="text-slate-500 text-xs mt-1">Merkezi sistemde bulunup da İAA'ya giriş yapmamış kimse yok.</p>
                            </div>
                        @endif
                    </div>
                    @endrole

                    {{-- 📜 GEÇMİŞ / LOGLAR SEKMESİ (SADECE SUPERADMIN) --}}
                    @role('Superadmin')
                    <div x-show="activeTab === 'logs'" class="space-y-6" style="display: none;">
                        
                        {{-- Aksiyon Logları (Onay/Red) --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Onay ve Red İşlemleri
                            </h3>
                            @if(isset($actionLogs) && $actionLogs->count() > 0)
                                <div class="overflow-hidden bg-white shadow-sm ring-1 ring-slate-200 sm:rounded-2xl">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider sm:pl-6">Kullanıcı</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">İşlem</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">İşlemi Yapan</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Detay / Sebep</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tarih</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 bg-white">
                                            @foreach($actionLogs as $log)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 flex-shrink-0 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 font-bold text-xs">
                                                            {{ mb_substr($log->user->name, 0, 1) }}
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="font-medium text-slate-900">{{ $log->user->name }}</div>
                                                            <div class="text-xs text-slate-500">{{ $log->user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                                    @if($log->action === 'approved')
                                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Onaylandı</span>
                                                    @elseif($log->action === 'rejected')
                                                        <span class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">Reddedildi</span>
                                                    @endif
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                                    {{ $log->actionBy ? $log->actionBy->name : 'Sistem' }}
                                                </td>
                                                <td class="px-3 py-4 text-sm text-slate-500 max-w-xs truncate" title="{{ $log->details }}">
                                                    {{ Str::limit($log->details, 50) }}
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                                    {{ $log->created_at->format('d M Y, H:i') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-sm text-slate-500 italic p-4 bg-slate-50 rounded-xl border border-slate-200">Henüz onay veya red işlemi kaydı bulunmuyor.</div>
                            @endif
                        </div>

                        {{-- Departman Değişim Logları --}}
                        <div>
                            <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                Departman Değişimleri
                            </h3>
                        @if(isset($departmentLogs) && $departmentLogs->count() > 0)
                            <div class="overflow-hidden bg-white shadow-sm ring-1 ring-slate-200 sm:rounded-2xl">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider sm:pl-6">Kullanıcı</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Eski Departman</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Yeni Departman</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tarih</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        @foreach($departmentLogs as $log)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 flex-shrink-0 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold text-xs">
                                                        {{ mb_substr($log->user->name, 0, 1) }}
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="font-medium text-slate-900">{{ $log->user->name }}</div>
                                                        <div class="text-xs text-slate-500">{{ $log->user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                                @if($log->oldBolum)
                                                    <span class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                                        {{ $log->oldBolum->ad }}
                                                    </span>
                                                @else
                                                    <span class="text-xs italic text-slate-400">Belirtilmemiş</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                                @if($log->newBolum)
                                                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                        {{ $log->newBolum->ad }}
                                                    </span>
                                                @else
                                                    <span class="text-xs italic text-slate-400">Belirtilmemiş</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $log->created_at->format('d M Y, H:i') }}
                                                </div>
                                                <div class="text-[10px] text-slate-400 ml-5">{{ $log->created_at->diffForHumans() }}</div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12 flex flex-col items-center">
                                <div class="h-16 w-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-700">Henüz Kayıt Yok</h3>
                                <p class="text-slate-500 text-xs mt-1">Departmanını değiştiren herhangi bir başvuru kaydı bulunmuyor.</p>
                            </div>
                        @endif
                    </div>
                    @endrole

                </div>
                
                {{-- Sayfalama Kontrolleri --}}
                <div x-show="totalPages > 1 && !['logs'].includes(activeTab)" class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl flex items-center justify-between">
                    <button type="button" @click="if(page > 1) page--" :disabled="page === 1" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Önceki
                    </button>
                    <span class="text-sm text-slate-600 font-medium">Sayfa <span x-text="page"></span> / <span x-text="totalPages"></span></span>
                    <button type="button" @click="if(page < totalPages) page++" :disabled="page >= totalPages" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Sonraki
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Özel Kaydırma Çubuğu Stili --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 99px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    {{-- Reddetme Modalı --}}
    <div x-data="{ 
            isOpen: false, 
            userId: null, 
            userName: ''
        }" 
        @open-reject-modal.window="isOpen = true; userId = $event.detail.id; userName = $event.detail.name;"
        x-show="isOpen" 
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" 
        style="display: none;"
    >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4" @click.away="isOpen = false">
            <form :action="'{{ url('admin/users') }}/' + userId + '/reddet'" method="POST">
                @csrf
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Başvuruyu Reddet</h3>
                    <p class="text-sm text-slate-500 mt-1"><span x-text="userName" class="font-semibold text-slate-700"></span> adlı kullanıcının başvurusunu reddediyorsunuz.</p>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Reddetme Sebebi <span class="text-rose-500">*</span></label>
                    <textarea name="rejection_reason" required rows="4" class="w-full text-sm border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500" placeholder="Kullanıcıya iletilecek red sebebini yazınız..."></textarea>
                    <p class="text-xs text-slate-500 mt-2">Bu mesaj kullanıcı sisteme girdiğinde karşısına çıkacaktır.</p>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" @click="isOpen = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-200 bg-slate-100 rounded-xl transition-colors">İptal</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition-colors">Reddet ve Bildir</button>
                </div>
            </form>
        </div>
    </div>
    {{-- Geri Al Modalı ve Pagination / SweetAlert JS --}}
    <script>
        function paginationComponent() {
            return {
                activeTab: 'pending',
                search: '',
                perPage: 20,
                page: 1,
                
                initPagination() {
                    this.updateDOM();
                    this.$watch('activeTab', () => { this.page = 1; this.search = ''; this.updateDOM(); });
                    this.$watch('search', () => { this.page = 1; this.updateDOM(); });
                    this.$watch('page', () => { this.updateDOM(); });
                    
                    // Listen for SweetAlert triggers
                    window.addEventListener('open-gerial-modal', (e) => {
                        let id = e.detail.id;
                        let name = e.detail.name;
                        Swal.fire({
                            title: 'Emin misiniz?',
                            text: name + ' isimli kullanıcının onayını geri alacaksınız. Kullanıcı tekrar "Bekleyenler" listesine düşecektir.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#4f46e5',
                            cancelButtonColor: '#94a3b8',
                            confirmButtonText: 'Evet, Geri Al!',
                            cancelButtonText: 'İptal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Create a form and submit
                                let form = document.createElement('form');
                                form.method = 'POST';
                                form.action = '{{ url("admin/users") }}/' + id + '/geri-al';
                                form.innerHTML = '@csrf';
                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    });
                },
                
                get totalPages() {
                    let tabContainer = document.getElementById('tab-' + this.activeTab);
                    if (!tabContainer) return 1;
                    
                    let rows = Array.from(tabContainer.querySelectorAll('.list-item-row'));
                    let filtered = rows.filter(row => {
                        if (this.search === '') return true;
                        return row.textContent.toLowerCase().includes(this.search.toLowerCase());
                    });
                    
                    return Math.ceil(filtered.length / this.perPage) || 1;
                },
                
                updateDOM() {
                    this.$nextTick(() => {
                        let tabContainer = document.getElementById('tab-' + this.activeTab);
                        if (!tabContainer) return;
                        
                        let rows = Array.from(tabContainer.querySelectorAll('.list-item-row'));
                        
                        let filtered = rows.filter(row => {
                            if (this.search === '') return true;
                            return row.textContent.toLowerCase().includes(this.search.toLowerCase());
                        });
                        
                        let start = (this.page - 1) * this.perPage;
                        let end = start + this.perPage;
                        
                        rows.forEach(row => { row.style.display = 'none'; });
                        filtered.slice(start, end).forEach(row => { row.style.display = ''; });
                    });
                }
            }
        }

        // Merkezden Çek Formu için Onay (SweetAlert)
        document.addEventListener('DOMContentLoaded', function() {
            const pullForms = document.querySelectorAll('form[action="{{ route('admin.users.merkezden_cek') }}"]');
            pullForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Merkezi Sistemden Çekilecek',
                        text: 'Kullanıcının belirtilen departman ile hesabı açılacak ve onaylanacaktır. Emin misiniz?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#d97706',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Evet, Çek & Onayla',
                        cancelButtonText: 'İptal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
            
            // Onayla Butonu için Onay (SweetAlert)
            const approveForms = document.querySelectorAll('form[action*="onayla"]');
            approveForms.forEach(form => {
                // Merkezden çek formuyla karışmaması için
                if(!form.action.includes('merkezden_cek')) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Başvuruyu Onayla',
                            text: 'Kullanıcıya İAA sistemine erişim yetkisi verilecek. Devam etmek istiyor musunuz?',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#059669',
                            cancelButtonColor: '#94a3b8',
                            confirmButtonText: 'Evet, Onayla',
                            cancelButtonText: 'İptal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                }
            });
        });
    </script>
</x-app-layout>
