@push('pageTitle')
    403 | 
@endpush

<x-app-layout>
    @php
        // Hata mesajını exception üzerinden al veya varsayılanı kullan
        $defaultMessage = 'Bu sayfaya erişim yetkiniz yok.';
        $exceptionMessage = $exception->getMessage() ?: $defaultMessage;

        // İngilizce mesajları Türkçeye çevirmek için basit bir harita
        $translations = [
            'User does not have the right roles.' => 'Bu işlem için gerekli role sahip değilsiniz.',
            'This action is unauthorized.' => 'Bu işlem yetkilendirilmedi.',
            'Bu raporu görüntüleme yetkiniz yok.' => 'Bu raporu görüntüleme yetkiniz yok.', // Zaten Türkçe
        ];

        // Mesajı çevir veya olduğu gibi kullan
        $displayMessage = $translations[$exceptionMessage] ?? $exceptionMessage;

        // Eğer mesaj hala İngilizce gibi duriyorsa ve çeviride yoksa (basit bir kontrol)
        // Burada daha gelişmiş bir çeviri mantığı kurulabilir ama şimdilik manuel mapping yeterli.
    @endphp

    <div class="min-h-[80vh] flex flex-col items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <!-- İkon -->
            <div class="mb-8 relative">
                <div class="absolute inset-0 bg-red-100 rounded-full animate-ping opacity-20"></div>
                <div
                    class="relative w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto text-red-500 shadow-sm border border-red-100">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Başlık -->
            <h1 class="text-6xl font-black text-slate-800 tracking-tighter mb-2">403</h1>
            <h2 class="text-2xl font-bold text-slate-700 mb-4">Erişim İzniniz Yok</h2>

            <!-- Dinamik Açıklama -->
            <div class="bg-red-50 border border-red-100 rounded-lg p-4 mb-8">
                <p class="text-red-600 font-medium leading-relaxed">
                    {{ $displayMessage }}
                </p>
                @if($exceptionMessage !== $defaultMessage && !array_key_exists($exceptionMessage, $translations))
                    {{-- Eğer özel bir mesaj varsa ve çeviride yoksa, altına küçük not olarak orijinalini de basabiliriz
                    (Debug için opsiyonel) --}}
                    {{-- <p class="text-xs text-red-400 mt-2 font-mono">{{ $exceptionMessage }}</p> --}}
                @endif
            </div>

            <p class="text-slate-500 text-sm mb-8">
                Eğer bir hata olduğunu düşünüyorsanız, lütfen yöneticinizle iletişime geçin.
            </p>

            <!-- Butonlar -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 shadow-sm text-sm font-medium rounded-xl text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Geri Dön
                </a>

                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Anasayfaya Git
                </a>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-100">
                <p class="text-xs text-slate-400 font-mono">HATA KODU: 403_FORBIDDEN_ACCESS</p>
            </div>
        </div>
    </div>
</x-app-layout>