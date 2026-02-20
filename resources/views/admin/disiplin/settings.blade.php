<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Disiplin Sistemi Ayarları') }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- 
            activeTab: Tarayıcı hafızasından son sekmeyi okur, yoksa 'parametreler' açar.
            Değişince localStorage'a yazar.
        --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" 
             x-data="{ 
                activeTab: localStorage.getItem('disiplinActiveTab') || 'parametreler',
                setTab(tab) { this.activeTab = tab; localStorage.setItem('disiplinActiveTab', tab); }
             }">
            
            {{-- Başarı Mesajı --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Sekmeler --}}
            <div class="bg-white shadow rounded-lg p-2 flex space-x-2 overflow-x-auto">
                <button @click="setTab('parametreler')" 
                        :class="activeTab === 'parametreler' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-md text-sm font-medium transition whitespace-nowrap">
                    1. Parametreler (Kategori/Etki/Kapsam)
                </button>
                <button @click="setTab('suclar')" 
                        :class="activeTab === 'suclar' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-md text-sm font-medium transition whitespace-nowrap">
                    2. Suç/Ceza Listesi
                </button>
                <button @click="setTab('katsayilar')" 
                        :class="activeTab === 'katsayilar' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-md text-sm font-medium transition whitespace-nowrap">
                    3. Hesaplama (Katsayı & Skala)
                </button>
            </div>

            {{-- TAB 1: PARAMETRELER --}}
            <div x-show="activeTab === 'parametreler'" class="grid grid-cols-1 md:grid-cols-3 gap-6" style="display: none;">
                
                {{-- A. KATEGORİLER (DÜZENLEME EKLENDİ) --}}
                <div class="bg-white shadow sm:rounded-lg p-4 h-fit" 
                     x-data="{ id: null, ad: '', isEdit: false, action: '{{ route('admin.disiplin.settings.category.store') }}' }">
                    
                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2 flex justify-between">
                        <span>Kategoriler</span>
                        <button x-show="isEdit" @click="isEdit=false; id=null; ad=''; action='{{ route('admin.disiplin.settings.category.store') }}'" class="text-xs text-red-500 underline">İptal</button>
                    </h4>

                    <form :action="action" method="POST" class="flex gap-2 mb-4">
                        @csrf
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        <input type="text" name="ad" x-model="ad" placeholder="Kategori Adı" class="w-full text-xs rounded border-gray-300" required>
                        <button type="submit" class="bg-green-600 text-white px-2 rounded text-xs font-bold" x-text="isEdit ? 'GÜNCELLE' : 'EKLE'">+</button>
                    </form>

                    <ul class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($categories as $c)
                        <li class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded border">
                            <span class="truncate">{{ $c->ad }}</span>
                            <div class="flex gap-2">
                                <button @click="isEdit=true; id={{$c->id}}; ad='{{ addslashes($c->ad) }}'; action='{{ route('admin.disiplin.settings.category.update', $c->id) }}'" class="text-blue-500 text-xs font-bold">DÜZENLE</button>
                                <form action="{{ route('admin.disiplin.settings.category.delete', $c->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 font-bold text-xs">SİL</button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- B. ETKİ (DÜZENLEME EKLENDİ) --}}
                <div class="bg-white shadow sm:rounded-lg p-4 h-fit"
                     x-data="{ id: null, tanim: '', puan: '', isEdit: false, action: '{{ route('admin.disiplin.settings.impact.store') }}' }">
                    
                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2 flex justify-between">
                        <span>Etki / Şiddet</span>
                        <button x-show="isEdit" @click="isEdit=false; id=null; tanim=''; puan=''; action='{{ route('admin.disiplin.settings.impact.store') }}'" class="text-xs text-red-500 underline">İptal</button>
                    </h4>

                    <form :action="action" method="POST" class="flex gap-2 mb-4">
                        @csrf
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        <input type="text" name="tanim" x-model="tanim" placeholder="Tanım" class="w-full text-xs rounded border-gray-300" required>
                        <input type="number" name="puan" x-model="puan" placeholder="Pn" class="w-12 text-xs rounded border-gray-300" required>
                        <button type="submit" class="bg-green-600 text-white px-2 rounded text-xs font-bold" x-text="isEdit ? '✓' : '+'">+</button>
                    </form>

                    <ul class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($impacts as $i)
                        <li class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded border">
                            <span>{{ $i->tanim }}</span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-indigo-600 text-xs">{{ $i->puan }}p</span>
                                <button @click="isEdit=true; id={{$i->id}}; tanim='{{ addslashes($i->tanim) }}'; puan='{{ $i->puan }}'; action='{{ route('admin.disiplin.settings.impact.update', $i->id) }}'" class="text-blue-500 text-xs font-bold">DÜZENLE</button>
                                <form action="{{ route('admin.disiplin.settings.impact.delete', $i->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 font-bold text-xs">SİL</button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- C. KAPSAM (DÜZENLEME EKLENDİ) --}}
                <div class="bg-white shadow sm:rounded-lg p-4 h-fit"
                     x-data="{ id: null, tanim: '', puan: '', isEdit: false, action: '{{ route('admin.disiplin.settings.scope.store') }}' }">
                    
                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2 flex justify-between">
                        <span>Kapsam</span>
                        <button x-show="isEdit" @click="isEdit=false; id=null; tanim=''; puan=''; action='{{ route('admin.disiplin.settings.scope.store') }}'" class="text-xs text-red-500 underline">İptal</button>
                    </h4>

                    <form :action="action" method="POST" class="flex gap-2 mb-4">
                        @csrf
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        <input type="text" name="tanim" x-model="tanim" placeholder="Tanım" class="w-full text-xs rounded border-gray-300" required>
                        <input type="number" name="puan" x-model="puan" placeholder="Pn" class="w-12 text-xs rounded border-gray-300" required>
                        <button type="submit" class="bg-green-600 text-white px-2 rounded text-xs font-bold" x-text="isEdit ? '✓' : '+'">+</button>
                    </form>

                    <ul class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($scopes as $s)
                        <li class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded border">
                            <span>{{ $s->tanim }}</span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-indigo-600 text-xs">{{ $s->puan }}p</span>
                                <button @click="isEdit=true; id={{$s->id}}; tanim='{{ addslashes($s->tanim) }}'; puan='{{ $s->puan }}'; action='{{ route('admin.disiplin.settings.scope.update', $s->id) }}'" class="text-blue-500 text-xs font-bold">DÜZENLE</button>
                                <form action="{{ route('admin.disiplin.settings.scope.delete', $s->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 font-bold text-xs">SİL</button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- TAB 2: SUÇ LİSTESİ (DÜZENLEME MEVCUT) --}}
            <div x-show="activeTab === 'suclar'" class="bg-white shadow sm:rounded-lg p-6" style="display: none;"
                 x-data="{ editMode: false, formAction: '{{ route('admin.disiplin.settings.behavior.store') }}', method: 'POST', kategori: '', tanim: '', yasal: '' }">
                
                <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-bold text-gray-700" x-text="editMode ? 'Suç Tanımını Düzenle' : 'Yeni Suç Tanımı Ekle'"></h4>
                        <button x-show="editMode" @click="editMode=false; method='POST'; formAction='{{ route('admin.disiplin.settings.behavior.store') }}'; kategori=''; tanim=''; yasal=''" class="text-xs text-red-500 underline">İptal ve Yeni Ekle</button>
                    </div>
                    
                    <form :action="formAction" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        @csrf
                        <input type="hidden" name="_method" :value="method">
                        
                        <div class="md:col-span-3">
                            <label class="text-xs font-bold text-gray-500">Kategori</label>
                            <select name="category_id" x-model="kategori" class="w-full mt-1 text-sm rounded border-gray-300" required>
                                <option value="">Seçiniz...</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->ad }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-6">
                            <label class="text-xs font-bold text-gray-500">Suç Tanımı</label>
                            <input type="text" name="tanim" x-model="tanim" class="w-full mt-1 text-sm rounded border-gray-300" required placeholder="Örn: İzinsiz işi terk etmek">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500">Dayanak (Madde)</label>
                            <input type="text" name="yasal_dayanak" x-model="yasal" class="w-full mt-1 text-sm rounded border-gray-300" placeholder="Md. 25/II">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded text-sm font-bold hover:bg-indigo-700" x-text="editMode ? 'GÜNCELLE' : 'EKLE'"></button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 w-10">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 w-40">Kategori</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Suç Tanımı</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 w-32">Dayanak</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 w-24">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($behaviors as $b)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-xs text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 text-xs font-bold text-indigo-600">{{ $b->category->ad ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800">{{ $b->tanim }}</td>
                                <td class="px-4 py-2 text-xs text-gray-500 italic">{{ $b->yasal_dayanak }}</td>
                                <td class="px-4 py-2 text-right flex justify-end gap-2">
                                    <button @click="editMode=true; method='PUT'; formAction='{{ route('admin.disiplin.settings.behavior.update', $b->id) }}'; kategori='{{ $b->category_id }}'; tanim='{{ addslashes($b->tanim) }}'; yasal='{{ $b->yasal_dayanak }}'" 
                                            class="text-blue-500 hover:text-blue-700 text-xs font-bold">DÜZENLE</button>
                                    
                                    <form action="{{ route('admin.disiplin.settings.behavior.delete', $b->id) }}" method="POST" onsubmit="return confirm('Silinecek?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold">SİL</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 3: KATSAYI VE SKALA --}}
            <div x-show="activeTab === 'katsayilar'" class="bg-white shadow sm:rounded-lg p-6" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                     {{-- Katsayılar --}}
                    <div class="bg-blue-50/50 p-4 rounded border border-blue-100">
                        <h4 class="font-bold text-gray-700 mb-4">Tekrar Katsayıları</h4>
                        <div class="space-y-2 mb-4">
                            @foreach($multipliers as $m)
                                <form action="{{ route('admin.disiplin.settings.multiplier.store') }}" method="POST" class="flex items-center justify-between bg-white p-2 rounded border shadow-sm">
                                    @csrf
                                    <input type="hidden" name="tekrar_sayisi" value="{{ $m->tekrar_sayisi }}">
                                    <span class="text-xs font-bold text-gray-600">{{ $m->tekrar_sayisi }}. Tekrar</span>
                                    <div class="flex gap-1 items-center">
                                        <span class="text-gray-400 text-xs">x</span>
                                        <input type="number" step="0.01" name="katsayi" value="{{ $m->katsayi }}" class="w-16 text-sm border-gray-300 rounded text-center font-bold">
                                        <button class="text-green-600 hover:text-green-800 text-xs px-2">💾</button>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    {{-- Skala --}}
                    <div class="bg-yellow-50/50 p-4 rounded border border-yellow-100">
                        <h4 class="font-bold text-gray-700 mb-4">Ceza Puan Skalası</h4>
                        <ul class="text-xs bg-white border rounded divide-y mb-4 shadow-sm">
                            @foreach($scales as $s)
                                <li class="flex justify-between p-2 hover:bg-gray-50">
                                    <span class="font-mono font-bold text-gray-600">{{ $s->min_puan }} - {{ $s->max_puan }}</span>
                                    <span class="font-bold text-gray-800">{{ $s->ceza_adi }}</span>
                                    <form action="{{ route('admin.disiplin.settings.scale.delete', $s->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 font-bold px-2">x</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                        <form action="{{ route('admin.disiplin.settings.scale.store') }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="number" name="min_puan" placeholder="Min" class="w-16 text-xs rounded border-gray-300">
                            <input type="number" name="max_puan" placeholder="Max" class="w-16 text-xs rounded border-gray-300">
                            <input type="text" name="ceza_adi" placeholder="Ceza Adı" class="flex-1 text-xs rounded border-gray-300">
                            <button type="submit" class="bg-yellow-500 text-white px-3 rounded text-xs font-bold">EKLE</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>