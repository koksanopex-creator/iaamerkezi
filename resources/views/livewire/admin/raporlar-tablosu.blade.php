<div>
    @push('styles')
    <style>
        /* EKRAN GÖRÜNÜMÜ */
        .chart-card { 
            background: #fff; 
            border-radius: 1rem; 
            padding: 1rem; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); 
            height: 100%; 
            border: 1px solid #e5e7eb; 
            min-height: 350px; 
        }

        /* YAZDIRMA (PRINT) ÖZEL AYARLARI */
        @media print {
            /* 1. SAYFAYI YATAY (LANDSCAPE) YAP VE KENAR BOŞLUKLARINI AYARLA */
            @page { 
                size: landscape; 
                margin: 10mm; 
            }

            /* 2. GENEL GÖVDE AYARLARI */
            body { 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
                font-family: sans-serif;
                background-color: white !important;
                font-size: 12px; /* Yazıları biraz küçült sığsın */
            }

            /* 3. GİZLENECEK ELEMANLAR */
            .no-print, button, input, select, nav, footer, header { 
                display: none !important; 
            }

            /* 4. SAYFA YAPISINI GENİŞLET */
            .max-w-7xl { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .py-12 { padding: 0 !important; }
            .mb-8 { margin-bottom: 20px !important; }

            /* 5. GRAFİK KARTLARI BÖLÜNMESİN */
            .chart-card {
                break-inside: avoid; /* Ortadan bölünmeyi engeller */
                page-break-inside: avoid;
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                margin-bottom: 20px !important;
            }

            /* 6. GRAFİK ALANI HEP AÇIK OLSUN */
            div[x-show="open"] { display: block !important; }

            /* 7. GRID YAPISINI KORU (Yan yana 2 grafik çıksın) */
            .grid {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important; /* Yan yana 2li zorla */
                gap: 20px !important;
            }
            /* Tek sütunluk (tam genişlik) alanlar için */
            .col-span-1, .md\:col-span-2 {
                grid-column: span 2 !important;
            }

            /* 8. TABLO AYARLARI */
            table { width: 100% !important; border-collapse: collapse !important; }
            th, td { border: 1px solid #ddd !important; padding: 8px !important; }
            
            /* Tablo başlığı diğer sayfaya geçerse tekrar etsin */
            thead { display: table-header-group; } 
            tfoot { display: table-footer-group; }
            
            /* Satırlar bölünmesin */
            tr { break-inside: avoid; page-break-inside: avoid; }

            /* 9. ÖZEL YAZDIRMA BAŞLIĞI */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
            }
            .print-logo { max-height: 60px; margin-bottom: 10px; }
            .print-title { font-size: 24px; font-weight: bold; color: #000; margin: 0; }
            .print-date { font-size: 12px; color: #555; margin-top: 5px; }

            /* 10. KAPSANMAMIŞ ALANLARI TOPARLA */
            .overflow-hidden { overflow: visible !important; }
            .overflow-x-auto { overflow-x: visible !important; }
        }
        
        /* Ekran modunda başlığı gizle */
        .print-header { display: none; }
    </style>
    @endpush

    {{-- YAZDIRMA BAŞLIĞI (Sadece kağıtta görünür) --}}
    <div class="print-header">
        <img src="{{ asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png') }}" class="print-logo" alt="Logo">
        <h1 class="print-title">Köksan İyileştirmeye Açık Alan (İAA) Sistemi</h1>
        <div class="print-date">Rapor Oluşturulma Tarihi: {{ now()->format('d.m.Y H:i') }}</div>
        @if($baslangicTarihi || $bitisTarihi)
            <div class="print-date">
                Kapsam: {{ $baslangicTarihi ? \Carbon\Carbon::parse($baslangicTarihi)->format('d.m.Y') : 'Başlangıç' }} - 
                        {{ $bitisTarihi ? \Carbon\Carbon::parse($bitisTarihi)->format('d.m.Y') : 'Bugün' }}
            </div>
        @endif
    </div>

    {{-- ÜST FİLTRE PANELİ --}}
    <div class="mb-8 space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-indigo-100 flex flex-col lg:flex-row items-center justify-between gap-4 no-print">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-indigo-100 text-indigo-600 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                <div><h3 class="text-lg font-bold text-gray-800">Rapor Filtreleme</h3><p class="text-sm text-gray-500">@if($baslangicTarihi) Seçili: {{ \Carbon\Carbon::parse($baslangicTarihi)->format('d.m.Y') }} - {{ $bitisTarihi ? \Carbon\Carbon::parse($bitisTarihi)->format('d.m.Y') : 'Bugün' }} @else Tüm zamanlar @endif</p></div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
                    <input wire:model.live="baslangicTarihi" type="date" class="border-0 bg-transparent text-gray-700 font-semibold focus:ring-0 rounded-lg text-sm">
                    <span class="text-gray-400 font-bold">&rarr;</span>
                    <input wire:model.live="bitisTarihi" type="date" class="border-0 bg-transparent text-gray-700 font-semibold focus:ring-0 rounded-lg text-sm">
                </div>
                <button wire:click="resetFilters" class="px-4 py-2 text-sm text-red-600 bg-red-50 hover:bg-red-100 rounded-lg font-bold transition">Temizle</button>
                <div class="flex gap-2 border-l pl-3 ml-1 border-gray-300">
                    <button onclick="window.print()" class="p-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm" title="Yazdır"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></button>
                    <button wire:click="downloadPdf" wire:loading.attr="disabled" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm" title="PDF"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></button>
                    <button wire:click="downloadExcel" wire:loading.attr="disabled" class="p-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm" title="Excel"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg></button>
                </div>
            </div>
        </div>

        {{-- KPI KARTLARI --}}
        <div class="space-y-6 break-inside-avoid">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
                <div class="bg-blue-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-blue-800 uppercase">Toplam Öneri</p><p class="text-2xl font-black text-blue-900">{{ $kpi['toplam_oneri'] ?? 0 }}</p></div>
                <div class="bg-yellow-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-yellow-800 uppercase">Onay Bekleyen</p><p class="text-2xl font-black text-yellow-900">{{ $kpi['onay_bekleyen_oneri'] ?? 0 }}</p></div>
                <div class="bg-gray-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-gray-800 uppercase">Havuzda</p><p class="text-2xl font-black text-gray-900">{{ $kpi['havuzdaki_oneri'] ?? 0 }}</p></div>
                <div class="bg-indigo-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-indigo-800 uppercase">Atanmış Proje</p><p class="text-2xl font-black text-indigo-900">{{ $kpi['atanmis_proje'] ?? 0 }}</p></div>
                <div class="bg-green-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-green-800 uppercase">Tamamlanan</p><p class="text-2xl font-black text-green-900">{{ $kpi['tamamlanan_proje'] ?? 0 }}</p></div>
                <div class="bg-red-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-red-800 uppercase">Reddedilen</p><p class="text-2xl font-black text-red-900">{{ $kpi['reddedilen_oneri'] ?? 0 }}</p></div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-teal-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-teal-800 uppercase">Kayıtlı Kullanıcı</p><p class="text-2xl font-black text-teal-900">{{ $kpi['kullanici_onerileri'] ?? 0 }}</p></div>
                <div class="bg-cyan-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-cyan-800 uppercase">Misafir</p><p class="text-2xl font-black text-cyan-900">{{ $kpi['misafir_onerileri'] ?? 0 }}</p></div>
                <div class="bg-purple-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-purple-800 uppercase">Toplam Takım</p><p class="text-2xl font-black text-purple-900">{{ $kpi['toplam_takim'] ?? 0 }}</p></div>
                <div class="bg-pink-100 p-4 rounded-xl shadow-sm"><p class="text-xs font-bold text-pink-800 uppercase">Sistem Kullanıcısı</p><p class="text-2xl font-black text-pink-900">{{ $kpi['toplam_kullanici'] ?? 0 }}</p></div>
            </div>
        </div>

        {{-- GRAFİKLER --}}
        <div x-data="{ open: true }" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden break-inside-avoid">
            <div @click="open = !open" class="p-4 bg-gray-50 cursor-pointer flex justify-between items-center hover:bg-gray-100 transition no-print">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2"><svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>Grafiksel Analiz</h3>
                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{'rotate-180': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>

            <div x-show="open" class="p-6 border-t border-gray-200 space-y-6">
                {{-- 1. SATIR: TREND --}}
                <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">Aylık Öneri Trendi</h4><div id="trendChart"></div></div>

                {{-- 2. SATIR: BÖLÜM VE DURUM --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">Bölüm Dağılımı</h4><div id="bolumChart"></div></div>
                    <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">Proje Durum Dağılımı</h4><div id="durumChart"></div></div>
                </div>

                {{-- 3. SATIR: BAŞARI VE TAKIM --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">Öneri Başarı Oranı</h4><div id="oranChart"></div></div>
                    <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">En Aktif Üyeler (İAA Takımları)</h4><div id="cokluUyelikChart"></div></div>
                </div>

                {{-- 4. SATIR: PUAN LİDERLİK --}}
                <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">Puan Liderlik Tablosu (Top 5)</h4><div id="liderlikChart"></div></div>

                {{-- 5. SATIR: EN İYİ PROJELER --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">En Yüksek Puanlı 5 Proje (Havuzda)</h4><div id="havuzPuanChart"></div></div>
                    <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">En Yüksek Puanlı 5 Proje (Tamamlanan)</h4><div id="tamamlananPuanChart"></div></div>
                </div>

                {{-- 6. SATIR: HIZ VE RİSK --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">En Kısa Sürede Biten 5 Proje</h4><div id="hizliProjeChart"></div></div>
                    <div class="chart-card" wire:ignore><h4 class="text-gray-800 font-bold mb-2">Proje Risk Analizi</h4><div id="riskChart"></div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLO --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden break-inside-avoid">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 no-print">
            <h3 class="text-xl font-bold text-gray-800">Proje Listesi</h3>
            <div class="flex gap-3 w-full md:w-auto">
                <select wire:model.live="durum" class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500"><option value="">Tüm Durumlar</option><option value="Onay Bekliyor">Onay Bekliyor</option><option value="Havuzda">Havuzda</option><option value="Atandı">Atandı</option><option value="Tamamlandı">Tamamlandı</option><option value="Reddedildi">Reddedildi</option></select>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Tabloda ara..." class="pl-4 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 w-full md:w-64">
            </div>
        </div>
        
        {{-- YAZDIRMA İÇİN GÖRÜNÜR TABLO BAŞLIĞI --}}
        <div class="print-header" style="border:none; margin:0; padding:10px; text-align:left; font-size:16px; font-weight:bold;">Proje Listesi</div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Başlık</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Bölüm</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Öneren</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Risk</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tarih</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase no-print">İşlem</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($iaas as $index => $iaa)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $startNumber + $index + 1 }}</td>
                            
                            <td class="px-6 py-4 text-sm text-gray-800 font-semibold">{{ Str::limit($iaa->baslik, 50) }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-bold">{{ $iaa->bolum->ad ?? 'Genel' }}</span>
                            </td>
                            
                            {{-- ÖNEREN (GÜNCELLENDİ: LİNK ÖZELLİĞİ) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($iaa->gonderen && $iaa->gonderen->is_personnel)
                                    {{-- NOT: Linki kendi kullanıcı profil rotanıza göre düzenleyin --}}
                                    <a href="{{ url('/admin/kullanicilar/' . $iaa->gonderen->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 hover:underline font-bold flex items-center gap-1">
                                        {{ $iaa->gonderen->name }}
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @else
                                    {{ $iaa->gonderen->name ?? $iaa->guest_name }}
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($iaa->risk)
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $this->getRiskBadgeClasses($iaa->risk) }}">
                                        {{ $this->getRiskLabel($iaa->risk) }}
                                    </span>
                                @else
                                    <span class="text-gray-300 font-bold">-</span>
                                @endif
                            </td>

                            {{-- DURUM VE PUAN (GÜNCELLENDİ: ATANDI İSE DE PUAN GÖSTER) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $this->getDurumBadgeClasses($iaa->durum) }}">
                                    {{ $iaa->durum }}
                                </span>
                                @if(in_array($iaa->durum, ['Havuzda', 'Atandı', 'Tamamlandı']) && $iaa->puan)
                                    <div class="text-xs text-yellow-600 font-bold mt-1">⭐ {{ number_format($iaa->puan, 0) }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $iaa->created_at->format('d.m.Y') }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium no-print">
                                <button wire:click="$dispatch('open-modal', 'detay-modal-{{ $iaa->id }}')" class="text-indigo-600 hover:text-indigo-900 font-bold">İncele</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                Kayıt bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 no-print">{{ $iaas->links() }}</div>
    </div>

    @foreach ($iaas as $iaa)
        <x-modal name="detay-modal-{{ $iaa->id }}" max-width="2xl"><div class="p-8 bg-white">@include('admin.raporlar.partials.iaa-detay-content', ['iaa' => $iaa])</div></x-modal>
    @endforeach

    <script>
        let trendChart, oranChart, cokluUyelikChart, liderlikChart, havuzPuanChart, tamamlananPuanChart, hizliProjeChart, bolumChart, durumChart, riskChart;
        document.addEventListener('livewire:initialized', () => {
            trendChart = new ApexCharts(document.querySelector("#trendChart"), {chart: { type: 'bar', height: 350, toolbar: { show: false }, animations: { enabled: false } }, series: [{ name: 'Öneri Sayısı', data: @json($chartData['trend']['data']) }], xaxis: { categories: @json($chartData['trend']['labels']) }, colors: ['#3b82f6'], noData: { text: 'Veri Yok' }}); trendChart.render();
            oranChart = new ApexCharts(document.querySelector("#oranChart"), {chart: { type: 'donut', height: 300, animations: { enabled: false } }, series: @json($chartData['oran']), labels: ['Tamamlanan', 'Diğer'], colors: ['#10b981', '#d1d5db'], legend: { position: 'bottom' }, noData: { text: 'Veri Yok' }}); oranChart.render();
            cokluUyelikChart = new ApexCharts(document.querySelector("#cokluUyelikChart"), {chart: { type: 'bar', height: 300, toolbar: { show: false }, animations: { enabled: false } }, series: [{ name: 'Takım Sayısı', data: @json($chartData['cokluUyelik']['data']) }], xaxis: { categories: @json($chartData['cokluUyelik']['labels']) }, plotOptions: { bar: { borderRadius: 4, horizontal: true } }, noData: { text: 'Veri Yok' }}); cokluUyelikChart.render();
            liderlikChart = new ApexCharts(document.querySelector("#liderlikChart"), {chart: { type: 'bar', height: 300, toolbar: { show: false }, animations: { enabled: false } }, series: [{ name: 'Puan', data: @json($chartData['puan']['data']) }], xaxis: { categories: @json($chartData['puan']['labels']) }, colors: ['#6366f1'], plotOptions: { bar: { borderRadius: 4, horizontal: true } }, noData: { text: 'Kayıt Yok' }}); liderlikChart.render();
            havuzPuanChart = new ApexCharts(document.querySelector("#havuzPuanChart"), {chart: { type: 'bar', height: 300, toolbar: { show: false }, animations: { enabled: false } }, series: [{ name: 'Puan', data: @json($chartData['havuzPuan']['data']) }], xaxis: { categories: @json($chartData['havuzPuan']['labels']) }, plotOptions: { bar: { borderRadius: 4, horizontal: true } }, noData: { text: 'Veri Yok' }}); havuzPuanChart.render();
            tamamlananPuanChart = new ApexCharts(document.querySelector("#tamamlananPuanChart"), {chart: { type: 'bar', height: 300, toolbar: { show: false }, animations: { enabled: false } }, series: [{ name: 'Puan', data: @json($chartData['tamamlananPuan']['data']) }], xaxis: { categories: @json($chartData['tamamlananPuan']['labels']) }, colors: ['#22c55e'], plotOptions: { bar: { borderRadius: 4, horizontal: true } }, noData: { text: 'Veri Yok' }}); tamamlananPuanChart.render();
            hizliProjeChart = new ApexCharts(document.querySelector("#hizliProjeChart"), {chart: { type: 'bar', height: 300, toolbar: { show: false }, animations: { enabled: false } }, series: [{ name: 'Gün', data: @json($chartData['hizliProje']['data']) }], xaxis: { categories: @json($chartData['hizliProje']['labels']) }, colors: ['#f59e0b'], plotOptions: { bar: { borderRadius: 4, horizontal: true } }, noData: { text: 'Veri Yok' }}); hizliProjeChart.render();
            bolumChart = new ApexCharts(document.querySelector("#bolumChart"), {chart: { type: 'bar', height: 300, toolbar: { show: false }, animations: { enabled: false } }, series: [{ name: 'Sayı', data: @json($chartData['bolum']['data']) }], xaxis: { categories: @json($chartData['bolum']['labels']) }, plotOptions: { bar: { borderRadius: 4, horizontal: false, columnWidth: '50%' } }, colors: ['#8b5cf6'], noData: { text: 'Veri Yok' }}); bolumChart.render();
            durumChart = new ApexCharts(document.querySelector("#durumChart"), {chart: { type: 'donut', height: 300, animations: { enabled: false } }, series: @json($chartData['durum']['data']), labels: @json($chartData['durum']['labels']), colors: @json($chartData['durum']['colors']), legend: { position: 'bottom' }, noData: { text: 'Veri Yok' }}); durumChart.render();
            riskChart = new ApexCharts(document.querySelector("#riskChart"), {chart: { type: 'bar', height: 300, toolbar: { show: false }, animations: { enabled: false } }, series: [{ name: 'Adet', data: @json($chartData['risk']['data']) }], xaxis: { categories: @json($chartData['risk']['labels']) }, plotOptions: { bar: { borderRadius: 4, horizontal: true, distributed: true } }, colors: @json($chartData['risk']['colors']), legend: { show: true, position: 'bottom' }, noData: { text: 'Risk Verisi Yok' }}); riskChart.render();

            Livewire.on('refreshCharts', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                if(trendChart) trendChart.updateOptions({ xaxis: { categories: payload.trend.labels }, series: [{ data: payload.trend.data }] });
                if(oranChart) oranChart.updateOptions({ series: payload.oran });
                if(cokluUyelikChart) cokluUyelikChart.updateOptions({ xaxis: { categories: payload.cokluUyelik.labels }, series: [{ data: payload.cokluUyelik.data }] });
                if(liderlikChart) liderlikChart.updateOptions({ xaxis: { categories: payload.puan.labels }, series: [{ data: payload.puan.data }] });
                if(havuzPuanChart) havuzPuanChart.updateOptions({ xaxis: { categories: payload.havuzPuan.labels }, series: [{ data: payload.havuzPuan.data }] });
                if(tamamlananPuanChart) tamamlananPuanChart.updateOptions({ xaxis: { categories: payload.tamamlananPuan.labels }, series: [{ data: payload.tamamlananPuan.data }] });
                if(hizliProjeChart) hizliProjeChart.updateOptions({ xaxis: { categories: payload.hizliProje.labels }, series: [{ data: payload.hizliProje.data }] });
                if(bolumChart) bolumChart.updateOptions({ xaxis: { categories: payload.bolum.labels }, series: [{ data: payload.bolum.data }] });
                if(durumChart) durumChart.updateOptions({ series: payload.durum.data, labels: payload.durum.labels, colors: payload.durum.colors });
                if(riskChart) riskChart.updateOptions({ xaxis: { categories: payload.risk.labels }, series: [{ data: payload.risk.data }], colors: payload.risk.colors });
            });
        });
    </script>
</div>