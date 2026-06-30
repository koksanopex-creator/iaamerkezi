@push('pageTitle')
    Bölüm Yönetimi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Bölüm Yönetimi') }}
            </h2>
            <a href="{{ route('admin.bolumler.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition-all duration-300 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Yeni Bölüm Ekle
            </a>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50 min-h-screen" x-data="{ 
        viewMode: localStorage.getItem('bolum_view_mode') || 'grid',
        updateViewMode(mode) {
            this.viewMode = mode;
            localStorage.setItem('bolum_view_mode', mode);
        }
    }">
        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">

            {{-- MODERN KATEGORİ ETİKETLERİ (TAGS) --}}
            <div class="mb-8 overflow-x-auto pb-2 scrollbar-hide">
                <div class="flex items-center gap-3 min-w-max">
                    <a href="{{ route('admin.bolumler.index', array_merge(request()->except('bolum_kategori_id', 'page'))) }}" 
                        class="px-4 py-2 rounded-xl text-sm font-bold transition-all border-2 flex items-center gap-2 {{ !request('bolum_kategori_id') ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg' : 'bg-white border-gray-100 text-gray-500 hover:border-indigo-200 hover:text-indigo-600' }}">
                        <span>Tümü</span>
                        <span class="px-2 py-0.5 rounded-lg text-[10px] {{ !request('bolum_kategori_id') ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-400' }}">{{ $totalBolumCount }}</span>
                    </a>
                    @foreach($categoryStats as $kategori)
                        <a href="{{ route('admin.bolumler.index', array_merge(request()->except('page'), ['bolum_kategori_id' => $kategori->id])) }}" 
                            class="px-4 py-2 rounded-xl text-sm font-bold transition-all border-2 flex items-center gap-2 {{ request('bolum_kategori_id') == $kategori->id ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg' : 'bg-white border-gray-100 text-gray-500 hover:border-indigo-200 hover:text-indigo-600' }}">
                            <span>{{ $kategori->ad }}</span>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] {{ request('bolum_kategori_id') == $kategori->id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-400' }}">{{ $kategori->bolumler_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ÜST ARAÇ ÇUBUĞU: ARAMA, SIRALAMA VE GÖRÜNÜM SEÇİCİ --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-4 mb-8">
                <form method="GET" action="{{ route('admin.bolumler.index') }}" class="flex flex-col lg:flex-row items-center gap-4">
                    <input type="hidden" name="bolum_kategori_id" value="{{ request('bolum_kategori_id') }}">
                    
                    {{-- Arama --}}
                    <div class="relative flex-grow w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="ad" value="{{ request('ad') }}" placeholder="Bölüm adı ile arayın..." 
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 rounded-2xl text-sm transition-all border border-gray-100">
                    </div>

                    {{-- Sıralama Criteria --}}
                    <div class="flex items-center gap-2 w-full lg:w-auto">
                        <select name="sort_by" onchange="this.form.submit()"
                            class="bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 rounded-2xl text-sm py-3 px-4 border border-gray-100 w-full lg:w-48">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>En Yeni</option>
                            <option value="ad" {{ request('sort_by') == 'ad' ? 'selected' : '' }}>Alfabetik (İsim)</option>
                            <option value="users_count" {{ request('sort_by') == 'users_count' ? 'selected' : '' }}>Personel Sayısı</option>
                            <option value="machines_count" {{ request('sort_by') == 'machines_count' ? 'selected' : '' }}>Makine Sayısı</option>
                            <option value="sikayetler_count" {{ request('sort_by') == 'sikayetler_count' ? 'selected' : '' }}>Şikayet Sayısı</option>
                        </select>
                        <select name="sort_order" onchange="this.form.submit()"
                            class="bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 rounded-2xl text-sm py-3 px-4 border border-gray-100 w-full lg:w-32">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Azalan</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Artan</option>
                        </select>
                    </div>

                    {{-- Görünüm Seçici --}}
                    <div class="flex bg-gray-100 p-1 rounded-2xl">
                        <button type="button" @click="updateViewMode('grid')"
                            :class="viewMode === 'grid' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="p-2 rounded-xl transition-all duration-200 flex items-center gap-2 px-3 text-xs font-bold font-sans">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>Kart</span>
                        </button>
                        <button type="button" @click="updateViewMode('list')"
                            :class="viewMode === 'list' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="p-2 rounded-xl transition-all duration-200 flex items-center gap-2 px-3 text-xs font-bold font-sans">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            <span>Liste</span>
                        </button>
                    </div>

                    {{-- Temizle --}}
                    @if(request()->anyFilled(['ad', 'sort_by', 'sort_order', 'bolum_kategori_id']))
                        <a href="{{ route('admin.bolumler.index') }}" class="text-xs font-bold text-rose-500 hover:text-rose-600 uppercase tracking-tighter transition-colors whitespace-nowrap">
                            Filtreleri Temizle
                        </a>
                    @endif
                </form>
            </div>

            {{-- SUCCESS MESAJI --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 p-4 mb-8 rounded-3xl flex items-center gap-3 animate-fade-in" x-data="{ show: true }" x-show="show">
                    <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <p class="text-sm text-emerald-800 font-bold">{{ session('success') }}</p>
                    <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
            @endif


            {{-- GRID CARD LOOP --}}
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 animate-fade-in">
                @forelse ($bolumler as $bolum)
                    <div class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col h-full group overflow-hidden">
                        
                        {{-- Kart Header --}}
                        <div class="p-6 pb-2 flex justify-between items-start">
                            {{-- Logo --}}
                            <div class="flex-shrink-0">
                                @if($bolum->logo_yolu)
                                    <img src="{{ asset('storage/' . $bolum->logo_yolu) }}" alt="{{ $bolum->ad }}" class="h-16 w-16 rounded-2xl object-cover border-2 border-gray-50 shadow-sm">
                                @else
                                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-sm">
                                        {{ substr($bolum->ad, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Durum & Kategori --}}
                            <div class="flex flex-col items-end gap-2">
                                @if($bolum->kategori)
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full uppercase tracking-wider">
                                        {{ $bolum->kategori->ad }}
                                    </span>
                                @endif
                                <span class="flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $bolum->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $bolum->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $bolum->is_active ? 'AKTİF' : 'PASİF' }}
                                </span>
                            </div>
                        </div>

                        {{-- Kart Body --}}
                        <div class="p-6 pt-2 flex-grow">
                             <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2 min-h-[3.5rem] mb-4" title="{{ $bolum->ad }}">
                                {{ $bolum->ad }}
                             </h3>

                             <div class="space-y-4">
                                 {{-- PERSONEL SAYILARI (YENİ) --}}
                                 <div class="grid grid-cols-3 gap-2 bg-gray-50/50 rounded-2xl p-3 border border-gray-100">
                                     <div class="text-center">
                                         <p class="text-[9px] font-bold text-blue-400 uppercase">Mavi</p>
                                         <p class="text-sm font-black text-blue-700">{{ $bolum->mavi_yaka_count }}</p>
                                     </div>
                                     <div class="text-center border-x border-gray-200">
                                         <p class="text-[9px] font-bold text-indigo-400 uppercase">Beyaz</p>
                                         <p class="text-sm font-black text-indigo-700">{{ $bolum->beyaz_yaka_count }}</p>
                                     </div>
                                     <div class="text-center">
                                         <p class="text-[9px] font-bold text-gray-400 uppercase">Top</p>
                                         <p class="text-sm font-black text-gray-700">{{ $bolum->users_count }}</p>
                                     </div>
                                 </div>

                                 {{-- Direktör (YENİ) --}}
                                 <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        DİREKTÖR
                                    </p>
                                    @if($bolum->director)
                                        <div class="flex items-center gap-2 bg-indigo-50/50 rounded-xl p-1.5 pr-3 border border-indigo-100/50 shadow-sm">
                                            @if($bolum->director->profile_photo_path)
                                                <img src="{{ asset('storage/' . $bolum->director->profile_photo_path) }}" class="w-7 h-7 rounded-full object-cover">
                                            @else
                                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] text-indigo-700 font-black uppercase">
                                                    {{ substr($bolum->director->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="text-[11px] font-black text-indigo-900">{{ $bolum->director->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-[11px] font-medium bg-gray-50 px-3 py-1 rounded-lg border border-dashed border-gray-200 flex items-center gap-1">
                                            Atanmamış
                                        </span>
                                    @endif
                                 </div>

                                {{-- Liderler --}}
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        BÖLÜM LİDERLERİ
                                    </p>
                                    @if($bolum->users->isNotEmpty())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($bolum->users->take(3) as $lider)
                                                 <a href="{{ route('profile.show', $lider->id) }}" class="flex items-center gap-2 bg-white hover:bg-indigo-50 rounded-xl p-1 pr-3 transition-all border border-gray-100 hover:border-indigo-200 shadow-sm" title="{{ $lider->name }}">
                                                     @if($lider->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $lider->profile_photo_path) }}" class="w-6 h-6 rounded-full object-cover">
                                                     @else
                                                        <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] text-indigo-700 font-bold uppercase">
                                                            {{ substr($lider->name, 0, 1) }}
                                                        </div>
                                                     @endif
                                                     <span class="text-[11px] font-bold text-gray-700 truncate max-w-[80px]">{{ $lider->name }}</span>
                                                 </a>
                                            @endforeach
                                            @if($bolum->users->count() > 3)
                                                <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-gray-100 text-[10px] font-bold text-gray-500 border border-gray-200" title="Diğer liderler">
                                                    +{{ $bolum->users->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-[11px] font-medium bg-gray-50 px-3 py-1 rounded-lg border border-dashed border-gray-200 flex items-center gap-1">
                                            Lider Atanmamış
                                        </span>
                                    @endif
                                </div>

                                 {{-- Makine & Şikayet --}}
                                 <div class="grid grid-cols-2 gap-3">
                                     @if($bolum->has_machines)
                                         <div class="flex flex-col bg-slate-50 rounded-2xl p-2.5 text-center border border-slate-100 group/item hover:bg-slate-100 transition-colors">
                                             <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Makine</span>
                                             <div class="flex items-center justify-center gap-1.5 font-black text-slate-700 text-sm">
                                                 <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                 {{ $bolum->machines_count }}
                                             </div>
                                         </div>
                                     @endif

                                     @if($bolum->sikayet_kategorileri_count > 0)
                                         <a href="{{ route('admin.bolumler.dashboard', $bolum->id) }}#sikayetler" 
                                            class="flex flex-col rounded-2xl p-2.5 text-center transition-all duration-200 border {{ !$bolum->has_machines ? 'col-span-2' : '' }} {{ $bolum->sikayetler_count > 0 ? 'bg-rose-50 border-rose-100 hover:bg-rose-100' : 'bg-gray-50 border-gray-100 hover:bg-gray-100' }}">
                                             <span class="text-[9px] font-bold {{ $bolum->sikayetler_count > 0 ? 'text-rose-400' : 'text-gray-400' }} uppercase tracking-wide">Şikayet</span>
                                             <div class="flex items-center justify-center gap-1.5 font-black {{ $bolum->sikayetler_count > 0 ? 'text-rose-700' : 'text-gray-700' }} text-sm">
                                                 <svg class="h-4 w-4 {{ $bolum->sikayetler_count > 0 ? 'text-rose-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                 {{ $bolum->sikayetler_count }}
                                             </div>
                                         </a>
                                     @endif
                                 </div>
                             </div>
                        </div>

                        {{-- Kart Footer --}}
                        <div class="px-6 py-4 bg-gray-50 flex justify-between items-center border-t border-gray-100">
                            <a href="{{ route('admin.bolumler.dashboard', $bolum) }}" class="text-xs font-black text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1 group/link">
                                YÖNETİM
                                <svg class="h-3 w-3 transition-transform group-hover/link:translate-x-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            </a>
                            
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.bolumler.edit', $bolum) }}" class="text-gray-400 hover:text-indigo-600 transition p-2 hover:bg-white rounded-xl shadow-sm border border-transparent hover:border-gray-100" title="Düzenle">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                </a>
                                <form action="{{ route('admin.bolumler.destroy', $bolum) }}" method="POST" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-white rounded-xl shadow-sm border border-transparent hover:border-gray-100" title="Sil">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Boş Durum Grid --}}
                    <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 border border-gray-200 shadow-inner">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Henüz Bölüm Bulunamadı</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-8">Arama kriterlerinize uygun bir sonuç yok.</p>
                        <a href="{{ route('admin.bolumler.index') }}" class="px-6 py-2 bg-indigo-100 text-indigo-700 rounded-xl font-bold hover:bg-indigo-200 transition-all">Tümünü Göster</a>
                    </div>
                @endforelse
            </div>

            {{-- LIST (TABLE) VIEW --}}
            <div x-show="viewMode === 'list'" class="animate-fade-in bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Bölüm / Logo</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Direktör</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Personel (M/B/T)</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Makine/Şikayet</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Liderler</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Durum</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($bolumler as $bolum)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            @if($bolum->logo_yolu)
                                                <img src="{{ asset('storage/' . $bolum->logo_yolu) }}" class="h-10 w-10 rounded-xl object-cover border border-gray-100">
                                            @else
                                                <div class="h-10 w-10 rounded-xl bg-indigo-500 flex items-center justify-center text-white font-bold text-xs">{{ substr($bolum->ad, 0, 1) }}</div>
                                            @endif
                                            <span class="text-sm font-bold text-gray-900 group-hover:text-indigo-600">{{ $bolum->ad }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($bolum->kategori)
                                            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-[10px] font-black rounded-lg uppercase tracking-tight">
                                                {{ $bolum->kategori->ad }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($bolum->director)
                                            <a href="{{ route('profile.show', $bolum->director->id) }}" class="flex items-center gap-2 group/dir hover:bg-slate-50 p-1 rounded-xl transition-colors">
                                                @if($bolum->director->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $bolum->director->profile_photo_path) }}" class="w-7 h-7 rounded-full object-cover shadow-sm ring-1 ring-slate-200">
                                                @else
                                                    <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-600 font-black group-hover/dir:bg-indigo-100 group-hover/dir:text-indigo-700 transition-colors">
                                                        {{ substr($bolum->director->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <span class="text-xs font-bold text-gray-700 group-hover/dir:text-indigo-600 transition-colors">{{ $bolum->director->name }}</span>
                                            </a>
                                        @else
                                            <span class="text-gray-300 text-[10px] italic px-2">Atanmamış</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-lg text-xs" title="Mavi Yaka">{{ $bolum->mavi_yaka_count }}</span>
                                            <span class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-lg text-xs" title="Beyaz Yaka">{{ $bolum->beyaz_yaka_count }}</span>
                                            <span class="text-gray-700 font-black bg-gray-100 px-2 py-0.5 rounded-lg text-xs" title="Toplam">{{ $bolum->users_count }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-3">
                                            @if($bolum->has_machines)
                                                <span class="flex items-center gap-1 text-slate-500 font-bold text-xs" title="Makine Yönetimi Aktif">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                                    {{ $bolum->machines_count }}
                                                </span>
                                            @endif

                                            @if($bolum->sikayet_kategorileri_count > 0)
                                                <span class="flex items-center gap-1 {{ $bolum->sikayetler_count > 0 ? 'text-rose-500' : 'text-gray-400' }} font-bold text-xs" title="Şikayet Yönetimi Aktif">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                    {{ $bolum->sikayetler_count }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1.5">
                                            @forelse($bolum->users->take(2) as $lider)
                                                <a href="{{ route('profile.show', $lider->id) }}" class="flex items-center gap-2 group/leader hover:text-indigo-600 transition-colors">
                                                    @if($lider->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $lider->profile_photo_path) }}" class="w-5 h-5 rounded-full object-cover">
                                                    @else
                                                        <div class="w-5 h-5 rounded-full bg-indigo-50 flex items-center justify-center text-[8px] text-indigo-500 font-black uppercase">
                                                            {{ substr($lider->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <span class="text-[11px] font-bold text-gray-600 group-hover/leader:text-indigo-600">{{ $lider->name }}</span>
                                                </a>
                                            @empty
                                                <span class="text-[10px] text-gray-300 italic">Lider yok</span>
                                            @endforelse
                                            
                                            @if($bolum->users->count() > 2)
                                                <span class="text-[9px] font-black text-gray-400 ml-7 tracking-tighter">
                                                    + {{ $bolum->users->count() - 2 }} DİĞER LİDER
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-tight {{ $bolum->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                            {{ $bolum->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.bolumler.dashboard', $bolum) }}" class="p-2 text-indigo-400 hover:text-indigo-600 transition hover:bg-white rounded-xl shadow-sm border border-transparent hover:border-gray-50" title="Yönet">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            </a>
                                            <a href="{{ route('admin.bolumler.edit', $bolum) }}" class="p-2 text-gray-400 hover:text-indigo-600 transition hover:bg-white rounded-xl shadow-sm border border-transparent hover:border-gray-50" title="Düzenle">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                            </a>
                                            <form action="{{ route('admin.bolumler.destroy', $bolum) }}" method="POST" class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)" class="p-2 text-gray-400 hover:text-red-500 transition hover:bg-white rounded-xl shadow-sm border border-transparent hover:border-gray-50" title="Sil">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-12 text-center text-gray-500 font-bold">Kayıt Bulunamadı.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINATION --}}
             <div class="mt-8">
                {{ $bolumler->links() }}
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu bölümü silmek istediğinize emin misiniz? Bu işlem geri alınamaz!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5', // Indigo-600
                cancelButtonColor: '#ef4444', // Red-500
                confirmButtonText: 'Evet, Sil!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
