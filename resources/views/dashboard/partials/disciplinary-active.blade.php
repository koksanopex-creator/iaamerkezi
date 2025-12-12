@php
    $aktifDosyalar = \App\Models\DisciplinaryCase::where('user_id', Auth::id())
        ->where('durum', '!=', 'Savunma Bekleniyor')
        ->with(['behavior.category']) // İlişkileri çekelim
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

@if($aktifDosyalar->isNotEmpty())
    <div class="mb-8">
        <h3 class="text-lg font-bold text-gray-700 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Disiplin Dosyalarım & Geçmişim
        </h3>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Dosya No</th>
                            <th class="px-6 py-3">Konu / İhlal</th>
                            <th class="px-6 py-3">Olay Tarihi</th>
                            <th class="px-6 py-3">Savunma Tarihi</th>
                            <th class="px-6 py-3">Durum</th>
                            <th class="px-6 py-3 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aktifDosyalar as $dosya)
                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    #{{ $dosya->id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">{{ $dosya->behavior->category->ad ?? 'Genel' }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($dosya->behavior->tanim ?? '-', 30) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $dosya->olay_tarihi->format('d.m.Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($dosya->savunma_tarihi)
                                        <span class="text-gray-600">{{ $dosya->savunma_tarihi->format('d.m.Y H:i') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeColor = match($dosya->durum) {
                                            'Yönetici Değerlendirmesi' => 'bg-blue-100 text-blue-800',
                                            'Kurulda' => 'bg-purple-100 text-purple-800',
                                            'Karar Verildi' => 'bg-green-100 text-green-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badgeColor }}">
                                        {{ $dosya->durum }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('disiplin.show', $dosya->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline flex items-center justify-end gap-1">
                                        İncele
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif