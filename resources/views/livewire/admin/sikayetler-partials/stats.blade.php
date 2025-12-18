<div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100 bg-gray-50/50 border-b border-gray-200/70">
    <div class="p-4 text-center group hover:bg-blue-50 transition-colors">
        <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Toplam
        </p>
        <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">{{ $stats['toplam'] }}</p>
    </div>
    <div class="p-4 text-center group hover:bg-yellow-50 transition-colors">
        <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Beklemede
        </p>
        <p class="text-3xl font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent">{{ $stats['beklemede'] }}</p>
    </div>
    <div class="p-4 text-center group hover:bg-blue-50 transition-colors">
        <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            İşlemde
        </p>
        <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">{{ $stats['islemde'] }}</p>
    </div>
    <div class="p-4 text-center group hover:bg-green-50 transition-colors">
        <p class="text-gray-600 text-sm font-medium mb-1 flex items-center justify-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Çözümlenmiş
        </p>
        <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">{{ $stats['cozulmus'] }}</p>
    </div>
</div>