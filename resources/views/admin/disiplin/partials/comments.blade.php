<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
            Dosya Tartışma & Notlar
        </h3>
        <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $case->comments->count() }} Mesaj</span>
    </div>

    <div class="p-6">
        {{-- YORUM LİSTESİ --}}
        <div class="space-y-6 mb-8 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
        @forelse($case->comments as $comment)
                {{-- SİLİNMİŞ YORUM KONTROLÜ --}}
                @php
                    $isDeleted = $comment->trashed(); // Silinmiş mi?
                    // Silinmişse soluk ve kırmızımsı, değilse normal
                    $containerOpacity = $isDeleted ? 'opacity-60 grayscale' : ''; 
                    $messageBg = $isDeleted 
                        ? 'bg-red-50 border border-red-200 text-gray-500' // Silinmiş Stil
                        : ($comment->user_id == Auth::id() ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800'); // Normal Stil
                @endphp

                <div x-data="{ editing: false }" class="flex gap-4 {{ $comment->user_id == Auth::id() ? 'flex-row-reverse' : '' }} {{ $containerOpacity }}">
                    
                    {{-- Avatar --}}
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm shadow-sm">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                    </div>

                    {{-- Mesaj Kutusu --}}
                    <div class="flex flex-col max-w-[85%] {{ $comment->user_id == Auth::id() ? 'items-end' : 'items-start' }}">
                        
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-gray-700">{{ $comment->user->name }}</span>
                            <span class="text-[10px] text-gray-400">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                            
                            {{-- Düzenlendi Uyarısı --}}
                            @if($comment->histories->count() > 0)
                                <span class="text-[9px] text-orange-500 italic flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    (Düzenlendi)
                                </span>
                            @endif

                            {{-- SİLİNDİ UYARISI (Sadece Admin Görür) --}}
                            @if($isDeleted)
                                <span class="bg-red-100 text-red-600 text-[9px] px-2 py-0.5 rounded-full font-bold border border-red-200">
                                    🗑️ SİLİNDİ
                                </span>
                            @endif
                        </div>

                        {{-- MESAJ İÇERİĞİ --}}
                        <div x-show="!editing" class="relative px-4 py-3 rounded-2xl text-sm shadow-sm {{ $messageBg }} {{ $comment->user_id == Auth::id() ? 'rounded-tr-none' : 'rounded-tl-none' }}">
                            <p class="whitespace-pre-line">
                                @if($isDeleted)
                                    <span class="italic font-bold">[Bu yorum silinmiştir]</span><br>
                                @endif
                                {{ $comment->yorum }}
                            </p>

                            {{-- Dosyalar --}}
                            @if(!empty($comment->dosyalar))
                                <div class="mt-3 pt-2 border-t border-black/10 flex flex-col gap-1">
                                    @foreach($comment->dosyalar as $dosya)
                                        <a href="{{ asset('storage/'.$dosya) }}" target="_blank" class="flex items-center gap-2 text-xs hover:underline">
                                            📎 {{ basename($dosya) }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- DÜZENLEME FORMU (Silinmişse Açılmaz) --}}
                        @if(!$isDeleted)
                            <div x-show="editing" x-cloak class="w-full min-w-[300px] bg-white border border-gray-200 rounded-xl p-3 shadow-lg z-10">
                                <form action="{{ route('admin.disiplin.comment.update', $comment->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <textarea name="yorum" rows="3" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-gray-800">{{ $comment->yorum }}</textarea>
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button type="button" @click="editing = false" class="text-xs text-gray-500 hover:text-gray-700">İptal</button>
                                        <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded text-xs font-bold">Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        {{-- AKSİYON BUTONLARI (Silinmişse Gösterme) --}}
                        @if(!$isDeleted)
                            <div class="flex gap-3 mt-1">
                                @if(Auth::id() == $comment->user_id)
                                    <button type="button" @click="editing = true" class="text-[10px] text-gray-400 hover:text-indigo-600">Düzenle</button>
                                @endif

                                @if(Auth::id() == $comment->user_id || Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']))
                                    <form action="{{ route('admin.disiplin.comment.destroy', $comment->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Silmek istediğinize emin misiniz?')" class="text-[10px] text-gray-400 hover:text-red-600">Sil</button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        {{-- LOGLAR (Sadece Superadmin ve Hukuk Admini Görür) --}}
                        @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']) && $comment->histories->count() > 0)
                            <div x-data="{ showHistory: false }" class="mt-1 w-full text-right">
                                <button @click="showHistory = !showHistory" class="text-[9px] text-indigo-400 hover:text-indigo-600 underline">
                                    Düzenleme Geçmişi ({{ $comment->histories->count() }})
                                </button>
                                <div x-show="showHistory" class="mt-2 bg-yellow-50 border border-yellow-200 p-2 rounded text-[10px] text-gray-700 space-y-2 text-left w-full">
                                    @foreach($comment->histories as $history)
                                        <div class="border-b border-yellow-200 pb-1 last:border-0">
                                            <strong class="block text-yellow-800">{{ $history->created_at->format('d.m.Y H:i') }} - {{ $history->user->name ?? 'Bilinmiyor' }}</strong>
                                            <p class="italic text-gray-600">"{{ $history->eski_yorum }}"</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="bg-gray-50 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <p class="text-gray-500 text-sm">Henüz bir not veya yorum eklenmemiş.</p>
                </div>
            @endforelse
        </div>

        {{-- YORUM EKLEME FORMU --}}
        {{-- YORUM EKLEME FORMU (Alpine.js ile Dosya Önizlemeli) --}}
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200" x-data="{ files: [] }">
            <form action="{{ route('admin.disiplin.comment.store', $case->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <textarea name="yorum" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 transition resize-none" placeholder="Bir not ekleyin veya tartışmaya katılın..." required></textarea>
                </div>
                
                {{-- SEÇİLEN DOSYALARIN LİSTESİ (YENİ EKLENDİ) --}}
                <div class="mb-3" x-show="files.length > 0" x-cloak>
                    <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Seçilen Dosyalar:</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="file in files" :key="file.name">
                            <div class="flex items-center gap-1 bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-xs font-semibold border border-indigo-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span x-text="file.name"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    {{-- Dosya Yükleme Butonu --}}
                    <label class="cursor-pointer group flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-white border border-transparent hover:border-gray-200 transition text-gray-500 hover:text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <span class="text-xs font-bold">Dosya Ekle</span>
                        
                        {{-- Input Event Listener Eklendi --}}
                        <input type="file" name="dosyalar[]" multiple class="hidden" 
                               @change="files = Array.from($event.target.files)">
                    </label>

                    <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg text-xs font-bold hover:bg-gray-800 transition shadow-lg flex items-center gap-2">
                        <span>Gönder</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 mt-2 pl-1">* Birden fazla dosya seçebilirsiniz. (Max: 10MB)</p>
            </form>
        </div>
    </div>
</div>