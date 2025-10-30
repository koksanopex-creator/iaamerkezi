<x-app-layout>
    {{-- ... Header ... --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Takım Detayları: <span class="text-indigo-600">{{ $takim->ad }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Başarı ve Hata Mesajları --}}
            @if(session('success'))<div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert"><p>{{ session('success') }}</p></div>@endif
            @if(session('error'))<div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert"><p>{{ session('error') }}</p></div>@endif
            
            {{-- İki Sütunlu Yapı --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    {{-- Üye Yönetim Kartı --}}
                    @include('admin.takim-yonetim.partials.uye-yonetim-karti')
                    {{-- Atanmış Projeler Kartı --}}
                    @include('admin.takim-yonetim.partials.atanmis-projeler-karti')
                </div>
                <div class="space-y-8">
                    {{-- Proje Atama Kartı --}}
                    @include('admin.takim-yonetim.partials.proje-atama-karti')
                    {{-- Takım Künyesi (Daha sonra düzenleme için) --}}
                    {{-- @include('takimlar.partials.takim-kunyesi') --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>