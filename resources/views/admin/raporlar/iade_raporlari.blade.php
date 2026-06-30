@push('pageTitle')
    İadeler Raporu | Müşteri Şikayeti Kaynaklı
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <div class="p-2 bg-rose-100 rounded-lg">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                    </svg>
                </div>
                {{ __('İadeler Raporu') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-rose-50 text-rose-700 text-xs font-black uppercase rounded-full border border-rose-100 shadow-sm">
                    Canlı Veri
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-slate-50">
        <div class="max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Livewire Bileşeni --}}
            @livewire('sikayet-iadesi-raporu')
        </div>
    </div>
</x-app-layout>
