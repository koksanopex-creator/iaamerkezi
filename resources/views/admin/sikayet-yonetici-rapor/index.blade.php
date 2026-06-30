<x-app-layout>
    @push('pageTitle')Yönetici Rapor Kuralları | @endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Yönetici Rapor Kuralları') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Yönetici Rapor Kuralları</h1>
                    <p class="text-sm text-slate-500 font-medium italic">Sistemin Müşteri Şikayeti Kurul Yöneticilerine göndereceği otomatik rapor kuralları</p>
                </div>
                <a href="{{ route('admin.sikayet-yonetici-rapor.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-black rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all hover:-translate-y-1 active:translate-y-0">
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
                                <th class="px-6 py-5">Aktif Kanallar</th>
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
                                            <span class="text-sm font-bold text-slate-600">{{ \Carbon\Carbon::parse($kural->saat)->format('H:i') }}</span>
                                        </div>
                                        @if($kural->siklik === 'haftalik' && $kural->haftanin_gunleri)
                                            <div class="mt-2 flex gap-1">
                                                @foreach((is_array($kural->haftanin_gunleri) ? $kural->haftanin_gunleri : json_decode($kural->haftanin_gunleri, true)) as $gun)
                                                    <span class="text-[9px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded uppercase font-bold">{{ $gun }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex gap-2">
                                            @if($kural->mail_aktif_et)
                                                <span class="px-2 py-1 bg-amber-50 text-amber-600 rounded-lg text-xs font-black uppercase">E-posta</span>
                                            @endif
                                            @if($kural->zili_aktif_et)
                                                <span class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-black uppercase">Zil Bildirimi</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        @if($kural->aktif)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-500">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-2"></span>
                                                Pasif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('admin.sikayet-yonetici-rapor.edit', $kural) }}" class="text-slate-400 hover:text-indigo-600 transition-colors p-2 hover:bg-indigo-50 rounded-xl">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('admin.sikayet-yonetici-rapor.destroy', $kural) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu kuralı silmek istediğinize emin misiniz?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-slate-400 hover:text-rose-600 transition-colors p-2 hover:bg-rose-50 rounded-xl">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </div>
                                        <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Henüz rapor kuralı eklenmemiş.</p>
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
