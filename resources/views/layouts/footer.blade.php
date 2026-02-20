<footer class="bg-white border-t border-gray-100 py-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2 text-gray-400 text-sm">
                <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.</span>
            </div>

            <div x-data="{ open: false }" class="relative">
                <div @mouseenter="open = true" @mouseleave="open = false"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 border border-gray-100 text-gray-500 hover:text-indigo-600 hover:border-indigo-100 transition-all cursor-help group">
                    <span class="text-xs font-medium uppercase tracking-wider opacity-60">Sistem Tasarımı &
                        Yönetimi:</span>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-indigo-600">Celal KARAMAN</span>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-400 transition-transform group-hover:rotate-12"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                </div>

                <!-- Hover Bilgi Kartı -->
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-cloak
                    class="absolute bottom-full right-0 mb-3 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 z-[110]">
                    <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-50">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">Celal KARAMAN</div>
                            <div class="text-[10px] text-indigo-500 font-black uppercase tracking-widest">Opex
                                Mühendisi</div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <a href="mailto:celal.karaman@koksan.com"
                            class="flex items-center gap-2 text-xs text-gray-600 hover:text-indigo-600 transition truncate">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            celal.karaman@koksan.com
                        </a>
                        <div class="flex items-center gap-2 text-xs text-gray-600 transition">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            0549 678 76 91
                        </div>
                    </div>

                    <div
                        class="absolute -bottom-1.5 right-6 w-3 h-3 bg-white border-b border-r border-gray-100 rotate-45">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>