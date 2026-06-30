<div x-show="activeTab === 'yorumlar'" class="space-y-6">
    
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h4 class="text-sm font-bold text-gray-700 mb-3">Bu profil hakkında geri bildirim yaz</h4>
        <form action="<?php echo e(route('profile.comment.store', $user->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <textarea name="yorum" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition text-sm" placeholder="Tebrik mesajı veya not bırakın..."><?php echo e(request('bday_msg') ? 'Doğum günün kutlu olsun! 🎉 Mutlu, sağlıklı ve başarılı bir yıl dilerim.' : (request('anniv_msg') ? (request('years') ? 'Şirketimizdeki ' . request('years') . '. iş yıldönümün kutlu olsun! 🎊 Başarılarının devamını dilerim.' : 'İş yıldönümün kutlu olsun! 🎊 Şirketimizdeki başarılarının devamını dilerim.') : '')); ?></textarea>
            <div class="mt-2 text-right">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wide transition-colors">Gönder</button>
            </div>
        </form>
    </div>

    
    <div class="space-y-6 max-h-[800px] overflow-y-auto custom-scrollbar pr-2" id="yorumlar">
        <?php $__empty_1 = true; $__currentLoopData = $yorumlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yorum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $yazan = $yorum->yazan;
                $yazanUnvan = $yazan->unvan;
                $isFocused = request('focused_comment') == $yorum->id;
                
                if (!$yazanUnvan || strtolower($yazanUnvan) == 'personel') {
                    if ($yazan->hasRole('Superadmin')) $yazanUnvan = 'Sistem Yöneticisi';
                    elseif ($yazan->hasRole('Yonetim')) $yazanUnvan = 'Yönetim Kurulu';
                    elseif ($yazan->hasRole('Direktör')) $yazanUnvan = 'Direktör';
                    elseif ($yazan->hasRole('Bölüm Lideri')) $yazanUnvan = 'Bölüm Lideri';
                    else $yazanUnvan = 'Personel';
                }

                $bgColor = 'bg-gray-50';
                $textColor = 'text-gray-500';
                $borderColor = 'border-gray-100';
                
                if ($yazan->hasRole(['Superadmin', 'Yonetim'])) {
                    $bgColor = 'bg-purple-50';
                    $textColor = 'text-purple-600';
                    $borderColor = 'border-purple-200';
                } elseif ($yazan->hasRole('Direktör')) {
                    $bgColor = 'bg-emerald-50';
                    $textColor = 'text-emerald-600';
                    $borderColor = 'border-emerald-200';
                } elseif ($yazan->hasRole('Bölüm Lideri')) {
                    $bgColor = 'bg-indigo-50';
                    $textColor = 'text-indigo-600';
                    $borderColor = 'border-indigo-200';
                }
            ?>

            <div id="comment-<?php echo e($yorum->id); ?>" x-data="{ showReplyForm: false, isEditing: false }" 
                 class="bg-white p-5 rounded-xl shadow-sm border <?php echo e($isFocused ? 'border-2 border-indigo-500 ring-4 ring-indigo-50 animate-[pulse_2s_infinite]' : 'border-gray-100'); ?> group transition-all duration-500">
                <div class="flex space-x-4">
                    <div class="flex-shrink-0">
                        <a href="<?php echo e(route('profile.show', $yorum->yazan_user_id)); ?>" target="_blank">
                            <?php if($yazan->profile_photo_path): ?>
                                <img class="h-10 w-10 rounded-full object-cover border <?php echo e($yazan->hasRole(['Superadmin', 'Yonetim', 'Direktör', 'Bölüm Lideri']) ? 'border-2 border-indigo-400 p-0.5' : 'border-gray-200'); ?> hover:opacity-80 transition-opacity" src="<?php echo e(asset('storage/' . $yazan->profile_photo_path)); ?>">
                            <?php else: ?>
                                <div class="h-10 w-10 rounded-full <?php echo e($bgColor); ?> flex items-center justify-center font-bold <?php echo e($textColor); ?> border <?php echo e($borderColor); ?> hover:bg-gray-200 transition-colors"><?php echo e(substr($yazan->name, 0, 1)); ?></div>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h5 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <a href="<?php echo e(route('profile.show', $yorum->yazan_user_id)); ?>" target="_blank" class="hover:text-indigo-600 hover:underline transition-colors">
                                        <?php echo e($yazan->name); ?>

                                    </a>
                                    <?php if($yazan->hasRole(['Superadmin', 'Yonetim', 'Direktör', 'Bölüm Lideri'])): ?>
                                        <svg class="w-3.5 h-3.5 <?php echo e($textColor); ?>" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <?php endif; ?>
                                </h5>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    
                                    <span class="text-[10px] font-black <?php echo e($textColor); ?>"><?php echo e($yazanUnvan); ?></span>
                                    
                                    <span class="text-[10px] text-gray-300">|</span>
                                    
                                    
                                    <div class="flex items-center bg-gray-900 text-white rounded-md px-1.5 py-0.5 group-hover:bg-indigo-600 transition-colors">
                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                        <span class="text-[9px] font-bold uppercase tracking-wider"><?php echo e($yazan->bolum->ad ?? 'Genel'); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-gray-400"><?php echo e($yorum->created_at->diffForHumans()); ?></span>
                                <?php
                                    $isAuthor = auth()->id() == $yorum->yazan_user_id;
                                    $canDelete = $isAuthor || 
                                                 (auth()->id() == $yorum->user_id && !$yazan->hasRole('Superadmin')) || 
                                                 auth()->user()->hasRole('Superadmin');
                                ?>
                                
                                <?php if($isAuthor || auth()->user()->hasRole('Superadmin')): ?>
                                    <button @click="isEditing = !isEditing" class="text-gray-300 hover:text-indigo-500 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                <?php endif; ?>

                                <?php if($canDelete): ?>
                                    <form action="<?php echo e(route('profile.comment.destroy', $yorum->id)); ?>" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');" class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div x-show="!isEditing">
                            <p class="text-sm text-gray-700 font-medium mt-3 leading-relaxed <?php echo e($yazan->hasRole(['Superadmin', 'Yonetim', 'Direktör', 'Bölüm Lideri']) ? 'bg-indigo-50/40 p-3 rounded-xl border border-indigo-100/50 shadow-sm' : ''); ?>"><?php echo e($yorum->yorum); ?></p>
                            <button @click="showReplyForm = !showReplyForm" class="text-xs text-indigo-600 hover:text-indigo-800 font-black mt-2 flex items-center gap-1.5 uppercase tracking-tighter">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                Cevap ver
                            </button>
                        </div>

                        
                        <div x-show="isEditing" class="mt-2" style="display: none;">
                            <form action="<?php echo e(route('profile.comment.update', $yorum->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <textarea name="yorum" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"><?php echo e($yorum->yorum); ?></textarea>
                                <div class="mt-2 flex justify-end gap-2">
                                    <button type="button" @click="isEditing = false" class="text-xs text-gray-500 font-bold px-3 py-1.5 hover:bg-gray-100 rounded-md transition-colors">İptal</button>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-colors">Güncelle</button>
                                </div>
                            </form>
                        </div>
                        
                        
                        <div x-show="showReplyForm" class="mt-3 pl-4 border-l-4 border-indigo-200" style="display: none;">
                            <form action="<?php echo e(route('profile.comment.store', $user->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="parent_id" value="<?php echo e($yorum->id); ?>">
                                <textarea name="yorum" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm text-xs focus:border-indigo-500 focus:ring focus:ring-indigo-200" placeholder="Cevabınızı yazın..."></textarea>
                                <div class="mt-2 text-right">
                                    <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wide transition-colors">Cevapla</button>
                                </div>
                            </form>
                        </div>

                        
                        <?php if($yorum->cevaplar->count() > 0): ?>
                            <div class="mt-6 space-y-4 pl-4 border-l-2 border-gray-100">
                                <?php $__currentLoopData = $yorum->cevaplar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cevap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $c_yazan = $cevap->yazan;
                                        $c_yazanUnvan = $c_yazan->unvan;
                                        $isFocusedReply = request('focused_comment') == $cevap->id;

                                        if (!$c_yazanUnvan || strtolower($c_yazanUnvan) == 'personel') {
                                            if ($c_yazan->hasRole('Superadmin')) $c_yazanUnvan = 'Sistem Yöneticisi';
                                            elseif ($c_yazan->hasRole('Yonetim')) $c_yazanUnvan = 'Yönetim Kurulu';
                                            elseif ($c_yazan->hasRole('Direktör')) $c_yazanUnvan = 'Direktör';
                                            elseif ($c_yazan->hasRole('Bölüm Lideri')) $c_yazanUnvan = 'Bölüm Lideri';
                                            else $c_yazanUnvan = 'Personel';
                                        }

                                        $c_bgColor = 'bg-gray-50';
                                        $c_textColor = 'text-gray-500';
                                        $c_borderColor = 'border-gray-100';
                                        
                                        if ($c_yazan->hasRole(['Superadmin', 'Yonetim'])) {
                                            $c_bgColor = 'bg-purple-50';
                                            $c_textColor = 'text-purple-600';
                                            $c_borderColor = 'border-purple-200';
                                        } elseif ($c_yazan->hasRole('Direktör')) {
                                            $c_bgColor = 'bg-emerald-50';
                                            $c_textColor = 'text-emerald-600';
                                            $c_borderColor = 'border-emerald-200';
                                        } elseif ($c_yazan->hasRole('Bölüm Lideri')) {
                                            $c_bgColor = 'bg-indigo-50';
                                            $c_textColor = 'text-indigo-600';
                                            $c_borderColor = 'border-indigo-200';
                                        }
                                    ?>
                                    <div id="comment-<?php echo e($cevap->id); ?>" x-data="{ isEditingReply: false }" 
                                         class="flex space-x-3 group/reply transition-all duration-500 <?php echo e($isFocusedReply ? 'p-2 rounded-xl bg-indigo-50 border-2 border-indigo-200 ring-2 ring-indigo-50' : ''); ?>">
                                        <div class="flex-shrink-0">
                                            <a href="<?php echo e(route('profile.show', $cevap->yazan_user_id)); ?>" target="_blank">
                                                <?php if($c_yazan->profile_photo_path): ?>
                                                    <img class="h-8 w-8 rounded-full object-cover border <?php echo e($c_yazan->hasRole(['Superadmin', 'Yonetim', 'Direktör', 'Bölüm Lideri']) ? 'border-2 border-indigo-400 p-0.5' : 'border-gray-200'); ?> hover:opacity-80" src="<?php echo e(asset('storage/' . $c_yazan->profile_photo_path)); ?>">
                                                <?php else: ?>
                                                    <div class="h-8 w-8 rounded-full <?php echo e($c_bgColor); ?> flex items-center justify-center font-bold text-xs <?php echo e($c_textColor); ?> hover:bg-gray-200 transition-colors"><?php echo e(substr($c_yazan->name, 0, 1)); ?></div>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="flex-1 bg-gray-50 p-3 rounded-lg relative <?php echo e($c_yazan->hasRole(['Superadmin', 'Yonetim', 'Direktör', 'Bölüm Lideri']) ? 'border-l-4 ' . $c_borderColor : 'border border-gray-100'); ?>">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <h6 class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                                        <a href="<?php echo e(route('profile.show', $cevap->yazan_user_id)); ?>" target="_blank" class="hover:text-indigo-600 hover:underline"><?php echo e($c_yazan->name); ?></a>
                                                        <?php if($c_yazan->hasRole(['Superadmin', 'Yonetim', 'Direktör', 'Bölüm Lideri'])): ?>
                                                            <svg class="w-2.5 h-2.5 <?php echo e($c_textColor); ?>" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                        <?php endif; ?>
                                                    </h6>
                                                    <div class="flex items-center gap-1.5 mt-0.5">
                                                        <span class="text-[9px] font-black <?php echo e($c_textColor); ?>"><?php echo e($c_yazanUnvan); ?></span>
                                                        <span class="text-[9px] text-gray-300">|</span>
                                                        <div class="flex items-center bg-gray-600 text-white rounded px-1 py-0.5">
                                                            <span class="text-[8px] font-black uppercase tracking-wider"><?php echo e($c_yazan->bolum->ad ?? 'Genel'); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] text-gray-400"><?php echo e($cevap->created_at->diffForHumans()); ?></span>
                                                    <?php
                                                        $isAuthorReply = auth()->id() == $cevap->yazan_user_id;
                                                        $canDeleteReply = $isAuthorReply || 
                                                                        (auth()->id() == $cevap->user_id && !$c_yazan->hasRole('Superadmin')) || 
                                                                        auth()->user()->hasRole('Superadmin');
                                                    ?>
                                                    
                                                    <?php if($isAuthorReply || auth()->user()->hasRole('Superadmin')): ?>
                                                        <button @click="isEditingReply = !isEditingReply" class="text-gray-300 hover:text-indigo-500 transition-colors opacity-0 group-hover/reply:opacity-100">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if($canDeleteReply): ?>
                                                        <form action="<?php echo e(route('profile.comment.destroy', $cevap->id)); ?>" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');" class="inline opacity-0 group-hover/reply:opacity-100 transition-opacity">
                                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            
                                            <div x-show="!isEditingReply">
                                                <p class="text-xs text-gray-600 mt-1.5 font-medium leading-relaxed"><?php echo e($cevap->yorum); ?></p>
                                            </div>

                                            
                                            <div x-show="isEditingReply" class="mt-2" style="display: none;">
                                                <form action="<?php echo e(route('profile.comment.update', $cevap->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                                    <textarea name="yorum" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm text-xs focus:border-indigo-500 focus:ring focus:ring-indigo-200"><?php echo e($cevap->yorum); ?></textarea>
                                                    <div class="mt-2 flex justify-end gap-1">
                                                        <button type="button" @click="isEditingReply = false" class="text-[10px] text-gray-500 font-bold px-2 py-1 hover:bg-gray-100 rounded transition-colors">İptal</button>
                                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1 rounded text-[10px] font-bold transition-colors">Güncelle</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-12 bg-white rounded-3xl border-2 border-dashed border-gray-100">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <p class="text-sm text-gray-400 font-medium italic">Henüz yorum yapılmamış. İlk yorumu sen yap!</p>
            </div>
        <?php endif; ?>

        
        <?php if($totalComments > $yorumlar->count()): ?>
            <div class="mt-8 flex justify-center">
                <a href="<?php echo e(request()->fullUrlWithQuery(['comment_limit' => $commentLimit + 5])); ?>#yorumlar" 
                   class="inline-flex items-center gap-2 px-8 py-3 bg-white text-indigo-600 border-2 border-indigo-50 hover:border-indigo-200 hover:bg-indigo-50 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    Daha Eski Yorumları Yükle (<?php echo e($totalComments - $yorumlar->count()); ?> yorum daha var)
                </a>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/profile/partials/show/tab-comments.blade.php ENDPATH**/ ?>