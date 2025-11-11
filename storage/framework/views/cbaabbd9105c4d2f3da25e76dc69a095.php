
<div class="flex items-center justify-end space-x-2">

    

    <?php if($type === 'atanmis'): ?>
        <a href="<?php echo e(route('proje.workspace.show', $iaa)); ?>" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">İlerleme İzle</a>
    <?php else: ?>
        <a href="<?php echo e(route('iaa.show', $iaa)); ?>" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700">İncele</a>
    <?php endif; ?>

    <?php if($type === 'onay'): ?>
        <button x-data @click="$dispatch('open-modal', 'onayla-modal-<?php echo e($iaa->id); ?>')" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">Onayla</button>
        <button x-data @click="$dispatch('open-modal', 'reddet-modal-<?php echo e($iaa->id); ?>')" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">Reddet</button>
    <?php endif; ?>
    
    <?php if(in_array($type, ['atanmis', 'havuz', 'reddedilmis', 'yonetici-onayi'])): ?>
        <?php if($iaa->puan): ?>
            <button x-data @click.prevent="$dispatch('open-modal', 'puan-duzenle-modal-<?php echo e($iaa->id); ?>')" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-slate-600 hover:bg-slate-700">Puanı Düzenle</button>
        <?php endif; ?>
        
        <?php if($iaa->gonderen_user_id): ?>
            <a href="<?php echo e(route('admin.iaa-yonetim.reassignForm', $iaa)); ?>" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700">Önereni Değiştir</a>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.iaa-yonetim.geriAl', $iaa)); ?>" class="inline"> <?php echo csrf_field(); ?> <?php echo method_field('patch'); ?> <button type="submit" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600">Geri Al</button></form>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.iaa-yonetim.destroy', $iaa)); ?>" class="inline" onsubmit="return confirm('Bu öneriyi kalıcı olarak silmek istediğinizden emin misiniz?');">
        <?php echo csrf_field(); ?> <?php echo method_field('delete'); ?>
        <button type="submit" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-black hover:bg-gray-800">Sil</button>
    </form>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/partials/actions.blade.php ENDPATH**/ ?>