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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Disiplin Sistemi Ayarları')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" 
             x-data="{ 
                activeTab: localStorage.getItem('disiplinActiveTab') || 'parametreler',
                setTab(tab) { this.activeTab = tab; localStorage.setItem('disiplinActiveTab', tab); }
             }">
            
            
            <?php if(session('success')): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            
            <div class="bg-white shadow rounded-lg p-2 flex space-x-2 overflow-x-auto">
                <button @click="setTab('parametreler')" 
                        :class="activeTab === 'parametreler' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-md text-sm font-medium transition whitespace-nowrap">
                    1. Parametreler (Kategori/Etki/Kapsam)
                </button>
                <button @click="setTab('suclar')" 
                        :class="activeTab === 'suclar' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-md text-sm font-medium transition whitespace-nowrap">
                    2. Suç/Ceza Listesi
                </button>
                <button @click="setTab('katsayilar')" 
                        :class="activeTab === 'katsayilar' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-md text-sm font-medium transition whitespace-nowrap">
                    3. Hesaplama (Katsayı & Skala)
                </button>
            </div>

            
            <div x-show="activeTab === 'parametreler'" class="grid grid-cols-1 md:grid-cols-3 gap-6" style="display: none;">
                
                
                <div class="bg-white shadow sm:rounded-lg p-4 h-fit" 
                     x-data="{ id: null, ad: '', isEdit: false, action: '<?php echo e(route('admin.disiplin.settings.category.store')); ?>' }">
                    
                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2 flex justify-between">
                        <span>Kategoriler</span>
                        <button x-show="isEdit" @click="isEdit=false; id=null; ad=''; action='<?php echo e(route('admin.disiplin.settings.category.store')); ?>'" class="text-xs text-red-500 underline">İptal</button>
                    </h4>

                    <form :action="action" method="POST" class="flex gap-2 mb-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        <input type="text" name="ad" x-model="ad" placeholder="Kategori Adı" class="w-full text-xs rounded border-gray-300" required>
                        <button type="submit" class="bg-green-600 text-white px-2 rounded text-xs font-bold" x-text="isEdit ? 'GÜNCELLE' : 'EKLE'">+</button>
                    </form>

                    <ul class="space-y-2 max-h-60 overflow-y-auto">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded border">
                            <span class="truncate"><?php echo e($c->ad); ?></span>
                            <div class="flex gap-2">
                                <button @click="isEdit=true; id=<?php echo e($c->id); ?>; ad='<?php echo e(addslashes($c->ad)); ?>'; action='/admin/disiplin-ayarlari/kategori/<?php echo e($c->id); ?>'" class="text-blue-500 text-xs font-bold">DÜZENLE</button>
                                <form action="<?php echo e(route('admin.disiplin.settings.category.delete', $c->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="text-red-500 hover:text-red-700 font-bold text-xs">SİL</button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>

                
                <div class="bg-white shadow sm:rounded-lg p-4 h-fit"
                     x-data="{ id: null, tanim: '', puan: '', isEdit: false, action: '<?php echo e(route('admin.disiplin.settings.impact.store')); ?>' }">
                    
                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2 flex justify-between">
                        <span>Etki / Şiddet</span>
                        <button x-show="isEdit" @click="isEdit=false; id=null; tanim=''; puan=''; action='<?php echo e(route('admin.disiplin.settings.impact.store')); ?>'" class="text-xs text-red-500 underline">İptal</button>
                    </h4>

                    <form :action="action" method="POST" class="flex gap-2 mb-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        <input type="text" name="tanim" x-model="tanim" placeholder="Tanım" class="w-full text-xs rounded border-gray-300" required>
                        <input type="number" name="puan" x-model="puan" placeholder="Pn" class="w-12 text-xs rounded border-gray-300" required>
                        <button type="submit" class="bg-green-600 text-white px-2 rounded text-xs font-bold" x-text="isEdit ? '✓' : '+'">+</button>
                    </form>

                    <ul class="space-y-2 max-h-60 overflow-y-auto">
                        <?php $__currentLoopData = $impacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded border">
                            <span><?php echo e($i->tanim); ?></span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-indigo-600 text-xs"><?php echo e($i->puan); ?>p</span>
                                <button @click="isEdit=true; id=<?php echo e($i->id); ?>; tanim='<?php echo e(addslashes($i->tanim)); ?>'; puan='<?php echo e($i->puan); ?>'; action='/admin/disiplin-ayarlari/etki/<?php echo e($i->id); ?>'" class="text-blue-500 text-xs font-bold">DÜZENLE</button>
                                <form action="<?php echo e(route('admin.disiplin.settings.impact.delete', $i->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="text-red-500 hover:text-red-700 font-bold text-xs">SİL</button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>

                
                <div class="bg-white shadow sm:rounded-lg p-4 h-fit"
                     x-data="{ id: null, tanim: '', puan: '', isEdit: false, action: '<?php echo e(route('admin.disiplin.settings.scope.store')); ?>' }">
                    
                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2 flex justify-between">
                        <span>Kapsam</span>
                        <button x-show="isEdit" @click="isEdit=false; id=null; tanim=''; puan=''; action='<?php echo e(route('admin.disiplin.settings.scope.store')); ?>'" class="text-xs text-red-500 underline">İptal</button>
                    </h4>

                    <form :action="action" method="POST" class="flex gap-2 mb-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        <input type="text" name="tanim" x-model="tanim" placeholder="Tanım" class="w-full text-xs rounded border-gray-300" required>
                        <input type="number" name="puan" x-model="puan" placeholder="Pn" class="w-12 text-xs rounded border-gray-300" required>
                        <button type="submit" class="bg-green-600 text-white px-2 rounded text-xs font-bold" x-text="isEdit ? '✓' : '+'">+</button>
                    </form>

                    <ul class="space-y-2 max-h-60 overflow-y-auto">
                        <?php $__currentLoopData = $scopes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded border">
                            <span><?php echo e($s->tanim); ?></span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-indigo-600 text-xs"><?php echo e($s->puan); ?>p</span>
                                <button @click="isEdit=true; id=<?php echo e($s->id); ?>; tanim='<?php echo e(addslashes($s->tanim)); ?>'; puan='<?php echo e($s->puan); ?>'; action='/admin/disiplin-ayarlari/kapsam/<?php echo e($s->id); ?>'" class="text-blue-500 text-xs font-bold">DÜZENLE</button>
                                <form action="<?php echo e(route('admin.disiplin.settings.scope.delete', $s->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="text-red-500 hover:text-red-700 font-bold text-xs">SİL</button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>

            
            <div x-show="activeTab === 'suclar'" class="bg-white shadow sm:rounded-lg p-6" style="display: none;"
                 x-data="{ editMode: false, formAction: '<?php echo e(route('admin.disiplin.settings.behavior.store')); ?>', method: 'POST', kategori: '', tanim: '', yasal: '' }">
                
                <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-bold text-gray-700" x-text="editMode ? 'Suç Tanımını Düzenle' : 'Yeni Suç Tanımı Ekle'"></h4>
                        <button x-show="editMode" @click="editMode=false; method='POST'; formAction='<?php echo e(route('admin.disiplin.settings.behavior.store')); ?>'; kategori=''; tanim=''; yasal=''" class="text-xs text-red-500 underline">İptal ve Yeni Ekle</button>
                    </div>
                    
                    <form :action="formAction" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_method" :value="method">
                        
                        <div class="md:col-span-3">
                            <label class="text-xs font-bold text-gray-500">Kategori</label>
                            <select name="category_id" x-model="kategori" class="w-full mt-1 text-sm rounded border-gray-300" required>
                                <option value="">Seçiniz...</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>"><?php echo e($c->ad); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="md:col-span-6">
                            <label class="text-xs font-bold text-gray-500">Suç Tanımı</label>
                            <input type="text" name="tanim" x-model="tanim" class="w-full mt-1 text-sm rounded border-gray-300" required placeholder="Örn: İzinsiz işi terk etmek">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500">Dayanak (Madde)</label>
                            <input type="text" name="yasal_dayanak" x-model="yasal" class="w-full mt-1 text-sm rounded border-gray-300" placeholder="Md. 25/II">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded text-sm font-bold hover:bg-indigo-700" x-text="editMode ? 'GÜNCELLE' : 'EKLE'"></button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 w-10">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 w-40">Kategori</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Suç Tanımı</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 w-32">Dayanak</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 w-24">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $behaviors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-xs text-gray-400"><?php echo e($loop->iteration); ?></td>
                                <td class="px-4 py-2 text-xs font-bold text-indigo-600"><?php echo e($b->category->ad ?? '-'); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-800"><?php echo e($b->tanim); ?></td>
                                <td class="px-4 py-2 text-xs text-gray-500 italic"><?php echo e($b->yasal_dayanak); ?></td>
                                <td class="px-4 py-2 text-right flex justify-end gap-2">
                                    <button @click="editMode=true; method='PUT'; formAction='/admin/disiplin-ayarlari/davranis/<?php echo e($b->id); ?>'; kategori='<?php echo e($b->category_id); ?>'; tanim='<?php echo e(addslashes($b->tanim)); ?>'; yasal='<?php echo e($b->yasal_dayanak); ?>'" 
                                            class="text-blue-500 hover:text-blue-700 text-xs font-bold">DÜZENLE</button>
                                    
                                    <form action="<?php echo e(route('admin.disiplin.settings.behavior.delete', $b->id)); ?>" method="POST" onsubmit="return confirm('Silinecek?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold">SİL</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div x-show="activeTab === 'katsayilar'" class="bg-white shadow sm:rounded-lg p-6" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                     
                    <div class="bg-blue-50/50 p-4 rounded border border-blue-100">
                        <h4 class="font-bold text-gray-700 mb-4">Tekrar Katsayıları</h4>
                        <div class="space-y-2 mb-4">
                            <?php $__currentLoopData = $multipliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <form action="<?php echo e(route('admin.disiplin.settings.multiplier.store')); ?>" method="POST" class="flex items-center justify-between bg-white p-2 rounded border shadow-sm">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="tekrar_sayisi" value="<?php echo e($m->tekrar_sayisi); ?>">
                                    <span class="text-xs font-bold text-gray-600"><?php echo e($m->tekrar_sayisi); ?>. Tekrar</span>
                                    <div class="flex gap-1 items-center">
                                        <span class="text-gray-400 text-xs">x</span>
                                        <input type="number" step="0.01" name="katsayi" value="<?php echo e($m->katsayi); ?>" class="w-16 text-sm border-gray-300 rounded text-center font-bold">
                                        <button class="text-green-600 hover:text-green-800 text-xs px-2">💾</button>
                                    </div>
                                </form>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div class="bg-yellow-50/50 p-4 rounded border border-yellow-100">
                        <h4 class="font-bold text-gray-700 mb-4">Ceza Puan Skalası</h4>
                        <ul class="text-xs bg-white border rounded divide-y mb-4 shadow-sm">
                            <?php $__currentLoopData = $scales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex justify-between p-2 hover:bg-gray-50">
                                    <span class="font-mono font-bold text-gray-600"><?php echo e($s->min_puan); ?> - <?php echo e($s->max_puan); ?></span>
                                    <span class="font-bold text-gray-800"><?php echo e($s->ceza_adi); ?></span>
                                    <form action="<?php echo e(route('admin.disiplin.settings.scale.delete', $s->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-500 font-bold px-2">x</button>
                                    </form>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <form action="<?php echo e(route('admin.disiplin.settings.scale.store')); ?>" method="POST" class="flex gap-2">
                            <?php echo csrf_field(); ?>
                            <input type="number" name="min_puan" placeholder="Min" class="w-16 text-xs rounded border-gray-300">
                            <input type="number" name="max_puan" placeholder="Max" class="w-16 text-xs rounded border-gray-300">
                            <input type="text" name="ceza_adi" placeholder="Ceza Adı" class="flex-1 text-xs rounded border-gray-300">
                            <button type="submit" class="bg-yellow-500 text-white px-3 rounded text-xs font-bold">EKLE</button>
                        </form>
                    </div>
                </div>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/settings.blade.php ENDPATH**/ ?>