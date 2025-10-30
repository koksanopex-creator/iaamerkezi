<div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-yellow-200">
    <div class="bg-gradient-to-r from-yellow-50 to-white px-6 py-5 border-b border-yellow-200"><h3 class="text-lg font-semibold text-yellow-800">Kayıtlı Kullanıcılardan Gelen Öneriler ({{ $onayBekleyenKullanicilar->count() }})</h3></div>
    @include('admin.iaa-yonetim.partials.table-content', ['iaas' => $onayBekleyenKullanicilar, 'type' => 'onay'])
</div>