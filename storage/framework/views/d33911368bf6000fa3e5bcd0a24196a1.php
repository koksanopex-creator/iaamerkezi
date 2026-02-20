

<?php
    // Gerekli Değişkenler
    $currentUser = auth()->user();
    $isLeader = $iaa->atananTakim && $currentUser->id == $iaa->atananTakim->lider_user_id;

    // Durumlar
    $activeStatuses = ['Yeni', 'Atandı', 'calisiliyor', 'Devam Ediyor', 'Revize Ediliyor'];
    $isRequestPendingQuality = $iaa->durum == 'talep_onayi_bekliyor_kalite';
    $isRequestPendingDirector = $iaa->durum == 'talep_onayi_bekliyor_direktor';
    $isRequestPendingSuperadmin = $iaa->durum == 'talep_onayi_bekliyor_superadmin';
    $isRequestClosed = $iaa->durum == 'talep_olarak_kapatildi';

    // Roller
    $isQuality = $currentUser->hasRole('Bölüm Kalite Yöneticisi');
    $isDirector = $currentUser->hasRole('Direktör');
    $isSuperAdmin = $currentUser->hasRole('Superadmin');

    // Direktör Bölüm Kontrolü
    if ($isDirector && !$isSuperAdmin) {
        $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? null;
        $bolumDirectorId = $bolum ? $bolum->director_id : null;
        if ($bolumDirectorId && $currentUser->id != $bolumDirectorId) {
            $isDirector = false;
        }
    }
?>



