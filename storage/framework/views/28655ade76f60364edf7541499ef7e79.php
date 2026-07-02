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
    <?php $__env->startPush('pageTitle'); ?><?php echo e($sikayet->musteri_sikayet_konusu); ?> | <?php $__env->stopPush(); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight uppercase">
                    Şikayet Detayı <span class="text-slate-300 font-medium">#<?php echo e($sikayet->id); ?></span>
                </h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">
                    <?php echo e($sikayet->musteri_sikayet_konusu); ?>

                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <?php
                    $previousUrl = url()->previous();
                    $currentUrl = url()->current();
                    $defaultBack = auth()->user()->hasRole('Müşteri|Müşteri Temsilcisi') ? route('dashboard') : route('admin.sikayetler.index');
                    $isInternalReferer = $previousUrl && $previousUrl !== $currentUrl && str_contains($previousUrl, request()->getHttpHost());
                    $backUrl = $isInternalReferer ? $previousUrl : $defaultBack;
                ?>

                
                <div class="flex items-center gap-2 bg-white/50 p-1 rounded-2xl border border-slate-200 shadow-sm">
                    <a href="<?php echo e($backUrl); ?>"
                        class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl font-bold text-[11px] text-slate-600 uppercase tracking-widest hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Geri
                    </a>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $sikayet)): ?>
                        <a href="<?php echo e(route('admin.sikayetler.edit', $sikayet)); ?>"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-bold text-[11px] text-white uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Düzenle
                        </a>
                    <?php endif; ?>
                </div>

                
                <div class="flex items-center gap-2 bg-slate-800 p-1 rounded-2xl border border-slate-700 shadow-lg">
                    <?php if($sikayet->hatirlatmalar()->exists()): ?>
                        <a href="#musteri-hatirlatma-sureci" 
                           class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-xl font-bold text-[11px] text-slate-100 uppercase tracking-widest hover:bg-slate-600 transition-all" title="Hatırlatma Geçmişi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="ml-2 hidden lg:inline">Geçmiş</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php echo $__env->make('admin.sikayet-hatirlatma.partials._hatirlatma-butonu', ['sikayet' => $sikayet], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <?php if(session('success')): ?>
                <div
                    class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r shadow-sm flex items-center justify-between animate-pulse">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-emerald-800 font-medium"><?php echo e(session('success')); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($sikayet->iaaProjesi && $sikayet->iaaProjesi->projeEkibi()->where('users.id', auth()->id())->where('iaa_user.durum', 'bekliyor')->exists()): ?>
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl shadow-lg mb-8 overflow-hidden transform transition-all hover:scale-[1.01] duration-300">
                    <div class="px-6 py-5 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-4 text-white">
                            <div class="p-3 bg-white/20 backdrop-blur-md rounded-xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight">Bu şikayet için açılan projeye davet edildiniz!</h3>
                                <p class="text-emerald-50 text-sm font-medium">Bu şikayeti çözümlemek için oluşturulan İAA ekibine katılın.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <form action="<?php echo e(route('iaa.davetYanitla', $sikayet->iaaProjesi->id)); ?>" method="POST" class="flex-1 md:flex-none">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="yanit" value="kabul">
                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-white text-emerald-700 rounded-xl font-black text-sm shadow-xl hover:bg-emerald-50 transition-all active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    EKİBE KATIL
                                </button>
                            </form>
                            <form action="<?php echo e(route('iaa.davetYanitla', $sikayet->iaaProjesi->id)); ?>" method="POST" class="flex-1 md:flex-none">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="yanit" value="red">
                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-emerald-500/30 text-white border border-white/30 rounded-xl font-bold text-sm hover:bg-rose-500/40 hover:border-rose-300/50 transition-all active:scale-95">
                                    REDDET
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($sikayet->trashed()): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-red-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <div>
                            <h3 class="text-red-800 font-bold text-lg">Bu şikayet Çöp Kutusunda!</h3>
                            <p class="text-red-700 text-sm mt-0.5">Bu kayıt silinmiş olup, sadece yetkili yöneticiler tarafından görüntülenebilmektedir. Tekrar işleme almak için geri yükleyebilirsiniz.</p>
                        </div>
                    </div>
                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Super Admin|Yonetim|Yönetim')): ?>
                        <form action="<?php echo e(route('admin.sikayetler.restore', $sikayet->id)); ?>" method="POST" class="flex-shrink-0">
                            <?php echo csrf_field(); ?>
                            <button type="submit" onclick="return confirm('Bu şikayeti çöp kutusundan geri çıkarmak istediğinize emin misiniz?')" 
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Şikayeti Geri Al
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 mb-6 p-4">
                <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 text-center divide-x divide-gray-100">

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Durum</span>
                        <div class="flex flex-col items-center gap-1.5">
                            <?php echo $sikayet->musteri_durum_badge; ?>

                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Öncelik</span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold <?php echo e($sikayet->oncelik_badge_class); ?>">
                            <?php echo e($sikayet->musteri_oncelik); ?>

                        </span>
                    </div>

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Bölge</span>
                        <?php if($sikayet->konum_tipi === 'Yurt İçi'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                YURT İÇİ
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-fuchsia-100 text-fuchsia-800 border border-fuchsia-200">
                                YURT DIŞI
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Puan</span>
                        <div class="flex items-center gap-2">
                            
                            
                            <?php if($sikayet->musteri_puan): ?>
                                <span
                                    class="text-sm bg-yellow-100 text-yellow-800 px-2 py-1 rounded border border-yellow-200 font-bold"
                                    title="Müşteri Puanı">
                                    ★ <?php echo e(number_format($sikayet->musteri_puan, 2)); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-sm text-gray-400 italic">Puanlanmamış</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Kategori / Bölüm</span>
                        <div class="flex flex-col items-center">
                            <span
                                class="text-sm font-bold text-gray-800"><?php echo e($sikayet->sikayetKategori->ad ?? 'Genel'); ?></span>
                            <?php if($sikayet->sikayetAltKategori): ?>
                                <span class="text-xs text-gray-500"><?php echo e($sikayet->sikayetAltKategori->ad); ?></span>
                                <?php if(Str::lower(trim($sikayet->sikayetAltKategori->ad)) === 'diğer / belirtilmemiş' || Str::lower(trim($sikayet->sikayetAltKategori->ad)) === 'diğer'): ?>
                                    <?php if($sikayet->sikayet_alt_kategori_diger): ?>
                                        <span class="text-[10px] text-gray-400 italic mt-0.5 text-center px-2 cursor-help" title="<?php echo e($sikayet->sikayet_alt_kategori_diger); ?>">
                                            (<?php echo e(Str::limit($sikayet->sikayet_alt_kategori_diger, 25)); ?>)
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php elseif($sikayet->sikayet_alt_kategori_diger): ?>
                                <span class="text-xs text-gray-500" title="<?php echo e($sikayet->sikayet_alt_kategori_diger); ?>"><?php echo e(Str::limit($sikayet->sikayet_alt_kategori_diger, 25)); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center p-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest mb-1">Çözüm Süresi</span>
                        <?php
                            $isResolved = in_array(trim($sikayet->musteri_durum), ['Çözümlendi', 'Kapatıldı', 'Tamamlandı']);
                            // Çözüm tarihi yoksa updated_at al, o da yoksa null
                            $solvedDate = $sikayet->musteri_cozum_tarihi ?? $sikayet->updated_at;
                        ?>

                        
                        <?php if($isResolved && $solvedDate): ?>
                            <?php
                                $created = $sikayet->created_at;
                                // floatDiffInDays ile tam gün farkını al, yukarı yuvarla (örn: 0.1 gün -> 1 gün)
                                $diff = $created->floatDiffInDays($solvedDate);
                                $days = ceil($diff);
                                if ($days < 1)
                                    $days = 1;
                             ?>
                            <span class="text-sm font-bold text-emerald-600 animate-pulse">
                                <?php echo e(intval($days)); ?> Günde Çözüldü
                            </span>

                            
                        <?php elseif($sikayet->musteri_cozum_son_tarihi): ?>
                            <?php
                                $daysLeft = now()->diffInDays($sikayet->musteri_cozum_son_tarihi, false);
                            ?>

                            <?php if($daysLeft < 0): ?>
                                <span class="text-sm font-bold text-red-600 animate-pulse">
                                    <?php echo e(abs(intval($daysLeft))); ?> Gün Geçti!
                                </span>
                                <span
                                    class="text-[10px] text-gray-400 block"><?php echo e($sikayet->musteri_cozum_son_tarihi->format('d.m.Y')); ?></span>
                            <?php else: ?>
                                <span class="text-sm font-bold text-green-600">
                                    <?php echo e(intval($daysLeft)); ?> Gün Kaldı
                                </span>
                                <span
                                    class="text-[10px] text-gray-400 block"><?php echo e($sikayet->musteri_cozum_son_tarihi->format('d.m.Y')); ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-sm text-gray-400">-</span>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 mb-6 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                        Müşteri Şikayeti Bölüm Süreci
                    </h3>

                    
                    <?php if($sikayet->iaaProjesi): ?>
                        <a href="<?php echo e(route('proje.workspace.show', $sikayet->iaaProjesi->id)); ?>"
                            class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:border-indigo-300 transition-colors shadow-sm font-semibold text-xs uppercase tracking-wide">
                            <span class="mr-2">🚀</span>İlgili İyileştirme Projesine Git
                        </a>
                    <?php endif; ?>
                </div>

                
                <?php
                    $steps = [
                        'Yeni' => ['color' => 'bg-yellow-400', 'text' => 'text-yellow-600', 'icon_bg' => 'bg-yellow-100'],
                        'Atandı' => ['color' => 'bg-blue-500', 'text' => 'text-blue-600', 'icon_bg' => 'bg-blue-100'],
                        'İnceleniyor' => ['color' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'icon_bg' => 'bg-indigo-100'],
                        'Çözümlendi' => ['color' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'icon_bg' => 'bg-emerald-100'],
                        'Kapatıldı' => ['color' => 'bg-gray-600', 'text' => 'text-gray-700', 'icon_bg' => 'bg-gray-100']
                    ];

                    $stepKeys = array_keys($steps);
                    $currentStatus = trim($sikayet->musteri_durum);

                    // Eşleşme düzeltmeleri
                    // EĞER PROJE REVİZE EDİLİYOR VEYA ONAY BEKLİYORSA -> İNCELENİYOR ADIMINA GEÇİR
                    if ($sikayet->iaaProjesi) {
                        $inceleniyorDurumlari = ['Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Onay Bekliyor', 'İşlemde', 'Devam Ediyor'];
                        if (in_array($sikayet->iaaProjesi->durum, $inceleniyorDurumlari)) {
                            $currentStatus = 'İnceleniyor';
                        } elseif ($sikayet->iaaProjesi->durum == 'Atandı' && $currentStatus == 'Yeni') {
                            $currentStatus = 'Atandı';
                        }
                    }
                    if ($currentStatus == 'İşlemde' || $currentStatus == 'Devam Ediyor') {
                        $currentStatus = 'İnceleniyor';
                    }

                    $currentIndex = array_search($currentStatus, $stepKeys);
                    if ($currentIndex === false)
                        $currentIndex = 0; 
                ?>

                <div class="relative">
                    
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-100 -translate-y-1/2 rounded z-0"></div>

                    
                    <?php
                        $activeColorClass = $steps[$stepKeys[$currentIndex]]['color'];
                        $progressWidth = ($currentIndex / (count($steps) - 1)) * 100;
                    ?>
                    <div class="absolute top-1/2 left-0 h-1 <?php echo e($activeColorClass); ?> -translate-y-1/2 rounded z-0 transition-all duration-700"
                        style="width: <?php echo e($progressWidth); ?>%"></div>

                    <div class="relative z-10 flex justify-between">
                        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $index = array_search($key, $stepKeys);
                                $isActive = $index <= $currentIndex;
                                $isCurrent = $index === $currentIndex;
                            ?>
                            <div class="flex flex-col items-center group">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-all duration-300 <?php echo e($isActive ? $style['color'] . ' border-white shadow-md' : 'bg-white border-gray-200'); ?>">
                                    <?php if($isActive): ?>
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    <?php else: ?>
                                        <span class="w-2 h-2 rounded-full bg-gray-300 group-hover:bg-gray-400"></span>
                                    <?php endif; ?>
                                </div>
                                <span
                                    class="mt-2 text-xs font-bold transition-colors <?php echo e($isActive ? $style['text'] : 'text-gray-400'); ?>">
                                    <?php echo e($key); ?>

                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                
                <div class="lg:col-span-2 space-y-6">

                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center border-b pb-2">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Şikayet Detayları
                            </h3>

                            <div class="mb-4">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Konu</span>
                                <p class="text-gray-900 font-medium text-lg"><?php echo e($sikayet->musteri_sikayet_konusu); ?></p>
                            </div>

                            <div class="mb-6">
                                <span
                                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Açıklama</span>
                                <div class="mt-2 text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100 text-sm leading-relaxed whitespace-pre-wrap font-sans"><?php echo e($sikayet->musteri_sikayet_detayi); ?></div>
                            </div>
                        </div>
                    </div>                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center border-b pb-2">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                    </path>
                                </svg>
                                Üretim ve Ürün Bilgileri
                            </h3>

                            <?php if($sikayet->teknikDetaylar->isNotEmpty()): ?>
                                <div class="space-y-3">
                                    <?php $__currentLoopData = $sikayet->teknikDetaylar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div
                                            class="bg-gray-50 p-3 rounded-lg border border-gray-100 relative group hover:border-indigo-200 transition">
                                            <span
                                                class="absolute top-2 right-2 text-[10px] font-bold text-gray-300 group-hover:text-indigo-300">#<?php echo e($loop->iteration); ?></span>
                                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                                <div>
                                                    <span class="text-xs text-gray-500 block mb-1">Lot Numarası</span>
                                                    <span
                                                        class="font-mono text-sm font-bold text-gray-800"><?php echo e($detay->lot_no ?? '-'); ?></span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 block mb-1">Makine / Hat</span>
                                                    <span class="text-sm font-bold text-gray-800">
                                                        <?php echo e($detay->machine->name ?? '-'); ?>

                                                        <?php if($detay->machine && $detay->machine->code): ?>
                                                            <span class="text-xs text-gray-400">(<?php echo e($detay->machine->code); ?>)</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 block mb-1">Hammadde</span>
                                                    <span
                                                        class="text-sm font-bold text-gray-800"><?php echo e($detay->genelHammadde->ad ?? '-'); ?></span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 block mb-1">Ürün Versiyonu</span>
                                                    <span
                                                        class="text-sm font-bold text-gray-800"><?php echo e($detay->urunVersiyonu->ad ?? '-'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php elseif($sikayet->lot_no || $sikayet->machine_id || $sikayet->genel_hammadde_id || $sikayet->urun_versiyonu_id): ?>
                                
                                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div>
                                            <span class="text-xs text-gray-500 block mb-1">Lot Numarası</span>
                                            <span
                                                class="font-mono text-sm font-bold text-gray-800"><?php echo e($sikayet->lot_no ?? '-'); ?></span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 block mb-1">Makine / Hat</span>
                                            <span
                                                class="text-sm font-bold text-gray-800"><?php echo e($sikayet->machine->name ?? '-'); ?></span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 block mb-1">Hammadde</span>
                                            <span
                                                class="text-sm font-bold text-gray-800"><?php echo e($sikayet->genelHammadde->ad ?? '-'); ?></span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 block mb-1">Ürün Versiyonu</span>
                                            <span
                                                class="text-sm font-bold text-gray-800"><?php echo e($sikayet->urunVersiyonu->ad ?? '-'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-gray-400 text-sm italic">
                                    Bu şikayet için teknik detay girilmemiştir.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4 border-b pb-2">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                        </path>
                                    </svg>
                                    Kanıt Dosyaları
                                </h3>
                                <span
                                    class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold"><?php echo e($sikayet->dosyalar->count()); ?>

                                    Dosya</span>
                            </div>

                            <?php if($sikayet->dosyalar->count() > 0): ?>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <?php $__currentLoopData = $sikayet->dosyalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $extension = strtolower(pathinfo($dosya->dosya_yolu, PATHINFO_EXTENSION));
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            $isVideo = in_array($extension, ['mp4', 'mov', 'avi']);
                                            $fileUrl = asset('storage/' . $dosya->dosya_yolu);
                                        ?>

                                        <a href="<?php echo e($fileUrl); ?>" data-fancybox="gallery"
                                            data-caption="<?php echo e($dosya->orijinal_adi); ?>"
                                            class="group relative aspect-square bg-gray-100 rounded-lg overflow-hidden border hover:border-indigo-400 transition cursor-zoom-in">

                                            <?php if($isImage): ?>
                                                <img src="<?php echo e($fileUrl); ?>"
                                                    class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                                                    alt="Evidence">
                                            <?php elseif($isVideo): ?>
                                                <div class="w-full h-full flex items-center justify-center bg-gray-900">
                                                    <svg class="w-10 h-10 text-white opacity-80" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-2">
                                                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                    <span
                                                        class="text-xs text-center text-gray-500 font-medium truncate w-full"><?php echo e($dosya->orijinal_adi); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <div
                                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all flex items-end p-2 opacity-0 group-hover:opacity-100">
                                                <span
                                                    class="text-xs text-white bg-black bg-opacity-50 px-2 py-1 rounded truncate w-full"><?php echo e($dosya->orijinal_adi); ?></span>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <div
                                    class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-300 text-gray-500 italic">
                                    Dosya yüklenmemiş.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <?php if($sikayet->iaaProjesi && $sikayet->iaaProjesi->ziyaretPlani): ?>
                        <?php
                            $ziyaret = $sikayet->iaaProjesi->ziyaretPlani;
                            $zStatus = $ziyaret->status;
                            $ziyaretRenk = match($zStatus) {
                                'Tamamlandı' => 'blue',
                                'Revizyon Bekliyor', 'Revize' => 'orange',
                                'Reddedildi' => 'red',
                                'Beklemede' => 'yellow',
                                'Onaylandı' => 'green',
                                default => 'gray'
                            };
                        ?>
                        <div class="bg-gradient-to-br from-<?php echo e($ziyaretRenk); ?>-50 to-white overflow-hidden shadow-sm sm:rounded-xl border mb-6 border-<?php echo e($ziyaretRenk); ?>-100 relative">
                            <div class="absolute top-0 left-0 w-1 h-full bg-<?php echo e($ziyaretRenk); ?>-500"></div>
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex justify-between items-center border-b border-<?php echo e($ziyaretRenk); ?>-100 pb-2">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-<?php echo e($ziyaretRenk); ?>-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Müşteri Ziyareti Bilgileri
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-<?php echo e($ziyaretRenk); ?>-100 text-<?php echo e($ziyaretRenk); ?>-800">
                                        <?php echo e($zStatus); ?>

                                    </span>
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-[13px] text-gray-700 font-medium w-full">
                                    <!-- Planlanan Tarih -->
                                    <div class="bg-white/50 p-3 rounded-xl border border-<?php echo e($ziyaretRenk); ?>-100 flex flex-col gap-1">
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Planlanan Tarih</span>
                                        <span class="font-black text-gray-900 text-sm flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <?php echo e(\Carbon\Carbon::parse($ziyaret->visit_date)->format('d.m.Y H:i')); ?>

                                        </span>
                                    </div>

                                    <!-- Planlayan -->
                                    <div class="bg-white/50 p-3 rounded-xl border border-<?php echo e($ziyaretRenk); ?>-100 flex flex-col gap-1">
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Planlayan</span>
                                        <span class="font-black text-gray-900 text-sm"><?php echo e($ziyaret->planner->name ?? 'Bilinmiyor'); ?></span>
                                    </div>

                                    <!-- Tahmini Dönüş Tarihi -->
                                    <div class="bg-white/50 p-3 rounded-xl border border-<?php echo e($ziyaretRenk); ?>-100 flex flex-col gap-1">
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tahmini Dönüş Tarihi</span>
                                        <span class="font-black text-gray-900 text-sm">
                                            <?php echo e($ziyaret->estimated_return_date ? \Carbon\Carbon::parse($ziyaret->estimated_return_date)->format('d.m.Y') : 'Belirtilmedi'); ?>

                                        </span>
                                    </div>

                                    <!-- Ziyaret Gün Sayısı -->
                                    <div class="bg-white/50 p-3 rounded-xl border border-<?php echo e($ziyaretRenk); ?>-100 flex flex-col gap-1">
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ziyaret Gün Sayısı</span>
                                        <span class="font-black text-gray-900 text-sm">
                                            <?php if($ziyaret->estimated_return_date && $ziyaret->visit_date): ?>
                                                <?php
                                                    $diff = \Carbon\Carbon::parse($ziyaret->visit_date)->floatDiffInDays(\Carbon\Carbon::parse($ziyaret->estimated_return_date));
                                                    $gunSayisi = ceil($diff);
                                                    if ($gunSayisi < 1) $gunSayisi = 1;
                                                ?>
                                                <?php echo e($gunSayisi); ?> Gün
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <!-- Durum Detayı -->
                                    <div class="bg-white/50 p-3 rounded-xl border border-<?php echo e($ziyaretRenk); ?>-100 flex flex-col gap-1">
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Durum Detayı</span>
                                        <div class="text-sm">
                                            <?php if($zStatus == 'Tamamlandı'): ?>
                                                <p class="text-blue-700 font-medium">
                                                    <span class="font-bold">Ziyarete giden:</span> 
                                                    <?php if(is_array($ziyaret->visitors) && count($ziyaret->visitors) > 0): ?>
                                                        <?php echo e(implode(', ', \App\Models\User::whereIn('id', $ziyaret->visitors)->pluck('name')->toArray())); ?>

                                                    <?php else: ?>
                                                        <?php echo e($ziyaret->visitor->name ?? 'Bilinmiyor'); ?>

                                                    <?php endif; ?>
                                                </p>
                                            <?php elseif($zStatus == 'Beklemede'): ?>
                                                <p class="text-yellow-700 font-medium">
                                                    <span class="font-bold">Onayını bekliyor:</span> <?php echo e($ziyaret->approver_id ? (\App\Models\User::find($ziyaret->approver_id)->name ?? 'Yönetici') : 'Yönetici'); ?>

                                                    <span class="text-xs text-yellow-600 block">(<?php echo e(now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($ziyaret->created_at)->startOfDay())); ?> gündür bekliyor)</span>
                                                </p>
                                            <?php elseif($zStatus == 'Revizyon Bekliyor' || $zStatus == 'Revize'): ?>
                                                <p class="text-orange-700 font-medium">
                                                    <span class="font-bold">Revize edecek:</span> 
                                                    <?php if(is_array($ziyaret->visitors) && count($ziyaret->visitors) > 0): ?>
                                                        <?php echo e(implode(', ', \App\Models\User::whereIn('id', $ziyaret->visitors)->pluck('name')->toArray())); ?>

                                                    <?php else: ?>
                                                        <?php echo e($ziyaret->visitor->name ?? 'Bilinmiyor'); ?>

                                                    <?php endif; ?>
                                                    <span class="text-xs text-orange-600 block">(<?php echo e(now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($ziyaret->updated_at)->startOfDay())); ?> gündür bekliyor)</span>
                                                </p>
                                            <?php elseif($zStatus == 'Onaylandı'): ?>
                                                <?php
                                                    $daysLeft = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($ziyaret->visit_date)->startOfDay(), false);
                                                ?>
                                                <p class="text-green-700 font-medium">
                                                    <?php if($daysLeft > 0): ?>
                                                        Ziyarete gidilecek tarihe <span class="font-bold"><?php echo e($daysLeft); ?> gün</span> kaldı.
                                                    <?php elseif($daysLeft == 0): ?>
                                                        Ziyaret <span class="font-bold">bugün</span> gerçekleştirilecek.
                                                    <?php else: ?>
                                                        Ziyaret tarihi <span class="font-bold"><?php echo e(abs($daysLeft)); ?> gün</span> geçti.
                                                    <?php endif; ?>
                                                </p>
                                            <?php elseif($zStatus == 'Reddedildi'): ?>
                                                <p class="text-red-700 font-medium">
                                                    <span class="font-bold">Reddeden:</span> <?php echo e($ziyaret->approver_id ? (\App\Models\User::find($ziyaret->approver_id)->name ?? 'Yönetici') : 'Yönetici'); ?>

                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if($zStatus == 'Tamamlandı'): ?>
                                        <!-- Ziyaret Sonuçlarını Giren -->
                                        <div class="bg-white/50 p-3 rounded-xl border border-<?php echo e($ziyaretRenk); ?>-100 flex flex-col gap-1">
                                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sonuçları Giren</span>
                                            <span class="font-black text-gray-900 text-sm"><?php echo e($ziyaret->completer->name ?? 'Bilinmiyor'); ?></span>
                                        </div>

                                        <!-- Sonuç Giriş Tarihi -->
                                        <div class="bg-white/50 p-3 rounded-xl border border-<?php echo e($ziyaretRenk); ?>-100 flex flex-col gap-1">
                                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Giriş Tarihi</span>
                                            <span class="font-black text-gray-900 text-sm">
                                                <?php echo e($ziyaret->completed_at ? \Carbon\Carbon::parse($ziyaret->completed_at)->format('d.m.Y H:i') : '-'); ?>

                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if($zStatus == 'Onaylandı' && now()->isAfter(\Carbon\Carbon::parse($ziyaret->visit_date))): ?>
                                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 flex items-center gap-2 animate-pulse shadow-sm">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span class="text-sm font-bold">Henüz ziyaret sonucu girilmemiş!</span>
                                    </div>
                                <?php endif; ?>

                                <?php if($ziyaret->purpose || $ziyaret->notes): ?>
                                    <div class="mt-4">
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Ziyaret Amacı / Notlar</span>
                                        <p class="text-sm text-gray-700 bg-white/50 p-3 rounded-xl border border-<?php echo e($ziyaretRenk); ?>-100 whitespace-pre-wrap"><?php echo e($ziyaret->purpose ?? $ziyaret->notes); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php
                        $hatirlatmalar = $sikayet->hatirlatmalar()->with('yorumlar.user')->latest()->get();
                    ?>

                    <?php if($hatirlatmalar->count() > 0): ?>
                        <div id="musteri-hatirlatma-sureci" class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center border-b pb-2">
                                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    Müşteri Hatırlatma Süreci
                                </h3>

                                <div class="space-y-4">
                                    <?php $__currentLoopData = $hatirlatmalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div onclick="window.location='<?php echo e(route('admin.sikayet-hatirlatma.show', $hat->id)); ?>'" 
                                             class="p-4 rounded-xl border cursor-pointer transition-all hover:shadow-md hover:scale-[1.01] <?php echo e($hat->durum == 'musteri_ikna_oldu' ? 'bg-emerald-50 border-emerald-100' : 'bg-red-50 border-red-100'); ?>">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 block mb-1">Hatırlatma #<?php echo e($hat->id); ?></span>
                                                    <h4 class="font-bold text-sm text-gray-800">Gönderim: <?php echo e($hat->created_at->format('d.m.Y H:i')); ?></h4>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold <?php echo e($hat->durum == 'musteri_ikna_oldu' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'); ?>">
                                                    <?php echo e(str_replace('_', ' ', strtoupper($hat->durum))); ?>

                                                </span>
                                            </div>

                                            <?php if($hat->yorumlar->count() > 0): ?>
                                                <div class="mt-3 pl-4 border-l-2 border-gray-300 space-y-3">
                                                    <?php if($hat->yorumlar->count() > 5): ?>
                                                        <div class="text-[10px] text-slate-400 italic mb-2">
                                                            <i class="fa fa-info-circle mr-1"></i> Önceki <?php echo e($hat->yorumlar->count() - 5); ?> mesaj gizlendi. Detaylar için tıklayın.
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php $__currentLoopData = $hat->yorumlar->take(-5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="text-sm bg-white bg-opacity-50 p-2 rounded-lg border border-gray-100 shadow-sm">
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-bold text-gray-800 text-xs"><?php echo e($log->user->name); ?></span>
                                                                <span class="text-[10px] text-gray-500"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></span>
                                                            </div>
                                                            <p class="text-gray-900 leading-snug">"<?php echo e($log->yorum); ?>"</p>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Yonetim')): ?>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Süreç Geçmişi & Loglar
                        </h3>

                        <?php if(isset($sikayet->loglar) && $sikayet->loglar->count() > 0): ?>
                            <div class="relative pl-4 sm:pl-6 border-l-2 border-indigo-100 space-y-8 before:absolute before:inset-0 before:ml-[15px] sm:before:ml-[23px] before:-translate-x-px md:before:mx-auto md:before:translate-x-0">
                                <?php $__currentLoopData = $sikayet->loglar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="relative flex items-start gap-4 group">
                                        
                                        <div class="absolute -left-[30px] sm:-left-[38px] mt-1 h-8 w-8 rounded-full border-4 border-white bg-white flex items-center justify-center shadow-sm z-10 transition-transform group-hover:scale-110">
                                            <?php if($log->eylem == 'Oluşturuldu'): ?>
                                                <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                </div>
                                            <?php elseif($log->eylem == 'Düzenlendi' || $log->eylem == 'Bağlandı'): ?>
                                                <div class="h-6 w-6 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </div>
                                            <?php else: ?>
                                                <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        
                                        <div class="flex-1 min-w-0 bg-gray-50 rounded-xl p-4 sm:p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2 sm:mb-1 gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-gray-900 text-sm sm:text-base"><?php echo e(optional($log->user)->name ?? 'Sistem / Üye Olmayan'); ?></span>
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold
                                                        <?php echo e($log->eylem == 'Oluşturuldu' ? 'bg-emerald-100 text-emerald-800' : ''); ?>

                                                        <?php echo e($log->eylem == 'Düzenlendi' ? 'bg-amber-100 text-amber-800' : ''); ?>

                                                        <?php echo e($log->eylem == 'Bağlandı' ? 'bg-purple-100 text-purple-800' : ''); ?>

                                                        <?php echo e(!in_array($log->eylem, ['Oluşturuldu', 'Düzenlendi', 'Bağlandı']) ? 'bg-blue-100 text-blue-800' : ''); ?>

                                                    ">
                                                        <?php echo e($log->eylem); ?>

                                                    </span>
                                                </div>
                                                <div class="text-xs sm:text-sm text-gray-500 font-medium flex items-center whitespace-nowrap">
                                                    <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <?php echo e($log->created_at->format('d.m.Y H:i')); ?>

                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-600 mt-2 sm:mt-1 leading-relaxed">
                                                <?php echo e($log->islem_aciklamasi ?? $log->aciklama); ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                
                                <div class="relative flex items-start gap-4 group">
                                    <div class="absolute -left-[30px] sm:-left-[38px] mt-1 h-8 w-8 rounded-full border-4 border-white bg-white flex items-center justify-center shadow-sm z-10">
                                        <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 bg-emerald-50 bg-opacity-50 rounded-xl p-4 sm:p-5 border border-emerald-100 shadow-sm">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2 sm:mb-1 gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900 text-sm sm:text-base">Sistem</span>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-100 text-emerald-800">Oluşturuldu</span>
                                            </div>
                                            <div class="text-xs sm:text-sm text-gray-500 font-medium flex items-center whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <?php echo e($sikayet->created_at->format('d.m.Y H:i')); ?>

                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-2 sm:mt-1 leading-relaxed">
                                            Şikayet kaydı oluşturuldu.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-6 md:p-8 bg-gray-50 border border-gray-100 rounded-xl flex flex-col items-center justify-center text-center">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <h4 class="text-base font-semibold text-gray-900 mb-1">Henüz Kayıt Yok</h4>
                                <p class="text-sm text-gray-500 max-w-sm">Bu şikayet için herhangi bir düzenleme veya işlem geçmişi bulunmuyor.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>

                
                <div class="space-y-6">

                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Müşteri Bilgileri
                        </h3>

                        <div class="flex items-center mb-4">
                            <?php if($sikayet->customer && $sikayet->customer->logo_path): ?>
                                <img src="<?php echo e(asset('storage/' . $sikayet->customer->logo_path)); ?>"
                                    class="w-12 h-12 rounded-lg object-contain bg-gray-50 border p-1 mr-3" alt="Logo">
                            <?php else: ?>
                                <div
                                    class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-700 font-bold text-lg mr-3">
                                    <?php echo e(substr($sikayet->musteri_adi, 0, 1)); ?>

                                </div>
                            <?php endif; ?>
                            <div class="overflow-hidden">
                                <h4 class="font-bold text-gray-900 truncate" title="<?php echo e($sikayet->musteri_adi); ?>">
                                    <?php if($sikayet->customer_id): ?>
                                        <a href="<?php echo e(route('musteri.profil.show', $sikayet->customer_id)); ?>" target="_blank"
                                            class="hover:underline hover:text-indigo-600">
                                            <?php echo e($sikayet->musteri_adi); ?>

                                        </a>
                                    <?php else: ?>
                                        <?php echo e($sikayet->musteri_adi); ?>

                                    <?php endif; ?>
                                </h4>
                                <span
                                    class="text-xs text-gray-500 inline-block bg-gray-100 px-1.5 py-0.5 rounded mt-1"><?php echo e($sikayet->konum_tipi ?? 'Belirtilmemiş'); ?></span>
                                
                                
                                <?php if(!$sikayet->customer_id): ?>
                                    <?php
                                        $user = auth()->user();
                                        $canAssign = $user->hasAnyRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Yonetim']);
                                        if(!$canAssign && $user->hasRole('Bölüm Kalite Yöneticisi')) {
                                            $yonetilenKategoriIds = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
                                            if (empty($yonetilenKategoriIds) && $user->bolum_id) {
                                                $yonetilenKategoriIds = \App\Models\SikayetKategori::where('bolum_id', $user->bolum_id)->pluck('id')->toArray();
                                            }
                                            $canAssign = in_array($sikayet->sikayet_kategorisi_id, $yonetilenKategoriIds);
                                        }
                                    ?>
                                    <?php if($canAssign): ?>
                                        <button type="button" 
                                            onclick="Livewire.dispatch('openMusteriAtamaModal', { sikayetId: <?php echo e($sikayet->id); ?> })"
                                            class="mt-3 w-full flex items-center justify-center px-3 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-all text-[11px] font-bold uppercase tracking-wider">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                            Müşteri Tanımla
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <?php if($sikayet->customer_id): ?>
                                <button type="button" 
                                    onclick="Livewire.dispatch('openDigerSikayetlerModal', { customerId: <?php echo e($sikayet->customer_id); ?>, currentSikayetId: <?php echo e($sikayet->id); ?> })"
                                    class="w-full grid grid-cols-2 gap-2 p-3 bg-gray-50 rounded-lg border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/50 transition-all cursor-pointer group focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <div class="text-center group-hover:scale-105 transition-transform">
                                        <span class="block text-xs text-gray-400 group-hover:text-indigo-400">Toplam Şikayet</span>
                                        <span class="block text-lg font-bold text-gray-800 group-hover:text-indigo-700"><?php echo e($firmaSikayetSayisi ?? '-'); ?></span>
                                    </div>
                                    <div class="text-center border-l border-gray-200 group-hover:border-indigo-100 group-hover:scale-105 transition-transform">
                                        <span class="block text-xs text-gray-400 group-hover:text-indigo-400">Bu Şikayet</span>
                                        <span class="block text-lg font-bold text-indigo-600"><?php echo e($kacinciSikayet ?? '-'); ?>.</span>
                                    </div>
                                </button>
                                <div class="text-[10px] text-center text-gray-400 mt-1 italic">
                                    Firmanın diğer şikayetlerini görmek için tıklayın
                                </div>
                            <?php else: ?>
                                <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="text-center">
                                        <span class="block text-xs text-gray-400">Toplam Şikayet</span>
                                        <span class="block text-lg font-bold text-gray-800"><?php echo e($firmaSikayetSayisi ?? '-'); ?></span>
                                    </div>
                                    <div class="text-center border-l border-gray-200">
                                        <span class="block text-xs text-gray-400">Bu Şikayet</span>
                                        <span class="block text-lg font-bold text-indigo-600"><?php echo e($kacinciSikayet ?? '-'); ?>.</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-gray-100">
                            <?php if($sikayet->customer): ?>
                                <div class="mb-3">
                                    <span class="text-xs text-gray-400 block">Firma Adresi</span>
                                    <span class="text-sm font-medium text-gray-800 block break-words"><?php echo e($sikayet->customer->address ?? '-'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-xs text-gray-400 block">Gövde E-posta</span>
                                    <span class="text-sm font-medium text-gray-800 block break-words"><?php echo e($sikayet->customer->email ?? '-'); ?></span>
                                </div>
                            <?php endif; ?>

                            <div>
                                <span class="text-xs text-gray-400 block">Firma İletişim (İlgili)</span>
                                <span
                                    class="text-sm font-medium text-gray-800 block break-words"><?php echo e($sikayet->musteri_iletisim ?? '-'); ?></span>
                            </div>

                            <?php
                                $snapshot = json_decode($sikayet->notified_snapshot, true) ?: [];
                                $notifiedIds = collect($snapshot)->pluck('user_id')->toArray();
                            ?>

                            <?php if($sikayet->yetkili_user): ?>
                                <div>
                                    <span class="text-xs text-gray-400 block">Yetkili Kişi</span>
                                    <div class="flex items-center mt-1">
                                        <div
                                            class="w-6 h-6 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold mr-2 flex-shrink-0">
                                            <?php echo e(substr($sikayet->yetkili_user->name, 0, 1)); ?>

                                        </div>
                                        <div class="overflow-hidden flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-800 truncate"><?php echo e($sikayet->yetkili_user->name); ?></span>
                                                
                                                
                                                <?php if(in_array($sikayet->yetkili_user->id, $notifiedIds)): ?>
                                                    <span class="inline-flex text-emerald-500" title="Bildirim Başarıyla Gönderildi">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex text-gray-300" title="Bildirim Bu Kişiye Gönderilmedi (Snapshota dahil değil)">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="text-xs text-gray-500 block truncate"><?php echo e($sikayet->yetkili_user->email); ?></span>
                                            <?php if($sikayet->yetkili_unvani): ?>
                                                <span class="text-[10px] text-indigo-500 font-bold block mt-0.5 uppercase"><?php echo e($sikayet->yetkili_unvani); ?></span>
                                            <?php endif; ?>
                                            <?php if($sikayet->yetkili_user->telefon): ?>
                                                <span class="text-xs text-gray-500 block font-bold mt-0.5">
                                                    <i class="fa fa-phone mr-1"></i> <?php echo e($sikayet->yetkili_user->telefon); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            
                            <?php if($sikayet->ekYetkililer->isNotEmpty()): ?>
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <span class="text-xs text-gray-400 block mb-2">Ek İlgililer</span>
                                    <div class="space-y-3">
                                        <?php $__currentLoopData = $sikayet->ekYetkililer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-center">
                                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold mr-2 flex-shrink-0">
                                                    <?php echo e(substr($ek->name, 0, 1)); ?>

                                                </div>
                                                <div class="overflow-hidden flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-bold text-gray-700 truncate"><?php echo e($ek->name); ?></span>
                                                        
                                                        <?php if(in_array($ek->id, $notifiedIds)): ?>
                                                            <span class="inline-flex text-emerald-500" title="Bildirim Başarıyla Gönderildi">
                                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="inline-flex text-red-300" title="Bildirim henüz bu kişiye iletilemedi veya kapsam dışı.">
                                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex flex-col text-[10px] text-gray-500">
                                                        <span><?php echo e($ek->email); ?></span>
                                                        <?php
                                                            $ekPivot = \Illuminate\Support\Facades\DB::table('customer_user')
                                                                ->where('customer_id', $sikayet->customer_id)
                                                                ->where('user_id', $ek->id)
                                                                ->first();
                                                            $ekUnvan = $ekPivot?->unvan ?? $ek->unvan;
                                                        ?>
                                                        <?php if($ekUnvan): ?>
                                                            <span class="text-indigo-500 font-bold uppercase"><?php echo e($ekUnvan); ?></span>
                                                        <?php endif; ?>
                                                        <?php if($ek->telefon): ?>
                                                            <span class="font-bold text-gray-600"><?php echo e($ek->telefon); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            Dahili Bilgiler
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <?php if($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->hasRole(['Müşteri Temsilcisi', 'Müşteri'])): ?>
                                    <span class="text-xs text-red-600 font-bold block">Şikayeti Giren Müşteri Temsilcisi</span>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400 block">Şikayeti Oluşturan Personel</span>
                                <?php endif; ?>
                                <?php if($sikayet->olusturanKurulUyesi): ?>
                                    <a href="<?php echo e(route('profile.show', $sikayet->olusturanKurulUyesi->id)); ?>"
                                        title="E-posta: <?php echo e($sikayet->olusturanKurulUyesi->email); ?> <?php echo e($sikayet->olusturanKurulUyesi->telefon ? ' | Tel: ' . $sikayet->olusturanKurulUyesi->telefon : ''); ?>"
                                        class="flex items-center mt-1 group hover:bg-gray-50 p-1 rounded -ml-1 transition">
                                        <div
                                            class="w-6 h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold mr-2 flex-shrink-0">
                                            <?php echo e(substr($sikayet->olusturanKurulUyesi->name, 0, 1)); ?>

                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-800 group-hover:text-indigo-600 transition"><?php echo e($sikayet->olusturanKurulUyesi->name); ?></span>
                                    </a>
                                <?php else: ?>
                                    <div class="flex items-center mt-1 p-1 rounded -ml-1">
                                        <div class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold mr-2 flex-shrink-0">
                                            SM
                                        </div>
                                        <span class="text-sm font-medium text-gray-500 italic">Sistem (Misafir Şikayeti)</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div>
                                <span class="text-xs text-gray-400 block">Atanan Çözüm Takımı</span>
                                <?php if($sikayet->cozumTakimi): ?>
                                    <a href="<?php echo e(route('admin.cozum-takimlari.show', $sikayet->cozumTakimi->id)); ?>"
                                        class="text-sm font-medium text-indigo-700 font-bold block mt-1 hover:underline">
                                        <?php echo e($sikayet->cozumTakimi->ad); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-sm font-medium text-gray-500 italic block mt-1">Henüz Atanmadı</span>
                                <?php endif; ?>
                            </div>

                            <div>
                                <span class="text-xs text-gray-400 block">Şikayet Tarihi (Sistem)</span>
                                <span
                                    class="text-sm text-gray-600"><?php echo e($sikayet->created_at->format('d.m.Y H:i')); ?></span>
                            </div>
                        </div>
                    </div>

                    
                    <?php if($sikayet->musteri_feedback): ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">
                            <h3 class="font-bold text-gray-900 mb-4">Müşteri Geri Bildirimi</h3>
                            <?php
                                $feedbackColors = [
                                    'memnun' => 'bg-green-50 border-green-200 text-green-800',
                                    'kismen_memnun' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                                    'memnun_degil' => 'bg-red-50 border-red-200 text-red-800'
                                ];
                                $feedbackClass = $feedbackColors[$sikayet->musteri_feedback_durumu] ?? 'bg-gray-50 border-gray-200 text-gray-800';
                            ?>
                            <div class="p-4 rounded-lg border <?php echo e($feedbackClass); ?>">
                                <p class="text-sm font-medium italic mb-2">"<?php echo e($sikayet->musteri_feedback); ?>"</p>
                                <div class="flex justify-between items-center text-xs opacity-75">
                                    <span
                                        class="font-bold uppercase"><?php echo e(str_replace('_', ' ', $sikayet->musteri_feedback_durumu)); ?></span>
                                    <?php if($sikayet->musteri_puan): ?>
                                        <span>Puan: <?php echo e($sikayet->musteri_puan); ?>/5</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind("[data-fancybox]", {
            // Your custom options
        });
    </script>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.sikayet-musteri-atama-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-924569912-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.sikayetler.musteri-diger-sikayetler-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-924569912-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/sikayetler/show.blade.php ENDPATH**/ ?>