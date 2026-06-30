@php
    use Illuminate\Support\Str;
@endphp

@push('pageTitle')
    Tüm Şikayet Listesi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tüm Müşteri Şikayetleri') }}
            </h2>
            <a href="{{ route('admin.sikayet-raporlari.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Raporlara Geri Dön
            </a>
        </div>
    </x-slot>

    {{-- Sayfada yatay scroll oluşmasın --}}
    <div class="py-10 bg-gradient-to-br from-gray-50 to-gray-100 overflow-x-hidden">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- === KPI KARTLARI GÜNCELLENDİ === --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Toplam Kayıt</div>
                    <div class="text-2xl font-black text-blue-600">{{ $sikayetler->total() }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Yeni (Beklemede)</div>
                    <div class="text-2xl font-black text-yellow-600">{{ $stats['yeni'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">İşlemde</div>
                    <div class="text-2xl font-black text-indigo-600">{{ $stats['islemde'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Çözülen / Kapatılan</div>
                    <div class="text-2xl font-black text-green-600">{{ $stats['cozulen'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Talep Olarak Kapatılan</div>
                    <div class="text-2xl font-black text-gray-600">{{ $stats['talep_kapatilan'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Hatalı Bildirim</div>
                    <div class="text-2xl font-black text-rose-500">{{ $stats['hatali_bildirim'] }}</div>
                </div>
                {{-- Not: "En Çok Kategori" için 4 kart yetersiz kalırsa diye ayrı bir kart yaptım --}}
            </div>

            @if($stats['enCokKategori'])
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">En Yoğun Kategori</div>
                    <div class="text-lg font-bold text-gray-800 truncate" title="{{ $stats['enCokKategori']->ad }}">
                        {{ $stats['enCokKategori']->ad }}
                        <span class="text-base font-medium text-gray-500">({{ $stats['enCokKategori']->total }} adet)</span>
                    </div>
                </div>
            @endif
            {{-- === FİLTRELEME BÖLÜMÜ === --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <form action="{{ route('admin.sikayet-raporlari.tum-liste') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bitiş Tarihi</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>
                        <select name="kategori_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tümü</option>
                            @foreach($kategoriler as $kat)
                                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->ad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Durum</label>
                        <select name="durum" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tümü</option>
                            <option value="Yeni" {{ request('durum') == 'Yeni' ? 'selected' : '' }}>Yeni</option>
                            <option value="İşlemde" {{ request('durum') == 'İşlemde' ? 'selected' : '' }}>İşlemde</option>
                            <option value="Çözümlendi" {{ request('durum') == 'Çözümlendi' ? 'selected' : '' }}>Çözümlendi</option>
                            <option value="Kapatıldı" {{ request('durum') == 'Kapatıldı' ? 'selected' : '' }}>Kapatıldı</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-indigo-700 transition shadow-md">Filtrele</button>
                        <a href="{{ route('admin.sikayet-raporlari.tum-liste') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-200 transition">Temizle</a>
                    </div>
                </form>
            </div>
            {{-- === FİLTRELEME SONU === --}}

            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-200">
                <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

                <div class="p-4 md:p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Kayıtlar</h3>
                </div>

                {{-- === 1. MASAÜSTÜ TABLO GÖRÜNÜMÜ (md ve üzeri) === --}}
                {{-- (Kaydırmayı engellemek için table-fixed ve truncate kullandık) --}}
                <div class="hidden md:block overflow-hidden">
                    <table class="w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 4%;">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 10%;">Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 13%;">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 14%;">Müşteri</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 18%;">Başlık</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 8%;">Durum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 8%;">Yorumlar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 8%;">Son Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 8%;">Resimler</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 9%;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($sikayetler as $index => $sikayet)
                                @php
                                    // === RENKLENDİRME GÜNCELLENDİ ===
                                    $rowBg = 'hover:bg-gray-50';
                                    $rowBar = 'border-l-4 border-transparent';
                                    if ($sikayet->musteri_durum === 'İşlemde') {
                                        $rowBg = 'bg-blue-50/60 hover:bg-blue-100/60';
                                        $rowBar = 'border-l-4 border-blue-400';
                                    } elseif ($sikayet->musteri_durum === 'Yeni') {
                                        $rowBg = 'bg-yellow-50/60 hover:bg-yellow-100/60';
                                        $rowBar = 'border-l-4 border-yellow-400';
                                    } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) {
                                        $rowBg = 'bg-green-50/60 hover:bg-green-100/60';
                                        $rowBar = 'border-l-4 border-green-400';
                                    } elseif (in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) {
                                        $rowBg = 'bg-gray-50 hover:bg-gray-100';
                                        $rowBar = 'border-l-4 border-gray-400';
                                    } elseif (in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) {
                                        $rowBg = 'bg-rose-50/30 hover:bg-rose-100/30';
                                        $rowBar = 'border-l-4 border-rose-300';
                                    } else {
                                        // Diğer durumlar (örn: Yeniden Açıldı) için
                                        $rowBg = 'bg-gray-50/60 hover:bg-gray-100/60';
                                        $rowBar = 'border-l-4 border-gray-400';
                                    }
                                    // === RENKLENDİRME SONU ===
                                @endphp

                                <tr class="{{ $rowBg }} {{ $rowBar }} transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ $sikayetler->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                        {{ $sikayet->created_at?->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 truncate"
                                        title="{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}">
                                        {{ $sikayet->sikayetKategori->ad ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 truncate"
                                        title="{{ $sikayet->musteri_adi }}">
                                        {{ $sikayet->musteri_adi }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 truncate"
                                        title="{{ $sikayet->musteri_sikayet_konusu }}">
                                        {{ $sikayet->musteri_sikayet_konusu }}
                                        @if($sikayet->iadeler_count > 0)
                                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black bg-red-100 text-red-600 border border-red-200 uppercase tracking-tighter" title="Bu şikayet için iade kaydı oluşturulmuş">
                                                İADELİ
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex flex-col gap-1.5">
                                            <div>
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                @if($sikayet->musteri_durum === 'Yeni') bg-yellow-100 text-yellow-800
                                                                @elseif($sikayet->musteri_durum === 'İşlemde') bg-blue-100 text-blue-800
                                                                @elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) bg-green-100 text-green-800
                                                                @elseif(in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) bg-gray-100 text-gray-800 border border-gray-300 font-bold
                                                                @elseif(in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) bg-rose-100 text-rose-800 border border-rose-200 line-through
                                                                @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $sikayet->musteri_durum ?? '—' }}
                                                </span>
                                            </div>
                                            @if(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']) && $sikayet->iaaProjesi)
                                                <div class="flex items-center gap-1 opacity-90 pl-1">
                                                    @php
                                                        $pDurum = $sikayet->iaaProjesi->durum;
                                                        $isFaulty = Str::contains($pDurum, 'hatali_bildirim');
                                                        $isRequest = Str::contains($pDurum, 'talep');
                                                        $tooltipText = $isFaulty ? 'Hatalı Bildirim Olarak Kapatıldı' : ($isRequest ? 'Talep Olarak Kapatıldı' : 'Proje Durumu: ' . $pDurum);
                                                    @endphp

                                                    @if($isFaulty)
                                                        <div class="group relative cursor-help">
                                                            <svg class="w-5 h-5 text-red-500 hover:text-red-700 transition-colors"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                                </path>
                                                            </svg>
                                                            <!-- Tooltip -->
                                                            <span
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                                                {{ $tooltipText }}
                                                            </span>
                                                        </div>
                                                    @elseif($isRequest)
                                                        <div class="group relative cursor-help">
                                                            <svg class="w-5 h-5 text-blue-500 hover:text-blue-700 transition-colors"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                                </path>
                                                            </svg>
                                                            <!-- Tooltip -->
                                                            <span
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                                                {{ $tooltipText }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div title="{{ $pDurum }}">
                                                            {!! $sikayet->iaaProjesi->durum_etiketi !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <div class="flex items-center space-x-1">
                                            @if($sikayet->proje_yorumlari_count > 0)
                                                <span class="font-bold text-gray-700 text-xs">{{ $sikayet->proje_yorumlari_count }}</span>
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                @if($sikayet->musteri_proje_yorumlari_count > 0)
                                                    <span class="text-yellow-500" title="Müşteri Yorumu Var">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-[10px] text-gray-400">Yok</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm whitespace-nowrap {{ $sikayet->musteri_cozum_son_tarihi ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                        {{ $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        @php
                                            $imageFiles = $sikayet->dosyalar->filter(fn($d) => Str::startsWith($d->mime_tipi, 'image/'));
                                        @endphp
                                        <div class="flex items-center space-x-1">
                                            @forelse ($imageFiles->take(2) as $dosya)
                                                <a href="{{ asset('storage/' . $dosya->dosya_yolu) }}" target="_blank"
                                                    title="{{ $dosya->orijinal_adi }}">
                                                    <img src="{{ asset('storage/' . $dosya->dosya_yolu) }}"
                                                        class="h-8 w-8 rounded-md object-cover border border-gray-300 hover:scale-110 transition-transform"
                                                        alt="Önizleme">
                                                </a>
                                            @empty
                                                <span class="text-xs">Yok</span>
                                            @endforelse
                                            @if($imageFiles->count() > 2)
                                                <span
                                                    class="text-xs text-gray-400 font-bold ml-1">+{{ $imageFiles->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.sikayetler.show', $sikayet) }}" target="_blank"
                                                class="inline-flex items-center px-3 py-1.5 font-semibold rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                                Detay
                                            </a>
                                            @if($sikayet->iaaProjesi ?? null)
                                                <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center px-3 py-1.5 font-semibold rounded-md bg-purple-100 text-purple-700 hover:bg-purple-200 transition">
                                                    Proje
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Kayıt bulunamadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- === 2. MOBİL KART GÖRÜNÜMÜ (md'den küçük) === --}}
                <div class="md:hidden">
                    <div class="space-y-4 p-4">
                        @forelse ($sikayetler as $index => $sikayet)
                            @php
                                // === RENKLENDİRME GÜNCELLENDİ ===
                                $rowBg = 'hover:bg-gray-50';
                                $rowBar = 'border-l-4 border-transparent';
                                if ($sikayet->musteri_durum === 'İşlemde') {
                                    $rowBg = 'bg-blue-50/60 hover:bg-blue-100/60';
                                    $rowBar = 'border-l-4 border-blue-400';
                                } elseif ($sikayet->musteri_durum === 'Yeni') {
                                    $rowBg = 'bg-yellow-50/60 hover:bg-yellow-100/60';
                                    $rowBar = 'border-l-4 border-yellow-400';
                                } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) {
                                    $rowBg = 'bg-green-50/60 hover:bg-green-100/60';
                                    $rowBar = 'border-l-4 border-green-400';
                                } elseif (in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) {
                                    $rowBg = 'bg-gray-50 hover:bg-gray-100';
                                    $rowBar = 'border-l-4 border-gray-400';
                                } elseif (in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) {
                                    $rowBg = 'bg-rose-50/30 hover:bg-rose-100/30';
                                    $rowBar = 'border-l-4 border-rose-300';
                                } else {
                                    // Diğer durumlar için
                                    $rowBg = 'bg-gray-50/60 hover:bg-gray-100/60';
                                    $rowBar = 'border-l-4 border-gray-400';
                                }
                                // === RENKLENDİRME SONU ===
                            @endphp



                            <div class="rounded-lg shadow border {{ $rowBg }} {{ $rowBar }} p-4 space-y-3 cursor-pointer"
                                onclick="window.open('{{ route('admin.sikayetler.show', $sikayet) }}', '_blank')"
                                title="Şikayet detayını görmek için tıklayın">
                                {{-- Kart Başı: Tarih ve Durum --}}
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span
                                            class="font-semibold text-gray-700">#{{ ($sikayetler->currentPage() - 1) * $sikayetler->perPage() + $index + 1 }}</span>
                                        <span
                                            class="text-sm text-gray-600 ml-2">{{ $sikayet->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            @if($sikayet->musteri_durum === 'Yeni') bg-yellow-100 text-yellow-800
                                                            @elseif($sikayet->musteri_durum === 'İşlemde') bg-blue-100 text-blue-800
                                                            @elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) bg-green-100 text-green-800
                                                            @elseif(in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) bg-gray-100 text-gray-800 border border-gray-300 font-bold
                                                            @elseif(in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) bg-rose-100 text-rose-800 border border-rose-200 line-through
                                                            @else bg-gray-100 text-gray-800 @endif">
                                        {{ $sikayet->musteri_durum ?? '—' }}
                                    </span>
                                </div>

                                {{-- Kart Gövdesi: Kategori, Başlık, Müşteri --}}
                                <div>
                                    <p class="text-xs text-gray-500 uppercase truncate"
                                        title="{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}">
                                        {{ $sikayet->sikayetKategori->ad ?? 'N/A' }}
                                    </p>
                                    <p class="text-base font-semibold text-gray-900 truncate"
                                        title="{{ $sikayet->musteri_sikayet_konusu }}">
                                        {{ $sikayet->musteri_sikayet_konusu }}
                                        @if($sikayet->iadeler_count > 0)
                                            <span class="inline-flex items-center px-1 py-0.5 rounded text-[8px] font-black bg-red-100 text-red-600 border border-red-200 uppercase tracking-tighter ml-1">
                                                İADELİ
                                            </span>
                                        @endif
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-700 truncate"
                                            title="{{ $sikayet->musteri_adi }}">{{ $sikayet->musteri_adi }}</p>
                                        <div class="flex items-center gap-1 text-xs text-gray-500">
                                            @if($sikayet->proje_yorumlari_count > 0)
                                                <span>{{ $sikayet->proje_yorumlari_count }}</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Kart Altı: Son Tarih ve Resimler --}}
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                    <div class="text-sm">
                                        <span class="text-gray-500">Son Tarih:</span>
                                        <span
                                            class="font-semibold {{ $sikayet->musteri_cozum_son_tarihi ? 'text-red-600' : 'text-gray-500' }}">
                                            {{ $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A' }}
                                        </span>
                                    </div>
                                    @php
                                        $imageFiles = $sikayet->dosyalar->filter(fn($d) => Str::startsWith($d->mime_tipi, 'image/'));
                                    @endphp
                                    <div class="flex items-center space-x-1">
                                        @forelse ($imageFiles->take(2) as $dosya)
                                            <a href="{{ asset('storage/' . $dosya->dosya_yolu) }}" target="_blank"
                                                title="{{ $dosya->orijinal_adi }}">
                                                <img src="{{ asset('storage/' . $dosya->dosya_yolu) }}"
                                                    class="h-8 w-8 rounded-md object-cover border border-gray-300"
                                                    alt="Önizleme">
                                            </a>
                                        @empty
                                            <span class="text-xs text-gray-400">Resim Yok</span>
                                        @endforelse
                                        @if($imageFiles->count() > 2)
                                            <span
                                                class="text-xs text-gray-400 font-bold ml-1">+{{ $imageFiles->count() - 2 }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Butonlar --}}
                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200">
                                    <a href="{{ route('admin.sikayetler.show', $sikayet) }}" target="_blank"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition"
                                        onclick="event.stopPropagation()">
                                        Detay
                                    </a>
                                    @if($sikayet->iaaProjesi ?? null)
                                        <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi) }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md bg-purple-100 text-purple-700 hover:bg-purple-200 transition"
                                            onclick="event.stopPropagation()">
                                            Proje Alanı
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-sm text-gray-500">
                                Kayıt bulunamadı.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Sayfalama Linkleri --}}
                <div class="px-4 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $sikayetler->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>