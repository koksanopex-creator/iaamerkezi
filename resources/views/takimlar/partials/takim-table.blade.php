<div class="bg-white/60 backdrop-blur-sm overflow-hidden">
    @if($takimlar->isNotEmpty())
        <table class="block sm:table min-w-full">
            <thead class="hidden sm:table-header-group">
                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                    <th scope="col" class="px-6 py-4">Takım Adı</th>
                    <th scope="col" class="px-6 py-4">Lider</th>
                    <th scope="col" class="px-6 py-4">Üye Sayısı</th>
                    @if($type === 'katildigim')
                        <th scope="col" class="px-6 py-4">Oluşturulma</th>
                    @endif
                    <th scope="col" class="relative px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="block sm:table-row-group">
                @foreach ($takimlar as $takim)
                    <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-gray-50 transition-colors duration-200 group">
                        
                        <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle">
                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span>
                            <div class="text-right sm:text-left flex items-center">
                                <div class="flex-shrink-0 h-10 w-10"><div class="h-10 w-10 rounded-full bg-gradient-to-r {{ $type === 'katildigim' ? 'from-blue-400 to-indigo-500' : 'from-gray-400 to-gray-500' }} flex items-center justify-center"><span class="text-sm font-bold text-white">{{ Str::substr($takim->ad, 0, 1) }}</span></div></div>
                                <div class="ml-4"><div class="text-sm font-semibold text-gray-900">{{ $takim->ad }}</div></div>
                            </div>
                        </td>
                        
                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Lider:</span>
                            <div class="text-right sm:text-left">
                                <div class="text-sm font-medium text-gray-900">{{ $takim->lider->name }}</div>
                                <div class="text-xs text-gray-500">{{ $takim->lider->bolum->ad ?? 'Bölüm Yok' }}</div>
                            </div>
                        </td>

                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                             <span class="font-semibold text-sm text-gray-500 sm:hidden">Üyeler:</span>
                             <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200">{{ $takim->uyeler_count }} Üye</span>
                        </td>

                        @if($type === 'katildigim')
                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Oluşturulma:</span>
                            <span class="text-right sm:text-left text-sm text-gray-500">{{ $takim->created_at->format('d.m.Y') }}</span>
                        </td>
                        @endif

                        <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center sm:space-x-2 space-y-2 sm:space-y-0">
                                @if($type === 'katildigim')
                                    @if($takim->lider_user_id === auth()->id())
                                        <a href="{{ route('takimlar.show', $takim) }}" class="group inline-flex justify-center items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-lg text-xs hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg">Yönet</a>
                                    @else
                                        <a href="{{ route('takimlar.show', $takim) }}" class="group inline-flex justify-center items-center px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg text-xs hover:bg-gray-600">Görüntüle</a>
                                    @endif
                                @else
                                    @if(isset($davetAlinanTakimIdleri) && $davetAlinanTakimIdleri->contains($takim->id))
                                        <a href="{{ route('takimlar.davetlerim') }}" class="px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg text-xs hover:bg-yellow-600 shadow-md">Davetiniz Var</a>
                                    @elseif(isset($istekGonderilenTakimIdleri) && $istekGonderilenTakimIdleri->contains($takim->id))
                                        <button class="px-4 py-2 bg-gray-300 text-gray-500 font-semibold rounded-lg text-xs cursor-not-allowed" disabled>İstek Gönderildi</button>
                                    @else
                                        <form action="{{ route('takimlar.katilmaIstegi', $takim) }}" method="POST"> @csrf <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg text-xs hover:from-green-600 hover:to-emerald-700 shadow-md">Katılma İsteği Gönder</button></form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="p-6 text-center text-gray-500">
            @if($type === 'katildigim') Henüz bir takıma üye değilsiniz. @else Katılabileceğiniz başka bir takım bulunmamaktadır. @endif
        </p>
    @endif
</div>