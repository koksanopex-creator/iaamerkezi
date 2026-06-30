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

    <title><?php echo $__env->yieldPushContent('pageTitle'); ?><?php echo e(config('app.name', 'Laravel')); ?></title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>


    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon.svg')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.png')); ?>">

    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased bg-gray-50">
    
    <?php echo $__env->make('layouts.partials.shadow-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="min-h-screen">
        
        <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php if(isset($header)): ?>
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <?php echo e($header); ?>

                </div>
            </header>
        <?php endif; ?>

        
        <?php if(auth()->guard()->check()): ?>
            <?php
                $user = Auth::user();
                $hideWarning = $user->hasRole('Superadmin') || !empty($user->customer_id);
                $missingInfo = !$user->dogum_tarihi || !$user->telefon || !$user->profile_photo_path;
            ?>
            <?php if(!$hideWarning && $missingInfo && !session()->has('dismiss_profile_warning')): ?>
                <div x-data="{ show: true }" x-show="show" x-transition 
                     class="bg-rose-600 border-b border-rose-700 py-3 px-4 sm:px-6 lg:px-8 text-white relative z-40">
                    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="flex p-2 rounded-lg bg-rose-500 shadow-sm">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <p class="font-medium text-sm sm:text-base">
                                <span class="hidden md:inline">Kişisel bilgilerinizi (Doğum tarihi, telefon, fotoğraf) tamamlamak için lütfen profilinizi güncelleyiniz. Aksi takdirde her girişinizde bu hatırlatmayı göreceksiniz.</span>
                                <span class="md:hidden">Profil bilgilerinizi (doğum tarihi, telefon, vb.) güncelleyin.</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto justify-end">
                            <a href="<?php echo e(route('profile.edit', ['tab' => 'settings'])); ?>" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-rose-600 bg-white hover:bg-rose-50 transition-colors whitespace-nowrap">
                                Profili Güncelle
                            </a>
                            <form action="<?php echo e(route('profile.dismiss.warning')); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" @click="show = false" class="p-2 ml-1 hover:bg-rose-500 rounded-lg transition-colors" title="Şimdilik yoksay">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        
        <main>
            <div class="py-8">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <?php if(isset($slot)): ?>
                        <?php echo e($slot); ?>

                    <?php else: ?>
                        <?php echo $__env->yieldContent('content'); ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        
        <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    
    <?php
        $appUrl = config('app.url');
        // APP_URL değerinden alt dizini otomatik algılar (.env'de APP_URL ne ise onu kullanır)
        // Örnek: https://kys.koksan.com/uuu ise $prefix = '/uuu' olur. 
        // localhost ise $prefix = '' olur.
        $prefix = rtrim(parse_url($appUrl, PHP_URL_PATH) ?? '', '/');
        
        $livewireUpdateUri = $prefix . '/livewire/update'; 
    ?>

    <script>
        window.livewire_app_url = '<?php echo e($appUrl); ?>';
        window.livewire_update_uri = '<?php echo e($livewireUpdateUri); ?>';
    </script>

    
    <script>
        window.livewireScriptConfig = {
            uri: '<?php echo e($livewireUpdateUri); ?>',
            asset_url: '<?php echo e($prefix); ?>',
            csrf: '<?php echo e(csrf_token()); ?>',
            updateUri: '<?php echo e($livewireUpdateUri); ?>',
            progressBar: '',
            nonce: ''
        };
    </script>

    
    <script src="<?php echo e($prefix); ?>/vendor/livewire/livewire.js" data-navigate-once></script>

    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.Livewire) {
                Livewire.start();
            }

            const searchButton = document.getElementById('globalSearchButton');
            const searchContainer = document.getElementById('globalSearchContainer');
        });
    </script>

    
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

        
        <?php if(Auth::user()->isShadowing()): ?>
            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.hook('request', ({
                        fail
                    }) => {
                        fail(({
                            status,
                            preventDefault
                        }) => {
                            if (status === 403) {
                                preventDefault(); // 403 hatasının modal açmasını engeller
                                console.warn('Gözlemci modu: Yazma işlemi (POST) engellendi.');
                            }
                        });
                    });
                });
            </script>
        <?php endif; ?>
    <?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal47fb85b8bc4f43a570dcf9cdfddbe94b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal47fb85b8bc4f43a570dcf9cdfddbe94b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.flash-notifications','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash-notifications'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal47fb85b8bc4f43a570dcf9cdfddbe94b)): ?>
<?php $attributes = $__attributesOriginal47fb85b8bc4f43a570dcf9cdfddbe94b; ?>
<?php unset($__attributesOriginal47fb85b8bc4f43a570dcf9cdfddbe94b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal47fb85b8bc4f43a570dcf9cdfddbe94b)): ?>
<?php $component = $__componentOriginal47fb85b8bc4f43a570dcf9cdfddbe94b; ?>
<?php unset($__componentOriginal47fb85b8bc4f43a570dcf9cdfddbe94b); ?>
<?php endif; ?>
</body>

</html><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/layouts/app.blade.php ENDPATH**/ ?>