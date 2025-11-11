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
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800">Şikayeti Düzenle</h2>
                <p class="text-sm text-gray-500 mt-1">Şikayet No: <span class="font-semibold text-gray-700">#<?php echo e($sikayet->id); ?></span></p>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl">
                <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

                <div class="p-8">
                    <div class="mb-8 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Şikayet Bilgilerini Güncelle</h3>
                        <p class="text-sm text-gray-600">Müşteri şikayetinin bilgilerini düzenleyin ve güncellemeleri kaydedin.</p>
                    </div>

                    
                    
                    <form action="<?php echo e(route('admin.sikayetler.update', $sikayet)); ?>" method="POST" enctype="multipart/form-data" x-data="fileUploadComponent()">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="group">
                                <label for="musteri_adi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                    Müşteri Adı <span class="ml-1 text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="musteri_adi"
                                    id="musteri_adi"
                                    value="<?php echo e(old('musteri_adi', $sikayet->musteri_adi)); ?>"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900"
                                    placeholder="Müşterinin adını ve soyadını giriniz">
                                <?php $__errorArgs = ['musteri_adi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="group">
                                <label for="musteri_iletisim" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    Müşteri İletişim <span class="ml-2 text-xs text-gray-500 font-normal">(Telefon veya E-posta)</span>
                                </label>
                                <input
                                    type="text"
                                    name="musteri_iletisim"
                                    id="musteri_iletisim"
                                    value="<?php echo e(old('musteri_iletisim', $sikayet->musteri_iletisim)); ?>"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900"
                                    placeholder="0532 123 45 67 veya ornek@mail.com">
                                <?php $__errorArgs = ['musteri_iletisim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="group md:col-span-2">
                                <label class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-6.998l-6 .75m-.75-7.5l6 .75m6-.75l-6 .75M3 12h18M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                    </svg>
                                    Konum Tipi <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="mt-2 flex items-center space-x-6">
                                    <label class="inline-flex items-center cursor-pointer">
                                        
                                        <input type="radio" name="konum_tipi" value="Yurt İçi" class="form-radio text-blue-600 focus:ring-blue-500" <?php echo e(old('konum_tipi', $sikayet->konum_tipi) == 'Yurt İçi' ? 'checked' : ''); ?>>
                                        <span class="ml-2 text-sm text-gray-700">Yurt İçi</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="konum_tipi" value="Yurt Dışı" class="form-radio text-blue-600 focus:ring-blue-500" <?php echo e(old('konum_tipi', $sikayet->konum_tipi) == 'Yurt Dışı' ? 'checked' : ''); ?>>
                                        <span class="ml-2 text-sm text-gray-700">Yurt Dışı</span>
                                    </label>
                                </div>
                                <?php $__errorArgs = ['konum_tipi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="group">
                                <label for="sikayet_kategorisi_id" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                    Şikayet Kategorisi <span class="ml-1 text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="sikayet_kategorisi_id" id="sikayet_kategorisi_id" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white">
                                        <option value="">-- Kategori Seçiniz --</option>
                                        <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($kategori->id); ?>" <?php echo e(old('sikayet_kategorisi_id', $sikayet->sikayet_kategorisi_id) == $kategori->id ? 'selected' : ''); ?>>
                                                <?php echo e($kategori->ad); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></div>
                                </div>
                                <?php $__errorArgs = ['sikayet_kategorisi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="group">
                                <label for="musteri_oncelik" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Öncelik <span class="ml-1 text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <?php $currentOncelik = old('musteri_oncelik', $sikayet->musteri_oncelik); ?>
                                    <select
                                        name="musteri_oncelik"
                                        id="musteri_oncelik"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white">
                                        <option value="Düşük" <?php echo e($currentOncelik == 'Düşük' ? 'selected' : ''); ?>>🟢 Düşük</option>
                                        <option value="Normal" <?php echo e($currentOncelik == 'Normal' ? 'selected' : ''); ?>>🟡 Normal</option>
                                        <option value="Yüksek" <?php echo e($currentOncelik == 'Yüksek' ? 'selected' : ''); ?>>🟠 Yüksek</option>
                                        <option value="Acil" <?php echo e($currentOncelik == 'Acil' ? 'selected' : ''); ?>>🔴 Acil</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <?php $__errorArgs = ['musteri_oncelik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="group">
                                <label for="musteri_sikayet_tarihi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    Şikayet Tarihi <span class="ml-1 text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    name="musteri_sikayet_tarihi"
                                    id="musteri_sikayet_tarihi"
                                    value="<?php echo e(old('musteri_sikayet_tarihi', $sikayet->musteri_sikayet_tarihi)); ?>"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900">
                                <?php $__errorArgs = ['musteri_sikayet_tarihi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-span-2 group">
                                <label for="musteri_sikayet_konusu" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    Şikayet Konusu <span class="ml-1 text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="musteri_sikayet_konusu"
                                    id="musteri_sikayet_konusu"
                                    value="<?php echo e(old('musteri_sikayet_konusu', $sikayet->musteri_sikayet_konusu)); ?>"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900"
                                    placeholder="Şikayetin kısa bir özeti">
                                <?php $__errorArgs = ['musteri_sikayet_konusu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-span-2 group">
                                <label for="musteri_sikayet_detayi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                    Şikayet Detayı <span class="ml-1 text-red-500">*</span>
                                </label>
                                <textarea
                                    name="musteri_sikayet_detayi"
                                    id="musteri_sikayet_detayi"
                                    rows="5"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900 resize-y"
                                    placeholder="Şikayetin detaylı açıklamasını giriniz..."><?php echo e(old('musteri_sikayet_detayi', $sikayet->musteri_sikayet_detayi)); ?></textarea>
                                <p class="mt-2 text-xs text-gray-500">Şikayetle ilgili tüm detayları mümkün olduğunca açıklayıcı bir şekilde yazınız.</p>
                                <?php $__errorArgs = ['musteri_sikayet_detayi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-span-2 group">
                                <label class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                    Mevcut Kanıtlar
                                </label>
                                <?php if($sikayet->dosyalar->isNotEmpty()): ?>
                                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                        <?php $__currentLoopData = $sikayet->dosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div x-show="!deletedFileIds.includes(<?php echo e($dosya->id); ?>)" class="relative group bg-gray-100 rounded-lg overflow-hidden border">
                                                <?php if(Str::startsWith($dosya->mime_tipi, 'image/')): ?>
                                                    
                                                    <a href="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" data-fancybox="gallery" data-caption="<?php echo e($dosya->orijinal_adi); ?>">
                                                        <img src="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" alt="<?php echo e($dosya->orijinal_adi); ?>" class="object-cover h-24 w-full">
                                                    </a>
                                                <?php else: ?>
                                                    
                                                    <a href="<?php echo e(asset('storage/' . $dosya->dosya_yolu)); ?>" target="_blank" class="flex flex-col items-center justify-center h-24 bg-gray-200 p-2">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    </a>
                                                <?php endif; ?>
                                                <div class="absolute bottom-0 left-0 right-0 p-1 bg-black bg-opacity-50">
                                                    <p class="text-xs text-white truncate" title="<?php echo e($dosya->orijinal_adi); ?>"><?php echo e($dosya->orijinal_adi); ?></p>
                                                </div>
                                                
                                                
                                                
                                                <button 
                                                    type="button" 
                                                    @click.prevent="markForDeletion(<?php echo e($dosya->id); ?>, $event.target)"
                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-75 group-hover:opacity-100 transition-opacity"
                                                    title="Silmek için işaretle">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                </button>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-2 text-center py-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-500">Bu şikayet için herhangi bir kanıt eklenmemiş.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-span-2 group">
                                <label for="dosyalar" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                    Yeni Kanıt Ekle
                                </label>
                                <input
                                    type="file"
                                    name="dosyalar[]"
                                    id="dosyalar"
                                    multiple
                                    accept="image/*,video/mp4,application/pdf,.doc,.docx,.xls,.xlsx"
                                    @change="updatePreviews($event)"
                                    x-ref="fileInput"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:transition-colors file:cursor-pointer border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="mt-2 text-xs text-gray-500">Yeni resim, PDF, Word, Video ekleyebilirsiniz (Mobil/Masaüstü). Maksimum: 10MB.</p>
                                <?php $__errorArgs = ['dosyalar.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                <div x-show="previews.length > 0" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    <template x-for="(preview, index) in previews" :key="index">
                                        <div class="relative group bg-gray-100 rounded-lg overflow-hidden border">
                                            <template x-if="preview.url">
                                                <img :src="preview.url" class="object-cover h-24 w-full" alt="Önizleme">
                                            </template>
                                            <template x-if="!preview.url">
                                                <div class="flex flex-col items-center justify-center h-24 bg-gray-200 p-2">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <p class="text-xs text-gray-500 mt-1 truncate" x-text="preview.name"></p>
                                                </div>
                                            </template>
                                            <div class="absolute bottom-0 left-0 right-0 p-1 bg-black bg-opacity-50">
                                                <p class="text-xs text-white truncate" x-text="preview.name"></p>
                                            </div>
                                            
                                            <button @click.prevent="removePreview(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-75 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
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
                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                            <a href="<?php echo e(route('admin.sikayetler.index')); ?>" class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                İptal
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>
                    

                </div>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    
    
    <script>
        // Fancybox'ı başlat
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {
                // Özel ayarlar
            });
        });

        // Alpine.js Fonksiyonu
        function fileUploadComponent() {
            return {
                previews: [], // Yeni seçilen dosyaların önizlemeleri
                files: [],    // Yeni seçilen dosyaların listesi
                
                // Silinmek üzere işaretlenen MEVCUT dosyaların ID'leri
                deletedFileIds: [], 

                // Mevcut dosyaları silmek için işaretleme
                markForDeletion(id, buttonElement) {
                    // 1. Kullanıcıdan onayı al
                    if (!confirm('Bu dosyayı silmek istediğinizden emin misiniz?\n(Değişiklikleri Kaydet butonuna basana kadar kalıcı olarak silinmeyecektir)')) {
                        return; // İptal'e basarsa işlemi durdur
                    }

                    // 2. ID'yi silinecekler listesine ekle
                    this.deletedFileIds.push(id);

                    // 3. Görsel olarak dosyayı gizle (x-show zaten yapar ama animasyon ekleyelim)
                    let wrapper = buttonElement.closest('.relative.group');
                    if (wrapper) {
                        wrapper.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        wrapper.style.opacity = '0';
                        wrapper.style.transform = 'scale(0.9)';
                        setTimeout(() => wrapper.style.display = 'none', 300); // Animasyon sonrası gizle
                    }
                },

                // Yeni dosya seçildiğinde önizlemeyi günceller
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

                    // Input'un file listesini GÜNCELLE
                    const dataTransfer = new DataTransfer();
                    this.files.forEach(file => dataTransfer.items.add(file));
                    this.$refs.fileInput.files = dataTransfer.files;
                },

                // Yeni eklenen dosya önizlemesini kaldırır
                removePreview(index) {
                    this.previews.splice(index, 1);
                    this.files.splice(index, 1);

                    // Dosyaları DataTransfer ile yeniden oluşturup input'a atama
                    const dataTransfer = new DataTransfer();
                    this.files.forEach(file => dataTransfer.items.add(file));
                    
                    // Tek input'un dosya listesini güncelle
                    this.$refs.fileInput.files = dataTransfer.files;
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