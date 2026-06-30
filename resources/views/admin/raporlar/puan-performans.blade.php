<x-app-layout>
    @push('pageTitle') Puan Sistemi Raporu - @endpush
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight tracking-tight">
                {{ __('Puan Sistemi Raporu') }}
            </h2>
            <div class="flex gap-2">
                @php
                    $thisWeekStart = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
                    $thisMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                    $thisYearStart = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
                @endphp
                <a href="{{ route('puan.raporu', ['start_date' => $thisWeekStart]) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('start_date') == $thisWeekStart ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-600 shadow-sm border border-gray-100 hover:bg-gray-50' }} transition">Haftalık</a>
                <a href="{{ route('puan.raporu', ['start_date' => $thisMonthStart]) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('start_date') == $thisMonthStart ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-600 shadow-sm border border-gray-100 hover:bg-gray-50' }} transition">Aylık</a>
                <a href="{{ route('puan.raporu', ['start_date' => $thisYearStart]) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('start_date') == $thisYearStart ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-600 shadow-sm border border-gray-100 hover:bg-gray-50' }} transition">Yıllık</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#FDFCF9] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- FİLTRE PANELİ -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 transition-all hover:shadow-md">
                <form action="{{ route('puan.raporu') }}" method="GET" class="flex flex-wrap items-end gap-6">
                    <div class="flex-1 min-w-[240px]">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Bölüm Filtresi</label>
                        <select name="bolum_id" onchange="this.form.submit()"
                                class="w-full bg-gray-50 border-none text-gray-700 text-sm font-bold rounded-2xl focus:ring-2 focus:ring-indigo-500 py-4 px-5 appearance-none shadow-inner transition-all">
                            <option value="">Tüm Bölümler</option>
                            @foreach($bolumler as $bolum)
                                <option value="{{ $bolum->id }}" {{ $selectedBolumId == $bolum->id ? 'selected' : '' }}>
                                    {{ $bolum->ad }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-auto">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Başlangıç</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                               class="bg-gray-50 border-none text-gray-700 text-sm font-bold rounded-2xl focus:ring-2 focus:ring-indigo-500 py-4 px-5 shadow-inner transition-all">
                    </div>

                    <div class="w-full sm:w-auto">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Bitiş</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                               class="bg-gray-50 border-none text-gray-700 text-sm font-bold rounded-2xl focus:ring-2 focus:ring-indigo-500 py-4 px-5 shadow-inner transition-all">
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl text-sm font-black hover:bg-indigo-700 transition shadow-xl shadow-indigo-100 flex items-center">
                            Filtrele
                        </button>
                        <a href="{{ route('puan.raporu') }}" class="bg-gray-100 text-gray-500 px-8 py-4 rounded-2xl text-sm font-black hover:bg-gray-200 transition">
                            Sıfırla
                        </a>
                    </div>
                </form>
            </div>

            <!-- ÜST KPI KARTLARI (MOKUP STİLİ) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Aktif Kullanıcı -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50 relative overflow-hidden group">
                    <div class="relative z-10">
                        <h4 class="text-gray-400 text-sm font-bold mb-2">Toplam aktif kullanıcı</h4>
                        <div class="text-4xl font-black text-gray-800 mb-4">{{ number_format($kpiStats['active_users']['value']) }}</div>
                        <div class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full inline-block">
                            Ort. {{ $kpiStats['active_users']['avg_net'] }} Net Puan
                        </div>
                    </div>
                </div>

                <!-- Kazanılan Puan -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50 relative overflow-hidden group">
                    <div class="relative z-10">
                        <h4 class="text-gray-400 text-sm font-bold mb-2">Kazanılan toplam puan</h4>
                        <div class="text-4xl font-black text-gray-800 mb-4">{{ number_format($kpiStats['gains']['value']) }}</div>
                        <div class="text-xs font-bold {{ $kpiStats['gains']['change'] >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-3 py-1.5 rounded-full inline-block">
                            {{ $kpiStats['gains']['change'] >= 0 ? '+' : '' }}{{ $kpiStats['gains']['change'] }}% önceki dönem
                        </div>
                    </div>
                </div>

                <!-- Kaybedilen Puan -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50 relative overflow-hidden group">
                    <div class="relative z-10">
                        <h4 class="text-gray-400 text-sm font-bold mb-2">Kaybedilen toplam puan</h4>
                        <div class="text-4xl font-black text-gray-800 mb-4">{{ number_format($kpiStats['losses']['value']) }}</div>
                        <div class="text-xs font-bold {{ $kpiStats['losses']['change'] <= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-3 py-1.5 rounded-full inline-block">
                            {{ $kpiStats['losses']['change'] >= 0 ? '+' : '' }}{{ $kpiStats['losses']['change'] }}% önceki dönem
                        </div>
                    </div>
                </div>

                <!-- Ortalama Net Puan -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50 relative overflow-hidden group">
                    <div class="relative z-10">
                        <h4 class="text-gray-400 text-sm font-bold mb-2">Toplam net puan</h4>
                        <div class="text-4xl font-black text-gray-800 mb-4">{{ number_format($kpiStats['net']['value']) }}</div>
                        <div class="text-xs font-bold {{ $kpiStats['net']['change'] >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-3 py-1.5 rounded-full inline-block">
                            {{ $kpiStats['net']['change'] >= 0 ? '+' : '' }}{{ $kpiStats['net']['change'] }}% önceki dönem
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAFİKLER (TREND & KAYNAKLAR) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="font-black text-lg text-gray-800">Puan Trend Analizi</h3>
                        <div class="flex gap-4">
                            <div class="flex items-center text-xs font-bold text-gray-400"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Kazanılan</div>
                            <div class="flex items-center text-xs font-bold text-gray-400"><span class="w-2 h-2 rounded-full bg-rose-500 mr-2"></span> Kesinti</div>
                        </div>
                    </div>
                    <div class="h-[350px]">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50 flex flex-col">
                    <h3 class="font-black text-lg text-gray-800 mb-8">Puan Kaynakları</h3>
                    <div class="flex-1 flex items-center justify-center relative">
                        <div class="h-[250px] w-full">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                    <div class="mt-8 grid grid-cols-1 gap-3">
                        @foreach($categoryStats['labels'] as $idx => $label)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $categoryStats['colors'][$idx] }}"></span>
                                <span class="text-xs font-bold text-gray-500">{{ $label }}</span>
                            </div>
                            <span class="text-xs font-black text-gray-800">{{ number_format($categoryStats['data'][$idx]) }} Puan</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- TAKIM SIRALAMASI VE BÖLÜM KARŞILAŞTIRMASI -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Takım Sıralaması -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50">
                    <h3 class="font-black text-lg text-gray-800 mb-6">Takım Sıralaması</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left border-b border-gray-50">
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest pl-2"># Takım</th>
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Net Puan</th>
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right pr-2">Değişim</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($teamStats as $idx => $team)
                                <tr class="group hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 pl-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-black text-gray-300 w-4">{{ $idx + 1 }}</span>
                                            <span class="text-sm font-bold text-gray-700">{{ $team->ad }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-right">
                                        <span class="text-sm font-black text-gray-800">{{ number_format($team->net_puan) }}</span>
                                    </td>
                                    <td class="py-4 text-right pr-2">
                                        <span class="text-[11px] font-black px-2 py-1 rounded-lg {{ $team->degisim >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }}">
                                            {{ $team->degisim >= 0 ? '+' : '' }}{{ $team->degisim }}%
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="py-8 text-center text-gray-400 font-bold">Takım verisi yok.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bölüm Karşılaştırması -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50">
                    <h3 class="font-black text-lg text-gray-800 mb-2">Bölüm Karşılaştırması</h3>
                    <p class="text-xs font-bold text-gray-400 mb-8">Tüm bölümlerin net puan dağılımı</p>
                    <div class="h-[350px]">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- EN BAŞARILI PERSONELLER (ALPINE İLE YÜKLE/GİZLE) -->
            <div x-data="{ expanded: false }" class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50 overflow-hidden">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="font-black text-lg text-gray-800">En Başarılı Personeller (Sıralama)</h3>
                    <span class="text-xs font-bold text-gray-400">İlk 50 Personel</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    @foreach($topPerformersArr as $idx => $tp)
                    <div x-show="expanded || {{ $idx < 10 ? 'true' : 'false' }}" 
                         x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-95"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="bg-gray-50 rounded-3xl p-6 border border-transparent hover:border-indigo-100 hover:bg-white hover:shadow-xl hover:shadow-indigo-50/50 transition-all duration-300 text-center relative group">
                        
                        <div class="absolute top-4 left-4 w-6 h-6 rounded-lg {{ $idx < 3 ? 'bg-amber-100 text-amber-600' : 'bg-white text-gray-300' }} flex items-center justify-center text-[10px] font-black">
                            #{{ $idx + 1 }}
                        </div>

                        <div class="relative inline-block mb-4">
                            <img src="{{ $tp->profile_photo_url }}" class="w-20 h-20 rounded-2xl object-cover mx-auto shadow-md transform group-hover:scale-110 transition-transform duration-500">
                            @if($idx < 3)
                            <div class="absolute -bottom-2 -right-2 bg-white rounded-lg shadow-sm p-1">
                                <span class="text-lg">@if($idx==0)🥇@elseif($idx==1)🥈@else🥉@endif</span>
                            </div>
                            @endif
                        </div>

                        <h5 class="text-sm font-black text-gray-800 truncate mb-1">{{ $tp->name }}</h5>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 truncate">{{ $tp->bolum->ad ?? '-' }}</p>
                        
                        <div class="bg-white rounded-2xl py-2 px-4 shadow-sm inline-block">
                            <span class="text-lg font-black text-indigo-600">{{ number_format($tp->period_puan) }}</span>
                            <span class="text-[10px] font-bold text-gray-400 ml-1">Puan</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-12 text-center">
                    <button @click="expanded = !expanded" 
                            class="bg-white border-2 border-indigo-600 text-indigo-600 px-10 py-4 rounded-2xl text-sm font-black hover:bg-indigo-600 hover:text-white transition-all shadow-lg shadow-indigo-50">
                        <span x-text="expanded ? 'Listeyi Daralt' : 'Devamını Göster (Daha Fazla Personel)'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fontSettings = { family: 'Outfit', weight: 'bold', size: 11 };

            // Trend Chart
            new Chart(document.getElementById('trendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendStats['labels']) !!},
                    datasets: {!! json_encode($trendStats['datasets']) !!}
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tension: 0.4,
                    plugins: { 
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1f2937', padding: 12, borderRadius: 16 }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { display: true, borderDash: [5, 5], color: '#f3f4f6' },
                            ticks: { font: fontSettings, color: '#9ca3af' }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { font: fontSettings, color: '#9ca3af' }
                        }
                    }
                }
            });

            // Category Chart
            new Chart(document.getElementById('categoryChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryStats['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($categoryStats['data']) !!},
                        backgroundColor: {!! json_encode($categoryStats['colors']) !!},
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1f2937', padding: 12, borderRadius: 16 }
                    }
                }
            });

            // Dept Chart (Horizontal Bar)
            new Chart(document.getElementById('deptChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($departmentStats, 'name')) !!},
                    datasets: [{
                        label: 'Net Puan',
                        data: {!! json_encode(array_column($departmentStats, 'puan')) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.4)',
                        borderColor: '#3b82f6',
                        borderWidth: 2,
                        borderRadius: 12,
                        barThickness: 24
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1f2937', padding: 12, borderRadius: 16 }
                    },
                    scales: {
                        x: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f3f4f6' }, ticks: { font: fontSettings, color: '#9ca3af' } },
                        y: { grid: { display: false }, ticks: { font: fontSettings, color: '#4b5563' } }
                    }
                }
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Outfit', sans-serif !important; }
        ::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
        }
    </style>
    @endpush
</x-app-layout>
