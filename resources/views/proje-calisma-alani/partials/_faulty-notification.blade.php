{{-- resources/views/proje-calisma-alani/partials/_faulty-notification.blade.php --}}

@php
    // Gerekli Değişkenler
    $currentUser = auth()->user();
    $isLeader = $currentUser && $iaa->atananTakim && $currentUser->id == $iaa->atananTakim->lider_user_id;
    // Müdahale yetkisi varsa QM de lider gibi davranabilir (Raporlama yapabilir)
    if ($currentUser && $currentUser->hasRole('Bölüm Kalite Yöneticisi') && ($isQualityManagerInterventionPower ?? false)) {
        $isLeader = true;
    }

    // Durumlar
    $activeStatuses = ['Yeni', 'Atandı', 'calisiliyor', 'Devam Ediyor', 'Revize Ediliyor'];
    $isFaultyPendingQuality = $iaa->durum == 'hatali_bildirim_onayi_bekliyor_kalite';
    $isFaultyPendingDirector = $iaa->durum == 'hatali_bildirim_onayi_bekliyor_direktor';
    $isFaultyPendingSuperadmin = $iaa->durum == 'hatali_bildirim_onayi_bekliyor_superadmin';
    $isFaultyClosed = $iaa->durum == 'hatali_bildirim_olarak_kapatildi';

    // Roller
    $isQuality = $currentUser && $currentUser->hasRole('Bölüm Kalite Yöneticisi');
    
    // YENİ: Eğer kullanıcı QM ise, müdahale yetkisi açık olmalı ki operasyonel işlem yapabilsin
    if ($currentUser && $currentUser->hasRole('Bölüm Kalite Yöneticisi') && !($isQualityManagerInterventionPower ?? false)) {
        $isQuality = false;
    }
    $isDirector = $currentUser && $currentUser->hasRole('Direktör');
    $isSuperAdmin = $currentUser && $currentUser->hasRole('Superadmin');

    // Direktör Bölüm Kontrolü
    if ($currentUser && $isDirector && !$isSuperAdmin) {
        $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? null;
        $bolumDirectorId = $bolum ? $bolum->director_id : null;
        if ($bolumDirectorId && $currentUser->id != $bolumDirectorId) {
            $isDirector = false;
        }
    }
@endphp

{{--
================================================================
1. LİDER PANELİ (RAPORLAMA VEYA GERİ ALMA)
================================================================
--}}