<?php if($isLeader && in_array($iaa->durum, $activeStatuses)): ?>
    <div
        class="mb-6 bg-purple-50 border-l-4 border-purple-500 p-4 rounded-r-lg shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="text-sm font-bold text-purple-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                Bu Bir Taleptir
            </h4>
            <p class="text-xs text-purple-700 mt-1">
                Eğer bu proje bir "Hata/Şikayet" değil, müşterinin yeni bir isteği ise buradan bildirin.
            </p>
        </div>
        <button onclick="document.getElementById('modalTalepBildirim').classList.remove('hidden')"
            class="whitespace-nowrap px-4 py-2 bg-purple-600 text-white rounded-lg text-xs font-bold hover:bg-purple-700 transition shadow-md">
            Müşteri Talebidir
        </button>
    </div>

    
    <div id="modalTalepBildirim" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/75 transition-opacity"
                onclick="document.getElementById('modalTalepBildirim').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="<?php echo e(route('proje.markAsRequest', $iaa->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Müşteri Talebi Gerekçesi</h3>
                        <p class="text-sm text-gray-500 mb-4">Lütfen bunun neden bir şikayet değil de talep olduğunu
                            açıklayınız.</p>
                        <textarea name="gerekce" rows="4"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            placeholder="Gerekçenizi yazınız..." required></textarea>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Onaya
                            Gönder</button>
                        <button type="button"
                            onclick="document.getElementById('modalTalepBildirim').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">İptal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if($iaa->talep_gerekcesi): ?>
    <div
        class="mb-8 bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-2xl shadow-md overflow-hidden animate-in fade-in slide-in-from-top duration-500">
        <div class="bg-white/50 backdrop-blur-sm px-5 py-3 border-b border-indigo-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse"></div>
                <span class="text-xs font-black text-indigo-900 uppercase tracking-widest">Talep Dönüşüm Süreci</span>
            </div>
            <?php if($isRequestClosed): ?>
                <span class="bg-gray-600 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-sm">TALEP OLARAK
                    KAPATILDI</span>
            <?php else: ?>
                <span
                    class="bg-indigo-200 text-indigo-900 text-[10px] font-black px-3 py-1 rounded-full border border-indigo-300">ONAY
                    SÜRECİNDE</span>
            <?php endif; ?>
        </div>

        <div class="p-5 space-y-6">
            
            <div class="flex gap-4 relative">
                <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-indigo-200/50"></div>
                <div class="flex-shrink-0 relative">
                    <img src="<?php echo e($iaa->hataliBildirimLiderUser->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode('Lider') . '&color=7F9CF5&background=EBF4FF'); ?>"
                        class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" alt="TL">
                </div>
                <div class="flex-1 bg-white/60 p-3 rounded-xl border border-indigo-100 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-gray-900">Takım Lideri</span>
                        
                    </div>
                    <p class="text-xs font-bold text-indigo-600 mb-1">TALEP GEREKÇESİ:</p>
                    <p class="text-sm text-gray-700 italic leading-relaxed">"<?php echo e($iaa->talep_gerekcesi); ?>"</p>
                </div>
            </div>

            
            <?php if($iaa->talep_kalite_notu): ?>
                <div class="flex gap-4 relative">
                    <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-indigo-200/50"></div>
                    <div class="flex-shrink-0 relative">
                        
                        <div
                            class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center border-2 border-white shadow-sm">
                            <span class="text-xs font-bold text-yellow-600">KY</span>
                        </div>
                    </div>
                    <div class="flex-1 bg-white/60 p-3 rounded-xl border border-indigo-100 shadow-sm">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-gray-900">Kalite Yöneticisi</span>
                        </div>
                        <p class="text-xs font-bold text-yellow-600 mb-1">KALİTE ONAYI NOTU:</p>
                        <p class="text-sm text-gray-700 italic leading-relaxed">
                            "<?php echo e($iaa->talep_kalite_notu); ?>"</p>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($iaa->talep_direktor_notu): ?>
                <div class="flex gap-4 relative">
                    <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-indigo-200/50"></div>
                    <div class="flex-shrink-0 relative">
                        <div
                            class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center border-2 border-white shadow-sm">
                            <span class="text-xs font-bold text-pink-600">DR</span>
                        </div>
                    </div>
                    <div class="flex-1 bg-white/60 p-3 rounded-xl border border-indigo-100 shadow-sm">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-gray-900">Direktör</span>
                            <span
                                class="text-[10px] text-gray-400 font-medium"><?php echo e($iaa->talep_direktor_at ? \Carbon\Carbon::parse($iaa->talep_direktor_at)->format('d.m.Y H:i') : '-'); ?></span>
                        </div>
                        <p class="text-xs font-bold text-pink-600 mb-1">DİREKTÖR ONAY NOTU:</p>
                        <p class="text-sm text-gray-700 italic leading-relaxed">
                            "<?php echo e($iaa->talep_direktor_notu); ?>"</p>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($iaa->talep_admin_notu): ?>
                <div class="flex gap-4 relative">
                    <div class="flex-shrink-0 relative">
                        <div
                            class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center border-2 border-white shadow-sm">
                            <span class="text-xs font-bold text-red-600">SA</span>
                        </div>
                    </div>
                    <div class="flex-1 bg-white/60 p-3 rounded-xl border border-indigo-100 shadow-sm">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-gray-900">Üst Yönetim</span>
                        </div>
                        <p class="text-xs font-bold text-red-600 mb-1">YÖNETİM FİNAL NOTU:</p>
                        <p class="text-sm text-gray-700 italic leading-relaxed">
                            "<?php echo e($iaa->talep_admin_notu); ?>"</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>




<?php if($isRequestPendingQuality && ($isQuality || $isSuperAdmin)): ?>
    <div class="mb-6 bg-purple-50 border-2 border-purple-200 rounded-xl p-6 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 bg-purple-200 text-purple-800 text-[10px] font-bold px-3 py-1 rounded-bl-lg">
            KALİTE ONAYI BEKLİYOR</div>
        <h3 class="text-lg font-bold text-purple-900 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Talep Onayı
        </h3>
        <form action="<?php echo e(route('proje.decideRequestByQuality', $iaa->id)); ?>" method="POST" class="mt-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Yorumunuz / Notunuz</label>
                <textarea name="not" rows="2"
                    class="w-full border-purple-200 rounded-lg p-2 text-sm focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Onay veya red için bir açıklama yazabilirsiniz..."></textarea>
            </div>

            <div class="mt-6 flex gap-3">
                <input type="hidden" name="action" id="qualityRequestAction" value="approve">
                <button type="submit"
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 shadow-md transition">Onayla</button>
                <button type="submit"
                    onclick="document.getElementById('qualityRequestAction').value='reject'; return confirm('Reddetmek istediğinize emin misiniz?')"
                    class="px-6 py-2 bg-white text-red-600 border border-red-200 rounded-lg font-bold hover:bg-red-50 transition">Reddet</button>
            </div>
        </form>
    </div>
<?php endif; ?>


<?php if($isRequestPendingDirector && ($isDirector || $isSuperAdmin)): ?>
    <div class="mb-6 bg-slate-800 text-white rounded-xl p-6 shadow-xl relative overflow-hidden">
        <div
            class="absolute top-0 right-0 bg-indigo-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg uppercase">
            Son Onay: Direktör</div>
        <h3 class="text-xl font-bold flex items-center gap-2">
            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Talep Onayı - Son Karar
        </h3>
        <form action="<?php echo e(route('proje.decideRequestByDirector', $iaa->id)); ?>" method="POST" class="mt-4">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Direktör Karar Notu</label>
                <textarea name="not" rows="2"
                    class="w-full bg-white/5 border-white/20 rounded-lg p-2 text-sm text-white focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Onay notunuzu buraya yazabilirsiniz..." required></textarea>
            </div>
            <div class="flex gap-4">
                <input type="hidden" name="action" id="directorRequestAction" value="approve">
                <button type="submit"
                    class="px-6 py-3 bg-white text-slate-900 rounded-lg font-black hover:bg-gray-100 shadow-lg transition">ONAYLA</button>
                <button type="submit"
                    onclick="document.getElementById('directorRequestAction').value='reject'; return confirm('Reddetmek istediğinize emin misiniz?')"
                    class="px-6 py-3 bg-transparent border-2 border-red-400 text-red-400 rounded-lg font-bold hover:bg-red-400/10 transition">REDDET</button>
            </div>
        </form>
    </div>
<?php endif; ?>


<?php if($isRequestPendingSuperadmin && $isSuperAdmin): ?>
    <div class="mb-6 bg-indigo-900 text-white rounded-xl p-6 shadow-2xl relative overflow-hidden ring-4 ring-indigo-500/20">
        <div
            class="absolute top-0 right-0 bg-yellow-400 text-indigo-900 text-[10px] font-black px-4 py-1 rounded-bl-lg uppercase shadow-lg">
            Final Onay: Üst Yönetim</div>
        <h3 class="text-xl font-bold flex items-center gap-2">
            <svg class="w-7 h-7 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            Müşteri Talebi - Yönetim Onayı
        </h3>
        <form action="<?php echo e(route('proje.decideRequestBySuperadmin', $iaa->id)); ?>" method="POST" class="mt-4">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-indigo-300 uppercase mb-1">Yönetim Kapanış Notu</label>
                <textarea name="not" rows="2"
                    class="w-full bg-white/5 border-white/10 rounded-lg p-3 text-sm text-white focus:ring-yellow-400 focus:border-yellow-400"
                    placeholder="Kapanış kararını özetleyiniz..." required></textarea>
            </div>
            <div class="flex gap-4">
                <input type="hidden" name="action" id="superRequestAction" value="approve">
                <button type="submit"
                    class="px-8 py-3.5 bg-white text-indigo-900 rounded-xl font-black hover:bg-gray-100 shadow-xl transition hover:scale-105">ONAYLA
                    VE KAPAT</button>
                <button type="submit"
                    onclick="document.getElementById('superRequestAction').value='reject'; return confirm('Reddetmek istediğinize emin misiniz?')"
                    class="px-8 py-3.5 bg-indigo-800 text-red-400 border-2 border-red-500/30 rounded-xl font-bold hover:bg-red-500/10 transition">REDDET</button>
            </div>
        </form>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_talep-notification.blade.php ENDPATH**/ ?>