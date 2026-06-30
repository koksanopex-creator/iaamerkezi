import os, re

filepath = r'c:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views\livewire\admin\musteri-sikayet-analiz-raporu.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Alt Kategori filter
alt_kategori_html = '''
            {{-- Alt Kategori --}}
            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Alt Kategori</label>
                <select wire:model.live="altKategoriId" class="w-full text-xs border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 py-2">
                    <option value="">Tüm Alt Kategoriler</option>
                    @foreach($filterData['altKategoriler'] as $ak)
                        <option value="{{ $ak->id }}">{{ $ak->ad }}</option>
                    @endforeach
                </select>
            </div>'''

content = content.replace('            {{-- Tarih Alanı Seçimi --}}', alt_kategori_html + '\n\n            {{-- Tarih Alanı Seçimi --}}')

# 2. Aktif Filtreler
aktif_filtreler_html = '''
    {{-- ============================= AKTİF FİLTRELER ============================= --}}
    @if($bolumId || $startDate || $endDate || $durum || $oncelik || $customerId || $konumTipi || $altKategoriId)
        <div class="flex flex-wrap items-center gap-2 bg-indigo-50/50 p-3 rounded-2xl border border-indigo-100/50">
            <span class="text-[11px] font-black text-indigo-800 uppercase tracking-wider mr-2 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 00-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Aktif Filtreler:
            </span>
            @if($bolumId) <span class="px-2.5 py-1 bg-white rounded-lg text-[10px] font-bold text-indigo-600 border border-indigo-100 shadow-sm">Bölüm Seçili</span> @endif
            @if($altKategoriId) <span class="px-2.5 py-1 bg-white rounded-lg text-[10px] font-bold text-indigo-600 border border-indigo-100 shadow-sm">Alt Kategori Seçili</span> @endif
            @if($durum) <span class="px-2.5 py-1 bg-white rounded-lg text-[10px] font-bold text-indigo-600 border border-indigo-100 shadow-sm">Durum: {{ $durum }}</span> @endif
            @if($oncelik) <span class="px-2.5 py-1 bg-white rounded-lg text-[10px] font-bold text-indigo-600 border border-indigo-100 shadow-sm">Öncelik: {{ $oncelik }}</span> @endif
            @if($customerId) <span class="px-2.5 py-1 bg-white rounded-lg text-[10px] font-bold text-indigo-600 border border-indigo-100 shadow-sm">Müşteri Seçili</span> @endif
            @if($konumTipi) <span class="px-2.5 py-1 bg-white rounded-lg text-[10px] font-bold text-indigo-600 border border-indigo-100 shadow-sm">Konum: {{ $konumTipi }}</span> @endif
            @if($startDate || $endDate) <span class="px-2.5 py-1 bg-white rounded-lg text-[10px] font-bold text-indigo-600 border border-indigo-100 shadow-sm">Tarih Filtresi Aktif</span> @endif
        </div>
    @endif
'''

content = content.replace('    {{-- ============================= KPI KARTLARI ============================= --}}', aktif_filtreler_html + '\n    {{-- ============================= KPI KARTLARI ============================= --}}')

# 3. Add wire:ignore to charts
content = re.sub(r'<div id="chart-([a-z0-9\-]+)" class="min-h-\[280px\]"></div>', r'<div id="chart-\1" class="min-h-[280px]" wire:ignore></div>', content)

# 4. Extract Tablo block
tablo_start = content.find('    {{-- ============================= DETAY TABLOSU ============================= --}}')
tablo_end = content.find('    {{-- ============================= GRAFİKLER (1. SATIR) ============================= --}}', tablo_start)
if tablo_end == -1:
    tablo_end = content.find('</div>\n\n@script')

tablo_block = content[tablo_start:tablo_end]
content = content[:tablo_start] + content[tablo_end:]

# Insert Tablo block before Grafikler
content = content.replace('    {{-- ============================= GRAFİKLER (1. SATIR) ============================= --}}', tablo_block + '\n    {{-- ============================= GRAFİKLER (1. SATIR) ============================= --}}')

