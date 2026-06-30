@push('pageTitle')
    Dış Avukat Düzenle | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tighter flex items-center gap-3">
            <a href="{{ route('admin.dis_avukatlar.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            {{ __('Dış Avukat Düzenle') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-slate-100">
                
                {{-- ÜST BİLGİ --}}
                <div class="bg-slate-900 p-8 text-white relative">
                    <div class="relative z-10">
                        <h3 class="text-xl font-black italic tracking-tighter mb-2 uppercase">Avukat Bilgileri Güncelleme</h3>
                        <p class="text-slate-400 text-sm font-medium">Sistem giriş yetkilerini ve iletişim detaylarını bu panelden revize edebilirsiniz.</p>
                    </div>
                    <div class="absolute right-0 top-0 bottom-0 w-32 bg-indigo-600 opacity-20 skew-x-[-20deg] translate-x-10"></div>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.dis_avukatlar.update', $lawyer->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- AD SOYAD --}}
                            <div class="space-y-1">
                                <label for="name" class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Avukat Adı Soyadı</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $lawyer->name) }}" required
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all font-bold text-slate-700">
                                @error('name') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- E-POSTA --}}
                            <div class="space-y-1">
                                <label for="email" class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">E-Posta Adresi (Giriş ID)</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $lawyer->email) }}" required
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all font-bold text-slate-700">
                                @error('email') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- TELEFON --}}
                            <div class="space-y-1">
                                <label for="telefon" class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Telefon Numarası</label>
                                <input type="text" name="telefon" id="telefon" value="{{ old('telefon', $lawyer->telefon) }}"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all font-bold text-slate-700" placeholder="05xx xxx xx xx">
                                @error('telefon') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- ŞİFRE (OPSİYONEL) --}}
                            <div class="space-y-1">
                                <label for="password" class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Yeni Şifre (Boş bırakılabilir)</label>
                                <input type="password" name="password" id="password"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all font-bold text-slate-700" placeholder="••••••••">
                                <p class="text-[10px] text-slate-400 italic mt-1 leading-tight">Şifreyi değiştirmek istemiyorsanız bu alanı boş bırakın.</p>
                                @error('password') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                            <div class="text-[11px] text-slate-400 italic flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Güncellemeler anında aktif olacaktır.
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.dis_avukatlar.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-slate-200 transition-all">İptal</a>
                                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-black uppercase text-xs tracking-widest hover:bg-slate-900 transition-all shadow-lg hover:shadow-indigo-200">
                                    Değişiklikleri Kaydet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
