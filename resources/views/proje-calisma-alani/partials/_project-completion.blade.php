@php
    // 1. Yetki ve Veri Hazırlığı
    $isLeader = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || (auth()->check() && auth()->user()->hasRole('Superadmin')) || ($isQualityManagerInterventionPower ?? false);
    
    // GÜVENLİ ERİŞİM: musteriSikayeti null ise hata vermemesi için
    $sikayet = $iaa->musteriSikayeti; 
    
    // Şikayet varsa bölüm ID'sini al, yoksa 0 (Tanımları çekmek için)
    $bolumId = $sikayet ? ($sikayet->sikayetKategori->bolum_id ?? 0) : 0;
    
    // Tanımları Çek (Sadece Şikayet Varsa Anlamlıdır)
    $tanimlar = collect();
    $urunGruplari = collect();
    $iadeSebepleri = collect();
    $birimler = collect();

    if ($sikayet) {
        $tanimlar = \App\Models\IadeTanimi::where('bolum_id', $bolumId)->where('aktif', true)->get()->groupBy('tip');
        $urunGruplari = $tanimlar['urun_grubu'] ?? collect();
        $iadeSebepleri = $tanimlar['iade_sebebi'] ?? collect();
        $birimler = $tanimlar['birim'] ?? collect();
    }

    // Mevcut İade Var mı? (Revizyon için)
    $mevcutIade = ($sikayet && $sikayet->iadeler) ? $sikayet->iadeler->first() : null;
    
    // Varsayılan: Eğer iade varsa toggle açık (true), yoksa kapalı (false)
    $iadeVarMi = $mevcutIade ? 'true' : 'false';

    // Ziyaret Bekliyor Mu?
    $ziyaretBekliyorMu = \App\Models\IaaZiyaretPlani::where('iaa_id', $iaa->id)
        ->whereIn('status', ['Beklemede', 'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetim Onayı Bekliyor', 'Revizyon Bekliyor'])
        ->exists();

    // Genel Ziyaret Kontrolü
    $genelZiyaret = \App\Models\IaaZiyaretPlani::where('iaa_id', $iaa->id)->whereNull('iaa_workflow_step_id')->first();
    $genelZiyaretVarMi = $genelZiyaret ? 'true' : 'false';
@endphp

