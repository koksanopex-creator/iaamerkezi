<div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-200">
    <div class="bg-gradient-to-r from-green-50 to-white px-6 py-5 border-b border-green-200"><h3 class="text-lg font-semibold text-green-800">Atanmış Projeler ({{ $atanmisOlanlar->count() }})</h3></div>
    @include('admin.iaa-yonetim.partials.table-content', ['iaas' => $atanmisOlanlar, 'type' => 'atanmis'])
</div>