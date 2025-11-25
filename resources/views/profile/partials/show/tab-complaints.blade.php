<div x-show="activeTab === 'sikayetler'" class="space-y-4">
    <h3 class="text-lg font-bold text-gray-800">Bildirilen Şikayetler</h3>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şikayet Konusu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kazanılan Puan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($girilenSikayetler as $sikayet)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $sikayet->baslik }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sikayet->durum == 'Tamamlandı' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $sikayet->durum }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-bold">{{ $sikayet->puan > 0 ? '+' . $sikayet->puan : '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $sikayet->created_at->format('d.m.Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Kayıt yok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>