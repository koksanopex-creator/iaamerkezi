<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['index', 'config']));

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

foreach (array_filter((['index', 'config']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php if (! $__env->hasRenderedOnce('eeeaaa24-6315-42b4-bd50-33edcae520e2')): $__env->markAsRenderedOnce('eeeaaa24-6315-42b4-bd50-33edcae520e2');
$__env->startPush('scripts'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        // Sayfa ilk yüklendiğinde ve Livewire güncellemesinden sonra Fancybox'ı bağla
        document.addEventListener('livewire:navigated', () => {
             Fancybox.bind("[data-fancybox]", { /* Özel ayarlar */ });
        });
        document.addEventListener('DOMContentLoaded', () => {
             Fancybox.bind("[data-fancybox]", { /* Özel ayarlar */ });
        });
    </script>
<?php $__env->stopPush(); endif; ?>

<div x-data="fileUploadWidget({ index: <?php echo e($index); ?>, componentId: '<?php echo e($this->getId()); ?>' })" x-init="init()">
    <label for="widget-<?php echo e($index); ?>-files" class="block text-lg font-semibold text-gray-800">
        <?php echo e($config['title'] ?? 'Dosya Yükle'); ?>

        <!--[if BLOCK]><![endif]--><?php if($config['required'] ?? false): ?> <span class="text-red-500">*</span> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </label>

    
    <!--[if BLOCK]><![endif]--><?php if(!empty($formData[$index]['files'])): ?>
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-600 mb-2">Mevcut Dosyalar:</p>
            <div class="flex flex-wrap gap-3">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $formData[$index]['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filePath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isImage = Str::endsWith(strtolower($filePath), ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.webp']);
                    ?>
                    <div class="relative group w-24">
                        <!--[if BLOCK]><![endif]--><?php if($isImage): ?>
                            
                            <a href="<?php echo e(asset('storage/' . $filePath)); ?>" data-fancybox="gallery-active-<?php echo e($index); ?>" data-caption="<?php echo e(basename($filePath)); ?>">
                                <img src="<?php echo e(asset('storage/' . $filePath)); ?>" alt="<?php echo e(basename($filePath)); ?>" class="h-24 w-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                            </a>
                        <?php else: ?>
                            
                            <a href="<?php echo e(asset('storage/' . $filePath)); ?>" target="_blank" class="h-24 w-24 flex flex-col items-center justify-center bg-gray-100 rounded-lg border border-gray-200 p-2" title="<?php echo e(basename($filePath)); ?>">
                                <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.41l-7.81 7.81a1.5 1.5 0 002.122 2.122l7.81-7.81" /></svg>
                                <span class="text-xs text-gray-500 mt-1 truncate"><?php echo e(basename($filePath)); ?></span>
                            </a>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <button type="button"
                                wire:click="markFileForDeletion(<?php echo e($index); ?>, '<?php echo e($filePath); ?>')"
                                wire:confirm="Bu dosyayı kalıcı olarak silmek istediğinizden emin misiniz? (Kaydet'e basınca uygulanır)"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-0.5 w-5 h-5 flex items-center justify-center opacity-75 group-hover:opacity-100 transition-opacity z-10">
                            &times;
                        </button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if(!empty($newUploads[$index])): ?>
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-600 mb-2">Yeni Yüklenenler (Kaydetmeyi bekliyor):</p>
            <div class="flex flex-wrap gap-3">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $newUploads[$index]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fileKey => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative group w-24">
                        <!--[if BLOCK]><![endif]--><?php if(method_exists($file, 'temporaryUrl') && Str::startsWith($file->getMimeType(), 'image')): ?>
                            <a href="<?php echo e($file->temporaryUrl()); ?>" data-fancybox="gallery-active-<?php echo e($index); ?>" data-caption="<?php echo e($file->getClientOriginalName()); ?>">
                                <img src="<?php echo e($file->temporaryUrl()); ?>" class="h-24 w-24 object-cover rounded-lg border border-blue-300">
                            </a>
                        <?php elseif(method_exists($file, 'temporaryUrl')): ?>
                            <div class="h-24 w-24 flex flex-col items-center justify-center bg-blue-50 rounded-lg border border-blue-300 p-2" title="<?php echo e($file->getClientOriginalName()); ?>">
                                <svg class="w-8 h-8 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.41l-7.81 7.81a1.5 1.5 0 002.122 2.122l7.81-7.81" /></svg>
                                <span class="text-xs text-blue-500 mt-1 truncate"><?php echo e($file->getClientOriginalName()); ?></span>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <button type="button"
                                wire:click="removeNewUpload(<?php echo e($index); ?>, <?php echo e($fileKey); ?>)"
                                class="absolute -top-2 -right-2 bg-gray-500 text-white rounded-full p-0.5 w-5 h-5 flex items-center justify-center opacity-75 group-hover:opacity-100 transition-opacity z-10">
                            &times;
                        </button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="mt-4">
        <label for="widget-<?php echo e($index); ?>-files" class="sr-only">Dosya Seç</label>
        
        <input
            type="file"
            wire:model="newUploads.<?php echo e($index); ?>"
            id="widget-<?php echo e($index); ?>-files"
            class="block w-full text-sm text-gray-500
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-lg file:border-0
                   file:text-sm file:font-semibold
                   file:bg-indigo-50 file:text-indigo-700
                   hover:file:bg-indigo-100"
            <?php if($config['multiple'] ?? false): ?> multiple <?php endif; ?>
        />

        
        <div wire:loading wire:target="newUploads.<?php echo e($index); ?>" class="text-sm text-gray-500 mt-1">Yükleniyor...</div>
    </div>
    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["newUploads.{$index}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["newUploads.{$index}.*"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Alpine.js component'ini tanımla (sadece bir kez tanımlanması için kontrol)
    if (typeof window.fileUploadWidget === 'undefined') {
        window.fileUploadWidget = (options) => ({
            index: options.index,
            componentId: options.componentId,
            gallerySelector: `[data-fancybox="gallery-active-${options.index}"]`,
            
            init() {
                // Sayfa ilk yüklendiğinde Fancybox'ı bağla
                this.$nextTick(() => {
                    Fancybox.bind(this.gallerySelector);
                });

                // Livewire component'i güncellendiğinde (dosya eklendi/silindi) Fancybox'ı tekrar bağla
                Livewire.hook('element.updated', (el, component) => {
                    if (component.id === this.componentId) {
                        this.$nextTick(() => {
                            Fancybox.unbind(this.gallerySelector);
                            Fancybox.bind(this.gallerySelector);
                        });
                    }
                });
            }
        });
    }
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/widgets/_file-upload.blade.php ENDPATH**/ ?>