<div>
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showModal', false)"></div>

            
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
                
                
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 flex justify-between items-center shadow-lg">
                    <h3 class="text-xl font-black text-white flex items-center tracking-tight">
                        <div class="p-2 bg-white/20 rounded-xl mr-3 backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        </div>
                        Şikayete Müşteri Ata
                    </h3>
                    <button type="button" wire:click="$set('showModal', false)" class="text-white/80 hover:text-white transition-colors bg-white/10 hover:bg-white/20 p-2 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-6 border-b border-gray-50 bg-indigo-50/30">
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3 shadow-sm">
                        <div class="p-2 bg-amber-100 rounded-xl text-amber-600 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-amber-900 text-sm">Bunu biliyor muydunuz?</h4>
                            <p class="text-xs text-amber-800 leading-relaxed mt-1 font-medium italic opacity-80">
                                Müşteri atadığınızda bu şikayet Takvim sistemine otomatik olarak aktarılacak ve ilgili firmaya senkronize edilecektir.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-5">
                    
                    <div class="flex p-1.5 bg-gray-100 rounded-2xl mb-8 shadow-inner">
                        <button type="button" wire:click="$set('activeTab', 'mevcut')" 
                            class="flex-1 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e($activeTab === 'mevcut' ? 'bg-white text-indigo-700 shadow-md transform scale-[1.02]' : 'text-gray-500 hover:text-gray-700 hover:bg-white/50'); ?>">
                            Mevcut Müşteri
                        </button>
                        <button type="button" wire:click="$set('activeTab', 'yeni')" 
                            class="flex-1 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e($activeTab === 'yeni' ? 'bg-white text-indigo-700 shadow-md transform scale-[1.02]' : 'text-gray-500 hover:text-gray-700 hover:bg-white/50'); ?>">
                            Yeni Müşteri Oluştur
                        </button>
                    </div>

                    
                    <!--[if BLOCK]><![endif]--><?php if($activeTab === 'mevcut'): ?>
                        <div class="space-y-6 animate-fadeIn">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Firma Seçimi</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <select wire:model.live="selectedCustomerId" class="block w-full pl-11 pr-10 py-4 bg-gray-50 border-gray-200 text-gray-900 text-sm font-bold rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all focus:bg-white">
                                        <option value="">Firma seçiniz...</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedCustomerId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block px-1 font-bold"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if($selectedCustomerId): ?>
                            <div class="animate-slideUp">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Yetkili Kişi</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <select wire:model.live="selectedRepId" class="block w-full pl-11 pr-10 py-4 bg-gray-50 border-gray-200 text-gray-900 text-sm font-bold rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm transition-all focus:bg-white">
                                        <option value="">Yetkili seçiniz...</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $representatives; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($rep->id); ?>"><?php echo e($rep->name); ?> (<?php echo e($rep->unvan ?? 'Yetkili'); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedRepId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block px-1 font-bold"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                    
                    <?php else: ?>
                        <div class="space-y-6 animate-fadeIn overflow-y-auto max-h-[60vh] pr-2 custom-scrollbar">
                            
                            
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 pb-2">1. Firma Bilgileri</h4>
                                
                                
                                <div class="flex items-center space-x-6 p-4 bg-gray-50 rounded-2xl border border-gray-100 group transition-all hover:bg-white hover:shadow-md">
                                    <div class="shrink-0 relative">
                                        <!--[if BLOCK]><![endif]--><?php if($logo): ?>
                                            <img src="<?php echo e($logo->temporaryUrl()); ?>" class="h-20 w-20 object-cover rounded-2xl border-2 border-indigo-100 shadow-md">
                                            <button wire:click="$set('logo', null)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-lg p-1 shadow-lg hover:bg-red-600 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        <?php else: ?>
                                            <div class="h-20 w-20 rounded-2xl bg-white flex items-center justify-center text-gray-400 border-2 border-dashed border-gray-300 group-hover:border-indigo-300 group-hover:bg-indigo-50 transition-all">
                                                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-black text-gray-700 mb-1">Firma Logosu</label>
                                        <input type="file" wire:model="logo" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition-all cursor-pointer">
                                        <p class="mt-1.5 text-[10px] text-gray-400 font-bold uppercase tracking-wider">PNG, JPG, GIF (Max 2MB)</p>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-[10px] font-bold block mt-1 uppercase"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Firma Adı <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="name" class="w-full rounded-2xl border-gray-200 bg-gray-50 py-3.5 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-all" placeholder="Örn: ABC Lojistik A.Ş.">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-[10px] font-bold block mt-1 uppercase"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Vergi No</label>
                                        <input type="text" wire:model="tax_number" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-2xl border-gray-200 bg-gray-50 py-3.5 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Konum</label>
                                        <select wire:model="location_type" class="w-full rounded-2xl border-gray-200 bg-gray-50 py-3.5 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-all">
                                            <option value="Yurt İçi">Yurt İçi</option>
                                            <option value="Yurt Dışı">Yurt Dışı</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Firma Telefonu</label>
                                        <input type="tel" wire:model="phone" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-2xl border-gray-200 bg-gray-50 py-3.5 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-all" placeholder="0212...">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Firma Adresi</label>
                                        <textarea wire:model="address" rows="2" class="w-full rounded-2xl border-gray-200 bg-gray-50 py-3.5 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-all custom-scrollbar" placeholder="Adres detayları..."></textarea>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="bg-indigo-50/50 p-6 rounded-3xl border border-indigo-100 space-y-5">
                                <div class="flex justify-between items-center border-b border-indigo-200/50 pb-3">
                                    <h4 class="text-[10px] font-black text-indigo-900/60 uppercase tracking-[0.2em] flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        YETKİLİ KİŞİLER
                                    </h4>
                                    <button wire:click="addRepRow" type="button" class="text-[10px] flex items-center bg-indigo-600 text-white px-3 py-1.5 rounded-xl font-black uppercase tracking-wider hover:bg-indigo-700 transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        Yeni Kişi Ekle
                                    </button>
                                </div>

                                <div class="space-y-6">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="relative bg-white p-5 rounded-2xl border border-indigo-100 shadow-sm transition-all hover:shadow-lg group/rep translate-y-0 hover:-translate-y-0.5">
                                            
                                            <!--[if BLOCK]><![endif]--><?php if(count($reps) > 1): ?>
                                                <button type="button" wire:click="removeRepRow(<?php echo e($index); ?>)" class="absolute -top-3 -right-3 bg-red-100 text-red-600 rounded-xl p-2 hover:bg-red-600 hover:text-white shadow-lg transition-all border-2 border-white opacity-0 group-hover/rep:opacity-100" title="Bu kişiyi sil">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <div class="grid grid-cols-2 gap-4 mb-4">
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-0.5">Ad Soyad <span class="text-red-500">*</span></label>
                                                    <input type="text" wire:model="reps.<?php echo e($index); ?>.name" class="w-full rounded-xl border-gray-100 bg-gray-50 py-2.5 focus:bg-white focus:border-indigo-400 transition-all text-sm font-bold shadow-inner" placeholder="Ad Soyad">
                                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["reps.{$index}.name"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-[10px] font-bold block mt-1 uppercase"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-0.5">Ünvan</label>
                                                    <input type="text" wire:model="reps.<?php echo e($index); ?>.title" class="w-full rounded-xl border-gray-100 bg-gray-50 py-2.5 focus:bg-white focus:border-indigo-400 transition-all text-sm font-bold shadow-inner" placeholder="Örn: Müdür">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-0.5">E-posta <span class="text-red-500">*</span></label>
                                                    <input type="email" wire:model="reps.<?php echo e($index); ?>.email" class="w-full rounded-xl border-gray-100 bg-gray-50 py-2.5 focus:bg-white focus:border-indigo-400 transition-all text-sm font-bold shadow-inner" placeholder="mail@ornek.com">
                                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["reps.{$index}.email"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-[10px] font-bold block mt-1 uppercase"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-0.5">Telefon</label>
                                                    <input type="tel" wire:model="reps.<?php echo e($index); ?>.phone" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full rounded-xl border-gray-100 bg-gray-50 py-2.5 focus:bg-white focus:border-indigo-400 transition-all text-sm font-bold shadow-inner" placeholder="05...">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="absolute -left-3 top-1/2 transform -translate-y-1/2">
                                                <span class="flex items-center justify-center w-6 h-6 rounded-xl bg-indigo-600 text-white text-[10px] font-black shadow-lg">
                                                    <?php echo e($loop->iteration); ?>

                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                
                                <div class="text-center">
                                    <button wire:click="addRepRow" type="button" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-black uppercase tracking-widest flex items-center justify-center w-full py-4 border-2 border-dashed border-indigo-200 rounded-2xl bg-white hover:bg-indigo-50 transition-all group">
                                        <svg class="w-4 h-4 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        + Başka bir yetkili daha ekle
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div class="bg-gray-50 px-8 py-6 flex items-center justify-between border-t border-gray-100 rounded-b-3xl">
                    <button type="button" wire:click="$set('showModal', false)" class="text-sm font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">
                        Vazgeç
                    </button>
                    
                    <button type="button" wire:click="assign" wire:loading.attr="disabled"
                        class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-black uppercase tracking-widest rounded-2xl hover:from-indigo-700 hover:to-purple-700 shadow-xl shadow-indigo-200 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 disabled:opacity-50 flex items-center gap-3">
                        <svg wire:loading wire:target="assign" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Müşteriyi Ata ve Senkronize Et
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
        .animate-slideUp { animation: slideUp 0.4s ease-out; }
    </style>
</div>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/admin/sikayet-musteri-atama-modal.blade.php ENDPATH**/ ?>