@push('pageTitle')
    Yeni Bölüm Oluştur | IAA
@endpush

<x-app-layout>
    <div class="py-10 bg-[#f8fafc] min-h-screen" x-data="bolumSistemi()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Bölümü --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Yeni Bölüm Oluştur</h1>
                    <p class="text-slate-500 mt-1 font-medium">Organizasyon yapınıza yeni bir departman ekleyin.</p>
                </div>
                <a href="{{ route('admin.bolumler.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Listeye Dön
                </a>
            </div>

            {{-- Hata Mesajları --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-100 rounded-2xl p-4 flex items-start gap-3 shadow-sm animate-shake">
                    <div class="bg-red-100 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Lütfen formdaki hataları kontrol edin</h3>
                        <ul class="mt-1 text-xs text-red-700/80 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.bolumler.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- GENEL BİLGİLER --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 transition-all hover:shadow-2xl hover:shadow-slate-200/60">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="h-12 w-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Bölüm Kimliği</h2>
                                <p class="text-sm text-slate-400">Temel tanımlamalar ve hiyerarşi</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Bölüm Adı --}}
                            <div class="md:col-span-2">
                                <label for="ad" class="block text-sm font-bold text-slate-700 mb-2">Bölüm Adı <span class="text-rose-500">*</span></label>
                                <input type="text" name="ad" id="ad" value="{{ old('ad') }}" required
                                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 placeholder:text-slate-300"
                                       placeholder="Örn: AR-GE, Kalite Kontrol...">
                            </div>

                            {{-- Kategori Seçimi + Hızlı Ekleme --}}
                            <div class="relative">
                                <label for="bolum_kategori_id" class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <select name="bolum_kategori_id" id="bolum_kategori_id" x-model="selectedCategory"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-700 appearance-none focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300">
                                            <option value="">Seçiniz (Opsiyonel)</option>
                                            <template x-for="cat in categories" :key="cat.id">
                                                <option :value="cat.id" x-text="cat.ad" :selected="cat.id == selectedCategory"></option>
                                            </template>
                                        </select>
                                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400/80">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <button type="button" @click="showCategoryModal = true"
                                            class="p-4 bg-white border border-slate-200 rounded-2xl text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all shadow-sm group">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Durum --}}
                            <div>
                                <label for="is_active" class="block text-sm font-bold text-slate-700 mb-2">Çalışma Durumu</label>
                                <div class="relative">
                                    <select name="is_active" id="is_active"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-700 appearance-none focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300">
                                        <option value="1">✅ Aktif</option>
                                        <option value="0">⛔ Pasif</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- YÖNETİM VE ATAMALAR --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 transition-all hover:shadow-2xl hover:shadow-slate-200/60">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="h-12 w-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Yönetim ve Atamalar</h2>
                                <p class="text-sm text-slate-400">Lider ve direktör sorumluluklarını belirleyin</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Bölüm Lideri Seçimi (Searchable) --}}
                            <div class="relative">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Bölüm Lideri Atayın</label>
                                <div class="relative" @click.away="liderSearch = ''">
                                    <input type="text" x-model="liderSearch" @focus="showLiderList = true"
                                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-12 py-4 text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all duration-300 placeholder:text-slate-400"
                                           placeholder="İsim ile ara...">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="hidden" name="lider_id" :value="selectedLiderId">
                                    
                                    {{-- Search Results --}}
                                    <div x-show="showLiderList && filteredLiderler.length > 0" 
                                         class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl max-h-60 overflow-y-auto"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0">
                                        <template x-for="user in filteredLiderler" :key="user.id">
                                            <div @click="selectLider(user)" 
                                                 class="flex items-center px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-50 last:border-0">
                                                <div class="h-8 w-8 bg-indigo-100 text-indigo-700 rounded-lg flex items-center justify-center font-bold text-xs mr-3" x-text="user.name.charAt(0)"></div>
                                                <div>
                                                    <div class="text-sm font-bold text-slate-700" x-text="user.name"></div>
                                                    <div class="text-[10px] text-slate-400 font-medium" x-text="user.unvan || 'Personel'"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div x-show="selectedLiderName" class="mt-3 flex items-center bg-indigo-50 text-indigo-700 border border-indigo-100 px-4 py-2 rounded-xl text-xs font-bold animate-fadeIn">
                                    <span class="mr-2">Seçili Lider:</span>
                                    <span x-text="selectedLiderName"></span>
                                    <button type="button" @click="resetLider()" class="ml-auto text-indigo-400 hover:text-rose-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-2 text-[10px] text-slate-400 italic font-medium px-1">* Seçilen personelin rolü otomatik olarak 'Bölüm Lideri' yapılacaktır.</p>
                            </div>

                            {{-- Direktör Seçimi (Searchable) --}}
                            <div class="relative">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Bölüm Direktörü</label>
                                <div class="relative" @click.away="direktorSearch = ''">
                                    <input type="text" x-model="direktorSearch" @focus="showDirektorList = true"
                                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-12 py-4 text-slate-700 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:bg-white transition-all duration-300 placeholder:text-slate-400"
                                           placeholder="İsim ile ara...">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="hidden" name="director_id" :value="selectedDirektorId">
                                    
                                    {{-- Search Results --}}
                                    <div x-show="showDirektorList && filteredDirektorler.length > 0" 
                                         class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl max-h-60 overflow-y-auto"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0">
                                        <template x-for="user in filteredDirektorler" :key="user.id">
                                            <div @click="selectDirektor(user)" 
                                                 class="flex items-center px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-50 last:border-0">
                                                <div class="h-8 w-8 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center font-bold text-xs mr-3" x-text="user.name.charAt(0)"></div>
                                                <div class="flex-1">
                                                    <div class="text-sm font-bold text-slate-700" x-text="user.name"></div>
                                                    <div class="text-[10px] text-slate-400 font-medium" x-text="user.unvan || 'Personel'"></div>
                                                </div>
                                                <template x-if="user.is_director">
                                                    <span class="text-[9px] bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full font-black tracking-tighter shadow-sm">DİREKTÖR</span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div x-show="selectedDirektorName" class="mt-3 flex items-center bg-amber-50 text-amber-700 border border-amber-100 px-4 py-2 rounded-xl text-xs font-bold animate-fadeIn">
                                    <span class="mr-2">Seçili Direktör:</span>
                                    <span x-text="selectedDirektorName"></span>
                                    <button type="button" @click="resetDirektor()" class="ml-auto text-amber-400 hover:text-rose-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-2 text-[10px] text-slate-400 italic font-medium px-1">* Direktör olmayan bir personel seçildiğinde otomatik terfi ettirilecektir.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ÖZELLİKLER VE ŞABLONLAR --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden transition-all hover:shadow-2xl hover:shadow-slate-200/60">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="h-12 w-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622l-.382-3.040z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Süreç ve Özellikler</h2>
                                <p class="text-sm text-slate-400">Modüller ve iş akışı yapılandırması</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            {{-- Şablon Seçimi --}}
                            <div>
                                <label for="sikayet_workflow_id" class="block text-sm font-bold text-slate-700 mb-2">Şikayet İş Akış Şablonu</label>
                                <div class="relative">
                                    <select name="sikayet_workflow_id" id="sikayet_workflow_id"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-700 appearance-none focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white transition-all duration-300">
                                        <option value="">Sistem Varsayılan Şablonu Kullan</option>
                                        @foreach($workflows as $workflow)
                                            <option value="{{ $workflow->id }}" {{ old('sikayet_workflow_id') == $workflow->id ? 'selected' : '' }}>
                                                {{ $workflow->name }} {{ $workflow->is_default ? '(⭐ Varsayılan)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Logo Yükleme --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Bölüm Logosu</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-slate-200 border-dashed rounded-3xl cursor-pointer bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition-all overflow-hidden relative">
                                        <template x-if="!logoPreview">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-10 h-10 mb-4 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                </svg>
                                                <p class="mb-2 text-sm text-slate-500"><span class="font-semibold">Logo yüklemek için tıklayın</span> veya sürükleyin</p>
                                                <p class="text-xs text-slate-400">SVG, PNG, JPG (Maks. 2MB)</p>
                                            </div>
                                        </template>
                                        <template x-if="logoPreview">
                                            <div class="w-full h-full relative group">
                                                <img :src="logoPreview" class="w-full h-full object-contain p-4" />
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                    <span class="text-white text-xs font-bold bg-white/20 backdrop-blur-md px-4 py-2 rounded-full border border-white/30">Değiştirmek için tıklayın</span>
                                                </div>
                                            </div>
                                        </template>
                                        <input type="file" name="logo_yolu" class="hidden" @change="handleLogoChange($event)" />
                                    </label>
                                </div>
                            </div>

                            {{-- Makine Yönetimi Switch --}}
                            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 flex items-center justify-between group hover:bg-emerald-50/30 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center text-emerald-600 shadow-sm border border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-700">Makine Yönetimi</h4>
                                        <p class="text-[11px] text-slate-400">Bu bölümde makineler ve bakım takip edilsin mi?</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="has_machines" value="1" class="sr-only peer" {{ old('has_machines') ? 'checked' : '' }}>
                                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-500/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORM AKSİYONLARI --}}
                <div class="flex items-center justify-between pt-4">
                    <button type="submit" 
                            class="flex-1 bg-indigo-600 text-white font-black text-sm uppercase tracking-widest py-5 rounded-3xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:shadow-2xl hover:shadow-indigo-300 transition-all duration-300 transform active:scale-95 mr-4">
                        Departmanı Sisteme Kaydet
                    </button>
                    <a href="{{ route('admin.bolumler.index') }}" 
                       class="px-10 py-5 bg-white text-slate-500 font-bold text-sm rounded-3xl border border-slate-200 hover:bg-slate-50 transition-all">
                        İptal
                    </a>
                </div>
            </form>
        </div>

        {{-- HIZLI KATEGORİ EKLEME MODALI --}}
        <div x-show="showCategoryModal" 
             class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCategoryModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                     @click="showCategoryModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCategoryModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                    <div class="bg-white px-8 pt-8 pb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Yeni Kategori Ekle</h3>
                            <button @click="showCategoryModal = false" class="text-slate-400 hover:text-rose-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-400 mb-2 tracking-widest">Kategori Adı</label>
                            <input x-model="newCategoryName" type="text" 
                                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-slate-700 font-bold focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-300"
                                   placeholder="Kategori ismini yazın..."
                                   @keyup.enter="saveCategory()">
                        </div>
                        <div x-show="categoryError" class="mt-3 text-rose-500 text-xs font-bold px-2 animate-bounce" x-text="categoryError"></div>
                    </div>
                    <div class="bg-slate-50 px-8 py-6 flex flex-row-reverse gap-3">
                        <button type="button" @click="saveCategory()" :disabled="categorySaving"
                                class="inline-flex justify-center flex-1 py-3 px-10 border border-transparent shadow-lg shadow-indigo-100 text-xs font-black uppercase tracking-widest rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-all">
                            <span x-show="!categorySaving">Oluştur</span>
                            <span x-show="categorySaving" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                İşleniyor...
                            </span>
                        </button>
                        <button type="button" @click="showCategoryModal = false"
                                class="inline-flex justify-center flex-1 py-3 px-10 border border-slate-200 text-xs font-black uppercase tracking-widest rounded-2xl text-slate-500 bg-white hover:bg-slate-50 transition-all">
                            Vazgeç
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function bolumSistemi() {
            return {
                // Kategori Verileri
                categories: @json($kategoriler),
                selectedCategory: "{{ old('bolum_kategori_id') }}",
                
                // Modal ve Ekleme
                showCategoryModal: false,
                newCategoryName: '',
                categorySaving: false,
                categoryError: '',
                logoPreview: null,

                // Lider Arama
                liderler: @json($atanabilirPersonel),
                liderSearch: '',
                showLiderList: false,
                selectedLiderId: "{{ old('lider_id') }}",
                selectedLiderName: '',

                // Direktör Arama
                direktorler: @json($directors),
                direktorSearch: '',
                showDirektorList: false,
                selectedDirektorId: "{{ old('director_id', '') }}",
                selectedDirektorName: '',

                init() {
                    // Sayfa açılışında kayıtlı verileri set et (validation return vb durumlar için)
                    if (this.selectedLiderId) {
                        const user = this.liderler.find(u => u.id == this.selectedLiderId);
                        if (user) this.selectedLiderName = user.name;
                    }
                    if (this.selectedDirektorId) {
                        const user = this.direktorler.find(u => u.id == this.selectedDirektorId);
                        if (user) this.selectedDirektorName = user.name;
                    }
                },

                get filteredLiderler() {
                    if (this.liderSearch === '') return [];
                    return this.liderler.filter(u => 
                        u.name.toLowerCase().includes(this.liderSearch.toLowerCase())
                    ).slice(0, 8);
                },

                get filteredDirektorler() {
                    if (this.direktorSearch === '') return this.direktorler.slice(0, 5); // Boşken mevcut direktörleri göster
                    // Aramada atanabilir personellerden de bak (direktörlüğe terfi için)
                    const allPossible = [...this.liderler]; // Tüm atanabilirler
                    // Mevcut direktörlerle birleştir ve unique yap
                    this.direktorler.forEach(d => {
                        if (!allPossible.find(p => p.id == d.id)) allPossible.push({...d, is_director: true});
                    });

                    return allPossible.filter(u => 
                        u.name.toLowerCase().includes(this.direktorSearch.toLowerCase())
                    ).map(u => ({
                        ...u, 
                        is_director: this.direktorler.some(d => d.id == u.id)
                    })).slice(0, 8);
                },

                selectLider(user) {
                    this.selectedLiderId = user.id;
                    this.selectedLiderName = user.name;
                    this.liderSearch = '';
                    this.showLiderList = false;
                },

                resetLider() {
                    this.selectedLiderId = '';
                    this.selectedLiderName = '';
                },

                selectDirektor(user) {
                    this.selectedDirektorId = user.id;
                    this.selectedDirektorName = user.name;
                    this.direktorSearch = '';
                    this.showDirektorList = false;
                },

                resetDirektor() {
                    this.selectedDirektorId = '';
                    this.selectedDirektorName = '';
                },

                handleLogoChange(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.logoPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                async saveCategory() {
                    if (!this.newCategoryName) {
                        this.categoryError = 'Lütfen kategori adı yazın.';
                        return;
                    }

                    this.categorySaving = true;
                    this.categoryError = '';

                    try {
                        const response = await fetch("{{ route('admin.bolum-kategorileri.store-ajax') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ad: this.newCategoryName })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.categories.push(data.category);
                            this.selectedCategory = data.category.id;
                            this.showCategoryModal = false;
                            this.newCategoryName = '';
                            // Başarı bildirimi fırlatılabilir (Toastr vb varsa)
                        } else {
                            this.categoryError = data.message || 'Bir hata oluştu.';
                        }
                    } catch (error) {
                        this.categoryError = 'Bağlantı hatası veya yetki sorunu.';
                    } finally {
                        this.categorySaving = false;
                    }
                }
            }
        }
    </script>
    @endpush

    @push('styles')
    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }
        .animate-shake { animation: shake 0.6s cubic-bezier(.36,.07,.19,.97) both; }
        
        /* Tarayıcı varsayılan oklarını kesin olarak gizle */
        select {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            background-image: none !important;
        }
        select::-ms-expand {
            display: none !important;
        }
    </style>
    @endpush
</x-app-layout>