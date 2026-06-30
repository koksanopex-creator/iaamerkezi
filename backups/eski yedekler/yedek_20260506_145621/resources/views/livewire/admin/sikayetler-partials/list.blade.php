<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    @can('deleteAny', \App\Models\MusteriSikayeti::class)
                    <th class="px-4 py-4 text-center w-10">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    </th>
                    @endcan
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center w-12">#No</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Firma / Şikayet</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Durum</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Öncelik</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Kategori</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Son Tarih</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($sikayetler as $index => $sikayet)
                    @php
                        $isSelected = in_array((string) $sikayet->id, $selectedSikayetler);
                        
                        $isCustomerEntry = false;
                        if ($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->is_personnel == 0) {
                            $isCustomerEntry = true;
                        } elseif ($sikayet->user_id && !$sikayet->olusturanKurulUyesi) {
                            $isCustomerEntry = true;
                        }
                        
                        // Sıra numarası hesaplama (sayfalama dikkate alınarak)
                        $rowNumber = ($sikayetler->currentPage() - 1) * $sikayetler->perPage() + $index + 1;
                    @endphp
                    <tr wire:key="list-{{ $sikayet->id }}" class="hover:bg-indigo-50/30 transition-colors group {{ $isCustomerEntry ? 'bg-red-50/30' : '' }} {{ $isSelected ? 'bg-indigo-50' : '' }}">
                        {{-- 0. Seçim Kutusu --}}
                        @can('deleteAny', \App\Models\MusteriSikayeti::class)
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" wire:model.live="selectedSikayetler" value="{{ (string) $sikayet->id }}"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        </td>
                        @endcan

                        {{-- 1. Sıra No --}}
                        <td class="px-4 py-4 text-center font-bold text-gray-400 text-xs">
                            {{ $rowNumber }}
                        </td>

                        {{-- 2. Logo ve Firma/Konu --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                @if($sikayet->customer && $sikayet->customer->logo_path)
                                    <img class="h-10 w-10 rounded-lg object-contain border bg-white p-0.5"
                                        src="{{ asset('storage/' . $sikayet->customer->logo_path) }}"
                                        alt="{{ $sikayet->customer->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-lg flex items-center justify-center font-bold text-xs shadow-sm bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                        {{ $sikayet->customer ? strtoupper(substr($sikayet->customer->name, 0, 1)) : strtoupper(substr($sikayet->musteri_adi, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-black text-gray-900 truncate">
                                            {{ $sikayet->customer ? $sikayet->customer->name : $sikayet->musteri_adi }}
                                            @if($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin']))
                                                <span class="text-amber-500 animate-pulse ml-1" title="Onay Bekliyor">⭐</span>
                                            @endif
                                        </span>
                                        @if($isCustomerEntry)
                                            <span class="bg-red-700 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-sm">MÜŞTERİ</span>
                                        @endif
                                        {{-- === YENİ: SİSTEM ETİKETİ === --}}
                                        @if(!$sikayet->customer_id)
                                            <span class="bg-amber-100 text-amber-800 text-[8px] font-black px-1.5 py-0.5 rounded border border-amber-200 shadow-sm">SİSTEM (TANITSIZ)</span>
                                        @endif
                                        @if($sikayet->iaaProjesi && $sikayet->iaaProjesi->visit_planned)
                                            <span class="bg-blue-100 text-blue-800 text-[8px] font-black px-1.5 py-0.5 rounded border border-blue-200 shadow-sm whitespace-nowrap" title="Ziyaret Planlandı">📅 ZİYARET</span>
                                        @endif
                                        @if($sikayet->iadeler()->exists() && $sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, ['Tamamlandı', 'Çözümlendi', 'Kapatıldı']))
                                            <span class="bg-rose-100 text-rose-800 text-[8px] font-black px-1.5 py-0.5 rounded border border-rose-200 shadow-sm whitespace-nowrap" title="İade Onaylandı">♻️ İADE</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-indigo-600 font-bold truncate hover:underline underline-offset-2">
                                        <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}">
                                            {{ Str::limit($sikayet->musteri_sikayet_konusu, 60) }}
                                        </a>
                                    </div>

                                    {{-- === YENİ: LİSTE GÖRÜNÜMÜ İLERLEME GÖSTERGESİ === --}}
                                    @if($sikayet->iaaProjesi)
                                        @php $ilerleme = $sikayet->iaaProjesi->ilerleme_verisi; @endphp
                                        @if($ilerleme['toplam'] > 0)
                                            <div class="mt-1 flex items-center gap-2">
                                                <div class="w-12 bg-gray-200 rounded-full h-1 overflow-hidden border border-gray-300">
                                                    <div class="bg-indigo-500 h-1" style="width: {{ $ilerleme['yuzde'] }}%"></div>
                                                </div>
                                                <span class="text-[9px] font-bold text-gray-500">{{ $ilerleme['tamamlanan'] }}/{{ $ilerleme['toplam'] }} ({{ $ilerleme['gecen_gun'] }}.G)</span>
                                                @if($ilerleme['kapanis_bekleniyor'])
                                                    <span class="text-[9px] font-black text-orange-600 animate-pulse flex items-center gap-0.5">
                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                        KAPANMA BEKLİYOR
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                    {{-- =============================================== --}}
                                </div>
                            </div>
                        </td>

                        {{-- 3. Durum --}}
                        <td class="px-4 py-4 text-center">
                            <div class="scale-90">
                                {!! $sikayet->musteri_durum_badge !!}
                            </div>
                            @php
                                $user = auth()->user();
                                $requiresAction = false;
                                if ($sikayet->musteri_durum === 'Bölüm Onayı Bekliyor' && $user->hasRole('Bölüm Lideri') && $sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum_id == $user->bolum_id) {
                                    $requiresAction = true;
                                } elseif ($sikayet->musteri_durum === 'Direktör Onayı Bekliyor' && $user->hasRole('Direktör') && $sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum && $sikayet->sikayetKategori->bolum->director_id == $user->id) {
                                    $requiresAction = true;
                                } elseif ($sikayet->musteri_durum === 'Yönetici Onayı Bekliyor' && $user->hasRole(['Superadmin', 'Yonetim'])) {
                                    $requiresAction = true;
                                } elseif ($sikayet->musteri_durum === 'Hukuk Onayı Bekliyor' && $user->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi'])) {
                                    $requiresAction = true;
                                }
                            @endphp
                            @if($requiresAction)
                                <div class="text-[9px] text-rose-600 mt-1 animate-pulse font-black flex justify-center items-center gap-1 bg-rose-50 rounded-full px-2 py-0.5 border border-rose-100">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="12"/>
                                    </svg>
                                    ONAYINIZ BEKLENİYOR
                                </div>
                            @endif
                        </td>

                        {{-- 4. Öncelik --}}
                        <td class="px-4 py-4 text-center">
                            @php
                                $oncelikClass = match ($sikayet->musteri_oncelik) {
                                    'Acil' => 'text-red-600 bg-red-50 border-red-100',
                                    'Yüksek' => 'text-orange-600 bg-orange-50 border-orange-100',
                                    'Normal' => 'text-blue-600 bg-blue-50 border-blue-100',
                                    'Düşük' => 'text-green-600 bg-green-50 border-green-100',
                                    default => 'text-gray-600 bg-gray-100 border-gray-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded border text-[10px] font-bold {{ $oncelikClass }}">
                                {{ $sikayet->musteri_oncelik }}
                            </span>
                        </td>

                        {{-- 5. Kategori --}}
                        <td class="px-4 py-4 text-center">
                            <span class="text-xs font-medium text-gray-600">{{ $sikayet->sikayetKategori->ad ?? 'Genel' }}</span>
                        </td>

                        {{-- 6. Son Tarih --}}
                        <td class="px-4 py-4 text-center text-xs">
                            <div class="font-bold {{ $sikayet->musteri_cozum_son_tarihi && \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->isPast() ? 'text-red-600' : 'text-gray-700' }}">
                                {{ $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : '-' }}
                            </div>
                        </td>

                        {{-- 7. İşlemler --}}
                        <td class="px-4 py-4 text-right space-x-1 whitespace-nowrap">
                            {{-- === YENİ: HIZLI MÜŞTERİ ATA BUTONU === --}}
                            @if(!$sikayet->customer_id)
                                @php
                                    $user = auth()->user();
                                    $canAssign = $user->hasAnyRole(['Superadmin', 'Müşteri Şikayeti Kurulu']);
                                    if(!$canAssign && $user->hasRole('Bölüm Kalite Yöneticisi')) {
                                        $yonetilenKategoriIds = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
                                        if (empty($yonetilenKategoriIds) && $user->bolum_id) {
                                            $yonetilenKategoriIds = \App\Models\SikayetKategori::where('bolum_id', $user->bolum_id)->pluck('id')->toArray();
                                        }
                                        $canAssign = in_array($sikayet->sikayet_kategorisi_id, $yonetilenKategoriIds);
                                    }
                                @endphp
                                @if($canAssign)
                                    <button onclick="Livewire.dispatch('openMusteriAtamaModal', { sikayetId: {{ $sikayet->id }} })"
                                        class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors inline-block" title="Müşteri Ata">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                    </button>
                                @endif
                            @endif

                            {{-- Yönetim Butonu --}}
                            @role('Superadmin|Müşteri Şikayeti Kurulu')
                                <button wire:click="$dispatch('openTriyajModal', { id: {{ $sikayet->id }} })"
                                    class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors inline-block" title="Yönet">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            @endrole

                            {{-- Detay Butonu --}}
                            <a href="{{ route('admin.sikayetler.show', $sikayet) }}" 
                                class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors inline-block" title="Detay">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            {{-- Düzenle Butonu --}}
                            @can('update', $sikayet)
                                <a href="{{ route('admin.sikayetler.edit', $sikayet) }}"
                                    class="p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-50 transition-colors inline-block" title="Düzenle">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            @endcan

                            {{-- Sil Butonu --}}
                                @if(Auth::user()->can('delete', clone $sikayet))
                                    @if($activeTab === 'cop_kutusu')
                                    <button wire:click="restoreFromCopKutusu({{ $sikayet->id }})" title="Geri Al"
                                        class="p-2 text-green-500 hover:text-green-700 bg-green-50 hover:bg-green-100 rounded-lg transition-colors border border-green-200 hidden sm:block">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                        </svg>
                                    </button>
                                    
                                    <button wire:click="confirmDelete({{ $sikayet->id }})" title="Kalıcı Sil"
                                        class="p-2 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200 hidden sm:block">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    @else
                                    <button wire:click="confirmDelete({{ $sikayet->id }})" title="Sil"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors hidden sm:block">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    @endif
                                @endif

                            {{-- Projeye Git --}}
                            @if($sikayet->iaaProjesi)
                                <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" target="_blank"
                                    class="p-1.5 rounded-lg text-purple-600 hover:bg-purple-50 transition-colors inline-block" title="Projeye Git">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">
                            Şikayet bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
