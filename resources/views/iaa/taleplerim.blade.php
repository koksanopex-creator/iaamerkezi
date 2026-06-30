@push('pageTitle')
    Projelerim - Taleplerim | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Talep Ettiğim İyileştirmeler') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- BAŞARI VE HATA MESAJLARI --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Talep Tarihi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="relative px-6 py-3"><span class="sr-only">İşlemler</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($taleplerim as $talep)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $talep->baslik }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $talep->atanma_tarihi ? \Carbon\Carbon::parse($talep->atanma_tarihi)->format('d.m.Y H:i') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($talep->durum == 'Talep Edildi') bg-blue-100 text-blue-800 @endif
                                            @if($talep->durum == 'Atandı') bg-green-100 text-green-800 @endif
                                        ">
                                            {{ $talep->durum }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center space-x-2 justify-end">
                                            <a href="{{ route('iaa.show', $talep) }}" class="px-3 py-1 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">İncele</a>
                                            
                                            @if ($talep->durum == 'Talep Edildi')
                                                <form method="POST" action="{{ route('iaa.talebiGeriCek', $talep) }}" onsubmit="return confirm('Bu talebi iptal etmek istediğinizden emin misiniz? Öneri tekrar havuza dönecektir.');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">Talebi İptal Et</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Henüz bir iyileştirme talebinde bulunmadınız.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>