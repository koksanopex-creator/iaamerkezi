<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <script>
        window.notificationApiUrls = {
            index: '<?php echo e(route("notifications.index")); ?>',
            unreadCount: '<?php echo e(route("notifications.unreadCount")); ?>',
            markAsRead: '<?php echo e(route("notifications.markAsRead")); ?>'
        };
    </script>

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">
</head>

<body class="font-sans antialiased bg-gray-50">

    <div class="min-h-screen">
        
        <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php if(isset($header)): ?>
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <?php echo e($header); ?>

                </div>
            </header>
        <?php endif; ?>

        
        <main>
            <div class="py-8">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <?php echo e($slot); ?>

                </div>
            </div>
        </main>

        
        <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <?php
        $appUrl = config('app.url');
        $isLocal = str_contains($appUrl, 'localhost:8000') || app()->isLocal();
        $livewireUpdateUri = $isLocal ? '/livewire/update' : asset('livewire/update');
    ?>

    <script>
        window.livewire_app_url = '<?php echo e($appUrl); ?>';
        window.livewire_update_uri = '<?php echo e($livewireUpdateUri); ?>';
    </script>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
    <?php if(auth()->guard()->check()): ?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('global-chat-bot', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-1131963148-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    <?php endif; ?>
</body>

</html><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/layouts/app.blade.php ENDPATH**/ ?>