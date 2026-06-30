@push('pageTitle')
    Takım Yönetimi | 
@endpush

<x-app-layout>
    {{-- ======================== SAYFA BAŞLIĞI (HEADER) ======================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Takım Yönetimi') }}
        </h2>
    </x-slot>

    {{-- ======================== ANA SAYFA İÇERİĞİ ======================== --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">

                    {{-- Modern Sayfa Başlığı ve "Yeni Takım" Butonu --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div class="flex items-center space-x-4">
                            <div
                                class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Tüm Takımlar</h3>
                                <p class="mt-1 text-base text-gray-600">Sistemde kayıtlı tüm takımları buradan
                                    yönetebilirsiniz.</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:mt-0 flex-shrink-0">
                            <a href="{{ route('admin.takim-yonetim.create') }}"
                                class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow-sm hover:from-indigo-700 hover:to-blue-600 transform hover:-translate-y-0.5 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Yeni Takım Oluştur
                            </a>
                        </div>
                    </div>

                    {{-- Başarı/Hata Mesajları --}}
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md"
                            role="alert">
                            <p>{{ session('success') }}</p>
                    </div>@endif
                    @if(session('error'))
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                            <p>{{ session('error') }}</p>
                    </div>@endif

                    
                    {{-- ========================================================== --}}
                    {{-- 1. GRUP: İAA ÇÖZÜM TAKIMLARI (Standart) --}}
                    {{-- ========================================================== --}}
                    <div class="mb-12">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">İAA Çözüm Takımları</h3>
                                <p class="text-sm text-gray-500">İyileştirme ve geliştirme projeleri için kurulan takımlar.</p>
                            </div>
                        </div>

                        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-inner border border-gray-200/80 overflow-hidden">
                            <table class="block sm:table min-w-full">
                                <thead class="hidden sm:table-header-group">
                                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200 bg-indigo-50/30">
                                        <th class="px-6 py-4">Takım Adı</th>
                                        <th class="px-6 py-4">Lider</th>
                                        <th class="px-6 py-4 text-center">Üye Sayısı</th>
                                        <th class="px-6 py-4">Oluşturulma Tarihi</th>
                                        <th class="px-6 py-4 text-right">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody class="block sm:table-row-group">
                                    @forelse ($iaaTakimlari as $takim)
                                        <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-indigo-50 transition-colors">
                                            <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle">
                                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span>
                                                <a href="{{ route('takimlar.show', $takim) }}" target="_blank" class="text-right sm:text-left font-medium text-indigo-600 hover:text-indigo-800 hover:underline block w-full h-full">{{ $takim->ad }}</a>
                                            </td>

                                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Lider:</span>
                                                <div class="text-right sm:text-left">
                                                    @if($takim->lider)
                                                        <a href="{{ route('profile.show', $takim->lider->id) }}" target="_blank" class="inline-flex items-center gap-2 group">
                                                            @if($takim->lider->profile_photo_path)
                                                                <img class="h-8 w-8 rounded-full object-cover border border-gray-200 group-hover:border-indigo-500 transition-colors" src="{{ asset('storage/' . $takim->lider->profile_photo_path) }}" alt="{{ $takim->lider->name }}">
                                                            @else
                                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs text-indigo-700 font-bold group-hover:bg-indigo-200 transition-colors">
                                                                    {{ substr($takim->lider->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                            <span class="text-sm font-medium text-gray-600 group-hover:text-indigo-600 hover:underline transition-colors">{{ $takim->lider->name }}</span>
                                                        </a>
                                                    @else
                                                        <span class="text-sm text-gray-400 italic">Lider Yok</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Üyeler:</span>
                                                <div class="w-full text-right sm:text-center"><span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $takim->uyeler_count }} Üye</span></div>
                                            </td>
                                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span><span class="text-right sm:text-left text-sm text-gray-500">{{ $takim->created_at->format('d.m.Y') }}</span>
                                            </td>
                                            <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                                <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center sm:space-x-2 space-y-2 sm:space-y-0">
                                                    <a href="{{ route('admin.takim-yonetim.show', $takim) }}" class="w-full sm:w-auto inline-flex justify-center text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 hover:bg-gray-50">Detay</a>
                                                    <a href="{{ route('admin.takim-yonetim.edit', $takim) }}" class="w-full sm:w-auto inline-flex justify-center text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-indigo-700">Düzenle</a>
                                                    <form class="inline-block w-full sm:w-auto" method="POST" action="{{ route('admin.takim-yonetim.destroy', $takim) }}" onsubmit="return confirm('\'{{ $takim->ad }}\' takımını kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="w-full inline-flex justify-center text-sm font-semibold text-white bg-red-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-red-700">Sil</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="block sm:table-row"><td colspan="5" class="p-12 text-center text-gray-500">Bu grupta henüz oluşturulmuş bir takım bulunmamaktadır.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $iaaTakimlari->appends(['sikayet_page' => request('sikayet_page')])->links() }}
                        </div>
                    </div>

                    {{-- ========================================================== --}}
                    {{-- 2. GRUP: MÜŞTERİ ŞİKAYETİ TAKIMLARI --}}
                    {{-- ========================================================== --}}
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Müşteri Şikayeti Takımları</h3>
                                <p class="text-sm text-gray-500">Müşteri şikayetlerini çözmek için kurulan özel takımlar.</p>
                            </div>
                        </div>

                        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-inner border border-gray-200/80 overflow-hidden">
                            <table class="block sm:table min-w-full">
                                <thead class="hidden sm:table-header-group">
                                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200 bg-red-50/30">
                                        <th class="px-6 py-4">Takım Adı</th>
                                        <th class="px-6 py-4">Lider</th>
                                        <th class="px-6 py-4 text-center">Üye Sayısı</th>
                                        <th class="px-6 py-4">Oluşturulma Tarihi</th>
                                        <th class="px-6 py-4 text-right">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody class="block sm:table-row-group">
                                    @forelse ($sikayetTakimlari as $takim)
                                        <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-red-50 transition-colors">
                                            <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle">
                                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span>
                                                <a href="{{ route('takimlar.show', $takim) }}" target="_blank" class="text-right sm:text-left font-medium text-red-600 hover:text-red-800 hover:underline block w-full h-full">{{ $takim->ad }}</a>
                                            </td>

                                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Lider:</span>
                                                <div class="text-right sm:text-left">
                                                    @if($takim->lider)
                                                        <a href="{{ route('profile.show', $takim->lider->id) }}" target="_blank" class="inline-flex items-center gap-2 group">
                                                            @if($takim->lider->profile_photo_path)
                                                                <img class="h-8 w-8 rounded-full object-cover border border-gray-200 group-hover:border-red-500 transition-colors" src="{{ asset('storage/' . $takim->lider->profile_photo_path) }}" alt="{{ $takim->lider->name }}">
                                                            @else
                                                                <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-xs text-red-700 font-bold group-hover:bg-red-200 transition-colors">
                                                                    {{ substr($takim->lider->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                            <span class="text-sm font-medium text-gray-600 group-hover:text-red-600 hover:underline transition-colors">{{ $takim->lider->name }}</span>
                                                        </a>
                                                    @else
                                                        <span class="text-sm text-gray-400 italic">Lider Yok</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Üyeler:</span>
                                                <div class="w-full text-right sm:text-center"><span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $takim->uyeler_count }} Üye</span></div>
                                            </td>
                                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span><span class="text-right sm:text-left text-sm text-gray-500">{{ $takim->created_at->format('d.m.Y') }}</span>
                                            </td>
                                            <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                                <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center sm:space-x-2 space-y-2 sm:space-y-0">
                                                    <a href="{{ route('admin.takim-yonetim.show', $takim) }}" class="w-full sm:w-auto inline-flex justify-center text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 hover:bg-gray-50">Detay</a>
                                                    <a href="{{ route('admin.takim-yonetim.edit', $takim) }}" class="w-full sm:w-auto inline-flex justify-center text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-indigo-700">Düzenle</a>
                                                    <form class="inline-block w-full sm:w-auto" method="POST" action="{{ route('admin.takim-yonetim.destroy', $takim) }}" onsubmit="return confirm('\'{{ $takim->ad }}\' takımını kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="w-full inline-flex justify-center text-sm font-semibold text-white bg-red-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-red-700">Sil</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="block sm:table-row"><td colspan="5" class="p-12 text-center text-gray-500">Bu grupta henüz oluşturulmuş bir takım bulunmamaktadır.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $sikayetTakimlari->appends(['iaa_page' => request('iaa_page')])->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>