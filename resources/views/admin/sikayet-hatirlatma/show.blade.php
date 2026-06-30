@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Müşteri Hatırlatma Detayı') }}
    </h2>
@endsection

@push('pageTitle')
    Müşteri Hatırlatma Detayı | 
@endpush

@section('content')
<div class="py-6 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Üst Başlık ve Navigasyon --}}
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.sikayet-hatirlatma.index') }}" class="p-2 bg-white rounded-full shadow-sm hover:bg-slate-100 transition-all text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Müşteri Hatırlatma Detayı</h1>
                    <p class="text-sm text-slate-500 font-medium">Şikayet: {{ $hatirlatma->musteriSikayeti->musteri_sikayet_konusu }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @if($hatirlatma->durum !== 'musteri_ikna_oldu')
                    @include('admin.sikayet-hatirlatma.partials._hatirlatma-butonu', ['sikayet' => $hatirlatma->musteriSikayeti])
                @endif
                
                @if($hatirlatma->durum !== 'musteri_ikna_oldu' && (auth()->id() === $hatirlatma->gonderen_user_id || auth()->user()->hasRole('Superadmin')))
                    <form action="{{ route('admin.sikayet-hatirlatma.iknaOldu', $hatirlatma->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all hover:-translate-y-0.5 active:translate-y-0">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Müşteri İkna Oldu (Onayla)
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- TEKRARLANAN HATIRLATMA UYARISI (ADMIN) --}}
        @if($hatirlatma->hatirlatma_sayisi > 1)
            <div class="bg-gradient-to-r from-rose-500 to-red-600 rounded-3xl p-6 mb-8 text-white shadow-xl shadow-red-200 relative overflow-hidden group">
                <div class="absolute right-0 top-0 opacity-20 -mr-10 -mt-10 transform group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-48 h-48 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.27 14.3H3.73L12 5.45zM11 16h2v2h-2v-2zm0-7h2v5h-2V9z"/></svg>
                </div>
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 animate-pulse">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black uppercase tracking-tighter leading-none mb-2">TEKRARLANAN MÜŞTERİ HATIRLATMASI!</h2>
                        <p class="text-rose-50 font-bold text-lg leading-tight mb-1">
                            Bu şikayet için <span class="bg-white text-red-600 px-3 py-1 rounded-xl shadow-sm">{{ $hatirlatma->hatirlatma_sayisi }}. kez</span> hatırlatma gönderildi.
                        </p>
                        <p class="text-rose-100 text-sm font-medium opacity-80 italic">Müşteri temsilcisi tarafından süreç takibi için birden fazla talep oluşturulmuştur. Acil aksiyon gereklidir.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Sol Taraf: Bilgi Kartları --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Durum Kartı --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-slate-50 rounded-full -mr-12 -mt-12 opacity-50"></div>
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Mevcut Durum (Sayı: {{ $hatirlatma->hatirlatma_sayisi }})</h3>
                    @php
                        $durumMapping = [
                            'bilgi_girisi_bekleniyor' => ['label' => 'Bilgi Bekleniyor', 'color' => 'bg-amber-100 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'],
                            'bilgi_girildi' => ['label' => 'Bilgi Girildi', 'color' => 'bg-blue-100 text-blue-700 border-blue-200', 'dot' => 'bg-blue-500'],
                            'musteri_ikna_oldu' => ['label' => 'Müşteri İkna Oldu', 'color' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                            'kapatildi' => ['label' => 'Kapatıldı', 'color' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-500'],
                        ];
                        $curr = $durumMapping[$hatirlatma->durum];
                    @endphp
                    <div class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-black {{ $curr['color'] }} border shadow-sm">
                        <span class="w-2 h-2 rounded-full {{ $curr['dot'] }} mr-2 animate-pulse"></span>
                        {{ $curr['label'] }}
                    </div>
                </div>

                {{-- Şikayeti Giren Personel Kartı --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Şikayeti Giren Personel</h3>
                    @php $olusturan = $hatirlatma->musteriSikayeti->olusturanKurulUyesi; @endphp
                    @if($olusturan)
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($olusturan->profile_photo_url)
                                    <img src="{{ $olusturan->profile_photo_url }}" class="w-14 h-14 rounded-2xl object-cover shadow-md border-2 border-white ring-1 ring-slate-100" alt="{{ $olusturan->name }}">
                                @else
                                    <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-lg">
                                        {{ substr($olusturan->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <a href="{{ url('/kullanici-profil/' . $olusturan->id) }}" class="text-slate-800 font-bold hover:text-indigo-600 transition-all">{{ $olusturan->name }}</a>
                                <p class="text-xs text-slate-500">{{ $olusturan->display_unvan }}</p>
                                <div class="mt-1 flex space-x-2">
                                    <a href="mailto:{{ $olusturan->email }}" class="text-slate-400 hover:text-rose-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg></a>
                                    <a href="tel:{{ $olusturan->phone }}" class="text-slate-400 hover:text-indigo-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg></a>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-slate-400 italic">Müşteri tarafından girildi.</p>
                    @endif
                </div>

                {{-- Bildirim Gönderilen Kişiler --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Bildirim Gönderilenler</h3>
                    <div class="space-y-4">
                        @foreach($hatirlatma->bildirilenler as $bildirilen)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs border border-slate-200">
                                        {{ $bildirilen->user ? substr($bildirilen->user->name, 0, 1) : '?' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-700">{{ $bildirilen->user->name ?? 'Bilinmeyen Kullanıcı' }}</p>
                                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-tighter">{{ $bildirilen->bildirim_rolu }}</p>
                                    </div>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200" title="Bildirim İletildi"></span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Sağ Taraf: Tartışma ve Detay --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Şikayet Detay Özeti --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 border-l-4 border-l-indigo-600">
                    <h2 class="text-xl font-black text-slate-800 mb-4">{{ $hatirlatma->musteriSikayeti->musteri_sikayet_konusu }}</h2>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed italic bg-slate-50 p-4 rounded-xl border border-slate-100 shadow-inner">
                        {!! nl2br(e($hatirlatma->musteriSikayeti->musteri_sikayet_detayi)) !!}
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs font-bold text-slate-400 uppercase">
                        <div class="flex items-center space-x-2">
                            <span class="bg-slate-100 px-2 py-1 rounded">Firma: {{ $hatirlatma->musteriSikayeti->customer->name ?? 'Genel' }}</span>
                            <span class="bg-slate-100 px-2 py-1 rounded">Tarih: {{ $hatirlatma->musteriSikayeti->created_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.sikayetler.show', $hatirlatma->musteriSikayeti->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-rose-200 text-rose-600 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-rose-50 hover:border-rose-300 transition-all shadow-sm group">
                                <svg class="w-4 h-4 mr-2 text-rose-400 group-hover:text-rose-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Şikayete Git
                            </a>
                            <a href="{{ url('/proje-calisma-alani/' . $hatirlatma->musteriSikayeti->iaa_id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 hover:-translate-y-0.5 transition-all shadow-md shadow-indigo-200 active:translate-y-0">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Çalışma Alanı
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Tartışma / Ticket Alanı --}}
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden flex flex-col min-h-[500px]">
                    <div class="px-8 py-5 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Süreç Tartışma Alanı (Ticket)</h3>
                        <span class="text-[10px] font-black bg-white px-3 py-1.5 rounded-full border border-slate-200 text-slate-500 uppercase tracking-widest">{{ $hatirlatma->yorumlar->count() }} MESAJ</span>
                    </div>

                    {{-- Yorum Listesi --}}
                    <div class="flex-grow p-8 space-y-8 max-h-[700px] overflow-y-auto bg-slate-50/30">
                        @php
                            $gruplanmisYorumlar = $hatirlatma->yorumlar->sortBy('id')->groupBy('hatirlatma_numarasi')->sortKeysDesc();
                        @endphp
                        @forelse($gruplanmisYorumlar as $numara => $yorumlar)
                            <div class="relative py-4 mb-4">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t border-slate-200/60"></div>
                                </div>
                                <div class="relative flex justify-center">
                                    <span class="bg-white px-4 text-[10px] font-black text-indigo-500 uppercase tracking-widest border border-slate-200 rounded-full py-1.5 shadow-sm flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                        {{ $numara }}. HATIRLATMA SÜRECİ
                                    </span>
                                </div>
                            </div>

                            @foreach($yorumlar as $yorum)
                                <div class="flex {{ $yorum->user_id === auth()->id() ? 'justify-end' : 'justify-start' }} mb-8">
                                    <div class="flex max-w-[85%] {{ $yorum->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : 'flex-row' }} space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center font-black text-slate-400 shadow-sm border border-slate-200 text-lg">
                                                {{ substr($yorum->user->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="{{ $yorum->user_id === auth()->id() ? 'items-end text-right' : 'items-start' }} flex flex-col group">
                                            <div class="flex items-center space-x-2 mb-1.5 px-1">
                                                <span class="text-xs font-black text-slate-800 uppercase tracking-tighter">{{ $yorum->user->name }}</span>
                                                <span class="text-[10px] text-slate-400 font-bold italic">{{ $yorum->created_at->diffForHumans() }}</span>
                                            </div>
                                            
                                            {{-- Ünvan ve Bölüm Bilgisi --}}
                                            <div class="flex items-center gap-2 mb-2 px-1">
                                                <span class="text-[9px] font-black text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 uppercase tracking-widest">
                                                    {{ $yorum->user->display_unvan }}
                                                </span>
                                                @if($yorum->user->bolum)
                                                    <span class="text-[9px] font-black text-slate-400 bg-white px-2 py-0.5 rounded-md border border-slate-200 uppercase tracking-widest">
                                                        {{ $yorum->user->bolum->ad }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="relative group">
                                                <div class="p-5 rounded-3xl text-sm leading-relaxed shadow-sm transition-all {{ $yorum->user_id === auth()->id() ? 'bg-indigo-600 text-white rounded-tr-none shadow-indigo-100' : 'bg-white text-slate-700 border border-slate-100 rounded-tl-none shadow-slate-100' }}">
                                                    {!! nl2br(e($yorum->yorum)) !!}
                                                </div>

                                                {{-- Düzenle / Sil Aksiyonları --}}
                                                <div class="absolute -bottom-6 {{ $yorum->user_id === auth()->id() ? 'right-0' : 'left-0' }} opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-3">
                                                    @if($yorum->user_id === auth()->id())
                                                        <button onclick="editComment({{ $yorum->id }}, '{{ addslashes($yorum->yorum) }}')" class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                            DÜZENLE
                                                        </button>
                                                    @endif
                                                    @if($yorum->user_id === auth()->id() || auth()->user()->hasRole('Superadmin'))
                                                        <form action="{{ route('admin.sikayet-hatirlatma.yorumSil', $yorum->id) }}" method="POST" onsubmit="return confirm('Bu yorumu silmek istediğinize emin misiniz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-[10px] font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                SİL
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="flex flex-col items-center justify-center h-full opacity-30 grayscale py-20">
                                <svg class="w-20 h-20 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <p class="mt-4 font-black uppercase text-xs tracking-widest">Henüz bir yanıt girilmedi</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Yorum Yazma Formu --}}
                    @if($hatirlatma->durum !== 'musteri_ikna_oldu')
                        <div class="p-8 bg-white border-t border-slate-200">
                            <form id="commentForm" action="{{ route('admin.sikayet-hatirlatma.yorum', $hatirlatma->id) }}" method="POST">
                                @csrf
                                <div id="methodContainer"></div>
                                <div class="relative group">
                                    <textarea name="yorum" id="commentTextarea" rows="4" required
                                              class="block w-full rounded-3xl border-slate-100 bg-slate-50 shadow-inner focus:border-indigo-500 focus:ring-indigo-500 text-sm p-6 group-hover:bg-white transition-all resize-none placeholder:text-slate-300" 
                                              placeholder="Açıklamanızı buraya yazınız..."></textarea>
                                    <div class="absolute bottom-4 right-4 flex items-center gap-3">
                                        <button type="button" id="cancelEditBtn" onclick="cancelEdit()" class="hidden px-5 py-2.5 bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-200 transition-all">Vazgeç</button>
                                        <button type="submit" id="submitBtn" class="inline-flex items-center px-8 py-3.5 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all hover:scale-105 active:scale-95 gap-2">
                                            <span id="btnText">Gönder</span>
                                            <svg id="btnIcon" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <p class="mt-4 text-[10px] text-slate-400 font-medium text-center italic tracking-tight">Yorumunuz ilgili tüm birimlere ve müşteri temsilcisine bildirilecektir.</p>
                        </div>
                    @else
                        <div class="p-8 bg-emerald-50 text-emerald-800 text-center font-black text-xs uppercase tracking-widest border-t border-emerald-100">
                            ✅ Bu süreç "Müşteri İkna Oldu" olarak tamamlanmıştır.
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function editComment(id, text) {
        const form = document.getElementById('commentForm');
        const textarea = document.getElementById('commentTextarea');
        const methodContainer = document.getElementById('methodContainer');
        const btnText = document.getElementById('btnText');
        const cancelBtn = document.getElementById('cancelEditBtn');

        // Formu güncelleme moduna al
        form.action = `/admin/musteri-hatirlatmalari/yorum/${id}`;
        methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        textarea.value = text;
        textarea.focus();
        btnText.innerText = 'GÜNCELLE';
        cancelBtn.classList.remove('hidden');
        
        // Scroll to textarea
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function cancelEdit() {
        const form = document.getElementById('commentForm');
        const textarea = document.getElementById('commentTextarea');
        const methodContainer = document.getElementById('methodContainer');
        const btnText = document.getElementById('btnText');
        const cancelBtn = document.getElementById('cancelEditBtn');

        // Formu varsayılan haline döndür
        form.action = "{{ route('admin.sikayet-hatirlatma.yorum', $hatirlatma->id) }}";
        methodContainer.innerHTML = '';
        textarea.value = '';
        btnText.innerText = 'GÖNDER';
        cancelBtn.classList.add('hidden');
    }
</script>
<style>
    /* Custom scrollbar for chat area */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f8fafc; }
    ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; border: 2px solid #f8fafc; }
    ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endpush

@push('styles')
<style>
    /* Custom scrollbar for chat area */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endpush
