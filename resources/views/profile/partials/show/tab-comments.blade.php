<div x-show="activeTab === 'yorumlar'" class="space-y-6">
    
    {{-- Yorum Formu --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h4 class="text-sm font-bold text-gray-700 mb-3">Bu profil hakkında geri bildirim yaz</h4>
        <form action="{{ route('profile.comment.store', $user->id) }}" method="POST">
            @csrf
            <textarea name="yorum" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition text-sm" placeholder="Tebrik mesajı veya not bırakın..."></textarea>
            <div class="mt-2 text-right">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wide transition-colors">Gönder</button>
            </div>
        </form>
    </div>

    {{-- Yorum Listesi --}}
    <div class="space-y-6 max-h-[600px] overflow-y-auto custom-scrollbar pr-2" id="yorumlar">
        @forelse($yorumlar as $yorum)
            <div x-data="{ showReplyForm: false }" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 group">
                <div class="flex space-x-4">
                    <div class="flex-shrink-0">
                        <a href="{{ route('profile.show', $yorum->yazan_user_id) }}" target="_blank">
                            @if($yorum->yazan->profile_photo_path)
                                <img class="h-10 w-10 rounded-full object-cover border hover:opacity-80 transition-opacity" src="{{ asset('storage/' . $yorum->yazan->profile_photo_path) }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-600 hover:bg-gray-300 transition-colors">{{ substr($yorum->yazan->name, 0, 1) }}</div>
                            @endif
                        </a>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h5 class="text-sm font-bold text-gray-900">
                                <a href="{{ route('profile.show', $yorum->yazan_user_id) }}" target="_blank" class="hover:text-indigo-600 hover:underline transition-colors">
                                    {{ $yorum->yazan->name }}
                                </a>
                            </h5>
                            
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400">{{ $yorum->created_at->diffForHumans() }}</span>
                                @php
                                    $canDelete = (auth()->id() == $yorum->yazan_user_id) || 
                                                 (auth()->id() == $yorum->user_id && !$yorum->yazan->hasRole('Superadmin')) || 
                                                 auth()->user()->hasRole('Superadmin');
                                @endphp
                                @if($canDelete)
                                    <form action="{{ route('profile.comment.destroy', $yorum->id) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $yorum->yorum }}</p>
                        
                        <button @click="showReplyForm = !showReplyForm" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium mt-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Cevapla
                        </button>

                        <div x-show="showReplyForm" class="mt-3 pl-4 border-l-2 border-indigo-100" style="display: none;">
                            <form action="{{ route('profile.comment.store', $user->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $yorum->id }}">
                                <textarea name="yorum" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm text-xs focus:border-indigo-500 focus:ring focus:ring-indigo-200" placeholder="Cevabınızı yazın..."></textarea>
                                <div class="mt-2 text-right">
                                    <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-colors">Cevapla</button>
                                </div>
                            </form>
                        </div>

                        @if($yorum->cevaplar->count() > 0)
                            <div class="mt-4 space-y-4 pl-4 border-l-2 border-gray-100">
                                @foreach($yorum->cevaplar as $cevap)
                                    <div class="flex space-x-3 group/reply">
                                        <div class="flex-shrink-0">
                                            <a href="{{ route('profile.show', $cevap->yazan_user_id) }}" target="_blank">
                                                @if($cevap->yazan->profile_photo_path)
                                                    <img class="h-8 w-8 rounded-full object-cover border hover:opacity-80" src="{{ asset('storage/' . $cevap->yazan->profile_photo_path) }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center font-bold text-xs text-gray-500 hover:bg-gray-200">{{ substr($cevap->yazan->name, 0, 1) }}</div>
                                                @endif
                                            </a>
                                        </div>
                                        <div class="flex-1 bg-gray-50 p-3 rounded-lg relative">
                                            <div class="flex items-center justify-between">
                                                <h6 class="text-xs font-bold text-gray-800">
                                                    <a href="{{ route('profile.show', $cevap->yazan_user_id) }}" target="_blank" class="hover:text-indigo-600 hover:underline">{{ $cevap->yazan->name }}</a>
                                                </h6>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] text-gray-400">{{ $cevap->created_at->diffForHumans() }}</span>
                                                    @php
                                                        $canDeleteReply = (auth()->id() == $cevap->yazan_user_id) || 
                                                                        (auth()->id() == $cevap->user_id && !$cevap->yazan->hasRole('Superadmin')) || 
                                                                        auth()->user()->hasRole('Superadmin');
                                                    @endphp
                                                    @if($canDeleteReply)
                                                        <form action="{{ route('profile.comment.destroy', $cevap->id) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');" class="inline opacity-0 group-hover/reply:opacity-100 transition-opacity">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-1">{{ $cevap->yorum }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 bg-white rounded-xl border border-dashed border-gray-300">
                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <p class="mt-2 text-sm text-gray-500">Henüz yorum yapılmamış. İlk yorumu sen yap!</p>
            </div>
        @endforelse
    </div>
</div>