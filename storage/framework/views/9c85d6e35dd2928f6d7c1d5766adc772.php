

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
    $isLeader = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || ($isQualityManagerInterventionPower ?? false);
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
        
        
        
        
        <div class="cursor-default pb-2">
            <h4 class="flex items-center justify-between text-base font-semibold <?php echo e($isCurrent ? 'text-blue-700' : 'text-gray-900'); ?>">
                
                
                <div class="flex items-center gap-2">
                    <span><?php echo e($step->order); ?>. <?php echo e($step->name); ?></span>

                    
                    <?php if($hasCustomer): ?>
                        <?php if($canManageVisibility): ?>
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

        
        
        
        
        <?php
            // Bekleme Süresi ve Atama Hesaplamaları (Tüm görünümler için ortak)
            $assignments = $stepAssignments[$step->id] ?? collect();
            $sorumluUserIds = $assignments->pluck('user_id')->toArray();
            
            $firstAssignment = $assignments->first();
            $waitingSince = null;
            if ($firstAssignment) {
                $startDate = \Carbon\Carbon::parse($firstAssignment->created_at);
                $endDate = ($isCompleted && $progressUpdate) 
                    ? \Carbon\Carbon::parse($progressUpdate->created_at)
                    : now();
                
                $diff = $startDate->diff($endDate);
                
                $parts = [];
                if ($diff->d > 0) $parts[] = $diff->d . ' gün';
                if ($diff->h > 0) $parts[] = $diff->h . ' saat';
                if ($diff->i > 0 && $diff->d == 0) $parts[] = $diff->i . ' dk'; 
                $waitingSince = !empty($parts) ? implode(' ', $parts) : 'Az önce';
            }
        ?>

        
        <?php if($isCustomerView): ?>
            <div class="mt-4">
                <?php if($isCompleted): ?>
                    <?php if($isHidden): ?>
                        
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-start gap-3">
                            <div class="p-2 bg-green-100 text-green-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    Bu adım projenin <strong><?php echo e($iaa->atananTakim ? $iaa->atananTakim->ad : 'ekibi'); ?></strong> tarafından 
                                    <strong><?php echo e($progressUpdate->completed_at ? \Carbon\Carbon::parse($progressUpdate->completed_at)->format('d.m.Y') : ($progressUpdate->created_at ? $progressUpdate->created_at->format('d.m.Y') : '-')); ?></strong> 
                                    tarihinde tamamlanmıştır.
                                </p>
                                <p class="text-[11px] text-gray-500 mt-1 italic">Teknik detaylar ve iç yazışmalar müşteri görünümüne kapalıdır.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        
                        <div class="flex items-center gap-2 mb-4">
                            <button @click.stop="open = !open" type="button" class="group flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 border-2 border-gray-100 hover:bg-indigo-50 hover:border-indigo-200 transition-all duration-300 shadow-sm">
                                <div class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                                </div>
                                <span class="text-[10px] font-black tracking-widest text-gray-600 group-hover:text-indigo-700 uppercase" x-text="open ? 'DETAYLARI KAPAT' : 'İÇERİĞİ GÖSTERMEK İÇİN TIKLAYIN'"></span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transition-transform duration-500" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                        <div x-show="open" x-transition>
                            <?php echo $__env->make('proje-calisma-alani.partials._step-content-completed', [
                                'progressUpdate' => $progressUpdate,
                                'step' => $step,
                                'iaa' => $iaa,
                                'isAssignedToSomeoneElse' => false,
                                'assignments' => $assignments,
                                'canEdit' => false
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    <?php endif; ?>
                <?php elseif($isCurrent): ?>
                    
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-start gap-3 shadow-sm">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg animate-pulse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-blue-900">
                                Sürecin tamamlanması <strong><?php echo e($iaa->atananTakim ? $iaa->atananTakim->ad : 'ilgili birim'); ?></strong> tarafından beklenmektedir.
                            </p>
                            <?php if($waitingSince): ?>
                                <p class="text-xs text-blue-700 mt-1">
                                    Bu adım üzerinde <strong><?php echo e($waitingSince); ?></strong> süredir çalışılmaktadır.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        
        <?php else: ?>
            
            <div class="mt-3 mb-4 bg-white p-3 rounded-xl border border-gray-200 shadow-sm ring-1 ring-black/5">
                <div class="w-full">
                    <?php
                        $allPossibleUsers = collect();
                        foreach($iaa->projeEkibi as $eu) { 
                            if($eu->pivot->durum == 'onaylandi') {
                                $allPossibleUsers->push([
                                    'id' => $eu->id, 
                                    'name' => $eu->name, 
                                    'photo' => $eu->profile_photo_path,
                                    'role' => $eu->roles->pluck('name')->first() ?? 'Personel',
                                    'bolum' => $eu->bolum?->ad ?? 'Genel'
                                ]);
                            }
                        }
                        if($takim) { 
                            foreach($takim->users as $tu) { 
                                if(!$allPossibleUsers->contains('id', $tu->id)) {
                                    $allPossibleUsers->push([
                                        'id' => $tu->id, 
                                        'name' => $tu->name, 
                                        'photo' => $tu->profile_photo_path,
                                        'role' => $tu->roles->pluck('name')->first() ?? 'Takım Üyesi',
                                        'bolum' => $tu->bolum?->ad ?? 'Genel'
                                    ]);
                                }
                            }
                        }
                    ?>

                    <div x-data="{ 
                        selectedIds: <?php echo e(json_encode($sorumluUserIds)); ?>,
                        allUsers: <?php echo e(json_encode($allPossibleUsers)); ?>,
                        isCompleted: <?php echo e($isCompleted ? 'true' : 'false'); ?>,
                        getSelectedPhotos() {
                            return this.allUsers.filter(u => this.selectedIds.includes(u.id));
                        }
                    }" class="flex items-center gap-3 w-full justify-between">
                        
                        <div class="flex items-center gap-3 flex-grow">
                            <template x-if="selectedIds.length > 0">
                                <div class="flex items-center gap-3">
                                    <div class="flex -space-x-3 overflow-hidden">
                                        <template x-for="user in getSelectedPhotos()" :key="user.id">
                                            <div class="relative inline-block" :title="user.name + ' (' + user.bolum + ' / ' + user.role + ')'">
                                                <template x-if="user.photo">
                                                    <img :src="'<?php echo e(asset('storage')); ?>/' + user.photo" class="w-9 h-9 rounded-full border-2 border-white shadow-sm ring-1 ring-gray-100">
                                                </template>
                                                <template x-if="!user.photo">
                                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600 border-2 border-white shadow-sm ring-1 ring-gray-100" x-text="user.name.substring(0,1)"></div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <template x-for="user in getSelectedPhotos()" :key="user.id">
                                                <span class="inline-flex items-center gap-1.5 bg-indigo-50/50 text-indigo-700 px-2 py-0.5 rounded-md border border-indigo-100 text-[10px] font-bold shadow-sm">
                                                    <span class="w-1 h-1 rounded-full bg-indigo-400"></span>
                                                    <span x-text="user.name"></span>
                                                    <span class="text-[9px] text-indigo-400 font-medium ml-0.5 opacity-70" x-text="'| ' + user.bolum + ' - ' + user.role"></span>
                                                </span>
                                            </template>
                                            
                                            <template x-if="!isCompleted">
                                                <span class="text-[10px] font-bold ml-1 uppercase tracking-tight text-indigo-500">BEKLENİYOR...</span>
                                            </template>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 mt-1.5">
                                            <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span x-text="isCompleted ? 'Süreç ' + '<?php echo e($waitingSince); ?>' + ' sürdü' : '<?php echo e($waitingSince); ?>' + 'dir bekleniyor'"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="selectedIds.length === 0">
                                <span class="text-gray-400 italic text-xs flex items-center gap-1.5">
                                    <div class="p-1 bg-gray-200 rounded-full">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    Sorumlu atanmamış (Ortak Görev)
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4 border-b border-gray-100/50"></div>
            </div>

            
            <?php if($isCompleted): ?>
                <div class="flex items-center gap-2 mb-4">
                    <button @click.stop="open = !open" type="button" class="group flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 border-2 border-gray-100 hover:bg-indigo-50 hover:border-indigo-200 transition-all duration-300 shadow-sm">
                        <div class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                        </div>
                        <span class="text-[10px] font-black tracking-widest text-gray-600 group-hover:text-indigo-700 uppercase" x-text="open ? 'DETAYLARI KAPAT' : 'İÇERİĞİ GÖSTERMEK İÇİN TIKLAYIN'"></span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transition-transform duration-500" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>
            <?php endif; ?>

            
            <?php if($isLeader && !$isCompleted && $canEdit): ?>
                <div class="mb-4 p-3 bg-gray-50 rounded-xl border border-gray-200">
                    <form action="<?php echo e(route('proje.workspace.assignUserToStep', ['iaa' => $iaa->id, 'step' => $step->id])); ?>" method="POST" class="flex items-center gap-2" x-data="{ showOptions: false, selectedIds: <?php echo e(json_encode($sorumluUserIds)); ?>, hasChanged: false }">
                        <?php echo csrf_field(); ?>
                        <div class="relative">
                            <button @click="showOptions = !showOptions" @click.away="showOptions = false" type="button" class="bg-white border border-gray-300 text-gray-700 text-[11px] font-medium rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 block w-44 py-1.5 px-3 text-left hover:border-gray-400 transition-all flex items-center justify-between">
                                <span class="truncate">
                                    <span x-text="selectedIds.length > 0 ? selectedIds.length + ' Kişi Seçili' : '-- Sorumlu Seç --'"></span>
                                </span>
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div x-show="showOptions" x-cloak class="absolute left-0 mt-1 w-56 bg-white border border-gray-200 rounded-lg shadow-xl z-50 max-h-64 overflow-y-auto p-1 py-2">
                                <div class="px-2 pb-1 mb-1 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-tight">Proje Ekibi (Squad)</div>
                                <?php $__currentLoopData = $iaa->projeEkibi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekipUyesi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($ekipUyesi->pivot->durum == 'onaylandi'): ?>
                                        <label class="flex items-center px-2 py-1.5 hover:bg-indigo-50 rounded cursor-pointer transition-colors">
                                            <input type="checkbox" name="user_ids[]" value="<?php echo e($ekipUyesi->id); ?>" x-model="selectedIds" @change="hasChanged = true" class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <span class="ml-2 text-xs text-gray-700 truncate"><?php echo e($ekipUyesi->name); ?></span>
                                        </label>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php if($takim): ?>
                                    <div class="px-2 pb-1 mt-2 mb-1 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-tight">Diğer Takım Üyeleri</div>
                                    <?php $__currentLoopData = $takim->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takimUyesi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!$iaa->projeEkibi->contains('id', $takimUyesi->id)): ?>
                                            <label class="flex items-center px-2 py-1.5 hover:bg-indigo-50 rounded cursor-pointer transition-colors">
                                                <input type="checkbox" name="user_ids[]" value="<?php echo e($takimUyesi->id); ?>" x-model="selectedIds" @change="hasChanged = true" class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                <span class="ml-2 text-xs text-gray-700 truncate"><?php echo e($takimUyesi->name); ?><?php echo e($takimUyesi->id == $takim->lider_user_id ? ' (Lider)' : ''); ?></span>
                                            </label>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <button x-show="hasChanged" type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white text-[11px] font-bold rounded shadow-md hover:bg-green-700 transition-all transform hover:scale-105 active:scale-95" title="Atamayı Kaydet">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <span>KAYDET</span>
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            
            <?php if($isCompleted && $progressUpdate): ?>
                <?php
                    $isSafeIaa = is_null($iaa->musteri_sikayeti_id);
                    $assignedUserIds = $assignments->pluck('user_id')->toArray();
                    $isAssignedToSomeoneElse = false;
                    
                    if ($isSafeIaa && $assignments->isNotEmpty() && auth()->check()) {
                        $isAssignedToMe = in_array(auth()->id(), $assignedUserIds);
                        $isSuperAdmin = auth()->user()->hasRole('Superadmin');
                        $isLeader = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id);
                        
                        if (!$isAssignedToMe && !$isSuperAdmin && !$isLeader) {
                            $isAssignedToSomeoneElse = true;
                        }
                    }
                ?>
                <div x-show="open" x-transition>
                    <?php echo $__env->make('proje-calisma-alani.partials._step-content-completed', [
                        'progressUpdate' => $progressUpdate,
                        'step' => $step,
                        'iaa' => $iaa,
                        'isAssignedToSomeoneElse' => $isAssignedToSomeoneElse,
                        'assignments' => $assignments,
                        'canEdit' => $canEdit
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endif; ?>

            
            <?php if($isCurrent): ?>
                <?php
                    $isSafeIaa = is_null($iaa->musteri_sikayeti_id);
                    $assignedUserIds = $assignments->pluck('user_id')->toArray();
                    $isAssignedToSomeoneElse = false;
                    
                    if ($isSafeIaa && $assignments->isNotEmpty() && auth()->check()) {
                        $isAssignedToMe = in_array(auth()->id(), $assignedUserIds);
                        $isSuperAdmin = auth()->user()->hasRole('Superadmin');
                        
                        if (!$isAssignedToMe && !$isSuperAdmin && !$isLeader) {
                            $isAssignedToSomeoneElse = true;
                        }
                    }
                ?>

                <?php if($canEdit && !$isAssignedToSomeoneElse): ?>
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
                    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <span class="font-bold">İzleyici Modu:</span> 
                                    <?php if($isAssignedToSomeoneElse): ?>
                                        <?php
                                            $sorumluNames = \App\Models\User::whereIn('id', $assignedUserIds)->pluck('name')->implode(', ');
                                        ?>
                                        Bu adım <strong><?php echo e($sorumluNames); ?></strong> kullanıcısına/kullanıcılarına atanmıştır. Sadece sorumlu kişiler veya lider müdahale edebilir.
                                    <?php else: ?>
                                        Bu proje adımını görüntüleme yetkiniz var ancak müdahale edemezsiniz.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
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
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/proje-calisma-alani/partials/_step-item.blade.php ENDPATH**/ ?>