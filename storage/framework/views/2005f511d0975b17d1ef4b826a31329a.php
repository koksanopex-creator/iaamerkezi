<?php if($iaa->musteriSikayeti): ?>
    <?php
        $isLeader = Auth::check() && ((Auth::id() == $iaa->atananTakim->lider_user_id) || Auth::user()->hasRole('Superadmin'));

        // KİLİT MANTIĞI: Bu durumlarda ekleme/çıkarma yapılamaz
        $kilitliDurumlar = [
            'talep_onayi_bekliyor_kalite',
            'talep_onayi_bekliyor_superadmin',
            'talep_olarak_kapatildi',
            'Bölüm Onayı Bekliyor',
            'Yönetici Onayı Bekliyor',
            'Tamamlandı',
            'hatali_bildirim_olarak_kapatildi'
        ];
        $isLocked = in_array($iaa->durum, $kilitliDurumlar);

        // İstatistikler (Aynen korundu)
        $toplamUye = $iaa->projeEkibi->count();
        $aktifSayisi = $iaa->projeEkibi->filter(fn($uye) => $uye->pivot->rol == 'Lider' || $uye->pivot->durum == 'onaylandi')->count();
        $bekleyenSayisi = $iaa->projeEkibi->where('pivot.durum', 'bekliyor')->count();
        $reddedenSayisi = $iaa->projeEkibi->where('pivot.durum', 'reddedildi')->count();
    ?>

    

    <div x-data="{ squadOpen: false }"
        class="bg-white rounded-xl shadow-sm border border-indigo-100 p-6 animate-fade-in-up mb-8">
        
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2 pb-2 border-b border-gray-100 cursor-pointer select-none group">

            
            <div @click="squadOpen = !squadOpen" class="flex items-center gap-4 flex-1">
                <div
                    class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm transition-colors group-hover:bg-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">Proje
                            Görev Gücü (Squad)</h3>

                        
                        <?php if($isLocked): ?>
                            <span
                                class="text-xs font-medium bg-gray-100 text-gray-500 px-2 py-0.5 rounded flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Kilitli
                            </span>
                        <?php endif; ?>

                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                            :class="{'rotate-180': squadOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">Toplam:
                            <?php echo e($toplamUye); ?></span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800"><span
                                class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></span><?php echo e($aktifSayisi); ?> Aktif</span>
                        <?php if($bekleyenSayisi > 0): ?>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-amber-100 text-amber-800 animate-pulse"><span
                                    class="w-2 h-2 bg-amber-500 rounded-full mr-1.5"></span><?php echo e($bekleyenSayisi); ?>

                                Bekliyor</span>
                        <?php endif; ?>
                        <?php if($reddedenSayisi > 0): ?>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-800"><span
                                    class="w-2 h-2 bg-red-500 rounded-full mr-1.5"></span><?php echo e($reddedenSayisi); ?> Reddetti</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            
            <?php if($isLeader && !$isLocked): ?>
                <div class="flex-shrink-0" @click.stop>
                    <button onclick="Livewire.dispatch('openSquadModal', { iaaId: <?php echo e($iaa->id); ?> })"
                        class="flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg> Ekibi Yönet
                    </button>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.squad-yonetim-modal', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-29143954-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            <?php endif; ?>
        </div>

        
        <div x-show="squadOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-4">
                <?php $__currentLoopData = $iaa->projeEkibi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('proje-calisma-alani.partials._squad-card', ['uye' => $uye], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_squad.blade.php ENDPATH**/ ?>