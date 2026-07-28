<?php
    $durum = $uye->pivot->durum; // onaylandi, bekliyor, reddedildi
    
    // Kim lider? 
    // 1. Proje tamamlanmış/onay sürecindeyse ve 'tamamlayan_lider_id' dondurulmuşsa o kişi liderdir.
    // 2. Proje devam ediyorsa, pivotta rolü 'Lider' olan ve hala aktif (işten ayrılmamış) kişi liderdir.
    // 3. VEYA projenin atanan takımının güncel lideri bu kişiyse (Halef-Selef durumu için).
    
    $isLider = false;
    if ($iaa->tamamlayan_lider_id) {
        $isLider = $iaa->tamamlayan_lider_id == $uye->id;
    } else {
        $pivotLiderMi = $uye->pivot->rol == 'Lider';
        $isLider = ($pivotLiderMi && !$uye->trashed()) || ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == $uye->id);
    }
    
    // Stil Ayarları
    $cardClass = 'bg-white border-gray-200';
    
    if($uye->trashed()) {
        $cardClass = 'bg-gray-50 border-gray-200 opacity-75 grayscale';
    } elseif($isLider) {
        $cardClass = 'bg-gradient-to-br from-indigo-50 to-white border-indigo-200 shadow-sm ring-1 ring-indigo-100';
    } elseif($durum == 'bekliyor') {
        $cardClass = 'bg-amber-50/50 border-amber-200 border-dashed';
    } elseif($durum == 'reddedildi') {
        $cardClass = 'bg-red-50/50 border-red-200 opacity-75';
    }
?>

<div class="relative flex items-center gap-2 px-3 py-1.5 rounded-full border transition-all duration-200 hover:shadow-sm <?php echo e($cardClass); ?> group w-auto">
    
    
    <a href="<?php echo e(route('profile.show', $uye->id)); ?>" class="flex-shrink-0 relative block">
        <?php if($uye->profile_photo_path): ?>
            <img class="h-8 w-8 rounded-full object-cover border <?php echo e($isLider ? 'border-indigo-200' : 'border-white'); ?> shadow-sm group-hover:scale-105 transition-transform duration-200 <?php echo e(($durum == 'reddedildi' || $uye->trashed()) ? 'grayscale' : ''); ?>" 
                    src="<?php echo e(asset('storage/'.$uye->profile_photo_path)); ?>" 
                    alt="<?php echo e($uye->name); ?>">
        <?php else: ?>
            <div class="h-8 w-8 rounded-full border <?php echo e($isLider ? 'border-indigo-200' : 'border-white'); ?> bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600 shadow-sm group-hover:scale-105 transition-transform duration-200 <?php echo e(($durum == 'reddedildi' || $uye->trashed()) ? 'grayscale' : ''); ?>">
                <?php echo e(substr($uye->name, 0, 1)); ?>

            </div>
        <?php endif; ?>

        
        <?php if($durum == 'bekliyor' && !$uye->trashed()): ?>
            <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-amber-400 animate-pulse" title="Yanıt Bekleniyor"></span>
        <?php elseif(($durum == 'reddedildi' || $uye->trashed()) && !$isLider): ?>
            <span class="absolute bottom-0 right-0 flex items-center justify-center h-3 w-3 rounded-full ring-1 ring-white bg-red-500 text-white text-[6px] font-bold" title="<?php echo e($uye->trashed() ? 'İşten Ayrıldı' : 'Reddetti'); ?>">X</span>
        <?php endif; ?>
    </a>

    
    <div class="flex flex-col min-w-0 pr-1">
        <a href="<?php echo e(route('profile.show', $uye->id)); ?>" class="text-xs font-bold text-gray-900 hover:text-indigo-600 transition-colors flex items-center gap-1 leading-tight whitespace-nowrap">
            <?php echo e($uye->name); ?>

            <?php if($uye->trashed()): ?>
                <span class="text-[8px] bg-red-100 text-red-600 px-1 py-0.5 rounded border border-red-200">AYRILDI</span>
            <?php endif; ?>
        </a>
        
        <p class="text-[10px] text-gray-500 font-medium truncate max-w-[140px]" title="<?php echo e(optional($uye->bolum)->ad); ?> - <?php echo e($isLider ? 'Proje Lideri' : 'Ekip Üyesi'); ?>">
            <?php echo e($isLider ? 'Lider' : 'Üye'); ?> &bull; <?php echo e(optional($uye->bolum)->ad ?? 'Bölüm Yok'); ?>

            <?php if($durum == 'bekliyor'): ?>
                <span class="text-amber-500 font-bold ml-1">Bekliyor</span>
            <?php elseif($durum == 'reddedildi'): ?>
                <span class="text-red-500 font-bold ml-1">Reddetti</span>
            <?php endif; ?>
        </p>
    </div>
    
    
    <?php if($durum == 'bekliyor' && isset($isLeader) && $isLeader && isset($isLocked) && !$isLocked): ?>
        <button onclick="if(confirm('Daveti iptal etmek istediğinize emin misiniz?')) { Livewire.dispatch('davetIptalFromOuter', { userId: <?php echo e($uye->id); ?>, iaaId: <?php echo e($iaa->id); ?> }) }" 
                class="ml-1 p-1 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 border border-red-100 shadow-sm" 
                title="Daveti İptal Et">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    <?php endif; ?>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_squad-card.blade.php ENDPATH**/ ?>