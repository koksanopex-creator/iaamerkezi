<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase font-bold">Süreç Türü</p>
        <p class="text-lg font-bold {{ $case->type == 'zorunlu' ? 'text-red-600' : 'text-green-600' }}">
            {{ ucfirst($case->type) }} Arabuluculuk
        </p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase font-bold">Talep Edilen</p>
        <p class="text-lg font-bold text-gray-800">{{ number_format($case->talep_tutari, 2) }} TL</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase font-bold">Anlaşılan Tutar</p>
        <p class="text-lg font-bold text-indigo-600">
            {{ $case->anlasilan_tutar ? number_format($case->anlasilan_tutar, 2) . ' TL' : '---' }}
        </p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase font-bold">Sorumlu Birim</p>
        <div class="flex items-center mt-1">
            @if($case->owner_role == 'hukuk')
                <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Hukuk
            @else
                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Personel
            @endif
        </div>
    </div>
</div>