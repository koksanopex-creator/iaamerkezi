<div x-show="activeTab === 'sikayetler'" class="space-y-4" x-data="{ limit: 5, total: {{ $girilenSikayetler->count() }} }">
    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
        Bildirilen Şikayetler
        <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-bold border border-gray-200">
            {{ $girilenSikayetler->count() }}
        </span>
    </h3>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şikayet Konusu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kazanılan Puan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($girilenSikayetler as $sikayet)
                    <tr x-show="{{ $loop->index }} < limit" x-transition>
                        <td class="px-6 py-4 text-sm text-gray-400 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" class="font-bold text-indigo-700 hover:text-indigo-900 hover:underline transition-colors">
                                {{ $sikayet->musteri_sikayet_konusu }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {!! $sikayet->getMusteriDurumBadgeAttribute() !!}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-bold">
                            {{ $sikayet->kazanilan_puan > 0 ? '+' . $sikayet->kazanilan_puan : '0' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $sikayet->created_at->format('d.m.Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Kayıt yok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($girilenSikayetler->count() > 5)
    <div class="mt-4 flex justify-center">
        <button @click="limit = (limit === 5 ? total : 5)" 
                class="inline-flex items-center px-6 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 shadow-sm hover:bg-gray-50 transition-all">
            <span x-text="limit === 5 ? 'Daha Fazla Göster' : 'Daha Az Göster'"></span>
            <svg class="w-3 h-3 ml-2 transform transition-transform" :class="limit !== 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
    </div>
    @endif
</div>