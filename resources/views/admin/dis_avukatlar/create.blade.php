@push('pageTitle')
    Yeni Dış Avukat Tanımla | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tighter flex items-center gap-3">
            <a href="{{ route('admin.dis_avukatlar.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            {{ __('Yeni Dış Avukat Tanımla') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-slate-100">
                
                {{-- ÜST BİLGİ --}}
                <div class="bg-indigo-600 p-8 text-white relative">
                    <div class="relative z-10">
                        <h3 class="text-xl font-black italic tracking-tighter mb-2 uppercase">Dış Avukat Kayıt Paneli</h3>
                        <p class="text-indigo-100 text-sm font-medium">Sisteme yeni bir dış avukat dahil etmek için aşağıdaki formu eksiksiz doldurunuz.</p>
                    </div>
                    <div class="absolute right-0 top-0 bottom-0 w-32 bg-slate-900 opacity-20 skew-x-[-20deg] translate-x-10"></div>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.dis_avukatlar.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- AD SOYAD --}}
                            <div class="space-y-1">
                                <label for="name" class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Avukat Adı Soyadı</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all font-bold text-slate-700"
                                    placeholder="Örn: Av. Serkan Aydın">
                                @error('name') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- E-POSTA --}}
                            <div class="space-y-1">
                                <label for="email" class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">E-Posta Adresi (Giriş ID)</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all font-bold text-slate-700"
                                    placeholder="ornek@hukuk.com">
                                @error('email') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- TELEFON --}}
                            <div class="space-y-1">
                                <label for="telefon" class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Telefon Numarası</label>
                                <input type="text" name="telefon" id="telefon" value="{{ old('telefon') }}"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all font-bold text-slate-700"
                                    placeholder="05xx xxx xx xx">
                                @error('telefon') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- ŞİFRE --}}
                            <div class="space-y-1">
                                <label for="password" class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Başlangıç Şifresi</label>
                                <input type="password" name="password" id="password" required
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all font-bold text-slate-700"
                                    placeholder="••••••••">
                                <p class="text-[10px] text-slate-400 italic mt-1 leading-tight">Lütfen en az 8 karakterden oluşan güçlü bir şifre belirleyin.</p>
                                @error('password') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                            <div class="text-[11px] text-slate-400 italic flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Kayıt işlemi sonrası avukatın rolü otomatik atanacaktır.
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.dis_avukatlar.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-slate-200 transition-all">Vazgeç</a>
                                <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-xl font-black uppercase text-xs tracking-widest hover:bg-indigo-600 transition-all shadow-lg hover:shadow-indigo-200">
                                    Kaydı Tamamla
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>