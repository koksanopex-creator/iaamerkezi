@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Otomatik Hatırlatıcı Kuralları') }}
    </h2>
@endsection

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Otomatik Hatırlatıcılar</h1>
                <p class="text-sm text-slate-500 font-medium italic">Sistemin periyodik olarak göndereceği bildirim kuralları</p>
            </div>
            <a href="{{ route('admin.sikayet-hatirlaticilar.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-black rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all hover:-translate-y-1 active:translate-y-0">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                YENİ KURAL EKLE
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5">Kural Adı</th>
                            <th class="px-6 py-5">Sıklık & Saat</th>
                            <th class="px-6 py-5">Uygulanan Durumlar</th>
                            <th class="px-6 py-5">Hedef Roller</th>
                            <th class="px-6 py-5">Durum</th>
                            <th class="px-8 py-5 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kurallar as $kural)
                            <tr class="hover:bg-slate-50/80 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-800">{{ $kural->ad }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase font-bold mt-0.5">Son Çalışma: {{ $kural->son_calisma_tarihi ? $kural->son_calisma_tarihi->format('d.m.Y H:i') : 'Hiç çalışmadı' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-black uppercase">{{ $kural->siklik }}</span>
                                        <span class="text-sm font-bold text-slate-600">{{ $kural->saat }}</span>
                                    </div>
                                    @if($kural->siklik == 'haftalik' && $kural->haftanin_gunleri)
                                        <p class="text-[10px] text-slate-400 font-bold mt-1">Günler: {{ implode(', ', array_map(fn($g) => ['Pzt','Sal','Çar','Per','Cum','Cmt','Paz'][$g-1] ?? '', $kural->haftanin_gunleri)) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($kural->proje_durumlari as $durum)
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded text-[10px] font-bold">{{ $durum }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($kural->bildirim_rolleri as $rol)
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-500 rounded text-[10px] font-bold">{{ $rol }}</span>
                                        @endforeach
                                        @if($kural->sikayeti_girene_bildir) <span class="px-2 py-0.5 bg-rose-50 text-rose-500 rounded text-[10px] font-bold">Personel</span> @endif
                                        @if($kural->musteriye_bildir) <span class="px-2 py-0.5 bg-emerald-50 text-emerald-500 rounded text-[10px] font-bold">Müşteri</span> @endif
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    @if($kural->aktif)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-500 border border-slate-200">
                                            Pasif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end items-center space-x-2">
                                        <a href="{{ route('admin.sikayet-hatirlaticilar.edit', $kural->id) }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('admin.sikayet-hatirlaticilar.destroy', $kural->id) }}" method="POST" onsubmit="return confirm('Bu kuralı silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-rose-600 hover:border-rose-200 transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center opacity-30 grayscale">
                                        <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m12 0a2 2 0 100-4m0 4a2 2 0 110-4m-6 8a2 2 0 100 4m0-4a2 2 0 110 4m-4 6h8a2 2 0 002-2v-5a2 2 0 00-2-2H8a2 2 0 00-2 2v5a2 2 0 002 2z"></path></svg>
                                        <p class="mt-4 font-black uppercase text-xs tracking-widest">Henüz bir otomatik hatırlatıcı kuralı tanımlanmadı</p>
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
@endsection
