<div>

    {{-- ======================== FİLTRELEME KARTI ======================== --}}
    <div class="bg-gradient-to-br from-white via-blue-50/20 to-indigo-50/30 p-6 sm:p-8 rounded-3xl shadow-xl border border-blue-100/50 mb-10 backdrop-blur-sm no-print">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            </div>
            <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Filtreler</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="relative group">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Başlıkta ara..." class="w-full pl-12 pr-4 py-3 border-2 border-gray-200/60 rounded-xl shadow-sm focus:border-blue-400 focus:ring-4 focus:ring-blue-100/50 transition-all bg-white/80 backdrop-blur-sm group-hover:border-blue-300">
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            
            {{-- ======================== YENİ FİLTRE SEÇENEKLERİ ======================== --}}
            <select wire:model.live="durum" class="w-full px-4 py-3 border-2 border-gray-200/60 rounded-xl shadow-sm focus:border-blue-400 focus:ring-4 focus:ring-blue-100/50 transition-all bg-white/80 backdrop-blur-sm hover:border-blue-300">
                <option value="">🔄 Tüm Durumlar</option>
                <option value="Onay Bekliyor">⏳ Onay Bekliyor</option>
                <option value="Havuzda">💧 Havuzda</option>
                <option value="Talep Alan">🙋‍♂️ Talep Alan</option>
                <option value="Atandı">✅ Atandı</option>
                <option value="Revize Ediliyor">🔄 Revize Ediliyor</option>
                <option value="Yönetici Onayı Bekliyor">⌛ Yönetici Onayında</option>
                <option value="Tamamlandı">🏆 Tamamlandı</option>
                <option value="Reddedildi">❌ Reddedildi</option>
                <option value="Tamamlanması Reddedildi">🚫 Tamamlanması Reddedildi</option>
            </select>
            
            <select wire:model.live="kullaniciTipi" class="w-full px-4 py-3 border-2 border-gray-200/60 rounded-xl shadow-sm focus:border-blue-400 focus:ring-4 focus:ring-blue-100/50 transition-all bg-white/80 backdrop-blur-sm hover:border-blue-300">
                <option value="">👥 Tüm Kullanıcılar</option>
                <option value="kayitli">🔐 Kayıtlı Kullanıcılar</option>
                <option value="misafir">👤 Misafirler</option>
            </select>
            
            <button wire:click="resetFilters" class="bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all transform hover:scale-105 hover:shadow-lg active:scale-95 border border-gray-300/50 backdrop-blur-sm">
                <span class="flex items-center justify-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Temizle</span>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="relative group">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2"><svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Başlangıç Tarihi</label>
                <input wire:model.live="baslangicTarihi" type="date" class="w-full px-4 py-3 border-2 border-gray-200/60 rounded-xl shadow-sm focus:border-blue-400 focus:ring-4 focus:ring-blue-100/50 transition-all bg-white/80 backdrop-blur-sm group-hover:border-blue-300">
            </div>
            <div class="relative group">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2"><svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Bitiş Tarihi</label>
                <input wire:model.live="bitisTarihi" type="date" class="w-full px-4 py-3 border-2 border-gray-200/60 rounded-xl shadow-sm focus:border-blue-400 focus:ring-4 focus:ring-blue-100/50 transition-all bg-white/80 backdrop-blur-sm group-hover:border-blue-300">
            </div>
        </div>
    </div>

    {{-- ======================== SONUÇ TABLOSU ======================== --}}

    {{-- YENİ EKLENEN BAŞLIK --}}
