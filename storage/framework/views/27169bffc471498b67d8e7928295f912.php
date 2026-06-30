<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wrapperClass' => 'w-[90%]']); ?>
    
    
    
    <script>
        // Bu kod, notification-handler.js dosyasının hata verip diğer kodları durdurmasını engeller.
        // Eğer değişken tanımlı değilse, boş bir nesne olarak tanımlarız.
        if (typeof window.notificationApiUrls === 'undefined') {
            window.notificationApiUrls = {
                index: '',
                unreadCount: '',
                markRead: ''
            };
        }
    </script>
    

    
    <div class="mb-8 pb-6 border-b border-gray-200">
        <div class="flex items-center space-x-3 mb-2">
            <div class="p-2 bg-red-100 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <h2 class="font-bold text-2xl text-gray-800">Müşteri Şikayet Bildirimi</h2>
        </div>
        <p class="text-sm text-gray-600 pl-11">Lütfen yaşadığınız sorunla ilgili aşağıdaki bilgileri eksiksiz
            doldurunuz.</p>
    </div>

    
    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
            <p><?php echo e(session('error')); ?></p>
        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Hata!</strong>
            <ul class="mt-1 list-disc list-inside text-sm">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <form action="<?php echo e(route('public.sikayet.store')); ?>" method="POST" enctype="multipart/form-data"
        x-data="fileUploadComponent()">
        <?php echo csrf_field(); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">

            
            <div class="group col-span-1">
                <label for="musteri_adi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                    Müşteri Adı <span class="ml-1 text-red-500">*</span>
                </label>
                <input type="text" name="musteri_adi" id="musteri_adi"
                    value="<?php echo e(old('musteri_adi', auth()->user()?->name)); ?>" required
                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-2.5 text-gray-900"
                    placeholder="Adınız ve Soyadınız">
                <?php $__errorArgs = ['musteri_adi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="group col-span-1">
                <label for="musteri_iletisim" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                    E-posta Adresiniz <span class="ml-1 text-red-500">*</span>
                </label>
                <input type="email" name="musteri_iletisim" id="musteri_iletisim"
                    value="<?php echo e(old('musteri_iletisim', auth()->user()?->email)); ?>" required
                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-2.5 text-gray-900"
                    placeholder="ornek@mail.com">
                <p class="mt-1 text-xs text-gray-500">Takip linki ve şifreniz bu adrese gönderilecektir.</p>
                <?php $__errorArgs = ['musteri_iletisim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="group col-span-1 md:col-span-2">
                <label class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 6.75V15m6-6v8.25m.503-6.998l-6 .75m-.75-7.5l6 .75m6-.75l-6 .75M3 12h18M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                    Konum Tipi <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="mt-2 flex items-center space-x-6">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="konum_tipi" value="Yurt İçi"
                            class="form-radio text-red-600 focus:ring-red-500" <?php echo e(old('konum_tipi', 'Yurt İçi') == 'Yurt İçi' ? 'checked' : ''); ?>>
                        <span class="ml-2 text-sm text-gray-700">Yurt İçi</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="konum_tipi" value="Yurt Dışı"
                            class="form-radio text-red-600 focus:ring-red-500" <?php echo e(old('konum_tipi') == 'Yurt Dışı' ? 'checked' : ''); ?>>
                        <span class="ml-2 text-sm text-gray-700">Yurt Dışı</span>
                    </label>
                </div>
                <?php $__errorArgs = ['konum_tipi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            
            
            <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" x-data="categorySystem()"
                x-init="init()">

                
                <div class="group">
                    <label for="sikayet_kategorisi_id"
                        class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                        </svg>
                        Şikayet Kategorisi <span class="ml-1 text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="sikayet_kategorisi_id" id="sikayet_kategorisi_id" required
                            x-model="selectedCategory" @change="fetchSubCategories(false)"
                            class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-10 py-2.5 text-gray-900 appearance-none bg-white">
                            <option value="">-- Kategori Seçiniz --</option>
                            <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($kategori->id); ?>"><?php echo e($kategori->ad); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg
                                class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg></div>
                    </div>
                    <?php $__errorArgs = ['sikayet_kategorisi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="group" x-show="subCategories.length > 0 || showOtherOption" style="display: none;">
                    <label for="sikayet_alt_kategori_id"
                        class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                        Alt Kategori <span class="ml-1 text-red-500">*</span>
                        <span x-show="isLoading" class="ml-2 text-xs text-gray-400">(Yükleniyor...)</span>
                    </label>
                    <div class="relative">
                        <select name="sikayet_alt_kategori_id" id="sikayet_alt_kategori_id"
                            x-model="selectedSubCategory"
                            class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-10 py-2.5 text-gray-900 appearance-none bg-white">
                            <option value="">-- Alt Kategori Seçiniz --</option>
                            <template x-for="sub in subCategories" :key="sub.id">
                                <option :value="sub.id" x-text="sub.ad"></option>
                            </template>
                            <template x-if="showOtherOption">
                                <option value="other">Diğer / Belirtilmemiş</option>
                            </template>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg
                                class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg></div>
                    </div>
                    <?php $__errorArgs = ['sikayet_alt_kategori_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="group md:col-span-2 bg-yellow-50 p-4 rounded border border-yellow-200"
                    x-show="selectedSubCategory === 'other'" style="display: none;" x-transition>
                    <label for="sikayet_alt_kategori_diger" class="block text-sm font-medium text-yellow-800 mb-1"
                        x-text="otherLabel"></label>
                    <input type="text" name="sikayet_alt_kategori_diger" id="sikayet_alt_kategori_diger"
                        value="<?php echo e(old('sikayet_alt_kategori_diger')); ?>"
                        placeholder="Lütfen sorunu kısaca tanımlayınız..."
                        class="block w-full border-yellow-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    <?php $__errorArgs = ['sikayet_alt_kategori_diger'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            

            
            <div class="group col-span-1">
                <label for="musteri_sikayet_tarihi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                    Olayın Yaşandığı Tarih <span class="ml-1 text-red-500">*</span>
                </label>
                <input type="date" name="musteri_sikayet_tarihi" id="musteri_sikayet_tarihi"
                    value="<?php echo e(old('musteri_sikayet_tarihi', date('Y-m-d'))); ?>" required
                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-2.5 text-gray-900">
                <?php $__errorArgs = ['musteri_sikayet_tarihi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="col-span-1 md:col-span-2 group">
                <label for="musteri_sikayet_konusu" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    Şikayet Konusu <span class="ml-1 text-red-500">*</span>
                </label>
                <input type="text" name="musteri_sikayet_konusu" id="musteri_sikayet_konusu"
                    value="<?php echo e(old('musteri_sikayet_konusu')); ?>" required
                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-2.5 text-gray-900"
                    placeholder="Sorunu kısaca özetleyin">
                <?php $__errorArgs = ['musteri_sikayet_konusu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="col-span-1 md:col-span-2 group">
                <label for="musteri_sikayet_detayi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd" />
                    </svg>
                    Şikayet Detayı <span class="ml-1 text-red-500">*</span>
                </label>
                <textarea name="musteri_sikayet_detayi" id="musteri_sikayet_detayi" rows="5" required
                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900 resize-y"
                    placeholder="Lütfen yaşadığınız sorunu tüm detaylarıyla açıklayınız..."><?php echo e(old('musteri_sikayet_detayi')); ?></textarea>
                <p class="mt-2 text-xs text-gray-500">Şikayetle ilgili tüm detayları mümkün olduğunca açıklayıcı bir
                    şekilde yazınız.</p>
                <?php $__errorArgs = ['musteri_sikayet_detayi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="col-span-1 md:col-span-2 group">
                <label for="dosyalar" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd" />
                    </svg>
                    Kanıtlar (Dosya Ekle)
                </label>
                <input type="file" name="dosyalar[]" id="dosyalar" multiple
                    accept="image/*,video/mp4,application/pdf,.doc,.docx,.xls,.xlsx" capture="environment"
                    @change="updatePreviews($event)" x-ref="fileInput"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 file:transition-colors file:cursor-pointer border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                <p class="mt-2 text-xs text-gray-500">Resim, PDF, Word, Video ekleyebilirsiniz (Mobil/Masaüstü).
                    Maksimum: 10MB.</p>
                <?php $__errorArgs = ['dosyalar.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                
                <div x-show="previews.length > 0"
                    class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <template x-for="(preview, index) in previews" :key="index">
                        <div class="relative group bg-gray-100 rounded-lg overflow-hidden border">
                            <template x-if="preview.url">
                                <img :src="preview.url" class="object-cover h-24 w-full" alt="Önizleme">
                            </template>
                            <template x-if="!preview.url">
                                <div class="flex flex-col items-center justify-center h-24 bg-gray-200 p-2">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="text-xs text-gray-500 mt-1 truncate" x-text="preview.name"></p>
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

        
        <div class="mt-8 flex items-start">
            <div class="flex items-center h-5">
                <input id="kvkk_approval" name="kvkk_approval" type="checkbox" required
                    class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded cursor-pointer">
            </div>
            <div class="ml-3 text-sm">
                <label for="kvkk_approval" class="font-medium text-gray-700 cursor-pointer select-none">
                    <span class="text-blue-600 hover:text-blue-800 underline cursor-pointer"
                        @click.prevent="showKvkk = true">Kişisel Verilerin Korunması Kanunu (KVKK) Metnini</span>
                    okudum ve onaylıyorum.
                </label>
                <?php $__errorArgs = ['kvkk_approval'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        
        <div x-show="showKvkk" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog"
            aria-modal="true">
            
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showKvkk" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showKvkk = false"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                
                <div x-show="showKvkk" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    KVKK Aydınlatma Metni
                                </h3>

                                <?php
                                    $kvkkPdfVal = \App\Models\Setting::where('key', 'kvkk_pdf')->value('value');
                                    $kvkkTextVal = \App\Models\Setting::where('key', 'kvkk_text')->value('value');
                                ?>

                                <?php if($kvkkPdfVal): ?>
                                    <div class="mt-4 w-full h-[60vh] rounded-lg overflow-hidden border border-gray-100">
                                        <embed src="<?php echo e(asset('storage/' . $kvkkPdfVal)); ?>#toolbar=0" type="application/pdf"
                                            class="w-full h-full">
                                    </div>
                                <?php else: ?>
                                    <div
                                        class="mt-4 max-h-96 overflow-y-auto bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        <div class="prose prose-sm text-gray-600">
                                            <?php echo !empty($kvkkTextVal) ? $kvkkTextVal : 'KVKK metni henüz sistem yöneticisi tarafından eklenmemiştir.'; ?>

                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="showKvkk = false; document.getElementById('kvkk_approval').checked = true;">
                            Okudum, Anladım
                        </button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="showKvkk = false">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
            <a href="<?php echo e(url('/')); ?>"
                class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                İptal
            </a>
            <button type="submit"
                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Şikayeti Kaydet
            </button>
        </div>
    </form>

    
    <script>
        function fileUploadComponent() {
            return {
                showKvkk: false,
                previews: [], files: [],
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

        // === KATEGORİ SİSTEMİ (Asset Mantığı İle) ===
        function categorySystem() {
            return {
                selectedCategory: '<?php echo e(old('sikayet_kategorisi_id')); ?>',
                selectedSubCategory: '<?php echo e(old('sikayet_alt_kategori_id', old('sikayet_alt_kategori_diger') ? 'other' : '')); ?>',
                otherText: '<?php echo e(old('sikayet_alt_kategori_diger')); ?>',
                subCategories: [],
                showOtherOption: false,
                otherLabel: 'Diğer Açıklama',
                isLoading: false,

                init() {
                    if (this.selectedCategory) {
                        this.fetchSubCategories(true);
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

                    // === URL DÜZELTMESİ ===
                    var baseUrl = "<?php echo e(asset('api/get-alt-kategoriler')); ?>";
                    var apiUrl = baseUrl + "/" + this.selectedCategory;

                    fetch(apiUrl)
                        .then(res => res.json())
                        .then(data => {
                            this.subCategories = data.alt_kategoriler;
                            this.showOtherOption = data.diger_goster;
                            this.otherLabel = data.diger_baslik || 'Lütfen detay belirtiniz:';
                            if (!keepSelection) { this.selectedSubCategory = ''; }
                        })
                        .catch(err => console.error(err))
                        .finally(() => this.isLoading = false);
                }
            }
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/public/sikayet/sikayet-form.blade.php ENDPATH**/ ?>