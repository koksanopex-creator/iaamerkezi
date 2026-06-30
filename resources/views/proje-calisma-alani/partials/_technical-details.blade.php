@if(isset($isComplaintProject) && $isComplaintProject && $iaa->musteriSikayeti)
    @php
        // Yetki Kontrolü: Sadece Lider ve Superadmin düzenleyebilir
        $isLeader = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id);
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('Superadmin');
        $isTeamMember = auth()->check() && $iaa->atanan_takim_id && auth()->user()->takimlar->contains('id', $iaa->atanan_takim_id);

        // Saf İAA ise tüm takım üyeleri düzenleyebilir, şikayet kaynaklı ise sadece lider/admin
        $canEditDetails = $isSuperAdmin || $isLeader || (is_null($iaa->musteri_sikayeti_id) && $isTeamMember);

        // Kısıtlama: Onayda veya Tamamlandıysa Lider DÜZENLEYEMEZ (Superadmin hariç)
        $kilitliDurumlar = [
            'Bölüm Onayı Bekliyor',
            'Yönetici Onayı Bekliyor',
            'Tamamlandı',
            'Talep Olarak Kapatıldı',
            'hatali_bildirim_olarak_kapatildi'
        ];
        if (!$isSuperAdmin && in_array($iaa->durum, $kilitliDurumlar)) {
            $canEditDetails = false;
        }

        // TAMAMEN KAPALI DURUMLAR (Superadmin dahil düzenleyemesin isteniyorsa - Opsiyonel ama talep bu yönde)
        if (in_array($iaa->durum, ['Tamamlandı', 'Talep Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi']) || ($iaa->musteriSikayeti && $iaa->musteriSikayeti->trashed())) {
            $canEditDetails = false;
        }

        // Veri Hazırlığı
        $technicalDetails = $iaa->musteriSikayeti->teknikDetaylar->map(function ($detay) {
            return [
                'lot_no' => $detay->lot_no,
                'machine_id' => $detay->machine_id,
                'genel_hammadde_id' => $detay->genel_hammadde_id,
                'urun_versiyonu_id' => $detay->urun_versiyonu_id
            ];
        })->toArray();

        // Eğer veritabanı boşsa (eski veri yoksa) en az 1 boş satır ekle
        if (empty($technicalDetails)) {
            $technicalDetails = [
                [
                    'lot_no' => $iaa->musteriSikayeti->lot_no, // Varsa eski sütundan al
                    'machine_id' => $iaa->musteriSikayeti->machine_id,
                    'genel_hammadde_id' => $iaa->musteriSikayeti->genel_hammadde_id,
                    'urun_versiyonu_id' => $iaa->musteriSikayeti->urun_versiyonu_id
                ]
            ];
        }

        // Eğer validation hatası varsa old() verilerini kullan
        if (old('lot_no')) {
            $technicalDetails = [];
            foreach (old('lot_no') as $key => $value) {
                $technicalDetails[] = [
                    'lot_no' => old('lot_no.' . $key),
                    'machine_id' => old('machine_id.' . $key),
                    'genel_hammadde_id' => old('genel_hammadde_id.' . $key),
                    'urun_versiyonu_id' => old('urun_versiyonu_id.' . $key),
                ];
            }
        }
    @endphp

    <div class="w-full mt-6">
        <div class="bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl shadow-lg border border-gray-200">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Şikayet Teknik Detayları</h3>
                        <p class="text-xs text-gray-500">Lot, Makine, Hammadde ve Versiyon bilgileri</p>
                    </div>
                </div>

                @if($canEditDetails)
                    <div
                        class="text-xs text-indigo-600 font-medium bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Düzenleme Modu Aktif
                    </div>
                @endif
            </div>

            @if($canEditDetails)
                {{-- DÜZENLEME FORMU (LİDER/ADMIN) --}}
                <form action="{{ route('proje.update-complaint-details', $iaa->id) }}" method="POST"
                    x-data="{ details: {{ json_encode($technicalDetails) }} }">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <template x-for="(detail, index) in details" :key="index">
                            <div class="relative bg-white p-4 rounded-lg border border-gray-200 shadow-sm group">

                                {{-- Satır Silme Butonu --}}
                                <button type="button" @click="details.splice(index, 1)" x-show="details.length > 1"
                                    class="absolute top-2 right-2 text-gray-300 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>

                                <span
                                    class="absolute -top-2 -left-2 bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-indigo-200"
                                    x-text="'#' + (index + 1)"></span>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    {{-- Lot Numarası --}}
                                    <div>
                                        <label :for="'lot_no_' + index"
                                            class="block text-xs font-bold text-gray-500 uppercase mb-1">Lot Numarası</label>
                                        <input type="text" name="lot_no[]" :id="'lot_no_' + index" x-model="detail.lot_no"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium transition-colors"
                                            placeholder="Lot No Giriniz">
                                    </div>

                                    {{-- Makine --}}
                                    <div>
                                        <label :for="'machine_id_' + index"
                                            class="block text-xs font-bold text-gray-500 uppercase mb-1">Makine Bilgisi</label>
                                        <select name="machine_id[]" :id="'machine_id_' + index" x-model="detail.machine_id"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium transition-colors">
                                            <option value="">Seçiniz</option>
                                            @foreach($machines as $machine)
                                                <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Hammadde --}}
                                    <div>
                                        <label :for="'genel_hammadde_id_' + index"
                                            class="block text-xs font-bold text-gray-500 uppercase mb-1">Kullanılan
                                            Hammadde</label>
                                        <select name="genel_hammadde_id[]" :id="'genel_hammadde_id_' + index"
                                            x-model="detail.genel_hammadde_id"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium transition-colors">
                                            <option value="">Seçiniz</option>
                                            @foreach($hammaddeler as $hammadde)
                                                <option value="{{ $hammadde->id }}">{{ $hammadde->ad }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Ürün Versiyonu --}}
                                    <div>
                                        <label :for="'urun_versiyonu_id_' + index"
                                            class="block text-xs font-bold text-gray-500 uppercase mb-1">Ürün Versiyonu</label>
                                        <select name="urun_versiyonu_id[]" :id="'urun_versiyonu_id_' + index"
                                            x-model="detail.urun_versiyonu_id"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium transition-colors">
                                            <option value="">Seçiniz</option>
                                            @foreach($versiyonlar as $versiyon)
                                                <option value="{{ $versiyon->id }}">{{ $versiyon->ad }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <button type="button"
                            @click="details.push({lot_no: '', machine_id: '', genel_hammadde_id: '', urun_versiyonu_id: ''})"
                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-xs font-bold transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Yeni Satır Ekle
                        </button>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Detayları Güncelle
                        </button>
                    </div>
                </form>
            @else
                {{-- SADECE GÖRÜNTÜLEME (DİĞERLERİ) --}}
                @if($iaa->musteriSikayeti->teknikDetaylar->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($iaa->musteriSikayeti->teknikDetaylar as $detay)
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative bg-white p-3 rounded-lg border border-gray-100">
                                <span class="absolute top-1 right-2 text-[10px] font-bold text-gray-300">#{{ $loop->iteration }}</span>
                                {{-- Lot Numarası --}}
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Lot Numarası</dt>
                                    <dd class="text-sm font-bold text-gray-900">
                                        {{ $detay->lot_no ?? '-' }}
                                    </dd>
                                </div>

                                {{-- Makine --}}
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Makine Bilgisi</dt>
                                    <dd class="text-sm font-bold text-gray-900">
                                        {{ $detay->machine->name ?? '-' }}
                                    </dd>
                                </div>

                                {{-- Hammadde --}}
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Kullanılan Hammadde</dt>
                                    <dd class="text-sm font-bold text-gray-900">
                                        {{ $detay->genelHammadde->ad ?? '-' }}
                                    </dd>
                                </div>

                                {{-- Versiyon --}}
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Ürün Versiyonu</dt>
                                    <dd class="text-sm font-bold text-gray-900">
                                        {{ $detay->urunVersiyonu->ad ?? '-' }}
                                    </dd>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- ESKİ TİP GÖRÜNTÜLEME (Fallback) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {{-- Lot Numarası --}}
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Lot Numarası</dt>
                            <dd class="text-sm font-bold text-gray-900">
                                {{ $iaa->musteriSikayeti->lot_no ?? '-' }}
                            </dd>
                        </div>

                        {{-- Makine --}}
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Makine Bilgisi</dt>
                            <dd class="text-sm font-bold text-gray-900">
                                {{ $iaa->musteriSikayeti->machine->name ?? '-' }}
                            </dd>
                        </div>

                        {{-- Hammadde --}}
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Kullanılan Hammadde</dt>
                            <dd class="text-sm font-bold text-gray-900">
                                {{ $iaa->musteriSikayeti->genelHammadde->ad ?? '-' }}
                            </dd>
                        </div>

                        {{-- Versiyon --}}
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <dt class="text-xs font-bold text-gray-500 uppercase mb-1">Ürün Versiyonu</dt>
                            <dd class="text-sm font-bold text-gray-900">
                                {{ $iaa->musteriSikayeti->urunVersiyonu->ad ?? '-' }}
                            </dd>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- BİLDİRİM DETAYLARI (Müşteri Temsilcisi Talebi) --}}
    @if($iaa->musteriSikayeti)
        @php
            $sikayetSnapshot = $iaa->musteriSikayeti->notified_snapshot ? json_decode($iaa->musteriSikayeti->notified_snapshot, true) : null;
            
            // Hover kartı fonksiyonu (Tooltip içeriğini döner)
            if(!function_exists('renderUserCardTooltip')){
                function renderUserCardTooltip($userData, $roleLabel) {
                    if (!$userData) return '';
                    
                    $name = is_array($userData) ? ($userData['name'] ?? 'İsimsiz') : ($userData->name ?? 'İsimsiz');
                    $email = is_array($userData) ? ($userData['email'] ?? null) : ($userData->email ?? null);
                    $phone = is_array($userData) ? ($userData['phone'] ?? null) : ($userData->telefon ?? $userData->phone ?? null);
                    $photoPath = is_array($userData) 
                        ? ($userData['photo'] ?? $userData['profile_photo_path'] ?? null) 
                        : ($userData->profile_photo_path ?? $userData->photo ?? null);
                    
                    $photo = ($photoPath && trim($photoPath) !== '') ? asset('storage/'.$photoPath) : null;
                    $nameFirstLetter = substr($name, 0, 1);
                    
                    $emailHtml = $email ? '
                        <div class="flex items-center gap-2.5 text-gray-600 font-medium">
                            <div class="w-5 h-5 rounded bg-gray-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="truncate">'.$email.'</span>
                        </div>' : '';

                    $phoneHtml = $phone ? '
                        <div class="flex items-center gap-2.5 text-gray-600 font-medium">
                            <div class="w-5 h-5 rounded bg-gray-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <span class="truncate">'.$phone.'</span>
                        </div>' : '';

                    return '
                    <div class="invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-300 absolute z-50 bottom-full left-1/2 -translate-x-1/2 mb-3 w-64 p-4 bg-white rounded-xl shadow-2xl border border-indigo-100 pointer-events-none">
                        <div class="flex items-center gap-3 mb-3">
                            '.($photo ? '<img src="'.$photo.'" class="w-12 h-12 rounded-full object-cover border-2 border-indigo-50 shadow-sm">' : '<div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-lg shadow-sm">'.$nameFirstLetter.'</div>').'
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 leading-tight truncate">'.$name.'</p>
                                <p class="text-[10px] font-medium text-indigo-600 uppercase tracking-wider">'.$roleLabel.'</p>
                            </div>
                        </div>
                        <div class="space-y-2 border-t border-gray-50 pt-3 text-xs">
                            '.$emailHtml.'
                            '.$phoneHtml.'
                        </div>
                        <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-r border-b border-indigo-100 rotate-45"></div>
                    </div>';
                }
            }
        @endphp

        <div x-data="{ showNotifications: false }" class="mt-8 pt-8 border-t border-gray-200">
            {{-- BAŞLIK VE TOGGLE --}}
            <div @click="showNotifications = !showNotifications" class="flex items-center justify-between cursor-pointer group/header mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100 ring-4 ring-indigo-50 group-hover/header:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 tracking-tight">Bildirim ve Bilgilendirme Geçmişi</h4>
                        <p class="text-sm text-gray-500 font-medium">Şikayet ilk açıldığında otomatik bilgilendirilen kişiler</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="px-3 py-1 bg-gray-100 rounded-lg text-[10px] font-black text-gray-600 uppercase tracking-widest border border-gray-200 shadow-sm leading-none inline-block">
                            {{ $iaa->musteriSikayeti->notified_snapshot ? 'SNAPSHOT AKTİF' : 'ESKİ KAYIT' }}
                        </div>
                        <p class="mt-1 text-[11px] font-bold text-gray-400 tracking-tighter">{{ $iaa->musteriSikayeti->created_at ? \Carbon\Carbon::parse($iaa->musteriSikayeti->created_at)->translatedFormat('d F Y H:i') : '-' }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover/header:bg-indigo-50 group-hover/header:text-indigo-600 transition-all">
                        <svg class="w-6 h-6 transform transition-transform duration-300" :class="showNotifications ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            {{-- İÇERİK (AKORDEON) --}}
            <div x-show="showNotifications" x-cloak x-collapse x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="pt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if($sikayetSnapshot)
                        {{-- SNAPSHOT VARSA --}}
                        @foreach($sikayetSnapshot as $item)
                            <div class="p-5 bg-white rounded-2xl border-2 border-gray-100 shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all duration-300 transform hover:-translate-y-1 relative group cursor-help">
                                <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500 opacity-60"></div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded shadow-sm">{{ $item['role_label'] }}</span>
                                    <div class="flex gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm ring-2 ring-white" title="Zil Bildirimi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" /></svg>
                                        </div>
                                        <div class="w-7 h-7 rounded-full bg-green-50 flex items-center justify-center text-green-500 shadow-sm ring-2 ring-white" title="E-Posta">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" /></svg>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-gray-900 font-bold transition-colors group-hover:text-indigo-600 block">{{ $item['name'] ?? 'İsimsiz' }}</span>
                                {!! renderUserCardTooltip($item, $item['role_label']) !!}
                            </div>
                        @endforeach
                    @else
                        {{-- SNAPSHOT YOKSA FALLBACK --}}
                        @php
                            $director = optional($iaa->musteriSikayeti->sikayetKategori->bolum)->director;
                            $kategori = $iaa->musteriSikayeti->sikayetKategori;
                            $kaliteYoneticileri = $kategori ? \App\Models\User::whereHas('yonettigiSikayetKategorileri', function($q) use ($kategori) {
                                $q->where('sikayet_kategori_id', $kategori->id);
                            })->get() : collect();
                            $deptLeaders = $kategori ? \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $kategori->bolum_id)->get() : collect();
                            
                            // Tüm diğer temsilcileri ekle
                            $otherReps = $iaa->musteriSikayeti->customer_id ? 
                                \App\Models\User::role('Müşteri Temsilcisi')->where('customer_id', $iaa->musteriSikayeti->customer_id)->get() : collect();
                        @endphp

                        {{-- Direktör --}}
                        @if($director)
                            <div class="p-5 bg-white rounded-2xl border-2 border-gray-100 shadow-sm relative group cursor-help transition-all hover:shadow-xl hover:border-blue-200 hover:-translate-y-1">
                                <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500 opacity-60"></div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded">Bölüm Direktörü</span>
                                </div>
                                <span class="text-gray-900 font-bold transition-colors group-hover:text-blue-600 block">{{ $director->name }}</span>
                                {!! renderUserCardTooltip($director, 'Bölüm Direktörü') !!}
                            </div>
                        @endif

                        {{-- Bölüm Liderleri (Müdürler) --}}
                        @foreach($deptLeaders as $dlider)
                            <div class="p-5 bg-white rounded-2xl border-2 border-gray-100 shadow-sm relative group cursor-help transition-all hover:shadow-xl hover:border-indigo-200 hover:-translate-y-1">
                                <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500 opacity-60"></div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded">Bölüm Müdürü / Lideri</span>
                                </div>
                                <span class="text-gray-900 font-bold transition-colors group-hover:text-indigo-600 block">{{ $dlider->name }}</span>
                                {!! renderUserCardTooltip($dlider, 'Bölüm Lideri') !!}
                            </div>
                        @endforeach

                        {{-- Kalite Liderleri --}}
                        @foreach($kaliteYoneticileri as $kyonetici)
                            <div class="p-5 bg-white rounded-2xl border-2 border-gray-100 shadow-sm relative group cursor-help transition-all hover:shadow-xl hover:border-purple-200 hover:-translate-y-1">
                                <div class="absolute top-0 left-0 w-1.5 h-full bg-purple-500 opacity-60"></div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black text-purple-500 uppercase tracking-widest bg-purple-50 px-2 py-0.5 rounded">Kalite Lideri</span>
                                </div>
                                <span class="text-gray-900 font-bold transition-colors group-hover:text-purple-600 block">{{ $kyonetici->name }}</span>
                                {!! renderUserCardTooltip($kyonetici, 'Bölüm Kalite Yöneticisi') !!}
                            </div>
                        @endforeach

                        {{-- Müşteri Temsilcileri --}}
                        @foreach($otherReps as $orep)
                            <div class="p-5 bg-white rounded-2xl border-2 border-gray-100 shadow-sm relative group cursor-help transition-all hover:shadow-xl hover:border-green-200 hover:-translate-y-1">
                                <div class="absolute top-0 left-0 w-1.5 h-full bg-green-500 opacity-60"></div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black text-green-500 uppercase tracking-widest bg-green-50 px-2 py-0.5 rounded">Müşteri Temsilcisi</span>
                                </div>
                                <span class="text-gray-900 font-bold transition-colors group-hover:text-green-600 block">{{ $orep->name }}</span>
                                {!! renderUserCardTooltip($orep, 'Müşteri Temsilcisi') !!}
                            </div>
                        @endforeach
                    @endif
                </div>

                @if(!$sikayetSnapshot)
                    <div class="mt-6 p-4 bg-amber-50 rounded-2xl border-2 border-dashed border-amber-200 flex items-center gap-4 group">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 shadow-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        </div>
                        <div>
                            <p class="text-sm text-amber-900 font-black leading-tight">Arşiv Kaydı Bilgilendirmesi</p>
                            <p class="text-[12px] text-amber-700 font-bold opacity-80 mt-0.5">Bu şikayet, yeni "sabit liste" sistemi aktif edilmeden önce açılmıştır. Bu nedenle yukarıdaki tablo şikayet anındaki kişileri değil, firmanın şu anki güncel yetkililerini göstermektedir.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif
