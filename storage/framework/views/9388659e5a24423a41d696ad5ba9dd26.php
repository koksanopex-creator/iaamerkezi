<?php if (isset($component)) { $__componentOriginal99769021b7e4c25d84bbeb0f3c1fb268 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal99769021b7e4c25d84bbeb0f3c1fb268 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '7224ff0b1fa42156c352216deff15b56::pulse','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pulse'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $lastPurge = \Illuminate\Support\Facades\Cache::get('last_pulse_purge_time');
        $purgeText = $lastPurge ? 'Son Temizleme: ' . \Carbon\Carbon::parse($lastPurge)->isoFormat('D MMM YYYY, HH:mm') : 'Logları Temizle';
    ?>
    <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; margin-bottom: 1rem;">
        <form action="<?php echo e(route('pulse.purge')); ?>" method="POST" onsubmit="return confirm('Tüm logları (veritabanı, hata vb.) silmek istediğinize emin misiniz? Bu işlem geri alınamaz.');">
            <?php echo csrf_field(); ?>
            <button type="submit" style="background-color: #ef4444; color: #ffffff; font-weight: 600; padding: 0.375rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                <?php echo e($purgeText); ?>

            </button>
        </form>
    </div>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('pulse.servers', ['cols' => 'full']);

$__html = app('livewire')->mount($__name, $__params, 'lw-2603882892-0', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('pulse.usage', ['cols' => '4','rows' => '2']);

$__html = app('livewire')->mount($__name, $__params, 'lw-2603882892-1', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('pulse.queues', ['cols' => '4']);

$__html = app('livewire')->mount($__name, $__params, 'lw-2603882892-2', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('pulse.cache', ['cols' => '4']);

$__html = app('livewire')->mount($__name, $__params, 'lw-2603882892-3', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('pulse.slow-queries', ['cols' => '8']);

$__html = app('livewire')->mount($__name, $__params, 'lw-2603882892-4', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('pulse.exceptions', ['cols' => '6']);

$__html = app('livewire')->mount($__name, $__params, 'lw-2603882892-5', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('pulse.slow-requests', ['cols' => '6']);

$__html = app('livewire')->mount($__name, $__params, 'lw-2603882892-6', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('pulse.slow-jobs', ['cols' => '6']);

$__html = app('livewire')->mount($__name, $__params, 'lw-2603882892-7', $__slots ?? [], get_defined_vars());

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
[$__name, $__params] = $__split('pulse.slow-outgoing-requests', ['cols' => '6']);

$__html = app('livewire')->mount($__name, $__params, 'lw-2603882892-8', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal99769021b7e4c25d84bbeb0f3c1fb268)): ?>
<?php $attributes = $__attributesOriginal99769021b7e4c25d84bbeb0f3c1fb268; ?>
<?php unset($__attributesOriginal99769021b7e4c25d84bbeb0f3c1fb268); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal99769021b7e4c25d84bbeb0f3c1fb268)): ?>
<?php $component = $__componentOriginal99769021b7e4c25d84bbeb0f3c1fb268; ?>
<?php unset($__componentOriginal99769021b7e4c25d84bbeb0f3c1fb268); ?>
<?php endif; ?>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/vendor/pulse/dashboard.blade.php ENDPATH**/ ?>