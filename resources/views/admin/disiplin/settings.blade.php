<x-app-layout>
    @push('pageTitle', 'Disiplin Ayarları | ')
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
                            <div class="flex gap-1.5">
                                <button @click="isEdit=true; id={{$c->id}}; ad='{{ addslashes($c->ad) }}'; action='{{ route('admin.disiplin.settings.category.update', $c->id) }}'" 
                                        class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Düzenle">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.disiplin.settings.category.delete', $c->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Sil" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
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
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-indigo-600 text-xs mr-1 bg-indigo-50 px-1.5 py-0.5 rounded">{{ $i->puan }}p</span>
                                <button @click="isEdit=true; id={{$i->id}}; tanim='{{ addslashes($i->tanim) }}'; puan='{{ $i->puan }}'; action='{{ route('admin.disiplin.settings.impact.update', $i->id) }}'" 
                                        class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Düzenle">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.disiplin.settings.impact.delete', $i->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Sil" onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
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
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-indigo-600 text-xs mr-1 bg-indigo-50 px-1.5 py-0.5 rounded">{{ $s->puan }}p</span>
                                <button @click="isEdit=true; id={{$s->id}}; tanim='{{ addslashes($s->tanim) }}'; puan='{{ $s->puan }}'; action='{{ route('admin.disiplin.settings.scope.update', $s->id) }}'" 
                                        class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Düzenle">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.disiplin.settings.scope.delete', $s->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Sil" onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- TAB 2: SUÇ LİSTESİ (DÜZENLEME MEVCUT) --}}
            {{-- TAB 2: SUÇ LİSTESİ --}}
            <div x-show="activeTab === 'suclar'" class="space-y-6" style="display: none;">
                {{-- Üst Form: SADECE YENİ EKLEME İÇİN --}}
                <div class="p-4 bg-white border border-indigo-100 rounded-xl shadow-sm">
                    <h4 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="bg-indigo-600 w-2 h-5 rounded-full"></span>
                        Yeni Suç Tanımı Ekle
                    </h4>
                    
                    <form action="{{ route('admin.disiplin.settings.behavior.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        @csrf
                        <div class="md:col-span-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kategori</label>
                            <select name="category_id" class="w-full mt-1 text-sm rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Seçiniz...</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->ad }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-6">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Suç Tanımı</label>
                            <input type="text" name="tanim" class="w-full mt-1 text-sm rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Örn: İzinsiz işi terk etmek">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Dayanak (Madde)</label>
                            <input type="text" name="yasal_dayanak" class="w-full mt-1 text-sm rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Md. 25/II">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                                EKLE
                            </button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto bg-white rounded-xl shadow-sm border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-10">#</th>
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-40">Kategori</th>
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Suç Tanımı</th>
                                <th class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-32">Dayanak</th>
                                <th class="px-4 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50" x-data="{ editingId: null }">
                            @foreach($behaviors as $categoryName => $group)
                                <tr class="bg-indigo-50/30">
                                    <td colspan="5" class="px-4 py-2 text-[10px] font-black text-indigo-800 uppercase tracking-widest border-y border-indigo-100/50">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            {{ $categoryName }}
                                            <span class="ml-auto bg-white/80 text-indigo-600 px-2 py-0.5 rounded-full text-[9px] shadow-sm border border-indigo-100">{{ $group->count() }} Kayıt</span>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($group as $b)
                                <tr class="hover:bg-gray-50/50 transition-colors group" :class="editingId === {{ $b->id }} ? 'bg-amber-50/50' : ''">
                                    {{-- DÜZENLEME MODU --}}
                                    <template x-if="editingId === {{ $b->id }}">
                                        <td colspan="5" class="p-0">
                                            <form action="{{ route('admin.disiplin.settings.behavior.update', $b->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-3 p-3 bg-amber-50/50 border-y border-amber-100 items-center">
                                                @csrf @method('PUT')
                                                <div class="md:col-span-1 text-center font-mono text-[10px] text-amber-600 font-bold">DÜZENLE</div>
                                                <div class="md:col-span-2">
                                                    <select name="category_id" class="w-full text-xs rounded-lg border-amber-200 focus:ring-amber-500 bg-white" required>
                                                        @foreach($categories as $c)
                                                            <option value="{{ $c->id }}" {{ $b->category_id == $c->id ? 'selected' : '' }}>{{ $c->ad }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="md:col-span-6">
                                                    <input type="text" name="tanim" value="{{ $b->tanim }}" class="w-full text-xs rounded-lg border-amber-200 focus:ring-amber-500 bg-white" required>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <input type="text" name="yasal_dayanak" value="{{ $b->yasal_dayanak }}" class="w-full text-xs rounded-lg border-amber-200 focus:ring-amber-500 bg-white">
                                                </div>
                                                <div class="md:col-span-1 flex gap-1">
                                                    <button type="submit" class="flex-1 bg-green-600 text-white p-1.5 rounded-lg hover:bg-green-700 shadow-sm" title="Kaydet">
                                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                    <button type="button" @click="editingId = null" class="flex-1 bg-gray-400 text-white p-1.5 rounded-lg hover:bg-gray-500 shadow-sm" title="Vazgeç">
                                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </template>

                                    {{-- OKUMA MODU --}}
                                    <template x-if="editingId !== {{ $b->id }}">
                                        <td class="px-4 py-3 text-[10px] text-gray-400 font-mono">{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                                    </template>
                                    <template x-if="editingId !== {{ $b->id }}">
                                        <td class="px-4 py-3 text-xs font-semibold text-gray-500">
                                            {{ $categoryName }}
                                        </td>
                                    </template>
                                    <template x-if="editingId !== {{ $b->id }}">
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-800 font-medium leading-relaxed">{{ $b->tanim }}</div>
                                            @if($b->created_at->diffInDays() < 7)
                                                <span class="text-[9px] bg-green-100 text-green-600 px-1.5 py-0.5 rounded font-black uppercase mt-1 inline-block">Yeni</span>
                                            @endif
                                        </td>
                                    </template>
                                    <template x-if="editingId !== {{ $b->id }}">
                                        <td class="px-4 py-3 text-xs text-gray-500 italic">{{ $b->yasal_dayanak ?: '-' }}</td>
                                    </template>
                                    <template x-if="editingId !== {{ $b->id }}">
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                                <button @click="editingId = {{ $b->id }}" 
                                                        class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100" title="Düzenle">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                
                                                <form action="{{ route('admin.disiplin.settings.behavior.delete', $b->id) }}" method="POST" onsubmit="return confirm('Bu suç tanımını silmek istediğinize emin misiniz?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-rose-100" title="Sil">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </template>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 3: KATSAYI VE SKALA --}}
            <div x-show="activeTab === 'katsayilar'" class="space-y-6" style="display: none;">
                
                {{-- Hesaplama Formülü Bilgi Kutusu --}}
                <div class="bg-gradient-to-br from-indigo-700 via-indigo-800 to-blue-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-indigo-500/20 rounded-full blur-2xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-white/20 p-2 rounded-lg backdrop-blur-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200">Disiplin Sistemi Hesaplama Mantığı</h4>
                        </div>
                        
                        <div class="flex flex-col md:flex-row md:items-center gap-6">
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-white/90">Puan</span>
                                <span class="text-indigo-400 text-xl">=</span>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-3 font-bold">
                                <div class="bg-white/10 px-4 py-2 rounded-xl border border-white/10 backdrop-blur-sm text-center min-w-[80px]">
                                    <span class="text-xs text-indigo-300 block font-black uppercase">Etki</span>
                                    <span class="text-lg">Puanı</span>
                                </div>
                                <span class="text-2xl text-indigo-400">×</span>
                                <div class="bg-white/10 px-4 py-2 rounded-xl border border-white/10 backdrop-blur-sm text-center min-w-[80px]">
                                    <span class="text-xs text-indigo-300 block font-black uppercase">Kapsam</span>
                                    <span class="text-lg">Puanı</span>
                                </div>
                                <span class="text-2xl text-indigo-400">×</span>
                                <div class="bg-white/10 px-4 py-2 rounded-xl border border-white/10 backdrop-blur-sm text-center min-w-[100px]">
                                    <span class="text-xs text-indigo-300 block font-black uppercase">Tekrar</span>
                                    <span class="text-lg">Katsayı</span>
                                </div>
                                <span class="text-2xl text-indigo-400">=</span>
                                <div class="bg-indigo-500 px-4 py-2 rounded-xl border border-indigo-400 shadow-lg shadow-indigo-900/20 text-center">
                                    <span class="text-xs text-indigo-100 block font-black uppercase">Sonuç</span>
                                    <span class="text-lg font-black italic text-white">Toplam Ceza Puanı</span>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-indigo-200/70 text-[10px] mt-4 italic border-t border-white/10 pt-4 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            Not: Toplam puan her bir olay kaydı için bağımsız hesaplanır ve ilgili ceza skalasındaki yaptırımı belirler.
                        </p>
                    </div>
                </div>

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
                    <div class="bg-yellow-50/50 p-4 rounded border border-yellow-100" 
                         x-data="{ editMode: false, formAction: '{{ route('admin.disiplin.settings.scale.store') }}', method: 'POST', min: '', max: '', ad: '', karar_metni: '' }">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-gray-700" x-text="editMode ? 'Skala Düzenle' : 'Ceza Puan Skalası'"></h4>
                            <button x-show="editMode" @click="editMode=false; method='POST'; formAction='{{ route('admin.disiplin.settings.scale.store') }}'; min=''; max=''; ad=''; karar_metni=''" class="text-[10px] text-red-500 underline">Vazgeç</button>
                        </div>

                        <ul class="text-xs bg-white border rounded divide-y mb-4 shadow-sm">
                            @foreach($scales as $s)
                                <li class="flex items-center justify-between p-2 hover:bg-gray-50 group">
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono font-bold text-gray-600 w-16">{{ $s->min_puan }} - {{ $s->max_puan }}</span>
                                        <span class="font-bold text-gray-800">{{ $s->ceza_adi }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <button @click="editMode=true; method='PUT'; formAction='{{ route('admin.disiplin.settings.scale.update', $s->id) }}'; min='{{ $s->min_puan }}'; max='{{ $s->max_puan }}'; ad='{{ addslashes($s->ceza_adi) }}'; karar_metni='{{ preg_replace('/\r|\n/', '\n', addslashes($s->karar_metni)) }}'" 
                                                class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Düzenle">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.disiplin.settings.scale.delete', $s->id) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Sil">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <form :action="formAction" method="POST" class="flex flex-col gap-3">
                            @csrf
                            <input type="hidden" name="_method" :value="method">
                            <div class="flex gap-2">
                                <input type="number" name="min_puan" x-model="min" placeholder="Min" class="w-20 text-xs rounded border-gray-300" required>
                                <input type="number" name="max_puan" x-model="max" placeholder="Max" class="w-20 text-xs rounded border-gray-300" required>
                                <input type="text" name="ceza_adi" x-model="ad" placeholder="Ceza Adı (Örn: Uyarı)" class="flex-1 text-xs rounded border-gray-300" required>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Karar Metni Şablonu (PDF/Yazdır)</label>
                                <textarea name="karar_metni" x-model="karar_metni" rows="4" class="w-full text-xs rounded border-gray-300" placeholder="Bu ceza verildiğinde yazdırılacak olan sabit metin şablonu..."></textarea>
                                <div class="mt-1 p-2 bg-indigo-50 border border-indigo-100 rounded text-[10px] text-indigo-700 leading-tight">
                                    <span class="font-bold">Kullanılabilir Değişkenler:</span><br>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{ad_soyad}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{bolum_adi}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{unvan}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{tutanak_tarihi}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{olay_tarihi}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{olay_aciklamasi}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{ceza_adi}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{tc_kimlik_no}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{ihlal_kategorisi}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{etki_siddet}</code>
                                    <code class="text-indigo-900 bg-white px-1 py-0.5 rounded mr-1 mb-1 inline-block">{kapsam}</code>
                                    <div class="mt-2 text-indigo-800">
                                        <span class="font-bold">Not:</span> Metinde kalın (bold) yapmak istediğiniz yerleri <code class="bg-white px-1 py-0.5 rounded font-bold">**kalın kelime**</code> şeklinde iki yıldız arasına alabilirsiniz.
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" :class="editMode ? 'bg-blue-600 hover:bg-blue-700' : 'bg-yellow-500 hover:bg-yellow-600'" class="text-white px-6 py-2 rounded text-xs font-bold transition-colors shadow-sm" x-text="editMode ? 'GÜNCELLE' : 'EKLE'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>