<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('logs.login.index') }}"
                    class="p-2 bg-white rounded-lg border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ $user->name }} - Giriş Geçmişi
                    </h2>
                    <p class="text-xs text-gray-500 font-medium">Birim: {{ $user->bolum->ad ?? '-' }} |
                        {{ $user->email }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">
                @forelse($activities as $month => $days)
                    <div class="relative">
                        <!-- Ay Başlığı -->
                        <div
                            class="sticky top-0 z-10 py-3 bg-gray-100/80 backdrop-blur-md px-4 rounded-xl border border-gray-200 mb-4 flex justify-between items-center shadow-sm">
                            <h3 class="font-black text-indigo-900 uppercase tracking-widest text-sm">{{ $month }}</h3>
                            <span class="text-[10px] font-bold bg-indigo-600 text-white px-2 py-0.5 rounded-full">
                                {{ $days->flatten()->count() }} Giriş
                            </span>
                        </div>

                        <div class="space-y-4 ml-4 border-l-2 border-indigo-100 pl-6 pb-2">
                            @foreach($days as $day => $logs)
                                <div
                                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                                    <div
                                        class="px-4 py-2 bg-gray-50/50 border-b border-gray-50 flex justify-between items-center">
                                        <h4 class="text-xs font-bold text-gray-700 capitalize">{{ $day }}</h4>
                                        <span class="text-[10px] text-gray-400">{{ $logs->count() }} kez</span>
                                    </div>
                                    <div class="p-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($logs as $log)
                                                <div
                                                    class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100 group hover:bg-white hover:border-indigo-200 transition-all">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="p-1.5 bg-white rounded-md border border-gray-100 text-indigo-500 shadow-sm">
                                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="flex flex-col">
                                                            @if($log->last_activity_at)
                                                                <span class="text-[10px] font-bold text-gray-800">{{ $log->created_at->format('H:i') }} - {{ $log->last_activity_at->format('H:i') }}</span>
                                                                @php $diff = $log->created_at->diffInMinutes($log->last_activity_at); @endphp
                                                                <span class="text-[9px] font-bold {{ $diff > 0 ? 'text-indigo-600' : 'text-blue-500' }}">
                                                                    @if($diff >= 60)
                                                                        {{ floor($diff/60) }}sa {{ $diff%60 }}dk
                                                                    @elseif($diff > 0)
                                                                        {{ $diff }} dk
                                                                    @else
                                                                        Yeni Giriş
                                                                    @endif
                                                                </span>
                                                            @else
                                                                <span class="text-[10px] font-bold text-gray-800">{{ $log->created_at->format('H:i') }}</span>
                                                                <span class="text-[9px] font-medium text-gray-400 italic">Ölçüm özellik öncesi</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="text-[9px] font-mono text-gray-400 group-hover:text-indigo-400 transition-colors">{{ $log->ip_address }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-12 rounded-3xl border border-dashed border-gray-300 text-center">
                        <div class="flex flex-col items-center gap-4 text-gray-400">
                            <svg class="h-16 w-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="font-medium italic">Bu kullanıcıya ait giriş kaydı bulunamadı.</p>
                            <a href="{{ route('logs.login.index') }}"
                                class="text-indigo-600 hover:underline text-sm font-bold">Listeye Geri Dön</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>