<div class="space-y-6">
    @forelse ($sikayetler as $sikayet)

        {{-- MANTIK: Şikayeti giren kişi bir Personel DEĞİLSE, o bir Müşteridir. --}}
        @php
            $isCustomerEntry = false;
            // 1. Durum: Oluşturan kişi var ama 'is_personnel' değeri 0 (Müşteri Yetkilisi)
            if ($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->is_personnel == 0) {
                $isCustomerEntry = true;
            }
            // 2. Durum: Oluşturan kişi yok ama user_id var (Public formdan giriş)
            elseif ($sikayet->user_id && !$sikayet->olusturanKurulUyesi) {
                $isCustomerEntry = true;
            }
        @endphp

        <div wire:key="card-{{ $sikayet->id }}" x-data="{ openLogs: false }"
            class="rounded-2xl border shadow-sm hover:shadow-xl transition-all duration-300 relative group overflow-hidden
                         {{-- MÜŞTERİ İSE: Koyu Kırmızı Çerçeve ve Açık Kırmızı Arkaplan --}}
                         {{ $isCustomerEntry ? 'bg-red-50 border-red-600 ring-1 ring-red-200' : 'bg-white border-gray-200' }}">

            {{-- MÜŞTERİ GİRDİSİ UYARI ŞERİDİ (SAĞ ÜST) --}}
            @if($isCustomerEntry)
                <div
                    class="absolute right-0 top-0 bg-red-700 text-white text-[10px] font-black tracking-wider px-3 py-1 rounded-bl-xl z-20 shadow-md flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    MÜŞTERİ GİRDİSİ
                </div>
            @endif

            {{-- SOL ŞERİT (Durum Rengi - Modelden) --}}
            @php
                // Modelden gelen renk kodunu (blue, green vs.) alıp Tailwind class'ına çeviriyoruz.
                $renkKodu = $sikayet->durum_rengi; // Modelden gelir (blue, green, red, purple, indigo)
                $seritRengi = 'bg-' . $renkKodu . '-500'; // Örn: bg-blue-500

                // Eğer gri ise biraz daha koyu yapalım görünsün
                if ($renkKodu == 'gray')
                    $seritRengi = 'bg-gray-400';
            @endphp
            <div class="absolute left-0 top-0 bottom-0 w-2 {{ $seritRengi }}"></div>

            <div class="p-5 md:p-6 pl-7"> {{-- Sol şerit için padding --}}

                {{-- ÜST KISIM --}}
                <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-5">
                    <div class="flex items-start gap-4 w-full">
                        {{-- LOGO ALANI --}}
                        <div class="flex-shrink-0">
                            @if($sikayet->customer && $sikayet->customer->logo_path)
                                <img class="h-16 w-16 rounded-xl object-contain border shadow-sm bg-white p-1 {{ $isCustomerEntry ? 'border-red-300' : 'border-gray-200' }}"
                                    src="{{ asset('storage/' . $sikayet->customer->logo_path) }}"
                                    alt="{{ $sikayet->customer->name }}">
                            @else
                                <div
                                    class="h-16 w-16 rounded-xl flex items-center justify-center text-2xl font-bold shadow-md
                                                                {{ $isCustomerEntry ? 'bg-red-200 text-red-800 border border-red-300' : 'bg-gradient-to-br from-indigo-500 to-purple-600 text-white' }}">
                                    {{ $sikayet->customer ? strtoupper(substr($sikayet->customer->name, 0, 1)) : strtoupper(substr($sikayet->musteri_adi, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Şikayet Konusu --}}
                            <div class="mb-2">
                                <span
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Şikayet
                                    Konusu:</span>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-xl font-black text-gray-900 leading-tight truncate hover:underline cursor-pointer
                                                {{ $isCustomerEntry ? 'text-red-900' : 'text-gray-900' }}">
                                        <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}">
                                            {{ $sikayet->musteri_sikayet_konusu }}
                                        </a>
                                    </h2>
                                    <span
                                        class="px-2 py-0.5 rounded-md text-[10px] font-bold border 
                                                    {{ $isCustomerEntry ? 'bg-red-200 text-red-900 border-red-300' : 'bg-indigo-50 text-indigo-700 border-indigo-100' }}">
                                        #{{ $sikayet->id }}
                                        @if($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin']))
                                            <span class="text-amber-500 animate-pulse ml-1" title="Onay Bekliyor">⭐</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- YETKİLİ KİŞİ BİLGİLERİ --}}
                            <div
                                class="text-sm text-gray-600 mb-4 p-2 rounded-lg border inline-block
                                            {{ $isCustomerEntry ? 'bg-red-100/60 border-red-200 text-red-900' : 'bg-gray-50 border-gray-100' }}">
                                @if($sikayet->yetkili_user)
                                    <div
                                        class="font-bold mb-1 flex items-center gap-1 {{ $isCustomerEntry ? 'text-red-900' : 'text-gray-800' }}">
                                        <svg class="w-4 h-4 {{ $isCustomerEntry ? 'text-red-500' : 'text-gray-500' }}"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $sikayet->yetkili_user->name }}
                                    </div>
                                    <div
                                        class="flex flex-wrap gap-x-4 gap-y-1 text-xs {{ $isCustomerEntry ? 'text-red-700' : 'text-gray-500' }}">
                                        @if($sikayet->yetkili_user->telefon)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 opacity-70" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                {{ $sikayet->yetkili_user->telefon }}
                                            </span>
                                        @endif
                                        @if($sikayet->yetkili_user->email)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 opacity-70" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $sikayet->yetkili_user->email }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span
                                        class="italic text-gray-500">{{ $sikayet->musteri_iletisim ?? 'İletişim bilgisi yok' }}</span>
                                @endif
                            </div>

                            {{-- Firma İsmi --}}
                            <div>
                                <span
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Firma
                                    İsmi:</span>
                                <h3 class="text-base font-bold leading-snug hover:underline cursor-pointer
                                                {{ $isCustomerEntry ? 'text-red-900' : 'text-indigo-700' }}">
                                        @if($sikayet->customer)
                                            <a href="{{ route('musteri.profil.show', $sikayet->customer->id) }}" target="_blank"
                                                title="Müşteri Profiline Git">
                                                {{ $sikayet->customer->name }}
                                            </a>
                                        @else
                                            {{ $sikayet->musteri_adi }}
                                        @endif
                                </h3>
                            </div>
                        </div>
                    </div>

                    {{-- Durum Badge --}}
                    <div class="flex-shrink-0 flex flex-col items-end gap-1">
                        {!! $sikayet->musteri_durum_badge !!}
                        
                        {{-- MAİL DURUMU (SADECE YETKİLİLER GÖRÜR) --}}
                        @hasanyrole('Superadmin|Yonetim|Müşteri Şikayeti Kurulu|Müşteri Şikayeti Kurulu - Yurt İçi|Müşteri Şikayeti Kurulu - Yurt Dışı|Müşteri Şikayeti Kurulu Yöneticisi|Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi|Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı|Bölüm Kalite Yöneticisi|Müşteri Şikayeti Çözüm Lideri')
                            @if(!$sikayet->mail_sent && $sikayet->mail_error)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-rose-100 text-rose-600 border border-rose-200 cursor-help animate-pulse" 
                                      title="BİLDİRİM HATASI: {{ $sikayet->mail_error }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2zM18.364 5.636l-12.728 12.728" />
                                    </svg>
                                    MAİL GİTMEDİ
                                </span>
                            @endif
                        @endhasanyrole

                        {{-- Ziyaret Planlandı Etiketi --}}
                        @if($sikayet->iaaProjesi && $sikayet->iaaProjesi->visit_planned)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-blue-100 text-blue-800 border border-blue-200" title="Ziyaret Planlandı">
                                📅 Ziyaret Planlandı
                            </span>
                        @endif

                        {{-- YENİ: İade Var (Onaylı) Etiketi --}}
                        @if($sikayet->iadeler()->exists() && $sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, ['Tamamlandı', 'Çözümlendi', 'Kapatıldı']))
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-rose-100 text-rose-800 border border-rose-200" title="İade Onaylandı">
                                ♻️ İade Var
                            </span>
                        @endif
                    </div>
                </div>

                {{-- ORTA KISIM: DETAY GRID (8 ELEMAN) --}}
                <div class="rounded-xl p-4 border grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 text-sm mb-5
                                {{ $isCustomerEntry ? 'bg-red-100/40 border-red-200' : 'bg-slate-50 border-slate-100' }}">

                    {{-- 1. Kategori --}}
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Kategori</span>
                        <div class="font-bold text-gray-700">{{ $sikayet->sikayetKategori->ad ?? 'Genel' }}</div>
                    </div>

                    {{-- 2. Alt Kategori --}}
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Alt Kategori</span>
                        <div class="font-medium text-gray-600">
                            @if($sikayet->sikayetAltKategori)
                                {{ $sikayet->sikayetAltKategori->ad }}
                                @if(Str::lower(trim($sikayet->sikayetAltKategori->ad)) === 'diğer / belirtilmemiş' || Str::lower(trim($sikayet->sikayetAltKategori->ad)) === 'diğer')
                                    @if($sikayet->sikayet_alt_kategori_diger)
                                        <br><span class="text-[10px] text-gray-400 italic" title="{{ $sikayet->sikayet_alt_kategori_diger }}">({{ Str::limit($sikayet->sikayet_alt_kategori_diger, 20) }})</span>
                                    @endif
                                @endif
                            @elseif($sikayet->sikayet_alt_kategori_diger)
                                <span title="{{ $sikayet->sikayet_alt_kategori_diger }}">{{ Str::limit($sikayet->sikayet_alt_kategori_diger, 30) }}</span>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    {{-- 3. Takım --}}
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Takım</span>
                        <div class="font-medium text-gray-700">
                            @if($sikayet->cozumTakimi)
                                <a href="{{ route('admin.cozum-takimlari.show', $sikayet->cozumTakimi->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 hover:underline transition-colors font-bold">
                                    {{ $sikayet->cozumTakimi->ad }}
                                </a>
                            @else
                                <span class="text-gray-400 italic">Atanmadı</span>
                            @endif
                        </div>
                    </div>

                    {{-- 4. Öncelik --}}
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Öncelik</span>
                        @php
                            $oncelikClass = match ($sikayet->musteri_oncelik) {
                                'Acil' => 'text-red-600 bg-red-50 border-red-100',
                                'Yüksek' => 'text-orange-600 bg-orange-50 border-orange-100',
                                'Normal' => 'text-blue-600 bg-blue-50 border-blue-100',
                                'Düşük' => 'text-green-600 bg-green-50 border-green-100',
                                default => 'text-gray-600 bg-gray-100 border-gray-200'
                            };
                        @endphp
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded border text-xs font-bold {{ $oncelikClass }}">
                            @if($sikayet->musteri_oncelik == 'Acil') 🔥 @endif
                            {{ $sikayet->musteri_oncelik }}
                        </span>
                    </div>

                    {{-- 5. Konum --}}
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Konum</span>
                        <div class="font-medium text-gray-700">{{ $sikayet->konum_tipi ?? 'Belirtilmedi' }}</div>
                    </div>

                    {{-- 6. Tarihler --}}
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Tarihler</span>
                        <div class="font-medium text-gray-700 text-xs mb-1">
                            <span class="text-gray-400">Kayıt:</span> {{ $sikayet->created_at->format('d.m.Y') }}
                        </div>
                        <div class="font-bold text-xs text-red-600">
                            <span class="text-red-400">Son:</span>
                            {{ $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A' }}
                        </div>
                    </div>

                    {{-- 7. Ekleyen (GÜNCELLENEN KISIM) --}}
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Ekleyen</span>
                        @if($isCustomerEntry)
                            {{-- MÜŞTERİ İSE VE FİRMA ID VARSA -> FİRMA PROFİLİNE LİNK --}}
                            @if($sikayet->customer_id)
                                <a href="{{ route('musteri.profil.show', $sikayet->customer_id) }}" target="_blank"
                                    class="font-black text-sm text-red-700 hover:text-red-900 hover:underline flex items-center gap-1 transition-colors">
                                    {{ $sikayet->olusturanKurulUyesi->name ?? 'Müşteri Yetkilisi' }}
                                    <span
                                        class="text-[9px] bg-red-200 text-red-800 px-1.5 py-0.5 rounded border border-red-300 shadow-sm font-bold">DIŞ</span>
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @else
                                <span class="font-black text-sm text-red-700 flex items-center gap-1">
                                    {{ $sikayet->olusturanKurulUyesi->name ?? 'Müşteri' }}
                                    <span
                                        class="text-[9px] bg-red-200 text-red-800 px-1.5 py-0.5 rounded border border-red-300 shadow-sm font-bold">DIŞ</span>
                                </span>
                            @endif
                        @elseif($sikayet->olusturanKurulUyesi)
                            <a href="{{ route('profile.show', $sikayet->olusturanKurulUyesi->id) }}" target="_blank"
                                class="text-indigo-600 font-bold hover:underline flex items-center gap-1">
                                {{ Str::limit($sikayet->olusturanKurulUyesi->name, 15) }}
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @else
                            <div class="flex items-center gap-1.5">
                                <span class="text-gray-600 font-bold text-sm">Sistem</span>
                                @php
                                    $user = auth()->user();
                                    $canAssign = $user->hasAnyRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı']);
                                    if(!$canAssign && $user->hasRole('Bölüm Kalite Yöneticisi')) {
                                        $yonetilenKategoriIds = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
                                        if (empty($yonetilenKategoriIds) && $user->bolum_id) {
                                            $yonetilenKategoriIds = \App\Models\SikayetKategori::where('bolum_id', $user->bolum_id)->pluck('id')->toArray();
                                        }
                                        $canAssign = in_array($sikayet->sikayet_kategorisi_id, $yonetilenKategoriIds);
                                    }
                                @endphp
                                @if($canAssign && !$sikayet->customer_id)
                                    <button type="button" 
                                        onclick="Livewire.dispatch('openMusteriAtamaModal', { sikayetId: {{ $sikayet->id }} })"
                                        class="p-1 rounded-full bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-colors shadow-sm" title="Müşteri Tanımla / Ata">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- 8. Puan --}}
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Puan</span>
                        <div
                            class="font-bold flex items-center gap-1 {{ $sikayet->musteri_puan ? 'text-yellow-600' : 'text-gray-400' }}">
                            @if($sikayet->musteri_puan) <svg class="w-3 h-3 text-yellow-500" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg> @endif
                            {{ $sikayet->musteri_puan ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                {{-- EK BİLGİLER: PROJE DURUMU VE FEEDBACK --}}
                <div
                    class="sm:ml-14 mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-x-4 gap-y-3 text-sm bg-gray-50/70 rounded-lg p-3 border border-gray-200/60 mb-5">

                    {{-- 1. PROJE DURUMU --}}
                    <div class="flex flex-wrap items-center gap-2 text-gray-600">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium mr-1">Proje Durumu:</span>

                        @if($sikayet->iaaProjesi)

                            {{-- DÜZELTME: Modelden gelen akıllı etiketi kullan + Bekleme Süresi --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <div class="scale-90 origin-left"> {{-- Biraz küçültelim ki sığsın --}}
                                    {!! $sikayet->iaaProjesi->durum_etiketi !!}
                                </div>
                                @if($sikayet->iaaProjesi->bekleme_suresi_metni)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100 shadow-sm animate-pulse">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ $sikayet->iaaProjesi->bekleme_suresi_metni }}
                                    </span>
                                @endif
                            </div>

                            {{-- === İLERLEME VE KAPANIŞ UYARISI === --}}
                            @php $ilerleme = $sikayet->iaaProjesi->ilerleme_verisi; @endphp

                            @if($ilerleme['toplam'] > 0)
                                <div class="flex flex-col gap-1 w-full max-w-[200px] mt-1">
                                    <div class="flex justify-between items-center text-[9px] font-black uppercase">
                                        <span class="text-indigo-600 tracking-tighter">İş Akışı ({{ $ilerleme['gecen_gun'] }}. Gün)</span>
                                        <span class="text-gray-500">{{ $ilerleme['tamamlanan'] }}/{{ $ilerleme['toplam'] }} Adım</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden border border-gray-300 shadow-inner">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-1.5 rounded-full transition-all duration-1000" 
                                             style="width: {{ $ilerleme['yuzde'] }}%"></div>
                                    </div>
                                </div>
                            @endif

                            @if($ilerleme['kapanis_bekleniyor'])
                                <div class="mt-2 flex items-center gap-2 px-3 py-1.5 bg-orange-100 text-orange-800 border-l-4 border-orange-500 rounded-r shadow-sm animate-bounce">
                                    <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="text-[10px] font-black uppercase leading-none tracking-tight">Kapanış İşlemleri Bekleniyor!</span>
                                </div>
                            @endif
                            {{-- ========================================= --}}

                            {{-- ========================================= --}}

                            {{-- Süre hesabı için değişkeni tanımlayalım --}}
                            @php $pDurum = $sikayet->iaaProjesi->durum; @endphp

                            {{-- SÜRE HESAPLAMA VE DURUM METNİ --}}

                            @php
                                $closedStates = ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi'];
                                $isClosed = in_array($pDurum, $closedStates);
                            @endphp

                            @if($isClosed)
                                @php
                                    $bitisTarihi = $sikayet->iaaProjesi->updated_at;
                                    $gecenGun = ceil($sikayet->created_at->diffInDays($bitisTarihi));
                                    if ($gecenGun <= 0)
                                        $gecenGun = 1;

                                    $text = match ($pDurum) {
                                        'Tamamlandı' => 'Günde Çözüldü',
                                        'talep_olarak_kapatildi' => 'Günde Kapandı',
                                        'hatali_bildirim_olarak_kapatildi' => 'Günde Kapandı',
                                        default => 'Günde Sonuçlandı'
                                    };

                                    $color = match ($pDurum) {
                                        'Tamamlandı' => 'text-emerald-600',
                                        'talep_olarak_kapatildi' => 'text-emerald-500',
                                        'hatali_bildirim_olarak_kapatildi' => 'text-emerald-500',
                                        default => 'text-emerald-500'
                                    };
                                @endphp
                                <div class="flex flex-col ml-1">
                                    <span class="text-xs font-black {{ $color }} flex items-center gap-1 animate-pulse">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        ({{ $gecenGun }} {{ $text }})
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-semibold italic">
                                        Kapanış: {{ $bitisTarihi->format('d.m.Y') }}
                                    </span>
                                </div>
                            @endif
                        @else
                            {{-- HİÇ PROJE YOKSA (ATANMADI DURUMU) --}}
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-gray-200 text-gray-700 border border-gray-300">
                                ATANMADI
                            </span>
                            @php
                                $gecenGun = ceil($sikayet->created_at->diffInDays(now()));
                            @endphp
                            <span class="text-xs font-bold text-red-600 flex items-center gap-1 ml-1 animate-pulse">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                ({{ $gecenGun }} Gündür Bekliyor)
                            </span>
                        @endif
                    </div>

                    {{-- MÜŞTERİ GERİ BİLDİRİMİ --}}
                    @if($sikayet->musteri_feedback)
                        @php
                            $fbRenk = match ($sikayet->musteri_feedback) {
                                'Onaylandı' => 'text-green-600 bg-green-50 border-green-100',
                                'Reddedildi' => 'text-red-600 bg-red-50 border-red-100',
                                default => 'text-gray-600 bg-gray-50 border-gray-100'
                            };
                        @endphp
                        <div class="mt-2 flex items-start gap-2 p-2 rounded-lg border {{ $fbRenk }}">
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold uppercase">Müşteri Kararı:
                                    {{ $sikayet->musteri_feedback }}</span>
                                @if($sikayet->musteri_feedback_note)
                                    <p class="text-xs mt-0.5 italic opacity-90 truncate">"{{ $sikayet->musteri_feedback_note }}"</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ALT KISIM: BUTONLAR VE LOGLAR --}}
                <div class="flex flex-wrap items-center justify-end gap-2 pt-3 border-t border-gray-100">

                    {{-- Geçmiş Butonu --}}
                    <button @click="openLogs = !openLogs"
                        class="text-xs font-bold text-gray-500 hover:text-indigo-600 bg-gray-50 hover:bg-indigo-50 px-3 py-2 rounded-lg transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="openLogs ? 'Geçmişi Gizle' : 'Geçmişi Gör'"></span>
                    </button>

                    <div class="flex-grow"></div>

                    {{-- Yönetim Butonu --}}
                    @role('Superadmin|Müşteri Şikayeti Kurulu|Müşteri Şikayeti Kurulu - Yurt İçi|Müşteri Şikayeti Kurulu - Yurt Dışı|Müşteri Şikayeti Kurulu Yöneticisi|Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi|Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı')
                    <button x-data="{ loading: false }"
                        @click="loading = true; $wire.$dispatch('openTriyajModal', { id: {{ $sikayet->id }} })"
                        @triyaj-modal-opened.window="loading = false"
                        :disabled="loading"
                        :class="loading ? 'opacity-50 cursor-wait bg-emerald-100' : 'bg-emerald-50 hover:bg-emerald-100'"
                        class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-emerald-700 border border-emerald-200 transition-all">
                        <svg x-show="!loading" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="loading" style="display: none;" class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-emerald-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="loading ? 'Bekleyin...' : 'Yönet'"></span>
                    </button>
                    @endrole

                    {{-- === YENİ: MÜŞTERİ ATA BUTONU === --}}
                    @if(!$sikayet->customer_id)
                        @php
                            $user = auth()->user();
                            $canAssign = $user->hasAnyRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı']);
                            if(!$canAssign && $user->hasRole('Bölüm Kalite Yöneticisi')) {
                                $yonetilenKategoriIds = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
                                if (empty($yonetilenKategoriIds) && $user->bolum_id) {
                                    $yonetilenKategoriIds = \App\Models\SikayetKategori::where('bolum_id', $user->bolum_id)->pluck('id')->toArray();
                                }
                                $canAssign = in_array($sikayet->sikayet_kategorisi_id, $yonetilenKategoriIds);
                            }
                        @endphp
                        @if($canAssign)
                            <button type="button" 
                                onclick="Livewire.dispatch('openMusteriAtamaModal', { sikayetId: {{ $sikayet->id }} })"
                                class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                Müşteri Ata
                            </button>
                        @endif
                    @endif

                    {{-- Detay Butonu --}}
                    <a href="{{ route('admin.sikayetler.show', $sikayet) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all">
                        Detay
                        @if($sikayet->dosyalar && $sikayet->dosyalar->isNotEmpty())
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Ekli Dosya Var">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        @endif
                    </a>

                    {{-- Düzenle Butonu --}}
                    @can('update', $sikayet)
                        <a href="{{ route('admin.sikayetler.edit', $sikayet) }}"
                            class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all">
                            Düzenle
                        </a>
                    @endcan

                    {{-- Sil / Geri Al Butonları --}}
                    @can('delete', clone $sikayet)
                        @if($activeTab === 'cop_kutusu')
                            <button type="button" wire:click="restoreFromCopKutusu({{ $sikayet->id }})"
                                class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all" title="Verileri geri listeye alır">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Geri Al
                            </button>
                            <button type="button" wire:click="confirmDelete({{ $sikayet->id }})"
                                class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition-all" title="Veritabanından tamamen siler">
                                Kalıcı Sil
                            </button>
                        @else
                            <button type="button" wire:click="confirmDelete({{ $sikayet->id }})"
                                class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition-all">
                                Sil
                            </button>
                        @endif
                    @endcan

                    {{-- Projeye Git --}}
                    @if($sikayet->iaaProjesi)
                        <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" target="_blank"
                            class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-black text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-indigo-200 transition-all duration-300 transform hover:-translate-y-0.5 group">
                            <svg class="w-5 h-5 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Projeye Git &rarr;
                        </a>
                    @endif
                </div>

                {{-- LOGLAR (Accordion - Timeline Style) --}}
                <div x-show="openLogs" x-transition
                    class="mt-4 pt-6 border-t border-gray-100 bg-gray-50/50 -mx-6 -mb-6 px-6 pb-6 relative">

                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-4 tracking-wider">İşlem Geçmişi</h4>

                    <div class="space-y-0 relative border-l-2 border-gray-200 ml-3">
                        @forelse($sikayet->loglar->sortByDesc('created_at') as $log)
                            <div class="mb-6 ml-6 relative group">
                                {{-- Timeline Dot --}}
                                <span
                                    class="absolute -left-[31px] top-1.5 flex items-center justify-center w-3 h-3 bg-white border-2 border-gray-300 rounded-full group-hover:border-indigo-500 group-hover:scale-125 transition-all"></span>

                                {{-- Tarih ve Kullanıcı --}}
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-gray-500 mb-1">
                                    <span
                                        class="font-mono font-bold text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">
                                        {{ $log->created_at->format('d.m.Y H:i') }}
                                    </span>
                                    {{-- <span class="text-[10px] opacity-75">Sistem/Kullanıcı</span> --}}
                                </div>

                                {{-- Açıklama --}}
                                <div
                                    class="text-sm text-gray-700 bg-white p-3 rounded-lg border border-gray-100 shadow-sm group-hover:shadow-md transition-shadow">
                                    {!! nl2br(e($log->aciklama)) !!}
                                </div>
                            </div>
                        @empty
                            <div class="ml-6 text-gray-400 italic text-xs">Henüz bir işlem kaydı yok.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Şikayet bulunamadı</h3>
            <p class="mt-1 text-sm text-gray-500">Arama kriterlerinizi değiştirin veya yeni bir şikayet oluşturun.</p>
        </div>
    @endforelse
</div>