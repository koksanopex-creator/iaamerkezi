@props(['progressUpdate', 'step'])

<div x-show="open" x-transition class="mt-4 border-t-2 border-gray-100 pt-4 space-y-6">
    
    {{-- Üst Bilgi Çubuğu (Tarih ve Buton) --}}
    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
        
        {{-- Sol Taraf: Tamamlanma Tarihi --}}
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">Tamamlanma:</span>
            <span class="font-bold text-gray-800">
                {{ $progressUpdate->completed_at ? \Carbon\Carbon::parse($progressUpdate->completed_at)->format('d.m.Y H:i') : '-' }}
            </span>
        </div>

        {{-- Sağ Taraf: Yeniden Düzenle Butonu (KİLİT KONTROLÜ İLE) --}}
        @php
            // Projenin durumunu kontrol et (Controller'da yaptığımız kilit mantığı)
            // Not: Bu partial içinde $iaa değişkeni direkt gelmeyebilir, $progressUpdate üzerinden erişiriz.
            // $iaa değişkeni show.blade.php'den gelebilir ama garanti olsun diye sorgulayalım:
            
            $iaaDurum = null;
            // Eğer üst katmandan $iaa geldiyse kullan, yoksa sorgula (Performans için üstten gelmesi iyidir)
            if(isset($iaa)) {
                $iaaDurum = $iaa->durum;
            } else {
                // Veritabanından bul (Maliyetli ama güvenli)
                $iaaDurum = DB::table('iaa_talepleri')
                    ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
                    ->where('iaa_talepleri.id', $progressUpdate->iaa_talep_id)
                    ->value('iaas.durum');
            }

            $kilitliDurumlar = ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlandı'];
            $isLocked = in_array($iaaDurum, $kilitliDurumlar);
        @endphp

        @if(!$isLocked)
            <form action="{{ route('proje.workspace.reopenStep', $progressUpdate) }}" method="POST" onsubmit="return confirm('Dikkat: Bu adımı yeniden açmak, onay sürecini sıfırlayabilir. Devam etmek istiyor musunuz?');">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Düzenle / Aç
                </button>
            </form>
        @else
            <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-200 rounded-md font-semibold text-xs text-gray-400 uppercase tracking-widest cursor-not-allowed" title="Proje onay aşamasında olduğu için düzenleme yapılamaz.">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Kilitli
            </span>
        @endif
    </div>

    {{-- JSON verisini ayrıştır --}}
    @php
        $reportData = $progressUpdate->content ? json_decode($progressUpdate->content, true) : null;
        $formData = $reportData['form_data'] ?? [];
        $toolsData = $reportData['tools'] ?? []; // five_whys, fishbone, pareto, bar_chart_data, line_chart_data içerir
    @endphp

    @if(!$reportData)
         <div class="text-sm text-red-600 italic">Bu adım için veri bulunamadı veya veri formatı bozuk.</div>
    @else
        {{-- === DİNAMİK WIDGET SONUÇ GÖSTERİMİ === --}}
        @foreach($step->widgets as $index => $widget)
            @php
                $widgetType = $widget['type'] ?? 'unknown';
                // Widget tanımından gelen config (sadece varsayılanlar için kullanılacak)
                $widgetConfigDefaults = $widget['config'] ?? []; 
                $widgetValue = $formData[$index] ?? null; // Form verisi
                 // Kaydedilmiş araç verisi (config + rows içerir)
                 $toolValue = null;
                 if ($widgetType === 'five_whys') $toolValue = $toolsData['five_whys'] ?? null;
                 elseif ($widgetType === 'fishbone') $toolValue = $toolsData['fishbone'] ?? null;
                 elseif ($widgetType === 'pareto') $toolValue = $toolsData['pareto'] ?? null;
                 elseif ($widgetType === 'bar_chart') $toolValue = $toolsData['bar_chart_data'][$index] ?? null;
                 elseif ($widgetType === 'line_chart') $toolValue = $toolsData['line_chart_data'][$index] ?? null;

            @endphp

            <div class="mb-6"> {{-- Her widget arasına boşluk --}}
                {{-- Info Text --}}
                @if($widgetType === 'info_text')
                    <div class="p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                         {{-- Başlığı widget tanımından al --}}
                         <h5 class="text-base font-semibold text-blue-800 mb-2">{{ $widgetConfigDefaults['title'] ?? 'Bilgilendirme' }}</h5>
                        <div class="mt-1 text-sm text-blue-700 prose prose-sm max-w-none">
                           {!! nl2br(e($widgetConfigDefaults['content'] ?? '')) !!}
                        </div>
                    </div>

                {{-- Normal Form Alanları (Grafikler ve Analiz Araçları Hariç) --}}
                 @elseif(!in_array($widgetType, ['five_whys', 'fishbone', 'pareto', 'bar_chart', 'line_chart']))
                     <div class="text-sm max-w-none">
                         {{-- Başlığı widget tanımından al --}}
                         <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace('_', ' ', $widgetType)) }}</h5>

                         @if($widgetType === 'textbox')
                         <p class="mt-1 text-gray-800 font-medium bg-gray-50 p-3 rounded-lg border border-gray-200">
                         {!! !empty($widgetValue['text']) ? nl2br(e($widgetValue['text'])) : '<span class="text-gray-400 italic">Girilmemiş</span>' !!}
                            </p>
                        @elseif($widgetType === 'user_select')
                            @php $user = isset($widgetValue['user_id']) ? \App\Models\User::find($widgetValue['user_id']) : null; @endphp
                            <p class="mt-1 text-gray-800 font-medium bg-gray-50 p-3 rounded-lg border border-gray-200">
                                {!! $user?->name ?? '<span class="text-gray-400 italic">Seçilmemiş</span>' !!}
                            </p>
                         @elseif($widgetType === 'date_picker')
                            <p class="mt-1 text-gray-800 font-medium bg-gray-50 p-3 rounded-lg border border-gray-200">
                                {!! isset($widgetValue['date']) && $widgetValue['date'] ? \Carbon\Carbon::parse($widgetValue['date'])->format('d.m.Y') : '<span class="text-gray-400 italic">Tarih Girilmemiş</span>' !!}
                            </p>
                            @elseif($widgetType === 'file_upload')
                                @if(!empty($widgetValue['files']) && is_array($widgetValue['files']))
                                    <div class="mt-1 flex flex-wrap gap-3">
                                        @foreach($widgetValue['files'] as $filePath)
                                            @php $isImage = Str::endsWith(strtolower($filePath), ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.webp']); @endphp
                                            @if($isImage)
                                                {{-- 🚨 DÜZELTME 1: Fancybox linki --}}
                                                <a href="{{ asset('storage/' . $filePath) }}" data-fancybox="gallery-{{$step->id}}-{{$index}}" data-caption="{{ basename($filePath) }}" class="block">
                                                    {{-- 🚨 DÜZELTME 2: Resim kaynağı --}}
                                                    <img src="{{ asset('storage/' . $filePath) }}" alt="{{ basename($filePath) }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                                </a>
                                            @else
                                                {{-- 🚨 DÜZELTME 3: Dosya linki --}}
                                                <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="flex items-center gap-2 text-blue-600 hover:underline bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm">
                                                    <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.122 2.122l7.81-7.81" /></svg>
                                                    <span>{{ basename($filePath) }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Dosya yüklenmemiş.</p>
                                @endif
                        @else
                             <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Veri gösterimi desteklenmiyor: {{ $widgetType }}</p>
                        @endif
                     </div>

                {{-- === ARAÇ GÖSTERİMİ === --}}
                {{-- Five Whys --}}
                @elseif($widgetType === 'five_whys' && !empty($toolValue) && count(array_filter($toolValue)) > 0)
                     {{-- Başlığı widget tanımından al --}}
                     <div class="text-sm max-w-none"> <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? '5 Neden Analizi Sonuçları' }}</h5> <dl class="border rounded-lg p-4 bg-indigo-50/50"> @foreach($toolValue as $key => $value) @if(!empty($value) && str_starts_with($key, 'why')) <dt class="font-bold text-gray-600">{{ str_replace('why', '', $key) }}. Neden?</dt> <dd class="ml-4 mb-2 text-gray-800 whitespace-pre-wrap">{{ $value }}</dd> @endif @endforeach </dl> </div>
                {{-- Fishbone --}}
                 @elseif($widgetType === 'fishbone' && !empty($toolValue) && count(array_filter(array_slice($toolValue, 1))) > 0)
                      {{-- Başlığı widget tanımından al --}}
                      <div class="text-sm max-w-none mt-4"> <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? 'Balık Kılçığı Analizi Sonuçları' }}</h5> <div class="border rounded-lg p-4 bg-gray-50"> <p class="mb-4"><span class="font-bold text-red-700">Problem:</span> {{ $toolValue['problem'] ?? '' }}</p> <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4"> @foreach(['insan', 'yontem', 'makine', 'malzeme', 'olcum', 'cevre'] as $key) @if(!empty($toolValue[$key])) <div> <dt class="font-bold text-gray-700 capitalize">{{ $key }}</dt> <dd class="ml-4 mt-1 text-gray-600 whitespace-pre-wrap">{{ $toolValue[$key] }}</dd> </div> @endif @endforeach </dl> </div> </div>
                {{-- Pareto --}}
                @elseif($widgetType === 'pareto' && !empty($toolValue) && !empty($toolValue['rows']) && count(array_filter(array_column($toolValue['rows'], 'problem'))) > 0 )
                      @php /* Pareto hesaplama */ $pareto = $toolValue; $rows = $pareto['rows'] ?? []; $processedData = collect($rows)->filter(fn($row) => !empty($row['problem']) && isset($row['frequency']) && is_numeric($row['frequency']) && $row['frequency'] > 0)->sortByDesc('frequency')->values(); $totalFrequency = $processedData->sum('frequency'); $cumulative = 0; $tableRows = $processedData->map(function ($item) use ($totalFrequency, &$cumulative) { $cumulative += (float)$item['frequency']; $item['cumulative_sum'] = $cumulative; $item['cumulative_percentage'] = $totalFrequency > 0 ? round(($cumulative / $totalFrequency) * 100, 2) : 0; return $item; }); $chartDataForJs = [ 'labels' => $tableRows->pluck('problem')->toArray(), 'frequencies' => $tableRows->pluck('frequency')->toArray(), 'percentages' => $tableRows->pluck('cumulative_percentage')->toArray(), 'header2' => $pareto['header2'] ?? 'Sıklık', ]; $header1 = $pareto['header1'] ?? 'Problem'; $header2 = $pareto['header2'] ?? 'Sıklık'; $chartId = "paretoChart-" . $progressUpdate->id . "-" . $index; @endphp 
                      {{-- Başlığı widget tanımından al --}}
                      <div class="text-sm max-w-none mt-4"> <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? 'Pareto Analizi Sonuçları' }}</h5> <div class="border rounded-lg p-2 bg-white mb-4" style="height: 300px;"> <canvas id="{{ $chartId }}"></canvas> </div> <div class="overflow-x-auto border rounded-lg"> <table class="min-w-full text-sm"> <thead class="bg-gray-100"><tr><th class="p-2 text-left font-bold">#</th> <th class="p-2 text-left font-bold">{{ $header1 }}</th> <th class="p-2 text-right font-bold">{{ $header2 }}</th> <th class="p-2 text-right font-bold">Toplam {{ $header2 }}</th> <th class="p-2 text-right font-bold">Kümülatif %</th></tr></thead> <tbody class="divide-y"> @foreach($tableRows as $row) <tr> <td class="p-2">{{ $loop->iteration }}</td> <td class="p-2">{{ $row['problem'] }}</td> <td class="p-2 text-right">{{ number_format($row['frequency'], 0) }}</td> <td class="p-2 text-right">{{ number_format($row['cumulative_sum'], 0) }}</td> <td class="p-2 text-right font-bold">{{ number_format($row['cumulative_percentage'], 2) }}%</td> </tr> @endforeach </tbody> </table> </div> </div> @push('scripts') <script> document.addEventListener('DOMContentLoaded', function () { const canvas = document.getElementById('{{ $chartId }}'); const chartData = @json($chartDataForJs); if (canvas && chartData && typeof Chart !== 'undefined') { new Chart(canvas.getContext('2d'), { type: 'bar', data: { labels: chartData.labels, datasets: [ { label: chartData.header2, data: chartData.frequencies, backgroundColor: 'rgba(59, 130, 246, 0.5)', borderColor: 'rgba(59, 130, 246, 1)', yAxisID: 'y', }, { label: 'Kümülatif %', data: chartData.percentages, type: 'line', borderColor: 'rgba(239, 68, 68, 1)', tension: 0.1, yAxisID: 'y1', } ] }, options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, scales: { y: { type: 'linear', display: true, position: 'left', beginAtZero: true, title: { display: true, text: chartData.header2 } }, y1: { type: 'linear', display: true, position: 'right', min: 0, max: 100, grid: { drawOnChartArea: false }, ticks: { callback: value => value + '%' }, title: { display: true, text: 'Kümülatif %' } } } } }); } }); </script> @endpush

                {{-- === YENİ GRAFİKLERİ GÖSTER === --}}
                {{-- Sütun Grafiği --}}
                @elseif($widgetType === 'bar_chart' && !empty($toolValue) && !empty($toolValue['rows']) && count(array_filter(array_column($toolValue['rows'], 'label'))) > 0 )
                     @php /* Sütun Grafiği verisini hesapla */ $rows = $toolValue['rows'] ?? []; $processedData = collect($rows)->filter(fn($row) => isset($row['label']) && $row['label'] !== '' && isset($row['value']) && is_numeric($row['value']))->values(); 
                         // DÜZELTME: Başlıkları $toolValue'dan al, yoksa widgetConfig'den
                         $chartDataForJs = [ 
                             'labels' => $processedData->pluck('label')->toArray(), 
                             'values' => $processedData->pluck('value')->toArray(), 
                             'title' => $toolValue['title'] ?? $widgetConfigDefaults['title'] ?? 'Sütun Grafiği', 
                             'axis_x' => $toolValue['axis_x_label'] ?? $widgetConfigDefaults['axis_x_label'] ?? 'Kategoriler', 
                             'axis_y' => $toolValue['axis_y_label'] ?? $widgetConfigDefaults['axis_y_label'] ?? 'Değerler', 
                         ]; 
                         $chartId = "barChart-" . $progressUpdate->id . "-" . $index; 
                     @endphp 
                     <div class="text-sm max-w-none mt-4"> 
                         <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $chartDataForJs['title'] }}</h5> 
                         <div class="border rounded-lg p-2 bg-white mb-4" style="height: 300px;"> <canvas id="{{ $chartId }}"></canvas> </div> 
                     </div> 
                     @push('scripts') <script> document.addEventListener('DOMContentLoaded', function () { const canvas = document.getElementById('{{ $chartId }}'); const chartData = @json($chartDataForJs); if (canvas && chartData && typeof Chart !== 'undefined') { new Chart(canvas.getContext('2d'), { type: 'bar', data: { labels: chartData.labels, datasets: [{ label: chartData.axis_y, data: chartData.values, backgroundColor: 'rgba(75, 192, 192, 0.5)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: chartData.title }, legend: { display: false } }, scales: { x: { title: { display: true, text: chartData.axis_x } }, y: { beginAtZero: true, title: { display: true, text: chartData.axis_y } } } } }); } }); </script> @endpush
                {{-- Çizgi Grafiği --}}
                @elseif($widgetType === 'line_chart' && !empty($toolValue) && !empty($toolValue['rows']) && count(array_filter(array_column($toolValue['rows'], 'label'))) > 0 )
                    @php /* Çizgi Grafiği verisini hesapla */ $rows = $toolValue['rows'] ?? []; $processedData = collect($rows)->filter(fn($row) => isset($row['label']) && $row['label'] !== '' && isset($row['value']) && is_numeric($row['value']))->values(); 
                        // DÜZELTME: Başlıkları $toolValue'dan al, yoksa widgetConfig'den
                        $chartDataForJs = [ 
                            'labels' => $processedData->pluck('label')->toArray(), 
                            'values' => $processedData->pluck('value')->toArray(), 
                            'title' => $toolValue['title'] ?? $widgetConfigDefaults['title'] ?? 'Çizgi Grafiği', 
                            'axis_x' => $toolValue['axis_x_label'] ?? $widgetConfigDefaults['axis_x_label'] ?? 'Kategoriler', 
                            'axis_y' => $toolValue['axis_y_label'] ?? $widgetConfigDefaults['axis_y_label'] ?? 'Değerler', 
                        ]; 
                        $chartId = "lineChart-" . $progressUpdate->id . "-" . $index; 
                    @endphp 
                    <div class="text-sm max-w-none mt-4"> 
                        <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $chartDataForJs['title'] }}</h5> 
                        <div class="border rounded-lg p-2 bg-white mb-4" style="height: 300px;"> <canvas id="{{ $chartId }}"></canvas> </div> 
                    </div> 
                    @push('scripts') <script> document.addEventListener('DOMContentLoaded', function () { const canvas = document.getElementById('{{ $chartId }}'); const chartData = @json($chartDataForJs); if (canvas && chartData && typeof Chart !== 'undefined') { new Chart(canvas.getContext('2d'), { type: 'line', data: { labels: chartData.labels, datasets: [{ label: chartData.axis_y, data: chartData.values, borderColor: 'rgba(75, 192, 192, 1)', tension: 0.1, fill: false }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: chartData.title }, legend: { display: false } }, scales: { x: { title: { display: true, text: chartData.axis_x } }, y: { beginAtZero: true, title: { display: true, text: chartData.axis_y } } } } }); } }); </script> @endpush
                {{-- Boş Araç/Grafik Gösterimi --}}
                 @elseif(in_array($widgetType, ['five_whys', 'fishbone', 'pareto', 'bar_chart', 'line_chart']))
                      <div class="text-sm max-w-none">
                         {{-- Başlığı widget tanımından al --}}
                         <h5 class="text-base font-semibold text-gray-800 mb-2">{{ $widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace(['_', 'chart', 'data'], [' ', '', ''], $widgetType)) }}</h5>
                         <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Bu araç için veri girilmemiş.</p>
                     </div>
                @endif
                {{-- === ARAÇ GÖSTERİMİ BİTİŞİ === --}}
            </div> {{-- Widget boşluk div'i --}}
        @endforeach
        {{-- === DİNAMİK GÖSTERİM BİTİŞİ === --}}
    @endif {{-- End if !$reportData --}}
</div>

{{-- Fancybox & Chart.js Scriptleri (Sadece bir kere yüklenir) --}}
@pushOnce('scripts')
    {{-- Fancybox --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
         function initFancybox() { Fancybox.bind("[data-fancybox]", { /* Custom options */ }); }
         document.addEventListener('DOMContentLoaded', initFancybox);
         // Livewire v3 uses 'navigate' event
         document.addEventListener('livewire:navigated', () => { if (typeof Fancybox !== 'undefined') { Fancybox.destroy(); } initFancybox(); });
    </script>
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script> {{-- Update Chart.js version --}}
@endpushOnce

