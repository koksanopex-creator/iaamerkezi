<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proje Çalışma Alanı: <span class="text-indigo-600"><?php echo e($iaa->baslik); ?></span>
            </h2>
            <a href="<?php echo e(url()->previous()); ?>" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Geri Dön
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            
            <?php echo $__env->make('proje-calisma-alani.partials._project-header', [
                'iaa' => $iaa,
                'takim' => $takim,
                'assignment' => $assignment,
                'progressPercentage' => $progressPercentage,
                'completedStepsCount' => $completedStepsCount,
                'totalStepsCount' => $totalStepsCount,
                'statusDate' => $statusDate ?? null
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._squad', ['iaa' => $iaa], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._customer-notification', ['iaa' => $iaa], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._complaint-details', ['iaa' => $iaa], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._timeline', [
                'steps' => $steps,
                'completedStepIds' => $completedStepIds,
                'progressUpdates' => $progressUpdates,
                'isTeamMember' => $isTeamMember,
                'iaa' => $iaa,
                'assignment' => $assignment,
                'takim' => $takim,
                'stepAssignments' => $stepAssignments ?? [],
                'canEdit' => $canEdit,
                'statusDate' => $statusDate ?? null
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._logs', [
                'sonOnLoglar' => $sonOnLoglar,
                'tumProjeLoglari' => $tumProjeLoglari
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>
    </div>

    
    <?php echo $__env->make('proje-calisma-alani.partials._scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/show.blade.php ENDPATH**/ ?>