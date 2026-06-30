<div class="flex flex-col bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 shadow-sm relative group overflow-hidden transition-all hover:ring-2 hover:ring-indigo-500/20">
    <div class="flex items-center gap-4 mb-4">
        <img src="<?php echo e($kat->user ? $kat->user->profile_photo_url : 'https://ui-avatars.com/api/?name='.urlencode($kat->dis_katilimci_adi)); ?>" class="w-12 h-12 rounded-2xl shadow-md object-cover">
        <div class="flex-1">
            <p class="text-[11px] font-black text-gray-800"><?php echo e($kat->user ? $kat->user->name : $kat->dis_katilimci_adi); ?></p>
            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-[0.2em]"><?php echo e($kat->rol); ?></p>
        </div>
        <div class="flex items-center gap-2">
            <!--[if BLOCK]><![endif]--><?php if($kat->katilim_durumu === 'katildi'): ?>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full shadow-lg shadow-emerald-500/20 animate-pulse"></span>
            <?php elseif($kat->katilim_durumu === 'katilmadi'): ?>
                <span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    
    <!--[if BLOCK]><![endif]--><?php if($canManage && !$isFinished): ?>
        <div class="space-y-3 pt-3 border-t border-gray-100">
            <div class="flex gap-2">
                <select wire:model.live="katilimciDurumlari.<?php echo e($kat->id); ?>" wire:change="updateAttendance(<?php echo e($kat->id); ?>)" class="flex-1 bg-white border-gray-100 rounded-xl text-[10px] font-black uppercase text-gray-500 focus:ring-indigo-500">
                    <option value="beklemede">Yoklama Bekleniyor</option>
                    <option value="katildi">KATILDI</option>
                    <option value="katilmadi">KATILMADI</option>
                </select>
            </div>
            <!--[if BLOCK]><![endif]--><?php if(($katilimciDurumlari[$kat->id] ?? '') === 'katilmadi'): ?>
                <textarea wire:model.blur="katilmamaNedenleri.<?php echo e($kat->id); ?>" wire:change="updateAttendance(<?php echo e($kat->id); ?>)" class="w-full bg-white border-gray-100 rounded-xl text-[10px] py-2 px-3 placeholder:italic focus:ring-rose-500" placeholder="Katılmama nedeni (opsiyonel)..." rows="1"></textarea>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php else: ?>
        <div class="text-[10px] font-bold <?php echo e($kat->katilim_durumu === 'katildi' ? 'text-emerald-600' : ($kat->katilim_durumu === 'katilmadi' ? 'text-rose-600' : 'text-gray-400')); ?> uppercase tracking-widest bg-white/50 py-2 px-4 rounded-xl border border-gray-50">
            <?php echo e($kat->katilim_durumu === 'katilmadi' ? 'KATILMADI: ' . ($kat->katilmama_nedeni ?? 'Neden belirtilmedi') : strtoupper($kat->katilim_durumu)); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/admin/disiplin/partials/katilimci-card.blade.php ENDPATH**/ ?>