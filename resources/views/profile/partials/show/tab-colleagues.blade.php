<div x-show="activeTab === 'DigerCalisanlar'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-900">Şirketteki Diğer Çalışanlar</h3>
        <p class="text-sm text-gray-500">Bu bölümde, {{ $user->customer->name ?? 'ilgili firma' }} firmasındaki diğer yetkilileri görebilirsiniz.</p>
    </div>

    @if($colleagues->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 border border-gray-100">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h4 class="text-lg font-bold text-gray-900">Diğer Çalışan Bulunamadı</h4>
            <p class="text-gray-500 max-w-xs mx-auto mt-1">Bu firmada kayıtlı başka bir yetkili bulunmamaktadır.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($colleagues as $colleague)
                <a href="{{ route('profile.show', $colleague->id) }}" class="group">
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-xl hover:border-indigo-100 transition-all duration-300">
                        <div class="flex items-center gap-4">
                            <div class="relative flex-shrink-0">
                                @if($colleague->profile_photo_path)
                                    <img src="{{ asset('storage/' . $colleague->profile_photo_path) }}" 
                                         alt="{{ $colleague->name }}" 
                                         class="w-16 h-16 rounded-2xl object-cover border-2 border-gray-50 group-hover:border-indigo-100 transition-all">
                                @else
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-xl font-bold text-gray-400 border-2 border-gray-50 group-hover:from-indigo-50 group-hover:to-indigo-100 group-hover:text-indigo-600 transition-all uppercase">
                                        {{ substr($colleague->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="absolute -bottom-1 -right-1 bg-emerald-500 w-4 h-4 rounded-full border-2 border-white shadow-sm"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-bold text-gray-900 group-hover:text-indigo-600 transition-colors truncate">
                                    {{ $colleague->name }}
                                </h4>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $colleague->unvan ?? 'Yetkili' }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        Görüntüle &rarr;
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
