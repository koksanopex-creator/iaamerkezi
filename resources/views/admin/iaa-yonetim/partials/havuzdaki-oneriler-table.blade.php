<div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-5 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-800">Havuzdaki Öneriler ({{ $havuzdakiler->count() }})</h3></div>
    @include('admin.iaa-yonetim.partials.table-content', ['iaas' => $havuzdakiler, 'type' => 'havuz'])
</div>