@if($isLeader && in_array($iaa->durum, $activeStatuses))
    <div
        class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="text-sm font-bold text-red-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Hatalı Bildirim Raporlama
            </h4>
            <p class="text-xs text-red-700 mt-1">
                Bu şikayetin/görevin şirketimizle alakası olmadığını veya tamamen hatalı olduğunu düşünüyorsanız buradan
                bildirin.
            </p>
        </div>
        <button onclick="document.getElementById('modalHataliBildirim').classList.remove('hidden')"
            class="whitespace-nowrap px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition shadow-md">
            Hatalı Bildirimdir
        </button>
    </div>

    {{-- MODAL --}}
    <div id="modalHataliBildirim" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/75 transition-opacity"
                onclick="document.getElementById('modalHataliBildirim').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('proje.markAsFaulty', $iaa->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Hatalı Bildirim Gerekçesi</h3>
                        <p class="text-sm text-gray-500 mb-4">Lütfen neden hatalı olduğunu detaylıca açıklayınız.</p>
                        <textarea name="gerekce" rows="4"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Gerekçenizi yazınız..." required></textarea>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Onaya
                            Gönder</button>
                        <button type="button"
                            onclick="document.getElementById('modalHataliBildirim').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">İptal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{--
================================================================
2. ONAY SÜRECİ ÖZETİ (DETAYLI VE GÖRSEL)
================================================================
--}}
@if($iaa->hatali_bildirim_gerekcesi)
    <div
        class="mb-8 bg-gradient-to-r from-orange-50 to-red-50 border-2 border-orange-200 rounded-2xl shadow-md overflow-hidden animate-in fade-in slide-in-from-top duration-500">
        <div class="bg-white/50 backdrop-blur-sm px-5 py-3 border-b border-orange-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                <span class="text-xs font-black text-orange-900 uppercase tracking-widest">Hatalı Bildirim Süreç
                    Geçmişi</span>
            </div>
            @if($isFaultyClosed)
                <span class="bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-sm">DOSYA
                    KAPATILDI</span>
            @else
                <span
                    class="bg-orange-200 text-orange-900 text-[10px] font-black px-3 py-1 rounded-full border border-orange-300">İNCELEMEDE</span>
            @endif
        </div>

        <div class="p-5 space-y-6">
            {{-- 1. Takım Lideri --}}
            <div class="flex gap-4 relative">
                <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-orange-200/50"></div>
                <div class="flex-shrink-0 relative">
                    <img src="{{ $iaa->hataliBildirimLiderUser->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($iaa->hataliBildirimLiderUser->name ?? 'TL') . '&color=7F9CF5&background=EBF4FF' }}"
                        class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" alt="TL">
                </div>
                <div class="flex-1 bg-white/60 p-3 rounded-xl border border-orange-100 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <span
                            class="text-xs font-bold text-gray-900">{{ $iaa->hataliBildirimLiderUser->name ?? 'Takım Lideri' }}</span>
                        <span
                            class="text-[10px] text-gray-400 font-medium">{{ $iaa->hatali_bildirim_tarihi ? \Carbon\Carbon::parse($iaa->hatali_bildirim_tarihi)->format('d.m.Y H:i') : '-' }}</span>
                    </div>
                    <p class="text-xs font-bold text-orange-600 mb-1">RAPORLAMA GEREKÇESİ:</p>
                    <p class="text-sm text-gray-700 italic leading-relaxed">"{{ $iaa->hatali_bildirim_gerekcesi }}"</p>
                </div>
            </div>

            {{-- 2. Kalite Yöneticisi --}}
            @if($iaa->hatali_bildirim_kalite_notu || $iaa->hataliBildirimKaliteUser)
                <div class="flex gap-4 relative">
                    <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-orange-200/50"></div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ $iaa->hataliBildirimKaliteUser->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($iaa->hataliBildirimKaliteUser->name ?? 'KY') . '&color=F59E0B&background=FEF3C7' }}"
                            class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" alt="KY">
                    </div>
                    <div class="flex-1 bg-white/60 p-3 rounded-xl border border-orange-100 shadow-sm">
                        <div class="flex items-center justify-between mb-1">
                            <span
                                class="text-xs font-bold text-gray-900">{{ $iaa->hataliBildirimKaliteUser->name ?? 'Kalite Yöneticisi' }}</span>
                            <span
                                class="text-[10px] text-gray-400 font-medium">{{ $iaa->hatali_bildirim_kalite_at ? \Carbon\Carbon::parse($iaa->hatali_bildirim_kalite_at)->format('d.m.Y H:i') : '-' }}</span>
                        </div>
                        <p class="text-xs font-bold text-yellow-600 mb-1">KALİTE ONAYI NOTU:</p>
                        <p class="text-sm text-gray-700 italic leading-relaxed">
                            "{{ $iaa->hatali_bildirim_kalite_notu ?? 'Not belirtilmedi.' }}"</p>
                    </div>
                </div>
            @endif

            {{-- 3. Direktör --}}
            @if($iaa->hatali_bildirim_direktor_notu || $iaa->hataliBildirimDirektorUser)
                <div class="flex gap-4 relative">
                    <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-orange-200/50"></div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ $iaa->hataliBildirimDirektorUser->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($iaa->hataliBildirimDirektorUser->name ?? 'DR') . '&color=4F46E5&background=E0E7FF' }}"
                            class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" alt="DR">
                    </div>
                    <div class="flex-1 bg-white/60 p-3 rounded-xl border border-orange-100 shadow-sm">
                        <div class="flex items-center justify-between mb-1">
                            <span
                                class="text-xs font-bold text-gray-900">{{ $iaa->hataliBildirimDirektorUser->name ?? 'Direktör' }}</span>
                            <span
                                class="text-[10px] text-gray-400 font-medium">{{ $iaa->hatali_bildirim_direktor_at ? \Carbon\Carbon::parse($iaa->hatali_bildirim_direktor_at)->format('d.m.Y H:i') : '-' }}</span>
                        </div>
                        <p class="text-xs font-bold text-indigo-600 mb-1">DİREKTÖR KAPANIŞ NOTU:</p>
                        <p class="text-sm text-gray-700 italic leading-relaxed">
                            "{{ $iaa->hatali_bildirim_direktor_notu ?? 'Not belirtilmedi.' }}"</p>
                    </div>
                </div>
            @endif

            {{-- 4. Superadmin --}}
            @if($iaa->hatali_bildirim_superadmin_notu || $iaa->hataliBildirimSuperadminUser)
                <div class="flex gap-4 relative">
                    <div class="flex-shrink-0 relative">
                        <img src="{{ $iaa->hataliBildirimSuperadminUser->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($iaa->hataliBildirimSuperadminUser->name ?? 'SA') . '&color=DC2626&background=FEE2E2' }}"
                            class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" alt="SA">
                    </div>
                    <div class="flex-1 bg-white/60 p-3 rounded-xl border border-orange-100 shadow-sm">
                        <div class="flex items-center justify-between mb-1">
                            <span
                                class="text-xs font-bold text-gray-900">{{ $iaa->hataliBildirimSuperadminUser->name ?? 'Üst Yönetim' }}</span>
                            <span
                                class="text-[10px] text-gray-400 font-medium">{{ $iaa->hatali_bildirim_superadmin_at ? \Carbon\Carbon::parse($iaa->hatali_bildirim_superadmin_at)->format('d.m.Y H:i') : '-' }}</span>
                        </div>
                        <p class="text-xs font-bold text-red-600 mb-1">YÖNETİM FİNAL NOTU:</p>
                        <p class="text-sm text-gray-700 italic leading-relaxed">
                            "{{ $iaa->hatali_bildirim_superadmin_notu ?? 'Not belirtilmedi.' }}"</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif

