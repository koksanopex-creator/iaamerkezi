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
    <?php $__env->startPush('pageTitle', 'Tutanak Düzenle | '); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Tutanak Düzenle')); ?> <span class="text-gray-500 text-sm">#<?php echo e($case->id); ?></span>
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            
            <?php if($errors->any()): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <strong class="font-bold">Lütfen hataları düzeltin:</strong>
                    <ul class="list-disc ml-5 text-sm">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                
                <form action="<?php echo e(route('admin.disiplin.update', $case->id)); ?>" method="POST" enctype="multipart/form-data"
                    x-data="{ 
                        selectedCategory: '<?php echo e($case->behavior->category_id); ?>', 
                        behaviors: <?php echo e(json_encode($categories)); ?>,
                        files: [], // Yeni dosyalar
                        serverFiles: <?php echo e(json_encode($case->kanit_dosyalari ?? [])); ?>, // Eski dosyalar
                        deletedServerFiles: [], // Silinecek eski dosyalar

                        // Yeni Dosya Ekleme
                        handleFileSelect(event) {
                            const fileList = event.target.files;
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            for (let i = 0; i < fileList.length; i++) {
                                if (!this.files.some(f => f.name === fileList[i].name)) {
                                    dt.items.add(fileList[i]);
                                    this.files.push(fileList[i]);
                                }
                            }
                            this.$refs.fileInput.files = dt.files;
                        },

                        // Yeni Dosyayı Silme
                        removeFile(index) {
                            this.files.splice(index, 1);
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            this.$refs.fileInput.files = dt.files;
                        },

                        // Eski (Server) Dosyasını Silme (Görünümden kaldır ve listeye ekle)
                        removeServerFile(index, path) {
                            this.deletedServerFiles.push(path); // Backend'e gidecek
                            this.serverFiles.splice(index, 1); // Ekrandan silinecek
                        },

                        formatSize(size) {
                            if (size > 1024 * 1024) return (size / (1024 * 1024)).toFixed(2) + ' MB';
                            return (size / 1024).toFixed(2) + ' KB';
                        }
                    }">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <h3 class="text-sm font-bold text-gray-700 uppercase mb-4 border-b pb-2">Olay Bilgileri</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İlgili Personel</label>
                                <select disabled class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-gray-500 cursor-not-allowed">
                                    <option><?php echo e($case->user->name); ?></option>
                                </select>
                                <input type="hidden" name="user_id" value="<?php echo e($case->user_id); ?>">
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Olay Tarihi ve Saati <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="olay_tarihi" value="<?php echo e($case->olay_tarihi->format('Y-m-d\TH:i')); ?>" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6 shadow-sm">
                        <h3 class="text-sm font-bold text-indigo-700 uppercase mb-4 border-b pb-2">İhlal / Suç Seçimi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Suç Kategorisi <span class="text-red-500">*</span></label>
                                <select x-model="selectedCategory" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Kategori Seçin --</option>
                                    <template x-for="cat in behaviors" :key="cat.id">
                                        <option :value="cat.id" x-text="cat.ad" :selected="cat.id == selectedCategory"></option>
                                    </template>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İhlal Edilen Madde <span class="text-red-500">*</span></label>
                                <select name="behavior_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Seçiniz --</option>
                                    <template x-for="cat in behaviors">
                                        <template x-if="cat.id == selectedCategory">
                                            <template x-for="b in cat.behaviors">
                                                <option :value="b.id" x-text="b.tanim" :selected="b.id == <?php echo e($case->behavior_id); ?>"></option>
                                            </template>
                                        </template>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-indigo-50 p-5 rounded-lg border border-indigo-100 mb-6">
                        <h3 class="text-sm font-bold text-indigo-900 uppercase mb-4">Ciddiyet Değerlendirmesi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Suçun Şiddeti (Etkisi) <span class="text-red-500">*</span></label>
                                <select name="impact_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="">-- Seçiniz --</option>
                                    <?php $__currentLoopData = $impacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($i->id); ?>" <?php echo e($case->impact_id == $i->id ? 'selected' : ''); ?>>
                                            <?php echo e($i->tanim); ?> (x<?php echo e($i->puan); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Etki Kapsamı <span class="text-red-500">*</span></label>
                                <select name="scope_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="">-- Seçiniz --</option>
                                    <?php $__currentLoopData = $scopes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s->id); ?>" <?php echo e($case->scope_id == $s->id ? 'selected' : ''); ?>>
                                            <?php echo e($s->tanim); ?> (x<?php echo e($s->puan); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Olayın Detaylı Açıklaması <span class="text-red-500">*</span></label>
                        <textarea name="olay_aciklamasi" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required><?php echo e($case->olay_aciklamasi); ?></textarea>
                    </div>

                    
                    <div class="mb-6 bg-white p-4 rounded-lg border border-gray-200">
                        <h3 class="text-sm font-bold text-gray-700 mb-4">Kanıt Dosyaları Yönetimi</h3>

                        
                        <template x-for="path in deletedServerFiles">
                            <input type="hidden" name="silinecek_dosyalar[]" :value="path">
                        </template>

                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:bg-gray-50 transition relative">
                            <input x-ref="fileInput" type="file" name="kanit_dosyalari[]" multiple 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                @change="handleFileSelect">

                            
                            <div class="text-center py-4" x-show="files.length === 0 && serverFiles.length === 0">
                                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <p class="text-sm text-gray-500 mt-2">Dosya yüklemek için tıklayın veya sürükleyin</p>
                            </div>

                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2" x-show="files.length > 0 || serverFiles.length > 0">
                                
                                
                                <template x-for="(path, index) in serverFiles" :key="'server-'+index">
                                    <div class="relative group border border-blue-200 bg-blue-50 rounded-lg overflow-hidden z-20">
                                        <div class="h-20 flex items-center justify-center">
                                            <a :href="'/storage/'+path" target="_blank" class="text-blue-500 font-bold text-xs hover:underline">Görüntüle</a>
                                        </div>
                                        <div class="bg-blue-100 p-1 text-[9px] text-blue-800 text-center truncate">
                                            Kayıtlı Dosya
                                        </div>
                                        
                                        <button type="button" @click.prevent="removeServerFile(index, path)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>

                                
                                <template x-for="(file, index) in files" :key="'new-'+index">
                                    <div class="relative group border border-green-200 bg-green-50 rounded-lg overflow-hidden z-20">
                                        <div class="h-20 flex items-center justify-center overflow-hidden">
                                            <template x-if="file.type.startsWith('image/')">
                                                <img :src="URL.createObjectURL(file)" class="h-full w-full object-cover opacity-80">
                                            </template>
                                            <template x-if="!file.type.startsWith('image/')">
                                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </template>
                                        </div>
                                        <div class="bg-green-100 p-1 text-[9px] text-green-800 text-center truncate">
                                            Yeni: <span x-text="file.name"></span>
                                        </div>
                                        
                                        <button type="button" @click.prevent="removeFile(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>

                                
                                <div class="border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-100 min-h-[80px]" @click="$refs.fileInput.click()">
                                    <span class="text-2xl text-gray-400">+</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="<?php echo e(route('admin.disiplin.show', $case->id)); ?>" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md font-semibold hover:bg-gray-50 transition">
                            İptal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold hover:bg-blue-700 shadow-lg transform hover:scale-105 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Değişiklikleri Kaydet
                        </button>
                    </div>

                </form>
            </div>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/edit.blade.php ENDPATH**/ ?>