{{-- Sadece aktif süreçte ve %100 tamamlandıysa göster --}}
@if($progressPercentage == 100 && in_array($iaa->durum, ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor']))

    {{-- A) LİDER DEĞİLSE UYARI (Sadece Lider Kapatabilir) --}}
    @if(!$isLeader)
        <div class="mt-8 mb-8">
            <div class="p-6 bg-blue-50 border border-blue-200 rounded-xl flex items-start gap-4 animate-fade-in-up mb-4">
                <div class="p-3 bg-blue-100 rounded-full shrink-0 mt-1">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="w-full">
                    <h4 class="text-lg font-bold text-blue-900">Tüm Adımlar Tamamlandı</h4>
                    <p class="text-sm text-blue-700">
                        @if($genelZiyaret)
                            @php
                                $onaylayacakKisiMakam = 'Yönetici';
                                if ($genelZiyaret->status === 'Direktör Onayı Bekliyor') $onaylayacakKisiMakam = 'Direktör';
                                elseif ($genelZiyaret->status === 'Yönetim Onayı Bekliyor') $onaylayacakKisiMakam = 'Yönetim';
                                elseif ($genelZiyaret->status === 'Bölüm Onayı Bekliyor') $onaylayacakKisiMakam = 'Bölüm Yöneticisi';
                            @endphp
                            Tüm Adımlar Tamamlandı. Proje Lideri ({{ $iaa->atananTakim->lider->name ?? 'Lider' }}) bir müşteri ziyareti planladı. Ziyaret planı {{ $onaylayacakKisiMakam }} tarafından onaylandıktan sonra süreç devam edecektir.
                        @else
                            Süreç başarıyla nihayete erdi. Proje Lideri ({{ $iaa->atananTakim->lider->name ?? 'Lider' }}) projeyi sonlandırıp onaya gönderecektir. Lütfen bekleyiniz.
                        @endif
                    </p>
                </div>
            </div>
            
            {{-- Lider olmayanlar (Direktör, Müşteri Temsilcisi vb.) için Ziyaret Bilgisi --}}
            @if($genelZiyaret)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden p-2">
                    @livewire('project.plan-visit', ['iaa' => $iaa, 'embedded' => true, 'isStepEnd' => true], key('visit-form-readonly-'.$iaa->id))
                </div>
            @endif
        </div>
    
    @else
        {{-- B) LİDER İÇİN KAPANIŞ EKRANI --}}
        
        <div class="mt-8 mb-8 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            
            {{-- Başlık --}}
            <div class="p-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex items-center gap-3">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Proje Kapanış İşlemleri</h4>
                    <p class="text-xs text-gray-500">Projeyi tamamlamak için aşağıdaki adımları takip edin.</p>
                </div>
            </div>

            <div class="p-6">

                {{-- DURUM 1: MÜŞTERİ ŞİKAYETİ İSE (İADE SORUSU SORULUR) --}}
                @if($iaa->musteriSikayeti)
                    
                    <div x-data="{ iadeVar: {{ $iadeVarMi }}, ziyaretVar: {{ $genelZiyaretVarMi }} }" @visit-synced.window="ziyaretVar = true">
                        
                        {{-- 1. ADIM: MÜŞTERİ ZİYARETİ --}}
                        <div class="mb-8 overflow-hidden rounded-2xl border border-indigo-100 shadow-sm">
                            <div class="flex items-center justify-between bg-indigo-50/50 p-4 transition-colors hover:bg-indigo-50">
                                <div>
                                    <span class="block text-sm font-extrabold text-indigo-900 uppercase tracking-wider">ADIM 1: Müşteri Ziyareti</span>
                                    <span class="text-xs text-indigo-600 font-medium" x-text="ziyaretVar ? 'Ziyaret planı detaylarını aşağıda doldurabilirsiniz.' : 'Bu proje için bir ziyaret planlanmadı mı?'"></span>
                                </div>
                                
                                <template x-if="!ziyaretVar">
                                    <button type="button" 
                                            @click="ziyaretVar = !ziyaretVar"
                                            class="bg-gray-300 relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        <span aria-hidden="true" 
                                              class="translate-x-0 pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                        </span>
                                    </button>
                                </template>
                                <template x-if="ziyaretVar">
                                    <div 
                                        class="flex items-center gap-2 font-bold text-[10px] uppercase"
                                        :class="{ 
                                            'text-green-600 cursor-pointer hover:text-green-700': !({{ ($genelZiyaret && $genelZiyaret->status === 'Tamamlandı') ? 'true' : 'false' }}),
                                            'text-green-500 opacity-75 cursor-not-allowed': {{ ($genelZiyaret && $genelZiyaret->status === 'Tamamlandı') ? 'true' : 'false' }}
                                        }"
                                        @if(!($genelZiyaret && $genelZiyaret->status === 'Tamamlandı'))
                                            @click="ziyaretVar = false"
                                        @endif
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        Planlandı{{ ($genelZiyaret && $genelZiyaret->status === 'Tamamlandı') ? '' : ' (Kilidi Aç)' }}
                                    </div>
                                </template>
                            </div>

                            <div x-show="ziyaretVar" x-transition class="p-2 bg-white">
                                @livewire('project.plan-visit', ['iaa' => $iaa, 'embedded' => true, 'isStepEnd' => true], key('visit-form-leader-'.$iaa->id))
                            </div>
                        </div>

                        {{-- 2. ADIM: ÜRÜN İADESİ --}}
                        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-100 shadow-sm">
                            <div class="flex items-center justify-between bg-gray-50 p-4 transition-colors hover:bg-gray-100">
                                <div>
                                    <span class="block text-sm font-extrabold text-gray-900 uppercase tracking-wider">ADIM 2: Ürün İadesi</span>
                                    <span class="text-xs text-gray-500 font-medium" x-text="iadeVar ? 'İade detaylarını aşağıda doldurabilirsiniz.' : 'Herhangi bir iade işlemi yapılmayacak.'"></span>
                                </div>
                                
                                <button type="button" 
                                        @click="iadeVar = !iadeVar"
                                        :class="iadeVar ? 'bg-red-500' : 'bg-gray-300'"
                                        class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <span aria-hidden="true" 
                                          :class="iadeVar ? 'translate-x-5' : 'translate-x-0'"
                                          class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                    </span>
                                </button>
                            </div>

                            {{-- İade YOK (İadesiz Tamamlama Butonu) --}}
                            <div x-show="!iadeVar" class="p-6 bg-white text-center space-y-4">
                                <div class="inline-flex p-3 bg-green-50 rounded-full text-green-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-800">İadesiz Kapatma</h5>
                                    <p class="text-xs text-gray-500 mt-1">İade girişi yapılmadan proje onaya sunulacaktır.</p>
                                </div>
                                
                                <form action="{{ route('proje.completeWithoutReturn', $iaa->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            :disabled="ziyaretVar && {{ $ziyaretBekliyorMu ? 'true' : 'false' }}"
                                            :class="(ziyaretVar && {{ $ziyaretBekliyorMu ? 'true' : 'false' }}) ? 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400' : 'bg-green-600 hover:bg-green-700 shadow-lg hover:shadow-green-500/30'"
                                            class="w-full sm:w-auto px-8 py-3 text-white font-bold rounded-xl transition flex items-center justify-center gap-2 mx-auto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Projeyi Tamamla ve Onaya Gönder
                                    </button>
                                    <div x-show="ziyaretVar && {{ $ziyaretBekliyorMu ? 'true' : 'false' }}" class="text-xs text-red-600 font-bold mt-3 text-center">
                                        Ziyaret planı onaylanmadan projeyi kapatamazsınız. Ziyaret onayını bekleyin veya 'Planlandı' kilidini açın.
                                    </div>
                                </form>

                                @if($mevcutIade)
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <p class="text-xs text-red-500 mb-2 font-bold">Uyarı: Sistemde kayıtlı eski bir iade bilgisi var.</p>
                                        <form action="{{ route('proje.deleteReturnInfo', $iaa->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 underline hover:text-red-800">Mevcut İade Kaydını Sil ve Devam Et</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- İade VAR (Form Alanı) --}}
                        <div x-show="iadeVar" x-transition class="bg-white border border-red-100 rounded-xl p-5 shadow-sm">
                            <form action="{{ route('proje.completeWithReturn', $iaa->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">İade Tarihi</label>
                                            <input type="date" name="iade_tarihi" value="{{ ($mevcutIade && $mevcutIade->iade_tarihi) ? $mevcutIade->iade_tarihi->format('Y-m-d') : date('Y-m-d') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Ürün Grubu</label>
                                            <select name="urun_turu" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
                                                <option value="">Seçiniz...</option>
                                                @foreach($urunGruplari as $item)
                                                    <option value="{{ $item->deger }}" {{ ($mevcutIade && $mevcutIade->urun_turu == $item->deger) ? 'selected' : '' }}>{{ $item->deger }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">İade Sebebi</label>
                                            <select name="iade_sebebi" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
                                                <option value="">Seçiniz...</option>
                                                @foreach($iadeSebepleri as $item)
                                                    <option value="{{ $item->deger }}" {{ ($mevcutIade && $mevcutIade->iade_sebebi == $item->deger) ? 'selected' : '' }}>{{ $item->deger }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Toplam Parti</label>
                                                <input type="number" step="0.01" name="toplam_parti_miktari" value="{{ $mevcutIade->toplam_parti_miktari ?? '' }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="Toplam">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">İade Miktarı</label>
                                                <input type="number" step="0.01" name="miktar" value="{{ $mevcutIade->miktar ?? '' }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="İade" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Birim</label>
                                            <select name="birim" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
                                                <option value="">Seçiniz...</option>
                                                @foreach($birimler as $item)
                                                    <option value="{{ $item->deger }}" {{ ($mevcutIade && $mevcutIade->birim == $item->deger) ? 'selected' : '' }}>{{ $item->deger }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Dosya (Opsiyonel)</label>
                                            <input type="file" name="dosya" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Açıklama</label>
                                    <textarea name="aciklama" rows="2" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="Eklemek istediğiniz notlar...">{{ $mevcutIade->aciklama ?? '' }}</textarea>
                                </div>

                                <button type="submit" 
                                        :disabled="ziyaretVar && {{ $ziyaretBekliyorMu ? 'true' : 'false' }}"
                                        :class="(ziyaretVar && {{ $ziyaretBekliyorMu ? 'true' : 'false' }}) ? 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400' : 'bg-red-600 hover:bg-red-700 shadow-lg hover:shadow-red-500/30'"
                                        class="w-full py-3 text-white font-bold rounded-lg transition flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $mevcutIade ? 'Güncelle ve Onaya Gönder' : 'Kaydet ve Onaya Gönder' }}
                                </button>
                                <div x-show="ziyaretVar && {{ $ziyaretBekliyorMu ? 'true' : 'false' }}" class="text-xs text-red-600 font-bold mt-3 text-center">
                                    Ziyaret planı onaylanmadan projeyi kapatamazsınız. Ziyaret onayını bekleyin veya 'Planlandı' kilidini açın.
                                </div>
                            </form>
                        </div>
                    </div>

                {{-- DURUM 2: NORMAL İAA PROJESİ (TEK TUŞLA KAPAT) --}}
                @else
                    
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-900 text-lg">Projeyi Tamamla</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    Tüm adımlar tamamlandı. Projeyi sonlandırıp <strong>Yönetici Onayına</strong> göndermek için aşağıdaki butona tıklayın.
                                </p>
                                <p class="text-xs text-blue-500 mt-2 italic">(Not: Proje Onaylandıktan Sonra Düzenleme Yapamazsınız.)</p>
                            </div>
                        </div>

                        {{-- Tek Tuşla Kapatma --}}
                        <form action="{{ route('proje.completeWithoutReturn', $iaa->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    @if($ziyaretBekliyorMu) disabled class="opacity-50 cursor-not-allowed bg-gray-400 text-white font-bold py-3 px-8 rounded-xl flex items-center gap-2 whitespace-nowrap" @else class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2 whitespace-nowrap" @endif>
                                <span>Tamamla ve Onaya Gönder</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                            @if($ziyaretBekliyorMu)
                                <div class="text-xs text-red-600 font-bold mt-3 text-center">
                                    Ziyaret planı onaylanmadan projeyi kapatamazsınız. Ziyaret onayını bekleyin.
                                </div>
                            @endif
                        </form>
                    </div>

                @endif

            </div>
        </div>
    @endif

@endif