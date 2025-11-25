<div class="bg-white/60 backdrop-blur-sm overflow-hidden">
    @if($takimlar->isNotEmpty())
        <table class="block sm:table min-w-full">
            <thead class="hidden sm:table-header-group">
                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                    <th scope="col" class="px-6 py-4">Takım Adı</th>
                    <th scope="col" class="px-6 py-4">Lider</th> {{-- YENİ SÜTUN --}}
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
                        
                        {{-- Takım Adı --}}
                        <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle">
                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span>
                            <div class="text-right sm:text-left flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r {{ $type === 'katildigim' ? 'from-blue-400 to-indigo-500' : 'from-gray-400 to-gray-500' }} flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                        {{ Str::substr($takim->ad, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $takim->ad }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Lider (YENİLENEN KISIM) --}}
                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Lider:</span>
                            <div class="text-right sm:text-left">
                                @if($takim->lider)
                                    <a href="{{ route('profile.show', $takim->lider->id) }}" target="_blank" class="inline-flex items-center gap-2 group">
                                        {{-- Avatar --}}
                                        @if($takim->lider->profile_photo_path)
                                            <img class="h-9 w-9 rounded-full object-cover border border-gray-200 group-hover:border-indigo-500 transition-colors shadow-sm" src="{{ asset('storage/' . $takim->lider->profile_photo_path) }}" alt="{{ $takim->lider->name }}">
                                        @else
                                            <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-xs text-indigo-700 font-bold group-hover:bg-indigo-200 transition-colors">
                                                {{ substr($takim->lider->name, 0, 1) }}
                                            </div>
                                        @endif
                                        
                                        {{-- İsim ve Bölüm --}}
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                                {{ $takim->lider->name }}
                                            </span>
                                            <span class="text-[10px] text-gray-500">
                                                {{ $takim->lider->bolum->ad ?? 'Bölüm Yok' }}
                                            </span>
                                        </div>
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400 italic">Lider Atanmamış</span>
                                @endif
                            </div>
                        </td>
                        {{-- =========================== --}}

                        {{-- Üye Sayısı --}}
                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Üyeler:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $takim->uyeler_count ?? $takim->uyeler->count() }} Üye
                            </span>
                        </td>

                        @if($type === 'katildigim')
                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Oluşturulma:</span>
                                <span class="text-right sm:text-left text-sm text-gray-600 font-mono">{{ $takim->created_at->format('d.m.Y') }}</span>
                            </td>
                        @endif

                        <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle text-right">
                            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center sm:space-x-2 space-y-2 sm:space-y-0">
                                @if($type === 'katildigim')
                                    @if($takim->lider_user_id === auth()->id())
                                        <a href="{{ route('takimlar.show', $takim) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                            Yönet
                                        </a>
                                    @else
                                        <a href="{{ route('takimlar.show', $takim) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                            Görüntüle
                                        </a>
                                    @endif
                                @else
                                    @if(isset($davetAlinanTakimIdleri) && $davetAlinanTakimIdleri->contains($takim->id))
                                        <a href="{{ route('takimlar.davetlerim') }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                                            Davetiniz Var
                                        </a>
                                    @elseif(isset($istekGonderilenTakimIdleri) && $istekGonderilenTakimIdleri->contains($takim->id))
                                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-200 text-xs font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                                            İstek Gönderildi
                                        </span>
                                    @else
                                        <form action="{{ route('takimlar.katilmaIstegi', $takim) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                                Katılma İsteği
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="p-12 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <p class="text-gray-500 font-medium">
                @if($type === 'katildigim') Henüz bir takıma üye değilsiniz. @else Katılabileceğiniz başka bir takım bulunmamaktadır. @endif
            </p>
        </div>
    @endif
</div>