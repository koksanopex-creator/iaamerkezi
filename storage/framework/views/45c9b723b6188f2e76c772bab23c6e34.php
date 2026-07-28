<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Köksan Portal')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon.svg')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.png')); ?>">

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['wrapperClass' => 'max-w-sm lg:w-96', 'fullWidth' => false]));

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

foreach (array_filter((['wrapperClass' => 'max-w-sm lg:w-96', 'fullWidth' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<body class="h-full bg-slate-50">
    <div class="min-h-screen flex flex-col <?php echo e($fullWidth ? '' : 'lg:flex-row'); ?>">

        
        <?php if(!$fullWidth): ?>
            <div class="hidden lg:flex w-1/2 relative bg-gray-900 overflow-hidden text-white">
                
                <div class="absolute inset-0 bg-cover bg-center opacity-40 mix-blend-overlay"
                    style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2301&auto=format&fit=crop');">
                </div>
                <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 to-slate-900/90"></div>

                
                <div class="relative z-10 w-full flex flex-col justify-between p-16">
                    <div>
                        
                        <div class="flex justify-center w-full mb-8">
                            <a href="<?php echo e(url('/')); ?>"
                                class="bg-white/10 p-6 rounded-3xl inline-block backdrop-blur-sm border border-white/20 hover:bg-white/20 transition-all duration-300 shadow-xl">
                                <img src="<?php echo e(asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png')); ?>"
                                    alt="Köksan Logo" class="h-20 w-auto brightness-0 invert">
                            </a>
                        </div>

                        <h1 class="text-5xl font-extrabold leading-tight tracking-tight mb-6 text-center">
                            Kurumsal <br>
                            <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-emerald-300">Yönetim
                                Sistemi</span>
                        </h1>

                        <p class="text-lg text-blue-100 max-w-lg leading-relaxed font-light text-center mx-auto">
                            Müşteri şikayetleri, iyileştirmeye açık alanlar, disiplin süreçleri ve öneri sistemi ile
                            şirketimizin kalitesini birlikte yükseltiyoruz.
                        </p>
                    </div>

                    <div class="flex flex-col gap-6">
                        <div class="text-sm text-blue-200/60 font-medium pt-8 border-t border-white/10">
                            <span>© <?php echo e(date('Y')); ?> Köksan A.Ş. Tüm hakları saklıdır. | Köksan Opex</span>
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <div @mouseenter="open = true" @mouseleave="open = false"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-blue-200 hover:text-white hover:bg-white/10 transition-all cursor-help group w-fit">
                                <span class="text-[10px] font-medium uppercase tracking-wider opacity-60">Sistem Tasarımı & Yönetimi:</span>
                                <span class="text-xs font-bold text-white">Celal KARAMAN</span>
                                <svg class="w-3 h-3 text-blue-400 group-hover:rotate-12 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </div>

                            <!-- Hover Bilgi Kartı -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-cloak
                                class="absolute bottom-full left-0 mb-3 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 z-[110] text-gray-900">
                                <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-50">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">Celal KARAMAN</div>
                                        <div class="text-[10px] text-indigo-500 font-black uppercase tracking-widest">Opex Mühendisi</div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <a href="mailto:celal.karaman@koksan.com" class="flex items-center gap-2 text-xs text-gray-600 hover:text-indigo-600 transition truncate">
                                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        celal.karaman@koksan.com
                                    </a>
                                    <div class="flex items-center gap-2 text-xs text-gray-600 transition">
                                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        0549 678 76 91
                                    </div>
                                </div>
                                <div class="absolute -bottom-1.5 left-6 w-3 h-3 bg-white border-b border-r border-gray-100 rotate-45"></div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
            </div>

            
            <div
                class="lg:hidden bg-gray-900 text-white p-6 relative overflow-hidden flex flex-col items-center justify-center text-center">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 to-slate-900/90 z-0"></div>
                <div class="relative z-10 w-full">
                    <a href="<?php echo e(url('/')); ?>" class="inline-block mb-4">
                        <img src="<?php echo e(asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png')); ?>"
                            alt="Köksan Logo" class="h-16 w-auto brightness-0 invert drop-shadow-lg">
                    </a>
                    <h2 class="text-2xl font-bold">Kurumsal Yönetim Sistemi</h2>
                    <p class="text-sm text-blue-100 mt-2 opacity-80">Müşteri şikayetleri, disiplin ve öneri süreçleri.</p>
                    <div class="mt-8 pt-6 border-t border-white/10 flex flex-col items-center gap-4">
                        <p class="text-[10px] text-blue-200/60 font-medium">© <?php echo e(date('Y')); ?> Köksan A.Ş. Tüm hakları saklıdır. | Köksan Opex</p>
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-blue-200">
                            <span class="text-[9px] font-medium uppercase tracking-wider opacity-60">Sistem Tasarımı:</span>
                            <span class="text-xs font-bold text-white">Celal KARAMAN</span>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            
            <div class="bg-gray-900 border-b border-gray-800 text-white p-4 shadow-sm flex items-center justify-between">
                <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-3">
                    <img src="<?php echo e(asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png')); ?>" alt="Köksan Logo"
                        class="h-10 w-auto brightness-0 invert drop-shadow-sm">
                    <span class="font-bold text-lg hidden sm:block tracking-tight">Kurumsal Yönetim Sistemi</span>
                </a>
            </div>
        <?php endif; ?>

        
        <div
            class="flex-1 flex flex-col justify-<?php echo e($fullWidth ? 'start mt-4' : 'center'); ?> py-10 px-4 sm:px-6 lg:px-8 bg-<?php echo e($fullWidth ? 'slate-50' : 'white'); ?> relative w-full <?php echo e($fullWidth ? '' : 'lg:flex-none lg:w-1/2 xl:px-24'); ?>">
            <div class="mx-auto w-full <?php echo e($wrapperClass); ?>">
                <?php echo e($slot); ?>

            </div>
        </div>

    </div>

    

</body>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>

</html><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/layouts/guest.blade.php ENDPATH**/ ?>