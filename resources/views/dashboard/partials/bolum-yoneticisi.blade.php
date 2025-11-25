<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('admin.iaa-yonetim.index') }}" onclick="localStorage.setItem('activeTab', 'onay-bekleyenler')" class="group relative bg-gradient-to-br from-purple-50 to-fuchsia-50 border border-purple-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-fuchsia-600/5 rounded-2xl"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Onayınızı Bekleyenler</h3>
            <p class="text-3xl font-bold text-gray-900 mb-4">{{ $bolumOnayiBekleyenSayisi ?? 0 }}</p>
            <p class="text-gray-500 text-sm italic">Bölüm onayınız için bekleyen tamamlanmış projeler.</p>
            <div class="flex items-center mt-6 text-purple-600 font-semibold text-sm group-hover:text-purple-700">
                <span>İncele ve Onayla</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </div>
    </a>

    {{-- Standart Kartlar --}}
    <a href="{{ route('iaa.havuz') }}" class="group relative bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
        <div class="relative">
            <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center mb-4">
                 <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Havuzdaki Öneriler</h3>
            <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['havuz_oneri_sayisi'] ?? 0 }}</p>
            <div class="flex items-center mt-6 text-emerald-600 font-semibold text-sm">
                <span>Havuzu İncele</span><svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </div>
    </a>

    <a href="{{ route('takimlar.index') }}" class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
        <div class="relative">
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Takımlar</h3>
            <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['takimlarim_sayisi'] ?? 0 }}</p>
            <div class="flex items-center mt-6 text-blue-600 font-semibold text-sm">
                <span>Takımlarımı Yönet</span><svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </div>
    </a>
</div>