<x-slot name="header">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="font-black text-3xl text-gray-800 tracking-tight">Yönetim</h2>
            <p class="text-sm text-gray-500">Genel Bakış ve Performans Analizi</p>
        </div>
        <span class="text-sm font-bold text-gray-600 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
            {{ \Carbon\Carbon::now()->locale('tr')->isoFormat('D MMMM YYYY, dddd') }}
        </span>
    </div>
</x-slot>