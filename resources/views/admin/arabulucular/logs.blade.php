<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Arabulucu İşlem Kayıtları (Loglar)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-4">
                    <a href="{{ route('admin.arabulucular.index') }}" class="text-indigo-600 hover:underline">&larr; Listeye Dön</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">İşlemi Yapan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">İşlem Türü</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Etkilenen Arabulucu</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Detay</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Tarih</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($logs as $log)
                                <tr class="hover:bg-gray-50 text-sm">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $log->user->name ?? 'Sistem' }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->ip_adres }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $renk = match($log->islem_turu) {
                                                'OLUŞTURMA' => 'bg-green-100 text-green-800',
                                                'DÜZENLEME' => 'bg-blue-100 text-blue-800',
                                                'SİLME' => 'bg-red-100 text-red-800',
                                                'DURUM DEĞİŞTİRME' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-bold {{ $renk }}">{{ $log->islem_turu }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $log->arabulucu->name ?? 'Silinmiş Kayıt' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ Str::limit($log->detay, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-500">
                                        {{ $log->created_at->format('d.m.Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>