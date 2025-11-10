<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <?php echo $__env->yieldPushContent('styles'); ?>
        
        
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?> 
        
        
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php if(isset($header)): ?>
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>

            <main>
                <?php echo e($slot); ?>

            </main>
        </div>

        
        <?php echo $__env->yieldPushContent('scripts'); ?>

        
        <?php
            $appUrl = config('app.url'); // .env'den (Lokal: http://localhost:8000)
            $isLocal = str_contains($appUrl, 'localhost:8000') || app()->isLocal();
            
            // Lokalde: /livewire/update
            // Sunucuda: /iaa/livewire/update
            $livewireUpdateUri = $isLocal ? '/livewire/update' : asset('livewire/update');
        ?>
        
        
        <script>
            window.livewire_app_url = '<?php echo e($appUrl); ?>';
            window.livewire_update_uri = '<?php echo e($livewireUpdateUri); ?>';
        </script>
        
        
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

        
        
        <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
        
    </body>
</html><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/layouts/app.blade.php ENDPATH**/ ?>