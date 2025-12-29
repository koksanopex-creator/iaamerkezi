{{-- 
    Bu bileşen, bir proje adımının (step) altındaki yorum/log akışını yönetir.
--}}
<div class="mt-6" x-data="{ open: false }"> {{-- DÜZELTME 1: Varsayılan olarak kapalı (false) --}}
    
    {{-- Başlık ve Açma/Kapatma Butonu --}}
    <div class="flex justify-between items-center cursor-pointer" @click="open = !open">
        <h4 class="text-lg font-semibold text-gray-700 flex items-center space-x-2">
            <span>Adım Geçmişi ve Yorumlar</span>
            <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded-full text-xs font-bold">{{ $yorumSayisi }}</span>
            
            {{-- Müşteri yorumu varsa KRİTİK ikonu göster --}}
            @if($musteriYorumSayisi > 0)
                <span class="flex items-center text-xs font-semibold text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded-full" title="Bu adımda {{ $musteriYorumSayisi }} müşteri yorumu var">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                    Müşteri Yorumu
                </span>
            @endif
        </h4>
        <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center">
            <span x-text="open ? 'Gizle' : 'Göster'">Göster</span> {{-- DÜZELTME 1: Varsayılan "Göster" yazar --}}
            <svg class="w-5 h-5 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
    </div>

    {{-- Açılır/Kapanır Alan --}}
    <div x-show="open" x-transition class="mt-4 pl-4 border-l-4 border-gray-200 space-y-6" style="display: none;">

        {{-- Yorum Listesi --}}
        <div class="space-y-5 max-h-96 overflow-y-auto pr-2">
            @forelse ($yorumlar as $yorum)
                <div class="flex space-x-3">
                    <div class="flex-shrink-0">
                        @if ($yorum->user)
                            {{-- AVATAR TIKLANABİLİR OLSUN --}}
                            <a href="{{ route('profile.show', $yorum->user->id) }}">
                                <div class="h-10 w-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold hover:ring-2 hover:ring-offset-2 hover:ring-indigo-500 transition-all" title="{{ $yorum->yapan_kisi_adi }}">
                                    {{ substr($yorum->yapan_kisi_adi, 0, 1) }}
                                </div>
                            </a>
                        @else
                            <div class="h-10 w-10 rounded-full bg-yellow-500 text-white flex items-center justify-center font-bold" title="{{ $yorum->yapan_kisi_adi }}">
                                M
                            </div>
                        @endif
                    </div>
                    
                    <div class="min-w-0 flex-1">
                        <div class="flex justify-between items-center">
                            <div>
                                {{-- İSİM TIKLANABİLİR OLSUN --}}
                                @if ($yorum->user_id)
                                    <a href="{{ route('profile.show', $yorum->user_id) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-600 hover:underline transition-colors">
                                        {{ $yorum->yapan_kisi_adi }}
                                    </a>
                                @else
                                    <span class="text-sm font-bold text-gray-900">
                                        {{ $yorum->yapan_kisi_adi }}
                                    </span>
                                @endif

                                <p class="text-sm text-gray-500">
                                    {{ $yorum->created_at->diffForHumans() }} ({{ $yorum->created_at->format('d.m.Y H:i') }})
                                </p>
                            </div>
                            
                    
                            
                            {{-- === DÜZELTME 2: DÜZENLEME BUTONU === --}}
                            @if (Auth::id() == $yorum->user_id || (Auth::check() && Auth::user()->hasRole('Superadmin')))
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                    </button>
                                    <div x-show="open" x-transition class="absolute right-0 z-10 w-32 bg-white shadow-lg rounded-md border py-1">
                                        <button wire:click.prevent="editComment({{ $yorum->id }})" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Düzenle</button>
                                        {{-- İleride silme de eklenebilir --}}
                                    </div>
                                </div>
                            @endif
                            {{-- === DÜZELTME 2 SONU === --}}
                        </div>

                        {{-- === DÜZELTME 3: DÜZENLEME ALANI / YORUM ALANI === --}}
                        @if ($editingCommentId == $yorum->id)
                            {{-- Yorumu Düzenleme Alanı --}}
                            <div class="mt-2">
                                <textarea wire:model="editingCommentBody" rows="3" 
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                @error('editingCommentBody') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                <div class="flex items-center justify-end space-x-2 mt-2">
                                    <button wire:click.prevent="cancelEdit" class="text-sm text-gray-600 hover:text-gray-900 px-3 py-1 rounded-md">İptal</button>
                                    <button wire:click.prevent="updateComment" class="text-sm text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1 rounded-md shadow-sm">Kaydet</button>
                                </div>
                            </div>
                        @else
                            {{-- Normal Yorum Gösterim Alanı --}}
                            <div class="mt-2 text-sm text-gray-800 prose prose-sm max-w-none">
                                {!! nl2br(e($yorum->yorum)) !!}
                            </div>
                        @endif
                        {{-- === DÜZELTME 3 SONU === --}}

                        {{-- Ekli Dosya Linki (Değişiklik yok) --}}
                        @if ($yorum->dosya_yolu)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $yorum->dosya_yolu) }}" target="_blank"
                                   class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors border border-gray-300">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span class="text-sm font-medium text-gray-800">{{ $yorum->dosya_adi ?? 'Eki Görüntüle' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Bu adım için henüz bir yorum veya kayıt eklenmemiş.</p>
            @endforelse
        </div>

        {{-- 
            YENİ YORUM FORMU 
            EĞER: Kullanıcı Yetkiliyse VEYA Müşteriyse Formu Göster 
        --}}
        @if ($kullaniciYetkiliMi || $isMusteri)
            <div class="pt-6 border-t border-gray-200">
                @if(session()->has('yorum_success'))
                    <div class="mb-4 text-sm text-green-700 bg-green-100 p-3 rounded-lg">{{ session('yorum_success') }}</div>
                @endif
                @if(session()->has('yorum_error'))
                    <div class="mb-4 text-sm text-red-700 bg-red-100 p-3 rounded-lg">{{ session('yorum_error') }}</div>
                @endif

                <form wire:submit="addYorum">
                    <div>
                        <label for="yeniYorum-{{ $step->id }}" class="block text-sm font-medium text-gray-700">Yorum Ekle</label>
                        <textarea wire:model="yeniYorum" id="yeniYorum-{{ $step->id }}" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                  placeholder="Bir yorum veya güncelleme notu yazın..."></textarea>
                        @error('yeniYorum') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-4">
                        <label for="yeniDosya-{{ $step->id }}" class="block text-sm font-medium text-gray-700">Dosya Ekle (Opsiyonel, Maks 5MB)</label>
                        <input type="file" wire:model="yeniDosya" id="yeniDosya-{{ $step->id }}" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer">
                        
                        <div wire:loading wire:target="yeniDosya" class="text-sm text-gray-500 mt-1">Yükleniyor...</div>
                        @if ($yeniDosya && !$errors->has('yeniDosya'))
                            <div class="text-sm text-green-600 mt-1">Dosya seçildi: {{ $yeniDosya->getClientOriginalName() }}</div>
                        @endif
                        @error('yeniDosya') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                            <span wire:loading.remove wire:target="addYorum">Yorumu Gönder</span>
                            <span wire:loading wire:target="addYorum">Gönderiliyor...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
        
    </div>
</div>