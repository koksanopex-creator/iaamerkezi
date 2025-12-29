<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-8">

    {{-- ÜST BİLGİ KARTI (HOŞGELDİNİZ) --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-700 font-bold text-2xl border border-indigo-100">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Sn. {{ Auth::user()->name }}</h2>
                <p class="text-gray-500 text-sm">{{ Auth::user()->customer->name ?? 'Firma Yetkilisi' }} | Müşteri Paneli</p>
                <div class="flex items-center mt-1 text-xs text-gray-400">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Son Giriş: {{ Auth::user()->last_seen_at ? \Carbon\Carbon::parse(Auth::user()->last_seen_at)->format('d.m.Y H:i') : 'İlk Giriş' }}
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('musteri.profil.show', Auth::user()->customer_id) }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Firma Profili
            </a>
            <a href="{{ route('public.sikayet.create') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-xl transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Yeni Şikayet
            </a>
        </div>
    </div>

    {{-- İSTATİSTİKLER VE GRAFİK --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Sol Taraf: Sayısal Veriler (2 Kolon Genişliğinde) --}}
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
            
            {{-- Toplam Şikayet --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-2 bg-blue-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Toplam Kayıt</p>
                        <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $stats['toplam_sikayet'] ?? 0 }}</h3>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Aktif Süreç --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-2 bg-orange-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">İşlemdekiler</p>
                        <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $stats['aktif_sikayet'] ?? 0 }}</h3>
                        <p class="text-xs text-orange-600 mt-1 font-medium">Çözüm bekleyen süreçler</p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-xl text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Çözülen --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-2 bg-emerald-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Çözümlenen</p>
                        <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $stats['cozulen_sikayet'] ?? 0 }}</h3>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Ortalama Hız --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-2 bg-indigo-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Ort. Çözüm Hızı</p>
                        <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $stats['ortalama_sure'] ?? 0 }} <span class="text-sm font-normal text-gray-400">Gün</span></h3>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sağ Taraf: Grafik --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col items-center justify-center">
            <h4 class="text-sm font-bold text-gray-600 mb-4 w-full text-left">Durum Dağılımı</h4>
            <div class="h-48 w-full flex justify-center">
                <canvas id="sikayetChart"></canvas>
            </div>
        </div>
    </div>

    {{-- DETAYLI LİSTE TABLOSU --}}
    @if(isset($stats['son_sikayetler']) && $stats['son_sikayetler']->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Aktif Süreç Takibi</h3>
                    <p class="text-xs text-gray-500">Çözüm takımlarının üzerinde çalıştığı son kayıtlar.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Konu & Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sorumlu Takım & Lider</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Kayıt Tarihi</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stats['son_sikayetler'] as $sikayet)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                
                                {{-- Konu --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $sikayet->musteri_sikayet_konusu }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $sikayet->sikayetKategori->ad ?? 'Genel' }}</div>
                                </td>

                                {{-- Sorumlu Takım --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sikayet->cozumTakimi)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-bold uppercase border border-indigo-200">
                                                {{ substr($sikayet->cozumTakimi->ad, 0, 2) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $sikayet->cozumTakimi->ad }}</div>
                                                
                                                {{-- Lider Bilgisi (Tooltip ile Detay) --}}
                                                @if($sikayet->cozumTakimi->lider)
                                                    <div class="text-xs text-gray-500 group relative inline-block cursor-help">
                                                        <span class="border-b border-dotted border-gray-400">Lider: {{ $sikayet->cozumTakimi->lider->name }}</span>
                                                        {{-- Tooltip --}}
                                                        <div class="invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-opacity absolute bottom-full left-0 mb-2 w-48 bg-gray-800 text-white text-xs rounded p-2 z-10 shadow-lg">
                                                            <p class="font-bold mb-1">İletişim Bilgileri:</p>
                                                            <p>Tel: {{ $sikayet->cozumTakimi->lider->telefon ?? 'Yok' }}</p>
                                                            <p>Email: {{ $sikayet->cozumTakimi->lider->email }}</p>
                                                            <svg class="absolute text-gray-800 h-2 w-full left-0 top-full" x="0px" y="0px" viewBox="0 0 255 255" xml:space="preserve"><polygon class="fill-current" points="0,0 127.5,127.5 255,0"/></svg>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-xs text-red-400">Lider Atanmamış</div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Atama Bekliyor
                                        </span>
                                    @endif
                                </td>

                                {{-- Durum --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $statusClass = match($sikayet->musteri_durum) {
                                            'Yeni' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'İşlemde', 'İnceleniyor', 'Atandı' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'Çözümlendi', 'Kapatıldı' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            default => 'bg-gray-100 text-gray-800 border-gray-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                                        {{ $sikayet->musteri_durum }}
                                    </span>
                                </td>

                                {{-- Tarih --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    {{ $sikayet->created_at->format('d.m.Y') }}
                                    <div class="text-xs text-gray-400">{{ $sikayet->created_at->diffForHumans() }}</div>
                                </td>

                                {{-- İşlemler --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3">
                                        @if($sikayet->iaaProjesi)
                                            <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold border border-indigo-200">
                                                Proje Alanı
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold border border-gray-200">
                                            Detay
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- GRAFİK SCRİPTİ --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('sikayetChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Bekleyen', 'İşlemde', 'Çözülen'],
                datasets: [{
                    data: [
                        {{ ($stats['toplam_sikayet'] ?? 0) - ($stats['aktif_sikayet'] ?? 0) - ($stats['cozulen_sikayet'] ?? 0) }}, // Yeni/Bekleyen (Tahmini)
                        {{ $stats['aktif_sikayet'] ?? 0 }},
                        {{ $stats['cozulen_sikayet'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#E5E7EB', // Gri (Bekleyen)
                        '#F97316', // Turuncu (İşlemde)
                        '#10B981'  // Yeşil (Çözülen)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            font: { size: 12 }
                        }
                    }
                },
                cutout: '75%', // İnce halka görünümü
            }
        });
    });
</script>