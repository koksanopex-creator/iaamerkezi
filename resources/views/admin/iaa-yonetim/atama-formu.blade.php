<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Projeyi Takıma Ata
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-medium text-gray-900">Proje Bilgileri</h3>
                        <p class="text-sm text-gray-600 mt-1"><strong>Proje Adı:</strong> {{ $iaa->baslik }}</p>
                        <p class="text-sm text-gray-600"><strong>Atanacak Takım:</strong> {{ $takim->ad }}</p>
                    </div>

                    <form action="{{ route('admin.iaa-yonetim.atamaYap', ['iaa' => $iaa, 'takim' => $takim]) }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="iaa_workflow_id" class="block text-sm font-medium text-gray-700">Proje Akış Şablonu</label>
                                <select id="iaa_workflow_id" name="iaa_workflow_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                                    <option value="" disabled selected>Lütfen bir şablon seçin...</option>
                                    @foreach ($workflows as $workflow)
                                        <option value="{{ $workflow->id }}" @if($workflow->is_default) selected @endif>
                                            {{ $workflow->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-gray-500">Bu proje için uygulanacak adımları içeren şablonu seçin.</p>
                                @error('iaa_workflow_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Proje Bitiş Tarihi</label>
                                <div class="mt-1">
                                    <input type="date" name="due_date" id="due_date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" min="{{ now()->toDateString() }}" required>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Takımın projeyi tamamlaması için son tarih.</p>
                                @error('due_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-200 pt-5">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ url()->previous() }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Geri Dön
                                </a>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Ata ve Projeyi Başlat
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>