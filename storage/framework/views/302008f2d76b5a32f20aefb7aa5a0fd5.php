

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'step',
    'isCompleted',
    'isCurrent',
    'progressUpdate',
    'isTeamMember',
    'iaa',
    'assignment',
    'takim'
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'step',
    'isCompleted',
    'isCurrent',
    'progressUpdate',
    'isTeamMember',
    'iaa',
    'assignment',
    'takim'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div id="step-card-<?php echo e($step->id); ?>" class="mb-10 ml-6" x-data="{ open: <?php echo e($isCompleted ? 'false' : ($isCurrent ? 'true' : 'false')); ?> }">
    
    
    <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-4 ring-white shadow-md transition-all duration-300
        <?php echo e($isCompleted ? 'bg-gradient-to-br from-green-400 to-green-600' : ($isCurrent ? 'bg-gradient-to-br from-blue-400 to-blue-600 animate-pulse' : 'bg-gray-300')); ?>">
        <?php if($isCompleted): ?> 
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        <?php elseif($isCurrent): ?>
            <span class="w-3 h-3 bg-white rounded-full"></span>
        <?php endif; ?>
    </span>
    
    
    <div class="bg-white border-2 <?php echo e($isCurrent ? 'border-blue-300 shadow-lg' : 'border-gray-200'); ?> rounded-xl p-5 hover:shadow-md transition-shadow duration-300">
        
        
        <div <?php if($isCompleted || $isCurrent): ?> @click="open = !open" class="cursor-pointer" <?php else: ?> class="cursor-default" <?php endif; ?>>
            <h4 class="flex items-center justify-between text-base font-semibold <?php echo e($isCurrent ? 'text-blue-700' : 'text-gray-900'); ?>">
                <span><?php echo e($step->order); ?>. <?php echo e($step->name); ?></span>
                <div>
                    <?php if($isCompleted): ?> 
                        <span class="bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm">✓ Tamamlandı</span> 
                    <?php endif; ?>
                    <?php if($isCurrent): ?> 
                        <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm animate-pulse">● Aktif Adım</span> 
                    <?php endif; ?>
                </div>
            </h4>
          
            
            <?php
                // Bu verileri show.blade.php'den (Controller'dan) gönderdiğimiz stepAssignments değişkeninden çekiyoruz
                // Eğer stepAssignments yoksa (hata olmasın diye) boş dizi kabul ediyoruz
                $assignmentData = $stepAssignments[$step->id] ?? null;
                
                // Lider mi?
                $isLeader = (Auth::id() == $takim->lider_user_id) || Auth::user()->hasRole('Superadmin');
                
                // Sorumlu Var mı?
                $sorumluUser = $assignmentData ? \App\Models\User::find($assignmentData->user_id) : null;
                
                // Ben miyim?
                $isMe = $assignmentData && $assignmentData->user_id == Auth::id();
                
                // Bekleme Süresi
                $waitingSince = $assignmentData ? \Carbon\Carbon::parse($assignmentData->updated_at)->diffForHumans() : '';
            ?>

            
            <div class="mt-3 mb-3 flex items-center justify-between bg-gray-50 p-2.5 rounded-lg border border-gray-200 shadow-sm">
                
                
                <div class="flex items-center gap-3 text-sm">
                    <?php if($sorumluUser): ?>
                        <?php if($isCompleted): ?>
                             <span class="text-gray-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-medium text-gray-900"><?php echo e($sorumluUser->name); ?></span> tarafından tamamlandı.
                            </span>
                        <?php else: ?>
                            
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <?php if($sorumluUser->profile_photo_path): ?>
                                        <img src="<?php echo e(asset('storage/'.$sorumluUser->profile_photo_path)); ?>" class="w-9 h-9 rounded-full border border-gray-300 shadow-sm">
                                    <?php else: ?>
                                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600 shadow-sm"><?php echo e(substr($sorumluUser->name, 0, 1)); ?></div>
                                    <?php endif; ?>
                                    
                                    <span class="absolute -bottom-1 -right-1 flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500 border border-white"></span>
                                    </span>
                                </div>
                                <div class="flex flex-col leading-tight">
                                    <span class="text-gray-800 font-semibold">
                                        <?php echo e($sorumluUser->name); ?> bekleniyor...
                                    </span>
                                    <span class="text-xs text-gray-500"><?php echo e($waitingSince); ?> atandı</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-gray-400 italic text-xs flex items-center gap-1.5">
                            <div class="p-1 bg-gray-200 rounded-full">
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            Sorumlu atanmamış (Ortak Görev)
                        </span>
                    <?php endif; ?>
                </div>

                
                <?php if($isLeader && !$isCompleted): ?>
                    <form action="<?php echo e(route('proje.workspace.assignUserToStep', ['iaa' => $iaa->id, 'step' => $step->id])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="relative">
                        <select name="user_id" onchange="this.form.submit()" class="appearance-none bg-white border border-gray-300 text-gray-700 text-xs rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-40 py-1.5 pl-3 pr-8 cursor-pointer hover:border-gray-400 transition-colors">
                                <option value="">-- Sorumlu Seç --</option>
                                
                                
                                <?php $__currentLoopData = $iaa->projeEkibi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekipUyesi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                    
                                    <?php if($ekipUyesi->pivot->durum == 'onaylandi'): ?>
                                        <option value="<?php echo e($ekipUyesi->id); ?>" <?php echo e(($sorumluUser && $sorumluUser->id == $ekipUyesi->id) ? 'selected' : ''); ?>>
                                            <?php echo e($ekipUyesi->name); ?>

                                        </option>
                                    <?php endif; ?>
                                    
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                 
                                 <?php if(!$iaa->projeEkibi->contains($takim->lider_user_id)): ?>
                                    <option value="<?php echo e($takim->lider_user_id); ?>" <?php echo e(($sorumluUser && $sorumluUser->id == $takim->lider_user_id) ? 'selected' : ''); ?>>
                                        <?php echo e($takim->lider->name); ?> (Lider)
                                    </option>
                                 <?php endif; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <p class="text-sm font-normal text-gray-600 mt-2"><?php echo e($step->description); ?></p>
        </div>
        
        
        <?php if($isCompleted && $progressUpdate): ?>
            <?php echo $__env->make('proje-calisma-alani.partials._step-content-completed', [
                'progressUpdate' => $progressUpdate,
                'step' => $step,
                'iaa' => $iaa // <--- BU SATIRI EKLEDİM
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        
        <?php if($isCurrent): ?>
             <?php echo $__env->make('proje-calisma-alani.partials._step-content-active', [
                'iaa' => $iaa,
                'assignment' => $assignment,
                'currentStep' => $step, // $currentStep yerine $step kullanmak daha doğru
                'progressUpdate' => $progressUpdate,
                'isTeamMember' => $isTeamMember,
                'takim' => $takim
             ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        
        <?php if($isCompleted || $isCurrent): ?>
        <div class="mt-8 mb-4 px-6 md:px-10">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.proje-adim-yorumlari', [
                'iaa' => $iaa, 
                'step' => $step
            ]);

$__html = app('livewire')->mount($__name, $__params, $step->id, $__slots ?? [], get_defined_vars());

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
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_step-item.blade.php ENDPATH**/ ?>