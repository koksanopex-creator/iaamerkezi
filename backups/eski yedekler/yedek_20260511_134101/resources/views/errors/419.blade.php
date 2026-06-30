@push('pageTitle')
    419 | 
@endpush

<x-app-layout>
    <div class="min-h-[80vh] flex flex-col items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8 relative">
                <div class="absolute inset-0 bg-yellow-100 rounded-full animate-pulse opacity-20"></div>
                <div class="relative w-24 h-24 bg-yellow-50 rounded-full flex items-center justify-center mx-auto text-yellow-600 shadow-sm border border-yellow-100">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-6xl font-black text-slate-800 tracking-tighter mb-2">419</h1>
            <h2 class="text-2xl font-bold text-slate-700 mb-4">Oturum Süresi Doldu</h2>

            <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-4 mb-8">
                <p class="text-yellow-700 font-medium leading-relaxed">
                    Sayfa çok uzun süre açık kaldığı için oturumunuzun süresi dolmuş olabilir. Lütfen sayfayı yenileyip tekrar deneyin.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url()->current() }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all duration-200 transform hover:-translate-y-0.5">
                    Sayfayı Yenile
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 shadow-sm text-sm font-medium rounded-xl text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                    Anasayfaya Git
                </a>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-100">
                <p class="text-xs text-slate-400 font-mono">HATA KODU: 419_SESSION_EXPIRED</p>
            </div>
        </div>
    </div>
</x-app-layout>
