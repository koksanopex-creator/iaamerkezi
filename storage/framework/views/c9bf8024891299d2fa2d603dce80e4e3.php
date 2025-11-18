<?php
    use Illuminate\Support\Str;
?>

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
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Tüm Müşteri Şikayetleri')); ?>

            </h2>
            <a href="<?php echo e(route('admin.sikayet-raporlari.index')); ?>"
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Raporlara Geri Dön
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    
    <div class="py-10 bg-gradient-to-br from-gray-50 to-gray-100 overflow-x-hidden">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Toplam Kayıt</div>
                    <div class="text-2xl font-black text-blue-600"><?php echo e($sikayetler->total()); ?></div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Yeni (Beklemede)</div>
                    <div class="text-2xl font-black text-yellow-600"><?php echo e($stats['yeni']); ?></div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">İşlemde</div>
                    <div class="text-2xl font-black text-indigo-600"><?php echo e($stats['islemde']); ?></div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Çözülen / Kapatılan</div>
                    <div class="text-2xl font-black text-green-600"><?php echo e($stats['cozulen']); ?></div>
                </div>
                
            </div>
            
            <?php if($stats['enCokKategori']): ?>
            <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 uppercase">En Yoğun Kategori</div>
                <div class="text-lg font-bold text-gray-800 truncate" title="<?php echo e($stats['enCokKategori']->ad); ?>">
                    <?php echo e($stats['enCokKategori']->ad); ?> 
                    <span class="text-base font-medium text-gray-500">(<?php echo e($stats['enCokKategori']->total); ?> adet)</span>
                </div>
            </div>
            <?php endif; ?>
            

            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-200">
                <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

                <div class="p-4 md:p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Kayıtlar</h3>
                </div>

                
                
                <div class="hidden md:block overflow-hidden">
                    <table class="w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 4%;">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%;">Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">Müşteri</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 20%;">Başlık</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Durum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 9%;">Son Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 8%;">Resimler</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 9%;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    // === RENKLENDİRME GÜNCELLENDİ ===
                                    $rowBg = 'hover:bg-gray-50';
                                    $rowBar = 'border-l-4 border-transparent';
                                    if ($sikayet->musteri_durum === 'İşlemde') {
                                        $rowBg = 'bg-blue-50/60 hover:bg-blue-100/60';
                                        $rowBar = 'border-l-4 border-blue-400';
                                    } elseif ($sikayet->musteri_durum === 'Yeni') {
                                        $rowBg = 'bg-yellow-50/60 hover:bg-yellow-100/60';
                                        $rowBar = 'border-l-4 border-yellow-400';
                                    } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi','Kapatıldı'])) {
                                        $rowBg = 'bg-green-50/60 hover:bg-green-100/60';
                                        $rowBar = 'border-l-4 border-green-400';
                                    } else { 
                                        // Diğer durumlar (örn: Yeniden Açıldı) için
                                        $rowBg = 'bg-gray-50/60 hover:bg-gray-100/60';
                                        $rowBar = 'border-l-4 border-gray-400';
                                    }
                                    // === RENKLENDİRME SONU ===
                                ?>

                                <tr class="<?php echo e($rowBg); ?> <?php echo e($rowBar); ?> transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <?php echo e($sikayetler->firstItem() + $index); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                        <?php echo e($sikayet->created_at?->format('d.m.Y H:i')); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 truncate" title="<?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?>">
                                        <?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 truncate" title="<?php echo e($sikayet->musteri_adi); ?>">
                                        <?php echo e($sikayet->musteri_adi); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 truncate" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>">
                                        <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php if($sikayet->musteri_durum === 'Yeni'): ?> bg-yellow-100 text-yellow-800
                                            <?php elseif($sikayet->musteri_durum === 'İşlemde'): ?> bg-blue-100 text-blue-800
                                            <?php elseif(in_array($sikayet->musteri_durum, ['Çözümlendi','Kapatıldı'])): ?> bg-green-100 text-green-800
                                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                            <?php echo e($sikayet->musteri_durum ?? '—'); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm whitespace-nowrap <?php echo e($sikayet->musteri_cozum_son_tarihi ? 'text-red-600 font-semibold' : 'text-gray-500'); ?>">
                                        <?php echo e($sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A'); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <?php
                                            $imageFiles = $sikayet->dosyalar->filter(fn($d) => Str::startsWith($d->mime_tipi, 'image/'));
                                        ?>
                                        <div class="flex items-center space-x-1">
                                            <?php $__empty_2 = true; $__currentLoopData = $imageFiles->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                <a href="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" target="_blank" title="<?php echo e($dosya->orijinal_adi); ?>">
                                                    <img src="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>"
                                                         class="h-8 w-8 rounded-md object-cover border border-gray-300 hover:scale-110 transition-transform"
                                                         alt="Önizleme">
                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <span class="text-xs">Yok</span>
                                            <?php endif; ?>
                                            <?php if($imageFiles->count() > 2): ?>
                                                <span class="text-xs text-gray-400 font-bold ml-1">+<?php echo e($imageFiles->count() - 2); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" target="_blank"
                                               class="inline-flex items-center px-3 py-1.5 font-semibold rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                                Detay
                                            </a>
                                            <?php if($sikayet->iaaProjesi ?? null): ?>
                                                <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaaProjesi)); ?>" target="_blank"
                                                   class="inline-flex items-center px-3 py-1.5 font-semibold rounded-md bg-purple-100 text-purple-700 hover:bg-purple-200 transition">
                                                    Proje
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Kayıt bulunamadı.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                
                <div class="md:hidden">
                    <div class="space-y-4 p-4">
                        <?php $__empty_1 = true; $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                // === RENKLENDİRME GÜNCELLENDİ ===
                                $rowBg = 'hover:bg-gray-50';
                                $rowBar = 'border-l-4 border-transparent';
                                if ($sikayet->musteri_durum === 'İşlemde') {
                                    $rowBg = 'bg-blue-50/60 hover:bg-blue-100/60';
                                    $rowBar = 'border-l-4 border-blue-400';
                                } elseif ($sikayet->musteri_durum === 'Yeni') {
                                    $rowBg = 'bg-yellow-50/60 hover:bg-yellow-100/60';
                                    $rowBar = 'border-l-4 border-yellow-400';
                                } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi','Kapatıldı'])) {
                                    $rowBg = 'bg-green-50/60 hover:bg-green-100/60';
                                    $rowBar = 'border-l-4 border-green-400';
                                } else {
                                    // Diğer durumlar için
                                    $rowBg = 'bg-gray-50/60 hover:bg-gray-100/60';
                                    $rowBar = 'border-l-4 border-gray-400';
                                }
                                // === RENKLENDİRME SONU ===
                            ?>

                            

                            <div class="rounded-lg shadow border <?php echo e($rowBg); ?> <?php echo e($rowBar); ?> p-4 space-y-3 cursor-pointer"
                                 onclick="window.open('<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>', '_blank')" 
                                 title="Şikayet detayını görmek için tıklayın">
                                
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="font-semibold text-gray-700">#<?php echo e(($sikayetler->currentPage() - 1) * $sikayetler->perPage() + $index + 1); ?></span>
                                        <span class="text-sm text-gray-600 ml-2"><?php echo e($sikayet->created_at?->format('d.m.Y H:i')); ?></span>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        <?php if($sikayet->musteri_durum === 'Yeni'): ?> bg-yellow-100 text-yellow-800
                                        <?php elseif($sikayet->musteri_durum === 'İşlemde'): ?> bg-blue-100 text-blue-800
                                        <?php elseif(in_array($sikayet->musteri_durum, ['Çözümlendi','Kapatıldı'])): ?> bg-green-100 text-green-800
                                        <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                        <?php echo e($sikayet->musteri_durum ?? '—'); ?>

                                    </span>
                                </div>

                                
                                <div>
                                    <p class="text-xs text-gray-500 uppercase truncate" title="<?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?>"><?php echo e($sikayet->sikayetKategori->ad ?? 'N/A'); ?></p>
                                    <p class="text-base font-semibold text-gray-900 truncate" title="<?php echo e($sikayet->musteri_sikayet_konusu); ?>"><?php echo e($sikayet->musteri_sikayet_konusu); ?></p>
                                    <p class="text-sm font-medium text-gray-700 truncate" title="<?php echo e($sikayet->musteri_adi); ?>"><?php echo e($sikayet->musteri_adi); ?></p>
                                </div>
                                
                                
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                    <div class="text-sm">
                                        <span class="text-gray-500">Son Tarih:</span>
                                        <span class="font-semibold <?php echo e($sikayet->musteri_cozum_son_tarihi ? 'text-red-600' : 'text-gray-500'); ?>">
                                            <?php echo e($sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : 'N/A'); ?>

                                        </span>
                                    </div>
                                    <?php
                                        $imageFiles = $sikayet->dosyalar->filter(fn($d) => Str::startsWith($d->mime_tipi, 'image/'));
                                    ?>
                                    <div class="flex items-center space-x-1">
                                        <?php $__empty_2 = true; $__currentLoopData = $imageFiles->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <a href="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" target="_blank" title="<?php echo e($dosya->orijinal_adi); ?>">
                                                <img src="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>"
                                                     class="h-8 w-8 rounded-md object-cover border border-gray-300"
                                                     alt="Önizleme">
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                            <span class="text-xs text-gray-400">Resim Yok</span>
                                        <?php endif; ?>
                                        <?php if($imageFiles->count() > 2): ?>
                                            <span class="text-xs text-gray-400 font-bold ml-1">+<?php echo e($imageFiles->count() - 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200">
                                    <a href="<?php echo e(route('admin.sikayetler.show', $sikayet)); ?>" target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition"
                                       onclick="event.stopPropagation()">
                                        Detay
                                    </a>
                                    <?php if($sikayet->iaaProjesi ?? null): ?>
                                        <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaaProjesi)); ?>" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md bg-purple-100 text-purple-700 hover:bg-purple-200 transition"
                                           onclick="event.stopPropagation()">
                                            Proje Alanı
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                             <div class="px-6 py-8 text-center text-sm text-gray-500">
                                Kayıt bulunamadı.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="px-4 py-4 bg-gray-50 border-t border-gray-200">
                    <?php echo e($sikayetler->withQueryString()->links()); ?>

                </div>
            </div>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/tum-sikayet-listesi.blade.php ENDPATH**/ ?>