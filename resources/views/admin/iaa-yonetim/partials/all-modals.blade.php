{{-- Gerekli tüm koleksiyonları tek bir listede birleştiriyoruz --}}
@php
    $tumOneriler = $yoneticiOnayiBekleyenler
                    ->merge($onayBekleyenMisafirler)
                    ->merge($onayBekleyenKullanicilar)
                    ->merge($talepAlanOneriler)
                    ->merge($atanmisOlanlar)
                    ->merge($havuzdakiler)
                    ->merge($reddedilenler)
                    ->unique('id');

    // Sadece yönetici onayı bekleyenleri ayrı bir koleksiyona alıyoruz
    $onaylanacakProjeler = $yoneticiOnayiBekleyenler;
@endphp

{{-- DİĞER TÜM ÖNERİLER İÇİN MODALLAR --}}
@foreach ($tumOneriler->whereNotIn('id', $onaylanacakProjeler->pluck('id')) as $iaa)
    @include('admin.iaa-yonetim.partials.onayla-modal')
    @include('admin.iaa-yonetim.partials.reddet-modal')
    
    @if($iaa->puan)
        @include('admin.iaa-yonetim.partials.puan-detay-modal')
    @endif
@endforeach


{{-- ====================================================================== --}}
{{-- !!!!!! SORUNU ÇÖZECEK ANA DEĞİŞİKLİK BURADA !!!!!! --}}
{{-- ====================================================================== --}}
{{-- Sadece "Yönetici Onayı Bekleyenler" için özel modallar oluşturuyoruz --}}

@foreach ($onaylanacakProjeler as $iaa)
    {{-- Onayla Tamamlandı Modalı --}}
    <x-modal name="onayla-tamamlandi-modal-{{ $iaa->id }}" :show="$errors->any() && session('error_modal_id') == $iaa->id" focusable>
        <form method="post" action="{{ route('admin.iaa.approveCompleted', $iaa->id) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Projeyi Onayla ve Kapat</h2>
            <p class="mt-1 text-sm text-gray-600">"{{ $iaa->baslik }}" başlıklı projeyi onaylayıp kapatmak istediğinizden emin misiniz?</p>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">İptal</x-secondary-button>
                <x-primary-button class="ml-3 bg-green-600 hover:bg-green-700">Onayla</x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Reddet Tamamlandı Modalı --}}
    <x-modal name="reddet-tamamlandi-modal-{{ $iaa->id }}" :show="$errors->any() && session('error_modal_id') == $iaa->id" focusable>
        <form method="post" action="{{ route('admin.iaa.rejectCompleted', $iaa->id) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Projeyi Reddet</h2>
            <p class="mt-1 text-sm text-gray-600">"{{ $iaa->baslik }}" başlıklı projeyi reddetmek istediğinizden emin misiniz?</p>
            <div class="mt-6">
                <x-input-label for="rejection_reason_{{ $iaa->id }}" value="Reddetme Gerekçesi" class="sr-only" />
                <textarea id="rejection_reason_{{ $iaa->id }}" name="rejection_reason" class="mt-1 block w-3/4" placeholder="Reddetme gerekçesini yazınız..."></textarea>
                @error('rejection_reason', 'rejectCompleted_' . $iaa->id)
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">İptal</x-secondary-button>
                <x-danger-button class="ml-3">Reddet</x-danger-button>
            </div>
        </form>
    </x-modal>

    {{-- Revize İste Modalı --}}
    <x-modal name="revize-iste-modal-{{ $iaa->id }}" :show="$errors->any() && session('error_modal_id') == $iaa->id" focusable>
        <form method="post" action="{{ route('admin.iaa.requestRevision', $iaa->id) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Revize İste</h2>
            <p class="mt-1 text-sm text-gray-600">"{{ $iaa->baslik }}" başlıklı proje için revize istiyorsunuz.</p>
            <div class="mt-6">
                <x-input-label for="revision_reason_{{ $iaa->id }}" value="Revizyon Talebi" class="sr-only" />
                <textarea id="revision_reason_{{ $iaa->id }}" name="revision_reason" class="mt-1 block w-3/4" placeholder="İstenen revizyonları detaylıca açıklayınız..."></textarea>
                @error('revision_reason', 'requestRevision_' . $iaa->id)
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">İptal</x-secondary-button>
                <x-primary-button class="ml-3 bg-yellow-500 hover:bg-yellow-600">Revize İste</x-primary-button>
            </div>
        </form>
    </x-modal>
@endforeach