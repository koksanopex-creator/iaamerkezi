
<div class="mt-6" x-data="{ open: false }"> 
    
    
    <div class="flex justify-between items-center cursor-pointer" @click="open = !open">
        <h4 class="text-lg font-semibold text-gray-700 flex items-center space-x-2">
            <span>Adım Geçmişi ve Yorumlar</span>
            <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded-full text-xs font-bold"><?php echo e($yorumSayisi); ?></span>
            
            
            <!--[if BLOCK]><![endif]--><?php if($musteriYorumSayisi > 0): ?>
                <span class="flex items-center text-xs font-semibold text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded-full" title="Bu adımda <?php echo e($musteriYorumSayisi); ?> müşteri yorumu var">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                    Müşteri Yorumu
                </span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </h4>
        <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center">
            <span x-text="open ? 'Gizle' : 'Göster'">Göster</span> 
            <svg class="w-5 h-5 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
    </div>

    
    <div x-show="open" x-transition class="mt-4 pl-4 border-l-4 border-gray-200 space-y-6" style="display: none;">

        
        <div class="space-y-5 max-h-96 overflow-y-auto pr-2">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $yorumlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yorum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex space-x-3">
                    <div class="flex-shrink-0">
                        <!--[if BLOCK]><![endif]--><?php if($yorum->user): ?>
                            
                            <a href="<?php echo e(route('profile.show', $yorum->user->id)); ?>">
                                <div class="h-10 w-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold hover:ring-2 hover:ring-offset-2 hover:ring-indigo-500 transition-all" title="<?php echo e($yorum->yapan_kisi_adi); ?>">
                                    <?php echo e(substr($yorum->yapan_kisi_adi, 0, 1)); ?>

                                </div>
                            </a>
                        <?php else: ?>
                            <div class="h-10 w-10 rounded-full bg-yellow-500 text-white flex items-center justify-center font-bold" title="<?php echo e($yorum->yapan_kisi_adi); ?>">
                                M
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    
                    <div class="min-w-0 flex-1">
                        <div class="flex justify-between items-center">
                            <div>
                                
                                <!--[if BLOCK]><![endif]--><?php if($yorum->user_id): ?>
                                    <a href="<?php echo e(route('profile.show', $yorum->user_id)); ?>" class="text-sm font-bold text-gray-900 hover:text-indigo-600 hover:underline transition-colors">
                                        <?php echo e($yorum->yapan_kisi_adi); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-sm font-bold text-gray-900">
                                        <?php echo e($yorum->yapan_kisi_adi); ?>

                                    </span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    <!--[if BLOCK]><![endif]--><?php if($yorum->user): ?>
                                        <?php
                                            $u = $yorum->user;
                                        ?>
                                        <!--[if BLOCK]><![endif]--><?php if(!$u->is_personnel): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200 shadow-sm">
                                                <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.822a2 2 0 00-1.788 0L2.606 5.822a2 2 0 000 3.578l1.447.724a2 2 0 001.788 0l1.447-.724a2 2 0 011.788 0l1.447.724a2 2 0 001.788 0l1.447-.724a2 2 0 011.788 0l1.447.724a2 2 0 001.788 0l1.447-.724a2 2 0 000-3.578l-1.447-.724z" /></svg>
                                                Müşteri Temsilcisi
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200 shadow-sm">
                                                <?php echo e($u->customer?->name ?? 'Firma Belirsiz'); ?>

                                            </span>
                                        <?php else: ?>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $u->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm uppercase tracking-tight">
                                                    <?php echo e($role->name); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($u->bolum): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm uppercase tracking-tight">
                                                    <?php echo e($u->bolum->ad); ?>

                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($u->unvan && !$u->hasRole('Direktör') && !$u->hasRole('Bölüm Lideri')): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-sm italic capitalize">
                                                    <?php echo e($u->unvan); ?>

                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200 shadow-sm">
                                            Dış Müşteri
                                        </span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1.5 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <?php echo e($yorum->created_at->diffForHumans()); ?> (<?php echo e($yorum->created_at->format('d.m.Y H:i')); ?>)
                                </p>
                            </div>
                            
                    
                            
                            
                            <!--[if BLOCK]><![endif]--><?php if(Auth::id() == $yorum->user_id || (Auth::check() && Auth::user()->hasRole('Superadmin'))): ?>
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full transition-colors hover:bg-gray-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                    </button>
                                    <div x-show="open" x-transition class="absolute right-0 z-10 w-32 bg-white shadow-xl rounded-xl border py-1.5 overflow-hidden">
                                        <button wire:click.prevent="editComment(<?php echo e($yorum->id); ?>)" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Düzenle
                                        </button>
                                        <button wire:click.prevent="deleteComment(<?php echo e($yorum->id); ?>)" wire:confirm="Bu yorumu silmek istediğinize emin misiniz?" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Sil
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if($editingCommentId == $yorum->id): ?>
                            
                            <div class="mt-2 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                                <textarea wire:model="editingCommentBody" rows="3" 
                                          class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                          placeholder="Yorumunuzu güncelleyin..."></textarea>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['editingCommentBody'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-sm text-red-600"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                <div class="flex items-center justify-end space-x-2 mt-3">
                                    <button wire:click.prevent="cancelEdit" class="text-xs font-bold text-gray-500 hover:text-gray-700 px-3 py-1.5 rounded-lg transition-colors uppercase tracking-tight">İptal</button>
                                    <button wire:click.prevent="updateComment" class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-1.5 rounded-lg shadow-sm transition-colors uppercase tracking-tight">Değişiklikleri Kaydet</button>
                                </div>
                            </div>
                        <?php else: ?>
                            
                            <div class="mt-2 text-sm text-gray-800 prose prose-sm max-w-none bg-gray-50/30 p-3 rounded-xl border border-gray-100/50">
                                <?php echo nl2br(e($yorum->yorum)); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        

                        
                        <!--[if BLOCK]><![endif]--><?php if($yorum->dosya_yolu): ?>
                            <div class="mt-2">
                                <a href="<?php echo e(asset('storage/' . $yorum->dosya_yolu)); ?>" target="_blank"
                                   class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-white hover:bg-indigo-50 transition-all border border-gray-200 hover:border-indigo-200 shadow-sm group">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span class="text-xs font-bold text-gray-600 group-hover:text-indigo-700"><?php echo e($yorum->dosya_adi ?? 'Eki Görüntüle'); ?></span>
                                </a>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <div class="mt-3 flex items-center space-x-4">
                            <button wire:click="setReply(<?php echo e($yorum->id); ?>)" class="inline-flex items-center text-[11px] font-black text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-widest bg-indigo-50 px-2 py-1 rounded-lg">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                Cevapla
                            </button>
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if($yorum->children->isNotEmpty()): ?>
                            <div class="mt-5 space-y-5 ml-4 border-l-2 border-indigo-100 pl-6">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $yorum->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex space-x-3 group relative">
                                        
                                        <div class="absolute -left-6 top-4 w-6 h-0.5 bg-indigo-100"></div>

                                        <div class="flex-shrink-0">
                                            <!--[if BLOCK]><![endif]--><?php if($child->user): ?>
                                                <div class="h-9 w-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs border border-indigo-100">
                                                    <?php echo e(substr($child->yapan_kisi_adi, 0, 1)); ?>

                                                </div>
                                            <?php else: ?>
                                                <div class="h-9 w-9 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center font-bold text-xs border border-yellow-100">
                                                    M
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-xs font-bold text-gray-900"><?php echo e($child->yapan_kisi_adi); ?></p>
                                                    
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        <!--[if BLOCK]><![endif]--><?php if($child->user): ?>
                                                            <?php $cu = $child->user; ?>
                                                            <!--[if BLOCK]><![endif]--><?php if(!$cu->is_personnel): ?>
                                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-orange-100 text-orange-700 border border-orange-200 shadow-sm uppercase tracking-tighter">
                                                                    Müşteri Temsilcisi
                                                                </span>
                                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-100 text-gray-700 border border-gray-200 shadow-sm uppercase tracking-tighter">
                                                                    <?php echo e($cu->customer?->name ?? 'Firma Belirsiz'); ?>

                                                                </span>
                                                            <?php else: ?>
                                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cu->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200 shadow-sm uppercase tracking-tighter">
                                                                        <?php echo e($role->name); ?>

                                                                    </span>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                <!--[if BLOCK]><![endif]--><?php if($cu->bolum): ?>
                                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-700 border border-blue-200 shadow-sm uppercase tracking-tighter">
                                                                        <?php echo e($cu->bolum->ad); ?>

                                                                    </span>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                <!--[if BLOCK]><![endif]--><?php if($cu->unvan && !$cu->hasRole('Direktör') && !$cu->hasRole('Bölüm Lideri')): ?>
                                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-sm italic capitalize tracking-tighter">
                                                                        <?php echo e($cu->unvan); ?>

                                                                    </span>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <?php else: ?>
                                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-orange-100 text-orange-700 border border-orange-200 shadow-sm uppercase tracking-tighter">
                                                                Dış Müşteri
                                                            </span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-[10px] text-gray-400 font-medium"><?php echo e($child->created_at->diffForHumans()); ?></span>
                                                    
                                                    
                                                    <?php if(Auth::id() == $child->user_id || (Auth::check() && Auth::user()->hasRole('Superadmin'))): ?>
                                                        <div x-data="{ open: false }" class="relative">
                                                            <button @click="open = !open" @click.away="open = false" class="text-gray-300 hover:text-gray-500">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                                            </button>
                                                            <div x-show="open" x-transition class="absolute right-0 z-20 w-28 bg-white shadow-xl rounded-lg border py-1">
                                                                <button wire:click.prevent="editComment(<?php echo e($child->id); ?>)" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 hover:bg-indigo-50 hover:text-indigo-700">Düzenle</button>
                                                                <button wire:click.prevent="deleteComment(<?php echo e($child->id); ?>)" wire:confirm="Bu yorumu silmek istediğinize emin misiniz?" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Sil</button>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>

                                            <!--[if BLOCK]><![endif]--><?php if($editingCommentId == $child->id): ?>
                                                <div class="mt-2 bg-indigo-50/50 p-2 rounded-lg border border-indigo-100">
                                                    <textarea wire:model="editingCommentBody" rows="2" class="block w-full rounded-lg border-gray-200 text-xs focus:ring-indigo-500"></textarea>
                                                    <div class="flex justify-end gap-2 mt-2">
                                                        <button wire:click="cancelEdit" class="text-[10px] text-gray-500 uppercase font-bold">İptal</button>
                                                        <button wire:click="updateComment" class="text-[10px] text-white bg-indigo-600 px-2 py-1 rounded uppercase font-bold">Güncelle</button>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-xs text-gray-700 mt-2 bg-gray-50/50 p-2 rounded-lg border border-gray-100">
                                                    <?php echo nl2br(e($child->yorum)); ?>

                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if($child->dosya_yolu): ?>
                                                <div class="mt-2">
                                                    <a href="<?php echo e(asset('storage/' . $child->dosya_yolu)); ?>" target="_blank" class="inline-flex items-center text-[10px] text-indigo-600 hover:text-indigo-800 font-bold bg-indigo-50/50 px-2 py-0.5 rounded border border-indigo-100">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                        <?php echo e($child->dosya_adi ?? 'Ekli Dosya'); ?>

                                                    </a>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            
                                            <div class="mt-2">
                                                <button wire:click="setReply(<?php echo e($child->id); ?>)" class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-tighter opacity-0 group-hover:opacity-100 transition-opacity">
                                                    Cevapla
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-gray-500">Bu adım için henüz bir yorum veya kayıt eklenmemiş.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($kullaniciYetkiliMi || $isMusteri): ?>
            <div class="pt-6 border-t border-gray-200">
                <!--[if BLOCK]><![endif]--><?php if(session()->has('yorum_success')): ?>
                    <div class="mb-4 text-sm text-green-700 bg-green-100 p-3 rounded-lg"><?php echo e(session('yorum_success')); ?></div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php if(session()->has('yorum_error')): ?>
                    <div class="mb-4 text-sm text-red-700 bg-red-100 p-3 rounded-lg"><?php echo e(session('yorum_error')); ?></div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <form wire:submit="addYorum">
                    <div>
                        
                        <!--[if BLOCK]><![endif]--><?php if($replyingToCommentId): ?>
                            <div class="mb-3 flex items-center justify-between bg-indigo-50 px-3 py-2 rounded-lg border border-indigo-100 animate-pulse">
                                <div class="flex items-center text-xs text-indigo-700">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    <strong><?php echo e($replyingToUserName); ?></strong> kişisine cevap veriliyor...
                                </div>
                                <button type="button" wire:click="cancelReply" class="text-[10px] font-bold text-red-600 hover:text-red-800 uppercase tracking-tighter">İptal</button>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <label for="yeniYorum-<?php echo e($step_id); ?>" class="block text-sm font-medium text-gray-700">
                            <?php echo e($replyingToCommentId ? 'Cevabınız' : 'Yorum Ekle'); ?>

                        </label>
                        <textarea wire:model="yeniYorum" id="yeniYorum-<?php echo e($step_id); ?>" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                  placeholder="<?php echo e($replyingToCommentId ? 'Cevabınızı yazın...' : 'Bir yorum veya güncelleme notu yazın...'); ?>"
                                  x-on:focus-comment-input.window="$el.focus()"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['yeniYorum'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-sm text-red-600"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="mt-4">
                        <label for="yeniDosya-<?php echo e($step_id); ?>" class="block text-sm font-medium text-gray-700">Dosya Ekle (Opsiyonel, Maks 5MB)</label>
                        <input type="file" wire:model="yeniDosya" id="yeniDosya-<?php echo e($step_id); ?>" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer">
                        
                        <div wire:loading wire:target="yeniDosya" class="text-sm text-gray-500 mt-1">Yükleniyor...</div>
                        <!--[if BLOCK]><![endif]--><?php if($yeniDosya && !$errors->has('yeniDosya')): ?>
                            <div class="text-sm text-green-600 mt-1">Dosya seçildi: <?php echo e($yeniDosya->getClientOriginalName()); ?></div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['yeniDosya'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-sm text-red-600"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                            <span wire:loading.remove wire:target="addYorum">Yorumu Gönder</span>
                            <span wire:loading wire:target="addYorum">Gönderiliyor...</span>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        
    </div>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/admin/proje-adim-yorumlari.blade.php ENDPATH**/ ?>