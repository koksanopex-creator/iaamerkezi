<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Müşteri Şikayetleri Paneli') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Livewire bileşenimizi burada çağırarak sayfaya dahil ediyoruz --}}
            @livewire('admin.sikayetler-tablosu')
        </div>
    </div>
</x-app-layout>