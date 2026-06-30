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
    <?php $__env->startPush('pageTitle'); ?><?php echo e($iaa->musteriSikayeti ? $iaa->musteriSikayeti->musteri_sikayet_konusu : $iaa->baslik); ?> | <?php $__env->stopPush(); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proje Çalışma Alanı: <span class="text-indigo-600"><?php echo e($iaa->baslik); ?></span>
            </h2>
            <div class="flex items-center space-x-4">
                <?php if(!$iaa->musteriSikayeti): ?>
                    <a href="<?php echo e(route('iaa.show', $iaa->id)); ?>"
                        class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors duration-200 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        İyileştirme Önerisine Git
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(url()->previous() == url()->current() ? route('dashboard') : url()->previous()); ?>"
                    class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors duration-200 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Geri Dön
                </a>
                
                
                <div class="flex items-center space-x-2 border-l pl-4 ml-4 border-gray-200">
                    <a href="<?php echo e(route('proje.export.pdf', $iaa->id)); ?>" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm" title="PDF Raporu İndir">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        PDF
                    </a>
                    <a href="<?php echo e(route('proje.export.excel', $iaa->id)); ?>" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm" title="Excel Raporu İndir">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Excel
                    </a>
                </div>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <?php if(session('success')): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl shadow-sm mb-6 flex items-start" role="alert">
                    <svg class="w-6 h-6 mr-3 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-bold">Başarılı İşlem</p>
                        <p class="text-sm mt-1"><?php echo e(session('success')); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($iaa->projeEkibi()->where('users.id', auth()->id())->where('iaa_user.durum', 'bekliyor')->exists()): ?>
                <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl shadow-lg mb-8 overflow-hidden transform transition-all hover:scale-[1.01] duration-300">
                    <div class="px-6 py-5 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-4 text-white">
                            <div class="p-3 bg-white/20 backdrop-blur-md rounded-xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight">Bu projeye davet edildiniz!</h3>
                                <p class="text-indigo-100 text-sm font-medium">Projeye tam erişim sağlamak ve görevleri yönetmek için daveti cevaplayın.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <form action="<?php echo e(route('iaa.davetYanitla', $iaa->id)); ?>" method="POST" class="flex-1 md:flex-none">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="yanit" value="kabul">
                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-white text-indigo-700 rounded-xl font-black text-sm shadow-xl hover:bg-indigo-50 transition-all active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    KABUL ET
                                </button>
                            </form>
                            <form action="<?php echo e(route('iaa.davetYanitla', $iaa->id)); ?>" method="POST" class="flex-1 md:flex-none">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="yanit" value="red">
                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-indigo-500/30 text-white border border-white/30 rounded-xl font-bold text-sm hover:bg-rose-500/40 hover:border-rose-300/50 transition-all active:scale-95">
                                    REDDET
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($iaa->musteriSikayeti && $iaa->musteriSikayeti->trashed()): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-red-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h3 class="text-red-800 font-bold text-lg">Bu proje durduruldu! Kaynak Şikayet Çöp Kutusunda!</h3>
                            <p class="text-red-700 text-sm mt-0.5">Bu projenin bağlı olduğu müşteri şikayeti silinmiş durumdadır. Bu nedenle projede yeni bir işlem yapılamaz, adımlar ilerletilemez ve proje tamamlanamaz. İşlem yapabilmek için kaynak şikayetin geri yüklenmesi gerekir.</p>
                        </div>
                    </div>
                    <?php
                        $isAuthorizedQM = auth()->user()->hasRole('Bölüm Kalite Yöneticisi') && ($isQualityManagerInterventionPower ?? false);
                    ?>
                    <?php if(auth()->user()->hasRole(['Superadmin', 'Super Admin', 'Yonetim', 'Yönetim']) || $isAuthorizedQM): ?>
                        <a href="<?php echo e(route('admin.sikayetler.show', $iaa->musteriSikayeti->id)); ?>" class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Şikayete Git
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            
            <?php if($iaa->musteriSikayeti && $iaa->musteriSikayeti->customer): ?>
                <div class="bg-white rounded-2xl shadow-sm border-l-8 border-indigo-500 overflow-hidden mb-6 p-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-50 rounded-xl">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">İlgili Müşteri</span>
                            <h2 class="text-lg font-black text-gray-800 uppercase">
                                <a href="<?php echo e(route('musteri.profil.show', $iaa->musteriSikayeti->customer_id)); ?>" class="hover:text-indigo-600 transition-colors">
                                    <?php echo e($iaa->musteriSikayeti->customer->name); ?>

                                </a>
                            </h2>
                        </div>
                    </div>
                    <div class="hidden md:block text-right">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Şikayet Konusu</span>
                        <p class="text-sm font-bold text-gray-700 italic">"<?php echo e($iaa->musteriSikayeti->musteri_sikayet_konusu); ?>"</p>
                        
                        
                        <div class="mt-2">
                            <?php echo $__env->make('admin.sikayet-hatirlatma.partials._hatirlatma-butonu', ['sikayet' => $iaa->musteriSikayeti], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._project-header', [
                'iaa' => $iaa,
                'takim' => $takim,
                'assignment' => $assignment,
                'progressPercentage' => $progressPercentage,
                'completedStepsCount' => $completedStepsCount,
                'totalStepsCount' => $totalStepsCount,
                'statusDate' => $statusDate ?? null
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>



            <?php
                $isTrashed = $iaa->musteriSikayeti && $iaa->musteriSikayeti->trashed();
            ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._project-tabs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._complaint-details', ['iaa' => $iaa], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._extension-request', [
                'iaa' => $iaa, 
                'isLeaderOrAdmin' => auth()->check() && (($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || auth()->user()->hasRole('Superadmin'))
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('proje-calisma-alani.partials._timeline', [
                'steps' => $steps,
                'completedStepIds' => $completedStepIds,
                'progressUpdates' => $progressUpdates,
                'isTeamMember' => $isTeamMember,
                'iaa' => $iaa,
                'assignment' => $assignment,
                'takim' => $takim,
                'stepAssignments' => $stepAssignments ?? [],
                'canEdit' => $isTrashed ? false : $canEdit,
                'statusDate' => $statusDate ?? null
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

           
            
            
            
            <?php
                // Durum Grupları
                $duzenlemeDurumlari = ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor'];
                $onayVeBitisDurumlari = ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Tamamlandı', 'Talep Olarak Kapatıldı', 'Revize Ediliyor', 'Tamamlanması Reddedildi'];

                // Kontroller
                $formGoster = !$isTrashed && in_array($iaa->durum, $duzenlemeDurumlari); // Sadece bu durumlarda form açılır
                $kartGoster = in_array($iaa->durum, $onayVeBitisDurumlari); // Sadece bu durumlarda kart açılır
                $showCompletionForm = $progressPercentage == 100 && in_array($iaa->durum, $duzenlemeDurumlari);

                // İade verisi var mı?
                $iadeVar = $iaa->musteriSikayeti && $iaa->musteriSikayeti->iadeler->isNotEmpty();

                // Yetki
                $isLeaderOrAdmin = false;
                if (auth()->check()) {
                    $isLeaderOrAdmin = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || auth()->user()->hasRole('Superadmin');
                }
            ?>

            
            <?php if(!$showCompletionForm || auth()->user()->hasRole('Müşteri Saha Temsilcisi')): ?>
                <?php if($iadeVar && $kartGoster): ?>
                    <?php echo $__env->make('proje-calisma-alani.partials._return-details-card', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>

                <?php if(isset($iaa->visit_planned) && $iaa->visit_planned): ?>
                    <?php echo $__env->make('proje-calisma-alani.partials._visit-details-card', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php if($kartGoster): ?>
                <?php echo $__env->make('proje-calisma-alani.partials._project-final-status', ['iaa' => $iaa, 'statusDate' => $statusDate ?? null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>

            
            <?php if($showCompletionForm && !auth()->user()->hasRole('Müşteri Saha Temsilcisi')): ?>
                <div id="ziyaret-bilgileri-alani"></div>
                <?php echo $__env->make('proje-calisma-alani.partials._project-completion', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>

            
            
            <?php if(!$isTrashed && in_array($iaa->durum, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Revize Ediliyor', 'Tamamlanması Reddedildi', 'Tamamlandı']) && !auth()->user()->hasRole('Müşteri Saha Temsilcisi')): ?>
                <?php echo $__env->make('proje-calisma-alani.partials._action-buttons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>

            
            <?php
                $user = Auth::user();
                $isAdminOrManagement = $user->hasRole(['Superadmin', 'Yonetim']);
                $isDirector = $user->hasRole('Direktör');
                $isTeamLeader = $iaa->projeEkibi()
                    ->where('user_id', $user->id)
                    ->where('iaa_user.rol', 'Lider')
                    ->exists();
                
                $canSeeHistory = $isAdminOrManagement || $isDirector || $isTeamLeader || ($isQualityManagerInterventionPower ?? false);
            ?>

            <?php if(Auth::check() && Auth::user()->is_personnel == 1 && $canSeeHistory): ?>
                <?php echo $__env->make('proje-calisma-alani.partials._logs', [
                    'sonOnLoglar' => $sonOnLoglar,
                    'tumProjeLoglari' => $tumProjeLoglari
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>

            
            <div class="mt-8 mb-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md relative">
                <div class="bg-gradient-to-r from-slate-100 via-white to-white px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between md:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <div class="p-2 bg-slate-200 text-slate-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </div>
                            Proje Çıktıları ve Raporlama
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 pl-11">Projenin tüm loglarını ve verilerini farklı formatlarda indirebilirsiniz.</p>
                    </div>
                </div>
                
                <div class="p-6 md:p-8 flex flex-col sm:flex-row justify-center items-center gap-4 bg-slate-50">
                    <a href="<?php echo e(route('proje.export.pdf', $iaa->id)); ?>" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-red-600 border border-transparent rounded-xl font-bold text-sm text-white tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        PDF RAPORU İNDİR
                    </a>
                    <a href="<?php echo e(route('proje.export.excel', $iaa->id)); ?>" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-green-600 border border-transparent rounded-xl font-bold text-sm text-white tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        EXCEL VERİSİ İNDİR
                    </a>
                </div>
            </div>

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