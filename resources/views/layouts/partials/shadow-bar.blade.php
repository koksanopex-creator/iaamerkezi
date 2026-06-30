@auth
    @if(Auth::user()->isShadowing())
        {{-- SHADOW BAR: Birini izlerken her sayfanın en üstünde görünür --}}
        <div class="bg-amber-600 text-white py-3 shadow-md sticky top-0 z-[9999] animate-pulse">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">👁️</span>
                    <div>
                        <p class="text-sm font-bold leading-none">GÖZLEMCİ MODU AKTİF</p>
                        <p class="text-xs opacity-90">Şu an <strong>{{ Auth::user()->getEffectiveUser()->name }}</strong> hesabını izliyorsunuz. (Salt Okunur)</p>
                    </div>
                </div>
                <form action="{{ route('observer.stop') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="bg-white text-amber-700 px-4 py-1.5 rounded-lg text-sm font-bold hover:bg-amber-50 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Kendi Hesabıma Dön
                    </button>
                </form>
            </div>
        </div>
    @endif
@endauth
