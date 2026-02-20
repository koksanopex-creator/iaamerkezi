

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
    'takim',
    'stepAssignments' => [],
    'canEdit' => false
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
    'takim',
    'stepAssignments' => [],
    'canEdit' => false
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // 1. MEVCUT KULLANICIYI VE DURUMU AL
    $currentUser = Auth::user();
    
    // 2. MÜŞTERİ Mİ? (EN KRİTİK KONTROL BURASI)
    // Şart 1: Hiç giriş yapmamışsa (Misafir) -> Müşteridir.
    // Şart 2: Giriş yapmış AMA 'is_personnel' değeri 0 ise -> Müşteridir.
    $isCustomerView = !Auth::check() || ($currentUser && $currentUser->is_personnel == 0);

    // 3. GİZLİLİK DURUMU
    // Veritabanında 'gizli' olarak işaretlenmiş mi?
    $isHidden = $progressUpdate ? $progressUpdate->is_hidden_from_customer : false;

    // 4. YÖNETİCİ BUTONUNU GÖRME YETKİSİ
    // Sadece Personel olanlar (is_personnel=1) ve yetkisi olanlar görebilir.
    $isLeader = $iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id;
    $canManageVisibility = Auth::check() && $currentUser->is_personnel == 1 && ($isLeader || $currentUser->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi']));
    
    // 5. Müşteri Şikayeti Kaynaklı mı?
    $hasCustomer = $iaa->musteriSikayeti ? true : false;
?>

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
                
                
                <div class="flex items-center gap-2">
                    <span><?php echo e($step->order); ?>. <?php echo e($step->name); ?></span>

                    
                    <?php if($canManageVisibility && $hasCustomer): ?>
                        <form action="<?php echo e(route('proje.step.toggleVisibility', ['iaa_id' => $iaa->id, 'step_id' => $step->id])); ?>" method="POST" class="inline-block" @click.stop>
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-[10px] uppercase font-bold flex items-center gap-1 px-2 py-0.5 rounded border transition-all <?php echo e($isHidden ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100' : 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100'); ?>" title="<?php echo e($isHidden ? 'Müşteriden GİZLİ. Göstermek için tıkla.' : 'Müşteriye AÇIK. Gizlemek için tıkla.'); ?>">
                                <?php if($isHidden): ?>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                    Gizli
                                <?php else: ?>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Açık
                                <?php endif; ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                
                <div>
                    <?php if($isCompleted): ?> 
                        <span class="bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm">✓ Tamamlandı</span> 
                    <?php endif; ?>
                    <?php if($isCurrent): ?> 
                        <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm animate-pulse">● Aktif Adım</span> 
                    <?php endif; ?>
                </div>
            </h4>
            
            
            <p class="text-sm font-normal text-gray-600 mt-2"><?php echo e($step->description); ?></p>
        </div>

        
        
        
        
        
        <?php if($isHidden && $isCustomerView): ?>
            
            <div x-show="open" x-transition class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <?php if($isCompleted): ?>
                        <div class="p-2 bg-green-100 text-green-600 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-gray-700 font-medium">
                            <?php echo e($step->name); ?> adımı ekip tarafından tamamlanmıştır.
                        </span>
                    <?php else: ?>
                        <div class="p-2 bg-blue-50 text-blue-400 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-gray-600 italic">
                            <?php echo e($step->name); ?> adımının ekip tarafından tamamlanması beklenmektedir.
                        </span>
                    <?php endif; ?>
                </div>
            </div>

        
        <?php else: ?>

            
            <?php
                $assignmentData = $stepAssignments[$step->id] ?? null;
                $sorumluUser = $assignmentData ? \App\Models\User::find($assignmentData->user_id) : null;
                $waitingSince = $assignmentData ? \Carbon\Carbon::parse($assignmentData->updated_at)->diffForHumans() : '';
            ?>

            <?php if(!$isCustomerView): ?> 
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

                    <?php if($isLeader && !$isCompleted && $canEdit): ?>
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
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            
            <?php if($isCompleted && $progressUpdate): ?>
                <?php echo $__env->make('proje-calisma-alani.partials._step-content-completed', [
                    'progressUpdate' => $progressUpdate,
                    'step' => $step,
                    'iaa' => $iaa 
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>

            
            <?php if($isCurrent): ?>
                <?php if($canEdit && !$isCustomerView): ?>
                    
                        <?php echo $__env->make('proje-calisma-alani.partials._step-content-active', [
                        'iaa' => $iaa,
                        'assignment' => $assignment,
                        'currentStep' => $step, 
                        'progressUpdate' => $progressUpdate,
                        'isTeamMember' => $isTeamMember,
                        'takim' => $takim,
                        'canEdit' => true
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
                    
                    <?php if(!$isCustomerView): ?>
                        <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <span class="font-bold">İzleyici Modu:</span> Bu proje adımını görüntüleme yetkiniz var ancak müdahale edemezsiniz.
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

            <?php endif; ?> 

            
            
            

            <?php if($isCompleted || $isCurrent): ?>
                <div class="mt-8 mb-4 px-6 md:px-10 border-t border-gray-100 pt-6">
                    <h5 class="text-xs font-bold text-gray-500 uppercase mb-3">İletişim & Notlar</h5>
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