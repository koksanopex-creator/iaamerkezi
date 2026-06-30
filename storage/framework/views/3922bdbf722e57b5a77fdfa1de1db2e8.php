<?php $__env->startPush('pageTitle'); ?>Disiplin Kurulu Raporu |<?php $__env->stopPush(); ?>
<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
 <?php $__env->slot('header', null, []); ?> 
<div class="flex justify-between items-center flex-wrap gap-3">
    <div>
        <h2 class="font-black text-xl text-gray-800 flex items-center gap-2">
            <span class="p-2 bg-indigo-100 rounded-lg text-indigo-600">📊</span>
            Disiplin Kurulu Raporu
        </h2>
        <p class="text-sm text-gray-500 mt-0.5"><?php echo e($reportMeta['date_range']); ?> — <?php echo e($reportMeta['generated_at']); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="exportExcel()" class="bg-emerald-600 text-white font-bold py-2 px-4 rounded-xl shadow-sm hover:bg-emerald-700 flex items-center gap-2 text-sm no-print">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Excel
        </button>
        <button onclick="exportPDF()" class="bg-rose-600 text-white font-bold py-2 px-4 rounded-xl shadow-sm hover:bg-rose-700 flex items-center gap-2 text-sm no-print">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            PDF
        </button>
        <button onclick="window.print()" class="bg-white border border-gray-200 text-gray-700 font-bold py-2 px-4 rounded-xl shadow-sm hover:bg-gray-50 flex items-center gap-2 text-sm no-print">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Yazdır
        </button>
    </div>
