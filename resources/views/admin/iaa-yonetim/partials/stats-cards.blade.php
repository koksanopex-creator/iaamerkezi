<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4 text-center">
    
    {{-- Onay Bekleyen --}}
    <div class="p-4 bg-yellow-100 rounded-lg shadow-sm">
        <p class="text-sm font-medium text-yellow-800">Onay Bekleyen</p>
        <p class="mt-1 text-3xl font-bold text-yellow-900">{{ $stats['onayBekleyen'] }}</p>
    </div>

    {{-- Havuzda --}}
    <div class="p-4 bg-blue-100 rounded-lg shadow-sm">
        <p class="text-sm font-medium text-blue-800">Havuzda</p>
        <p class="mt-1 text-3xl font-bold text-blue-900">{{ $stats['havuzda'] }}</p>
    </div>

    {{-- Talep Alan --}}
    <div class="p-4 bg-cyan-100 rounded-lg shadow-sm">
        <p class="text-sm font-medium text-cyan-800">Talep Alan</p>
        <p class="mt-1 text-3xl font-bold text-cyan-900">{{ $stats['talepAlan'] }}</p>
    </div>

    {{-- Atanmış Projeler --}}
    <div class="p-4 bg-green-100 rounded-lg shadow-sm">
        <p class="text-sm font-medium text-green-800">Atanmış Projeler</p>
        <p class="mt-1 text-3xl font-bold text-green-900">{{ $stats['atanmis'] }}</p>
    </div>

    {{-- Yönetici Onayı --}}
    <div class="p-4 bg-purple-100 rounded-lg shadow-sm border-2 border-purple-300">
        <p class="text-sm font-medium text-purple-800">Yönetici Onayı</p>
        <p class="mt-1 text-3xl font-bold text-purple-900">{{ $stats['yoneticiOnayi'] }}</p>
    </div>

    {{-- Tamamlanan --}}
    <div class="p-4 bg-gray-200 rounded-lg shadow-sm">
        <p class="text-sm font-medium text-gray-800">Tamamlanan</p>
        <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['tamamlanan'] }}</p>
    </div>

    {{-- Reddedilen --}}
    <div class="p-4 bg-red-100 rounded-lg shadow-sm">
        <p class="text-sm font-medium text-red-800">Reddedilen</p>
        <p class="mt-1 text-3xl font-bold text-red-900">{{ $stats['reddedilen'] }}</p>
    </div>

</div>