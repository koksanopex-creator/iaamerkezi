<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">İş Yıldönümleri</h2>
                <p class="text-gray-500 mt-1">Ekip arkadaşlarımızın şirketteki gurur yıllarını buradan takip edebilirsiniz.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('personel.dogum-gunleri') }}" class="px-4 py-2 bg-pink-50 text-pink-700 font-bold rounded-xl border border-pink-100 hover:bg-pink-100 transition-all flex items-center gap-2 shadow-sm">
                    🎂 Doğum Günleri
                </a>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white text-gray-600 font-bold rounded-xl border border-gray-200 hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Panele Dön
                </a>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-col lg:flex-row gap-6 items-center">
                {{-- Search --}}
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="İsim ile ara..." 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                {{-- Tabs --}}
                <div class="flex bg-gray-50 p-1.5 rounded-2xl w-full lg:w-auto">
                    <button wire:click="$set('type', 'past')" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $type == 'past' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Geçmiş
                    </button>
                    <button wire:click="$set('type', 'today')" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $type == 'today' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-500 hover:text-gray-700' }}">
                        Bugün
                    </button>
                    <button wire:click="$set('type', 'upcoming')" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $type == 'upcoming' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Yaklaşan
                    </button>
                    <button wire:click="$set('type', 'all')" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $type == 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Hepsi
                    </button>
                </div>
            </div>
        </div>

        {{-- Results Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($anniversaries as $u)
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group relative overflow-hidden">
                    {{-- Decorative Background --}}
                    @if($u->is_today)
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 transition-all group-hover:scale-110"></div>
                    @endif

                    <div class="relative flex flex-col items-center text-center">
                        {{-- Profile Photo --}}
                        <div class="relative mb-4">
                            @if($u->profile_photo_path)
                                <img src="{{ asset('storage/' . $u->profile_photo_path) }}" class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-lg">
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-blue-500 font-black text-2xl border-4 border-white shadow-lg">
                                    {{ substr($u->name, 0, 1) }}
                                </div>
                            @endif
                            
                            @if($u->is_today)
                                <div class="absolute -bottom-2 -right-2 bg-white rounded-full px-3 py-1 shadow-md animate-bounce border border-blue-100">
                                    <span class="text-xs font-black text-blue-600">{{ $u->current_anniv_years }}. Yıl</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <h3 class="text-lg font-black text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">{{ $u->name }}</h3>
                        <p class="text-xs text-gray-400 font-medium mb-4">{{ $u->bolum->ad ?? 'Genel' }}</p>

                        <div class="w-full pt-4 border-t border-gray-50 flex flex-col items-center">
                            @php
                                $displayAnniv = ($type == 'past') ? $u->past_anniv : $u->upcoming_anniv;
                                $displayYears = ($type == 'past') ? $u->past_years : $u->upcoming_years;
                                if ($type == 'today' || $u->is_today) {
                                    $displayAnniv = now()->startOfDay();
                                    $displayYears = $u->current_anniv_years;
                                }
                                
                                $days = (int)now()->startOfDay()->diffInDays($displayAnniv, false);
                            @endphp
                            
                            <span class="text-sm font-bold {{ $u->is_today ? 'text-blue-600' : 'text-gray-700' }}">
                                {{ $displayAnniv->translatedFormat('d F') }} ({{ $displayYears }}. Yıl)
                            </span>
                            
                            <span class="text-[10px] font-black uppercase tracking-widest mt-1 {{ $u->is_today ? 'text-blue-400' : ($days > 0 ? 'text-indigo-400' : 'text-gray-300') }}">
                                @if($u->is_today)
                                    Büyük Gün!
                                @elseif($days > 0)
                                    {{ $days }} GÜN KALDI
                                @else
                                    {{ abs($days) }} GÜN ÖNCEYDİ
                                @endif
                            </span>
                        </div>

                        {{-- Action --}}
                        <div class="mt-6 w-full opacity-0 group-hover:opacity-100 transition-all translate-y-2 group-hover:translate-y-0">
                            <a href="{{ route('profile.show', $u->id) }}?tab=yorumlar&anniv_msg=1&years={{ $displayYears }}" class="w-full block py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-black uppercase rounded-2xl shadow-lg shadow-blue-200 hover:scale-105 transition-all">
                                Tebrik Mesajı Gönder
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 flex flex-col items-center justify-center bg-white rounded-3xl border-2 border-dashed border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-4xl mb-4">🔍</div>
                    <h4 class="text-xl font-bold text-gray-900">Sonuç Bulunamadı</h4>
                    <p class="text-gray-400 italic">Arama kriterlerinize uygun kimse yok.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $anniversaries->links() }}
        </div>
    </div>
</div>
