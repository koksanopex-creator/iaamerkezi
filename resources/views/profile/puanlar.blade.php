<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ $user->name }}
            </h2>
            <div class="text-right">
                <span class="text-xs text-gray-500 block">Son Görülme</span>
                <span class="text-sm font-medium {{ $user->isOnline() ? 'text-green-600' : 'text-gray-600' }}">
                    {{ $user->last_seen_at ? \Carbon\Carbon::parse($user->last_seen_at)->diffForHumans() : 'Giriş Yapmadı' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- TARİH FİLTRESİ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('profile.puanlar', $user->id) }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Bitiş Tarihi</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="flex gap-2">
                         <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Filtrele
                        </button>
                        @if($startDate || $endDate)
                            <a href="{{ route('profile.puanlar', $user->id) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Temizle
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- ÖZET KARTI --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-between items-center bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                <div>
                    <p class="text-sm opacity-80 uppercase tracking-widest font-bold">
                        @if($startDate || $endDate)
                            Seçili Tarih Aralığı Toplamı
                        @else
                            Genel Toplam Puan
                        @endif
                    </p>
                    <p class="text-5xl font-black mt-2">{{ number_format($toplam_puan, 0) }}</p>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-sm opacity-80">Son Güncelleme</p>
                    <p class="font-bold">{{ now()->format('d.m.Y H:i') }}</p>
                </div>
            </div>

            {{-- 1. PROJELER TABLOSU --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-800 p-2 rounded-lg mr-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </span>
                        Tamamlanan Projelerden Kazanılan Puanlar
                    </h3>
                    @if($tum_projeler->isEmpty())
                        <p class="text-gray-500 italic">Bu kritere uygun tamamlanmış proje bulunamadı.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proje</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol / Sebep</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Onay Tarihi</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Puan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($tum_projeler as $proje)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <a href="{{ route('proje.workspace.show', $proje->id) }}" class="hover:text-blue-600 hover:underline">
                                                    {{ $proje->baslik }}
                                                </a>
                                            </td>
                                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($proje->musteriSikayeti)
                                                     <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                                                        Müşteri Şikayeti
                                                    </span>
                                                @else
                                                     <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                        İAA Projesi
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $proje->kazanma_sebebi == 'Takım Lideri' ? 'bg-purple-100 text-purple-800' : ($proje->kazanma_sebebi == 'Proje Ekibi (Squad)' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}" title="{{ $proje->kazanma_sebebi == 'Takım Üyesi' ? 'Bu projeye doğrudan atanmadınız ancak üyesi olduğunuz takım bu projeyi tamamladığı için puan kazandınız.' : '' }}">
                                                    {{ $proje->kazanma_sebebi }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($proje->onaylanma_tarihi)->format('d.m.Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                                +{{ number_format($proje->puan, 0) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. ŞİKAYET GİRİŞLERİ --}}
            @if($sikayet_girisleri->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-rose-100 text-rose-800 p-2 rounded-lg mr-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </span>
                            Şikayet Bildirim Puanları
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Toplam {{ $sikayet_girisleri->count() }} adet şikayet bildirimi yapıldı. (Adet Başına: {{ $sikayet_giris_puani }} Puan)</p>
                        
                        <div class="overflow-x-auto mb-4">
                             <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şikayet Başlığı</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Puan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($sikayet_girisleri as $sikayet)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" class="hover:underline hover:text-rose-600">
                                                     {{ $sikayet->musteri_sikayet_konusu }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $sikayet->created_at->format('d.m.Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                                +{{ $sikayet_giris_puani }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                             </table>
                        </div>

                        <div class="text-right font-bold text-green-600 text-xl border-t pt-2">
                            Toplam: +{{ number_format($sikayet_girisleri->count() * $sikayet_giris_puani, 0) }} Puan
                        </div>
                    </div>
                </div>
            @endif

            {{-- 3. ÖNERİLER --}}
             @if($oneriler->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                           <span class="bg-yellow-100 text-yellow-800 p-2 rounded-lg mr-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            </span>
                            İAA Öneri Puanları
                        </h3>
                        
                        <div class="overflow-x-auto mb-4">
                             <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Öneri Başlığı</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Puan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($oneriler as $oneri)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <a href="{{ route('proje.workspace.show', $oneri->id) }}" class="hover:underline hover:text-yellow-600">
                                                     {{ $oneri->baslik }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $oneri->created_at->format('d.m.Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                                +{{ $oneri_puani }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                             </table>
                        </div>

                        <div class="text-right font-bold text-green-600 text-xl">
                            +{{ number_format($oneriler->count() * $oneri_puani, 0) }} Puan
                        </div>
                    </div>
                </div>
            @endif

            {{-- 4. CEZALAR --}}
            @if($cezalar->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-red-700 mb-4 flex items-center">
                            <span class="bg-red-100 text-red-800 p-2 rounded-lg mr-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </span>
                            Disiplin Cezaları (Kesintiler)
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Olay</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karar Tarihi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karar</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ceza Puanı</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($cezalar as $ceza)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                @php
                                                    $canView = false;
                                                    // 1. Kullanıcının kendisi
                                                    if (auth()->id() == $user->id) $canView = true;
                                                    // 2. Üst Düzey Roller
                                                    elseif (auth()->user()->hasRole(['Superadmin', 'Yonetim', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Disiplin Kurulu Üyesi'])) $canView = true;
                                                    // 3. Kendi Bölümünün Lideri (Farklı bölüm liderleri göremez)
                                                    elseif (auth()->user()->hasRole('Bölüm Lideri') && auth()->user()->bolum_id == $user->bolum_id) $canView = true;
                                                @endphp

                                                @if($canView)
                                                    <a href="{{ route('disiplin.show', $ceza->id) }}" class="hover:underline hover:text-red-600 font-medium">
                                                        {{ $ceza->olay_basligi ?? $ceza->olay_aciklamasi }}
                                                    </a>
                                                    @if(isset($ceza->olay_basligi))
                                                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($ceza->olay_aciklamasi, 50) }}</p>
                                                    @endif
                                                @else
                                                    <span class="italic text-gray-400 select-none blur-sm hover:blur-none transition-all duration-300">Gizli İçerik (Disiplin Cezası)</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($ceza->karar_tarihi)->format('d.m.Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $ceza->final_karar }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-red-600">
                                                -{{ number_format($ceza->hesaplanan_puan, 0) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>