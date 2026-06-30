<x-app-layout>
    @push('pageTitle'){{ isset($kural) ? 'Kural Düzenle' : 'Yeni Kural Ekle' }} | @endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('admin.sikayet-yonetici-rapor.index') }}" class="text-indigo-600 hover:text-indigo-800 mr-2">&larr;</a>
            {{ isset($kural) ? __('Kural Düzenle') : __('Yeni Rapor Kuralı') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
                <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                    <h1 class="text-xl font-black text-slate-800 uppercase tracking-tight">{{ isset($kural) ? 'Kuralı Düzenle' : 'Yeni Kural Oluştur' }}</h1>
                    <p class="text-sm text-slate-500 font-medium mt-1">Bu kural Yöneticilere sistem performans durumlarını iletir.</p>
                </div>

                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-8 p-4 bg-rose-50 border border-rose-200 rounded-2xl">
                            <ul class="list-disc list-inside text-sm text-rose-600 font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($kural) ? route('admin.sikayet-yonetici-rapor.update', $kural) : route('admin.sikayet-yonetici-rapor.store') }}" method="POST" class="space-y-8">
                        @csrf
                        @if(isset($kural)) @method('PUT') @endif

                        {{-- TEMEL BİLGİLER --}}
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-slate-100 pb-2">Temel Ayarlar</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Kural Adı <span class="text-rose-500">*</span></label>
                                    <input type="text" name="ad" value="{{ old('ad', $kural->ad ?? '') }}" required class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-colors">
                                </div>
                                
                                <div class="col-span-2 flex items-center p-4 bg-emerald-50/50 rounded-xl border border-emerald-100">
                                    <input type="checkbox" name="aktif" value="1" id="aktif" {{ old('aktif', $kural->aktif ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                                    <label for="aktif" class="ml-3 flex flex-col cursor-pointer">
                                        <span class="text-sm font-black text-slate-800">Kural Aktif</span>
                                        <span class="text-xs font-medium text-slate-500">Bu kural çalıştırılacak</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- ZAMANLAMA --}}
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-slate-100 pb-2">Zamanlama & Sıklık</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Gönderim Sıklığı</label>
                                    <select name="siklik" id="siklikSelect" class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-colors">
                                        <option value="gunluk" {{ old('siklik', $kural->siklik ?? '') == 'gunluk' ? 'selected' : '' }}>Her Gün</option>
                                        <option value="haftalik" {{ old('siklik', $kural->siklik ?? 'haftalik') == 'haftalik' ? 'selected' : '' }}>Haftanın Belirli Günleri</option>
                                        <option value="aylik" {{ old('siklik', $kural->siklik ?? '') == 'aylik' ? 'selected' : '' }}>Her Ayın İlk Günü</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Çalışma Saati</label>
                                    <input type="time" name="saat" value="{{ old('saat', isset($kural) ? \Carbon\Carbon::parse($kural->saat)->format('H:i') : '09:00') }}" required class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-colors">
                                </div>

                                <div class="col-span-2" id="haftaninGunleriDiv" style="display: {{ old('siklik', $kural->siklik ?? 'haftalik') == 'haftalik' ? 'block' : 'none' }};">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-3">Hangi Günler Gönderilsin?</label>
                                    <div class="flex flex-wrap gap-3">
                                        @php
                                            $gunler = ['Pazartesi', 'Sali', 'Carsamba', 'Persembe', 'Cuma', 'Cumartesi', 'Pazar'];
                                            $seciliGunler = old('haftanin_gunleri', isset($kural) ? (is_array($kural->haftanin_gunleri) ? $kural->haftanin_gunleri : json_decode($kural->haftanin_gunleri, true)) : ['Pazartesi']) ?? [];
                                        @endphp
                                        @foreach($gunler as $gun)
                                            <label class="inline-flex items-center p-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition-all has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500">
                                                <input type="checkbox" name="haftanin_gunleri[]" value="{{ $gun }}" {{ in_array($gun, $seciliGunler) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="ml-2 text-xs font-bold text-slate-700 uppercase">{{ $gun }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BİLDİRİM KANALLARI --}}
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-slate-100 pb-2">Bildirim Kanalları</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col gap-4">
                                    <label class="inline-flex items-center p-4 bg-white border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-indigo-500 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500">
                                        <input type="checkbox" name="mail_aktif_et" value="1" {{ old('mail_aktif_et', $kural->mail_aktif_et ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <div class="ml-3">
                                            <span class="block text-sm font-black text-slate-800 uppercase tracking-tight">E-Posta Gönder</span>
                                            <span class="block text-[10px] font-bold text-slate-400 mt-0.5">Sistem kayıtlı e-posta adresine rapor iletilir</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="flex flex-col gap-4">
                                    <label class="inline-flex items-center p-4 bg-white border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-indigo-500 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500">
                                        <input type="checkbox" name="zili_aktif_et" value="1" {{ old('zili_aktif_et', $kural->zili_aktif_et ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <div class="ml-3">
                                            <span class="block text-sm font-black text-slate-800 uppercase tracking-tight">Sistem Zili (Bildirim Merkezi)</span>
                                            <span class="block text-[10px] font-bold text-slate-400 mt-0.5">Sistem içi bildirim panelinde gösterilir</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- İÇERİK AYARLARI --}}
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-slate-100 pb-2">Mesaj & İçerik</h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Mail Konusu</label>
                                    <input type="text" name="mail_konusu" value="{{ old('mail_konusu', $kural->mail_konusu ?? 'Müşteri Şikayeti Yönetici Raporu') }}" class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-colors">
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Zil Bildirim Metni</label>
                                    <input type="text" name="bildirim_metni" value="{{ old('bildirim_metni', $kural->bildirim_metni ?? 'Haftalık Müşteri Şikayeti Kurul Raporunuz hazır.') }}" class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold shadow-sm transition-colors">
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Mail İçerik Metni (Opsiyonel Ön Yazı)</label>
                                    <textarea name="mail_taslagi" rows="4" class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium shadow-sm transition-colors">{{ old('mail_taslagi', $kural->mail_taslagi ?? 'Aşağıda yönettiğiniz ekibin güncel performans özet raporunu bulabilirsiniz.') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                            <a href="{{ route('admin.sikayet-yonetici-rapor.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 text-sm font-black rounded-xl hover:bg-slate-50 transition-colors uppercase tracking-widest">
                                İptal
                            </a>
                            <button type="submit" class="px-8 py-3 bg-indigo-600 text-white text-sm font-black rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest">
                                {{ isset($kural) ? 'Değişiklikleri Kaydet' : 'Kuralı Oluştur' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('siklikSelect').addEventListener('change', function() {
            if (this.value === 'haftalik') {
                document.getElementById('haftaninGunleriDiv').style.display = 'block';
            } else {
                document.getElementById('haftaninGunleriDiv').style.display = 'none';
            }
        });
    </script>
    @endpush
</x-app-layout>
