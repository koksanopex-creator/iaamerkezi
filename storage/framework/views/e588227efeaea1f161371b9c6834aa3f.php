<?php $__env->startPush('pageTitle'); ?>
    Bölüm Paneli: <?php echo e($bolum->ad); ?>

<?php $__env->stopPush(); ?>

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
    <style>
        :root {
            --color-background-primary: #ffffff;
            --color-background-secondary: #f8fafc;
            --color-background-tertiary: #f1f5f9;
            --color-border-primary: #e2e8f0;
            --color-border-secondary: #cbd5e1;
            --color-text-primary: #0f172a;
            --color-text-secondary: #475569;
            --color-text-tertiary: #94a3b8;
        }

        .dashboard-container { padding: 1.5rem; max-width: 1400px; margin: 0 auto; }
        
        /* HERO KART TASARIMI */
        .hero-premium {
            background: var(--color-background-primary);
            border-radius: 24px;
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
            border: 1px solid var(--color-border-primary);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .hero-banner { height: 10px; background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%); }
        .hero-content { padding: 2rem; display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; }
        .dept-avatar {
            width: 100px; height: 100px; border-radius: 20px;
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.1);
            border: 4px solid #fff; object-fit: cover;
        }
        .dept-placeholder {
            width: 100px; height: 100px; border-radius: 20px;
            background: #eef2ff; color: #4f46e5;
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; font-weight: 800; border: 4px solid #fff;
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.1);
        }

        /* SEKME BUTONLARI */
        .main-tabs {
            display: flex; gap: 8px; margin-bottom: 2rem;
            padding: 6px; background: #e2e8f0; border-radius: 16px;
            width: 100%;
        }
        .main-tab-btn {
            flex: 1;
            padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: all 0.2s; border: none;
            display: flex; align-items: center; justify-content: center; gap: 8px; color: var(--color-text-secondary);
        }
        .main-tab-btn.active {
            background: #fff; color: #4f46e5;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        /* İSTATİSTİK BAR */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            border-top: 1px solid var(--color-border-primary);
            background: #fff;
        }
        .stat-card {
            padding: 1.5rem; text-align: center; border-right: 1px solid var(--color-border-primary);
            transition: background 0.2s;
        }
        .stat-card:last-child { border-right: none; }
        .stat-card:hover { background: #f8fafc; }
        .stat-val { font-size: 28px; font-weight: 800; display: block; line-height: 1; margin-bottom: 4px; }
        .stat-tit { font-size: 11px; font-weight: 700; color: var(--color-text-tertiary); text-transform: uppercase; letter-spacing: 0.05em; }

        /* KARTLAR */
        .premium-card {
            background: #fff; border-radius: 20px; border: 1px solid var(--color-border-primary);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02); overflow: hidden;
        }
        .p-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border-primary); display: flex; justify-content: space-between; align-items: center; }
        .p-card-title { font-size: 16px; font-weight: 700; color: var(--color-text-primary); display: flex; align-items: center; gap: 10px; }
        
        /* TABLO TASARIMI */
        .p-table { width: 100%; border-collapse: collapse; }
        .p-table th { background: #f8fafc; padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--color-text-tertiary); text-transform: uppercase; }
        .p-table td { padding: 14px 16px; border-bottom: 1px solid var(--color-border-primary); font-size: 13px; }
        .p-table tr:hover { background: #f1f5f9; }

        /* ETİKETLER */
        .badge-p { padding: 4px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .badge-p-blue { background: #eff6ff; color: #1e40af; }
        .badge-p-green { background: #ecfdf5; color: #065f46; }
        .badge-p-amber { background: #fffbeb; color: #92400e; }
        .badge-p-purple { background: #f5f3ff; color: #5b21b6; }

        /* HOVER CARD */
        .hover-card {
            position: fixed;
            z-index: 200;
            width: 320px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 28px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
            padding: 24px;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [x-cloak] { display: none !important; }
    </style>

    <div class="dashboard-container" x-data="{ 
        mainTab: localStorage.getItem('activeTab') || 'projeler',
        personelTab: 'beyaz-yaka',
        userSearch: '',
        machineSearch: '',
        addModalOpen: false,
        editModalOpen: false,
        selectedMachine: null,
        addHammaddeModal: false,
        editHammaddeModal: false,
        selectedHammadde: {},
        addVersiyonModal: false,
        editVersiyonModal: false,
        selectedVersiyon: {},
        hoveredUser: null
    }" 
    x-init="$watch('mainTab', value => localStorage.setItem('activeTab', value))"
    @mousemove="if(hoveredUser) { $refs.hoverCard.style.left = ($event.clientX + 20) + 'px'; $refs.hoverCard.style.top = ($event.clientY + 20) + 'px'; }">
        
        
        <div x-show="hoveredUser" x-ref="hoverCard" class="hover-card" x-cloak>
            <div class="flex items-center gap-4">
                <img :src="hoveredUser?.photo" class="w-16 h-16 rounded-2xl object-cover">
                <div>
                    <h4 class="font-bold text-gray-900" x-text="hoveredUser?.name"></h4>
                    <p class="text-xs text-indigo-600 font-semibold" x-text="hoveredUser?.unvan"></p>
                </div>
            </div>
            <div class="mt-4 space-y-2 text-sm text-gray-600">
                <p class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> <span x-text="hoveredUser?.email"></span></p>
                <p class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> <span x-text="hoveredUser?.phone"></span></p>
            </div>
        </div>

        
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-semibold text-gray-400 mb-2">
                    <a href="<?php echo e(route('admin.bolumler.index')); ?>" class="hover:text-indigo-600 transition uppercase tracking-wider">Bölümler</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    <span class="text-indigo-600 uppercase tracking-wider"><?php echo e($bolum->ad); ?></span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight"><?php echo e($bolum->ad); ?> <span class="text-gray-300 font-light">PANELİ</span></h1>
            </div>
            
            <?php if(Auth::user()->hasAnyRole(['Superadmin', 'Bölüm Lideri']) && !($isReadOnly ?? false)): ?>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('admin.bolum-yonetim.index', ['bolum_id' => $bolum->id])); ?>" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white hover:bg-indigo-700 transition-all gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Yetki Matrisini Yönet
                    </a>
                    <?php if(Auth::user()->hasRole('Superadmin')): ?>
                    <a href="<?php echo e(route('admin.bolumler.edit', $bolum)); ?>" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 rounded-xl shadow-sm text-sm font-bold text-gray-700 hover:text-indigo-600 hover:border-indigo-200 transition-all gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Ayarları Düzenle
                    </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        
        <?php if(session('success')): ?>
        <div class="mb-8 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-xl shadow-sm flex items-center gap-3 animate-fade-in-down">
            <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-emerald-900"><?php echo e(session('success')); ?></p>
            </div>
        </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
        <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl shadow-sm flex items-center gap-3 animate-fade-in-down">
            <div class="flex-shrink-0 w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-red-900"><?php echo e(session('error')); ?></p>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="hero-premium">
            <div class="hero-banner"></div>
            <div class="hero-content">
                <?php if($bolum->logo_yolu): ?>
                    <img src="<?php echo e(asset('storage/' . $bolum->logo_yolu)); ?>" class="dept-avatar">
                <?php else: ?>
                    <div class="dept-placeholder"><?php echo e(substr($bolum->ad, 0, 1)); ?></div>
                <?php endif; ?>
                
                <div class="flex-grow">
                    <div class="flex items-center gap-3 mb-3">
                        <h2 class="text-2xl font-extrabold text-gray-900"><?php echo e($bolum->ad); ?></h2>
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 border border-green-100">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                            </span>
                            <span class="text-[10px] font-bold text-green-700 uppercase tracking-tight">AKTİF</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <span class="badge-p badge-p-blue uppercase"><?php echo e($bolum->kategori?->ad ?? 'Genel'); ?></span>
                        
                        
                        <?php if($bolum->director): ?>
                            <a href="<?php echo e(route('profile.show', $bolum->director->id)); ?>" 
                               @mouseenter="hoveredUser = { 
                                   name: '<?php echo e($bolum->director->name); ?>', 
                                   email: '<?php echo e($bolum->director->email); ?>', 
                                   phone: '<?php echo e($bolum->director->telefon ?? 'Belirtilmemiş'); ?>', 
                                   unvan: '<?php echo e($bolum->director->unvan ?? 'Direktör'); ?>',
                                   photo: '<?php echo e($bolum->director->profile_photo_url); ?>'
                               }" 
                               @mouseleave="hoveredUser = null"
                               class="inline-flex items-center gap-2 group relative">
                                <div class="relative">
                                    <?php if($bolum->director->profile_photo_path): ?>
                                        <img src="<?php echo e(asset('storage/' . $bolum->director->profile_photo_path)); ?>" class="w-8 h-8 rounded-full border-2 border-white shadow-sm ring-1 ring-purple-200">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold border-2 border-white shadow-sm ring-1 ring-purple-200">
                                            <?php echo e(substr($bolum->director->name, 0, 1)); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase leading-none mb-1">Direktör</span>
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-purple-600 transition"><?php echo e($bolum->director->name); ?></span>
                                </div>
                            </a>
                        <?php endif; ?>

                        
                        <?php $deptLeader = $users->first(fn($u) => $u->hasRole('Bölüm Lideri')); ?>
                        <?php if($deptLeader): ?>
                            <a href="<?php echo e(route('profile.show', $deptLeader->id)); ?>" 
                               @mouseenter="hoveredUser = { 
                                   name: '<?php echo e($deptLeader->name); ?>', 
                                   email: '<?php echo e($deptLeader->email); ?>', 
                                   phone: '<?php echo e($deptLeader->telefon ?? 'Belirtilmemiş'); ?>', 
                                   unvan: '<?php echo e($deptLeader->unvan ?? 'Bölüm Lideri'); ?>',
                                   photo: '<?php echo e($deptLeader->profile_photo_url); ?>'
                               }" 
                               @mouseleave="hoveredUser = null"
                               class="inline-flex items-center gap-2 group relative">
                                <div class="relative">
                                    <?php if($deptLeader->profile_photo_path): ?>
                                        <img src="<?php echo e(asset('storage/' . $deptLeader->profile_photo_path)); ?>" class="w-8 h-8 rounded-full border-2 border-white shadow-sm ring-1 ring-amber-200">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 text-xs font-bold border-2 border-white shadow-sm ring-1 ring-amber-200">
                                            <?php echo e(substr($deptLeader->name, 0, 1)); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase leading-none mb-1">Müdür</span>
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-amber-600 transition"><?php echo e($deptLeader->name); ?></span>
                                </div>
                            </a>
                        <?php endif; ?>

                        
                        <?php $__currentLoopData = $bolum->yardimcilar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yardimci): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('profile.show', $yardimci->id)); ?>" 
                               @mouseenter="hoveredUser = { 
                                   name: '<?php echo e($yardimci->name); ?>', 
                                   email: '<?php echo e($yardimci->email); ?>', 
                                   phone: '<?php echo e($yardimci->telefon ?? 'Belirtilmemiş'); ?>', 
                                   unvan: '<?php echo e($yardimci->unvan ?? 'Bölüm Lider Yardımcısı'); ?>',
                                   photo: '<?php echo e($yardimci->profile_photo_url); ?>'
                               }" 
                               @mouseleave="hoveredUser = null"
                               class="inline-flex items-center gap-2 group relative">
                                <div class="relative">
                                    <?php if($yardimci->profile_photo_path): ?>
                                        <img src="<?php echo e(asset('storage/' . $yardimci->profile_photo_path)); ?>" class="w-8 h-8 rounded-full border-2 border-white shadow-sm ring-1 ring-emerald-200">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-xs font-bold border-2 border-white shadow-sm ring-1 ring-emerald-200">
                                            <?php echo e(substr($yardimci->name, 0, 1)); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase leading-none mb-1">Yardımcı</span>
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-emerald-600 transition"><?php echo e($yardimci->name); ?></span>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <?php
                $user = Auth::user();
                $canSeeStats = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']) || $user->hasBolumAuthority('bolum.dashboard.ozet');
                $canSeeSikayet = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']) || $user->hasBolumAuthority('bolum.sikayet.gor');
                $canSeeIaa = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']) || $user->hasBolumAuthority('bolum.iaa.gor');
                $canSeeDisiplin = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']) || $user->hasBolumAuthority('bolum.disiplin.gor');
                $canSeeMakine = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']) || $user->hasBolumAuthority('bolum.makine.yonet');
                $canSeeIade = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']) || $user->hasBolumAuthority('bolum.iade.gor');
                $canSeeHammadde = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']) || $user->hasBolumAuthority('bolum.hammadde.yonet');
            ?>

            <?php if($canSeeStats): ?>
            <div class="stats-grid">
                <?php if($bolum->sikayet_kategorileri_count > 0 && $canSeeSikayet): ?>
                <div class="stat-card">
                    <span class="stat-val text-red-600"><?php echo e($bolum->sikayetler_count); ?></span>
                    <span class="stat-tit">Şikayet</span>
                </div>
                <?php endif; ?>
                
                <?php if($canSeeIaa): ?>
                <div class="stat-card">
                    <span class="stat-val text-indigo-600"><?php echo e($iaa_count); ?></span>
                    <span class="stat-tit">İAA Projesi</span>
                </div>
                <?php endif; ?>

                <?php if($canSeeDisiplin): ?>
                <div class="stat-card">
                    <span class="stat-val text-orange-600"><?php echo e($disiplin_count); ?></span>
                    <span class="stat-tit">Disiplin</span>
                </div>
                <?php endif; ?>

                <div class="stat-card">
                    <span class="stat-val text-indigo-600"><?php echo e($beyazYakaCount); ?></span>
                    <span class="stat-tit">Beyaz Yaka</span>
                </div>
                <div class="stat-card">
                    <span class="stat-val text-blue-500"><?php echo e($maviYakaCount); ?></span>
                    <span class="stat-tit">Mavi Yaka</span>
                </div>

                <?php if($bolum->has_machines && $canSeeMakine): ?>
                <div class="stat-card">
                    <span class="stat-val text-emerald-600"><?php echo e($machines->count()); ?></span>
                    <span class="stat-tit">Makine</span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            
        <div class="main-tabs">
            <?php if($canSeeIaa || $canSeeSikayet): ?>
            <button @click="mainTab = 'projeler'" :class="mainTab === 'projeler' ? 'active' : ''" class="main-tab-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Projeler & Süreçler
            </button>
            <?php endif; ?>

            <?php if($canSeeIade): ?>
            <button @click="mainTab = 'iadeler'" :class="mainTab === 'iadeler' ? 'active' : ''" class="main-tab-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m16 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                İadeler
            </button>
            <?php endif; ?>

            <button @click="mainTab = 'personel'" :class="mainTab === 'personel' ? 'active' : ''" class="main-tab-btn relative">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Personel İşlemleri
                <?php if(($pendingUsersCount ?? 0) > 0 && (Auth::user()->hasRole('Bölüm Lideri') || Auth::user()->hasRole('Superadmin'))): ?>
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse shadow-sm border border-white"></span>
                <?php endif; ?>
            </button>

            <?php if($canSeeMakine || $canSeeHammadde): ?>
            <button @click="mainTab = 'teknik'" :class="mainTab === 'teknik' ? 'active' : ''" class="main-tab-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Teknik & Üretim
            </button>
            <?php endif; ?>

            <?php if($user->hasAnyRole(['Superadmin', 'Yonetim'])): ?>
            <button @click="mainTab = 'gecmis'" :class="mainTab === 'gecmis' ? 'active' : ''" class="main-tab-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                İşlem Geçmişi
            </button>
            <?php endif; ?>
        </div>

        
        <div>
            
            <?php if($canSeeIaa || $canSeeSikayet): ?>
            <div x-show="mainTab === 'projeler'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8" x-cloak>
                
                
                <?php if($canSeeIaa): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.bolum-iaa-listesi', ['bolumId' => $bolum->id]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3291019286-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php endif; ?>

                <?php if($bolum->sikayet_kategorileri_count > 0 && $canSeeSikayet): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.bolum-sikayet-listesi', ['bolumId' => $bolum->id]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3291019286-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            
            <?php if($canSeeIade): ?>
            <div x-show="mainTab === 'iadeler'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8" x-cloak>
                <div class="premium-card">
                    <div class="p-card-header bg-red-50/30">
                        <h3 class="p-card-title text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m16 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Bölüm İade Kayıtları
                        </h3>
                    </div>
                    <?php echo $__env->make('dashboard.partials.iadeler-tablosu', [
                        'iadeVerileri' => $iadeVerileri,
                        'hideHeader' => true
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
            <?php endif; ?>

            
            <div x-show="mainTab === 'personel'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-1">
                        <div class="premium-card p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="p-card-title">Personel Listesi (<?php echo e($toplamPersonelCount); ?>)</h3>
                                <div class="flex gap-1 bg-gray-100 p-1 rounded-lg">
                                    <button @click="personelTab = 'beyaz-yaka'" :class="personelTab === 'beyaz-yaka' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 rounded-md text-[10px] font-bold transition">BEYAZ YAKA (<?php echo e($beyazYakaCount); ?>)</button>
                                    <button @click="personelTab = 'mavi-yaka'" :class="personelTab === 'mavi-yaka' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 rounded-md text-[10px] font-bold transition">MAVİ YAKA (<?php echo e($maviYakaCount); ?>)</button>
                                </div>
                            </div>

                            <?php if(($pendingUsersCount ?? 0) > 0 && (Auth::user()->hasRole('Bölüm Lideri') || Auth::user()->hasRole('Superadmin'))): ?>
                            <div class="mb-4">
                                <a href="<?php echo e(route('admin.users.onay_bekleyenler')); ?>" class="w-full flex items-center justify-between bg-red-50 border border-red-100 px-4 py-3 rounded-xl hover:bg-red-100 transition shadow-sm group">
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                        </div>
                                        <div class="flex flex-col text-left">
                                            <span class="text-xs font-bold text-red-900">Onay Bekleyenler</span>
                                            <span class="text-[10px] text-red-700">İşlem bekleyen başvurularınız var</span>
                                        </div>
                                    </div>
                                    <span class="bg-red-500 text-white text-xs font-black px-2.5 py-1 rounded-lg group-hover:scale-110 transition-transform"><?php echo e($pendingUsersCount); ?></span>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if(!($isReadOnly ?? false) || $user->hasRole('Bölüm Lideri')): ?>
                            <div x-show="personelTab === 'mavi-yaka'" class="mb-4">
                                <a href="<?php echo e(route('admin.mavi-yaka.create')); ?>" class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                                    Yeni Personel Ekle
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-4 relative">
                                <input type="text" x-model="userSearch" placeholder="İsim ile ara..." class="w-full pl-9 pr-4 py-2 border-gray-200 rounded-xl text-sm focus:ring-indigo-500">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>

                            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                                <template x-if="personelTab === 'beyaz-yaka'">
                                    <div class="space-y-2">
                                        <?php $__currentLoopData = $users->where('is_mavi_yaka', false); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div x-show="userSearch === '' || '<?php echo e(strtolower($u->name)); ?>'.includes(userSearch.toLowerCase())" 
                                                 class="flex items-center gap-3 p-3 <?php echo e($u->hasRole('Bölüm Lideri') ? 'bg-indigo-50 border border-indigo-100' : 'hover:bg-gray-50'); ?> rounded-xl transition">
                                                <div class="relative">
                                                    <img src="<?php echo e($u->profile_photo_url); ?>" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                                    <?php if($u->hasRole('Bölüm Lideri')): ?>
                                                        <div class="absolute -top-1 -right-1 bg-yellow-400 text-white rounded-full p-0.5 shadow-sm border border-white">
                                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v11l-5-2.5L5 16V5z"/></svg>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex flex-col flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <a href="<?php echo e(route('profile.show', $u->id)); ?>" class="text-sm font-bold text-gray-900 hover:text-indigo-600 truncate"><?php echo e($u->name); ?></a>
                                                        <?php if($u->hasRole('Bölüm Lideri')): ?>
                                                            <span class="bg-yellow-100 text-yellow-700 text-[8px] px-1.5 py-0.5 rounded font-black uppercase tracking-tighter">LİDER</span>
                                                        <?php elseif($u->hasRole('Bölüm Lider Yardımcısı')): ?>
                                                            <span class="bg-emerald-100 text-emerald-700 text-[8px] px-1.5 py-0.5 rounded font-black uppercase tracking-tighter">YARDIMCI</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="text-[10px] text-gray-400 font-bold uppercase"><?php echo e($u->unvan ?? 'Personel'); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </template>
                                <template x-if="personelTab === 'mavi-yaka'">
                                    <div class="space-y-2">
                                            <?php $__currentLoopData = $users->where('is_mavi_yaka', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div x-show="userSearch === '' || '<?php echo e(strtolower($u->name)); ?>'.includes(userSearch.toLowerCase())" class="flex items-center gap-3 p-2 hover:bg-blue-50 rounded-xl transition">
                                                    <img src="<?php echo e($u->profile_photo_url); ?>" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                                    <div class="flex flex-col">
                                                        <a href="<?php echo e(route('profile.show', $u->id)); ?>" class="text-sm font-bold text-gray-900 hover:text-blue-600 truncate"><?php echo e($u->name); ?></a>
                                                        <span class="text-[10px] text-blue-400 font-bold uppercase">Sicil: <?php echo e($u->sicil_no ?? '-'); ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    
                    <?php if($canSeeDisiplin): ?>
                    <div class="lg:col-span-2">
                        <div class="premium-card">
                            <div class="p-card-header">
                                <h3 class="p-card-title">Disiplin Dosyaları</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="p-table">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Tarih</th>
                                            <th class="text-left">Personel</th>
                                            <th class="text-left">İhlal</th>
                                            <th class="text-left">Durum</th>
                                            <th class="text-right pr-6">İŞLEMLER</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $disiplinDosyalari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($dosya->olay_tarihi ? $dosya->olay_tarihi->format('d.m.Y') : '-'); ?></td>
                                            <td><span class="font-bold"><?php echo e($dosya->user->name ?? 'Bilinmiyor'); ?></span></td>
                                            <td><div class="text-xs truncate max-w-[200px]"><?php echo e($dosya->behavior->name ?? '-'); ?></div></td>
                                            <td><span class="badge-p badge-p-amber"><?php echo e($dosya->durum); ?></span></td>
                                            <td class="text-right">
                                                <a href="<?php echo e(route('admin.disiplin.show', $dosya->id)); ?>" class="inline-flex items-center px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-100 hover:bg-amber-600 hover:text-white transition-all shadow-sm">
                                                    Görüntüle
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="5" class="text-center py-10 text-gray-400 italic">Disiplin dosyası bulunamadı.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if($canSeeMakine || $canSeeHammadde): ?>
            <div x-show="mainTab === 'teknik'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8" x-cloak>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    
                    <?php if($canSeeMakine): ?>
                    <div class="lg:col-span-2">
                        <div class="premium-card">
                            <div class="p-card-header">
                                <h3 class="p-card-title">Makine Envanteri (<?php echo e($machines->count()); ?>)</h3>
                                <?php if(!$isReadOnly || $user->hasBolumAuthority('bolum.makine.yonet')): ?>
                                <button @click="addModalOpen = true" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 transition">Makine Ekle</button>
                                <?php endif; ?>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="p-table">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Makine Adı</th>
                                            <th class="text-left">Durum</th>
                                            <th class="text-left">Kurulum</th>
                                            <th class="text-right pr-6">İŞLEMLER</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="font-bold text-gray-900"><?php echo e($machine->name); ?></td>
                                            <td>
                                                <span class="badge-p <?php echo e($machine->status == 'active' ? 'badge-p-green' : 'badge-p-amber'); ?>">
                                                    <?php echo e($machine->status == 'active' ? 'AKTİF' : 'BAKIM/ARIZA'); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($machine->installation_date ? \Carbon\Carbon::parse($machine->installation_date)->format('d.m.Y') : '-'); ?></td>
                                            <td class="text-right">
                                                <div class="flex justify-end items-center gap-2">
                                                    <?php if(!$isReadOnly || $user->hasBolumAuthority('bolum.makine.yonet')): ?>
                                                    <button @click="selectedMachine = <?php echo e($machine); ?>; editModalOpen = true" 
                                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-black hover:bg-indigo-600 hover:text-white hover:shadow-md transition-all border border-indigo-100 uppercase tracking-tighter">
                                                        DÜZENLE
                                                    </button>
                                                    <?php if($user->hasRole('Superadmin')): ?>
                                                    <form id="delete-machine-<?php echo e($machine->id); ?>" action="<?php echo e(route('admin.machines.destroy', $machine->id)); ?>" method="POST" class="inline">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        <button type="button" @click="if(confirm('Makineyi silmek istediğinize emin misiniz?')) $el.closest('form').submit()" 
                                                                class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-[10px] font-black hover:bg-red-600 hover:text-white hover:shadow-md transition-all border border-red-100 uppercase tracking-tighter">
                                                            SİL
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-[10px] text-gray-400 font-bold uppercase">Sadece Görüntüleme</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="4" class="text-center py-10 text-gray-400 italic">Kayıtlı makine bulunamadı.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($canSeeHammadde): ?>
                    <div class="premium-card">
                        <div class="p-card-header">
                            <h3 class="p-card-title flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Hammaddeler (<?php echo e($hammaddeler->count()); ?>)
                            </h3>
                            <?php if(!$isReadOnly || $user->hasBolumAuthority('bolum.hammadde.yonet')): ?>
                            <button @click="addHammaddeModal = true" class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-black hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100 uppercase tracking-tighter cursor-pointer">+ EKLE</button>
                            <?php endif; ?>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="p-table">
                                <thead>
                                    <tr>
                                        <th class="text-left">Hammadde Adı</th>
                                        <th class="text-right pr-6">İŞLEMLER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $hammaddeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hammadde): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="font-bold text-gray-900"><?php echo e($hammadde->ad); ?></td>
                                        <td class="text-right">
                                            <div class="flex justify-end items-center gap-2">
                                                <?php if(!$isReadOnly || $user->hasBolumAuthority('bolum.hammadde.yonet')): ?>
                                                <button @click="selectedHammadde = <?php echo e($hammadde); ?>; editHammaddeModal = true" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-black hover:bg-indigo-600 hover:text-white hover:shadow-md transition-all border border-indigo-100 uppercase tracking-tighter">
                                                    DÜZENLE
                                                </button>
                                                <form action="<?php echo e(route('admin.bolumler.hammaddeler.delete', $hammadde->id)); ?>" method="POST" class="inline">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="button" @click="if(confirm('Hammaddeyi silmek istediğinize emin misiniz?')) $el.closest('form').submit()" 
                                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-[10px] font-black hover:bg-red-600 hover:text-white hover:shadow-md transition-all border border-red-100 uppercase tracking-tighter">
                                                        SİL
                                                    </button>
                                                </form>
                                                <?php else: ?>
                                                    <span class="text-[10px] text-gray-400 font-bold uppercase">Sadece Görüntüleme</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="2" class="text-center py-10 text-gray-400 italic">Hammadde tanımlanmamış.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    
                    <div class="premium-card">
                        <div class="p-card-header">
                            <h3 class="p-card-title flex items-center gap-2">
                                <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Ürün Versiyonları (<?php echo e($versiyonlar->count()); ?>)
                            </h3>
                            <?php if(!$isReadOnly || $user->hasBolumAuthority('bolum.hammadde.yonet')): ?>
                            <button @click="addVersiyonModal = true" class="bg-cyan-50 text-cyan-700 px-3 py-1.5 rounded-lg text-xs font-black hover:bg-cyan-600 hover:text-white transition-all shadow-sm border border-cyan-100 uppercase tracking-tighter cursor-pointer">+ EKLE</button>
                            <?php endif; ?>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="p-table">
                                <thead>
                                    <tr>
                                        <th class="text-left">Versiyon Adı</th>
                                        <th class="text-right pr-6">İŞLEMLER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $versiyonlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $versiyon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="font-bold text-gray-900"><?php echo e($versiyon->ad); ?></td>
                                        <td class="text-right">
                                            <div class="flex justify-end items-center gap-2">
                                                <?php if(!$isReadOnly || $user->hasBolumAuthority('bolum.hammadde.yonet')): ?>
                                                <button @click="selectedVersiyon = <?php echo e($versiyon); ?>; editVersiyonModal = true" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-black hover:bg-indigo-600 hover:text-white hover:shadow-md transition-all border border-indigo-100 uppercase tracking-tighter">
                                                    DÜZENLE
                                                </button>
                                                <form action="<?php echo e(route('admin.bolumler.versiyonlar.delete', $versiyon->id)); ?>" method="POST" class="inline">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="button" @click="if(confirm('Versiyonu silmek istediğinize emin misiniz?')) $el.closest('form').submit()" 
                                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-[10px] font-black hover:bg-red-600 hover:text-white hover:shadow-md transition-all border border-red-100 uppercase tracking-tighter">
                                                        SİL
                                                    </button>
                                                </form>
                                                <?php else: ?>
                                                    <span class="text-[10px] text-gray-400 font-bold uppercase">Sadece Görüntüleme</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="2" class="text-center py-10 text-gray-400 italic">Versiyon tanımlanmamış.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if($user->hasAnyRole(['Superadmin', 'Yonetim'])): ?>
            
            <div x-show="mainTab === 'gecmis'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8" x-cloak>
                <div class="premium-card">
                    <div class="p-card-header">
                        <h3 class="p-card-title">Bölüm İşlem Geçmişi</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="p-table">
                            <thead>
                                <tr>
                                    <th class="text-left">Zaman</th>
                                    <th class="text-left">Kullanıcı</th>
                                    <th class="text-left">Modül</th>
                                    <th class="text-left">İşlem</th>
                                    <th class="text-right pr-6">İŞLEMLER</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $machineLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="text-gray-500 text-[10px]"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></td>
                                    <td><span class="font-bold"><?php echo e($log->user->name ?? 'Sistem'); ?></span></td>
                                    <td><span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded font-bold uppercase text-gray-500">MAKİNE</span></td>
                                    <td>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                            <?php echo e($log->action == 'Ekleme' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); ?>">
                                            <?php echo e($log->action); ?>

                                        </span>
                                    </td>
                                    <td class="text-xs text-gray-500"><?php echo e($log->machine->name ?? 'Silinmiş Ünite'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="text-center py-10 text-gray-400 italic">Henüz bir işlem kaydı bulunmamaktadır.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div> 



    
    
    
    <div x-show="addModalOpen || editModalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="addModalOpen = false; editModalOpen = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form :action="editModalOpen ? '<?php echo e(url('admin/machines')); ?>/' + selectedMachine?.id : '<?php echo e(route('admin.bolumler.machines.store', $bolum->id)); ?>'" method="POST">
                    <?php echo csrf_field(); ?>
                    <template x-if="editModalOpen"><input type="hidden" name="_method" value="PUT"></template>
                    
                    <div class="bg-white p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900" x-text="editModalOpen ? 'Makine Düzenle' : 'Yeni Makine Ekle'"></h3>
                            <button type="button" @click="addModalOpen = false; editModalOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Makine Adı</label>
                                <input type="text" name="name" x-model="selectedMachine.name" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kurulum Tarihi</label>
                                    <input type="date" name="installation_date" x-model="selectedMachine.installation_date" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Durum</label>
                                    <select name="status" x-model="selectedMachine.status" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition">
                                        <option value="active">Aktif</option>
                                        <option value="maintenance">Bakımda</option>
                                        <option value="broken">Arızalı</option>
                                        <option value="inactive">Pasif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-6 flex justify-end gap-3">
                        <button type="button" @click="addModalOpen = false; editModalOpen = false" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-200 transition">İptal</button>
                        <button type="submit" class="px-8 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition" x-text="editModalOpen ? 'Güncelle' : 'Kaydet'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div x-show="addHammaddeModal || editHammaddeModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="addHammaddeModal = false; editHammaddeModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                <form :action="editHammaddeModal ? '<?php echo e(url('admin/hammaddeler')); ?>/' + selectedHammadde?.id : '<?php echo e(route('admin.bolumler.hammaddeler.store', $bolum->id)); ?>'" method="POST">
                    <?php echo csrf_field(); ?>
                    <template x-if="editHammaddeModal"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6" x-text="editHammaddeModal ? 'Hammadde Düzenle' : 'Yeni Hammadde'"></h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Hammadde Adı</label>
                                <input type="text" name="ad" x-model="selectedHammadde.ad" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 transition" required>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-6 flex justify-end gap-3">
                        <button type="button" @click="addHammaddeModal = false; editHammaddeModal = false" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-200 transition">İptal</button>
                        <button type="submit" class="px-8 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition">Onayla</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div x-show="addVersiyonModal || editVersiyonModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="addVersiyonModal = false; editVersiyonModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                <form :action="editVersiyonModal ? '<?php echo e(url('admin/versiyonlar')); ?>/' + selectedVersiyon?.id : '<?php echo e(route('admin.bolumler.versiyonlar.store', $bolum->id)); ?>'" method="POST">
                    <?php echo csrf_field(); ?>
                    <template x-if="editVersiyonModal"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6" x-text="editVersiyonModal ? 'Versiyon Düzenle' : 'Yeni Versiyon'"></h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Versiyon Adı</label>
                                <input type="text" name="ad" x-model="selectedVersiyon.ad" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 transition" required>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-6 flex justify-end gap-3">
                        <button type="button" @click="addVersiyonModal = false; editVersiyonModal = false" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-200 transition">İptal</button>
                        <button type="submit" class="px-8 py-2.5 rounded-xl text-sm font-bold text-white bg-cyan-600 hover:bg-cyan-700 shadow-lg shadow-cyan-200 transition">Onayla</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div> 
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/bolumler/dashboard.blade.php ENDPATH**/ ?>