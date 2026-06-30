<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            📜 Tüm İşlem Kayıtları
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <div class="mb-4 flex justify-between items-center">
                    <p class="text-sm text-gray-500">Sistemde gerçekleşen tüm müşteri ve yetkili işlemleri aşağıda gruplandırılmıştır.</p>
                    <span class="text-xs bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-bold">Toplam: {{ $logs->total() }} Kayıt</span>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Açıklama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">IP</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $currentCustomerId = null; @endphp

                            @foreach($logs as $log)
                                {{-- GRUPLAMA MANTIĞI: Müşteri ID değiştiyse başlık at --}}
                                @if($currentCustomerId !== $log->customer_id)
                                    <tr class="bg-indigo-50 border-t-2 border-indigo-100">
                                        <td colspan="5" class="px-6 py-3 text-left">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                @if($log->customer)
                                                    <a href="{{ route('musteri.profil.show', $log->customer_id) }}" class="font-bold text-indigo-800 text-sm hover:underline">
                                                        {{ $log->customer->name }}
                                                    </a>
                                                @else
                                                    <span class="font-bold text-gray-600 text-sm">Genel / Sistemsel Kayıtlar</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @php $currentCustomerId = $log->customer_id; @endphp
                                @endif

                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 w-32">
                                        {{ $log->created_at->format('d.m.Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap w-32">
                                        @if(Str::contains($log->islem_turu, 'Silme'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                {{ $log->islem_turu }}
                                            </span>
                                        @elseif(Str::contains($log->islem_turu, 'Giriş'))
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $log->islem_turu }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $log->islem_turu }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 w-48">
                                        {{ $log->user ? $log->user->name : 'Sistem/Misafir' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ Str::limit($log->aciklama, 60) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 font-mono w-32">
                                        {{ $log->ip_adresi }}
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