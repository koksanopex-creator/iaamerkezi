<?php
    // KİLİT MANTIĞI: Eğer süreç 'Talep' aşamasına girdiyse veya Hatalı Bildirim Onayı bekliyorsa Timeline gizlenmeli.
    $kilitliDurumlar = [
        'talep_onayi_bekliyor_kalite',
        'talep_onayi_bekliyor_superadmin',
        'talep_olarak_kapatildi',
        'hatali_bildirim_onayi_bekliyor_kalite',
        'hatali_bildirim_onayi_bekliyor_direktor',
        'hatali_bildirim_onayi_bekliyor_superadmin',
        'hatali_bildirim_olarak_kapatildi'
    ];
    $isLocked = in_array($iaa->durum, $kilitliDurumlar);
?>


<?php if(!$isLocked): ?>
    <div class="w-full">
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Proje Adımları</h3>

            <?php if(session('success')): ?>
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                    <p><?php echo e(session('success')); ?></p>
                </div>
            <?php endif; ?>

            <div class="relative border-l-2 border-gray-200">
                <?php $currentStepFound = false; ?>

                <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isCompleted = in_array($step->id, $completedStepIds);
                        $progressUpdate = $progressUpdates[$step->id] ?? null;
                        $isCurrent = !$isCompleted && !$currentStepFound;

                        if ($isCurrent) {
                            $currentStepFound = true;
                        }
                    ?>

                    
                    
                    <div id="step-<?php echo e($step->id); ?>" class="scroll-mt-24">
                        <?php echo $__env->make('proje-calisma-alani.partials._step-item', [
                            'step' => $step,
                            'isCompleted' => $isCompleted,
                            'isCurrent' => $isCurrent,
                            'progressUpdate' => $progressUpdate,
                            'isTeamMember' => $isTeamMember,
                            'iaa' => $iaa,
                            'assignment' => $assignment,
                            'takim' => $takim,
                            'stepAssignments' => $stepAssignments ?? [],
                            'canEdit' => $canEdit
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_timeline.blade.php ENDPATH**/ ?>