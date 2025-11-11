<?php
    // Controller'dan gelen $settings (collection) ve $users, $roles (collection) değişkenlerini kullandığımızı varsayalım.
    $notifyUserIds = explode(',', $settings->get('sikayet_notify_user_ids')?->value ?? '');
    $notifyRoleIds = explode(',', $settings->get('sikayet_notify_role_ids')?->value ?? '');
    $notifyManualEmails = $settings->get('sikayet_notify_manual_emails')?->value ?? '';
    $atamaNotifyManualEmails = $settings->get('sikayet_atama_notify_manual_emails')?->value ?? '';
?>

<div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 lg:col-span-2">
    <div class="bg-gradient-to-r from-cyan-500 to-sky-600 p-4 md:p-6">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h3 class="text-lg md:text-xl font-bold text-white">Şikayet Bildirim Ayarları (Yönetici/İlgili Birim)</h3>
        </div>
    </div>
    <div class="p-4 md:p-6 space-y-5">

        <h4 class="text-md font-semibold text-gray-800 mb-1 border-b pb-2">1. Yeni Müşteri Şikayeti Bildirimi</h4>
        <p class="text-xs md:text-sm text-gray-500 -mt-3 mb-3">Sisteme yeni bir şikayet düştüğünde kimlere bildirim gitsin?</p>

        <div>
            <label for="sikayet_notify_role_ids" class="block text-sm font-semibold text-gray-700 mb-1">Bildirim Gönderilecek Roller</label>
            <p class="text-xs md:text-sm text-gray-500 mb-3">Bu rollerdeki tüm kullanıcılara e-posta gider. (Çoklu seçim için Ctrl/Cmd)</p>
            <select multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors text-sm h-32" 
                    id="sikayet_notify_role_ids" name="sikayet_notify_role_ids[]">
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($role->id); ?>" <?php if(in_array($role->id, $notifyRoleIds)): ?> selected <?php endif; ?>>
                        <?php echo e($role->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="pt-4 border-t">
            <label for="sikayet_notify_user_ids" class="block text-sm font-semibold text-gray-700 mb-1">Ek Bildirim Gönderilecek Kullanıcılar</label>
            <p class="text-xs md:text-sm text-gray-500 mb-3">Rollerden bağımsız, spesifik olarak bildirim alacak kullanıcıları seçin. (Çoklu seçim için Ctrl/Cmd)</p>
            <select multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors text-sm h-40" 
                    id="sikayet_notify_user_ids" name="sikayet_notify_user_ids[]">
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($user->id); ?>" <?php if(in_array($user->id, $notifyUserIds)): ?> selected <?php endif; ?>>
                        <?php echo e($user->name); ?> (<?php echo e($user->email); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="pt-4 border-t">
            <label for="sikayet_notify_manual_emails" class="block text-sm font-semibold text-gray-700 mb-1">Ek Bildirim E-postaları (Yeni Şikayet)</label>
            <p class="text-xs md:text-sm text-gray-500 mb-3">Sistemde kayıtlı olmasalar bile bildirim alacak e-posta adresleri. Virgül (,) veya alt satır ile ayırın.</p>
            <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors text-sm resize-y"
                      id="sikayet_notify_manual_emails" name="sikayet_notify_manual_emails" rows="3"><?php echo e($notifyManualEmails); ?></textarea>
        </div>

        <hr class="my-4">

        <h4 class="text-md font-semibold text-gray-800 mb-1 border-b pb-2">2. Şikayet Atama Bildirimi</h4>
        <p class="text-xs md:text-sm text-gray-500 -mt-3 mb-3">Bir şikayet çözüm ekibine atandığında kime bildirim gitsin?</p>
        <p class="text-xs md:text-sm text-blue-700 bg-blue-50 p-3 rounded-lg -mt-2 mb-3"><strong>Not:</strong> Çözüm ekibinin üyelerine zaten otomatik olarak bildirim gönderilecektir. Burası, bu ekip *dışında* ekstra bildirim alacak kişiler içindir.</p>
        
        <div>
            <label for="sikayet_atama_notify_manual_emails" class="block text-sm font-semibold text-gray-700 mb-1">Ek Bildirim E-postaları (Atama)</label>
            <p class="text-xs md:text-sm text-gray-500 mb-3">Atama yapıldığında, atanan ekip *dışında* bilgilendirilmesini istediğiniz e-posta adresleri. (Virgül veya alt satır ile ayırın)</p>
            <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors text-sm resize-y"
                      id="sikayet_atama_notify_manual_emails" name="sikayet_atama_notify_manual_emails" rows="3"><?php echo e($atamaNotifyManualEmails); ?></textarea>
        </div>

    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/ayarlar/_mail_notification_settings.blade.php ENDPATH**/ ?>