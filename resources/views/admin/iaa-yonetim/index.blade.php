<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('İyileştirmeye Açık Alan Yönetimi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert"><p>{{ session('success') }}</p></div>
            @endif
            
            @include('admin.iaa-yonetim.partials.stats-cards')

            {{-- ================= DEĞİŞİKLİK BURADA ================= --}}
            {{-- Her bir @include satırına eksik olan 'color' ve 'title' parametreleri eklendi --}}

            @include('admin.iaa-yonetim.partials.talep-alan-oneriler-table', [
                'iaas' => $talepAlanOneriler, 
                'title' => 'Talep Alan Öneriler', 
                'color' => 'blue'
            ])
            @include('admin.iaa-yonetim.partials.onay-bekleyen-misafirler-table', [
                'iaas' => $onayBekleyenMisafirler, 
                'type' => 'onay', 
                'title' => 'Misafirlerden Gelen Öneriler', 
                'color' => 'yellow'
            ])
            @include('admin.iaa-yonetim.partials.onay-bekleyen-kullanicilar-table', [
                'iaas' => $onayBekleyenKullanicilar, 
                'type' => 'onay', 
                'title' => 'Kayıtlı Kullanıcılardan Gelen Öneriler', 
                'color' => 'yellow'
            ])
            @include('admin.iaa-yonetim.partials.yonetici-onayi-bekleyenler-table', [
                'iaas' => $yoneticiOnayiBekleyenler, 
                'title' => 'Onay Bekleyen Tamamlanmış Projeler', 
                'color' => 'purple'
            ])
            @include('admin.iaa-yonetim.partials.atanmis-projeler-table', [
                'iaas' => $atanmisOlanlar, 
                'type' => 'atanmis', 
                'title' => 'Atanmış Projeler', 
                'color' => 'green'
            ])
            @include('admin.iaa-yonetim.partials.havuzdaki-oneriler-table', [
                'iaas' => $havuzdakiler, 
                'type' => 'havuz', 
                'title' => 'Havuzdaki Öneriler', 
                'color' => 'gray'
            ])
            @include('admin.iaa-yonetim.partials.reddedilen-oneriler-table', [
                'iaas' => $reddedilenler, 
                'type' => 'reddedilmis', 
                'title' => 'Reddedilen Öneriler', 
                'color' => 'red'
            ])
            @include('admin.iaa-yonetim.partials.tamamlanmasi-reddedilen-projeler-table', [
                'iaas' => $tamamlanmasiReddedilenler,
                'title' => 'Tamamlanması Reddedilen Projeler'
            ])
            {{-- YENİ EKLENEN BÖLÜM --}}
            @include('admin.iaa-yonetim.partials.tamamlanmis-projeler-ozet-table', [
                'iaas' => $sonTamamlananlar,
                'title' => 'Son 5 Tamamlanan Proje',
                'color' => 'gray'
            ])

        </div>
    </div>

    {{-- MODALS --}}
    @include('admin.iaa-yonetim.partials.all-modals')

    
    
    @if ($errors->any() && session('error_modal_id'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reddet-modal-{{ session('error_modal_id') }}' }));
        });
    </script>
    @endif
</x-app-layout>