{{--
================================================================
3. ONAY PANELLERİ
================================================================
--}}

{{-- A. KALİTE YÖNETİCİSİ ONAYI --}}
@if($isFaultyPendingQuality && ($isQuality || $isSuperAdmin))
    <div class="mb-6 bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 bg-yellow-200 text-yellow-800 text-[10px] font-bold px-3 py-1 rounded-bl-lg">
            KALİTE ONAYI BEKLİYOR</div>
        <h3 class="text-lg font-bold text-yellow-900 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Hatalı Bildirim Onayı
        </h3>
        <form action="{{ route('proje.decideFaultyByQuality', $iaa->id) }}" method="POST" class="mt-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Yorumunuz / Notunuz</label>
                <textarea name="not" rows="2"
                    class="w-full border-yellow-200 rounded-lg p-2 text-sm focus:ring-yellow-500 focus:border-yellow-500"
                    placeholder="Onay veya red için bir açıklama yazabilirsiniz..."></textarea>
            </div>
            <div
                class="mt-4 text-[11px] text-yellow-800 border border-yellow-200 bg-yellow-100/30 p-2 rounded flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"></path>
                </svg>
                Onaylarsanız; ayara göre Direktör veya Üst Yönetim onayına sunulacaktır.
            </div>
            <div class="mt-6 flex gap-3">
                <input type="hidden" name="action" id="qualityFaultAction" value="approve">
                <button type="submit"
                    class="px-6 py-2 bg-yellow-600 text-white rounded-lg font-bold hover:bg-yellow-700 shadow-md transition">Onayla</button>
                <button type="submit"
                    onclick="document.getElementById('qualityFaultAction').value='reject'; return confirm('Reddetmek istediğinize emin misiniz?')"
                    class="px-6 py-2 bg-white text-red-600 border border-red-200 rounded-lg font-bold hover:bg-red-50 transition">Reddet</button>
            </div>
        </form>
    </div>