<div class="bg-gradient-to-br from-white via-blue-50/20 to-indigo-50/30 p-6 sm:p-8 rounded-3xl shadow-xl border border-blue-100/50 mb-10 backdrop-blur-sm no-print">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            </div>
        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Proje Listesi</h3></div>
        <p class="text-gray-600">Filtrelenen kriterlere göre bulunan öneriler.</p>
    

    <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-100/80 overflow-hidden ring-1 ring-gray-900/5">
        <table class="block sm:table min-w-full">
            <thead class="hidden sm:table-header-group bg-gradient-to-r from-gray-50 to-blue-50/30">
                <tr class="text-left text-sm font-bold text-gray-700 tracking-wide">
                    <th class="p-6 w-16 text-center">#</th>
                    <th class="px-6 py-6">Başlık</th>
                    <th class="px-6 py-6">Öneren</th>
                    <th class="px-6 py-6 text-center">Durum</th>
                    <th class="px-6 py-6 text-center">Tamamlayan</th> {{-- YENİ SÜTUN BAŞLIĞI --}}
                    <th class="px-6 py-6">Tarih</th>
                </tr>
            </thead>
            <tbody class="block sm:table-row-group divide-y divide-gray-100">
                @forelse($iaas as $index => $iaa)
                    <tr x-data @click="$dispatch('open-modal', 'detay-modal-{{ $iaa->id }}')" class="block mb-6 border bg-gradient-to-r from-white to-blue-50/20 border-gray-200 rounded-2xl sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 cursor-pointer group hover:shadow-xl hover:scale-[1.02] transform-gpu hover:-translate-y-1">
                        
                        <td class="flex justify-between items-center p-4 sm:table-cell sm:p-6 align-middle text-center"><span class="font-bold text-sm text-gray-600 sm:hidden">No:</span><div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl flex items-center justify-center font-bold text-sm group-hover:scale-110 transition-transform">{{ $startNumber + $index + 1 }}</div></td>
                        <td class="flex justify-between items-start p-4 border-t sm:border-0 sm:table-cell sm:p-6 align-middle"><span class="font-bold text-sm text-gray-600 sm:hidden">Başlık:</span><div class="text-right sm:text-left max-w-md"><p class="text-gray-800 font-bold text-lg group-hover:text-indigo-700 transition-colors leading-tight">{{ $iaa->baslik }}</p></div></td>
                        <td class="flex justify-between items-center p-4 border-t sm:border-0 sm:table-cell sm:p-6 align-middle"><span class="font-bold text-sm text-gray-600 sm:hidden">Öneren:</span><div class="text-right sm:text-left">@if($iaa->gonderen)<div class="flex items-center gap-3"><div class="w-10 h-10 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">{{ substr($iaa->gonderen->name, 0, 2) }}</div><div><p class="font-bold text-gray-800">{{ $iaa->gonderen->name }}</p><p class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full inline-block">{{ $iaa->gonderen->bolum->ad ?? 'Bölüm Yok' }}</p></div></div>@else<div class="flex items-center gap-3"><div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div><div><p class="font-bold text-gray-800">{{ $iaa->guest_name }}</p><span class="text-xs text-white bg-gradient-to-r from-orange-500 to-red-500 px-3 py-1 rounded-full font-semibold">👤 Misafir</span></div></div>@endif</div></td>
                        {{-- DURUM HÜCRESİ (Yeni ve En Basit Hali) --}}
                        <td class="flex justify-between items-center p-4 border-t sm:border-0 sm:table-cell sm:p-6 align-middle">
                            <span class="font-bold text-sm text-gray-600 sm:hidden">Durum:</span>
                            <div class="w-full text-right sm:text-center">
                                {{-- Yeni fonksiyondan gelen sınıfları doğrudan kullanıyoruz --}}
                                <span class="px-4 py-2 inline-flex items-center gap-2 text-sm font-bold rounded-2xl 
                                            {{ $this->getDurumBadgeClasses($iaa->durum) }}">
                                    
                                    {{-- Bu kısım sadece görsel, isterseniz silebilirsiniz --}}
                                    @switch($iaa->durum)
                                        @case('Onay Bekliyor') <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div> @break
                                        @case('Atandı') <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div> @break
                                        @case('Reddedildi') <div class="w-2 h-2 bg-red-500 rounded-full"></div> @break
                                        @default <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                                    @endswitch
                                    
                                    {{ $iaa->durum }}
                                </span>
                            </div>
                        </td>

                        {{-- YENİ EKLENEN "TAMAMLAYAN" SÜTUNU ve TOOLTIP --}}
                        <td class="flex justify-between items-center p-4 border-t sm:border-0 sm:table-cell sm:p-6 align-middle">
                            <span class="font-bold text-sm text-gray-600 sm:hidden">Tamamlayan:</span>
                            <div class="w-full text-center">
                                @if($iaa->durum == 'Tamamlandı' && $iaa->atananTakim)
                                    <div x-data="{ tooltip: false }" class="relative inline-block">
                                        <span @mouseenter="tooltip = true" @mouseleave="tooltip = false" class="font-semibold text-blue-700 cursor-pointer">
                                            {{ $iaa->atananTakim->ad }}
                                        </span>
                                        <div x-show="tooltip" 
                                            x-transition
                                            class="absolute z-10 w-48 p-2 text-sm text-left text-white bg-gray-800 rounded-lg shadow-lg"
                                            x-cloak style="bottom: 125%; left: 50%; transform: translateX(-50%);">
                                            <h4 class="font-bold mb-1 border-b border-gray-600 pb-1">Takım Üyeleri</h4>
                                            <ul class="list-disc list-inside">
                                                @forelse($iaa->atananTakim->uyeler as $uye)
                                                    <li>{{ $uye->name }}</li>
                                                @empty
                                                    <li>Üye bulunamadı.</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                @else
                                    <span>-</span>
                                @endif
                            </div>
                        </td>

                        {{-- TARİH SÜTUNU (BU KOD ZATEN VAR) --}}
                        <td class="flex justify-between items-center p-4 border-t sm:border-0 sm:table-cell sm:p-6 align-middle"><span class="font-bold text-sm text-gray-600 sm:hidden">Tarih:</span><div class="text-right sm:text-left"><div class="bg-gradient-to-r from-blue-100 to-indigo-100 px-3 py-2 rounded-xl border border-blue-200"><span class="text-sm font-bold text-blue-800">{{ $iaa->created_at->format('d.m.Y') }}</span></div></div></td>
                    </tr>
                @empty
                    <tr class="block sm:table-row"><td colspan="5" class="p-16 text-center"><div class="flex flex-col items-center gap-4"><div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center"><svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><div class="text-center"><p class="text-xl font-bold text-gray-600 mb-2">Sonuç Bulunamadı</p><p class="text-gray-500">Seçtiğiniz kriterlere uygun bir öneri bulunamadı.</p></div></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    <div class="mt-8 flex justify-center no-print">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/80 p-2">
            {{ $iaas->links() }}
        </div>
    </div>

    @foreach ($iaas as $iaa)
        <x-modal name="detay-modal-{{ $iaa->id }}" max-width="2xl">
            <div class="p-8 bg-gradient-to-br from-white to-blue-50/20">
                @include('admin.raporlar.partials.iaa-detay-content', ['iaa' => $iaa])
            </div>
        </x-modal>
    @endforeach
</div>




<script>
    document.addEventListener('livewire:initialized', () => {
        window.dispatchEvent(new CustomEvent('updateChart', {
            detail: @json($initialChartData)
        }));
    });
</script>