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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e($cozumTakimi->ad); ?> - Takım Detayı
            </h2>
            <div class="flex space-x-2">
                <a href="javascript:history.back()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Geri Dön
                </a>
                <a href="<?php echo e(route('admin.cozum-takimlari.index')); ?>"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Diğer Çözüm Takımları
                </a>
                <a href="<?php echo e(route('admin.cozum-takimlari.edit', $cozumTakimi)); ?>"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    DÜZENLE
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- HEPSİ -->
                <a href="<?php echo e(request()->fullUrlWithQuery(['durum' => 'hepsi'])); ?>"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center border-l-4 border-indigo-500 hover:bg-gray-50 transition cursor-pointer">
                    <span class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Toplam Atanan
                        Şikayet</span>
                    <span class="text-3xl font-bold text-gray-800"><?php echo e($toplamSikayet); ?></span>
                </a>
                <!-- ÇÖZÜMLENENLER -->
                <a href="<?php echo e(request()->fullUrlWithQuery(['durum' => 'cozulmus'])); ?>"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center border-l-4 border-green-500 hover:bg-green-50 transition cursor-pointer">
                    <span class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Çözümlenen</span>
                    <span class="text-3xl font-bold text-green-600"><?php echo e($cozulmusSikayet); ?></span>
                </a>
                <!-- DEVAM EDENLER -->
                <a href="<?php echo e(request()->fullUrlWithQuery(['durum' => 'devam_eden'])); ?>"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center border-l-4 border-orange-500 hover:bg-orange-50 transition cursor-pointer">
                    <span class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Devam Eden</span>
                    <span class="text-3xl font-bold text-orange-600"><?php echo e($devamEdenSikayet); ?></span>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-6">
                        <h3 class="font-bold text-gray-900 border-b pb-2 mb-4">Takım Lideri</h3>
                        <?php if($cozumTakimi->lider): ?>
                            <div class="flex items-center">
                                <div
                                    class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xl mr-3 flex-shrink-0">
                                    <?php echo e(substr($cozumTakimi->lider->name, 0, 1)); ?>

                                </div>
                                <div class="overflow-hidden">
                                    <h4 class="font-bold text-gray-800 truncate" title="<?php echo e($cozumTakimi->lider->name); ?>">
                                        <?php echo e($cozumTakimi->lider->name); ?></h4>
                                    <span class="text-sm text-gray-500 truncate block"
                                        title="<?php echo e($cozumTakimi->lider->email); ?>"><?php echo e($cozumTakimi->lider->email); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-red-500 italic">Lider atanmamış.</span>
                        <?php endif; ?>
                    </div>

                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6 mb-6">
                        <h3 class="font-bold text-gray-900 border-b pb-2 mb-4">Filtrele</h3>
                        <form action="<?php echo e(route('admin.cozum-takimlari.show', $cozumTakimi)); ?>" method="GET">

                            <!-- Durum -->
                            <div class="mb-4">
                                <label for="durum" class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                                <select name="durum" id="durum"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Hepsi</option>
                                    <option value="cozulmus" <?php echo e(request('durum') == 'cozulmus' ? 'selected' : ''); ?>>
                                        Çözümlenenler</option>
                                    <option value="devam_eden" <?php echo e(request('durum') == 'devam_eden' ? 'selected' : ''); ?>>
                                        Devam Edenler</option>
                                    <option value="Yeni" <?php echo e(request('durum') == 'Yeni' ? 'selected' : ''); ?>>Yeni</option>
                                    <option value="Atandı" <?php echo e(request('durum') == 'Atandı' ? 'selected' : ''); ?>>Atandı
                                    </option>
                                    <option value="İnceleniyor" <?php echo e(request('durum') == 'İnceleniyor' ? 'selected' : ''); ?>>
                                        İnceleniyor</option>
                                </select>
                            </div>

                            <!-- Müşteri -->
                            <div class="mb-4">
                                <label for="musteri_id"
                                    class="block text-sm font-medium text-gray-700 mb-1">Müşteri</label>
                                <select name="musteri_id" id="musteri_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Tümü</option>
                                    <?php $__currentLoopData = $musteriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $musteri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($musteri->id); ?>" <?php echo e(request('musteri_id') == $musteri->id ? 'selected' : ''); ?>>
                                            <?php echo e(Str::limit($musteri->name, 25)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Tarih Aralığı -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Şikayet Tarihi</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="date" name="baslangic_tarihi" value="<?php echo e(request('baslangic_tarihi')); ?>"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-xs">
                                    <input type="date" name="bitis_tarihi" value="<?php echo e(request('bitis_tarihi')); ?>"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-xs">
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition font-medium text-sm">
                                Uygula
                            </button>

                            <?php if(request()->anyFilled(['durum', 'musteri_id', 'baslangic_tarihi', 'bitis_tarihi'])): ?>
                                <a href="<?php echo e(route('admin.cozum-takimlari.show', $cozumTakimi)); ?>"
                                    class="block text-center mt-2 text-xs text-gray-500 underline hover:text-gray-700">
                                    Filtreleri Temizle
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                        <div class="p-6">
                            <h3 class="font-bold text-gray-900 border-b pb-2 mb-4 flex justify-between items-center">
                                <span>Atanan Şikayetler</span>
                                <span
                                    class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded"><?php echo e($sikayetler->total()); ?>

                                    Kayıt</span>
                            </h3>

                            <?php if($sikayetler->count() > 0): ?>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 table-fixed">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">ID</th>
                                                <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri / Konu</th>
                                                <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Durum</th>
                                                <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Tarih</th>
                                                <th class="px-2 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-16">İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php $__currentLoopData = $sikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $isResolved = in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı', 'Tamamlandı', 'Talep Olarak Kapatıldı']);
                                                    // Çözüm tarihi yoksa updated_at kullan
                                                    $resolutionDate = $sikayet->musteri_cozum_tarihi ?? ($isResolved ? $sikayet->updated_at : null);
                                                ?>
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-2 py-3 whitespace-nowrap text-xs font-medium text-gray-900">
                                                        #<?php echo e($sikayet->id); ?>

                                                    </td>
                                                    <td class="px-2 py-3 text-xs text-gray-500 break-words">
                                                        <div class="flex flex-col">
                                                            <span class="block font-bold text-gray-800 mb-1">
                                                                <?php echo e($sikayet->musteri_adi); ?>

                                                            </span>
                                                            <span class="text-xs text-gray-500">
                                                                <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-2 py-3 whitespace-nowrap">
                                                        <div class="scale-90 origin-left">
                                                            <?php echo $sikayet->musteri_durum_badge; ?>

                                                        </div>
                                                    </td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-xs text-gray-500">
                                                        <div class="flex flex-col">
                                                            <span class="text-gray-900 font-medium" title="Şikayet Tarihi">
                                                                <?php echo e($sikayet->created_at->format('d.m.Y')); ?>

                                                            </span>
                                                            
                                                            <?php if($isResolved && $resolutionDate): ?>
                                                                 <span class="text-[10px] text-green-600 mt-1 flex items-center font-bold" title="Çözüm Tarihi">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                    <?php echo e($resolutionDate->format('d.m.Y')); ?>

                                                                 </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td class="px-2 py-3 whitespace-nowrap text-right text-xs font-medium">
                                                        <a href="<?php echo e(route('admin.sikayetler.show', $sikayet->id)); ?>" class="text-indigo-600 hover:text-indigo-900 font-bold uppercase">İncele</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    <?php echo e($sikayetler->links()); ?>

                                </div>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 italic">Kriterlere uygun şikayet bulunamadı.</p>
                                    <?php if(request()->anyFilled(['durum', 'musteri_id', 'baslangic_tarihi', 'bitis_tarihi'])): ?>
                                        <a href="<?php echo e(route('admin.cozum-takimlari.show', $cozumTakimi)); ?>"
                                            class="mt-2 text-indigo-600 hover:text-indigo-800 font-medium text-sm">Filtreleri
                                            Temizle</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/cozum_takimlari/show.blade.php ENDPATH**/ ?>