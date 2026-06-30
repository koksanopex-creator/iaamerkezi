{{-- Bekleyen Proje Davetleri Bölümü --}}
<div class="max-w-7xl mx-auto mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 overflow-hidden">
        <div class="p-6 border-b border-indigo-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Bekleyen Proje Davetleri</h3>
                    <p class="text-xs text-gray-500">Aşağıdaki projelere katılmanız bekleniyor</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-black rounded-full">
                {{ $bekleyenProjeDavetleri->count() }}
            </span>
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($bekleyenProjeDavetleri as $proje)
                <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            {{ substr($proje->baslik ?? 'P', 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('proje.workspace.show', $proje->id) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-600 transition truncate block">
                                {{ $proje->baslik }}
                            </a>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if($proje->atananTakim)
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">
                                        Takım: {{ $proje->atananTakim->ad }}
                                    </span>
                                    @if($proje->atananTakim->lider)
                                        <span class="text-[10px] text-gray-300">•</span>
                                        <span class="text-[10px] text-gray-400">
                                            Lider: {{ $proje->atananTakim->lider->name }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('iaa.davetYanitla', $proje->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="yanit" value="kabul">
                            <button type="submit" 
                                class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Kabul Et
                            </button>
                        </form>

                        <form action="{{ route('iaa.davetYanitla', $proje->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="yanit" value="red">
                            <button type="submit" 
                                class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-rose-50 text-rose-700 rounded-lg text-xs font-bold border border-rose-100 hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reddet
                            </button>
                        </form>

                        <a href="{{ route('proje.workspace.show', $proje->id) }}" 
                           class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            İncele
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
