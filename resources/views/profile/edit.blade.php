<x-app-layout>
    {{-- Arka Plan: Resim yerine CSS Gradient (Yazı sorunu çözüldü) --}}
    <div class="relative bg-gradient-to-r from-indigo-900 to-blue-800 pb-32 overflow-hidden shadow-xl">
        <div class="relative max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">Profilim</h1>
            <p class="mt-2 text-xl text-indigo-200">Hoşgeldiniz, {{ $user->name }}</p>
            <p class="mt-1 text-sm text-indigo-300 opacity-80">Son İşlem: {{ $user->updated_at->format('d.m.Y H:i') }}</p>
        </div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12">
        
        {{-- ÜST KART --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="sm:flex sm:items-center sm:justify-between p-6 sm:p-8">
                <div class="sm:flex sm:items-center">
                    <div class="flex-shrink-0">
                        @if($user->profile_photo_path)
                            <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                        @else
                            <div class="h-24 w-24 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 p-1 shadow-lg">
                                <div class="h-full w-full rounded-full bg-white flex items-center justify-center text-3xl font-bold text-indigo-600 uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6 text-center sm:text-left">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-sm font-medium text-gray-500">{{ $user->email }}</p>
                        @if($user->telefon)
                            <p class="text-sm text-gray-400 mt-1 flex items-center justify-center sm:justify-start gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $user->telefon }}
                            </p>
                        @endif
                        <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            {{-- Rol Etiketi --}}
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $user->roles->first()->name ?? 'Kullanıcı' }}
                            </span>

                            {{-- EKLENEN BUTON: HERKESE AÇIK PROFİLİ GÖR --}}
                            <a href="{{ route('profile.show', $user->id) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors border border-gray-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Herkese Açık Profili Gör
                            </a>
                        </div>
                    </div>
                </div>
                
                {{-- PUAN KARTI (LİNKLİ) --}}
                <div class="mt-5 sm:mt-0 text-center">
                    <a href="{{ route('kullanici.puanlari', $user->id) }}" class="inline-block p-4 rounded-xl bg-gradient-to-br from-yellow-50 to-amber-50 border border-amber-200 shadow-sm hover:shadow-md transition-all transform hover:scale-105 cursor-pointer">
                        <p class="text-xs font-bold text-amber-600 uppercase tracking-wide">TOPLAM PUAN</p>
                        <p class="text-3xl font-extrabold text-amber-500 mt-1">{{ number_format($user->toplam_puan ?? 0, 0, ',', '.') }}</p>
                        <span class="text-[10px] text-amber-400 block mt-1">Detaylar &rarr;</span>
                    </a>
                </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50 grid grid-cols-3 divide-x divide-gray-200">
                <div class="p-4 text-center">
                    <span class="block text-xl font-bold text-gray-800">{{ $tamamlananProjeSayisi }}</span>
                    <span class="block text-xs text-gray-500 font-medium uppercase">Tamamlanan</span>
                </div>
                <div class="p-4 text-center">
                    <span class="block text-xl font-bold text-gray-800">{{ $aktifProjeSayisi }}</span>
                    <span class="block text-xs text-gray-500 font-medium uppercase">Aktif Görev</span>
                </div>
                <div class="p-4 text-center">
                    <span class="block text-xl font-bold text-gray-800">{{ $takimlar->count() }}</span>
                    <span class="block text-xs text-gray-500 font-medium uppercase">Takım</span>
                </div>
            </div>
        </div>

        {{-- TAB YAPISI --}}
        <div x-data="{ activeTab: 'timeline' }" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <nav class="space-y-2 bg-white rounded-xl shadow-sm p-2">
                    <button @click="activeTab = 'timeline'" :class="activeTab === 'timeline' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'" class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        Son Aktiviteler
                    </button>
                    <button @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'" class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        Projelerim ({{ $kullaniciProjeleri->count() }})
                    </button>
                    <button @click="activeTab = 'teams'" :class="activeTab === 'teams' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'" class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        Takımlarım
                    </button>
                    <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'" class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        Hesap Ayarları
                    </button>
                    {{-- DİSİPLİN SEKMESİ (SAYAÇLI) --}}
                    @php
                        $disiplinSayisi = \App\Models\DisciplinaryCase::where('user_id', Auth::id())->count();
                    @endphp
                    
                    <button @click="activeTab = 'disciplinary'" :class="activeTab === 'disciplinary' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'" class="group px-4 py-3 flex items-center justify-between text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        <div class="flex items-center">
                             <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-indigo-500" :class="activeTab === 'disciplinary' ? 'text-indigo-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            Disiplin Dosyalarım ({{ $disiplinSayisi }})
                        </div>
                    </button>
                </nav>
            </div>

            <div class="lg:col-span-2">
                
                {{-- TAB: SON AKTİVİTELER --}}
                <div x-show="activeTab === 'timeline'" x-transition class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Son Aktiviteler</h3>
                    <ul class="divide-y divide-gray-100">
                        @forelse($sonAktiviteler as $log)
                            <li class="py-4">
                                <div class="flex space-x-3">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800 font-medium">{{ $log->eylem }}</p>
                                        <p class="text-sm text-gray-500">{{ $log->aciklama }}</p>
                                        <span class="text-xs text-gray-400 block mt-1">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="py-4 text-center text-gray-500 text-sm">Henüz bir aktivite yok.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- TAB: PROJELERİM --}}
                <div x-show="activeTab === 'projects'" x-transition style="display: none;" class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Dahil Olduğum Projeler</h3>
                    <div class="space-y-4">
                        @forelse($kullaniciProjeleri as $proje)
                            <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition">
                                <div>
                                    <a href="{{ route('proje.workspace.show', $proje->id) }}" class="font-bold text-indigo-600 hover:underline">
                                        {{ $proje->baslik }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">Takım: {{ $proje->atananTakim->ad ?? '-' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $proje->durum == 'Tamamlandı' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $proje->durum }}
                                </span>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">Henüz bir projede yer almıyorsunuz.</p>
                        @endforelse
                    </div>
                </div>

                {{-- TAB: TAKIMLARIM --}}
                <div x-show="activeTab === 'teams'" x-transition style="display: none;" class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Takımlarım</h3>
                    <div class="grid grid-cols-1 gap-4">
                        @forelse($takimlar as $takim)
                            <div class="p-4 border rounded-lg flex items-center justify-between hover:bg-gray-50">
                                <div>
                                    <p class="font-bold text-gray-900">{{ $takim->ad }}</p>
                                    <p class="text-xs text-gray-500">{{ $takim->lider ? $takim->lider->name . ' (Lider)' : '' }}</p>
                                </div>
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">{{ substr($takim->ad, 0, 1) }}</div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 text-sm">Henüz bir takıma dahil değilsiniz.</div>
                        @endforelse
                    </div>
                </div>

                {{-- TAB: AYARLAR --}}
                <div x-show="activeTab === 'settings'" x-transition style="display: none;">
                    <div class="space-y-6">
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                {{-- TAB İÇERİĞİ: DİSİPLİN --}}
                <div x-show="activeTab === 'disciplinary'" x-transition style="display: none;">
                    
                    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Disiplin Geçmişim</h3>

                        @php
                            // Kullanıcının hiç disiplin kaydı var mı kontrol edelim
                            $hasDisciplinaryRecord = \App\Models\DisciplinaryCase::where('user_id', Auth::id())->exists();
                        @endphp

                        @if($hasDisciplinaryRecord)
                            {{-- KAYIT VARSA: Dashboard parçalarını çağır --}}
                            @include('dashboard.partials.disciplinary-waiting')
                            @include('dashboard.partials.disciplinary-active')
                        @else
                            {{-- KAYIT YOKSA: Tertemiz Sicil Kartı Göster --}}
                            <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4 animate-bounce">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">Siciliniz Tertemiz!</h3>
                                <p class="mt-2 text-gray-500 max-w-sm mx-auto">
                                    Adınıza kayıtlı herhangi bir disiplin süreci veya tutanak bulunmamaktadır. Örnek çalışmalarınız için teşekkür ederiz.
                                </p>
                                <div class="mt-6 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-bold text-sm shadow-lg shadow-green-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                                    Performansa Devam
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>