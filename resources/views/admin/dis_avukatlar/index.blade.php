@push('pageTitle')
    Dış Avukat Listesi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Dış Avukat Listesi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-end mb-4">
                    <a href="{{ route('admin.dis_avukatlar.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        + Yeni Dış Avukat Tanımla
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Avukat Adı</th>
                                <th class="py-3 px-6 text-left">E-posta</th>
                                <th class="py-3 px-6 text-left">Kayıt Tarihi</th>
                                <th class="py-3 px-6 text-center">Durum</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            {{-- Controller'dan $lawyers veya $avukatlar değişkeni geliyor --}}
                            @forelse($lawyers ?? $avukatlar as $avukat)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left whitespace-nowrap font-bold">{{ $avukat->name }}</td>
                                    <td class="py-3 px-6 text-left">{{ $avukat->email }}</td>
                                    <td class="py-3 px-6 text-left">{{ $avukat->created_at->format('d.m.Y') }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Aktif</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">Henüz tanımlı dış avukat yok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>