# 5. Modify Table Headers & Cells
old_tarih_th = '<th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Tarih</th>'
new_tarih_th = '<th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">{{ $tarihAlani == \'musteri_sikayet_tarihi\' ? \'Bildirim T.\' : ($tarihAlani == \'musteri_cozum_son_tarihi\' ? \'Son Çözüm T.\' : \'Giriş T.\') }}</th>'
content = content.replace(old_tarih_th, new_tarih_th)

old_tarih_td = '<td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $sikayet->created_at->format(\'d.m.Y\') }}</td>'
new_tarih_td = '''<td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                @php
                                    $tarihVal = $tarihAlani == 'musteri_sikayet_tarihi' ? $sikayet->musteri_sikayet_tarihi :
                                                ($tarihAlani == 'musteri_cozum_son_tarihi' ? $sikayet->musteri_cozum_son_tarihi : $sikayet->created_at);
                                @endphp
                                {{ $tarihVal ? $tarihVal->format('d.m.Y') : '-' }}
                            </td>'''
content = content.replace(old_tarih_td, new_tarih_td)

# Add Islemler
content = content.replace('<th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Öncelik</th>', '<th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Öncelik</th>\n                        <th class="px-4 py-3 text-right font-bold text-gray-500 uppercase tracking-wider">İşlemler</th>')

old_oncelik_td = '''<td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sikayet->oncelik_badge_class }}">{{ $sikayet->musteri_oncelik ?? '-' }}</span>
                            </td>'''
new_islemler_td = '''<td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sikayet->oncelik_badge_class }}">{{ $sikayet->musteri_oncelik ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" target="_blank" class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 rounded-lg text-[10px] font-bold transition-colors">Detay</a>
                                @if($sikayet->iaa_id)
                                    <a href="{{ route('proje.workspace.show', $sikayet->iaa_id) }}" target="_blank" class="inline-flex items-center px-2.5 py-1 bg-purple-50 text-purple-600 hover:bg-purple-100 hover:text-purple-700 rounded-lg text-[10px] font-bold transition-colors">Proje</a>
                                @endif
                            </td>'''
content = content.replace(old_oncelik_td, new_islemler_td)

# Customer profile image
old_customer_td = '''<td class="px-4 py-3">
                                @if($sikayet->customer)
                                    <div>
                                        <a href="{{ route('musteri.profil.show', $sikayet->customer_id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ Str::limit($sikayet->customer->name, 25) }}</a>
                                    </div>
                                @else
                                    {{ Str::limit($sikayet->musteri_adi, 25) }}
                                @endif
                            </td>'''
new_customer_td = '''<td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($sikayet->customer && $sikayet->customer->logo_path)
                                        <img src="{{ asset('storage/'.$sikayet->customer->logo_path) }}" class="w-6 h-6 rounded-full object-cover border border-gray-200">
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-[10px] flex-shrink-0">
                                            {{ mb_substr($sikayet->customer ? $sikayet->customer->name : $sikayet->musteri_adi, 0, 1) }}
                                        </div>
                                    @endif
                                    @if($sikayet->customer)
                                        <a href="{{ route('musteri.profil.show', $sikayet->customer_id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium truncate max-w-[150px]" title="{{ $sikayet->customer->name }}">
                                            {{ Str::limit($sikayet->customer->name, 20) }}
                                        </a>
                                    @else
                                        <span class="truncate max-w-[150px]" title="{{ $sikayet->musteri_adi }}">{{ Str::limit($sikayet->musteri_adi, 20) }}</span>
                                    @endif
                                </div>
                            </td>'''
content = content.replace(old_customer_td, new_customer_td)

# Fix empty colspan
content = content.replace('colspan="7"', 'colspan="8"')

# Devamini Goster (Load More)
old_pagination = '''        @if($detayTablosu->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                {{ $detayTablosu->links() }}
            </div>
        @endif'''
new_pagination = '''        @if($detayTablosu->hasMorePages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
                <button wire:click="loadMore" wire:loading.attr="disabled" class="px-6 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:text-indigo-600 hover:border-indigo-200 shadow-sm transition-all flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="loadMore">
                        <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                    <span wire:loading wire:target="loadMore">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </span>
                    Devamını Göster
                </button>
            </div>
        @endif'''
content = content.replace(old_pagination, new_pagination)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)

print('Success')
