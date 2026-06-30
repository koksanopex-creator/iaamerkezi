<div>
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="close"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Proje Ekibini Yönet
                                </h3>
                                <p class="text-sm text-gray-500 mt-1"><?php echo e(Str::limit($projeBasligi, 40)); ?></p>
                                
                                
                                <div class="mt-4 relative">
                                    <input type="text" wire:model.live.debounce.300ms="aramaMetni" placeholder="Kullanıcı Ara (İsim yazın...)" 
                                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                    
                                    
                                    <!--[if BLOCK]><![endif]--><?php if(!empty($bulunanKullanicilar)): ?>
                                        <ul class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $bulunanKullanicilar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50 flex justify-between items-center">
                                                    <div class="flex items-center">
                                                        <span class="font-medium block truncate"><?php echo e($user->name); ?></span>
                                                        
                                                        <span class="text-xs text-gray-400 ml-2">(<?php echo e($user->bolum->ad ?? '-'); ?>)</span>
                                                    </div>
                                                    <button wire:click="uyeEkle(<?php echo e($user->id); ?>)" class="text-white bg-indigo-600 hover:bg-indigo-700 px-2 py-1 rounded text-xs">Davet Et</button>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </ul>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Mevcut Ekip (<?php echo e(count($mevcutUyeListesi)); ?>)</h4>
                        
                        
                        <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
                            <div class="mb-2 text-xs text-green-600 bg-green-100 p-2 rounded">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php if(session()->has('error')): ?>
                            <div class="mb-2 text-xs text-red-600 bg-red-100 p-2 rounded">
                                <?php echo e(session('error')); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $mevcutUyeListesi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                    
                                    
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <!--[if BLOCK]><![endif]--><?php if($uye->profile_photo_path): ?>
                                                <img src="<?php echo e(asset('storage/'.$uye->profile_photo_path)); ?>" class="w-10 h-10 rounded-full object-cover border">
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-600 border border-gray-300">
                                                    <?php echo e(substr($uye->name, 0, 1)); ?>

                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            
                                            <!--[if BLOCK]><![endif]--><?php if(isset($uye->pivot)): ?>
                                                <!--[if BLOCK]><![endif]--><?php if($uye->pivot->rol == 'Lider'): ?>
                                                    <div class="absolute -bottom-1 -right-1 bg-indigo-500 text-white p-0.5 rounded-full" title="Lider">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                                    </div>
                                                <?php elseif($uye->pivot->durum == 'onaylandi'): ?>
                                                    <div class="absolute -bottom-1 -right-1 bg-green-500 text-white p-0.5 rounded-full" title="Kabul Etti">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                <?php elseif($uye->pivot->durum == 'bekliyor'): ?>
                                                    <div class="absolute -bottom-1 -right-1 bg-amber-500 text-white p-0.5 rounded-full animate-pulse" title="Bekliyor">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </div>
                                                <?php elseif($uye->pivot->durum == 'reddedildi'): ?>
                                                    <div class="absolute -bottom-1 -right-1 bg-red-500 text-white p-0.5 rounded-full" title="Reddedildi">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900"><?php echo e($uye->name); ?></span>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <!--[if BLOCK]><![endif]--><?php if(isset($uye->pivot)): ?>
                                                    <!--[if BLOCK]><![endif]--><?php if($uye->pivot->rol == 'Lider'): ?>
                                                        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded font-semibold">Proje Lideri</span>
                                                    <?php else: ?>
                                                        <!--[if BLOCK]><![endif]--><?php if($uye->pivot->durum == 'bekliyor'): ?>
                                                            <span class="text-[10px] bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded font-semibold">Davet Bekleniyor</span>
                                                        <?php elseif($uye->pivot->durum == 'onaylandi'): ?>
                                                            <span class="text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded font-semibold">Ekip Üyesi (Aktif)</span>
                                                        <?php elseif($uye->pivot->durum == 'reddedildi'): ?>
                                                            <span class="text-[10px] bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-semibold">Daveti Reddetti</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <!--[if BLOCK]><![endif]--><?php if($uye->id != $liderId): ?>
                                        <div class="flex items-center gap-2">
                                            <?php if(isset($uye->pivot) && $uye->pivot->durum == 'bekliyor'): ?>
                                                
                                                <button wire:click="davetIptal(<?php echo e($uye->id); ?>)" 
                                                        class="group/cancel flex items-center justify-center w-8 h-8 rounded-full bg-amber-50 text-amber-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 border border-amber-100 hover:border-red-100 shadow-sm" 
                                                        title="Daveti İptal Et">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            <?php elseif(isset($uye->pivot) && $uye->pivot->durum == 'reddedildi'): ?>
                                                
                                                <button wire:click="uyeCikar(<?php echo e($uye->id); ?>)" class="text-gray-400 hover:text-red-600 transition-colors" title="Listeden Kaldır">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            <?php else: ?>
                                                
                                                <button wire:click="uyeCikar(<?php echo e($uye->id); ?>)" class="text-gray-400 hover:text-red-600 transition-colors" title="Ekip'ten Çıkar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="close" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/admin/squad-yonetim-modal.blade.php ENDPATH**/ ?>