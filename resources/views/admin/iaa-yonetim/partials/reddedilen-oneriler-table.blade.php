<div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-red-200">
    <div class="bg-gradient-to-r from-red-50 to-white px-6 py-5 border-b border-red-200"><h3 class="text-lg font-semibold text-red-800">Reddedilen Öneriler ({{ $reddedilenler->count() }})</h3></div>
    @include('admin.iaa-yonetim.partials.table-content', ['iaas' => $reddedilenler, 'type' => 'reddedilmis'])
</div>