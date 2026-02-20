@php
    $user = Auth::user();
    
    // YILLIK İSTATİSTİKLER
    $yillikIstatistikler = collect();
    if($user->customer) {
        $yillikIstatistikler = $user->customer->sikayetler()
            ->selectRaw('YEAR(created_at) as yil, count(*) as toplam')
            ->groupBy('yil')
            ->orderBy('yil', 'desc')
            ->get();
    }

    // FİRMA YETKİLİLERİ
    $digerYetkililer = collect();
    if($user->customer) {
        $digerYetkililer = $user->customer->representatives->where('id', '!=', $user->id);
    }
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6">

    {{-- 1. ÜST BİLGİ KARTI --}}
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4 relative overflow-hidden">
        {{-- Arkaplan Süsü --}}
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 blur-xl pointer-events-none"></div>

        <div class="flex items-center gap-4 relative z-10 w-full md:w-auto">
            {{-- LOGO ALANI --}}
            <div class="h-14 w-14 flex-shrink-0 bg-white rounded-lg border border-indigo-100 shadow-sm flex items-center justify-center overflow-hidden p-1 group transition-transform hover:scale-105">
                @if($user->customer && $user->customer->logo_path)
                    <img src="{{ asset('storage/' . $user->customer->logo_path) }}" 
                         alt="{{ $user->customer->name }}" 
                         class="h-full w-full object-contain">
                @else
                    <div class="h-full w-full bg-indigo-50 rounded-md flex items-center justify-center text-indigo-600 font-black text-xl">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
            </div>

            {{-- BİLGİ ALANI --}}
            <div>
                <h2 class="text-lg font-bold text-gray-900 leading-tight">Sn. {{ $user->name }}</h2>
                
                @if($user->customer)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 mt-0.5">
                        <span class="text-indigo-700 font-bold text-xs">{{ $user->customer->name }}</span>
                        <span class="hidden sm:inline text-gray-300 text-xs">|</span>
                        <span class="text-gray-500 text-[10px] font-medium bg-gray-100 px-1.5 py-0.5 rounded">Müşteri Paneli</span>
                    </div>
                @else
                    <p class="text-gray-500 text-xs mt-0.5">Firma Yetkilisi | Müşteri Paneli</p>
                @endif

                <div class="flex items-center mt-1 text-[10px] text-gray-400">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Son Giriş: {{ $user->last_seen_at ? \Carbon\Carbon::parse($user->last_seen_at)->format('d.m.Y H:i') : 'İlk Giriş' }}
                </div>
            </div>
        </div>

        {{-- BUTONLAR --}}
        <div class="flex gap-2 w-full md:w-auto relative z-10">
            @if($user->customer_id)
                <a href="{{ route('musteri.profil.show', $user->customer_id) }}" class="flex-1 md:flex-none justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm hover:shadow flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Firma Profili
                </a>
            @endif
            
            <a href="{{ route('admin.sikayetler.create', ['musteri_id' => $user->customer_id]) }}" 
               class="flex-1 md:flex-none justify-center px-4 py-2 bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 text-xs font-bold rounded-lg transition-all shadow-sm hover:shadow flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Yeni Şikayet
            </a>
        </div>
    </div>

    {{-- 2. İSTATİSTİKLER VE GRAFİK --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        {{-- Sol Taraf: Sayısal Veriler --}}
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
            
            {{-- Toplam Şikayet --}}
            <div class="bg-white p-3.5 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Toplam Kayıt</p>
                        <h3 class="text-2xl font-black text-gray-800 mt-0.5">{{ $stats['toplam_sikayet'] ?? 0 }}</h3>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Aktif Süreç --}}
            <div class="bg-white p-3.5 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1 bg-orange-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">İşlemdekiler</p>
                        <h3 class="text-2xl font-black text-gray-800 mt-0.5">{{ $stats['aktif_sikayet'] ?? 0 }}</h3>
                        <p class="text-[9px] text-orange-600 mt-0.5 font-bold bg-orange-50 inline-block px-1.5 py-0.5 rounded">Çözüm Bekleyen</p>
                    </div>
                    <div class="p-2 bg-orange-50 rounded-lg text-orange-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Çözülen --}}
            <div class="bg-white p-3.5 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1 bg-emerald-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Çözümlenen</p>
                        <h3 class="text-2xl font-black text-gray-800 mt-0.5">{{ $stats['cozulen_sikayet'] ?? 0 }}</h3>
                    </div>
                    <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Ortalama Hız --}}
            <div class="bg-white p-3.5 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1 bg-indigo-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ort. Çözüm Hızı</p>
                        <h3 class="text-2xl font-black text-gray-800 mt-0.5">{{ $stats['ortalama_sure'] ?? 0 }} <span class="text-xs font-normal text-gray-400">Gün</span></h3>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sağ Taraf: Grafik ve Yıllık Özet --}}
        <div class="flex flex-col gap-3">
            {{-- Grafik --}}
            <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center justify-center flex-1">
                <h4 class="text-[10px] font-bold text-gray-500 uppercase w-full text-left mb-2">Durum Dağılımı</h4>
                <div class="h-32 w-full flex justify-center relative">
                    <canvas id="sikayetChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-xs font-bold text-gray-400 opacity-50">Özet</span>
                    </div>
                </div>
            </div>

            {{-- YILLIK İSTATİSTİKLER --}}
            @if($yillikIstatistikler->isNotEmpty())
                <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
                    <h4 class="text-[10px] font-bold text-gray-500 uppercase mb-2 border-b border-gray-100 pb-1">Yıllık Kayıtlar</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($yillikIstatistikler as $stat)
                            <div class="flex items-center justify-between px-2 py-1.5 bg-gray-50 rounded-lg border border-gray-100 flex-1 min-w-[70px]">
                                <span class="text-[10px] font-bold text-gray-600">{{ $stat->yil }}</span>
                                <span class="text-xs font-black text-indigo-600">{{ $stat->toplam }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 3. FİRMA YETKİLİLERİ --}}
    @if($digerYetkililer->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800 text-sm">Firma Yetkilileri</h3>
                <p class="text-[10px] text-gray-500">Sizin dışınızda firmayı temsil eden yetkili kişiler.</p>
            </div>
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($digerYetkililer as $yetkili)
                    <div class="flex items-start gap-3 p-2.5 rounded-lg border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-sm hover:border-indigo-100 transition-all">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-sm font-bold flex-shrink-0">
                            {{ substr($yetkili->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="text-xs font-bold text-gray-900 truncate" title="{{ $yetkili->name }}">{{ $yetkili->name }}</h4>
                            <p class="text-[10px] text-gray-500 truncate">{{ $yetkili->unvan ?? 'Yetkili' }}</p>
                            <div class="mt-0.5 flex flex-col gap-0.5">
                                <a href="mailto:{{ $yetkili->email }}" class="text-[9px] text-indigo-600 hover:underline truncate">{{ $yetkili->email }}</a>
                                @if($yetkili->telefon)
                                    <span class="text-[9px] text-gray-400">{{ $yetkili->telefon }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 4. DETAYLI LİSTE TABLOSU --}}
    @if(isset($stats['son_sikayetler']) && $stats['son_sikayetler']->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Aktif Süreç Takibi</h3>
                    <p class="text-[10px] text-gray-500">Çözüm takımlarının üzerinde çalıştığı son kayıtlar.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Oluşturan</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konu & Kategori</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Sorumlu Takım</th>
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                            <th class="px-5 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tarihler</th>
                            <th class="px-5 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stats['son_sikayetler'] as $sikayet)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                
                                {{-- OLUŞTURAN (GÜNCELLENEN ALAN) --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @php
                                        // Yaratıcıyı belirle (user veya olusturanKurulUyesi)
                                        $creator = $sikayet->user ?? $sikayet->olusturanKurulUyesi;
                                        $creatorName = $creator ? $creator->name : 'Misafir';
                                        
                                        // Ünvan Belirleme Mantığı
                                        $creatorTitle = '';
                                        $creatorColorClass = 'text-gray-500'; // Varsayılan renk

                                        if($creator) {
                                            // 1. Durum: Firma Yetkilisi mi? (customer_id varsa)
                                            if(!empty($creator->customer_id)) {
                                                $creatorTitle = 'Firma Yetkilisi';
                                                $creatorColorClass = 'text-gray-500';
                                            } else {
                                                // 2. Durum: Personel/Kurul Üyesi (customer_id yoksa)
                                                // Rolünü çek, yoksa 'Personel' yaz
                                                $roleName = $creator->roles->isNotEmpty() ? $creator->roles->first()->name : 'Personel';
                                                $creatorTitle = $roleName;
                                                $creatorColorClass = 'text-indigo-400';
                                            }
                                        }
                                    @endphp
                                    
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-900">{{ $creatorName }}</span>
                                        <span class="text-[9px] {{ $creatorColorClass }}">{{ $creatorTitle }}</span>
                                    </div>
                                </td>

                                {{-- Konu --}}
                                <td class="px-5 py-3">
                                    <div class="text-xs font-bold text-gray-900 line-clamp-1" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                        {{ Str::limit($sikayet->musteri_sikayet_konusu, 40) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 mt-0.5">{{ $sikayet->sikayetKategori->ad ?? 'Genel' }}</div>
                                </td>

                                {{-- Sorumlu Takım --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($sikayet->cozumTakimi)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6 rounded bg-indigo-100 flex items-center justify-center text-indigo-700 text-[9px] font-bold border border-indigo-200">
                                                {{ substr($sikayet->cozumTakimi->ad, 0, 2) }}
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-xs font-medium text-gray-900">{{ $sikayet->cozumTakimi->ad }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                            Atanmadı
                                        </span>
                                    @endif
                                </td>

                                {{-- Durum --}}
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    @php
                                        $isCompleted = in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı', 'Tamamlandı']);
                                        $statusClass = match($sikayet->musteri_durum) {
                                            'Yeni' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'İşlemde', 'İnceleniyor', 'Atandı' => 'bg-orange-50 text-orange-700 border-orange-200',
                                            'Çözümlendi', 'Kapatıldı', 'Tamamlandı' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            default => 'bg-gray-50 text-gray-700 border-gray-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $statusClass }}">
                                        {{ $sikayet->musteri_durum }}
                                    </span>
                                </td>

                                {{-- Tarihler --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex flex-col gap-0.5">
                                        <div class="text-[10px] text-gray-500">
                                            <span class="font-bold">Kayıt:</span> {{ $sikayet->created_at->format('d.m.Y') }}
                                        </div>
                                        @if($isCompleted)
                                            <div class="text-[10px] text-emerald-600 font-bold">
                                                <span>Çözüm:</span> {{ $sikayet->updated_at->format('d.m.Y') }}
                                            </div>
                                        @else
                                            <div class="text-[9px] text-gray-400 italic">
                                                {{ $sikayet->created_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- İşlemler --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($sikayet->iaaProjesi)
                                            <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-lg transition-colors border border-indigo-200" title="Proje Alanı">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-3 py-1 rounded-lg transition-colors text-[10px] font-bold border border-gray-200">
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
        
        const bekleyen = {{ ($stats['toplam_sikayet'] ?? 0) - ($stats['aktif_sikayet'] ?? 0) - ($stats['cozulen_sikayet'] ?? 0) }};
        const islemde = {{ $stats['aktif_sikayet'] ?? 0 }};
        const cozulen = {{ $stats['cozulen_sikayet'] ?? 0 }};
        
        const data = (bekleyen + islemde + cozulen) === 0 ? [1] : [bekleyen, islemde, cozulen];
        const colors = (bekleyen + islemde + cozulen) === 0 ? ['#f3f4f6'] : ['#E5E7EB', '#F97316', '#10B981'];
        const labels = (bekleyen + islemde + cozulen) === 0 ? ['Veri Yok'] : ['Bekleyen', 'İşlemde', 'Çözülen'];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
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
                            boxWidth: 6,
                            font: { size: 10, family: "'Figtree', sans-serif" }
                        }
                    },
                    tooltip: {
                        enabled: (bekleyen + islemde + cozulen) > 0
                    }
                },
                cutout: '70%',
            }
        });
    });
</script>