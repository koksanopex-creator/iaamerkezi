<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                    @if(Auth::user()->hasRole('Superadmin'))
                        {{ __('Yönetici Paneli') }}
                    @else
                        {{ __('Dashboard') }}
                    @endif
                </h2>
                <p class="text-gray-600 mt-1">
                    @if(Auth::user()->hasRole('Superadmin'))
                        Sistem durumunu ve verileri yönetin
                    @else
                        Sistemdeki genel durumunuzu görüntüleyin
                    @endif
                </p>
            </div>
            <div class="hidden md:flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-500">Sistem Aktif</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Bu puan kartı Superadmin'e GÖRÜNMEYECEK --}}
            @if(!Auth::user()->hasRole('Superadmin'))
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
            @endif


            {{-- ============================================= --}}
            {{-- === ROLE ÖZEL KART GÖRÜNÜMLERİ BAŞLANGIÇ === --}}
            {{-- ============================================= --}}
            
            @if(isset($stats))

                {{-- === 1. GÖRÜNÜM: SUPERADMIN === --}}
                @if(Auth::user()->hasRole('Superadmin'))
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
                        
                        <!-- Müşteri Şikayetleri Kartı -->
                        <a href="{{ route('admin.sikayetler.index') }}" 
                            class="group relative bg-gradient-to-br from-red-50 to-orange-50 border border-red-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-red-600/5 to-orange-600/5 rounded-2xl"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-red-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col space-y-1 text-right">
                                        @if($stats['yeni_sikayet'] > 0)<div class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full font-medium">{{ $stats['yeni_sikayet'] }} yeni</div>@endif
                                        @if($stats['islemde_sikayet'] > 0)<div class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium">{{ $stats['islemde_sikayet'] }} işlemde</div>@endif
                                    </div>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Müşteri Şikayetleri</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['toplam_sikayet'] }}</p>
                                <div class="space-y-2">
                                    @forelse($stats['son_sikayetler']->take(3) as $sikayet)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($sikayet->musteri_sikayet_konusu, 20) }}</span>
                                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap">{{ $sikayet->created_at->format('d.m.Y') }}</span>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-sm italic">Henüz şikayet yok.</p>
                                    @endforelse
                                </div>
                                <div class="flex items-center mt-6 text-red-600 font-semibold text-sm group-hover:text-red-700">
                                    <span>Şikayetleri Yönet</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                {{-- === SUPERADMIN GÖRÜNÜMÜ SONU === --}}


                {{-- === 2. GÖRÜNÜM: MÜŞTERİ ŞİKAYETİ KURULU === --}}
                @if(Auth::user()->hasRole('Müşteri Şikayeti Kurulu'))
                    
                    <!-- Hızlı Erişim Kartı -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 border border-gray-200/50">
                        <div class="px-6 py-5">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Hızlı Erişim (Kurul)
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                <a href="{{ route('admin.sikayetler.create') }}" class="flex flex-col items-center justify-center p-4 bg-indigo-50 text-indigo-700 font-semibold rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                                    <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Yeni Şikayet Gir</span>
                                </a>
                                <a href="{{ route('admin.sikayetler.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>Tüm Şikayetler</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Şikayet Raporu Listesi -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 border border-gray-200/50">
                        <div class="px-6 py-5 bg-gray-50/70 border-b border-gray-200/70">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                                Müşteri Şikayet Raporu (Özet)
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-gray-200/70">
                            <div class="bg-white px-4 py-5 text-center transition-all duration-300 hover:bg-blue-50/50">
                                <p class="text-sm font-medium text-gray-500">Toplam Şikayet</p>
                                <p class="mt-1 text-3xl font-bold text-blue-600">{{ $stats['toplam_sikayet'] }}</p>
                            </div>
                            <div class="bg-white px-4 py-5 text-center transition-all duration-300 hover:bg-yellow-50/50">
                                <p class="text-sm font-medium text-gray-500">Yeni (Atanmamış)</p>
                                <p class="mt-1 text-3xl font-bold text-yellow-500">{{ $stats['yeni_sikayet'] }}</p>
                            </div>
                            <div class="bg-white px-4 py-5 text-center transition-all duration-300 hover:bg-cyan-50/50">
                                <p class="text-sm font-medium text-gray-500">Çözüm Sürecinde</p>
                                <p class="mt-1 text-3xl font-bold text-cyan-600">{{ $stats['islemde_sikayet'] }}</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200/70">
                            <h4 class="text-md font-semibold text-gray-800">Son Gelen Şikayetler</h4>
                        </div>
                        <div class="flow-root">
                            <ul role="list" class="divide-y divide-gray-200/70">
                                @forelse($stats['son_sikayetler'] as $sikayet)
                                    <li class="hover:bg-gray-50/70 transition-colors duration-150">
                                        <a href="{{ route('admin.sikayetler.show', $sikayet) }}" class="flex items-center justify-between p-4 sm:p-6 space-x-4">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-indigo-700 truncate" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                                    {{ $sikayet->musteri_sikayet_konusu }}
                                                </p>
                                                <p class="text-sm text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                                                    <span class="inline-flex items-center gap-1.5">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                        <span>{{ $sikayet->musteri_adi }}</span>
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0 flex flex-col items-end space-y-1">
                                                {!! $sikayet->musteri_durum_badge !!}
                                                <span class="text-xs text-gray-400 mt-1">{{ $sikayet->created_at->diffForHumans() }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="p-6 text-center text-gray-500">
                                        Henüz sisteme girilmiş bir şikayet bulunmuyor.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="bg-gray-50/70 px-6 py-4 border-t border-gray-200/70 text-center">
                            <a href="{{ route('admin.sikayetler.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                Tüm Şikayetleri Yönet →
                            </a>
                        </div>
                    </div>
                @endif
                {{-- === KURUL GÖRÜNÜMÜ SONU === --}}


                {{-- === 3. GÖRÜNÜM: MÜŞTERİ ŞİKAYETİ ÇÖZÜM LİDERİ === --}}
                @if(Auth::user()->hasRole('Müşteri Şikayeti Çözüm Lideri'))
                    @if(isset($stats['lider_takim']))
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                            
                            <!-- Lideri Olduğu Takım Kartı -->
                            <a href="{{ route('takimlar.show', $stats['lider_takim']) }}" 
                                class="group relative bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-violet-600/5 rounded-2xl"></div>
                                <div class="absolute top-0 right-0 w-20 h-20 bg-purple-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Lideri Olduğum Takım</h3>
                                    <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['lider_takim']->ad }}</p>
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700 font-medium">Toplam Üye</span>
                                            <span class="text-purple-600 text-xs bg-purple-100 px-2 py-1 rounded-md font-medium">{{ $stats['lider_takim']->uyeler_count }} üye</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center mt-6 text-purple-600 font-semibold text-sm group-hover:text-purple-700">
                                        <span>Takımı Görüntüle</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                            </a>

                            <!-- Takımın İşlemde Olan Projeleri -->
                            {{-- === DÜZELTME: Lider artık admin/sikayetler sayfasına gider === --}}
                            <a href="{{ route('admin.sikayetler.index') }}" 
                                class="group relative bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-cyan-600/5 rounded-2xl"></div>
                                <div class="absolute top-0 right-0 w-20 h-20 bg-blue-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                                <div class="relative">
                                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">İşlemde Olan Projeler</h3>
                                    <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['islemde_projeler_count'] }}</p>
                                    <div class="space-y-2">
                                        @forelse($stats['son_islemde_projeler'] as $proje)
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($proje->baslik, 20) }}</span>
                                                <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap">{{ $proje->created_at->format('d.m.Y') }}</span>
                                            </div>
                                        @empty
                                            <p class="text-gray-500 text-sm italic">İşlemde proje yok.</p>
                                        @endforelse
                                    </div>
                                    <div class="flex items-center mt-6 text-blue-600 font-semibold text-sm group-hover:text-blue-700">
                                        <span>Şikayet Paneline Git</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                            </a>

                            <!-- Takımın Çözdüğü Projeler -->
                            <div class="group relative bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-2xl p-6 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-green-600/5 to-emerald-600/5 rounded-2xl"></div>
                                <div class="absolute top-0 right-0 w-20 h-20 bg-green-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                                <div class="relative">
                                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Çözülen Projeler</h3>
                                    <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['cozulen_projeler_count'] }}</p>
                                    <p class="text-gray-500 text-sm italic">Takımınızın tamamladığı toplam proje sayısı.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Liderin takımı yoksa -->
                        <div class="bg-gradient-to-br from-yellow-50 to-orange-100 border border-yellow-200 rounded-2xl shadow-lg overflow-hidden p-8">
                            <h3 class="text-xl font-bold text-yellow-900 mb-2">Henüz Bir Takıma Lider Değilsiniz</h3>
                            <p class="text-yellow-700">Şu anda bir müşteri şikayeti çözüm takımına lider olarak atanmamışsınız. Atandığınızda, takımınızın istatistiklerini burada görebileceksiniz.</p>
                        </div>
                    @endif
                @endif
                {{-- === ÇÖZÜM LİDERİ GÖRÜNÜMÜ SONU === --}}

                {{-- === 4. GÖRÜNÜM: BÖLÜM KALİTE YÖNETİCİSİ (YENİ EKLENDİ) === --}}
                @if(Auth::user()->hasRole('Bölüm Kalite Yöneticisi'))
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                        
                        {{-- Onay Bekleyenler Kartı --}}
                        <a href="{{ route('admin.iaa-yonetim.index') }}" onclick="localStorage.setItem('activeTab', 'onay-bekleyenler')"
                           class="group relative bg-gradient-to-br from-purple-50 to-fuchsia-50 border border-purple-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-fuchsia-600/5 rounded-2xl"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-purple-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        {{-- Onay İkonu --}}
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Onayınızı Bekleyenler</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $bolumOnayiBekleyenSayisi ?? 0 }}</p>
                                <p class="text-gray-500 text-sm italic">Bölüm onayınız için bekleyen tamamlanmış projeler.</p>
                                
                                <div class="flex items-center mt-6 text-purple-600 font-semibold text-sm group-hover:text-purple-700">
                                    <span>İncele ve Onayla</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>

                        {{-- Standart Kartlar (Havuz vb. - Kullanıcı istatistiklerinden gelir) --}}
                        {{-- Havuzdaki Öneriler --}}
                         <a href="{{ route('iaa.havuz') }}" 
                           class="group relative bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            {{-- ... Havuz kartı içeriği (yukarıdaki standart kullanıcı kartıyla aynı) ... --}}
                            <div class="relative">
                                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center mb-4">
                                     <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Havuzdaki Öneriler</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['havuz_oneri_sayisi'] ?? 0 }}</p>
                                <div class="flex items-center mt-6 text-emerald-600 font-semibold text-sm">
                                    <span>Havuzu İncele</span><svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>

                        {{-- Takımlarım --}}
                        <a href="{{ route('takimlar.index') }}" 
                           class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="relative">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Takımlarım</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['takimlarim_sayisi'] ?? 0 }}</p>
                                <div class="flex items-center mt-6 text-blue-600 font-semibold text-sm">
                                    <span>Takımlarımı Yönet</span><svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                {{-- === BÖLÜM YÖNETİCİSİ GÖRÜNÜMÜ SONU === --}}

                {{-- === 4. GÖRÜNÜM: STANDART KULLANICI (ve KURUL ÜYESİ) === --}}
                {{-- Bu bölüm Superadmin ve Çözüm Lideri DIŞINDAKİ herkese gösterilir (Talep: Kurul üyesi de bunları görmeli) --}}
                @if(!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Müşteri Şikayeti Çözüm Lideri') && !Auth::user()->hasRole('Bölüm Kalite Yöneticisi'))
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                        
                        <!-- Havuzdaki Öneriler Kartı -->
                        <a href="{{ route('iaa.havuz') }}" 
                            class="group relative bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/5 to-teal-600/5 rounded-2xl"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                            <div class="relative">
                                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Havuzdaki Öneriler</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['havuz_oneri_sayisi'] }}</p>
                                <div class="space-y-2">
                                    @forelse($stats['son_havuz_onerileri'] as $iaa)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($iaa->baslik, 20) }}</span>
                                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap">{{ $iaa->created_at->format('d.m.Y') }}</span>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-sm italic">Havuzda öneri yok.</p>
                                    @endforelse
                                </div>
                                <div class="flex items-center mt-6 text-emerald-600 font-semibold text-sm group-hover:text-emerald-700">
                                    <span>Havuzu İncele</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>

                        <!-- Takımlarım Kartı -->
                        <a href="{{ route('takimlar.index') }}" 
                            class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 rounded-2xl"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                            <div class="relative">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Takımlarım</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['takimlarim_sayisi'] }}</p>
                                <div class="space-y-2">
                                    @forelse($stats['son_takimlarim'] as $takim)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700 font-medium">{{ $takim->ad }}</span>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-sm italic">Henüz bir takıma üye değilsiniz.</p>
                                    @endforelse
                                </div>
                                <div class="flex items-center mt-6 text-blue-600 font-semibold text-sm group-hover:text-blue-700">
                                    <span>Takımlarımı Yönet</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>
                        
                        <!-- Katılıma Açık Takımlar Kartı -->
                        <a href="{{ route('takimlar.index') }}" 
                            class="group relative bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-violet-600/5 rounded-2xl"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-purple-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                            <div class="relative">
                                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Katılıma Açık Takımlar</h3>
                                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['acik_takim_sayisi'] }}</p>
                                <div class="space-y-2">
                                    @forelse($stats['son_acik_takimlar'] as $takim)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-700 font-medium">{{ $takim->ad }}</span>
                                            <span class="text-purple-600 text-xs bg-purple-100 px-2 py-1 rounded-md font-medium">{{ $takim->uyeler_count }} üye</span>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-sm italic">Katılıma açık takım yok.</p>
                                    @endforelse
                                </div>
                                <div class="flex items-center mt-6 text-purple-600 font-semibold text-sm group-hover:text-purple-700">
                                    <span>Takımlara Göz At</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>

                        <!-- YENİ: Projelerim (IAA) Kartı -->
                        @if(isset($stats['iaa_projelerim_count']))
                            <a href="{{ route('iaa.takimProjeleri') }}" 
                                class="group relative bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-yellow-600/5 to-orange-600/5 rounded-2xl"></div>
                                <div class="absolute top-0 right-0 w-20 h-20 bg-yellow-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                                <div class="relative">
                                    <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Devam Eden İAA Projelerim</h3>
                                    <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['iaa_projelerim_count'] }}</p>
                                    <div class="space-y-2">
                                        @forelse($stats['son_iaa_projelerim'] as $proje)
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($proje->baslik, 20) }}</span>
                                                <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap">{{ $proje->onaylanma_tarihi->format('d.m.Y') }}</span>
                                            </div>
                                        @empty
                                            <p class="text-gray-500 text-sm italic">Devam eden İAA projeniz yok.</p>
                                        @endforelse
                                    </div>
                                    <div class="flex items-center mt-6 text-yellow-600 font-semibold text-sm group-hover:text-yellow-700">
                                        <span>Projelerime Git</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                            </a>
                        @endif

                        <!-- YENİ: Projelerim (Şikayet) Kartı -->
                        @if(isset($stats['sikayet_projelerim_count']))
                            {{-- === DÜZELTME: Rota YENİ SAYFAYA GİTMELİ === --}}
                            <a href="{{ route('sikayet-gorevlerim.index') }}" 
                                class="group relative bg-gradient-to-br from-red-50 to-pink-50 border border-red-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-red-600/5 to-pink-600/5 rounded-2xl"></div>
                                <div class="absolute top-0 right-0 w-20 h-20 bg-red-500/10 rounded-full transform translate-x-8 -translate-y-8"></div>
                                <div class="relative">
                                    <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Devam Eden Şikayet Projelerim</h3>
                                    <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['sikayet_projelerim_count'] }}</p>
                                    <div class="space-y-2">
                                        @forelse($stats['son_sikayet_projelerim'] as $proje)
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($proje->baslik, 20) }}</span>
                                                <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap">{{ $proje->onaylanma_tarihi->format('d.m.Y') }}</span>
                                            </div>
                                        @empty
                                            <p class="text-gray-500 text-sm italic">Devam eden şikayet projeniz yok.</p>
                                        @endforelse
                                    </div>
                                    <div class="flex items-center mt-6 text-red-600 font-semibold text-sm group-hover:text-red-700">
                                        <span>Projelerime Git</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                            </a>
                        @endif

                    </div>
                @endif
                {{-- === STANDART KULLANICI / KURUL GÖRÜNÜMÜ SONU === --}}
            
            @endif {{-- isset($stats) kapanışı --}}
            
        </div>
    </div>
</x-app-layout>