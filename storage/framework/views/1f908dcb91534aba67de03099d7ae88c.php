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
    <?php $__env->startPush('pageTitle'); ?>
        Şikayeti Düzenle | 
    <?php $__env->stopPush(); ?>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl md:text-2xl text-gray-800">Şikayeti Düzenle</h2>
                <p class="text-sm text-gray-500 mt-1">Şikayet No: <span
                        class="font-semibold text-gray-700">#<?php echo e($sikayet->id); ?></span></p>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <div class="py-6 md:py-12 bg-gradient-to-br from-gray-50 to-gray-100 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-2xl rounded-xl sm:rounded-2xl">
                <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

                <div class="p-4 sm:p-8">
                    <div class="mb-6 md:mb-8 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Şikayet Bilgilerini Güncelle</h3>
                        <p class="text-sm text-gray-600">Müşteri şikayetinin bilgilerini düzenleyin ve güncellemeleri
                            kaydedin.</p>
                    </div>

                    
                    
                    <form action="<?php echo e(route('admin.sikayetler.update', $sikayet)); ?>" method="POST"
                        enctype="multipart/form-data" x-data="fileUploadComponent()">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-6 gap-y-8" x-data="categorySystem()"
                            x-init="init()">

                            
                            
                            <div class="lg:col-span-2 mb-6">
                                
                                
                                

                                <?php if($sikayet->customer_id): ?>
                                    
                                    <div class="lg:col-span-2">
                                        <?php if($tamYetki): ?>
                                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.sikayet-musteri-secimi', ['preselectedCustomerId' => $sikayet->customer_id,'selectedRepId' => $sikayet->yetkili_user_id,'selectedEkRepIds' => $sikayet->ekYetkililer->pluck('id')->toArray()]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1158601976-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                                        <?php else: ?>
                                            
                                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-2">
                                                <div class="flex items-start">
                                                    <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600 mr-3">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4
                                                            class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                            Müşteri Firması</h4>
                                                        <p class="text-sm font-semibold text-gray-800">
                                                            <?php echo e($sikayet->customer->name ?? 'Belirtilmemiş'); ?></p>
                                                        <input type="hidden" name="customer_id"
                                                            value="<?php echo e($sikayet->customer_id); ?>">
                                                        <input type="hidden" name="yetkili_user_id"
                                                            value="<?php echo e($sikayet->yetkili_user_id); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    
                                    
                                    <div class="lg:col-span-2 bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <h4 class="text-sm font-bold text-yellow-800">Eski Kayıt Tipi</h4>
                                                <p class="text-xs text-yellow-700 mt-1">Bu şikayet, müşteri veritabanı
                                                    sistemine geçilmeden önce oluşturulmuş. Aşağıdaki bilgiler metin olarak
                                                    saklanmaktadır.</p>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    
                                    <div class="group flex flex-col mb-6">
                                        <label for="musteri_adi"
                                            class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>Müşteri Adı <span class="ml-1 text-red-500">*</span></span>
                                        </label>
                                        <input type="text" name="musteri_adi" id="musteri_adi"
                                            value="<?php echo e(old('musteri_adi', $sikayet->musteri_adi)); ?>" required
                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-4 py-3 text-gray-900 block <?php echo e(!$tamYetki ? 'bg-gray-100 cursor-not-allowed' : ''); ?>"
                                            <?php echo e(!$tamYetki ? 'readonly' : ''); ?>>
                                        <?php $__errorArgs = ['musteri_adi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    
                                    <div class="group flex flex-col mb-6">
                                        <label for="musteri_iletisim"
                                            class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                            <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                            <span>Müşteri İletişim <span
                                                    class="text-xs text-gray-500 font-normal ml-1">(Telefon/E-posta)</span></span>
                                        </label>
                                        <input type="text" name="musteri_iletisim" id="musteri_iletisim"
                                            value="<?php echo e(old('musteri_iletisim', $sikayet->musteri_iletisim)); ?>"
                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-4 py-3 text-gray-900 block <?php echo e(!$tamYetki ? 'bg-gray-100 cursor-not-allowed' : ''); ?>"
                                            <?php echo e(!$tamYetki ? 'readonly' : ''); ?>>
                                        <?php $__errorArgs = ['musteri_iletisim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span
                                        class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php
                                $usr = auth()->user();
                                $onlyYurtIci = $usr->hasRole('Müşteri Şikayeti Kurulu - Yurt İçi') && !$usr->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt Dışı']);
                                $onlyYurtDisi = $usr->hasRole('Müşteri Şikayeti Kurulu - Yurt Dışı') && !$usr->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi']);
                            ?>

                            <div class="group flex flex-col lg:col-span-2 mb-6">
                                <label class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 6.75V15m6-6v8.25m.503-6.998l-6 .75m-.75-7.5l6 .75m6-.75l-6 .75M3 12h18M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                    </svg>
                                    <span>Konum Tipi <span class="text-red-500 ml-1">*</span></span>
                                </label>
                                <div class="flex flex-wrap items-center gap-4">
                                    <?php if($tamYetki): ?>
                                        <?php if($onlyYurtIci || $onlyYurtDisi): ?>
                                            <input type="hidden" name="konum_tipi" value="<?php echo e($sikayet->konum_tipi); ?>">
                                        <?php endif; ?>
                                        <label
                                            class="inline-flex items-center <?php echo e($onlyYurtDisi ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'); ?> bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                            <input type="radio" name="konum_tipi" value="Yurt İçi"
                                                class="form-radio w-5 h-5 text-blue-600 focus:ring-blue-500" 
                                                <?php echo e(old('konum_tipi', $sikayet->konum_tipi) == 'Yurt İçi' ? 'checked' : ''); ?>

                                                <?php echo e(($onlyYurtIci || $onlyYurtDisi) ? 'disabled' : ''); ?>>
                                            <span class="ml-2 text-sm text-gray-700 font-medium">Yurt İçi</span>
                                        </label>
                                        <label
                                            class="inline-flex items-center <?php echo e($onlyYurtIci ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'); ?> bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                            <input type="radio" name="konum_tipi" value="Yurt Dışı"
                                                class="form-radio w-5 h-5 text-blue-600 focus:ring-blue-500" 
                                                <?php echo e(old('konum_tipi', $sikayet->konum_tipi) == 'Yurt Dışı' ? 'checked' : ''); ?>

                                                <?php echo e(($onlyYurtIci || $onlyYurtDisi) ? 'disabled' : ''); ?>>
                                            <span class="ml-2 text-sm text-gray-700 font-medium">Yurt Dışı</span>
                                        </label>
                                    <?php else: ?>
                                        
                                        <div
                                            class="bg-gray-100 px-4 py-2.5 rounded-lg border border-gray-200 w-full text-gray-700 font-medium cursor-not-allowed">
                                            <?php echo e($sikayet->konum_tipi ?? 'Belirtilmemiş'); ?>

                                        </div>
                                        <input type="hidden" name="konum_tipi" value="<?php echo e($sikayet->konum_tipi); ?>">
                                    <?php endif; ?>
                                </div>
                                <?php $__errorArgs = ['konum_tipi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            
                            <div class="lg:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                
                                <div class="group flex flex-col">
                                    <label for="sikayet_kategorisi_id"
                                        class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z">
                                            </path>
                                        </svg>
                                        <span>Şikayet Kategorisi <span class="ml-1 text-red-500">*</span></span>
                                    </label>
                                    <div class="relative">
                                        <?php if($tamYetki): ?>
                                            <select name="sikayet_kategorisi_id" id="sikayet_kategorisi_id" required
                                                x-model="selectedCategory" @change="fetchSubCategories(false)"
                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white block">
                                                <option value="">-- Kategori Seçiniz --</option>
                                                <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($kategori->id); ?>"><?php echo e($kategori->ad); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg></div>
                                        <?php else: ?>
                                            
                                            <div
                                                class="bg-gray-100 pl-4 pr-10 py-3 rounded-lg border border-gray-200 w-full text-gray-700 cursor-not-allowed">
                                                <?php echo e($sikayet->sikayetKategori->ad ?? 'Belirtilmemiş'); ?>

                                            </div>
                                            <input type="hidden" name="sikayet_kategorisi_id"
                                                value="<?php echo e($sikayet->sikayet_kategorisi_id); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <?php $__errorArgs = ['sikayet_kategorisi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span
                                    class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                
                                <div class="group flex flex-col" x-show="subCategories.length > 0 || showOtherOption"
                                    style="display: none;">
                                    <label for="sikayet_alt_kategori_id"
                                        class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                        Alt Kategori <span class="ml-1 text-red-500">*</span>
                                        <span x-show="isLoading"
                                            class="ml-2 text-xs text-gray-400">(Yükleniyor...)</span>
                                    </label>
                                    <div class="relative">
                                        
                                        <select name="sikayet_alt_kategori_id" id="sikayet_alt_kategori_id"
                                            x-model="selectedSubCategory"
                                            :required="subCategories.length > 0 || showOtherOption"
                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white block">
                                            <option value="">-- Alt Kategori Seçiniz --</option>
                                            <template x-for="sub in subCategories" :key="sub.id">
                                                <option :value="sub.id" x-text="sub.ad"></option>
                                            </template>
                                            <template x-if="showOtherOption">
                                                <option value="other">Diğer / Belirtilmemiş</option>
                                            </template>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg></div>
                                    </div>
                                    <?php $__errorArgs = ['sikayet_alt_kategori_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span
                                    class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                
                                <div class="group flex flex-col lg:col-span-2 bg-gray-50 p-4 rounded border border-gray-200"
                                    x-show="selectedSubCategory === 'other'" style="display: none;" x-transition>
                                    <label for="sikayet_alt_kategori_diger"
                                        class="block text-sm font-medium text-gray-800 mb-1">
                                        <span x-text="otherLabel"></span> <span class="ml-1 text-red-500">*</span>
                                    </label>
                                    <input type="text" name="sikayet_alt_kategori_diger" id="sikayet_alt_kategori_diger"
                                        value="<?php echo e(old('sikayet_alt_kategori_diger', $sikayet->sikayet_alt_kategori_diger)); ?>"
                                        :required="selectedSubCategory === 'other'"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 block">
                                    <?php $__errorArgs = ['sikayet_alt_kategori_diger'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span
                                    class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            
                            
                            <div class="group flex flex-col mb-6">
                                <label for="musteri_oncelik"
                                    class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Öncelik <span class="ml-1 text-red-500">*</span></span>
                                </label>
                                <div class="relative">
                                    <?php $currentOncelik = old('musteri_oncelik', $sikayet->musteri_oncelik); ?>
                                    <?php if($tamYetki): ?>
                                        <select name="musteri_oncelik" id="musteri_oncelik"
                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white block">
                                            <option value="Düşük" <?php echo e($currentOncelik == 'Düşük' ? 'selected' : ''); ?>>🟢 Düşük
                                            </option>
                                            <option value="Normal" <?php echo e($currentOncelik == 'Normal' ? 'selected' : ''); ?>>🟡
                                                Normal</option>
                                            <option value="Yüksek" <?php echo e($currentOncelik == 'Yüksek' ? 'selected' : ''); ?>>🟠
                                                Yüksek</option>
                                            <option value="Acil" <?php echo e($currentOncelik == 'Acil' ? 'selected' : ''); ?>>🔴 Acil
                                            </option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg></div>
                                    <?php else: ?>
                                        <div
                                            class="bg-gray-100 pl-4 py-3 rounded-lg border border-gray-200 w-full text-gray-700 font-medium flex items-center cursor-not-allowed">
                                            <?php if($currentOncelik == 'Düşük'): ?> 🟢 Düşük <?php endif; ?>
                                            <?php if($currentOncelik == 'Normal'): ?> 🟡 Normal <?php endif; ?>
                                            <?php if($currentOncelik == 'Yüksek'): ?> 🟠 Yüksek <?php endif; ?>
                                            <?php if($currentOncelik == 'Acil'): ?> 🔴 Acil <?php endif; ?>
                                        </div>
                                        <input type="hidden" name="musteri_oncelik" value="<?php echo e($sikayet->musteri_oncelik); ?>">
                                    <?php endif; ?>
                                </div>
                                <?php $__errorArgs = ['musteri_oncelik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            
                            <div class="group flex flex-col mb-6">
                                <label for="musteri_sikayet_tarihi"
                                    class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Şikayet Tarihi <span class="ml-1 text-red-500">*</span></span>
                                </label>
                                <?php if($tamYetki): ?>
                                    <input type="date" name="musteri_sikayet_tarihi" id="musteri_sikayet_tarihi"
                                        value="<?php echo e(old('musteri_sikayet_tarihi', $sikayet->musteri_sikayet_tarihi->format('Y-m-d'))); ?>"
                                        required
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-4 pr-4 py-3 text-gray-900 block">
                                <?php else: ?>
                                    <div
                                        class="bg-gray-100 pl-4 py-3 rounded-lg border border-gray-200 w-full text-gray-700 cursor-not-allowed">
                                        <?php echo e($sikayet->musteri_sikayet_tarihi->format('d.m.Y')); ?>

                                    </div>
                                    <input type="hidden" name="musteri_sikayet_tarihi"
                                        value="<?php echo e($sikayet->musteri_sikayet_tarihi->format('Y-m-d')); ?>">
                                <?php endif; ?>
                                <?php $__errorArgs = ['musteri_sikayet_tarihi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span
                                class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            
                            <div class="group flex flex-col lg:col-span-2 mb-6">
                                <label for="musteri_sikayet_konusu"
                                    class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Şikayet Konusu <span class="ml-1 text-red-500">*</span></span>
                                </label>
                                <input type="text" name="musteri_sikayet_konusu" id="musteri_sikayet_konusu"
                                    value="<?php echo e(old('musteri_sikayet_konusu', $sikayet->musteri_sikayet_konusu)); ?>"
                                    required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-4 pr-4 py-3 text-gray-900 block"
                                    placeholder="Şikayetin kısa bir özeti">
                                <?php $__errorArgs = ['musteri_sikayet_konusu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span
                                class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            
                            <div class="group flex flex-col lg:col-span-2 mb-6">
                                <label for="musteri_sikayet_detayi"
                                    class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Şikayet Detayı <span class="ml-1 text-red-500">*</span></span>
                                </label>
                                <textarea name="musteri_sikayet_detayi" id="musteri_sikayet_detayi" rows="5" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-4 pr-4 py-3 text-gray-900 block resize-y"
                                    placeholder="Şikayetin detaylı açıklamasını giriniz..."><?php echo e(old('musteri_sikayet_detayi', $sikayet->musteri_sikayet_detayi)); ?></textarea>
                                <?php $__errorArgs = ['musteri_sikayet_detayi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span
                                class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            
                            
                            
                            
                            
                            <div class="lg:col-span-2 bg-blue-50 rounded-xl p-6 border border-blue-100 mb-6"
                                id="uretim_detaylari_container">

                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-base font-bold text-blue-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Üretim ve Ürün Detayları
                                    </h4>
                                    <button type="button" @click="addDetail()"
                                        class="text-xs flex items-center gap-1 bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Yeni Satır Ekle
                                    </button>
                                </div>

                                <template x-for="(detail, index) in technicalDetails" :key="index">
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4 pb-4 border-b border-blue-200 last:border-0 last:pb-0 relative animate-fade-in-down">

                                        
                                        <div class="md:col-span-12 flex justify-end"
                                            x-show="technicalDetails.length > 1">
                                            <button type="button" @click="removeDetail(index)"
                                                class="text-red-500 hover:text-red-700 text-xs font-semibold flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Sil
                                            </button>
                                        </div>

                                        
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Lot
                                                Numarası</label>
                                            <input type="text" name="lot_no[]" x-model="detail.lot_no"
                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 text-sm"
                                                placeholder="Örn: LT-2024-001">
                                        </div>

                                        
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Makine /
                                                Hat</label>
                                            <select name="machine_id[]" x-model="detail.machine_id"
                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 bg-white text-sm">
                                                <option value="">Seçiniz</option>
                                                <?php $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($machine->id); ?>">
                                                        <?php echo e($machine->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>

                                        
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Hammadde</label>
                                            <select name="genel_hammadde_id[]" x-model="detail.genel_hammadde_id"
                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 bg-white text-sm">
                                                <option value="">Seçiniz</option>
                                                <?php $__currentLoopData = $genelHammaddeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hammadde): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($hammadde->id); ?>">
                                                        <?php echo e($hammadde->ad); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>

                                        
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Versiyon</label>
                                            <select name="urun_versiyonu_id[]" x-model="detail.urun_versiyonu_id"
                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 bg-white text-sm">
                                                <option value="">Seçiniz</option>
                                                <?php $__currentLoopData = $urunVersiyonlari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $versiyon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($versiyon->id); ?>">
                                                        <?php echo e($versiyon->ad); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            
                            
                            <div class="group flex flex-col lg:col-span-2 mb-6">
                                <label class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Mevcut Kanıtlar</span>
                                </label>
                                <?php if($sikayet->dosyalar->isNotEmpty()): ?>
                                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                        <?php $__currentLoopData = $sikayet->dosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div x-show="!deletedFileIds.includes(<?php echo e($dosya->id); ?>)"
                                                class="relative group bg-gray-100 rounded-lg overflow-hidden border">
                                                <?php if(Str::startsWith($dosya->mime_tipi, 'image/')): ?>
                                                    <a href="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" data-fancybox="gallery"
                                                        data-caption="<?php echo e($dosya->orijinal_adi); ?>">
                                                        <img src="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>"
                                                            alt="<?php echo e($dosya->orijinal_adi); ?>" class="object-cover h-24 w-full">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" target="_blank"
                                                        class="flex flex-col items-center justify-center h-24 bg-gray-200 p-2">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                <?php endif; ?>
                                                <div class="absolute bottom-0 left-0 right-0 p-1 bg-black bg-opacity-50">
                                                    <p class="text-xs text-white truncate"><?php echo e($dosya->orijinal_adi); ?></p>
                                                </div>
                                                <?php if($tamYetki || $dosya->yukleyen_id == auth()->id()): ?>
                                                    <button type="button"
                                                        @click.prevent="markForDeletion(<?php echo e($dosya->id); ?>, $event.target)"
                                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-75 group-hover:opacity-100 transition-opacity"
                                                        title="Silmek için işaretle">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-2 text-center py-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-500">Bu şikayet için herhangi bir kanıt eklenmemiş.</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            
                            
                            <div class="group flex flex-col lg:col-span-2 mb-6">
                                <label for="dosyalar" class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Yeni Kanıt Ekle</span>
                                </label>
                                <input type="file" name="dosyalar[]" id="dosyalar" multiple
                                    accept="image/*,video/mp4,application/pdf,.doc,.docx,.xls,.xlsx"
                                    @change="updatePreviews($event)" x-ref="fileInput"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:transition-colors file:cursor-pointer border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="mt-2 text-xs text-gray-500">Yeni resim, PDF, Word, Video ekleyebilirsiniz
                                    (Mobil/Masaüstü). Maksimum: 10MB.</p>
                                <?php $__errorArgs = ['dosyalar.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                <div x-show="previews.length > 0"
                                    class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    <template x-for="(preview, index) in previews" :key="index">
                                        <div class="relative group bg-gray-100 rounded-lg overflow-hidden border">
                                            <template x-if="preview.url">
                                                <img :src="preview.url" class="object-cover h-24 w-full" alt="Önizleme">
                                            </template>
                                            <template x-if="!preview.url">
                                                <div
                                                    class="flex flex-col items-center justify-center h-24 bg-gray-200 p-2">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    <p class="text-xs text-gray-500 mt-1 truncate"
                                                        x-text="preview.name"></p>
                                                </div>
                                            </template>
                                            <div class="absolute bottom-0 left-0 right-0 p-1 bg-black bg-opacity-50">
                                                <p class="text-xs text-white truncate" x-text="preview.name"></p>
                                            </div>
                                            <button @click.prevent="removePreview(index)"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-0.5 opacity-75 group-hover:opacity-100 transition-opacity focus:outline-none focus:ring-2 focus:ring-red-600">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>

                        <div x-show="deletedFileIds.length > 0" class="hidden">
                            <template x-for="id in deletedFileIds" :key="id">
                                <input type="hidden" name="dosyalar_sil[]" :value="id">
                            </template>
                        </div>

                        
                        <div
                            class="flex flex-col-reverse sm:flex-row items-center justify-between mt-8 pt-6 border-t border-gray-200 gap-4">
                            <?php
                                $previousUrl = url()->previous();
                                $currentUrl = url()->current();
                                $defaultBack = auth()->user()->hasRole('Müşteri|Müşteri Temsilcisi') ? route('dashboard') : route('admin.sikayetler.index');
                                $isInternalReferer = $previousUrl && $previousUrl !== $currentUrl && str_contains($previousUrl, request()->getHttpHost());
                                $backUrl = $isInternalReferer ? $previousUrl : $defaultBack;
                            ?>
                            <a href="<?php echo e($backUrl); ?>"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                İptal
                            </a>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>

                
                <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Superadmin|Yönetim')): ?>
                <div class="p-4 sm:p-8 bg-white border-t border-gray-100 rounded-b-xl sm:rounded-b-2xl">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Süreç Geçmişi & Loglar
                    </h3>
                    
                    <?php if(isset($loglar) && $loglar->count() > 0): ?>
                        <div class="relative pl-4 sm:pl-6 border-l-2 border-indigo-100 space-y-8 before:absolute before:inset-0 before:ml-[15px] sm:before:ml-[23px] before:-translate-x-px md:before:mx-auto md:before:translate-x-0">
                            <?php $__currentLoopData = $loglar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="relative flex items-start gap-4 group">
                                    
                                    <div class="absolute -left-[30px] sm:-left-[38px] mt-1 h-8 w-8 rounded-full border-4 border-white bg-white flex items-center justify-center shadow-sm z-10 transition-transform group-hover:scale-110">
                                        <?php if($log->eylem == 'Oluşturuldu'): ?>
                                            <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                            </div>
                                        <?php elseif($log->eylem == 'Düzenlendi' || $log->eylem == 'Bağlandı'): ?>
                                            <div class="h-6 w-6 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </div>
                                        <?php else: ?>
                                            <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <div class="flex-1 min-w-0 bg-gray-50 rounded-xl p-4 sm:p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2 sm:mb-1 gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900 text-sm sm:text-base"><?php echo e(optional($log->user)->name ?? 'Bilinmeyen Kullanıcı'); ?></span>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold
                                                    <?php echo e($log->eylem == 'Oluşturuldu' ? 'bg-emerald-100 text-emerald-800' : ''); ?>

                                                    <?php echo e($log->eylem == 'Düzenlendi' ? 'bg-amber-100 text-amber-800' : ''); ?>

                                                    <?php echo e($log->eylem == 'Bağlandı' ? 'bg-purple-100 text-purple-800' : ''); ?>

                                                    <?php echo e(!in_array($log->eylem, ['Oluşturuldu', 'Düzenlendi', 'Bağlandı']) ? 'bg-blue-100 text-blue-800' : ''); ?>

                                                ">
                                                    <?php echo e($log->eylem); ?>

                                                </span>
                                            </div>
                                            <div class="text-xs sm:text-sm text-gray-500 font-medium flex items-center whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <?php echo e($log->created_at->format('d.m.Y H:i')); ?>

                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-2 sm:mt-1 leading-relaxed">
                                            <?php echo e($log->aciklama); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="p-6 md:p-8 bg-gray-50 border border-gray-100 rounded-xl flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h4 class="text-base font-semibold text-gray-900 mb-1">Henüz Kayıt Yok</h4>
                            <p class="text-sm text-gray-500 max-w-sm">Bu şikayet için herhangi bir düzenleme veya işlem geçmişi bulunmuyor.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {});
        });

        function fileUploadComponent() {
            return {
                previews: [],
                files: [],
                deletedFileIds: [], 
                markForDeletion(id, buttonElement) {
                    if (!confirm('Bu dosyayı silmek istediğinizden emin misiniz?\n(Değişiklikleri Kaydet butonuna basana kadar kalıcı olarak silinmeyecektir)')) {
                        return;
                    }
                    this.deletedFileIds.push(id);
                    let wrapper = buttonElement.closest('.relative.group');
                    if (wrapper) {
                        wrapper.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        wrapper.style.opacity = '0';
                        wrapper.style.transform = 'scale(0.9)';
                        setTimeout(() => wrapper.style.display = 'none', 300);
                    }
                },
                updatePreviews(event) {
                    let selectedFiles = Array.from(event.target.files);
                    this.files = this.files.concat(selectedFiles);
                    selectedFiles.forEach(file => {
                        let reader = new FileReader();
                        reader.onload = (e) => {
                            let preview = {
                                url: file.type.startsWith('image/') ? e.target.result : null,
                                name: file.name
                            };
                            this.previews.push(preview);
                        };
                        reader.readAsDataURL(file);
                    });
                    const dataTransfer = new DataTransfer();
                    this.files.forEach(file => dataTransfer.items.add(file));
                    this.$refs.fileInput.files = dataTransfer.files;
                },
                removePreview(index) {
                    this.previews.splice(index, 1);
                    this.files.splice(index, 1);
                    const dataTransfer = new DataTransfer();
                    this.files.forEach(file => dataTransfer.items.add(file));
                    this.$refs.fileInput.files = dataTransfer.files;
                }
            }
        }

        function categorySystem() {
            return {
                selectedCategory: <?php echo json_encode(old('sikayet_kategorisi_id', $sikayet->sikayet_kategorisi_id ?? ''), 512) ?>,
                selectedSubCategory: '', // Başlangıçta boş, fetch içinde dolacak
                otherText: <?php echo json_encode(old('sikayet_alt_kategori_diger', $sikayet->sikayet_alt_kategori_diger ?? ''), 512) ?>,
                
                subCategories: [],
                showOtherOption: false,
                otherLabel: 'Diğer Açıklama',
                isLoading: false,

                // Veritabanından gelen asıl değerler
                dbSubId: <?php echo json_encode($sikayet->sikayet_alt_kategori_id, 15, 512) ?>,
                dbOther: <?php echo json_encode($sikayet->sikayet_alt_kategori_diger, 15, 512) ?>,

                // YENİ: TEKNİK DETAYLAR
                technicalDetails: [],

                init() {
                    // Sayfa açıldığında kategori varsa alt kategorileri çek
                    if (this.selectedCategory) {
                        this.fetchSubCategories(true); // true = Seçimi Koru
                    }

                    // TEKNİK DETAYLARI DOLDUR
                    // 1. Önce validation hatasından dönen veri var mı?
                    var oldLots = <?php echo json_encode(old('lot_no', []), 512) ?>;
                    var oldMachines = <?php echo json_encode(old('machine_id', []), 512) ?>;
                    var oldHammaddeler = <?php echo json_encode(old('genel_hammadde_id', []), 512) ?>;
                    var oldVersiyonlar = <?php echo json_encode(old('urun_versiyonu_id', []), 512) ?>;

                    this.technicalDetails = []; // Temiz başla

                    if (Array.isArray(oldLots) && oldLots.length > 0) {
                        for (let i = 0; i < oldLots.length; i++) {
                            // Sadece en az bir alanı doluysa veya tek satırsa ekle
                            this.technicalDetails.push({
                                lot_no: oldLots[i] || '',
                                machine_id: oldMachines[i] ?? '',
                                genel_hammadde_id: oldHammaddeler[i] ?? '',
                                urun_versiyonu_id: oldVersiyonlar[i] ?? ''
                            });
                        }
                    } else {
                        // 2. Yoksa Veritabanındaki kayıtları çek
                        var dbDetails = <?php echo json_encode($sikayet->teknikDetaylar, 15, 512) ?> || [];
                        
                        if (dbDetails && dbDetails.length > 0) {
                            this.technicalDetails = JSON.parse(JSON.stringify(dbDetails)); // Deep copy
                        }
                    }

                    // 3. Hala boşsa veya hiç kayıt yoksa tam olarak 1 boş satır ekle
                    if (this.technicalDetails.length === 0) {
                        this.addDetail();
                    }

                },

                addDetail() {
                    this.technicalDetails.push({
                        lot_no: '',
                        machine_id: '',
                        genel_hammadde_id: '',
                        urun_versiyonu_id: ''
                    });
                },

                removeDetail(index) {
                    if (this.technicalDetails.length > 1) {
                        this.technicalDetails.splice(index, 1);
                    }
                },

                fetchSubCategories(keepSelection = false) {
                    if (!this.selectedCategory) {
                        this.subCategories = [];
                        this.selectedSubCategory = '';
                        this.showOtherOption = false;
                        return;
                    }

                    this.isLoading = true;
                    var path = window.location.pathname;
                    var rootUrl = window.location.origin;
                    if (path.indexOf('/iaa/') > -1) {
                        rootUrl += '/iaa';
                    }
                    var apiUrl = rootUrl + '/api/get-alt-kategoriler/' + this.selectedCategory;

                    fetch(apiUrl)
                        .then(res => {
                            if (!res.ok) throw new Error("API Hatası");
                            return res.json();
                        })
                        .then(data => {
                            this.subCategories = data.alt_kategoriler;
                            this.showOtherOption = data.diger_goster;
                            this.otherLabel = data.diger_baslik || 'Lütfen detay belirtiniz:';

                            // === KRİTİK DÜZELTME BURASI ===
                            // Veriler yüklendikten SONRA seçimi yapıyoruz
                            if (keepSelection) {
                                // 1. Önce old() var mı? (Validation hatasından dönüş)
                                let oldSub = <?php echo json_encode(old('sikayet_alt_kategori_id'), 15, 512) ?>;
                                
                                if (oldSub) {
                                    this.selectedSubCategory = oldSub === 'other' ? 'other' : oldSub;
                                } 
                                // 2. Yoksa Veritabanındaki ID var mı?
                                else if (this.dbSubId) {
                                    this.selectedSubCategory = this.dbSubId;
                                } 
                                // 3. O da yoksa "Diğer" dolu mu?
                                else if (this.dbOther) {
                                    this.selectedSubCategory = 'other';
                                }
                            } else {
                                // Kullanıcı ana kategoriyi değiştirdiyse sıfırla
                                this.selectedSubCategory = '';
                            }
                        })
                        .catch(err => console.error("Kategori yükleme hatası:", err))
                        .finally(() => this.isLoading = false);
                }
            }
        }
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/sikayetler/edit.blade.php ENDPATH**/ ?>