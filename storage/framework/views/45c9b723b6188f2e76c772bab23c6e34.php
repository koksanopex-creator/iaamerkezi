<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Köksan Portal')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">

    
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
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['wrapperClass' => 'max-w-sm lg:w-96']));

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

foreach (array_filter((['wrapperClass' => 'max-w-sm lg:w-96']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<body class="h-full">
    <div class="min-h-screen flex flex-col lg:flex-row">

        
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

                <div class="flex gap-4 text-sm text-blue-200/60 font-medium">
                    <span>© <?php echo e(date('Y')); ?> Köksan A.Ş.</span>
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
            </div>
        </div>

        
        <div
            class="flex-1 flex flex-col justify-center py-10 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white relative w-full lg:w-1/2">
            <div class="mx-auto w-full <?php echo e($wrapperClass); ?>">
                <?php echo e($slot); ?>

            </div>
        </div>

    </div>

    

</body>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>

</html><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/layouts/guest.blade.php ENDPATH**/ ?>