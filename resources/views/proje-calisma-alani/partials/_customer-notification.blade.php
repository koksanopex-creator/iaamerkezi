@if($iaa->musteriSikayeti)
    @php
        $sikayet = $iaa->musteriSikayeti;
        // Yetki Kontrolü
        $canNotify = Auth::check() && Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi']);
        
        // Alıcı seçeneklerini hazırla
        $recipientOptions = collect();
        
        // 1. Müşteri İletişim (şikayet formundaki e-posta)
        if (!empty($sikayet->musteri_iletisim)) {
            $recipientOptions->push([
                'email' => $sikayet->musteri_iletisim,
                'name' => $sikayet->musteri_adi ?? 'Müşteri',
                'type' => 'musteri_iletisim',
                'label' => 'Müşteri İletişim',
            ]);
        }
        
        // 2. Firma Genel E-posta (customer bağlıysa ve farklıysa)
        if ($sikayet->customer && !empty($sikayet->customer->email)) {
            $firmaEmail = $sikayet->customer->email;
            if (strtolower($firmaEmail) !== strtolower($sikayet->musteri_iletisim ?? '')) {
                $recipientOptions->push([
                    'email' => $firmaEmail,
                    'name' => $sikayet->customer->name,
                    'type' => 'firma_iletisim',
                    'label' => 'Firma İletişim',
                ]);
            }
        }
        
        // 3. Firmaya bağlı yetkili kişiler
        if ($sikayet->customer && $sikayet->customer->users) {
            foreach ($sikayet->customer->users as $yetkili) {
                if (!empty($yetkili->email) && strtolower($yetkili->email) !== strtolower($sikayet->musteri_iletisim ?? '')) {
                    $recipientOptions->push([
                        'email' => $yetkili->email,
                        'name' => $yetkili->name,
                        'type' => 'yetkili',
                        'label' => 'Yetkili Kişi',
                    ]);
                }
            }
        }

        // Önceden gönderilen alıcılar
        $sentPasswords = $sikayet->guestPasswords ?? collect();
    @endphp

        <div x-data="{ showModal: false }" class="bg-white rounded-xl shadow-sm border border-indigo-100 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-3 flex justify-between items-center select-none">
                <h3 class="text-white font-bold text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Müşteri Takip Sistemi
                </h3>
            </div>

            <div class="p-5">
                {{-- ŞİFRE GÖSTERİM ALANI (Çoklu) --}}
                @if(session('generated_passwords'))
                    <div class="mb-6 bg-indigo-50 border border-indigo-200 p-4 rounded-lg shadow-sm">
                        <p class="text-xs font-bold text-indigo-800 uppercase mb-3">OLUŞTURULAN MÜŞTERİ ŞİFRELERİ:</p>
                        <div class="space-y-2">
                            @foreach(session('generated_passwords') as $pw)
                                <div class="flex items-center justify-between bg-white p-3 rounded border border-indigo-200">
                                    <div>
                                        <span class="text-sm font-semibold text-gray-700">{{ $pw['name'] }}</span>
                                        <span class="text-xs text-gray-500 ml-2">({{ $pw['email'] }})</span>
                                    </div>
                                    <code class="text-lg font-mono font-bold text-indigo-700 tracking-wider">{{ $pw['password'] }}</code>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-red-500 mt-2">* Bu şifreleri bir daha göremeyeceksiniz, gerekirse not alınız.</p>
                    </div>
                @endif

                {{-- Eski tekli şifre gösterimi (geriye uyumluluk) --}}
                @if(session('generated_password'))
                    <div class="mb-6 bg-indigo-50 border border-indigo-200 p-4 rounded-lg shadow-sm">
                        <p class="text-xs font-bold text-indigo-800 uppercase mb-2">YENİ OLUŞTURULAN MÜŞTERİ ŞİFRESİ:</p>
                        <div class="flex items-center justify-between bg-white p-3 rounded border border-indigo-200">
                            <code class="text-xl font-mono font-bold text-indigo-700 tracking-wider">{{ session('generated_password') }}</code>
                            <span class="text-xs text-gray-500 italic">(Bu şifre müşteriye e-posta olarak gönderildi)</span>
                        </div>
                        <p class="text-xs text-red-500 mt-2">* Bu şifreyi bir daha göremeyeceksiniz, gerekirse not alınız.</p>
                    </div>
                @endif

                @if(!$sikayet->musteri_bildirim_tarihi && $sentPasswords->isEmpty())
                    {{-- ==========================================
                         1. DURUM: Henüz Bildirim Yapılmamış
                    ========================================== --}}
                    <div class="flex items-center justify-between bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                        <div class="flex items-start gap-3">
                            <div class="text-yellow-600 bg-yellow-100 p-2 rounded-full">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-yellow-800">Bildirim Bekleniyor</h4>
                                <p class="text-xs text-yellow-700 mt-1">Müşteriye henüz takip bilgileri gönderilmemiş. Alıcıları seçerek bilgileri gönderebilirsiniz.</p>
                            </div>
                        </div>
                        @if(!$sikayet->trashed())
                            <button @click="showModal = true"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition shadow-sm flex items-center gap-2 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Bilgileri Gönder
                            </button>
                        @endif
                    </div>

                @else
                    {{-- ==========================================
                         2. DURUM: Bildirim Yapılmış
                    ========================================== --}}
                    
                    {{-- A. Genel Bildirim Bilgisi --}}
                    @if($sikayet->musteri_bildirim_tarihi)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm font-bold text-green-800">Müşteri Bilgilendirildi</p>
                                    <p class="text-xs text-green-700 mt-1">
                                        {{ $sikayet->musteri_bildirim_tarihi->format('d.m.Y H:i') }} tarihinde
                                        <strong>{{ \App\Models\User::find($sikayet->musteri_bildirim_yapan_id)->name ?? 'Sistem' }}</strong> tarafından.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    {{-- B. Takip Linki --}}
                    @if($sikayet->takip_token)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-gray-500 uppercase">Takip Linki</span>
                                <a href="{{ route('public.sikayet.show', $sikayet->takip_token) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Görüntüle &rarr;</a>
                            </div>
                            <code class="text-xs bg-white p-1 rounded border text-gray-600 truncate block">{{ route('public.sikayet.show', $sikayet->takip_token) }}</code>
                        </div>
                    @endif

                    {{-- C. Alıcı Listesi (Yeni Tablo) --}}
                    @if($sentPasswords->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Gönderilen Alıcılar</h4>
                            <div class="space-y-2">
                                @foreach($sentPasswords as $gp)
                                    <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg p-3">
                                        <div class="flex items-center gap-3">
                                            {{-- Alıcı Türü Etiketi --}}
                                            @php
                                                $typeColor = match($gp->recipient_type) {
                                                    'firma_iletisim' => 'bg-blue-100 text-blue-700',
                                                    'yetkili' => 'bg-purple-100 text-purple-700',
                                                    'musteri_iletisim' => 'bg-green-100 text-green-700',
                                                    default => 'bg-gray-100 text-gray-700',
                                                };
                                                $typeLabel = match($gp->recipient_type) {
                                                    'firma_iletisim' => 'Firma',
                                                    'yetkili' => 'Yetkili',
                                                    'musteri_iletisim' => 'Müşteri',
                                                    default => 'Diğer',
                                                };
                                            @endphp
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $typeColor }}">{{ $typeLabel }}</span>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-800">{{ $gp->recipient_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $gp->email }} · {{ $gp->sent_at?->format('d.m.Y H:i') }}</p>
                                            </div>
                                        </div>
                                        @if(!$sikayet->trashed())
                                            <form action="{{ route('proje.reset_customer_password', $iaa->id) }}" method="POST"
                                                  onsubmit="return confirm('Bu alıcı için şifre sıfırlanacak. Emin misiniz?');">
                                                @csrf
                                                <input type="hidden" name="guest_password_id" value="{{ $gp->id }}">
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    Şifre Sıfırla
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- D. Yeni Alıcıya Gönder Butonu --}}
                    @if(!$sikayet->trashed() && $recipientOptions->count() > $sentPasswords->count())
                        <button @click="showModal = true"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 mt-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Yeni Alıcıya Bildirim Gönder
                        </button>
                    @endif
                @endif
            </div>

            {{-- ==========================================
                 ALICI SEÇİM MODALI (Alpine.js)
            ========================================== --}}
            <div x-show="showModal" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                 @keydown.escape.window="showModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">

                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-lg mx-4 overflow-hidden"
                     @click.away="showModal = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                    {{-- Modal Header --}}
                    <div class="bg-gradient-to-r from-violet-600 to-indigo-600 p-4 flex justify-between items-center">
                        <h3 class="text-white font-bold text-base flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Alıcı Seçimi
                        </h3>
                        <button @click="showModal = false" class="text-white/80 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <form action="{{ route('proje.notify_customer', $iaa->id) }}" method="POST" x-data="{ selectedCount: 0 }">
                        @csrf
                        <div class="p-5 max-h-80 overflow-y-auto">
                            <p class="text-sm text-gray-600 mb-4">Bildirim göndermek istediğiniz alıcıları seçin. Her alıcıya ayrı bir şifre üretilecektir.</p>

                            @if($recipientOptions->isEmpty())
                                <div class="text-center py-6 text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    <p class="text-sm">Gönderilebilecek alıcı bulunamadı.</p>
                                    <p class="text-xs mt-1">Şikayette müşteri iletişim bilgisi veya firma ataması yapılmamış.</p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($recipientOptions as $index => $option)
                                        @php
                                            // Bu alıcıya daha önce gönderilmiş mi?
                                            $alreadySent = $sentPasswords->where('email', $option['email'])->isNotEmpty();
                                            $typeIcon = match($option['type']) {
                                                'musteri_iletisim' => '👤',
                                                'firma_iletisim' => '🏢',
                                                'yetkili' => '👨‍💼',
                                                default => '📧',
                                            };
                                            $typeBg = match($option['type']) {
                                                'musteri_iletisim' => 'border-green-200 bg-green-50/50',
                                                'firma_iletisim' => 'border-blue-200 bg-blue-50/50',
                                                'yetkili' => 'border-purple-200 bg-purple-50/50',
                                                default => 'border-gray-200 bg-gray-50/50',
                                            };
                                        @endphp
                                        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition hover:shadow-sm {{ $typeBg }} {{ $alreadySent ? 'opacity-60' : '' }}">
                                            <input type="checkbox"
                                                   name="recipients[{{ $index }}][email]"
                                                   value="{{ $option['email'] }}"
                                                   {{ $alreadySent ? 'disabled' : '' }}
                                                   @change="selectedCount = document.querySelectorAll('input[name^=recipients]:checked').length"
                                                   class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                            <input type="hidden" name="recipients[{{ $index }}][name]" value="{{ $option['name'] }}" {{ $alreadySent ? 'disabled' : '' }}>
                                            <input type="hidden" name="recipients[{{ $index }}][type]" value="{{ $option['type'] }}" {{ $alreadySent ? 'disabled' : '' }}>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-base">{{ $typeIcon }}</span>
                                                    <span class="text-sm font-semibold text-gray-800">{{ $option['name'] }}</span>
                                                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500">{{ $option['label'] }}</span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-0.5 ml-7">{{ $option['email'] }}</p>
                                                @if($alreadySent)
                                                    <p class="text-[10px] text-orange-600 mt-0.5 ml-7 font-medium">✓ Daha önce gönderildi</p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Modal Footer --}}
                        <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                            <button type="button" @click="showModal = false"
                                class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                                İptal
                            </button>
                            <button type="submit"
                                :disabled="selectedCount === 0"
                                :class="selectedCount === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                                class="text-white font-bold py-2 px-5 rounded-lg text-sm transition shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span x-text="selectedCount > 0 ? selectedCount + ' Alıcıya Gönder' : 'Alıcı Seçin'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif