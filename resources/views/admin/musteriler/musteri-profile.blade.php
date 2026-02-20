<x-app-layout>
    {{-- === 1. HEADER (Kompakt & Şık) === --}}
    <div class="relative bg-slate-900 pt-6 pb-16 overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')]"></div>
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-indigo-500 blur-3xl opacity-20"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- YENİ EKLENEN KISIM: ÜST NAVİGASYON (GERİ DÖN BUTONU) --}}
                @role('Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Bölüm Lideri|Bölüm Kalite Yöneticisi')
                    <div class="mb-6">
                        <a href="{{ route('admin.musteriler.index') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors text-sm font-medium group">
                            <svg class="w-4 h-4 mr-1 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Müşteri Listesine Geri Dön
                        </a>
                    </div>
                @endrole
            {{-- [BİTİŞ] --}}


            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                
                {{-- Logo ve İsim --}}
                <div class="flex items-center gap-5 w-full md:w-auto">
                    <div class="relative group flex-shrink-0">
                        <div class="relative h-20 w-20 bg-white rounded-xl p-2 flex items-center justify-center shadow-2xl">
                            @if($customer->logo_path)
                                <img src="{{ asset('storage/' . $customer->logo_path) }}" alt="{{ $customer->name }}" class="object-contain h-full w-full">
                            @else
                                <span class="text-2xl font-black text-slate-800">{{ substr($customer->name, 0, 1) }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-white">
                        <h1 class="text-2xl font-bold tracking-tight leading-tight">{{ $customer->name }}</h1>
                        <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs text-slate-300">
                            @if($customer->tax_number)
                                <span class="flex items-center gap-1 bg-white/5 px-2.5 py-0.5 rounded-full border border-white/10">
                                    <svg class="w-3 h-3 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    VN: {{ $customer->tax_number }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1 bg-white/5 px-2.5 py-0.5 rounded-full border border-white/10">
                                <svg class="w-3 h-3 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $customer->location_type }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Şikayet Oluştur Butonu --}}
                @if((auth()->user()->is_personnel || auth()->user()->customer_id == $customer->id) && !auth()->user()->hasRole('Direktör'))
                <div>
                    <a href="{{ route('admin.sikayetler.create', ['musteri_id' => $customer->id]) }}" 
                       class="group relative inline-flex items-center px-5 py-2.5 bg-white text-indigo-600 font-bold rounded-lg shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5 text-sm">
                        <svg class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Şikayet Oluştur
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- === 2. BİLGİ PANELLERİ (İletişim & Yetkililer) === --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- İletişim Kartı --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 h-full">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Kurumsal İletişim</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase text-gray-400 font-bold">Adres</span>
                            <span class="text-gray-800 text-sm leading-snug block">{{ $customer->address ?? 'Belirtilmemiş' }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-50 text-green-600 rounded-lg flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] uppercase text-gray-400 font-bold">Telefon</span>
                                <span class="text-gray-800 font-medium text-sm truncate block">{{ $customer->phone ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] uppercase text-gray-400 font-bold">E-Posta</span>
                                <a href="mailto:{{ $customer->email }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm truncate block">{{ $customer->email ?? '-' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Yetkililer Kartı (Yönetilebilir) --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 h-full flex flex-col relative overflow-hidden">
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-2">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Firma Yetkilileri</h3>
                    
                    {{-- ADMIN: HIZLI EKLE BUTONU --}}
                    @role('Superadmin|Yonetim|Bölüm Lideri')
                    <button onclick="document.getElementById('addRepModal').classList.remove('hidden')" 
                            class="text-xs flex items-center gap-1 bg-indigo-50 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-100 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Ekle
                    </button>
                    @endrole
                </div>
                
                <div class="flex-1 overflow-y-auto max-h-48 space-y-3 pr-2 scrollbar-thin scrollbar-thumb-gray-200">
                    @forelse($temsilciler as $temsilci)
                        <div class="group flex items-start justify-between p-2 hover:bg-slate-50 rounded-lg transition border border-transparent hover:border-slate-100">
                            <div class="flex items-start gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600 flex items-center justify-center font-bold text-xs border border-white shadow-sm flex-shrink-0">
                                    {{ substr($temsilci->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-800 text-sm truncate">{{ $temsilci->name }}</div>
                                    <div class="text-[11px] text-gray-500 truncate">{{ $temsilci->unvan ?? 'Ünvan Yok' }}</div>
                                    
                                    {{-- DETAYLI BİLGİLER --}}
                                    <div class="mt-1 flex flex-wrap gap-2 text-[10px] text-gray-400">
                                        @if($temsilci->email)
                                            <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>{{ $temsilci->email }}</span>
                                        @endif
                                        @if($temsilci->telefon)
                                            <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>{{ $temsilci->telefon }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- ADMIN: SİLME BUTONU --}}
                            @role('Superadmin|Yonetim')
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                <form action="{{ route('musteri.yetkili.destroy', $temsilci->id) }}" method="POST" onsubmit="return confirm('Bu yetkiliyi silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                            @endrole
                        </div>
                    @empty
                        <div class="text-sm text-gray-400 italic text-center py-4">Henüz yetkili eklenmemiş.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- === 3. İSTATİSTİK & VERİ MERKEZİ === --}}
    {{-- x-data GÜNCELLENDİ: URL'den 'tab' parametresini okur, yoksa varsayılanı açar --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8" 
         x-data="{ activeTab: new URLSearchParams(window.location.search).get('tab') || 'sikayetler' }">
        
        {{-- İSTATİSTİK KARTLARI (Filtreli - Üst Bölüm) --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            {{-- Toplam Şikayet --}}
            <a href="{{ route('musteri.profil.show', $customer->id) }}" class="relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all group">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Toplam Süreç</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $toplamSikayet }}</p>
                @if(!request('filtre')) <div class="absolute bottom-0 left-0 w-full h-1 bg-slate-800 rounded-b-xl"></div> @endif
            </a>
            
            {{-- Aktif Süreç --}}
            <a href="{{ route('musteri.profil.show', ['customer' => $customer->id, 'filtre' => 'aktif']) }}" class="relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all group">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Aktif Süreç</p>
                <p class="text-2xl font-black text-orange-500 mt-1">{{ $aktifSikayet }}</p>
                @if(request('filtre') == 'aktif') <div class="absolute bottom-0 left-0 w-full h-1 bg-orange-500 rounded-b-xl"></div> @endif
            </a>

            {{-- Tamamlanan Proje --}}
            <a href="{{ route('musteri.profil.show', ['customer' => $customer->id, 'filtre' => 'tamamlanan']) }}" class="relative bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all group">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Proje (Biten)</p>
                <p class="text-2xl font-black text-green-500 mt-1">{{ $tamamlananProje }}</p>
                @if(request('filtre') == 'tamamlanan') <div class="absolute bottom-0 left-0 w-full h-1 bg-green-500 rounded-b-xl"></div> @endif
            </a>

            {{-- İade Toplamı (Statik Özet) --}}
            <div class="relative bg-white rounded-xl shadow-sm border border-red-100 p-4">
                <p class="text-[10px] font-bold text-red-400 uppercase">Toplam İade (Ton)</p>
                <p class="text-2xl font-black text-red-600 mt-1">
                    {{ number_format($iadeToplamlari['Ton'] ?? 0, 0) }} <span class="text-sm font-bold">Ton</span>
                </p>
            </div>
        </div>

        {{-- TEK PENCERE (TABS) --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden min-h-[500px]">
            
            {{-- TAB BAŞLIKLARI --}}
            <div class="flex items-center border-b border-gray-100 bg-gray-50/50 px-6">
                
                {{-- TAB 1: ŞİKAYETLER --}}
                <button @click="activeTab = 'sikayetler'" 
                        :class="activeTab === 'sikayetler' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors mr-8 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Şikayet & Proje Geçmişi
                </button>

                {{-- TAB 2: İADELER (Sadece Personel Görür) --}}
                @if(!Auth::user()->customer_id)
                <button @click="activeTab = 'iadeler'"
                        :class="activeTab === 'iadeler' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                    İade & Maliyet Analizi
                    <span class="ml-1 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs font-bold">{{ $iadeler->count() }}</span>
                </button>
                @endif

                {{-- Filtre Temizle (Sağda) --}}
                @if(request('filtre'))
                    <div class="ml-auto">
                        <a href="{{ route('musteri.profil.show', $customer->id) }}" class="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded-full">Filtreyi Temizle</a>
                    </div>
                @endif
            </div>

            {{-- TAB İÇERİKLERİ --}}
            
            {{-- 1. ŞİKAYETLER TABLOSU --}}
            <div x-show="activeTab === 'sikayetler'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-200 uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="px-6 py-4 w-10">#</th>
                                <th class="px-6 py-4">Konu & Oluşturan</th>
                                <th class="px-6 py-4">Takım</th>
                                <th class="px-6 py-4">Sorumlu</th>
                                <th class="px-6 py-4">Durum</th>
                                <th class="px-6 py-4">Proje</th>
                                <th class="px-6 py-4">Tarih</th>
                                <th class="px-6 py-4 text-right">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($sikayetler as $sikayet)
                                <tr class="hover:bg-indigo-50/30 transition group">
                                    <td class="px-6 py-4 font-medium text-gray-400 text-xs">
                                        {{ ($sikayetler->currentPage() - 1) * $sikayetler->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-bold bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded">#{{ $sikayet->id }}</span>
                                            
                                            {{-- Şikayete Tıklanabilir Link --}}
                                            @if($sikayet->iaaProjesi)
                                                <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" target="_blank" class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline text-sm truncate max-w-[200px]" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                                    {{ Str::limit($sikayet->musteri_sikayet_konusu, 30) }}
                                                </a>
                                            @else
                                                <div class="font-bold text-gray-800 text-sm truncate max-w-[200px]" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                                    {{ Str::limit($sikayet->musteri_sikayet_konusu, 30) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-gray-400 flex items-center gap-1">
                                            @if($sikayet->olusturanKurulUyesi)
                                                <span>{{ $sikayet->olusturanKurulUyesi->name }}</span>
                                            @else
                                                <span>Bilinmiyor</span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        @if($sikayet->cozumTakimi)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">{{ $sikayet->cozumTakimi->ad }}</span>
                                        @else
                                            <span class="text-gray-400 text-xs italic">- Atanmadı -</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($sikayet->cozumTakimi && $sikayet->cozumTakimi->lider)
                                            <span class="text-sm font-medium text-gray-700">{{ $sikayet->cozumTakimi->lider->name }}</span>
                                        @else
                                            <span class="text-gray-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{!! $sikayet->musteri_durum_badge !!}</td>
                                    <td class="px-6 py-4">
                                        @if($sikayet->iaaProjesi)
                                            <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-emerald-500 to-green-600 text-white hover:from-emerald-600 hover:to-green-700 shadow-sm hover:shadow transition transform hover:-translate-y-0.5">
                                                <span>Proje</span>
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                            </a>
                                        @else
                                            <span class="text-gray-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $sikayet->created_at->format('d.m.Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-indigo-600 hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-6 py-16 text-center text-gray-500">Kayıtlı işlem bulunamadı.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">{{ $sikayetler->links() }}</div>
            </div>

            {{-- 2. İADELER TABLOSU (Sadece Personel) --}}
            @if(!Auth::user()->customer_id)
            <div x-show="activeTab === 'iadeler'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                
                {{-- ÜST FİLTRE VE ÖZET ALANI --}}
                <div class="bg-slate-50 border-b border-slate-200 p-6">
                    
                    {{-- Yıl Filtresi ve Başlık --}}
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wide">İade Genel Görünümü</h4>
                            {{-- Dinamik Bilgi Etiketi --}}
                            <span class="text-[10px] bg-white border border-slate-200 px-2 py-0.5 rounded text-slate-500 font-medium">
                                {{ request('yil') ? request('yil').' Verileri' : 'Tüm Zamanlar' }}
                            </span>
                        </div>
                        
                        {{-- Yıl Seçimi Formu --}}
                        <form action="{{ route('musteri.profil.show', $customer->id) }}" method="GET" class="flex items-center gap-2">
                            {{-- SEKME HAFIZASI: Bu hidden input, sayfa yenilenince 'iadeler' sekmesinin açılmasını sağlar --}}
                            <input type="hidden" name="tab" value="iadeler">
                            
                            <select name="yil" onchange="this.form.submit()" class="text-xs border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 pl-3 pr-8 bg-white cursor-pointer hover:bg-gray-50">
                                <option value="">Tüm Yıllar</option>
                                @foreach($mevcutYillar as $yil)
                                    <option value="{{ $yil }}" {{ request('yil') == $yil ? 'selected' : '' }}>{{ $yil }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    {{-- KÜÇÜLTÜLMÜŞ DETAYLI KARTLAR --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        
                        {{-- Kart 1: Toplam Vaka --}}
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group">
                            <div class="absolute right-0 top-0 p-3 opacity-5 group-hover:opacity-10 transition-opacity">
                                <svg class="w-16 h-16 text-slate-800" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Toplam İade Kaydı</p>
                            <p class="text-2xl font-black text-slate-800 mt-1">{{ $iadeler->count() }} <span class="text-sm font-medium text-slate-400">Adet</span></p>
                            <div class="mt-3 flex flex-wrap gap-1">
                                @php
                                    $bolumVakaSayilari = $iadeler->groupBy(function($item) {
                                        return $item->sikayet->sikayetKategori->bolum->ad ?? 'Diğer';
                                    })->map->count();
                                @endphp
                                @foreach($bolumVakaSayilari as $bolum => $sayi)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $bolum }}: {{ $sayi }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Kart 2 & 3: Birim Bazlı Toplamlar --}}
                        @foreach($iadeToplamlari as $birim => $toplam)
                            <div class="bg-white p-4 rounded-xl border border-red-100 shadow-sm relative overflow-hidden group">
                                <div class="absolute right-0 top-0 p-3 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <svg class="w-16 h-16 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                </div>

                                <p class="text-[10px] font-bold text-red-400 uppercase tracking-wider">Toplam İade ({{ $birim }})</p>
                                <p class="text-2xl font-black text-red-600 mt-1 tracking-tight">{{ number_format($toplam, 2) }} <span class="text-sm font-bold text-red-300">{{ $birim }}</span></p>
                                
                                {{-- Bölüm Kırılımı --}}
                                <div class="mt-3 space-y-1.5 relative z-10">
                                    @if(isset($bolumKirilimi[$birim]))
                                        @foreach($bolumKirilimi[$birim] as $bolumAdi => $miktar)
                                            @php $yuzde = $toplam > 0 ? ($miktar / $toplam) * 100 : 0; @endphp
                                            <div>
                                                <div class="flex justify-between text-[9px] mb-0.5">
                                                    <span class="font-bold text-slate-600">{{ $bolumAdi }}</span>
                                                    <span class="font-medium text-slate-500">{{ number_format($miktar, 0) }}</span>
                                                </div>
                                                <div class="w-full bg-red-50 rounded-full h-1">
                                                    <div class="bg-red-500 h-1 rounded-full" style="width: {{ $yuzde }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                {{-- DETAYLI LİSTE TABLOSU --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 font-semibold border-b border-gray-200 uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-6 py-4 w-10">#</th>
                                <th class="px-6 py-4">Tarih</th>
                                <th class="px-6 py-4">Bölüm</th>
                                <th class="px-6 py-4">Kaynak Şikayet (Projeye Git)</th>
                                <th class="px-6 py-4">Ürün Grubu & Sebep</th>
                                <th class="px-6 py-4 text-right">Miktar</th>
                                <th class="px-6 py-4 text-center">Belge</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($iadeler as $iade)
                                <tr class="hover:bg-red-50/20 transition group">
                                    {{-- 1. SIRA NO --}}
                                    <td class="px-6 py-4 font-medium text-gray-400 text-xs">
                                        {{ $loop->iteration }}
                                    </td>
                                    
                                    {{-- 2. İADE TARİHİ (Güncellendi) --}}
                                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap text-xs">
                                        <div class="font-bold text-slate-700">{{ $iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : '-' }}</div>
                                        <div class="text-[9px] text-slate-400">İşlem: {{ $iade->created_at->format('d.m') }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap text-xs font-mono">
                                        {{ $iade->created_at->format('d.m.Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $iade->sikayet->sikayetKategori->bolum->ad ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($iade->sikayet && $iade->sikayet->iaaProjesi)
                                            <a href="{{ route('proje.workspace.show', $iade->sikayet->iaaProjesi->id) }}" target="_blank" class="flex items-center group/link">
                                                <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-md group-hover/link:bg-indigo-600 group-hover/link:text-white transition mr-3">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </div>
                                                <div>
                                                    <span class="block font-bold text-gray-800 text-xs group-hover/link:text-indigo-600 transition truncate max-w-[250px]" title="{{ $iade->sikayet->musteri_sikayet_konusu }}">
                                                        {{ $iade->sikayet->musteri_sikayet_konusu }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400">Proje ID: #{{ $iade->sikayet->iaaProjesi->id }}</span>
                                                </div>
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic text-xs">Proje bağlantısı yok</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-gray-800">{{ $iade->urun_turu }}</div>
                                        <div class="text-[10px] text-red-500 mt-0.5">{{ $iade->iade_sebebi }}</div>
                                    </td>
                                    {{-- 5. MİKTAR (GÜNCELLENDİ) --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="text-xs font-bold text-gray-700">
                                            <span class="text-red-600 text-sm">{{ $iade->miktar }}</span> 
                                            <span class="text-gray-400">/</span> 
                                            {{ $iade->toplam_parti_miktari }} 
                                            {{ $iade->birim }}
                                        </div>
                                        @php $oran = ($iade->toplam_parti_miktari > 0) ? ($iade->miktar / $iade->toplam_parti_miktari) * 100 : 0; @endphp
                                        <div class="text-[9px] text-red-500 font-bold">%{{ number_format($oran, 1) }} İade</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($iade->dosya_yolu)
                                            <a href="{{ asset('storage/'.$iade->dosya_yolu) }}" target="_blank" class="text-gray-400 hover:text-indigo-600 transition-colors inline-block p-1" title="Dosyayı Görüntüle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            </a>
                                        @else
                                            <span class="text-gray-200 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-16 text-center text-gray-500 italic bg-white">Bu filtreye uygun iade kaydı bulunmuyor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- === 5. GÜVENLİK GEÇMİŞİ (Sadece Superadmin ve Yönetim Görür) === --}}
    @if(auth()->user()->hasRole(['Superadmin', 'Super Admin', 'Yonetim']))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pb-12">
        <div class="bg-slate-800 rounded-xl shadow-lg p-6 text-slate-300 border border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        İşlem Geçmişi
                    </h3>
                    <span class="text-xs bg-slate-700 px-2 py-1 rounded text-slate-400">Son 10 Kayıt</span>
                </div>
                
                {{-- TÜMÜNÜ GÖR BUTONU --}}
                <a href="{{ route('musteri-logs.index') }}" target="_blank" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-1 transition">
                    Tüm Kayıtları İncele
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-700">
                <table class="w-full text-xs text-left">
                    <thead class="text-slate-400 uppercase bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tarih</th>
                            <th class="px-4 py-3 font-semibold">İşlem</th>
                            <th class="px-4 py-3 font-semibold">Kullanıcı</th>
                            <th class="px-4 py-3 font-semibold">Açıklama</th>
                            <th class="px-4 py-3 font-semibold">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700 bg-slate-800">
                        @if(isset($logs) && $logs->count() > 0)
                            @foreach($logs as $log)
                                <tr class="hover:bg-slate-700/50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap text-slate-400">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="px-4 py-3 font-bold text-white whitespace-nowrap">
                                        @if(Str::contains($log->islem_turu, 'Silme'))
                                            <span class="text-red-400">{{ $log->islem_turu }}</span>
                                        @elseif(Str::contains($log->islem_turu, 'Ekleme') || Str::contains($log->islem_turu, 'Oluşturma'))
                                            <span class="text-green-400">{{ $log->islem_turu }}</span>
                                        @else
                                            <span class="text-blue-400">{{ $log->islem_turu }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-slate-300">
                                        {{ $log->user ? $log->user->name : 'Sistem/Misafir' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">{{ $log->aciklama }}</td>
                                    <td class="px-4 py-3 font-mono text-slate-500 whitespace-nowrap">{{ $log->ip_adresi }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500 italic">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <span>Bu müşteriye ait henüz bir işlem kaydı bulunmuyor.</span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- === 6. YETKİLİ EKLEME MODALI === --}}
    @role('Superadmin|Yonetim|Bölüm Lideri')
    <div id="addRepModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addRepModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('musteri.yetkili.store', $customer->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Yeni Yetkili Ekle</h3>
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700">Ad Soyad *</label><input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
                            <div><label class="block text-sm font-medium text-gray-700">E-Posta *</label><input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-sm font-medium text-gray-700">Telefon</label><input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
                                <div><label class="block text-sm font-medium text-gray-700">Ünvan</label><input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
                            </div>
                            <p class="text-xs text-orange-600 bg-orange-50 p-2 rounded">Kullanıcıya otomatik şifre atanacaktır.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Kaydet</button>
                        <button type="button" onclick="document.getElementById('addRepModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">İptal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endrole

    {{-- Script Mesajları --}}
    @if(session('success'))
        <div class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-xl z-50 animate-bounce">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="fixed bottom-5 right-5 bg-red-500 text-white px-6 py-3 rounded-lg shadow-xl z-50">{{ session('error') }}</div>
    @endif
</x-app-layout>