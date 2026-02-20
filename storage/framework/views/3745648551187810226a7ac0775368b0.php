<div class="space-y-6">
    
    <div class="flex justify-between items-center border-b border-gray-200 pb-4">
        <div>
            <h3 class="text-lg font-medium leading-6 text-gray-900">Tanımlı Rapor Kuralları</h3>
            <p class="mt-1 text-sm text-gray-500">Otomatik gönderilecek raporların listesi ve ayarları.</p>
        </div>
        
        <button type="button" wire:click="yeniKural" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Yeni Kural Ekle
        </button>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
        <div class="rounded-md bg-green-50 p-4 border-l-4 border-green-400">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periyot / Saat</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alıcılar</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İçerik</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">İşlemler</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $kurallar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kural): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($kural->baslik); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                                            <?php echo e($kural->periyot); ?>

                                        </span>
                                        <div class="text-sm text-gray-500 mt-1">
                                            <?php echo e(\Carbon\Carbon::parse($kural->gonderim_saati)->format('H:i')); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-500">
                                            <div class="flex items-center gap-1">
                                                <span class="font-bold">Roller:</span> <?php echo e(count($kural->alicilar['roller'] ?? [])); ?>

                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="font-bold">Kişiler:</span> <?php echo e(count($kural->alicilar['users'] ?? [])); ?>

                                            </div>
                                            <!--[if BLOCK]><![endif]--><?php if(!empty($kural->alicilar['emails'])): ?>
                                            <div class="flex items-center gap-1 text-amber-600">
                                                <span class="font-bold">Harici:</span> Var
                                            </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $kural->icerik_ayarlari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <!--[if BLOCK]><![endif]--><?php if($val): ?> 
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        <?php echo e(str_replace('_', ' ', ucfirst($key))); ?>

                                                    </span> 
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button type="button" wire:click="manuelGonder(<?php echo e($kural->id); ?>)" class="text-amber-600 hover:text-amber-900" title="Şimdi Gönder">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                        </button>
                                        <button type="button" wire:click="duzenle(<?php echo e($kural->id); ?>)" class="text-indigo-600 hover:text-indigo-900" title="Düzenle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button type="button" wire:click="sil(<?php echo e($kural->id); ?>)" onclick="confirm('Bu kuralı silmek istediğinize emin misiniz?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900" title="Sil">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Henüz tanımlı bir rapor kuralı bulunmamaktadır.
                                    </td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($isModalOpen): ?>
    <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isModalOpen', false)"></div>

            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        <?php echo e($aktifKuralId ? 'Kuralı Düzenle' : 'Yeni Rapor Kuralı Ekle'); ?>

                    </h3>
                    
                    <div class="mt-4 space-y-4">
                    
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Rapor Başlığı</label>
                                <input wire:model="baslik" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['baslik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div class="flex gap-2">
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700">Periyot</label>
                                    
                                    <select wire:model.live="periyot" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border">
                                        <option value="gunluk">Günlük</option>
                                        <option value="haftalik">Haftalık</option>
                                        <option value="aylik">Aylık</option>
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700">Saat</label>
                                    <input wire:model="gonderim_saati" type="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border">
                                </div>
                            </div>

                            
                            
                            
                            <!--[if BLOCK]><![endif]--><?php if($periyot == 'haftalik'): ?>
                            <div class="md:col-span-2 bg-purple-50 p-3 rounded-lg border border-purple-100">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hangi Günler Gönderilsin?</label>
                                <div class="flex flex-wrap gap-3">
                                    <?php
                                        $haftaninGunleri = [
                                            'Monday' => 'Pazartesi', 'Tuesday' => 'Salı', 'Wednesday' => 'Çarşamba',
                                            'Thursday' => 'Perşembe', 'Friday' => 'Cuma', 'Saturday' => 'Cumartesi', 'Sunday' => 'Pazar'
                                        ];
                                    ?>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $haftaninGunleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eng => $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="inline-flex items-center space-x-2 bg-white px-3 py-1.5 rounded border border-gray-200 cursor-pointer hover:border-purple-400">
                                            <input type="checkbox" wire:model="gunler" value="<?php echo e($eng); ?>" class="rounded text-purple-600 focus:ring-purple-500">
                                            <span class="text-sm text-gray-700"><?php echo e($tr); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Birden fazla gün seçebilirsiniz.</p>
                            </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            
                            <!--[if BLOCK]><![endif]--><?php if($periyot == 'aylik'): ?>
                            <div class="md:col-span-2 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Ayın Hangi Günü?</label>
                                <select wire:model="gunler" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                                    <!--[if BLOCK]><![endif]--><?php for($i=1; $i<=31; $i++): ?>
                                        <option value="<?php echo e($i); ?>"><?php echo e($i); ?>. Gün</option>
                                    <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                            </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            

                        </div>

                        <hr>

                        
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-2">Alıcılar</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Roller (Ctrl+Click)</label>
                                    <select wire:model="secili_roller" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm h-32 p-2 border">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $roller; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($rol->id); ?>"><?php echo e($rol->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Kullanıcılar (Ctrl+Click)</label>
                                    <select wire:model="secili_users" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm h-32 p-2 border">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs font-medium text-gray-500 uppercase">Harici E-postalar</label>
                                <textarea wire:model="harici_mailler" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2 border" placeholder="ornek@firma.com, diger@firma.com"></textarea>
                            </div>
                        </div>

                        <hr>

                        
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-2">Rapor İçeriği</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <div>
                                    <span class="block text-xs font-bold text-purple-700 mb-1">Şikayet Sistemi</span>
                                    <div class="space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="icerik.sikayet_ozet" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Genel Özet</span>
                                        </label>
                                        <br>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="icerik.sikayet_detay" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Bölüm Dağılımı</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <span class="block text-xs font-bold text-purple-700 mb-1">İAA Projeleri</span>
                                    <div class="space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="icerik.iaa_ozet" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Genel Durum</span>
                                        </label>
                                        <br>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="icerik.iaa_havuz" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Havuz Bekleyen</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <span class="block text-xs font-bold text-purple-700 mb-1">Diğer</span>
                                    <div class="space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="icerik.disiplin_ozet" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Disiplin Özet</span>
                                        </label>
                                        <br>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="icerik.arabuluculuk_ozet" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">Arabuluculuk</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    
                    <button type="button" wire:click="kaydet" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Kaydet
                    </button>
                    <button type="button" wire:click="$set('isModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        İptal
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/ayarlar/rapor-kurallari.blade.php ENDPATH**/ ?>