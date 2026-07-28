<?php
    // Sorumlu kişi listesine sadece personelleri alıyoruz
    $users = \App\Models\User::where('onaylandi_mi', true)
        ->where('is_personnel', true)
        ->whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['Superadmin', 'Yonetim']);
        })
        ->orderBy('name')->get();

    // Seçili id'ler
    $selectedIds = $formData[$index]['info_user_ids'] ?? [];
    if (!is_array($selectedIds)) {
        $selectedIds = [];
    }
?>

<?php if (! $__env->hasRenderedOnce('9be5f102-9aed-4558-9363-61eab112571d')): $__env->markAsRenderedOnce('9be5f102-9aed-4558-9363-61eab112571d'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<?php endif; ?>

<div class="space-y-3">
    <label for="widget-<?php echo e($index); ?>-user" class="block text-lg font-semibold text-gray-800">
        <?php echo e(!empty($config['title']) ? $config['title'] : 'Kişi(ler)'); ?>

        <!--[if BLOCK]><![endif]--><?php if($config['required'] ?? false): ?> <span class="text-red-500">*</span> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </label>
    
    <div wire:ignore class="opacity-0 transition-opacity duration-300" x-data x-init="
                const choices = new Choices($refs.selectEl, {
                    removeItemButton: true,
                    searchPlaceholderValue: 'İsim arayın...',
                    noResultsText: 'Sonuç bulunamadı',
                    noChoicesText: 'Seçilecek kişi kalmadı',
                    itemSelectText: 'Seç',
                    placeholder: true,
                    placeholderValue: 'Lütfen kullanıcı seçin...',
                    callbackOnCreateTemplates: function(template) {
                        return {
                            item: (classNames, data) => {
                                let props = data.customProperties || {};
                                let avatarHtml = props.avatar ? `<img class='w-6 h-6 rounded-full border border-indigo-200 shadow-sm' src='${props.avatar}' alt=''>` : '';
                                let bolumHtml = props.bolum ? `<span class='text-[10px] text-indigo-500 leading-none mt-0.5 whitespace-nowrap'>${props.bolum}</span>` : '';
                                
                                return template(`
                                    <div class='${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable} inline-flex items-center gap-2 bg-indigo-50 text-indigo-900 border border-indigo-200 rounded-full pl-1.5 pr-1.5 py-1 m-1 shadow-sm' data-item data-id='${data.id}' data-value='${data.value}' ${data.active ? 'aria-selected=true' : ''} ${data.disabled ? 'aria-disabled=true' : ''}>
                                        ${avatarHtml}
                                        <div class='flex flex-col justify-center'>
                                            <span class='text-sm font-bold leading-none whitespace-nowrap'>${data.label}</span>
                                            ${bolumHtml}
                                        </div>
                                        <button type='button' class='ml-1 w-5 h-5 flex items-center justify-center rounded-full bg-indigo-200 text-indigo-700 hover:bg-indigo-300 hover:text-indigo-900 transition-colors focus:outline-none' data-button aria-label='Kaldır'>
                                            <svg class='w-3 h-3' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='M6 18L18 6M6 6l12 12'/></svg>
                                        </button>
                                    </div>
                                `);
                            },
                            choice: (classNames, data) => {
                                let props = data.customProperties || {};
                                let bolumHtml = props.bolum ? `<span class='text-xs text-gray-400 mt-0.5'>${props.bolum}</span>` : '';
                                let avatarHtml = props.avatar ? `<img class='w-10 h-10 rounded-full border border-gray-200 shadow-sm' src='${props.avatar}' alt=''>` : '';
                                return template(`
                                    <div class='${classNames.item} ${classNames.itemChoice} ${data.disabled ? classNames.itemDisabled : classNames.itemSelectable}' data-select-text='Seç' data-choice ${data.disabled ? 'data-choice-disabled aria-disabled=true' : 'data-choice-selectable'} data-id='${data.id}' data-value='${data.value}' ${data.groupId > 0 ? 'role=treeitem' : 'role=option'}>
                                        <div class='flex items-center gap-3 p-1'>
                                            ${avatarHtml}
                                            <div class='flex flex-col'>
                                                <span class='font-medium text-gray-800'>${data.label}</span>
                                                ${bolumHtml}
                                            </div>
                                        </div>
                                    </div>
                                `);
                            },
                        };
                    }
                });
                $refs.selectEl.addEventListener('change', () => {
                    $wire.set('formData.<?php echo e($index); ?>.info_user_ids', Array.from($refs.selectEl.selectedOptions).map(option => option.value));
                    $wire.autosaveUserSelect();
                });
                $el.classList.remove('opacity-0');
            ">
        <select x-ref="selectEl"
            id="widget-<?php echo e($index); ?>-user" 
            multiple
            class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"
            <?php if($config['required'] ?? false): ?> required <?php endif; ?>
            <?php if($isLockedForUser): ?> disabled <?php endif; ?>>
            
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $avatarUrl = $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF';
                    $customProps = json_encode(['avatar' => $avatarUrl, 'bolum' => $user->bolum ? $user->bolum->name : 'Bölüm Yok']);
                ?>
                <option value="<?php echo e($user->id); ?>" 
                        data-custom-properties='<?php echo e($customProps); ?>'
                        <?php if(in_array($user->id, $selectedIds)): ?> selected <?php endif; ?>>
                    <?php echo e($user->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </select>
    </div>
    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["formData.{$index}.info_user_ids"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/widgets/_user-select-info.blade.php ENDPATH**/ ?>