<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Yeni Takım Oluştur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('takimlar.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="ad" class="block text-gray-700 text-sm font-bold mb-2">Takım Adı:</label>
                            <input type="text" name="ad" id="ad" value="{{ old('ad') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <div class="mb-4">
                            <label for="amac" class="block text-gray-700 text-sm font-bold mb-2">Takımın Amacı:</label>
                            <textarea name="amac" id="amac" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('amac') }}</textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="vizyon" class="block text-gray-700 text-sm font-bold mb-2">Vizyon:</label>
                            <textarea name="vizyon" id="vizyon" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('vizyon') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="misyon" class="block text-gray-700 text-sm font-bold mb-2">Misyon:</label>
                            <textarea name="misyon" id="misyon" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('misyon') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="kurallar" class="block text-gray-700 text-sm font-bold mb-2">Takım Kuralları:</label>
                            <textarea name="kurallar" id="kurallar" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('kurallar') }}</textarea>
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <a href="{{ route('takimlar.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-4">
                                İptal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Takımı Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>