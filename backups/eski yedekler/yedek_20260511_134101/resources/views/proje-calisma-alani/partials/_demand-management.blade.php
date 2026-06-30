{{-- resources/views/proje-calisma-alani/partials/_demand-management.blade.php --}}

{{-- 1. LİDER İÇİN: "BU BİR TALEPTİR" BUTONU --}}
@if($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id && in_array($iaa->durum, ['Yeni', 'Atandı', 'calisiliyor']))
    <div
        class="mb-6 bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="text-sm font-bold text-orange-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Şikayet / Talep Ayrımı
            </h4>
            <p class="text-xs text-orange-700 mt-1">
                Bu dosya bir hata/şikayet değil, müşteri talebi (yeni özellik vb.) mi?
            </p>
        </div>
        <button onclick="document.getElementById('modalTalepBaslat').classList.remove('hidden')"
            class="whitespace-nowrap px-4 py-2 bg-orange-600 text-white rounded-lg text-xs font-bold hover:bg-orange-700 transition shadow-md">
            Bu Bir Taleptir
        </button>
    </div>

    {{-- MODAL: Talep Gerekçesi --}}
    <div id="modalTalepBaslat" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/75 transition-opacity"
                onclick="document.getElementById('modalTalepBaslat').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('proje.markAsRequest', $iaa->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Talep Bildirimi</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Lütfen bunun neden bir şikayet değil de talep olduğunu açıklayınız. Bu açıklama <strong>Bölüm
                                Kalite Yöneticisine</strong> sunulacaktır.
                        </p>
                        <textarea name="gerekce" rows="4"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Gerekçenizi detaylıca yazınız..." required></textarea>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Onaya Gönder
                        </button>
                        <button type="button" onclick="document.getElementById('modalTalepBaslat').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- 2. KALİTE YÖNETİCİSİ ONAY ALANI --}}
@if($iaa->durum == 'talep_onayi_bekliyor_kalite' && (auth()->user()->hasRole('Bölüm Kalite Yöneticisi') || auth()->user()->hasRole('Superadmin')))
    <div class="mb-6 bg-purple-50 border-2 border-purple-200 rounded-xl p-6 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 bg-purple-200 text-purple-800 text-[10px] font-bold px-2 py-1 rounded-bl-lg">ONAY
            BEKLİYOR</div>

        <h3 class="text-lg font-bold text-purple-900 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Talep Sınıflandırma Onayı
        </h3>

        <div class="mt-4 bg-white p-4 rounded-lg border border-purple-100 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase mb-1">Takım Lideri Açıklaması:</p>
            <p class="text-gray-800 italic">"{{ $iaa->talep_gerekcesi }}"</p>
        </div>

        <p class="mt-4 text-sm text-purple-800">
            Takım lideri bu dosyanın şikayet değil <strong>TALEP</strong> olduğunu belirtiyor. Onaylarsanız son karar için
            <strong>Superadmin</strong>'e iletilecektir. Reddederseniz süreç <strong>Şikayet</strong> olarak devam
            edecektir.
        </p>

        <div class="mt-6 flex gap-3">
            {{-- Onay Formu --}}
            <form action="{{ route('proje.decideRequestByQuality', $iaa->id) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="approve">
                <button type="submit"
                    class="px-6 py-2.5 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 shadow-md transition transform hover:-translate-y-0.5">
                    ✅ Onayla ve İlet
                </button>
            </form>

            {{-- Red Butonu --}}
            <button onclick="document.getElementById('modalKaliteRed').classList.remove('hidden')"
                class="px-6 py-2.5 bg-white text-red-600 border border-red-200 rounded-lg font-bold hover:bg-red-50 hover:border-red-300 transition">
                ❌ Reddet (Geri Çevir)
            </button>
        </div>
    </div>

    {{-- MODAL: Kalite Red --}}
    <div id="modalKaliteRed" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/75 transition-opacity"
                onclick="document.getElementById('modalKaliteRed').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                <form action="{{ route('proje.decideRequestByQuality', $iaa->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-red-700 mb-2">Red Gerekçesi</h3>
                        <input type="hidden" name="action" value="reject">
                        <textarea name="not" rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Neden reddediyorsunuz? Takıma not..." required></textarea>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Reddet ve Geri Gönder
                        </button>
                        <button type="button" onclick="document.getElementById('modalKaliteRed').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- 3. DURUM: SUPERADMIN ONAY EKRANI --}}
@if($iaa->durum == 'talep_onayi_bekliyor_superadmin' && auth()->check() && auth()->user()->hasRole('Superadmin'))
    <div class="mb-6 p-6 bg-indigo-900 text-white rounded-xl shadow-lg relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-xl font-bold flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                Son Karar: Talep Onayı
            </h3>
            <p class="text-indigo-200 mt-2 text-sm">
                Takım Lideri ve Kalite Yöneticisi bu dosyanın <strong>TALEP</strong> olduğu konusunda hemfikir.
                Onaylarsanız dosya "Talep Olarak Kapatıldı" statüsüne geçecek ve <u>puan dağıtılmayacaktır.</u>
            </p>

            <div class="mt-4 bg-white/10 p-3 rounded border border-white/20 text-sm">
                <p class="opacity-70 text-xs uppercase">Liderin Gerekçesi:</p>
                <p class="italic">"{{ $iaa->talep_gerekcesi }}"</p>
            </div>

            <div class="mt-6 flex gap-4">
                {{-- ONAY --}}
                <form action="{{ route('proje.decideRequestBySuperadmin', $iaa->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <button type="submit"
                        class="px-6 py-3 bg-white text-indigo-900 rounded-lg text-sm font-black hover:bg-gray-100 transition shadow-lg transform hover:-translate-y-0.5">
                        Evet, Taleptir. Dosyayı Kapat.
                    </button>
                </form>

                {{-- RED --}}
                <button onclick="document.getElementById('modalSuperRed').classList.remove('hidden')"
                    class="px-6 py-3 bg-transparent border-2 border-white/30 text-white rounded-lg text-sm font-bold hover:bg-white/10 transition">
                    Hayır, Bu Bir Şikayettir (İade Et)
                </button>
            </div>
        </div>

        {{-- Arkaplan Efekti --}}
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500 rounded-full blur-3xl opacity-20"></div>
    </div>

    {{-- MODAL: Superadmin Red --}}
    <div id="modalSuperRed" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/80 transition-opacity"
                onclick="document.getElementById('modalSuperRed').classList.add('hidden')"></div>
            <div
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">İade Gerekçesi</h3>
                <p class="text-xs text-gray-500 mb-4">Dosya Kalite Yöneticisine geri gönderilecektir.</p>
                <form action="{{ route('proje.decideRequestBySuperadmin', $iaa->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <textarea name="not" rows="3" class="w-full border-gray-300 rounded-lg text-sm mb-4"
                        placeholder="Gerekçeniz..." required></textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modalSuperRed').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-200 rounded-lg text-gray-700 text-xs font-bold">İptal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-900 text-white rounded-lg text-xs font-bold">Kaydet
                            ve İade Et</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif