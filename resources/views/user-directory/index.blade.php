<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kullanıcı Rehberi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Arama Kutusu --}}
            <div class="mb-6">
                <form method="GET" action="{{ route('user-directory.index') }}" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="İsim, E-posta veya Bölüm ara..." class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm shadow-sm transition duration-150 ease-in-out">
                </form>
            </div>

            {{-- Kullanıcı Kartları --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($users as $user)
                    <a href="{{ route('profile.show', $user->id) }}" class="group block bg-white rounded-xl shadow-sm hover:shadow-md border border-gray-100 overflow-hidden transition-all duration-200 transform hover:-translate-y-1">
                        <div class="p-6 text-center">
                            {{-- Avatar --}}
                            <div class="mx-auto h-20 w-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 p-0.5 shadow-md group-hover:shadow-lg transition-all">
                                @if($user->profile_photo_path)
                                    <img class="h-full w-full rounded-full object-cover border-2 border-white" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                                @else
                                    <div class="h-full w-full rounded-full bg-white flex items-center justify-center text-2xl font-bold text-indigo-600 uppercase">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            
                            <h3 class="mt-4 text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            
                            <div class="mt-3 flex flex-wrap justify-center gap-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                    {{ $user->roles->first()->name ?? 'Kullanıcı' }}
                                </span>
                                @if($user->bolum)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                                        {{ $user->bolum->ad }}
                                    </span>
                                @endif
                            </div>

                            {{-- Puan Rozeti (Süper Admin değilse göster) --}}
                            @if(!$user->hasRole('Superadmin'))
                                <div class="mt-4 border-t border-gray-100 pt-3">
                                    <div class="text-xs font-bold text-amber-500 uppercase tracking-wider">PUAN</div>
                                    <div class="text-xl font-black text-gray-800">{{ number_format($user->toplam_puan, 0) }}</div>
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Kullanıcı Bulunamadı</h3>
                        <p class="mt-1 text-sm text-gray-500">Arama kriterlerinize uygun bir sonuç yok.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>