<div> 
    <div class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 min-h-screen p-4 md:p-6">
        <div class="max-w-7xl mx-auto">

            
            <?php echo $__env->make('livewire.admin.sikayetler-partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                
                
                <?php echo $__env->make('livewire.admin.sikayetler-partials.stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
                <?php echo $__env->make('livewire.admin.sikayetler-partials.filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            </div>

            
            <?php echo $__env->make('livewire.admin.sikayetler-partials.cards', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="mt-6">
                <?php echo e($sikayetler->links()); ?>

            </div>

        </div>
    </div>
    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.sikayet-triyaj-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-1186699771-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    
    <style>
        @keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in 0.5s ease-out forwards; }
        @keyframes slide-in { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .animate-slide-in { animation: slide-in 0.4s ease-out forwards; }
        @keyframes slide-up { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slide-up 0.3s ease-out forwards; }
        @keyframes pulse { 50% { opacity: .5; } }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/sikayetler-tablosu.blade.php ENDPATH**/ ?>