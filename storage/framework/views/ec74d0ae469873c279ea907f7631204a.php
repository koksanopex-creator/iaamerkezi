<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?> 

    <div class="mb-6 pb-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">Şikayet Detayları</h3>
                <p class="text-sm text-gray-600">
                    Şikayet No: <span class="font-semibold text-indigo-600">#<?php echo e($sikayet->id); ?></span>
                    (Takip Kodu: <span class="font-semibold text-gray-900"><?php echo e($sikayet->takip_token ?? 'N/A'); ?></span>)
                </p>
            </div>
            <div>
                <?php echo $sikayet->musteri_durum_badge; ?> 
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        <div class="bg-white shadow-lg rounded-lg p-5 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-500">Geçen Süre</div>
                <div class="text-xl font-bold text-gray-900">
                    <?php echo e((int)round(\Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->diffInDays(now())) + 1); ?>. Gün
                </div>
            </div>
        </div>
        
        <?php if($sikayet->musteri_cozum_son_tarihi && $sikayet->musteri_durum != 'Kapatıldı'): ?>
            <?php
                $simdi = now();
                $sonTarih = \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi);
                $geciktiMi = $simdi->isAfter($sonTarih);
            ?>
            <div class="bg-white shadow-lg rounded-lg p-5 flex items-center gap-4 <?php echo e($geciktiMi ? 'border-l-4 border-red-500' : ''); ?>">
                <div class="flex-shrink-0 w-12 h-12 <?php echo e($geciktiMi ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'); ?> rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008z" /></svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500"><?php echo e($geciktiMi ? 'Gecikme Süresi' : 'Çözüm İçin Kalan Süre'); ?></div>
                    <div class="text-xl font-bold <?php echo e($geciktiMi ? 'text-red-700' : 'text-gray-900'); ?>">
                        <?php echo e($sonTarih->diffForHumans(null, true)); ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if($sikayet->cozumTakimi): ?>
             <div class="bg-white shadow-lg rounded-lg p-5 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 00-3.741-.56c-.63-.042-1.249-.065-1.87-.065A18.707 18.707 0 005.25 18.72m12.75 0A18.707 18.707 0 0115 18.72m-3 0A18.707 18.707 0 009 18.72m9 0a8.966 8.966 0 00-9 0m9 0c1.943 0 3.713.463 5.25 1.285A18.707 18.707 0 0015 18.72m-6 0c1.943 0 3.713.463 5.25 1.285A18.707 18.707 0 019 18.72m-6 0A18.707 18.707 0 013 18.72m0 0c1.943 0 3.713.463 5.25 1.285A18.707 18.707 0 003 18.72z" /></svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">İlgili Ekip</div>
                    <div class="text-xl font-bold text-gray-900"><?php echo e($sikayet->cozumTakimi->ad); ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert"><p><?php echo e(session('success')); ?></p></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert"><p><?php echo e(session('error')); ?></p></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <strong class="font-bold">Hata!</strong>
            <ul class="mt-2 list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Şikayet Bilgileri</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 text-sm">
                <div><strong class="text-gray-500">Müşteri Adı:</strong> <span class="text-gray-900"><?php echo e($sikayet->musteri_adi); ?></span></div>
                <div><strong class="text-gray-500">E-posta:</strong> <span class="text-gray-900"><?php echo e($sikayet->musteri_iletisim); ?></span></div>
                <div><strong class="text-gray-500">Konum Tipi:</strong> <span class="text-gray-900"><?php echo e($sikayet->konum_tipi); ?></span></div>
                <div><strong class="text-gray-500">Şikayet Tarihi:</strong> <span class="text-gray-900"><?php echo e(\Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->format('d.m.Y')); ?></span></div>
                <div class="md:col-span-2"><strong class="text-gray-500">Kategori:</strong> <span class="text-gray-900"><?php echo e($sikayet->sikayetKategori->ad ?? 'Belirtilmemiş'); ?></span></div>
            </div>
            <div class="mt-6">
                <strong class="text-sm text-gray-500 block mb-1">Şikayet Konusu:</strong>
                <p class="text-lg text-gray-900 font-medium"><?php echo e($sikayet->musteri_sikayet_konusu); ?></p>
            </div>
            <div class="mt-4">
                <strong class="text-sm text-gray-500 block mb-1">Şikayet Detayı:</strong>
                <p class="text-gray-700 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border border-gray-200"><?php echo e($sikayet->musteri_sikayet_detayi); ?></p>
            </div>
            <?php if($sikayet->dosyalar && $sikayet->dosyalar->count() > 0): ?>
            <div class="mt-4">
                <strong class="text-sm text-gray-500 block mb-1">Eklenen Dosyalar:</strong>
                <ul class="list-disc list-inside space-y-1 pl-1">
                    <?php $__currentLoopData = $sikayet->dosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e(asset('storage/' . $dosya->dosya_olu)); ?>" target="_blank" class="text-indigo-600 hover:underline text-sm">
                            <?php echo e($dosya->orijinal_adi); ?> <span class="text-gray-400 text-xs">(<?php echo e($dosya->mime_tipi); ?>)</span>
                        </a>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if(is_null($sikayet->edit_locked_at) && $sikayet->musteri_durum == 'Yeni'): ?>
        <div class="p-6 bg-blue-50 border border-blue-200 rounded-lg text-center mb-8 shadow-sm">
             <p class="text-sm text-blue-700 mb-3">Şikayetiniz henüz işleme alınmadı. İsterseniz detayları güncelleyebilirsiniz.</p>
             <a href="<?php echo e(route('public.sikayet.edit', ['token' => $sikayet->takip_token])); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 transition">
                 Şikayeti Düzenle
             </a>
        </div>
    <?php endif; ?>

    
    <?php if((!is_null($sikayet->edit_locked_at) || $sikayet->musteri_durum != 'Yeni') && $sikayet->musteri_durum != 'Kapatıldı'): ?>
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Şikayet Süreci İlerlemesi</h3>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    </div>
                    <div class="ml-4">
                        <p class="font-medium text-gray-900">Şikayetiniz şu anda <?php echo e($sikayet->cozumTakimi->ad ?? 'ekibimiz'); ?> tarafından incelenmektedir.</p>
                        <p class="text-sm text-gray-500">Durum: <?php echo e($sikayet->musteri_durum); ?></p>
                    </div>
                </div>

                <?php if($sikayet->iaa_id && $totalSteps > 0): ?>
                <div class="flex items-start border-t border-gray-200 pt-6">
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3m0 0l3-3m-3 3v-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 w-full">
                        <p class="font-medium text-gray-900">Proje İlerlemesi: (<?php echo e($completedSteps); ?> / <?php echo e($totalSteps); ?> Adım Tamamlandı)</p>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?php echo e($totalSteps > 0 ? ($completedSteps / $totalSteps) * 100 : 0); ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($sikayet->loglar->whereNotNull('user_id')->isNotEmpty()): ?>
                    <?php $__currentLoopData = $sikayet->loglar->whereNotNull('user_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start border-t border-gray-200 pt-6">
                            <div class="flex-shrink-0 w-10 h-10 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-gray-800"><?php echo e($log->eylem); ?></p>
                                <p class="text-sm text-gray-600 italic">"<?php echo e($log->aciklama); ?>"</p>
                                <p class="text-xs text-gray-400 mt-0.5"><?php echo e($log->created_at->format('d.m.Y H:i')); ?> - (<?php echo e($log->user->name ?? 'Sistem'); ?>)</p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if($sikayet->iaa_id && $yorumlar->isNotEmpty()): ?>
        <?php
            $yorumSayisi = $yorumlar->count();
            $sonYorum = $yorumlar->first();
            // "Yeni" = Son yorumu müşteri (user_id = null) YAPMADIYSA
            $yeniYorumVarMi = $sonYorum && !is_null($sonYorum->user_id); 
        ?>
        
        <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Proje Geçmişi ve Yorumlar</h3>
            
            
            <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaa_id)); ?>" 
               class="block bg-white shadow-lg rounded-lg border border-gray-200 transition hover:shadow-xl hover:border-indigo-500">
                <div class="px-6 py-5 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 <?php echo e($yeniYorumVarMi ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-500'); ?> rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443h2.884c1.584 0 2.863-1.279 2.863-2.863V12.76M2.25 12.76V6.226c0-1.6 1.123-2.994 2.707-3.227 1.087-.16 2.185-.283 3.293-.369V2.25l4.076 4.076c.296.296.678.443 1.037.443h2.884c1.584 0 2.863 1.279 2.863 2.863v6.534M2.25 12.76c0-1.6 1.123-2.994 2.707-3.227 1.087-.16 2.185-.283 3.293-.369V6.25" /></svg>
                        </div>
                        
                        <div>
                            <?php if($yeniYorumVarMi): ?>
                                <span class="text-sm font-semibold text-indigo-600 flex items-center gap-2">
                                    <span class="relative flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                    </span>
                                    Yeni Cevap Geldi
                                </span>
                                <p class="text-base font-semibold text-gray-900"><strong><?php echo e($sonYorum->yapan_kisi_adi); ?></strong>'dan yeni bir yorumunuz var.</p>
                            <?php else: ?>
                                <p class="text-base font-semibold text-gray-900">Toplam <?php echo e($yorumSayisi); ?> Yorum</p>
                                <p class="text-sm text-gray-500">Son yorum sizden geldi.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-indigo-600 hidden md:block">Yorumları Gör ve Cevap Yaz</span>
                        <svg class="w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </div>
                </div>
            </a>
        </div>
    <?php endif; ?>
    
    <?php if($sikayet->musteri_durum == 'Kapatıldı'): ?>
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Çözüm Değerlendirmeniz</h3>
            </div>
            <div class="p-6">
                <?php if($sikayet->musteri_feedback): ?>
                    
                    <div class="p-4 <?php echo e($sikayet->musteri_feedback == 'Onaylandı' ? 'bg-green-50 border-green-200' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200')); ?> border rounded-lg">
                        <p class="text-sm font-medium <?php echo e($sikayet->musteri_feedback == 'Onaylandı' ? 'text-green-800' : ($sikayet->musteri_feedback == 'Reddedildi' ? 'text-red-800' : 'text-yellow-800')); ?>">
                            Geri bildiriminiz: <strong><?php echo e($sikayet->musteri_feedback); ?></strong>
                        </p>
                        <?php if($sikayet->musteri_feedback_note): ?>
                         <p class="text-sm text-gray-600 mt-2 italic">Notunuz: "<?php echo e($sikayet->musteri_feedback_note); ?>"</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    
                    <p class="text-sm text-gray-600 mb-4">Şikayetiniz çözümlenmiştir. Lütfen çözümü değerlendirerek aşağıdaki butonlardan birini seçiniz.</p>
                    <form method="POST" action="<?php echo e(route('public.sikayet.storeFeedback', ['token' => $sikayet->takip_token])); ?>" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label for="feedback_note" class="block text-sm font-medium text-gray-700 mb-1">Ek Not (Reddetme veya Revizyon için açıklama ekleyebilirsiniz):</label>
                            <textarea name="feedback_note" id="feedback_note" rows="3" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm resize-y" placeholder="Çözümle ilgili ek yorumlarınız..."><?php echo e(old('feedback_note')); ?></textarea>
                            <?php $__errorArgs = ['feedback_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button type="submit" name="feedback" value="Onaylandı" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Çözümü Onayla</button>
                            <button type="submit" name="feedback" value="Reddedildi" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Çözümü Reddet</button>
                            <button type="submit" name="feedback" value="Revizyon İstendi" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">Revizyon İste</button>
                        </div>
                        <?php $__errorArgs = ['feedback'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between items-center">
        <a href="<?php echo e(url('/')); ?>" class="text-sm text-gray-600 hover:text-gray-900 hover:underline">
            &larr; Ana Sayfaya Dön
        </a>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/public/sikayet/sikayet-detay.blade.php ENDPATH**/ ?>