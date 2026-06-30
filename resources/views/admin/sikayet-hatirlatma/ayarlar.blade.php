<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hatırlatma Sistemi Ayarları') }}
        </h2>
    </x-slot>

    @push('pageTitle')
        Hatırlatma Sistemi Ayarları | 
    @endpush

<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Başlık --}}
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Hatırlatma Sistemi Ayarları</h1>
            <p class="mt-2 text-slate-500 font-medium italic">Müşteri temsilcisi butonunun görünürlüğü, alıcılar ve bildirim şablonlarını yönetin.</p>
        </div>

        <form action="{{ route('admin.sikayet-hatirlatma.ayarlariKaydet') }}" method="POST" class="space-y-8">
            @csrf

            {{-- 1. GENEL SİSTEM AYARI --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 bg-rose-600 text-white flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-tight">Sistem Anahtarı</h3>
                        <p class="text-rose-100 text-xs font-medium">Bu ayar tüm sistemi etkiler.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="hatirlatma_sistemi_aktif" value="1" class="sr-only peer" {{ $ayarlar['aktif'] ? 'checked' : '' }}>
                        <div class="w-14 h-8 bg-rose-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>
                <div class="p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 uppercase tracking-tighter">Hatırlatma Butonu Görünürlüğü</p>
                            <p class="text-xs text-slate-500">Kapatıldığında hatırlatma butonu hiçbir sayfada görünmez.</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 shadow-inner mb-4">
                        <div>
                            <span class="text-sm font-black text-slate-600 uppercase tracking-widest block">Bekleme Süresi (Cooldown)</span>
                            <p class="text-[10px] text-slate-400 font-medium">İki hatırlatma arasındaki minimum süre.</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="number" name="hatirlatma_cooldown_saat" value="{{ $ayarlar['cooldown'] }}" class="w-20 rounded-xl border-slate-200 text-center font-black text-slate-700" min="0.01" step="0.01">
                            <span class="text-xs font-bold text-slate-400">SAAT</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 shadow-inner">
                        <div>
                            <span class="text-sm font-black text-slate-600 uppercase tracking-widest block">İlk Aktifleşme Süresi</span>
                            <p class="text-[10px] text-slate-400 font-medium">Şikayet girildikten kaç saat sonra hatırlat butonu aktif olur?</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="number" name="hatirlatma_ilk_aktif_saat" value="{{ $ayarlar['ilk_aktif_saat'] ?? 0 }}" class="w-20 rounded-xl border-slate-200 text-center font-black text-slate-700" min="0" step="0.01">
                            <span class="text-xs font-bold text-slate-400">SAAT</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. ALICI ROLLERİ (TOGGLE PANELİ) --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                <div class="flex items-center space-x-3 mb-8 border-b border-slate-100 pb-6">
                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Hatırlatma Alıcıları</h3>
                        <p class="text-slate-400 text-xs font-medium">Butona basıldığında hangi rollere bildirim gidecek?</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $roller = [
                            'hatirlatma_sikayeti_giren_bildir' => ['label' => 'Şikayeti Giren Personel', 'icon' => '👤', 'val' => $ayarlar['sikayeti_giren']],
                            'hatirlatma_cozum_lideri_bildir' => ['label' => 'Çözüm Takımı Lideri', 'icon' => '⚡', 'val' => $ayarlar['cozum_lideri']],
                            'hatirlatma_kalite_yoneticisi_bildir' => ['label' => 'Bölüm Kalite Yöneticisi', 'icon' => '🛡️', 'val' => $ayarlar['kalite_yoneticisi']],
                            'hatirlatma_bolum_lideri_bildir' => ['label' => 'Bölüm Lideri (Müdür)', 'icon' => '👔', 'val' => $ayarlar['bolum_lideri']],
                            'hatirlatma_direktor_bildir' => ['label' => 'Direktör', 'icon' => '💼', 'val' => $ayarlar['direktor']],
                            'hatirlatma_yonetim_bildir' => ['label' => 'Yönetim', 'icon' => '🏢', 'val' => $ayarlar['yonetim']],
                        ];
                    @endphp

                    @foreach($roller as $key => $data)
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-indigo-200 transition-all group">
                            <div class="flex items-center space-x-3">
                                <span class="text-xl grayscale group-hover:grayscale-0 transition-all">{{ $data['icon'] }}</span>
                                <span class="text-xs font-black text-slate-600 uppercase tracking-tighter">{{ $data['label'] }}</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="{{ $key }}" value="1" class="sr-only peer" {{ $data['val'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 shadow-inner"></div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 3. MESAJ ŞABLONLARI --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                <div class="flex items-center space-x-3 mb-8 border-b border-slate-100 pb-6">
                    <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Bildirim Mesaj Şablonları</h3>
                        <p class="text-slate-400 text-xs font-medium">Mail ve zil bildirim metinlerini özelleştirin.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Hatırlatma Mail Konusu</label>
                        <input type="text" name="hatirlatma_mail_konu" value="{{ $ayarlar['mail_konu'] }}" class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Hatırlatma Mail Gövdesi</label>
                        <textarea name="hatirlatma_mail_govde" rows="4" class="block w-full rounded-2xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm leading-relaxed text-slate-600">{{ $ayarlar['mail_govde'] }}</textarea>
                    </div>
                    
                    <div class="bg-slate-50 p-4 rounded-2xl border border-dashed border-slate-300">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Kullanılabilir Değişkenler</p>
                        <div class="flex flex-wrap gap-2">
                            @php $tags = ['{sikayet_konusu}', '{musteri_adi}', '{firma_adi}', '{tarih}', '{gonderen_adi}', '{sikayet_link}']; @endphp
                            @foreach($tags as $tag)
                                <code class="px-2 py-1 bg-white border border-slate-200 rounded text-indigo-600 font-mono text-[10px]">{{ $tag }}</code>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- KAYDET BUTONU --}}
            <div class="flex justify-center pt-6">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center px-12 py-4 bg-slate-800 text-white font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-slate-200 hover:bg-slate-900 transition-all hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Ayarları Güncelle
                </button>
            </div>

        </form>
    </div>
</div>
</x-app-layout>
