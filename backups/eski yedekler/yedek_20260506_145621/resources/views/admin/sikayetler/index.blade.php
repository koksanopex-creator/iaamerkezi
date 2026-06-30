@push('pageTitle')
    Müşteri Şikayetleri | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Müşteri Şikayetleri Paneli') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success_html'))
                <div
                    class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3 animate-fade-in-down">
                    <div class="bg-green-100 p-2 rounded-full text-green-600 shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-green-800">Başarılı!</h4>
                        <p class="text-sm text-green-700 mt-1">{!! session('success_html') !!}</p>
                    </div>
                </div>
            @elseif(session('success'))
                <div
                    class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3 animate-fade-in-down">
                    <div class="bg-green-100 p-2 rounded-full text-green-600 shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-green-800">Başarılı!</h4>
                        <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Livewire bileşenimizi burada çağırarak sayfaya dahil ediyoruz --}}
            @livewire('admin.sikayetler-tablosu')

            @livewire('admin.sikayet-musteri-atama-modal')
        </div>
    </div>
</x-app-layout>