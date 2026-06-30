<?php $__env->startPush('pageTitle'); ?>
    Müşteri Şikayetleri | 
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
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tight uppercase">
                    <?php echo e(__('Aktif Süreç Takibi')); ?>

                </h2>
                <p class="text-xs font-medium text-slate-500 italic mt-1 uppercase tracking-widest">Müşteri Şikayetleri Yönetim Paneli</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">

                <?php
                    $user = auth()->user();
                    $pendingRemindersCount = \App\Models\SikayetHatirlatma::where('durum', 'bilgi_girisi_bekleniyor');
                    
                    if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu'])) {
                        $allowedBolumIds = $user->getAllowedBolumIds();
                        $pendingRemindersCount->where(function($q) use ($user, $allowedBolumIds) {
                            $q->whereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($allowedBolumIds) {
                                if ($allowedBolumIds !== '*') {
                                    $sq->whereIn('bolum_id', $allowedBolumIds);
                                }
                            })->orWhereHas('musteriSikayeti.cozumTakimi', function($sq) use ($user) {
                                $sq->where('lider_user_id', $user->id);
                            });
                        });
                    }
                    $pendingRemindersCount = $pendingRemindersCount->count();
                ?>
                <?php if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Bölüm Lideri']) || Auth::user()->hasBolumAuthority('bolum.hatirlatma.gor')): ?>
                <a href="<?php echo e(route('admin.sikayet-hatirlatma.index')); ?>"
                    class="relative inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    HATIRLATMALAR

                    <?php if($pendingRemindersCount > 0): ?>
                        <span class="absolute -top-2.5 -right-2.5 flex h-6 w-6">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-6 w-6 bg-red-600 items-center justify-center text-[11px] font-black text-white shadow-lg border-2 border-white animate-pulse">
                                <?php echo e($pendingRemindersCount); ?>

                            </span>
                        </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                    <a href="<?php echo e(route('admin.sikayet-hatirlatma.ayarlar')); ?>" 
                        class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl font-black text-[11px] text-indigo-600 uppercase tracking-widest hover:bg-indigo-50 hover:border-indigo-200 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Sistem Ayarları
                    </a>
                <?php endif; ?>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success_html')): ?>
                <div
                    class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3 animate-fade-in-down">
                    <div class="bg-green-100 p-2 rounded-full text-green-600 shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-green-800">Başarılı!</h4>
                        <p class="text-sm text-green-700 mt-1"><?php echo session('success_html'); ?></p>
                    </div>
                </div>
            <?php elseif(session('success')): ?>
                <div
                    class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3 animate-fade-in-down">
                    <div class="bg-green-100 p-2 rounded-full text-green-600 shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-green-800">Başarılı!</h4>
                        <p class="text-sm text-green-700 mt-1"><?php echo e(session('success')); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.sikayetler-tablosu');

$__html = app('livewire')->mount($__name, $__params, 'lw-1825170221-0', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('admin.sikayet-musteri-atama-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-1825170221-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
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
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/sikayetler/index.blade.php ENDPATH**/ ?>