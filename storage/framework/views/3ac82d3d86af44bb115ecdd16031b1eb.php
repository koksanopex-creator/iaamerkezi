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
            <?php echo e(__('Takım Detayı')); ?>: <span class="text-indigo-600"><?php echo e($takim->ad); ?></span>
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            
            <div
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold"><?php echo e($takim->ad); ?></h3>
                    <p class="opacity-90 mt-1">
                        Lider: <span class="font-semibold"><?php echo e($takim->lider->name ?? 'Atanmamış'); ?></span>
                    </p>
                </div>
                <div class="mt-4 md:mt-0 text-center md:text-right">
                    <p class="text-sm opacity-80 uppercase tracking-widest font-bold">Takım Puanı</p>
                    <p class="text-4xl font-black mt-1"><?php echo e(number_format($takim->toplam_puan, 0)); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                
                <div class="lg:col-span-1 space-y-6">

                    
                    <?php if(auth()->id() == $takim->lider_user_id || auth()->user()->hasRole(['Superadmin', 'Yonetim'])): ?>
                        <div class="text-right mb-2">
                             <a href="<?php echo e(route('takimlar.edit', $takim->id)); ?>" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Bilgileri Düzenle
                            </a>
                        </div>
                    <?php endif; ?>

                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Takım Üyeleri
                            </h3>

                            
                            <?php if($takim->lider): ?>
                                <div class="mb-4 p-4 bg-indigo-50 rounded-lg border border-indigo-100 flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-200 flex items-center justify-center text-lg font-bold text-indigo-700 mr-4">
                                        <?php echo e(Str::substr($takim->lider->name, 0, 1)); ?>

                                    </div>
                                    <div>
                                        <p class="text-xs text-indigo-500 uppercase font-bold tracking-wider">Takım Lideri</p>
                                        <a href="<?php echo e(route('profile.puanlar', $takim->lider->id)); ?>" class="text-lg font-bold text-gray-900 hover:text-indigo-600 hover:underline">
                                            <?php echo e($takim->lider->name); ?>

                                        </a>
                                        <p class="text-sm text-gray-500"><?php echo e($takim->lider->bolum->ad ?? '-'); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <ul class="divide-y divide-gray-100">
                                <?php $__empty_1 = true; $__currentLoopData = $takim->users->where('id', '!=', $takim->lider_user_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 mr-3">
                                                <?php echo e(Str::substr($uye->name, 0, 1)); ?>

                                            </div>
                                            <div>
                                                <a href="<?php echo e(route('profile.puanlar', $uye->id)); ?>" class="text-sm font-medium text-gray-900 hover:text-indigo-600 hover:underline">
                                                    <?php echo e($uye->name); ?>

                                                </a>
                                                <p class="text-xs text-gray-500"><?php echo e($uye->bolum->ad ?? '-'); ?></p>
                                            </div>
                                        </div>
                                        <?php if(auth()->id() == $takim->lider_user_id || auth()->user()->hasRole(['Superadmin', 'Yonetim'])): ?>
                                            <form action="<?php echo e(route('takimlar.uyeCikar', [$takim->id, $uye->id])); ?>" method="POST" onsubmit="return confirm('Bu üyeyi takımdan çıkarmak istediğinize emin misiniz?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 px-2 py-1 rounded transition">
                                                    Çıkar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <li class="py-3 text-sm text-gray-500 italic">Başka üye yok.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            
                            <?php if(auth()->id() == $takim->lider_user_id): ?>
                                <div class="mb-6 border-b pb-6 bg-indigo-50/30 p-4 rounded-xl border border-indigo-100/50">
                                    <h4 class="text-sm font-bold text-indigo-900 mb-3 uppercase flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                        Yeni Üye Davet Et
                                    </h4>
                                    <form action="<?php echo e(route('takimlar.davetGonder', $takim->id)); ?>" method="POST" id="inviteForm" class="flex flex-col sm:flex-row gap-3">
                                        <?php echo csrf_field(); ?>
                                        <div class="flex-1 relative">
                                            <input list="personel_list" id="user_search" name="user_name" 
                                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm py-2.5" 
                                                   placeholder="Personel ismini yazın..." autocomplete="off">
                                            <datalist id="personel_list">
                                                <?php $__currentLoopData = $bostaPersoneller; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $personel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($personel->name); ?>" data-id="<?php echo e($personel->id); ?>">
                                                        <?php echo e($personel->bolum ? $personel->bolum->ad : 'Bölüm Belirtilmemiş'); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </datalist>
                                            <input type="hidden" name="user_id" id="selected_user_id">
                                            <div id="selection_status" class="mt-1 text-[10px] text-gray-400 italic"></div>
                                        </div>
                                        <button type="submit" id="inviteBtn" disabled
                                                class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                            Davetiye Gönder
                                        </button>
                                    </form>
                                    <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-xs mt-2 font-medium">Lütfen listeden geçerli bir personel seçiniz.</p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                    <script>
                                        document.getElementById('user_search').addEventListener('input', function(e) {
                                            const input = e.target;
                                            const val = input.value;
                                            const list = document.getElementById('personel_list');
                                            const options = list.options;
                                            const hiddenInput = document.getElementById('selected_user_id');
                                            const status = document.getElementById('selection_status');
                                            const btn = document.getElementById('inviteBtn');
                                            
                                            let found = false;
                                            for (let i = 0; i < options.length; i++) {
                                                if (options[i].value === val) {
                                                    hiddenInput.value = options[i].getAttribute('data-id');
                                                    status.textContent = '✓ Personel seçildi';
                                                    status.className = 'mt-1 text-[10px] text-green-600 font-bold';
                                                    btn.disabled = false;
                                                    found = true;
                                                    break;
                                                }
                                            }
                                            
                                            if (!found) {
                                                hiddenInput.value = '';
                                                status.textContent = 'Lütfen listeden seçin...';
                                                status.className = 'mt-1 text-[10px] text-gray-400 italic';
                                                btn.disabled = true;
                                            }
                                        });

                                        // Formun yanlışlıkla gönderilmesini engelle (ID yoksa)
                                        document.getElementById('inviteForm').addEventListener('submit', function(e) {
                                            if (!document.getElementById('selected_user_id').value) {
                                                e.preventDefault();
                                                alert('Lütfen listeden bir personel seçiniz.');
                                            }
                                        });
                                    </script>
                                </div>
                            <?php endif; ?>

                            
                            <h4 class="text-sm font-bold text-gray-700 mb-2 uppercase">Katılım İstekleri</h4>
                            <?php if(auth()->id() == $takim->lider_user_id): ?>
                                <?php if($gelenIstekler->count() > 0): ?>
                                    <ul class="divide-y divide-gray-100 mb-4">
                                        <?php $__currentLoopData = $gelenIstekler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $istek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="py-2 flex justify-between items-center">
                                                <span class="text-sm"><?php echo e($istek->davetEden->name); ?></span>
                                                <div class="flex space-x-1">
                                                     <form action="<?php echo e(route('takimlar.istek-onayla', $istek->id)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">Onayla</button>
                                                    </form>
                                                    <form action="<?php echo e(route('takimlar.istek-reddet', $istek->id)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">Red</button>
                                                    </form>
                                                </div>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-sm text-gray-400 italic mb-4">Bekleyen istek yok.</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-sm text-gray-400 italic mb-4">Sadece lider görebilir.</p>
                            <?php endif; ?>

                            
                            <h4 class="text-sm font-bold text-gray-700 mb-2 uppercase border-t pt-4">Gönderilen Davetler</h4>
                            <?php if($gonderilenDavetler->count() > 0): ?>
                                <ul class="divide-y divide-gray-100">
                                    <?php $__currentLoopData = $gonderilenDavetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $davet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="py-2 flex justify-between items-center">
                                            <span class="text-sm"><?php echo e($davet->davetEdilen->name); ?></span>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Bekliyor</span>
                                                <?php if(auth()->id() == $takim->lider_user_id): ?>
                                                    <form action="<?php echo e(route('takimlar.davetiIptalEt', $davet->id)); ?>" method="POST" onsubmit="return confirm('Bu daveti geri çekmek istediğinize emin misiniz?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 transition">Geri Çek</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-sm text-gray-400 italic">Bekleyen davet yok.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                     
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200 space-y-4">
                            <div>
                                <h4 class="font-bold text-gray-900">Amaç</h4>
                                <p class="text-sm text-gray-600 mt-1"><?php echo e($takim->amac ?? 'Belirtilmemiş'); ?></p>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Vizyon</h4>
                                <p class="text-sm text-gray-600 mt-1"><?php echo e($takim->vizyon ?? 'Belirtilmemiş'); ?></p>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Misyon</h4>
                                <p class="text-sm text-gray-600 mt-1"><?php echo e($takim->misyon ?? 'Belirtilmemiş'); ?></p>
                            </div>
                        </div>
                    </div>

                </div>

                
                
                <div class="lg:col-span-2 space-y-6">

                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            
                            
                            <div class="mb-8 border-b pb-6 border-dashed border-gray-300">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <span class="bg-gray-100 text-gray-800 p-2 rounded-lg mr-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </span>
                                    Bekleyen İAA Talepleri (Havuzda)
                                </h3>
                                <?php if($bekleyenTalepler->isNotEmpty()): ?>
                                    <div class="grid grid-cols-1 gap-4">
                                        <?php $__currentLoopData = $bekleyenTalepler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="border rounded-lg p-4 shadow-sm bg-gray-50 flex justify-between items-center opacity-75">
                                                <div>
                                                    <h4 class="text-md font-semibold text-gray-700"><?php echo e($talep->baslik); ?></h4>
                                                    <p class="text-xs text-gray-500 mt-1">Talep Tarihi: <?php echo e(\Carbon\Carbon::parse($talep->talep_tarihi)->diffForHumans()); ?></p>
                                                </div>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">
                                                    Sıra Bekliyor
                                                </span>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-500 italic">Havuzda bekleyen talep bulunmamaktadır.</p>
                                <?php endif; ?>
                            </div>

                            
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <span class="bg-blue-100 text-blue-800 p-2 rounded-lg mr-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                                </span>
                                Devam Eden Projeler
                            </h3>
                            <?php if($devamEdenProjeler->count() > 0): ?>
                                <div class="grid grid-cols-1 gap-4">
                                    <?php $__currentLoopData = $devamEdenProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="block hover:bg-gray-50 transition duration-150 ease-in-out">
                                            <div class="border rounded-lg p-4 shadow-sm hover:shadow-md bg-white">
                                                <div class="flex justify-between items-start mb-2">
                                                    
                                                    <div class="flex gap-2">
                                                        <?php if($proje->musteriSikayeti): ?>
                                                            <span class="px-2 py-1 text-xs font-bold rounded bg-rose-100 text-rose-800">Müşteri Şikayeti</span>
                                                        <?php else: ?>
                                                            <span class="px-2 py-1 text-xs font-bold rounded bg-indigo-100 text-indigo-800">İAA Projesi</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        <?php echo e($proje->aktifAdim->name ?? 'Başlangıç'); ?>

                                                    </span>
                                                </div>
                                                <h4 class="text-md font-semibold text-gray-800 break-words w-3/4">
                                                    <?php echo e($proje->baslik); ?>

                                                </h4>
                                                <div class="mt-2 text-sm text-gray-500">
                                                    <span>Son Güncelleme: <?php echo e($proje->updated_at->diffForHumans()); ?></span>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 italic">Aktif proje bulunmuyor.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <?php if($onayBekleyenTamamlanmisProjeler->count() > 0): ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-400">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <span class="bg-yellow-100 text-yellow-800 p-2 rounded-lg mr-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </span>
                                    Yönetici Onayı Bekleyen Tamamlanmış Projeler
                                </h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <?php $__currentLoopData = $onayBekleyenTamamlanmisProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="block hover:bg-gray-50 transition duration-150 ease-in-out">
                                            <div class="border rounded-lg p-4 shadow-sm hover:shadow-md bg-white">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div class="flex gap-2">
                                                        <?php if($proje->musteriSikayeti): ?>
                                                            <span class="px-2 py-1 text-xs font-bold rounded bg-rose-100 text-rose-800">Müşteri Şikayeti</span>
                                                        <?php else: ?>
                                                            <span class="px-2 py-1 text-xs font-bold rounded bg-indigo-100 text-indigo-800">İAA Projesi</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Onay Bekliyor</span>
                                                </div>
                                                <h4 class="text-md font-semibold text-gray-800 break-words w-3/4">
                                                    <?php echo e($proje->baslik); ?>

                                                </h4>
                                                <div class="mt-2 text-sm text-gray-500">
                                                    Tamamlanma Tarihi: <?php echo e($proje->updated_at->format('d.m.Y')); ?>

                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <span class="bg-green-100 text-green-800 p-2 rounded-lg mr-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Tamamlanan Projeler
                            </h3>
                            <?php if($tamamlananProjeler->count() > 0): ?>
                                <div class="grid grid-cols-1 gap-4">
                                    <?php $__currentLoopData = $tamamlananProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="block hover:bg-gray-50 transition duration-150 ease-in-out">
                                            <div class="border rounded-lg p-4 shadow-sm hover:shadow-md bg-white flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex gap-2 mb-1">
                                                        <?php if($proje->musteriSikayeti): ?>
                                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-rose-100 text-rose-800 uppercase">Müşteri Şikayeti</span>
                                                        <?php else: ?>
                                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-indigo-100 text-indigo-800 uppercase">İAA</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <h4 class="text-md font-semibold text-gray-800 break-words"><?php echo e($proje->baslik); ?></h4>
                                                    <p class="text-xs text-gray-500 mt-1"><?php echo e(\Carbon\Carbon::parse($proje->onaylanma_tarihi)->format('d.m.Y')); ?></p>
                                                </div>
                                                <div class="mt-2 sm:mt-0 sm:ml-4 flex-shrink-0">
                                                    <span class="text-lg font-bold text-green-600">+<?php echo e(number_format($proje->puan, 0)); ?> Puan</span>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 italic">Tamamlanan proje bulunmuyor.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg opacity-75">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <span class="bg-red-100 text-red-800 p-2 rounded-lg mr-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                İptal Edilen / Reddedilen Projeler
                            </h3>
                            <?php if($iptalEdilenProjeler->count() > 0): ?>
                                <div class="grid grid-cols-1 gap-4">
                                    <?php $__currentLoopData = $iptalEdilenProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="block hover:bg-gray-50 transition duration-150 ease-in-out">
                                            <div class="border rounded-lg p-4 shadow-sm bg-gray-50 flex justify-between items-start">
                                                <div>
                                                     <div class="flex gap-2 mb-1">
                                                        <?php if($proje->musteriSikayeti): ?>
                                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-rose-100 text-rose-800 uppercase">Müşteri Şikayeti</span>
                                                        <?php else: ?>
                                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-indigo-100 text-indigo-800 uppercase">İAA</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <h4 class="text-md font-semibold text-gray-600 line-through">
                                                        <?php echo e($proje->baslik); ?>

                                                    </h4>
                                                </div>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    <?php echo e($proje->durum); ?>

                                                </span>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 italic">Reddedilen veya iptal edilen proje bulunmamaktadır.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="flex justify-between mt-6">
                <?php if(auth()->user()->hasRole(['Superadmin', 'Yonetim'])): ?>
                    <a href="<?php echo e(route('admin.takim-yonetim.index')); ?>"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        &larr; Geri Dön
                    </a>
                <?php elseif(auth()->user()->hasRole('Bölüm Kalite Yöneticisi') || auth()->user()->hasRole('Bölüm Lideri')): ?>
                    <a href="<?php echo e(route('takimlar.index')); ?>"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        &larr; Takımlarıma Dön
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('dashboard')); ?>"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        &larr; Ana Sayfaya Dön
                    </a>
                <?php endif; ?>

                <?php if(auth()->user()->hasRole(['Bölüm Kalite Yöneticisi', 'Bölüm Lideri']) && !$takim->is_kurul_takimi): ?>
                    <a href="<?php echo e(route('admin.cozum-takimlari.index')); ?>"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Diğer Çözüm Takımları
                    </a>
                <?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/takimlar/show.blade.php ENDPATH**/ ?>