<section class="relative overflow-hidden">
    <header class="mb-6">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <!-- Sleek Key Icon -->
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m-9 5a2 2 0 012-2m7-7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7z" />
            </svg>
            {{ __('Şifre Güncelle') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Merkezi kimlik doğrulama sistemi (SSO) aktif olduğu için şifre güncellemelerinizi tek bir noktadan güvenle yapabilirsiniz.') }}
        </p>
    </header>

    @php
        $ssoUrl = rtrim(config('services.central_sso.url', 'http://localhost:8001'), '/');
        $ssoPasswordUrl = $ssoUrl . '/profile';
    @endphp

    <div class="bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 sm:p-8 shadow-xl relative overflow-hidden group">
        <!-- Decorative glowing gradient lights in background -->
        <div class="absolute -right-24 -bottom-24 w-48 h-48 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 blur-2xl opacity-35 group-hover:scale-110 transition duration-500 pointer-events-none"></div>
        <div class="absolute -left-24 -top-24 w-48 h-48 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 blur-2xl opacity-20 pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 relative z-10">
            <div class="space-y-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    ⚡ {{ __('Merkezi Güvenlik') }}
                </span>
                <h4 class="text-xl font-bold tracking-tight text-white sm:text-2xl">
                    {{ __('Şifreniz Güvende') }}
                </h4>
                <p class="text-sm text-indigo-200/90 leading-relaxed max-w-md">
                    {{ __('Şifre değişiklik işlemlerinizi KÖKSAN Portal üzerinden tek şifre (Single Sign-On) standartlarına uygun olarak gerçekleştirebilirsiniz. Değişiklik anında tüm entegre uygulamalarınızda geçerli olacaktır.') }}
                </p>
            </div>
            
            <a href="{{ $ssoPasswordUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-500 bg-[length:200%_auto] text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-950/50 hover:bg-right hover:scale-[1.03] active:scale-95 transition-all duration-300 group whitespace-nowrap">
                <span>{{ __('Şifremi Merkezi SSO Üzerinde Güncelle') }}</span>
                <svg class="w-4.5 h-4.5 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</section>