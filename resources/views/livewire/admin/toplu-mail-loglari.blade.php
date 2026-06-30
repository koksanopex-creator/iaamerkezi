@push('pageTitle')
    Toplu Mail Logları | 
@endpush

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-2xl border border-white/20">
            <div class="p-8">
                <div class="mb-8 border-b border-gray-200 pb-4">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Toplu Mail Gönderim Geçmişi</h2>
                    <p class="text-sm text-slate-500 font-medium mt-1">Sistemden müşterilere gönderilen tüm toplu maillerin log kayıtlarını ve teslimat durumlarını inceleyin.</p>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="py-4 px-6 font-bold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Tarih</th>
                                <th class="py-4 px-6 font-bold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Gönderen</th>
                                <th class="py-4 px-6 font-bold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Konu</th>
                                <th class="py-4 px-6 font-bold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">Alıcı Sayısı</th>
                                <th class="py-4 px-6 font-bold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200 text-right">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-6 text-sm text-slate-600 font-medium whitespace-nowrap">
                                        {{ $log->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="py-4 px-6 text-sm text-slate-800 font-bold whitespace-nowrap">
                                        {{ $log->sender->name ?? 'Bilinmiyor' }}
                                    </td>
                                    <td class="py-4 px-6 text-sm text-slate-700">
                                        {{ Str::limit($log->subject, 50) }}
                                    </td>
                                    <td class="py-4 px-6 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                            {{ $log->total_recipients }} Kişi
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-right whitespace-nowrap">
                                        <button wire:click="viewDetails({{ $log->id }})" class="text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                            Detayları Gör
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-500 font-medium">
                                        Henüz hiçbir toplu mail gönderimi yapılmamış.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-data="{ open: false }" 
         x-on:open-log-modal.window="open = true" 
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 transition-opacity" 
                 aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900 opacity-75 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-slate-100">
                
                @if($selectedLog)
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-black text-slate-800" id="modal-title">
                                Gönderim Detayları
                            </h3>
                            <div class="mt-4 bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Konu</p>
                                <p class="text-md text-slate-800 font-medium mb-4">{{ $selectedLog->subject }}</p>
                                
                                <p class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Mesaj İçeriği</p>
                                <div class="text-sm text-slate-700 bg-white p-4 rounded-lg border border-slate-200 max-h-48 overflow-y-auto prose prose-sm max-w-none">
                                    {!! $selectedLog->body !!}
                                </div>
                            </div>
                            
                            <h4 class="mt-6 mb-3 text-sm font-bold text-slate-700 uppercase tracking-wider">Alıcı Durumları</h4>
                            <div class="max-h-64 overflow-y-auto bg-white border border-slate-200 rounded-xl">
                                <ul class="divide-y divide-slate-100">
                                    @foreach($selectedLog->recipients as $recipient)
                                    <li class="p-3 flex items-center justify-between hover:bg-slate-50">
                                        <div class="flex flex-col min-w-0 flex-1">
                                            <span class="text-sm font-bold text-slate-800 truncate">{{ $recipient->user->name ?? 'Bilinmiyor' }}</span>
                                            <span class="text-xs text-slate-500 truncate">{{ $recipient->user->customer->name ?? 'Firma Yok' }} ({{ $recipient->user->email ?? '' }})</span>
                                            @if($recipient->status === 'failed')
                                                <span class="text-xs text-red-500 mt-1 truncate" title="{{ $recipient->error_message }}">{{ $recipient->error_message }}</span>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            @if($recipient->status === 'queued')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                                    <svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Kuyrukta
                                                </span>
                                            @elseif($recipient->status === 'sent')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Gönderildi
                                                </span>
                                            @elseif($recipient->status === 'failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    Hata
                                                </span>
                                            @endif
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                    <button type="button" x-on:click="open = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-2.5 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto transition-colors">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
