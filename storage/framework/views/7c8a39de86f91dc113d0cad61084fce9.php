<?php $__env->startPush('pageTitle'); ?>
    Günlük Müşteri Şikayetleri Raporu (<?php echo e($tarih); ?>) | 
<?php $__env->stopPush(); ?>

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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Günlük Müşteri Şikayetleri Raporu')); ?>

            <span class="text-sm font-normal text-gray-500 ml-2">(<?php echo e($tarih); ?>)</span>
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <?php if(isset($raporData['sikayet_genel'])): ?>
                <!-- ÖZET KARTLARI (FİLTRELEMEYE YARIYOR) -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- TOPLAM -->
                    <a href="<?php echo e(route('admin.sikayetler.index')); ?>"
                        class="block p-6 bg-pink-50 border border-pink-200 rounded-lg shadow-sm hover:shadow-md transition">
                        <h3 class="text-xs font-bold text-pink-700 uppercase tracking-wide">TOPLAM ŞİKAYET</h3>
                        <p class="mt-2 text-3xl font-extrabold text-pink-700">
                            <?php echo e($raporData['sikayet_genel']['toplam_kayit']); ?></p>
                    </a>

                    <!-- YENİ (FİLTRE LİNKİ) -->
                    <!-- YENİ (FİLTRE LİNKİ) -->
                    <a href="<?php echo e(route('admin.sikayetler.index', ['durum' => 'Yeni'])); ?>"
                        class="block p-6 bg-white border border-red-200 rounded-lg shadow-sm hover:shadow-md transition group">
                        <h3
                            class="text-xs font-bold text-gray-500 uppercase tracking-wide group-hover:text-red-600 transition">
                            YENİ / BEKLEYEN</h3>
                        <p class="mt-2 text-3xl font-extrabold text-red-500">
                            <?php echo e($raporData['sikayet_genel']['bekleyen_yeni']); ?></p>
                    </a>

                    <!-- İŞLEMDE -->
                    <a href="<?php echo e(route('admin.sikayetler.index', ['durum' => 'İşlemde'])); ?>"
                        class="block p-6 bg-white border border-amber-200 rounded-lg shadow-sm hover:shadow-md transition group">
                        <h3
                            class="text-xs font-bold text-gray-500 uppercase tracking-wide group-hover:text-amber-600 transition">
                            İŞLEMDE</h3>
                        <p class="mt-2 text-3xl font-extrabold text-amber-500">
                            <?php echo e($raporData['sikayet_genel']['islemde_olan']); ?></p>
                    </a>

                    <!-- ÇÖZÜLEN -->
                    <!-- ÇÖZÜLEN -->
                    <a href="<?php echo e(route('admin.sikayetler.index', ['durum' => 'Kapatıldı'])); ?>"
                        class="block p-6 bg-white border border-emerald-200 rounded-lg shadow-sm hover:shadow-md transition group">
                        <h3
                            class="text-xs font-bold text-gray-500 uppercase tracking-wide group-hover:text-emerald-600 transition">
                            ÇÖZÜLEN / KAPALI</h3>
                        <p class="mt-2 text-3xl font-extrabold text-emerald-500">
                            <?php echo e($raporData['sikayet_genel']['cozumlenen']); ?></p>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- ZAMAN BAZLI TABLO -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Dönemsel Performans</h3>
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">Dönem</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-500">Gelen</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-500">Kapanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">Bugün</td>
                                    <td class="px-3 py-3 text-right"><?php echo e($raporData['sikayet_zaman']['bugun']['gelen']); ?>

                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <?php echo e($raporData['sikayet_zaman']['bugun']['kapanan']); ?>

                                        <?php if($raporData['sikayet_zaman']['bugun']['kapanan'] > $raporData['sikayet_zaman']['bugun']['gelen']): ?>
                                            <span
                                                class="text-emerald-600 text-xs block">(+<?php echo e($raporData['sikayet_zaman']['bugun']['kapanan'] - $raporData['sikayet_zaman']['bugun']['gelen']); ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">Bu Hafta / Geçen H.</td>
                                    <td class="px-3 py-3 text-right"><?php echo e($raporData['sikayet_zaman']['bu_hafta']['gelen']); ?>

                                        <span class="text-gray-400">/
                                            <?php echo e($raporData['sikayet_zaman']['gecen_hafta']['gelen']); ?></span></td>
                                    <td class="px-3 py-3 text-right">
                                        <?php echo e($raporData['sikayet_zaman']['bu_hafta']['kapanan']); ?> <span
                                            class="text-gray-400">/
                                            <?php echo e($raporData['sikayet_zaman']['gecen_hafta']['kapanan']); ?></span></td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">Bu Ay / Geçen Ay</td>
                                    <td class="px-3 py-3 text-right"><?php echo e($raporData['sikayet_zaman']['bu_ay']['gelen']); ?>

                                        <span class="text-gray-400">/
                                            <?php echo e($raporData['sikayet_zaman']['gecen_ay']['gelen']); ?></span></td>
                                    <td class="px-3 py-3 text-right"><?php echo e($raporData['sikayet_zaman']['bu_ay']['kapanan']); ?>

                                        <span class="text-gray-400">/
                                            <?php echo e($raporData['sikayet_zaman']['gecen_ay']['kapanan']); ?></span></td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 font-medium text-gray-900">Bu Yıl / Geçen Yıl</td>
                                    <td class="px-3 py-3 text-right"><?php echo e($raporData['sikayet_zaman']['bu_yil']['gelen']); ?>

                                        <span class="text-gray-400">/
                                            <?php echo e($raporData['sikayet_zaman']['gecen_yil']['gelen']); ?></span></td>
                                    <td class="px-3 py-3 text-right"><?php echo e($raporData['sikayet_zaman']['bu_yil']['kapanan']); ?>

                                        <span class="text-gray-400">/
                                            <?php echo e($raporData['sikayet_zaman']['gecen_yil']['kapanan']); ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ÇEYREK TABLOSU -->
                    <?php if(isset($raporData['sikayet_ceyrekler'])): ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Çeyrek Bazlı Hedefler</h3>
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">Çeyrek</th>
                                        <th class="px-3 py-2 text-right font-medium text-gray-500">Gelen</th>
                                        <th class="px-3 py-2 text-right font-medium text-gray-500">Kapanan</th>
                                        <th class="px-3 py-2 text-right font-medium text-gray-500">Başarı</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php $__currentLoopData = $raporData['sikayet_ceyrekler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $qData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="px-3 py-3 font-medium text-gray-900"><?php echo e(date('Y')); ?> <?php echo e($key); ?></td>
                                            <td class="px-3 py-3 text-right"><?php echo e($qData['gelen']); ?></td>
                                            <td class="px-3 py-3 text-right"><?php echo e($qData['kapanan']); ?></td>
                                            <td class="px-3 py-3 text-right">
                                                <?php if($qData['gelen'] > 0): ?>
                                                    <?php $success = round(($qData['kapanan'] / $qData['gelen']) * 100); ?>
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?php echo e($success >= 80 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                                        %<?php echo e($success); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-gray-300">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- KATEGORİ BAZLI DAĞILIM (DETAY LİNKLERİ İLE) -->
                <?php if(isset($raporData['sikayet_bolumler'])): ?>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Kategori Bazlı Dağılım</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Kategori / Bölüm</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Toplam</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Yeni</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">İşlemde</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Biten</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-500">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php $__currentLoopData = $raporData['sikayet_bolumler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($row['kategori_adi']); ?></td>
                                            <td class="px-4 py-3 text-center font-bold"><?php echo e($row['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center">
                                                <?php if($row['yeni'] > 0): ?>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><?php echo e($row['yeni']); ?></span>
                                                <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <?php if($row['islemde'] > 0): ?>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800"><?php echo e($row['islemde']); ?></span>
                                                <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <?php if($row['kapali'] > 0): ?>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"><?php echo e($row['kapali']); ?></span>
                                                <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                
                                                
                                                <a href="<?php echo e(route('admin.sikayetler.index', ['search' => $row['kategori_adi']])); ?>"
                                                    class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">
                                                    İncele &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/reports/daily-complaint-report.blade.php ENDPATH**/ ?>