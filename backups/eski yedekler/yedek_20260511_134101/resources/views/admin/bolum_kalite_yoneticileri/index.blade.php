@push('pageTitle')
    Bölüm Kalite Yöneticisi Atamaları | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bölüm Kalite Yöneticisi Atamaları
            </h2>
            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                Toplam {{ $yoneticiler->count() }} Yönetici
            </span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Başarı Mesajı --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-green-800 font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
                </div>
            @endif

            {{-- Ana Düzen (Alpine.js ile Durum Yönetimi) --}}
            {{-- İlk açılışta listenin başındaki kullanıcıyı seçili getiriyoruz --}}
            <div x-data="{ 
                    selectedUserId: {{ $yoneticiler->first()->id ?? 'null' }}, 
                    search: '' 
                 }" 
                 class="flex flex-col md:flex-row gap-6 h-[calc(100vh-200px)] min-h-[600px]">

                {{-- SOL PANEL: KULLANICI LİSTESİ --}}
                <div class="w-full md:w-1/3 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden">
                    {{-- Arama Kutusu --}}
                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                        <div class="relative">
                            <input x-model="search" 
                                   type="text" 
                                   placeholder="Yönetici ara..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition duration-150">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Liste --}}
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        @if ($yoneticiler->isEmpty())
                            <div class="p-6 text-center text-gray-500">
                                <p>Henüz atanmış bir yönetici yok.</p>
                                <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:underline text-sm mt-2 block">Kullanıcı Ekle</a>
                            </div>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach ($yoneticiler as $yonetici)
                                    <li x-show="search === '' || '{{ strtolower($yonetici->name) }}'.includes(search.toLowerCase())"
                                        @click="selectedUserId = {{ $yonetici->id }}"
                                        :class="selectedUserId === {{ $yonetici->id }} ? 'bg-indigo-50 border-l-4 border-indigo-600' : 'hover:bg-gray-50 border-l-4 border-transparent'"
                                        class="cursor-pointer transition-all duration-150 ease-in-out group">
                                        <div class="p-4 flex items-center">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm group-hover:bg-indigo-200 transition">
                                                    {{ strtoupper(substr($yonetici->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $yonetici->name)[1] ?? '', 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate" 
                                                   :class="selectedUserId === {{ $yonetici->id }} ? 'text-indigo-700' : ''">
                                                    {{ $yonetici->name }}
                                                </p>
                                                <p class="text-xs text-gray-500 truncate">{{ $yonetici->email }}</p>
                                            </div>
                                            <div x-show="selectedUserId === {{ $yonetici->id }}">
                                                <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                {{-- SAĞ PANEL: YETKİ FORMU --}}
                <div class="w-full md:w-2/3 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden relative">
                    
                    {{-- Seçili Kullanıcı Yoksa --}}
                    <div x-show="selectedUserId === null" x-cloak class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                        <svg class="h-16 w-16 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-lg">Yetkilerini düzenlemek için soldan bir yönetici seçin.</p>
                    </div>

                    {{-- Her kullanıcı için Form (Sadece seçili olan görünür) --}}
                    @foreach ($yoneticiler as $yonetici)
                        <div x-show="selectedUserId === {{ $yonetici->id }}" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-x-4"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             class="flex flex-col h-full">
                            
                            {{-- Form Başlığı --}}
                            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $yonetici->name }}</h3>
                                    <p class="text-sm text-gray-500">Sorumlu olduğu kalite kategorilerini belirleyin</p>
                                </div>
                                <div class="text-right">
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Bölüm Kalite Yöneticisi
                                    </span>
                                </div>
                            </div>

                            {{-- Form İçeriği --}}
                            <form action="{{ route('admin.kalite-yoneticileri.update', $yonetici->id) }}" method="POST" class="flex flex-col flex-1 min-h-0">
                                @csrf
                                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach ($kategoriler as $kategori)
                                            <label class="relative flex items-start p-4 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors duration-200 group"
                                                   :class="{ 'ring-2 ring-indigo-500 border-transparent bg-indigo-50': $el.querySelector('input').checked }">
                                                <div class="flex items-center h-5">
                                                    <input type="checkbox" 
                                                           name="kategoriler[]" 
                                                           value="{{ $kategori->id }}" 
                                                           {{ $yonetici->yonettigiSikayetKategorileri->contains($kategori->id) ? 'checked' : '' }}
                                                           class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded"
                                                           onclick="this.closest('label').classList.toggle('ring-2'); this.closest('label').classList.toggle('ring-indigo-500'); this.closest('label').classList.toggle('bg-indigo-50');">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <span class="font-medium text-gray-900 group-hover:text-indigo-700">{{ $kategori->ad }}</span>
                                                    @if($kategori->varsayilanTakim)
                                                        <p class="text-xs text-gray-500 mt-1">Takım: {{ $kategori->varsayilanTakim->ad }}</p>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Form Alt Alanı (Action Bar) --}}
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                                    <p class="text-xs text-gray-500 italic">
                                        * Seçili kategorilerden gelen projeler, bu kullanıcının onayına düşecektir.
                                    </p>
                                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        Değişiklikleri Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Alpine.js yüklenene kadar gizle */
        [x-cloak] { display: none !important; }

        /* İnce Scrollbar Tasarımı */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>
</x-app-layout>