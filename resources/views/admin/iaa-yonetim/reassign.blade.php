<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">İAA Önerisini Yeniden Ata</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">"{{ $iaa->baslik }}"</h3>
                    <p class="text-sm text-gray-600 mb-6">Mevcut Sahip: <strong>{{ $iaa->gonderen->name ?? 'N/A' }}</strong></p>
                    <form method="POST" action="{{ route('admin.iaa-yonetim.reassignUpdate', $iaa) }}">
                        @csrf
                        @method('PATCH')
                        <div>
                            <x-input-label for="gonderen_user_id" :value="__('Yeni Sahip Olarak Ata')" />
                            <select id="gonderen_user_id" name="gonderen_user_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected($iaa->gonderen_user_id == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>Atamayı Güncelle</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>