<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                    {{ __('Yönetici Paneli') }}
                </h2>
                <p class="text-gray-600 mt-1">Sistem durumunu ve verileri yönetin</p>
            </div>
            <div class="hidden md:flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-500">Sistem Aktif</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


            {{-- =================================================================== --}}
            {{-- === YENİ KULLANICI PUAN KARTI (TÜM KULLANICILAR İÇİN) === --}}
            {{-- =================================================================== --}}
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-2xl shadow-lg text-white mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-indigo-200 uppercase tracking-wider">Toplam Puanınız</p>
                        <p class="text-4xl font-black tracking-tight">{{ number_format(Auth::user()->toplam_puan, 0) }}</p>
                    </div>
                    <a href="{{ route('puan-durumu') }}" class="text-indigo-200 hover:text-white transition-colors" title="Liderlik Tablosu">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </a>
                </div>
            </div>

            {{-- Sadece Superadmin'in göreceği içerik --}}
            @role('Superadmin')
                @if(isset($stats))
                    <!-- Stats Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                        
                        <!-- Kullanıcılar Kartı -->
                        <a href="{{ route('admin.users.index') }}" 
                           class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 rounded-2xl"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>
                                    </div>
                                    @if($stats['onay_bekleyen_kullanici'] > 0)
                                        <div class="bg-amber-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                            {{ $stats['onay_bekleyen_kullanici'] }} bekliyor
                                        </div>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Kullanıcılar</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['toplam_kullanici'] }}</p>
                                <div class="space-y-2">
                                    @forelse($stats['son_kullanicilar'] as $user)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700 font-medium">{{ $user->name }}</span>
                                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md">{{ $user->created_at->format('d.m.Y') }}</span>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-sm italic">Henüz kullanıcı yok.</p>
                                    @endforelse
                                </div>
                                <div class="flex items-center mt-6 text-blue-600 font-semibold text-sm group-hover:text-blue-700">
                                    <span>Kullanıcıları Yönet</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>

                        <!-- İyileştirme Önerileri (İAA) Kartı -->
                        <a href="{{ route('admin.iaa-yonetim.index') }}" 
                           class="group relative bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/5 to-teal-600/5 rounded-2xl"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                    </div>
                                    <div class="flex flex-col space-y-1 text-right">
                                        @if($stats['onay_bekleyen_iaa'] > 0)<div class="bg-amber-500 text-white text-xs px-2 py-1 rounded-full font-medium">{{ $stats['onay_bekleyen_iaa'] }} onay</div>@endif
                                        @if($stats['atama_bekleyen_iaa'] > 0)<div class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium">{{ $stats['atama_bekleyen_iaa'] }} atama</div>@endif
                                    </div>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">İyileştirme Önerileri</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['toplam_iaa'] }}</p>
                                <div class="space-y-2">
                                    @forelse($stats['son_iaalar'] as $iaa)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($iaa->baslik, 20) }}</span>
                                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap">{{ $iaa->created_at->format('d.m.Y') }}</span>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-sm italic">Henüz öneri yok.</p>
                                    @endforelse
                                </div>
                                <div class="flex items-center mt-6 text-emerald-600 font-semibold text-sm group-hover:text-emerald-700">
                                    <span>İAA'ları Yönet</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>

                        <!-- Takımlar Kartı -->
                        <a href="{{ route('takimlar.index') }}" 
                           class="group relative bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-violet-600/5 rounded-2xl"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-purple-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Takımlar</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['toplam_takim'] }}</p>
                                <div class="space-y-2">
                                    @forelse($stats['son_takimlar'] as $takim)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700 font-medium">{{ $takim->ad }}</span>
                                            <span class="text-purple-600 text-xs bg-purple-100 px-2 py-1 rounded-md font-medium">{{ $takim->uyeler_count }} üye</span>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-sm italic">Henüz takım yok.</p>
                                    @endforelse
                                </div>
                                <div class="flex items-center mt-6 text-purple-600 font-semibold text-sm group-hover:text-purple-700">
                                    <span>Takımları Görüntüle</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>

                        <!-- Bölümler Kartı -->
                        <a href="{{ route('admin.bolumler.index') }}" 
                           class="group relative bg-gradient-to-br from-rose-50 to-pink-50 border border-rose-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-rose-600/5 to-pink-600/5 rounded-2xl"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-rose-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-rose-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Bölümler</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['toplam_bolum'] }}</p>
                                <div class="space-y-2">
                                    @forelse($stats['son_bolumler'] as $bolum)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700 font-medium">{{ $bolum->ad }}</span>
                                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md">{{ $bolum->created_at->format('d.m.Y') }}</span>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-sm italic">Henüz bölüm yok.</p>
                                    @endforelse
                                </div>
                                <div class="flex items-center mt-6 text-rose-600 font-semibold text-sm group-hover:text-rose-700">
                                    <span>Bölümleri Yönet</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

            {{-- Diğer roller için karşılama mesajı --}}
            @else
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200 rounded-2xl shadow-lg overflow-hidden">
                    <div class="relative p-8">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/10"></div>
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full transform translate-x-16 -translate-y-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-indigo-500/10 rounded-full transform -translate-x-12 translate-y-12"></div>
                        <div class="relative text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __("Sisteme hoş geldiniz!") }}</h3>
                            <p class="text-gray-600">Platformumuza katıldığınız için teşekkür ederiz.</p>
                        </div>
                    </div>
                </div>



            @endrole
        </div>
    </div>
</x-app-layout>

