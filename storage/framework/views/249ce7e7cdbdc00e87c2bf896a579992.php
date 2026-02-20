<?php
    $durum = $uye->pivot->durum; // onaylandi, bekliyor, reddedildi
    $isLider = $uye->pivot->rol == 'Lider';
    
    // Stil Ayarları
    $cardClass = 'bg-white border-gray-200';
    
    if($isLider) {
        $cardClass = 'bg-gradient-to-br from-indigo-50 to-white border-indigo-200 shadow-sm ring-1 ring-indigo-100';
    } elseif($durum == 'bekliyor') {
        $cardClass = 'bg-amber-50/50 border-amber-200 border-dashed';
    } elseif($durum == 'reddedildi') {
        $cardClass = 'bg-red-50/50 border-red-200 opacity-75';
    }
?>

<div class="relative flex items-start gap-4 p-4 rounded-xl border transition-all duration-200 hover:shadow-md <?php echo e($cardClass); ?> group">
    
    
    <a href="<?php echo e(route('profile.show', $uye->id)); ?>" class="flex-shrink-0 relative block">
        <?php if($uye->profile_photo_path): ?>
            <img class="h-14 w-14 rounded-full object-cover border-2 <?php echo e($isLider ? 'border-indigo-200' : 'border-white'); ?> shadow-sm group-hover:scale-105 transition-transform duration-200 <?php echo e($durum == 'reddedildi' ? 'grayscale' : ''); ?>" 
                    src="<?php echo e(asset('storage/'.$uye->profile_photo_path)); ?>" 
                    alt="<?php echo e($uye->name); ?>">
        <?php else: ?>
            <div class="h-14 w-14 rounded-full border-2 <?php echo e($isLider ? 'border-indigo-200' : 'border-white'); ?> bg-gray-100 flex items-center justify-center text-lg font-bold text-gray-600 shadow-sm group-hover:scale-105 transition-transform duration-200 <?php echo e($durum == 'reddedildi' ? 'grayscale' : ''); ?>">
                <?php echo e(substr($uye->name, 0, 1)); ?>

            </div>
        <?php endif; ?>

        
        <?php if($durum == 'bekliyor'): ?>
            <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full ring-2 ring-white bg-amber-400 animate-pulse" title="Yanıt Bekleniyor"></span>
        <?php elseif($durum == 'reddedildi'): ?>
            <span class="absolute bottom-0 right-0 flex items-center justify-center h-4 w-4 rounded-full ring-2 ring-white bg-red-500 text-white text-[8px] font-bold" title="Reddetti">X</span>
        <?php endif; ?>
    </a>

    
    <div class="flex-1 min-w-0 pt-0.5">
        <div class="flex items-start justify-between">
            <div>
                
                <a href="<?php echo e(route('profile.show', $uye->id)); ?>" class="text-sm font-bold text-gray-900 hover:text-indigo-600 transition-colors block leading-tight">
                    <?php echo e($uye->name); ?>

                </a>
                
                
                <p class="text-xs text-gray-500 mt-0.5 font-medium truncate max-w-[140px]" title="<?php echo e(optional($uye->bolum)->ad); ?>">
                    <?php echo e(optional($uye->bolum)->ad ?? 'Bölüm Yok'); ?>

                </p>
            </div>
            
            
            <?php if($isLider): ?>
                <div class="relative group/icon" title="Proje Lideri">
                    <div class="p-1 bg-amber-100 rounded-lg">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="mt-2 flex items-center gap-2">
            <?php if($isLider): ?>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-indigo-100 text-indigo-700">
                    Proje Lideri
                </span>
            <?php else: ?>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-600">
                    Ekip Üyesi
                </span>
            <?php endif; ?>

            <?php if($durum == 'bekliyor'): ?>
                <span class="text-[10px] text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">Yanıt Bekleniyor</span>
            <?php elseif($durum == 'reddedildi'): ?>
                <span class="text-[10px] text-red-600 font-bold bg-red-50 px-1.5 py-0.5 rounded border border-red-100">Reddetti</span>
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_squad-card.blade.php ENDPATH**/ ?>