</div>
 <?php $__env->endSlot(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap">
<style>
body { font-family: 'Inter', sans-serif; }
.stat-card { background:#fff; border-radius:16px; padding:20px 24px; border:1px solid #e5e7eb; box-shadow:0 1px 4px rgba(0,0,0,.05); transition:.2s; }
.stat-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); transform:translateY(-2px); }
.chart-card { background:#fff; border-radius:16px; padding:20px; border:1px solid #e5e7eb; box-shadow:0 1px 4px rgba(0,0,0,.05); }
.section-title { font-size:13px; font-weight:800; color:#6366f1; text-transform:uppercase; letter-spacing:.08em; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
.filter-badge { display:inline-flex; align-items:center; gap:6px; background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe; border-radius:999px; padding:4px 12px; font-size:11px; font-weight:700; }
.filter-badge .lbl { color:#6b7280; font-weight:600; }
@media print {
    .no-print { display:none !important; }
    .print-header { display:block !important; }
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .stat-card, .chart-card { break-inside:avoid; box-shadow:none; border:1px solid #e5e7eb; }
    nav, header.sticky, .sidebar { display:none !important; }
}
.print-header { display:none; }
</style>
<?php $__env->stopPush(); ?>

<div class="py-6">
<div class="max-w-[98%] mx-auto sm:px-6 lg:px-8 space-y-6">


<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <form method="GET" action="<?php echo e(route('admin.disiplin.report')); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Başlangıç</label>
            <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Bitiş</label>
            <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Bölüm</label>
            <select name="bolum_id" class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tüm Bölümler</option>
                <?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($b->id); ?>" <?php echo e(request('bolum_id')==$b->id?'selected':''); ?>><?php echo e($b->ad); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Personel</label>
            <select name="user_id" class="w-full py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tüm Personel</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>" <?php echo e(request('user_id')==$u->id?'selected':''); ?>><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-lg font-bold text-sm hover:bg-indigo-700">Filtrele</button>
            <a href="<?php echo e(route('admin.disiplin.report')); ?>" class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-lg font-bold text-sm hover:bg-gray-200 text-center">Sıfırla</a>
        </div>
    </form>
</div>


<?php if(count($activeFilters) > 0): ?>
<div class="flex flex-wrap items-center gap-2 px-1 no-print">
    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aktif Filtreler:</span>
    <?php $__currentLoopData = $activeFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <span class="filter-badge">
        <span class="lbl"><?php echo e($f['type']); ?>:</span>
        <?php echo e($f['value']); ?>

        <a href="<?php echo e(request()->fullUrlWithoutQuery(in_array($f['type'], ['Başlangıç']) ? ['start_date'] : (in_array($f['type'], ['Bitiş']) ? ['end_date'] : (in_array($f['type'], ['Bölüm']) ? ['bolum_id'] : ['user_id'])))); ?>" class="ml-1 text-indigo-400 hover:text-rose-600 font-black">×</a>
    </span>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('admin.disiplin.report')); ?>" class="text-xs text-rose-500 hover:text-rose-700 font-bold underline ml-1">Tümünü Temizle</a>
</div>
<?php endif; ?>


<div class="print-header mb-6 border-b-2 border-indigo-600 pb-4">
    <div style="display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:16px;">
            <img src="<?php echo e(asset('logo.svg')); ?>" alt="Logo" style="height:48px;" onerror="this.style.display='none'">
            <div>
                <div style="font-size:20px;font-weight:900;color:#1e293b;">DİSİPLİN KURULU RAPORU</div>
                <div style="font-size:12px;color:#6366f1;font-weight:700;"><?php echo e($reportMeta['date_range']); ?></div>
            </div>
        </div>
        <div style="text-align:right;font-size:11px;color:#64748b;">
            <div><strong>Raporu Alan:</strong> <?php echo e($reportMeta['generated_by']); ?></div>
            <div><strong>Tarih:</strong> <?php echo e($reportMeta['generated_at']); ?></div>
            <?php if(count($activeFilters) > 0): ?>
            <div style="margin-top:4px;">
                <strong>Filtreler:</strong>
                <?php $__currentLoopData = $activeFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($f['type']); ?>: <?php echo e($f['value']); ?><?php echo e(!$loop->last ? ' | ' : ''); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
    <?php
    $cards = [
        ['label'=>'Toplam Dosya','value'=>$totalCases,'color'=>'indigo','icon'=>'📁'],
        ['label'=>'Ceza Verilen','value'=>$cezaVerilenler,'color'=>'rose','icon'=>'⚠️'],
        ['label'=>'Ceza Yok','value'=>$cezaVerilmeyenler,'color'=>'emerald','icon'=>'✅'],
        ['label'=>'Kurulda','value'=>$kuruldakiler,'color'=>'violet','icon'=>'⚖️'],
        ['label'=>'Savunma Bekleyen','value'=>$savunmaBekleyenler,'color'=>'amber','icon'=>'📝'],
        ['label'=>'Yön. Değerlendirmede','value'=>$yoneticiDegerlendirmesinde,'color'=>'sky','icon'=>'👔'],
        ['label'=>'Ertelenen','value'=>$ertelenenDosyalar,'color'=>'orange','icon'=>'🔄'],
        ['label'=>'Toplam Toplantı','value'=>$toplamToplanti,'color'=>'teal','icon'=>'🗓️'],
    ];
    $colorMap=['indigo'=>'#6366f1','rose'=>'#f43f5e','emerald'=>'#10b981','violet'=>'#8b5cf6','amber'=>'#f59e0b','sky'=>'#0ea5e9','orange'=>'#f97316','teal'=>'#14b8a6'];
    ?>
    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="stat-card text-center" style="border-top:3px solid <?php echo e($colorMap[$card['color']]); ?>">
        <div class="text-2xl mb-1"><?php echo e($card['icon']); ?></div>
        <div class="text-2xl font-black" style="color:<?php echo e($colorMap[$card['color']]); ?>"><?php echo e($card['value']); ?></div>
        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mt-1"><?php echo e($card['label']); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 chart-card">
        <div class="section-title">📊 Bölüm Bazlı Dosya Sayısı</div>
        <div id="chartBolum" style="min-height:280px"></div>
    </div>
    <div class="chart-card">
        <div class="section-title">🔵 Durum Dağılımı</div>
        <div id="chartDurum" style="min-height:280px"></div>
    </div>
</div>


<div class="chart-card">
    <div class="section-title">📈 Disiplin Dosyaları Trend Grafiği (Aylık)</div>
    <div id="chartTrend" style="min-height:250px"></div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="chart-card">
        <div class="section-title">⚖️ Karar Karşılaştırması</div>
        <div id="chartKarsilastirma" style="min-height:260px"></div>
    </div>
    <div class="chart-card">
        <div class="section-title">🏛️ Ceza Puan Skalası Dağılımı</div>
        <div id="chartSkala" style="min-height:260px"></div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="chart-card">
        <div class="section-title">💥 Suçun Şiddeti (Etkisi)</div>
        <div id="chartImpact" style="min-height:260px"></div>
    </div>
    <div class="chart-card">
        <div class="section-title">🌐 Etki Kapsamı</div>
        <div id="chartScope" style="min-height:260px"></div>
    </div>
</div>


<div class="chart-card">
    <div class="section-title">📋 En Çok Seçilen İhlal Maddeleri</div>
    <div id="chartBehavior" style="min-height:300px"></div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="chart-card">
        <div class="section-title">👤 En Çok Dosyası Olan Personeller</div>
        <?php if($topPersoneller->isEmpty()): ?>
            <p class="text-gray-400 text-sm text-center py-8">Veri bulunamadı.</p>
        <?php else: ?>
        <?php $maxP = $topPersoneller->first()['count'] ?? 1; $colors=['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f97316','#f59e0b','#10b981','#14b8a6','#0ea5e9','#3b82f6']; ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $topPersoneller; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0" style="background:<?php echo e($colors[$i] ?? '#6366f1'); ?>"><?php echo e($i+1); ?></div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-bold text-gray-700 truncate"><?php echo e($p['user']->name ?? '-'); ?></span>
                        <span class="text-sm font-black ml-2" style="color:<?php echo e($colors[$i] ?? '#6366f1'); ?>"><?php echo e($p['count']); ?></span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all" style="width:<?php echo e($maxP>0?round($p['count']/$maxP*100):0); ?>%;background:<?php echo e($colors[$i] ?? '#6366f1'); ?>"></div>
                    </div>
                    <div class="text-[10px] text-gray-400 mt-0.5"><?php echo e($p['bolum']); ?></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="chart-card">
        <div class="section-title">✍️ En Çok Tutanak Tutan Personeller</div>
        <?php if($topReporters->isEmpty()): ?>
            <p class="text-gray-400 text-sm text-center py-8">Veri bulunamadı.</p>
        <?php else: ?>
        <?php $maxR = $topReporters->first()['count'] ?? 1; ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $topReporters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0" style="background:<?php echo e($colors[$i] ?? '#10b981'); ?>"><?php echo e($i+1); ?></div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-bold text-gray-700 truncate"><?php echo e($r['user']->name ?? '-'); ?></span>
                        <span class="text-sm font-black ml-2" style="color:<?php echo e($colors[$i] ?? '#10b981'); ?>"><?php echo e($r['count']); ?></span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all" style="width:<?php echo e($maxR>0?round($r['count']/$maxR*100):0); ?>%;background:<?php echo e($colors[$i] ?? '#10b981'); ?>"></div>
                    </div>
                    <div class="text-[10px] text-gray-400 mt-0.5"><?php echo e($r['bolum']); ?></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</div>


<?php if($tekrarliDosyalar->isNotEmpty()): ?>
<div class="chart-card">
    <div class="section-title">🔁 Tekrarlı Dosyalar (Aynı Kişi + Aynı İhlal)</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left py-2 px-3 text-xs font-black text-gray-400 uppercase">Personel</th>
                    <th class="text-left py-2 px-3 text-xs font-black text-gray-400 uppercase">Bölüm</th>
                    <th class="text-left py-2 px-3 text-xs font-black text-gray-400 uppercase">İhlal Maddesi</th>
                    <th class="text-center py-2 px-3 text-xs font-black text-gray-400 uppercase">Dosya Sayısı</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tekrarliDosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b border-gray-50 hover:bg-rose-50 transition">
                    <td class="py-2 px-3 font-bold text-gray-800"><?php echo e($t['user']->name ?? '-'); ?></td>
                    <td class="py-2 px-3 text-gray-500 text-xs"><?php echo e($t['bolum']); ?></td>
                    <td class="py-2 px-3 text-gray-600 text-xs max-w-xs truncate"><?php echo e($t['behavior']); ?></td>
                    <td class="py-2 px-3 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-black text-sm"><?php echo e($t['count']); ?></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

</div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
<script>
const baseOpts = {
    chart:{toolbar:{show:false},fontFamily:'Inter,sans-serif',animations:{enabled:true,speed:600}},
    grid:{borderColor:'#f1f5f9',strokeDashArray:4},
    tooltip:{theme:'light'},
    dataLabels:{enabled:false}
};

// 1. BÖLÜM BAR
<?php
$bolumLabels = $bolumBazli->pluck('label')->toJson();
$bolumData   = $bolumBazli->pluck('count')->toJson();
?>
new ApexCharts(document.querySelector('#chartBolum'), {
    ...baseOpts,
    chart:{...baseOpts.chart,type:'bar',height:280},
    series:[{name:'Dosya Sayısı',data:<?php echo $bolumData; ?>}],
    xaxis:{categories:<?php echo $bolumLabels; ?>,labels:{style:{fontSize:'11px'}}},
    colors:['#6366f1'],
    plotOptions:{bar:{borderRadius:6,columnWidth:'55%'}},
    yaxis:{labels:{formatter:v=>Math.round(v)}}
}).render();

// 2. DURUM DONUT
<?php
$durumLabels = $durumDagilimi->pluck('durum')->toJson();
$durumData   = $durumDagilimi->pluck('count')->toJson();
?>
new ApexCharts(document.querySelector('#chartDurum'), {
    ...baseOpts,
    chart:{...baseOpts.chart,type:'donut',height:280},
    series:<?php echo $durumData; ?>,
    labels:<?php echo $durumLabels; ?>,
    colors:['#6366f1','#f43f5e','#f59e0b','#10b981','#8b5cf6','#0ea5e9'],
    legend:{position:'bottom',fontSize:'11px'},
    plotOptions:{pie:{donut:{size:'60%'}}}
}).render();

// 3. TREND AREA
<?php
$trendLabels = $trendData->pluck('ay')->toJson();
$trendCounts = $trendData->pluck('count')->toJson();
?>
new ApexCharts(document.querySelector('#chartTrend'), {
    ...baseOpts,
    chart:{...baseOpts.chart,type:'area',height:250},
    series:[{name:'Açılan Dosya',data:<?php echo $trendCounts; ?>}],
    xaxis:{categories:<?php echo $trendLabels; ?>,labels:{style:{fontSize:'11px'}}},
    colors:['#6366f1'],
    fill:{type:'gradient',gradient:{shadeIntensity:1,opacityFrom:.4,opacityTo:.05}},
    stroke:{curve:'smooth',width:2},
    yaxis:{labels:{formatter:v=>Math.round(v)}}
}).render();

// 4. KARAR KARŞILAŞTIRMA
<?php
$kkLabels = collect($kararKarsilastirma)->pluck('label')->toJson();
$kkData   = collect($kararKarsilastirma)->pluck('count')->toJson();
?>
new ApexCharts(document.querySelector('#chartKarsilastirma'), {
    ...baseOpts,
    chart:{...baseOpts.chart,type:'bar',height:260},
    series:[{name:'Dosya Sayısı',data:<?php echo $kkData; ?>}],
    xaxis:{categories:<?php echo $kkLabels; ?>,labels:{style:{fontSize:'12px',fontWeight:700}}},
    colors:['#f43f5e','#10b981','#f59e0b'],
    plotOptions:{bar:{borderRadius:8,columnWidth:'40%',distributed:true}},
    legend:{show:false},
    dataLabels:{enabled:true,style:{fontSize:'13px',fontWeight:900}},
    yaxis:{labels:{formatter:v=>Math.round(v)}}
}).render();

// 5. CEZA SKALASI
<?php
$skalaLabels = collect($skalaBazli)->where('count','>',0)->pluck('ceza_adi')->values()->toJson();
$skalaData   = collect($skalaBazli)->where('count','>',0)->pluck('count')->values()->toJson();
?>
new ApexCharts(document.querySelector('#chartSkala'), {
    ...baseOpts,
    chart:{...baseOpts.chart,type:'bar',height:260},
    series:[{name:'Dosya',data:<?php echo $skalaData; ?>}],
    xaxis:{categories:<?php echo $skalaLabels; ?>,labels:{style:{fontSize:'11px'}}},
    colors:['#8b5cf6'],
    plotOptions:{bar:{borderRadius:6,columnWidth:'50%',horizontal:false}},
    yaxis:{labels:{formatter:v=>Math.round(v)}}
}).render();

// 6. IMPACT (SUÇUN ŞİDDETİ)
<?php
$impactLabels = $impactStats->pluck('tanim')->toJson();
$impactData   = $impactStats->pluck('count')->toJson();
?>
new ApexCharts(document.querySelector('#chartImpact'), {
    ...baseOpts,
    chart:{...baseOpts.chart,type:'donut',height:260},
    series:<?php echo $impactData; ?>,
    labels:<?php echo $impactLabels; ?>,
    colors:['#f43f5e','#f97316','#f59e0b','#10b981','#6366f1'],
    legend:{position:'bottom',fontSize:'11px'},
    plotOptions:{pie:{donut:{size:'55%'}}}
}).render();

// 7. SCOPE (ETKİ KAPSAMI)
<?php
$scopeLabels = $scopeStats->pluck('tanim')->toJson();
$scopeData   = $scopeStats->pluck('count')->toJson();
?>
new ApexCharts(document.querySelector('#chartScope'), {
    ...baseOpts,
    chart:{...baseOpts.chart,type:'donut',height:260},
    series:<?php echo $scopeData; ?>,
    labels:<?php echo $scopeLabels; ?>,
    colors:['#0ea5e9','#8b5cf6','#10b981','#f59e0b','#f43f5e'],
    legend:{position:'bottom',fontSize:'11px'},
    plotOptions:{pie:{donut:{size:'55%'}}}
}).render();

// 8. İHLAL MADDELERİ
<?php
$bLabels = $behaviorStats->take(15)->pluck('tanim')->toJson();
$bData   = $behaviorStats->take(15)->pluck('count')->toJson();
?>
new ApexCharts(document.querySelector('#chartBehavior'), {
    ...baseOpts,
    chart:{...baseOpts.chart,type:'bar',height:Math.max(300, <?php echo e($behaviorStats->take(15)->count()); ?> * 36)},
    series:[{name:'Dosya',data:<?php echo $bData; ?>}],
    xaxis:{categories:<?php echo $bLabels; ?>,labels:{style:{fontSize:'11px'},maxWidth:280}},
    colors:['#6366f1'],
    plotOptions:{bar:{borderRadius:5,horizontal:true,barHeight:'60%'}},
    yaxis:{labels:{style:{fontSize:'11px'}}},
    dataLabels:{enabled:true,style:{fontSize:'11px',fontWeight:700}}
}).render();

// EXCEL EXPORT
<?php
$excelRows = [
    ['Disiplin Kurulu Raporu'],
    ['Rapor Dönemi', $reportMeta["date_range"]],
    ['Raporu Alan', $reportMeta["generated_by"]],
    ['Oluşturulma', $reportMeta["generated_at"]],
    [],
    ['=== ÖZET İSTATİSTİKLER ==='],
    ['Metrik', 'Değer'],
    ['Toplam Dosya', $totalCases],
    ['Ceza Verilen', $cezaVerilenler],
    ['Ceza Verilmeyen', $cezaVerilmeyenler],
    ['Kurulda', $kuruldakiler],
    ['Savunma Bekleyen', $savunmaBekleyenler],
    ['Yön. Değerlendirmede', $yoneticiDegerlendirmesinde],
    ['Ertelenen', $ertelenenDosyalar],
    ['Toplam Toplantı', $toplamToplanti],
    [],
    ['=== BÖLÜM BAZLI DOSYA SAYISI ==='],
    ['Bölüm', 'Dosya Sayısı'],
];

foreach($bolumBazli as $b) {
    $excelRows[] = [$b->label, $b->count];
}

$excelRows[] = [];
$excelRows[] = ['=== CEZA SKALASI DAĞILIMI ==='];
$excelRows[] = ['Ceza Türü', 'Dosya Sayısı'];

foreach($skalaBazli as $s) {
    if($s['count'] > 0) {
        $excelRows[] = [$s['ceza_adi'], $s['count']];
    }
}

$excelRows[] = [];
$excelRows[] = ['=== EN ÇOK İHLAL MADDELERİ ==='];
$excelRows[] = ['İhlal Maddesi', 'Dosya Sayısı'];

foreach($behaviorStats->take(15) as $b) {
    $excelRows[] = [$b['tanim'], $b['count']];
}

$excelRows[] = [];
$excelRows[] = ['=== EN ÇOK DOSYASI OLAN PERSONELLER ==='];
$excelRows[] = ['Personel', 'Bölüm', 'Dosya Sayısı'];

foreach($topPersoneller as $p) {
    $excelRows[] = [$p['user']->name ?? "-", $p['bolum'], $p['count']];
}

$excelRows[] = [];
$excelRows[] = ['=== EN ÇOK TUTANAK TUTAN PERSONELLER ==='];
$excelRows[] = ['Personel', 'Bölüm', 'Tutanak Sayısı'];

foreach($topReporters as $r) {
    $excelRows[] = [$r['user']->name ?? "-", $r['bolum'], $r['count']];
}
?>

function exportExcel() {
    const rows = <?php echo json_encode($excelRows); ?>;

    if (typeof XLSX === 'undefined') {
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
        s.onload = () => doExcel(rows);
        document.head.appendChild(s);
    } else {
        doExcel(rows);
    }
}

function doExcel(rows) {
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(rows);
    ws['!cols'] = [{wch:40},{wch:20},{wch:15}];
    XLSX.utils.book_append_sheet(wb, ws, 'Disiplin Raporu');
    XLSX.writeFile(wb, 'disiplin-raporu-<?php echo e(now()->format("Y-m-d")); ?>.xlsx');
}

function exportPDF() {
    document.querySelectorAll('.no-print').forEach(el => el.style.display = 'none');
    document.querySelector('.print-header').style.display = 'block';
    window.print();
    setTimeout(() => {
        document.querySelectorAll('.no-print').forEach(el => el.style.display = '');
        document.querySelector('.print-header').style.display = 'none';
    }, 1000);
}
</script>
<?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/disiplin/report.blade.php ENDPATH**/ ?>