@push('pageTitle')
    Müşteri Şikayet Raporu | 
@endpush

@push('pageTitle')
    Müşteri Şikayet Raporu | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Şikayet Rapor Sayfası') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            @livewire('admin.sikayet-rapor-sayfasi')
        </div>
    </div>
</x-app-layout>