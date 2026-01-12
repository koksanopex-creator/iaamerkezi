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
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200 text-lg">
                    <?php echo e(substr($arabulucu->name, 0, 1)); ?>

                </div>
                <div>
                    <?php echo e($arabulucu->name); ?>

                    <span class="block text-xs font-normal text-gray-500">Sicil: <?php echo e($arabulucu->sicil_no); ?></span>
                </div>
                
                
                <?php if($arabulucu->is_active): ?>
                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 border border-green-200">Aktif</span>
                <?php else: ?>
                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600 border border-gray-200">Pasif (Listelerde Görünmez)</span>
                <?php endif; ?>
            </h2>

            <div class="flex gap-2">
                
                <form action="<?php echo e(route('admin.arabulucular.toggleStatus', $arabulucu->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="px-4 py-2 text-sm font-bold rounded shadow-sm transition <?php echo e($arabulucu->is_active ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-green-600 text-white hover:bg-green-700'); ?>">
                        <?php echo e($arabulucu->is_active ? 'Pasife Al' : 'Aktife Al'); ?>

                    </button>
                </form>

                <a href="<?php echo e(route('admin.arabulucular.edit', $arabulucu->id)); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-bold shadow-sm transition">
                    Düzenle
                </a>
                <a href="<?php echo e(route('admin.arabulucular.index')); ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded text-sm font-bold shadow-sm transition">
                    Geri Dön
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                
                <div class="space-y-6">
                    
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">İletişim Bilgileri</h3>
                        <ul class="space-y-3 text-sm text-gray-700">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span><?php echo e($arabulucu->telefon ?? 'Telefon Yok'); ?></span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span><?php echo e($arabulucu->email ?? 'E-posta Yok'); ?></span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span><?php echo e($arabulucu->adres ?? $arabulucu->sehir); ?></span>
                            </li>
                        </ul>
                    </div>

                    
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                        <h3 class="font-bold text-gray-700 text-sm mb-3">Sistem Kaydı</h3>
                        <div class="text-xs space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Kayıt Tarihi:</span>
                                <span class="font-mono text-gray-700"><?php echo e($arabulucu->created_at->format('d.m.Y')); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Kaydeden:</span>
                                <span class="font-bold text-gray-700"><?php echo e($arabulucu->creator->name ?? 'Sistem'); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Son İşlem:</span>
                                <span class="font-mono text-gray-700"><?php echo e($arabulucu->updated_at->diffForHumans()); ?></span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 border-b pb-2 mb-4 flex justify-between items-center">
                            <span>Yıllara Göre Sistem Payı</span>
                            <span class="text-xs font-normal text-gray-400 bg-gray-100 px-2 py-1 rounded">2024 - <?php echo e(now()->year); ?></span>
                        </h3>

                        <?php if(count($yillikVeriler) > 0): ?>
                            <div class="space-y-4">
                                <?php $__currentLoopData = $yillikVeriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $veri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <div x-data="{ expanded: false }" class="border border-gray-100 rounded-lg overflow-hidden">
                                        
                                        
                                        <div @click="expanded = !expanded" class="p-3 bg-gray-50 cursor-pointer hover:bg-gray-100 transition">
                                            <div class="flex justify-between items-end mb-2">
                                                <div>
                                                    <span class="font-black text-gray-700 text-lg"><?php echo e($veri['yil']); ?></span>
                                                    <span class="text-xs text-gray-500 ml-1">
                                                        (Sistem: <?php echo e($veri['sistem_toplam']); ?> / Bu Kişi: <?php echo e($veri['kendi_toplam']); ?>)
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="font-bold text-indigo-600 text-sm">%<?php echo e(number_format($veri['oran'], 1)); ?></span>
                                                    
                                                    <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-500 h-2 rounded-full transition-all duration-1000" style="width: <?php echo e($veri['oran']); ?>%"></div>
                                            </div>
                                        </div>

                                        
                                        <div x-show="expanded" x-collapse style="display: none;">
                                            <div class="p-3 bg-white border-t border-gray-100">
                                                <?php if($veri['dosyalar']->count() > 0): ?>
                                                    <ul class="space-y-2">
                                                        <?php $__currentLoopData = $veri['dosyalar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="flex justify-between items-center text-xs p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-100">
                                                                <div class="flex flex-col">
                                                                    <a href="<?php echo e(route('admin.arabuluculuk.show', $dosya->id)); ?>" class="font-bold text-indigo-600 hover:underline">
                                                                        #<?php echo e($dosya->dosya_no ?? 'No Yok'); ?>

                                                                    </a>
                                                                    
                                                                    <span class="text-[10px] text-gray-500 mt-0.5">
                                                                        <span class="font-semibold text-gray-600">Personel:</span> <?php echo e($dosya->calisan->name ?? '?'); ?>

                                                                    </span>
                                                                </div>
                                                                
                                                                <?php
                                                                    $renk = 'bg-gray-100 text-gray-600';
                                                                    if($dosya->status == 'kapatildi') $renk = 'bg-green-100 text-green-700';
                                                                    if($dosya->status == 'arabulucuda') $renk = 'bg-blue-100 text-blue-700';
                                                                ?>
                                                                <span class="px-2 py-0.5 rounded <?php echo e($renk); ?>">
                                                                    <?php echo e(str_replace('_', ' ', ucfirst($dosya->status))); ?>

                                                                </span>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                <?php else: ?>
                                                    <p class="text-xs text-gray-400 italic text-center py-2">Bu yıl, bu arabulucuya ait dosya bulunamadı.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-sm text-gray-400 italic">Veri hesaplanamadı.</p>
                        <?php endif; ?>

                        
                        <?php if(collect($yillikVeriler)->sum('kendi_toplam') > 0): ?>
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <h4 class="font-bold text-gray-700 text-sm mb-4 text-center">Dosya Dağılım Grafiği</h4>
                                
                                <div id="filesChart" class="flex justify-center"></div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                
<div class="lg:col-span-2 space-y-6">
    
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <form action="<?php echo e(route('admin.arabulucular.show', $arabulucu->id)); ?>" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hızlı Personel Ara</label>
                    <div class="relative">
                        <input type="text" id="personelSearchInput" class="w-full border-gray-300 rounded text-sm pl-8 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Personel adı yazın...">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Başlangıç Tarihi</label>
                    <input type="date" name="baslangic_tarihi" value="<?php echo e(request('baslangic_tarihi')); ?>" class="w-full border-gray-300 rounded text-sm text-gray-600">
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dosya Durumu</label>
                    <select name="durum" class="w-full border-gray-300 rounded text-sm text-gray-600">
                        <option value="">Tümü</option>
                        <option value="kapatildi" <?php echo e(request('durum') == 'kapatildi' ? 'selected' : ''); ?>>Kapatıldı (Tamamlanan)</option>
                        <option value="arabulucuda" <?php echo e(request('durum') == 'arabulucuda' ? 'selected' : ''); ?>>Arabulucuda</option>
                        <option value="odeme_bekliyor" <?php echo e(request('durum') == 'odeme_bekliyor' ? 'selected' : ''); ?>>Ödeme Bekliyor</option>
                    </select>
                </div>

                
                <div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm transition shadow">
                        Filtreleri Uygula
                    </button>
                </div>
            </div>
        </form>
    </div>

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Dosya Arşivi
            </h3>
            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold">
                Toplam: <?php echo e($groupedCases->flatten()->count()); ?>

            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="casesTable">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosya No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İlgili Personel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $groupedCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yil => $dosyalar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        
                        
                        <tr class="bg-gray-50 border-b border-gray-200 year-header-row" data-year="<?php echo e($yil); ?>">
                            <td colspan="6" class="px-6 py-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black text-gray-700 bg-gray-200 px-2 py-1 rounded"><?php echo e($yil); ?></span>
                                    <span class="text-xs text-gray-500 font-medium">(<?php echo e($dosyalar->count()); ?> Dosya)</span>
                                </div>
                            </td>
                        </tr>

                        
                        <?php $__currentLoopData = $dosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-indigo-50 transition border-b border-gray-100 case-row" data-personel="<?php echo e(strtolower($case->calisan->name ?? '')); ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-900">#<?php echo e($case->dosya_no); ?></span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600 uppercase">
                                            <?php echo e(substr($case->calisan->name ?? '?', 0, 1)); ?>

                                        </div>
                                        <div class="ml-2">
                                            <div class="text-xs font-bold text-gray-800"><?php echo e($case->calisan->name ?? 'Silinmiş'); ?></div>
                                            <div class="text-[10px] text-gray-500"><?php echo e($case->calisan->email ?? ''); ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($case->created_at->format('d.m.Y')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                        $colors = [
                                            'taslak' => 'bg-gray-100 text-gray-600',
                                            'hukuk_incelemesinde' => 'bg-blue-100 text-blue-700',
                                            'yonetim_onayinda' => 'bg-purple-100 text-purple-700',
                                            'arabulucuda' => 'bg-indigo-100 text-indigo-700',
                                            'odeme_bekliyor' => 'bg-orange-100 text-orange-800',
                                            'kapatildi' => 'bg-green-100 text-green-700',
                                            'anlasma_saglanamadi' => 'bg-red-100 text-red-700',
                                        ];
                                        $color = $colors[$case->status] ?? 'bg-gray-100 text-gray-600';
                                    ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($color); ?>">
                                        <?php echo e(str_replace('_', ' ', strtoupper($case->status))); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700 font-mono">
                                    <?php echo e($case->anlasilan_tutar ? number_format($case->anlasilan_tutar, 2) . ' ₺' : '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo e(route('admin.arabuluculuk.show', $case->id)); ?>" class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline">İncele</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                Kriterlere uygun dosya bulunamadı.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('personelSearchInput');
        const rows = document.querySelectorAll('.case-row');
        const yearHeaders = document.querySelectorAll('.year-header-row');

        searchInput.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();

            // 1. Satırları Filtrele
            rows.forEach(row => {
                const name = row.getAttribute('data-personel');
                if (name.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // 2. Yıl Başlıklarını Kontrol Et (Eğer o yılın altında hiç görünür satır kalmadıysa başlığı da gizle)
            yearHeaders.forEach(header => {
                // Header'dan sonraki satırları bul, bir sonraki header'a kadar
                let nextSibling = header.nextElementSibling;
                let hasVisibleChildren = false;

                while(nextSibling && !nextSibling.classList.contains('year-header-row')) {
                    if (nextSibling.style.display !== 'none') {
                        hasVisibleChildren = true;
                        break; // En az bir tane görünür bulduk, yeterli
                    }
                    nextSibling = nextSibling.nextElementSibling;
                }

                if (hasVisibleChildren) {
                    header.style.display = '';
                } else {
                    header.style.display = 'none';
                }
            });
        });
    });
</script>

            </div>
        </div>
    </div>

    
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // PHP'den gelen verileri JS formatına çeviriyoruz
        // Controller'dan gelen $yillikVeriler dizisini kullanıyoruz
        var rawData = <?php echo json_encode($yillikVeriler, 15, 512) ?>;

        // Verileri ayrıştır (Yıllar ve Sayılar)
        var seriesData = [];
        var labelsData = [];

        rawData.forEach(function(item) {
            // Sadece dosyası olan yılları grafiğe katalım ki grafik boş görünmesin
            if(item.kendi_toplam > 0) {
                seriesData.push(item.kendi_toplam);
                labelsData.push(item.yil.toString());
            }
        });

        // Eğer hiç veri yoksa grafiği çizme
        if (seriesData.length === 0) return;

        var options = {
            series: seriesData,
            chart: {
                type: 'donut', // 'pie' de yapabilirsiniz
                height: 250,
                fontFamily: 'inherit',
            },
            labels: labelsData,
            colors: ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'], // Tailwind renkleri (Indigo, Purple, Pink, Amber, Emerald)
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Toplam',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0) + ' Dosya'
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false // Grafik üzerindeki sayıları gizle (temiz görünüm için)
            },
            legend: {
                position: 'bottom',
                fontFamily: 'inherit',
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + " Dosya"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#filesChart"), options);
        chart.render();
    });
</script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabulucular/show.blade.php ENDPATH**/ ?>