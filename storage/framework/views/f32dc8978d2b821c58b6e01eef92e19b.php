<?php if($case->status == 'odeme_bekliyor' && $case->payments->isEmpty() && auth()->id() == $case->created_by): ?>
    <div class="mb-4 text-right">
        <form action="<?php echo e(route('admin.arabuluculuk.revertToMediation', $case->id)); ?>" method="POST" onsubmit="return confirm('Emin misiniz?');">
            <?php echo csrf_field(); ?>
            <button type="submit" class="text-sm text-gray-500 underline hover:text-red-600">
                &larr; Dosyayı Düzenlemek İçin Geri Çek
            </button>
        </form>
    </div>
<?php endif; ?>

<?php if($case->mutabakat != 'anlasildi'): ?>
    <div class="text-center py-10">
        <div class="bg-orange-50 text-orange-800 p-4 rounded-lg inline-block">
            <p class="font-bold">Ödeme Ekranı Kapalı</p>
            <p class="text-sm mt-1">Ödeme ekranının açılması için sürecin "Anlaşma Sağlandı" olarak sonuçlandırılması gerekir.</p>
        </div>
    </div>
<?php else: ?>
    <div class="bg-green-50 p-6 rounded-lg border border-green-100">
        
        <div class="flex justify-between items-start mb-6 border-b border-green-200 pb-4">
            <div>
                <h3 class="font-bold text-green-900 text-lg">Finansal İşlemler</h3>
                <p class="text-xs text-green-700">Ödeme planı ve doğrulama ekranı.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-end gap-2">
                <?php $dekont = $case->files->where('doc_type', 'dekont')->where('is_active', true)->last(); ?>
                <?php if($dekont): ?>
                    <a href="<?php echo e(asset('storage/' . $dekont->dosya_yolu)); ?>" target="_blank" class="flex items-center gap-2 bg-white px-3 py-2 rounded border border-green-300 text-green-700 hover:bg-green-50 transition shadow-sm">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <div class="text-left"><p class="text-xs font-bold">Ödeme Dekontu</p><p class="text-[10px] text-gray-500">Görüntüle</p></div>
                    </a>
                <?php endif; ?>
                <?php $sonTutanak = $case->files->where('doc_type', 'arabuluculuk_son_tutanak')->where('is_active', true)->first(); ?>
                <?php if($sonTutanak): ?>
                    <a href="<?php echo e(asset('storage/' . $sonTutanak->dosya_yolu)); ?>" target="_blank" class="flex items-center gap-2 bg-white px-3 py-2 rounded border border-green-300 text-green-700 hover:bg-green-100 transition shadow-sm">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                        <div class="text-left"><p class="text-xs font-bold">Arabuluculuk Son Tutanağı</p><p class="text-[10px] text-gray-500">İncelemek için tıklayın</p></div>
                    </a>
                <?php else: ?>
                    <span class="text-xs text-red-600 font-bold bg-red-100 px-2 py-1 rounded self-center">DİKKAT: Son tutanak bulunamadı!</span>
                <?php endif; ?>
            </div>
        </div>
            
        <?php $mevcutOdeme = $case->payments->first(); ?> 
        <?php if(!$mevcutOdeme || !empty($mevcutOdeme->red_gerekcesi)): ?>
            <?php if(auth()->user()->hasRole('Superadmin') || auth()->user()->can('arabuluculuk.manage_payee')): ?>
                <?php if($errors->any()): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">Lütfen şu hataları düzeltin:</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($error); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form action="<?php echo e(route('admin.arabuluculuk.savePayment', $case->id)); ?>" method="POST" x-data="{ banka: '' }">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2 mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Ödeme Yapılacak Kişi
                            </label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="w-full sm:w-5/12 flex items-center gap-2 bg-indigo-50 px-4 py-3 rounded-lg border border-indigo-200">
                                    <label class="text-sm font-semibold text-indigo-700 whitespace-nowrap">Tip:</label>
                                    <select class="w-full bg-transparent border-none p-0 text-sm font-semibold text-gray-700 focus:ring-0 cursor-pointer" name="odeme_alici_tipi" id="odeme_alici_tipi" onchange="toggleAliciInput()">
                                        <option value="calisan" selected>Çalışana Öde</option>
                                        <option value="diger">Diğer / Avukat</option>
                                    </select>
                                </div>
                                
                                <input type="text" class="w-full sm:w-7/12 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent px-4 py-3 bg-gray-50" name="odenecek_kisi" id="odenecek_kisi_ad_soyad" value="<?php echo e($mevcutOdeme ? $mevcutOdeme->odenecek_kisi : ($case->calisan->name ?? '')); ?>" placeholder="Ad Soyad Giriniz..." readonly>
                            </div>
                            <div class="mt-2 flex items-start gap-2 text-xs text-gray-600 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                
                                <span id="input_aciklama">Ödeme varsayılan olarak ilgili personele (<?php echo e($case->calisan->name ?? 'Çalışan Bilgisi Yok'); ?>) yapılacaktır.</span>
                            </div>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ödenecek Tutar (Otomatik)</label>
                            <div class="relative">
                                <input type="text" value="<?php echo e(number_format($case->anlasilan_tutar, 2)); ?> TL" class="w-full bg-gray-100 border-gray-300 rounded text-sm font-bold text-gray-600 cursor-not-allowed" readonly>
                                <input type="hidden" name="tutar" value="<?php echo e($mevcutOdeme ? $mevcutOdeme->tutar : $case->anlasilan_tutar); ?>">
                            </div>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Banka</label>
                            <select name="banka_adi" x-model="banka" class="w-full border-gray-300 rounded text-sm focus:ring-green-500 focus:border-green-500" required>
                                <option value="">Banka Seçiniz...</option>
                                <?php $bankalar = ['Ziraat Bankası', 'Garanti BBVA', 'İş Bankası', 'Akbank', 'Yapı Kredi', 'Halkbank', 'Vakıfbank', 'QNB Finansbank', 'Denizbank', 'TEB']; ?>
                                <?php $__currentLoopData = $bankalar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($b); ?>" <?php echo e(($mevcutOdeme && $mevcutOdeme->banka_adi == $b) ? 'selected' : ''); ?>><?php echo e($b); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <option value="Diğer" <?php echo e(($mevcutOdeme && !in_array($mevcutOdeme->banka_adi, $bankalar)) ? 'selected' : ''); ?>>Diğer</option>
                            </select>
                            <div x-show="banka === 'Diğer'" class="mt-2" style="display: none;">
                                <input type="text" name="banka_adi_manuel" class="w-full border-gray-300 rounded text-sm placeholder-gray-400" placeholder="Banka adını yazınız...">
                            </div>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">IBAN (TR ile Başlayan)</label>
                            <input type="text" name="iban" maxlength="34" class="w-full border-gray-300 rounded text-sm font-mono uppercase focus:ring-green-500 focus:border-green-500" value="<?php echo e($mevcutOdeme ? $mevcutOdeme->iban : ''); ?>" placeholder="TR76 0000..." required oninput="this.value = this.value.toUpperCase()">
                            <p class="text-[10px] text-gray-500 mt-1">Boşluklu veya bitişik yazabilirsiniz.</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Son Ödeme Tarihi (Opsiyonel)</label>
                            <input type="date" name="son_odeme_tarihi" class="w-full border-gray-300 rounded text-sm focus:ring-green-500 focus:border-green-500">
                            <p class="text-[10px] text-gray-500 mt-1">Belirtilirse finans yetkilisi ekranında uyarı olarak görünecektir.</p>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-3 rounded font-bold shadow hover:bg-indigo-700 transition">Ödeme Planını Kaydet ve Finansa Gönder</button>
                </form>
            <?php else: ?>
                <div class="text-center p-4 bg-white rounded border border-gray-200">
                    <p class="text-red-500 text-sm font-bold">Ödeme planı henüz oluşturulmadı.</p>
                    <p class="text-xs text-gray-500">Hukuk birimi tarafından plan oluşturulduğunda burada görünecektir.</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Ödenecek Kişi</label>
                        <p class="text-gray-800 font-bold text-base"><?php echo e($case->payments->first()->odenecek_kisi); ?></p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Tutar</label>
                        <p class="text-gray-800 font-bold text-base"><?php echo e(number_format($case->anlasilan_tutar, 2)); ?> TL</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Banka & IBAN</label>
                        <p class="text-gray-800 text-sm"><?php echo e($case->payments->first()->banka_adi); ?></p>
                        <p class="text-gray-600 font-mono text-sm tracking-wide bg-gray-100 px-2 py-1 rounded inline-block mt-1"><?php echo e($case->payments->first()->iban); ?></p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Durum</label>
                        <?php if($case->payments->first()->odeme_durumu == 'odendi'): ?>
                            <span class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                ÖDENDİ (<?php echo e(\Carbon\Carbon::parse($case->payments->first()->odeme_tarihi)->format('d.m.Y')); ?>)
                            </span>
                        <?php else: ?>
                            <span class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                ÖDEME BEKLİYOR
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if($case->payments->first()->son_odeme_tarihi && $case->payments->first()->odeme_durumu != 'odendi'): ?>
                        <div class="col-span-2 mt-2">
                            <div class="flex items-center gap-2 p-2 bg-red-50 border border-red-200 rounded animate-pulse">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-red-700 font-bold text-sm">SON ÖDEME TARİHİ: <?php echo e(\Carbon\Carbon::parse($case->payments->first()->son_odeme_tarihi)->format('d.m.Y')); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if(auth()->user()->can('arabuluculuk.finance_pay') || auth()->user()->hasRole('Superadmin')): ?>
                <?php if($case->payments->first()->odeme_durumu == 'bekliyor'): ?>
                    <div class="border-t border-green-200 pt-4">
                        <p class="text-sm text-gray-600 mb-3 text-right">İşlemi tamamlamak için lütfen önce DEKONT yükleyiniz.</p>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('rejectForm').classList.toggle('hidden')" class="bg-red-600 text-white px-4 py-2 rounded font-bold hover:bg-red-700">Reddet / İade Et</button>
                            <form action="<?php echo e(route('admin.arabuluculuk.approvePayment', $case->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded font-bold hover:bg-green-800">Ödemeyi Onayla ve Son Kontrole İlet</button>
                            </form>
                        </div>
                        <div id="rejectForm" class="hidden mt-4 bg-red-50 p-4 rounded border border-red-200">
                            <form action="<?php echo e(route('admin.arabuluculuk.rejectPayment', $case->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <textarea name="reason" class="w-full border-red-300 rounded mb-2 text-sm" placeholder="Red gerekçesi (IBAN hatalı, Tutar yanlış vb.)..." required></textarea>
                                <div class="text-right">
                                    <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded text-sm font-bold">Gerekçeyi Kaydet ve Geri Gönder</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>


<?php if($case->status == 'son_onay_bekliyor' && (auth()->user()->can('arabuluculuk.final_check') || auth()->user()->hasRole('Superadmin'))): ?>
    <div class="bg-indigo-900 text-white p-6 rounded-lg mb-6 shadow-xl mt-6">
        <h3 class="font-bold text-xl mb-2">🏁 Son Kontrol ve Kapanış</h3>
        <p class="mb-4 text-indigo-200">Ödeme yapılmış ve dekont yüklenmiş. Lütfen son kontrolleri yapıp dosyayı kapatınız.</p>
        <form action="<?php echo e(route('admin.arabuluculuk.finalClose', $case->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full bg-white text-indigo-900 font-bold py-3 rounded hover:bg-gray-100 transition">Dosyayı Kapat ve Arşivle</button>
        </form>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabuluculuk/parcalar/sekme-finans-ve-odeme.blade.php ENDPATH**/ ?>