@endif

{{-- B. DİREKTÖR ONAYI --}}
@if($isFaultyPendingDirector && ($isDirector || $isSuperAdmin))
    <div class="mb-6 bg-slate-800 text-white rounded-xl p-6 shadow-xl relative overflow-hidden">
        <div
            class="absolute top-0 right-0 bg-indigo-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg uppercase">
            Son Onay: Direktör</div>
        <h3 class="text-xl font-bold flex items-center gap-2">
            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Hatalı Bildirim - Son Karar
        </h3>
        <form action="{{ route('proje.decideFaultyByDirector', $iaa->id) }}" method="POST" class="mt-4">
            @csrf
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Direktör Karar Notu</label>
                <textarea name="not" rows="2"
                    class="w-full bg-white/5 border-white/20 rounded-lg p-2 text-sm text-white focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Kapanış notunuzu buraya yazabilirsiniz..." required></textarea>
            </div>
            <div class="flex gap-4">
                <input type="hidden" name="action" id="directorFaultAction" value="approve">
                <button type="submit"
                    class="px-6 py-3 bg-white text-slate-900 rounded-lg font-black hover:bg-gray-100 shadow-lg transition">ONAYLA
                    VE KAPAT</button>
                <button type="submit"
                    onclick="document.getElementById('directorFaultAction').value='reject'; return confirm('Reddetmek istediğinize emin misiniz?')"
                    class="px-6 py-3 bg-transparent border-2 border-red-400 text-red-400 rounded-lg font-bold hover:bg-red-400/10 transition">REDDET</button>
            </div>
        </form>
    </div>
@endif

{{-- C. SUPERADMIN ONAYI --}}
@if($isFaultyPendingSuperadmin && $isSuperAdmin)
    <div class="mb-6 bg-indigo-900 text-white rounded-xl p-6 shadow-2xl relative overflow-hidden ring-4 ring-indigo-500/20">
        <div
            class="absolute top-0 right-0 bg-yellow-400 text-indigo-900 text-[10px] font-black px-4 py-1 rounded-bl-lg uppercase shadow-lg">
            Final Onay: Üst Yönetim</div>
        <h3 class="text-xl font-bold flex items-center gap-2">
            <svg class="w-7 h-7 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            Hatalı Bildirim - Yönetim Onayı
        </h3>
        <form action="{{ route('proje.decideFaultyBySuperadmin', $iaa->id) }}" method="POST" class="mt-4">
            @csrf
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-indigo-300 uppercase mb-1">Yönetim Kapanış Notu</label>
                <textarea name="not" rows="2"
                    class="w-full bg-white/5 border-white/10 rounded-lg p-3 text-sm text-white focus:ring-yellow-400 focus:border-yellow-400"
                    placeholder="Neden hatalı olduğunu ve kapanış kararını özetleyiniz..." required></textarea>
            </div>
            <div class="flex gap-4">
                <input type="hidden" name="action" id="superFaultAction" value="approve">
                <button type="submit"
                    class="px-8 py-3.5 bg-white text-indigo-900 rounded-xl font-black hover:bg-gray-100 shadow-xl transition hover:scale-105">ONAYLA
                    VE KAPAT</button>
                <button type="submit"
                    onclick="document.getElementById('superFaultAction').value='reject'; return confirm('Reddetmek istediğinize emin misiniz?')"
                    class="px-8 py-3.5 bg-indigo-800 text-red-400 border-2 border-red-500/30 rounded-xl font-bold hover:bg-red-500/10 transition">REDDET</button>
            </div>
        </form>
    </div>
@endif

