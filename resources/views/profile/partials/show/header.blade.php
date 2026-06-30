<div class="relative bg-gray-900 pb-24 overflow-hidden shadow-2xl">
    {{-- ARKA PLAN --}}
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-gray-900 to-black">
        <div class="absolute inset-0 bg-gradient-to-b from-white/5 via-transparent to-black/60"></div>
    </div>

    <div
        class="relative max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-8">

        {{-- SOL TARAF: AVATAR VE İSİM --}}
        <div class="flex items-center gap-6 w-full md:w-auto">
            <div class="relative flex-shrink-0"
                style="width: 112px; height: 112px; min-width: 112px; min-height: 112px;">
                <div
                    class="h-full w-full rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 p-1 shadow-2xl ring-4 ring-white/10">
                    @if($user->profile_photo_path)
                        <img class="h-full w-full rounded-full object-cover border-4 border-gray-900"
                            src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                    @else
                        <div
                            class="h-full w-full rounded-full bg-gray-800 flex items-center justify-center text-4xl font-bold text-white uppercase border-4 border-gray-900">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="absolute bottom-1 right-1 bg-emerald-500 h-6 w-6 rounded-full border-4 border-gray-900 shadow-lg"
                    title="Çevrimiçi"></div>
            </div>

            <div class="text-left flex-1">
                <div class="flex items-center flex-wrap gap-3">
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white drop-shadow-lg">
                        {{ $user->name }}
                    </h1>
                    @if(method_exists($user, 'trashed') && $user->trashed())
                        <span class="px-3 py-1 bg-red-600 text-white text-xs md:text-sm font-black rounded-lg shadow-[0_0_15px_rgba(220,38,38,0.5)] border border-red-400">
                            PASİF (İŞTEN AYRILDI)
                        </span>
                    @endif
                </div>
                <div class="flex flex-wrap items-center justify-start gap-2 mt-3">
                    @foreach($user->roles as $role)
                        <span
                            class="px-3 py-1 rounded-lg text-xs font-bold bg-indigo-600 text-white shadow-md border border-indigo-400/50">
                            {{ $role->name }}
                        </span>
                    @endforeach
                    @if($user->roles->isEmpty())
                        <span
                            class="px-3 py-1 rounded-lg text-xs font-bold bg-indigo-600 text-white shadow-md border border-indigo-400/50">
                            Kullanıcı
                        </span>
                    @endif
                    <span
                        class="px-3 py-1 rounded-lg text-xs font-medium bg-black/30 text-gray-300 border border-white/10">
                        Son Giriş: {{ $lastLogin ? $lastLogin->format('d.m.Y H:i') : 'Bilinmiyor' }}
                    </span>

                    @if(auth()->user()->hasRole('Superadmin') && auth()->id() != $user->id)
                        <a href="{{ (isset($isCustomerRep) && $isCustomerRep) ? route('admin.musteriler.index') : route('admin.users.edit', $user->id) }}"
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-amber-500 text-white hover:bg-amber-400 transition-colors shadow-md border border-amber-400 ml-2">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Yönetici Olarak Düzenle
                        </a>
                    @endif

                    @if(auth()->id() == $user->id)
                        <a href="{{ route('profile.edit') }}"
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white hover:bg-blue-500 transition-colors shadow-md border border-blue-500 ml-2">
                            Profili Düzenle
                        </a>
                    @endif
                </div>

                {{-- YENİ: HİYERARŞİ BİLGİLERİ --}}
                @if($user->is_personnel)
                <div class="flex flex-wrap items-center justify-start gap-3 mt-4">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm shadow-sm group hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4 text-indigo-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-tighter text-gray-400 leading-none">Bölüm</span>
                            <span class="text-xs font-bold text-white">{{ $user->bolum->ad ?? 'Belirtilmedi' }}</span>
                        </div>
                    </div>

                    @if(isset($bolumManager) && $bolumManager)
                    <a href="{{ route('profile.show', $bolumManager->id) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm shadow-sm group hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-tighter text-gray-400 leading-none">Bölüm Lideri (Müdür)</span>
                            <span class="text-xs font-bold text-white">{{ $bolumManager->name }}</span>
                        </div>
                    </a>
                    @endif

                    @if(isset($director) && $director)
                    <a href="{{ route('profile.show', $director->id) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm shadow-sm group hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4 text-purple-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-tighter text-gray-400 leading-none">Direktörlük</span>
                            <span class="text-xs font-bold text-white">{{ $director->name }}</span>
                        </div>
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- SAĞ TARAF: PUAN KARTI --}}
        @if(!isset($isCustomerRep) || !$isCustomerRep)
        <a href="{{ route('profile.puanlar', $user->id) }}"
            class="block transform transition hover:scale-105 w-full md:w-auto">
            <div
                class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10 text-center min-w-[220px] shadow-2xl hover:bg-white/10 transition-all group relative overflow-hidden">
                <p
                    class="text-xs font-bold text-gray-300 uppercase tracking-widest group-hover:text-white transition-colors">
                    GENEL PUAN</p>
                <div class="flex items-center justify-center gap-2 mt-1 relative z-10">
                    <svg class="w-6 h-6 text-yellow-400 drop-shadow-md" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <p class="text-5xl font-black text-white tracking-tight drop-shadow-xl">
                        {{ number_format($user->toplam_puan ?? 0, 0, ',', '.') }}</p>
                </div>
                <div
                    class="mt-2 inline-flex items-center px-3 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-gray-200 border border-white/10 relative z-10 shadow-inner">
                    {{ ($user->toplam_puan ?? 0) > 1000 ? '🏆 Usta Seviye' : (($user->toplam_puan ?? 0) > 500 ? '⭐ Uzman Seviye' : '🌱 Başlangıç') }}
                </div>
                <div
                    class="mt-4 pt-3 border-t border-white/10 text-[10px] text-gray-400 group-hover:text-white transition-colors flex justify-center items-center gap-1 relative z-10">
                    Detayları Gör <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>
        @elseif(isset($isCustomerRep) && $isCustomerRep && $user->customer)
        {{-- MÜŞTERİ İÇİN ŞIK ŞİRKET KARTI (SAĞ TARAF) --}}
        <div class="relative group w-full md:w-auto">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-50 transition duration-1000"></div>
            <div class="relative bg-white/10 backdrop-blur-2xl rounded-2xl p-7 border border-white/20 flex flex-col items-center justify-center min-w-[300px] shadow-2xl ring-1 ring-white/10">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg mb-4 transform group-hover:rotate-6 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                
                <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-[0.3em] mb-2 opacity-70">Kurumsal Kimlik</p>
                <a href="{{ route('musteri.profil.show', $user->customer->id) }}" class="group/link block">
                    <h3 class="text-2xl font-black text-white text-center leading-tight tracking-tight drop-shadow-md group-hover/link:text-indigo-300 transition-colors">
                        {{ $user->customer->name }}
                    </h3>
                    <div class="h-0.5 w-0 group-hover/link:w-full bg-gradient-to-r from-transparent via-indigo-400 to-transparent transition-all duration-500 mt-1"></div>
                </a>
                
                <div class="mt-6 flex items-center gap-3 py-2 px-4 bg-white/5 rounded-full border border-white/10">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-bold text-gray-300">Aktif İş Ortağı</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>