<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profil Bilgileri') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Hesap profil bilgilerinizi ve e-posta adresinizi güncelleyin.") }}
        </p>
        @if($user->is_personnel && (empty($user->profile_photo_path) || empty($user->dogum_tarihi) || empty($user->telefon)))
        <p class="mt-2 text-xs text-indigo-600 bg-indigo-50 border border-indigo-100 p-2 rounded-md font-medium">
            💡 Sayfa üstünde çıkan profil tamamlama uyarısının kaybolması için <strong>Profil Fotoğrafı</strong>, <strong>Doğum Tarihi</strong> ve <strong>Telefon Numarası</strong> alanlarını doldurmanız gerekmektedir.
        </p>
        @endif
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <div x-data="{
        showBolumRequest: false
    }">

    @php
        $ssoUrl = rtrim(config('services.central_sso.url', 'http://localhost:8001'), '/');
        $ssoProfileUrl = $ssoUrl . '/profile';
    @endphp

    <div class="bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/20 border border-indigo-100/85 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-8 relative overflow-hidden">
        <!-- Decorative subtle background shape -->
        <div class="absolute -right-16 -top-16 w-36 h-36 rounded-full bg-indigo-600/5 blur-xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-36 h-36 rounded-full bg-purple-600/5 blur-xl pointer-events-none"></div>

        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 p-3 rounded-xl text-white shadow-md shadow-indigo-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-indigo-950">
                    {{ __('Merkezi Kimlik Yönetimi (SSO) Aktif') }}
                </h3>
                <p class="mt-1 text-xs text-indigo-800/80 leading-relaxed max-w-xl">
                    {{ __('Güvenliğiniz için Ad Soyad, E-posta ve Şifre bilgileriniz KÖKSAN Merkezi SSO sistemi tarafından yönetilmektedir. Bu alanlarda değişiklik yapmak için aşağıdaki butonu kullanarak Merkezi Profil sayfanıza geçiş yapabilirsiniz.') }}
                </p>
            </div>
        </div>
        <a href="{{ $ssoProfileUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xs font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 hover:scale-[1.02] active:scale-95 transition-all duration-200 shadow-md shadow-indigo-200/50 whitespace-nowrap">
            <span>{{ __('Merkezi Profil Sayfasına Git') }}</span>
            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Fotoğraf Yükleme --}}
        @php $hasPhoto = !empty($user->profile_photo_path); @endphp
        <div class="{{ ($user->is_personnel && !$hasPhoto) ? 'p-4 rounded-lg border-2 border-dashed border-indigo-200 bg-indigo-50/50 relative' : '' }}">
            @if($user->is_personnel && !$hasPhoto)
                <div class="absolute -top-3 left-4 bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Eksik Bilgi</div>
            @endif
            <x-input-label for="photo" :value="__('Profil Fotoğrafı')" class="{{ ($user->is_personnel && !$hasPhoto) ? 'font-bold text-indigo-900' : '' }}" />
            <div class="flex items-center gap-4 mt-2 mb-2">
                @if($user->profile_photo_path)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover border-2 border-indigo-50 shadow-sm flex-shrink-0">
                @else
                    <div class="h-16 w-16 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 p-0.5 shadow-sm flex-shrink-0">
                        <div class="h-full w-full rounded-full bg-white flex items-center justify-center text-xl font-bold text-indigo-600 uppercase">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                    </div>
                @endif
                <div class="flex-1">
                    <input type="file" name="photo" id="photo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                </div>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('photo')" />
        </div>

        {{-- İsim --}}
        <div>
            <x-input-label for="name" :value="__('Ad Soyad')" />
            <x-text-input id="name" type="text" class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed font-medium" :value="$user->name" disabled />
            <p class="text-[11px] text-gray-400 mt-1">Adınız ve soyadınız Merkezi SSO üzerinden yönetilmektedir.</p>
        </div>

        {{-- Bölüm / Firma (Müşteri için Bölüm gizlenir, Firma adı gösterilir) --}}
        @if($user->is_personnel)
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="bolum" :value="__('Bölümünüz')" />
                    @if(isset($bekleyenIstekler['bolum_degisikligi']))
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1.5 rounded-lg font-medium border border-yellow-200">Bölüm değişikliği talebiniz bekleniyor.</span>
                            <button type="button" onclick="document.getElementById('cancel-bolum-form').submit();" class="inline-flex items-center px-3 py-1.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-lg hover:bg-rose-100 hover:scale-[1.02] transition-all shadow-sm">İptal Et</button>
                        </div>
                    @else
                        <button type="button" @click="showBolumRequest = true" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100 hover:scale-[1.02] transition-all shadow-sm">Bölüm Değişikliği Talep Et</button>
                    @endif
                </div>
                <x-text-input id="bolum" type="text" class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed font-medium" :value="$user->bolum ? $user->bolum->ad : 'Belirtilmemiş'" disabled />
                <p class="text-[11px] text-gray-500 mt-1">Bölümünüzü doğrudan değiştiremezsiniz. Lütfen değişiklik talep edin.</p>
            </div>
        @else
            <div>
                <x-input-label for="firma" :value="__('Bağlı Olduğunuz Firma')" />
                <x-text-input id="firma" type="text" class="mt-1 block w-full bg-gray-100 text-gray-400 cursor-not-allowed font-medium" :value="$user->customer->name ?? 'Belirtilmemiş'" disabled />
                <p class="text-[11px] text-gray-500 mt-1">Firma bilginizi değiştirmek için sistem yöneticisine danışın.</p>
            </div>
        @endif

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('E-posta')" />
            <x-text-input id="email" type="email" class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed font-medium" :value="$user->email" disabled />
            <p class="text-[11px] text-gray-400 mt-1">E-posta adresiniz Merkezi SSO üzerinden yönetilmektedir.</p>
        </div>

        {{-- Doğum Tarihi (Yeni) --}}
        @php $hasDoB = !empty($user->dogum_tarihi); @endphp
        <div class="{{ ($user->is_personnel && !$hasDoB) ? 'p-4 rounded-lg border-2 border-dashed border-indigo-200 bg-indigo-50/50 relative' : '' }}">
            @if($user->is_personnel && !$hasDoB)
                <div class="absolute -top-3 left-4 bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Eksik Bilgi</div>
            @endif
            <x-input-label for="dogum_tarihi" :value="__('Doğum Tarihi')" class="{{ ($user->is_personnel && !$hasDoB) ? 'font-bold text-indigo-900' : '' }}" />
            <x-text-input id="dogum_tarihi" name="dogum_tarihi" type="date" class="mt-1 block w-full" :value="old('dogum_tarihi', $user->dogum_tarihi ? $user->dogum_tarihi->format('Y-m-d') : '')" />
            <x-input-error class="mt-2" :messages="$errors->get('dogum_tarihi')" />
        </div>

        {{-- Telefon (Yeni) --}}
        @php $hasPhone = !empty($user->telefon); @endphp
        <div class="{{ ($user->is_personnel && !$hasPhone) ? 'p-4 rounded-lg border-2 border-dashed border-indigo-200 bg-indigo-50/50 relative' : '' }}">
            @if($user->is_personnel && !$hasPhone)
                <div class="absolute -top-3 left-4 bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Eksik Bilgi</div>
            @endif
            <x-input-label for="telefon" :value="__('Telefon Numarası')" class="{{ ($user->is_personnel && !$hasPhone) ? 'font-bold text-indigo-900' : '' }}" />
            <x-text-input id="telefon" name="telefon" type="text" class="mt-1 block w-full" :value="old('telefon', $user->telefon)" placeholder="0555 555 55 55" />
            <x-input-error class="mt-2" :messages="$errors->get('telefon')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Kaydet') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <div 
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2"
                    x-init="setTimeout(() => show = false, 5000)"
                    class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2 rounded-xl text-sm font-bold shadow-sm"
                >
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ __('Profil bilgileriniz başarıyla güncellendi.') }}
                </div>
            @endif
        </div>
    </form>

        {{-- Bölüm Değişikliği Modalı --}}
        <div x-show="showBolumRequest" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showBolumRequest = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('profile.istek.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="talep_turu" value="bolum_degisikligi">
                        <input type="hidden" name="yeni_deger" value="-"> {{-- Null olmaması için gereksiz ama geçilecek değer --}}
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Bölüm Değişikliği Talebi</h3>
                            <div class="mt-4">
                                <label for="yeni_bolum_id" class="block text-sm font-medium text-gray-700">Yeni Bölümünüz</label>
                                <select name="yeni_bolum_id" id="yeni_bolum_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="" disabled selected>Bölüm Seçin...</option>
                                    @if(isset($bolumler))
                                        @foreach($bolumler as $b)
                                            <option value="{{ $b->id }}">{{ $b->ad }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Talep Gönder
                            </button>
                            <button type="button" @click="showBolumRequest = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                İptal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Talep Geçmişi (Son 5 İşlem) - Arık Katlanabilir --}}
        @if($gecmisIstekler->isNotEmpty())
            <div x-data="{ openHistory: false }" class="mt-8 pt-6 border-t border-gray-100">
                <button type="button" @click="openHistory = !openHistory" class="flex items-center justify-between w-full group">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest group-hover:text-indigo-600 transition-colors">
                        📢 Son Değişiklik Talepleriniz
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-medium text-gray-400 group-hover:text-indigo-400 transition-colors" x-text="openHistory ? 'Daralt' : 'Görüntülemek için tıkla'"></span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition-transform duration-300" :class="openHistory ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>

                <div x-show="openHistory" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="mt-6 space-y-4">
                    @foreach($gecmisIstekler as $istek)
                        <div class="flex flex-col p-4 rounded-xl border {{ $istek->durum === 'onaylandi' ? 'bg-emerald-50/30 border-emerald-100' : 'bg-red-50/30 border-red-100' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-bold {{ $istek->durum === 'onaylandi' ? 'text-emerald-700' : 'text-red-700' }}">
                                    @php
                                        $typeText = 'Değişiklik';
                                        if($istek->talep_turu == 'isim_degisikligi') $typeText = 'İsim Değişikliği';
                                        elseif($istek->talep_turu == 'bolum_degisikligi') $typeText = 'Bölüm Değişikliği';
                                        elseif($istek->talep_turu == 'email_degisikligi') $typeText = 'E-posta Değişikliği';
                                    @endphp
                                    {{ $typeText }}
                                    ({{ $istek->durum === 'onaylandi' ? 'Onaylandı' : 'Reddedildi' }})
                                </span>
                                <span class="text-[10px] text-gray-400 font-medium">{{ $istek->updated_at->diffForHumans() }}</span>
                            </div>
                            
                            <div class="text-xs text-gray-600 space-y-1">
                                <p><strong>Talep Edilen:</strong> {{ $istek->talep_turu == 'isim_degisikligi' ? $istek->yeni_deger : ($istek->yeniBolum->ad ?? '-') }}</p>
                                @if($istek->admin_notu)
                                    <div class="mt-2 p-2 bg-white/50 rounded border border-dashed {{ $istek->durum === 'onaylandi' ? 'border-emerald-200' : 'border-red-200' }}">
                                        <p class="font-bold text-gray-700">Yönetici Notu:</p>
                                        <p class="italic">"{{ $istek->admin_notu }}"</p>
                                    </div>
                                @endif
                                <p class="text-[10px] mt-2 opacity-60">İşlem Yapan: {{ $istek->admin->name ?? 'Sistem' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</section>