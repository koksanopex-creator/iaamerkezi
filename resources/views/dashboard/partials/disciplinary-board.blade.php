@php
    $kurulDosyalari = \App\Models\DisciplinaryCase::where('durum', 'Kurulda')
        ->with(['user.bolum', 'behavior.category', 'reporter.bolum', 'oylar.user'])
        ->orderBy('toplanti_tarihi', 'asc')
        ->get();

    $kurulUyeleri = \App\Models\User::role(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'])->get();
@endphp

@if($kurulDosyalari->isNotEmpty())
    <style>
        @keyframes blinker {
            50% { opacity: 0.3; }
        }
        .animate-blink {
            animation: blinker 1.5s linear infinite;
        }
    </style>
    <div class="mb-8" x-cloak
        x-data="{ 
            isBoardOpen: false, 
            isBannerHidden: localStorage.getItem('disc_board_hidden') === 'true',
            hasInteracted: localStorage.getItem('disc_board_interacted') === 'true',
            closeBanner() {
                this.isBannerHidden = true;
                localStorage.setItem('disc_board_hidden', 'true');
            },
            restoreBanner() {
                this.isBannerHidden = false;
                localStorage.setItem('disc_board_hidden', 'false');
            },
            handleInteraction() {
                this.isBoardOpen = !this.isBoardOpen;
                if(!this.hasInteracted) {
                    this.hasInteracted = true;
                    localStorage.setItem('disc_board_interacted', 'true');
                }
            }
        }">
        {{-- Geri Getirme Butonu (Sadece gizliyken görünür) --}}
        <div x-show="isBannerHidden" class="flex justify-start mb-4">
            <button @click="restoreBanner()" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-bold hover:bg-indigo-100 transition shadow-sm border border-indigo-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Disiplin Kurulu Bildirimlerini Göster
            </button>
        </div>

        {{-- Ana Banner --}}
        <div x-show="!isBannerHidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between mb-4 bg-indigo-100 p-4 rounded-2xl border-2 border-indigo-200 shadow-md group relative">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3 cursor-pointer hover:opacity-90 flex-1" @click="handleInteraction()">
                        <div class="flex-shrink-0 p-2 bg-white rounded-xl shadow-sm border border-indigo-200 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-indigo-600 transition-transform duration-200" :class="isBoardOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-800 flex items-center gap-3" :class="hasInteracted ? '' : 'animate-blink'">
                            <span class="tracking-tight italic uppercase drop-shadow-sm font-black text-indigo-900">Disiplin Kurulu: Toplantı Bekleyen Dosyalar</span>
                        </h3>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-600"></span>
                            </span>
                            <span class="bg-indigo-600 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm">
                                {{ $kurulDosyalari->count() }} AKTİF SEVK
                            </span>
                        </div>
                        
                        {{-- Gizleme Butonu --}}
                        <button @click="closeBanner()" class="p-1.5 hover:bg-indigo-200 rounded-lg transition text-indigo-400 hover:text-indigo-700" title="Gizle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="isBoardOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Kurul Üyeleri Bande --}}
        <div class="mb-4 bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 flex flex-wrap items-center gap-2">
            <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mr-1">Aktif Kurul Üyeleri:</span>
            @foreach($kurulUyeleri as $uye)
                @php $isBaskan = $uye->hasRole('Disiplin Kurulu Başkanı'); @endphp
                <a href="{{ route('profile.show', $uye->id) }}"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold transition hover:opacity-80 {{ $isBaskan ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-700 border border-indigo-200' }}">
                    {{ $isBaskan ? '👑 ' : '' }}{{ $uye->name }}
                    @if($isBaskan)<span class="text-[9px] opacity-75">(Başkan)</span>@endif
                </a>
            @endforeach
        </div>

        {{-- Tablo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">Personel / Birim</th>
                            <th class="px-5 py-3.5">İhlal Kategorisi</th>
                            <th class="px-5 py-3.5">Tutanağı Yazan / Tarih</th>
                            <th class="px-5 py-3.5">Toplantı Tarihi</th>
                            <th class="px-5 py-3.5 text-center">Kalan Süre</th>
                            <th class="px-5 py-3.5 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kurulDosyalari as $dosya)
                            @php
                                $now = \Carbon\Carbon::now();
                                $toplantiTarihi = $dosya->toplanti_tarihi ? \Carbon\Carbon::parse($dosya->toplanti_tarihi) : null;

                                $kalanGun = null;
                                $kalanDurumu = 'belirsiz';

                                if ($toplantiTarihi) {
                                    $diffSeconds = $now->diffInSeconds($toplantiTarihi, false);

                                    if ($diffSeconds < 0) {
                                        // Geçmiş
                                        $gecenGun = (int) ceil(abs($diffSeconds) / 86400);
                                        $kalanDurumu = 'gecti';
                                        $kalanGun = 'Geçti (' . $gecenGun . ' gün önce)';
                                    } else {
                                        // Gelecek
                                        $kalanSaniye = $diffSeconds;
                                        $kalanSaat = (int) ceil($kalanSaniye / 3600);
                                        $kalanGunSayisi = (int) ceil($kalanSaniye / 86400);

                                        if ($kalanGunSayisi <= 0 || $kalanSaat < 24) {
                                            $kalanDurumu = 'bugun';
                                            $kalanGun = 'Bugün — ' . $kalanSaat . ' saat kaldı';
                                        } elseif ($kalanGunSayisi <= 2) {
                                            $kalanDurumu = 'yakin';
                                            $kalanGun = $kalanGunSayisi . ' gün kaldı';
                                        } else {
                                            $kalanDurumu = 'normal';
                                            $kalanGun = $kalanGunSayisi . ' gün kaldı';
                                        }
                                    }
                                }

                                $badgeClass = match ($kalanDurumu) {
                                    'gecti' => 'bg-red-100 text-red-700 border border-red-200',
                                    'bugun' => 'bg-orange-100 text-orange-700 border border-orange-200',
                                    'yakin' => 'bg-amber-100 text-amber-700 border border-amber-200',
                                    'normal' => 'bg-green-100 text-green-700 border border-green-200',
                                    default => 'bg-slate-100 text-slate-500 border border-slate-200',
                                };
                                $pulseClass = in_array($kalanDurumu, ['bugun', 'yakin', 'gecti']) ? 'animate-pulse' : '';
                            @endphp
                            <tr class="hover:bg-slate-50 transition">

                                {{-- # --}}
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-600">#{{ $dosya->id }}</span>
                                </td>

                                {{-- Personel --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 bg-gradient-to-br from-slate-200 to-slate-300 rounded-full flex items-center justify-center font-bold text-slate-700 text-sm flex-shrink-0">
                                            {{ strtoupper(substr($dosya->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('profile.show', $dosya->user->id) }}"
                                                class="font-semibold text-slate-900 hover:text-indigo-700 hover:underline transition-colors">
                                                {{ $dosya->user->name }}
                                            </a>
                                            <a href="{{ $dosya->user->bolum ? route('admin.bolumler.show', $dosya->user->bolum->id) : '#' }}"
                                                class="block text-[10px] text-indigo-500 hover:underline uppercase tracking-wide mt-0.5">
                                                {{ $dosya->user->bolum->ad ?? 'Birim Belirtilmedi' }}
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                {{-- İhlal --}}
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                        {{ $dosya->behavior->category->ad ?? 'Genel Disiplin' }}
                                    </span>
                                </td>

                                {{-- Tutanağı Yazan --}}
                                <td class="px-5 py-4">
                                    @if($dosya->reporter)
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center font-bold text-indigo-700 text-xs flex-shrink-0">
                                                {{ strtoupper(substr($dosya->reporter->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('profile.show', $dosya->reporter->id) }}"
                                                    class="text-sm font-semibold text-slate-700 hover:text-indigo-700 hover:underline transition-colors">
                                                    {{ $dosya->reporter->name }}
                                                </a>
                                                <p class="text-[10px] text-slate-400">
                                                    {{ $dosya->reporter->bolum->ad ?? 'Birim Belirtilmedi' }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">
                                                    {{ $dosya->created_at->format('d.m.Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Toplantı Tarihi --}}
                                <td class="px-5 py-4">
                                    @if($toplantiTarihi)
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{{ $toplantiTarihi->format('d.m.Y') }}</p>
                                            <p class="text-[11px] text-slate-500">{{ $toplantiTarihi->format('H:i') }}</p>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs">Belirtilmedi</span>
                                    @endif
                                </td>

                                {{-- Kalan Süre --}}
                                <td class="px-5 py-4 text-center">
                                    @if($kalanGun)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $badgeClass }} {{ $pulseClass }}">
                                            @if(in_array($kalanDurumu, ['bugun', 'yakin', 'gecti']))
                                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-80 animate-ping"></span>
                                            @endif
                                            {{ $kalanGun }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- İşlem --}}
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.disiplin.show', $dosya->id) }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-indigo-600 transition-colors shadow-sm whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Dosyayı İncele
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
@endif