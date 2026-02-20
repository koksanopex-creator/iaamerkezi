<div> 
    <div class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 min-h-screen p-4 md:p-6">
        <div class="max-w-7xl mx-auto">

            
            <?php echo $__env->make('livewire.admin.sikayetler-partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 overflow-hidden">

                
                <div class="border-b border-gray-200 bg-white px-2 sm:px-6 mb-4 rounded-t-xl">
                    <nav class="-mb-px flex flex-wrap lg:flex-nowrap justify-start lg:justify-between items-center gap-x-3 md:gap-x-6"
                        aria-label="Tabs">

                        
                        <button wire:click="setTab('tumu')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                <?php echo e($activeTab === 'tumu' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            Tümü
                            <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-bold">
                                <?php echo e($stats['tumu'] ?? 0); ?>

                            </span>
                        </button>

                        
                        <button wire:click="setTab('yeni')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                <?php echo e($activeTab === 'yeni' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            Yeni
                            <span
                                class="<?php echo e($activeTab === 'yeni' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'); ?> py-0.5 px-2.5 rounded-full text-xs font-bold">
                                <?php echo e($stats['yeni'] ?? 0); ?>

                            </span>
                        </button>

                        
                        <button wire:click="setTab('islemde')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                <?php echo e($activeTab === 'islemde' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            İşlemde
                            <span
                                class="<?php echo e($activeTab === 'islemde' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600'); ?> py-0.5 px-2.5 rounded-full text-xs font-bold">
                                <?php echo e($stats['islemde'] ?? 0); ?>

                            </span>
                        </button>

                        
                        <button wire:click="setTab('onay_bekleyenler')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                <?php echo e($activeTab === 'onay_bekleyenler' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            Onay Bekleyen
                            <span
                                class="<?php echo e($activeTab === 'onay_bekleyenler' ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-600'); ?> py-0.5 px-2.5 rounded-full text-xs font-bold">
                                <?php echo e($stats['onay_bekleyenler'] ?? 0); ?>

                            </span>
                        </button>

                        
                        <button wire:click="setTab('cozulmus')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                <?php echo e($activeTab === 'cozulmus' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            Çözülenler
                            <span
                                class="<?php echo e($activeTab === 'cozulmus' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'); ?> py-0.5 px-2.5 rounded-full text-xs font-bold">
                                <?php echo e($stats['cozulmus'] ?? 0); ?>

                            </span>
                        </button>

                        
                        <button wire:click="setTab('talep_kapali')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                <?php echo e($activeTab === 'talep_kapali' ? 'border-gray-500 text-gray-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            Talep Kapanan
                            <span
                                class="<?php echo e($activeTab === 'talep_kapali' ? 'bg-gray-200 text-gray-800' : 'bg-gray-100 text-gray-600'); ?> py-0.5 px-2.5 rounded-full text-xs font-bold">
                                <?php echo e($stats['talep_kapali'] ?? 0); ?>

                            </span>
                        </button>

                        
                        <button wire:click="setTab('hatali_bildirim')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                <?php echo e($activeTab === 'hatali_bildirim' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            🚫 Hatalı Bildirim
                            <span
                                class="<?php echo e($activeTab === 'hatali_bildirim' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600'); ?> py-0.5 px-2.5 rounded-full text-xs font-bold">
                                <?php echo e($stats['hatali_bildirim'] ?? 0); ?>

                            </span>
                        </button>

                        
                        <button wire:click="setTab('iptal')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-colors focus:outline-none
                                <?php echo e($activeTab === 'iptal' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            İptal/Red
                            <span
                                class="<?php echo e($activeTab === 'iptal' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600'); ?> py-0.5 px-2.5 rounded-full text-xs font-bold">
                                <?php echo e($stats['iptal'] ?? 0); ?>

                            </span>
                        </button>

                    </nav>
                </div>

                
                <?php echo $__env->make('livewire.admin.sikayetler-partials.filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            </div>

            
            <!--[if BLOCK]><![endif]--><?php if($viewMode === 'list'): ?>
                <?php echo $__env->make('livewire.admin.sikayetler-partials.list', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
                <?php echo $__env->make('livewire.admin.sikayetler-partials.cards', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
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
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.5s ease-out forwards;
        }

        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-slide-in {
            animation: slide-in 0.4s ease-out forwards;
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slide-up 0.3s ease-out forwards;
        }

        @keyframes pulse {
            50% {
                opacity: .5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/sikayetler-tablosu.blade.php ENDPATH**/ ?>