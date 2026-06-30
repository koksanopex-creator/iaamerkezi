@push('pageTitle')
    Dış Avukat Yönetimi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tighter flex items-center gap-3">
                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                {{ __('Dış Avukat Portalı') }}
            </h2>
            <div class="flex items-center gap-3">
                <div class="hidden md:flex flex-col text-right">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sistem Durumu</span>
                    <span class="text-xs font-bold text-emerald-600 flex items-center justify-end gap-1">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        Aktif Kayıtlı Avukat: {{ $totalLawyers }}
                    </span>
                </div>
                <a href="{{ route('admin.dis_avukatlar.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-slate-900 text-white font-bold py-2.5 px-5 rounded-xl transition-all shadow-lg hover:shadow-indigo-200 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Yeni Avukat Tanımla
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- İSTATİSTİK VE FİLTRELEME ALANI --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Toplam Kartı --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group hover:border-indigo-200 transition-all">
                    <div>
                        <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-1">Toplam Avukat</p>
                        <h3 class="text-3xl font-black text-slate-900 leading-none group-hover:text-indigo-600 transition-colors">{{ $totalLawyers }}</h3>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl group-hover:bg-indigo-50 transition-colors">
                        <svg class="w-8 h-8 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>

                {{-- ARAMA FORMU --}}
                <div class="md:col-span-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center">
                    <form action="{{ route('admin.dis_avukatlar.index') }}" method="GET" class="w-full flex gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" 
                                class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all sm:text-sm" 
                                placeholder="İsim, E-posta veya Telefon ile hızlı ara...">
                        </div>
                        <button type="submit" class="bg-slate-900 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-indigo-600 transition-all shadow-md">Filtrele</button>
                        @if($search)
                            <a href="{{ route('admin.dis_avukatlar.index') }}" class="bg-rose-50 text-rose-600 px-4 py-2.5 rounded-xl font-bold hover:bg-rose-100 transition-all flex items-center">Sıfırla</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-slate-100">
                <div class="overflow-x-auto min-h-[400px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Avukat Profili</th>
                                <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">İletişim Bilgileri</th>
                                <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Ekleyen Kullanıcı</th>
                                <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Kayıt Tarihi</th>
                                <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($lawyers as $avukat)
                                <tr class="hover:bg-slate-50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-black border border-indigo-100">
                                                {{ strtoupper(substr($avukat->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-slate-800 leading-tight">{{ $avukat->name }}</p>
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[10px] font-black uppercase tracking-widest">Dış Avukat</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-slate-700 mb-0.5">{{ $avukat->email }}</p>
                                        <p class="text-xs text-slate-400 italic">Tel: {{ $avukat->telefon ?? 'Tanımlanmamış' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-3">
                                            {{-- EKLEYEN --}}
                                            @if($avukat->addedBy)
                                                <div class="flex items-center gap-2 group/user cursor-pointer">
                                                    <a href="{{ route('profile.show', $avukat->addedBy->id) }}" class="flex items-center gap-2">
                                                        <img src="{{ $avukat->addedBy->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($avukat->addedBy->name).'&color=7F9CF5&background=EBF4FF' }}" 
                                                             class="w-6 h-6 rounded-full border border-slate-200 group-hover/user:border-indigo-400 transition-colors object-cover" 
                                                             title="Ekleyen: {{ $avukat->addedBy->name }}">
                                                        <span class="text-xs font-bold text-slate-600 underline decoration-slate-200 hover:text-indigo-600 hover:decoration-indigo-200 transition-all">{{ $avukat->addedBy->name }}</span>
                                                    </a>
                                                    <span class="text-[10px] text-slate-300 font-medium">(Ekleyen)</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-300 italic">Log kaydı yok</span>
                                            @endif

                                            {{-- DÜZENLEYEN --}}
                                            @if($avukat->updatedBy)
                                                <div class="flex flex-col gap-1 border-t border-slate-50 pt-2">
                                                    <div class="flex items-center gap-2 group/user cursor-pointer">
                                                        <a href="{{ route('profile.show', $avukat->updatedBy->id) }}" class="flex items-center gap-2">
                                                            <img src="{{ $avukat->updatedBy->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($avukat->updatedBy->name).'&color=F59E0B&background=FEF3C7' }}" 
                                                                 class="w-6 h-6 rounded-full border border-slate-200 group-hover/user:border-amber-400 transition-colors object-cover" 
                                                                 title="Son Düzenleyen: {{ $avukat->updatedBy->name }}">
                                                            <span class="text-[11px] font-bold text-slate-500 hover:text-amber-600 transition-all italic">{{ $avukat->updatedBy->name }}</span>
                                                        </a>
                                                        <span class="text-[9px] text-slate-300 font-medium uppercase tracking-tighter">Düzenledi</span>
                                                    </div>
                                                    <p class="text-[9px] text-slate-400 ml-8 font-medium leading-none">
                                                        {{ $avukat->updated_at->translatedFormat('d F Y') }}
                                                        <span class="text-slate-300">({{ $avukat->updated_at->diffForHumans() }})</span>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-black text-slate-500 uppercase">{{ $avukat->created_at->translatedFormat('d F Y') }}</p>
                                        <p class="text-[10px] text-slate-400 tracking-tighter">{{ $avukat->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            @php
                                                $canEdit = auth()->user()->hasRole(['Superadmin', 'Hukuk Admini']) || $avukat->created_by_id === auth()->id();
                                            @endphp
                                            
                                            @if($canEdit)
                                                <a href="{{ route('admin.dis_avukatlar.edit', $avukat->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Bilgileri Düzenle">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <form action="{{ route('admin.dis_avukatlar.destroy', $avukat->id) }}" method="POST" onsubmit="return confirm('Bu avukat kaydını silmek istediğinize emin misiniz?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Kaydı Sil">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[10px] bg-slate-50 text-slate-400 px-2 py-1 rounded italic font-medium">Yetkiniz Yok</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="flex flex-col items-center justify-center py-20 bg-slate-50/30">
                                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-slate-400 uppercase tracking-widest italic">Henüz tanımlı dış avukat bulunmamaktadır...</h3>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>