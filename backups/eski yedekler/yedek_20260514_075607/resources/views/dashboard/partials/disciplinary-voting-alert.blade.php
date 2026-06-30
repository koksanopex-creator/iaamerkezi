@php
    $isAuthorizedToSeeVotingBanner = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) 
                        || (Auth::user()->hasRole('Hukuk Yöneticisi') && Auth::user()->can('disiplin.kurul.portal.gor'));
@endphp

@if($isAuthorizedToSeeVotingBanner && isset($activeVotingCases) && $activeVotingCases->isNotEmpty())
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6 space-y-4">
        @foreach($activeVotingCases as $votingCase)
            <div class="relative overflow-hidden bg-white border-2 border-indigo-500 rounded-2xl p-4 shadow-xl animate-in fade-in slide-in-from-top-4 duration-500">
                <div class="absolute inset-0 bg-indigo-50/30 animate-pulse"></div>
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <h4 class="text-indigo-900 font-black text-base">DİSİPLİN OYLAMASI DEVAM EDİYOR</h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 animate-bounce">AKTİF</span>
                            </div>
                            <p class="text-indigo-700/80 text-xs font-semibold uppercase tracking-wider">
                                Dosya #{{ $votingCase->id }} — {{ $votingCase->user->name ?? 'Bilinmeyen Personel' }}
                            </p>
                            <p class="text-slate-500 text-[11px] mt-0.5">
                                {{ Str::limit($votingCase->behavior->tanim ?? 'İçerik belirtilmedi.', 80) }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.disiplin.show', $votingCase->id) }}?tab=kurul" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg shadow-indigo-200 flex items-center justify-center gap-2 group whitespace-nowrap">
                        Oylama Odasına Git
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
