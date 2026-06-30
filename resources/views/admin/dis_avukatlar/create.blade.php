<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Yeni Dış Avukat Ekle') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Hata Mesajları --}}
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4">
                        <ul class="list-disc ml-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- FORM BAŞLANGICI --}}
                {{-- DÜZELTME BURADA YAPILDI: admin.dis-avukatlar.store -> admin.dis_avukatlar.store --}}
                <form action="{{ route('admin.dis_avukatlar.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        
                        {{-- Ad Soyad --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Avukat Adı Soyadı</label>
                            <input type="text" name="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">E-posta Adresi (Giriş için)</label>
                            <input type="email" name="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        {{-- Şifre --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                            <input type="password" name="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        {{-- Telefon (Opsiyonel) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                            <input type="text" name="telefon" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow font-bold transition">
                            Avukatı Sisteme Ekle
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>