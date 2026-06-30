@push('pageTitle')
    500 | 
@endpush

<x-app-layout>
    <div class="min-h-[80vh] flex flex-col items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8 relative">
                <div class="absolute inset-0 bg-red-100 rounded-full animate-pulse opacity-20"></div>
                <div class="relative w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto text-red-500 shadow-sm border border-red-100">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-6xl font-black text-slate-800 tracking-tighter mb-2">500</h1>
            <h2 class="text-2xl font-bold text-slate-700 mb-4">Sunucu Hatası</h2>

            <div class="bg-red-50 border border-red-100 rounded-lg p-4 mb-8">
                <p class="text-red-600 font-medium leading-relaxed">
                    İşlem sırasında beklenmedik bir hata oluştu. Teknik ekibimiz durumdan haberdar edildi.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 shadow-sm text-sm font-medium rounded-xl text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                    Geri Dön
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all duration-200 transform hover:-translate-y-0.5">
                    Anasayfaya Git
                </a>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-100">
                <p class="text-xs text-slate-400 font-mono">HATA KODU: 500_INTERNAL_SERVER_ERROR</p>
            </div>
        </div>
    </div>
</x-app-layout>
