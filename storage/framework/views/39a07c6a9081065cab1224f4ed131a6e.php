<?php
    $isAdmin = Auth::check() && Auth::user()->hasRole('Superadmin');
    $direktorOnayiAktif = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->value('value');
    $isDirector = false;

    if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum) {
        if (Auth::check() && $iaa->musteriSikayeti->sikayetKategori->bolum->director_id == Auth::id()) {
            $isDirector = true;
        }
    }

    // Bitiş tarihi kontrolü (Modal her zaman çağrılabilsin istiyoruz, eski kısıtlamayı kaldırıyoruz)

    $durum = $iaa->musteriSikayeti->musteri_ek_sure_talep_durumu ?? null;
    $isComplaint = $iaa->musteriSikayeti != null;

    $isApprover = false;
    $canSeeEvaluating = false;
    if (auth()->check() && $isComplaint) {
        if ($iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum) {
            $isAdmin = auth()->user()->hasRole('Superadmin');
            $direktorOnayiSetting = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->value('value');
            $direktorOnayiAktifH = filter_var($direktorOnayiSetting, FILTER_VALIDATE_BOOLEAN);
            $isDirectorH = $iaa->musteriSikayeti->sikayetKategori->bolum->director_id == auth()->id();

            if (($direktorOnayiAktifH && $isDirectorH) || (!$direktorOnayiAktifH && $isAdmin) || ($isAdmin)) {
                $isApprover = true;
            }

            if ($isDirectorH || auth()->user()->hasRole('Direktör')) {
                $canSeeEvaluating = true;
            }
        }

        $isLeaderOrAdminHeader = auth()->check() && (($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || auth()->user()->hasRole('Superadmin') || ($isQualityManagerInterventionPower ?? false));

        $isAuthorizedQuality = auth()->user()->hasRole('Bölüm Kalite Yöneticisi') && ($isQualityManagerInterventionPower ?? false);
        if ($isLeaderOrAdminHeader || $isAuthorizedQuality || auth()->user()->hasRole('Kurul Üyesi') || auth()->user()->hasRole('Superadmin')) {
            $canSeeEvaluating = true;
        }
    }

?>

<?php if($iaa->musteriSikayeti): ?>
    
    <?php if($isLeaderOrAdminHeader && in_array($durum, [null, 'Reddedildi'])): ?>
        <div x-data="{ modalOpen: false }" @open-extension-modal.window="modalOpen = true" x-show="modalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4">

            <div @click.outside="modalOpen = false"
                class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-lg transform transition-all">

                
                <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex justify-between items-center">
                    <h3 class="text-orange-800 font-semibold flex items-center text-lg">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ek Süre Talep Et
                    </h3>

                    <div class="flex items-center space-x-2">
                        
                        <?php if(isset($ekSureLoglari) && $ekSureLoglari->count() > 0): ?>
                            <button type="button" @click="$dispatch('open-extension-history-modal')"
                                class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center bg-white px-2.5 py-1.5 rounded-lg border border-indigo-200 shadow-sm transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Geçmiş (<?php echo e($ekSureLoglari->count()); ?>)
                            </button>
                        <?php endif; ?>

                        
                        <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none ml-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <form action="<?php echo e(route('proje.talep.extension.request', $iaa->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Talep Edilen Gün</label>
                                <input type="number" name="gun_sayisi" min="1" max="30" value="1" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gerekçe</label>
                                <textarea name="aciklama" rows="3" required
                                    placeholder="Lütfen neden ek süreye ihtiyaç duyduğunuzu belirtin..."
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="modalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                İptal
                            </button>
                            <button type="submit"
                                class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm">
                                Talep Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>


    
    <?php if($durum == 'Talep Edildi' && (isset($canSeeEvaluating) && $canSeeEvaluating)): ?>
        <div x-data="{ modalOpen: false }" @open-evaluating-extension-modal.window="modalOpen = true" x-show="modalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4">

            <div @click.outside="modalOpen = false"
                class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
                <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                    <h3 class="text-blue-800 font-semibold flex items-center text-lg">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ek Süre Talebi Değerlendiriliyor
                    </h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 font-medium mb-3">
                        Gerekçe ve Süre:
                    </p>
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-600 mb-4">
                        <?php echo e($iaa->musteriSikayeti->ek_sure_talep_aciklamasi); ?>

                    </div>

                    <?php
                        $sonTarih = $iaa->musteriSikayeti->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($iaa->musteriSikayeti->musteri_cozum_son_tarihi) : null;
                    ?>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mt-4">
                        <p class="text-sm text-blue-700 font-medium">
                            Ek süre talebiniz henüz onaylanmadı veya reddedilmedi, eğer belirtilen süre içerisinde onay veya red
                            gelmezse proje bitiş tarihi "<?php echo e($sonTarih ? $sonTarih->format('d.m.Y H:i') : ''); ?>" olarak geçerli
                            olacaktır.
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($durum == 'Talep Edildi'): ?>
        <?php if(isset($isApprover) && $isApprover): ?>
            <div x-data="{ modalOpen: false }" @open-extension-approval-modal.window="modalOpen = true" x-show="modalOpen" x-cloak
                class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4">

                <div @click.outside="modalOpen = false" x-data="{ isRejecting: false, rejectReason: '' }"
                    class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all border-t-4 border-yellow-400">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-0.5 rounded shadow-sm mr-3">ONAY
                                BEKLİYOR</span>
                            Ek Süre Talebi Değerlendirme
                        </h3>
                        <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="mb-5 space-y-2">
                            <p class="text-sm"><strong>Talep Eden:</strong> <span
                                    class="text-gray-700"><?php echo e($iaa->atananTakim->lider->name ?? 'Belirsiz'); ?> (Takım Lideri)</span>
                            </p>
                            <p class="text-sm"><strong>Tarih:</strong> <span
                                    class="text-gray-700"><?php echo e($iaa->musteriSikayeti->updated_at->format('d.m.Y H:i')); ?></span></p>

                            <div class="mt-4">
                                <span class="block text-sm font-medium text-gray-700 mb-1">Gerekçe:</span>
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-800">
                                    <?php echo e($iaa->musteriSikayeti->ek_sure_talep_aciklamasi); ?>

                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <?php if($direktorOnayiAktif && $isDirector): ?>
                                <div class="flex-1 w-full" x-show="!isRejecting">
                                    <form action="<?php echo e(route('proje.talep.extension.director', $iaa->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit"
                                            class="w-full justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7">
                                                </path>
                                            </svg>
                                            Onayla & Süreyi Uzat
                                        </button>
                                    </form>
                                    <button type="button" @click="isRejecting = true"
                                        class="w-full mt-3 justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Reddet
                                    </button>
                                </div>
                                <div class="w-full" x-show="isRejecting" x-cloak>
                                    <form action="<?php echo e(route('proje.talep.extension.director', $iaa->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="reject">

                                        <label class="block text-sm font-medium text-red-700 mb-1">Reddetme Gerekçesi (Zorunlu)</label>
                                        <textarea name="ek_sure_red_nedeni" x-model="rejectReason" required rows="3"
                                            class="w-full border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-sm mb-3"
                                            placeholder="Lütfen talebi neden reddettiğinizi açıklayın..."></textarea>

                                        <div class="flex gap-2">
                                            <button type="button" @click="isRejecting = false; rejectReason = ''"
                                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                                                İptal
                                            </button>
                                            <button type="submit" :disabled="rejectReason.trim() === ''"
                                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                                Talebi Reddet
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            <?php elseif($isAdmin): ?>
                                <div class="flex-1 w-full" x-show="!isRejecting">
                                    <form action="<?php echo e(route('proje.talep.extension.superadmin', $iaa->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit"
                                            class="w-full justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7">
                                                </path>
                                            </svg>
                                            Superadmin: Onayla
                                        </button>
                                    </form>
                                    <button type="button" @click="isRejecting = true"
                                        class="w-full mt-3 justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Superadmin: Reddet
                                    </button>
                                </div>
                                <div class="w-full" x-show="isRejecting" x-cloak>
                                    <form action="<?php echo e(route('proje.talep.extension.superadmin', $iaa->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="reject">

                                        <label class="block text-sm font-medium text-red-700 mb-1">Reddetme Gerekçesi (Zorunlu)</label>
                                        <textarea name="ek_sure_red_nedeni" x-model="rejectReason" required rows="3"
                                            class="w-full border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-sm mb-3"
                                            placeholder="Lütfen talebi neden reddettiğinizi açıklayın..."></textarea>

                                        <div class="flex gap-2">
                                            <button type="button" @click="isRejecting = false; rejectReason = ''"
                                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                                                İptal
                                            </button>
                                            <button type="submit" :disabled="rejectReason.trim() === ''"
                                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                                Talebi Reddet
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    
    <?php if($durum == 'Onaylandı' && (isset($canSeeEvaluating) && $canSeeEvaluating)): ?>
        <div x-data="{ modalOpen: false }" @open-approved-extension-modal.window="modalOpen = true" x-show="modalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4">

            <div @click.outside="modalOpen = false"
                class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all border-t-4 border-green-500">
                <div class="bg-green-50 px-6 py-4 border-b border-green-100 flex justify-between items-center">
                    <h3 class="text-green-800 font-semibold flex items-center text-lg">
                        <svg class="h-5 w-5 mr-2 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Ek Süre Talebi Onaylandı
                    </h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 mb-4">
                        Süre uzatımı yapılarak projenin Bitiş Tarihi güncellenmiştir.
                    </p>
                    <?php if($iaa->musteriSikayeti->ek_sure_talep_aciklamasi): ?>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-600 italic">
                            Talep Edilen Gerekçe: "<?php echo e($iaa->musteriSikayeti->ek_sure_talep_aciklamasi); ?>"
                        </div>
                    <?php endif; ?>
                    <div class="mt-6 flex justify-end">
                        <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($durum == 'Reddedildi' && (isset($canSeeEvaluating) && $canSeeEvaluating)): ?>
        <div x-data="{ modalOpen: false }" @open-rejected-extension-modal.window="modalOpen = true" x-show="modalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4">

            <div @click.outside="modalOpen = false"
                class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all border-t-4 border-red-500">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                    <h3 class="text-red-800 font-semibold flex items-center text-lg">
                        <svg class="h-5 w-5 mr-2 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        Süre Uzatma Talebi Reddedildi
                    </h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 font-semibold mb-2">
                        Uyarı: Proje bitiş tarihi değişmemiştir.
                    </p>
                    <p class="text-sm text-gray-600 mb-4">
                        Lütfen mevcut süre zarfında projeyi tamamlayınız veya gerekirse yeni bir talep oluşturunuz.
                    </p>
                    <?php if($iaa->musteriSikayeti->ek_sure_red_nedeni): ?>
                        <div class="p-3 bg-red-50 rounded-lg border border-red-200 text-sm text-red-700 italic mb-2">
                            <strong>Red Gerekçesi:</strong> <?php echo e($iaa->musteriSikayeti->ek_sure_red_nedeni); ?>

                        </div>
                    <?php endif; ?>
                    <?php if($iaa->musteriSikayeti->ek_sure_talep_aciklamasi): ?>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-500 italic mt-3">
                            İlk Talep Açıklamanız: "<?php echo e($iaa->musteriSikayeti->ek_sure_talep_aciklamasi); ?>"
                        </div>
                    <?php endif; ?>
                    <div class="mt-6 flex justify-end">
                        <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_extension-request.blade.php ENDPATH**/ ?>