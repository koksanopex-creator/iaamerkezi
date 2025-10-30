<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Takım Davetlerim') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6 text-gray-900">
                @if(session('success')) <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">{{ session('success') }}</div> @endif
                <div class="space-y-4">
                    @forelse ($davetler as $davet)
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div>
                                <p><span class="font-bold">{{ $davet->takim->lider->name }}</span> sizi <span class="font-bold">{{ $davet->takim->ad }}</span> takımına davet ediyor.</p>
                                <p class="text-sm text-gray-500">{{ $davet->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="{{ route('takimlar.davetiKabulEt', $davet) }}" method="POST"> @csrf <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Kabul Et</button></form>
                                <form action="{{ route('takimlar.davetiReddet', $davet) }}" method="POST"> @csrf <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Reddet</button></form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500">Bekleyen takım davetiniz bulunmamaktadır.</p>
                    @endforelse
                </div>
            </div></div>
        </div>
    </div>
</x-app-layout>