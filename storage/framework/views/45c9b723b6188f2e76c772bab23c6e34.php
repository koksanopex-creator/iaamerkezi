<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'Laravel')); ?></title>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Inter', sans-serif; }
            .float-animation { animation: float 8s ease-in-out infinite; }
            .float-animation:nth-child(2) { animation-delay: -2.5s; }
            @keyframes float {
                0%, 100% { transform: translateY(0px) translateX(0px); }
                50% { transform: translateY(-20px) translateX(10px); }
            }
            .gradient-bg { background: linear-gradient(135deg, #f1f5f9 0%, #ffffff 100%); }
        </style>
    </head>
    <body class="gradient-bg min-h-screen">
        
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
            <div class="float-animation absolute top-20 left-10 w-64 h-64 bg-blue-100/50 rounded-full opacity-50 blur-2xl"></div>
            <div class="float-animation absolute bottom-20 right-10 w-80 h-80 bg-indigo-100/50 rounded-full opacity-50 blur-2xl"></div>
        </div>
        
        
        <div class="relative min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 z-10">
            
            <div class="mb-6">
                <a href="/">
                    <img src="<?php echo e(asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png')); ?>" alt="Köksan Logo" class="h-16 sm:h-20 w-auto mx-auto drop-shadow-sm">
                </a>
            </div>
            
            
            <div class="w-full max-w-3xl p-8 sm:p-10 bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/80">
                <?php echo e($slot); ?>

            </div>

          

            
            <footer class="w-full text-center py-6">
                 <p class="text-sm text-gray-500">
                    © <?php echo e(date('Y')); ?> Köksan. Her fikir değerlidir.
                </p>
            </footer>
        </div>
    </body>
</html><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/layouts/guest.blade.php ENDPATH**/ ?>