<?php
    $hatirlatmaSistemiAktif = \App\Models\Setting::get('hatirlatma_sistemi_aktif', 1);
    $isAuthorizedForReminder = auth()->user()->hasAnyRole(['Superadmin', 'Müşteri Temsilcisi', 'Müşteri Şikayeti Kurulu']);
    $sikayetKapatildi = in_array($sikayet->musteri_durum, ['Kapatıldı', 'Çözümlendi']);
?>

<?php if($hatirlatmaSistemiAktif && $isAuthorizedForReminder && !$sikayetKapatildi): ?>
    <?php
        $hDurum = $sikayet->musteri_hatirlatma_durumu;
    ?>

    <div class="inline-block">
        <?php if(!$hDurum['can_send']): ?>
            <?php
                $reminderId = $hDurum['id'] ?? null;
                $isCustomer = auth()->user()->customer_id !== null;
                $targetRoute = $isCustomer ? 'iaa.hatirlatmalarim.show' : 'admin.sikayet-hatirlatma.show';
            ?>
            <?php if($reminderId): ?>
                <a href="<?php echo e(route($targetRoute, $reminderId)); ?>" 
                   class="inline-flex items-center px-3 py-1.5 border border-amber-200 text-xs font-black rounded-lg shadow-sm text-amber-700 bg-amber-50 hover:bg-amber-100 transition-all duration-200 space-x-1 group animate-pulse"
                   title="Detayı görmek için tıklayın (Henüz yeni hatırlatma gönderilemez)">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span><?php echo e($hDurum['message']); ?></span>
                </a>
            <?php else: ?>
                <div class="inline-flex items-center px-3 py-1.5 border border-amber-200 text-xs font-black rounded-lg shadow-sm text-amber-700 bg-amber-50 space-x-1 animate-pulse"
                     title="İlk hatırlatma için sürenin dolması bekleniyor">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span><?php echo e($hDurum['message']); ?></span>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <form id="reminder-form-<?php echo e($sikayet->id); ?>" action="<?php echo e(route('admin.sikayet-hatirlatma.gonder', $sikayet->id)); ?>" method="POST" class="inline">
                <?php echo csrf_field(); ?>
                <button type="button" 
                        onclick="confirmReminder<?php echo e($sikayet->id); ?>()"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg shadow-sm text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-all duration-200 hover:scale-105 active:scale-95 space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span>Hatırlatma Gönder</span>
                </button>
            </form>

            <?php $__env->startPush('scripts'); ?>
                <?php if (! $__env->hasRenderedOnce('c92f1b2d-3051-4f1a-bed3-f35733d1c0e7')): $__env->markAsRenderedOnce('c92f1b2d-3051-4f1a-bed3-f35733d1c0e7'); ?>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <?php endif; ?>
                <script>
                    function confirmReminder<?php echo e($sikayet->id); ?>() {
                        Swal.fire({
                            title: 'Hatırlatma Gönderilsin mi?',
                            input: 'textarea',
                            inputLabel: 'Hatırlatma Notu (Opsiyonel)',
                            inputPlaceholder: 'Sürecin hızlandırılması için bir not ekleyebilirsiniz...',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48', // rose-600
                            cancelButtonColor: '#94a3b8', // slate-400
                            confirmButtonText: 'Evet, Gönder!',
                            cancelButtonText: 'Vazgeç',
                            background: '#ffffff',
                            customClass: {
                                popup: 'rounded-2xl shadow-xl border border-gray-100',
                                title: 'text-gray-900 font-bold',
                                confirmButton: 'rounded-xl px-6 py-2.5 text-sm font-bold',
                                cancelButton: 'rounded-xl px-6 py-2.5 text-sm font-bold'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.getElementById('reminder-form-<?php echo e($sikayet->id); ?>');
                                if (result.value) {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'aciklama';
                                    input.value = result.value;
                                    form.appendChild(input);
                                }
                                form.submit();
                            }
                        })
                    }
                </script>
            <?php $__env->stopPush(); ?>
        <?php endif; ?>

        
        <?php if(session('son_hatirlatma_id') && session('son_hatirlatma_sikayet_id') == $sikayet->id): ?>
            <div class="inline-flex items-center gap-2 ml-2">
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1.5 rounded-lg border border-emerald-100 shadow-sm" title="Durum: <?php echo e(str_replace('_', ' ', session('son_hatirlatma_durum'))); ?>">
                    ✓ Gönderildi
                </span>
                <a href="<?php echo e(route('admin.sikayet-hatirlatma.show', session('son_hatirlatma_id'))); ?>" 
                   class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-[10px] font-black rounded-lg hover:bg-indigo-700 transition shadow-sm uppercase tracking-tighter">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    DETAY
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/sikayet-hatirlatma/partials/_hatirlatma-butonu.blade.php ENDPATH**/ ?>