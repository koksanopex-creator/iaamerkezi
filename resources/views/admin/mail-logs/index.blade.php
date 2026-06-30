<x-app-layout>
    @push('pageTitle') Mail Bildirim Logları | @endpush
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- BAŞLIK --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Mail Bildirim Logları
                </h1>
                <p class="text-sm text-slate-500 mt-1">Gönderilemeyen mail bildirimlerinin takibi ve yeniden gönderim</p>
            </div>
        </div>

        {{-- DURUM MESAJLARI --}}
        @foreach(['success' => 'emerald', 'error' => 'rose', 'info' => 'indigo'] as $type => $color)
            @if(session($type))
                <div class="mb-4 p-3 rounded-lg bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 text-sm font-medium flex items-center gap-2">
                    @if($type === 'success')
                        <svg class="w-5 h-5 text-{{ $color }}-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    @elseif($type === 'error')
                        <svg class="w-5 h-5 text-{{ $color }}-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    @else
                        <svg class="w-5 h-5 text-{{ $color }}-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    @endif
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        {{-- İSTATİSTİK KARTLARI --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Toplam Log</p>
                        <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['toplam'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-rose-200 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-rose-400 uppercase tracking-wider">Çözülmedi</p>
                        <p class="text-2xl font-black text-rose-600 mt-1">{{ $stats['cozulmedi'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-emerald-200 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Çözüldü</p>
                        <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['cozuldu'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- FİLTRELER --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6 shadow-sm">
            <form method="GET" action="{{ route('admin.mail-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Başlangıç Tarihi</label>
                    <input type="date" name="tarih_baslangic" value="{{ request('tarih_baslangic') }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Bitiş Tarihi</label>
                    <input type="date" name="tarih_bitis" value="{{ request('tarih_bitis') }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Durum</label>
                    <select name="durum" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tümü</option>
                        <option value="cozulmedi" {{ request('durum') == 'cozulmedi' ? 'selected' : '' }}>Çözülmedi</option>
                        <option value="cozuldu" {{ request('durum') == 'cozuldu' ? 'selected' : '' }}>Çözüldü</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Arama</label>
                    <input type="text" name="arama" value="{{ request('arama') }}" placeholder="İşlem veya hata ara..."
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors">
                        Filtrele
                    </button>
                    <a href="{{ route('admin.mail-logs.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition-colors" title="Filtreleri Temizle">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </a>
                </div>
            </form>
        </div>

        {{-- LOG TABLOSU --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            @if($logs->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-emerald-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-bold text-slate-700">Hata Kaydı Yok</h3>
                    <p class="text-sm text-slate-500 mt-1">Tüm mail bildirimleri başarıyla gönderilmiş görünüyor.</p>
                </div>
            @else
                {{-- Masaüstü Tablo --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">İşlem</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Alıcılar</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Hata</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Bölüm</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-slate-500 uppercase tracking-wider">Durum</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-slate-500 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($logs as $log)
                                <tr class="hover:bg-slate-50/50 transition-colors {{ $log->isResolved() ? 'opacity-60' : '' }}">
                                    {{-- Tarih --}}
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-xs font-bold text-slate-700">{{ $log->created_at->format('d.m.Y') }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log->created_at->format('H:i') }}</div>
                                    </td>

                                    {{-- İşlem --}}
                                    <td class="px-4 py-3">
                                        <div class="text-xs font-bold text-slate-800 max-w-[200px] truncate" title="{{ $log->source_action }}">{{ $log->source_action }}</div>
                                        @if($log->source_page)
                                            <div class="text-[10px] text-slate-400 max-w-[200px] truncate" title="{{ $log->source_page }}">{{ parse_url($log->source_page, PHP_URL_PATH) }}</div>
                                        @endif
                                    </td>

                                    {{-- Alıcılar --}}
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-slate-600 max-w-[180px]">
                                            @if($log->recipients && is_array($log->recipients))
                                                @foreach(array_slice($log->recipients, 0, 2) as $recipient)
                                                    <div class="truncate" title="{{ $recipient }}">{{ $recipient }}</div>
                                                @endforeach
                                                @if(count($log->recipients) > 2)
                                                    <div class="text-indigo-500 font-bold text-[10px]">+{{ count($log->recipients) - 2 }} kişi daha</div>
                                                @endif
                                            @else
                                                <span class="text-slate-400 italic">Bilinmiyor</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Hata Mesajı --}}
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-rose-600 max-w-[220px] truncate cursor-help" title="{{ $log->error_message }}">
                                            {{ Str::limit($log->error_message, 80) }}
                                        </div>
                                    </td>

                                    {{-- Bölüm --}}
                                    <td class="px-4 py-3">
                                        <span class="text-xs text-slate-500">{{ $log->bolum->ad ?? '—' }}</span>
                                    </td>

                                    {{-- Durum --}}
                                    <td class="px-4 py-3 text-center">
                                        @if($log->isResolved())
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700" title="Çözüldü: {{ $log->resolved_at->format('d.m.Y H:i') }}{{ $log->resolver ? ' — ' . $log->resolver->name : '' }}">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                ÇÖZÜLDÜ
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-600 animate-pulse">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                BAŞARISIZ
                                            </span>
                                            @if($log->retry_count > 0)
                                                <div class="text-[9px] text-slate-400 mt-0.5">{{ $log->retry_count }}x denenmiş</div>
                                            @endif
                                        @endif
                                    </td>

                                    {{-- İşlem Butonları --}}
                                    <td class="px-4 py-3 text-center">
                                        @if(!$log->isResolved())
                                            <div class="flex flex-col gap-1 items-center">
                                                @if($log->notification_class && $log->notification_data)
                                                    <form method="POST" action="{{ route('admin.mail-logs.retry', $log->id) }}" class="inline" onsubmit="return confirm('Bu bildirimi tekrar göndermeyi denemek istediğinize emin misiniz?')">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-sm">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                            Tekrar Dene
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.mail-logs.mark-resolved', $log->id) }}" class="inline" onsubmit="return confirm('Bu kaydı çözüldü olarak işaretlemek istediğinize emin misiniz?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Çözüldü İşaretle
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobil Kartlar --}}
                <div class="lg:hidden divide-y divide-slate-100">
                    @foreach($logs as $log)
                        <div class="p-4 {{ $log->isResolved() ? 'opacity-60' : '' }}">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div>
                                    <div class="text-xs font-black text-slate-800">{{ $log->source_action }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $log->created_at->format('d.m.Y H:i') }}</div>
                                </div>
                                @if($log->isResolved())
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-700">✓ ÇÖZÜLDÜ</span>
                                @else
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-100 text-rose-600 animate-pulse">✕ BAŞARISIZ</span>
                                @endif
                            </div>

                            <div class="text-[11px] text-rose-600 mb-2 line-clamp-2">{{ $log->error_message }}</div>

                            @if($log->recipients && is_array($log->recipients))
                                <div class="text-[10px] text-slate-500 mb-2">
                                    <span class="font-bold">Alıcılar:</span> {{ implode(', ', array_slice($log->recipients, 0, 3)) }}
                                    @if(count($log->recipients) > 3) <span class="text-indigo-500 font-bold">+{{ count($log->recipients) - 3 }}</span> @endif
                                </div>
                            @endif

                            @if(!$log->isResolved())
                                <div class="flex gap-2 mt-2">
                                    @if($log->notification_class && $log->notification_data)
                                        <form method="POST" action="{{ route('admin.mail-logs.retry', $log->id) }}" class="inline" onsubmit="return confirm('Tekrar göndermek istediğinize emin misiniz?')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                                                🔄 Tekrar Dene
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.mail-logs.mark-resolved', $log->id) }}" class="inline" onsubmit="return confirm('Çözüldü olarak işaretlemek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-600 hover:bg-emerald-100 transition-colors">
                                            ✓ Çözüldü
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
