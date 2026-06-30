<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('iaa.hatirlatmalarim.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-full transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Hatırlatma Detayı') }}
                    </h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Süreç Takibi & Bilgi Paylaşımı</p>
                </div>
            </div>
            <div class="flex gap-3">
                @if($hatirlatma->durum !== 'musteri_ikna_oldu' && $hatirlatma->durum !== 'kapatildi')
                    <form id="iknaOlduForm" action="{{ route('admin.sikayet-hatirlatma.iknaOldu', $hatirlatma->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-200 text-xs font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            İkna Oldum (Onayla)
                        </button>
                    </form>
                @endif
                <a href="{{ route('iaa.sikayetler.show', $hatirlatma->musteri_sikayeti_id) }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-200 text-xs font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    İlgili Şikayete Git
                </a>
            </div>
        </div>
        @push('pageTitle')
        {{ $hatirlatma->musteriSikayeti->musteri_sikayet_konusu }} | Hatırlatma Detayı | 
    @endpush
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- TEKRARLANAN HATIRLATMA UYARISI --}}
            @if($hatirlatma->hatirlatma_sayisi > 1)
                <div class="bg-gradient-to-r from-red-50 to-orange-50 border-l-8 border-red-500 p-6 rounded-3xl shadow-lg flex items-center gap-6 animate-fade-in relative overflow-hidden group">
                    <div class="absolute right-0 top-0 opacity-10 -mr-8 -mt-8 transform group-hover:scale-110 transition-transform duration-700">
                        <svg class="w-32 h-32 text-red-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.27 14.3H3.73L12 5.45zM11 16h2v2h-2v-2zm0-7h2v5h-2V9z"/></svg>
                    </div>
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 flex-shrink-0 animate-pulse border border-red-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-red-900 uppercase tracking-tighter">⚠️ Tekrarlanan Hatırlatma Talebi!</h4>
                        <p class="text-sm text-red-800 font-bold leading-relaxed">
                            Bu şikayet için <span class="bg-red-600 text-white px-2 py-0.5 rounded-lg text-base">{{ $hatirlatma->hatirlatma_sayisi }}. kez</span> hatırlatma gönderilmiştir. 
                        </p>
                        <p class="text-xs text-red-700/70 font-medium italic mt-1">Sürecin aciliyetini göz önünde bulundurarak lütfen hızlandırma aksiyonu alınız.</p>
                    </div>
                </div>
            @endif

            {{-- ÜST BİLGİ KARTI --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 bg-slate-800 text-white flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black uppercase tracking-tight">Hatırlatma Bilgileri (Sayı: {{ $hatirlatma->hatirlatma_sayisi }})</h3>
                            <p class="text-indigo-200 text-xs font-medium">Bu bölümden temsilci ile doğrudan yazışabilirsiniz.</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block text-[10px] text-white/50 font-black uppercase">Durum</span>
                        @php
                            $isPending = in_array($hatirlatma->durum, ['bilgi_girisi_bekleniyor', 'bilgi_girildi']);
                            
                            if ($hatirlatma->durum === 'bilgi_girisi_bekleniyor') {
                                $statusColor = 'text-red-400';
                                $statusText = 'Cevap Bekliyor';
                            } elseif ($hatirlatma->durum === 'bilgi_girildi') {
                                $statusColor = 'text-blue-400';
                                $statusText = 'Bilgi Girildi';
                            } else {
                                $statusColor = 'text-emerald-400';
                                $statusText = 'Tamamlandı';
                            }
                        @endphp
                        <span class="text-sm font-black {{ $statusColor }} uppercase tracking-widest">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 lg:grid-cols-4 gap-8 bg-slate-50/50">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Şikayet Konusu</label>
                            <p class="text-sm font-bold text-gray-700">{{ $hatirlatma->musteriSikayeti->musteri_sikayet_konusu }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Şikayet Kategorisi</label>
                            <p class="text-sm font-bold text-gray-700">{{ $hatirlatma->musteriSikayeti->sikayetKategori->ad ?? 'Genel' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Müşteri Temsilcisi</label>
                            <div class="flex items-center gap-3 mt-1">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-[10px] font-black">
                                    {{ substr(optional($hatirlatma->gonderen)->name ?? 'Sistem', 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-gray-700">{{ optional($hatirlatma->gonderen)->name ?? 'Sistem / Bilinmiyor' }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Talep Tarihi</label>
                            <p class="text-sm font-bold text-gray-700">{{ $hatirlatma->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Şikayeti Giren Personel</label>
                        @php $olusturan = $hatirlatma->musteriSikayeti->olusturanKurulUyesi; @endphp
                        @if($olusturan)
                            <div class="flex items-center gap-3 mt-1">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-[10px] font-black">
                                    {{ mb_substr(optional($olusturan)->name ?? '??', 0, 2) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">{{ optional($olusturan)->name ?? 'Bilinmeyen Personel' }}</span>
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">{{ optional($olusturan)->display_unvan ?? 'Personel' }}</span>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        @if($olusturan->email)<a href="mailto:{{ $olusturan->email }}" class="text-gray-400 hover:text-indigo-500" title="{{ $olusturan->email }}"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg></a>@endif
                                        @if($olusturan->phone)<a href="tel:{{ $olusturan->phone }}" class="text-gray-400 hover:text-emerald-500" title="{{ $olusturan->phone }}"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg></a>@endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic mt-2">Müşteri tarafından girildi.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Bildirim Gönderilenler</label>
                        <div class="space-y-3 max-h-32 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($hatirlatma->bildirilenler as $bildirilen)
                                <div class="flex items-center justify-between group cursor-help" title="Email: {{ optional($bildirilen->user)->email ?? 'Belirtilmemiş' }} | Tel: {{ optional($bildirilen->user)->phone ?? 'Belirtilmemiş' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                            {{ substr(optional($bildirilen->user)->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-700 group-hover:text-indigo-600 transition-colors">{{ optional($bildirilen->user)->name ?? 'Bilinmeyen' }}</p>
                                            <p class="text-[9px] font-black text-indigo-500 uppercase tracking-tighter">{{ $bildirilen->bildirim_rolu }}</p>
                                        </div>
                                    </div>
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200" title="Bildirim İletildi"></span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- YORUMLAR / TARTIŞMA ALANI --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-8">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-6">
                    <div class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Geri Bildirimler</h3>
                        <p class="text-gray-400 text-xs font-medium">Temsilcinin sorularını buradan yanıtlayabilirsiniz.</p>
                    </div>
                </div>

                <div class="space-y-10">
                    @php
                        $gruplanmisYorumlar = $hatirlatma->yorumlar->sortBy('id')->groupBy('hatirlatma_numarasi')->sortKeysDesc();
                    @endphp
                    @forelse($gruplanmisYorumlar as $numara => $yorumlar)
                        <div class="relative py-4 mb-4">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-gray-100"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white px-4 text-[10px] font-black text-indigo-500 uppercase tracking-widest border border-gray-100 rounded-full py-1.5 shadow-sm flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                    {{ $numara }}. HATIRLATMA SÜRECİ
                                </span>
                            </div>
                        </div>

                        @foreach($yorumlar as $yorum)
                            <div class="flex gap-4 {{ $yorum->user_id == auth()->id() ? 'flex-row-reverse' : '' }} mb-8">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-2xl {{ $yorum->user_id == auth()->id() ? 'bg-indigo-600' : 'bg-gray-200' }} flex items-center justify-center text-white text-xs font-black shadow-sm">
                                        {{ substr(optional($yorum->user)->name ?? '?', 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex flex-col {{ $yorum->user_id == auth()->id() ? 'items-end' : 'items-start' }} max-w-[85%] group">
                                    <div class="flex items-center gap-2 mb-1.5 px-1">
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ optional($yorum->user)->name ?? 'Bilinmeyen' }}</span>
                                        <span class="text-[10px] text-gray-300 font-medium italic">{{ $yorum->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    {{-- Ünvan Bilgisi --}}
                                    <div class="flex items-center gap-2 mb-2 px-1">
                                        <span class="text-[9px] font-black {{ $yorum->user_id == auth()->id() ? 'text-indigo-400' : 'text-gray-400' }} uppercase tracking-widest">
                                            {{ optional($yorum->user)->display_unvan ?? 'Kullanıcı' }}
                                        </span>
                                    </div>

                                    <div class="relative">
                                        <div class="p-5 rounded-2xl shadow-sm leading-relaxed text-sm {{ $yorum->user_id == auth()->id() ? 'bg-indigo-50 border border-indigo-100 text-indigo-900 rounded-tr-none' : 'bg-gray-50 border border-gray-100 text-gray-700 rounded-tl-none' }}">
                                            {!! nl2br(e($yorum->yorum)) !!}
                                        </div>

                                        {{-- Aksiyonlar --}}
                                        @if($yorum->user_id === auth()->id() && $isPending)
                                            <div class="absolute -bottom-6 right-0 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-3">
                                                <button onclick="editComment({{ $yorum->id }}, '{{ addslashes($yorum->yorum) }}')" class="text-[9px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest">DÜZENLE</button>
                                                <form action="{{ route('admin.sikayet-hatirlatma.yorumSil', $yorum->id) }}" method="POST" onsubmit="return confirm('Bu yorumu silmek istediğinize emin misiniz?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[9px] font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest">SİL</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="text-center py-10 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Henüz bir yazışma bulunmuyor.</p>
                        </div>
                    @endforelse
                </div>

                {{-- YORUM YAZMA ALANI --}}
                @if($isPending)
                    <div class="mt-10 pt-8 border-t border-gray-100">
                        <form id="commentForm" action="{{ route('admin.sikayet-hatirlatma.yorum', $hatirlatma) }}" method="POST">
                            @csrf
                            <div id="methodContainer"></div>
                            <div class="relative group">
                                <textarea name="yorum" id="commentTextarea" rows="4" placeholder="Cevabınızı buraya yazın..." class="block w-full rounded-3xl border-gray-100 bg-gray-50/50 shadow-inner focus:border-indigo-500 focus:ring-indigo-500 text-sm p-6 group-hover:bg-white transition-all resize-none" required></textarea>
                                <div class="absolute bottom-4 right-4 flex items-center gap-3">
                                    <button type="button" id="cancelEditBtn" onclick="cancelEdit()" class="hidden px-5 py-2.5 bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 transition-all">Vazgeç</button>
                                    <button type="submit" id="submitBtn" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all hover:scale-105 active:scale-95 gap-2">
                                        <span id="btnText">CEVABI GÖNDER</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-4 text-[10px] text-gray-400 font-medium italic text-center">
                                * Cevap verdiğinizde müşteri temsilcisine otomatik bildirim gönderilecektir.
                            </p>
                        </form>
                    </div>
                @else
                    <div class="mt-10 p-6 bg-emerald-50 rounded-3xl border border-emerald-100 text-center">
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-widest flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Bu süreç başarıyla tamamlanmıştır.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function editComment(id, text) {
            const form = document.getElementById('commentForm');
            const textarea = document.getElementById('commentTextarea');
            const methodContainer = document.getElementById('methodContainer');
            const btnText = document.getElementById('btnText');
            const cancelBtn = document.getElementById('cancelEditBtn');

            form.action = `/admin/musteri-hatirlatmalari/yorum/${id}`;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            textarea.value = text;
            textarea.focus();
            btnText.innerText = 'GÜNCELLEMEYİ KAYDET';
            cancelBtn.classList.remove('hidden');
            textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function cancelEdit() {
            const form = document.getElementById('commentForm');
            const textarea = document.getElementById('commentTextarea');
            const methodContainer = document.getElementById('methodContainer');
            const btnText = document.getElementById('btnText');
            const cancelBtn = document.getElementById('cancelEditBtn');

            form.action = "{{ route('admin.sikayet-hatirlatma.yorum', $hatirlatma->id) }}";
            methodContainer.innerHTML = '';
            textarea.value = '';
            btnText.innerText = 'CEVABI GÖNDER';
            cancelBtn.classList.add('hidden');
        }
    </script>
    @endpush

</x-app-layout>
