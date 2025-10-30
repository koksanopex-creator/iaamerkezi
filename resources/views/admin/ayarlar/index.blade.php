<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                {{ __('Sistem Ayarları') }}
            </h2>
            <p class="text-sm md:text-base text-gray-600">Sitenin genel ayarlarını ve işleyişini buradan yönetin.</p>
        </div>
    </x-slot>

    <div class="py-4 md:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fade-in">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm md:text-base text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.sistem-ayarlari.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                    
                    <!-- Marka Ayarları -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-4 md:p-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg md:text-xl font-bold text-white">Marka Ayarları</h3>
                            </div>
                        </div>
                        <div class="p-4 md:p-6 space-y-4">
                            <div>
                                <label for="site_logo" class="block text-sm font-semibold text-gray-700 mb-2">Site Logosu</label>
                                <p class="text-xs md:text-sm text-gray-500 mb-3">Navigasyon çubuğunda görünecek logo (SVG, PNG)</p>
                                @if($logo && $logo->value)
                                    <div class="mb-4 p-4 bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg inline-block">
                                        <img src="{{ Storage::url($logo->value) }}" alt="Mevcut Logo" class="h-12 object-contain">
                                    </div>
                                @endif
                                <input type="file" name="site_logo" id="site_logo" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:transition-colors file:cursor-pointer border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Finans Ayarları -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-4 md:p-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg md:text-xl font-bold text-white">Finans Ayarları</h3>
                            </div>
                        </div>
                        <div class="p-4 md:p-6 space-y-4">
                            <div>
                                <label for="para_birimleri" class="block text-sm font-semibold text-gray-700 mb-2">Kullanılabilir Para Birimleri</label>
                                <p class="text-xs md:text-sm text-gray-500 mb-3">Virgül (,) ile ayırarak giriniz (Örn: TL,USD,EUR)</p>
                                <input type="text" name="para_birimleri" id="para_birimleri" 
                                       value="{{ old('para_birimleri', $paraBirimleri->value ?? 'TL,USD,EUR') }}" 
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm md:text-base"
                                       placeholder="TL,USD,EUR">
                            </div>
                        </div>
                    </div>

                    <!-- Puanlama Ayarları -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-4 md:p-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg md:text-xl font-bold text-white">Puanlama Ayarları</h3>
                            </div>
                        </div>
                        <div class="p-4 md:p-6 space-y-4">
                            <div>
                                <label for="standart_puan" class="block text-sm font-semibold text-gray-700 mb-2">Standart Öneri Puanı</label>
                                <p class="text-xs md:text-sm text-gray-500 mb-3">Onay sırasında varsayılan puan değeri</p>
                                <input type="number" name="standart_puan" id="standart_puan" 
                                       value="{{ old('standart_puan', $standartPuan->value ?? 100) }}" 
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors text-sm md:text-base"
                                       placeholder="100">
                            </div>
                            <div>
                                <label for="musteri_sikayeti_standart_puan" class="block text-sm font-semibold text-gray-700 mb-2">Müşteri Şikayeti Giriş Puanı</label>
                                <p class="text-xs md:text-sm text-gray-500 mb-3">Yeni şikayet eklendiğinde verilecek puan</p>
                                <input type="number" name="musteri_sikayeti_standart_puan" id="musteri_sikayeti_standart_puan"
                                       value="{{ old('musteri_sikayeti_standart_puan', $musteriSikayetiPuan->value ?? 0) }}"
                                       min="0"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors text-sm md:text-base"
                                       placeholder="0">
                                @error('musteri_sikayeti_standart_puan') 
                                    <span class="text-red-500 text-xs md:text-sm mt-1 block">{{ $message }}</span> 
                                @enderror
                            </div>

                            {{-- === YENİ: Şikayet Çözüm Puanı Çarpanı === --}}
                            <div>
                                <label for="musteri_sikayeti_cozum_carpan" class="block text-sm font-semibold text-gray-700 mb-2">Şikayet Çözüm Puanı Çarpanı</label>
                                <p class="text-xs md:text-sm text-gray-500 mb-3">Şikayet çözümünde (Etki + Karmaşıklık) puanını çarpan katsayı.</p>
                                <input type="number" name="musteri_sikayeti_cozum_carpan" id="musteri_sikayeti_cozum_carpan"
                                       value="{{ old('musteri_sikayeti_cozum_carpan', $musteriSikayetiCozumCarpan->value ?? 10) }}"
                                       min="1"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors text-sm md:text-base"
                                       placeholder="10">
                                @error('musteri_sikayeti_cozum_carpan') 
                                    <span class="text-red-500 text-xs md:text-sm mt-1 block">{{ $message }}</span> 
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Kullanıcı Ayarları -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-4 md:p-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg md:text-xl font-bold text-white">Kullanıcı Ayarları</h3>
                            </div>
                        </div>
                        <div class="p-4 md:p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Yeni Kullanıcı Kayıt Onayı</label>
                                <p class="text-xs md:text-sm text-gray-500 mb-4">Bu ayar aktif ise, yeni kullanıcılar onay bekler</p>
                                <div class="space-y-3">
                                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 group">
                                        <input id="onay_aktif" name="kayit_onay_sistemi" type="radio" value="1" 
                                               {{ ($kayitOnay && $kayitOnay->value == 1) || !$kayitOnay ? 'checked' : '' }} 
                                               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <div class="ml-3 flex-1">
                                            <span class="block text-sm font-semibold text-gray-900 group-hover:text-blue-700">Aktif (Onay Gerekli)</span>
                                            <span class="block text-xs text-gray-500 mt-0.5">Manuel onay sistemi</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 group">
                                        <input id="onay_pasif" name="kayit_onay_sistemi" type="radio" value="0" 
                                               {{ $kayitOnay && $kayitOnay->value == 0 ? 'checked' : '' }} 
                                               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <div class="ml-3 flex-1">
                                            <span class="block text-sm font-semibold text-gray-900 group-hover:text-blue-700">Pasif (Otomatik Onay)</span>
                                            <span class="block text-xs text-gray-500 mt-0.5">Anında erişim</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================== YENİ E-POSTA AYAR KARTI ================== --}}
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 lg:col-span-2"> {{-- Geniş olması için lg:col-span-2 --}}
                <div class="bg-gradient-to-r from-pink-500 to-rose-600 p-4 md:p-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-white">Müşteri Şikayet E-posta Ayarları</h3>
                    </div>
                </div>
                <div class="p-4 md:p-6 space-y-5">

                    {{-- Yeni Şikayet Bildirim E-postası --}}
                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Yeni Şikayet Bildirimi (Müşteriye)</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="sikayet_onay_email_subject" class="block text-sm font-semibold text-gray-700 mb-1">E-posta Konusu</label>
                                <input type="text" name="sikayet_onay_email_subject" id="sikayet_onay_email_subject"
                                       value="{{ old('sikayet_onay_email_subject', $settings->get('sikayet_onay_email_subject')->value ?? 'Şikayetiniz Alınmıştır - Takip Bilgileriniz') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors text-sm"
                                       placeholder="Şikayetiniz Alınmıştır...">
                                @error('sikayet_onay_email_subject') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="sikayet_onay_email_body" class="block text-sm font-semibold text-gray-700 mb-1">E-posta İçeriği</label>
                                <textarea name="sikayet_onay_email_body" id="sikayet_onay_email_body" rows="6"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors text-sm resize-y"
                                          placeholder="Sayın {musteri_adi}, ...">{{ old('sikayet_onay_email_body', $settings->get('sikayet_onay_email_body')->value ?? "Sayın {musteri_adi},\n\nŞikayetiniz alınmıştır. Takip bilgileriniz aşağıdadır:\nTakip Linki: {takip_linki}\nŞifreniz: {sifre}\n\nTeşekkür ederiz.") }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Kullanılabilir değişkenler: <code>{musteri_adi}</code>, <code>{sikayet_konusu}</code>, <code>{takip_linki}</code>, <code>{sifre}</code></p>
                                @error('sikayet_onay_email_body') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Çözüm Bildirim E-postası --}}
                    <div class="pt-4 border-t">
                        <h4 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Çözüm Bildirimi (Müşteriye)</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="sikayet_cozum_email_subject" class="block text-sm font-semibold text-gray-700 mb-1">E-posta Konusu</label>
                                <input type="text" name="sikayet_cozum_email_subject" id="sikayet_cozum_email_subject"
                                       value="{{ old('sikayet_cozum_email_subject', $settings->get('sikayet_cozum_email_subject')->value ?? 'Şikayetiniz Çözümlenmiştir') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors text-sm"
                                       placeholder="Şikayetiniz Çözümlenmiştir">
                                @error('sikayet_cozum_email_subject') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                           </div>
                            <div>
                                <label for="sikayet_cozum_email_body" class="block text-sm font-semibold text-gray-700 mb-1">E-posta İçeriği</label>
                                <textarea name="sikayet_cozum_email_body" id="sikayet_cozum_email_body" rows="6"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors text-sm resize-y"
                                          placeholder="Sayın {musteri_adi}, ...">{{ old('sikayet_cozum_email_body', $settings->get('sikayet_cozum_email_body')->value ?? "Sayın {musteri_adi},\n\n'{sikayet_konusu}' konulu şikayetiniz çözümlenmiştir.\nÇözüm Tarihi: {cozum_tarihi}\n\nDetayları incelemek ve geri bildirimde bulunmak için: {takip_linki}\n\nTeşekkür ederiz.") }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Kullanılabilir değişkenler: <code>{musteri_adi}</code>, <code>{sikayet_konusu}</code>, <code>{cozum_tarihi}</code>, <code>{takip_linki}</code></p>
                                @error('sikayet_cozum_email_body') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                           </div>
                        </div>
                    </div>

                     {{-- Diğer Ayarlar --}}
                    <div class="pt-4 border-t">
                         <h4 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Diğer Ayarlar</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="sikayet_admin_notification_email" class="block text-sm font-semibold text-gray-700 mb-1">Yönetici Bildirim E-postası</label>
                                <input type="email" name="sikayet_admin_notification_email" id="sikayet_admin_notification_email"
                                       value="{{ old('sikayet_admin_notification_email', $settings->get('sikayet_admin_notification_email')->value ?? '') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors text-sm"
                                       placeholder="admin@example.com">
                                <p class="text-xs text-gray-500 mt-1">Yeni şikayet/geri bildirim gelince buraya e-posta gider.</p>
                                @error('sikayet_admin_notification_email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                           </div>
                             <div>
                                <label for="sikayet_response_time_hours" class="block text-sm font-semibold text-gray-700 mb-1">Hedef Yanıt Süresi (Saat)</label>
                                <input type="number" name="sikayet_response_time_hours" id="sikayet_response_time_hours"
                                       value="{{ old('sikayet_response_time_hours', $settings->get('sikayet_response_time_hours')->value ?? 72) }}"
                                       min="1"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors text-sm"
                                       placeholder="72">
                                <p class="text-xs text-gray-500 mt-1">İlk onay e-postasında müşteriye belirtilecek süre.</p>
                                @error('sikayet_response_time_hours') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                           </div>
                         </div>
                    </div>

                </div>
            </div>
            {{-- ================== E-POSTA KARTI SONU ================== --}}

            {{-- ================== YENİ YÖNETİCİ BİLDİRİM KARTI ================== --}}
        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 lg:col-span-2">
            <div class="bg-gradient-to-r from-cyan-500 to-sky-600 p-4 md:p-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        {{-- Bell Icon --}}
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold text-white">Yeni Şikayet Bildirim Ayarları (Yöneticiler)</h3>
                </div>
            </div>
            <div class="p-4 md:p-6 space-y-5">

                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-1">Bildirim Gönderilecek Sistem Kullanıcıları</h4>
                    <p class="text-xs md:text-sm text-gray-500 mb-3">Yeni bir müşteri şikayeti geldiğinde e-posta ile bilgilendirilecek mevcut sistem kullanıcılarını seçin. (Çoklu seçim için Ctrl/Cmd tuşuna basılı tutun)</p>
                    {{-- Çoklu Seçim Dropdown --}}
                    <select name="sikayet_notify_user_ids[]" id="sikayet_notify_user_ids" multiple
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors text-sm h-40">
                        {{-- Controller'dan gelen $users değişkenini kullanacağız --}}
                        @php
                            // Controller'dan gelen $settings ile seçili ID'leri alalım
                            $selectedUserIds = explode(',', $settings->get('sikayet_notify_user_ids')->value ?? '');
                        @endphp
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, $selectedUserIds) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('sikayet_notify_user_ids.*') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    @error('sikayet_notify_user_ids') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 border-t">
                    <h4 class="text-md font-semibold text-gray-800 mb-1">Bildirim Gönderilecek Manuel E-posta Adresleri</h4>
                    <p class="text-xs md:text-sm text-gray-500 mb-3">Sistemde kaydı olmayan ancak bilgilendirilmesini istediğiniz e-posta adreslerini girin (Her bir adresi yeni satıra veya virgülle ayırarak yazabilirsiniz).</p>
                    <textarea name="sikayet_notify_manual_emails" id="sikayet_notify_manual_emails" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors text-sm resize-y"
                              placeholder="ornek1@mail.com, ornek2@mail.com&#10;ornek3@mail.com">{{ old('sikayet_notify_manual_emails', $settings->get('sikayet_notify_manual_emails')->value ?? '') }}</textarea>
                     <p class="text-xs text-gray-500 mt-1">Geçerli e-posta formatında olmalıdır.</p>
                    @error('sikayet_notify_manual_emails') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
               </div>

            </div>
        </div>
        {{-- ================== BİLDİRİM KARTI SONU ================== --}}

                </div>

                <!-- Kaydet Butonu -->
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="w-full md:w-auto group relative inline-flex items-center justify-center gap-2 px-6 md:px-8 py-3 md:py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 active:scale-95 transition-all duration-200 ease-in-out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Ayarları Kaydet</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>