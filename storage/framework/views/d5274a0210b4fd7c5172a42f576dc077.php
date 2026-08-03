<?php $__env->startPush('pageTitle'); ?>
    Günlük Disiplin Süreçleri Raporu (<?php echo e($tarih); ?>) | 
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
            <?php echo e(__('Günlük Disiplin Süreçleri Raporu')); ?>

            <span class="text-sm font-normal text-gray-500 ml-2">(<?php echo e($tarih); ?>)</span>
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6 shadow-sm">
                <form method="GET" action="<?php echo e(route('admin.reports.daily_disiplin')); ?>" class="flex flex-col md:flex-row items-end gap-4">
                    <div class="w-full md:w-1/3">
                        <label for="kural_id" class="block text-sm font-bold text-indigo-800 mb-1">Rapor Kuralı Simülasyonu</label>
                        <select name="kural_id" id="kural_id" class="block w-full rounded-md border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="this.form.submit()">
                            <option value="">-- Varsayılan (Kendi Yetkim) --</option>
                            <?php if(isset($tumKurallar)): ?>
                                <?php $__currentLoopData = $tumKurallar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kural): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kural->id); ?>" <?php if(request('kural_id') == $kural->id): echo 'selected'; endif; ?>>
                                        <?php echo e($kural->baslik); ?> 
                                        (<?php echo e($kural->disiplin_kapsam === 'kendi_bolumu' ? 'Bölüm Bazlı' : 'Tüm Veriler'); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <?php if(isset($seciliKural) && $seciliKural): ?>
                    <div class="w-full mt-4">
                        <label class="block text-sm font-bold text-indigo-800 mb-2">Alıcılar (Kimin Gözünden Görmek İstiyorsunuz? Tıklayın)</label>
                        <div class="flex flex-wrap gap-2">
                            <?php if(isset($kuralAlicilari) && $kuralAlicilari->count() > 0): ?>
                                <?php $__currentLoopData = $kuralAlicilari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alici): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('admin.reports.daily_disiplin', ['kural_id' => $seciliKural->id, 'user_id' => $alici->id])); ?>" 
                                       class="px-3 py-1.5 text-sm font-medium rounded-full border <?php echo e(request('user_id') == $alici->id ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-indigo-700 border-indigo-200 hover:bg-indigo-100'); ?> transition-all">
                                        <?php echo e($alici->name); ?> (<?php echo e(optional($alici->bolum)->ad ?? 'Bölüm Yok'); ?>)
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <span class="text-sm text-gray-500 italic">Bu kural için uygun alıcı bulunamadı.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(isset($seciliKural) && $seciliKural): ?>
                    <div class="w-full md:w-1/3 flex items-center mb-1">
                        <a href="<?php echo e(route('admin.reports.daily_disiplin')); ?>" class="text-sm text-indigo-600 hover:text-indigo-900 underline">Simülasyonu Kapat</a>
                    </div>
                    <?php endif; ?>
                </form>
                
                <?php if(isset($seciliKural) && $seciliKural): ?>
                <div class="mt-3 text-xs text-indigo-700 bg-white p-2 rounded border border-indigo-100">
                    <span class="font-bold">Kural Detayı:</span> 
                    <?php if(!empty($seciliKural->disiplin_suc_kategorileri)): ?>
                        Sadece <?php echo e(count($seciliKural->disiplin_suc_kategorileri)); ?> adet seçili suç kategorisi gösteriliyor.
                    <?php else: ?>
                        Tüm suç kategorileri dahil.
                    <?php endif; ?>
                    
                    <?php if(isset($simulatedUser) && $simulatedUser): ?>
                        Şu anda <strong><?php echo e($simulatedUser->name); ?></strong> adlı alıcının e-postasına gidecek olan özel rapor verilerini görüntülüyorsunuz.
                    <?php elseif($seciliKural->disiplin_kapsam === 'kendi_bolumu'): ?>
                        Rapor verisini görmek için lütfen yukarıdaki listeden bir alıcı seçin.
                    <?php else: ?>
                        Bu kural, seçilen tüm alıcılara bu sayfada gördüğünüz "Genel Verileri" göndermektedir.
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if(isset($raporData['disiplin'])): ?>
                <!-- ÖZET KARTLARI -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- TOPLAM -->
                    <div class="block p-6 bg-slate-50 border border-slate-200 rounded-lg shadow-sm hover:shadow-md transition">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide">TOPLAM DOSYA (Tüm Zmn.)</h3>
                        <p class="mt-2 text-3xl font-extrabold text-slate-700">
                            <?php echo e($raporData['disiplin']['genel']['tum']['toplam']); ?></p>
                    </div>

                    <!-- AÇIK -->
                    <div class="block p-6 bg-white border border-red-200 rounded-lg shadow-sm hover:shadow-md transition">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">AÇIK DOSYALAR</h3>
                        <p class="mt-2 text-3xl font-extrabold text-red-500">
                            <?php echo e($raporData['disiplin']['genel']['tum']['acik']); ?></p>
                    </div>

                    <!-- SAVUNMA BEKLEYEN -->
                    <div class="block p-6 bg-white border border-amber-200 rounded-lg shadow-sm hover:shadow-md transition">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">SAVUNMA BEKLEYEN</h3>
                        <p class="mt-2 text-3xl font-extrabold text-amber-500">
                            <?php echo e($raporData['disiplin']['genel']['tum']['savunma']); ?></p>
                    </div>

                    <!-- KARARA BAĞLANAN -->
                    <div class="block p-6 bg-white border border-emerald-200 rounded-lg shadow-sm hover:shadow-md transition">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">KAPALI DOSYALAR</h3>
                        <p class="mt-2 text-3xl font-extrabold text-emerald-500">
                            <?php echo e($raporData['disiplin']['genel']['tum']['kapali']); ?></p>
                    </div>
                </div>

                <!-- GENEL ÖZET TABLOSU -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Genel Özet</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Dönem</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500">Toplam Dosya</th>
                                    <th class="px-4 py-3 text-center font-bold text-gray-600">Açık (Toplam)</th>
                                    <th class="px-4 py-3 text-center font-medium text-amber-700 text-xs bg-amber-50/50">↳ Savunma</th>
                                    <th class="px-4 py-3 text-center font-medium text-indigo-700 text-xs bg-indigo-50/50">↳ Kurul / Yön.</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500">Karara Bağlanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php $__currentLoopData = ['tum' => 'Tüm Zamanlar', 'bu_yil' => 'Bu Yıl', 'bu_ay' => 'Bu Ay', 'bu_hafta' => 'Bu Hafta']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($label); ?></td>
                                        <td class="px-4 py-3 text-center font-bold"><?php echo e($raporData['disiplin']['genel'][$key]['toplam']); ?></td>
                                        <td class="px-4 py-3 text-center">
                                            <?php if($raporData['disiplin']['genel'][$key]['acik'] > 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800"><?php echo e($raporData['disiplin']['genel'][$key]['acik']); ?></span>
                                            <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-center bg-amber-50/30">
                                            <?php if($raporData['disiplin']['genel'][$key]['savunma'] > 0): ?>
                                                <span class="text-amber-700 font-bold text-xs"><?php echo e($raporData['disiplin']['genel'][$key]['savunma']); ?></span>
                                            <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-center bg-indigo-50/30">
                                            <?php $kurulYon = $raporData['disiplin']['genel'][$key]['kurul'] + $raporData['disiplin']['genel'][$key]['yonetici']; ?>
                                            <?php if($kurulYon > 0): ?>
                                                <span class="text-indigo-700 font-bold text-xs"><?php echo e($kurulYon); ?></span>
                                            <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <?php if($raporData['disiplin']['genel'][$key]['kapali'] > 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"><?php echo e($raporData['disiplin']['genel'][$key]['kapali']); ?></span>
                                            <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- DÖNEMSEL PERFORMANS (ÇEYREKLER) -->
                <?php if(isset($raporData['disiplin']['ceyrekler'])): ?>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Dönemsel Performans (Çeyrekler)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Çeyrek</th>
                                        <th class="px-4 py-3 text-center font-bold text-gray-600">Toplam Dosya</th>
                                        <th class="px-4 py-3 text-center font-bold text-gray-600">Açık (Toplam)</th>
                                        <th class="px-4 py-3 text-center font-medium text-amber-700 text-xs bg-amber-50/50">↳ Savunma</th>
                                        <th class="px-4 py-3 text-center font-medium text-indigo-700 text-xs bg-indigo-50/50">↳ Kurul / Yön.</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Karara Bağlanan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php $__currentLoopData = $raporData['disiplin']['ceyrekler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $qData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e(date('Y')); ?> <?php echo e($key); ?></td>
                                            <td class="px-4 py-3 text-center font-bold"><?php echo e($qData['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center">
                                                <?php if($qData['acik'] > 0): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800"><?php echo e($qData['acik']); ?></span>
                                                <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3 text-center bg-amber-50/30">
                                                <?php if($qData['savunma'] > 0): ?>
                                                    <span class="text-amber-700 font-bold text-xs"><?php echo e($qData['savunma']); ?></span>
                                                <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3 text-center bg-indigo-50/30">
                                                <?php $kurulYon = $qData['kurul'] + $qData['yonetici']; ?>
                                                <?php if($kurulYon > 0): ?>
                                                    <span class="text-indigo-700 font-bold text-xs"><?php echo e($kurulYon); ?></span>
                                                <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <?php if($qData['kapali'] > 0): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"><?php echo e($qData['kapali']); ?></span>
                                                <?php else: ?> <span class="text-gray-300">-</span> <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 gap-6">
                    <!-- BÖLÜM BAZLI DAĞILIM -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Bölüm Bazlı Dağılım</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Bölüm</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Tüm Zamanlar</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Bu Yıl</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Bu Ay</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Bu Hafta</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php $__currentLoopData = $raporData['disiplin']['bolumler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($bolum['ad']); ?></td>
                                            <td class="px-4 py-3 text-center font-bold"><?php echo e($bolum['tum']['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center"><?php echo e($bolum['bu_yil']['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center"><?php echo e($bolum['bu_ay']['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center"><?php echo e($bolum['bu_hafta']['toplam']); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- KATEGORİ BAZLI DAĞILIM -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Suç Kategorisi Bazlı Dağılım</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Kategori</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Tüm Zamanlar</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Bu Yıl</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Bu Ay</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Bu Hafta</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php $__currentLoopData = $raporData['disiplin']['kategoriler']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($kat['ad']); ?></td>
                                            <td class="px-4 py-3 text-center font-bold"><?php echo e($kat['tum']['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center"><?php echo e($kat['bu_yil']['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center"><?php echo e($kat['bu_ay']['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center"><?php echo e($kat['bu_hafta']['toplam']); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- YAKA BAZLI DAĞILIM -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Yaka Bazlı Dağılım (Mavi/Beyaz)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Yaka Türü</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Tüm Zamanlar</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Bu Yıl</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Bu Ay</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-500">Bu Hafta</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php $__currentLoopData = $raporData['disiplin']['yakalar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yaka): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($yaka['ad']); ?></td>
                                            <td class="px-4 py-3 text-center font-bold"><?php echo e($yaka['tum']['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center"><?php echo e($yaka['bu_yil']['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center"><?php echo e($yaka['bu_ay']['toplam']); ?></td>
                                            <td class="px-4 py-3 text-center"><?php echo e($yaka['bu_hafta']['toplam']); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
<?php endif; ?>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/reports/daily-disiplin-report.blade.php ENDPATH**/ ?>