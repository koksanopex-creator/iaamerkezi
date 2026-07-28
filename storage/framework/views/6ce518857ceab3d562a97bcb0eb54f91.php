<div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
        
        
        <div class="group">
            <label class="flex items-center font-bold text-sm text-gray-700 mb-2">
                <div class="p-1.5 bg-indigo-100 rounded-md text-indigo-600 mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                Firma Seçimi <span class="ml-1 text-red-500">*</span>
            </label>
            <div class="flex gap-2">
                <div class="relative w-full">
                    <select wire:model.live="selectedCustomerId" wire:key="select-customer-<?php echo e(count($customers)); ?>" name="customer_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 pl-4 pr-10 text-sm">
                        <option value="">-- Firma Seçiniz --</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($customer->id); ?>" wire:key="customer-opt-<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                <button type="button" wire:click="$set('showCreateModal', true)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 flex items-center justify-center" title="Yeni Firma Ekle">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>
        </div>

        
        <div class="group">
            <label class="flex items-center font-bold text-sm text-gray-700 mb-2">
                <div class="p-1.5 bg-green-100 rounded-md text-green-600 mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                Ana Yetkili (İletişim Kişisi)
            </label>
            <select wire:model.live="selectedRepId" wire:key="select-rep-<?php echo e($selectedCustomerId); ?>-<?php echo e(count($representatives)); ?>" name="yetkili_user_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 py-2.5 pl-4 pr-10 text-sm bg-white disabled:bg-gray-100 disabled:text-gray-400" <?php if(empty($representatives)): ?> disabled <?php endif; ?>>
                <option value="">-- <!--[if BLOCK]><![endif]--><?php if(empty($representatives)): ?> Önce Firma Seçiniz <?php else: ?> Lütfen Seçiniz <?php endif; ?><!--[if ENDBLOCK]><![endif]--> --</option>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $representatives; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($rep->id); ?>" wire:key="rep-opt-<?php echo e($rep->id); ?>"><?php echo e($rep->name); ?> (<?php echo e($rep->pivot->unvan ?? $rep->unvan ?? 'Yetkili'); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if(!empty($representatives)): ?>
            <div class="md:col-span-2 mt-4 p-4 bg-white rounded-xl border border-dashed border-gray-300">
                <label class="flex items-center font-bold text-xs text-gray-400 uppercase tracking-wider mb-3">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Ek İlgililer (Bildirim Gidecek Diğer Yetkililer)
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $representatives; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <!--[if BLOCK]><![endif]--><?php if($rep->id != $selectedRepId): ?>
                            <label class="relative flex items-center p-3 rounded-lg border border-gray-100 bg-gray-50/50 hover:bg-white hover:border-indigo-200 transition-all cursor-pointer group">
                                <input type="checkbox" 
                                       wire:model.live="selectedEkRepIds" 
                                       value="<?php echo e($rep->id); ?>"
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="ml-3">
                                    <p class="text-xs font-bold text-gray-700 group-hover:text-indigo-700"><?php echo e($rep->name); ?></p>
                                    <p class="text-[10px] text-gray-400"><?php echo e($rep->pivot->unvan ?? $rep->unvan ?? 'Yetkili'); ?></p>
                                </div>
                            </label>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <!--[if BLOCK]><![endif]--><?php if(empty($selectedEkRepIds)): ?>
                    <p class="text-[10px] text-gray-400 mt-2 italic">* Şikayet süreciyle ilgili bilgilendirilmesini istediğiniz diğer kişileri seçebilirsiniz.</p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($successMessage): ?>
        <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r text-emerald-700 text-sm flex items-center justify-between animate-fadeIn">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span><?php echo e($successMessage); ?></span>
            </div>
            <button type="button" wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700 ml-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($showCreateModal): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showCreateModal', false)"></div>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    
                    <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600 mr-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            Hızlı Müşteri Ekle
                        </h3>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 space-y-6">
                        
                        
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b pb-1">Firma Bilgileri</h4>
                            
                            
                            <div class="flex items-center space-x-4">
                                <div class="shrink-0">
                                    <!--[if BLOCK]><![endif]--><?php if($logo): ?>
                                        <img src="<?php echo e($logo->temporaryUrl()); ?>" class="h-16 w-16 object-cover rounded-full border border-gray-200">
                                    <?php else: ?>
                                        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Firma Logosu</label>
                                    <input type="file" wire:model="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF (Max 2MB)</p>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Firma Adı <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Örn: ABC Lojistik A.Ş.">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Vergi No</label>
                                    <input type="text" wire:model="tax_number" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Konum</label>
                                    <select wire:model="location_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="Yurt İçi">Yurt İçi</option>
                                        <option value="Yurt Dışı">Yurt Dışı</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Firma Telefonu</label>
                                    <input type="tel" wire:model="phone" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0212...">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Firma Adresi</label>
                                    <textarea wire:model="address" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Adres detayları..."></textarea>
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-4">
                            <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Yetkili Kişiler
                                </h4>
                                <button wire:click="addRepRow" type="button" class="text-xs flex items-center text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Yeni Kişi Ekle
                                </button>
                            </div>

                            <div class="space-y-6">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="relative bg-white p-3 rounded-lg border border-slate-200 shadow-sm transition-all hover:shadow-md">
                                        
                                        
                                        <!--[if BLOCK]><![endif]--><?php if(count($reps) > 1): ?>
                                            <button type="button" wire:click="removeRepRow(<?php echo e($index); ?>)" type="button" class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1 hover:bg-red-200 shadow-sm" title="Bu kişiyi sil">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        <div class="grid grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Ad Soyad <span class="text-red-500">*</span></label>
                                                
                                                <input type="text" wire:model="reps.<?php echo e($index); ?>.name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Ad Soyad">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["reps.{$index}.name"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Ünvan</label>
                                                <input type="text" wire:model="reps.<?php echo e($index); ?>.title" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Örn: Müdür">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">E-posta <span class="text-red-500">*</span></label>
                                                <input type="email" wire:model="reps.<?php echo e($index); ?>.email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="mail@ornek.com">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["reps.{$index}.email"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-600 mb-1">Telefon</label>
                                                <input type="tel" wire:model="reps.<?php echo e($index); ?>.phone" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="05...">
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="absolute -left-2 top-1/2 transform -translate-y-1/2">
                                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-slate-500 text-[10px] font-bold">
                                                <?php echo e($loop->iteration); ?>

                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            
                            
                            <div class="text-center mt-2">
                                <button wire:click="addRepRow" type="button" class="text-xs text-indigo-600 hover:underline">+ Başka bir yetkili daha ekle</button>
                            </div>
                        </div>

                    </div>

                    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="button" wire:click="storeNewCustomer" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-indigo-800 shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                            Kaydet
                        </button>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="w-full sm:w-auto px-6 py-2.5 bg-white text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 shadow-sm transition-all duration-200">
                            İptal
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        
        
        <input type="hidden" name="customer_id" value="<?php echo e($selectedCustomerId); ?>">
        <input type="hidden" name="yetkili_user_id" value="<?php echo e($selectedRepId); ?>">
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $selectedEkRepIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <input type="hidden" name="ek_yetkili_user_ids[]" value="<?php echo e($ekId); ?>">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    
</div> <?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/sikayet-musteri-secimi.blade.php ENDPATH**/ ?>