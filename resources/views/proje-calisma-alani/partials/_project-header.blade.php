<div class="w-full">
    <div class="bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl shadow-lg border border-gray-200">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-5 border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Proje Künyesi</h3>
            </div>
            
            {{-- HIZLI LİNKLER (QUICK ANCHORS) --}}
            <div class="flex flex-wrap items-center gap-2">
                @if($iaa->musteriSikayeti && $iaa->musteriSikayeti->iadeler->isNotEmpty())
                    <a href="#iade-hurda-alani" onclick="document.getElementById('iade-hurda-alani').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 rounded-lg text-xs font-bold transition-all shadow-sm transform hover:scale-105">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        İadeler
                    </a>
                @endif
                
                @if($iaa->visit_planned)
                    @php
                        $visitDotClass = 'hidden';
                        if ($iaa->ziyaretPlani && $iaa->musteriSikayeti) {
                            $vStatus = $iaa->ziyaretPlani->status;
                            if (in_array($vStatus, ['Beklemede', 'Onaylandı', 'Revizyon Bekliyor'])) {
                                $visitDotClass = 'bg-red-500 animate-pulse';
                            } elseif ($vStatus == 'Tamamlandı') {
                                $visitDotClass = 'bg-emerald-500 animate-pulse';
                            }
                        }
                    @endphp
                    <a href="#ziyaret-bilgileri-alani" onclick="document.getElementById('ziyaret-bilgileri-alani').scrollIntoView({behavior: 'smooth'})" class="relative inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg text-xs font-bold transition-all shadow-sm transform hover:scale-105">
                        <span class="absolute -top-1 -right-1 flex h-3 w-3 {{ $visitDotClass == 'hidden' ? 'hidden' : '' }}">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $visitDotClass }} opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 {{ $visitDotClass }}"></span>
                        </span>
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Ziyaretler
                    </a>
                @endif
                
                <a href="#onay-durum-paneli" onclick="document.getElementById('onay-durum-paneli').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-lg text-xs font-bold transition-all shadow-sm transform hover:scale-105">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Onay Durumu
                </a>

                @if($iaa->musteriSikayeti)
                    <a href="{{ auth()->user()->is_personnel ? route('admin.sikayetler.show', $iaa->musteriSikayeti->id) : route('iaa.sikayetler.show', $iaa->musteriSikayeti->id) }}" class="inline-flex items-center px-3 py-1.5 bg-orange-50 text-orange-700 hover:bg-orange-100 border border-orange-200 rounded-lg text-xs font-bold transition-all shadow-sm transform hover:scale-105">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Şikayet Sayfasına Git
                    </a>
                @endif
            </div>
        </div>

        {{-- AKIŞ ŞABLONU VE ADIMLARI --}}
            <div class="bg-blue-50 rounded-lg p-4 shadow-sm border border-blue-100 col-span-1 md:col-span-4 mt-2 mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-1.5 bg-blue-100 rounded-md">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-blue-600/80">Uygulanan Akış</dt>
                        <dd class="text-sm font-bold text-blue-900 leading-tight">
                            {{ $workflow->name ?? 'Standart Akış' }}
                        </dd>
                    </div>
                </div>
                
                {{-- Adımları Görselleştirme Kısmı --}}
                @if(isset($steps) && count($steps) > 0)
                    <div class="flex flex-wrap items-center gap-y-3">
                        @foreach($steps as $step)
                            @php
                                $isCompleted = in_array($step->id, $completedStepIds);
                                
                                // Gizlilik verisini çekme
                                $pUpdate = isset($progressUpdates) ? ($progressUpdates[$step->id] ?? null) : null;
                                $isHidden = $pUpdate ? $pUpdate->is_hidden_from_customer : false;
                                
                                // Personel kontrolü
                                $isPersonnel = auth()->check() && auth()->user()->is_personnel;
                            @endphp

                            <div class="flex items-center group">
                                
                                {{-- 1. DURUM İKONU --}}
                                @if($isCompleted)
                                    {{-- Tamamlandı: Yeşil Tik --}}
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 flex items-center justify-center mr-1.5 border border-green-200" title="Tamamlandı">
                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    {{-- Bekliyor: Gri Daire --}}
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-white flex items-center justify-center mr-1.5 border-2 border-gray-300" title="Bekliyor">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                                    </div>
                                @endif

                                {{-- 2. ADIM ADI ve NUMARASI --}}
                                {{-- $loop->iteration: Döngüdeki sırayı verir (1, 2, 3...) --}}
                                <a href="#step-{{ $step->id }}" class="text-sm font-medium transition-colors hover:underline decoration-blue-400 decoration-2 underline-offset-2 cursor-pointer
                                    {{ $isCompleted ? 'text-gray-900' : 'text-gray-500' }}">
                                    {{ $loop->iteration }}. {{ $step->name }}
                                </a>

                                {{-- 3. GİZLİLİK KİLİDİ (SADECE PERSONEL GÖRÜR) --}}
                                @if($isHidden && $isPersonnel)
                                     <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 border border-red-200 select-none" title="Bu adım müşteriye GİZLENMİŞTİR">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                        GİZLİ
                                     </span>
                                @endif

                                {{-- 4. OK İŞARETİ (Son değilse) --}}
                                @if(!$loop->last)
                                    <svg class="w-4 h-4 text-blue-300 mx-2 sm:mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        
        <dl class="grid grid-cols-1 md:grid-cols-{{ $iaa->musteriSikayeti ? 4 : 5 }} gap-4">
            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                <dt class="text-xs font-medium text-gray-500 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Takım
                </dt>
                <dd class="text-sm font-bold text-gray-900">
                    <a href="{{ route('takimlar.show', $takim->id) }}" class="hover:text-indigo-600 hover:underline transition-colors flex items-center gap-1">
                        {{ $takim->ad }}
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </dd>
            </div>
            
            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                <dt class="text-xs font-medium text-gray-500 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Takım Lideri
                </dt>
                    @php
                        $isClosed = in_array($iaa->durum, ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']);
                        // Tamamlanmışsa dondurulan lideri, değilse takımın güncel liderini al
                        $displayLider = $isClosed ? ($iaa->tamamlayanLider ?? ($takim->lider ?? null)) : ($takim->lider ?? null);
                        $originalLider = $iaa->atamadakiLider;
                        // Eğer proje aktifse ve atandığı andaki liderden farklı biri şu an liderse uyarı göster
                        $leaderChanged = !$isClosed && $originalLider && $displayLider && ($originalLider->id != $displayLider->id);
                    @endphp
                    <dd class="text-sm font-bold text-gray-900">
                        @if($displayLider)
                            <div class="flex flex-col gap-1">
                                <a href="{{ route('profile.show', $displayLider->id) }}" class="hover:text-indigo-600 hover:underline transition-colors flex items-center gap-1">
                                    {{ $displayLider->name }}
                                    @if(method_exists($displayLider, 'trashed') && $displayLider->trashed())
                                        <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded border border-red-200">İşten Ayrıldı{{ $displayLider->termination_date ? ' (' . \Carbon\Carbon::parse($displayLider->termination_date)->format('d.m.Y') . ')' : '' }}</span>
                                    @endif
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>

                                @if($leaderChanged)
                                    <div class="inline-flex items-center gap-1.5 px-2 py-1 bg-orange-50 text-orange-700 border border-orange-100 rounded text-[10px] font-bold leading-none w-fit">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span>Önceki Lider: {{ $originalLider->name }} ({{ $originalLider->trashed() ? 'İşten Ayrıldı' . ($originalLider->termination_date ? ' - ' . \Carbon\Carbon::parse($originalLider->termination_date)->format('d.m.Y') : '') : 'Görev Değişikliği' }})</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <span class="text-red-500 text-xs italic">Lider Atanmamış</span>
                        @endif
                    </dd>
            </div>

            @if(!$iaa->musteriSikayeti)
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100 italic">
                    <dt class="text-xs font-medium text-amber-600 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        İlgili Öneri
                    </dt>
                    <dd class="text-sm font-bold text-gray-900">
                        <a href="{{ route('iaa.show', $iaa->id) }}" target="_blank" class="text-amber-700 hover:text-amber-900 hover:underline transition-colors flex items-center gap-1 font-bold">
                            İncele
                            <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </dd>
                </div>
            @endif
            
            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                <dt class="text-xs font-medium text-gray-500 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Başlangıç
                </dt>
                <dd class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($assignment->start_date)->format('d.m.Y') }}</dd>
            </div>

            
            <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-lg p-3 shadow-sm border-2 border-red-200 flex flex-col justify-between"
                x-data="extensionTimer('{{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->toISOString() : '' }}')"
                x-init="start()">
                <div class="flex justify-between items-start">
                    <div>
                        <dt class="text-xs font-medium text-red-600 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Hedef Bitiş (Termin)
                        </dt>
                        <dd class="text-sm font-bold text-red-700">{{ \Carbon\Carbon::parse($assignment->due_date)->format('d.m.Y H:i') }}</dd>
                    </div>
                </div>

                {{-- Countdown Timer / Tamamlanma Süresi --}}
                @if(in_array($iaa->durum, ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']))
                    <div class="mt-2 text-sm font-bold text-green-600 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $iaa->completion_duration_in_days ?? 'İşlem' }}</span> {{ $iaa->durum == 'Tamamlandı' ? 'tamamlandı' : 'kapatıldı' }}
                    </div>
                    
                    {{-- Gecikme ve Gerçekleşen Tarih Notu --}}
                    @if(isset($statusDate))
                        <div class="mt-1 text-[11px] font-medium text-gray-500">
                            Gerçekleşen: {{ \Carbon\Carbon::parse($statusDate)->format('d.m.Y H:i') }}
                            @if($assignment->due_date && \Carbon\Carbon::parse($statusDate)->startOfDay()->gt(\Carbon\Carbon::parse($assignment->due_date)->startOfDay()))
                                @php
                                    $gecikmeGun = \Carbon\Carbon::parse($assignment->due_date)->startOfDay()->diffInDays(\Carbon\Carbon::parse($statusDate)->startOfDay());
                                @endphp
                                @if($gecikmeGun > 0)
                                    <span class="text-red-500 font-bold ml-1">({{ $gecikmeGun }} gün gecikti)</span>
                                @endif
                            @endif
                        </div>
                    @endif
                @else
                    <div x-show="deadline" class="mt-2 text-sm font-black tracking-tight"
                        :class="{'text-red-800 animate-pulse': days < 0, 'text-green-600': days >= 0}" x-cloak>
                        <span x-text="Math.abs(Math.ceil((deadline - now) / (1000 * 60 * 60 * 24)))"></span> gün <span x-text="(deadline - now) < 0 ? 'gecikti' : 'kaldı'"></span>
                    </div>
                @endif

                {{-- Ek Süre Talep Et Butonu veya Durum Belirteci --}}
                @php
                    $isLeaderOrAdminHeader = auth()->check() && (($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || auth()->user()->hasRole('Superadmin') || ($isQualityManagerInterventionPower ?? false));
                    $extensionDurum = $iaa->musteriSikayeti->musteri_ek_sure_talep_durumu ?? null;
                    $isComplaint = $iaa->musteriSikayeti != null;
                    
                    // Butonun GİZLENECEĞİ durumlar
                    $gizlenecekDurumlar = [
                        'Tamamlandı', 
                        'hatali_bildirim_olarak_kapatildi', 
                        'talep_olarak_kapatildi', 
                        'hatali_bildirim_olarak_kapatildi',
                        'Bölüm Onayı Bekliyor', 
                        'Direktör Onayı Bekliyor', 
                        'Yönetici Onayı Bekliyor'
                    ];
                    $ekSureGosterilebilir = !in_array($iaa->durum, $gizlenecekDurumlar);

                    $isApprover = false;
                    $canSeeEvaluating = false;
                    if (auth()->check() && $isComplaint) {
                        if ($iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum) {
                            $isAdminHeader = auth()->user()->hasRole('Superadmin');
                            $direktorOnayiSetting = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->value('value');
                            $direktorOnayiAktifH = filter_var($direktorOnayiSetting, FILTER_VALIDATE_BOOLEAN);
                            $isDirectorH = $iaa->musteriSikayeti->sikayetKategori->bolum->director_id == auth()->id();
                            
                            if (($direktorOnayiAktifH && $isDirectorH) || (!$direktorOnayiAktifH && $isAdminHeader) || ($isAdminHeader)) {
                                $isApprover = true;
                            }

                            if ($isDirectorH || auth()->user()->hasRole('Direktör')) {
                                $canSeeEvaluating = true;
                            }
                        }
                        
                        // Lider, Admin, Kalite Yöneticisi (Müdahale Yetkili), Kurul veya Direktör ise sonuç/süreç modallarını görebilir
                        $isAuthorizedQuality = auth()->user()->hasRole('Bölüm Kalite Yöneticisi') && ($isQualityManagerInterventionPower ?? false);
                        if ($isLeaderOrAdminHeader || $isAuthorizedQuality || auth()->user()->hasRole('Kurul Üyesi') || auth()->user()->hasRole('Superadmin') || auth()->user()->hasRole('Direktör')) {
                            $canSeeEvaluating = true;
                        }
                    }
                @endphp

                @if($isComplaint && $isLeaderOrAdminHeader && $ekSureGosterilebilir && in_array($extensionDurum, [null, 'Reddedildi']) && !$iaa->musteriSikayeti->trashed())
                    <div class="mt-3">
                        <button type="button" @click="$dispatch('open-extension-modal')" class="w-full inline-flex justify-center items-center px-3 py-1.5 text-xs font-bold rounded-lg text-orange-700 bg-orange-100 hover:bg-orange-200 border border-orange-300 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @if($extensionDurum == 'Reddedildi') Yeni Ek Süre Talep Et @else Ek Süre Talep Et @endif
                        </button>
                    </div>
                @endif

                @if($isComplaint && $canSeeEvaluating)
                    @if($extensionDurum == 'Reddedildi')
                        <div class="mt-2 text-center">
                            <button type="button" @click="$dispatch('open-rejected-extension-modal')" class="text-xs font-semibold text-red-600 hover:text-red-800 transition-colors inline-flex items-center justify-center w-full">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                Önceki Talep Reddedildi
                            </button>
                        </div>
                    @elseif($extensionDurum == 'Onaylandı')
                        <div class="mt-3">
                            <button type="button" @click="$dispatch('open-approved-extension-modal')" class="w-full inline-flex justify-center items-center px-3 py-1.5 text-xs font-bold rounded-lg text-green-700 bg-green-100 hover:bg-green-200 border border-green-300 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Ek Süre Talebi Onaylandı
                            </button>
                        </div>
                    @elseif($extensionDurum == 'Talep Edildi')
                        @if($isApprover)
                            <div class="mt-3">
                                <button type="button" @click="$dispatch('open-extension-approval-modal')" class="w-full inline-flex justify-center items-center px-3 py-1.5 text-xs font-bold rounded-lg text-yellow-800 bg-yellow-100 hover:bg-yellow-200 border border-yellow-400 transition-colors shadow-sm animate-pulse">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Onay Bekliyor
                                </button>
                            </div>
                        @else
                            <div class="mt-3">
                                <button type="button" @click="$dispatch('open-evaluating-extension-modal')" class="w-full inline-flex justify-center items-center px-3 py-1.5 text-xs font-bold rounded-lg text-blue-700 bg-blue-100 hover:bg-blue-200 border border-blue-300 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Değerlendiriliyor
                                </button>
                            </div>
                        @endif
                    @endif
                @endif
            </div>
        </dl>
        <script>
            document.addEventListener('alpine:init', () => {
                if(!Alpine.data('extensionTimer')) {
                    Alpine.data('extensionTimer', (deadlineString) => ({
                        deadline: deadlineString ? new Date(deadlineString).getTime() : null,
                        now: new Date().getTime(),
                        days: 0,
                        hours: 0,
                        minutes: 0,
                        seconds: 0,
                        interval: null,

                        start() {
                            if (!this.deadline) return;

                            this.updateTime();
                            this.interval = setInterval(() => {
                                this.updateTime();
                            }, 1000);
                        },

                        updateTime() {
                            this.now = new Date().getTime();
                            let distance = this.deadline - this.now;

                            if (distance < 0) {
                                clearInterval(this.interval);
                                this.days = 0;
                                this.hours = 0;
                                this.minutes = 0;
                                this.seconds = 0;
                                return;
                            }

                            this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        }
                    }));
                }
            });
        </script>

        <div class="mt-8 mb-4 animate-fade-in-up">
            <div class="relative overflow-hidden rounded-2xl border border-indigo-100 bg-indigo-50/40 p-4 sm:p-5 shadow-sm transition-all duration-300 hover:shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center border border-white bg-white/60 shadow-sm">
                        @php
                            $isAnimated = in_array($iaa->durum, ['Devam Ediyor', 'Atandı', 'Revize Ediliyor']) || mb_stripos($iaa->durum, 'bekliyor') !== false;
                        @endphp
                        @if($iaa->durum == 'Tamamlandı')
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($isAnimated)
                            <svg class="w-5 h-5 text-indigo-600 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        @else
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @endif
                    </div>
                    
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Mevcut Süreç Durumu</div>
                        <div class="flex items-center">
                            {!! $iaa->durum_etiketi !!}
                        </div>
                    </div>
                </div>
                
                @if(isset($statusDate))
                    <div class="flex items-center">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-600 bg-white shadow-sm px-4 py-2.5 rounded-xl border border-slate-200">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">İşlem Tarihi:</span>
                            <span class="text-[13px] tracking-tight">{{ \Carbon\Carbon::parse($statusDate)->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                @endif
                
            </div>
        </div>

        {{-- DURUM BİLGİLENDİRME KUTUSU (GÜNCELLENMİŞ) --}}
        @if($iaa->durum == 'Revize Ediliyor' || $iaa->durum == 'Tamamlanması Reddedildi' || $iaa->durum == 'Tamamlandı')
            @php
                // === 1. LOG VERİSİNİ ÇEK ===
                $logKaydi = null;
                $yapanKisi = 'Yönetici';
                $yapanUnvan = 'Yönetici';
                $islemTarihi = null;

                // Sadece Red veya Revize durumunda loga bakmaya gerek var
                if ($iaa->durum == 'Revize Ediliyor' || $iaa->durum == 'Tamamlanması Reddedildi') {
                    $aranacakKelime = ($iaa->durum == 'Revize Ediliyor') ? 'Revizyon' : 'Red';
                    
                    $logKaydi = \App\Models\IaaLog::where('iaa_id', $iaa->id)
                        ->where('eylem', 'like', '%' . $aranacakKelime . '%')
                        ->with('user')
                        ->latest()
                        ->first();
                } elseif ($iaa->durum == 'Tamamlandı') {
                    $logKaydi = \App\Models\IaaLog::where('iaa_id', $iaa->id)
                        ->whereIn('eylem', ['Proje Onaylandı', 'Direktör Onayı Verildi', 'Bölüm Onayı Verildi'])
                        ->with('user')
                        ->latest()
                        ->first();
                }

                if ($logKaydi && $logKaydi->user) {
                    $yapanKisi = $logKaydi->user->name;
                    $islemTarihi = $logKaydi->created_at;
                    
                    if ($logKaydi->user->hasRole('Superadmin')) {
                        $yapanUnvan = 'Süper Yönetici';
                    } elseif ($logKaydi->user->hasRole('Bölüm Kalite Yöneticisi')) {
                        $yapanUnvan = 'Bölüm Yöneticisi';
                    } elseif ($logKaydi->user->hasRole('Direktör')) {
                        $yapanUnvan = 'Direktör';
                    }
                } else {
                    $islemTarihi = $iaa->onaylanma_tarihi ?? $statusDate ?? now();
                }

                // === 2. KUTU AYARLARINI YAP ===
                $kutuAyar = match($iaa->durum) {
                    'Tamamlandı' => [
                        'baslik' => 'Proje Onaylandı',
                        'renk' => 'green',
                        'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                        'mesaj' => 'Proje yönetici tarafından onaylanarak başarıyla tamamlandı.'
                    ],
                    'Tamamlanması Reddedildi' => [
                        'baslik' => $yapanUnvan . ' (' . $yapanKisi . ') Reddedildi', // Başlık Dinamik Oldu
                        'renk' => 'red',
                        'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
                        'mesaj' => $iaa->yonetici_notu
                    ],
                    'Revize Ediliyor' => [
                        'baslik' => $yapanUnvan . ' Revizyon Talebi', // Başlık Dinamik Oldu
                        'renk' => 'yellow',
                        'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />',
                        'mesaj' => $iaa->yonetici_notu
                    ],
                    default => [
                        'renk' => 'gray', 'ikon' => '', 'baslik' => 'Bilinmeyen Durum', 'mesaj' => ''
                    ]
                };
            @endphp

            <div class="mt-6 p-4 bg-{{ $kutuAyar['renk'] }}-50 border-l-4 border-{{ $kutuAyar['renk'] }}-400 rounded-r-lg shadow-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-{{ $kutuAyar['renk'] }}-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            {!! $kutuAyar['ikon'] !!}
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-bold text-{{ $kutuAyar['renk'] }}-800 flex items-center gap-2">
                                    {{ $kutuAyar['baslik'] }}
                                    @if(isset($yapanKisi) && ($iaa->durum == 'Revize Ediliyor' || $iaa->durum == 'Tamamlanması Reddedildi'))
                                        <span class="text-xs font-normal bg-white/50 px-2 py-0.5 rounded text-{{ $kutuAyar['renk'] }}-700 border border-{{ $kutuAyar['renk'] }}-200">
                                            {{ $yapanKisi }}
                                        </span>
                                    @endif
                                </h4>
                                <div class="mt-2 text-sm text-{{ $kutuAyar['renk'] }}-700">
                                    <p class="whitespace-pre-wrap font-medium">"{{ $kutuAyar['mesaj'] }}"</p>
                                </div>
                            </div>
                            @if(!empty($islemTarihi))
                            <div class="ml-4 flex-shrink-0 text-xs text-{{ $kutuAyar['renk'] }}-600 font-medium flex items-center space-x-1.5 whitespace-nowrap">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h18"></path></svg>
                                <span>{{ \Carbon\Carbon::parse($islemTarihi)->format('d.m.Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm font-semibold text-gray-700">İlerleme Durumu</p>
                        <span class="text-2xl font-bold text-blue-600">{{ round($progressPercentage) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-500 shadow-sm" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-center md:justify-end gap-6">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-900">{{ $completedStepsCount }}</p>
                        <p class="text-xs text-gray-600">Tamamlanan</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-400">{{ $totalStepsCount - $completedStepsCount }}</p>
                        <p class="text-xs text-gray-600">Kalan</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-blue-600">{{ $totalStepsCount }}</p>
                        <p class="text-xs text-gray-600">Toplam Adım</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- İADE BİLGİSİ ÖZETİ (Sadece Onay Beklerken veya Tamamlanınca Görünür) --}}
    @if($iaa->musteriSikayeti && $iaa->musteriSikayeti->iadeler->isNotEmpty())
        @php $iade = $iaa->musteriSikayeti->iadeler->first(); @endphp
        <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-3 flex items-start gap-3 animate-pulse-slow">
            <div class="p-2 bg-red-100 rounded-full shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-red-900">İADE ALINMIŞTIR</h4>
                <p class="text-xs text-red-700 mt-0.5">
                    Bu şikayet kapsamında <strong>{{ $iaa->musteriSikayeti->customer->name ?? 'Müşteri' }}</strong> firmasından 
                    <strong>{{ $iade->miktar }} {{ $iade->birim }} {{ $iade->urun_turu }}</strong> ürün, 
                    <strong>{{ $iade->iade_sebebi }}</strong> sebebiyle iade alınmıştır.
                </p>
            </div>
        </div>
    @endif

</div>