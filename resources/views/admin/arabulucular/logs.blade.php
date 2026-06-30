@push('pageTitle') Sistem Logları | @endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sistem Logları') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-[#f8fafc] py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8 animate-fade-in">
            
            {{-- ÜST HEADER BÖLÜMÜ --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase italic">SİSTEM LOGLARI</h1>
                    </div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2 pl-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        Arabulucu İşlem Kayıtları ve Güvenlik Denetimi
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.arabulucular.index') }}" 
                       class="group flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl text-xs font-black hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>LİSTEYE DÖN</span>
                    </a>
                </div>
            </div>

            {{-- ANA LOG TABLOSU --}}
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden outline outline-1 outline-slate-50">
                <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-6 bg-indigo-600 rounded-full"></div>
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">İşlem Geçmişi</h2>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border border-slate-100 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">Canlı Kayıtlar</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-5 text-[11px] font-black uppercase text-slate-400 tracking-[0.2em]">İşlemi Yapan</th>
                                <th class="px-8 py-5 text-[11px] font-black uppercase text-slate-400 tracking-[0.2em]">İşlem Türü</th>
                                <th class="px-8 py-5 text-[11px] font-black uppercase text-slate-400 tracking-[0.2em]">Etkilenen Kayıt</th>
                                <th class="px-8 py-5 text-[11px] font-black uppercase text-slate-400 tracking-[0.2em]">Detay</th>
                                <th class="px-8 py-5 text-right text-[11px] font-black uppercase text-slate-400 tracking-[0.2em]">Tarih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($logs as $log)
                                <tr class="group hover:bg-slate-50/80 transition-all duration-300 cursor-default">
                                    {{-- İŞLEMİ YAPAN --}}
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center border-2 border-white shadow-sm ring-1 ring-slate-100 italic font-black text-slate-500 text-sm">
                                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-slate-900 tracking-tight group-hover:text-indigo-600 transition-colors">
                                                    {{ $log->user->name ?? 'Sistem' }}
                                                </div>
                                                <div class="flex items-center gap-1 text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21h6l-.75-4M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                                    IP: {{ $log->ip_adres }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- İŞLEM TÜRÜ --}}
                                    <td class="px-8 py-6">
                                        @php
                                            $config = match($log->islem_turu) {
                                                'OLUŞTURMA' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'icon' => '<path d="M12 4v16m8-8H4" />'],
                                                'DÜZENLEME' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-100', 'icon' => '<path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />'],
                                                'SİLME' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-100', 'icon' => '<path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />'],
                                                'DURUM DEĞİŞTİRME' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'icon' => '<path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />'],
                                                default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-100', 'icon' => '<path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />']
                                            };
                                        @endphp
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border {{ $config['border'] }} {{ $config['bg'] }} {{ $config['text'] }} transition-all group-hover:scale-105">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                {!! $config['icon'] !!}
                                            </svg>
                                            <span class="text-[10px] font-black uppercase tracking-wider">{{ $log->islem_turu }}</span>
                                        </div>
                                    </td>

                                    {{-- ETKİLENEN KAYIT --}}
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-bold text-slate-700">
                                            {{ $log->arabulucu->name ?? 'Arabulucu Kaydı Bulunamadı' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">
                                            ID: {{ $log->arabulucu_id }}
                                        </div>
                                    </td>

                                    {{-- DETAY --}}
                                    <td class="px-8 py-6">
                                        <div class="text-xs text-slate-500 font-medium leading-relaxed italic max-w-sm truncate group-hover:text-slate-700 transition-colors" title="{{ $log->detay }}">
                                            {{ $log->detay }}
                                        </div>
                                    </td>

                                    {{-- TARİH --}}
                                    <td class="px-8 py-6 text-right">
                                        <div class="text-sm font-black text-slate-900 tracking-tight">
                                            {{ $log->created_at->format('d.m.Y') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">
                                            {{ $log->created_at->format('H:i') }} • {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ALT BİLGİ VE SAYFALAMA --}}
                <div class="p-8 bg-slate-50/50 border-t border-slate-50">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</x-app-layout>