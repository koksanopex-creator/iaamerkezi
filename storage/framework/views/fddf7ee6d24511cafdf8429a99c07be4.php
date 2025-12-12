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
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">
                <?php echo e(__('Sistem Konfigürasyonu')); ?>

            </h2>
            <p class="text-sm text-gray-500">Platformun tüm işleyiş kurallarını, yetkilerini ve iletişim şablonlarını buradan yönetebilirsiniz.</p>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8" x-data="{ activeTab: 'genel' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            
            <?php if(session('success')): ?>
                <div class="mb-8 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3 animate-fade-in-down">
                    <div class="bg-emerald-100 p-2 rounded-full text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-800">Başarılı!</h4>
                        <p class="text-sm text-emerald-700"><?php echo e(session('success')); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin.sistem-ayarlari.update')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
                    <nav class="flex overflow-x-auto scrollbar-hide">
                        
                        
                        <button type="button" @click="activeTab = 'genel'" 
                            :class="activeTab === 'genel' ? 'bg-indigo-50 text-indigo-700 border-b-2 border-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                            class="flex-1 py-4 px-6 text-sm font-bold text-center whitespace-nowrap transition-all duration-200 flex items-center justify-center gap-2 min-w-[140px]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Genel
                        </button>

                        
                        <button type="button" @click="activeTab = 'finans'" 
                            :class="activeTab === 'finans' ? 'bg-amber-50 text-amber-700 border-b-2 border-amber-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                            class="flex-1 py-4 px-6 text-sm font-bold text-center whitespace-nowrap transition-all duration-200 flex items-center justify-center gap-2 min-w-[140px]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Puanlama
                        </button>

                        
                        <button type="button" @click="activeTab = 'disiplin'" 
                            :class="activeTab === 'disiplin' ? 'bg-red-50 text-red-700 border-b-2 border-red-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                            class="flex-1 py-4 px-6 text-sm font-bold text-center whitespace-nowrap transition-all duration-200 flex items-center justify-center gap-2 min-w-[140px]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            Disiplin Yetki
                        </button>

                        
                        <button type="button" @click="activeTab = 'musteri'" 
                            :class="activeTab === 'musteri' ? 'bg-pink-50 text-pink-700 border-b-2 border-pink-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                            class="flex-1 py-4 px-6 text-sm font-bold text-center whitespace-nowrap transition-all duration-200 flex items-center justify-center gap-2 min-w-[140px]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Müşteri E-posta
                        </button>

                        
                        <button type="button" @click="activeTab = 'bildirim'" 
                            :class="activeTab === 'bildirim' ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                            class="flex-1 py-4 px-6 text-sm font-bold text-center whitespace-nowrap transition-all duration-200 flex items-center justify-center gap-2 min-w-[140px]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            İç Bildirimler
                        </button>
                    </nav>
                </div>

                
                <div class="space-y-6">

                    
                    <div x-show="activeTab === 'genel'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            
                            
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                                    <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                                    <h3 class="text-base font-bold text-gray-900">Marka ve Görünüm</h3>
                                </div>
                                <div class="p-6">
                                    <label for="site_logo" class="block text-sm font-bold text-gray-700 mb-2">Site Logosu</label>
                                    <?php if($logo && $logo->value): ?>
                                        <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg inline-block shadow-inner">
                                            <img src="<?php echo e(asset('storage/' . $logo->value)); ?>" alt="Mevcut Logo" class="h-12 object-contain">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="site_logo" id="site_logo" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer">
                                </div>
                            </div>

                            
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <h3 class="text-base font-bold text-gray-900">Kullanıcı Erişimi</h3>
                                </div>
                                <div class="p-6">
                                    <label class="block text-sm font-bold text-gray-700 mb-3">Yeni Kayıt Onay Sistemi</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 w-full transition <?php echo e(($kayitOnay && $kayitOnay->value == 1) || !$kayitOnay ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500' : ''); ?>">
                                            <input type="radio" name="kayit_onay_sistemi" value="1" <?php echo e(($kayitOnay && $kayitOnay->value == 1) || !$kayitOnay ? 'checked' : ''); ?> class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <div>
                                                <span class="block text-sm font-bold text-gray-900">Aktif (Onay Gerekli)</span>
                                                <span class="text-xs text-gray-500">Manuel yönetici onayı</span>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 w-full transition <?php echo e($kayitOnay && $kayitOnay->value == 0 ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500' : ''); ?>">
                                            <input type="radio" name="kayit_onay_sistemi" value="0" <?php echo e($kayitOnay && $kayitOnay->value == 0 ? 'checked' : ''); ?> class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <div>
                                                <span class="block text-sm font-bold text-gray-900">Pasif (Otomatik)</span>
                                                <span class="text-xs text-gray-500">Anında erişim</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="activeTab === 'finans'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            
                            
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="bg-amber-500 px-6 py-4 border-b border-amber-600 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    <h3 class="text-base font-bold text-white">Puan Değerleri</h3>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        
                                        
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Standart Öneri Puanı</label>
                                            <p class="text-xs text-gray-400 mb-2">Onay sırasında varsayılan puan değeri</p>
                                            <input type="number" name="standart_puan" value="<?php echo e(old('standart_puan', $standartPuan->value ?? 100)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 font-mono font-bold text-gray-700 p-3">
                                        </div>

                                        
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Müşteri Şikayeti Giriş Puanı</label>
                                            <p class="text-xs text-gray-400 mb-2">Yeni şikayet eklendiğinde verilecek puan</p>
                                            <input type="number" name="musteri_sikayeti_standart_puan" value="<?php echo e(old('musteri_sikayeti_standart_puan', $musteriSikayetiPuan->value ?? 0)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 font-mono font-bold text-gray-700 p-3">
                                        </div>

                                        
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Şikayet Çözüm Puanı Çarpanı</label>
                                            <p class="text-xs text-gray-400 mb-2">Şikayet çözümünde (Etki + Karmaşıklık) puanını çarpan katsayı.</p>
                                            <input type="number" name="musteri_sikayeti_cozum_carpan" value="<?php echo e(old('musteri_sikayeti_cozum_carpan', $musteriSikayetiCozumCarpan->value ?? 10)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 font-mono font-bold text-gray-700 p-3">
                                        </div>

                                        
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kurulun Atadığı Şikayet Puanı</label>
                                            <p class="text-xs text-gray-400 mb-2">"Müşteri Şikayeti Kurulu" bir takıma atama yaptığında otomatik verilecek puan.</p>
                                            <input type="number" name="kurul_default_puan" value="<?php echo e(old('kurul_default_puan', $kurulDefaultPuan->value ?? 0)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 font-mono font-bold text-gray-700 p-3">
                                        </div>

                                    </div>
                                </div>
                            </div>

                            
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-fit">
                                <div class="bg-emerald-500 px-6 py-4 border-b border-emerald-600 flex items-center gap-2">
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                    <h3 class="text-base font-bold text-white">Finansal Ayarlar</h3>
                                </div>
                                <div class="p-6">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kullanılabilir Para Birimleri</label>
                                    <p class="text-xs text-gray-500 mb-3">Virgül (,) ile ayırarak giriniz (Örn: TL,USD,EUR)</p>
                                    <input type="text" name="para_birimleri" value="<?php echo e(old('para_birimleri', $paraBirimleri->value ?? 'TL,USD,EUR')); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="TL,USD,EUR">
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="activeTab === 'disiplin'" style="display: none;">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-red-600 to-rose-700 px-6 py-5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-white">Global Disiplin Yetkisi</h3>
                                        <p class="text-xs text-rose-100 opacity-90">Hangi bölümler tüm fabrikaya tutanak tutabilir?</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                                    <div class="space-y-2 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                                        <?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:border-red-300 transition-all">
                                                <div class="flex items-center gap-3">
                                                    
                                                    <input type="checkbox" name="disciplinary_auth[<?php echo e($bolum->id); ?>][global]" value="1" 
                                                        <?php echo e($bolum->is_disciplinary_global ? 'checked' : ''); ?>

                                                        class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500 transition cursor-pointer">
                                                    <span class="text-sm font-bold text-gray-700"><?php echo e($bolum->ad); ?></span>
                                                </div>
                                                
                                                <?php if($bolum->is_disciplinary_global): ?>
                                                    <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold border border-red-200">
                                                        Tüm Fabrika
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full border border-gray-200">
                                                        Sadece Kendi Bölümü
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    
                                    <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-700 flex gap-2">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <div>
                                            <strong>Bilgi:</strong> "Tüm Fabrika" yetkisi verilen bölümler (Örn: İSG), herhangi bir personele tutanak tutabilir. Ancak listede <u>sadece kendi birimlerinin oluşturduğu</u> tutanakları görürler. Diğer bölümlerin iç işlerini göremezler.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="activeTab === 'musteri'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-pink-500 px-6 py-4 border-b border-pink-600 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                    <h3 class="text-base font-bold text-white">Otomatik E-posta Şablonları</h3>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600 bg-white px-3 py-1 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="font-semibold">Hedef Yanıt:</span>
                                    <input type="number" name="sikayet_response_time_hours" value="<?php echo e(old('sikayet_response_time_hours', $settings->get('sikayet_response_time_hours')->value ?? 72)); ?>" class="w-16 py-0 px-2 border-none text-right font-bold text-pink-600 focus:ring-0">
                                    <span>Saat</span>
                                </div>
                            </div>
                            
                            <div class="p-6 space-y-8">
                                
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-1.5 h-6 bg-pink-500 rounded-full"></span>
                                        <h4 class="font-bold text-gray-800">Şikayet Alındı Bildirimi</h4>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Konu</label>
                                            <input type="text" name="sikayet_onay_email_subject" value="<?php echo e(old('sikayet_onay_email_subject', $settings->get('sikayet_onay_email_subject')->value ?? '')); ?>" class="w-full border-gray-300 rounded-lg text-sm focus:ring-pink-500 focus:border-pink-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">İçerik</label>
                                            <textarea name="sikayet_onay_email_body" rows="4" class="w-full border-gray-300 rounded-lg text-sm focus:ring-pink-500 focus:border-pink-500"><?php echo e(old('sikayet_onay_email_body', $settings->get('sikayet_onay_email_body')->value ?? '')); ?></textarea>
                                            <p class="text-[10px] text-gray-400 mt-1 font-mono">Variables: {musteri_adi}, {takip_linki}, {sifre}</p>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-gray-100">

                                
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-1.5 h-6 bg-green-500 rounded-full"></span>
                                        <h4 class="font-bold text-gray-800">Çözüm Bildirimi</h4>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Konu</label>
                                            <input type="text" name="sikayet_cozum_email_subject" value="<?php echo e(old('sikayet_cozum_email_subject', $settings->get('sikayet_cozum_email_subject')->value ?? '')); ?>" class="w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">İçerik</label>
                                            <textarea name="sikayet_cozum_email_body" rows="4" class="w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"><?php echo e(old('sikayet_cozum_email_body', $settings->get('sikayet_cozum_email_body')->value ?? '')); ?></textarea>
                                            <p class="text-[10px] text-gray-400 mt-1 font-mono">Variables: {musteri_adi}, {sikayet_konusu}, {cozum_tarihi}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="activeTab === 'bildirim'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        
                        
                        <?php echo $__env->make('admin.ayarlar._mail_notification_settings', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-bold text-gray-800">Genel Yedek E-postası</label>
                                <p class="text-xs text-gray-500 mt-1">Sistem bildirimleri için genel yedek e-posta adresi.</p>
                            </div>
                            <input type="email" name="sikayet_admin_notification_email" value="<?php echo e(old('sikayet_admin_notification_email', $settings->get('sikayet_admin_notification_email')->value ?? '')); ?>" class="w-1/2 border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="admin@example.com">
                        </div>
                    </div>

                </div>

                
                <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg z-50 md:sticky md:bottom-4 md:rounded-xl md:mx-auto md:max-w-7xl md:mb-4">
                    <div class="flex justify-between items-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <span class="text-xs text-gray-500 hidden md:inline-block">Değişiklikleri kaydetmeyi unutmayın.</span>
                        <button type="submit" class="w-full md:w-auto inline-flex justify-center items-center px-8 py-3 bg-gray-900 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition ease-in-out duration-150 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Tüm Ayarları Kaydet
                        </button>
                    </div>
                </div>

            </form>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/ayarlar/index.blade.php ENDPATH**/ ?>