{{-- Talebi Geri Alma Butonu (En Altta Kalsın) --}}
{{-- Lider İçin Geri Alma --}}
@if($isLeader && ($isFaultyPendingQuality || $isFaultyPendingDirector || $isFaultyPendingSuperadmin))
    <div class="mb-6 bg-orange-50 border border-orange-200 rounded-xl p-3 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <p class="text-xs text-orange-900">Onay bekliyor. <span class="hidden sm:inline">Yanlışlıkla yaptıysanız geri
                    alabilirsiniz.</span></p>
        </div>
        <form action="{{ route('proje.recallFaulty', $iaa->id) }}" method="POST">
            @csrf
            <button type="submit"
                class="px-3 py-1.5 bg-white border border-orange-200 text-orange-700 rounded-lg text-[10px] font-bold hover:bg-orange-100 transition shadow-sm">
                Talebi Geri Al
            </button>
        </form>
    </div>
@endif

{{-- Kalite Yöneticisi İçin Geri Alma --}}
@if(($isQuality || $isSuperAdmin) && ($isFaultyPendingDirector || $isFaultyPendingSuperadmin))
    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl p-3 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <p class="text-xs text-yellow-900">Sonraki onayı bekliyor. <span class="hidden sm:inline">Onayınızı yanlışlıkla
                    verdiyseniz geri alabilirsiniz.</span></p>
        </div>
        <form action="{{ route('proje.recallFaultyByQuality', $iaa->id) }}" method="POST">
            @csrf
            <button type="submit"
                onclick="return confirm('Verdiğiniz kalite onayını geri almak istediğinize emin misiniz?')"
                class="px-3 py-1.5 bg-white border border-yellow-200 text-yellow-700 rounded-lg text-[10px] font-bold hover:bg-yellow-100 transition shadow-sm">
                Kalite Onayını Geri Al
            </button>
        </form>
    </div>
@endif

{{-- Direktör İçin Geri Alma --}}
@if(($isDirector || $isSuperAdmin) && $iaa->durum == 'hatali_bildirim_olarak_kapatildi' && ($iaa->hatali_bildirim_direktor_user_id == auth()->id() || $isSuperAdmin) && $iaa->hatali_bildirim_direktor_user_id != null)
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-3 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <p class="text-xs text-red-900">Dosya direktör tarafından kapatıldı. <span class="hidden sm:inline">Onayınızı
                    yanlışlıkla
                    verdiyseniz geri alabilirsiniz.</span></p>
        </div>
        <form action="{{ route('proje.recallFaultyByDirector', $iaa->id) }}" method="POST">
            @csrf
            <button type="submit"
                onclick="return confirm('Verdiğiniz direktör onayını geri almak ve dosyayı yeniden açmak istediğinize emin misiniz?')"
                class="px-3 py-1.5 bg-white border border-red-200 text-red-700 rounded-lg text-[10px] font-bold hover:bg-red-100 transition shadow-sm">
                Direktör Onayını Geri Al
            </button>
        </form>
    </div>
@endif

{{-- Superadmin İçin Geri Alma --}}
@if($isSuperAdmin && $iaa->durum == 'hatali_bildirim_olarak_kapatildi' && $iaa->hatali_bildirim_superadmin_user_id == auth()->id() && $iaa->hatali_bildirim_superadmin_user_id != null)
    <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-xl p-3 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <p class="text-xs text-indigo-900">Dosya yönetim tarafından kapatıldı. <span class="hidden sm:inline">Onayınızı
                    yanlışlıkla
                    verdiyseniz geri alabilirsiniz.</span></p>
        </div>
        <form action="{{ route('proje.recallFaultyBySuperadmin', $iaa->id) }}" method="POST">
            @csrf
            <button type="submit"
                onclick="return confirm('Verdiğiniz yönetim onayını geri almak ve dosyayı yeniden açmak istediğinize emin misiniz?')"
                class="px-3 py-1.5 bg-white border border-indigo-200 text-indigo-700 rounded-lg text-[10px] font-bold hover:bg-indigo-100 transition shadow-sm">
                Yönetim Onayını Geri Al
            </button>
        </form>
    </div>
@endif