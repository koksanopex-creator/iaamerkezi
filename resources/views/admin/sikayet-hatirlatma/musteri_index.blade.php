<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Hatırlatmalarım') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Dashboard'a Dön
                </a>
            </div>
        </div>
    </x-slot>

    @push('pageTitle')
        Hatırlatmalarım | 
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FİLTRELEME --}}
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('iaa.hatirlatmalarim.index') }}" class="px-4 py-2 {{ !request('filtre') ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100' }} text-xs font-bold rounded-lg transition-all">
                        Tümü
                    </a>
                    
                    @php
                        $user = auth()->user();
                        $baseQuery = \App\Models\SikayetHatirlatma::whereHas('musteriSikayeti', function($q) use ($user) {
                            $q->where('customer_id', session('active_customer_id_' . $user->id, $user->customer_id))
                              ->whereNotIn('musteri_durum', ['Kapatıldı', 'Çözümlendi']);
                        });
                        
                        $bekleyenCount = (clone $baseQuery)->where('durum', 'bilgi_girisi_bekleniyor')->count();
                        $bilgiGirildiCount = (clone $baseQuery)->where('durum', 'bilgi_girildi')->count();
                    @endphp

                    <a href="{{ route('iaa.hatirlatmalarim.index', ['filtre' => 'bekleyen']) }}" class="px-4 py-2 {{ request('filtre') == 'bekleyen' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100' }} text-xs font-bold rounded-lg transition-all flex items-center gap-2">
                        Cevap Bekleyenler
                        @if($bekleyenCount > 0)
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $bekleyenCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('iaa.hatirlatmalarim.index', ['filtre' => 'bilgi_girildi']) }}" class="px-4 py-2 {{ request('filtre') == 'bilgi_girildi' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100' }} text-xs font-bold rounded-lg transition-all flex items-center gap-2">
                        Bilgi Girilenler
                        @if($bilgiGirildiCount > 0)
                            <span class="bg-blue-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $bilgiGirildiCount }}</span>
                        @endif
                    </a>
                </div>
                <p class="text-[11px] text-gray-400 font-medium italic">
                    * Şikayetlerinizle ilgili ek bilgi taleplerini buradan takip edebilirsiniz.
                </p>
            </div>

            {{-- LİSTE --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Şikayet Konusu</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Gönderen / Tarih</th>
                                <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Durum</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($hatirlatmalar as $hat)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                                {{ Str::limit($hat->musteriSikayeti->musteri_sikayet_konusu, 60) }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 mt-0.5">
                                                {{ $hat->musteriSikayeti->sikayetKategori->ad ?? 'Genel' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold">
                                                {{ substr($hat->gonderen->name, 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-gray-700">{{ $hat->gonderen->name }}</span>
                                                <span class="text-[10px] text-gray-400">{{ $hat->created_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            if ($hat->durum === 'bilgi_girisi_bekleniyor') {
                                                $badgeClass = 'bg-red-50 text-red-700 border-red-100 animate-pulse';
                                                $statusText = 'CEVAP BEKLİYOR';
                                            } elseif ($hat->durum === 'bilgi_girildi') {
                                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                                $statusText = 'BİLGİ GİRİLDİ';
                                            } else {
                                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                                $statusText = 'TAMAMLANDI';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black border {{ $badgeClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('iaa.sikayetler.show', $hat->musteri_sikayeti_id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 text-gray-500 hover:text-indigo-700 text-[9px] font-black rounded-lg transition-all shadow-sm" title="Şikayet Detayı">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </a>
                                            <a href="{{ route('iaa.hatirlatmalarim.show', $hat->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent hover:bg-indigo-700 text-white text-[10px] font-black rounded-lg transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                                                DETAYA GİT
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="p-4 bg-gray-50 rounded-full mb-4">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                            </div>
                                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest">Henüz Hatırlatma Yok</h3>
                                            <p class="text-xs text-gray-400 mt-1">Herhangi bir şikayetiniz için ek bilgi talebi bulunmamaktadır.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($hatirlatmalar->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $hatirlatmalar->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
