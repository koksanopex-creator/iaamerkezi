<div class="space-y-8">

    {{-- 1. ONLINE KULLANICILAR (EN ÜSTTE) --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-green-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                Online Kullanıcılar & Son Aktiviteler
            </h3>
            <a href="{{ route('logs.login.index') }}"
                class="text-[10px] font-bold text-green-600 hover:text-green-800 uppercase tracking-widest bg-white/50 px-2 py-1 rounded border border-green-100 shadow-sm transition-all">&larr;
                Tüm Giriş Kayıtları</a>
            <span
                class="text-xs font-bold text-green-700 bg-white px-3 py-1 rounded-full border border-green-200 shadow-sm">
                {{ $stats['online_users_list']->count() }} Kişi Aktif
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 divide-x divide-gray-100">
            {{-- Sol: Şu An Online --}}
            <div class="max-h-60 overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2">Kullanıcı</th>
                            <th class="px-4 py-2">Bölüm</th>
                            <th class="px-4 py-2 text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stats['online_users_list'] as $user)
                            <tr class="bg-white hover:bg-gray-50 transition-colors group relative">
                                <td class="px-4 py-3 font-medium text-gray-900 flex items-center gap-3">
                                    <div class="relative">
                                        @if($user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                alt="{{ $user->name }}"
                                                class="w-8 h-8 rounded-full object-cover border border-gray-200">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold border border-indigo-200">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <span
                                            class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <a href="{{ route('profile.show', $user->id) }}"
                                            class="hover:text-indigo-600 hover:underline text-sm font-semibold">
                                            {{ $user->name }}
                                        </a>
                                        <span class="text-[10px] text-gray-500">{{ $user->unvan ?? 'Kullanıcı' }}</span>
                                    </div>

                                    {{-- HOVER CARD (MODAL-LIKE) --}}
                                    <div
                                        class="absolute left-14 top-10 z-50 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200 ease-in-out">
                                        <div
                                            class="bg-white rounded-lg shadow-xl border border-gray-100 w-64 p-3 ring-1 ring-black ring-opacity-5">
                                            <div class="flex items-center gap-3 mb-3 pb-2 border-b border-gray-50">
                                                @if($user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                        class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-800">{{ $user->name }}</h4>
                                                    <p class="text-[10px] text-gray-500">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Son
                                                    7 Giriş</h5>
                                                <ul class="space-y-1.5">
                                                    @forelse($user->loginActivities->take(7) as $activity)
                                                        <li class="flex justify-between items-center text-[10px] text-gray-600">
                                                            <span
                                                                class="font-medium">{{ $activity->created_at->format('d.m H:i') }}</span>
                                                            <span
                                                                class="text-gray-400 font-mono">{{ $activity->ip_address }}</span>
                                                        </li>
                                                    @empty
                                                        <li class="text-[10px] text-gray-400 italic">Kayıt bulunamadı.</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600 font-medium bg-gray-50/50">
                                    {{ $user->bolum ? Str::limit($user->bolum->ad, 20) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span
                                        class="bg-green-100 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-full border border-green-200 shadow-sm">
                                        Çevrimiçi
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-400 italic">Şu an aktif kullanıcı
                                    yok.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
            {{-- Sağ: Son Görülme --}}
            <div class="max-h-60 overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2">Son Aktif Kullanıcı</th>
                            <th class="px-4 py-2 text-right">Son Görülme</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($stats['last_active_users'] as $user)
                            <tr class="bg-white hover:bg-gray-50 transition-colors group relative">
                                <td class="px-4 py-3 font-medium text-gray-900 flex items-center gap-3">
                                    <div class="relative">
                                        @if($user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                alt="{{ $user->name }}"
                                                class="w-8 h-8 rounded-full object-cover border border-gray-200 grayscale group-hover:grayscale-0 transition-all">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 group-hover:bg-indigo-100 group-hover:text-indigo-600 flex items-center justify-center text-xs font-bold border border-gray-200 transition-colors">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-semibold text-gray-700 group-hover:text-indigo-600 transition-colors">
                                            {{ $user->name }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 group-hover:text-gray-500">
                                            {{ $user->bolum ? Str::limit($user->bolum->ad, 20) : '-' }}
                                        </span>
                                    </div>

                                    {{-- HOVER CARD (MODAL-LIKE) --}}
                                    <div
                                        class="absolute left-10 top-8 z-50 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200 ease-in-out">
                                        <div
                                            class="bg-white rounded-lg shadow-xl border border-gray-100 w-64 p-3 ring-1 ring-black ring-opacity-5">
                                            <div class="flex items-center gap-3 mb-3 pb-2 border-b border-gray-50">
                                                @if($user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                        class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-800">{{ $user->name }}</h4>
                                                    <p class="text-[10px] text-gray-500">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Son
                                                    7 Giriş</h5>
                                                <ul class="space-y-1.5">
                                                    @forelse($user->loginActivities->take(7) as $activity)
                                                        <li class="flex justify-between items-center text-[10px] text-gray-600">
                                                            <span
                                                                class="font-medium">{{ $activity->created_at->format('d.m H:i') }}</span>
                                                            <span
                                                                class="text-gray-400 font-mono">{{ $activity->ip_address }}</span>
                                                        </li>
                                                    @empty
                                                        <li class="text-[10px] text-gray-400 italic">Kayıt bulunamadı.</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-gray-500 font-mono">
                                    {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 2. MODÜL LİSTELERİ (GRID) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- İAA PROJELERİ LİSTESİ --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">İAA Projeleri</h3>
                    <p class="text-xs text-gray-500">Devam Eden & Onay Bekleyen</p>
                </div>
                <a href="{{ route('admin.iaa-raporlari.index') }}"
                    class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase">Tüm Rapor &rarr;</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Proje</th>
                        <th class="px-4 py-2">Bölüm</th>
                        <th class="px-4 py-2">Durum</th>
                        <th class="px-4 py-2 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stats['iaa']['active_list'] as $iaa)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">
                                <a href="{{ route('proje.workspace.show', $iaa->id) }}"
                                    class="hover:text-indigo-600 hover:underline block truncate max-w-[150px]"
                                    title="{{ $iaa->baslik }}">
                                    {{ $iaa->baslik }}
                                </a>
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    {{ $iaa->atananTakim ? $iaa->atananTakim->ad : ($iaa->gonderen ? $iaa->gonderen->name : '-') }}
                                </div>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                {{ $iaa->bolum ? Str::limit($iaa->bolum->ad, 15) : '-' }}
                            </td>
                            <td class="px-4 py-2 relative"> <!-- Tooltip için relative -->
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold whitespace-nowrap 
                                                                            @if(Str::contains($iaa->durum, 'Bekliyor') || Str::contains($iaa->durum, 'Onay')) bg-amber-100 text-amber-800 
                                                                            @elseif($iaa->durum == 'Devam Ediyor' || $iaa->durum == 'Atandı') bg-blue-100 text-blue-800 
                                                                            @else bg-gray-100 text-gray-800 @endif"
                                    title="{{ $iaa->durum }}">
                                    {{ Str::limit($iaa->durum, 15) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right text-xs whitespace-nowrap">
                                {{ $iaa->updated_at->format('d.m') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-400 italic">Aktif proje yok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MÜŞTERİ ŞİKAYETLERİ LİSTESİ --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-red-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Müşteri Şikayetleri</h3>
                    <p class="text-xs text-gray-500">İşlemdeki Kayıtlar</p>
                </div>
                <a href="{{ route('admin.sikayet-raporlari.index') }}"
                    class="text-xs font-bold text-red-600 hover:text-red-800 uppercase">Tüm Rapor &rarr;</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Müşteri / Konu</th>
                        <th class="px-4 py-2">Bölüm</th>
                        <th class="px-4 py-2">Durum</th>
                        <th class="px-4 py-2 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stats['sikayetler']['active_list'] as $sikayet)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">
                                <div class="text-xs font-bold text-gray-800 truncate max-w-[150px]">
                                    {{ $sikayet->customer ? $sikayet->customer->name : 'Bilinmeyen Müşteri' }}
                                </div>
                                <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}"
                                    class="text-[11px] text-gray-500 hover:text-red-600 block truncate max-w-[200px]"
                                    title="{{ $sikayet->musteri_sikayet_konusu }}">
                                    {{ $sikayet->musteri_sikayet_konusu }}
                                </a>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                {{ $sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum ? Str::limit($sikayet->sikayetKategori->bolum->ad, 15) : '-' }}
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $statusColor = 'bg-gray-100 text-gray-800';
                                    if (in_array($sikayet->musteri_durum, ['Yeni', 'Beklemede']))
                                        $statusColor = 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                    elseif (in_array($sikayet->musteri_durum, ['İşlemde', 'Atandı', 'İnceleniyor', 'Devam Ediyor']))
                                        $statusColor = 'bg-blue-100 text-blue-800 border border-blue-200';
                                    elseif (in_array($sikayet->musteri_durum, ['Tamamlandı', 'Çözümlendi', 'Kapatıldı']))
                                        $statusColor = 'bg-green-100 text-green-800 border border-green-200';
                                @endphp
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold whitespace-nowrap {{ $statusColor }}">
                                    {{ $sikayet->musteri_durum }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right text-xs whitespace-nowrap">
                                {{ $sikayet->created_at->format('d.m') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-400 italic">Aktif şikayet yok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- DİSİPLİN --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-rose-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Disiplin Süreçleri</h3>
                    <p class="text-xs text-gray-500">Aktif Dosyalar</p>
                </div>
                <a href="{{ route('admin.disiplin.index') }}"
                    class="text-xs font-bold text-rose-600 hover:text-rose-800 uppercase">Yönetim &rarr;</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Personel</th>
                        <th class="px-4 py-2">Bölüm</th>
                        <th class="px-4 py-2">İhlal</th>
                        <th class="px-4 py-2 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stats['disiplin']['active_list'] as $case)
                        <tr
                            class="bg-white hover:bg-gray-50 border-l-4 {{ $case->durum == 'Karar Verildi' || $case->durum == 'Kapandı' ? 'border-gray-300 bg-gray-50' : 'border-rose-500' }}">
                            <td class="px-4 py-2 font-medium text-gray-900">
                                <a href="{{ route('admin.disiplin.show', $case->id) }}"
                                    class="hover:text-rose-600 hover:underline flex items-center gap-2">
                                    {{ $case->user ? $case->user->name : '-' }}
                                    @if($case->durum == 'Karar Verildi' || $case->durum == 'Kapandı')
                                        <span class="text-[8px] px-1.5 py-0.5 rounded bg-gray-200 text-gray-600">KAPALI</span>
                                    @endif
                                </a>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                {{ $case->user && $case->user->bolum ? Str::limit($case->user->bolum->ad, 15) : '-' }}
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-600">
                                <span title="{{ $case->behavior ? $case->behavior->tanim : '-' }}">
                                    {{ $case->behavior ? Str::limit($case->behavior->tanim, 20) : '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right text-xs whitespace-nowrap">
                                {{ $case->created_at->format('d.m') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400 italic">Dosya bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Bölüm Dağılımı Özeti (Alt Bilgi) --}}
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center gap-4 overflow-x-auto">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Yoğunluk:</span>
                @foreach($stats['disiplin']['bolum_dagilimi'] as $bolum)
                    <span
                        class="text-[10px] text-gray-600 bg-white border border-gray-200 px-2 py-1 rounded shadow-sm whitespace-nowrap">
                        {{ $bolum->bolum_adi }}: <strong class="text-rose-600">{{ $bolum->toplam }}</strong>
                    </span>
                @endforeach
            </div>
        </div>

        {{-- ARABULUCULUK --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-blue-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Arabuluculuk</h3>
                    <p class="text-xs text-gray-500">Devam Eden Süreçler</p>
                </div>
                <a href="{{ route('admin.arabuluculuk.index') }}"
                    class="text-xs font-bold text-blue-600 hover:text-blue-800 uppercase">Yönetim &rarr;</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">Taraf / Dosya</th>
                        <th class="px-4 py-2">Durum</th>
                        <th class="px-4 py-2 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stats['arabuluculuk']['active_list'] as $case)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">
                                <a href="{{ route('admin.arabuluculuk.show', $case->id) }}"
                                    class="hover:text-blue-600 hover:underline block truncate max-w-[150px]">
                                    {{ $case->calisan ? $case->calisan->name : 'Personel Dosyası' }}
                                </a>
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $statusColorMed = 'bg-gray-100 text-gray-600';
                                    if ($case->status == 'gorusuluyor' || $case->status == 'yonetim_onayinda')
                                        $statusColorMed = 'bg-blue-100 text-blue-700 border border-blue-200';
                                    elseif ($case->status == 'anlasildi' || $case->status == 'odeme_yapildi')
                                        $statusColorMed = 'bg-green-100 text-green-700 border border-green-200';
                                    elseif ($case->status == 'dava_acildi' || $case->status == 'anlasilamadi')
                                        $statusColorMed = 'bg-red-100 text-red-700 border border-red-200';
                                @endphp
                                <span class="text-[10px] px-1.5 py-0.5 rounded border {{ $statusColorMed }}">
                                    {{ ucwords(str_replace('_', ' ', $case->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right text-xs whitespace-nowrap">
                                {{ $case->updated_at->format('d.m') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-400 italic">Aktif süreç yok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- 3. BEKLEYEN İŞLER LİSTESİ (TABLO) --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Bekleyen İşler Listesi</h3>
                <p class="text-xs text-gray-500">Onay veya aksiyon bekleyen tüm süreçler</p>
            </div>
            <a href="{{ route('admin.tum-bekleyen-isler') }}"
                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wide">Tümünü
                İncele &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 font-bold text-gray-600">Tür</th>
                        <th class="px-6 py-3 font-bold text-gray-600 w-1/3">Konu / Başlık</th>
                        <th class="px-6 py-3 font-bold text-gray-600">Bekleyen Taraf</th>
                        <th class="px-6 py-3 font-bold text-gray-600">Durum</th>
                        <th class="px-6 py-3 font-bold text-gray-600">Süre</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-600">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stats['waiting_tasks'] as $task)
                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                @if($task['type'] == 'Müşteri Şikayeti')
                                    <span
                                        class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded border border-red-200">MÜŞTERİ
                                        ŞİKAYETİ</span>
                                @elseif($task['type'] == 'İAA')
                                    <span
                                        class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-1 rounded border border-indigo-200">İAA</span>
                                @elseif($task['type'] == 'Arabuluculuk')
                                    <span
                                        class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded border border-blue-200">ARABULUCULUK</span>
                                @else
                                    <span
                                        class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded border border-gray-200">{{ strtoupper($task['type']) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-900 font-medium block"
                                    title="{{ $task['subject'] }}">{{ Str::limit($task['subject'], 50) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800 text-xs">{{ $task['waiting_person'] }}</span>
                                    <span class="text-[10px] text-gray-500 uppercase">{{ $task['waiting_dept'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded inline-block">
                                    {{ $task['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="flex items-center gap-1 font-bold {{ $task['days'] > 5 ? 'text-red-500' : 'text-amber-500' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ number_format($task['days'], 0) }} Gün
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ $task['link'] }}"
                                    class="text-indigo-600 hover:text-indigo-900 font-bold text-xs flex items-center justify-end gap-1">
                                    İNCELE <span class="text-lg leading-none">&rsaquo;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 bg-gray-50/50">
                                Bekleyen iş bulunmamaktadır.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. PORTFÖY VE İADELER (2 KOLONLU TABLOLAR) --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- En Çok Şikayet Edenler --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">En Çok Şikayet Bildiren Müşteriler</h3>
                <span class="text-xs text-gray-500">Top 5</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Firma Adı</th>
                        <th class="px-6 py-3 text-right">Şikayet Sayısı</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($stats['musteriler']['en_cok_sikayet'] as $musteri)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900 truncate">
                                <a href="{{ route('musteri.profil.show', $musteri->id) }}"
                                    class="hover:underline hover:text-indigo-600">
                                    {{ $musteri->name }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span
                                    class="bg-red-50 text-red-600 px-2 py-0.5 rounded font-bold text-xs border border-red-100">
                                    {{ $musteri->sikayetler_count }} Adet
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Bölüm İadeleri --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Bölümlere Göre İade Miktarları</h3>
                <span class="text-xs text-gray-500">Analiz</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Bölüm</th>
                        <th class="px-6 py-3">Birim</th>
                        <th class="px-6 py-3 text-right">Miktar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stats['musteriler']['iadeler_bolum_bazli'] as $bolumAdi => $iadeler)
                        @foreach($iadeler as $index => $iade)
                            <tr class="bg-white hover:bg-gray-50 {{ $loop->last ? 'border-b border-gray-100' : '' }}">
                                <td class="px-6 py-3 font-medium text-gray-900 border-r border-gray-50">
                                    {{ $index === 0 ? $bolumAdi : '' }}
                                </td>
                                <td class="px-6 py-3 text-xs text-gray-600 uppercase">{{ $iade->birim }}</td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-gray-800">
                                    {{ number_format($iade->toplam_miktar, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">İade kaydı bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>