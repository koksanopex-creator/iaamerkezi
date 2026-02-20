<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Kullanıcı Giriş Logları') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                &larr; Dashboard'a Dön
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Search Bar -->
                    <div class="mb-6">
                        <form action="{{ route('logs.login.index') }}" method="GET" class="relative max-w-md">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="İsim veya e-posta ile ara..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            @if($search)
                                <a href="{{ route('logs.login.index') }}"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50/80 backdrop-blur-sm border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Kullanıcı</th>
                                    <th class="px-6 py-4 font-bold">Bölüm</th>
                                    <th class="px-6 py-4 font-bold">Giriş / Son İşlem</th>
                                    <th class="px-6 py-4 font-bold">Süre</th>
                                    <th class="px-6 py-4 font-bold">IP Adresi</th>
                                    <th class="px-6 py-4 text-right font-bold">İşlem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($users as $user)
                                    <tr class="bg-white hover:bg-indigo-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200 text-sm">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-900">{{ $user->name }}</span>
                                                    <span class="text-[10px] text-gray-400">{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs text-gray-600 font-medium whitespace-nowrap">
                                                {{ $user->bolum->ad ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->loginActivities->isNotEmpty())
                                                @php $lastAct = $user->loginActivities->first(); @endphp
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-tighter">İLK: {{ $lastAct->created_at->format('H:i') }}</span>
                                                    @if($lastAct->last_activity_at)
                                                        <span class="text-[10px] text-indigo-500 font-bold uppercase tracking-tighter">SON: {{ $lastAct->last_activity_at->format('H:i') }}</span>
                                                    @else
                                                        <span class="text-[9px] text-gray-400 italic">Takip Öncesi</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Kayıt yok</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->loginActivities->isNotEmpty())
                                                @php 
                                                    $lastAct = $user->loginActivities->first();
                                                    $diff = $lastAct->last_activity_at ? $lastAct->created_at->diffInMinutes($lastAct->last_activity_at) : null;
                                                @endphp
                                                @if(!is_null($diff))
                                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold {{ $diff > 0 ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                                        @if($diff >= 60)
                                                            {{ floor($diff / 60) }} sa {{ $diff % 60 }} dk
                                                        @elseif($diff > 0)
                                                            {{ $diff }} dk
                                                        @else
                                                            Yeni
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-[10px] text-gray-300 italic border-b border-dotted border-gray-200" title="Bu özellik 13 Şubat'ta eklendi">N/A</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->loginActivities->isNotEmpty())
                                                <span
                                                    class="text-xs font-mono text-gray-500 bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                                    {{ $user->loginActivities->first()->ip_address }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('logs.login.show', $user->id) }}"
                                                class="inline-flex items-center px-4 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 hover:scale-105 transition-all shadow-sm">
                                                Detayları Gör
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg class="h-12 w-12 text-gray-200" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z">
                                                    </path>
                                                </svg>
                                                <span class="text-gray-400 font-medium italic">Kullanıcı bulunamadı.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>