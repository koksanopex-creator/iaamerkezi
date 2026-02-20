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

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- MODERN İSTATİSTİK KARTLARI --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- Toplam --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500 flex items-center justify-between transition hover:-translate-y-1 hover:shadow-md">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Toplam Bölüm</p>
                        <p class="mt-1 text-3xl font-extrabold text-gray-900">{{ $totalBolumCount }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-full text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>

                {{-- Kategoriler --}}
                @foreach($categoryStats as $stat)
                     @php
                        $colorMap = [
                            ['border-green-500', 'bg-green-50', 'text-green-600'],
                            ['border-blue-500', 'bg-blue-50', 'text-blue-600'],
                            ['border-orange-500', 'bg-orange-50', 'text-orange-600'],
                            ['border-purple-500', 'bg-purple-50', 'text-purple-600'],
                            ['border-pink-500', 'bg-pink-50', 'text-pink-600'],
                            ['border-teal-500', 'bg-teal-50', 'text-teal-600'],
                        ];
                        [$borderColor, $bgColor, $textColor] = $colorMap[$loop->index % count($colorMap)];
                     @endphp
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 {{ $borderColor }} flex items-center justify-between transition hover:-translate-y-1 hover:shadow-md">
                        <div>
                             <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $stat->ad }}</p>
                            <p class="mt-1 text-3xl font-extrabold text-gray-900">{{ $stat->bolumler_count }}</p>
                        </div>
                        <div class="p-3 {{ $bgColor }} rounded-full {{ $textColor }}">
                            {{-- Basit bir klasör/kategori ikonu --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- FİLTRELEME & ARAMA --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
                 <form method="GET" action="{{ route('admin.bolumler.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                     {{-- Arama --}}
                     <div class="md:col-span-5">
                         <label for="ad" class="block text-xs font-semibold text-gray-500 uppercase mb-1">BÖLÜM ARA</label>
                         <div class="relative">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                             </div>
                             <input type="text" name="ad" id="ad" value="{{ request('ad') }}" 
                                 class="pl-10 block w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 transition transaction-duration-200" 
                                 placeholder="Bölüm adı yazın...">
                         </div>
                     </div>

                     {{-- Kategori --}}
                     <div class="md:col-span-3">
                         <label for="bolum_kategori_id" class="block text-xs font-semibold text-gray-500 uppercase mb-1">KATEGORİ</label>
                         <select name="bolum_kategori_id" id="bolum_kategori_id" 
                            class="block w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 transition">
                            <option value="">Tümü</option>
                            @foreach($kategoriler as $kategori)
                                <option value="{{ $kategori->id }}" {{ request('bolum_kategori_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->ad }}
                                </option>
                            @endforeach
                         </select>
                     </div>

                     {{-- Sıralama --}}
                     <div class="md:col-span-2">
                        <label for="sort_machines" class="block text-xs font-semibold text-gray-500 uppercase mb-1">SIRALAMA</label>
                        <select name="sort_machines" id="sort_machines" 
                           class="block w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 transition">
                           <option value="">Varsayılan</option>
                           <option value="desc" {{ request('sort_machines') == 'desc' ? 'selected' : '' }}>En Çok Makine</option>
                           <option value="asc" {{ request('sort_machines') == 'asc' ? 'selected' : '' }}>En Az Makine</option>
                        </select>
                    </div>

                    {{-- Butonlar --}}
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-800 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 shadow-sm flex justify-center items-center gap-2">
                            <span>Filtrele</span>
                        </button>
                        @if(request()->anyFilled(['ad', 'bolum_kategori_id', 'sort_machines']))
                            <a href="{{ route('admin.bolumler.index') }}" class="flex items-center justify-center w-12 bg-gray-200 text-gray-600 rounded-lg hover:bg-gray-300 transition" title="Temizle">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif
                    </div>
                 </form>
            </div>

            {{-- SUCCESS MESAJI --}}
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif


            {{-- GRID CARD LOOP --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                @forelse ($bolumler as $bolum)
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col h-full group">
                        
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
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full uppercase tracking-wider">
                                        {{ $bolum->kategori->ad }}
                                    </span>
                                @endif
                                <span class="flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $bolum->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $bolum->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
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
                                {{-- Liderler --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">LİDERLER</p>
                                    @if($bolum->users->isNotEmpty())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($bolum->users->take(3) as $lider)
                                                 <a href="{{ route('profile.show', $lider->id) }}" class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 pr-3 transition border border-transparent hover:border-gray-200" title="{{ $lider->name }}">
                                                     @if($lider->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $lider->profile_photo_path) }}" class="w-6 h-6 rounded-full object-cover">
                                                     @else
                                                        <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] text-indigo-700 font-bold">
                                                            {{ substr($lider->name, 0, 1) }}
                                                        </div>
                                                     @endif
                                                     <span class="text-xs font-medium text-gray-700 truncate max-w-[80px]">{{ $lider->name }}</span>
                                                 </a>
                                            @endforeach
                                            @if($bolum->users->count() > 3)
                                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-xs font-medium text-gray-500" title="Diğer liderler">
                                                    +{{ $bolum->users->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs italic flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            Atanmamış
                                        </span>
                                    @endif
                                </div>

                                 {{-- Makine Sayısı & Şikayet Sayısı Grid --}}
                                 <div class="grid grid-cols-2 gap-2">
                                     @if($bolum->has_machines)
                                         <div class="flex flex-col bg-gray-50 rounded-lg p-2 text-center">
                                             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Makine</span>
                                             <div class="flex items-center justify-center gap-1 font-bold text-gray-800 text-sm">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                  </svg>
                                                 {{ $bolum->machines_count }}
                                             </div>
                                         </div>
                                     @endif

                                     @if($bolum->sikayetler_count > 0 || in_array($bolum->kategori->ad ?? '', ['Üretim', 'Kalite', 'Sevkiyat']))
                                         <a href="{{ route('admin.bolumler.dashboard', $bolum->id) }}#sikayetler" class="flex flex-col bg-red-50 rounded-lg p-2 text-center {{ !$bolum->has_machines ? 'col-span-2' : '' }} hover:bg-red-100 transition duration-200 cursor-pointer group">
                                             <span class="text-[10px] font-bold text-red-400 uppercase tracking-wide group-hover:text-red-500">Şikayet</span>
                                             <div class="flex items-center justify-center gap-1 font-bold text-red-700 text-sm group-hover:text-red-800">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                  </svg>
                                                 {{ $bolum->sikayetler_count }}
                                             </div>
                                         </a>
                                     @endif
                                 </div>
                             </div>
                        </div>

                        {{-- Kart Footer --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                            <a href="{{ route('admin.bolumler.dashboard', $bolum) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1 group-hover/link">
                                Yönetim Paneli
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover/link:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.bolumler.edit', $bolum) }}" class="text-gray-400 hover:text-indigo-600 transition p-2 hover:bg-white rounded-lg shadow-sm hover:shadow" title="Düzenle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.bolumler.destroy', $bolum) }}" method="POST" onsubmit="return confirm('Bu bölümü silmek istediğinizden emin misiniz?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-white rounded-lg shadow-sm hover:shadow" title="Sil">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
                        <div class="bg-white rounded-full p-8 shadow-sm mb-6 border border-gray-100">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Henüz Bölüm Bulunamadı</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-8">Arama kriterlerinize uygun bir bölüm yok veya henüz sisteme bölüm eklenmemiş.</p>
                        <a href="{{ route('admin.bolumler.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold border-b-2 border-indigo-100 hover:border-indigo-500 transition-colors pb-1">Filtreleri Temizle</a>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
             <div class="mt-8">
                {{ $bolumler->links() }}
            </div>

        </div>
    </div>
</x-app-layout>