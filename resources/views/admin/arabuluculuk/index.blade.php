<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Arabuluculuk Dosyaları') }}
            </h2>

            {{-- Herhangi bir dosya açma yetkisi varsa butonu göster --}}
            @if(auth()->user()->hasRole('Superadmin') || auth()->user()->canAny(['arabuluculuk.create_ihtiyari', 'arabuluculuk.create_zorunlu']))
                <a href="{{ route('admin.arabuluculuk.create') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Yeni Dosya Aç
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- İstatistik Kartları (Opsiyonel) --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-xs font-bold uppercase">Toplam Dosya</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $cases->total() }}</div>
                </div>
                {{-- Buraya Bekleyen, Tamamlanan gibi diğer istatistikler eklenebilir --}}
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dosya No / Tarih</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Çalışan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tür / Sorumlu</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Arabulucu / Avukat</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Durum</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mutabakat</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($cases as $case)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $case->dosya_no ?? '---' }}</div>
                                        <div class="text-xs text-gray-500">{{ $case->created_at->format('d.m.Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                                {{ substr($case->calisan->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $case->calisan->name ?? 'Silinmiş Kullanıcı' }}</div>
                                                <div class="text-xs text-gray-500">{{ $case->calisan->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($case->type == 'ihtiyari')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">İhtiyari</span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Zorunlu</span>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">Yöneten: {{ ucfirst($case->owner_role) }}
                                        </div>
                                        <div class="text-[10px] text-indigo-400 mt-0.5">
                                            <span class="font-semibold">Açan:</span> {{ $case->creator->name ?? 'Sistem' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $case->arabulucu->name ?? '-' }}</div>
                                        @if($case->external_lawyer_id)
                                            <div class="text-xs text-purple-600 font-semibold">Av.
                                                {{ $case->externalLawyer->name ?? '' }} (Dış)</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        {{-- Durum Badge Mantığı --}}
                                        @php
                                            $statusColors = [
                                                'taslak' => 'bg-gray-100 text-gray-800',
                                                'hukuk_incelemesinde' => 'bg-yellow-100 text-yellow-800',
                                                'yonetim_onayinda' => 'bg-purple-100 text-purple-800',
                                                'arabulucuda' => 'bg-blue-100 text-blue-800',
                                                'imza_asamasinda' => 'bg-indigo-100 text-indigo-800',
                                                'odeme_bekliyor' => 'bg-orange-100 text-orange-800 border border-orange-500 animate-pulse font-black',
                                                'kapatildi' => 'bg-green-100 text-green-800',
                                                'anlasma_saglanamadi' => 'bg-red-100 text-red-800',
                                            ];
                                            $color = $statusColors[$case->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-md {{ $color }}">
                                            {{ str_replace('_', ' ', strtoupper($case->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($case->mutabakat == 'anlasildi')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-emerald-100 text-emerald-800">
                                                ANLAŞILDI
                                            </span>
                                        @elseif($case->mutabakat == 'anlasilmadi')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-rose-100 text-rose-800">
                                                ANLAŞILMADI
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-md bg-slate-100 text-slate-500 text-[10px]">
                                                BİLGİ YOK
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(!auth()->user()->hasRole('Direktör') || auth()->user()->hasRole('Superadmin'))
                                            <a href="{{ route('admin.arabuluculuk.show', $case->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 font-bold">Detay &rarr;</a>
                                        @else
                                            <span class="text-gray-400 text-xs italic">Erişim Kısıtlı</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        Henüz kayıtlı bir arabuluculuk dosyası bulunmamaktadır.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    {{ $cases->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>